<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('System &amp; Settings'));?>
<form>
<?php
foreach($categories as $cat) { ?>

	<div class="page-header">
	<h3><a href="<?php echo Loader::helper('navigation')->getLinkToCollection($cat)?>"><?php echo $cat->getCollectionName()?></a>
	<small><?php echo $cat->getCollectionDescription()?></small>
	</h3>
	</div>
	
	<?php
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead() && $subcat->getAttribute('exclude_nav') != 1) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	<div class="clearfix">
	
	<?php foreach($show as $subcat) { ?>
	
	<div class="span4">
		<a href="<?php echo Loader::helper('navigation')->getLinkToCollection($cat)?>"><?php echo $subcat->getCollectionName()?></a>
	</div>
	
	<?php } ?>
	
	</div>
	
	<?php } ?>

<?php } ?>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
