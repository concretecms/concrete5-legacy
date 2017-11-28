<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Interface Settings'), false, 'span10 offset1', false)?>
<form method="post" class="form-horizontal" action="<?php echo $this->action('save_interface_settings')?>" enctype="multipart/form-data" >
<div class="ccm-pane-body">
<?php echo Loader::helper('validation/token')->output('save_interface_settings')?>

<?php if (!defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED') && !defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC')) { ?>

<fieldset>
	<legend style="margin-bottom: 0px"><?php echo t('Dashboard')?></legend>
	<div class="control-group">
		<label class="control-label"><?php echo t('Background Image')?></label>
		<div class="controls">
			<label class="radio"><?php echo $form->radio('DASHBOARD_BACKGROUND_IMAGE', '', $DASHBOARD_BACKGROUND_IMAGE)?> <span><?php echo t('Pull a picture of the day from concrete5.org (Default)')?></span></label>
			<label class="radio"><?php echo $form->radio('DASHBOARD_BACKGROUND_IMAGE', 'none', $DASHBOARD_BACKGROUND_IMAGE)?> <span><?php echo t('None')?></span></label>
			<label class="radio"><?php echo $form->radio('DASHBOARD_BACKGROUND_IMAGE', 'custom', $DASHBOARD_BACKGROUND_IMAGE)?> <span><?php echo t('Specify Custom Image')?></span>
					<div id="custom-background-image" <?php if ($DASHBOARD_BACKGROUND_IMAGE != 'custom') { ?>style="display: none" <?php } ?>>
						<br/>
						<?php echo Loader::helper('concrete/asset_library')->image('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID', DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID, t('Choose Image'), $imageObject)?>
					</div>
			</label>
		</div>
	</div>
</fieldset>


<script type="text/javascript">
$(function() {
	$("input[name=DASHBOARD_BACKGROUND_IMAGE]").change(function() {
		if ($("input[name=DASHBOARD_BACKGROUND_IMAGE]:checked").val() == 'custom') { 
			$("#custom-background-image").show();
		} else {
			$("#custom-background-image").hide();
		}
	});
});
</script>

<?php  } else { ?>
    <?php echo t('Options disabled, interface settings are specified in config/site.php.')?>
<?php } ?>

</div>
<div class="ccm-pane-footer">
	<?php echo Loader::helper('concrete/interface')->submit(t('Save'), 'submit', 'right', 'primary')?>
</div>
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
