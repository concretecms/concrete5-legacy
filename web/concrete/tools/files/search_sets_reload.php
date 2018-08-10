<?php
defined('C5_EXECUTE') or die("Access Denied.");

$cp = FilePermissions::getGlobal();
if (!$cp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}

Loader::model('file_list');
Loader::model('file_set');

$fileList = new FileList();
$fileList->enableStickySearchRequest();
$req = $fileList->getSearchRequest();
$form = Loader::helper('form');

$s1 = FileSet::getMySets();
if (count($s1) > 0) { ?>
		<div class="span4" style="width: 280px">
	<?php echo $form->label('fsID', t('In Set(s)'))?>
	<div class="input">
		<select multiple name="fsID[]" class="chosen-select">
			<?php foreach($s1 as $s) { ?>
				<option value="<?php echo $s->getFileSetID()?>"><?php echo $s->getFileSetName()?></option>
			<?php } ?>
			<optgroup label="<?php echo t('Other')?>">
				<option value="-1" <?php if (is_array($req['fsID']) && in_array(-1, $req['fsID'])) { ?> selected="selected" <?php } ?>><?php echo t('Files in no sets.')?></option>
			</optgroup>
		</select>
	</div>
</div>
<?php } ?>