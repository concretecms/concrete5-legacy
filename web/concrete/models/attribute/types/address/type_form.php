<?php
$co = Loader::helper('lists/countries');
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

if (isset($_POST['akCustomCountries'])) {
	$akCustomCountries = $_POST['akCustomCountries'];
} else if (!is_array($akCustomCountries)) {
	$akCustomCountries = array();
}
if (isset($_POST['akHasCustomCountries'])) {
	$akHasCustomCountries = $_POST['akHasCustomCountries'];
}

?>

<fieldset>
<legend><?php echo t('Address Options')?></legend>

<div class="clearfix">
<label><?php echo t("Available Countries")?></label>
<div class="input">
<ul class="inputs-list">
<li><label><?php echo $form->radio('akHasCustomCountries', 0, $akHasCustomCountries)?> <span><?php echo t('All Available Countries')?></span></label></li>
<li><label><?php echo $form->radio('akHasCustomCountries', 1, $akHasCustomCountries)?> <span><?php echo t('Selected Countries')?></span></label></li>
</ul>
</div>
</div>
<div class="clearfix">
<label></label>
<div class="input">
	<select id="akCustomCountries" name="akCustomCountries[]" multiple size="7" disabled="disabled">
		<?php foreach ($countries as $key=>$val) { ?>
			<?php if (empty($key) || empty($val)) continue; ?>
			<option <?php echo (in_array($key, $akCustomCountries) || $akHasCustomCountries == 0 ?'selected ':'')?>value="<?php echo $key?>"><?php echo $val?></option>
		<?php } ?>
	</select>
</div>
</div>

<div class="clearfix">
<label for="akDefaultCountry"><?php echo t('Default Country')?></label>
<div class="input">
<?php echo $form->select('akDefaultCountry', $countries, $akDefaultCountry)?></div>
</div>

</fieldset>