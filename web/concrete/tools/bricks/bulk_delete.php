<?php defined('C5_EXECUTE') or die(_("Access Denied."));
if ($_REQUEST['task'] == 'delete') {
	if (!ini_get('safe_mode')) {
		@set_time_limit(0);
	}
	$js = Loader::helper('json');
	$decoded = $js->decode($_REQUEST['json']);
	$akc = AttributeKeyCategory::getByHandle($decoded->akCategoryHandle);
	foreach($decoded->akcIDs as $ID) {
		$akci = $akc->getItemObject($ID);
		$akci->delete();
	}
} else { ?>
	<p>Are you sure you want to delete the selected items?</p>
<?php
	$js = Loader::helper('json');
	$decoded = $js->decode($_REQUEST['json']);
	$akc = AttributeKeyCategory::getByHandle($_REQUEST['akCategoryHandle']);
	foreach($decoded->akcIDs as $ID) {
		$akci = $akc->getItemObject($ID);
	}
	$ih = Loader::helper('concrete/interface');
	print "<script type='text/javascript'>var URIComponents = '".$_REQUEST['json']."';</script>";
	print $ih->button_js(t('Yes'), 'ccm_deleteAndRefeshSearch(URIComponents, \''.$_REQUEST['searchInstance'].'\')');
	print $ih->button_js(t('No'), 'ccm_closeModalRefeshSearch(\''.$_REQUEST['searchInstance'].'\')', 'right');
} ?>
