<?php defined('C5_EXECUTE') or die('Access Denied.');

class ConcreteUpgradeVersion5613Helper {

	public function run() {
		$sp = Page::getByPath('/dashboard/system/basics/translate_special_items');
		if(!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/system/basics/translate_special_items');
			$sp->update(array('cName' => t('Translate Special Items')));
			$sp->setAttribute('meta_keywords', t('translate special items, translation, attribute names, attribute set names, attribute type names, permission names, permission descriptions, access entity type names'));
		}
	}
	
}
