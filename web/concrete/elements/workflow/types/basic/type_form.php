<div class="control-group">
<fieldset>
<legend><?php echo t("Workflow Access")?></legend>

<?php echo Loader::element("permission/lists/basic_workflow", array('enablePermissions' => false, 'workflow' => $workflow));?>
</fieldset></div>