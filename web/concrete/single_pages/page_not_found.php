<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1 class="error"><?=t('Page Not Found')?></h1>

<?=t('No page could be found at this address.')?>

<?php if (is_object($c)) { ?>
	<br/><br/>
	<?php $a = new Area("Main"); $a->display($c); ?>
<?php } ?>

<br/><br/>

<a href="<?=DIR_REL?>/"><?=t('Back to Home')?></a>.