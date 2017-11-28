<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Storage Locations'), false, 'span6 offset3', false)?>

	<form method="post" class="form-inline" id="file-access-storage" action="<?php echo $this->url('/dashboard/system/environment/file_storage_locations', 'save')?>">
	<div class="ccm-pane-body">
			<?php echo $validation_token->output('file_storage');?>
			<fieldset>
			<legend><?php echo t('Standard File Location')?></legend>
			<div class="control-group">
			<label class="control-label" for="DIR_FILES_UPLOADED"><?php echo t('Path')?></label>
			<div class="controls">
			<?php echo $form->text('DIR_FILES_UPLOADED', DIR_FILES_UPLOADED, array('rows'=>'2','class' => 'span5'))?>
			</div>
			</div>
			
			</fieldset>
			<fieldset>
			<legend><?php echo t('Alternate Storage Directory')?></legend>
			
			<div class="control-group">
			<label for="fslName" class="control-label"><?php echo t('Location Name')?></label>
			<div class="controls">
			<?php echo $form->text('fslName', $fslName, array('class' => 'span5'))?>
			</div></div>
			<div class="control-group">
			<label for="fslDirectory" class="control-label"><?php echo t('Path')?></label>
			<div class="controls">
			<?php echo $form->text('fslDirectory', $fslDirectory, array('rows' => '2', 'class' => 'span5'))?>
			</div></div>
			</fieldset>
	</div>
	<div class="ccm-pane-footer">
		<?php		
			$b1 = $concrete_interface->submit(t('Save'), 'file-storage', 'right', 'primary');
			print $b1;
		?>		
		<?php if (is_object($fsl)) { ?>
			<button type="submit" name="delete" value="1" onclick="return confirm('<?php echo t('Are you sure? (Note: this will not remove any files, it will simply remove the pointer to the directory, and reset any files that are set to this location.)')?>')" class="pull-right btn btn-danger"><?php echo t('Delete Alternate')?></button>
		<?php } ?>

	</div>
	</form>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
