<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

	<?php ob_start(); ?>
	<?php echo Loader::element('permission/help');?>
	<?php $help = ob_get_contents(); ?>
	<?php ob_end_clean(); ?>
	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Permissions'), $help, 'span8 offset2', false)?>
	<form method="post" action="<?php echo $this->action('save')?>">
	<?php echo Loader::helper('validation/token')->output('save_permissions')?>
	<div class="ccm-pane-body">
	<?php
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<?php Loader::element('permission/lists/user')?>
	<?php } else { ?>
		<p><?php echo t('You cannot access task permissions.')?></p>
	<?php } ?>
	</div>
	<div class="ccm-pane-footer">
		<a href="<?php echo $this->url('/dashboard/system/permissions/users')?>" class="btn"><?php echo t('Cancel')?></a>
		<button type="submit" value="<?php echo t('Save')?>" class="btn primary ccm-button-right"><?php echo t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
	</form>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>