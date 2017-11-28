<?php
defined('C5_EXECUTE') or die("Access Denied.");
header('Content-type: text/javascript');?>

var menuHTML = '';

<?php
/* @var $jh JsonHelper */
$jh = Loader::helper('json');
/* @var $datehelper DateHelper */
$datehelper = Loader::helper('date');

Loader::library('3rdparty/mobile_detect');
$md = new Mobile_Detect();

if ($_REQUEST['cvID'] > 0) {
	$c = Page::getByID($_REQUEST['cID'], $_REQUEST['cvID']);
} else {
	$c = Page::getByID($_REQUEST['cID']);
}
$cp = new Permissions($c);
$req = Request::get();
$req->setCurrentPage($c);

$valt = Loader::helper('validation/token');
$sh = Loader::helper('concrete/dashboard/sitemap');
$dh = Loader::helper('concrete/dashboard');
$ish = Loader::helper('concrete/interface');
$token = '&' . $valt->getParameter();

$workflowList = PageWorkflowProgress::getList($c);

if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
		}
	}

	if ($cp->canViewToolbar()) {
		$cID = $c->getCollectionID(); ?>





menuHTML += '<div id="ccm-page-controls-wrapper" class="ccm-ui">';
menuHTML += '<div id="ccm-toolbar">';

menuHTML += '<ul id="ccm-main-nav">';
menuHTML += '<li id="ccm-logo-wrapper"><?php echo Loader::helper('concrete/interface')->getToolbarLogoSRC()?></li>';

<?php if ($c->isMasterCollection()) { ?>
	menuHTML += '<li><a class="ccm-icon-back ccm-menu-icon" href="<?php echo View::url('/dashboard/pages/types')?>">' + <?php echo $jh->encode(t('Page Types'))?> + '</a></li>';
<?php } ?>

<?php
	if ($cp->canViewToolbar()) {  ?>

	menuHTML += '<li <?php if ($c->isEditMode()) { ?>class="ccm-nav-edit-mode-active"<?php } ?>><a class="ccm-icon-edit ccm-menu-icon" id="ccm-nav-edit" href="<?php if (!$c->isEditMode()) { ?><?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-out<?php echo $token?><?php } else { ?>javascript:void(0);<?php } ?>" <?php if (!$c->isEditMode()) { ?> onclick="$(\'#ccm-edit-overlay\').hide()" <?php } ?>>' + <?php echo $jh->encode($c->isEditMode() ? t('Editing') : t('Edit'))?> + '</a></li>';
	<?php
	$items = $ihm->getPageHeaderMenuItems('left');
	foreach($items as $ih) {
		$cnt = $ih->getController();
		if ($cnt->displayItem()) {
		?>
			menuHTML += '<li>' + <?php echo $jh->encode($cnt->getMenuLinkHTML())?> + '</li>';
		<?php
		}
	}

} ?>

<?php if (Loader::helper('concrete/interface')->showWhiteLabelMessage()) { ?>
	menuHTML += '<li id="ccm-white-label-message">' + <?php echo $jh->encode(t('Powered by <a href="%s">concrete5</a>.', CONCRETE5_ORG_URL))?> + '</li>';
<?php }
?>
menuHTML += '</ul>';
menuHTML += '<ul id="ccm-system-nav">';
<?php
$items = $ihm->getPageHeaderMenuItems('right');
foreach($items as $ih) {
	$cnt = $ih->getController();
	if ($cnt->displayItem()) {
	?>
		menuHTML += '<li>' + <?php echo $jh->encode($cnt->getMenuLinkHTML())?> + '</li>';
	<?php
	}
}
?>

<?php if ($dh->canRead()) { ?>
	menuHTML += '<li><a class="ccm-icon-dashboard ccm-menu-icon" id="ccm-nav-dashboard<?php if ($md->isMobile()) { ?>-mobile<?php } ?>" href="<?php echo View::url('/dashboard')?>">' + <?php echo $jh->encode(t('Dashboard'))?> + '</a></li>';
<?php } ?>
menuHTML += '<li id="ccm-nav-intelligent-search-wrapper"><input type="search" placeholder="' + <?php echo $jh->encode(t('Intelligent Search'))?> + '" id="ccm-nav-intelligent-search" tabindex="1" /></li>';
menuHTML += '<li><a id="ccm-nav-sign-out" class="ccm-icon-sign-out ccm-menu-icon" href="<?php echo View::url('/login', 'logout')?>">' + <?php echo $jh->encode(t('Sign Out'))?> + '</a></li>';
menuHTML += '</ul>';

menuHTML += '</div>';

<?php
$dh = Loader::helper('concrete/dashboard');
?>

menuHTML += <?php echo $jh->encode($dh->addQuickNavToMenus($dh->getDashboardAndSearchMenus()))?>;

menuHTML += '<div id="ccm-edit-overlay">';
menuHTML += '<div class="ccm-edit-overlay-inner">';

<?php if ($c->isEditMode()) { ?>

menuHTML += '<div id="ccm-exit-edit-mode-direct" <?php if ($vo->isNew()) { ?>style="display: none"<?php } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions">';
menuHTML += '<a href="javascript:void(0)" onclick="window.location.href=\'<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-in<?php echo $token?>\'" id="ccm-nav-exit-edit-direct" class="btn primary">' + <?php echo $jh->encode(t('Exit Edit Mode'))?> + '</a>';
menuHTML += '</div>';
menuHTML += '<span class="label notice">' + <?php echo $jh->encode(t('Version %s', $c->getVersionID()))?> + '</span>';
menuHTML += <?php echo $jh->encode(t('Page currently in edit mode on %s', $datehelper->formatDateTime('now', false, false)))?>;

menuHTML += '</div>';

menuHTML += '<div id="ccm-exit-edit-mode-comment" <?php if (!$vo->isNew()) { ?>style="display: none"<?php } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions clearfix">';
menuHTML += '<form method="post" id="ccm-check-in" action="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-in">';
<?php $valt = Loader::helper('validation/token'); ?>
menuHTML += <?php echo $jh->encode($valt->output('', true))?>;
menuHTML += '<h4>' + <?php echo $jh->encode(t('Version Comments'))?> + '</h4>';
menuHTML += '<p><input type="text" name="comments" id="ccm-check-in-comments" style="width:520px" maxlength="255" /></p>';
<?php if ($cp->canApprovePageVersions()) { ?>
	<?php
	$publishTitle = t('Publish My Edits');
	$pk = PermissionKey::getByHandle('approve_page_versions');
	$pk->setPermissionObject($c);
	$pa = $pk->getPermissionAccessObject();
	$workflows = array();
	$canApproveWorkflow = true;
	if (is_object($pa)) {
		$workflows = $pa->getWorkflows();
	}
	foreach($workflows as $wf) {
		if (!$wf->canApproveWorkflow()) {
			$canApproveWorkflow = false;
		}
	}

	if (count($workflows) > 0 && !$canApproveWorkflow) {
		$publishTitle = t('Submit to Workflow');
	}
?>
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-publish" class="btn primary" style="float: right"><span>' + <?php echo $jh->encode($publishTitle)?> + '</span></a>';
<?php } ?>
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-preview" class="btn" style="float: right"><span>' + <?php echo $jh->encode(t('Preview My Edits'))?> + '</span></a>';
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-discard" class="btn" style="float: left"><span>' + <?php echo $jh->encode(t('Discard My Edits'))?> + '</span></a>';
menuHTML += '<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />';
menuHTML += '</form><br/>';

menuHTML += '</div>';
menuHTML += '<span class="label notice">' + <?php echo $jh->encode(t('Version %s', $c->getVersionID()))?> + '</span>';
menuHTML += <?php echo $jh->encode(t('Page currently in edit mode on %s', $datehelper->formatDateTime('now', false, false)))?>;

menuHTML += '</div>';

<?php } else { ?>

menuHTML += '<div class="ccm-edit-overlay-actions">';
<?php if ($cp->canEditPageContents()) { ?>
	menuHTML += '<a id="ccm-nav-check-out" href="<?php if (!$cantCheckOut) { ?><?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-out<?php echo $token?><?php } else { ?>javascript:void(0);<?php } ?>" class="btn primary <?php if ($cantCheckOut) { ?> disabled <?php } ?> launch-tooltip" <?php if ($cantCheckOut) { ?>title="' + <?php echo $jh->encode(h(t('Someone has already checked this page out for editing.')))?> + '"<?php } ?>>' + <?php echo $jh->encode(t('Edit this Page'))?> + '</a>';
<?php } ?>
<?php if ($cp->canAddSubpage()) { ?>
	menuHTML += '<a id="ccm-toolbar-add-subpage" dialog-width="645" dialog-modal="false" dialog-append-buttons="true" dialog-height="345" dialog-title="' + <?php echo $jh->encode(h(t('Add a Sub-Page')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?php echo $cID?>&ctask=add" class="btn">' + <?php echo $jh->encode(t('Add a Sub-Page'))?> + '</a>';
<?php } ?>
menuHTML += '</div>';
menuHTML += '<span class="label notice">' + <?php echo $jh->encode(t('Version %s', $c->getVersionID()))?> + '</span>';
menuHTML += <?php echo $jh->encode(t('Page last edited on %s', $datehelper->formatDateTime($c->getCollectionDateLastModified(), false, false)))?>;


<?php } ?>

menuHTML += '</div>';

<?php if (!$cantCheckOut) { ?>

menuHTML += '<div id="ccm-edit-overlay-footer">';
menuHTML += '<div class="ccm-edit-overlay-inner">';
menuHTML += '<ul>';
<?php if ($cp->canEditPageProperties()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-properties" <?php if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?php echo $c->getCollectionID()?>)" <?php } ?> id="ccm-toolbar-nav-properties" dialog-width="640" dialog-height="<?php if ($cp->canApprovePageVersions() && (!$c->isEditMode())) { ?>450<?php } else { ?>390<?php } ?>" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('Page Properties')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?<?php if ($cp->canApprovePageVersions() && (!$c->isEditMode())) { ?>approveImmediately=1<?php } ?>&cID=<?php echo $c->getCollectionID()?>&ctask=edit_metadata">' + <?php echo $jh->encode(t('Properties'))?> + '</a></li>';
<?php } ?>
<?php if ($cp->canPreviewPageAsUser() && PERMISSIONS_MODEL == 'advanced') { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-preview-as-user" <?php if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?php echo $c->getCollectionID()?>)" <?php } ?> id="ccm-toolbar-nav-preview-as-user" dialog-width="90%" dialog-height="70%" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('View Page as Someone Else')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?php echo $c->getCollectionID()?>&ctask=preview_page_as_user">' + <?php echo $jh->encode(t('Preview as User'))?> + '</a></li>';
<?php } ?>
<?php if ($cp->canEditPageTheme() || $cp->canEditPageType()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-design" <?php if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?php echo $c->getCollectionID()?>)" <?php } ?> id="ccm-toolbar-nav-design" dialog-append-buttons="true" dialog-width="610" dialog-height="405" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('Design')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?php echo $cID?>&ctask=set_theme">' + <?php echo $jh->encode(t('Design'))?> + '</a></li>';
<?php } ?>
<?php if ($cp->canEditPagePermissions()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-permissions" <?php if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?php echo $c->getCollectionID()?>)" <?php } ?> dialog-append-buttons="true" id="ccm-toolbar-nav-permissions" dialog-width="420" dialog-height="630" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('Permissions')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?php echo $cID?>&ctask=edit_permissions">' + <?php echo $jh->encode(t('Permissions'))?> + '</a></li>';
<?php } ?>
<?php if ($cp->canViewPageVersions()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-versions" <?php if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?php echo $c->getCollectionID()?>)" <?php } ?> id="ccm-toolbar-nav-versions" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('Page Versions')))?> + '" id="menuVersions<?php echo $cID?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>">' + <?php echo $jh->encode(t('Versions'))?> + '</a></li>';
<?php } ?>
<?php if ($cp->canMoveOrCopyPage()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-move-copy" id="ccm-toolbar-nav-move-copy" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('Move/Copy Page')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?sitemap_select_mode=move_copy_delete&cID=<?php echo $cID?>">' + <?php echo $jh->encode(t('Move/Copy'))?> + '</a></li>';
<?php } ?>
<?php if ($cp->canEditPageSpeedSettings()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-speed-settings" <?php if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?php echo $c->getCollectionID()?>)" <?php } ?> id="ccm-toolbar-nav-speed-settings" dialog-append-buttons="true" dialog-width="550" dialog-height="280" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('Full Page Caching')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?php echo $cID?>&ctask=edit_speed_settings">' + <?php echo $jh->encode(t('Full Page Caching'))?> + '</a></li>';
<?php } ?>
<?php if ($cp->canDeletePage()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-delete" <?php if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?php echo $c->getCollectionID()?>)" <?php } ?>  dialog-append-buttons="true" id="ccm-toolbar-nav-delete" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + <?php echo $jh->encode(h(t('Delete Page')))?> + '" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?php echo $cID?>&ctask=delete">' + <?php echo $jh->encode(t('Delete'))?> + '</a></li>';
<?php } ?>
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '</div>';

