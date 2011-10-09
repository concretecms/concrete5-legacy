<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
if(!$akCategoryHandle) $akCategoryHandle = $_REQUEST['akCategoryHandle'];
if(!$searchInstance) $searchInstance = uniqid($akCategoryHandle);
if(!$baseId) $baseId = $searchInstance;


if(isset($_REQUEST['searchInstance'])) $searchInstance = $_REQUEST['searchInstance'];
if(isset($_REQUEST['baseId'])) $baseId = $_REQUEST['baseId'];
if(isset($_REQUEST['administrationDisabled'])) $administrationDisabled = $_REQUEST['administrationDisabled'];
if(isset($_REQUEST['userDefinedColumnsDisabled'])) $userDefinedColumnsDisabled = $_REQUEST['userDefinedColumnsDisabled'];
if(isset($_REQUEST['keywords'])) $keywords = $_REQUEST['keywords'];
if(isset($_REQUEST['numResults'])) $numResults = $_REQUEST['numResults'];
if(isset($_REQUEST['defaults'])) $defaults = $_REQUEST['defaults'];
if(is_string($defaults)) $defaults = unserialize(urldecode($defaults));


$wrapId = $baseId.'_form';




Loader::model('attribute_key_category_item_list');
$akcdca = new AttributeKeyCategoryAvailableColumnSet($akCategoryHandle);
if(count($akcdca->getColumns())) foreach($akcdca->getColumns() as $col) {
	$searchFieldsStandard[$col->getColumnKey()] = $col->getColumnName();
}

$ak = new AttributeKey($akCategoryHandle);
$searchFieldAttributes = $ak->getSearchableList($akCategoryHandle);
foreach($searchFieldAttributes as $ak) {
	$searchFieldsAdditional[$ak->getAttributeKeyID()] = $ak->getAttributeKeyName();
}
$form = Loader::helper('form');


$json = Loader::helper('json');
$jsInitArgs['searchInstance'] = $searchInstance;
$jsInitArgs['baseId'] = $baseId;

$jsInitArgsStr = htmlspecialchars($json->encode($jsInitArgs));
	
	
?>
<div id="<?php echo $wrapId ?>-field-base-elements" style="display: none">
	<span class="ccm-search-option" search-field="onlyMine">
		<?php
			$handle = '';
			if(is_numeric($searchInstance)) $handle = 'dsp_';
			$handle .= 'onlyMine';
			$u = new User(); 
			print $form->checkbox($handle, $u->uID); ?>
	</span>
<?php if(count($akcdca->getColumns())) foreach($akcdca->getColumns() as $col) { ?>
	<span class="ccm-search-option" search-field="<?php echo $col->getColumnKey()?>">
		<?php 
			$handle = '';
			if(is_numeric($searchInstance)) $handle = 'dsp_';
			$handle .= $col->getColumnKey();
			if($col->formType){
				$formType = $col->formType;
				print $form->$formType($handle);
			} else {
				print $form->text($handle);
			}
		?>
	</span>
<?php } ?>	
	<?php  foreach($searchFieldAttributes as $sfa) { 
		$sfa->render('search'); ?>
	<?php  } ?>
	
</div>


<form method="get" id="<?php echo $wrapId ?>" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/search_results' ?>" data-options-ccm_akcitemsearchform="<?php echo $jsInitArgsStr ?>">
	<?php echo $form->hidden('searchInstance', $searchInstance) ?>
    <?php echo $form->hidden('baseId', $baseId) ?>
    <?php echo $form->hidden('akCategoryHandle', $akCategoryHandle) ?>
    
	<?php if($mode) echo $form->hidden('mode', $mode); ?>
    
	<?php echo $form->hidden('search', 1); ?>
