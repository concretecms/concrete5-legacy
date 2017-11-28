<?php
defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
if ($showDownloadBox && $downloadableUpgradeAvailable) { ?>
	<?php echo $h->getDashboardPaneHeaderWrapper(t('Download Update'), false, 'span8 offset2');?>
	<?php if (!defined('MULTI_SITE') || MULTI_SITE == false) { ?>
		<a href="<?php echo $this->action('check_for_updates')?>" class="btn" style="float: right"><?php echo t('Check For Updates')?></a>
	<?php } ?>
		<h2><?php echo t('Currently Running %s',config::get('SITE_APP_VERSION'))?></h2>
		<div class="clearfix">
		</div>
		<br/>
		<h2><?php echo t('Available Update')?></h2>
		<form method="post" action="<?php echo $this->action('download_update')?>" id="ccm-download-update-form">
		
			<?php echo Loader::helper('validation/token')->output('download_update')?>
			<?php echo Loader::helper('concrete/interface')->submit(t('Download'), 'ccm-download-update-form', 'right', 'primary')?>
		
			<h3><?php echo t('Version: %s', $update->version)?>. <?php echo t('Release Date: %s', date(t('F d, Y'), strtotime($update->date)))?></h3>
			<hr/>
			<div id="ccm-release-notes">
			<?php echo $update->notes?>
			</div>
			<hr/>
			<span class="notes"><?php echo t('Note: Downloading an update will NOT automatically install it.')?></span>
		
		</form>
	<?php echo $h->getDashboardPaneFooterWrapper();?>
<?php } else if (count($updates)) { ?>
	<?php echo $h->getDashboardPaneHeaderWrapper(t('Install Local Update'),false,'span8 offset2',false);?>
		<div class="ccm-pane-body">
			<?php print '<strong>' . t('Make sure you <a href="%s">backup your database</a> before updating.', $this->url('/dashboard/system/backup_restore/backup')) . '</strong><br/>';
			$ih = Loader::helper('concrete/interface');

			if (count($updates) == 1) { ?>
					<p><?php echo t('An update is available. Click below to update to <strong>%s</strong>.', $updates[0]->getUpdateVersion())?></p>
					<span class="label"><?php echo t('Current Version %s',config::get('SITE_APP_VERSION'))?></span>
				</div>
				<div class="ccm-pane-footer">
					<form method="post" action="<?php echo $this->action('do_update')?>" id="ccm-update-form">
						<input type="hidden" name="updateVersion" value="<?php echo $updates[0]->getUpdateVersion()?>" />
						<?php echo $ih->submit(t('Update'), 'maintenance-mode-form', 'right', 'primary')?>
					</form>
				</div>
			<?php } else { ?>
				<p><?php echo t('Several updates are available. Please choose the desired update from the list below.')?></p>
					<span class="label"><?php echo t('Current Version')?> <?php echo config::get('SITE_APP_VERSION')?></span>
				<form method="post" action="<?php echo $this->action('do_update')?>" id="ccm-update-form">
				<?php  $checked = true;
					foreach($updates as $upd) { ?>
						<div class="ccm-dashboard-radio"><input type="radio" name="updateVersion" value="<?php echo $upd->getUpdateVersion()?>" <?php echo (!$checked?'':"checked")?> />
							<?php echo $upd->getUpdateVersion()?>
						</div>
						<?php $checked = false;
					} ?>
					</div>
					<div class="ccm-pane-footer">
						<?php echo $ih->submit(t('Update'),false, 'right', 'primary')?>
					</div>
				</form>
			<?php } ?>
		</div>
	<?php echo $h->getDashboardPaneFooterWrapper(false);?>
	<div class="clearfix">&nbsp;</div>
<?php } else { ?>
	<?php echo $h->getDashboardPaneHeaderWrapper(t('Update concrete5'), false, 'span8 offset2');?>
	<?php if (!defined('MULTI_SITE') || MULTI_SITE == false) { ?>
		<a href="<?php echo $this->action('check_for_updates')?>" class="btn" style="float: right"><?php echo t('Check For Updates')?></a>
	<?php } ?>
		<h2><?php echo t('Currently Running %s',config::get('SITE_APP_VERSION'))?></h2>
		<div class="clearfix">
		</div>
		<br/>
		
		<p><?php echo t('No updates available.')?></p>

	<?php echo $h->getDashboardPaneFooterWrapper();?>
<?php } ?>