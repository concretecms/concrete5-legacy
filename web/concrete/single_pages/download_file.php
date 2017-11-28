<?php 

defined('C5_EXECUTE') or die("Access Denied.");

?>

<h1><?php echo t('Download File')?></h1>

<?php if (!isset($filename)) { ?>

	<p><?php echo t("Invalid File.");?></p>

<?php } else { ?>
	
	<p><?php echo t('This file requires a password to download.')?></p>
	
	<?php if (isset($error)) {  ?>
		<div class="ccm-error-response"><?php echo $error?></div>
	<?php } ?>
	
	<form action="<?php echo View::url('/download_file', 'submit_password', $fID) ?>" method="post">
		<?php if(isset($force)) { ?>
			<input type="hidden" value="<?php echo $force ?>" name="force" />
		<?php } ?>
		<input type="hidden" value="<?php echo $rcID ?>" name="rcID"/>
		<label for="password"><?php echo t('Password')?>: <input type="password" name="password" /></label>
		<br /><br />
		<button type="submit"><?php echo t('Download')?></button>
	</form>

<?php } ?>

<?php if (is_object($rc)) { ?>
<p><a href="<?php echo Loader::helper('navigation')->getLinkToCollection($rc)?>">&lt; <?php echo t('Back')?></a></p>
<?php } ?>
