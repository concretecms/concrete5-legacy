<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="clearfix">

<?php if (isset($_REQUEST['message'])) { ?>


<div class="alert-message success" id="ccm-permissions-message-list">
<?php
if ($_REQUEST['message'] == 'custom_options_saved') { ?>
	<?php echo t('Custom Options saved.')?>
<?php } else if ($_REQUEST['message'] == 'workflows_saved') { ?>
	<?php echo t('Workflow Options saved.')?>
<?php } else if ($_REQUEST['message'] == 'entity_removed') { ?>
	<?php echo t('User/Group Removed')?>
<?php } else if ($_REQUEST['message'] == 'entity_added') { ?>
	<?php echo t('User/Group Added')?>
<?php } ?>
</div>

<?php } ?>
</div>
<script type="text/javascript">
$(function() {
	$("#ccm-permissions-message-list").show('highlight', {'color': '#fff'}, function() {
		setTimeout(function() {
			$("#ccm-permissions-message-list").fadeOut(300, 'easeInExpo');
		}, 1200);
	});
});
</script>

