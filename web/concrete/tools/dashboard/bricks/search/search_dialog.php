<?
defined('C5_EXECUTE') or die("Access Denied.");

/* permissions
$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(t("You have no access to users."));
}
*/

if (!isset($mode)) {
	$mode = $_REQUEST['mode'];
}
?>

<div id="ccm-search-overlay" >

		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col"><?php  Loader::element('bricks/search_form_advanced', array('akCategoryHandle' => $_REQUEST['akCategoryHandle'], 'akID' => $_REQUEST['akID']) ); ?></td>
				<td valign="top" width="100%"><div id="ccm-search-advanced-results-wrapper">
						<div id="ccm-new-object-search-results">
							<?php  Loader::element('bricks/search_results', array('akCategoryHandle' => $_REQUEST['akCategoryHandle'], 'akID' => $_REQUEST['akID'])); ?>
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
<script type="text/javascript">
	ccm_setupNewObjectSearch<?php if($_REQUEST['akID']) print '_'.$_REQUEST['akID'];?>();
</script>