<?php defined('C5_EXECUTE') or die("Access Denied.");

//Prep the variables array for the elements
$view = View::getInstance();
$controller = $this->controller;

$vars = array_merge($this->controller->getSets(), $this->controller->getHelperObjects(), array(
	'view'=>$view,
	'controller'=>$controller
));

if($akcp->canAdd()) {	 
	$df = Loader::helper('form/date_time');
	?>
	<form method="post" action="" id="new-object-form">	
			
        <table width="100%">
            <tbody>
                <tr>
                    <td valign="top">
                        <h1><a class="ccm-dashboard-header-option" style="right:130px;" href="<?php echo $view->url('/dashboard/bricks/structure/');?>">Global Attributes</a><a class="ccm-dashboard-header-option" href="<?php echo $view->url('/dashboard/bricks/structure/'.$akCategoryHandle)?>">Category Attributes</a><span><?php echo 'Insert New '.$text->unhandle($akCategoryHandle).t(' Item')?></span></h1>
						<div class="ccm-dashboard-inner">
							<?php Loader::element('bricks/edit/attributes', $vars); ?>
                        </div>
                    </td>
                    <td valign="top">
                    	<h1><span><?php echo t('Owner')?></span></h1>
						<div class="ccm-dashboard-inner">
                        	<?php Loader::element('bricks/edit/owner', $vars); ?>
                        </div>
                        <h1><span><?php echo t('Permissions')?></span></h1>
                        <div class="ccm-dashboard-inner">
                        	<?php Loader::element('bricks/edit/permissions', $vars); ?>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2">
                        <h1><span><?php echo t('Action')?></span></h1>
                        <div class="ccm-dashboard-inner">
                            <?php
                                print $ih->button(t('Cancel'), $view->url('/dashboard/bricks/search/'.$akCategoryHandle), 'left');
                                print $ih->submit('Add');
                            ?>
                            <div class="ccm-spacer">&nbsp;</div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
	</form>
	
	
<?php } else { ?>

		
	<h1><span><?php echo t($text->unhandle($akCategoryHandle).' Insert')?></span></h1>
	<div class="ccm-dashboard-inner">
	<?php echo t('You are not allowed to add items in this category.')?>
	</div>


<?php } ?>
