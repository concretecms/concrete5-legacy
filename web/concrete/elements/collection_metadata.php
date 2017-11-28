<?php
defined('C5_EXECUTE') or die("Access Denied.");
global $c;
Loader::model('collection_types');
Loader::model('collection_attributes');
$dt = Loader::helper('form/date_time');
$uh = Loader::helper('form/user_selector');

if ($cp->canEditPageType()) {
	$ctArray = CollectionType::getList();
}

$pk = PermissionKey::getByHandle('edit_page_properties');
$pk->setPermissionObject($c);
$asl = $pk->getMyAssignment();

$approveImmediately = false;
if ($_REQUEST['approveImmediately'] == 1) {
	$approveImmediately = 1;
}
?>
<div class="ccm-pane-controls ccm-ui">
<?php if ($approveImmediately) { ?>
	<div class="alert-message block-message notice">
		<?php echo t("Note: Since you haven't checked this page out for editing, these changes will immediately be approved.")?>
	</div>
<?php } ?>

<form method="post" name="permissionForm" id="ccmMetadataForm" action="<?php echo $c->getCollectionAction()?>">
<input type="hidden" name="approveImmediately" value="<?php echo $approveImmediately?>" />
<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />

	<script type="text/javascript"> 
		
		function ccm_triggerSelectUser(uID, uName) {
			$('#ccm-uID').val(uID);
			$('#ccm-uName').html(uName);
		}
		
		
		var ccm_activePropertiesTab = "ccm-properties-standard";
		
		$("#ccm-properties-tabs a").click(function() {
			$("li.active").removeClass('active');
			$("#" + ccm_activePropertiesTab + "-tab").hide();
			ccm_activePropertiesTab = $(this).attr('id');
			$(this).parent().addClass("active");
			$("#" + ccm_activePropertiesTab + "-tab").show();
		});
		
		$(function() {
			$("#ccmMetadataForm").ajaxForm({
				type: 'POST',
				iframe: true,
				beforeSubmit: function() {
					jQuery.fn.dialog.showLoader();
				},
				success: function(r) {
					try {
						var r = eval('(' + r + ')');
						jQuery.fn.dialog.hideLoader();
						jQuery.fn.dialog.closeTop();
						if (r != null && r.rel == 'SITEMAP') {
							ccmSitemapHighlightPageLabel(r.cID, r.name);
						} else {
							ccm_mainNavDisableDirectExit();
						}
						ccmAlert.hud(ccmi18n.savePropertiesMsg, 2000, 'success', ccmi18n.properties);
					} catch(e) {
						alert(r);
					}
				}
			});
		});
	</script>
	

	<div id="ccm-required-meta"></div>
	
	
	<?php if (!$c->isMasterCollection()) { ?>
	<ul class="nav-tabs nav" id="ccm-properties-tabs">
		<li class="active"><a href="javascript:void(0)" id="ccm-properties-standard"><?php echo t('Standard Properties')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-properties-custom"><?php echo t('Custom Attributes')?></a></li>
		<li <?php if ($c->isMasterCollection() || !$asl->allowEditPaths()) { ?>style="display: none"<?php } ?>><a href="javascript:void(0)" id="ccm-page-paths"><?php echo t('Page Paths and Location')?></a></li>
	</ul>
	<?php } ?>

	<div id="ccm-properties-standard-tab" <?php if ($c->isMasterCollection()) { ?>style="display: none" <?php } ?>>
	
	<?php if ($asl->allowEditName()) { ?>
	<div class="clearfix">
		<label for="cName"><?php echo t('Name')?></label>
		<div class="input"><input type="text" id="cName" name="cName" value="<?php echo htmlentities( $c->getCollectionName(), ENT_QUOTES, APP_CHARSET) ?>" />
			<span class="help-inline"><?php echo t("Page ID: %s", $c->getCollectionID())?></span>
		</div>
	</div>
	<?php } ?>

	<?php if ($asl->allowEditDateTime()) { ?>
	<div class="clearfix">
		<label for="cDatePublic"><?php echo t('Public Date/Time')?></label>
		<div class="input"><?php print $dt->datetime('cDatePublic', $c->getCollectionDatePublic(null, 'user')); ?></div>
	</div>
	<?php } ?>
	
	<?php if ($asl->allowEditUserID()) { ?>
	<div class="clearfix">
	<label><?php echo t('Owner')?></label>
	<div class="input">
		<?php 
		print $uh->selectUser('uID', $c->getCollectionUserID());
		?>
	</div>
	</div>
	<?php } ?>
	

	<?php if ($asl->allowEditDescription()) { ?>
	<div class="clearfix">
	<label for="cDescription"><?php echo t('Description')?></label>
	<div class="input"><textarea id="cDescription" name="cDescription" class="ccm-input-text" style="width: 495px; height: 50px"><?php echo h($c->getCollectionDescription())?></textarea></div>
	</div>
	<?php } ?>
	
	</div>
	
	<?php if ($asl->allowEditPaths()) { ?>
	<div id="ccm-page-paths-tab" style="display: none">
		<?php if ($c->getCollectionID() != 1) { ?>
		<div class="clearfix">
		<label for="cHandle"><?php echo t('Canonical URL')?></label>
		<div class="input">
		<?php if (!$c->isGeneratedCollection()) { ?>
			<?php echo BASE_URL . DIR_REL;?><?php if (URL_REWRITING == false) { ?>/<?php echo DISPATCHER_FILENAME?><?php } ?><?php
			$cPath = substr($c->getCollectionPath(), strrpos($c->getCollectionPath(), '/') + 1);
			print substr($c->getCollectionPath(), 0, strrpos($c->getCollectionPath(), '/'))?>/<input type="text" name="cHandle" value="<?php echo $cPath?>" id="cHandle" maxlength="128"><input type="hidden" name="oldCHandle" id="oldCHandle" value="<?php echo $c->getCollectionHandle()?>"><br /><br />
		<?php  } else { ?>
			<?php echo $c->getCollectionHandle()?><br /><br />
		<?php  } ?>
			<span class="help-block"><?php echo t('This page must always be available from at least one URL. That URL is listed above.')?></span>
		</div>
		</div>
		<?php } ?>
		
		<?php if (!$c->isGeneratedCollection()) { ?>
		<div class="clearfix" id="ccm-more-page-paths">
			<label><?php echo t('More URLs') ?></label>

			<?php
				$paths = $c->getPagePaths();
				foreach ($paths as $path) {
					if (!$path['ppIsCanonical']) {
						$ppID = $path['ppID'];
						$cPath = $path['cPath'];
						echo '<div class="input ccm-meta-path">' .
			     			'<input type="text" name="ppURL-' . $ppID . '" class="ccm-input-text" value="' . $cPath . '" id="ppID-'. $ppID . '"> ' .
			     			'<a href="javascript:void(0)" class="ccm-meta-path-del">' . t('Remove Path') . '</a></div>'."\n";
					}
				}
			?>
		    <div class="input ccm-meta-path">
	     		<input type="text" name="ppURL-add-0" class="ccm-input-text" value="" id="ppID-add-0">
		 		<a href="javascript:void(0)" class="ccm-meta-path-add"><?php echo t('Add Path')?></a>
			</div>

		</div>
			<div class="input">
		 		<p><?php echo t('Note: Additional page paths are not versioned. They will be available immediately.')?></p>
			</div>			

		<?php } ?>
	
	</div>
	
	<style type="text/css">
	#ccm-more-page-paths div.input {margin-bottom: 10px;}
	</style>
	<?php } ?>
	
	
	<div id="ccm-properties-custom-tab" <?php if (!$c->isMasterCollection()) { ?>style="display: none" <?php } ?>>
		<?php Loader::element('collection_metadata_fields', array('c'=>$c, 'assignment' => $asl) ); ?>
	</div>

	
	<input type="hidden" name="update_metadata" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" class="btn primary ccm-button-right" onclick="$('#ccmMetadataForm').submit()"><?php echo t('Save')?></a>
	</div>
