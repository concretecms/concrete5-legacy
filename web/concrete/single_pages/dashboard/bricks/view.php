<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

if(is_array($piles)) foreach($piles as $pkgName => $pile) { ?>
<h1><span><?=$pkgName?></span></h1>

<div class="ccm-dashboard-inner">
<?php 
	switch($pkgName) {
		case 'Built-In':
			$args = array('pile' => $pile, 'disableAction' => array('drop' => TRUE));
			break;
		default:
			$args = array('pile' => $pile);
			break;
	}

	Loader::element('attribute_key_category_table', $args);
?>
</div>
<?php } ?>
