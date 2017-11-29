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
ccm_areaMenuObj<?php echo $a->getAreaID()?>.canAddStacks = <?php echo (int) ($ap->canAddStacks() && $a->areaAcceptsBlocks()) ?>;
ccm_areaMenuObj<?php echo $a->getAreaID()?>.canAddBlocks = <?php echo (int) ($ap->canAddBlockToArea() && $a->areaAcceptsBlocks()) ?>;
ccm_areaMenuObj<?php echo $a->getAreaID()?>.canWrite = <?php echo $ap->canEditAreaContents()?>;
<?php if ($ap->canEditAreaPermissions() && PERMISSIONS_MODEL != 'simple' && (!$a->isGlobalArea())) { ?>
    ccm_areaMenuObj<?php echo $a->getAreaID()?>.canModifyGroups = true;
<?php } ?>
<?php if ($ap->canAddLayoutToArea() && ENABLE_AREA_LAYOUTS == true && (!$a->isGlobalArea()) && (!$c->isMasterCollection()) && $a->areaAcceptsBlocks()) { ?>
    ccm_areaMenuObj<?php echo $a->getAreaID()?>.canLayout = true;
<?php } else { ?>
    ccm_areaMenuObj<?php echo $a->getAreaID()?>.canLayout = false;
<?php } ?>
<?php if ($ap->canEditAreaDesign() && ENABLE_CUSTOM_DESIGN == true && (!$c->isMasterCollection())) { ?>
    ccm_areaMenuObj<?php echo $a->getAreaID()?>.canDesign = true;
<?php } else { ?>
    ccm_areaMenuObj<?php echo $a->getAreaID()?>.canDesign = false;
<?php } ?>
