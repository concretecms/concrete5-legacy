<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if (isset($wf)) { ?>

<?php if ($this->controller->getTask() == 'edit_details') { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Workflow'), false, 'span10 offset1', false)?>
<form method="post"  action="<?php echo $this->action('save_workflow_details')?>" method="post" class="form-horizontal">
<input type="hidden" name="wfID" value="<?php echo $wf->getWorkflowID()?>" />
<?php echo Loader::helper('validation/token')->output('save_workflow_details')?>

<div class="ccm-pane-body">
	<?php Loader::element("workflow/edit_type_form_required", array('workflow' => $wf)); ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/workflow/list/view_detail', $wf->getWorkflowID())?>" class="btn"><?php echo t("Cancel")?></a>
	<input type="submit" name="submit" value="<?php echo t('Save')?>" class="ccm-button-right primary btn" />
</div>
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<?php } else { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($wf->getWorkflowName(), false, 'span10 offset1', false)?>

<?php Loader::element("workflow/type_form_required", array('workflow' => $wf)); ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<?php } ?>



<?php } else if ($this->controller->getTask() == 'add' || $this->controller->getTask() == 'submit_add') { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Workflow'), false, 'span10 offset1', false)?>

	<form method="post" class="form-horizontal" action="<?php echo $this->action('submit_add')?>" id="ccm-attribute-type-form">
	<?php echo Loader::helper('validation/token')->output('add_workflow')?>
	<div class="ccm-pane-body">

	<div class="control-group">
	<?php echo $form->label('wfName', t('Name'))?>
	<div class="controls">
		<?php echo $form->text('wfName', $wfName)?>
		<span class="help-inline"><?php echo t('Required')?></span>
	</div>
	</div>

	<div class="control-group">
	<?php echo $form->label('wftID', t('Type'))?>
	<div class="controls">
	
	<?php echo $form->select('wftID', $types)?>
	
	</div>
	</div>

	<?php foreach($typeObjects as $type) { ?>
		
		<div style="display: none" class="ccm-workflow-type-form" id="ccm-workflow-type-<?php echo $type->getWorkflowTypeID()?>">
			<?php 
			if ($type->getPackageID() > 0) { 
				@Loader::packageElement('workflow/types/' . $type->getWorkflowTypeHandle()  . '/add_type_form', $type->getPackageHandle(), array('type' => $type));
			} else {
				@Loader::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/add_type_form', array('type' => $type));
			}
			?>
		</div>
	<?php } ?>

	</div>
	<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/workflow/list')?>" class="btn"><?php echo t("Cancel")?></a>
	<input type="submit" name="submit" value="<?php echo t('Add')?>" class="ccm-button-right primary btn" />
	</div>	
	</form>
	
	<script type="text/javascript">
	$(function() {
		$('select[name=wftID]').change(function() {
			$('.ccm-workflow-type-form').hide();
			$('#ccm-workflow-type-' + $(this).val()).show();
		})
		$('#ccm-workflow-type-' + $('select[name=wftID]').val()).show();
	});
	</script>
	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<?php } else { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Workflows'), false, 'span10 offset1')?>

	<a href="<?php echo View::url('/dashboard/workflow/list', 'add')?>" style="float: right" class="btn primary"><?php echo t("Add Workflow")?></a>
	
	<h4><?php echo count($workflows)?> <?php
		if (count($workflows) == 1) {
			print t('Workflow');
		} else {
			print t('Workflows');
		}
	?></h4>
	<br/>
	<?php foreach($workflows as $workflow) { ?>
	<div class="ccm-workflow">
		<a href="<?php echo $this->url('/dashboard/workflow/list', 'view_detail', $workflow->getWorkflowID())?>"><?php echo $workflow->getWorkflowName()?></a>
	</div>
	<?php } ?>
		
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
<?php } ?>