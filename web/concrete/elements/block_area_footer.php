<?php 
defined('C5_EXECUTE') or die("Access Denied.");
?>
</div>

<?php 

// simple file that controls the adding of blocks.

// $blockTypes is an array using the btID as the key and btHandle as the value.
// It is defined within Area->_getAreaAddBlocks(), which then calls a 
// function in Content to include the file

// note, we're also passed an area & collection object from the original function

$arHandle = $a->getAreaHandle();
$arHandleTrunc = strtolower(preg_replace("/[^0-9A-Za-z]/", "", $a->getAreaHandle()));

$c = $a->getAreaCollectionObject();
$cID = $c->getCollectionID();
$u = new User();
$ap = new Permissions($a);
$cp = new Permissions($c);

if (!$c->isArrangeMode()) { ?>
	<script type="text/javascript">
	ccm_areaMenuObj<?php echo $a->getAreaID()?> = new Object();
	ccm_areaMenuObj<?php echo $a->getAreaID()?>.type = "AREA";
	ccm_areaMenuObj<?php echo $a->getAreaID()?>.aID = <?php echo $a->getAreaID()?>;
	ccm_areaMenuObj<?php echo $a->getAreaID()?>.arHandle = "<?php echo $arHandle?>";
	ccm_areaMenuObj<?php echo $a->getAreaID()?>.maximumBlocks = <?php echo $a->maximumBlocks?>;
    <?php Loader::element('block_area_permissions_js', array('a' => $a, 'ap' => $ap, 'c' => $c, 'cp' => $cp)); ?> 
	$(function() {ccm_menuInit(ccm_areaMenuObj<?php echo $a->getAreaID()?>)});
	</script>
	<?php if ($a->isGlobalArea()) { ?>
		<div id="a<?php echo $a->getAreaID()?>controls" class="ccm-add-block"><?php echo t('Add To Sitewide %s', tc('AreaName', $arHandle))?></div>
	<?php } else { ?>
		<div id="a<?php echo $a->getAreaID()?>controls" class="ccm-add-block"><?php echo t('Add To %s', tc('AreaName', $arHandle))?></div>
	<?php } ?>
<?php } ?>
