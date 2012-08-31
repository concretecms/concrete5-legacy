<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');

$wfName = $workflow->getWorkflowName();
$type = $workflow->getWorkflowTypeObject();

?>

<input type="hidden" name="wfID" value="<?=$workflow->getWorkflowID()?>" />

<div class="ccm-pane-body">

<?php if (is_object($workflow)) { ?>

	<?php
	$valt = Loader::helper('validation/token');
	$ih = Loader::helper('concrete/interface');
	$delConfirmJS = t('Are you sure you want to remove this workflow?');
	?>
	<script type="text/javascript">
	deleteWorkflow = function() {
		if (confirm('<?=$delConfirmJS?>')) { 
			location.href = "<?=$this->action('delete', $workflow->getWorkflowID(), $valt->generate('delete_workflow'))?>";				
		}
	}
	</script>
	
	<?php print $ih->button_js(t('Delete Workflow'), "deleteWorkflow()", 'right', 'error');?>
<?php } ?>


<h3><?=t('Type')?></h3>
<p><?=$type->getWorkflowTypeName()?></p>

<?php 
if ($type->getPackageID() > 0) { 
	Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle()  . '/type_form', $type->getPackageHandle(), array('type' => $type, 'workflow' => $workflow));
} else {
	Loader::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/type_form', array('type' => $type, 'workflow' => $workflow));
}
?>

</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/workflow/list')?>" class="btn"><?=t('Back to List')?></a>
	<div style="float: right">
<?php 
if ($type->getPackageID() > 0) {
	Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle() . '/type_form_buttons', $type->getPackageHandle(), array('type' => $type, 'workflow' => $workflow));
} ?>
	<a href="<?=$this->action('edit_details', $workflow->getWorkflowID())?>" class="btn"><?=t('Edit Details')?></a>
</div>
</div>