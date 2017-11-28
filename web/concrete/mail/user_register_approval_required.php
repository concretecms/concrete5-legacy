<?php
defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE.' '.t("Registration - Approval Required");

/**
 * HTML BODY START
 */
ob_start()

?>
<h2><?php echo t('Registration Approval Required') ?></h2>
<?php echo t('A new user has registered on your website. This account must be approved before it is active and may login.') ?><br />
<?php echo t('User Name') ?>: <b><?php echo $uName ?></b><br />
<?php echo t('Email') ?>: <b><?php echo $uEmail ?></b><br />
<br />
<?php echo t('You may approve or remove this user account here:') ?><br />
<a href="<?php echo BASE_URL.View::url('/dashboard/users/search?uID='.$uID) ?>"><?php echo BASE_URL.View::url('/dashboard/users/search?uID='.$uID) ?></a>
<?php if($attribs): ?>
	<ul>
	<?php foreach($attribs as $item): ?>
		<li><?php echo $item ?></li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
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
<?php echo t('Registration Approval Required') ?>

<?php echo t('A new user has registered on your website. This account must be approved before it is active and may login.') ?>

<?php echo t('User Name') ?>: <?php echo $uName ?>

<?php echo t('Email Address') ?>: <?php echo $uEmail ?>

<?php if($attribs): ?>
	<?php foreach($attribs as $item): ?>
		<?php echo $item ?>

	<?php endforeach ?>
<?php endif ?>

<?php echo t('You may approve or remove this user account here') ?>:

<?php echo BASE_URL . View::url('/dashboard/users/search?uID=' . $uID) ?>
<?php

$body = ob_get_clean();
/**
 * PLAIN TEXT BODY END
 */
