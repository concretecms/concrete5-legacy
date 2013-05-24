<?
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$valc = Loader::helper('concrete/validation');
$form = Loader::helper('form');
$ctArray = CollectionType::getList();
$args['section'] = 'collection_types';
$u = new User();
$json = Loader::helper('json');
$interface = Loader::helper('concrete/interface');

Loader::model('file_set');
$pageTypeIconsFS = FileSet::getByName("Page Type Icons");

if ($_GET['cID'] && $_GET['task'] == 'load_master') { 
	$u->loadMasterCollectionEdit($_GET['cID'], 1);
	header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit');
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

if ($_POST['update']) {
	$ctName = Loader::helper("text")->entities($_POST['ctName']);
	$ctHandle = Loader::helper('text')->entities($_POST['ctHandle']);
	$vs = Loader::helper('validation/strings');
	
	$error = array();
	if (!$ctHandle) {
		$error[] = t("Handle required.");
	} else if (!$vs->handle($ctHandle)) {
		$error[] = t('Handles must contain only letters, numbers or the underscore symbol.');
	}
	
	if (!$ctName) {
		$error[] = t("Name required.");
	} else if (!$vs->alphanum($ctName, true)) {
		$error[] = t('Page type names can only contain letters, numbers and spaces.');
	}
	
	if (!$valt->validate('update_page_type')) {
		$error[] = $valt->getErrorMessage();
	}
	
	$akIDArray = $_POST['akID'];
	if (!is_array($akIDArray)) {
		$akIDArray = array();
	}
	
	if (count($error) == 0) {
		try {
			if (is_object($ct)) {
				$ct->update($_POST);
				$this->controller->redirect('/dashboard/pages/types', 'page_type_updated');
			}		
			exit;
		} catch(Exception $e1) {
			$error[] = $e1->getMessage();
		}
	}
}

if ($_REQUEST['updated']) {
	$message = t('Page Type updated.');
}


?>

<?
if ($ctEditMode) { 
	$ct->populateAvailableAttributeKeys();
	?>
	
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Page Type').'<span class="label" style="position:relative;top:-3px;left:12px;">'.t('* required field').'</span>', false, false, false);?>
    
    <form class="form-horizontal" method="post" id="update_page_type" action="<?=$this->url('/dashboard/pages/types/')?>">
	<?=$valt->output('update_page_type')?>
    <?=$form->hidden('ctID', $_REQUEST['ctID']); ?>
    <?=$form->hidden('task', 'edit'); ?>
    <?=$form->hidden('update', '1'); ?>
    
	<div class="ccm-pane-body">
		
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="header"><?=t('Name')?> <span class="required">*</span></th>
                    <th class="header"><?=t('Handle')?> <span class="required">*</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 60%">
                        <?=$form->text('ctName', $ctName, array('style' => 'width:100%'))?>
                    </td>
                    <td>
                        <?=$form->text('ctHandle', $ctHandle, array('style' => 'width:100%'))?>
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="subheader">
                    <?=t('Icon')?>
                    <?
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
                        
                            <? 
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
                                    <input type="radio" name="ctIcon" value="<?= $ic->getFileID() ?>" style="vertical-align: middle" <?=$checked?> />
                                    <img src="<?= $fv->getRelativePath(); ?>" width="<?=COLLECTION_TYPE_ICON_WIDTH?>" height="<?=COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                                    </label>
                                <? 
                                } else {
                                    $checked = false;
                                    if ($ct->getCollectionTypeIcon() == $ic || $first) { 
                                        $checked = 'checked';
                                    }
                                    $first = false;
                                    ?>
                                    <label class="checkbox inline">
                                    <input type="radio" name="ctIcon" value="<?= $ic ?>" style="vertical-align: middle" <?=$checked?> />
                                        <img src="<?=REL_DIR_FILES_COLLECTION_TYPE_ICONS.'/'.$ic;?>" width="<?=COLLECTION_TYPE_ICON_WIDTH?>" height="<?=COLLECTION_TYPE_ICON_HEIGHT?>" style="vertical-align: middle" />
                                    </label>
                                <?
                                }
                            
                            } ?>
                        
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table class="table" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="3" class="subheader"><?= t('Default Attributes'); ?></th>
                </tr>
			</thead>
            <tbody>
                    <?
                    $attribs = CollectionAttributeKey::getList();
                    $i = 0;
                    foreach($attribs as $ak) { 
                    if ($i == 0) { ?>
                        <tr class="inputs-list">
                    <? } ?>
                    
                            <td width="33%">
                            <label>
                            <input type="checkbox" name="akID[]" value="<?=$ak->getAttributeKeyID()?>" <? if (($this->controller->isPost() && in_array($ak->getAttributeKeyID(), $akIDArray))) { ?> checked <? } else if ((!$this->controller->isPost()) && $ct->isAvailableCollectionTypeAttribute($ak->getAttributeKeyID())) { ?> checked <? } ?> />
                            <span><?=$ak->getAttributeKeyName()?></span>
                            </label>
                            </td>
                    
                    <? $i++;
                    
                    if ($i == 3) { ?>
                        </tr>
                    <? 
                    $i = 0;
                    }
                    
                    }
                
                    if ($i < 3 && $i > 0) {
                        for ($j = $i; $j < 3; $j++) { ?>
                            <td>&nbsp;</td>
                        <? } ?>
                        </tr>
                    <? } ?>
            </tbody>
        </table>
	</div>

    <script type="text/javascript">
	deletePageType = function() {
		$.ajax(<?=$json->encode($this->url('/dashboard/pages/types/', 'get_delete_info', $ct->getCollectionTypeID(), $valt->generate('get_delete_info')))?>, {
			dataType: 'json',
			type: 'post',
			data: {ctID: <?=$ct->getCollectionTypeID(); ?>},
			error: function(r) {
				alert(r.responseText);
			},
			success: function(r) {
				if(typeof r.error != "undefined") {
					alert(r.error);
				} else {
					if(r.usage == 0) {
						if(confirm('<?=t('Are you sure?'); ?>')){
							location.href = "<?=$this->url('/dashboard/pages/types/', 'delete', $ct->getCollectionTypeID(), $valt->generate('delete_page_type')); ?>";
						}
					}
					else if(!r.replace_existing_with.length) {
						alert("<?=t("This page type can't be deleted since it is in use and it is the only defined page type."); ?>");
					}
					else {
						var $dialog;
						$dialog = $('<div></div>')
							.append('<p><?=t('This page type is currently associated to some page.').'<br />'.t('In order to delete the page type you have to specify the new page type to use for these pages:'); ?></p>')
							.append(r.replace_existing_with)
						;
						$(document.body).append($dialog);
						$dialog.dialog({
							width: 500,
							title: "<?= t('Page type in use'); ?>",
							dialogClass: "ccm-ui",
							buttons: [
								{
									"text": "<?php echo t('Delete page type'); ?>",
									"class": "btn error do-delete",
									"click": function() {
										deletePageTypeWithReplace();
									}
								}	
							],
							close: function() { $dialog.remove(); }
						});
					}
				}
			}
		});
	}
	deletePageTypeWithReplace = function() {
		var $s, url;
		$s = $("#replace_existing_with");
		if((url = $s.val()) == "") {
			$s.focus();
			return;
		}
		if(confirm('<?=t('Are you sure?'); ?>')){
			location.href = url;
		}
	};
	</script>
    
    <div class="ccm-pane-footer">
        <? print $ih->submit(t('Save'), 'update_page_type', 'right', 'primary'); ?>
		<? print $ih->button_js(t('Delete'), "deletePageType()", 'right', 'error'); ?>
        <? print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
    </form>
    
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    
<? } else { ?>
    <!-- START: Default Page Types pane -->
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Page Types'), false, false);?>
	
	<div class="clearfix">
       <? print $ih->button(t('Add a Page Type'), $this->url('/dashboard/pages/types/add'), 'right'); ?>
       <br/><br/>
	</div>
	
	<? if (count($ctArray) == 0) { ?>
		<br/><strong><?=t('No page types found.')?></strong><br/><br>
	<? } else { ?>
	
	<table border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped">
    	<thead>
            <tr>
                <th width="100%"><?=t('Name')?></th>
                <th><?=t('Handle')?></th>
                <th><?=t('Package')?></th>
                <th <? if ($cap->canAccessComposer()) { ?>colspan="3"<? } else { ?>colspan="2"<? } ?>></th>
            </tr>
		</thead>
		<tbody>
            <? foreach ($ctArray as $ct) { ?>
            <tr>
                <td><?=$ct->getCollectionTypeName()?></td>
                <td><?=$ct->getCollectionTypeHandle()?></td>
                <td><?
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
                <? if ($ct->getMasterCollectionID()) {?>
                    <?
                    $tp = new TaskPermission();
                    if ($tp->canAccessPageDefaults()) { ?>
                        <? print $ih->button(t('Defaults'), $this->url('/dashboard/pages/types?cID=' . $ct->getMasterCollectionID() . '&task=load_master'), 'left','small')?>
                    <? } else { 
                        $defaultsErrMsg = t('You do not have access to page type default content.');
                        ?>
                        <? print $ih->button_js(t('Defaults'), "alert('" . $defaultsErrMsg . "')", 'left', 'small ccm-button-inactive', array('title'=>t('Lets you set default permissions and blocks for a particular page type.')) );?>
                    <? } ?>
                <? } ?>
            
                </td>
                
                <td><? print $ih->button(t('Settings'), $this->url('/dashboard/pages/types?ctID=' . $ct->getCollectionTypeID() . '&task=edit'), 'left','small')?></td>
                <? if ($cap->canAccessComposer()) { ?>
                    <td><? print $ih->button(t('Composer'), $this->url('/dashboard/pages/types/composer', 'view', $ct->getCollectionTypeID()), 'left', 'small')?></td>
                <? } ?>	
            </tr>
            <? } ?>
		</tbody>
	</table>
	
	<? } ?>
   
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
    
    <!-- END: Default Page Type pane -->
	
<? } ?>