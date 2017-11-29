<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
	<?php 
	global $c;
	global $a;

$enablePermissions = false;
if (!$b->overrideAreaPermissions()) { ?>

	<div class="block-message alert-message notice">
	<p>
	<?php echo t("Permissions for this block are currently dependent on the area containing this block.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_setBlockPermissionsToOverride()"><?php echo t('Override Permissions')?></a>
	</div>
	
<?php } else { 
	$enablePermissions = true;
	?>

	<div class="block-message alert-message notice">
	<p><?php echo t("Permissions for this block currently override those of the area and page.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_revertToAreaPermissions()"><?php echo t('Revert to Area Permissions')?></a>
	</div>

<?php } ?>


<?php echo Loader::element('permission/help');?>

<?php $cat = PermissionKeyCategory::getByHandle('block');?>
<form method="post" id="ccm-permission-list-form" action="<?php echo $cat->getToolsURL("save_permission_assignments")?>&cID=<?php echo $c->getCollectionID()?>&arHandle=<?php echo urlencode($b->getAreaHandle())?>&cvID=<?php echo $c->getVersionID()?>&bID=<?php echo $b->getBlockID()?>">

<table class="ccm-permission-grid">

<?php
$permissions = PermissionKey::getList('block');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($b);

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
<div class="dialog-buttons">
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
		href: '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?duplicate=' + dupe + '&bID=<?php echo $b->getBlockID()?>&arHandle=<?php echo urlencode($b->getAreaHandle())?>&cvID=<?php echo $c->getVersionID()?>&bID=<?php echo $b->getBlockID()?>&cID=<?php echo $c->getCollectionID()?>&btask=set_advanced_permissions&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
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
			ccm_mainNavDisableDirectExit();
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}		
	});
});

ccm_revertToAreaPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo $pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("revert_to_area_permissions")?>&bID=<?php echo $b->getBlockID()?>&cvID=<?php echo $c->getVersionID()?>&arHandle=<?php echo urlencode($b->getAreaHandle())?>&cID=<?php echo $c->getCollectionID()?>', function() { 
		ccm_mainNavDisableDirectExit();
		ccm_refreshBlockPermissions();
	});
}

ccm_setBlockPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo $pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("override_area_permissions")?>&bID=<?php echo $b->getBlockID()?>&cvID=<?php echo $c->getVersionID()?>&arHandle=<?php echo urlencode($b->getAreaHandle())?>&cID=<?php echo $c->getCollectionID()?>', function() { 
		ccm_mainNavDisableDirectExit();
		ccm_refreshBlockPermissions();
	});
}

ccm_refreshBlockPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=groups&bID=<?php echo $b->getBlockID()?>&cvID=<?php echo $c->getVersionID()?>&arHandle=<?php echo urlencode($b->getAreaHandle())?>&cID=<?php echo $c->getCollectionID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
}

</script>