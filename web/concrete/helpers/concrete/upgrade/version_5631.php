<?php defined('C5_EXECUTE') or die("Access Denied.");

class ConcreteUpgradeVersion5631Helper {

	public $dbRefreshTables = array(
		'Users'
	);

	public function run() {
		$bt = BlockType::getByHandle('google_map');
		if(is_object($bt) && (!$bt->isError())) {
			$bt->refresh();
		}
	}

}
