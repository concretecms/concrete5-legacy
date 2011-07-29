<?php defined('C5_EXECUTE') or die(_("Access Denied."));
if ($_REQUEST['task'] == 'delete') {
	if (!ini_get('safe_mode')) {
		@set_time_limit(0);
	}
	$js = Loader::helper('json');
	$decoded = $js->decode($_REQUEST['json']);
	Loader::model('attribute_key_category_item');
	foreach($decoded->newObjectIDs as $ID) {
		$akci = AttributeKeyCategoryItem::getByID($ID);
		$akci->delete();
	}
} else { ?>
	<p>Are you sure you want to delete the selected items?</p>
<?php
	$ih = Loader::helper('concrete/interface');
	print "<script type='text/javascript'>var URIComponents = '".$_REQUEST['json']."';</script>";
	print $ih->button_js(t('Yes'), 'ccm_deleteAndRefeshSearch(URIComponents)');
	print $ih->button_js(t('No'), 'ccm_closeModalRefeshSearch()', 'right');
} ?>
