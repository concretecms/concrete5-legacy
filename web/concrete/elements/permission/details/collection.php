<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
if ($cp->canEditPagePermissions()) {
	// if it's composer mode and we have a target, then we hack the permissions collection id
	if (isset($isComposer) && $isComposer) {
		$cd = ComposerPage::getByID($c->getCollectionID());
		if ($cd->isComposerDraft()) {
			if ($cd->getComposerDraftPublishParentID() > 0) {
				if ($cd->getCollectionInheritance() == 'PARENT') {
					$c->cParentID = $cd->getComposerDraftPublishParentID();
					$cpID = $c->getParentPermissionsCollectionID();
					$c->cInheritPermissionsFromCID = $cpID;
				}
			}
		}
	}

}
?>

<?php
$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($c);
?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
var ccm_permissionDialogURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?ctask=set_advanced_permissions&cID=<?php echo $c->getCollectionID()?>'; 
</script>