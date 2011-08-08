<table class="entry-form" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader"><?php echo t('Select Table')?></td>
	</tr>
	<tr>
		<td>
			<?php print Loader::helper('form/attribute_key_category_selector')->select($akCategoryHandle); ?>
		</td>
	</tr>
</table>