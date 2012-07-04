<?php     
defined('C5_EXECUTE') or die("Access Denied.");

$passwordStr = ($data['uPassword'] != '') ? '<strong>Password :</strong> '.$data['uPassword'].'<br/>' : '';

$bodyHTML = t("
Hi,
<br/><br/>
You will find below your User Information from %s :
<br/><br/>
<strong>Username :</strong> %s <br/>
%s
<strong>Email Address :</strong> %s <br/><br/>
You can sign in at %s", SITE, $data['uName'], $passwordStr, $data['uEmail'], BASE_URL.DIR_REL.'/login');