<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
$arHandle = $b->getAreaHandle();
?>

<?php $pk = BlockPermissionKey::getByID($_REQUEST['pkID']); ?>
<?php $pk->setPermissionObject($b); ?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
var ccm_permissionDialogURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?bID=<?php echo $b->getBlockID()?>&arHandle=<?php echo urlencode($b->getAreaHandle())?>&cvID=<?php echo $c->getVersionID()?>&bID=<?php echo $b->getBlockID()?>&cID=<?php echo $c->getCollectionID()?>&btask=set_advanced_permissions'; 
</script>