<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Waiting for Me'), false)?>

<?php echo Loader::helper('concrete/interface')->tabs($tabs, false); ?>
	
<?php if ($category->getPackageID() > 0) { ?>
	<?php Loader::packageElement('workflow/progress/categories/' . $category->getWorkflowProgressCategoryHandle() . '/pending', $category->getPackageHandle(), array('category' => $category))?>
<?php } else { ?>
	<?php Loader::element('workflow/progress/categories/' . $category->getWorkflowProgressCategoryHandle() . '/pending', array('category' => $category)); ?>
<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
