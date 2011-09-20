<?php defined('C5_EXECUTE') or die(_("Access Denied."));

$searchInstance = isset($_REQUEST['searchInstance']) ? $_REQUEST['searchInstance'] : NULL;

if ($_REQUEST['task'] == 'delete') {
	if (!ini_get('safe_mode')) {
		@set_time_limit(0);
	}
	//$js = Loader::helper('json');
	//$decoded = $js->decode($_REQUEST['json']);
	$akc = AttributeKeyCategory::getByHandle($_REQUEST['akCategoryHandle']);
	foreach($_REQUEST['akcID'] as $ID) {
		$akci = $akc->getItemObject($ID);
		// REALLY NEED TO CHECK PERMISSIONS HERE
		$akci->delete(); 
	}
} else { ?>
	<form id="<?php echo $searchInstance ?>_delete" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] ?>&task=delete">
    	<?php //$form->hidden('akCategoryHandle', $_REQUEST['akCategoryHandle']) ?>
        <?php //$form->hidden('searchInstance', $_REQUEST['akCategoryHandle']) ?>
		<p>Are you sure you want to delete the selected items?</p>
    
		<?php
            //$js = Loader::helper('json');
            //$decoded = $js->decode($_REQUEST['json']);
            $akc = AttributeKeyCategory::getByHandle($_REQUEST['akCategoryHandle']);
            //foreach($decoded->akcIDs as $ID) {
                //$akci = $akc->getItemObject($ID);
            //}
            $ih = Loader::helper('concrete/interface');
            
            print $ih->button_js(t('Yes'), '$(\'#'.$searchInstance.'_delete\').submit();');
            print $ih->button_js(t('No'), 'jQuery.fn.dialog.closeTop()');
        ?>
        
        <script type="text/javascript">
            $(function(){
                $("#<?php echo $searchInstance ?>_delete").ajaxForm({
                    success:function(){
                        $.fn.dialog.closeTop();	
                        $("#<?php echo $searchInstance ?>_form").submit();
                    }
                });
            });
        </script>
	</form>

<?php } ?>
