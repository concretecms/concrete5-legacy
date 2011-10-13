<table class="entry-form" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader"><?php echo t('Select Table')?></td>
	</tr>
	<tr>
		<td>
			<?php print Loader::helper('form/attribute_key_category_selector')->select($akCategoryHandle); ?>
		</td>
	</tr>
    <tr>
		<td class="subheader"><?php echo t('Maximum Number of Items')?></td>
	</tr>
	<tr>
		<td>
			<?php print $form->text('max', $max); ?> <small><?php echo t('Use "0" or leave blank for no limit.') ?></small>
		</td>
	</tr>
</table>