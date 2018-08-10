<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="clearfix">

<?php 

$enablePermissions = false;
if (!$f->overrideFileSetPermissions()) { ?>

	<div class="block-message alert-message notice">
	<p>
	<?php echo t("Permissions for this file are currently dependent on file sets and global file permissions.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_setFilePermissionsToOverride()"><?php echo t('Override Permissions')?></a>
	</div>
	
<?php } else { 
	$enablePermissions = true;
	?>

	<div class="block-message alert-message notice">
	<p><?php echo t("Permissions for this file currently override its sets and the global file permissions.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_revertToGlobalFilePermissions()"><?php echo t('Revert to File Set and Global Permissions')?></a>
	</div>

<?php } ?>


<?php echo Loader::element('permission/help');?>

<?php $cat = PermissionKeyCategory::getByHandle('file');?>

<form method="post" id="ccm-permission-list-form" action="<?php echo $cat->getToolsURL("save_permission_assignments")?>&fID=<?php echo $f->getFileID()?>">

<table class="ccm-permission-grid">
<?php
$permissions = PermissionKey::getList('file');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($f);
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
</form>

<?php if ($enablePermissions) { ?>
<div id="ccm-file-permissions-advanced-buttons" style="display: none">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn"><?php echo t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn primary ccm-button-right"><?php echo t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
<?php } ?>

</div>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	var dupe = $(link).attr('data-duplicate');
	if (dupe != 1) {
		dupe = 0;
	}
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file?duplicate=' + dupe + '&fID=<?php echo $f->getFileID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: false,
		width: 500,
		height: 380
	});		
}

$(function() {
	$('#ccm-permission-list-form').ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}		
	});
});

ccm_revertToGlobalFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo $pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("revert_to_global_file_permissions")?>&fID=<?php echo $f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_setFilePermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo $pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("override_global_file_permissions")?>&fID=<?php echo $f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_refreshFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions?fID=<?php echo $f->getFileID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		ccm_filePermissionsSetupButtons();
		jQuery.fn.dialog.hideLoader();
	});
}

</script>