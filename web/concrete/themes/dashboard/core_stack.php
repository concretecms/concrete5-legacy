<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($c->getCollectionName())?>

<?php 

$a = new Area(STACKS_AREA_NAME);
$a->display($c); 
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<?php $this->inc('elements/footer.php'); ?>