<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$bObj=$controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<div class="clearfix">
<label><?php echo t('Video File')?></label>
<div class="input">
	<?php echo $al->video('ccm-b-flv-file', 'fID', t('Choose Video File') );?>
</div>
</div>

<?php $this->inc('form_setup_html.php'); ?> 
