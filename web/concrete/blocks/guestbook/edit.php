<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?=t('Title')?><br />
<input type="text" name="title" value="<?=$title?>" /><br /><br />
<?
if (!$dateFormat) {
	$dateFormat = t('M jS, Y');
}
?>
<?=t('Date Format')?><br/>
<input type="text" name="dateFormat" value="<?=$dateFormat?>" />
<div class="ccm-note">(<?=t('Enter a <a href="%s" target="_blank">PHP date string</a> here.', 'http://www.php.net/date')?>)</div>
<br/>

<?=t('Comments Require Moderator Approval?')?><br/>
<input type="radio" name="requireApproval" value="1" <?=($requireApproval?"checked=\"checked\"":"") ?> /> <?=t('Yes')?><br />
<input type="radio" name="requireApproval" value="0" <?=($requireApproval?"":"checked=\"checked\"") ?> /> <?=t('No')?><br /><br />

<?=t('Posting Comments is Enabled?')?><br/>
<input type="radio" name="displayGuestBookForm" value="1" <?=($displayGuestBookForm?"checked=\"checked\"":"") ?> /> <?=t('Yes')?><br />
<input type="radio" name="displayGuestBookForm" value="0" <?=($displayGuestBookForm?"":"checked=\"checked\"") ?> /> <?=t('No')?><br /><br />

<?=t('Authentication Required to Post')?><br/>
<input type="radio" name="authenticationRequired" value="0" <?=($authenticationRequired?"":"checked=\"checked\"") ?> /> <?=t('Email Only')?><br />
<input type="radio" name="authenticationRequired" value="1" <?=($authenticationRequired?"checked=\"checked\"":"") ?> /> <?=t('Users must login to C5')?><br /><br />

<?=t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha')?><br/>
<input type="radio" name="displayCaptcha" value="1" <?php echo ($displayCaptcha?"checked=\"checked\"":"") ?> /><?php echo t('Yes')?><br />
<input type="radio" name="displayCaptcha" value="0" <?php echo ($displayCaptcha?"":"checked=\"checked\"") ?> /> <?php echo t('No')?><br /><br />

<?=t('Alert Email Address when Comment Posted')?><br/>
<input name="notifyEmail" type="text" value="<?=$notifyEmail?>" size="30" /><br /><br />
<input id="ccm-guestbook-closed-comments-on" type="checkbox" name="closedComments" class="closedComments" value="<?php echo $closedComments; ?>" <?php echo ($closedComments?"checked=\"checked\"":"")?>/> <?=t('Enable Closed Comments')?> 	   &nbsp;&nbsp;
	   <br /><br />
<div id="ccm-guestbook-closed-comments">
<?php echo t('Close comments in:');?>
<table>
<tr>
<td><?php echo t('Years')?></td>
<td><?php echo t('Months')?></td>
<td><?php echo t('Weeks')?></td>
<td><?php echo t('Days')?></td>
<td><?php echo t('Hours')?></td>
</tr>
<tr>
<td><input name="inYears" type="number" min="0" id="inYears" value="<?php echo $inYears;?>" size="3"/></td>
<td><input name="inMonths" type="number" min="0" id="inMonths" value="<?php echo $inMonths;?>"size="3"/></td>
<td><input name="inWeeks" type="number" min="0" id="inWeeks" value="<?php echo $inWeeks;?>" size="3"/></td>
<td><input name="inDays" type="number" min="0" id="inDays" value="<?php echo $inDays;?>" size="3"/></td>
<td><input name="inHours" type="number" min="0" id="inHours" value="<?php echo $inHours;?>" size="3"/></td>
</tr>
</table><br />
</div>

