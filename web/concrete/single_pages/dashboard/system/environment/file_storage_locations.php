	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Storage Locations'), false, false, false)?>

	<form method="post" id="file-access-storage" action="<?=$this->url('/dashboard/system/environment/file_storage_locations', 'save')?>">
	<div class="ccm-pane-body">
			<?=$validation_token->output('file_storage');?>
			<fieldset>
			<legend><?=t('Standard File Location')?></legend>
			<div class="clearfix">
				<label for="DIR_FILES_UPLOADED"><?=t('Path')?></label>
				<div class="input">
				<?=$form->text('DIR_FILES_UPLOADED', DIR_FILES_UPLOADED)?>
				</div>
			</div>
			</fieldset>
			<fieldset>
			<legend><?=t('Alternate Storage Directory')?></legend>
			<div class="clearfix">
				<label for="fslName"><?=t('Location Name')?></label>
				<div class="input">
					<?=$form->text('fslName', $fslName, array('style' => 'width:530px'))?>
				</div>
			</div>
			<div class="clearfix">
				<label for="fslDirectory"><?=t('Path')?></label>
				<div class="input">
					<?=$form->text('fslDirectory', $fslDirectory, array('rows' => '2', 'style' => 'width:530px'))?>
				</div>
			</div>
			</fieldset>
	</div>
	<div class="ccm-pane-footer">
			<?php		
				$b1 = $concrete_interface->submit(t('Save'), 'file-storage', 'right', 'primary');
				print $b1;
			?>		
	</div>
	</form>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
