<?php
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('text');

Loader::model('attribute/categories/user');
$attribs = UserAttributeKey::getRegistrationList();
$assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();

Loader::model("search/group");
$gl = new GroupSearch();
$gl->setItemsPerPage(10000);
$gArray = $gl->getPage();

$locales = Localization::getAvailableInterfaceLanguageDescriptions(ACTIVE_LOCALE);

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add User'), false, false, false);?>

<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?php echo $this->url('/dashboard/users/add')?>">
	<?php echo $valt->output('create_account')?>
	
	<input type="hidden" name="_disableLogin" value="1">

	<div class="ccm-pane-body">
	
    	<table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="2"><?php echo t('User Information')?></th>
                </tr>
            </thead>
            <tbody>
            	<tr>
                    <td><?php echo t('Username')?> <span class="required">*</span></td>
                    <td><?php echo t('Password')?> <span class="required">*</span></td>
                </tr>
                <tr>
					<td><input type="text" name="uName" autocomplete="off" value="<?php echo $th->entities($_POST['uName'])?>" style="width: 95%"></td>
					<td><input type="password" autocomplete="off" name="uPassword" value="" style="width: 95%"></td>
				</tr>
                <tr>
                    <td><?php echo t('Email Address')?> <span class="required">*</span></td>
                    <td><?php if ($assignment->allowEditAvatar()) { ?><?php echo t('User Avatar')?><?php } ?></td>
                </tr>
                <tr>
					<td><input type="text" name="uEmail" autocomplete="off" value="<?php echo $th->entities($_POST['uEmail'])?>" style="width: 95%"></td>
					<td><?php if ($assignment->allowEditAvatar()) { ?><input type="file" name="uAvatar" style="width: 95%"/><?php } ?></td>
				</tr>
                
                
				<?php if (count($locales) > 1) { // "> 1" because en_US is always available ?>
			
				<tr>
					<td colspan="2"><?php echo t('Language')?></td>
				</tr>	
				<tr>
					<td colspan="2">
					<?php print $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
					</td>
				</tr>
                
				<?php } ?>
                
			</tbody>
		</table>

	<?php if (count($attribs) > 0) { ?>
	
        <table class="table table-striped">
        	<thead>
	        	<tr>
            		<th><?php echo t('Registration Data')?></th>
	        	</tr>
			</thead>
            <tbody>
            
			<?php foreach($attribs as $ak) { 
				if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { 
				?>
                <tr>
                    <td class="clearfix">
                    	<label><?php echo $ak->getAttributeKeyDisplayName()?> <?php if ($ak->isAttributeKeyRequiredOnRegister()) { ?><span class="required">*</span><?php } ?></label>
                        <?php $ak->render('form', $caValue, false)?>
                    </td>
                </tr>
                <?php } ?>
            <?php } // END Foreach ?>
        
			</tbody>
        </table>
	
	<?php } ?>

		<table class="inputs-list table-striped table">
        	<thead>
				<tr>
					<th><?php echo t('Groups')?></th>
				</tr>
        	</thead>
            <tbody>
				<tr>
					<td>
                    
					<?php 
					$gak = PermissionKey::getByHandle('assign_user_groups');
					foreach ($gArray as $g) { 
						if ($gak->validate($g['gID'])) {


						?>
						<label>
							<input type="checkbox" name="gID[]" value="<?php echo $g['gID']?>" <?php 
                            if (is_array($_POST['gID'])) {
                                if (in_array($g['gID'], $_POST['gID'])) {
                                    echo(' checked ');
                                }
                            }
                        ?> />
							<span><?php echo h(tc('GroupName', $g['gName']))?></span>
						</label>
                    <?php }
                    
                    
                } ?>
			
					<div id="ccm-additional-groups"></div>
			
					</td>
				</tr>
			</tbody>
		</table>

	</div>

    <div class="ccm-pane-footer">
        <div class="ccm-buttons">
            <input type="hidden" name="create" value="1" />
            <?php print $ih->submit(t('Add'), 'ccm-user-form', 'right', 'primary'); ?>
        </div>	
    </div>

</form>
    
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>