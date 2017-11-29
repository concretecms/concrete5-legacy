<?php
defined('C5_EXECUTE') or die("Access Denied.");
global $c;?>
<?php
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$bi = $b->getInstance();
	$bx = Block::getByID($bi->getOriginalBlockID());
	$bt = BlockType::getByID($bx->getBlockTypeID());
} else {
	$bt = BlockType::getByID($b->getBlockTypeID());
}
$templates = $bt->getBlockTypeCustomTemplates();
$txt = Loader::helper('text');
?>
<div class="ccm-ui" style="padding-top:10px;">
<form method="post" id="ccmCustomTemplateForm" action="<?php echo $b->getBlockUpdateInformationAction()?>&amp;rcID=<?php echo intval($rcID) ?>" class="form-vertical">
	
	<?php if (count($templates) == 0) { ?>

		<?php echo t('There are no custom templates available.')?>

	<?php } else { ?>
		
    	<div class="control-group">
          <label for="bFilename" class="control-label"><?php echo t('Custom Template')?></label>
            <div class="controls">
                <select id="bFilename" name="bFilename" class="xlarge">
                    <option value="">(<?php echo t('None selected')?>)</option>
                    <?php
                    foreach($templates as $tpl) {
                        ?><option value="<?php echo $tpl->getTemplateFileFilename()?>" <?php if ($b->getBlockFilename() == $tpl->getTemplateFileFilename()) { ?> selected <?php } ?>><?php echo $tpl->getTemplateFileDisplayName()?></option><?php
                    }
                    ?>
                </select>
            </div>
				</div>

	<?php } ?>

        <div class="control-group" style="padding-top:10px">
          <label for="bName" class="control-label"><?php echo t('Block Name')?></label>
            <div class="controls">
						<input type="text" id="bName" name="bName" class="bName" value="<?php echo $b->getBlockName() ?>" />
            </div>
        </div>

		<div class="ccm-buttons dialog-buttons">
			<a href="#" class="btn ccm-dialog-close ccm-button-left cancel"><?php echo t('Cancel')?></a>
			<a href="javascript:void(0)" onclick="$('#ccmCustomTemplateForm').submit()" class="ccm-button-right accept primary btn"><span><?php echo t('Save')?></span></a>
		</div>
		
<?php
$valt = Loader::helper('validation/token');
$valt->output();
?>
</form>
</div>

<script type="text/javascript">
$(function() {
	$('#ccmCustomTemplateForm').each(function() {
		ccm_setupBlockForm($(this), '<?php echo $b->getBlockID()?>', 'edit');
	});
});
</script>
