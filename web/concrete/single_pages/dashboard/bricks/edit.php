<?php defined('C5_EXECUTE') or die("Access Denied.");

if($permission) {	 
	$df = Loader::helper('form/date_time');
	?>
	<form method="post" action="<?php echo $this->action($akCategoryHandle, $akci->ID); ?>" id="new-object-form">
	<table width="100%">
		<tr>
			<td valign="top">
				<h1><a class="ccm-dashboard-header-option" style="right:130px;" href="<?php echo $this->url('/dashboard/bricks/structure/');?>">Global Attributes</a><a class="ccm-dashboard-header-option" href="<?php echo $this->url('/dashboard/bricks/structure/'.$akCategoryHandle)?>">Category Attributes</a><span><?php echo 'Editing '.$txt->unhandle($akCategoryHandle).t(' Item')?></span></h1>
				<div class="ccm-dashboard-inner">
						<?php echo $form->hidden('akciID', $akci->ID); 
				if (count($attribs) > 0) { 
					if (count($sets) > 0) { 
						foreach($sets as $as) { ?>
						<fieldset style="border-color:#CCCCCC"><legend style="font-size:16px; font-weight:bold; color:#457DA5;"><?php echo $as->getAttributeSetName()?></legend>
						<?php 	
							$setattribs = $as->getAttributeKeys();
							if (count($setattribs) == 0) {
								echo t('No attributes defined.')?>
						</fieldset>
						<?php  } else { ?>
							<?php 
								foreach($setattribs as $ak) {
									if($ak->isAttributeKeyEditable()) {?>
							<div style="margin-bottom:20px;">
								<h3><?php echo $ak->render('label');?></h3>
								<?php
										if(is_object($akci)) {
											$aValue = $akci->getAttributeValueObject($ak);
										}
										echo $ak->render('form', $aValue);
									}?>
							</div>
							<?php  } ?>
						</fieldset>
						<?php  } ?>
						<?php  } 
						$unsetattribs = $category->getUnassignedAttributeKeys();
						if (count($unsetattribs) > 0) { ?>
						<fieldset style="border-color:#CCCCCC"><legend style="font-size:16px; font-weight:bold; color:#457DA5;"><?php echo t('Other')?></legend>
						<?php 
							foreach($unsetattribs as $ak) {
								if($ak->isAttributeKeyEditable()) {?>
							<div style="margin-bottom:20px;">
								<h3><?php echo $ak->render('label');?></h3>
								<?php
										if(is_object($akci)) {
											$aValue = $akci->getAttributeValueObject($ak);
										}
										echo $ak->render('form', $aValue);
									}?>
							</div>
						<?php  } ?>
						</fieldset>
						<?php }
					} else { ?>
						<div class="ccm-attributes-list">
							<?php 
						foreach($attribs as $ak) { 
							if($ak->isAttributeKeyEditable()) {?>
							<div style="margin-bottom:20px;">
								<h3><?php echo $ak->render('label');?></h3>
								<?php
										if(is_object($akci)) {
											$aValue = $akci->getAttributeValueObject($ak);
										}
										echo $ak->render('form', $aValue);
									}?>
							</div>
							<?php  } ?>
						</div>
						<?php  } ?>
						<?php  } else { ?>
						<br/>
						<strong><?php echo t('No attributes defined.'); ?></strong> <br/>
						<br/>
						<?php  } ?>
				</div>
			</td>
			<td valign="top" width="34%">
				<h1><span><?php echo t('Owner')?></span></h1>
				<div class="ccm-dashboard-inner">
				<?php 
					$uh = Loader::helper('form/user_selector');
					global $u;
					print $uh->selectUser('uID', $u->uID); 
				?>
				</div>
				<h1><span><?php echo t('Permissions')?></span></h1>
				<div class="ccm-dashboard-inner">
					<?php echo $this->controller->token->output('update_permissions');?>
			
					<?php  print Loader::helper('attribute_key_category_item_permissions')->getForm(AttributeKeyCategoryItemPermission::get($akci, NULL, FALSE), t('Set permissions for this item.'));?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h1><span><?php echo t('Action')?></span></h1>
				<div class="ccm-dashboard-inner">
					<?php
						print $ih->button(t('Cancel'), $this->url('/dashboard/bricks/search/'.$akCategoryHandle), 'left');
						print $ih->button(t('Delete'), $this->url('/dashboard/bricks/edit', 'delete', $akCategoryHandle, $akci->ID, $delete_token));
						print $ih->submit('Save', 'new-object-form');
					?>
					<div class="ccm-spacer">&nbsp;</div>
				</div>
			</td>
		</tr>
	</table>
	</form>
	
	
<?php } else { ?>

		
	<h1><span><?php echo t($txt->unhandle($akCategoryHandle).' Edit')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?php echo t('You are not allowed to edit items in this category.')?>
	</div>


<?php } ?>
