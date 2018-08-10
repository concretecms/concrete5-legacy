<?php
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
$ih = Loader::helper('concrete/interface'); 
$f = File::getByID($_REQUEST['fID']);
$cp = new Permissions($f);
if (!$cp->canAdmin()) {
	die(t("Access Denied."));
}
$form = Loader::helper('form');

if ($_POST['task'] == 'set_password') {
	$f->setPassword($_POST['fPassword']);
	exit;
}


Loader::model('file_storage_location');
if ($_POST['task'] == 'set_location') {
	if ($_POST['fslID'] == 0) {
		$f->setStorageLocation(0);
	} else {
		$fsl = FileStorageLocation::getByID($_POST['fslID']);
		if (is_object($fsl)) {
			$f->setStorageLocation($fsl);
		}
	}
	exit;
}

?>

<div class="ccm-ui" id="ccm-file-permissions-dialog-wrapper">

<ul class="tabs" id="ccm-file-permissions-tabs">
	<?php if (PERMISSIONS_MODEL != 'simple') { ?>
		<li class="active"><a href="javascript:void(0)" id="ccm-file-permissions-advanced"><?php echo t('Permissions')?></a></li>
	<?php } ?>
	<li <?php if (PERMISSIONS_MODEL == 'simple') { ?> class="active" <?php } ?>><a href="javascript:void(0)" id="ccm-file-password"><?php echo t('Protect with Password')?></a></li>
	<li><a href="javascript:void(0)" id="ccm-file-storage"><?php echo t('Storage Location')?></a></li>
</ul>

<div class="clearfix"></div>

<?php if (PERMISSIONS_MODEL != 'simple') { ?>

<div id="ccm-file-permissions-advanced-tab">

	<?php Loader::element('permission/lists/file', array('f' => $f)); ?>

</div>
<?php } ?>

<div id="ccm-file-password-tab" <?php if (PERMISSIONS_MODEL != 'simple') { ?> style="display: none" <?php } ?>>
<br/>

<h4><?php echo t('Requires Password to Access')?></h4>

<p><?php echo t('Leave the following form field blank in order to allow everyone to download this file.')?></p>

<form method="post" id="ccm-<?php echo $searchInstance?>-password-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?php echo $form->hidden('task', 'set_password')?>
<?php echo $form->hidden('fID', $f->getFileID())?>
<?php echo $form->text('fPassword', $f->getPassword(), array('style' => 'width: 250px'))?>

<div id="ccm-file-password-buttons"  style="display: none">
<?php echo $ih->button_js(t('Save Password'), 'ccm_alSubmitPasswordForm(\'' . $searchInstance . '\')', 'left', 'primary')?>
</div>

</form>

<div class="help-block"><p><?php echo t('Users who access files through the file manager will not be prompted for a password.')?></p></div>

</div>

<div id="ccm-file-storage-tab" style="display: none">

<br/>

<h4><?php echo t('Choose File Storage Location')?></h4>

<form method="post" id="ccm-<?php echo $searchInstance?>-storage-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<div class="help-block"><p><?php echo t('All versions of a file will be moved to the selected location.')?></p></div>

<?php echo $form->hidden('task', 'set_location')?>
<?php echo $form->hidden('fID', $f->getFileID())?>
<label class="radio"><?php echo $form->radio('fslID', 0, $f->getStorageLocationID()) ?> <?php echo t('Default Location')?> (<?php echo DIR_FILES_UPLOADED?>)</label>

<?php
$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
if (is_object($fsl)) { ?>
	<label class="radio"><?php echo $form->radio('fslID', FileStorageLocation::ALTERNATE_ID, $f->getStorageLocationID()) ?> <?php echo $fsl->getName()?> (<?php echo $fsl->getDirectory()?>)</label>
<?php } ?>
</form>

<div id="ccm-file-storage-buttons" style="display: none">
<?php echo $ih->button_js(t('Save Location'), 'ccm_alSubmitStorageForm(\'' . $searchInstance . '\')', 'left', 'primary')?>
</div>



</div>

</div>

<script type="text/javascript">
	
$("#ccm-file-permissions-tabs a").click(function() {
	$("li.active").removeClass('active');
	$("#" + ccm_fpActiveTab + "-tab").hide();
	ccm_fpActiveTab = $(this).attr('id');
	$(this).parent().addClass("active");
	$("#" + ccm_fpActiveTab + "-tab").show();
	ccm_filePermissionsSetupButtons();
});

ccm_filePermissionsSetupButtons = function() {
	if ($("#" + ccm_fpActiveTab + "-buttons").length > 0) {
		$("#ccm-file-permissions-dialog-wrapper").closest('.ui-dialog-content').jqdialog('option', 'buttons', [{}]);
		$("#" + ccm_fpActiveTab + "-buttons").clone().show().appendTo($('#ccm-file-permissions-dialog-wrapper').closest('.ui-dialog').find('.ui-dialog-buttonpane').addClass('ccm-ui'));
	} else {
		$("#ccm-file-permissions-dialog-wrapper").closest('.ui-dialog-content').jqdialog('option', 'buttons', false);
	}

}

var ccm_fpActiveTab;

$(function() {
<?php if (PERMISSIONS_MODEL == 'simple') { ?>
	ccm_fpActiveTab = "ccm-file-password";
<?php } else { ?>
	ccm_fpActiveTab = "ccm-file-permissions-advanced";
<?php } ?>

	ccm_filePermissionsSetupButtons();
	ccm_setupGridStriping('ccmPermissionsTable');
	$("#ccm-<?php echo $searchInstance?>-storage-form").submit(function() {
		ccm_alSubmitStorageForm('<?php echo $searchInstance?>');
		return false;
	});
	$("#ccm-<?php echo $searchInstance?>-password-form").submit(function() {
		ccm_alSubmitPasswordForm('<?php echo $searchInstance?>');
		return false;
	});
});
	
</script>
