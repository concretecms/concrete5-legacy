<?php defined('C5_EXECUTE') or die('Access Denied'); ?>
<script type="text/javascript">
CCM_LAUNCHER_SITEMAP = 'search'; // we need this for when we are moving and copying
CCM_SEARCH_INSTANCE_ID = '<?php echo $searchInstance?>';
</script>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Search Pages'), t('Search the pages of your site and perform bulk actions on them.'), false, false);?>

	<?php
	$dh = Loader::helper('concrete/dashboard/sitemap');
	if ($dh->canRead()) { ?>
	
		<div class="ccm-pane-options" id="ccm-<?php echo $searchInstance?>-pane-options">
			<?php Loader::element('pages/search_form_advanced', array('columns' => $columns, 'searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
		</div>
	
		<?php Loader::element('pages/search_results', array('columns' => $columns, 'searchInstance' => $searchInstance, 'searchType' => 'DASHBOARD', 'pages' => $pages, 'pageList' => $pageList, 'pagination' => $pagination)); ?>
	
	<?php } else { ?>
		<div class="ccm-pane-body">
			<p><?php echo t("You must have access to the dashboard sitemap to search pages.")?></p>
		</div>	
	
	<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>