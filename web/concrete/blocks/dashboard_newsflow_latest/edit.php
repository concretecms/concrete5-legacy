<?php 
defined('C5_EXECUTE') or die("Access Denied.");
?>
<h2><?php echo t('Slot')?></h2>
<div>
<?php echo Loader::helper('form')->select('slot', array('A' => 'A', 'B' => 'B', 'C' => 'C'), $slot)?>
</div>