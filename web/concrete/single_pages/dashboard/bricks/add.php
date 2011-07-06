<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div style="width:200px">
	<h1><span>Add Custom Category</span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" action="" id="add-new-category">
			<?php print $form->hidden('package_handle', $packageHandle); ?>
			<?php print $form->label('akCategoryName', 'Category Name'); ?>
			<?php print $form->text('akCategoryName'); ?>
			<br />
			<br />
			<?php print $form->label('enableSets', 'Enable Sets?'); ?>
			<?php print $form->radio('enableSets', 1). 'Yes'; ?>
			<?php print $form->radio('enableSets', 0). 'No'; ?>
			<br />
			<br />
			<?php print $ih->submit('Add', 'add-new-category', 'right'); ?>
			<?php print $ih->button('Cancel', '..', 'left'); ?>
		</form>
	</div>
</div>
