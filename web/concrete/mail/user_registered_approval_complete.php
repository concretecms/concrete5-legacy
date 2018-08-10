<?php
defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE.' '.t('Registration Approved');

/**
 * HTML BODY START
 */
ob_start();

?>
<h2><?php echo t('Welcome to') ?> <?php echo SITE ?></h2>
<?php echo t("Your registration has been approved. You can log into your new account here") ?>:<br />
<br />
<a href="<?php echo BASE_URL.View::url('/login') ?>"><?php echo BASE_URL.View::url('/login') ?></a>
<?php

$bodyHTML = ob_get_clean();
/**
 * HTML BODY END
 *
 * =====================
 *
 * PLAIN TEXT BODY START
 */
ob_start();

?>
<?php echo t('Welcome to') ?> <?php echo SITE ?>

<?php echo t("Your registration has been approved. You can log into your new account here") ?>:

<?php echo BASE_URL.View::url('/login') ?>
<?php

$body = ob_get_clean();
/**
 * PLAIN TEXT BODY END
 */
