<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksAddController extends Controller {
	
	public function view() {
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/structure'), t('Global Attributes')),
			array(View::url('dashboard/bricks/access'), t('Global Permissions'))
		);
		$this->set('subnav', $subnav);
	}
		
} ?>
