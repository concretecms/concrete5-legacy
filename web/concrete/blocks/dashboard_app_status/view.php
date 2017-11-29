<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1><?php echo t('Welcome Back')?></h1>
<br/>

<?php if (version_compare($latest_version, APP_VERSION, '>')) { ?>
<p><span class="label notice"><?php echo t('concrete5 Update')?></span> <?php echo t('The latest version of concrete5 is <strong>%s</strong>. You are currently running concrete5 version <strong>%s</strong>.', $latest_version, APP_VERSION)?> <a class="" href="<?php echo $this->url('/dashboard/system/backup_restore/update')?>"><?php echo t('Learn more and update.')?></a></p>

<?php } else if (version_compare(APP_VERSION, Config::get('SITE_APP_VERSION'), '>')) { ?>
<p><span class="label warning"><?php echo t('concrete5')?></span>
<?php echo t('You have downloaded a new version of concrete5 but have not upgraded to it yet.');?> <a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade"><?php echo t('Update concrete5.')?></a></p>
<?php } ?>

<?php if ($updates > 0) { ?>
	<p><span class="label"><?php echo t('Add-On Updates')?></span> 
	<?php if ($updates == 1) { ?>
		<?php echo t('There is currently <strong>1</strong> update available.')?>
	<?php } else { ?>
		<?php echo t('There are currently <strong>%s</strong> updates available.', $updates)?>
	<?php } ?>
	<a class="" href="<?php echo $this->url('/dashboard/extend/update')?>"><?php echo t('Update add-ons.')?></a></p>
<?php } ?>

