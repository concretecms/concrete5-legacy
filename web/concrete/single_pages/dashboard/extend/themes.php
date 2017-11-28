<?php
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if ($tp->canInstallPackages()) {
	$mi = Marketplace::getInstance();
}
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Browse Themes'), t('Get more themes from concrete5.org.'), false, false);?>
<div class="ccm-pane-options">
	<?php echo Loader::element('marketplace/search_form', array('action' => $this->url('/dashboard/extend/themes'), 'sets' => $sets, 'sortBy' => $sortBy));?>
</div>
<div class="ccm-pane-body">
	<?php echo Loader::element('marketplace/results', array('type' => 'themes', 'items' => $items));?>
</div>	

<div class="ccm-pane-footer" id="ccm-marketplace-browse-footer"><?php echo $list->displayPagingV2()?></div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>

<script type="text/javascript">
$(function() {
	ccm_marketplaceBrowserInit(); 
});
</script>