<?php  defined('C5_EXECUTE') or die("Access Denied.");?>
<div class="ccm-ui">
<?php

Loader::library('marketplace');
$mi = Marketplace::getInstance();
$tp = new TaskPermission();
if (!$tp->canInstallPackages()) { ?>
	<p><?php echo t('You do not have permission to download packages from the marketplace.')?></p>
	<?php exit;
} else if (!$mi->isConnected()) { ?>
	<div class="ccm-pane-body-inner">
		<?php Loader::element('dashboard/marketplace_connect_failed')?>
	</div>
<?php } else {	


$cnt = Loader::controller('/dashboard/extend/add-ons');
$cnt->view();
$list = $cnt->get('list');
$items = $list->getPage();
$pagination = $list->getPagination();
$sets = $cnt->get('sets');
$sortBy = $cnt->get('sortBy');
$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/marketplace/add-ons';
?>

	<div class="ccm-pane-options">
		<?php echo Loader::element('marketplace/search_form', array('action' => $bu, 'sets' => $sets, 'sortBy' => $sortBy));?>
	</div>
	<div class="ccm-pane-body" style="margin-left: -10px; margin-right: -10px">
		<?php echo Loader::element('marketplace/results', array('type' => 'addons', 'items' => $items));?>
	</div>	
	<div class="ccm-pane-dialog-pagination"><?php echo $list->displayPagingV2($bu)?></div>

	<script type="text/javascript">

	$(function() {
		ccm_setupMarketplaceDialogForm();
	});
	</script>
<?php } ?>

</div>