<?php
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

if ($_POST['task'] == 'design_pages') {
	$json['error'] = false;

	if ($_POST['plID'] > 0) {
		$pl = PageTheme::getByID($_POST['plID']);
	}
	if ($_POST['ctID'] > 0) {
		$ct = CollectionType::getByID($_POST['ctID']);
	}
	if (is_array($_POST['cID'])) {
		foreach($_POST['cID'] as $cID) {
			$c = Page::getByID($cID);
			$cp = new Permissions($c);
			if ($cp->canEditPageTheme($pl)) {
				if ($_POST['plID'] > 0) {
					$c->setTheme($pl);
				}
				if ($_POST['ctID'] > 0 && (!$c->isMasterCollection() && !$c->isGeneratedCollection())) {
					$parentC = Page::getByID($c->getCollectionParentID());
					$parentCP = new Permissions($parentC);
					if ($c->getCollectionID() == HOME_CID || $parentCP->canAddSubCollection($ct)) { 
						$data = array('ctID' => $_POST['ctID']);
						$c->update($data);
					}
				}				
			} else {
				$json['error'] = t('Unable to delete one or more pages.');
			}
		}
	}
	
	$js = Loader::helper('json');
	print $js->encode($json);
	exit;
}

$form = Loader::helper('form');

$pages = array();
if (is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $cID) {
		$pages[] = Page::getByID($cID);
	}
} else {
	$pages[] = Page::getByID($_REQUEST['cID']);
}

$pcnt = 0;
$isMasterCollection = false;
$isSinglePage = false;
$tArray = PageTheme::getGlobalList();
$tArray2 = PageTheme::getLocalList();
$tArray = array_merge($tArray, $tArray2);

foreach($pages as $c) { 
	if ($c->isGeneratedCollection()) {
		$isSinglePage = true;
	}
	if ($c->isMasterCollection()) {
		$isMasterCollection = true;
	}
	$cp = new Permissions($c);
	if ($cp->canEditPageTheme() && $cp->canEditPageType()) {
		$pcnt++;
	}
}

if ($pcnt > 0) { 
	// i realize there are a lot of loops through this, but the logic here is a bit tough to follow if you don't do it this way.
	// first we determine which page types to show, if any
	$notAllowedPageTypes = array();
	$allowedPageTypes = array();
	$ctArray = CollectionType::getList();
	foreach($ctArray as $ct) {
		foreach($pages as $c) {
			if ($c->getCollectionID() != HOME_CID) {
				$parentC = Page::getByID($c->getCollectionParentID());
				$parentCP = new Permissions($parentC);
				if (!$parentCP->canAddSubCollection($ct)) {
					$notAllowedPageTypes[] = $ct;
				}
			}
		}
	}
	foreach($ctArray as $ct) {
		if (!in_array($ct, $notAllowedPageTypes)) {
			$allowedPageTypes[] = $ct;
		}
	}
	$cnt = count($allowedPageTypes);	
	// next we determine which page type to select, if any
	$ctID = -1;
	foreach($pages as $c) {
		if ($c->getCollectionTypeID() != $ctID && $ctID != -1) {
			$ctID = 0;
		} else {
			$ctID = $c->getCollectionTypeID();
		}
	}
	// now we determine which theme to select, if any
	$plID = -1;
	foreach($pages as $c) {
		if ($c->getCollectionThemeID() != $plID && $plID != -1) {
			$plID = 0;
		} else {
			$plID = $c->getCollectionThemeID();
		}
	}
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

?>
<div class="ccm-ui">

<?php if ($pcnt == 0) { ?>
	<?php echo t("You do not have permission to modify the page type or theme on any of the selected pages."); ?>
<?php } else { ?>
	<form id="ccm-<?php echo $searchInstance?>-design-form" method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/design">
	<input type="hidden" name="plID" value="<?php echo $plID?>" />
	<input type="hidden" name="ctID" value="<?php echo $ctID?>" />
	<?php foreach($pages as $c) { ?>
		<input type="hidden" name="cID[]" value="<?php echo $c->getCollectionID()?>" />
	<?php } ?>
	
	<?php echo $form->hidden('task', 'design_pages')?>

	<?php 
	if ($isMasterCollection) { ?>
		<h3><?php echo t('Choose a Page Type')?></h3>
	
		<p>
		<?php echo t("This is the defaults page for the %s page type. You cannot change it.", $c->getCollectionTypeName()); ?>
		</p>
		
	<?php } else if ($isSinglePage) { ?>
	<h3><?php echo t('Choose a Page Type')?></h3>

	<p>
	<?php echo t("This page is a single page, which means it doesn't have a page type associated with it."); ?>
	</p>

	<?php } else if ($cnt > 0) { ?>
	
	<h3><?php echo t('Choose a Page Type')?></h3>

	<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?php echo ceil($cnt/4)?>">
		<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
		<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>

		<div class="ccm-scroller-inner">
			<ul id="ccm-select-page-type" style="width: <?php echo $cnt * 132?>px">
				<?php 
				foreach($allowedPageTypes as $ct) { ?>		
					<?php $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
			
					<li class="<?php echo $class?>"><a href="javascript:void(0)" ccm-page-type-id="<?php echo $ct->getCollectionTypeID()?>"><?php echo $ct->getCollectionTypeIconImage();?></a><span><?php echo $ct->getCollectionTypeName()?></span>
					</li>
				<?php
				}?>
			</ul>
		</div>
	</div>
	<?php } ?>
	
	
	<?php if(ENABLE_MARKETPLACE_SUPPORT){ ?>
		<a href="javascript:void(0)" class="btn ccm-button-right"><?php echo t("Get more themes.")?></a>
	<?php } ?>

	<h3 ><?php echo t('Themes')?></h3>

	<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?php echo ceil(count($tArray)/4)?>">
		<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
		<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>
		
		<div class="ccm-scroller-inner">
			<ul id="ccm-select-theme" style="width: <?php echo count($tArray) * 132?>px">
			<?php foreach($tArray as $t) { ?>
			
				<?php $class = ($t->getThemeID() == $plID) ? 'ccm-item-selected' : ''; ?>
				<li class="<?php echo $class?> themeWrap">
				
					<a href="javascript:void(0)" ccm-theme-id="<?php echo $t->getThemeID()?>"><?php echo $t->getThemeThumbnail()?></a>
						<?php if ($t->getThemeID() != $plID) { ?><a title="<?php echo t('Preview')?>" onclick="ccm_previewInternalTheme(<?php echo $c->getCollectionID()?>, <?php echo intval($t->getThemeID())?>,'<?php echo addslashes(str_replace(array("\r","\n",'\n'),'',$t->getThemeDisplayName())) ?>')" href="javascript:void(0)" class="preview">
						<img src="<?php echo ASSETS_URL_IMAGES?>/icons/magnifying.png" alt="<?php echo t('Preview')?>" class="ccm-preview" /></a><?php } ?>
					<div class="ccm-theme-name" ><?php echo $t->getThemeDisplayName()?></div>
			
				</li>
			<?php } ?>
			</ul>
		</div>
	</div>
	
	
	</form>
	<div class="dialog-buttons">
	<?php $ih = Loader::helper('concrete/interface')?>
	<?php echo $ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
	<?php echo $ih->button_js(t('Update'), 'ccm_sitemapUpdateDesign(\'' . $searchInstance . '\')', 'right', 'btn primary')?>
	</div>		
		
	<?php
	
}
?>
</div>

<script type="text/javascript">
$(function() {
	ccm_enableDesignScrollers();
});
</script>