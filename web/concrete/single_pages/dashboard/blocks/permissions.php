<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

	<?php ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<?php $help = ob_get_contents(); ?>
	<?php ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Block &amp; Stack Permissions'), $help, 'span10 offset1', false)?>
	<form method="post" action="<?=$this->action('save')?>">
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	<div class="ccm-pane-body">
	<?php
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<?php Loader::element('permission/lists/block_type')?>
	<?php } else { ?>
		<p><?=t('You cannot access these permissions.')?></p>
	<?php } ?>
	</div>
	<div class="ccm-pane-footer">
		<a href="<?=$this->url('/dashboard/blocks/permissions')?>" class="btn"><?=t('Cancel')?></a>
		<button type="submit" value="<?=t('Save')?>" class="btn primary ccm-button-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
	</form>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>