<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Dashboard'), false, false, false, false, false, false); ?>

<div class="ccm-pane-body" style="padding-bottom: 0px">


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
		if ($catp->canRead() && !$subcat->getAttribute('exclude_nav')) { 
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

	<div class="clearfix"></div>
	
</div>

<div class="ccm-pane-footer">
<?php
	$newsPage = Page::getByPath('/dashboard/news');
	$newsPageP = new Permissions($newsPage);
	if ($newsPageP->canRead()) { ?>
		<div><a href="<?php echo Loader::helper('navigation')->getLinkToCollection($newsPage, false, true)?>"><strong><?php echo t('News')?></strong></a> - <?php echo t('Learn about your site and concrete5.')?></div>
	<?php }

	$settingsPage = Page::getByPath('/dashboard/system');
	$settingsPageP = new Permissions($settingsPage);
	if ($settingsPageP->canRead()) { ?>
		<div><a href="<?php echo Loader::helper('navigation')->getLinkToCollection($settingsPage, false, true)?>"><strong><?php echo t('System &amp; Settings')?></strong></a> - <?php echo t('Secure and setup your site.')?></div>
	<?php }
	
	$tpa = new TaskPermission();
	$extendPage = Page::getByPath('/dashboard/extend');
	$extendPageP = new Permissions($extendPage);
	if ($tpa->canInstallPackages() && $extendPageP->canRead()) { ?>
		<div><a href="<?php echo View::url('/dashboard/extend') ?>"><strong><?php echo t("Extend concrete5") ?></strong></a> – 
		<?php if (ENABLE_MARKETPLACE_SUPPORT) { ?>
		<?php echo sprintf(t('<a href="%s">Install</a>, <a href="%s">update</a> or download more <a href="%s">themes</a> and <a href="%s">add-ons</a>.'),
			View::url('/dashboard/extend/install'),
			View::url('/dashboard/extend/update'),
			View::url('/dashboard/extend/themes'),
			View::url('/dashboard/extend/add-ons')); ?>
		<?php } else { ?>
		<?php echo sprintf(t('<a href="%s">Install</a> or <a href="%s">update</a> packages.'),
			View::url('/dashboard/extend/install'),
			View::url('/dashboard/extend/update')); 
		} ?>
		</div>
	<?php } ?>
	
</div>
<script type="text/javascript">
$(function() {
	ccm_dashboardEqualizeMenus();
	$(window).resize(function() {
		ccm_dashboardEqualizeMenus();
	});
});
</script>


<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
