<?php
defined('C5_EXECUTE') or die("Access Denied.");
global $c; ?>

<a name="_edit<?php echo $b->getBlockID()?>"></a>

<?php $bt = $b->getBlockTypeObject(); ?>

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
		<?php if (is_object($b->getProxyBlock())) { ?>
			ccm_setupBlockForm($(this), '<?php echo $b->getProxyBlock()->getBlockID()?>', 'edit');
		<?php } else { ?>
			ccm_setupBlockForm($(this), '<?php echo $b->getBlockID()?>', 'edit');
		<?php } ?>
	});
});
</script>

<?php
$hih = Loader::helper("concrete/interface/help");
$blockTypes = $hih->getBlockTypes();
$cont = $bt->getController();
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$bx = Block::getByID($b->getController()->getOriginalBlockID());
	$cont = $bx->getController();
}

if (isset($blockTypes[$bt->getBlockTypeHandle()])) {
	$help = $blockTypes[$bt->getBlockTypeHandle()];
} else {
	if ($cont->getBlockTypeHelp()) {
		$help = $cont->getBlockTypeHelp();
	}
}
if (isset($help)) { ?>
	<div class="dialog-help" id="ccm-menu-help-content"><?php 
		if (is_array($help)) { 
			print $help[0] . '<br><br><a href="' . $help[1] . '" class="btn small" target="_blank">' . t('Learn More') . '</a>';
		} else {
			print $help;
		}
	?></div>
<?php } ?>

<?php if ($cont->getBlockTypeWrapperClass() != '') { ?>
	<div class="<?php echo $cont->getBlockTypeWrapperClass();?>">
<?php } ?>

<form method="post" id="ccm-block-form" class="validate form-horizontal" action="<?php echo $b->getBlockEditAction()?>&rcID=<?php echo intval($rcID)?>" enctype="multipart/form-data">

<input type="hidden" name="ccm-block-form-method" value="REGULAR" />

<?php foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?php echo $key?>" value="<?php echo h($val)?>" />
<?php } ?>


<div id="ccm-block-fields">
