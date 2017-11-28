<?php 
Loader::model('pile');
$ci = Loader::helper('concrete/urls');
$ap = new Permissions($a);
?>
	<div id="ccm-scrapbook-list">
	<?php

	$sp = Pile::getDefault();
	$contents = $sp->getPileContentObjects('date_desc');
	if (count($contents) == 0) { 
		print t('You have no items in your Clipboard.');
	}
	foreach($contents as $obj) { 
		$item = $obj->getObject();
		if (is_object($item)) {
			$bt = $item->getBlockTypeObject();
			$btIcon = $ci->getBlockTypeIconURL($bt);
			if (!$ap->canAddBlockToArea($bt)) {
				continue;
			}
			?>			
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?php echo $obj->getPileContentID()?>">
				<div class="ccm-block-type">
					<a class="ccm-scrapbook-delete" title="Remove from Clipboard" href="javascript:void(0)" id="sb<?php echo $obj->getPileContentID()?>"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a>
					<a class="ccm-block-type-inner" style="background-image: url(<?php echo $btIcon?>)" href="javascript:void(0)" onclick="var me=this; if(me.disabled)return; me.disabled=true; jQuery.fn.dialog.showLoader();$.get('<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?pcID[]=<?php echo $obj->getPileContentID()?>&add=1&processBlock=1&cID=<?php echo $c->getCollectionID()?>&arHandle=<?php echo $a->getAreaHandle()?>&btask=alias_existing_block&<?php echo $token?>', function(r) { me.disabled=false; ccm_parseBlockResponse(r, false, 'add'); })"><?php echo $bt->getBlockTypeName()?></a>
					<div class="ccm-scrapbook-list-item-detail">	
						<?php	
						try {
							$bv = new BlockView();
							$bv->render($item, 'scrapbook');
						} catch(Exception $e) {
							print t('This block is no longer available.');
						}	
						?>
					</div>
				</div>
			</div>	
			<?php
			$i++;
		} else { ?>
		
		
			<div class="ccm-scrapbook-list-item" id="ccm-pc-<?php echo $obj->getPileContentID()?>">
				<div class="ccm-block-type">
					<a class="ccm-scrapbook-delete" title="<?php echo t('Remove from Clipboard')?>" href="javascript:void(0)" id="sb<?php echo $obj->getPileContentID()?>"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" width="16" height="16" /></a>
					<div class="ccm-scrapbook-list-item-detail">	
						<?php	
						print t('This block is no longer available.');
						?>
					</div>
				</div>
			</div>	

		
		<?php } 
	}	?> 
	</div>
