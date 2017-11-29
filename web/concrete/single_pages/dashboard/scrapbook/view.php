<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
$ih = Loader::helper('concrete/interface'); 
$ci = Loader::helper('concrete/urls');
$valt = Loader::helper('validation/token');
$u = new User();
?> 
<style type="text/css">
#ccm-scrapbook-list { margin-top:32px; margin-bottom:32px; } 
#ccm-scrapbook-list .ccm-block-type{border:none 0px}
#ccm-scrapbook-list .ccm-block-type .options { float:right; padding:8px }
#ccm-scrapbook-list .ccm-block-type-inner{ border:1px solid #e1e1e1; background-color:#f6f6f6; padding-left:8px; }
#ccm-scrapbook-list .ccm-block-type-inner .ccm-block-type-inner-icon {width:16px; height:16px; margin-right:8px; float:left; cursor:move}
#ccm-scrapbook-list .ccm-scrapbook-list-item-detail{margin:8px 0px}
#ccm-scrapbook-list .ccm-scrapbook-list-item{margin-bottom:16px; border:none;}

#ccm-scrapbook-list .ccm-block-type-inner .edit{ display:none }
#ccm-scrapbook-list .ccm-block-type-inner.editMode .view{ display:none }
#ccm-scrapbook-list .ccm-block-type-inner.editMode .edit{ display:block }
#ccm-scrapbook-list .ccm-block-type-inner a{ cursor:pointer }
#ccm-scrapbook-list .ccm-block-type-inner.editMode .view a{cursor:text}

#ccm-scrapbook-list .ccm-scrapbook-list-item .ccm-scrapbook-list-item-detail{ overflow:hidden } 

#availableScrapbooks { width:100%; margin-bottom:16px; }
#availableScrapbooks .options { text-align:left; white-space:nowrap; width:18% }

#addScrapbookForm #fieldsWrap{ display:none }
#addScrapbookForm #enableButton{ display:block }
#addScrapbookForm.editMode #fieldsWrap{ display:block }
#addScrapbookForm.editMode #enableButton{ display:none }
#addScrapbookForm.editMode #fieldsWrap input.faint{ color:#999 }

#ccm-scrapbook-list.user-scrapbook .ccm-scrapbook-list-item a.ccm-block-type-inner,
#ccm-scrapbook-list.user-scrapbook .ccm-scrapbook-list-item a.ccm-block-type-inner:hover{ border:1px solid #e1e1e1; background-color:#f6f6f6; margin-bottom:8px  }

.ccm-scrapbookNameWrap .view { display:block }
.ccm-scrapbookNameWrap .edit { display:none }
.ccm-scrapbookNameWrap.editMode .view { display:none }
.ccm-scrapbookNameWrap.editMode .edit { display:block }

div.ccm-scrapbook-item-handle:hover {cursor: move}

</style> 

<script type="text/javascript">
var GlobalScrapbook = { 
	init:function(){
		this.enableSorting();
	},  
	enableSorting:function(){ 
		$("div#ccm-scrapbook-list").sortable({
			handle: 'div.ccm-scrapbook-item-handle',
			cursor: 'move',
			opacity: 0.5,
			stop: function() {
				var idslist = $('#ccm-scrapbook-list').sortable('serialize'); 
				idslist=idslist+'&arHandle=<?php echo ($globalScrapbookArea) ? urlencode($globalScrapbookArea->getAreaHandle()) : '' ?>';
				$.post('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/scrapbook_services.php?mode=reorder', idslist, function(r) {
					
				});
			}
		});
	},
	addBlock:function(e){
		<?php if(!$globalScrapbookArea){ ?>
		return false;
		<?php }else{ ?>
		ccm_openAreaAddBlock("<?php echo urlencode($globalScrapbookArea->getAreaHandle()) ?>", true);
		<?php } ?>
	},
	editBlock:function(bID,w,h){ 
		if(!w) w=550;
		if(!h) h=380; 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.editBlock,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=<?php echo urlencode($scrapbookName)?>&btask=edit&isGlobal=1',
			appendButtons: true, 
			width: w,
			modal: false,
			height: h
		});		
	},
	editBlockTemplate:function(bID){ 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.changeBlockTemplate,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=<?php echo urlencode($scrapbookName)?>&btask=template',
			appendButtons: true, 
			width: 300,
			modal: false,
			height: 100
		});		
	},
	editBlockDesign:function(bID){ 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: '<?php echo t("Design")?>',
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=<?php echo urlencode($scrapbookName)?>&btask=block_css',
			appendButtons: true, 
			width: 450,
			modal: false,
			height: 420
		});		
	},
	editBlockPermissions:function(bID){ 
		var editBlockURL = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/edit_block_popup';
		$.fn.dialog.open({
			title: ccmi18n.changeBlockTemplate,
			href: editBlockURL+'?cID='+CCM_CID+'&bID='+bID+'&arHandle=<?php echo urlencode($scrapbookName)?>&btask=groups',
			appendButtons: true, 
			width: 400,
			modal: false,
			height: 380
		});		
	},

	confirmDelete:function(){
		if(!confirm("<?php echo t('Are you sure you want to delete this block?').'\n'.t('(All page instances will also be removed)') ?>")) return false;
		return true;
	},
	toggleRename:function(bID){
		$('#ccm-block-type-inner'+bID).toggleClass('editMode'); 
	},
	toggleScrapbookRename:function(arID){
		$('#ccm-scrapbookNameWrap'+arID).toggleClass('editMode'); 
	},	
	clrInitTxt:function(field,initText,removeClass,blurred){
		if(blurred && field.value==''){
			field.value=initText;
			$(field).addClass(removeClass);
			return;	
		}
		if(field.value==initText) field.value='';
		if($(field).hasClass(removeClass)) $(field).removeClass(removeClass);
	},
	toggleAddScrapbook:function(){
		$('#addScrapbookForm').toggleClass('editMode');
	}/*,
	submitAddScrapbookForm:function(){
		$('#addScrapbookForm').submit();
	}*/
}
$(function(){ GlobalScrapbook.init(); }); 
</script>



