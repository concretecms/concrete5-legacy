<?
defined('C5_EXECUTE') or die("Access Denied.");

/* permissions
$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(t("You have no access to users."));
}
*/

$cnt = Loader::controller('/dashboard/virtual_tables/search');
$newObjectList = $cnt->getRequestedSearchResults($_REQUEST['table']);
$newObjects = $newObjectList->getPage();
$pagination = $newObjectList->getPagination();

Loader::helper('virtual_tables_category', 'virtual_tables');
$names = new VirtualTablesObjectsNamingConvention();
$names->useHandle($_REQUEST['table']);

if (!isset($mode)) {
	$mode = $_REQUEST['mode'];
}
?>

<div id="ccm-search-overlay" >

		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col"><?php  Loader::packageElement('dashboard/virtual_tables/search/search_form_advanced', 'virtual_tables', array('names' => $names) ); ?></td>
				<td valign="top" width="100%"><div id="ccm-search-advanced-results-wrapper">
						<div id="ccm-new-object-search-results">
							<?php  Loader::packageElement('dashboard/virtual_tables/search/search_results', 'virtual_tables', array('newObjects' => $newObjects, 'newObjectList' => $newObjectList, 'pagination' => $pagination, 'names' => $names, 'akID' => $_REQUEST['akID'])); ?>
						</div>
					</div></td>
			</tr>
		</table>
		
</div>

<script type="text/javascript">
$(function() {
	ccm_setupAdvancedSearch('new-object');
});
</script>