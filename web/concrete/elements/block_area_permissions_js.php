<?php 
defined('C5_EXECUTE') or die("Access Denied.");
?>
<?php
// Adds the permissions specification for a given area to that area's
// javascript configuration object.
// $a - the area (REQUIRED)
// $ap - the area's permissions object (optional optimization)
// $c - the collection that the area is in (optional optimization)
// $cp - the collection's permissions object (optional optimization)

$ap = isset($ap) ? $ap : new Permissions($a);
$c = isset($c) ? $c : $a->getAreaCollectionObject();
$cp = isset($cp) ? $cp : new Permissions($cp);
?>
ccm_areaMenuObj<?=$a->getAreaID()?>.canAddStacks = <?= (int) ($ap->canAddStacks() && $a->areaAcceptsBlocks()) ?>;
ccm_areaMenuObj<?=$a->getAreaID()?>.canAddBlocks = <?= (int) ($ap->canAddBlockToArea() && $a->areaAcceptsBlocks()) ?>;
ccm_areaMenuObj<?=$a->getAreaID()?>.canWrite = <?=$ap->canEditAreaContents()?>;
<?php if ($ap->canEditAreaPermissions() && PERMISSIONS_MODEL != 'simple') { ?>
    ccm_areaMenuObj<?=$a->getAreaID()?>.canModifyGroups = true;
<?php } ?>
<?php if ($ap->canAddLayoutToArea() && ENABLE_AREA_LAYOUTS == true && (!$a->isGlobalArea()) && (!$c->isMasterCollection()) && $a->areaAcceptsBlocks()) { ?>
    ccm_areaMenuObj<?=$a->getAreaID()?>.canLayout = true;
<?php } else { ?>
    ccm_areaMenuObj<?=$a->getAreaID()?>.canLayout = false;
<?php } ?>
<?php if ($ap->canEditAreaDesign() && ENABLE_CUSTOM_DESIGN == true && (!$c->isMasterCollection())) { ?>
    ccm_areaMenuObj<?=$a->getAreaID()?>.canDesign = true;
<?php } else { ?>
    ccm_areaMenuObj<?=$a->getAreaID()?>.canDesign = false;
<?php } ?>
