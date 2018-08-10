<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<ul class="ccm-pane-header-icons">
	<li><a href="javascript:void(0)" onclick="ccm_closeNewsflow(this)" class="ccm-icon-close"><?php echo t('Close')?></a></li>
</ul>

<?php
$_c = Page::getCurrentPage();
$valt = Loader::Helper('validation/token');
$token = '&' . $valt->getParameter();
if ($_c->getCollectionPath() != '/dashboard/news' && $_c->getCollectionPath() != '/dashboard/welcome' && !$_GET['_ccm_dashboard_external']) { ?>
<div class="well" style="margin-bottom: 0px">
	<?php if ($_c->isCheckedOut()) { ?>
	<a href="#" id="ccm-nav-save-arrange" class="btn ccm-main-nav-arrange-option" style="display: none"><?php echo t('Save Positioning')?></a>
	<a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $_c->getCollectionID()?>&approve=APPROVE&ctask=check-in&<?php echo Loader::helper('validation/token')->getParameter()?>" id="ccm-nav-exit-edit-direct" class="btn success ccm-main-nav-edit-option"><?php echo t('Save Changes')?></a>
	<?php } ?>
	<?php if (!$_c->isCheckedOut()) { ?><a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-out<?php echo $token?>" id="ccm-nav-check-out" class="btn"><?php echo t('Edit Page')?></a><?php } ?>
</div>
<?php } ?>

<?php

$u = new User();
$u->saveConfig('NEWSFLOW_LAST_VIEWED', time());
