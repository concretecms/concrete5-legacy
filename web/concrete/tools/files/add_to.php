<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Access Denied."));
}
$files = array();
$searchInstance = $_REQUEST['searchInstance'];

if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID($fID);
		$fp = new Permissions($f);
		if ($fp->canRead()) {
			$files[] = $f;
		}
	}
} else {
	$f = File::getByID($_REQUEST['fID']);
	$fp = new Permissions($f);
	if ($fp->canRead()) {
		$files[] = $f;
	}
} ?>
<form method="post" id="ccm-<?=$searchInstance?>-add-to-set-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to/">
	<?=$form->hidden('task', 'add_to_sets')?>
	<? foreach($files as $f) { ?>
		<input type="hidden" name="fID[]" value="<?=$f->getFileID();?>" />
	<? } 
	Loader::element('files/add_to_sets', array('disableForm'=>true)); ?>
	<br/><br/>
	<? $h = Loader::helper('concrete/interface'); ?>
	<div class="dialog-buttons">
		<?=$h->button_js(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
		<?=$h->button_js(t('Update'), '$(\'#ccm-'.$searchInstance.'-add-to-set-form\').submit();', 'right', 'primary'); ?>
	</div>
</form>