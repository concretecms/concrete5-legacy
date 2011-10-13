<?php defined('C5_EXECUTE') or die(_("Access Denied."));

$baseId = isset($_REQUEST['baseId']) ? $_REQUEST['baseId'] : $_REQUEST['akCategoryHandle'];
$wrapId = $baseId.'_bulk_delete_form';

$json = Loader::helper('json');
$form = Loader::helper('form');

Loader::model('attribute_key_category_item_permission');

if ($_REQUEST['task'] == 'delete') {
	if (!ini_get('safe_mode')) {
		@set_time_limit(0);
	}
	$output = array('deleted'=>array());
	$akc = AttributeKeyCategory::getByHandle($_REQUEST['akCategoryHandle']);
	foreach($_REQUEST['akciID'] as $ID) {
		$akci = $akc->getItemObject($ID);		
		$akcip = AttributeKeyCategoryItemPermission::get($akci);
		if($akcip->canDelete()){
			$output['deleted'][] = $akci->getID();
			$akci->delete();		
		}
	}
	echo $json->encode($output);
	
} else { ?>
	<form id="<?php echo $wrapId ?>" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<?php echo $form->hidden('task', 'delete') ?>
    	<?php echo $form->hidden('akCategoryHandle', $_REQUEST['akCategoryHandle']) ?>
        <?php echo $form->hidden('baseId', $baseId) ?>
    
		<?php
            
            $akc = AttributeKeyCategory::getByHandle($_REQUEST['akCategoryHandle']);
            foreach($_REQUEST['akciID'] as $ID) {
                $akci = $akc->getItemObject($ID);
				echo $form->hidden('akciID[]', $akci->getID());
            }            
        ?>
        
        <p>Are you sure you want to delete the selected items?</p>
        
        <?php
        
        	$ih = Loader::helper('concrete/interface');            
            print $ih->submit(t('Yes'));
            print $ih->button_js(t('No'), 'jQuery.fn.dialog.closeTop()', 'LEFT');
		?>
        
        <script type="text/javascript">
            $(function(){
                $("#<?php echo $wrapId ?>").ajaxForm({
                    success:function(){
                        $.fn.dialog.closeTop();
                        $("#<?php echo $baseId ?>_form").submit();
                    }
                });
            });
        </script>
	</form>

<?php } ?>
