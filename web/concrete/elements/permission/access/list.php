<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php foreach($accessTypes as $accessType => $title) { 
	$list = $permissionAccess->getAccessListItems($accessType); 
	?>
	<h3><?php echo $title?></h3>

<table class="ccm-permission-access-list table table-bordered">
<tr>
	<th colspan="3">
		<div style="position: relative">
		<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?accessType=<?php echo $accessType?>&pkCategoryHandle=<?php echo $pkCategoryHandle?>" dialog-width="500" dialog-height="500" dialog-title="<?php echo t('Add Access Entity')?>" class="ccm-advanced-search-add-field dialog-launch"><span class="ccm-menu-icon ccm-icon-view"></span><?php echo t('Add')?></a>
		

	<?php echo t('Access')?>
	</div>
	</th>
</tr>
<?php if (count($list) > 0) { ?>

<?php foreach($list as $pa) {
	$pae = $pa->getAccessEntityObject(); 
	$pdID = 0;
	if (is_object($pa->getPermissionDurationObject())) { 
		$pdID = $pa->getPermissionDurationObject()->getPermissionDurationID();
	}
	
	?>
<tr>
	<td width="100%"><?php echo $pae->getAccessEntityLabel()?></td>
	<td><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?peID=<?php echo $pae->getAccessEntityID()?>&pdID=<?php echo $pdID?>&accessType=<?php echo $accessType?>" dialog-width="500" dialog-height="500" dialog-title="<?php echo t('Add Access Entity')?>" class="dialog-launch"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/clock<?php if (is_object($pa->getPermissionDurationObject())) { ?>_active<?php } ?>.png" width="16" height="16" /></a></td>
	<td><a href="javascript:void(0)" onclick="ccm_deleteAccessEntityAssignment(<?php echo $pae->getAccessEntityID()?>)"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a></td>
</tr>

<?php } ?>

<?php } else { ?>
	<tr>
	<td colspan="3"><?php echo t('None')?></td>
	</tr>
<?php } ?>

</table>


<?php } ?>
