<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksAccessController extends Controller {
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $akCategoryHandle = 'GLOBAL';
		switch($akCategoryHandle) {
			case 'GLOBAL':
				$subnav = array(
					array(View::url('dashboard/bricks'), t('Categories')),
					array(View::url('dashboard/bricks/structure'), t('Global Attributes')),
					array(View::url('dashboard/bricks/access'), t('Global Permissions'), TRUE)
				);
				$this->set('subnav', $subnav);
				break;
			default:
				$subnav = array(
					array(View::url('dashboard/bricks'), t('Categories')),
					array(View::url('dashboard/bricks/search', $akCategoryHandle), t('Search')),
					array(View::url('dashboard/bricks/structure', $akCategoryHandle), t('Structure')),
					array(View::url('dashboard/bricks/access', $akCategoryHandle), t('Permissions'), TRUE)
				);
				$this->set('subnav', $subnav);
				break;
		}
	}
		
} ?>
