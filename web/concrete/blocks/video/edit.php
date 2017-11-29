<?php
	defined('C5_EXECUTE') or die("Access Denied.");
	$bObj=$controller;
	$al = Loader::helper('concrete/asset_library');
	$bf = null;
	if ($controller->getFileID() > 0) { 
		$bf = $controller->getFileObject();
	}
?>
<div class="clearfix">
<label><?php echo t('Video File')?></label>
<div class="input">
	<?php echo $al->video('ccm-b-flv-file', 'fID', t('Choose Video File'), $bf);?>
</div>
</div>

<?php $this->inc('form_setup_html.php'); ?> 