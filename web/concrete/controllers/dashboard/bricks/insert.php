<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

require_once('edit.php');

class DashboardBricksInsertController extends DashboardBricksEditController {
	
	public $helpers = array('html','form','text');
	
	
	
	public function view($akCategoryHandle = NULL) {
		$req = Request::get();
		if(!$akCategoryHandle){
			if(!$req->isIncludeRequest()) $this->redirect('dashboard/bricks');
		}else{
			$this->set('akCategoryHandle', $akCategoryHandle);
		}
		
		$akcsh = Loader::helper('attribute_key_category_settings');
		$rs = $akcsh->getRegisteredSettings($akCategoryHandle);
		$subnav = array(array(View::url('dashboard/bricks'), t('Categories')));
		foreach($akcsh->getActions() as $action) {
			if(!$rs['url_'.$action.'_hidden']) {
				$url = View::url('dashboard/bricks/', $action, $akCategoryHandle);
				if($rs['url_'.$action]) $url = View::url($rs['url_'.$action]);
				$subnav[] = array(
					$url,
					t(ucwords($action)),
					($this->getCollectionObject()->getCollectionHandle() == $action)
				);
			}
		}
		$this->set('subnav', $subnav);
		$this->set('ih', Loader::helper('concrete/interface'));
		
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.permissions.js'));
		
		Loader::model('attribute_key_category_item_permission');
		$akcp = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
		$this->set('akcp',$akcp);
		
		$owner = $_POST['uID'] && $akcp->canAdmin() ? User::getByUserID($_POST['uID']) : new User(); 
		$this->set('owner', $owner);
		
		$this->set('attribs', AttributeKey::getList($akCategoryHandle));
		$category = AttributeKeyCategory::getByHandle($akCategoryHandle);
		$this->set('category', $category);
		$sets = $category->getAttributeSets();
		$this->set('sets', $sets);
		
		
		if($this->isPost() && $akcp->canAdd()) {
			$this->validate();
			if(!$this->error->has()) {
				Loader::model('attribute_key_category_item');
				$akcit = new AttributeKeyCategoryItem($akCategoryHandle);
				$akci = $akcit->add($owner);
				$this->set('akci', $akci);
				$this->saveData($akci);
				
				if(!$req->isIncludeRequest()) $this->redirect('/dashboard/bricks/edit/'.$akCategoryHandle.'/'.$akci->ID);
			}else{
				header("HTTP/1.1 412");
				
			}
		}
	}
	
	
} ?>
