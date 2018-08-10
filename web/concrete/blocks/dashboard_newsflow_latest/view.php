<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php if (is_object($slot)) { ?>
<div>
<?php echo $slot->getContent()?>
</div>
<?php } ?>

<?php if ($controller->slot == 'C') { ?>
	<div class="newsflow-paging-next"><a href="javascript:void(0)" onclick="ccm_showNewsflowOffsite(<?php echo $editionID?>)"><span></span></a></div>

	<script type="text/javascript">
	$(function() {
		ccm_setNewsflowPagingArrowHeight();
	});
	</script>
	
<?php } ?>