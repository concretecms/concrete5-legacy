<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksPermissionsController extends Controller {

	public function view($akCategoryHandle = NULL, $task = NULL) {
		Loader::model('attribute_key_category_item_permission');
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.permissions.js'));
		$this->token = Loader::helper('validation/token');
		$this->set('ih', Loader::helper('concrete/interface'));
		if(!$akCategoryHandle) {
			$akcip = AttributeKeyCategoryItemPermission::get('GLOBAL', NULL, FALSE);
			$this->set('permission', $akcip->canAdmin());
			$subnav = array(
				array(View::url('dashboard/bricks'), t('Categories')),
				array(View::url('dashboard/bricks/structure'), t('Attribute Management')),
				array(View::url('dashboard/bricks/permissions'), t('Global Permissions'), TRUE)
			);
			$this->set('subnav', $subnav);
		} else {
			$this->set('txt', Loader::helper('text'));
			$this->set('akCategoryHandle', $akCategoryHandle);
			$akcip = AttributeKeyCategoryItemPermission::get($akCategoryHandle, NULL, FALSE);
			
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
			$this->set('permission', $akcip->canAdmin());
		}
		$this->set('akcip', $akcip);

		if($task) {
			switch($task) {
				case 'save_permissions':
					if (!$this->token->validate("update_permissions")) {
						$this->set('error', array($this->token->getErrorMessage()));
						return;
					}
					
					$akciph = Loader::helper('attribute_key_category_item_permissions');
					$akciph->save($this->post());
					$this->redirect('/dashboard/bricks/permissions', $akCategoryHandle, 'permissions_saved');
					break;
				case 'permissions_saved':
					$this->set('message', t('Permissions saved.'));
					break;
			}
		}
	}
	
	public function global_permissions_saved() {
		if($this->post()) {
			$akciph = Loader::helper('attribute_key_category_item_permissions');
			$akciph->save($this->post());
		}
		$this->set('message', t('Global Permissions saved.'));
		$this->view();
	}
		
} ?>
