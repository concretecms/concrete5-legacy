<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksInsertController extends Controller {
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $this->redirect('dashboard/bricks');
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/search', $akCategoryHandle), t('Search')),
			array(View::url('dashboard/bricks/structure', $akCategoryHandle), t('Structure')),
			array(View::url('dashboard/bricks/access', $akCategoryHandle), t('Permissions'))
		);
		$this->set('subnav', $subnav);
	}
		
} ?>
