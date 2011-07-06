<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<div style="width:250px">
	<h1><span>Add Custom Category</span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" action="<?php print View::url('dashboard/bricks/add'); ?>" id="add-new-category">
			<?php print $form->hidden('package_handle', $packageHandle); ?>
			<h2><?php print $form->label('akCategoryName', 'Category Display Name'); ?></h2>
			<?php print $form->text('akCategoryName'); ?>
			<br />
			<br />
			<h2><?php print $form->label('enableSets', 'Enable Sets?'); ?></h2>
			<?php print $form->radio('enableSets', 1, 1). 'Yes'; ?>
			<?php print $form->radio('enableSets', 0, 1). 'No'; ?>
			<br />
			<br />
			<h2><?php print $form->label('associateAttributeTypes', 'Associate all Attribute Types?'); ?></h2>
			<?php print $form->radio('associateAttributeTypes', 1, 1). 'Yes'; ?>
			<?php print $form->radio('associateAttributeTypes', 0, 1). 'No'; ?>
			<br />
			<br />
			<?php print $ih->submit('Add', 'add-new-category', 'right'); ?>
			<?php print $ih->button('Cancel', View::url('dashboard/bricks'), 'left'); ?>
		</form>
	</div>
</div>
