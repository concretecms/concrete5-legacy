<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksDropController extends Controller {
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $this->redirect('dashboard/bricks');
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/structure'), t('Global Attributes')),
			array(View::url('dashboard/bricks/access'), t('Global Permissions'))
		);
		$this->set('subnav', $subnav);
	}
		
} ?>
