<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 

<div class="control-group">
<?php echo $form->label('title', t('Title'))?>
<div class="controls">
	<input type="text" name="title" value="<?php echo $title?>" />
</div>
</div>
<?php
if (!$dateFormat) {
	$dateFormat = t('M jS, Y');
}
?>

<div class="control-group">
<?php echo $form->label('dateFormat', t('Date Format'))?>
<div class="controls">
<input type="text" name="dateFormat" value="<?php echo $dateFormat?>" />
<div class="help-block">(<?php echo t('Enter a <a href="%s" target="_blank">PHP date string</a> here.', 'http://www.php.net/date')?>)</div>
</div>
</div>

<div class="control-group">
<?php echo $form->label('displayGuestBookForm', t('Comments enabled.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="displayGuestBookForm" value="1" <?php echo ($displayGuestBookForm?"checked=\"checked\"":"") ?> /> <span><?php echo t('Yes')?></span></label>
	<label class="radio"><input type="radio" name="displayGuestBookForm" value="0" <?php echo ($displayGuestBookForm?"":"checked=\"checked\"") ?> /> <span><?php echo t('No')?></span></label>

</div>
</div>

<div class="control-group">
<?php echo $form->label('requireApproval', t('Comments require approval.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="requireApproval" value="1" <?php echo ($requireApproval?"checked=\"checked\"":"") ?> /> <span><?php echo t('Yes')?></span></label>
	<label class="radio"><input type="radio" name="requireApproval" value="0" <?php echo ($requireApproval?"":"checked=\"checked\"") ?> /> <span><?php echo t('No')?></span></label>

</div>
</div>

<div class="control-group">
<?php echo $form->label('authenticationRequired', t('Authentication required.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="authenticationRequired" value="0" <?php echo ($authenticationRequired?"":"checked=\"checked\"") ?> /> <span><?php echo t('Email Only')?></span></label>
	<label class="radio"><input type="radio" name="authenticationRequired" value="1" <?php echo ($authenticationRequired?"checked=\"checked\"":"") ?> /> <span><?php echo t('Users must login')?></span></label>

</div>
</div>

<div class="control-group">
<?php echo $form->label('displayCaptcha', t('CAPTCHA Required.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="displayCaptcha" value="1" <?php echo ($displayCaptcha?"checked=\"checked\"":"") ?> /> <span><?php echo t('Yes')?></span></label>
	<label class="radio"><input type="radio" name="displayCaptcha" value="0" <?php echo ($displayCaptcha?"":"checked=\"checked\"") ?> /> <span><?php echo t('No')?></span></label>

</div>
</div>

<div class="control-group">
<?php echo $form->label('notifyEmail', t('Notify Email on Comment'))?>
<div class="controls">
<input type="text" name="notifyEmail" value="<?php echo $notifyEmail?>" />
</div>
</div>