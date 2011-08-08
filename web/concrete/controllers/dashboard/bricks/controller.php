<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksController extends Controller {
	
	public function view() {
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories'), TRUE),
			array(View::url('dashboard/bricks/structure'), t('Attribute Management')),
			array(View::url('dashboard/bricks/permissions'), t('Global Permissions'))
		);
		$this->set('subnav', $subnav);
		
		foreach(AttributeKeyCategory::getList() as $akc) {
			if($akc->pkgID == '0') $pkgName = 'Custom Additions';
			if($akc->pkgID) $pkgName = Package::getByID($akc->pkgID)->getPackageName();
			if(!$pkgName) $pkgName = 'Built-In';
			$piles[$pkgName][] = $akc;
			unset($pkgName);
		}
		if(empty($piles['Custom Additions'])) $piles['Custom Additions'] = NULL;
		$this->set('piles', $piles);
	}
	
} ?>
