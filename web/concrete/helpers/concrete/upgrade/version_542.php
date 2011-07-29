<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion542Helper {

	public function run() {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(*) from TaskPermissions where tpHandle = ?', array('delete_user'));
		if ($cnt < 1) {
			$g3 = Group::getByID(ADMIN_GROUP_ID);
			$tip = TaskPermission::addTask('delete_user', t('Delete Users'), false);
			if (is_object($g3)) {
				$tip->addAccess($g3);
			}
		}
		
		// Install Single Pages
		Loader::model('single_page');
		$sp = Page::getByPath('/dashboard/settings/multilingual');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/settings/multilingual');
			$d1a->update(array('cName'=>t('Multilingual Setup')));
		}
		
		$sp = Page::getByPath('/dashboard/bricks');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks');
			$d1a->update(array('cDescription'=>t('Easy to understand database')));
		}
		$sp = Page::getByPath('/dashboard/bricks/add');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks/add');
		}
		$sp = Page::getByPath('/dashboard/bricks/search');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks/search');
		}
		$sp = Page::getByPath('/dashboard/bricks/insert');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks/insert');
		}
		$sp = Page::getByPath('/dashboard/bricks/structure');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks/structure');
		}
		$sp = Page::getByPath('/dashboard/bricks/permissions');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks/permissions');
		}
		$sp = Page::getByPath('/dashboard/bricks/drop');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks/drop');
		}
		$sp = Page::getByPath('/dashboard/bricks/edit');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/bricks/edit');
		}
		
		// Install AttributeTypes
		$akci = AttributeType::add('attribute_key_category_items', t('Bricks'));
	}
	
	public function prepare() {
		// we install the updated schema just for tables that matter
		Package::installDB(dirname(__FILE__) . '/db/version_542.xml');
	}

	
}
