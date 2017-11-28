<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
$editPermissions = false;
if ($c->getCollectionInheritance() == 'OVERRIDE') { 
	$editPermissions = true;
}
?>
<div class="ccm-ui">
<form>
<div class="ccm-pane-options" style="padding-bottom: 0px">
<div class="clearfix">
<label for="ccm-page-permissions-inherit"><?php echo t('Assign Permissions')?></label>
<div class="input">
   <select id="ccm-page-permissions-inherit" style="width: 220px">
	<?php if ($c->getCollectionID() > 1) { ?><option value="PARENT" <?php if ($c->getCollectionInheritance() == "PARENT") { ?> selected<?php } ?>><?php echo t('By Area of Site (Hierarchy)')?></option><?php } ?>
	<?php if ($c->getMasterCollectionID() > 1) { ?><option value="TEMPLATE"  <?php if ($c->getCollectionInheritance() == "TEMPLATE") { ?> selected<?php } ?>><?php echo t('From Page Type Defaults')?></option><?php } ?>
	<option value="OVERRIDE" <?php if ($c->getCollectionInheritance() == "OVERRIDE") { ?> selected<?php } ?>><?php echo t('Manually')?></option>
  </select>
</div>
</div>
<?php if (!$c->isMasterCollection()) { ?>
<div class="clearfix">
<label for="ccm-page-permissions-subpages-override-template-permissions"><?php echo t('Subpage Permissions')?></label>
<div class="input">
	<select id="ccm-page-permissions-subpages-override-template-permissions" style="width: 260px">
		<option value="0"<?php if (!$c->overrideTemplatePermissions()) { ?>selected<?php } ?>><?php echo t('Inherit page type default permissions.')?></option>
		<option value="1"<?php if ($c->overrideTemplatePermissions()) { ?>selected<?php } ?>><?php echo t('Inherit the permissions of this page.')?></option>
	</select>
</div>
</div>

<?php } ?>
</div>
</form>

<?php
	  $cpc = $c->getPermissionsCollectionObject();
	if ($c->getCollectionInheritance() == "PARENT") { ?>
	<div><strong><?php echo t('This page inherits its permissions from:');?> <a target="_blank" href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $cpc->getCollectionID()?>"><?php echo $cpc->getCollectionName()?></a></strong></div><br/><br/>
	<?php } ?>		


<?php $cat = PermissionKeyCategory::getByHandle('page'); ?>
<form method="post" id="ccm-permission-list-form" action="<?php echo $cat->getToolsURL("save_permission_assignments")?>&cID=<?php echo $c->getCollectionID()?>">

<table class="ccm-permission-grid">
<?php
$permissions = PermissionKey::getList('page');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($c);
	?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?php echo $pk->getPermissionKeyID()?>"><strong><?php if ($editPermissions) { ?><a dialog-title="<?php echo $pk->getPermissionKeyDisplayName()?>" data-pkID="<?php echo $pk->getPermissionKeyID()?>" data-paID="<?php echo $pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?php } ?><?php echo $pk->getPermissionKeyDisplayName()?><?php if ($editPermissions) { ?></a><?php } ?></strong></td>
	<td id="ccm-permission-grid-cell-<?php echo $pk->getPermissionKeyID()?>" <?php if ($editPermissions) { ?>class="ccm-permission-grid-cell"<?php } ?>><?php echo Loader::element('permission/labels', array('pk' => $pk))?></td>
	</tr>
<?php } ?>
<?php if ($editPermissions) { ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?php echo Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>
<?php } ?>
</table>

</form>

<script type="text/javascript">
ccm_permissionLaunchDialog = function(link) {
	var dupe = $(link).attr('data-duplicate');
	if (dupe != 1) {
		dupe = 0;
	}
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?php echo $c->getCollectionID()?>&ctask=set_advanced_permissions&duplicate=' + dupe + '&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: false,
		width: 500,
		height: 380
	});		
}
</script>



<div id="ccm-page-permissions-confirm-dialog" style="display: none">
<?php echo t('Changing this setting will affect this page immediately. Are you sure?')?>
<div id="dialog-buttons-start">
	<input type="button" class="btn" value="Cancel" onclick="jQuery.fn.dialog.closeTop()" />
	<input type="button" class="btn error ccm-button-right" value="Ok" onclick="ccm_pagePermissionsConfirmInheritanceChange()" />
</div>
</div>


 <?php if ($editPermissions) { ?>
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn"><?php echo t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn primary ccm-button-right"><?php echo t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
<?php } ?>

</div>


<script type="text/javascript">
var inheritanceVal = '';

ccm_pagePermissionsCancelInheritance = function() {
	$('#ccm-page-permissions-inherit').val(inheritanceVal);
}

ccm_pagePermissionsConfirmInheritanceChange = function() { 
	jQuery.fn.dialog.showLoader();
	$.getJSON('<?php echo $pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_permission_inheritance")?>&cID=<?php echo $c->getCollectionID()?>&mode=' + $('#ccm-page-permissions-inherit').val(), function(r) { 
		if (r.deferred) {
			jQuery.fn.dialog.closeAll();
			jQuery.fn.dialog.hideLoader();
			ccmAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
		} else {
			jQuery.fn.dialog.closeTop();
			ccm_refreshPagePermissions();
		}
	});
}


$(function() {
	$('#ccm-permission-list-form').ajaxForm({
		dataType: 'json',
		
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		
		success: function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
			if (!r.deferred) {
				ccmAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			} else {
				jQuery.fn.dialog.closeTop();
				ccmAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			}

		}		
	});
	
	inheritanceVal = $('#ccm-page-permissions-inherit').val();
	$('#ccm-page-permissions-inherit').change(function() {
		$('#dialog-buttons-start').addClass('dialog-buttons');
		jQuery.fn.dialog.open({
			element: '#ccm-page-permissions-confirm-dialog',
			title: '<?php echo t("Confirm Change")?>',
			width: 280,
			height: 100,
			onClose: function() {
				ccm_pagePermissionsCancelInheritance();
			}
		});
	});
	
	$('#ccm-page-permissions-subpages-override-template-permissions').change(function() {
		jQuery.fn.dialog.showLoader();
		$.getJSON('<?php echo $pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_subpage_defaults_inheritance")?>&cID=<?php echo $c->getCollectionID()?>&inherit=' + $(this).val(), function(r) { 
			if (r.deferred) {
				jQuery.fn.dialog.closeTop();
				jQuery.fn.dialog.hideLoader();
				ccmAlert.hud(ccmi18n.setPermissionsDeferredMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
			} else {
				ccm_refreshPagePermissions();
			}
		});
	});
	
});

ccm_refreshPagePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?ctask=edit_permissions&cID=<?php echo $c->getCollectionID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});	
}

</script>
