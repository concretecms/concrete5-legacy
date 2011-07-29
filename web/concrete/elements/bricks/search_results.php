<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
	$cnt = Loader::controller('/dashboard/bricks/search');
	$newObjectList = $cnt->getRequestedSearchResults($_REQUEST['akCategoryHandle']);
	$newObjects = $newObjectList->getPage();
	$pagination = $newObjectList->getPagination();
?>

<div id="ccm-list-wrapper">
	<?php 
	if (!$mode) {
		$mode = $_REQUEST['mode'];
	}
	
	$soargs = array();
	$soargs['mode'] = $mode;
	$soargs['handle'] = $akCategoryHandle;

	?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="100%"><?php echo $newObjectList->displaySummary();?></td>
			<td style="white-space: nowrap"><?php echo t('With Selected: ')?>&nbsp;</td>
			<td align="right"><select id="ccm-new-object-list-multiple-operations<?php if($akID){ print "-".$akID; }?>" table="<?php echo $akCategoryHandle; ?>" disabled>
					<option value="">**</option>
					<option value="properties"><?php echo t('Edit Properties')?></option>
					<option value="delete"><?php echo t('Delete')?></option>
					<?php  if ($mode == 'choose_multiple') { ?>
					<option value="choose"><?php echo t('Choose')?></option>
					<?php  } ?>
				</select></td>
		</tr>
	</table>
	<?php 
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/dashboard/bricks/search/search_results';
	if (count($newObjects) > 0) { ?>
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-new-object-list" class="ccm-results-list">
		<tr>
			<th width="20px"><input id="ccm-new-object-list-cb-all" type="checkbox" /></th>
	<?php  
		$akcsh = Loader::helper('attribute_key_category_settings');
		$rs = $akcsh->getRegisteredSettings($akCategoryHandle);
		if(is_array($rs['static_attributes'])) foreach($rs['static_attributes'] as $key => $value) { ?>
			<th class="<?=$newObjectList->getSearchResultsClass($key)?>">
				<a href="<?=$newObjectList->getSortByURL($key, 'asc', $bu, array('handle' => $akCategoryHandle))?>"><?=$value?></a>
			</th>
		<?php }
		$ak = new AttributeKey($akCategoryHandle);
		$hList = $ak->getColumnHeaderList($akCategoryHandle);
		if(is_array($hList)) foreach($hList as $ak) { ?>
			<th class="<?=$newObjectList->getSearchResultsClass($ak)?>">
				<a href="<?=$newObjectList->getSortByURL($ak, 'asc', $bu, array('handle' => $akCategoryHandle))?>"><?=$ak->getAttributeKeyName()?></a>
			</th>
		<?php  } ?>
			<th width="20px" class="ccm-search-add-column-header">
				<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED . '/dashboard/bricks/search/customize_search_columns?handle='.$akCategoryHandle;?>" id="ccm-search-add-column">
					<img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" />
				</a>
			</th>
		</tr>
		<?php 
		foreach($newObjects as $key => $value) { 
			$action = "location.href='".View::url('/dashboard/bricks/edit/', $key)."'";
			
			if ($mode == 'choose_one' || $mode == 'choose_multiple') {
				$action = 'ccm_triggerSelectNewObject'.$akID.'($(this).parent()); jQuery.fn.dialog.closeTop();';
			}
			
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		<tr class="ccm-list-record <?php echo $striped?>">
			<td class="ccm-new-object-list-cb" style="vertical-align: middle !important">
				<input type="checkbox" 
					value="<?php echo $key?>" />
			</td>
			<?php 
			if(is_array($rs['static_attributes'])) foreach($rs['static_attributes'] as $key => $sa) { ?>
			<td onclick="<?=$action;?>"><?php
				$path = '';
            	$arrayPath = explode('/', $key);
				foreach($arrayPath as $level) $path .= '->'.$level;
				eval('$sav = $value'.$path.';');
				print $sav;
			?></td>
			<?php }
			if(is_array($hList)) foreach($hList as $ak) { ?>
			<td onclick="<?=$action;?>">
				<?=$value->getAttribute($ak->akHandle);?>
			</td>
			<?php  } ?>
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
$(function() { 
	ccm_setupNewObjectSearch<?php if($akID) { echo '_'.$akID; }?>(); 
});
</script>
