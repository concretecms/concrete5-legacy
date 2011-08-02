<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

$form = Loader::helper('form');

$selectedAKIDs = array();
$slist = AttributeKey::getColumnHeaderList($_REQUEST['akCategoryHandle']);
foreach($slist as $sk) {
	$selectedAKIDs[] = $sk->getAttributeKeyID();
}

if ($_POST['task'] == 'update_columns') {
	$sc = AttributeKeyCategory::getByHandle($_REQUEST['akCategoryHandle']);
	$sc->clearAttributeKeyCategoryColumnHeaders();
	
	if (is_array($_POST['akID'])) {
		foreach($_POST['akID'] as $akID) {
			$vtak = new AttributeKey($_REQUEST['akCategoryHandle']);
			$ak = $vtak->getByID($akID);
			$ak->setAttributeKeyColumnHeader(1);
		}
	}
	
	exit;
}

$list = AttributeKey::getList($_REQUEST['akCategoryHandle']);
?>

<form method="post" id="ccm-new-object-customize-search-columns-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/customize_search_columns/';?>">
<?php echo $form->hidden('task', 'update_columns')?>

<h1><?php echo t('Additional Searchable Attributes')?></h1>

<p><?php echo t('Choose the additional attributes you wish to include as column headers.')?></p>

<?php  foreach($list as $ak) { ?>

	<div><?php echo $form->checkbox('akID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAKIDs), array('style' => 'vertical-align: middle'))?> <?php echo $ak->getAttributeKeyDisplayHandle()?></div>
	
<?php  } ?>
<input type="hidden" name="akCategoryHandle" value="<?php echo $_REQUEST['akCategoryHandle']; ?>" />
<br/><br/>
<?php 
$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Save'), 'ccm_submitCustomizeSearchColumnsForm()', 'left');
print $b1;
?>

</form>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	ccm_deactivateSearchResults();
	$("#ccm-new-object-customize-search-columns-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		$("#ccm-new-object-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp);
		});
	});
	$("#ccm-new-object-advanced-search").submit();
}

$(function() {
	$('#ccm-file-customize-search-columns-form').submit(function() {
		ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>
