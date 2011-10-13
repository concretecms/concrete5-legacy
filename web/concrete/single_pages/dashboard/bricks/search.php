<?php defined('C5_EXECUTE') or die(_("Access Denied."));

if($akcp->canSearch()) {

$vars = array_merge($this->controller->getSets(), $this->controller->getHelperObjects(), array(
	'controller'=>$this->controller,
	'view'=>View::getInstance()
));

$wrapId = $baseId.'_search';
?>

<h1><?php if(!$rs['url_insert_hidden']){?><a class="ccm-dashboard-header-option" href="<?php echo $this->url('/dashboard/bricks/insert/', $akCategoryHandle)?>">Add Item</a><?php } ?><span><?php echo $text->unhandle($akCategoryHandle).t(' Search')?></span></h1>
<div class="ccm-dashboard-inner">
	
    
        <div id="<?php echo $wrapId ?>">
            <table id="ccm-search-form-table" >
                <tr>
                    <td valign="top" class="ccm-search-form-advanced-col">
                        <?php
                            Loader::element(
                                'bricks/search_form_advanced',
                                $vars
                            );
                        ?>
                    </td>
                    <td valign="top" width="100%">
						<?php
						
							Loader::element(
								'bricks/search_results', 
								array_merge($vars, array('itemClickAction'=>'choose'))
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
								akCategoryHandle = "<?php echo $akCategoryHandle ?>",
								baseId = "<?php echo $baseId ?>",
								resultsSelector = "#"+baseId+"_results";
							
							
							$(resultsSelector).live("ccm_akcitemsearchresults_item_choose", function(evt, data){
								var id = data.$item.find("input[name=ID]").first().val();
								location.href = "<?php echo View::url('/dashboard/bricks/edit/', $akCategoryHandle) ?>"+id;
							});
						});
						</script>
                        
                       
                    </td>
                </tr>
            </table>
        </div>
    </div>

<?php } else { ?>
<h1><span><?php echo $text->unhandle($akCategoryHandle).t(' Search')?></span></h1>
<div class="ccm-dashboard-inner">
	You do not have permission to search this category.
</div>
<?php } ?>
