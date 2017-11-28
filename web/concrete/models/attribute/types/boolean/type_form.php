<fieldset>
<legend><?php echo t('Checkbox Options')?></legend>

<div class="clearfix">
<label><?php echo t("Default Value")?></label>
<div class="input">
<ul class="inputs-list">
<li><label><?php echo $form->checkbox('akCheckedByDefault', 1, $akCheckedByDefault)?> <span><?php echo t('The checkbox will be checked by default.')?></span></label></li>
</ul>
</div>
</div>

</fieldset>