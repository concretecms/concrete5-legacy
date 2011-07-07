<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$cnt = Loader::controller('/dashboard/bricks/search');
$newObjectList = $cnt->getRequestedSearchResults($_REQUEST['handle']);
$newObjects = $newObjectList->getPage();
$pagination = $newObjectList->getPagination();

Loader::element('dashboard/bricks/search/search_results', array('newObjects' => $newObjects, 'newObjectList' => $newObjectList, 'pagination' => $pagination, 'akCategoryHandle' => $_REQUEST['handle']));
