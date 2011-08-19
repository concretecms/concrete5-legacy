<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksStructureController extends Controller {
	
	var $helpers = array('form'); 
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}
	
	public function view($akCategoryHandle = NULL, $task = NULL, $ID = NULL, $token = NULL) {
		if(!$akCategoryHandle) $akCategoryHandle = 'GLOBAL';
		Loader::model('attribute_key_category_item_permission');
		$this->set('txt', Loader::helper('text'));
		$this->set('ih', Loader::helper('concrete/interface'));
		$valt = Loader::helper('validation/token');
		$this->set('valt', $valt);
		switch($akCategoryHandle) {
			case 'GLOBAL':
				$subnav = array(
					array(View::url('dashboard/bricks'), t('Categories')),
					array(View::url('dashboard/bricks/structure'), t('Attribute Management'), TRUE),
					array(View::url('dashboard/bricks/permissions'), t('Global Permissions'))
				);
				$this->set('subnav', $subnav);
				$akcip = AttributeKeyCategoryItemPermission::get('GLOBAL');
				$this->set('permission', $akcip->canAdmin());
				$this->set('types', AttributeType::getList());
				break;
			default:
				$category = AttributeKeyCategory::getByHandle($akCategoryHandle);
				$settings = $category->getRegisteredSettings();
				if($settings['url_'.$this->getcollectionObject()->getCollectionHandle()])
					$this->redirect($settings['url_'.$this->getcollectionObject()->getCollectionHandle()]);
				
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
				$this->set('akCategoryHandle', $akCategoryHandle);
				$akcip = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
				$this->set('permission', $akcip->canAdmin());
				$this->set('attribs', AttributeKey::getList($akCategoryHandle));
				$this->set('category', $category);
				$otypes = AttributeType::getList();
				$types = array();
				foreach($otypes as $at) {
					if($at->isAssociatedWithCategory($category)) {
						$types[$at->getAttributeTypeID()] = $at->getAttributeTypeName();
					}
				}
				$this->set('attributeTypes', $types);
				break;
		}
		if($task) {
			$this->set('task', $task);
			switch($task) {
				case 'attribute_deleted':
					$this->set('message', t('Attribute Deleted.'));
					break;
				case 'attribute_created':
					$this->set('message', t('Attribute Created.'));
					break;
				case 'attribute_updated':
					$this->set('message', t('Attribute Updated.'));
					break;
				case 'attribute_type_added':
					$this->set('message', 'Attribute Type added.');
					break;
				case 'associations_updated':
					$this->set('message', 'Attribute Types saved.');
					break;
				case 'sets_disabled':
					$this->set('message', 'Attribute Sets have been disabled.');
					break;
				
				case 'select_type':						
					$atID = $this->request('atID');
					$at = AttributeType::getByID($atID);
					$this->set('type', $at);
					break;
				case 'add':
					$this->view($akCategoryHandle, 'select_type');
					$type = $this->get('type');
				
						$type = AttributeType::getByID($this->post('atID'));
						$ak = new AttributeKey($akCategoryHandle);
						$ak->create($type, $this->post(), $category->getPackageID());
						$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'attribute_created');
				
					break;
				case 'edit':						
					if ($this->post('akID')) {
						$ID = $this->post('akID');
					}
				
					$ak = new AttributeKey($akCategoryHandle);
					$key = $ak->getByID($ID, $akCategoryHandle);
				
					$type = $key->getAttributeType();
					$this->set('key', $key);
					$this->set('type', $type);
					if ($this->isPost()) {
						$key->update($this->post());
						$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'attribute_updated');
					}
					break;
				case 'delete':
					try {
						$ak = new AttributeKey($akCategoryHandle);
						$ak = $ak->getByID($ID);
					
						if(!($ak instanceof AttributeKey)) {
							throw new Exception(t("Invalid attribute ID."));
						}
						if(!$valt->validate('delete_attribute', $token)) {
							throw new Exception($valt->getErrorMessage());
						}
						$ak->delete();
					
						$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'attribute_deleted');
					} catch (Exception $e) {
						$this->set('error', $e);
					}
					break;
				case 'sets':
					if($this->isPost()) {							
						foreach($category->getAttributeSets() as $set) {
							if($this->post('setHandle') == $set->asHandle) {
								$fail = TRUE;
							}
						}
						if(!$fail) {
							$category->addSet($this->post('setHandle'), $this->post('setName'), Package::getByID($category->getPackageID()));
							$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'sets', 'set_added');
						} else { 
							$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'sets', 'set_exists');
						}
					}
				
					if($valt->validate('allow_sets', $token)) {
						$category->setAllowAttributeSets(TRUE);
						$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'sets');
					} elseif($valt->validate('disable_sets', $token)) {
						$category->setAllowAttributeSets(FALSE);
						$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'sets_disabled');
					}
				
					$this->set('sets', $category->getAttributeSets());
				
					switch($ID) {
						case 'set_exists':
							$this->set('message', t('Set handle already exists.'));
							break;
						case 'set_added':
							$this->set('message', t('Set added successfully.'));
							break;	
						case 'set_deleted':
							$this->set('message', t('Set removed successfully.'));
							break;
						case 'set_delete_error':
							$this->set('message', t('An error occured when trying to delete the set.'));
							break;
					}
					break;
				case 'delete_set':
					$as = AttributeSet::getByID($ID);
					if(is_object($as) && $valt->validate('delete_set', $token)) {
						$as->delete();
						$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'sets', 'set_deleted');
					} else {
						$this->redirect('/dashboard/bricks/structure/'.$akCategoryHandle, 'sets','set_delete_error');
					}
					break;
			}
		}
	}

	
	public function save_attribute_type_associations($akCategoryHandle) {
		if($akCategoryHandle != 'GLOBAL') {
			$cat = AttributeKeyCategory::getByHandle($akCategoryHandle);
			$cat->clearAttributeKeyCategoryTypes();
			if (is_array($this->post($cat->getAttributeKeyCategoryHandle()))) {
				foreach($this->post($cat->getAttributeKeyCategoryHandle()) as $id) {
					$type = AttributeType::getByID($id);
					$cat->associateAttributeKeyType($type);
				}
			}
		} else {
			$list = AttributeKeyCategory::getList();
			foreach($list as $cat) {
				$cat->clearAttributeKeyCategoryTypes();
				if (is_array($this->post($cat->getAttributeKeyCategoryHandle()))) {
					foreach($this->post($cat->getAttributeKeyCategoryHandle()) as $id) {
						$type = AttributeType::getByID($id);
						$cat->associateAttributeKeyType($type);
					}
				}
			}
		}
		$this->redirect($this->getCollectionObject()->getCollectionPath(), $akCategoryHandle, 'associations_updated');
	}

} ?>
