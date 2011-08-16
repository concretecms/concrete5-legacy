<?php defined('C5_EXECUTE') or die("Access Denied.");
$searchInstance = 'block'.$persistantBID;?>
<div id="ccm-<?=$searchInstance?>-search-overlay">
	<input type="hidden" name="persistantBID_<?=$searchInstance?>" value="<?=$persistantBID?>" />
	<table id="ccm-search-form-table" >
		<tr>
			<td valign="top" class="ccm-search-form-advanced-col"<?php if(!$isSearchable) { ?> style="display:none"<?php }?>>
				<?php
					Loader::element(
						'bricks/search_form_advanced', 
						array(
							'searchInstance'				=> $searchInstance,
							'akCategoryHandle'				=> $akCategoryHandle,
							'persistantBID'					=> $persistantBID,
							'administrationDisabled'		=> $administrationDisabled,
							'userDefinedColumnsDisabled'	=> $userDefinedColumnsDisabled,
							'columns'						=> $columns,
							'action'						=> 'null',
							'keywords'						=> $keywords,
							'numResults'					=> $numResults,
							'akID'							=> $akID
						)
					);
				?>
			</td>
			<td valign="top" width="100%">
				<div id="ccm-<?=$searchInstance?>-search-results">
				</div>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	//$("#ccm-<?=$searchInstance?>-advanced-search").submit();
</script>