<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php 
if ($_REQUEST['paID'] && $_REQUEST['paID'] > 0) { 
	$pa = PermissionAccess::getByID($_REQUEST['paID'], $permissionKey);
	if ($pa->isPermissionAccessInUse() || $_REQUEST['duplicate'] == '1') {
		$pa = $pa->duplicate();
	}
} else { 
	$pa = PermissionAccess::create($permissionKey);
}

?>

<div class="ccm-ui" id="ccm-permission-detail">
<form id="ccm-permissions-detail-form" onsubmit="return ccm_submitPermissionsDetailForm()" method="post" action="<?php echo $permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL()?>">

<input type="hidden" name="paID" value="<?php echo $pa->getPermissionAccessID()?>" />

<?php $workflows = Workflow::getList();?>

<?php Loader::element('permission/message_list'); ?>

<?php
$tabs = array();

 if ($permissionKey->hasCustomOptionsForm() || ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0)) { ?>
	<?php
	$tabs[] = array('access-types', t('Access'), true);
	if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) {
		$tabs[] = array('workflow', t('Workflow'));
	}
	if ($permissionKey->hasCustomOptionsForm()) {
		$tabs[] = array('custom-options', t('Details'));
	}
	?>
	<?php echo Loader::helper('concrete/interface')->tabs($tabs);?>
<?php } ?>
	
<?php if ($permissionKey->getPermissionKeyDisplayDescription()) { ?>
<div class="dialog-help">
<?php echo $permissionKey->getPermissionKeyDisplayDescription()?>
</div>
<?php } ?>


<div id="ccm-tab-content-access-types" <?php if (count($tabs) > 0) { ?>class="ccm-tab-content"<?php } ?>>
<?php
$pkCategoryHandle = $permissionKey->getPermissionKeyCategoryHandle();
$accessTypes = $permissionKey->getSupportedAccessTypes();
Loader::element('permission/access/list', array('pkCategoryHandle' => $pkCategoryHandle, 'permissionAccess' => $pa, 'accessTypes' => $accessTypes)); ?>
</div>

<?php if ($permissionKey->hasCustomOptionsForm()) { ?>
<div id="ccm-tab-content-custom-options" class="ccm-tab-content">

<?php if ($permissionKey->getPackageID() > 0) { ?>
	<?php Loader::packageElement('permission/keys/' . $permissionKey->getPermissionKeyHandle(), $permissionKey->getPackageHandle(), array('permissionAccess' => $pa)); ?>
<?php } else { ?>
	<?php Loader::element('permission/keys/' . $permissionKey->getPermissionKeyHandle(), array('permissionAccess' => $pa)); ?>
<?php } ?>

</div>

<?php } ?>

<?php if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) { ?>
	<?php
	$selectedWorkflows = $pa->getWorkflows();
	$workflowIDs = array();
	foreach($selectedWorkflows as $swf) {
		$workflowIDs[] = $swf->getWorkflowID();
	}
	?>
		
	<div id="ccm-tab-content-workflow" class="ccm-tab-content">
			<h3><?php echo t('Attach a workflow to this permission?')?></h3>
			<div class="clearfix">
			<label><?php echo t('Workflow')?></label>
			<div class="input">
			<ul class="inputs-list">
				<?php foreach($workflows as $wf) { ?>
					<li><label><input type="checkbox" name="wfID[]" value="<?php echo $wf->getWorkflowID()?>" <?php if (count($wf->getRestrictedToPermissionKeyHandles()) > 0 && (!in_array($permissionKey->getPermissionKeyHandle(), $wf->getRestrictedToPermissionKeyHandles()))) { ?> disabled="disabled" <?php } ?>
					<?php if (in_array($wf->getWorkflowID(), $workflowIDs)) { ?> checked="checked" <?php } ?> /> <span><?php echo $wf->getWorkflowName()?></span></label></li>
				<?php } ?>
			</ul>
			</div>
			</div>
	</div>
<?php } ?>

	<div class="dialog-buttons">
		<a href="javascript:void(0)" class="btn" onclick="jQuery.fn.dialog.closeTop()"><?php echo t('Cancel')?></a>
		<button type="submit" class="btn primary ccm-button-right" class="btn primary" onclick="$('#ccm-permissions-detail-form').submit()"><?php echo t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
</form>
</div>

<script type="text/javascript">

$(function() {
	
	ccm_addAccessEntity = function(peID, pdID, accessType) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.showLoader();
	
		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}
	
		$.get('<?php echo $permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("add_access_entity")?>&paID=<?php echo $pa->getPermissionAccessID()?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) { 
			$.get(ccm_permissionDialogURL + qs + 'paID=<?php echo $pa->getPermissionAccessID()?>&message=entity_added&pkID=<?php echo $permissionKey->getPermissionKeyID()?>', function(r) { 
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
	}
	
	ccm_deleteAccessEntityAssignment = function(peID) {
		jQuery.fn.dialog.showLoader();

		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}
		
		$.get('<?php echo $permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("remove_access_entity")?>&paID=<?php echo $pa->getPermissionAccessID()?>&peID=' + peID, function() { 
			$.get(ccm_permissionDialogURL + qs + 'paID=<?php echo $pa->getPermissionAccessID()?>&message=entity_removed&pkID=<?php echo $permissionKey->getPermissionKeyID()?>', function(r) { 
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
	}

	ccm_submitPermissionsDetailForm = function() {
		jQuery.fn.dialog.showLoader();
		$("#ccm-permissions-detail-form").ajaxSubmit(function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
			// now we reload the permission key to use the new permission assignment
			$('#ccm-permission-grid-cell-<?php echo $permissionKey->getPermissionKeyID()?>').load(
				'<?php echo $permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("display_access_cell")?>&paID=<?php echo $pa->getPermissionAccessID()?>', function() {
					$('#ccm-permission-grid-name-<?php echo $permissionKey->getPermissionKeyID()?> a').attr('data-paID', '<?php echo $pa->getPermissionAccessID()?>');	
					if (typeof(ccm_submitPermissionsDetailFormPost) != 'undefined') {
						ccm_submitPermissionsDetailFormPost();
					}
				}
			);
		});
		return false;
	}
	
	
	<?php if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'custom_options_saved') { ?>
		$('a[data-tab=custom-options]').click();
	<?php } ?>

	<?php if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'workflows_saved') { ?>
		$('a[data-tab=workflow]').click();
	<?php } ?>


});
</script>
