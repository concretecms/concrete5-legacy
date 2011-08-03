<?php defined('C5_EXECUTE') or die(_("Access Denied."));
if($permission) { ?>
<div style="width:300px">
	<h1><span>Warning!</span></h1>
	<div class="ccm-dashboard-inner">
		<p>Are you sure you want to drop the <strong><?=$akCategoryName?></strong> category?</p>
		<form method="post" action="" id="drop-category">
			<?php print $ih->submit('Yes', 'drop-category', 'right'); ?>
			<?php print $ih->button('No', '..', 'left'); ?>
		</form>
	</div>
</div>
<?php } else { ?>
<div style="width:300px">
	<h1><span>Warning!</span></h1>
	<div class="ccm-dashboard-inner">
		You do not have permission to drop this category.
	</div>
</div>
<?php } ?>