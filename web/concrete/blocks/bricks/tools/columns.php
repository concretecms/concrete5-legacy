<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

$selectedAKIDs = array();

Loader::model('attribute_key_category_item_list');
$akcdca = new AttributeKeyCategoryAvailableColumnSet($_REQUEST['akCategoryHandle']);
if(!$_REQUEST['columns']) {
	$akcdc = AttributeKeyCategoryColumnSet::getCurrent($_REQUEST['akCategoryHandle']);
	$columns = $akcdc;
} else {
	$columns = unserialize(urldecode($_REQUEST['columns']));
	if(is_string($columns)) $columns = unserialize($columns);
}
$list = AttributeKey::getList($_REQUEST['akCategoryHandle']);
?>
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top">
				<h1>
					<?=t('Choose Headers')?>
				</h1>
				<?php if(count($columns)){?>
				<h2>
					<?=t('Standard Properties')?>
				</h2>
				<?php foreach($akcdca->getColumns() as $col) { ?>
				<div>
					<?=$form->checkbox($col->getColumnKey(), 1, $columns->contains($col), array('style' => 'vertical-align: middle'))?>
					<label style="display:inline" for="<?=$col->getColumnKey()?>">
						<?=$col->getColumnName()?>
					</label>
				</div>
				<?php } ?>
				<?php } ?>
				<h2>
					<?=t('Additional Attributes')?>
				</h2>
				<?php foreach($list as $ak) { ?>
				<div>
					<?=$form->checkbox('ak_' . $ak->getAttributeKeyHandle(), 1, $columns->contains($ak), array('style' => 'vertical-align: middle'))?>
					<label style="display:inline" for="ak_<?=$ak->getAttributeKeyHandle()?>">
						<?=$ak->getAttributeKeyDisplayHandle()?>
					</label>
				</div>
				<?php } ?></td>
			<td><div style="width: 20px">&nbsp;</div></td>
			<td valign="top" width="50%">
				<h2>
					<?=t('Column Order')?>
				</h2>
				<p>
					<?=t('Click and drag to change column order.')?>
				</p>
				<ul class="ccm-search-sortable-column-wrapper" id="ccm-<?='block'.$_REQUEST['bID']?>-sortable-column-wrapper">
					<?php if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) { ?>
					<li id="field_<?=$col->getColumnKey()?>">
						<input type="hidden" name="column[]" value="<?=$col->getColumnKey()?>" />
						<?=$col->getColumnName()?>
					</li>
					<? } ?>
				</ul>
				<h2>
					<?=t('Sort By')?>
				</h2>
				<div class="ccm-sortable-column-sort-controls">
					<?php $ds = $columns->getDefaultSortColumn(); ?>
					<select <?php if(count($columns->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?='block'.$_REQUEST['bID']?>-sortable-column-default" name="fSearchDefaultSort">
						<?php if(is_array($columns->getSortableColumns())) foreach($columns->getSortableColumns() as $col) { ?>
						<option id="opt_<?=$col->getColumnKey()?>" value="<?=$col->getColumnKey()?>" <? if ($col->getColumnKey() == $ds->getColumnKey()) { ?> selected="selected" <? } ?>>
						<?=$col->getColumnName()?>
						</option>
						<? } ?>
					</select>
					<select <? if (count($columns->getSortableColumns()) == 0) { ?>disabled="true"<? } ?> id="ccm-<?='block'.$_REQUEST['bID']?>-sortable-column-default-direction" name="fSearchDefaultSortDirection">
						<option value="asc" <? if ($ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="true" <? } ?>>
						<?=t('Ascending')?>
						</option>
						<option value="desc" <? if ($ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="true" <? } ?>>
						<?=t('Descending')?>
						</option>
					</select>
				</div></td>
		</tr>
	</table>
	<script type="text/javascript">
$(function() {
	$('#ccm-<?='block'.$_REQUEST['bID']?>-sortable-column-wrapper').sortable({
		cursor: 'move',
		opacity: 0.5
	});
	$('#ccm-bricksPane-columns input[type=checkbox]').click(function() {
		var thisLabel = $(this).parent().find('label').html();
		var thisID = $(this).attr('id');
		if ($(this).prop('checked')) {
			if ($('#field_' + thisID).length == 0) {
				$('#ccm-<?='block'.$_REQUEST['bID']?>-sortable-column-default').append('<option value="' + thisID + '" id="opt_' + thisID + '">' + thisLabel + '<\/option>');
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', false);
				$('#ccm-<?='block'.$_REQUEST['bID']?>-sortable-column-wrapper').append('<li id="field_' + thisID + '"><input type="hidden" name="column[]" value="' + thisID + '" />' + thisLabel + '<\/li>');
			}
		} else {
			$('#field_' + thisID).remove();
			$('#opt_' + thisID).remove();
			if ($('#ccm-<?='block'.$_REQUEST['bID']?>-sortable-column-wrapper li').length == 0) {
				$('div.ccm-sortable-column-sort-controls select').attr('disabled', true);
			}
		}
	});
});
</script> 