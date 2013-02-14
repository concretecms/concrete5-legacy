<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');
$view = View::getInstance();

if (isset($cp)) {
	if ($cp->canViewToolbar()) { 

?>

<style type="text/css">html {margin-top: 49px !important;} </style>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?
$dh = Loader::helper('concrete/dashboard');
if (!$dh->inDashboard()) {
	$view->addHeaderItem($html->css('ccm.app.css'));
	if (MOBILE_THEME_IS_ACTIVE == true) {
		$view->addHeaderItem($html->css('ccm.app.mobile.css'));
	}
	$view->addHeaderItem($html->css('jquery.ui.css'));
	$view->addFooterItem('<div id="ccm-page-controls-wrapper"><div id="ccm-toolbar"></div></div>');
	
	$view->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
	$view->addHeaderItem($html->javascript('jquery.js'));
	$view->addFooterItem($html->javascript('jquery.ui.js'));
	$view->addFooterItem($html->javascript('jquery.form.js'));
	$view->addFooterItem($html->javascript('jquery.rating.js'));
	$view->addFooterItem($html->javascript('bootstrap.js'));
	$view->addFooterItem($html->javascript('ccm.app.js'));
	if (ENABLE_PROGRESSIVE_PAGE_REINDEX && Config::get('DO_PAGE_REINDEX_CHECK')) {
		$view->addHeaderItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
	}
	$cih = Loader::helper('concrete/interface');
	if (LANGUAGE != 'en') {
		$view->addFooterItem($html->javascript('i18n/ui.datepicker-' . LANGUAGE . '.js'));
		$view->addFooterItem('<script type="text/javascript">$(function() { jQuery.datepicker.setDefaults({dateFormat: \'yy-mm-dd\'}); });</script>');
	}
	if (!Config::get('SEEN_INTRODUCTION')) {
		$view->addHeaderItem('<script type="text/javascript">$(function() { ccm_showAppIntroduction(); });</script>');
		Config::save('SEEN_INTRODUCTION', 1);
	}
	$view->addFooterItem($html->javascript('tiny_mce/tiny_mce.js'));
}

$cID = ($c->isAlias()) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();
$btask = '';
if (Loader::helper('validation/strings')->alphanum($_REQUEST['btask'])) {
	$btask = $_REQUEST['btask'];
}
$view->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/page_controls_menu_js?cID=' . $cID . '&amp;cvID=' . $cvID . '&amp;btask=' . $btask . '&amp;ts=' . time() . '"></script>'); 

	}
	
}