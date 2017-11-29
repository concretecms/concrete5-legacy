<?php
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('collection_types');
$stringHelper=Loader::helper('text');
$tArray = PageTheme::getGlobalList();
$tArray2 = PageTheme::getLocalList();
$tArrayTmp = array_merge($tArray, $tArray2);
$tArray = array();
foreach($tArrayTmp as $pt) {
	if ($cp->canEditPageTheme($pt)) {
		$tArray[] = $pt;
	}
}
$ctArray = CollectionType::getList();

$cp = new Permissions($c);
if ($c->getCollectionID() > 1) {
	$parent = Page::getByID($c->getCollectionParentID());
	$parentCP = new Permissions($parent);
}
if (!$cp->canEditPageType() && !$cp->canEditPageTheme()) {
	die(t('Access Denied'));
}

$cnt = 0;
for ($i = 0; $i < count($ctArray); $i++) {
	$ct = $ctArray[$i];
	if ($c->getCollectionID() == 1 || $parentCP->canAddSubCollection($ct)) { 
		$cnt++;
	}
}

$plID = $c->getCollectionThemeID();
$ctID = $c->getCollectionTypeID();
if ($plID == 0) {
	$pl = PageTheme::getSiteTheme();
	$plID = $pl->getThemeID();
}
?>

<div class="ccm-ui">
<form method="post" name="ccmThemeForm" action="<?php echo $c->getCollectionAction()?>">
	<input type="hidden" name="plID" value="<?php echo $c->getCollectionThemeID()?>" />
	<input type="hidden" name="ctID" value="<?php echo $c->getCollectionTypeID()?>" />
	<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />


	<?php 
	if (!$cp->canEditPageType()) { ?>

		<h3><?php echo t('Choose a Page Type')?></h3>
		<p>
		<?php echo t("You do not have access to change this page's type.")?>
		</p>
		<br/><br/>

	<?php	
	
	} else if ($c->isMasterCollection()) { ?>
		<h3><?php echo t('Choose a Page Type')?></h3>
		<p>
		<?php echo t("This is the defaults page for the %s page type. You cannot change it.", $c->getCollectionTypeName()); ?>
		</p>
		<br/><br/>
	
	<?php } else if ($c->isGeneratedCollection()) { ?>
	<h3><?php echo t('Choose a Page Type')?></h3>
	<p><?php echo t("This page is a single page, which means it doesn't have a page type associated with it."); ?></p>

	<?php } else if ($cnt > 0) { ?>

	<h3><?php echo t('Choose a Page Type')?></h3>

	<div class="ccm-scroller" current-page="1" current-pos="0" num-pages="<?php echo ceil($cnt/4)?>">
		<a href="javascript:void(0)" class="ccm-scroller-l"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_l.png" width="28" height="79" alt="l" /></a>
		<a href="javascript:void(0)" class="ccm-scroller-r"><img src="<?php echo ASSETS_URL_IMAGES?>/button_scroller_r.png" width="28" height="79" alt="l" /></a>

		<div class="ccm-scroller-inner">
			<ul id="ccm-select-page-type" style="width: <?php echo $cnt * 132?>px">
				<?php 
				foreach($ctArray as $ct) { 
					if ($c->getCollectionID() == 1 || $parentCP->canAddSubCollection($ct)) { 
					?>		
					<?php $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
			
					<li class="<?php echo $class?>"><a href="javascript:void(0)" ccm-page-type-id="<?php echo $ct->getCollectionTypeID()?>"><?php echo $ct->getCollectionTypeIconImage();?></a><span><?php echo $ct->getCollectionTypeName()?></span>
					</li>
				<?php } 
				
				}?>
			</ul>
		</div>
	</div>

	<?php } ?>
	
	<?php if(ENABLE_MARKETPLACE_SUPPORT){ ?>
		<a href="javascript:void(0)" onclick="ccm_openThemeLauncher()" class="btn ccm-button-right success"><?php echo t("Get More Themes")?></a>
	<?php } ?>

	<h3 ><?php echo t('Themes')?></h3>
	
	<?php if ($cp->canEditPageTheme()) { ?>

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
	<?php } else { ?>
	
	<p><?php echo t("You do not have access to change this page's theme."); ?></p>

	<?php } ?>
	
	<div class="dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="ccm-button-left btn"><?php echo t('Cancel')?></a>
		<a href="javascript:void(0)" onclick="$('form[name=ccmThemeForm]').submit()" class="ccm-button-right primary btn"><?php echo t('Save')?></a>
	</div>	
	<input type="hidden" name="update_theme" value="1" class="accept">
	<input type="hidden" name="processCollection" value="1">

	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	

<script type="text/javascript">

$(function() {
	ccm_enableDesignScrollers();
	<?php if ($_REQUEST['rel'] == 'SITEMAP') { ?>
		$("form[name=ccmThemeForm]").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			if (r != null && r.rel == 'SITEMAP') {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				ccmSitemapHighlightPageLabel(r.cID);
			} else {
				jQuery.fn.dialog.closeTop();
			}
			ccmAlert.hud(ccmi18n_sitemap.pageDesignMsg, 2000, 'success', ccmi18n_sitemap.pageDesign);
		}
	});

	<?php } else { ?>
		$('form[name=ccmThemeForm]').submit(function() {
			jQuery.fn.dialog.showLoader();
		});
	<?php } ?>


});
</script>
