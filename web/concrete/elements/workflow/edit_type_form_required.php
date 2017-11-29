<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');

$wfName = $workflow->getWorkflowName();
$type = $workflow->getWorkflowTypeObject();

?>

<div class="control-group">
<fieldset>
<legend><?php echo t("Workflow Basics")?></legend>

<div class="control-group">
	<?php echo $form->label('wfName', t('Name'))?>
	<div class="controls">
		<?php echo $form->text('wfName', $wfName)?>
	</div>
</div>
</fieldset></div>

<?php 
if ($type->getPackageID() > 0) { 
	@Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle()  . '/edit_type_form', $type->getPackageHandle(), array('type' => $type, 'workflow' => $workflow));
} else {
	@Loader::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/edit_type_form', array('type' => $type, 'workflow' => $workflow));
}
?>
