<?php
	defined('C5_EXECUTE') or die("Access Denied.");

	if (!Loader::helper('validation/numbers')->integer($_GET['cID'])) {
		die(t('Access Denied'));
	}

	$valt = Loader::helper('validation/token');
	$fh = Loader::helper('file');
	
	$token = '&' . $valt->getParameter();
	
	$c = Page::getByID($_REQUEST['cID']);
	$cID = $c->getCollectionID();
	$cp = new Permissions($c);
	$u = new User();
	$isCheckedOut = $c->isCheckedOut() && !$c->isEditMode();
	
	if (!$cp->canViewPageVersions() && !$cp->canApprovePageVersions()) {
		die(t("Access Denied."));
	}
	
	if ($_GET['vtask'] == 'view_versions') { ?>
		
		<div class="ccm-ui" style="height: 100%">
		
		<?php 
		$ih = Loader::helper('concrete/interface');
		$display = 'block';
		$i = 0;
		if (count($_REQUEST['cvID']) > 0) {
			$tabs = array();
			foreach($_REQUEST['cvID'] as $cvID) {
				$cvID=Loader::helper('security')->sanitizeInt($cvID);
				$tabs[] = array('view-version-' . $cvID, t('Version %s', $cvID), ($i == 0));
				$i++;
			}
			print $ih->tabs($tabs);			
		}


		foreach($_REQUEST['cvID'] as $cvID) { 
			$cvID = Loader::helper('security')->sanitizeInt($cvID);
?>
			
		
		<div id="ccm-tab-content-view-version-<?php echo $cvID?>" style="display: <?php echo $display?>; height: 100%">
		<iframe border="0" id="v<?php echo time()?>" frameborder="0" height="100%" width="100%" src="<?php echo BASE_URL . DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cvID=<?php echo $cvID?>&cID=<?php echo $_REQUEST['cID']?>&vtask=view_versions" />
		</div>
		
		<?php if ($display == 'block') {
			$display = 'none';
		}
		
		
		}
		?>
		</div>
		
	
	<?php 
		exit;
	}
	
	
	if (!$isCheckedOut) {
		
		if ($valt->validate()) {
			switch($_REQUEST['vtask']) {
				case 'remove_group':
					if ($cp->canDeletePageVersions() && !$isCheckedOut) {
						$cvIDs = explode('_', $_REQUEST['cvIDs']);
						if (is_array($cvIDs)) {
							foreach($cvIDs as $cvID) {
								$v = CollectionVersion::get($c, $cvID);
								if (!$v->isApproved()) {
									$v->delete();							
								}
							}
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&cID=" . $cID);
							exit;
						}
					}
					break;
				case 'copy_version':
					$u = new User();
					$c->loadVersionObject($_REQUEST['cvID']);
					$c->cloneVersion(t('Copy of Version: %s', $c->getVersionID()));
					header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&cID=" . $cID);
					exit;
					break;
				case 'approve':
					if ($cp->canApprovePageVersions() && !$isCheckedOut) {
						$u = new User();
						$pkr = new ApprovePagePageWorkflowRequest();
						$pkr->setRequestedPage($c);
						$v = CollectionVersion::get($c, $_GET['cvID']);
						$pkr->setRequestedVersionID($v->getVersionID());
						$pkr->setRequesterUserID($u->getUserID());
						$u->unloadCollectionEdit($c);
						$response = $pkr->trigger();
						$cvID = Loader::helper('security')->sanitizeInt($_GET['cvID']);
						if (!($response instanceof WorkflowProgressResponse)) {
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&deferred=true&cID=" . $cID . "&cvID=" . $cvID);
							exit;
						} else {
							// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
							header("Location: " . REL_DIR_FILES_TOOLS_REQUIRED . "/versions.php?forcereload=1&cID=" . $cID . "&cvID=" . $cvID);
							exit;
						}
					}
					break;
			}
			
		}
		
		$page = $_REQUEST[PAGING_STRING];
		if (!$page) {
			$page = 1;
		}
		$vl = new VersionList($c,20, $page);
		$total = $vl->getVersionListCount();
		$vArray = $vl->getVersionListArray();
		$ph = Loader::helper('pagination');
		$ph->init($page, $total, '',20, 'ccm_goToVersionPage');
	}



if (!$_GET['versions_reloaded']) { ?>
	<div id="ccm-versions-container">
	<?php if ($_REQUEST['deferred']) { ?>
		<div class="alert alert-info">
			<?php echo t('<strong>Request Saved.</strong> You must complete the workflow before this change is active.')?>
		</div>
	<?php } ?>
<?php } ?>

<div class="ccm-pane-controls ccm-ui">

<script type="text/javascript">

var ccm_versionsChecked = 0;
/* if this gets set to true, exiting this pane reloads the page */
var ccm_versionsMustReload = false;

