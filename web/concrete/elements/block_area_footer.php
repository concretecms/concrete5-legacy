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

if ($a->areaAcceptsBlocks()) { ?>

<?php if (!$c->isArrangeMode()) { ?>
    <script type="text/javascript">
    $(function() {
        var ccm_areaMenuObj<?=$a->getAreaID()?> = {};
        ccm_areaMenuObj<?=$a->getAreaID()?>.type = "AREA";
        ccm_areaMenuObj<?=$a->getAreaID()?>.aID = <?=$a->getAreaID()?>;
        ccm_areaMenuObj<?=$a->getAreaID()?>.arHandle = "<?=$arHandle?>";
        ccm_areaMenuObj<?=$a->getAreaID()?>.canAddBlocks = <?=$ap->canAddBlocks()?>;
        ccm_areaMenuObj<?=$a->getAreaID()?>.canWrite = <?=$ap->canWrite()?>;
    <?php if ($cp->canAdmin() && PERMISSIONS_MODEL != 'simple') { ?>
        ccm_areaMenuObj<?=$a->getAreaID()?>.canModifyGroups = true;
    <?php } ?>
    <?php if ($ap->canWrite() && ENABLE_AREA_LAYOUTS == true && (!$a->isGlobalArea()) && (!$c->isMasterCollection())) { ?>
        ccm_areaMenuObj<?=$a->getAreaID()?>.canLayout = true;
    <?php } else { ?>
        ccm_areaMenuObj<?=$a->getAreaID()?>.canLayout = false;
    <?php } ?>
    <?php if ($ap->canWrite() && ENABLE_CUSTOM_DESIGN == true && (!$c->isMasterCollection())) { ?>
        ccm_areaMenuObj<?=$a->getAreaID()?>.canDesign = true;
    <?php } else { ?>
        ccm_areaMenuObj<?=$a->getAreaID()?>.canDesign = false;
    <?php } ?>
        ccm_menuInit(ccm_areaMenuObj<?=$a->getAreaID()?>);
    } );
    </script>
    <?php if ($a->isGlobalArea()) { ?>
        <div id="a<?=$a->getAreaID()?>controls" class="ccm-add-block"><?=t('Add To Sitewide %s', $arHandle)?></div>
    <?php } else { ?>
        <div id="a<?=$a->getAreaID()?>controls" class="ccm-add-block"><?=t('Add To %s', $arHandle)?></div>
    <?php } ?>
    <?php } ?>
<?php } ?>
