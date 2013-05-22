<?php defined('C5_EXECUTE') or die("Access Denied.");
$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
if(!strlen($searchInstance)) {
	$searchInstance = 'user';
}

$sk = PermissionKey::getByHandle('access_user_search');
$ek = PermissionKey::getByHandle('activate_user');

$form = Loader::helper('form');
$ih = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if (!$tp->canActivateUser()) { 
	die(t("Access Denied."));
}

$users = array();
if (is_array($_REQUEST['uID'])) {
	foreach($_REQUEST['uID'] as $uID) {
		$ui = UserInfo::getByID($uID);
		$users[] = $ui;
	}
}

foreach($users as $ui) {
	if (!$sk->validate($ui)) { 
		die(t("Access Denied."));
	}
}

if ($_POST['task'] == 'activate') {
	$workflowAttached = false;
	
	// check if workflow is attached to this request
	$pk = PermissionKey::getByHandle('activate_user');
	$pa = $pk->getPermissionAccessObject();
	$workflows = $pa->getWorkflows();
	$workflowAttached = count((array)$workflows);
	
	if($workflowAttached) {
		// workflow is attached
		$alertMessage = t('User Settings saved. You must complete the workflow before this change is active.');
	} else {
		// workflow is not attached
		$alertMessage = t('User Settings saved.');
	}
	
	foreach($users as $ui) {
		$workflowRequestActions = array();
		
		// when this request is attached to workflow, featch triggered workflow request actions of current user
		// when this request is not attached to workflow, do nothing
		if($workflowAttached) {
			$workflowList = UserWorkflowProgress::getList($ui->getUserID());
			
			if (count($workflowList) > 0) {
				foreach($workflowList as $wp) {
					$wr = $wp->getWorkflowRequestObject();
					$workflowRequestActions[] = $wr->getRequestAction();
				}
			}
		}
	
		if(!$ui->isActive() && !in_array('activate',$workflowRequestActions)) {
			$ui->triggerActivate();
		}
	}
		
	echo Loader::helper('json')->encode(array('error'=>false, 'alertMessage' => $alertMessage));
	exit;
} 

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-user-bulk-activate-wrapper">
<? } ?>

	<div id="ccm-user-activate" class="ccm-ui">
		<form method="post" id="ccm-user-bulk-activate" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/users/bulk_activate">
			<?php
			echo $form->hidden('task','activate');
			foreach($users as $ui) {
				echo $form->hidden('uID[]' , $ui->getUserID());
			}
			?>
			<?php echo t('Are you sure you would like to activate the following users?');?><br/><br/>
			<?php Loader::element('users/confirm_list',array('users'=>$users)); ?>
		</form>	
	</div>
	<div class="dialog-buttons">
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
		<?=$ih->button_js(t('Activate'), 'ccm_userBulkActivate()', 'right', 'btn primary')?>
	</div>
<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? } ?>

<script type="text/javascript">
ccm_userBulkActivate = function() { 
	jQuery.fn.dialog.showLoader();
	$("#ccm-user-bulk-activate").ajaxSubmit(function(resp) {
		var respObj = jQuery.parseJSON(resp);
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();
		ccm_deactivateSearchResults('<?=$searchInstance?>');
		ccmAlert.hud(respObj.alertMessage, 2000, 'success', ccmi18n.user_activate);
		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(r) {
		       ccm_parseAdvancedSearchResponse(r, '<?=$searchInstance?>');
		});
	});
};
</script>
