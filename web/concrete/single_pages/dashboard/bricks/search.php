<?php defined('C5_EXECUTE') or die(_("Access Denied."));
if($permission) {
?>

<h1><?php if(!$rs['url_insert_hidden']){?><a class="ccm-dashboard-header-option" href="<?php echo $this->url('/dashboard/bricks/insert/', $akCategoryHandle)?>">Add Item</a><?php } ?><span><?php echo $txt->unhandle($akCategoryHandle).t(' Search')?></span></h1>
<div class="ccm-dashboard-inner">
	<?php
		$searchInstance = uniqid($akCategoryHandle.'_search');
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
                                    'searchInstance'	=> $searchInstance,
                                    'akCategoryHandle'	=> $akCategoryHandle
                                )
                            );
                        ?>
                    </td>
                    <td valign="top" width="100%">
						<?php
						
							Loader::element(
								'bricks/search_results', 
								array(
									'searchInstance'	=> $searchInstance,
									'akCategoryHandle'	=> $akCategoryHandle
								)
							);
							
						/*
							Loader::element(
								'bricks/search_results', 
								array(
									'searchInstance'	=> $searchInstance,
									'akCategoryHandle'	=> $akCategoryHandle,
									'onLeftClick'		=> "location.href='".View::url('/dashboard/bricks/edit/', $akCategoryHandle)."'+$(this).parent().children(':first-child').find('input[name=ID]').val()"
								)
							);
							
							*/
						?>
                        <script type="text/javascript">
						$(function(){
							
							var searchInstance = "<?php echo $searchInstance ?>",
								akCategoryHandle = "<?php echo $akCategoryHandle ?>";
								resultsSelector = "#"+searchInstance+"_results";
							
							$(resultsSelector).live("ccm_attributekeycategoryitemsearchresults_propertiesitem ccm_attributekeycategoryitemsearchresults_propertiesitems", function(evt, data){
						
								var params = {
										searchInstance:searchInstance,
										akCategoryHandle:akCategoryHandle
									},
									ids = data.$item.find("input[name=ID]").map(function(){
										return this.value;
									}).get();
								params["newObjectID[]"] = ids;
								//console.log($.param(params));
						
								jQuery.fn.dialog.open({
									width: 630,
									height: 450,
									modal: true,
									href: CCM_TOOLS_PATH +'/bricks/bulk_properties?' + $.param(params),
									title: ccmi18n.properties
								});	
								
								return false;
							});
							
							
							$(resultsSelector).live("ccm_attributekeycategoryitemsearchresults_deleteitem ccm_attributekeycategoryitemsearchresults_deleteitems", function(evt, data){								
								var params = {
										searchInstance:searchInstance,
										akCategoryHandle:akCategoryHandle
									},
									ids = data.$item.find("input[name=ID]").map(function(){
										return this.value;
									}).get();
								params["akcID[]"] = ids;
						
								jQuery.fn.dialog.open({
									width: 300,
									height: 100,
									modal: true,
									href: CCM_TOOLS_PATH +'/bricks/bulk_delete?' + $.param(params),
									title: ccmi18n.properties
								});	
								return false;
							});
							
							
							$(resultsSelector).live("ccm_attributekeycategoryitemsearchresults_chooseitem", function(evt, data){
								var id = data.$item.find("input[name=ID]").first().val();
								location.href = "<?php echo View::url('/dashboard/bricks/edit/', $akCategoryHandle) ?>"+id;
							});
							
						});
						</script>
                        
                       
                    </td>
                </tr>
            </table>
        </div>
    <? if (!isset($_REQUEST['refreshDialog'])) { ?> 
    </div>
    <? } ?>
</div>
<?php } else { ?>
<h1><span><?php echo $txt->unhandle($akCategoryHandle).t(' Search')?></span></h1>
<div class="ccm-dashboard-inner">
	You do not have permission to search this category.
</div>
<?php } ?>
