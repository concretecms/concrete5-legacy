<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

?>


<?php echo $controller->token->output('update_permissions');?>

<?php  print Loader::helper('attribute_key_category_item_permissions')->getInheritanceForm(AttributeKeyCategoryItemPermission::get($akCategoryHandle), t('Inheriting the following permissions.'), !$akcip->canAdmin()); ?>
