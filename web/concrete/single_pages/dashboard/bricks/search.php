<?php defined('C5_EXECUTE') or die(_("Access Denied."));?>

<h1><?php if(!$rs['url_insert_hidden']){?><a class="ccm-dashboard-header-option" href="<?php echo $this->url('/dashboard/bricks/insert/', $akCategoryHandle)?>">Add Item</a><?php } ?><span><?php echo $txt->unhandle($akCategoryHandle).t(' Search')?></span></h1>
<div class="ccm-dashboard-inner">
	<?php
		$searchInstance = $akCategoryHandle.time();
        if (isset($_REQUEST['searchInstance'])) {
			$searchInstance = $_REQUEST['searchInstance'];
		}
		
		if (!isset($mode)) {
			$mode = $_REQUEST['mode'];
		}
	?>
    <? if (!isset($_REQUEST['refreshDialog'])) { ?> 
    <div id="ccm-<?=$searchInstance?>-overlay-wrapper">
    <? } ?>
        <div id="ccm-<?=$searchInstance?>-search-overlay">
            <table id="ccm-search-form-table" >
                <tr>
                    <td valign="top" class="ccm-search-form-advanced-col">
                        <?php
                            Loader::element(
                                'bricks/search_form_advanced', 
                                array(
                                    'searchInstance' => $searchInstance,
                                    'akCategoryHandle' => $akCategoryHandle
                                )
                            );
                        ?>
                    </td>
                    <td valign="top" width="100%">
                        <div id="ccm-<?=$searchInstance?>-search-results">
                            <?php
                                Loader::element(
                                    'bricks/search_results', 
                                    array(
                                        'searchInstance' => $searchInstance,
                                        'akCategoryHandle' => $akCategoryHandle
                                    )
                                );
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    <? if (!isset($_REQUEST['refreshDialog'])) { ?> 
    </div>
    <? } ?>
</div>

<script type="text/javascript">
	ccm_setupAttributeKeyCategoryItemSearch('<?=$searchInstance?>');
</script>