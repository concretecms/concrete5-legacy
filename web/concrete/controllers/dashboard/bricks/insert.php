<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksInsertController extends Controller {
	
	public $helpers = array('html','form','text');
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->token = Loader::helper('validation/token');
	}
	
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
		$akcip = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
		$this->set('akcip', $akcip);
		
		$this->set('attribs', AttributeKey::getList($akCategoryHandle));
		$category = AttributeKeyCategory::getByHandle($akCategoryHandle);
		$this->set('category', $category);
		$sets = $category->getAttributeSets();
		$this->set('sets', $sets);
		
		
		if($this->isPost() && $akcip->canAdd()) {
			$this->validate();
			if(!$this->error->has()) {
				Loader::model('attribute_key_category_item');
				$akci = new AttributeKeyCategoryItem($akCategoryHandle);
				$newObject = $akci->add();
				$this->set('akci', $newObject);
				$this->saveData($newObject);
				
				if(!$req->isIncludeRequest()) $this->redirect('/dashboard/bricks/search/'.$akCategoryHandle);
			}else{
				header("HTTP/1.1 412");
				
			}
		}
	}
	
	private function saveData($item) {
		if($_POST['akID']) {
			foreach(array_keys($_POST['akID']) as $akID) {
				$ak = AttributeKey::getInstanceByID($akID);
				$item->setAttribute($ak, $_POST['akID'][$akID]['value']);
			}
		}
		
		if($item instanceof AttributeKeyCategoryItem) {
			$item->setOwner($post['uID']);
			$post['akcipID'] = $item->getID();
			
			$akciph = Loader::helper('attribute_key_category_item_permissions');
			$akciph->save($post);
		}
		
		$item->reindex();
	}
	
	private function validate() {		
		if($_POST['akID']) {
			foreach(array_keys($_POST['akID']) as $akID) {
				$ak = AttributeKey::getInstanceByID($akID);
				if(is_object($ak) && !($valid = $ak->getController()->validateForm($_POST['akID'][$akID]))){
					if(is_string($valid)){
						$this->error->add($valid, $ak->getAttributeKeyName());
					}else{
						$this->error->add(t('%s has an incorrect value.', $ak->getAttributeKeyName()));
					}
				}
			}
		}
	}
} ?>
