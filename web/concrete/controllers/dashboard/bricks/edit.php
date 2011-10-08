<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksEditController extends Controller {
	
	public $helpers = array('html','form','text');
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->token = Loader::helper('validation/token');
	}
	
	public function view($akCategoryHandle = NULL, $akciID = NULL) {
		$req = Request::get();
		
		if(!$akciID && !$akCategoryHandle) {
			if(!$req->isIncludeRequest()) $this->redirect('dashboard/bricks');
			return;
		}
		if(!$akciID && $akCategoryHandle){
			if(!$req->isIncludeRequest()) $this->redirect('dashboard/bricks/search/'.$akCategoryHandle);
			return;
		}
		
		if($akCategoryHandle) {
			$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
			$akci = $akc->getItemObject($akciID);
		} else {
			Loader::model('attribute_key_category_item');
			$akci = AttributeKeyCategoryItem::getByID($akciID);
			$akCategoryHandle = $akci->akCategoryHandle;
		}
		
		if(!is_object($akci)){
			$this->error->add(t('Item #%s does not exist.', $akciID));
			return;
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
		
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.permissions.js'));
		
		Loader::model('attribute_key_category_item_permission');
		$akcip = AttributeKeyCategoryItemPermission::get($akci);
		$this->set('akcip', $akcip);
		
		$this->set('ih', Loader::helper('concrete/interface'));
		$this->set('attribs', AttributeKey::getList($akCategoryHandle));
		$category = AttributeKeyCategory::getByHandle($akCategoryHandle);
		$this->set('category', $category);
		$sets = $category->getAttributeSets();
		$this->set('sets', $sets);
		$this->set('delete_token', $this->token->generate('delete_akci_'.$akciID));
		
		if($this->isPost()) {
			$this->validate();
			if(!$this->error->has()) {
				$this->saveData($akci);
				if(!$req->isIncludeRequest()) $this->redirect('/dashboard/bricks/search/'.$akCategoryHandle);
			}
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
		//Save attributes
		if($_POST['akID']) {
			foreach(array_keys($_POST['akID']) as $akID) {
				$ak = AttributeKey::getInstanceByID($akID);
				$akPostData = $_POST['akID'][$akID];
				
				if(!empty($akPostData)){
					$item->setAttribute($ak, $_POST['akID'][$akPostData]);
				}else{					
					$item->clearAttribute($ak);
				}				
			}
		}
		//Save permissions
		if($item instanceof AttributeKeyCategoryItem) {
			$item->setOwner($post['uID']);
			$post['akcipID'] = $item->getID();
			
			$akciph = Loader::helper('attribute_key_category_item_permissions');
			$akciph->save($post);
		}
		
		$item->update();
	}

	public function validate() {
		//Validate attributes
		if($_POST['akID']) {			
			foreach(array_keys($_POST['akID']) as $akID) {
				$ak = AttributeKey::getInstanceByID($akID);
				if(!empty($_POST['akID']['value']) && is_object($ak) && !($valid = $ak->validateAttributeForm($_POST['akID'][$akID]))){
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