<?php
$scrapbookDeprecationNote = t('<strong>Note</strong>: Scrapbooks are preserved for backward compatibility, but you really should be using <a href="%s">stacks</a> instead.', View::url('/dashboard/blocks/stacks'));

if(!$scrapbookName){ ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Choose a Scrapbook'), $scrapbookDeprecationNote)?>
	<div class="block-message warning alert-message"><p><?php echo t('<strong>Note</strong>: Scrapbooks are preserved for backward compatibility, but you really should be using <a href="%s">stacks</a> instead.', View::url('/dashboard/blocks/stacks'))?></p></div>
		<table id="availableScrapbooks" border="0" cellspacing="1" class="grid-list table table-bordered" >
			<tr>
				<td class="header">
					<?php echo t('Scrapbook Name')?>
				</td>
				<td class="header">
					<?php echo t('Options')?>
				</td>
			</tr>		
			<?php if(is_array($availableScrapbooks)) 
				foreach($availableScrapbooks as $availableScrapbook){ ?>
			<tr>
				<td>		
					<div id="ccm-scrapbookNameWrap<?php echo $availableScrapbook['arID'] ?>" class="ccm-scrapbookNameWrap">
						<div class="view">
							<a href="<?php echo View::url($cPath,'view','?scrapbookName='.urlencode($availableScrapbook['arHandle']) ) ?>" >
								<?php echo $availableScrapbook['arHandle'] ?>
							</a>&nbsp;
						</div>
						<div class="edit">
							<form method="post" action="<?php echo $this->action('rename_scrapbook')?>">
								<?php $valt->output('rename_scrapbook')?>
								<input name="arID" type="hidden" value="<?php echo intval($availableScrapbook['arID']) ?>" /> 
								<input name="scrapbookName" type="text" value="<?php echo $availableScrapbook['arHandle'] ?>" />
								<input name="Submit" type="submit" value="<?php echo t('Save')?>" />
								<input onclick="GlobalScrapbook.toggleScrapbookRename(<?php echo intval($availableScrapbook['arID']) ?>)" name="cancel" type="button" value="<?php echo t('Cancel')?>" />
								&nbsp;
							</form>
						</div>
					</div>					
				</td>
				<td class="options">
					<a href="<?php echo View::url($cPath,'view','?scrapbookName='.urlencode($availableScrapbook['arHandle']) ) ?>"><?php echo t('View')?></a> &nbsp;|&nbsp; 
						<a onclick="GlobalScrapbook.toggleScrapbookRename(<?php echo intval($availableScrapbook['arID']) ?>); return false;" href="#"><?php echo t('Rename')?></a> &nbsp;|&nbsp; 
						<a onclick="if(!confirm('<?php echo t('Are you sure you want to permantly delete this scrapbook?')?>')) return false;" 
						   href="<?php echo $this->action('delete_scrapbook', urlencode($availableScrapbook['arHandle']), $valt->generate('delete_scrapbook') ) ?>"><?php echo t('Delete')?></a>
				</td>
			</tr> 
			<?php } 
			
			$form = Loader::helper('form'); ?>
			
			</table>
			
			<h3><?php echo t('Add a Global Scrapbook')?></h3>

			<form id="addScrapbookForm" method="post" action="<?php echo $this->action('addScrapbook') ?>">
			<?php $valt->output('add_scrapbook');?>
			<div class="clearfix">
			<?php echo $form->label('scrapbookName', t('Scrapbook Name'))?>
			<div class="input">
				<input name="scrapbookName" id="scrapbookName" class="ccm-input-text" type="text" value="" class="span6"  />
			<?php echo $ih->submit(t('Add'), 'addScrapbookForm', 'left')?>
			</div>
			</div>

			</form>
		
		
		<div class="ccm-spacer"></div>			
		
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>



<?php }else{ ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(htmlentities($scrapbookName, ENT_QUOTES, APP_CHARSET), $scrapbookDeprecationNote)?>
	
		<a style="float: right" href="<?php echo View::url($cPath) ?>"><?php echo t("&laquo; Return to Scrapbook List") ?></a>		
		
		<div class="sillyIE7"><?php echo $ih->button_js( t('Add Block to Scrapbook'), 'GlobalScrapbook.addBlock(event)','left'); ?></div>
		
		<div class="ccm-spacer"></div>	
		
		<div id="ccm-scrapbook-list" class="ui-sortable">			
			<?php 		 			
			if( !count($globalScrapbookBlocks) ){
				echo t('You have no items in this scrapbook.');
			}else foreach($globalScrapbookBlocks as $b) {
				 $b->setBlockAreaObject($globalScrapbookArea);
				 $bv = new BlockView();
				 $bt = BlockType::getByID( $b->getBlockTypeID() ); 
				 $bp = new Permissions($b);
				 $btIcon = $ci->getBlockTypeIconURL($bt); 			 
				 
				 //give this block a name if it doesn't have one
				 if( !strlen($b->getBlockName()) ){ 
					$b->updateBlockName( $scrapbookName.' '.intval($b->bID) );
				 }
				 ?>
				 <div class="ccm-scrapbook-list-item" id="ccm-scrapbook-list-item-<?php echo intval($b->bID)?>"> 
					 <div class="ccm-block-type">  
						<div class="options"> 
							<?php if ($bp->canWrite()) { ?>
							<a href="javascript:void(0)" onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)"><?php echo t('Rename')?></a>
							&nbsp;|&nbsp; 
							<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlockTemplate(<?php echo intval($b->bID) ?>)" ><?php echo t('Custom Template')?></a> 
							&nbsp;|&nbsp; 
							<?php if (ENABLE_CUSTOM_DESIGN == true) { ?>
							<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlockDesign(<?php echo intval($b->bID) ?>)" ><?php echo t('Design')?></a> 
							&nbsp;|&nbsp; 
							<?php } ?>
							<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlock(<?php echo intval($b->bID) ?>,<?php echo $bt->getBlockTypeInterfaceWidth()?> , <?php echo $bt->getBlockTypeInterfaceHeight()?> )" ><?php echo t('Edit')?></a> 
							&nbsp;|&nbsp; 
							
							<?php } ?>
							
							<?php if (PERMISSIONS_MODEL != 'simple' && $bp->canEditBlockPermissions()) { ?>
								<a href="javascript:void(0)" onclick="GlobalScrapbook.editBlockPermissions(<?php echo $b->getBlockID()?>)" ><?php echo t('Permissions')?></a> 
								<?php if ($bp->canDeleteBlock()) { ?>
									&nbsp;|&nbsp;
								<?php } ?>
							<?php } ?>
							
							<?php if ($bp->canDeleteBlock()) { ?>
							<a href="<?php echo $this->action('deleteBlock', Loader::helper('text')->entities($scrapbookName), 0, intval($b->bID), $valt->generate('delete_scrapbook_block'))?>" onclick="return GlobalScrapbook.confirmDelete()">
								<?php echo t('Delete')?>
							</a> 
							
							<?php } ?>
						</div>  
						<div id="ccm-block-type-inner<?php echo intval($b->bID)?>" class="ccm-block-type-inner">
							<div class="ccm-block-type-inner-icon ccm-scrapbook-item-handle" style="background: url(<?php echo $btIcon?>) no-repeat center left;">
							<img src="<?php echo ASSETS_URL_IMAGES?>/spacer.gif" width="16" height="16" />
							</div>
							<div class="view">
								<a onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)" >
									<?php echo t($bt->getBlockTypeName())?>: "<?php echo $b->getBlockName() ?>"
								</a>&nbsp;
							</div>
							<div class="edit">
								<form method="post" action="<?php echo $this->action('rename_block')?>">
									<?php $valt->output('rename_scrapbook_block')?>
									<input name="bID" type="hidden" value="<?php echo intval($b->bID) ?>" />
									<input name="scrapbookName" type="hidden" value="<?php echo $scrapbookName ?>" />
									<input name="bName" type="text" value="<?php echo $b->getBlockName() ?>" />
									<input name="Submit" type="submit" value="<?php echo t('Save')?>" />
									<input onclick="GlobalScrapbook.toggleRename(<?php echo intval($b->bID) ?>)" name="cancel" type="button" value="<?php echo t('Cancel')?>" />
									&nbsp;
								</form>
							</div>
						</div>
						<div class="ccm-scrapbook-list-item-detail">	
							<?php echo $bv->render($b, 'scrapbook'); ?>
						</div>
					</div>
				</div>
			<?php } ?>	
			
		</div> 	
		
	
	</div><?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php } ?>
