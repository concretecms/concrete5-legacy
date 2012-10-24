<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion570Helper {

	
	public $dbRefreshTables = array(
		'Blocks',
		'CollectionVersionBlocksOutputCache'
	);
	
	
	public function run() {
		$tt = AttributeType::getByHandle('social_links');
		if (!is_object($tt) || $tt->getAttributeTypeID() == 0) {
			$tt = AttributeType::add('social_links', t('Social Link'));
		}
		$akc = AttributeKeyCategory::getByHandle('user');
		if (is_object($akc)) {
			$akc->associateAttributeKeyType($tt);
		}
	}
		
}
