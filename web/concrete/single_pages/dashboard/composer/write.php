<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php

if (isset($entry)) { 

	$pk = PermissionKey::getByHandle('edit_page_properties');
	$pk->setPermissionObject($entry);
	$asl = $pk->getMyAssignment();
	$allowedAKIDs = $asl->getAttributesAllowedArray();

	$pk = PermissionKey::getByHandle('approve_page_versions');
	$pk->setPermissionObject($entry);
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
		$workflow = true;
	}


	?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(ucfirst($action) . ' ' . $ct->getCollectionTypeName(), false, false, false)?>
	<form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?php echo $this->action('save')?>" id="ccm-dashboard-composer-form">
	<input type="hidden" name="ccm-publish-draft" value="0" />

	<div class="ccm-pane-body">
	

	<div id="composer-save-status"></div>
	
	<fieldset>
	<legend><?php echo t("Basic Information")?></legend>
	<?php if ($asl->allowEditName()) { ?>
	<div class="control-group">
		<?=$form->label('cName', t('Name'))?>
		<div class="controls"><?=$form->text('cName', Loader::helper("text")->entities($name), array('class' => 'input-xlarge'))?></div>		
	</div>
	<?php } ?>
	
	<?php if ($asl->allowEditPaths()) { ?>
	<div class="control-group">
		<?php echo $form->label('cHandle', t('URL Slug'))?>
		<div class="controls"><?php echo $form->text('cHandle', $handle, array('class' => 'span3'))?>
		<img src="<?php echo ASSETS_URL_IMAGES?>/loader_intelligent_search.gif" width="43" height="11" id="ccm-url-slug-loader" style="display: none" />
		</div>		
	</div>
	<?php } ?>

	<?php if ($asl->allowEditDescription()) { ?>
	<div class="control-group">
		<?php echo $form->label('cDescription', t('Short Description'))?>
		<div class="controls"><?php echo $form->textarea('cDescription', Loader::helper("text")->entities($description), array('class' => 'input-xlarge', 'rows' => 5))?></div>		
	</div>
	<?php } ?>

	<?php if ($asl->allowEditDateTime()) { ?>
	<div class="control-group">
		<?php echo $form->label('cDatePublic', t('Date Posted'))?>
		<div class="controls"><?php 
		if ($this->controller->isPost()) { 	
			$cDatePublic = Loader::helper('form/date_time')->translate('cDatePublic');
		}
		?><?php echo Loader::helper('form/date_time')->datetime('cDatePublic', $cDatePublic)?></div>		
	</div>
<?php } ?>

	</fieldset>
	
	<?php if ($entry->isComposerDraft()) { ?>
	<fieldset>
	<legend><?php echo t('Publish Location')?></legend>
	<div class="control-group">
		<span id="ccm-composer-publish-location"><?php
		print $this->controller->getComposerDraftPublishText($entry);
		?>
		</span>
		
		<?php 
	
	if ($ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE' || $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE') { ?>
		
		<a href="javascript:void(0)" onclick="ccm_openComposerPublishTargetWindow(false)"><?php echo t('Choose publish location.')?></a>
	
	<?php } 
	
	?></div>
	</fieldset>
	<?php } ?>
	
	<fieldset>
	<legend><?php echo t('Attributes &amp; Content')?></legend>
	<?php 
	foreach($contentitems as $ci) {
		if ($ci instanceof AttributeKey) { 
			$ak = $ci;
			if (!in_array($ak->getAttributeKeyID(), $allowedAKIDs)) {
				continue;
			}
			
			if (is_object($entry)) {
				$value = $entry->getAttributeValueObject($ak);
			}
			?>
			<div class="control-group">
				<?php echo $ak->render('label');?>
				<div class="controls">
					<?php echo $ak->render('composer', $value, true)?>
				</div>
			</div>
		
		<?php } else { 
			$b = $ci; 
			$b = $entry->getComposerBlockInstance($b);
			?>
		
		<div class="control-group">
		<?php
		if (is_object($b)) {
			$bv = new BlockView();
			$bv->render($b, 'composer');
		} else {
			print t('Block not found. Unable to edit in composer.');
		}
		?>
		
		</div>
		
		<?php
		} ?>
	<?php }  ?>
	</fieldset>
	

	</div>
	<div class="ccm-pane-footer">
	<?php
	$v = $entry->getVersionObject();
	
	?>
	

	<?php if ($entry->isComposerDraft()) { 
	$pp = new Permissions($entry);
	?>
		<?php if ($workflow) { ?>
			<?php echo Loader::helper('concrete/interface')->submit(t('Submit to Workflow'), 'publish', 'right', 'primary')?>
		<?php } else { ?>
			<?php echo Loader::helper('concrete/interface')->submit(t('Publish Page'), 'publish', 'right', 'primary')?>
		<?php } ?>
		<?php if (PERMISSIONS_MODEL != 'simple' && $pp->canEditPagePermissions()) { ?>
			<?php echo Loader::helper('concrete/interface')->button_js(t('Permissions'), 'javascript:ccm_composerLaunchPermissions()', 'left', 'primary ccm-composer-hide-on-no-target')?>
		<?php } ?>
	<?php } else { ?>
		<?php if ($workflow) { ?>
			<?php echo Loader::helper('concrete/interface')->submit(t('Submit to Workflow'), 'publish', 'right', 'primary')?>
		<?php } else { ?>
			<?php echo Loader::helper('concrete/interface')->submit(t('Publish Changes'), 'publish', 'right', 'primary')?>
		<?php } ?>
	<?php } ?>

	<?php echo Loader::helper('concrete/interface')->button_js(t('Preview'), 'javascript:ccm_composerLaunchPreview()', 'right', 'ccm-composer-hide-on-approved')?>
	<?php echo Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'right')?>
	<?php echo Loader::helper('concrete/interface')->submit(t('Discard'), 'discard', 'left', 'error ccm-composer-hide-on-approved')?>
	
	<?php echo $form->hidden('entryID', $entry->getCollectionID())?>
	<?php if ($entry->isComposerDraft()) { ?>
		<input type="hidden" name="cPublishParentID" value="<?php echo $entry->getComposerDraftPublishParentID()?>" />
	<?php } ?>
	<?php echo $form->hidden('autosave', 0)?>
	<?php echo Loader::helper('validation/token')->output('composer')?>
	</div>
	</form>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>


	<script type="text/javascript">
	var ccm_composerAutoSaveInterval = false;
	var ccm_composerDoAutoSaveAllowed = true;

	ccm_updateAddPageHandle = function() {
		if(ccm_updateAddPageHandle.lastRequested === $.trim($('#ccm-dashboard-composer-form input[name=cName]').val())) {
			return;
		}
		if(ccm_updateAddPageHandle.timer) {
			clearTimeout(ccm_updateAddPageHandle.timer);
		}
		ccm_updateAddPageHandle.timer = setTimeout(function() {
			var val = $.trim($('#ccm-dashboard-composer-form input[name=cName]').val());
			ccm_updateAddPageHandle.lastRequested = val;
			delete ccm_updateAddPageHandle.timer;
			$('#ccm-url-slug-loader').show();
			ccm_updateAddPageHandle.xhr = $.ajax({
				type: 'POST',
				url: '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/url_slug',
				data: {
					'token': '<?php echo Loader::helper('validation/token')->generate('get_url_slug')?>',
					'name': val,
					'parentID' : $("input[name=cPublishParentID]").val()
				}
			})
			.done(function(r, textStatus, xhr) {
				if(ccm_updateAddPageHandle.xhr == xhr) {
					$('#ccm-dashboard-composer-form input[name=cHandle]').val(r);
					$('#ccm-url-slug-loader').hide();
				}
			})
			.fail(function(xhr) {
				if(ccm_updateAddPageHandle.xhr == xhr) {
					$('#ccm-url-slug-loader').hide();
				}
			});
		}, 150);
	};
	
	ccm_composerDoAutoSave = function(callback) {
		if (!ccm_composerDoAutoSaveAllowed) {
			return false;
		}
		$('#ccm-submit-save').attr('disabled',true);
		$('input[name=autosave]').val('1');
		try {
			tinyMCE.triggerSave(true, true);
		} catch(e) { }
		
		$('#ccm-dashboard-composer-form').ajaxSubmit({
			'dataType': 'json',
			'success': function(r) {
				$('input[name=autosave]').val('0');
				ccm_composerLastSaveTime = new Date();
				$("#composer-save-status").html('<div class="alert alert-info"><?php echo t("Page saved at ")?>' + r.time + '</div>');
				$(".ccm-composer-hide-on-approved").show();
				$('#ccm-submit-save').attr('disabled',false);
				if (callback) {
					callback();
				}
			}
		});
		
	}
	
	ccm_composerLaunchPreview = function() {
		jQuery.fn.dialog.showLoader();
		<?php $t = PageTheme::getSiteTheme(); ?>
		ccm_composerDoAutoSave(function() {
			ccm_previewComposerDraft(<?php echo $entry->getCollectionID()?>,
				"<?php echo strlen($entry->getCollectionName())?$entry->getCollectionName():t("New Page")?>");
		});
	}
	
	ccm_composerSelectParentPage = function(cID) {
		$("input[name=cPublishParentID]").val(cID);
		$(".ccm-composer-hide-on-no-target").show();
		$("#ccm-composer-publish-location").load('<?php echo $this->action("select_publish_target")?>', {'entryID': <?php echo $entry->getCollectionID()?>, 'cPublishParentID': cID});
		jQuery.fn.dialog.closeTop();

	}	

	ccm_composerSelectParentPageAndSubmit = function(cID) {
		$("input[name=cPublishParentID]").val(cID);
		$(".ccm-composer-hide-on-no-target").show();
		$("#ccm-composer-publish-location").load('<?php echo $this->action("select_publish_target")?>', {'entryID': <?php echo $entry->getCollectionID()?>, 'cPublishParentID': cID}, function() {
		 	$("input[name=ccm-publish-draft]").val(1);
		 	$('#ccm-dashboard-composer-form').submit();
		});
	}	
		
	ccm_composerLaunchPermissions = function(cID) {
		var shref = CCM_TOOLS_PATH + '/edit_collection_popup?ctask=edit_permissions&cID=<?php echo $entry->getCollectionID()?>';
		jQuery.fn.dialog.open({
			title: '<?php echo t("Permissions")?>',
			href: shref,
			width: '640',
			modal: false,
			height: '310'
		});
	}
	
	ccm_composerEditBlock = function(cID, bID, arHandle, w, h) {
		if(!w) w=550;
		if(!h) h=380; 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.editBlock,
			href: editBlockURL+'?cID='+cID+'&bID='+bID+'&arHandle=' + encodeURIComponent(arHandle) + '&btask=edit',
			width: w,
			modal: false,
			height: h
		});		
	}
	
	ccm_openComposerPublishTargetWindow = function(submitOnChoose) {
		var shref = CCM_TOOLS_PATH + '/composer_target?cID=<?php echo $entry->getCollectionID()?>';
		if (submitOnChoose) {
			shref += '&submitOnChoose=1';
		}
		jQuery.fn.dialog.open({
			title: '<?php echo t("Publish Page")?>',
			href: shref,
			width: '550',
			modal: false,
			height: '400'
		});
	}
	
	$(function() {
		<?php if (is_object($v) && $v->isApproved()) { ?>
			$(".ccm-composer-hide-on-approved").hide();
		<?php } ?>

		if ($("input[name=cPublishParentID]").val() < 1) {
			$(".ccm-composer-hide-on-no-target").hide();
		}
		
		var ccm_composerAutoSaveIntervalTimeout = 7000;
		var ccm_composerIsPublishClicked = false;
		
		$("#ccm-submit-discard").click(function() {
			return (confirm('<?php echo t("Discard this draft?")?>'));
		});
		
		$("#ccm-submit-publish").click(function() {
			ccm_composerIsPublishClicked = true;
			$('input[name=ccm-publish-draft]').val(1);
		});
		
		$("#ccm-dashboard-composer-form").submit(function(e) {
			var proceed = true;
			if ($('#ccm-url-slug-loader').is(':visible')) {
				proceed = false;
			}
			else {
				proceed = true;
				<?php if ($entry->isComposerDraft()) { ?>
					if ($("input[name=cPublishParentID]").val() == 0) {
						if (ccm_composerIsPublishClicked) {
							ccm_composerIsPublishClicked = false;			
							$('input[name=ccm-publish-draft]').val(0);
							<?php if ($ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE' || $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE') { ?>
								ccm_openComposerPublishTargetWindow(true);
								proceed = false;
							<?php } else if ($ct->getCollectionTypeComposerPublishMethod() == 'PARENT') { ?>
								proceed = true;
							<?php } else { ?>
								proceed = false;
							<?php } ?>
						}
					}
				<?php } ?>
			}
			if(proceed) {
				jQuery.fn.dialog.showLoader();
				ccm_composerDoAutoSaveAllowed = false;
			}
			else {
				e.preventDefault();
			}
			return proceed;
		});
		ccm_composerAutoSaveInterval = setInterval(function() {
			ccm_composerDoAutoSave();
		}, 
		ccm_composerAutoSaveIntervalTimeout);
		
	});
	</script>
	
	
<?php } else { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer'), false, 'span10 offset1')?>
	
	<?php if (count($ctArray) > 0) { ?>
	<h3><?php echo t('What type of page would you like to write?')?></h3>
	<ul class="item-select-list">
	<?php foreach($ctArray as $ct) { ?>
		<li class="item-select-page"><a href="<?php echo $this->url('/dashboard/composer/write', $ct->getCollectionTypeID())?>"><?php echo $ct->getCollectionTypeName()?></a></li>
	<?php } ?>
	</ul>
	<?php } else { ?>
		<p><?php echo t('You have not setup any page types for Composer.')?></p>
	<?php } ?>

	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
	
<?php } ?>

