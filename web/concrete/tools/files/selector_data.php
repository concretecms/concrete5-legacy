<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (!Loader::helper('validation/numbers')->integer($_REQUEST['fID'])) {
	die(t('Access Denied'));
}

$selectedField = Loader::helper('text')->entities($_REQUEST['ccm_file_selected_field']);
$fID = Loader::helper('text')->entities($_REQUEST['fID']);

$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}



$f = File::getByID($fID);
$fp = new Permissions($f);
if (!$fp->canViewFileInFileManager()) {
	die(t("Access Denied."));
}

$fv = $f->getApprovedVersion();

$canViewInline = $fv->canView() ? 1 : 0;
$canEdit = $fv->canEdit() ? 1 : 0;
?>

<div class="ccm-file-selected" fID="<?php echo $fID?>" ccm-file-manager-field="<?php echo $selectedField?>" ccm-file-manager-can-duplicate="<?php echo $fp->canCopyFile()?>" ccm-file-manager-can-admin="<?php echo ($fp->canEditFilePermissions())?>" ccm-file-manager-can-delete="<?php echo $fp->canDeleteFile()?>" ccm-file-manager-can-view="<?php echo $canViewInline?>" ccm-file-manager-can-replace="<?php echo $fp->canEditFileContents()?>" ccm-file-manager-can-edit="<?php echo $canEdit?>"  >
<div class="ccm-file-selected-thumbnail"><?php echo $fv->getThumbnail(1)?></div>
<div class="ccm-file-selected-data"><div><?php echo $fv->getTitle()?></div><div></div></div>
<div class="ccm-spacer">&nbsp;</div>
</div>
