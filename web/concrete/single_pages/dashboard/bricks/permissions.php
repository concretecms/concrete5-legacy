<?php defined('C5_EXECUTE') or die(_("Access Denied."));
if($permission) {
	if($akCategoryHandle) { ?>	


		<h1><span><?php echo t('\''.$txt->unhandle($akCategoryHandle).'\' Permissions')?></span></h1>
		<div class="ccm-dashboard-inner">
			<form method="post" id="ccm-task-permissions" action="<?php echo $this->action($akCategoryHandle, 'save_permissions')?>">
				<?php echo $this->controller->token->output('update_permissions');?>
		
				<?php  print Loader::helper('attribute_key_category_item_permissions')->getForm($akcip, t('Set default permissions for the \''.$txt->unhandle($akCategoryHandle).'\' category.')); ?>
				
				<div class="ccm-spacer">&nbsp;</div>
				
				
				<?php  print $ih->submit(t('Save'), 'ccm-task-permissions'); ?>
		
				<div class="ccm-spacer">&nbsp;</div>
			</form>
		</div>
		<h1><span><?php echo t('\''.$txt->unhandle($akCategoryHandle).'\' API Settings')?></span></h1>
		<div class="ccm-dashboard-inner"> This page will address how to access data externally via a REST API to accomidate <small style="color:#090">GET</small>ting XML, JSON, BSON or similar feeds. As well as generating and requiring an optional API key and other security measures. </div>


	<?php } else { ?>


		<h1><span><?php echo t('Global Virtual Table Permissions')?></span></h1>
		<div class="ccm-dashboard-inner">
			<form method="post" id="ccm-task-permissions" action="<?php echo $this->action('global_permissions_saved')?>">
				<?php echo $this->controller->token->output('update_permissions');?>
		
				<?php  print Loader::helper('attribute_key_category_item_permissions')->getInheritanceForm($akcip, t('Set default permissions for all custom Attribute Key Categories.')); ?>
				
				<div class="ccm-spacer">&nbsp;</div>
				
				
				<?php  print $ih->submit(t('Save'), 'ccm-task-permissions'); ?>
		
				<div class="ccm-spacer">&nbsp;</div>
			</form>
		</div>
		<h1><span><?php echo t('Global API Settings')?></span></h1>
		<div class="ccm-dashboard-inner"> This page will address how to access data externally via a REST API to accomidate <small style="color:#090">GET</small>ting XML, JSON, BSON or similar feeds. As well as generating and requiring an optional API key and other security measures. </div>


	<?php  } ?>
	
<?php } else {
	if($akCategoryHandle) { ?>
		<h1><span><?php echo t('\''.$txt->unhandle($akCategoryHandle).'\' Permissions')?></span></h1>
		<div class="ccm-dashboard-inner">
		<?php echo t('You are not allowed to change this Attribute Key Category\'s permissions.')?>
		</div>
	<?php  } else { ?>
		<h1><span><?php echo t('Global Virtual Table Permissions')?></span></h1>
		<div class="ccm-dashboard-inner">
		<?php echo t('You are not allowed to change the global permissions.')?>
		</div>
	<?php  }
} ?>
