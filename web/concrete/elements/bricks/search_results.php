<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
if(!$akCategoryHandle) $akCategoryHandle = $_REQUEST['akCategoryHandle'];
if(!$searchInstance) $searchInstance = $akCategoryHandle.time();
if(isset($_REQUEST['searchInstance'])) $searchInstance = $_REQUEST['searchInstance'];
$u = new User();
?>

<div id="ccm-<?=$searchInstance?>-search-results">
<?php try {
if(isset($_REQUEST['administrationDisabled'])) $administrationDisabled = $_REQUEST['administrationDisabled'];
if(isset($_REQUEST['userDefinedColumnsDisabled'])) $userDefinedColumnsDisabled = $_REQUEST['userDefinedColumnsDisabled'];
if(isset($_REQUEST['persistantBID'])) $persistantBID = $_REQUEST['persistantBID'];
if(isset($_REQUEST['fieldName'])) $fieldName = $_REQUEST['fieldName'];

Loader::model('attribute_key_category_item_list');
$akccs = new AttributeKeyCategoryColumnSet($akCategoryHandle);
$db = Loader::db();
$exists = $db->GetOne('SELECT columns FROM BricksCustomColumns WHERE searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array($searchInstance, $u->getUserID(), $akCategoryHandle));
if($exists) {
	$columns = unserialize(urldecode($exists));
} elseif(isset($_REQUEST['defaults'])) {
	$defaults = unserialize(urldecode($_REQUEST['defaults']));
	$columns = $defaults['columns'];
} else if(isset($_REQUEST['defaults_'.$searchInstance])) {
	$defaults = unserialize(urldecode($_REQUEST['defaults_'.$searchInstance]));
	$columns = $defaults['columns'];
}
if(is_string($columns)) $columns = unserialize(urldecode($columns));
if(!$columns) $columns = $akccs->getCurrent();

if($defaults) {
	$onLeftClick = $defaults['onLeftClick'];
	$onRightClick = $defaults['onRightClick'];
}

$akcdca = new AttributeKeyCategoryAvailableColumnSet($akCategoryHandle);
$ak = new AttributeKey($akCategoryHandle);
$fieldAttributes = $ak->getSearchableList($akCategoryHandle);
foreach($fieldAttributes as $ak) {
	$akcdca->addColumn($akcdca->getColumnByKey('ak_'.$ak->getAttributeKeyHandle()));
}

$cnt = Loader::controller('/dashboard/bricks/search');
if(is_object($columns)) $sortBy = $columns->getDefaultSortColumn();
$newObjectList = $cnt->getRequestedSearchResults($akCategoryHandle, $sortBy);
$newObjects = $newObjectList->getPage();
$pagination = $newObjectList->getPagination();
?>
	<div id="ccm-list-wrapper">
		<?php 
		if (!$mode) {
			$mode = $_REQUEST['mode'];
		} 
		if($administrationDisabled) {
			$canAdmin = FALSE;
		} else {
			Loader::model('attribute_key_category_item_permission');
			$akcip = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
			$canAdmin = $akcip->canAdmin();
		}
		if($mode == 'block') {
			$canAdmin = FALSE;
			$userDefinedColumnsDisabled = TRUE;
		}
		$soargs = array();
		if($mode) $soargs['mode'] = $mode;
		if($akCategoryHandle) $soargs['akCategoryHandle'] = $akCategoryHandle;
		if($searchInstance) $soargs['searchInstance'] = $searchInstance;
		if($administrationDisabled) $soargs['administrationDisabled'] = $administrationDisabled;
		if($userDefinedColumnsDisabled) $soargs['userDefinedColumnsDisabled'] = $userDefinedColumnsDisabled;
		if($fieldName) $soargs['fieldName'] = $fieldName;
		if($persistantBID) $soargs['persistantBID'] = $persistantBID;
		?>
		
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="100%"><?php echo $newObjectList->displaySummary();?></td>
				<?php if($canAdmin) { ?>
				<td style="white-space: nowrap"><?php echo t('With Selected: ')?>&nbsp;</td>
				<td align="right"><select id="ccm-<?=$searchInstance?>-list-multiple-operations" akCategoryHandle="<?php echo $akCategoryHandle; ?>" disabled>
						<option value="">**</option>
						<?php if(!$mode) {?>
						<option value="properties"><?php echo t('Edit Properties')?></option>
						<option value="delete"><?php echo t('Delete')?></option>
						<?php } ?>
						<?php  if ($mode == 'choose_multiple') { ?>
						<option value="choose"><?php echo t('Choose')?></option>
						<?php  } ?>
					</select></td>
				<?php } ?>
			</tr>
		</table>
		<?php
		$txt = Loader::helper('text');
		$keywords = $_REQUEST['keywords'];
		$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/search_results';
		if (count($newObjects) > 0) { ?>
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-<?=$searchInstance?>-list" class="ccm-results-list">
			<tr><?php if($canAdmin) { ?>
				<th width="20px"><input id="ccm-<?=$searchInstance?>-list-cb-all" type="checkbox" /></th>
		<?php }
			if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
				<th class="<?=$newObjectList->getSearchResultsClass($col->getColumnKey())?>">
					<?php if($col->isColumnSortable()) {?>
					<a href="<?=$newObjectList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?=$col->getColumnName()?></a>
					<?php } else { ?>
					<?=$col->getColumnName()?>
					<?php } ?>
				</th>
			<?php } if(!$userDefinedColumnsDisabled && $u->isRegistered()) { ?>
				<th width="20px" class="ccm-search-add-column-header">
					<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/bricks/customize_search_columns?akCategoryHandle=<?=$akCategoryHandle?>&searchInstance=<?=$searchInstance?>" id="ccm-search-<?=$searchInstance?>-add-column">
						<img src="<?php echo ASSETS_URL_IMAGES?>/icons/column_preferences.png" width="16" height="16" />
					</a>
				</th>
			<?php } ?>
			</tr>
			<?php 
			foreach($newObjects as $item) { 
				$ID = $item->ID;
				if(!$ID) {
					$pkg = AttributeKeyCategory::getByHandle($akCategoryHandle)->getPackageHandle();
					if(method_exists($item, 'getID')) {
						$ID = $item->getID();
					} elseif(method_exists($item, 'get'.$txt->camelcase($akCategoryHandle).'ID')) {
						$pkg = AttributeKeyCategory::getByHandle($akCategoryHandle)->getPackageHandle();
						$txt = Loader::helper('text');
						eval('$ID = $item->get'.$txt->camelcase($akCategoryHandle).'ID();');
					} elseif(method_exists($item, 'get'.$txt->camelcase(str_replace($pkg.'_', '', $akCategoryHandle)).'ID')) {
						eval('$ID = $item->get'.$txt->camelcase(str_replace($pkg.'_', '', $akCategoryHandle)).'ID();');
					}
				}
				
				if ($mode == 'choose_one' || $mode == 'choose_multiple') {
					$onLeftClick = 'ccm_triggerSelectAttributeKeyCategoryItem('.$akID.', $(this).parent()); jQuery.fn.dialog.closeTop();';
				}
				
				if (!isset($striped) || $striped == 'ccm-list-record-alt') {
					$striped = '';
				} else if ($striped == '') { 
					$striped = 'ccm-list-record-alt';
				}
	
				?>
			<tr class="ccm-list-record <?php echo $striped?>"<?php if($fieldName) {?> fieldName="<?=$fieldName?>"<?php } ?>>
				<?php if($canAdmin) { ?>
				<td class="ccm-<?=$searchInstance?>-list-cb" style="vertical-align: middle !important">
				<?php foreach($akcdca->getColumns() as $aCol) { ?>
					<input type="hidden" name="<?=$aCol->getColumnKey()?>" value="<?=urlencode($aCol->getColumnValue($item))?>" />
				<?php } ?>
					<input type="checkbox" name="ID" value="<?=$ID?>" />
				</td>
				<?php }
				if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
				<td class="ccm-onclick-effect"><?=$col->getColumnValue($item)?></td>
				<?php } 
				if(!$userDefinedColumnsDisabled && $u->isRegistered()) { ?>
				<td class="ccm-onclick-effect">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php 
			}
	
		?>
		</table>
	<?php if($onLeftClick) { ?>
		<script type="text/javascript">
			$('#ccm-<?=$searchInstance?>-list td.ccm-onclick-effect').live('click', function () {
				<?=$onLeftClick;?>
			});
		</script>
	<?php } ?>
	<?php if($onRightClick) { ?>
		<script type="text/javascript">
			$('#ccm-<?=$searchInstance?>-list td.ccm-onclick-effect').live("contextmenu",function(e){
				e.preventDefault();
				<?=$onRightClick;?>
				$(this).addClass('selected');
			});
		</script>
	<?php } ?>
		<?php  } else { ?>
		<div id="ccm-list-none"><?php echo t('No items found.')?></div>
		<?php  } 
		$newObjectList->displayPaging($bu, false, $soargs);?>
	</div>

<script type="text/javascript"><?php if(!$onLeftClick && $canAdmin) { ?>
	$('#ccm-<?=$searchInstance?>-list tr')
		.filter(':has(:checkbox:checked)')
		.end()
		.find('input[type=checkbox]')
		.click(function(event) {
			if($(this).is(':checked')) {
				$(this).parent().parent().addClass('selected');
			} else {
				$(this).parent().parent().removeClass('selected');
			}
		}
	);
	$('#ccm-<?=$searchInstance?>-list tr')
		.filter(':has(:checkbox:checked)')
		.end()
		.click(function(event) {
			if(event.target.type !== 'checkbox') {
				if($(this).find('input[type=checkbox]').is(':checked')) {
					$(this).find('input[type=checkbox]').removeAttr('checked');
				} else {
					$(this).find('input[type=checkbox]').attr('checked', 'checked');
				}
				$(this).find('input[type=checkbox]').trigger('click');
				if($(this).find('input[type=checkbox]').is(':checked')) {
					$(this).find('input[type=checkbox]').removeAttr('checked');
				} else {
					$(this).find('input[type=checkbox]').attr('checked', 'checked');
				}
			}
		}
	);
	<?php } ?>
	ccm_setupAttributeKeyCategoryItemSearch('<?=$searchInstance?>');
</script>
<?php } catch (Exception $e) { ?>
<h2><span style="color:red">Error</span></h2>
<p><?=$e->getMessage()?></p>
<?php } ?>
</div>