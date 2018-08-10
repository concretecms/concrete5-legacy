<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $form = Loader::helper('form'); ?>
<div class="clearfix">
<?php echo $form->label('cstFilename', t('File to include'))?>
<div class="input">
<select name="filename" id="cstFilename">
	<option value="">** <?php echo t('Select a form')?></option>
<?php foreach($filenames as $filename) {
	echo('<option value="' . $filename . '">' . $file->unfilename($filename) . '</option>');
} ?>
</select>
</div>

<br/>

<div class="help-block">
	<p><?php echo t('This is a list of all files found in your external forms directory: %s', DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL);?></p>
</div>

</div>