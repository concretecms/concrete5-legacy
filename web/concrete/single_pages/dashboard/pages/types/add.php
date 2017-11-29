<?php
defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$form = Loader::helper('form');
$u = new User();

Loader::model('file_set');
$pageTypeIconsFS = FileSet::getByName("Page Type Icons");

?>
	
    <!-- START: Add Page Type pane -->
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Page Type'), false, false, false);?>
	
    <form method="post" class="form-horizontal" id="add_page_type" action="<?php echo $this->url('/dashboard/pages/types/add', 'do_add')?>">
	<?php echo $valt->output('add_page_type')?>
    <?php echo $form->hidden('task', 'add'); ?>
	
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
                        <?php echo $form->text('ctName', $_POST['ctName'], array('style' => 'width: 100%'))?>
                    </td>
                    <td>
                        <?php echo $form->text('ctHandle', $_POST['ctHandle'], array('style' => 'width: 100%'))?>
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
                            if (isset($_POST['ctIcon']) && $_POST['ctIcon'] == $ic->getFileID()) {
                                $checked = 'checked';
                            } else {
                                if ($first) { 
                                    $checked = 'checked';
                                }
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
                            if (isset($_POST['ctIcon']) && $_POST['ctIcon'] == $ic) {
                                $checked = 'checked';
                            } else {
                                if ($first) { 
                                    $checked = 'checked';
                                }
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
                    <th colspan="3" class="subheader"><?php echo t('Default Attributes to Display')?></th>
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
                            <label class="">
                                <input type="checkbox" name="akID[]" value="<?php echo $ak->getAttributeKeyID()?>" <?php echo (isset($_POST['akID']) && is_array($_POST['akID']) && in_array($ak->getAttributeKeyID(), $_POST['akID'])) ? 'checked' : ''; ?> />
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
                    <?php }
                ?></tr>
        	<?php } ?>
        	</tbody>
        </table>
	
	</div>
    
    <div class="ccm-pane-footer">
        <?php print $ih->submit(t('Add'), 'add_page_type', 'right', 'primary'); ?>
        <?php print $ih->button(t('Cancel'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
    </form>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    
    <!-- END Add Page Type pane -->