
<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
$bf = null;
if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
}
?>

<div class="clearfix">
<?php echo $form->label('ccm-b-file', t('Flash File'))?>
<div class="input">
<?php echo $al->file('ccm-b-file', 'fID', t('Choose File'), $bf);?>

</div>
</div>

<div class="clearfix">
<?php echo $form->label('quality', t('Quality'))?>
<div class="input">
<select name="quality" class="span2">
	<option value="low" <?php echo ($quality == "low"?"selected=\"selected\"":"")?>><?php echo t('low')?></option>
    <option value="autolow" <?php echo ($quality == "autolow"?"selected=\"selected\"":"")?>><?php echo t('autolow')?></option>
    <option value="autohigh" <?php echo ($quality == "autohigh"?"selected=\"selected\"":"")?>><?php echo t('autohigh')?></option>
    <option value="medium" <?php echo ($quality == "medium"?"selected=\"selected\"":"")?>><?php echo t('medium')?></option>
    <option value="high" <?php echo ($quality == "high"?"selected=\"selected\"":"")?>><?php echo t('high')?></option>
    <option value="best" <?php echo ($quality == "best"?"selected=\"selected\"":"")?>><?php echo t('best')?></option>
</select>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('minVersion', t('Minimum Version'))?>
<div class="input">
	<input type="text" name="minVersion" value="<?php echo $minVersion?>" class="span3"/>
</div>
</div>