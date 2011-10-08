<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 


//Prep the controller
$cnt = Loader::controller('/dashboard/bricks/search');
$cnt->on_start();
$cnt->view($_REQUEST['akCategoryHandle']);
$cnt->on_before_render();

//Prep the variables array for the elements/view
$vars = array_merge($cnt->getSets(), $cnt->getHelperObjects(), array(
	'view'=>View::getInstance(),
	'controller' => $cnt,
	'action' => $_REQUEST['action']
));

Loader::element('bricks/search_results', $vars);

/*
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
 
 */
?>