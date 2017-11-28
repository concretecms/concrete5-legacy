<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
<?php
$assignments = $cp->getAllTimedAssignmentsForPage();
if (count($assignments) > 0) { ?>

<table class="ccm-permission-grid">
<?php
foreach($assignments as $ppc) {
	$pk = $ppc->getPermissionKeyObject();
	?>
	<tr>
	<td>
	<strong><?php echo $pk->getPermissionKeyDisplayName()?></strong>
	<?php echo t('Permission on ')?><?php
		if ($pk instanceof AreaPermissionKey) {  ?>
			<strong><?php echo $pk->getPermissionObject()->getAreaHandle() ?></strong>.
		<?php } else if ($pk instanceof BlockPermissionKey) { 
			$bt = BlockType::getByID($pk->getPermissionObject()->getBlockTypeID());
			$obj = $pk->getPermissionObject();
			if ($obj->getBlockName() != '') { ?>

			<?php echo t('the %s block named <strong>%s</strong> in <strong>%s</strong> Area. ', t($bt->getBlockTypeName()), $obj->getBlockName(), $pk->getPermissionObject()->getAreaHandle())?>
			
			<?php } else { ?>
			
			<?php echo t('<strong>%s Block</strong> in <strong>%s</strong> Area. ', t($bt->getBlockTypeName()), $pk->getPermissionObject()->getAreaHandle())?>
			
			<?php } ?>		
		<?php } else { ?>
			<strong><?php echo t('Entire Page')?></strong>.
		<?php } ?>
		<?php
		$pd = $ppc->getDurationObject();
		?>
		<div>
		<?php 
		$assignee = t('Nobody');
		$pae = $ppc->getAccessEntityObject();
		if (is_object($pae)) {
			$assignee = $pae->getAccessEntityLabel();
		}?>
		<?php echo t('Assigned to <strong>%s</strong>. ', $assignee)?>
		<?php echo $pd->getTextRepresentation()?>
		</div>
	</td>
	</tr>
<?php } ?>
</table>

<?php } else { ?>
	<p><?php echo t('No timed permission assignments')?></p>
<?php } ?>

</div>
