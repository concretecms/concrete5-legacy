<div class="control-group">
<fieldset>
<legend><?php echo t("Workflow Access")?></legend>
<div id="ccm-permission-list-form">
<?php echo Loader::element("permission/lists/basic_workflow", array('enablePermissions' => true, 'workflow' => $workflow));?>
</div>
</fieldset></div>