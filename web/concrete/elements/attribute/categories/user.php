<?php
if (is_object($key)) {
	$uakProfileDisplay = $key->isAttributeKeyDisplayedOnProfile();
	$uakProfileEdit = $key->isAttributeKeyEditableOnProfile();
	$uakProfileEditRequired = $key->isAttributeKeyRequiredOnProfile();
	$uakRegisterEdit = $key->isAttributeKeyEditableOnRegister();
	$uakRegisterEditRequired = $key->isAttributeKeyRequiredOnRegister();
	$uakMemberListDisplay = $key->isAttributeKeyDisplayedOnMemberList();
	$uakIsActive = $key->isAttributeKeyActive();
}
?>
<?php $form = Loader::helper('form'); ?>
<fieldset>
<legend><?php echo t('User Attribute Options')?></legend>
<div class="clearfix">
<label><?php echo t('Public Display')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?php echo $form->checkbox('uakProfileDisplay', 1, $uakProfileDisplay)?> <span><?php echo t('Displayed in Public Profile.');?></span></label></li>
	<li><label><?php echo $form->checkbox('uakMemberListDisplay', 1, $uakMemberListDisplay)?> <span><?php echo t('Displayed on Member List.');?></span></label></li>
</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Edit Mode')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?php echo $form->checkbox('uakProfileEdit', 1, $uakProfileEdit)?> <span><?php echo t('Editable in Profile.');?></span></label></li>
	<li><label><?php echo $form->checkbox('uakProfileEditRequired', 1, $uakProfileEditRequired)?> <span><?php echo t('Editable and Required in Profile.');?></span></label></li>
</ul>
</div>
</div>


<div class="clearfix">
<label><?php echo t('Registration')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?php echo $form->checkbox('uakRegisterEdit', 1, $uakRegisterEdit)?> <span><?php echo t('Show on Registration Form.');?></span></label></li>
	<li><label><?php echo $form->checkbox('uakRegisterEditRequired', 1, $uakRegisterEditRequired)?> <span><?php echo t('Require on Registration Form.');?></span></label></li>
</ul>
</div>
</div>
</fieldset>

<script type="text/javascript">
$(function() {
	$('input[name=uakProfileEdit]').click(function() {
		if ($(this).prop('checked')) {
			$('input[name=uakProfileEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakProfileEditRequired]').attr('checked', false);
			$('input[name=uakProfileEditRequired]').attr('disabled', true);		
		}
	});

	$('input[name=uakRegisterEdit]').click(function() {
		if ($(this).prop('checked')) {
			$('input[name=uakRegisterEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakRegisterEditRequired]').attr('checked', false);
			$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
		}
	});
	

	if ($('input[name=uakProfileEdit]').prop('checked')) {
		$('input[name=uakProfileEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakProfileEditRequired]').attr('checked', false);
		$('input[name=uakProfileEditRequired]').attr('disabled', true);		
	}	

	if ($('input[name=uakRegisterEdit]').prop('checked')) {
		$('input[name=uakRegisterEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakRegisterEditRequired]').attr('checked', false);
		$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
	}	

});
</script>