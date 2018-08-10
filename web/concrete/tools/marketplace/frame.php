<?php  defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
if($valt->validate('marketplace_token', $_REQUEST['ccm_token'])) {
	$tp = new TaskPermission();
	if ($tp->canInstallPackages()) {
		Loader::library('marketplace');
		$mi = Marketplace::getInstance();
		if ($_REQUEST['complete']) {

			Config::save('MARKETPLACE_SITE_TOKEN', $_POST['csToken']);
			Config::save('MARKETPLACE_SITE_URL_TOKEN', $_POST['csURLToken']);

			?>
			<script type="text/javascript">
				<?php if ($_REQUEST['task'] == 'get') { ?>
					parent.ccm_getMarketplaceItem({mpID: '<?php echo $_REQUEST['mpID']?>', token: '<?php echo $valt->generate('marketplace_token')?>', closeTop: true});
				<?php } else if ($_REQUEST['task'] == 'open_theme_launcher') { ?>
					parent.ccm_openThemeLauncher();
				<?php } else if ($_REQUEST['task'] == 'open_addon_launcher') { ?>
					parent.ccm_openAddonLauncher();
				<?php } else if ($_REQUEST['task'] == 'get_item_details') { ?>
					parent.jQuery.fn.dialog.closeTop();
					parent.ccm_getMarketplaceItemDetails('<?php echo $_REQUEST['mpID']?>');
				<?php } ?>
			</script>
		<?php } else { ?>
			<script type="text/javascript" src="<?php echo ASSETS_URL_JAVASCRIPT?>/jquery.postmessage.js"></script>
			<?php
			$completeURL = BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/marketplace/frame?complete=1&task=' . $_REQUEST['task'] . '&mpID=' . $_REQUEST['mpID'] . '&ccm_token=' . $valt->generate('marketplace_token');
			print $mi->getMarketplaceFrame('100%', '100%', $completeURL);
		}
	} else {
		print t('You do not have permission to connect to the marketplace.');
	}
} else {
	print $valt->getErrorMessage();
}