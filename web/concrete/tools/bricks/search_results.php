<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::element('bricks/search_results', array(
	'akCategoryHandle' => $_REQUEST['akCategoryHandle'],
	'searchInstance' => $_REQUEST['searchInstance']
));
