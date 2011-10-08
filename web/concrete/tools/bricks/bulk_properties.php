<?php defined('C5_EXECUTE') or die(_("Access Denied."));

$form = Loader::helper('form');
Loader::model('attribute_key_category_item');

$searchInstance = $_REQUEST['searchInstance'];

$attribs = AttributeKey::getList($_REQUEST['akCategoryHandle']);

$akcItems = array();
if (is_array($_REQUEST['akciID'])) {
	foreach($_REQUEST['akciID'] as $akciID) {
		$akc = AttributeKeyCategory::getByHandle($_REQUEST['akCategoryHandle']);
		$akci = $akc->getItemObject($akciID);
		$akcItems[] = $akci;
	}
}

if ($_POST['task'] == 'update_extended_attribute') {
	$akc = AttributeKeyCategory::getByHandle($_POST['akCategoryHandle']);
	foreach($akcItems as $akci) {
		$ak = AttributeKey::getInstanceByID($_POST['fakID']);
		$akci->setAttribute($ak, $_POST['akID'][$_POST['fakID']]['value']);
		$val = $akci->getAttributeValueObject($ak)->getValue('display');
	}
	
	print $val;	
	exit;
} 

if ($_POST['task'] == 'clear_extended_attribute') {

	$ak = AttributeKey::getInstanceByID($_POST['fakID']);
	foreach($akcItems as $akci) {
		$akci->clearAttribute($ak);
	}

	print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	exit;
}


function printAttributeRow($ak, $akCategoryHandle) {
	global $akcItems, $form;
	
	$value = '';
	for ($i = 0; $i < count($akcItems); $i++) {
		$lastValue = $value;
		$noO = $akcItems[$i];
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
	foreach($akcItems as $noO) {
		$ID = $noO->ID;
		if(!$ID) {
			eval('$ID = $noO->'.substr($akCategoryHandle, 0, 1).'ID;');
		}
		$hiddenfields.=' '.$form->hidden('akciID[]' , $ID).' ';
	}	
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<th><a href="javascript:void(0)">' . $ak->getAttributeKeyName() . '</a></th>
		<td width="100%" class="ccm-attribute-editable-field-central"><div class="ccm-attribute-editable-field-text">' . $text . '</div>
		<form method="post" action="' . REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/bulk_properties">
			<input type="hidden" name="akCategoryHandle" value="' . $akCategoryHandle . '" />
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

<h1><?php echo t('Additional Properties')?></h1>


<div id="ccm-new-object-properties">
<table border="0" cellspacing="0" cellpadding="0" class="ccm-grid">
<?php 

foreach($attribs as $at) {

	printAttributeRow($at, $_REQUEST['akCategoryHandle']);

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
print $ih->button_js(t('Close'), (!empty($searchInstance) ? '$(\'#'.$searchInstance.'_form\').submit();' : '').'jQuery.fn.dialog.closeTop()', 'right'); ?>
