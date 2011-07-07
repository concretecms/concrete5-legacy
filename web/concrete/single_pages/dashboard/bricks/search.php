<?php defined('C5_EXECUTE') or die(_("Access Denied."));?>

<h1><a class="ccm-dashboard-header-option" href="<?php echo $this->url('/dashboard/bricks/insert/', $akCategoryHandle)?>">Add Item</a><span>'<?php echo $txt->unhandle($akCategoryHandle).t('\' Item Search')?></span></h1>
<div class="ccm-dashboard-inner">
	<table id="ccm-search-form-table" >
		<tr>
			<td valign="top" class="ccm-search-form-advanced-col"><?php Loader::element('dashboard/bricks/search/search_form_advanced', array('akCategoryHandle' => $akCategoryHandle) ); ?></td>
			<td valign="top" width="100%"><div id="ccm-search-advanced-results-wrapper">
				<div id="ccm-new-object-search-results">
					<?php  Loader::element('dashboard/bricks/search/search_results', array('newObjects' => $newObjects, 'newObjectList' => $newObjectList, 'pagination' => $pagination, 'akCategoryHandle' => $akCategoryHandle)); ?>
				</div>
			</td>
		</tr>
	</table>
</div>
