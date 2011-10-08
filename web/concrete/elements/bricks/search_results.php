<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

$wrapId = $baseId.'_results';

$u = new User();


if(!isset($itemActions)){
	$itemActions = array(
		'choose'=>t('Choose'),
		'properties'=>t('Edit Properties'),
		'delete'=>t('Delete')
	);
} 
if(!isset($bulkActions)){
	$bulkActions = array(
		'choose'=>t('Choose'),
		'properties'=>t('Edit Properties'),
		'delete'=>t('Delete')
	);
}
if(!isset($itemActionPerms)){
	$itemActionPerms = array(
		'choose'=>'read',
		'properties'=>'edit',
		'delete'=>'delete'
	);
}
foreach($itemActions as $label=>$action){
	if(!$akcip->can($itemActionPerms[$action])){
		unset($itemActions[$label]);	
	}
}
foreach($bulkActions as $label=>$action){
	if(!$akcip->can($itemActionPerms[$action])){
		unset($itemActions[$label]);	
	}
}

?>

<div id="<?php echo $wrapId ?>" class="ccm-list-wrapper ccm-akci-search">

<?php try {	

if(isset($_REQUEST['fieldName'])) $fieldName = $_REQUEST['fieldName'];


if($defaults) {
	$onLeftClick = $defaults['onLeftClick'];
	$onRightClick = $defaults['onRightClick'];
	$onDoubleClick = $defaults['onDoubleClick'];
}

$akcacs = new AttributeKeyCategoryAvailableColumnSet($akCategoryHandle);
//echo '<pre>';
//print_r($akcacs->getColumns());
/*$ak = new AttributeKey($akCategoryHandle);
$fieldAttributes = $ak->getSearchableList($akCategoryHandle);
foreach($fieldAttributes as $ak) {
	//$akcacs->addColumn($akcacs->getColumnByKey('ak_'.$ak->getAttributeKeyHandle()));
}*/

//print_r($akciList->getSearchRequest());
//$akciList->debug(TRUE);


if(!$sortBy) $sortBy = $akcacs->getDefaultSortColumn();

//Make sure we're sorting correctly
if(isset($_REQUEST['ccm_order_by'])){
	$sortBy = $akcacs->getColumnByKey($_REQUEST['ccm_order_by']);
}

if(isset($_REQUEST['ccm_order_dir'])){
	$sortDir =$_REQUEST['ccm_order_dir'];
}else{
	$sortDir = $sortBy->getColumnDefaultSortDirection();	
}

//Not sure why the DatabaseItemList we get from the controller isn't already sorted correctly. Seems to work fine for Collection, User, and File.
//TODO: Need to figure out why the 
$akciList->sortBy($sortBy->getColumnKey(), $sortDir);

$akciListPage = $akciList->getPage();

?>
	
		<?php 
		if (!isset($mode) || empty($mode)) {		
			$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'admin';			
		} 
		
		$soargs = array();	
		$soargs['akCategoryHandle'] = $akCategoryHandle;
		$soargs['searchInstance'] = $searchInstance;
		$soargs['baseId'] = $baseId;
		if($mode) $soargs['mode'] = $mode;
		if($fieldName) $soargs['fieldName'] = $fieldName;
		?>
		
		<?php if (count($akciListPage) > 0) { ?>
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="100%"><?php echo $akciList->displaySummary();?></td>
				<?php if(!empty($bulkActions)) { ?>
				<td style="white-space: nowrap"><?php echo t('With Selected: ')?>&nbsp;</td>
				<td align="right"><select id="<?php echo $wrapId ?>-list-multiple-operations" data-akCategoryHandle="<?php echo $akCategoryHandle; ?>" disabled>
						<option value="">**</option>
						<?php foreach($bulkActions as $action=>$label){
							echo "<option value=\"$action\">$label</option>";
						}?>
					</select></td>
				<?php } ?>
			</tr>
		</table>
		<?php
		$keywords = $_REQUEST['keywords'];
		$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/search_results';
		?>
		<table border="0" cellspacing="0" cellpadding="0" id="<?php echo $wrapId ?>-list" class="ccm-results-list">
			<tr>
				<th width="20">
					<?php if(!empty($bulkActions)){ ?>
					<input id="<?php echo $wrapId ?>-list-cb-all" type="checkbox" />
					<?php } ?>
				</th>
		<?php
			if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
				<th class="<?php echo $akciList->getSearchResultsClass($col->getColumnKey())?>">
					<?php if($col->isColumnSortable()) {?>
					<a href="<?php echo $akciList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?php echo $col->getColumnName()?></a>
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
			foreach($akciListPage as $item) { 
				$ID = $item->ID;
				if(!$ID) {
					$pkg = AttributeKeyCategory::getByHandle($akCategoryHandle)->getPackageHandle();
					if(method_exists($item, 'getID')) {
						$ID = $item->getID();
					} elseif(method_exists($item, 'get'.$text->camelcase($akCategoryHandle).'ID')) {
						$pkg = AttributeKeyCategory::getByHandle($akCategoryHandle)->getPackageHandle();
						eval('$ID = $item->get'.$text->camelcase($akCategoryHandle).'ID();');
					} elseif(method_exists($item, 'get'.$text->camelcase(str_replace($pkg.'_', '', $akCategoryHandle)).'ID')) {
						eval('$ID = $item->get'.$text->camelcase(str_replace($pkg.'_', '', $akCategoryHandle)).'ID();');
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
			<tr class="ccm-list-record <?php echo $striped ?>">
				<td class="<?php echo $wrapId ?>-list-cb">
					<?php if(!empty($bulkActions)){ ?>
					<input type="checkbox" name="ID" value="<?php echo $ID?>" />
					<?php } ?>
					
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
		
	
		<?php  } else { ?>
		<div id="ccm-list-none"><?php echo t('No items found.')?></div>
		<?php  } 
		$akciList->displayPaging($bu, false, $soargs);?>
	
    
    
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