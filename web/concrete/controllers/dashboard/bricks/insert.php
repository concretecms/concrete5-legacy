<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksInsertController extends Controller {
	
	public $helpers = array('html','form');
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->token = Loader::helper('validation/token');
	}
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $this->redirect('dashboard/bricks');
		
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
	
		$this->addHeaderItem(Loader::helper('html')->javascript('attribute_key_category.permissions.js'));
		
		Loader::model('attribute_key_category_item_permission');
		$akcip = new AttributeKeyCategoryItemPermission($akCategoryHandle);
		$this->set('permission', $akcip->canAdd());
		if($this->isPost()) {
			$this->validate();
			if(!$this->error->has()) {
				Loader::model('attribute_key_category_item');
				$akci = new AttributeKeyCategoryItem($akCategoryHandle);
				$newObject = $akci->add();
				$this->saveData($newObject, $akCategoryHandle);
				$this->redirect('/dashboard/bricks/search/'.$akCategoryHandle);
			}
		} else {
			$this->set('ih', Loader::helper('concrete/interface'));
			$this->set('txt', Loader::helper('text'));
			$this->set('akCategoryHandle', $akCategoryHandle);
			$this->set('attribs', AttributeKey::getList($akCategoryHandle));
			$category = AttributeKeyCategory::getByHandle($akCategoryHandle);
			$this->set('category', $category);
			$sets = $category->getAttributeSets();
			$this->set('sets', $sets);
			
			$form = Loader::helper('form');
			$this->set('form', $form);
			$this->addHeaderItem(Loader::helper('html')->javascript('attribute_key_category.ui.js'));
			$searchInstance = $akCategoryHandle.time();
			if (isset($_REQUEST['searchInstance'])) {
				$searchInstance = $_REQUEST['searchInstance'];
			}
			$this->addHeaderItem('<script type="text/javascript">$(function(){ccm_setupAdvancedSearch(\''.$searchInstance.'\');});</script>');
		}
	}
	
	private function saveData($object, $akCategoryHandle) {
		$post = $this->post();
		$akIDs = $post['akID'];
		if($akIDs) {
			foreach(array_keys($akIDs) as $akID) {
				$ak = AttributeKey::getByID($akID);
				$object->saveAttribute($ak);
			}
		}
		$object->setOwner($post['uID']);
		$post['akcipID'] = $object->getID();
		
		$akciph = Loader::helper('attribute_key_category_item_permissions');
		$akciph->save($post);
		
		$object->update();
	}
	
	private function validate() {
		
	}
} ?>
