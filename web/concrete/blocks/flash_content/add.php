<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>

<div class="clearfix">
<?php echo $form->label('ccm-b-file', t('Flash File'))?>
<div class="input">
<?php echo $al->file('ccm-b-file', 'fID', t('Choose File'));?>

</div>
</div>

<div class="clearfix">
<?php echo $form->label('quality', t('Quality'))?>
<div class="input">
<select name="quality" class="span2">
	<option value="low"><?php echo t('low')?></option>
    <option value="autolow"><?php echo t('autolow')?></option>
    <option value="autohigh"><?php echo t('autohigh')?></option>
    <option value="medium"><?php echo t('medium')?></option>
    <option value="high" selected="selected"><?php echo t('high')?></option>
    <option value="best"><?php echo t('best')?></option>
</select>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('minVersion', t('Minimum Version'))?>
<div class="input">
	<input type="text" name="minVersion" value="8.0" class="span3"/>
</div>
</div>