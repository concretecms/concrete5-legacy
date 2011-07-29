<?php
$txt = Loader::helper('text');
$list = AttributeKeyCategory::getList();
?>
<table class="entry-form" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader"><?php echo t('Select Table')?></td>
	</tr>
	<tr>
		<td>
			<select name="akCategory"><?php foreach($list as $cat) { ?>
				<option<?php if($akCategoryHandle == $cat->getAttributeKeyCategoryHandle()){ ?> selected<?php } ?> value="<?php echo $cat->getAttributeKeyCategoryHandle();?>"><?php echo $txt->unhandle($cat->getAttributeKeyCategoryHandle());?></option>
			<?php }?></select>
		</td>
	</tr>
</table>