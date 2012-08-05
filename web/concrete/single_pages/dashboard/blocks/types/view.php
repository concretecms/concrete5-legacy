<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">
<div class="row">
<div class="span12 offset2 columns">
<div class="ccm-pane">
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeader(t('Block Types'), t('Add custom block types, refresh the database tables of installed blocks, and uninstall blocks types from here.'));?>
<?php if ($this->controller->getTask() == 'inspect' || $this->controller->getTask() == 'refresh') { ?>

<div class="ccm-pane-body">

    <h3><img src="<?=$ci->getBlockTypeIconURL($bt)?>" /> <?=$bt->getBlockTypeName()?></h3>

    <h5><?=t('Description')?></h5>
    <p><?=$bt->getBlockTypeDescription()?></p>

    <h5><?=t('Usage Count')?></h5>
    <p><?=$num?></p>

    <?php if ($bt->isBlockTypeInternal()) { ?>
    <h5><?=t('Internal')?></h5>
    <p><?=t('This is an internal block type.')?></p>
    <?php } ?>

</div>
<div class="ccm-pane-footer">
    <a href="<?=$this->url('/dashboard/blocks/types')?>" class="btn"><?=t('Back to Block Types')?></a>

    <?php print $ch->button(t("Refresh"), $this->url('/dashboard/blocks/types','refresh', $bt->getBlockTypeID()), "right"); ?>
    <?php
    $u = new User();
    if ($u->isSuperUser()) {

        $removeBTConfirm = t('This will remove all instances of the %s block type. This cannot be undone. Are you sure?', $bt->getBlockTypeHandle());

        print $ch->button_js(t('Remove'), 'removeBlockType()', 'right', 'error');?>

        <script type="text/javascript">
        removeBlockType = function() {
            if (confirm('<?=$removeBTConfirm?>')) {
                location.href = "<?=$this->url('/dashboard/blocks/types', 'uninstall', $bt->getBlockTypeID(), $valt->generate('uninstall'))?>";
            }
        }
        </script>

    <?php } else { ?>
        <?php print $ch->button_js(t('Remove'), 'alert(\'' . t('Only the super user may remove block types.') . '\')', 'right', 'disabled error');?>
    <?php } ?>

</div>

<?php } else { ?>

<div class="ccm-pane-body ccm-pane-body-footer">

    <h3><?=t('Custom Block Types')?></h3>
    <h5><?=t('Awaiting Installation')?></h5>
    <?php if (count($availableBlockTypes) > 0) { ?>
        <ul id="ccm-block-type-list">
        <?php	foreach ($availableBlockTypes as $bt) {
            $btIcon = $ci->getBlockTypeIconURL($bt);
    ?>
            <li class="ccm-block-type ccm-block-type-available">
                <p style="background-image: url(<?=$btIcon?>)" class="ccm-block-type-inner"><?=$ch->button(t("Install"), $this->url('/dashboard/blocks/types','install', $bt->getBlockTypeHandle()), "right", 'small');?> <?=$bt->getBlockTypeName()?></p>
            </li>
        <?php } ?>
        </ul>

    <?php } else { ?>
        <p><?=t('No custom block types are awaiting installation.')?></p>
    <?php } ?>

    <h5><?=t('Currently Installed')?></h5>
    <?php if (count($webBlockTypes) > 0) { ?>
        <ul id="ccm-block-type-list">
            <?php foreach ($webBlockTypes as $bt) {
                $btIcon = $ci->getBlockTypeIconURL($bt);
                ?>
                <li class="ccm-block-type ccm-block-type-available">
                    <a style="background-image: url(<?=$btIcon?>)" class="ccm-block-type-inner" href="<?=$this->action('inspect', $bt->getBlockTypeID())?>"><?=$bt->getBlockTypeName()?></a>
                    <div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>"><?=$bt->getBlockTypeDescription()?></div>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p><?=t('No custom block types are installed.')?></p>
    <?php } ?>

    <?php if (ENABLE_MARKETPLACE_SUPPORT == true) { ?>

    <div class="well" style="padding:10px 20px;">
        <h3><?=t('More Blocks')?></h3>
        <p><?=t('Browse our marketplace of add-ons to extend your site!')?></p>
        <p><a class="btn success" href="<?=$this->url('/dashboard/extend/add-ons')?>"><?=t("More Add-ons")?></a></p>
    </div>

    <?php } ?>

    <h3><?=t('Core Block Types')?></h3>
        <ul id="ccm-block-type-list">
            <?php foreach ($coreBlockTypes as $bt) {
                $btIcon = $ci->getBlockTypeIconURL($bt);
                ?>
                <li class="ccm-block-type ccm-block-type-available">
                    <a style="background-image: url(<?=$btIcon?>)" class="ccm-block-type-inner" href="<?=$this->action('inspect', $bt->getBlockTypeID())?>"><?=$bt->getBlockTypeName()?></a>
                    <div class="ccm-block-type-description"  id="ccm-bt-help<?=$bt->getBlockTypeID()?>"><?=$bt->getBlockTypeDescription()?></div>
                </li>
            <?php } ?>
        </ul>

</div>

<?php } ?>
</div>
</div>
</div>
</div>
