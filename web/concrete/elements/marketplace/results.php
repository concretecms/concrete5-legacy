<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<table class="ccm-marketplace-results">
	<tr>
	<?php 
	$numCols=3;
	$colCount=0;
	$i = 0;
	foreach($items as $item){ 
		if($colCount==$numCols){
			echo '</tr><tr>';
			$colCount=0;
		}
		?>
		<td valign="top" width="33%" mpID="<?php echo $item->getMarketplaceItemID()?>" class="ccm-marketplace-item ccm-marketplace-item-unselected"> 
		
		<img class="ccm-marketplace-item-thumbnail" width="44" height="44" src="<?php echo $item->getRemoteIconURL() ?>" />
		<div class="ccm-marketplace-results-info">
			<h4><?php echo $item->getName()?></h4>
			<h5><?php echo ((float) $item->getPrice() == 0) ? t('Free') : $item->getPrice()?></h5>
			<p><?php echo $item->getDescription() ?></p>
			<?php $thumb = $item->getLargeThumbnail(); ?>
			<?php if ($thumb && $type == 'themes') { ?>
				
				<div class="ccm-marketplace-results-image-hover ccm-ui"><ul class="media-grid"><li><a href="javascript:void(0)"><img src="<?php echo $thumb->src?>" width="<?php echo $thumb->width?>" height="<?php echo $thumb->height?>" /></a></li></ul></div>
				
			
			<?php
			} ?>
		</div>
			
		</td>
	<?php   $colCount++;
	$i++;
	}
	for($i=$colCount;$i<$numCols;$i++){
		echo '<td>&nbsp;</td>'; 
	} 
	?>
	</tr>
</table>