<?php } ?>

menuHTML += '</div>';
<?php
	}

} ?>

$(function() {
	var item, sbitem, btn, btn1, btn2;
	<?php if ($c->isEditMode()) { ?>
		$(ccm_editInit);
	<?php } ?>

	<?php
	if (!$dh->inDashboard()) { ?>
		$("#ccm-page-controls-wrapper").html(menuHTML);
		<?php if ($cantCheckOut) { ?>
			item = new ccm_statusBarItem();
			item.setCSSClass('info');
			item.setDescription(<?php echo $jh->encode(t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName()))?>);
			ccm_statusBar.addItem(item);
		<?php } ?>

		<?php if ($c->getCollectionPointerID() > 0) { ?>

			sbitem  = new ccm_statusBarItem();
			sbitem.setCSSClass('info');
			sbitem.setDescription(<?php echo $jh->encode(t("This page is an alias of one that actually appears elsewhere."))?>);
			btn1 = new ccm_statusBarItemButton();
			btn1.setLabel(<?php echo $jh->encode(t('View/Edit Original'))?>);
			btn1.setURL('<?php echo DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID()?>');
			sbitem.addButton(btn1);
			<?php if ($cp->canApprovePageVersions()) { ?>
				btn2 = new ccm_statusBarItemButton();
				btn2.setLabel(<?php echo $jh->encode(t('Remove Alias'))?>);
				btn2.setCSSClass('danger');
				btn2.setURL('<?php echo DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token?>');
				sbitem.addButton(btn2);
			<?php } ?>
			ccm_statusBar.addItem(sbitem);

		<?php }

		if ($c->isMasterCollection()) { ?>

			sbitem = new ccm_statusBarItem();
			sbitem.setCSSClass('info');
			sbitem.setDescription(<?php echo $jh->encode(h(t('Page Defaults for %s Page Type. All edits take effect immediately.', $c->getCollectionTypeName())))?>);
			ccm_statusBar.addItem(sbitem);
		<?php } ?>
		<?php
		$hasPendingPageApproval = false;

		if ($cp->canViewToolbar()) { ?>
			<?php if (is_array($workflowList)) { ?>
				<?php foreach($workflowList as $wl) { ?>
					<?php $wr = $wl->getWorkflowRequestObject();
					$wrk = $wr->getWorkflowRequestPermissionKeyObject();
					if ($wrk->getPermissionKeyHandle() == 'approve_page_versions') {
						$hasPendingPageApproval = true;
					}
					?>
					<?php $wf = $wl->getWorkflowObject(); ?>
					sbitem = new ccm_statusBarItem();
					sbitem.setCSSClass(<?php echo $jh->encode($wr->getWorkflowRequestStyleClass())?>);
					sbitem.setDescription(<?php echo $jh->encode($wf->getWorkflowProgressCurrentDescription($wl))?>);
					sbitem.setAction(<?php echo $jh->encode($wl->getWorkflowProgressFormAction())?>);
					sbitem.enableAjaxForm();
					<?php $actions = $wl->getWorkflowProgressActions(); ?>
					<?php foreach($actions as $act) { ?>
						btn = new ccm_statusBarItemButton();
						btn.setLabel(<?php echo $jh->encode($act->getWorkflowProgressActionLabel())?>);
						btn.setCSSClass(<?php echo $jh->encode($act->getWorkflowProgressActionStyleClass())?>);
						btn.setInnerButtonLeftHTML(<?php echo $jh->encode($act->getWorkflowProgressActionStyleInnerButtonLeftHTML())?>);
						btn.setInnerButtonRightHTML(<?php echo $jh->encode($act->getWorkflowProgressActionStyleInnerButtonRightHTML())?>);
						<?php if ($act->getWorkflowProgressActionURL() != '') { ?>
							btn.setURL(<?php echo $jh->encode($act->getWorkflowProgressActionURL())?>);
						<?php } else { ?>
							btn.setAction(<?php echo $jh->encode($act->getWorkflowProgressActionTask())?>);
						<?php } ?>
						<?php if (count($act->getWorkflowProgressActionExtraButtonParameters()) > 0) { ?>
							<?php foreach($act->getWorkflowProgressActionExtraButtonParameters() as $key => $value) { ?>
								btn.addAttribute(<?php echo $jh->encode($key)?>, <?php echo $jh->encode($value)?>);
							<?php } ?>
						<?php } ?>
						sbitem.addButton(btn);
					<?php } ?>
					ccm_statusBar.addItem(sbitem);
				<?php } ?>

			<?php } ?>
		<?php } ?>

		<?php

		if (!$c->getCollectionPointerID() && !$hasPendingPageApproval) {
			if (is_object($vo)) {
				if (!$vo->isApproved() && !$c->isEditMode() && $cp->canViewToolbar()) { ?>

					sbitem = new ccm_statusBarItem();
					sbitem.setCSSClass('info');
					sbitem.setDescription(<?php echo $jh->encode(t("This page is pending approval."))?>);
					<?php if ($cp->canApprovePageVersions() && !$c->isCheckedOut()) {
						$pk = PagePermissionKey::getByHandle('approve_page_versions');
						$pk->setPermissionObject($c);
						$pa = $pk->getPermissionAccessObject();
						if (is_object($pa)) {
							if (count($pa->getWorkflows()) > 0) {
								$appLabel = t('Submit for Approval');
							}
						}
						if (!$appLabel) {
							$appLabel = t('Approve Version');
						}
						?>
						btn1 = new ccm_statusBarItemButton();
						btn1.setCSSClass('btn-success');
						btn1.setLabel(<?php echo $jh->encode($appLabel)?> + ' <i class="icon-thumbs-up icon-white"></i>');
						btn1.setURL('<?php echo DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token?>');
						sbitem.addButton(btn1);
					<?php } ?>
					ccm_statusBar.addItem(sbitem);
				<?php }
			}
		} ?>


		ccm_statusBar.activate();
		$(".launch-tooltip").tooltip();
		ccm_activateToolbar();
	<?php } ?>




});
