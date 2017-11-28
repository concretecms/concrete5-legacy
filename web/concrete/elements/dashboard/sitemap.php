<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::helper('concrete/dashboard/sitemap');


$cID = 1;

if (isset($reveal)) {
	$nc = Page::getByID($reveal);
	$cID = $nc->getCollectionID();
	$node = $nc->getCollectionParentID();
	if ($node < 1) {
		$node = 1;
	}
}

$cID = 1;
if (isset($selectedPageID)) {
	$cID = $selectedPageID;
}

?>
<div class="ccm-pane-controls">
<script type="text/javascript">
var CCM_BACK_TITLE = "<?php echo $previous_title?>";
var CCM_NODE_ACTION = "<?php echo $node_action?>";
var CCM_DIALOG_TITLE = "<?php echo $dialog_title?>";
var CCM_DIALOG_HEIGHT = "<?php echo $dialog_height?>";
var CCM_DIALOG_WIDTH = "<?php echo $dialog_width?>";
var CCM_TARGET_ID = "<?php echo $target_id?>";
var CCM_SITEMAP_EXPLORE_NODE = "<?php echo $node?>";
</script>

<?php $display_mode = $_REQUEST['display_mode'];
if ($display_mode != 'explore') { 
	$display_mode = 'full';
}
?>

<div id="tree" sitemap-wrapper="1" sitemap-select-callback="<?php echo $callback?>" sitemap-instance-id="<?php echo $instance_id?>" <?php if ($display_mode == 'explore') { ?>class="ccm-sitemap-explore"<?php } ?>>
	<ul id="tree-root0" tree-root-node-id="0" sitemap-select-callback="<?php echo $sitemap_select_callback?>" sitemap-display-mode="<?php echo $display_mode?>" sitemap-select-mode="<?php echo $select_mode?>" sitemap-instance-id="<?php echo $instance_id?>">
	</ul>
</div>

<script type="text/javascript">
$(function() {
	ccmSitemapLoad('<?php echo $instance_id?>', '<?php echo $display_mode?>', '<?php echo $select_mode?>', '<?php echo $node?>', '<?php echo $cID?>');
});
</script>

</div>