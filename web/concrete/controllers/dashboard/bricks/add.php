<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksAddController extends Controller {
	
	public function view($packageHandle = NULL) {
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/structure'), t('Global Attributes')),
			array(View::url('dashboard/bricks/permissions'), t('Global Permissions'))
		);
		$this->set('subnav', $subnav);
		if($post = $this->post()) {
			$txt = Loader::helper('text');
			if(!$post['enableSets']) $post['enableSets'] = 0;
			if(!$post['package_handle']) $post['package_handle'] = 0;
			AttributeKeyCategory::add($txt->uncamelcase($txt->camelcase($post['akCategoryName'])), $post['enableSets'], $post['package_handle']);
			$this->redirect('dashboard/bricks');
		} else {
			$this->set('packageHandle', $packageHandle);
			$form = Loader::helper('form');
			$this->set('form', $form);
			$ih = Loader::helper('concrete/interface');
			$this->set('ih', $ih);
		}
	}
		
} ?>
