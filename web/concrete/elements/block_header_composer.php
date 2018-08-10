<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $bt = BlockType::getByID($b->getBlockTypeID());
$ci = Loader::helper("concrete/urls");
$btIcon = $ci->getBlockTypeIconURL($bt); 			 
$cont = $bt->getController();

?>

<script type="text/javascript">

<?php $ci = Loader::helper("concrete/urls"); ?>
<?php $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	ccm_addHeaderItem("<?php echo $url?>", 'JAVASCRIPT');
<?php } 

$identifier = strtoupper('BLOCK_CONTROLLER_' . $btHandle);
if (is_array($headerItems[$identifier])) {
	foreach($headerItems[$identifier] as $item) { 
		if ($item instanceof CSSOutputObject) {
			$type = 'CSS';
		} else {
			$type = 'JAVASCRIPT';
		}
		?>
		ccm_addHeaderItem("<?php echo $item->file?>", '<?php echo $type?>');
	<?php
	}
}
?>

$(function() {
	$('#ccm-block-form').each(function() {
		ccm_setupBlockForm($(this), false, 'add');
	});
});

</script>

<?php
if ($b->getBlockName() != '') { 
	$btName = $b->getBlockName();
} else {
	$btName = t($bt->getBlockTypeName());
}
?>

<?php if ($displayEditLink) { ?>
	<label class="control-label"><a href="javascript:void(0)" onclick="ccm_composerEditBlock(<?php echo $b->getBlockCollectionID()?>, <?php echo $b->getBlockID()?>, '<?php echo $b->getAreaHandle()?>', <?php echo $bt->getBlockTypeInterfaceWidth()?> , <?php echo $bt->getBlockTypeInterfaceHeight()?> )" ><?php echo $btName?></a></label>
<?php } else { ?>
	<label class="control-label"><?php echo $btName?></label>
<?php } ?>

<div class="controls">
<?php Loader::element('block_header', array('b' => $b))?>
