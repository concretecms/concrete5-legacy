<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<?php 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

if ($c->isAlias() || $c->getCollectionPointerExternalLink() != '') {

if ($c->getCollectionPointerExternalLink() != '') {
	$cID = $c->getCollectionID();
} else {
	$cID = $c->getCollectionPointerOriginalID();
}

?>

<script type="text/javascript">
$(function() {
	$("#ccm-delete-external-link-form").ajaxForm({
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
				<?php if ($_REQUEST['display_mode'] == 'explore') { ?>
					ccmSitemapExploreNode('<?php echo $_REQUEST['instance_id']?>', 'explore', '<?php echo $_REQUEST['select_mode']?>', resp.cParentID);
				<?php } else { ?>
					deleteBranchFade(r.cID);
				<?php } ?>
	 			ccmAlert.hud(ccmi18n_sitemap.deletePageSuccessMsg, 2000, 'delete_small', ccmi18n_sitemap.deletePage);
			} else {
				window.location.href = '<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=' + r.cParentID;
			}
		}
	});
});
</script>

	<form class="form-stacked" method="post" id="ccm-delete-external-link-form" action="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $cID?>&<?php echo Loader::helper('validation/token')->getParameter()?>">		
	
	<?php echo t('Remove this alias or external link?')?>

	<div class="ccm-buttons dialog-buttons">
	<input type="button" class="btn" value="<?php echo t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" />
	<a href="javascript:void(0)" onclick="$('#ccm-delete-external-link-form').submit()" class="btn ccm-button-right accept error"><span><?php echo ('Delete')?></span></a>
	</div>	
	<input type="hidden" name="display_mode" value="<?php echo $_REQUEST['display_mode']?>" />
	<input type="hidden" name="instance_id" value="<?php echo $_REQUEST['instance_id']?>" />
	<input type="hidden" name="select_mode" value="<?php echo $_REQUEST['select_mode']?>" />
	<input type="hidden" name="ctask" value="remove-alias" />
	<input type="hidden" name="processCollection" value="1" />
	<input type="hidden" name="rel" value="SITEMAP" />


</form>
<?php } ?>
</div>
