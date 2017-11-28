<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 

<div id="ccm-user-search-results">

<?php if ($searchType == 'DASHBOARD') { ?>

<div class="ccm-pane-body">

<?php } 

$ek = PermissionKey::getByHandle('edit_user_properties');
$ik = PermissionKey::getByHandle('activate_user');
$gk = PermissionKey::getByHandle('assign_user_groups');
$dk = PermissionKey::getByHandle('delete_user');

if (!$mode) {
		$mode = $_REQUEST['mode'];
	}
	if (!$searchType) {
		$searchType = $_REQUEST['searchType'];
	}
	
	$soargs = array();
	$soargs['searchType'] = $searchType;
	$soargs['mode'] = $mode;
	$searchInstance = 'user';

	?>

<div id="ccm-list-wrapper"><a name="ccm-<?php echo $searchInstance?>-list-wrapper-anchor"></a>

	<div style="margin-bottom: 10px">
		<?php $form = Loader::helper('form'); ?>

		<?php if ($ek->validate()) { ?>
			<a href="<?php echo View::url('/dashboard/users/add')?>" style="float: right" class="btn primary"><?php echo t("Add User")?></a>
		<?php } ?>
		<select id="ccm-<?php echo $searchInstance?>-list-multiple-operations" class="span3" disabled>
					<option value="">** <?php echo t('With Selected')?></option>
					<?php if ($ek->validate()) { ?>
						<option value="properties"><?php echo t('Edit Properties')?></option>
					<?php } ?>
					<?php if ($ik->validate()) { ?>
						<option value="activate"><?php echo t('Activate')?></option>
						<option value="deactivate"><?php echo t('Deactivate')?></option>
					<?php } ?>
					<?php if ($gk->validate()) { ?>
					<option value="group_add"><?php echo t('Add to Group')?></option>
					<option value="group_remove"><?php echo t('Remove from Group')?></option>
					<?php } ?>
					<?php if ($dk->validate()) { ?>
					<option value="delete"><?php echo t('Delete')?></option>
					<?php } ?>
				<?php if ($mode == 'choose_multiple') { ?>
					<option value="choose"><?php echo t('Choose')?></option>
				<?php } ?>
				</select>

	</div>

	<?php
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/users/search_results';
	
	if (count($users) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-user-list" class="ccm-results-list">
		<tr>
			<th width="1"><input id="ccm-user-list-cb-all" type="checkbox" /></th>
			<?php foreach($columns->getColumns() as $col) { ?>
				<?php if ($col->isColumnSortable()) { ?>
					<th class="<?php echo $userList->getSearchResultsClass($col->getColumnKey())?>"><a href="<?php echo $userList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?php echo $col->getColumnName()?></a></th>
				<?php } else { ?>
					<th><?php echo $col->getColumnName()?></th>
				<?php } ?>
			<?php } ?>

		</tr>
	<?php
		foreach($users as $ui) { 
			$action = View::url('/dashboard/users/search?uID=' . $ui->getUserID());
			
			if ($mode == 'choose_one' || $mode == 'choose_multiple') {
				$action = 'javascript:void(0); ccm_triggerSelectUser(' . $ui->getUserID() . ',\'' . $txt->entities($ui->getUserName()) . '\',\'' . $txt->entities($ui->getUserEmail()) . '\'); jQuery.fn.dialog.closeTop();';
			}
			
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		
			<tr class="ccm-list-record <?php echo $striped?>">
			<td class="ccm-user-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?php echo $ui->getUserID()?>" user-email="<?php echo $ui->getUserEmail()?>" user-name="<?php echo $ui->getUserName()?>" /></td>
			<?php foreach($columns->getColumns() as $col) { ?>
				<?php if ($col->getColumnKey() == 'uName') { ?>
					<td><a href="<?php echo $action?>"><?php echo $ui->getUserName()?></a></td>
				<?php } else { ?>
					<td><?php echo $col->getColumnValue($ui)?></td>
				<?php } ?>
			<?php } ?>

			</tr>
			<?php
		}

	?>
	
	</table>
	
	

	<?php } else { ?>
		
		<div id="ccm-list-none"><?php echo t('No users found.')?></div>
		
	
	<?php }  ?>

</div>

<?php
$tp = new TaskPermission();
if ($tp->canAccessUserSearchExport()) {  ?>
	<div id="ccm-export-results-wrapper">
		<a id="ccm-export-results" href="javascript:void(0)" onclick="$('#ccm-user-advanced-search').attr('action', '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results_export'); $('#ccm-user-advanced-search').get(0).submit(); $('#ccm-user-advanced-search').attr('action', '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results');"><span></span><?php echo t('Export')?></a>
	</div>
<?php } ?>
<?php
	$userList->displaySummary();
?>

<?php if ($searchType == 'DASHBOARD') { ?>
</div>

<div class="ccm-pane-footer">
	<?php 	$userList->displayPagingV2($bu, false, $soargs); ?>
</div>

<?php } else { ?>
	<div class="ccm-pane-dialog-pagination">
		<?php 	$userList->displayPagingV2($bu, false, $soargs); ?>
	</div>
<?php } ?>

</div>

<script type="text/javascript">
$(function() { 
	ccm_setupUserSearch('<?php echo $searchInstance?>'); 
});
</script>