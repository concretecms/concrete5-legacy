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
<form method="post" id="ccmCustomTemplateForm" action="<?=$b->getBlockUpdateInformationAction()?>&amp;rcID=<?=intval($rcID) ?>" class="form-stacked clearfix">

    <?php if (count($templates) == 0) { ?>

        <?=t('There are no custom templates available.')?>

    <?php } else { ?>

        <div class="clearfix">
          <label for="bFilename"><?=t('Custom Template')?></label>
            <div class="input">
                <select id="bFilename" name="bFilename" class="xlarge">
                    <option value="">(<?=t('None selected')?>)</option>
                    <?php foreach ($templates as $tpl) { ?>
                        <option value="<?=$tpl?>" <?php if ($b->getBlockFilename() == $tpl) { ?> selected <?php } ?>><?php
                            if (strpos($tpl, '.') !== false) {
                                print substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'));
                            } else {
                                print $txt->unhandle($tpl);
                            }
                            ?></option>
                    <?php } ?>
                </select>
            </div>
                </div>

    <?php } ?>

        <div class="clearfix" style="padding-top:10px">
          <label for="bName"><?=t('Block Name')?></label>
            <div class="input">
                        <input type="text" id="bName" name="bName" class="bName" value="<?=$b->getBlockName() ?>" />
            </div>
        </div>

        <div class="ccm-buttons dialog-buttons">
            <a href="#" class="btn ccm-dialog-close ccm-button-left cancel"><?=t('Cancel')?></a>
            <a href="javascript:void(0)" onclick="$('#ccmCustomTemplateForm').submit()" class="ccm-button-right accept primary btn"><span><?=t('Save')?></span></a>
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
        ccm_setupBlockForm($(this), '<?=$b->getBlockID()?>', 'edit');
    });
});
</script>
