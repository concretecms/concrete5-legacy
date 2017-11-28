<?php
defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE.' '.t("Registration - A New User Has Registered");

/**
 * HTML BODY START
 */
ob_start();

?>
<h2><?php echo t('New User Registration') ?></h2>
<?php echo t('A new user has registered on your website.') ?><br />
<br />
<?php echo t('User Name') ?>: <b><?php echo $uName ?></b><br />
<?php echo t('Email Address') ?>: <b><?php echo $uEmail ?></b><br />
<br />
<?php if($attribs): ?>
	<ul>
	<?php foreach($attribs as $item): ?>
		<li><?php echo $item ?></li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
<br />
<?php t('This account may be managed directly at') ?><br />
<a href="<?php echo BASE_URL.View::url('/dashboard/users/search?uID='.$uID) ?>"><?php echo BASE_URL.View::url('/dashboard/users/search?uID='.$uID) ?></a>
<?php

$bodyHTML = ob_get_clean();
/**
 * HTML BODY END
 *
 * ======================
 *
 * PLAIN TEXT BODY START
 */
ob_start();

?>
<?php echo t('New User Registration') ?>

<?php echo t('A new user has registered on your website.') ?>

<?php echo t('User Name') ?>: <?php echo $uName ?>

<?php echo t('Email Address') ?>: <?php echo $uEmail ?>

<?php if($attribs): ?>
	<?php foreach($attribs as $item): ?>
		<?php echo $item ?>

	<?php endforeach ?>
<?php endif ?>

<?php t('This account may be managed directly at') ?>

<?php echo BASE_URL.View::url('/dashboard/users/search?uID='.$uID) ?>
<?php

$body = ob_get_clean();
ob_end_clean();
/**
 * PLAIN TEXT BODY END
 */
