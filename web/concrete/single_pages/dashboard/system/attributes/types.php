<?php defined('C5_EXECUTE') or die("Access Denied.");

$types = AttributeType::getList();
$categories = AttributeKeyCategory::getList();
$txt = Loader::helper('text');
$form = Loader::helper('form');
$interface = Loader::helper('concrete/interface');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Attribute Type Associations'), false, 'span12 offset2');?>
<form method="post" class="" id="attribute_type_associations_form" action="<?=$this->action('save_attribute_type_associations')?>">
    <table border="0" cellspacing="1" cellpadding="0" border="0" class="zebra-striped">
        <tr>
            <th><?=t('Name')?></th>
            <?php foreach ($categories as $cat) { ?>
                <th><?=$txt->unhandle($cat->getAttributeKeyCategoryHandle())?></th>
            <?php } ?>
        </tr>
        <?php foreach ($types as $at) { ?>

            <tr>
                <td><?=$at->getAttributeTypeName()?></td>
                <?php foreach ($categories as $cat) { ?>
                    <td style="width: 1px; text-align: center"><?=$form->checkbox($cat->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($cat))?></td>
                <?php } ?>
            </tr>

        <?php } ?>

    </table>
    <div class="well clearfix">
    <?php
    $b1 = $interface->submit(t('Save'), 'attribute_type_associations_form', 'right', 'primary');
    print $b1;
    ?>
    </div>
</form>

<h3><?=t('Custom Attribute Types')?></h3>
<?php
$ch = Loader::helper('concrete/interface');
$types = PendingAttributeType::getList(); ?>
<?php if (count($types) == 0) { ?>
    <?=t('There are no available attribute types awaiting installation.')?>
<?php } else { ?>
    <ul id="ccm-block-type-list">
        <?php foreach ($types as $at) { ?>
            <li class="ccm-block-type ccm-block-type-available">
                <form id="attribute_type_install_form_<?=$at->getAttributeTypeHandle()?>" style="margin: 0px" method="post" action="<?=$this->action('add_attribute_type')?>">
                    <?php
                    print $form->hidden("atHandle", $at->getAttributeTypeHandle());
                    ?>
                    <p style="background-image: url(<?=$at->getAttributeTypeIconSRC()?>)" class="ccm-block-type-inner"><?=$ch->submit(t("Install"), 'submit', 'right', 'small')?><?=$at->getAttributeTypeName()?></p>
                </form>
            </li>
        <?php } ?>
    </ul>

<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
