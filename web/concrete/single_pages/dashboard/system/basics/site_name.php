<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Site Name'), false, 'span10 offset1', false)?>
<form method="post" class="form-horizontal" id="site-form" action="<?php echo $this->action('update_sitename')?>">
<div class="ccm-pane-body">
	<?php echo $this->controller->token->output('update_sitename')?>
	<div class="control-group">
	<?php echo $form->label('SITE', t('Site Name'))?>
	<div class="controls">
	<?php echo $form->text('SITE', $site, array('class' => 'span4'))?>
	</div>
	</div>
</div>
<div class="ccm-pane-footer">
	<?php
	print $interface->submit(t('Save'), 'site-form', 'right','primary');
	?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
