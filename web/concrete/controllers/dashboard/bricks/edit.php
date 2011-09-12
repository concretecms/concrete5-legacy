<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksEditController extends Controller {
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->token = Loader::helper('validation/token');
	}
	
	public function view($akCategoryHandle = NULL, $akciID = NULL) {
		if(!$akciID && !$akCategoryHandle) $this->redirect('dashboard/bricks');
		if(!$akciID && $akCategoryHandle) $this->redirect('dashboard/bricks/search/'.$akCategoryHandle);
		if($akCategoryHandle) {
			$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
			$akci = $akc->getItemObject($akciID);
		} else {
			Loader::model('attribute_key_category_item');
			$akci = AttributeKeyCategoryItem::getByID($akciID);
			$akCategoryHandle = $akci->akCategoryHandle;
		}
		$this->set('akci', $akci);
		$this->set('akCategoryHandle', $akCategoryHandle);
		
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
		$akcip = AttributeKeyCategoryItemPermission::get($akci);
		$this->set('permission', $akcip->canWrite());
		if($this->isPost()) {
			$this->validate();
			if(!$this->error->has()) {
				$this->saveData($akci);
				$this->redirect('/dashboard/bricks/search/'.$akCategoryHandle);
			}
		} else {
			$this->set('ih', Loader::helper('concrete/interface'));
			$this->set('txt', Loader::helper('text'));
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
			
			$this->set('delete_token', $this->token->generate('delete_akci_'.$akciID));
		}
	}
	
	public function delete($akciID, $token) {
		if($this->token->validate('delete_akci_'.$akciID, $token)) {
			Loader::model('attribute_key_category_item');
			$akci = AttributeKeyCategoryItem::getByID($akciID);
			$akCategoryHandle = $akci->akCategoryHandle;
			if(is_object($akci)) $akci->delete();
		}
		$this->redirect('/dashboard/bricks/search/'.$akCategoryHandle);
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
		
		$item->update();
	}

	public function validate() {
		
	}
		
} ?>
