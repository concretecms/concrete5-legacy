<?php defined('C5_EXECUTE') or die("Access Denied.");

if($permission) {
	

	if(!$task) { $task = $this->controller->getTask(); }
	
	
	if($task == 'edit') { ?>
	
	
		<h1><span><?php echo t('Edit Attribute')?></span></h1>
		<div class="ccm-dashboard-inner">
			<h2><?php echo t('Attribute Type')?></h2>
			<strong><?php echo $type->getAttributeTypeName()?></strong> <br/>
			<br/>
			<form method="post" action="<?php echo $this->action($akCategoryHandle.'/edit')?>" id="ccm-attribute-key-form">
				<table class="entry-form" cellspacing="1" cellpadding="0">
					<tr>
						<td class="subheader">Editable?*</td>
					</tr>
					<tr>
						<td>
							<select id="akIsEditable" class="ccm-input-select" name="akIsEditable">
								<option <?php if($key->isAttributeKeyEditable()) {?>selected="selected" <?php }?>value="1">Yes</option>
								<option <?php if(!$key->isAttributeKeyEditable()) {?>selected="selected" <?php }?>value="0">No</option>
							</select>
						</td>
					</tr>
				</table>
				<?php  Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>
			</form>
		</div>
		<h1><span><?php echo t('Delete Attribute')?></span></h1>
		<div class="ccm-dashboard-inner">
			<div class="ccm-spacer"></div>
			<?php 
			$delConfirmJS = t('Are you sure you want to remove this attribute?');
			?>
			<script type="text/javascript">
			deleteAttribute = function() {
				if (confirm('<?php echo $delConfirmJS?>')) { 
					location.href = "<?php echo $this->url('/dashboard/bricks/structure', $akCategoryHandle, 'delete', $key->getAttributeKeyID(), $valt->generate('delete_attribute'))?>";				
				}
			}
			</script>
			<?php  print $ih->button_js(t('Delete Attribute'), "deleteAttribute()", 'left');?>
			<div class="ccm-spacer"></div>
		</div>
		
		
	<?php } elseif($task == 'sets') { ?>
	
	
		<table width="100%">
			<tr>
				<td width="50%" valign="top"><h1><span><?php echo t('Manage '.$txt->unhandle($akCategoryHandle).' Attribute Sets')?></span></h1>
					<div class="ccm-dashboard-inner">
						<table width="100%">
						<?php $deleteConfirmJS	= t('Are you sure you want to delete this attribute set?'); ?>
						<?php foreach($sets as $set) { ?>
							<tr>
								<td style="font-size: 16px; font-weight:bolder"><?php echo $set->asName; ?></td>
								<td>
									<script type="text/javascript">
										delete_<?php echo $set->asHandle; ?> = function() {
											if (confirm("<?php echo $deleteConfirmJS;?>")) { 
												location.href = "<?php echo $this->action($akCategoryHandle, 'delete_set', $set->asID, $valt->generate('delete_set'));?>";				
											}
										}
									</script>
									<?php  print $ih->button_js(t('Delete'), 'delete_'.$set->asHandle.'()');?>
								</td>
							</tr>
						<?php } ?>
					</table>
					</div></td>
				<td width="50%" valign="top"><h1><span><?php echo t('Add Attribute Set')?></span></h1>
					<div class="ccm-dashboard-inner">
						<form method="post" action="<?php echo $this->url($this->getCollectionObject()->getCollectionPath(), $akCategoryHandle, 'sets')?>" id="ccm-attribute-set-form">
							<input type="hidden" name="tableHandle" value="<?php echo $akCategoryHandle;?>" />
							<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
								<tr>
									<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
									<td class="subheader"><?php echo t('Name')?> <span class="required">*</span></td>
								</tr>
								<tr>
									<td style="padding-right: 15px"><?php echo $form->text('setHandle', $akHandle, array('style' => 'width: 100%'))?></td>
									<td style="padding-right: 15px"><?php echo $form->text('setName', $akName, array('style' => 'width: 100%'))?></td>
								</tr>
							</table>
							<?php echo $ih->submit(t('Add Set'), 'ccm-attribute-set-form')?>
						</form>
						<div class="ccm-spacer">&nbsp;</div>
					</div>
					<h1><span><?php echo t('Disable Attribute Sets')?></span></h1>
					<div class="ccm-dashboard-inner"> 
						<script type="text/javascript">
							disableSets = function() {
								if (confirm("Attribute Sets are currently enabled for this Attribute Key Category. Are you sure you want to disable them now?")) { 
									location.href = "<?php echo $this->url('/dashboard/bricks/structure/', $akCategoryHandle, 'sets', 'NULL', $valt->generate('disable_sets')); ?>";				
								}
							}
							</script> 
						<?php print $ih->button_js(t('Disable'), 'disableSets()', 'left'); ?> </div></td>
			</tr>
		</table>
		
		
	<?php } elseif($task == 'select_type') { ?>
	
	
		<h1>
			<?php if($category->allowAttributeSets() == AttributeKeyCategory::ASET_ALLOW_SINGLE) {?>
			<a class="ccm-dashboard-header-option" href="<?php echo $this->url('/dashboard/bricks/structure/sets/'.$akCategoryHandle)?>">Manage Attribute Sets</a>
			<?php } ?>
			<span><?php echo t('Add New Attribute to '.$txt->unhandle($akCategoryHandle))?></span></h1>
		<div class="ccm-dashboard-inner">
			<h2><?php echo t('Choose Attribute Type')?></h2>
			<form method="post" action="<?php echo $this->action($akCategoryHandle.'/select_type')?>" id="ccm-attribute-type-form">
				<?php echo $form->select('atID', $attributeTypes)?>
				<?php echo $form->submit('submit', t('Go'))?>
			</form>
			<?php  if (isset($type)) { ?>
				<br/>
				<form method="post" action="<?php echo $this->action($akCategoryHandle, 'add')?>" id="ccm-attribute-key-form">
					<table class="entry-form" cellspacing="1" cellpadding="0">
						<tr>
							<td class="subheader">Editable?*</td>
						</tr>
						<tr>
							<td>
								<select id="akIsEditable" class="ccm-input-select" name="akIsEditable">
									<option selected="selected" value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</td>
						</tr>
					</table>
					<?php  Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
				</form>	
			<?php  } ?>
		</div>
		
		
	<?php } elseif($akCategoryHandle) {
		
		
		$types = AttributeType::getList();
		$cellWidth = 100/(count($types)+1);
		$cat = AttributeKeyCategory::getByHandle($akCategoryHandle); ?>
		<table width="100%">
			<tr>
				<td width="50%" valign="top"><h1><span><?php echo t($txt->unhandle($akCategoryHandle).' Attributes')?></span></h1>
					<div class="ccm-dashboard-inner">
						<?php echo Loader::element(
							'dashboard/attributes_table', 
							array(
								'category'	=> $category, 
								'attribs'	=> $attribs, 
								'currentURL'	=> View::url('/dashboard/bricks/structure/'.$akCategoryHandle), 
								'editURL'	=> '/dashboard/bricks/structure/'.$akCategoryHandle
							)
						); 
					?> <br/>
						<div class="ccm-spacer">&nbsp;</div>
						<?php if($category->allowAttributeSets()) {
							print $ih->button(t('Edit Sets'), $this->url('/dashboard/bricks/structure', $akCategoryHandle, 'sets'));
						} else { ?>
					<script type="text/javascript">
						enableSets<?php echo ucwords($txt->camelcase($akCategoryHandle)); ?> = function() {
							if (confirm("Attribute Sets are not enabled for this Attribute Key Category. Would you like to enable them now?")) {
								location.href = "<?php echo $this->url('/dashboard/bricks/structure', $akCategoryHandle, 'sets', 'NULL', $valt->generate('allow_sets')); ?>";				
							}
						}
					</script> 
					<?php print $ih->button_js(t('Enable Sets'), 'enableSets'.ucwords($txt->camelcase($akCategoryHandle)).'()'); 
						} ?>
						<div class="ccm-spacer">&nbsp;</div>
					</div>
				</td>
				<td width="50%" valign="top"><h1><span><?php echo t('Add New Attribute')?></span></h1>
					<div class="ccm-dashboard-inner">
						<h2><?php echo t('Choose Attribute Type')?></h2>
						<form method="post" action="<?php echo $this->action($akCategoryHandle, 'select_type'); ?>" id="ccm-attribute-type-form">
							<?php echo $form->select('atID', $attributeTypes)?> <?php echo $form->submit('submit', t('Go'))?>
						</form>
						<?php  if (isset($type)) { ?>
							<br/>
							<form method="post" action="<?php echo $this->action('add')?>" id="ccm-attribute-key-form">
								<?php  Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
							</form>
						<?php  } ?>
					</div></td>
			</tr>
		</table>
		<form method="post" id="attribute_type_associations_form" action="<?php echo $this->action('save_attribute_type_associations/'.$akCategoryHandle)?>">
			<h1><span><?php echo t($txt->unhandle($akCategoryHandle).' Attribute Type Associations')?></span></h1>
			<div class="ccm-dashboard-inner">
				<table border="0" cellspacing="1" cellpadding="0" border="0" class="grid-list">
				<tr>
					<?php foreach($types as $at) { ?>
					<td class="header" align="center" width="<?php echo $cellWidth;?>%"><strong><?php echo $at->getAttributeTypeName()?></strong></td>
					<?php  } ?>
					<td class="header"></td>
				</tr>
				<tr>
					<?php foreach($types as $at) { ?>
					<td align="center"><?php echo $form->checkbox($category->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($category))?></td>
					<?php  } ?>
					<td><?php 
								$b1 = $ih->submit(t('Save'), 'attribute_type_associations_form');
								print $ih->buttons($b1);
								?></td>
				</tr>
				</table>
			</div>
		</form>
		
	<?php } else { ?>
	
		<?php $categories = AttributeKeyCategory::getList(); ?>
		<form method="post" id="attribute_type_associations_form" action="<?php echo $this->action('save_attribute_type_associations', 'GLOBAL')?>">
			<h1><span><?php echo t('Attribute Type Associations')?></span></h1>
			<div class="ccm-dashboard-inner">
				<table border="0" cellspacing="1" cellpadding="0" border="0" class="grid-list">
				<tr>
					<td class="header"></td>
					<?php foreach($types as $at) { ?>
					<td class="header" align="center" width="<?php echo $cellWidth;?>%"><strong><?php echo $at->getAttributeTypeName()?></strong></td>
					<?php  } ?>
				</tr>
				<?php foreach($categories as $cat) { ?>
				<tr>
					<td><a href="<?php echo $this->url($this->getCollectionObject()->getCollectionPath(), $cat->getAttributeKeyCategoryHandle()); ?>"><strong><?php echo $txt->unhandle($cat->getAttributeKeyCategoryHandle())?></strong></a></td>
					<?php foreach($types as $at) { ?>
					<td align="center"><?php echo $form->checkbox($cat->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($cat))?></td>
					<?php  } ?>
				</tr>
				<?php  } ?>
				</table>
				<br/>
				<?php print $ih->submit(t('Save'), 'attribute_type_associations_form'); ?>
				<br/>
				<br/>
				<br/>
			</div>
		</form>
		
		
	<?php } ?>
	
	
<?php } else { ?>


	<?php if($akCategoryHandle) { ?>

			
		<h1><span><?php echo t($txt->unhandle($akCategoryHandle).' Attribute Key Category Attributes')?></span></h1>
		<div class="ccm-dashboard-inner">
		<?php echo t('You are not allowed to change these permissions.')?>
		</div>
		
		
	<?php  } else { ?>
	
	
		<h1><span><?php echo t('Global Attribute Key Category Attributes')?></span></h1>
		<div class="ccm-dashboard-inner">
		<?php echo t('You are not allowed to change these permissions.')?>
		</div>
		
		
	<?php  } ?>



<?php } ?>