$(function() {
	$('.tooltip').hide();	
	$('button[name=vCompare]').tooltip();
	$('button[name=vApprove]').tooltip();
	$('button[name=vCopy]').tooltip();
	$('button[name=vRemove]').tooltip();
	$(".ccm-version").dialog();
	
	$("input[type=checkbox]").click(function() {
		if ($(this).get(0).checked) {
			ccm_versionsChecked++;
		} else {
			ccm_versionsChecked--;
		}
		
		ccm_setSelectors();
		
	});
	
	<?php if ($_REQUEST['forcereload']) { ?>
		ccm_versionsMustReload = true;
	<?php } ?>

});

ccm_setSelectors = function() {
	if (ccm_versionsChecked < 0) {
		ccm_versionsChecked = 0;
	}
	
	/* first, we grab whether an active version is checked, so we can use that later */
	var isActiveChecked = ( $("input.cb-version-active:checked").length > 0 );
	
	/* if two and only two are checked, we can compare */
	
	if (ccm_versionsChecked > 1) {
		$("button[name=vCompare]").prop('disabled', false);
	} else {
		$("button[name=vCompare]").prop('disabled', true);
	}
	
	
	if (ccm_versionsChecked > 0 && (!isActiveChecked)) {
		$("button[name=vRemove]").prop('disabled', false);
	} else {
		$("button[name=vRemove]").prop('disabled', true);
	}
	
	if (ccm_versionsChecked == 1 && (!isActiveChecked)) {
		$("button[name=vApprove]").prop('disabled', false);
	} else {
		$("button[name=vApprove]").prop('disabled', true);
	}

	if (ccm_versionsChecked == 1) {
		$("button[name=vCopy]").prop('disabled', false);
	} else {
		$("button[name=vCopy]").prop('disabled', true);
	}
	
}

ccm_deselectVersions = function() {
	$("input[type=checkbox]").each(function() {
		$(this).get(0).checked = false;
	});
	
	ccm_versionsChecked = 0;
}

ccm_exitVersionList = function() {
	if (ccm_versionsMustReload) {
		window.location.reload();
	} else {
		jQuery.fn.dialog.closeTop();
	}
}

ccm_runAction = function(item) {
	$("#ccm-versions-container").load(item.href, function() {
		
	} );
	return false;
}

$("a#ccm-version-select-none").click(function() {
	ccm_deselectVersions();
	ccm_setSelectors();
});

$("a#ccm-version-select-old").click(function() {
	ccm_deselectVersions();
	$("input[class=cb-version-old]").each(function() {
		$(this).get(0).checked = true;
		ccm_versionsChecked++;
	});
	ccm_setSelectors();

});

$("button[name=vCompare]").click(function() {
	
	var cvidstr = '';
	$("table#ccm-versions-list input[type=checkbox]:checked").each(function() {
		cvidstr += '&cvID[]=' + $(this).val();
	});
	$.fn.dialog.open({
		title: ccmi18n.compareVersions,
		href: CCM_TOOLS_PATH + '/versions.php?cID=<?php echo $c->getCollectionID()?>' + cvidstr + '&vtask=view_versions',
		width: '85%',
		modal: false,
		height: '80%'
	});
});

