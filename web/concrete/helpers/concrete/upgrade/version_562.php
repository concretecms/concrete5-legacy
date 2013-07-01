<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion562Helper {

	public function run() {
		$bt = BlockType::getByHandle('image');
		if (is_object($bt)) {
			$bt->refresh();
		}

	}

}
