<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksDropController extends Controller {
	
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
		
		Loader::model('attribute_key_category_item_permission');
		$akcip = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
		$this->set('permission', $akcip->canAdmin());
		if($akcip->canAdmin()) {
			if($post = $this->post()) {
				AttributeKeyCategory::getByHandle($akCategoryHandle)->drop();
				$this->redirect('dashboard/bricks');
			} else {
				$txt = Loader::helper('text');
				$this->set('akCategoryName', $txt->unhandle($akCategoryHandle));
				$form = Loader::helper('form');
				$this->set('form', $form);
				$ih = Loader::helper('concrete/interface');
				$this->set('ih', $ih);
			}
		}
	}
		
} ?>
