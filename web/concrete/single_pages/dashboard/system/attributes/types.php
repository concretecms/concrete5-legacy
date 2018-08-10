<?php defined('C5_EXECUTE') or die("Access Denied.");

$types = AttributeType::getList();
$categories = AttributeKeyCategory::getList();
$txt = Loader::helper('text');
$form = Loader::helper('form');
$interface = Loader::helper('concrete/interface');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Attribute Type Associations'), false, 'span10 offset1');?>
<form method="post" class="" id="attribute_type_associations_form" action="<?php echo $this->action('save_attribute_type_associations')?>">
	<table border="0" cellspacing="1" cellpadding="0" border="0" class="table">
		<tr>
			<th><?php echo t('Name')?></th>
			<?php foreach($categories as $cat) { ?>
				<th><?php
					$akcHandle = $cat->getAttributeKeyCategoryHandle();
					switch($akcHandle) {
						case 'collection':
							echo t('Page');
							break;
						case 'user':
							echo t('User');
							break;
						case 'file':
							echo t('File');
							break;
						default:
							echo t($txt->unhandle($akcHandle));
							break;
					}
				?></th>
			<?php } ?>
		</tr>
		<?php foreach($types as $at) { ?>

			<tr>
				<td><?php echo $at->getAttributeTypeDisplayName()?></td>
				<?php foreach($categories as $cat) { ?>
					<td style="width: 1px; text-align: center"><?php echo $form->checkbox($cat->getAttributeKeyCategoryHandle() . '[]', $at->getAttributeTypeID(), $at->isAssociatedWithCategory($cat))?></td>
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

<h3><?php echo t('Custom Attribute Types')?></h3>
<?php
$ch = Loader::helper('concrete/interface');
$types = PendingAttributeType::getList(); ?>
<?php if (count($types) == 0) { ?>
	<?php echo t('There are no available attribute types awaiting installation.')?>
<?php } else { ?>
	<ul id="ccm-block-type-list">
		<?php foreach($types as $at) { ?>
			<li class="ccm-block-type ccm-block-type-available">
				<form id="attribute_type_install_form_<?php echo $at->getAttributeTypeHandle()?>" style="margin: 0px" method="post" action="<?php echo $this->action('add_attribute_type')?>">
					<?php
					print $form->hidden("atHandle", $at->getAttributeTypeHandle());
					?>
					<p style="background-image: url(<?php echo $at->getAttributeTypeIconSRC()?>)" class="ccm-block-type-inner"><?php echo $ch->submit(t("Install"), 'submit', 'right', 'small')?><?php echo $at->getAttributeTypeDisplayName()?></p>
				</form>
			</li>
		<?php } ?>
	</ul>

<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);