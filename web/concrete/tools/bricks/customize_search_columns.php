<?php defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
if($u->isRegistered()) {
	$form = Loader::helper('form');
	
	if(!$akCategoryHandle) $akCategoryHandle = $_REQUEST['akCategoryHandle'];
	
	$selectedAKIDs = array();
	$searchInstance = $_REQUEST['searchInstance'];
	if($_REQUEST['persistantBID'] != 'undefined') $persistantBID = $_REQUEST['persistantBID'];
	if(!$searchInstance) $searchInstance = 'block'.$persistantBID;
	
	$akcsh = Loader::helper('attribute_key_category_settings');
	$rs = $akcsh->getRegisteredSettings($akCategoryHandle);		
	
	Loader::model('attribute_key_category_item_list');
	$akcdca = new AttributeKeyCategoryAvailableColumnSet($akCategoryHandle);
	if ($_POST['task'] == 'update_columns') {
		$akcdc = new AttributeKeyCategoryColumnSet($akCategoryHandle);
		if(is_array($_POST['column'])) {
			
			foreach($_POST['column'] as $key) {
				$akcdc->addColumn($akcdca->getColumnByKey($key));
			}
			$sortCol = $akcdca->getColumnByKey($_POST['fSearchDefaultSort']);
			$akcdc->setDefaultSortColumn($sortCol, $_POST['fSearchDefaultSortDirection']);
			
			
			if($persistantBID) {
				$db = Loader::db();
				$exists = $db->GetOne('SELECT columns FROM BricksCustomColumns WHERE searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array($searchInstance, $u->getUserID(), $akCategoryHandle));
				if($exists) {
					$db->Execute('UPDATE BricksCustomColumns SET columns = ? WHERE  searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array(urlencode(serialize($akcdc)), $searchInstance, $u->getUserID(), $akCategoryHandle));
				} else {
					$db->Execute('INSERT INTO BricksCustomColumns (searchInstance, uID, akCategoryHandle, columns) values (?,?,?,?)', array($searchInstance, $u->getUserID(), $akCategoryHandle, urlencode(serialize($akcdc))));
				}
			} else {
				$u->saveConfig(strtoupper($akCategoryHandle).'_LIST_DEFAULT_COLUMNS', serialize($akcdc));
			}
		}
		
		$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
		$list = $akc->getItemList();
		$list->resetSearchRequest();
		exit;
	}
	$db = Loader::db();
	$exists = $db->GetOne('SELECT columns FROM BricksCustomColumns WHERE searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array($searchInstance, $u->getUserID(), $akCategoryHandle));
	if($exists) {
		$columns = unserialize(urldecode($exists));
	} else {
		if(!$_REQUEST['columns'] || $_REQUEST['columns'] == 'undefined') {
			$columns = AttributeKeyCategoryColumnSet::getCurrent($akCategoryHandle);
		} else {
			$columns = unserialize(urldecode($_REQUEST['columns']));
			if(is_string($columns)) $columns = unserialize($columns);
		}
	}
	$list = AttributeKey::getList($akCategoryHandle);
	?>
	<form method="post" id="ccm-<?=$searchInstance?>-customize-search-columns-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/bricks/customize_search_columns/">
	<?=$form->hidden('task', 'update_columns')?>
	<?=$form->hidden('akCategoryHandle', $akCategoryHandle)?>
	<?=$form->hidden('searchInstance', $searchInstance)?>
	<?=$form->hidden('persistantBID', $persistantBID)?>
	
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="35%" valign="top">
		<h1><?=t('Choose Headers')?></h1>
		
		<?  
		if(is_array($rs['standard_properties'])){
		?>
		<h2><?=t('Standard Properties')?></h2>
		
		<? 
			foreach($rs['standard_properties'] as $sp) { ?>
			
			<div><?=$form->checkbox($col->getColumnKey(), 1, $columns->contains($col), array('style' => 'vertical-align: middle'))?> <label for="<?=$col->getColumnKey()?>"><?=$col->getColumnName()?></label></div>
		
		<?
			} 
		
		}
		?>
		
		<h2><?=t('Additional Attributes')?></h2>
		
		<? foreach($list as $ak) { ?>
		
			<div><?=$form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $columns->contains($ak), array('style' => 'vertical-align: middle'))?> <label for="ak_<?=$ak->getAttributeKeyHandle()?>"><?=$ak->getAttributeKeyDisplayHandle()?></label></div>
			
		<? } ?>
		
		</td>
		<td><div style="width: 20px">&nbsp;</div></td>
		<td valign="top" width="65%">
		
		<h1><?=t('Column Order')?></h1>
		
		<p><?=t('Click and drag to change column order.')?></p>
		
		<ul class="ccm-search-sortable-column-wrapper" id="ccm-<?=$searchInstance?>-sortable-column-wrapper">
		<? if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
			<li id="field_<?=$col->getColumnKey()?>"><input type="hidden" name="column[]" value="<?=$col->getColumnKey()?>" /><?=$col->getColumnName()?></li>	
		<? } ?>	
		</ul>
		
		<h1><?=t('Sort By')?></h1>
		
		<div class="ccm-sortable-column-sort-controls">
		<?
		$h = Loader::helper('concrete/interface');
		$b1 = $h->submit(t('Save'), 'save', 'right');
		print $b1;
		?>
	
		
		<? $ds = $columns->getDefaultSortColumn(); ?>
		<select <? if (count($columns->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?=$searchInstance?>-sortable-column-default" name="fSearchDefaultSort">
		<? if(is_array($columns->getSortableColumns())) foreach($columns->getSortableColumns() as $col) { ?>
			<option id="opt_<?=$col->getColumnKey()?>" value="<?=$col->getColumnKey()?>" <? if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="selected" <? } ?>><?=$col->getColumnName()?></option>
		<? } ?>	
		</select>
		<select <? if (count($columns->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?=$searchInstance?>-sortable-column-default-direction" name="fSearchDefaultSortDirection">
			<option value="asc" <? if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <? } ?>><?=t('Ascending')?></option>
			<option value="desc" <? if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <? } ?>><?=t('Descending')?></option>	
		</select>	
		</div>
		
		</td>
	</tr>
	</table>
	
	</form>
	<script type="text/javascript">
	ccm_submitCustomizeSearchColumnsForm = function() {
		//ccm_deactivateSearchResults('<?=$searchInstance?>');
		$("#ccm-<?=$searchInstance?>-customize-search-columns-form").ajaxSubmit(function(resp) {
			var sortDirection = $("#ccm-<?=$searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSortDirection]").val();
			var sortCol = $("#ccm-<?=$searchInstance?>-customize-search-columns-form select[name=fSearchDefaultSort]").val();
			$("#ccm-<?=$searchInstance?>-advanced-search input[name=ccm_order_dir]").val(sortDirection);
			$("#ccm-<?=$searchInstance?>-advanced-search input[name=ccm_order_by]").val(sortCol);
			jQuery.fn.dialog.closeTop();
			$("#<?=$searchInstance?>_form").submit();
		});
		return false;
	}
	
	$(function() {
		$('#ccm-<?=$searchInstance?>-sortable-column-wrapper').sortable({
			cursor: 'move',
			opacity: 0.5
		});
		$('form#ccm-<?=$searchInstance?>-customize-search-columns-form input[type=checkbox]').click(function() {
			var thisLabel = $(this).parent().find('label').html();
			var thisID = $(this).attr('id');
			if ($(this).prop('checked')) {
				if ($('#field_' + thisID).length == 0) {
					$('#ccm-<?=$searchInstance?>-sortable-column-default').append('<option value="' + thisID + '" id="opt_' + thisID + '">' + thisLabel + '<\/option>');
					$('div.ccm-sortable-column-sort-controls select').attr('disabled', false);
					$('#ccm-<?=$searchInstance?>-sortable-column-wrapper').append('<li id="field_' + thisID + '"><input type="hidden" name="column[]" value="' + thisID + '" />' + thisLabel + '<\/li>');
				}
			} else {
				$('#field_' + thisID).remove();
				$('#opt_' + thisID).remove();
				if ($('#ccm-<?=$searchInstance?>-sortable-column-wrapper li').length == 0) {
					$('div.ccm-sortable-column-sort-controls select').attr('disabled', true);
				}
			}
		});
		$('#ccm-<?=$searchInstance?>-customize-search-columns-form').submit(function() {
			return ccm_submitCustomizeSearchColumnsForm();
		});
	});
	</script>
<?php } else { ?>
<p>You must be logged in to use this feature.</p>
<?php } ?>