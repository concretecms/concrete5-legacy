<?php defined('C5_EXECUTE') or die("Access Denied.");
if($akcip->canWrite()) {	 
	$df = Loader::helper('form/date_time');
	?>
	<?php
		
		$view = View::getInstance();
		$controller = $this->controller;
		
		
		$vars = array_merge($this->controller->getSets(), $this->controller->getHelperObjects(), array(
			'view'=>$view,
			'controller'=>$controller
		));	
		
		
		//Somehow, using the following as the form action is conflicting with the akID[xy][akCategoryHandle] field of the brick type attributes:
		// echo $this->action($akCategoryHandle, $akci->ID);
		//We don't really need it to post back to the same page, so I'm dropping it for now. (Stephen Rushing 2011-10-09)
	?>
	<form method="post" action="" id="new-object-form">
	<table width="100%">
		<tr>
			<td valign="top">
				<h1><span><?php echo t('Editing %s Item #%s', $text->unhandle($akCategoryHandle), $akci->ID) ?></span></h1>
				<div class="ccm-dashboard-inner">
						<?php echo $form->hidden('akCategoryHandle', $akCategoryHandle); ?>
						<?php echo $form->hidden('akciID', $akci->ID); ?>
						
						<?php Loader::element('bricks/edit/attributes', $vars); ?>
						 
				
				</div>
			</td>
			<td valign="top" width="34%">
				<h1><span><?php echo t('Owner')?></span></h1>
				<div class="ccm-dashboard-inner">
                	<?php Loader::element('bricks/edit/owner', $vars); ?>
                </div>
                <h1><span><?php echo t('Permissions')?></span></h1>
                <div class="ccm-dashboard-inner">
                	<?php Loader::element('bricks/edit/permissions', $vars); ?>
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
						print $ih->submit('Save & Finish', 'save-finish');
						print $ih->submit('Save', 'save');
					?>
					<div class="ccm-spacer">&nbsp;</div>
				</div>
			</td>
		</tr>
	</table>
	</form>
	
	
<?php } else { ?>

		
	<h1><span><?php echo t($text->unhandle($akCategoryHandle).' Edit')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?php echo t('You are not allowed to edit items in this category.')?>
	</div>


<?php } ?>
