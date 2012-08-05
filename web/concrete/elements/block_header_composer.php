<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $bt = BlockType::getByID($b->getBlockTypeID());
$ci = Loader::helper("concrete/urls");
$btIcon = $ci->getBlockTypeIconURL($bt);
$cont = $bt->getController();

?>

<script type="text/javascript">

<?php $ci = Loader::helper("concrete/urls"); ?>
<?php $url = $ci->getBlockTypeJavaScriptURL($bt);
if ($url != '') { ?>
    ccm_addHeaderItem("<?=$url?>", 'JAVASCRIPT');
<?php }

$identifier = strtoupper('BLOCK_CONTROLLER_' . $btHandle);
if (is_array($headerItems[$identifier])) {
    foreach ($headerItems[$identifier] as $item) {
        if ($item instanceof CSSOutputObject) {
            $type = 'CSS';
        } else {
            $type = 'JAVASCRIPT';
        }
        ?>
        ccm_addHeaderItem("<?=$item->file?>", '<?=$type?>');
    <?php
    }
}
?>

$(function() {
    $('#ccm-block-form').each(function() {
        ccm_setupBlockForm($(this), false, 'add');
    });
});

</script>

<?php
if ($b->getBlockName() != '') {
    $btName = $b->getBlockName();
} else {
    $btName = $bt->getBlockTypeName();
}
?>

<?php if ($displayEditLink) { ?>
    <label><a href="javascript:void(0)" onclick="ccm_composerEditBlock(<?=$b->getBlockCollectionID()?>, <?=$b->getBlockID()?>, '<?=$b->getAreaHandle()?>', <?=$bt->getBlockTypeInterfaceWidth()?> , <?=$bt->getBlockTypeInterfaceHeight()?> )" ><?=$btName?></a></label>
<?php } else { ?>
    <label><?=$btName?></label>
<?php } ?>

<div class="clearfix">

<div class="input">
<?php Loader::element('block_header', array('b' => $b))?>
