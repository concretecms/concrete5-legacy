<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php
$searchFields = array(
	'' => '** ' . t('Fields'),
	'date_added' => t('Registered Between'),
	'is_active' => t('Activated Users')
);

if (PERMISSIONS_MODEL == 'advanced') { 
	$searchFields['group_set'] = t('Group Set');
}

Loader::model('user_attributes');
$searchFieldAttributes = UserAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
}


?>

<?php $form = Loader::helper('form'); ?>

	
	<div id="ccm-user-search-field-base-elements" style="display: none">

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
		<?php echo $form->text('date_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option"  search-field="is_active">
		<?php echo $form->select('active', array('0' => t('Inactive Users'), '1' => t('Active Users')), array('style' => 'vertical-align: middle'))?>
		</span>
		
		<?php if (PERMISSIONS_MODEL == 'advanced') { 
			$gsl = new GroupSetList();
			$groupsets = array();
			foreach($gsl->get() as $gs) { 
				$groupsets[$gs->getGroupSetID()] = $gs->getGroupSetDisplayName();
			}
		?>
		<span class="ccm-search-option"  search-field="group_set">
		<?php echo $form->select('gsID', $groupsets)?>
		</span>
		<?php } ?>
		
		<?php foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php } ?>
		
	</div>
	
	<form class="form-horizontal" method="get" id="ccm-user-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results">
	<?php echo $form->hidden('mode', $mode); ?>
	<?php echo $form->hidden('searchType', $searchType); ?>
	<input type="hidden" name="search" value="1" />
	<br/>
	<div class="ccm-pane-options-permanent-search">

		<div class="span4">
		<?php echo $form->label('keywords', t('Keywords'))?>
		<div class="controls">
			<?php echo $form->text('keywords', $_REQUEST['keywords'], array('placeholder' => t('Username or Email'), 'style'=> 'width: 140px')); ?>
		</div>
		</div>
				
		<?php 
		$pk = PermissionKey::getByHandle('access_user_search');
		Loader::model('search/group');
		$gl = new GroupSearch();
		$gl->setItemsPerPage(-1);
		$g1 = $gl->getPage();
		?>		

		<div class="span4" style="width:280px">
			<?php echo $form->label('gID', t('Group(s)'))?>
			<div class="controls">
				<select multiple name="gID[]" class="chosen-select" style="width: 220px">
					<?php foreach($g1 as $g) {
						if ($pk->validate($g['gID'])) { ?>
						<option value="<?php echo $g['gID']?>"  <?php if (is_array($_REQUEST['gID']) && in_array($g['gID'], $_REQUEST['gID'])) { ?> selected="selected" <?php } ?>><?php echo h(tc('GroupName', $g['gName']))?></option>
					<?php 
						}
					} ?>
				</select>
			</div>
		</div>
		
		<div class="span3">
		<?php echo $form->label('numResults', t('# Per Page'))?>
		<div class="controls">
			<?php echo $form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), $_REQUEST['numResults'], array('style' => 'width:65px'))?>
		</div>

		<?php echo $form->submit('ccm-search-users', t('Search'), array('style' => 'margin-left: 10px'))?>

		</div>
		
	</div>

	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-closed"><?php echo t('Advanced Search')?></a>
	<div class="clearfix ccm-pane-options-content">
		<br/>
		<table class="table table-bordered table-striped ccm-search-advanced-fields" id="ccm-user-search-advanced-fields">
		<tr>
			<th colspan="2" width="100%"><?php echo t('Additional Filters')?></th>
			<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-user-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?php echo t('Add')?></a></th>
		</tr>
		<tr id="ccm-search-field-base">
			<td><?php echo $form->select('searchField', $searchFields);?></td>
			<td width="100%">
			<input type="hidden" value="" class="ccm-user-selected-field" name="selectedSearchField[]" />
			<div class="ccm-selected-field-content">
				<?php echo t('Select Search Field.')?>				
			</div></td>
			<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
		</tr>

		</table>

		<div id="ccm-search-fields-submit">
			<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns" id="ccm-list-view-customize"><span class="ccm-menu-icon ccm-icon-properties"></span><?php echo t('Customize Results')?></a>
		</div>

	</div>	

</form>	

<script type="text/javascript">
$(function() { 
	ccm_setupUserSearch('<?php echo $searchInstance?>'); 
});
</script>
