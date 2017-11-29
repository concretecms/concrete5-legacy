<?php defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
Loader::model('attribute/categories/collection');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}


Loader::model('page_list');
$selectedAKIDs = array();

$fldc = PageSearchColumnSet::getCurrent();
$fldca = new PageSearchAvailableColumnSet();


$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
if ($_POST['task'] == 'update_columns') {
	
	$fdc = new PageSearchColumnSet();
	foreach($_POST['column'] as $key) {
		$fdc->addColumn($fldca->getColumnByKey($key));
	}	
	$sortCol = $fldca->getColumnByKey($_POST['fSearchDefaultSort']);
	$fdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
	$u->saveConfig('PAGE_LIST_DEFAULT_COLUMNS', serialize($fdc));
	
	$pageList = new PageList();
	$pageList->resetSearchRequest();
	exit;
}

$list = CollectionAttributeKey::getList();

?>
<div class="ccm-ui">

<form method="post" id="ccm-<?php echo $searchInstance?>-customize-search-columns-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/customize_search_columns/">
<?php echo $form->hidden('task', 'update_columns')?>

	<h3><?php echo t('Choose Headers')?></h3>
	
	<div class="clearfix">
	<label><?php echo t('Standard Properties')?></label>
	<div class="input">
	<ul class="inputs-list">
	
	<?php
	$columns = $fldca->getColumns();
	foreach($columns as $col) { 

		?>

		<li><label><?php echo $form->checkbox($col->getColumnKey(), 1, $fldc->contains($col))?> <span><?php echo $col->getColumnName()?></span></label></li>
	
	<?php } ?>
	
	</ul>
	</div>
	</div>

	<div class="clearfix">
	<label><?php echo t('Additional Attributes')?></label>
	<div class="input">
	<ul class="inputs-list">
	
	<?php foreach($list as $ak) { ?>

		<li><label><?php echo $form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $fldc->contains($ak))?> <span><?php echo $ak->getAttributeKeyDisplayName()?></span></label></li>
	
	<?php } ?>
	
	</ul>
	</div>
	</div>
	
	<h3><?php echo t('Column Order')?></h3>
	
	<p><?php echo t('Click and drag to change column order.')?></p>
	
	<ul class="ccm-search-sortable-column-wrapper" id="ccm-<?php echo $searchInstance?>-sortable-column-wrapper">
	<?php foreach($fldc->getColumns() as $col) { ?>
		<li id="field_<?php echo $col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?php echo $col->getColumnKey()?>" /><?php echo $col->getColumnName()?></li>	
	<?php } ?>	
	</ul>
	
	<br/>
	
	<h3><?php echo t('Sort By')?></h3>
	
	<div class="ccm-sortable-column-sort-controls">
	
	<?php $ds = $fldc->getDefaultSortColumn(); ?>
	
	<select <?php if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<?php } ?> id="ccm-<?php echo $searchInstance?>-sortable-column-default" name="fSearchDefaultSort">
	<?php foreach($fldc->getSortableColumns() as $col) { ?>
		<option id="opt_<?php echo $col->getColumnKey()?>" value="<?php echo $col->getColumnKey()?>" <?php if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="true" <?php } ?>><?php echo $col->getColumnName()?></option>
	<?php } ?>	
	</select>
	<select <?php if (count($fldc->getSortableColumns()) == 0) { ?>disabled="true"<?php } ?> id="ccm-<?php echo $searchInstance?>-sortable-column-default-direction" name="fSearchDefaultSortDirection">
		<option value="asc" <?php if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <?php } ?>><?php echo t('Ascending')?></option>
		<option value="desc" <?php if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <?php } ?>><?php echo t('Descending')?></option>	
	</select>	
	</div>

	<div class="dialog-buttons">
	<input type="button" class="btn primary" onclick="$('#ccm-<?php echo $searchInstance?>-customize-search-columns-form').submit()" value="<?php echo t('Save')?>" />
	</div>

</form>
</div>

<script type="text/javascript">
ccm_submitCustomizeSearchColumnsForm = function() {
	//ccm_deactivateSearchResults('<?php echo $searchInstance?>');
	$("#ccm-<?php echo $searchInstance?>-customize-search-columns-form").ajaxSubmit(function(resp) {
		var sortDirection = $("#ccm-<?php echo $searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSortDirection]").val();
		var sortCol = $("#ccm-<?php echo $searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSort]").val();
		$("#ccm-<?php echo $searchInstance?>-advanced-search input[name=ccm_order_dir]").val(sortDirection);
		$("#ccm-<?php echo $searchInstance?>-advanced-search input[name=ccm_order_by]").val(sortCol);
		jQuery.fn.dialog.closeTop();
		$("#ccm-<?php echo $searchInstance?>-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, '<?php echo $searchInstance?>');
		});
	});
	return false;
}

$(function() {
	$('#ccm-<?php echo $searchInstance?>-sortable-column-wrapper').sortable({
		cursor: 'move',
		opacity: 0.5
	});
	$('form#ccm-<?php echo $searchInstance?>-customize-search-columns-form input[type=checkbox]').click(function() {
		var thisLabel = $(this).parent().find('span').html();
		var thisID = $(this).attr('id');
		if ($(this).prop('checked')) {
			if ($('#field_' + thisID).length == 0) {
				$('#ccm-<?php echo $searchInstance?>-sortable-column-default').append('<option value="' + thisID + '" id="opt_' + thisID + '">' + thisLabel + '<\/option>');
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', false);
				$('#ccm-<?php echo $searchInstance?>-sortable-column-wrapper').append('<li id="field_' + thisID + '"><input type="hidden" name="column[]" value="' + thisID + '" />' + thisLabel + '<\/li>');
			}
		} else {
			$('#field_' + thisID).remove();
			$('#opt_' + thisID).remove();
			if ($('#ccm-<?php echo $searchInstance?>-sortable-column-wrapper li').length == 0) {
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', true);
			}
		}
	});
	$('#ccm-<?php echo $searchInstance?>-customize-search-columns-form').submit(function() {
		return ccm_submitCustomizeSearchColumnsForm();
	});
});


</script>