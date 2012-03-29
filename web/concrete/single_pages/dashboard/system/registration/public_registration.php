<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Public Registration'), t('Control the options available for Public Registration.'), 'span10 offset3', false);?>
<?php
$h = Loader::helper('concrete/interface');
?>	
    <form method="post" id="registration-type-form" action="<?php echo $this->url('/dashboard/system/registration/public_registration', 'update_registration_type')?>">  
    
    <div class="ccm-pane-body"> 
    	
    	<div class="clearfix">
            <label id="registrationRadioboxes"><?php echo t('Allow visitors to signup as site members?')?></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="disabled" <?php echo ( $registration_type == "disabled" || !strlen($registration_type) )?'checked="checked"':''?> />
			        <span><?php echo t('Off')?></span>
			      </label>
			    </li> 
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="validate_email" <?php echo ( $registration_type == "validate_email" )?'checked="checked"':''?> />
			        <span><?php echo t(' On - email validation')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="manual_approve" <?php echo ( $registration_type == "manual_approve" )?'checked="checked"':''?> />
			        <span><?php echo t('On - approve manually')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="radio" name="registration_type" value="enabled" <?php echo ( $registration_type == "enabled" )?'checked="checked"':''?> />
			        <span><?php echo t('On - signup and go')?></span>
			      </label>
			    </li>  
			  </ul>
			</div>
		</div>  
		
		<div class="clearfix">
            <label id="optionsCheckboxes"><?php echo t('Options')?></label>
            <div class="input">
			  <ul class="inputs-list">
			    <li>
			      <label>
			        <input type="checkbox" name="enable_registration_captcha" value="1" <?php echo ( $enable_registration_captcha )?'checked="checked"':''?> />
			        <span><?php echo t('CAPTCHA required')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			        <input type="checkbox" name="enable_openID" value="1" <?php echo ( $enable_openID )?'checked="checked"':''?> />
			        <span><?php echo t('Enable OpenID')?></span>
			      </label>
			    </li>
			    <li>
			      <label>
			       <input type="checkbox" name="email_as_username" value="1" <?php echo ( $email_as_username )?'checked="checked"':''?> />
			        <span><?php echo t('Use emails for login')?></span>
			      </label>
			    </li>  
			  </ul>
			</div>
        </div>  
	</div>
<div class="ccm-pane-footer">
<? print $h->submit(t('Save'), 'registration-type-form', 'right', 'primary'); ?>
</div>
</form> 	
    
<script type="text/javascript">
$(function() {
	$("input[name=registration_type]").bind('click', function() {
		if (this.value == 'disabled') { 
			$("[name=enable_registration_captcha]").prop({
				'disabled': true,
				'checked': false
			});
		} else {
			$("[name=enable_registration_captcha]").prop({ 'disabled': false });
		}	
	});
	$("input[name=registration_type]:checked").trigger('click');
});
</script>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>