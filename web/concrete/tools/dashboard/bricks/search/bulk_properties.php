<?php defined('C5_EXECUTE') or die(_("Access Denied."));

$form = Loader::helper('form');
Loader::model('attribute_key_category_item');

$attribs = AttributeKey::getList($_REQUEST['table']);

$newObjects = array();
if (is_array($_REQUEST['newObjectID'])) {
	foreach($_REQUEST['newObjectID'] as $newObjectID) {
		$noO = AttributeKeyCategoryItem::getByID($newObjectID);
		$newObjects[] = $noO;
	}
}

if ($_POST['task'] == 'update_extended_attribute') {
	$akID = $_REQUEST['fakID'];
	
	$ak = AttributeKey::getByID($akID);
	foreach($newObjects as $noO) {
		$noO->saveAttribute($ak);
	}
	$val = $noO->getAttributeValueObject($ak);
	print $val->getValue('display');	
	exit;
} 

if ($_POST['task'] == 'clear_extended_attribute') {

	$fakID = $_REQUEST['fakID'];
	$value = ''; 
	
	$ak = AttributeKey::getByID($fakID);
	foreach($newObjects as $noO) {
		$noO->clearAttribute($ak);
	}

	print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	exit;
}


function printAttributeRow($ak, $handle) {
	global $newObjects, $form;
	
	$value = '';
	for ($i = 0; $i < count($newObjects); $i++) {
		$lastValue = $value;
		$noO = $newObjects[$i];
		$vo = $noO->getAttributeValueObject($ak);
		if (is_object($vo)) {
			$value = $vo->getValue('display');
			if ($i > 0 ) {
				if ($lastValue != $value) {
					$value = '<div class="ccm-attribute-field-none">' . t('Multiple Values') . '</div>';
					break;
				}
			}
		}
	}	
	
	if ($value == '') {
		$text = '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	} else {
		$text = $value;
	}
	if ($ak->isAttributeKeyEditable()) { 
	$type = $ak->getAttributeType();
	$hiddenFIDfields='';
	foreach($newObjects as $noO) {
		$hiddenfields.=' '.$form->hidden('newObjectID[]' , $noO->getID()).' ';
	}	
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<th><a href="javascript:void(0)">' . $ak->getAttributeKeyName() . '</a></th>
		<td width="100%" class="ccm-attribute-editable-field-central"><div class="ccm-attribute-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/dashboard/bricks/search/bulk_properties?table='.$handle.'">
			<input type="hidden" name="fakID" value="' . $ak->getAttributeKeyID() . '" />
			'.$hiddenfields.'
			<input type="hidden" name="task" value="update_extended_attribute" />
			<div class="ccm-attribute-editable-field-form ccm-attribute-editable-field-type-' . strtolower($type->getAttributeTypeHandle()) . '">
			' . $ak->render('form', $vo, true) . '
			</div>
		</form>
		</td>
		<td class="ccm-attribute-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-attribute-editable-field-save-button" /></a>
		<a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/close.png" width="16" height="16" class="ccm-attribute-editable-field-clear-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-attribute-editable-field-loading" />
		</td>
	</tr>';
	
	} else {

	$html = '
	<tr>
		<th>' . $ak->getAttributeKeyName() . '</th>
		<td width="100%" colspan="2">' . $text . '</td>
	</tr>';	
	}
	print $html;
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-new-object-properties-wrapper">
<?php  } ?>

<h1><?php echo t('Details')?></h1>


<div id="ccm-new-object-properties">

<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
<?php 

foreach($attribs as $at) {

	printAttributeRow($at, $names->fileName);

}

?>
</table>

<br/>  

</div>

<script type="text/javascript">
$(function() { 
	ccm_activateEditablePropertiesGrid();  
});
</script>

<?php 
if (!isset($_REQUEST['reload'])) { ?>
</div>
<?php  } ?>
<?php 
$ih = Loader::helper("concrete/interface");
print $ih->button_js(t('Close'), 'ccm_closeModalRefeshSearch()', 'right'); ?>
