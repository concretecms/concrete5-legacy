<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Manager'), array(t('Add, search, replace and modify the files for your website.'), 'http://www.concrete5.org/documentation/editors-guide/dashboard/file-manager/'), false, false);?>

<?php 
$c = Page::getCurrentPage();
$ocID = $c->getCollectionID();
$fp = FilePermissions::getGlobal();
if ($fp->canAddFile() || $fp->canSearchFiles()) { ?>
<div class="ccm-pane-options" id="ccm-<?php echo $searchInstance?>-pane-options">

<div class="ccm-file-manager-search-form"><?php Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?></div>

</div>

<?php Loader::element('files/search_results', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'columns' => $columns, 'searchType' => 'DASHBOARD', 'files' => $files, 'fileList' => $fileList)); ?>

<?php } else { ?>
<div class="ccm-pane-body">
	<p><?php echo t("You do not have access to the file manager.");?></p>
</div>	
<div class="ccm-pane-footer"></div>

<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
