<?php    
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('text');
$fh = Loader::helper('form');

Loader::model('attribute/categories/user');
$attribs = UserAttributeKey::getRegistrationList();

Loader::model("search/group");
$gl = new GroupSearch();
$gl->setItemsPerPage(10000);
$gArray = $gl->getPage();

$languages = Localization::getAvailableInterfaceLanguages();

?>

<?php    echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add User'), false, false, false);?>

<script type="text/javascript">
	function GeneratePassword() {
	    var length = 8;
	    var sPassword = "";
	    	    
	    for (i=0; i < length; i++) {
	        numI = getRandomNum();
	        while (checkPunc(numI)) { numI = getRandomNum(); }
	        sPassword = sPassword + String.fromCharCode(numI);
	    }
	    
	    document.getElementById('ccm-user-form').uPassword.value = sPassword;
	    document.getElementById('generatedPassword').innerHTML = 'Generated Password is : <strong>' + sPassword + '</strong>';
	    
	    return true;
	}
	
	function getRandomNum() {
	    var rndNum = Math.random();  
	    rndNum = parseInt(rndNum * 1000);      
	    rndNum = (rndNum % 94) + 33;     
	    return rndNum;
	}
	
	function checkPunc(num) {
	    if ((num >=33) && (num <=47)) { return true; }
	    if ((num >=58) && (num <=64)) { return true; }    
	    if ((num >=91) && (num <=96)) { return true; }
	    if ((num >=123) && (num <=126)) { return true; }
	    return false;
	}
</script>

<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?php    echo $this->url('/dashboard/users/add')?>">
	<?php    echo $valt->output('create_account')?>
	
	<input type="hidden" name="_disableLogin" value="1">

	<div class="ccm-pane-body">
	
    	<table border="0" cellspacing="0" cellpadding="0" width="100%">
            <thead>
                <tr>
                    <th colspan="2"><?php    echo t('User Information')?></th>
                </tr>
            </thead>
            <tbody>
            	<tr>
                    <td><?php    echo t('Username')?> <span class="required">*</span></td>
                    <td><?php    echo t('Password')?> <span class="required">*</span> </span></td>
                </tr>
                <tr>
					<td><input type="text" name="uName" autocomplete="off" value="<?php    echo $th->entities($_POST['uName'])?>" style="width: 95%"></td>
					<td><input type="password" autocomplete="off" name="uPassword" value="" style="width: 95%"> <?php    print $ih->button_js(t('Generate Password'), 'GeneratePassword();', 'left', 'info', array('style'=>'margin-top:5px; float:left;')); ?> <br/> <span id="generatedPassword" style="float: left; color: #C1C1C1; display: block; margin: 10px 0 0 10px;" ></span></td>
				</tr>
                <tr>
                    <td><?php    echo t('Email Address')?> <span class="required">*</span></td>
                    <td><?php    echo t('User Avatar')?></td>
                </tr>
                <tr>
					<td><input type="text" name="uEmail" autocomplete="off" value="<?php    echo $th->entities($_POST['uEmail'])?>" style="width: 95%"></td>
					<td><input type="file" name="uAvatar" style="width: 95%"/></td>
				</tr>
				<tr>
					<td colspan="2"><?php    echo t('Send User Information to Email Address'); ?>.<br/><?php    print $fh->checkbox('send_user_info', '1', false); ?> <?php    echo t('Yes'); ?></td>
				</tr>
                
                
				<?php     if (count($languages) > 0) { ?>
			
				<tr>
					<td colspan="2"><?php    echo t('Language')?></td>
				</tr>	
				<tr>
					<td colspan="2">
					<?php    
						array_unshift($languages, 'en_US');
						$locales = array();
						$locales[''] = t('** Default');
						Loader::library('3rdparty/Zend/Locale');
						Loader::library('3rdparty/Zend/Locale/Data');
						Zend_Locale_Data::setCache(Cache::getLibrary());
						foreach($languages as $lang) {
							$loc = new Zend_Locale($lang);
							$locales[$lang] = Zend_Locale::getTranslation($loc->getLanguage(), 'language', ACTIVE_LOCALE);
						}
						print $form->select('uDefaultLanguage', $locales);
					?>
					</td>
				</tr>
                
				<?php     } ?>
                
			</tbody>
		</table>

	<?php     if (count($attribs) > 0) { ?>
	
        <table border="0" cellspacing="0" cellpadding="0" width="100%" class="zebra-striped">
        	<thead>
	        	<tr>
            		<th><?php    echo t('Registration Data')?></th>
	        	</tr>
			</thead>
            <tbody class="inputs-list">
            
			<?php     foreach($attribs as $ak) { ?>
                <tr>
                    <td class="clearfix">
                    	<label><?php    echo $ak->getAttributeKeyName()?> <?php     if ($ak->isAttributeKeyRequiredOnRegister()) { ?><span class="required">*</span><?php     } ?></label>
                        <?php     $ak->render('form', $caValue, false)?>
                    </td>
                </tr>
            <?php     } // END Foreach ?>
        
			</tbody>
        </table>
	
	<?php     } ?>

		<table border="0" cellspacing="0" cellpadding="0" width="100%" class="inputs-list zebra-striped">
        	<thead>
				<tr>
					<th><?php    echo t('Groups')?></th>
				</tr>
        	</thead>
            <tbody>
				<tr>
					<td>
                    
					<?php     foreach ($gArray as $g) { ?>
						<label>
							<input type="checkbox" name="gID[]" value="<?php    echo $g['gID']?>" <?php     
                            if (is_array($_POST['gID'])) {
                                if (in_array($g['gID'], $_POST['gID'])) {
                                    echo(' checked ');
                                }
                            }
                        ?> />
							<span><?php    echo $g['gName']?></span>
						</label>
                    <?php     } ?>
			
					<div id="ccm-additional-groups"></div>
			
					</td>
				</tr>
			</tbody>
		</table>

	</div>

    <div class="ccm-pane-footer">
        <div class="ccm-buttons">
            <input type="hidden" name="create" value="1" />
            <?php     print $ih->submit(t('Add'), 'ccm-user-form', 'right', 'primary'); ?>
        </div>	
    </div>

</form>
    
<?php    echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>