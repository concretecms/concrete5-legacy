<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

if(is_array($piles)) foreach($piles as $pkgName => $pile) { ?>
<h1><?php if($pkgName == "Custom Additions") { ?><a class="ccm-dashboard-header-option" href="add">Add Custom Category</a><?php } ?><span><?=$pkgName?></span></h1>

<div class="ccm-dashboard-inner">
<?php
	$args = array('pile' => $pile);
	Loader::element('attribute_key_category_table', $args);
?>
</div>
<?php } ?>
