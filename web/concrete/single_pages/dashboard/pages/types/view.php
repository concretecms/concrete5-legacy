<?php
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$valc = Loader::helper('concrete/validation');
$form = Loader::helper('form');
$ctArray = CollectionType::getList();
$args['section'] = 'collection_types';
$u = new User();

Loader::model('file_set');
$pageTypeIconsFS = FileSet::getByName("Page Type Icons");

$cID = Loader::helper('security')->sanitizeInt($_GET['cID']);

if ($cID && $_GET['task'] == 'load_master') { 
	$u->loadMasterCollectionEdit($cID, 1);
	header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&mode=edit');
	exit;
}

if ($_REQUEST['task'] == 'edit') {
	$ct = CollectionType::getByID($_REQUEST['ctID']);
	if (is_object($ct)) { 		
			
		$ctName = $ct->getCollectionTypeName();
		$ctHandle = $ct->getCollectionTypeHandle();		
		$ctName = Loader::helper("text")->entities($ctName);
		$ctHandle = Loader::helper('text')->entities($ctHandle);

		$ctEditMode = true;
	}
}

?>

<?php
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();

        $akIDArray = $_POST['akID'];
        if (!is_array($akIDArray)) {
            $akIDArray = array();
        }

	?>
	
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Page Type').'<span class="label" style="position:relative;top:-3px;left:12px;">'.t('* required field').'</span>', false, false, false);?>
    
    <form class="form-horizontal" method="post" id="update_page_type" action="<?php echo $this->url('/dashboard/pages/types/', 'update')?>">
	<?php echo $valt->output('update_page_type')?>
    <?php echo $form->hidden('ctID', $_REQUEST['ctID']); ?>
    <?php echo $form->hidden('task', 'edit'); ?>
    <?php echo $form->hidden('update', '1'); ?>
    
	<div class="ccm-pane-body">
		
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="header"><?php echo t('Name')?> <span class="required">*</span></th>
                    <th class="header"><?php echo t('Handle')?> <span class="required">*</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 60%">
                        <?php echo $form->text('ctName', $ctName, array('style' => 'width:100%'))?>
                    </td>
                    <td>
                        <?php echo $form->text('ctHandle', $ctHandle, array('style' => 'width:100%'))?>
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="subheader">
                    <?php echo t('Icon')?>
                    <?php
                        if (!is_object($pageTypeIconsFS)) {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(To add your own page type icons, create a file set named "%s" and add files to that set)', 'Page Type Icons');
                            print '</span>';
                        } else {
                            print '<span style="margin-left: 4px; color: #aaa">';
                            print t('(Pulling icons from file set "%s". Icons will be displayed at %s x %s.)', 'Page Type Icons', COLLECTION_TYPE_ICON_WIDTH, COLLECTION_TYPE_ICON_HEIGHT);
                            print '</span>';
                        }
                    ?>
                    </th>
                </tr>
			</thead>
            <tbody>
                <tr>
                    <td>
                        
                            <?php 
                            $first = true;
                            foreach($icons as $ic) { 
                                if(is_object($ic)) {
                                    $fv = $ic->getApprovedVersion(); 
                                    $checked = false;
                                    if ($ct->getCollectionTypeIcon() == $ic->getFileID() || $first) { 
                                        $checked = 'checked';
                                    }
                                    $first = false;
                                    ?>
                                    <label class="checkbox inline">
                                    <input type="radio" name="ctIcon" value="<?php echo $ic->getFileID() ?>" style="vertical-align: middle" <?php echo $checked?> />
                                    <img src="<?php echo $fv->getRelativePath(); ?>" width="<?php echo COLLECTION_TYPE_ICON_WIDTH?>" height="<?php echo COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                                    </label>
                                <?php 
                                } else {
                                    $checked = false;
                                    if ($ct->getCollectionTypeIcon() == $ic || $first) { 
                                        $checked = 'checked';
                                    }
                                    $first = false;
                                    ?>
                                    <label class="checkbox inline">
                                    <input type="radio" name="ctIcon" value="<?php echo $ic ?>" style="vertical-align: middle" <?php echo $checked?> />
                                        <img src="<?php echo REL_DIR_FILES_COLLECTION_TYPE_ICONS.'/'.$ic;?>" width="<?php echo COLLECTION_TYPE_ICON_WIDTH?>" height="<?php echo COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                                    </label>
                                <?php
                                }
                            
                            } ?>
                        
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="3" class="subheader"><?php echo t('Default Attributes'); ?></th>
                </tr>
			</thead>
            <tbody>
                    <?php
                    $attribs = CollectionAttributeKey::getList();
                    $i = 0;
                    foreach($attribs as $ak) { 
                    if ($i == 0) { ?>
                        <tr class="inputs-list">
                    <?php } ?>
                    
                            <td width="33%">
                            <label>
                            <input type="checkbox" name="akID[]" value="<?php echo $ak->getAttributeKeyID()?>" <?php if (($this->controller->isPost() && in_array($ak->getAttributeKeyID(), $akIDArray))) { ?> checked <?php } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getAttributeKeyID())) { ?> checked <?php } ?> />
                            <span><?php echo $ak->getAttributeKeyDisplayName()?></span>
                            </label>
                            </td>
                    
                    <?php $i++;
                    
                    if ($i == 3) { ?>
                        </tr>
                    <?php 
                    $i = 0;
                    }
                    
                    }
                
                    if ($i < 3 && $i > 0) {
                        for ($j = $i; $j < 3; $j++) { ?>
                            <td>&nbsp;</td>
                        <?php } ?>
                        </tr>
                    <?php } ?>
            </tbody>
        </table>
	</div>

    <?php $confirmMsg = t('Are you sure?'); ?>
	<script type="text/javascript">
	deletePageType = function() {
		if(confirm('<?php echo $confirmMsg?>')){ 
			location.href="<?php echo $this->url('/dashboard/pages/types/','delete',$_REQUEST['ctID'], $valt->generate('delete_page_type'))?>";
		}	
	}
	</script>
    
    <div class="ccm-pane-footer">
        <?php print $ih->submit(t('Save'), 'update_page_type', 'right', 'primary'); ?>
		<?php print $ih->button_js(t('Delete'), "deletePageType()", 'right', 'error'); ?>
        <?php print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
    </form>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    
