<?php
	Loader::element(
		'bricks/search_form_advanced', 
		array(
			'searchInstance'				=> $_REQUEST['searchInstance'],
			'akCategoryHandle'				=> $_REQUEST['akCategoryHandle'],
			'persistantBID'					=> $_REQUEST['persistantBID'],
			'administrationDisabled'		=> $_REQUEST['administrationDisabled'],
			'userDefinedColumnsDisabled'	=> $_REQUEST['userDefinedColumnsDisabled'],
			'columns'						=> $_REQUEST['columns'],
			'action'						=> $_REQUEST['action']
		)
	);
?>