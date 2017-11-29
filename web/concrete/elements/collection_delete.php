<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<?php
$sh = Loader::helper('concrete/dashboard/sitemap');
$numChildren = $c->getNumChildren();
$u = new User();
$securityHelper = Loader::helper('security');
?>

<script type="text/javascript">
$(function() {
	$("#ccmDeletePageForm").ajaxForm({
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
				if (r.deferred) {
		 			ccmAlert.hud(ccmi18n_sitemap.deletePageSuccessDeferredMsg, 2000, 'delete_small', ccmi18n_sitemap.deletePage);
				} else {
		 			ccmAlert.hud(ccmi18n_sitemap.deletePageSuccessMsg, 2000, 'delete_small', ccmi18n_sitemap.deletePage);
					<?php if ($_REQUEST['display_mode'] == 'explore') { ?>
						ccmSitemapExploreNode('<?php echo $_REQUEST['instance_id']?>', 'explore', '<?php echo $_REQUEST['select_mode']?>', r.cParentID);
					<?php } else { ?>
						deleteBranchFade(r.cID);
					<?php } ?>
				}
			} else {
				window.location.href = '<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=' + r.refreshCID;
			}
		}
	});
});
</script>

<?php if ($c->getCollectionID() == 1) {  ?>
	<div class="error alert-message"><?php echo t('You may not delete the home page.');?></div>
	<div class="dialog-buttons"><input type="button" class="btn" value="<?php echo t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
<?php }  else if ($numChildren > 0 && !$u->isSuperUser()) { ?>
		<div class="error alert-message"><?php echo t('Before you can delete this page, you must delete all of its child pages.')?></div>
		<div class="dialog-buttons"><input type="button" class="btn" value="<?php echo t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
		
	<?php } else { 

		$request_rel = $securityHelper->sanitizeString($_REQUEST['rel']);
		?>
		
		<div class="ccm-buttons">

		<form method="post" id="ccmDeletePageForm" action="<?php echo $c->getCollectionAction()?>">	
			<input type="hidden" name="rel" value="<?php echo h($request_rel); ?>" />

			<div class="dialog-buttons"><input type="button" class="btn" value="<?php echo t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" />
			<a href="javascript:void(0)" onclick="$('#ccmDeletePageForm').submit()" class="ccm-button-right btn error"><span><?php echo t('Delete')?></span></a>
			</div>
		<?php if($c->isSystemPage()) { ?>
			<div class="alert alert-error"><?php echo t('Warning! This is a system page. Deleting it could potentially break your site. Please proceed with caution.') ?></div>
		<?php } ?>
		<h4><?php echo t('Are you sure you wish to delete this page?')?></h4>
		<?php if ($u->isSuperUser() && $numChildren > 0) { ?>
			<h5><?php echo t('This will remove %s child page(s).', $numChildren)?></h5>
		<?php } ?>
		
		<?php if (ENABLE_TRASH_CAN) { ?>
			<p><?php echo t('Deleted pages are moved to the trash can in the sitemap.')?></p>
		<?php } else { ?>
			<p><?php echo t('This cannot be undone.')?></p>
		<?php } ?>
		
			<input type="hidden" name="cID" value="<?php echo $c->getCollectionID()?>">
			<input type="hidden" name="ctask" value="delete">
			<input type="hidden" name="processCollection" value="1" />

			<?php 
			$display_mode = $securityHelper->sanitizeString($_REQUEST['display_mode']);
			$instance_id = $securityHelper->sanitizeInt($_REQUEST['instance_id']);
			$select_mode = $securityHelper->sanitizeString($_REQUEST['select_mode']);
			?>			
			<input type="hidden" name="display_mode" value="<?php echo h($display_mode); ?>" />
			<input type="hidden" name="instance_id" value="<?php echo h($instance_id); ?>" />
			<input type="hidden" name="select_mode" value="<?php echo h($select_mode); ?>" />
		</form>
		</div>
		
	<?php }
?>
</div>