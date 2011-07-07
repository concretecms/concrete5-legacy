<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksEditController extends Controller {
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->token = Loader::helper('validation/token');
	}
	
	public function view($akciID = NULL) {
		if(!$akciID) $this->redirect('dashboard/bricks');
		Loader::model('attribute_key_category_item');
		$akci = AttributeKeyCategoryItem::getByID($akciID);
		$this->set('akci', $akci);
		$akCategoryHandle = $akci->akCategoryHandle;
		$this->set('akCategoryHandle', $akCategoryHandle);
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/search', $akCategoryHandle), t('Search'), TRUE),
			array(View::url('dashboard/bricks/insert', $akCategoryHandle), t('Insert')),
			array(View::url('dashboard/bricks/structure', $akCategoryHandle), t('Structure')),
			array(View::url('dashboard/bricks/permissions', $akCategoryHandle), t('Permissions')),
			array(View::url('dashboard/bricks/drop', $akCategoryHandle), t('Drop'))
		);
		$this->set('subnav', $subnav);
		$this->addHeaderItem(Loader::helper('html')->javascript('attribute_key_category.permissions.js'));
		
		Loader::model('attribute_key_category_item_permission');
		$akcip = new AttributeKeyCategoryItemPermission($akCategoryHandle);
		$this->set('permission', $akcip->canAdd());
		if($this->isPost()) {
			$this->validate();
			if(!$this->error->has()) {
				Loader::model('attribute_key_category_item');
				$akci = AttributeKeyCategoryItem::getByID($akciID);
				$this->saveData($akci, $akCategoryHandle);
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
			$this->addHeaderItem('<script type="text/javascript">$(function(){ccm_setupAdvancedSearch(\'new-object\');});</script>');
			
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

	public function validate() {
		
	}
		
} ?>
