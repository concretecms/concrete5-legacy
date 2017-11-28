<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<?php 
$ci = Loader::helper('concrete/urls'); 
$st = Stack::getByID($_REQUEST['stackID']);
$blocks = $st->getBlocks(STACKS_AREA_NAME);
if (count($blocks) == 0) { ?>
	<p><?php echo t('There are no blocks in this stack.')?></p>
	<div id="ccm-tab-content-add-stack">
        <h3><?php echo t('Add Stack')?></h3>
        <p><?php echo t('Add the entire stack to this page.')?></p>
        <p><a class="btn primary" href="javascript:void(0)" onclick="ccmStackAddToArea(<?php echo $st->getCollectionID()?>, '<?php echo Loader::helper('text')->entities($a->getAreaHandle())?>')"><?php echo t("Add Stack")?></a></p>
    </div>

<?php } else { ?>
	
	<?php echo Loader::helper('concrete/interface')->tabs(array(
		array('add-stack', t('Full Stack'), true),
		array('add-stack-block', t('Individual Block'))
	));
	?>
		
	<div id="ccm-tab-content-add-stack">
        <h3><?php echo t('Add Stack')?></h3>
        <p><?php echo t('Add the entire stack to this page.')?></p>
        <p><a class="btn primary" href="javascript:void(0)" onclick="ccmStackAddToArea(<?php echo $st->getCollectionID()?>, '<?php echo Loader::helper('text')->entities($a->getAreaHandle())?>')"><?php echo t("Add Stack")?></a></p>
    </div>

	<div id="ccm-tab-content-add-stack-block" style="display: none">
	
	<?php foreach($blocks as $b) { 
		$bt = $b->getBlockTypeObject();
		$btIcon = $ci->getBlockTypeIconURL($bt);
		$name = t($bt->getBlockTypeName());
		if ($b->getBlockName() != '') {
			$name = $b->getBlockName();
		}
		?>			
		<div class="ccm-scrapbook-list-item" id="ccm-stack-block-<?php echo $b->getBlockID()?>">
			<div class="ccm-block-type">
				<a class="ccm-block-type-inner" style="background-image: url(<?php echo $btIcon?>)" href="javascript:void(0)" onclick="var me=this; if(me.disabled)return; me.disabled=true; jQuery.fn.dialog.showLoader();$.get('<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?bID=<?php echo $b->getBlockID()?>&add=1&processBlock=1&cID=<?php echo $c->getCollectionID()?>&arHandle=<?php echo $a->getAreaHandle()?>&btask=alias_existing_block&<?php echo $token?>', function(r) { me.disabled=false; ccm_parseBlockResponse(r, false, 'add'); })"><?php echo $name?></a>
				<div class="ccm-scrapbook-list-item-detail">	
					<?php	
					try {
						$bv = new BlockView();
						$bv->render($b, 'scrapbook');
					} catch(Exception $e) {
						print t('This block is no longer available.');
					}	
					?>
				</div>
			</div>
		</div>	
		<?php
		}
	} ?>
	</div>
	
</div>
