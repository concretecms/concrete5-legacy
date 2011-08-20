<?php 

defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE . " " . t("account activated");
$body = t("Your account has been just activated.");

$body .= t("

Please use the following url to login to your user account

%s", BASE_URL . View::url('/login'));