<?php } else { ?>
    <!-- START: Default Page Types pane -->
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Types'), false, false);?>
	
	<div class="clearfix">
       <?php print $ih->button(t('Add a Page Type'), $this->url('/dashboard/pages/types/add'), 'right'); ?>
       <br/><br/>
	</div>
	
	<?php if (count($ctArray) == 0) { ?>
		<br/><strong><?php echo t('No page types found.')?></strong><br/><br>
	<?php } else { ?>
	
	<table border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped">
    	<thead>
            <tr>
                <th width="100%"><?php echo t('Name')?></th>
                <th><?php echo t('Handle')?></th>
                <th><?php echo t('Package')?></th>
                <th <?php if ($cap->canAccessComposer()) { ?>colspan="3"<?php } else { ?>colspan="2"<?php } ?>></th>
            </tr>
		</thead>
		<tbody>
            <?php foreach ($ctArray as $ct) { ?>
            <tr>
                <td><?php echo $ct->getCollectionTypeName()?></td>
                <td><?php echo $ct->getCollectionTypeHandle()?></td>
                <td><?php
                    $package = false;
                    if ($ct->getPackageID() > 0) {
                        $package = Package::getByID($ct->getPackageID());
                    }
                    if (is_object($package)) {
                        print $package->getPackageName(); 
                    } else {
                        print t('None');
                    }
                    ?></td>
                <td>
                <?php if ($ct->getMasterCollectionID()) {?>
                    <?php
                    $tp = new TaskPermission();
                    if ($tp->canAccessPageDefaults()) { ?>
                        <?php print $ih->button(t('Defaults'), $this->url('/dashboard/pages/types?cID=' . $ct->getMasterCollectionID() . '&task=load_master'), 'left','small')?>
                    <?php } else { 
                        $defaultsErrMsg = t('You do not have access to page type default content.');
                        ?>
                        <?php print $ih->button_js(t('Defaults'), "alert('" . $defaultsErrMsg . "')", 'left', 'small ccm-button-inactive', array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
                    <?php } ?>
                <?php } ?>
            
                </td>
                
                <td><?php print $ih->button(t('Settings'), $this->url('/dashboard/pages/types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'), 'left','small')?></td>
                <?php if ($cap->canAccessComposer()) { ?>
                    <td><?php print $ih->button(t('Composer'), $this->url('/dashboard/pages/types/composer', 'view', $ct->getCollectionTypeID()), 'left', 'small')?></td>
                <?php } ?>	
            </tr>
            <?php } ?>
		</tbody>
	</table>
	
	<?php } ?>
   
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
    
    <!-- END: Default Page Type pane -->
	
<?php } ?>