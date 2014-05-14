<?php defined('C5_EXECUTE') or die("Access Denied.");

class ConcreteUpgradeVersion5632Helper {

	public function run() {
		$bt = BlockType::getByHandle('google_map');
		if(is_object($bt) && (!$bt->isError())) {
			$bt->refresh();
		}
	}

}
