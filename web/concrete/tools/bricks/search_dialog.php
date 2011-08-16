<?
defined('C5_EXECUTE') or die("Access Denied.");

/* permissions
$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(t("You have no access to users."));
}
*/

$searchInstance = $_REQUEST['akCategoryHandle'] . time();
if (isset($_REQUEST['searchInstance'])) $searchInstance = $_REQUEST['searchInstance'];

if (!isset($mode)) $mode = $_REQUEST['mode'];

?>
<div id="ccm-<?=$searchInstance?>-search-overlay">
	<table id="ccm-search-form-table" >
		<tr>
			<td valign="top" class="ccm-search-form-advanced-col">
				<?php
					Loader::element(
						'bricks/search_form_advanced', 
						array(
							'searchInstance' => $searchInstance,
							'akCategoryHandle' => $_REQUEST['akCategoryHandle'], 
							'akID' => $_REQUEST['akID']
						)
					);
				?>
			</td>
			<td valign="top" width="100%">
				<?php
					Loader::element(
						'bricks/search_results', 
						array(
							'searchInstance' => $searchInstance,
							'akCategoryHandle' => $_REQUEST['akCategoryHandle'], 
							'akID' => $_REQUEST['akID']
						)
					);
					?>
			</td>
		</tr>
	</table>
</div>