<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
if(!$akCategoryHandle) $akCategoryHandle = $_REQUEST['akCategoryHandle'];

if(!$searchInstance) $searchInstance = $akCategoryHandle.time();
if(isset($_REQUEST['searchInstance'])) $searchInstance = $_REQUEST['searchInstance'];

if(isset($_REQUEST['administrationDisabled'])) $administrationDisabled = $_REQUEST['administrationDisabled'];

if(isset($_REQUEST['action'])) $action = $_REQUEST['action'];

if(isset($_REQUEST['persistantBID'])) $persistantBID = $_REQUEST['persistantBID'];

if(isset($_REQUEST['columns'])) $columns = $_REQUEST['columns'];
if($columns) $columns = urlencode(serialize($columns));

$searchFields = array('' => '** ' . t('Fields'));

$akc = new AttributeKey($akCategoryHandle);
$searchFieldAttributes = $akc->getSearchableList($akCategoryHandle);
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyName();
}
?>

<?php  $form = Loader::helper('form'); ?>

	
	<div id="ccm-<?=$searchInstance?>-search-field-base-elements" style="display: none">
		
		<?php  foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php  } ?>
		
	</div>
	
	<form method="get" id="ccm-<?=$searchInstance?>-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/search_results?akCategoryHandle='.$akCategoryHandle; if(!empty($akID)) print '&akID='.$akID;?>">
	<?php echo $form->hidden('mode', $mode); ?>
	<?php echo $form->hidden('akCategoryHandle', $akCategoryHandle); ?>
	<?php echo $form->hidden('searchInstance', $searchInstance); ?>
	<div id="ccm-<?=$searchInstance?>-search-advanced-fields" class="ccm-search-advanced-fields" >
	
		<input type="hidden" name="search" value="1" />
		<input type="hidden" name="administrationDisabled" value="<?=$administrationDisabled?>" />
		<?php if($columns) { ?>
		<input type="hidden" name="columns_<?=$searchInstance?>" value="<?=$columns?>" />
		<?php } ?>
		<input type="hidden" name="action" value="<?=$action?>" />
		<input type="hidden" name="persistantBID" value="<?=$persistantBID?>" />
		
		<div id="ccm-search-box-title">
			<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-search-loading"  id="ccm-<?=$searchInstance?>-search-loading" />
			<h2><?php echo t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?php echo $form->label('keywords', t('Key Words'))?>
					<?php echo $form->text('keywords', array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>
		
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php echo t('Results Per Page')?></div></td>
					<td width="100%">
						<?php echo $form->select('numResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), false, array('style' => 'width:65px'))?>
					</td>
					<td><a href="javascript:void(0)" id="ccm-<?=$searchInstance?>-search-add-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				</tr>	
				</table>
			</div>
			
			<div id="ccm-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?php echo $form->select('searchField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-<?=$searchInstance?>-selected-field" name="selectedSearchField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-selected-field-content">
						<?php echo t('Select Search Field.')?>
						</td>
						<td valign="top">
						<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="ccm-search-fields-wrapper">			
			</div>
			
			<div id="ccm-search-fields-submit">
				<?php echo $form->submit('ccm-'.$searchInstance.'-advanced-search', 'Search')?>
			</div>
		</div>
	
</div>
</form>	