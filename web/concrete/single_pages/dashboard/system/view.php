<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
$upToPage = Page::getByPath("/dashboard");
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('System &amp; Settings'), false, false, true, -1, $upToPage); ?>

<?php
for ($i = 0; $i < count($categories); $i++) {
	$cat = $categories[$i];
	?>

	<div class="dashboard-icon-list">
	<div class="well" style="visibility: hidden">


	<ul class="nav nav-list">
	<li class="nav-header"><?php echo t($cat->getCollectionName())?></li>

	
	<?php
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead()) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	
	<?php foreach($show as $subcat) { ?>
	
	<li>
	<a href="<?php echo Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><i class="<?php echo $subcat->getAttribute('icon_dashboard')?>"></i> <?php echo t($subcat->getCollectionName())?></a>
	</li>
	
	<?php } ?>
	
	
	<?php } else { ?>
	
	<li>
		<a href="<?php echo Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><i class="<?php echo $cat->getAttribute('icon_dashboard')?>"></i> <?php echo t('Home')?></a>
	</li>
			
	<?php } ?>
	
	</ul>

	</div>
	</div>
	
<?php } ?>


	<div class="clearfix">
	</div>

<script type="text/javascript">
$(function() {
	ccm_dashboardEqualizeMenus();
	$(window).resize(function() {
		ccm_dashboardEqualizeMenus();
	});
});
</script>


<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
