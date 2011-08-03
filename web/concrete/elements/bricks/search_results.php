<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
if(!$akCategoryHandle) $akCategoryHandle = $_REQUEST['akCategoryHandle'];
$searchInstance = $akCategoryHandle.time();
if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = $_REQUEST['searchInstance'];
}

$cnt = Loader::controller('/dashboard/bricks/search');
$newObjectList = $cnt->getRequestedSearchResults($akCategoryHandle);
$newObjects = $newObjectList->getPage();
$pagination = $newObjectList->getPagination();

Loader::model('attribute_key_category_item_list');
$akccs = new AttributeKeyCategoryColumnSet($akCategoryHandle);
$columns = $akccs->getCurrent();
?>

<div id="ccm-list-wrapper">
	<?php 
	if (!$mode) {
		$mode = $_REQUEST['mode'];
	}
	
	$soargs = array();
	$soargs['mode'] = $mode;
	$soargs['akCategoryHandle'] = $akCategoryHandle;
	$soargs['searchInstance'] = $searchInstance;

	?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="100%"><?php echo $newObjectList->displaySummary();?></td>
			<td style="white-space: nowrap"><?php echo t('With Selected: ')?>&nbsp;</td>
			<td align="right"><select id="ccm-<?=$searchInstance?>-list-multiple-operations<?php if($akID){ print "-".$akID; }?>" akCategoryHandle="<?php echo $akCategoryHandle; ?>" disabled>
					<option value="">**</option>
                    <?php if(!$mode) {?>
					<option value="properties"><?php echo t('Edit Properties')?></option>
					<option value="delete"><?php echo t('Delete')?></option>
                    <?php } ?>
					<?php  if ($mode == 'choose_multiple') { ?>
					<option value="choose"><?php echo t('Choose')?></option>
					<?php  } ?>
				</select></td>
		</tr>
	</table>
	<?php 
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/search_results';
	if (count($newObjects) > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-<?=$searchInstance?>-list" class="ccm-results-list">
		<tr>
			<th width="20px"><input id="ccm-<?=$searchInstance?>-list-cb-all" type="checkbox" /></th>
	<?php 
		if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
			<th class="<?=$newObjectList->getSearchResultsClass($col->getColumnKey())?>">
            	<?php if($col->isColumnSortable()) {?>
				<a href="<?=$newObjectList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?=$col->getColumnName()?></a>
                <?php } else { ?>
                <?=$col->getColumnName()?>
                <?php } ?>
			</th>
		<?php } ?>
			<th width="20px" class="ccm-search-add-column-header">
				<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED.'/bricks/customize_search_columns?akCategoryHandle='.$akCategoryHandle.'&searchInstance='.$searchInstance;?>" id="ccm-search-add-column">
					<img src="<?php echo ASSETS_URL_IMAGES?>/icons/column_preferences.png" width="16" height="16" />
				</a>
			</th>
		</tr>
		<?php 
		foreach($newObjects as $item) { 
			$ID = $item->ID;
			if(!$ID) {
				eval('$ID = $item->'.substr($akCategoryHandle, 0, 1).'ID;');
			}
			$action = "location.href='".View::url('/dashboard/bricks/edit/', $akCategoryHandle, $ID)."'";
			
			if ($mode == 'choose_one' || $mode == 'choose_multiple') {
				$action = 'ccm_triggerSelectAttributeKeyCategoryItem('.$akID.', $(this).parent()); jQuery.fn.dialog.closeTop();';
			}
			
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		<tr class="ccm-list-record <?php echo $striped?>">
			<td class="ccm-<?=$searchInstance?>-list-cb" style="vertical-align: middle !important">
				<input type="checkbox" 
					value="<?php echo $ID?>" />
			</td>
			<?php 
			if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
			<td onclick="<?=$action;?>"><?=$col->getColumnValue($item)?></td>
			<?php } ?>
			<td>&nbsp;</td>
		</tr>
		<?php 
		}

	?>
	</table>
	<?php  } else { ?>
	<div id="ccm-list-none"><?php echo t('No items found.')?></div>
	<?php  } 
	$newObjectList->displayPaging($bu, false, $soargs);?>
</div>
<script type="text/javascript">
	ccm_setupAttributeKeyCategoryItemSearch(
		'<?=$searchInstance?>'<?php if($_REQUEST['akID']){?>, 
		<?=$_REQUEST['akID']?><?php }?>
	);
</script>