<?  defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('view');
?>

<div id="ccm-popup-alert" class="ccm-ui">
	<div id="ccm-popup-alert-message" class="alert-message block-message error"></div>
</div>
<? $ih = Loader::helper('concrete/interface')?>
<div class="dialog-buttons">
	<?=$ih->button_js(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
</div>