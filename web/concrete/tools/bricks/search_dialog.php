<?
defined('C5_EXECUTE') or die("Access Denied.");

//Prep the controller
$cnt = Loader::controller('/dashboard/bricks/search');
$cnt->on_start();
$cnt->view($_REQUEST['akCategoryHandle']);
$cnt->on_before_render();

//Prep the variables array for the elements/view
$vars = array_merge($cnt->getSets(), $cnt->getHelperObjects(), array(
	'view'=>View::getInstance(),
	'controller' => $cnt
));


extract($vars);

if(!$akcp->canSearch()){
	die(t('You do not have permission to search this category.'));
}
?>
<div id="ccm-<?php echo $baseId ?>-search-overlay">
	<table width="100%">
		<tr>
			<td valign="top" class="ccm-search-form-advanced-col">
				<?php
					Loader::element('bricks/search_form_advanced', $vars);
				?>
			</td>
			<td valign="top" width="100%">
				<?php
					Loader::element('bricks/search_results',$vars);
				?>
			</td>
		</tr>
	</table>
</div>