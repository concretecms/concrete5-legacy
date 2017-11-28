<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $cat = PermissionKeyCategory::getByHandle('basic_workflow');?>

<table class="ccm-permission-grid">
<?php
$permissions = PermissionKey::getList('basic_workflow');

foreach($permissions as $pk) { 
	$pk->setPermissionObject($workflow);
	?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?php echo $pk->getPermissionKeyID()?>"><strong><?php if ($enablePermissions) { ?><a dialog-title="<?php echo $pk->getPermissionKeyDisplayName()?>" data-pkID="<?php echo $pk->getPermissionKeyID()?>" data-paID="<?php echo $pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?php } ?><?php echo $pk->getPermissionKeyDisplayName()?><?php if ($enablePermissions) { ?></a><?php } ?></strong></td>
	<td id="ccm-permission-grid-cell-<?php echo $pk->getPermissionKeyID()?>" <?php if ($enablePermissions) { ?>class="ccm-permission-grid-cell"<?php } ?>><?php echo Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<?php } ?>
<?php if ($enablePermissions) { ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?php echo Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>
<?php } ?>

</table>
<?php if ($enablePermissions) { ?>

	<script type="text/javascript">
	ccm_permissionLaunchDialog = function(link) {
		var dupe = $(link).attr('data-duplicate');
		if (dupe != 1) {
			dupe = 0;
		}
		jQuery.fn.dialog.open({
			title: $(link).attr('dialog-title'),
			href: '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/basic_workflow?duplicate=' + dupe + '&wfID=<?php echo $workflow->getWorkflowID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
			modal: false,
			width: 500,
			height: 380
		});		
	}
	</script>
	
<?php } ?>
