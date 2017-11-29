<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $ih = Loader::helper('concrete/interface'); ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Set'), false, 'span10 offset1', false)?>
    <form method="post" class="form-horizontal" id="file-sets-add" action="<?php echo $this->url('/dashboard/files/add_set', 'do_add')?>">
	<div class="ccm-pane-body">
    	
		<?php echo $validation_token->output('file_sets_add');?>

		<div class="control-group">
			<?php echo Loader::helper("form")->label('file_set_name', t('Name'))?>
			<div class="controls">
				<?php echo $form->text('file_set_name','', array('class' => 'span4'))?>
			</div>
		</div>
	</div>
	<div class="ccm-pane-footer">
			<?php echo Loader::helper("form")->submit('add', t('Add'), array('class' => 'ccm-button-right primary'))?>
	</div>
    </form>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>