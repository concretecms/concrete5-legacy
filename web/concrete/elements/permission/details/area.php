<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = $a->getAreaCollectionObject();
?>

<?php $pk = AreaPermissionKey::getByID($_REQUEST['pkID']); ?>
<?php $pk->setPermissionObject($a); ?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
var ccm_permissionDialogURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?atask=set_advanced_permissions&cID=<?php echo $c->getCollectionID()?>&arHandle=<?php echo urlencode($a->getAreaHandle())?>'; 
</script>