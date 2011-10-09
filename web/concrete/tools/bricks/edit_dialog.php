<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

//Prep the controller
if($_REQUEST['akciID']){
	$isNew = FALSE;
	$cnt = Loader::controller('/dashboard/bricks/edit');
	$cnt->on_start();
	$cnt->view($_REQUEST['akCategoryHandle'], $_REQUEST['akciID']);
	$cnt->on_before_render();
}else{
	$isNew = TRUE;
	$cnt = Loader::controller('/dashboard/bricks/insert');
	$cnt->on_start();
	$cnt->view($_REQUEST['akCategoryHandle']);
	$cnt->on_before_render();
}

//Prep the variables array for the elements/view
$vars = array(
	'view'=>View::getInstance(),
	'controller'=>$cnt,
	'baseId'=> ($_REQUEST['baseId']) ? $_REQUEST['baseId'] : uniqid($_REQUEST['akCategoryHandle'])
);

$vars['wrapId'] = $vars['baseId'].'_edit_dialog';

$vars = array_merge($cnt->getSets(), $cnt->getHelperObjects(), $vars);
extract($vars);

$view->setController($controller);

if($isNew && !$akcp->canAdd()){
	header("HTTP/1.1 401");	
	echo t('You are not allowed to add %s items.', $text->unhandle($akCategoryHandle));
	exit;
}else if(!$isNew && !$akcip->canEdit()){
	header("HTTP/1.1 401");
	echo t('You are not allowed to edit %s item #%s.', $text->unhandle($akCategoryHandle), $akci->ID);
	exit;
}
//echo '<pre>';
//print_r($_SERVER);


//If item saved OK, give 'em some json
if($controller->isPost() && !$error->has()) {	 
	
	$json = Loader::helper('json');
	
	$output = array('akciID'=>$akci->getID());
	
	echo $json->encode($output);
	exit;
	
}else if($controller->isPost() && $error->has()){
	

}?>
	</pre>
<form id="<?php echo $wrapId ?>" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
	<?php echo $form->hidden('akCategoryHandle', $akCategoryHandle) ?>
	<?php echo $form->hidden('baseId', $baseId) ?>
    <?php echo $form->hidden('newObjectID', $newObjectID) ?>
    
	<!--h2><?php echo t('New %s Item', $text->unhandle($akCategoryHandle))?></h2-->	
	
	<?php if($error->has()) $error->output() ?>	
	
	<ul class="ccm-dialog-tabs" >
		<li class="ccm-nav-active"><a href="javascript:;"><?php echo t('Attributes')?></a></li>
		<li class=""><a href="javascript:;"><?php echo t('Owner/Permissions')?></a></li>
	</ul>

	
    
	<div class="ccm-dialog-tabs-content">
    	<h1>Attributes</h1>					
		<?php Loader::element('bricks/edit/attributes', $vars); ?>      
    </div>
    
    <div class="ccm-dialog-tabs-content" style="display:none;">
    	
    	<h1><?php echo t('Owner')?></h1>
        <?php Loader::element('bricks/edit/owner', $vars); ?>
    	
    	<h1><?php echo t('Permissions')?></h1>
        <?php Loader::element('bricks/edit/permissions', $vars); ?>
    </div>
    
    <hr/>
    <div>
        <?php
            echo $ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'LEFT');
            echo $ih->submit('Add');
        ?>
    </div>
    
    <script type="text/javascript">
        $(function(){
            $("#<?php echo $wrapId ?>").ccm_akcItemEditDialogForm();
        });
    </script>
</form>
