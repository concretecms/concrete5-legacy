<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<?php 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

if ($c->isAlias() && $c->getCollectionPointerExternalLink() != '') {
?>

	<form class="form-stacked" method="post" action="<?php echo $c->getCollectionAction()?>" id="ccmEditLink">		
	
	<div class="ccm-form-area">
	<div class="ccm-field">
	
	<label><?php echo t('Name')?></label> <input type="text" name="cName" value="<?php echo $c->getCollectionName()?>" class="text" style="width: 95%" />
	
	</div>
	<div class="ccm-field">

	<label><?php echo t('URL')?></label> <input type="text" name="cExternalLink" style="width: 95%" value="<?php echo $c->getCollectionPointerExternalLink()?>" />

	</div>

	<div class="ccm-field">

	<label for="cExternalLinkNewWindow"><input type="checkbox" value="1" <?php if ($c->openCollectionPointerExternalLinkInNewWindow()) { ?> checked <?php } ?> name="cExternalLinkNewWindow" id="cExternalLinkNewWindow" style="vertical-align: middle" /> <?php echo t('Open Link in New Window')?></label>

	</div>
	
	<div class="ccm-spacer">&nbsp;</div>
	</div>


	<div class="ccm-buttons dialog-buttons">
	<input type="button" class="btn" value="<?php echo t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" />
	<a href="javascript:void(0)" onclick="$('#ccmEditLink').get(0).submit()" class="btn primary ccm-button-right accept"><span><?php echo ('Save')?></span></a>
	</div>	
	<input type="hidden" name="update_external" value="1" />
	<input type="hidden" name="processCollection" value="1">

</form>
<?php } ?>
</div>
