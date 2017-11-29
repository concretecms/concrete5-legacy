<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Allowed File Types'), false, 'span8 offset2', false)?>

	<form method="post" id="file-access-extensions" action="<?php echo $this->url('/dashboard/system/permissions/file_types', 'file_access_extensions')?>">
	<div class="ccm-pane-body">
			<?php echo $validation_token->output('file_access_extensions');?>
			<p>
			<?php echo t('Only files with the following extensions will be allowed. Separate extensions with commas. Periods and spaces will be ignored.')?>
			</p>
			<?php if (UPLOAD_FILE_EXTENSIONS_CONFIGURABLE) { ?>
				<?php echo $form->textarea('file-access-file-types',$file_access_file_types,array('rows'=>'5','class' => 'span7'));?>

			<?php } else { ?>
				<?php echo $file_access_file_types?>
			<?php } ?>
	</div>
	<div class="ccm-pane-footer">
		<?php print $concrete_interface->submit(t('Save'), 'file-access-extensions', 'right', 'primary'); ?>
	</div>
	</form>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>