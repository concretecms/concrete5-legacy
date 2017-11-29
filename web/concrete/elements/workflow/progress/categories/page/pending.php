<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
$list = $category->getPendingWorkflowProgressList();
$items = $list->get();
if (count($items) > 0) { ?>

<div id="ccm-workflow-waiting-for-me-wrapper">
<table class="ccm-results-list" id="ccm-workflow-waiting-for-me">
<tr>
	<th class="<?php echo $list->getSearchResultsClass('cvName')?>"><a href="<?php echo $list->getSortByURL('cvName', 'asc')?>"><?php echo t('Page Name')?></a></th>
	<th><?php echo t('URL')?></th>
	<th class="<?php echo $list->getSearchResultsClass('wpDateLastAction')?>"><a href="<?php echo $list->getSortByURL('wpDateLastAction', 'desc')?>"><?php echo t('Last Action')?></a></th>
	<th class="<?php echo $list->getSearchResultsClass('wpCurrentStatus')?>"><a href="<?php echo $list->getSortByURL('wpCurrentStatus', 'desc')?>"><?php echo t('Current Status')?></a></th>
	<th>&nbsp;</th>
</tr>
<?php 
$dh = Loader::helper('date');
/* @var $dh DateHelper */
$noitems = true;
	foreach($items as $it) { 
	$p = $it->getPageObject();
	$wp = $it->getWorkflowProgressObject();
	$wf = $wp->getWorkflowObject();
	if ($wf->canApproveWorkflowProgressObject($wp)) { 
		$noitems = false;
	?>
<tr class="ccm-workflow-waiting-for-me-row<?php echo $wp->getWorkflowProgressID()?>">
	<td><?php echo $p->getCollectionName()?></td>
	<td><a href="<?php echo Loader::helper('navigation')->getLinkToCollection($p)?>"><?php echo $p->getCollectionPath()?></a>
	<td><?php echo $dh->formatDateTime($wp->getWorkflowProgressDateLastAction(), true, false)?></td>
	<td><a href="javascript:void(0)" title="<?php echo t('Click for history.')?>" onclick="$(this).parentsUntil('tr').parent().next().show()"><?php echo $wf->getWorkflowProgressStatusDescription($wp)?></a></td>
	<td class="ccm-workflow-progress-actions">
	<form action="<?php echo $wp->getWorkflowProgressFormAction()?>" method="post">
	<?php $actions = $wp->getWorkflowProgressActions(); ?>
	<?php foreach($actions as $act) { 
		$attribs = '';
		$_attribs = $act->getWorkflowProgressActionExtraButtonParameters();
		foreach($_attribs as $key => $value) {
			$attribs .= $key . '="' . $value . '" ';
		}
		$br = '';
		$bl = '';
		if ($act->getWorkflowProgressActionStyleInnerButtonLeftHTML()) {
			$bl = $act->getWorkflowProgressActionStyleInnerButtonLeftHTML() . ' ';
		}
		if ($act->getWorkflowProgressActionStyleInnerButtonRightHTML()) {
			$br = ' ' . $act->getWorkflowProgressActionStyleInnerButtonRightHTML();
		}
		if ($act->getWorkflowProgressActionURL() != '') {
			print '<a href="' . $act->getWorkflowProgressActionURL() . '&source=dashboard" ' . $attribs . ' class="btn btn-mini ' . $act->getWorkflowProgressActionStyleClass() . '">' . $bl . $act->getWorkflowProgressActionLabel() . $br . '</a> ';
		} else { 
			print '<button type="submit" ' . $attribs . ' name="action_' . $act->getWorkflowProgressActionTask() . '" class="btn btn-mini ' . $act->getWorkflowProgressActionStyleClass() . '">' . $bl . $act->getWorkflowProgressActionLabel() . $br . '</button> ';
		}
	 } ?>
	</form>
	</td>
</tr>
<tr class="ccm-workflow-waiting-for-me-row<?php echo $wp->getWorkflowProgressID()?> ccm-workflow-progress-history">
	<td colspan="6">
		<?php echo Loader::element('workflow/progress/history', array('wp' => $wp))?>
	</td>
</tr>

<?php } 

} ?>
<?php if ($noitems) { ?>
	<tr>
		<td colspan="5"><?php echo t('There is nothing currently waiting for you.')?></td>
	</tr>
<?php } ?>
</table>
</div>

<script type="text/javascript">
$(function() {
	$('.ccm-workflow-progress-actions form').ajaxForm({ 
		dataType: 'json',
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var wpID = r.wpID;
			$('.ccm-workflow-waiting-for-me-row' + wpID).fadeOut(300, function() {
				jQuery.fn.dialog.hideLoader();
				$('.ccm-workflow-waiting-for-me-row' + wpID).remove();
				if ($('#ccm-workflow-waiting-for-me tr').length == 1) { 
					$("#ccm-workflow-waiting-for-me-wrapper").html('<?php echo t('None.')?>');
				}
			});
		}
	});
});
</script>

<?php } else { ?>
	<p><?php echo t('None.')?></p>
<?php } ?>