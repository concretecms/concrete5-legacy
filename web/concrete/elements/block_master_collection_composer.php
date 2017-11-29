<?php
defined('C5_EXECUTE') or die("Access Denied.");
global $c;?>
<?php
$form = Loader::helper('form');
$bt = BlockType::getByID($b->getBlockTypeID());
$templates = $bt->getBlockTypeComposerTemplates();
$txt = Loader::helper('text');
?>
<div class="ccm-ui">

<form method="post" class="form-stacked" id="ccmComposerCustomTemplateForm" action="<?php echo $b->getBlockUpdateComposerSettingsAction()?>&rcID=<?php echo intval($rcID) ?>">

	<div class="clearfix">
	<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->checkbox('bIncludeInComposer', 1, $b->isBlockIncludedInComposer())?> <span><?php echo t("Include block in Composer")?></span></label></li>
	</ul>
	</div>
	</div>
	
	<div class="clearfix">
	<?php echo $form->label('bName', t('Block Name'))?>
	<div class="input">
	<?php echo $form->text('bName', $b->getBlockName(), array('style' => 'width: 280px'))?>
	</div>
	</div>


	<?php if (count($templates) > 0) { ?>

	<div class="clearfix">
	<?php echo $form->label('cbFilename', t('Custom Composer Template'))?>
	<div class="input">
		<select name="cbFilename">
			<option value="">(<?php echo t('None selected')?>)</option>
			<?php
			foreach($templates as $tpl) {
				?><option value="<?php echo $tpl->getTemplateFileFilename()?>" <?php if ($b->getBlockComposerFilename() == $tpl->getTemplateFileFilename()) { ?> selected <?php } ?>><?php echo $tpl->getTemplateFileDisplayName()?></option><?php
			}
			?>
		</select>
	</div>
	</div>
	
	<?php } ?>
<?php
$valt = Loader::helper('validation/token');
$valt->output();
?>

		<div class="dialog-buttons">
		<a href="#" class="ccm-dialog-close ccm-button-left cancel btn"><?php echo t('Cancel')?></a>
		<a href="javascript:void(0)" onclick="$('#ccmComposerCustomTemplateForm').submit()" class="ccm-button-right accept primary btn"><?php echo t('Update')?></a>
		</div>

</form>
</div>

<script type="text/javascript">
$(function() {
	$('#ccmComposerCustomTemplateForm').each(function() {
		ccm_setupBlockForm($(this), '<?php echo $b->getBlockID()?>', 'edit');
	});
});
</script>