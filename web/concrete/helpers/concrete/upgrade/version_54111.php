<?php 
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Tao Sasaki <tao@xross-cube.com>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion54111Helper {


	public function run() {
		$db = Loader::db();
		// Converting the arHandle without t()
        $db->Execute("UPDATE Areas SET arHandle = replace(arHandle,'".t('Layout')."','Layout') WHERE arHandle LIKE '%: ".t('Layout')."%'");
        $db->Execute("UPDATE CollectionVersionBlockStyles SET arHandle = replace(arHandle,'".t('Layout')."','Layout') WHERE arHandle LIKE '%: ".t('Layout')."%'");
        $db->Execute("UPDATE CollectionVersionBlocks SET arHandle = replace(arHandle,'".t('Layout')."','Layout') WHERE arHandle LIKE '%: ".t('Layout')."%'");
	}

	
}