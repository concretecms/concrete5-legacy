<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksDropController extends Controller {
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $this->redirect('dashboard/bricks');
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/search', $akCategoryHandle), t('Search')),
			array(View::url('dashboard/bricks/insert', $akCategoryHandle), t('Insert')),
			array(View::url('dashboard/bricks/structure', $akCategoryHandle), t('Structure')),
			array(View::url('dashboard/bricks/access', $akCategoryHandle), t('Permissions')),
			array(View::url('dashboard/bricks/drop', $akCategoryHandle), t('Drop'), TRUE)
		);
		$this->set('subnav', $subnav);
		if($post = $this->post()) {
			AttributeKeyCategory::getByHandle($akCategoryHandle)->delete();
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
		
} ?>
