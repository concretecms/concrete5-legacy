<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Allowed File Types'), false, false, false)?>
	<form method="post" id="file-access-extensions" action="<?php echo $this->url('/dashboard/system/permissions/file_types', 'file_access_extensions')?>">
	<div class="ccm-pane-body">
			<?php echo $validation_token->output('file_access_extensions');?>
		<div class="clearfix">
			<label for="file-access-file-types"><?php echo t('Allowed File Types')?></label>
			<div class="input">
				<?php if (UPLOAD_FILE_EXTENSIONS_CONFIGURABLE) { ?>
					<?php echo $form->textarea('file-access-file-types', $file_access_file_types, array('rows' => 5, 'class' => 'xxlarge'));?>
	
				<?php } else { ?>
					<?php echo $file_access_file_types?>
				<?php } ?>
				<span class="help-block">
				<?php echo t('Only files with the following extensions will be allowed. Separate extensions with commas. Periods and spaces will be ignored.')?>
				</span>
			</div>
		</div>
	</div>
	<div class="ccm-pane-footer">
		<?php echo $concrete_interface->submit(t('Save'), 'file-access-extensions', 'right', 'primary'); ?>
	</div>
	</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>