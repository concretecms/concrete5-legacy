<?
defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if ( ! $tp->canBackup()) {
	die('Access Denied.');
}

//not working? try $_GET['force']=1
$v = View::getInstance();
$v->setTheme('concrete');
$v->render('/upgrade');
exit;