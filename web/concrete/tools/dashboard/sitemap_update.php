<?
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

if (isset($_REQUEST['cID']) && is_array($_REQUEST['cID'])) {
	
	// Find out if there are any system pages before the first page to be sorted.
	// If there are, preserve their original order to keep them in correct 
	// positions if the system pages are not included in the search request
	$displayOrderIncrement = 0;
	$first = Page::getByID(current($_REQUEST['cID']));
	
	if (!$first->isSystemPage()) {
		$db = Loader::db();
		
		// Find out the display order of the first non-system page
		$firstOrder = $db->GetOne("SELECT cDisplayOrder FROM Pages WHERE cIsSystemPage = 0 AND cParentID = ? ORDER BY cDisplayOrder", array($first->getCollectionParentID()));
		
		if ($firstOrder > 0) {
			// And if it does not start the list, find out how many system pages there are before that and
			// use their maximum display order as the display order increment for the pages to be updated.
			$row = $db->GetRow("SELECT COUNT(cID) AS cnt, MAX(cDisplayOrder) AS maxOrder FROM Pages WHERE cParentID = ? AND cIsSystemPage = 1 AND cDisplayOrder < ?", array($first->getCollectionParentID(), $firstOrder));
			if ($row['cnt'] > 0) {
				$displayOrderIncrement = 1+$row['maxOrder'];
			}
		}
	}
	
	foreach($_REQUEST['cID'] as $displayOrder => $cID) { 
		//$v = array($displayOrder, $cID);
		$c = Page::getByID($cID);
		$c->updateDisplayOrder($displayOrder+$displayOrderIncrement,$cID);
	}
}

$json['error'] = false;
$json['message'] = t("Display order saved.");
$js = Loader::helper('json');
print $js->encode($json);

