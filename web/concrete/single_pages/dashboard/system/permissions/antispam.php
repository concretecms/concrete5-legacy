<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Spam Control'), false, 'span10 offset1', (!is_object($activeLibrary) || (!$activeLibrary->hasOptionsForm())))?>
<form method="post" id="site-form" action="<?php echo $this->action('update_library')?>">
<?php if (is_object($activeLibrary) && $activeLibrary->hasOptionsForm()) { ?>
	<div class="ccm-pane-body">
<?php } ?>

	<?php echo $this->controller->token->output('update_library')?>
	<?php if (count($libraries) > 0) { ?>

		<div class="clearfix">
		<?php echo $form->label('activeLibrary', t('Active Library'))?>
		<div class="input">
		<?php 
		$activeHandle = '';
		if (is_object($activeLibrary)) {
			$activeHandle = $activeLibrary->getSystemAntispamLibraryHandle();
		}
		?>
		
		<?php echo $form->select('activeLibrary', $libraries, $activeHandle, array('class' => 'span4'))?>
		</div>
		</div>
		
		<?php if (is_object($activeLibrary)) {
			if ($activeLibrary->hasOptionsForm()) {
				if ($activeLibrary->getPackageID() > 0) { 
					Loader::packageElement('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form', $activeLibrary->getPackageHandle());
				} else {
					Loader::element('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form');
				}
				
				?>
				
				<div class="clearfix">
				<?php echo $form->label('ANTISPAM_LOG_SPAM', t('Log settings'))?>
				<div class="input">
				<ul class="inputs-list">
					<li><label><?php echo $form->checkbox('ANTISPAM_LOG_SPAM', 1, Config::get('ANTISPAM_LOG_SPAM'))?> <span><?php echo t('Log entries marked as spam.')?></span></label>
						<span class="help-block"><?php echo t('Logged entries can be found in <a href="%s" style="color: #bfbfbf; text-decoration: underline">Dashboard > Reports > Logs</a>', $this->url('/dashboard/reports/logs'))?></span>
					</li>
				</ul>
				</div>
				</div>

				<div class="clearfix">
				<?php echo $form->label('ANTISPAM_NOTIFY_EMAIL', t('Email Notification'))?>
				<div class="input">
					<?php echo $form->text('ANTISPAM_NOTIFY_EMAIL', Config::get('ANTISPAM_NOTIFY_EMAIL'))?>
				<span class="help-block"><?php echo t('Any email address in this box will be notified when spam is detected.')?></span>
				</div>

				</div>
				
				
				<?php
			}
		} ?>


	<?php } else { ?>
		<p><?php echo t('You have no anti-spam libraries installed.')?></p>
	<?php } ?>

<?php if (is_object($activeLibrary) && $activeLibrary->hasOptionsForm()) { ?>
	</div>
	<div class="ccm-pane-footer">
		<?php echo Loader::helper('concrete/interface')->submit(t('Save Additional Settings'), 'submit', 'right', 'primary')?>
	</div>
<?php } ?>	
</form>

<script type="text/javascript">
$(function() {
	$('select[name=activeLibrary]').change(function() {
		$('#site-form').submit();
	});
});
</script>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper( (!is_object($activeLibrary) || (!$activeLibrary->hasOptionsForm())));?>