$("button[name=vApprove]").click(function() {
	
	var cvID = $("table#ccm-versions-list input[type=checkbox]:checked").get(0).value;
	jQuery.fn.dialog.showLoader();
	$.get(CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1&cID=<?php echo $c->getCollectionID()?>&cvID=' + cvID + '&vtask=approve<?php echo $token?>', function(r) {	
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
	
});

$("button[name=vCopy]").click(function() {
	
	var cvID = $("table#ccm-versions-list input[type=checkbox]:checked").get(0).value;
	jQuery.fn.dialog.showLoader();
	$.get(CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1&cID=<?php echo $c->getCollectionID()?>&cvID=' + cvID + '&vtask=copy_version<?php echo $token?>', function(r) {	
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
	
});

ccm_goToVersionPage = function(p, url) {
	jQuery.fn.dialog.showLoader();
	var dest = CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1&cID=<?php echo $c->getCollectionID()?>&<?php echo PAGING_STRING?>' + p;
	$.get(dest, function(r) {
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
	return false;
}


$("button[name=vRemove]").click(function() {

	jQuery.fn.dialog.showLoader();
	
	var cvIDs = $("table#ccm-versions-list input[type=checkbox]:checked");
	var cvIDStr = '';
	for (i = 0; i < cvIDs.length; i++) {
		cvIDStr += "_";
		cvIDStr += cvIDs.get(i).value;
	}
	
	if (cvIDStr != '') {
		cvIDStr = cvIDStr.substring(1);
	}
	
	//ccm_showTopbarLoader();
	var params = {
		'vtask': 'remove_group',
		'ccm_token': '<?php echo $valt->generate()?>',
		'cID': <?php echo $c->getCollectionID()?>,
		'cvIDs': cvIDStr
	}
	
	$.get(CCM_TOOLS_PATH + '/versions.php?versions_reloaded=1', params, function(r) {
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
	
});




</script>

<div class="dialog-help"><?php echo t("Here are earlier versions of this page. The bold line is the live version.<br/><br/>Anyone who can't edit this page sees this active version if permissions allow. Your edits are always made to the latest version. If you'd like to start editing from an old version, copy that version.")?></div>

<br/>



	<?php if ($isCheckedOut) { ?> 
		<?php echo t('Someone has already checked out this page for editing.')?>
	<?php } else { ?>

	<table border="0" cellspacing="0" width="100%" class="table table-striped" cellpadding="0" id="ccm-versions-list">
	<tr>
		<th><div class="btn-group" style="margin-left: auto; margin-right: auto">
		<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
		<span class="caret"></span>
  		</a>
		<ul class="dropdown-menu">
		<li><a id="ccm-version-select-none" href="#"><?php echo t('None')?></a></li>
		<li><a id="ccm-version-select-old" href="#"><?php echo t('Old Versions')?></a></li>
		</ul>
	</div>
	</th>
		<th style="vertical-align: middle"><?php echo t('ID')?></th>
		<th style="vertical-align: middle"><?php echo t('Comments')?></th>
		<th style="vertical-align: middle"><?php echo t('Creator')?></th>
		<th style="vertical-align: middle"><?php echo t('Approver')?></th>
		<th style="vertical-align: middle"><?php echo t('Created')?></th>
		<th style="white-space: nowrap; width: 145px;">
	<div class="btn-group" style="float: right; white-space: nowrap">
	<?php
	$ih = Loader::helper("concrete/dashboard");
	if (!$ih->inDashboard($c)) { ?><button class="btn" name="vCompare" title="<?php echo t('Compare')?>" disabled><i class="icon-zoom-in"></i></button><?php } ?>
	<button class="btn" name="vApprove" title="<?php echo t('Approve')?>" disabled><i class="icon-thumbs-up"></i></button>
	<button class="btn" name="vCopy" value="<?php echo t('Copy')?>" title="<?php echo t('Copy Version')?>" disabled><i class="icon-plus-sign"></i></button>
	<?php if ($cp->canDeletePageVersions()) { ?>
		<button class="btn" name="vRemove" value="<?php echo t('Remove')?>" disabled><i class="icon-trash"></i></button>
	<?php } ?>
	</div>
		</th>
	</tr>
	<?php 
	$dh = Loader::helper('date');
	/* @var $dh DateHelper */
	$vIsPending = true;
	foreach ($vArray as $v) { 
		if ($v->isApproved()) {
			$vIsPending = false;
		}
		
		if ($vrAlt) { 
			$class = 'ccm-row-alt ';
			$vrAlt = false;
		} else {
			$class = '';
			$vrAlt = true;
		}
		
		if ($vIsPending) {
			$class .= 'version-pending';
		} else if ($v->isApproved()) {
			$class .= "version-active";
		}
		
	?> 
	<tr id="ccm-version-row<?php echo $v->getVersionID()?>" class="<?php echo $class?>">
		<td style="text-align: center"><input type="checkbox" <?php if ($vIsPending) { ?> class="cb-version-pending"<?php } else if ($v->isApproved()) { ?> class="cb-version-active"<?php } else { ?> class="cb-version-old" <?php } ?> id="cb<?php echo $v->getVersionID()?>" name="vID[]" value="<?php echo $v->getVersionID()?>" /></td>
		<td><?php echo $v->getVersionID()?></td>
		<td><a dialog-width="85%" dialog-height="80%" title="<?php echo t('View Versions')?>" class="ccm-version" dialog-title="<?php echo t('View Versions')?>" dialog-modal="false" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?php echo $cID?>&cvID[]=<?php echo $v->getVersionID()?>&vtask=view_versions"><?php echo $v->getVersionComments()?></a></td>
		<td><?php
			print $v->getVersionAuthorUserName();
			
			?></td>
		<td><?php
			print $v->getVersionApproverUserName();
			
			?></td>
		<td colspan="2"><?php echo $dh->formatSpecial('PAGE_VERSIONS', $v->getVersionDateCreated())?></td>
	</tr>	
	<?php } ?>
	</table>
	<?php if ($total > 20 ) { ?>
	<div class="ccm-ui">
		<div class="pagination ccm-pagination">
		<ul>
			<li class="prev"><?php echo $ph->getPrevious()?></li>
			<?php echo $ph->getPages('li'); ?>
			<li class="next"><?php echo $ph->getNext()?></li>
		</ul>
		</div>
	</div>
	<?php } ?>
	<br>
	
<?php 	}

?>

</div>

<?php if (!$_GET['versions_reloaded']) { ?>
</div>
<?php } ?>
