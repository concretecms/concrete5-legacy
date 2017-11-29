<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper("form");
$tp = new TaskPermission();
$dt = Loader::helper('form/date_time');
if (!$tp->canAccessUserSearch() && !$tp->canAccessGroupSearch()) { 
	die(t("You do not have user search or group search permissions."));
}
$pae = false;
if ($_REQUEST['peID']) {
	$pae = PermissionAccessEntity::getByID($_REQUEST['peID']);
}
if (!is_object($pae)) {
	$pae = false;
}

$pd = false;
if ($_REQUEST['pdID']) {
	$pd = PermissionDuration::getByID($_REQUEST['pdID']);
}
if (!is_object($pd)) {
	$pd = false;
}

if ($_POST['task'] == 'save_permissions') { 
	$js = Loader::helper('json');
	$r = new stdClass;
	
	if (is_object($pae)) {
		$pd = PermissionDuration::translateFromRequest();
	} else {
		$r->error = true;
		$r->message = t('You must choose who this permission is for.');
	}
	
	if (!$r->error) {
		$r->peID = $pae->getAccessEntityID();
		if (is_object($pd)) {
			$r->pdID = $pd->getPermissionDurationID();
		} else {
			$r->pdID = 0;
		}
	}
	
	print $js->encode($r);
	exit;
}

?>
<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">

<form id="ccm-permissions-access-entity-form" method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity">
<input type="hidden" name="task" value="save_permissions" />
<?php echo $form->hidden('accessType');?>
<?php echo $form->hidden('peID');?>
<?php echo $form->hidden('pdID');?>

<h4><?php echo t('Access')?></h4>

<p><?php echo t('Who gets access to this permission?')?></p>

<div id="ccm-permissions-access-entity-label"><?php if (is_object($pae)) { ?><div class="alert alert-info"><?php echo $pae->getAccessEntityLabel()?></div><?php } else { ?><div class="alert alert-warning"><?php echo t('None Selected')?></div><?php } ?></div>

<?php if (!is_object($pae)) { ?>

<div class="btn-group">
	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
	<i class="icon-plus-sign"></i> <?php echo t('Select')?>
	<span class="caret"></span>
		</a>
	<ul class="dropdown-menu">
	<?php 
	$category = PermissionKeyCategory::getByHandle($_REQUEST['pkCategoryHandle']);
	$entitytypes = PermissionAccessEntityType::getList($category);
	foreach($entitytypes as $type) { ?>
		<li><?php echo $type->getAccessEntityTypeLinkHTML()?></li>
	<?php } ?>
	</ul>
</div>
<br/><br/>

<?php foreach($entitytypes as $type) { ?>
	
<?php if ($type->getPackageID() > 0) { ?>
	<?php Loader::packageElement('permission/access/entity/types/' . $type->getAccessEntityTypeHandle(), $type->getPackageHandle(), array('type' => $type)); ?>
<?php } else { ?>
	<?php Loader::element('permission/access/entity/types/' . $type->getAccessEntityTypeHandle(), array('type' => $type)); ?>
<?php } ?>

	
<?php } ?>

<?php } ?>


<?php if (!isset($_REQUEST['disableDuration'])) { ?>

<h4><?php echo t('Time Settings')?></h4>

<p><?php echo t('How long will this permission be valid for?')?></p>

<?php echo Loader::element('permission/duration', array('pd' => $pd)); ?>

<?php } ?>

<div class="dialog-buttons">
	<input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?php echo t('Cancel')?>" class="btn" />
	<input type="submit" onclick="$('#ccm-permissions-access-entity-form').submit()" value="<?php echo t('Save')?>" class="btn primary ccm-button-right" />
</div>


</form>

</div>

<script type="text/javascript">
	$("#ccm-permissions-access-entity-form").ajaxForm({
		beforeSubmit: function(r) {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			if (r.error) {
				ccmAlert.notice('<?php echo t("Error")?>', r.message);
			} else {
				if (typeof(ccm_addAccessEntity) == 'function') { 
					ccm_addAccessEntity(r.peID, r.pdID, '<?php echo addslashes($_REQUEST["accessType"])?>');
				} else {
					alert(r.peID);
					alert(r.pdID);
				}
			}
		}
	});
	
</script>


