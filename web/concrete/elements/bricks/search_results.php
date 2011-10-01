<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
if(empty($akCategoryHandle)) $akCategoryHandle = $_REQUEST['akCategoryHandle'];
if(empty($searchInstance)) $searchInstance = $akCategoryHandle;
if(empty($baseId)) $baseId = $searchInstance;

if(isset($_REQUEST['searchInstance'])) $searchInstance = $_REQUEST['searchInstance'];
if(isset($_REQUEST['baseId'])) $baseId = $_REQUEST['baseId'];

$wrapId = $baseId.'_results';

$u = new User();


?>

<div id="<?php echo $wrapId ?>" class="ccm-list-wrapper ccm-akc-search">

<?php try {	


Loader::model('attribute_key_category_item_list');
$akccs = new AttributeKeyCategoryColumnSet($akCategoryHandle);
$db = Loader::db();
$exists = $db->GetOne('SELECT columns FROM BricksCustomColumns WHERE searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array($searchInstance, 0, $akCategoryHandle));
if($exists) {
	$defaults = unserialize(urldecode($exists));
	$columns = $defaults['columns'];
}
$exists = $db->GetOne('SELECT columns FROM BricksCustomColumns WHERE searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array($searchInstance, $u->getUserID(), $akCategoryHandle));
if($exists) {
	$defaults = unserialize(urldecode($exists));
	$columns = $defaults['columns'];
} elseif(isset($_REQUEST['defaults'])) {
	$defaults = unserialize(urldecode($_REQUEST['defaults']));
	$columns = $defaults['columns'];
} elseif(isset($_REQUEST['defaults_'.$searchInstance])) {
	$defaults = unserialize(urldecode($_REQUEST['defaults_'.$searchInstance]));
	$columns = $defaults['columns'];
}
	
if($defaults) $administrationDisabled = $defaults['administrationDisabled'];
if(isset($_REQUEST['administrationDisabled'])) $administrationDisabled = $_REQUEST['administrationDisabled'];
if($defaults) $userDefinedColumnsDisabled = $defaults['userDefinedColumnsDisabled'];
if(isset($_REQUEST['userDefinedColumnsDisabled'])) $userDefinedColumnsDisabled = $_REQUEST['userDefinedColumnsDisabled'];
if(isset($_REQUEST['persistantBID'])) $persistantBID = $_REQUEST['persistantBID'];
if(isset($_REQUEST['fieldName'])) $fieldName = $_REQUEST['fieldName'];
if(is_string($columns)) $columns = unserialize(urldecode($columns));
if(!$columns) $columns = $akccs->getCurrent();

if($defaults) {
	$onLeftClick = $defaults['onLeftClick'];
	$onRightClick = $defaults['onRightClick'];
	$onDoubleClick = $defaults['onDoubleClick'];
}
$akcdca = new AttributeKeyCategoryAvailableColumnSet($akCategoryHandle);
$ak = new AttributeKey($akCategoryHandle);
$fieldAttributes = $ak->getSearchableList($akCategoryHandle);
foreach($fieldAttributes as $ak) {
	$akcdca->addColumn($akcdca->getColumnByKey('ak_'.$ak->getAttributeKeyHandle()));
}
if(!$sortBy && is_object($columns)) $sortBy = $columns->getDefaultSortColumn();
$cnt = Loader::controller('/dashboard/bricks/search');
$newObjectList = $cnt->getRequestedSearchResults($akCategoryHandle, $sortBy);

//print_r($newObjectList->getSearchRequest());
//$newObjectList->debug(TRUE);

//Make sure we're sorting correctly
if(isset($_REQUEST['ccm_order_by'])){
	$sortBy = $akcdca->getColumnByKey($_REQUEST['ccm_order_by']);
}

if(isset($_REQUEST['ccm_order_dir'])){
	$sortDir =$_REQUEST['ccm_order_dir'];
}else{
	$sortDir = $sortBy->getColumnDefaultSortDirection();	
}
//Not sure why the DatabaseItemList we get from the controller isn't already sorted correctly. Seems to work fine for Collection, User, and File.
//TODO: Need to figure out why the 
$newObjectList->sortBy($sortBy->getColumnKey(), $sortDir);



$newObjects = $newObjectList->getPage();

$pagination = $newObjectList->getPagination();
?>
	
		<?php 
		if (!isset($mode) || empty($mode)) {		
			$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'admin';			
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
		if($fieldName) $soargs['fieldName'] = $fieldName;
		?>
		
		<?php if (count($newObjects) > 0) { ?>
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="100%"><?php echo $newObjectList->displaySummary();?></td>
				<?php if($canAdmin) { ?>
				<td style="white-space: nowrap"><?php echo t('With Selected: ')?>&nbsp;</td>
				<td align="right"><select id="<?php echo $wrapId ?>-list-multiple-operations" data-akCategoryHandle="<?php echo $akCategoryHandle; ?>" disabled>
						<option value="">**</option>
						<?php if($mode == 'admin') {?>
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
		?>
		<table border="0" cellspacing="0" cellpadding="0" id="<?php echo $wrapId ?>-list" class="ccm-results-list">
			<tr>
				<th width="20px"<?php if(!$canAdmin) { ?> style="display:none"<?php } ?>>
					<input id="<?php echo $wrapId ?>-list-cb-all" type="checkbox" />
				</th>
		<?php
			if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
				<th class="<?php echo $newObjectList->getSearchResultsClass($col->getColumnKey())?>">
					<?php if($col->isColumnSortable()) {?>
					<a href="<?php echo $newObjectList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?php echo $col->getColumnName()?></a>
					<?php } else { ?>
					<?php echo $col->getColumnName()?>
					<?php } ?>
				</th>
			<?php } if(!$userDefinedColumnsDisabled && $u->isRegistered()) { ?>
				<th width="20" class="ccm-search-add-column-header">
					<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/bricks/customize_search_columns?akCategoryHandle=<?php echo $akCategoryHandle?>&searchInstance=<?php echo $searchInstance?>" class="ccm-search-add-column" id="ccm-search-<?php echo $searchInstance?>-add-column">
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
					$onLeftClick = 'ccm_triggerSelectAttributeKeyCategoryItem('.$akCategoryHandle.', $(this).closest("tr")); jQuery.fn.dialog.closeTop();';
				}
				
				if (!isset($striped) || $striped == 'ccm-list-record-alt') {
					$striped = '';
				} else if ($striped == '') { 
					$striped = 'ccm-list-record-alt';
				}
	
				?>
			<tr class="ccm-list-record <?php echo $striped ?>"<?php if($fieldName) {?> data-fieldName="<?php echo $fieldName ?>"<?php } ?>>
				<td class="<?php echo $wrapId ?>-list-cb" style="<?php if(!$canAdmin) { ?> display:none;<?php } ?>">
				<?php foreach($akcdca->getColumns() as $aCol) { ?>
					<input type="hidden" name="<?php echo $aCol->getColumnKey()?>" value="<?php echo urlencode($aCol->getColumnValue($item))?>" />
				<?php } ?>
					<input type="checkbox" name="ID" value="<?php echo $ID?>" />
				</td>
				<?php 
				if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
				<td class="ccm-onclick-effect"><?php echo $col->getColumnValue($item)?></td>
				<?php } 
				if(!$userDefinedColumnsDisabled && $u->isRegistered()) { ?>
				<td class="ccm-onclick-effect">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php 
			}
	
		?>
		</table>
		<script type="text/javascript">/*
			ccm_setupAttributeKeyCategoryItemSearch("<?php echo $searchInstance?>");
			if(typeof beendone === 'undefined') beendone = Array();
			if(typeof beendone["<?php echo $searchInstance?>"] === 'undefined') {
				beendone["<?php echo $searchInstance?>"] = Array();
				beendone["<?php echo $searchInstance?>"]["leftClick"] = 0;
				beendone["<?php echo $searchInstance?>"]["dblClick"] = 0;
				beendone["<?php echo $searchInstance?>"]["rightClick"] = 0;
			}*/
		</script>
	<?php if($onLeftClick) { ?>
		<script type="text/javascript">
			/*if(beendone['<?php echo $searchInstance?>']['leftClick'] == 0) {
				$('#<?php echo $searchInstance ?>-list td.ccm-onclick-effect').live('click', function () {
					<?php echo $onLeftClick;?>
				});
				beendone['<?php echo $searchInstance?>']['leftClick']++;
			}*/
		</script>
	<?php } ?>
	<?php if($onDoubleClick) { ?>
		<script type="text/javascript">
			/*if(beendone['<?php echo $searchInstance?>']['dblClick'] == 0) {
				$('#<?php echo $searchInstance ?>-list td.ccm-onclick-effect').live('dblclick', function () {
					if (window.getSelection)
						window.getSelection().removeAllRanges();
					else if (document.selection)
						document.selection.empty();
					<?php echo $onDoubleClick;?>
				});
				beendone['<?php echo $searchInstance?>']['dblClick']++;
			}*/
		</script>
	<?php }?>
	<?php if($onRightClick) { ?>
		<script type="text/javascript">
			/*if(beendone['<?php echo $searchInstance?>']['rightClick'] == 0) {
				$('#<?php echo $searchInstance ?>-list td.ccm-onclick-effect').live("contextmenu",function(e){
					e.preventDefault();
					<?php echo $onRightClick;?>
					$(this).addClass('selected');
				});
				beendone['<?php echo $searchInstance?>']['rightClick']++;
			}*/
		</script>
		<script type="text/javascript">
		/*
		<?php if(!$onLeftClick && $canAdmin) { ?>
			$('#<?php echo $searchInstance ?>-list tr')
				.filter(':has(:checkbox:checked)')
				.end()
				.find('input[type=checkbox]')
				.unbind('click')
				.click(function(event) {
					if($(this).is(':checked')) {
						$(this).parent().parent().addClass('selected');
					} else {
						$(this).parent().parent().removeClass('selected');
					}
				}
			);
			$('#<?php echo $searchInstance ?>-list tr')
				.filter(':has(:checkbox:checked)')
				.end()
				.unbind('click')
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
			ccm_setupAttributeKeyCategoryItemSearch('<?php echo $searchInstance?>');
			*/
		</script>
	<?php } ?>
		<?php  } else { ?>
		<div id="ccm-list-none"><?php echo t('No items found.')?></div>
		<?php  } 
		$newObjectList->displayPaging($bu, false, $soargs);?>
	
    
    
    <?php
		//Prep the jsInitArgs
		$json = Loader::helper('json');
		
		$jsInitArgs['akcHandle'] = $akCategoryHandle;
		$jsInitArgs['mode'] = $mode;
		$jsInitArgs['searchInstance'] = $searchInstance;
		$jsInitArgs['baseId'] = $baseId;
		$jsInitArgsStr = $json->encode($jsInitArgs);
	?>
    
    <script type="text/javascript">
		$(function(){
			$("#<?php echo $wrapId ?>").ccm_akcItemSearchResults(<?php echo $jsInitArgsStr ?>);			
		});
	</script>

<?php } catch (Exception $e) { ?>
<h2><span style="color:red">Error</span></h2>
<p><?php echo $e->getMessage()?></p>
<?php } ?>

</div>