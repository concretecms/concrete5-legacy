<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	if ($a->isGlobalArea()) {
		$c = Page::getCurrentPage();
		$cID = $c->getCollectionID();
	} else {
		$cID = $b->getBlockCollectionID();
		$c = $b->getBlockCollectionObject();
	}
	$btw = BlockType::getByID($b->getBlockTypeID());
	$btOriginal = $btw;
	$bID = $b->getBlockID();
	$heightPlus = 20;
	if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
		$_bi = $b->getInstance();
		$_bo = Block::getByID($_bi->getOriginalBlockID());
		$btOriginal = BlockType::getByHandle($_bo->getBlockTypeHandle());
		$heightPlus = 80;
	}
	$isAlias = $b->isAlias();
	$u = new User();
	$numChildren = (!$isAlias) ? $b->getNumChildren() : 0;
	if ($isAlias) {
		//$message = 'This item is an alias. Editing it will create a new instance of this block.';
		$deleteMessage = t('Do you want to delete this block?');
	} else if ($numChildren) {
		$editMessage =  t('This block is aliased by other blocks. If you edit this block, your changes will effect those other blocks. Are you sure you want to edit this block?');
		$deleteMessage = t('Do you want to delete this block? This item is an original. If you delete it, you will delete all blocks aliased to it');
	} else {
		$deleteMessage = t('Do you want to delete this block?');
	}
	if ($_GET['step']) {
		$step = "&step={$_GET['step']}";
	}
?>
	

<script type="text/javascript">
$(function() {
<?php $id = $bID . $a->getAreaID(); ?>

var ccm_menuObj<?php echo $id?> = {};
ccm_menuObj<?php echo $id?>.type = "BLOCK";
ccm_menuObj<?php echo $id?>.arHandle = '<?php echo $a->getAreaHandle()?>';
ccm_menuObj<?php echo $id?>.aID = <?php echo $a->getAreaID()?>;
ccm_menuObj<?php echo $id?>.bID = <?php echo $bID?>;
ccm_menuObj<?php echo $id?>.cID = <?php echo $cID?>;
<?php if ($p->canWrite() && $btOriginal->getBlockTypeHandle() != BLOCK_HANDLE_STACK_PROXY) { ?>
ccm_menuObj<?php echo $id?>.canWrite =true;
<?php if ($b->isEditable()) { ?>
	ccm_menuObj<?php echo $id?>.hasEditDialog = true;
<?php } else { ?>
	ccm_menuObj<?php echo $id?>.hasEditDialog = false;
<?php } ?>
ccm_menuObj<?php echo $id?>.btName = <?php echo Loader::helper('json')->encode(t($btOriginal->getBlockTypeName()))?>;
ccm_menuObj<?php echo $id?>.width = <?php echo $btOriginal->getBlockTypeInterfaceWidth()?>;
ccm_menuObj<?php echo $id?>.height = <?php echo $btOriginal->getBlockTypeInterfaceHeight()+$heightPlus ?>;
<?php } else if ($btOriginal->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) { 
	if (is_object($_bo)) {
		$bi = $_bo->getInstance();
	} else { 
		$bi = $b->getInstance();
	}
	$stack = Stack::getByID($bi->stID);
	if (is_object($stack)) {
		$sp = new Permissions($stack);
		if ($sp->canWrite()) {
		?>
		ccm_menuObj<?php echo $id?>.canWriteStack =true;
		ccm_menuObj<?php echo $id?>.stID = <?php echo $bi->stID?>;
		<?php } 
	}
}
?>
ccm_menuObj<?php echo $id?>.canCopyToScrapbook = true;
<?php if ($p->canEditBlockPermissions() && PERMISSIONS_MODEL != 'simple' && (!$a->isGlobalArea())) { ?>
ccm_menuObj<?php echo $id?>.canModifyGroups = true;
<?php }
if (PERMISSIONS_MODEL != 'simple' && $p->canGuestsViewThisBlock() && $p->canScheduleGuestAccess() && (!$a->isGlobalArea())) { ?>
	ccm_menuObj<?php echo $id?>.canScheduleGuestAccess = true;
<?php }
if ($p->canEditBlockDesign() && ENABLE_CUSTOM_DESIGN == true) { ?>
	ccm_menuObj<?php echo $id?>.canDesign = true;
<?php } else { ?>
	ccm_menuObj<?php echo $id?>.canDesign = false;
<?php }
if ($p->canEditBlockCustomTemplate()) { ?>
	ccm_menuObj<?php echo $id?>.canEditBlockCustomTemplate = true;
<?php } else { ?>
	ccm_menuObj<?php echo $id?>.canEditBlockCustomTemplate = false;
<?php }
if ($p->canEditBlockPermissions()) { ?>
ccm_menuObj<?php echo $id?>.canAdmin = true;
<?php }
if ($p->canDeleteBlock()) { ?>
ccm_menuObj<?php echo $id?>.canDelete = true;
ccm_menuObj<?php echo $id?>.deleteMessage = "<?php echo $deleteMessage?>";
<?php }
if ($c->isMasterCollection() && !$a->isGlobalArea()) { ?>
ccm_menuObj<?php echo $id?>.canAliasBlockOut = true;
<?php
$ct = CollectionType::getByID($c->getCollectionTypeID());
if ($ct->isCollectionTypeIncludedInComposer()) { ?>
	ccm_menuObj<?php echo $id?>.canSetupComposer = true;
<?php }

}

if ($p->canWrite() && (!$a->isGlobalArea())) {  ?>
	ccm_menuObj<?php echo $id?>.canArrange = true;
<?php 
}
if ($editMessage) { ?>
ccm_menuObj<?php echo $id?>.editMessage = "<?php echo $editMessage?>";
<?php } ?>
ccm_menuInit(ccm_menuObj<?php echo $id?>);
});

</script>