<div id="<?php echo $wrapId ?>-advanced-fields" class="ccm-search-advanced-fields" >		
	<div class="ccm-search-box-title">
		<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-search-loading"  id="ccm-<?php echo $searchInstance?>-search-loading" />
		<h2><?php echo t('Search')?></h2>
	</div>
	

	<div class="ccm-search-advanced-fields-inner">
		<div class="ccm-search-field">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%">
				<?php echo $form->label('keywords', t('Key Words'))?>
				<?php echo $form->text('keywords', $keywords, array('style' => 'width:200px')); ?>
				</td>
			</tr>
			</table>
		</div>
	
		<div class="ccm-search-field">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td style="white-space: nowrap"><div style="width: 85px; padding-right:5px"><?php echo t('Results Per Page')?></div></td>
				<td width="1%">
					<?php echo $form->select('numResults', array(
						'0' => 'All',
						'10' => '10',
						'25' => '25',
						'50' => '50',
						'100' => '100',
						'500' => '500'
					), $numResults, array('style' => 'width:65px'))?>
				</td>
				<td width="1%" align="center" style="padding-left:5px;"><a href="javascript:;" id="<?php echo $wrapId ?>-add-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
			</tr>	
			</table>
		</div>
		
		<div id="<?php echo $wrapId ?>-field-base" class="ccm-search-field" style="display:none">				
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="top" style="padding-right: 4px">
					<select class="ccm-input-select" style="width: 85px" id="searchField" name="searchField">
						<option value="">** Fields</option>
						<option value="onlyMine">Only Mine</option>
					<?php if(is_array($searchFieldsStandard)) { ?>
						<optgroup label="Standard Properties">
						<?php foreach($searchFieldsStandard as $value => $name) { ?>
							<option value="<?php echo $value?>"><?php echo $name?></option>
						<?php } ?>
						</optgroup>
					<?php } ?>
					<?php if(is_array($searchFieldsAdditional)) { ?>
						<optgroup label="Additional Properties">
						<?php foreach($searchFieldsAdditional as $value => $name) { ?>
							<option value="<?php echo $value?>"><?php echo $name?></option>
						<?php } ?>
						</optgroup>
					<?php } ?>
					</select>
					<input type="hidden" value="" class="ccm-selected-search-field" name="selectedSearchField[]" />
					</td>
					<td width="100%" valign="top" class="ccm-selected-field-content">
					<?php echo t('Select Search Field.')?>
					</td>
					<td valign="top">
					<a href="javascript:;" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="<?php echo $wrapId ?>-fields-wrapper">
		<?php $i=1; if(is_array($defaults['filters'])) foreach($defaults['filters'] as $key => $value) {
			$continue = false;
			if(is_array($value)) {
				foreach($value as $akv) {
					if(is_array($akv)) {
						foreach($akv as $akValue) if(!empty($akValue)) $continue = TRUE;
					} elseif(!empty($akv)) {
						$continue = TRUE;
					}
				}
			} elseif(!empty($value)) {
				$continue = TRUE;
			}
			if($continue) { 
				$handle = '';
				if(is_numeric($searchInstance)) $handle = 'dsp_';
			?>
			<div id="<?php echo $wrapId ?>-field-set<?php echo $i?>" class="ccm-search-field">	
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<select class="ccm-input-select" style="width: 85px" id="searchField" name="searchField">
							<option value="">** Fields</option>
							<option value="onlyMine"<?php if($key == 'onlyMine'){?> selected="selected"<?php } ?>>Only Mine</option>
						<?php if(is_array($searchFieldsStandard)) { ?>
							<optgroup label="Standard Properties">
							<?php foreach($searchFieldsStandard as $item => $name) { ?>
								<option value="<?php echo $item?>"<?php if($key == $item){?> selected="selected"<?php } ?>><?php echo $name?></option>
							<?php } ?>
							</optgroup>
						<?php } ?>
						<?php if(is_array($searchFieldsAdditional)) { ?>
							<optgroup label="Additional Properties">
							<?php foreach($searchFieldsAdditional as $item => $name) { ?>
								<option value="<?php echo $item?>"<?php if($key == $item){?> selected="selected"<?php } ?>><?php echo $name?></option>
							<?php } ?>
							</optgroup>
						<?php } ?>
						</select>
						<input type="hidden" value="<?php echo $key?>" class="ccm-selected-search-field" name="selectedSearchField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-selected-field-content">
						<span class="ccm-search-option" search-field="<?php echo $key?>">
						<?php
							if($key == 'onlyMine') { 
								print $form->checkbox($handle.$key, $value, TRUE);
							} elseif(is_numeric($key)) {
								$ak->getByID($key)->render('search');
							} else {
								print $form->text($handle.$key, $value);
							}
						?>
						</span>
						</td>
						<td valign="top">
						<a href="javascript:;" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
					</tr>
				</table>
				<?php if(is_array($value)) foreach($value as $valueName => $akv) { ?>
				<script>
					formInputValueItem = $('#ccm-<?php echo $searchInstance?>-search-fields-wrapper').find('input[name=akID\\[<?php echo $key?>\\]\\[<?php echo $valueName?>\\]]');
					switch(formInputValueItem.attr('type')) {
						case 'checkbox':
							if(<?php echo $akv?> == 1) formInputValueItem.attr('checked', 'checked');
							break;
						default:
							formInputValueItem.attr('value', '<?php echo $akv?>');
							break;
					}
				</script>
				<?php } else { ?>
				<script>
					formInputValueItem = $('#ccm-<?php echo $searchInstance?>-search-fields-wrapper').find('input[name=akID\\[<?php echo $key?>\\]\\[value\\]]');
					switch(formInputValueItem.attr('type')) {
						case 'checkbox':
							if(<?php echo $value?> == 1) formInputValueItem.attr('checked', 'checked');
							break;
						default:
							formInputValueItem.attr('value', '<?php echo $value?>');
							break;
					}
				</script>
				<?php } ?>
				<script>
					//ccm_activateAdvancedSearchFields("<?php echo $searchInstance?>", <?php echo $i?>);
                </script>
			</div>
		<?php $i++;}}?>		
		</div>
		
		<div id="ccm-search-fields-submit">
			<?php echo $form->submit('ccm-'.$searchInstance.'-advanced-search-submit', 'Search')?>
		</div>
		
	</div>

</div>
</form>	


<?php
	if($jsInit !== FALSE){
?>
<script type="text/javascript">
$(function(){
	$("#<?php echo $wrapId ?>").ccm_akcItemSearchForm();
});
</script>
<?php } ?>