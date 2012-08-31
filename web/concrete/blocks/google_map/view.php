<?php defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if ($c->isEditMode()) { ?>
	<div class="ccm-edit-mode-disabled-item" style="height: 400px">
		<div style="padding: 80px 0px 0px 0px"><?=t('Google Map disabled in edit mode.')?></div>
	</div>
<?php } else { ?>	
	<?php if( strlen($title)>0){ ?><h3><?=$title?></h3><?php } ?>
	<div id="googleMapCanvas<?=$bID?>" class="googleMapCanvas"></div>	
<?php } ?>