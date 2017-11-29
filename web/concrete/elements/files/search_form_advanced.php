<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php
Loader::model('file_set');

$searchFields = array(
	'' => '** ' . t('Fields'),
	'size' => t('Size'),
	'type' => t('Type'),
	'extension' => t('Extension'),
	'date_added' => t('Added Between'),
	'added_to' => t('Added to Page')
);

if ($_REQUEST['fType'] != false) {
	unset($searchFields['type']);
}
if ($_REQUEST['fExtension'] != false) {
	unset($searchFields['extension']);
}

$html = Loader::helper('html');
$text = Loader::helper('text');

Loader::model('file_attributes');
$searchFieldAttributes = FileAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
}

$ext1 = FileList::getExtensionList();
$extensions = array();
foreach($ext1 as $value) {
	$extensions[$value] = $value;
}

$t1 = FileList::getTypeList();
$types = array();
foreach($t1 as $value) {
	$types[$value] = FileType::getGenericTypeText($value);
}

?>

<?php $form = Loader::helper('form'); ?>
	
	<div id="ccm-<?php echo $searchInstance?>-search-field-base-elements" style="display: none">
	
		<span class="ccm-search-option" search-field="size">
		<?php echo $form->text('size_from', array('style' => 'width: 30px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('size_to', array('style' => 'width: 30px'))?>
		KB
		</span>
	
		<span class="ccm-search-option"  search-field="type">
		<?php echo $form->select('type', $types)?>
		</span>
	
		<span class="ccm-search-option"  search-field="extension">
		<?php echo $form->select('extension', $extensions)?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
		<?php echo $form->text('date_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option" search-field="added_to">
		<div style="width: 100px">
		<?php $ps = Loader::helper("form/page_selector");
		print $ps->selectPage('ocIDSearchField');
		?>
		</div>
		</span>
		
		<?php foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php } ?>
		
	</div>

	<form method="get" class="form-horizontal" id="ccm-<?php echo $searchInstance?>-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">
	<?php if ($_REQUEST['fType'] != false) {
		$showTypes = array();
		if(is_array($_REQUEST['fType'])) {
			foreach($_REQUEST['fType'] as $showTypeId) {
				$showTypes[] = FileType::getGenericTypeText($showTypeId);
			}
		}
		else {
			$showTypes[] = FileType::getGenericTypeText($_REQUEST['fType']);
		}
		?>
		<div class="ccm-file-manager-pre-filter"><?php echo t('Only displaying %s files.', implode(', ', $showTypes))?></div>
	<?php } else if ($_REQUEST['fExtension'] != false) {
		if(is_array($_REQUEST['fExtension'])) {
			$showExtensions = $_REQUEST['fExtension'];
		}
		else {
			$showExtensions = array($_REQUEST['fExtension']);
		}
		?>
		<div class="ccm-file-manager-pre-filter"><?php echo t('Only displaying files with extension .%s.', implode(', ', $showExtensions))?></div>
	<?php } ?>

	<input type="hidden" name="submit_search" value="1" />
	<?php
		foreach(array('fType', 'fExtension') as $filterName) {
			$filterValues = '';
			if(is_array($_REQUEST[$filterName])) {
				foreach($_REQUEST[$filterName] as $filterValue) {
					print '<input type="hidden" name="' . $filterName . '[]" value="' . $text->entities($filterValue) . '" />';
				}
			}
			else {
				print $form->hidden($filterName);
			}
		}
		print $form->hidden('searchType', $searchType); 
		print $form->hidden('ccm_order_dir', $searchRequest['ccm_order_dir']); 
		print $form->hidden('ccm_order_by', $searchRequest['ccm_order_by']); 
		print $form->hidden('fileSelector', $fileSelector); 
	?>	
	<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
	<br/>
	<div class="ccm-pane-options-permanent-search">

	<?php
		$s2 = FileSet::getSavedSearches();
		if (count($s2) > 0) { 
			if ($_REQUEST['fssID'] < 1) {
				$savedSearches = array('' => t('** Select a saved search.'));
			} else {
				$savedSearches = array('' => t('** None (Exit Saved Search)'));
			}
			
			foreach($s2 as $fss) {
				$savedSearches[$fss->getFileSetID()] = $fss->getFileSetName();
			}
		?>
			<div class="control-group">
			<?php echo $form->label('fssID', t('Saved Search'))?>
			<div class="controls">
				<?php echo $form->select('fssID', $savedSearches, $fssID, array('class' => 'span3', 'style' => 'vertical-align: middle'))?>
				<?php if ($_REQUEST['fssID'] > 0) { ?>
					<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete_set?fsID=<?php echo h($_REQUEST['fssID'])?>&searchInstance=<?php echo $searchInstance?>" class="ccm-file-set-delete-saved-search" dialog-append-buttons="true" dialog-title="<?php echo t('Delete File Set')?>" dialog-width="320" dialog-height="110" dialog-modal="false" style="vertical-align: middle"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" style="vertical-align: middle" width="16" height="16" border="0" /></a>
				<?php } ?>
			</div>
			</div>
			
		<?php } ?>

		<div class="span4">
		<?php echo $form->label('fvKeywords', t('Keywords'))?>
		<div class="controls">
			<?php echo $form->text('fKeywords', $searchRequest['fKeywords'], array('style'=> 'width: 130px')); ?>
		</div>
		</div>
		
		<div id="ccm-<?php echo $searchInstance?>-sets-search-wrapper">
		<?php
		$s1 = FileSet::getMySets();
		if (count($s1) > 0) { ?>
		<div class="span4" style="width: 280px">
			<?php echo $form->label('fsID', t('In Set(s)'))?>
			<?php if ($_REQUEST['fssID'] > 0) { ?>
			
				<div class="controls">
					<?php foreach($s1 as $s) { ?>
						<?php if ((is_array($searchRequest['fsID']) && in_array($s->getFileSetID(), $searchRequest['fsID'])) || (is_string($searchRequest['fsID']) && $searchRequest['fsID'] == $s->getFileSetID())) { ?>
						<label class="checkbox">
						<input type="checkbox"  checked disabled><?php echo wordwrap($s->getFileSetName(), '23', '&shy;', true)?>
						</label>
						<?php } ?>
					<?php } ?>
					<?php if ((is_array($searchRequest['fsID']) && in_array(-1, $searchRequest['fsID'])) || (is_string($searchRequest['fsID']) && $searchRequest['fsID'] == '-1')) { ?>
					<label class="checkbox">
					<input type="checkbox"  checked disabled><?php echo t('Files in no sets.')?>
					</label>
					<?php } ?> 
				</div>

			<?php } else { ?>
			
			<div class="input">
				<select multiple name="fsID[]" class="chosen-select">
					<optgroup label="<?php echo t('Sets')?>">
					<?php foreach($s1 as $s) { ?>
						<option value="<?php echo $s->getFileSetID()?>"  <?php if ((is_array($searchRequest['fsID']) && in_array($s->getFileSetID(), $searchRequest['fsID'])) || (is_string($searchRequest['fsID']) && $searchRequest['fsID'] == $s->getFileSetID())) { ?> selected="selected" <?php } ?>><?php echo wordwrap($s->getFileSetName(), '23', '&shy;', true)?></option>
					<?php } ?>
					</optgroup>
					<optgroup label="<?php echo t('Other')?>">
						<option value="-1" <?php if ((is_array($searchRequest['fsID']) && in_array(-1, $searchRequest['fsID'])) || (is_string($searchRequest['fsID']) && $searchRequest['fsID'] == '-1')) { ?> selected="selected" <?php } ?>><?php echo t('Files in no sets.')?></option>
					</optgroup>
				</select>
			</div>
			
			<?php } ?>
		</div>
		<?php } ?>
		</div>
		
		<div class="span3">
		<?php echo $form->label('numResults', t('# Per Page'))?>
		<div class="input">
			<?php echo $form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), $searchRequest['numResults'], array('style' => 'width:65px'))?>

		</div>
		<?php echo $form->submit('ccm-search-files', t('Search'), array('style' => 'margin-left: 10px'))?>

		</div>

		
	</div>
	
	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<?php if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>open<?php } else { ?>closed<?php } ?>"><?php echo t('Advanced Search')?></a>
	<div class="control-group ccm-pane-options-content" <?php if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>style="display: block" <?php } ?>>
		<br/>
		<table class="table table-striped ccm-search-advanced-fields" id="ccm-<?php echo $searchInstance?>-search-advanced-fields">
		<?php if ($_REQUEST['fssID'] < 1) { ?>
		<tr>
			<th colspan="2" width="100%"><?php echo t('Additional Filters')?></th>
			<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-<?php echo $searchInstance?>-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?php echo t('Add')?></a></th>
		</tr>
		<?php } ?>
		<tr id="ccm-search-field-base">
			<td><?php echo $form->select('searchField', $searchFields);?></td>
			<td width="100%">
			<input type="hidden" value="" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
			<div class="ccm-selected-field-content">
				<?php echo t('Select Search Field.')?>				
			</div></td>
			<?php if ($_REQUEST['fssID'] < 1) { ?><td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td><?php } ?>
		</tr>
		<?php 
		$i = 1;
		if (is_array($searchRequest['selectedSearchField'])) { 
			foreach($searchRequest['selectedSearchField'] as $req) { 
				if ($req == '') {
					continue;
				}
				?>
				
				<tr class="ccm-search-field ccm-search-request-field-set" ccm-search-type="<?php echo $req?>" id="ccm-<?php echo $searchInstance?>-search-field-set<?php echo $i?>">
				<td><?php echo $form->select('searchField' . $i, $searchFields, $req); ?></td>
				<td width="100%"><input type="hidden" value="<?php echo $req?>" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
					<div class="ccm-selected-field-content">
					<?php if ($req == 'size') { ?>
						<span class="ccm-search-option" search-field="size">
						<?php echo $form->text('size_from', $searchRequest['size_from'], array('style' => 'width: 30px'))?>
						<?php echo t('to')?>
						<?php echo $form->text('size_to', $searchRequest['size_to'], array('style' => 'width: 30px'))?>
						KB
						</span>
					<?php } ?>
				
					<?php if ($req == 'type') { ?>
						<span class="ccm-search-option"  search-field="type">
						<?php echo $form->select('type', $types, $searchRequest['type'])?>
						</span>
					<?php } ?>
					
					<?php if ($req == 'extension') { ?>
						<span class="ccm-search-option"  search-field="extension">
						<?php echo $form->select('extension', $extensions, $searchRequest['extension'])?>
						</span>
					<?php } ?>
					
					<?php if ($req == 'date_added') { ?>
						<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
						<?php echo $form->text('date_from', $searchRequest['date_from'], array('style' => 'width: 86px'))?>
						<?php echo t('to')?>
						<?php echo $form->text('date_to', $searchRequest['date_to'], array('style' => 'width: 86px'))?>
						</span>
					<?php } ?>

					<?php if ($req == 'added_to') { ?>
					<span class="ccm-search-option" search-field="parent">
					<div style="width: 100px">
					<?php $ps = Loader::helper("form/page_selector");
					print $ps->selectPage('ocIDSearchField', $searchRequest['ocIDSearchField']);
					?>
					</div>
					</span>
					<?php } ?>
					
					<?php foreach($searchFieldAttributes as $sfa) { 
						if ($sfa->getAttributeKeyID() == $req) {
							$at = $sfa->getAttributeType();
							$at->controller->setRequestArray($searchRequest);
							$at->render('search', $sfa);
						}
					} ?>
					
					</div>
					</td>
					<?php if ($_REQUEST['fssID'] < 1) { ?><td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td><?php } ?>
					</tr>
				<?php 
					$i++;
				} 
				
				} ?>
		</table>

		<?php if ($_REQUEST['fssID'] < 1) { ?>
		<div id="ccm-search-fields-submit">
				<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns?searchInstance=<?php echo $searchInstance?>" id="ccm-list-view-customize"><span class="ccm-menu-icon ccm-icon-properties"></span><?php echo t('Customize Results')?></a>
				<a class="ccm-search-save" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/save_search?searchInstance=<?php echo $searchInstance?>" id="ccm-<?php echo $searchInstance?>-launch-save-search" dialog-title="<?php echo t('Save Search')?>" dialog-width="320" dialog-height="200" dialog-modal="false"><span class="ccm-menu-icon ccm-icon-search-pages"></span><?php echo t('Save Search')?></a>
		</div>
		<?php } ?>

	</div>
</form>	

<script type="text/javascript">$(function() {
	$('a#ccm-<?php echo $searchInstance?>-launch-save-search').dialog();
	$('a.ccm-file-set-delete-saved-search').dialog();
	
	<?php if ($_REQUEST['fssID'] > 0) { ?>
	$('#ccm-<?php echo $searchInstance?>-advanced-search input, #ccm-<?php echo $searchInstance?>-advanced-search select, #ccm-<?php echo $searchInstance?>-advanced-search textarea').attr('disabled',true);
	$('#ccm-<?php echo $searchInstance?>-advanced-search select[name=fssID]').attr('disabled', false);
	<?php } ?>
	
	$(".chosen-select").chosen(ccmi18n_chosen);	

});</script>
