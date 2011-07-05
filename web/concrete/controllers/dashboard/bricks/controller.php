<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksController extends Controller {
	
	public function on_start() {		
		/* Core Commerce Settings */

		// Products
		AttributeKeyCategory::registerSetting('core_commerce_product', 'list_model_path', 'product/list');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_search', 'dashboard/core_commerce/products/search');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_insert', 'dashboard/core_commerce/products/add');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_structure', 'dashboard/core_commerce/products/attributes');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_permission_disabled', TRUE);
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_drop_disabled', TRUE);

		// Orders
		AttributeKeyCategory::registerSetting('core_commerce_order', 'list_model_path', 'order/list');
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_search', 'dashboard/core_commerce/orders/search');
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_insert_disabled', TRUE);
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_structure', 'dashboard/core_commerce/orders/attributes');
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_permission_disabled', TRUE);
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_drop_disabled', TRUE);
		
		// Product Options
		AttributeKeyCategory::registerSetting('core_commerce_product_option', 'hidden', TRUE);
	}
	
	public function view() {
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories'), TRUE),
			array(View::url('dashboard/bricks/structure'), t('Global Attributes')),
			array(View::url('dashboard/bricks/access'), t('Global Permissions'))
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
