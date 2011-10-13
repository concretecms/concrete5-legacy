<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
	
	$akciph = Loader::helper('attribute_key_category_item_permissions');
?>


<?php echo $controller->token->output('update_permissions'); ?>

<?php 

if(!is_object($akci)){
	echo $akciph->getInheritanceForm(AttributeKeyCategoryItemPermission::get($akCategoryHandle), t('Inheriting the following permissions.'), !$akcp->canAdmin());
}else{
	echo $akciph->getForm(AttributeKeyCategoryItemPermission::get($akci, NULL, FALSE), t('Set permissions for this item.'));
} 
?>