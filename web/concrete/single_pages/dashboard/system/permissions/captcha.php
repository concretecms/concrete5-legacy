<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Captcha Settings'), false, 'span10 offset1', (!is_object($activeCaptcha) || (!$activeCaptcha->hasOptionsForm())))?>
<form method="post" id="site-form" action="<?php echo $this->action('update_captcha')?>">
<?php if (is_object($activeCaptcha) && $activeCaptcha->hasOptionsForm()) { ?>
	<div class="ccm-pane-body">
<?php } ?>
<?php echo $this->controller->token->output('update_captcha')?>
	<?php if (count($captchas) > 0) { ?>

		<div class="clearfix">
		<?php echo $form->label('activeCaptcha', t('Active Captcha'))?>
		<div class="input">
		<?php 
		$activeHandle = '';
		if (is_object($activeCaptcha)) {
			$activeHandle = $activeCaptcha->getSystemCaptchaLibraryHandle();
		}
		?>
		
		<?php echo $form->select('activeCaptcha', $captchas, $activeHandle, array('class' => 'span4'))?>
		</div>
		</div>
		
		<?php if (is_object($activeCaptcha)) {
			if ($activeCaptcha->hasOptionsForm()) {
				if ($activeCaptcha->getPackageID() > 0) { 
					Loader::packageElement('system/captcha/' . $activeCaptcha->getSystemCaptchaLibraryHandle() . '/form', $activeCaptcha->getPackageHandle());
				} else {
					Loader::element('system/captcha/' . $activeCaptcha->getSystemCaptchaLibraryHandle() . '/form');
				}
			}
		} ?>


	<?php } else { ?>
		<p><?php echo t('You have no captcha libraries installed.')?></p>
	<?php } ?>

<?php if (is_object($activeCaptcha) && $activeCaptcha->hasOptionsForm()) { ?>
	</div>
	<div class="ccm-pane-footer">
		<?php echo Loader::helper('concrete/interface')->submit(t('Save Additional Settings'), 'submit', 'right', 'primary')?>
	</div>
<?php } ?>	

	
</form>

<script type="text/javascript">
$(function() {
	$('select[name=activeCaptcha]').change(function() {
		$('#site-form').submit();
	});
});
</script>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper((!is_object($activeCaptcha) || (!$activeCaptcha->hasOptionsForm())));?>
