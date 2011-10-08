<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

//Prep the controller
$cnt = Loader::controller('/dashboard/bricks/insert');
$cnt->on_start();
$cnt->view($_REQUEST['akCategoryHandle']);
$cnt->on_before_render();

//Prep the variables array for the elements/view
$vars = array(
	'akCategoryHandle' => $_REQUEST['akCategoryHandle'],
	'view'=>View::getInstance(),
	'controller'=>$cnt,
	'baseId'=> isset($_REQUEST['baseId']) ? $_REQUEST['baseId'] : uniqid($_REQUEST['akCategoryHandle']),
	'urls' => Loader::helper('concrete/urls')
);

$vars['wrapId'] = $vars['baseId'].'_insert_dialog';

$local = array_merge($cnt->getSets(), $cnt->getHelperObjects(), $vars);
extract($local);

$view->setController($controller);

if(!$akcip->canAdd()){	
	echo t('You are not allowed to add %s items.', $akCategoryHandle);
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
		<?php Loader::element('bricks/insert/attributes', $local); ?>      
    </div>
    
    <div class="ccm-dialog-tabs-content" style="display:none;">
    	
    	<h1><?php echo t('Owner')?></h1>
        <?php Loader::element('bricks/insert/owner', $local); ?>
    	
    	<h1><?php echo t('Permissions')?></h1>
        <?php Loader::element('bricks/insert/permissions', $local); ?>
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
            $("#<?php echo $wrapId ?>").ccm_akcItemInsertDialogForm();
        });
    </script>
</form>
