<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<h6><?php echo t('Featured Add-On')?></h6>
<?php if (is_object($remoteItem)) { ?>
	<div class="clearfix">
	<img src="<?php echo $remoteItem->getRemoteIconURL()?>" width="97" height="97" style="float: left; margin-right: 10px; margin-bottom: 10px" />
	<h3><?php echo $remoteItem->getName()?></h3>
	<p><?php echo $remoteItem->getDescription()?></p>
	</div>
	
	<a href="javascript:void(0)" onclick="ccm_getMarketplaceItemDetails(<?php echo $remoteItem->getMarketplaceItemID()?>)" class="btn"><?php echo t('Learn More')?></a>
<?php } else {?>
	<p><?php echo t('Cannot retrieve data from the concrete5 marketplace.')?></p>
<?php } ?>