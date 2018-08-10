<?php
	defined('C5_EXECUTE') or die("Access Denied.");
?>
<?php
$bo = Block::getByID($bOriginalID);
$bp = new Permissions($bo);
$bo->setProxyBlock($b);
if ($bp->canWrite()) {
	$bv = new BlockView(); ?>
	
		<div class="ccm-ui">
			<div class="alert alert-info"><?php echo t("This block was copied from another location. Editing it will create a new instance of it.")?></div>
		</div>
		
	<?php
	
	$bv->render($bo, 'edit', array(
		'c' => $c,
		'a' => $a
	));
} ?>
