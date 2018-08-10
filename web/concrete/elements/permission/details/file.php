<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php $pk = FilePermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($f);
?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>

<script type="text/javascript">
var ccm_permissionDialogURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file?fID=<?php echo $f->getFileID()?>'; 
</script>