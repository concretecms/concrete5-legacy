<?php
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
	$mi = Marketplace::getInstance();
}

$pkgRemote = array();
$pkgLocal = array();
if (ENABLE_MARKETPLACE_SUPPORT && is_object($mi)) {
	if ($mi->isConnected()) { 
		$pkgArray = Package::getInstalledList();
		foreach($pkgArray as $pkg) {
			if ($pkg->isPackageInstalled() && version_compare($pkg->getPackageVersion(), $pkg->getPackageVersionUpdateAvailable(), '<')) { 
				$pkgRemote[] = $pkg;
			}
		}
	}
}
$pkgAvailableArray = Package::getLocalUpgradeablePackages();
foreach($pkgAvailableArray as $pkg) {
	if (!in_array($pkg, $pkgRemote)) {
		$pkgLocal[] = $pkg;
	}
}

?>
		<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Update Add-Ons'));?>

<?php
if (!$tp->canInstallPackages()) { ?>
	<p class="block-message alert-message error"><?php echo t('You do not have access to download themes or add-ons from the marketplace.')?></p>
<?php } else { ?>

		<?php if (count($pkgLocal) == 0 && count($pkgRemote) == 0) { ?>
			<p><?php echo t('No updates for your add-ons are available.')?></p>
		<?php } else { ?>

			<table class="table table-striped">
			<?php foreach($pkgRemote as $pkg) { 

				$rpkg = MarketplaceRemoteItem::getByHandle($pkg->getPackageHandle());
			?>
			
				<tr>
					<td class="ccm-marketplace-list-thumbnail" rowspan="2"><img src="<?php echo $ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?php echo $pkg->getPackageName()?></h3><p><?php echo $pkg->getPackageDescription()?></p>
					<p><strong><?php echo t('New Version: %s. Upgrading from: %s.', $pkg->getPackageVersionUpdateAvailable(), $pkg->getPackageVersion())?></strong></p>

					</td>
					<?php if (!is_object($rpkg)) { ?>
						<td class="ccm-marketplace-list-install-button"><input class="btn" disabled="disabled" type="button" value="<?php echo t('More Information')?>" /> <input class="btn primary" disabled="disabled" type="button" value="<?php echo t('Download and Install')?>" />
					<?php } else { ?>
						<td class="ccm-marketplace-list-install-button"><a class="btn" target="_blank" href="<?php echo $rpkg->getRemoteURL()?>"><?php echo t('More Information')?></a> <?php echo $ch->button(t("Download and Install"), View::url('/dashboard/extend/update', 'prepare_remote_upgrade', $rpkg->getMarketplaceItemID()), "", "primary")?></td>					
					<?php } ?>
				</tr>
				<?php if (is_object($rpkg)) { ?>
				<tr>
					<td colspan="2" style="border-top: 0px">
						<?php $versionHistory = $rpkg->getVersionHistory();?>
						<?php if (trim($versionHistory) != '') { ?>
							<div class="ccm-marketplace-update-changelog">
								<h6><?php echo t('Version History')?></h6>
								<?php echo $versionHistory?>
							</div>
							<div class="ccm-marketplace-item-information-more">
								<a href="javascript:void(0)" onclick="ccm_marketplaceUpdatesShowMore(this)"><?php echo t('More Details')?></a>
							</div>
						<?php } ?>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td colspan="2" style="border-top: 0px">
						<div class="block-message alert-message error"><p><?php echo t('Unable to locate this add-on on concrete5.org')?></p></div>
					</td>
				</tr>
				<?php } ?>		
			<?php }
			
			foreach($pkgLocal as $pkg) { ?>
			
				<tr>
					<td class="ccm-marketplace-list-thumbnail" rowspan="2"><img src="<?php echo $ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?php echo $pkg->getPackageName()?></h3><p><?php echo $pkg->getPackageDescription()?></p>
					<p><strong><?php echo t('New Version: %s. Upgrading from: %s.', $pkg->getPackageVersion(), $pkg->getPackageCurrentlyInstalledVersion())?></strong></p>
					</td>
					<td class="ccm-marketplace-list-install-button"><?php echo $ch->button(t("Update Add-On"), View::url('/dashboard/extend/update', 'do_update', $pkg->getPackageHandle()), "", "primary")?></td>					
				</tr>
				<tr>
					<td colspan="2" style="border-top: 0px">
						<?php $versionHistory = $pkg->getChangelogContents();?>
						<?php if (trim($versionHistory) != '') { ?>
							<div class="ccm-marketplace-update-changelog">
								<h6><?php echo t('Version History')?></h6>
								<?php echo $versionHistory?>
							</div>
							<div class="ccm-marketplace-item-information-more">
								<a href="javascript:void(0)" onclick="ccm_marketplaceUpdatesShowMore(this)"><?php echo t('More Details')?></a>
							</div>
						<?php } ?>
					</td>
				</tr>
				
			<?php } ?>		
			
			</table>
			
		<?php } ?>

<?php } ?>

		<?php
		if (is_object($mi) && $mi->isConnected()) { ?>

			<h3><?php echo t("Project Page")?></h3>
			<p><?php echo t('Your site is currently connected to the concrete5 community. Your project page URL is:')?><br/>
			<a href="<?php echo $mi->getSitePageURL()?>"><?php echo $mi->getSitePageURL()?></a></p>
		
		<?php } else if (is_object($mi) && $mi->hasConnectionError()) { ?>
			
			<?php echo Loader::element('dashboard/marketplace_connect_failed');?>
		
		<?php } else if ($tp->canInstallPackages() && ENABLE_MARKETPLACE_SUPPORT == true) { ?>

			<div class="well" style="padding:10px 20px;">
				<h3><?php echo t('Connect to Community')?></h3>
				<p><?php echo t('Your site is not connected to the concrete5 community. Connecting lets you easily extend a site with themes and add-ons. Connecting enables automatic updates.')?></p>
				<p><a class="btn success" href="<?php echo $this->url('/dashboard/extend/connect', 'register_step1')?>"><?php echo t("Connect to Community")?></a></p>
			</div>
		
		<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
