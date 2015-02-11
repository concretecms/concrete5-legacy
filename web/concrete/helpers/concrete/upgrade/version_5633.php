<?php defined('C5_EXECUTE') or die("Access Denied.");

class ConcreteUpgradeVersion5633Helper {

	public function run() {
		if (!ENABLE_USER_PROFILES) {
			$membersPage = Page::getByPath('/members');
			if ($membersPage instanceof Page && !$membersPage->isError()) {
				$membersPage->delete();
			}
		}
	}

}
