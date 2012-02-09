<?
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');
$v = View::getInstance();

if (isset($cp)) {
	if ($dh->canRead() || $cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) {

?>

<style type="text/css">body {margin-top: 49px !important;} </style>

<script type="text/javascript">
<?
$valt = Loader::helper('validation/token');
print "var CCM_SECURITY_TOKEN = '" . $valt->generate() . "';";
?>
</script>

<?
$dh = Loader::helper('concrete/dashboard');
if (!$dh->inDashboard()) {
	$v->addHeaderItem($html->css('ccm.app.css'));
	$v->addHeaderItem($html->css('jquery.ui.css'));
	$v->addFooterItem('<div id="ccm-page-controls-wrapper"><div id="ccm-toolbar"></div></div>');
	
	$v->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>');
	$v->addHeaderItem($html->javascript('jquery.js'));
	$v->addFooterItem($html->javascript('jquery.ui.js'));
	$v->addFooterItem($html->javascript('jquery.form.js'));
	$v->addFooterItem($html->javascript('jquery.rating.js'));
	$v->addFooterItem($html->javascript('ccm.app.js'));
	if (ENABLE_PROGRESSIVE_PAGE_REINDEX && Config::get('DO_PAGE_REINDEX_CHECK')) {
		$v->addHeaderItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
	}
	$cih = Loader::helper('concrete/interface');
	if (ACTIVE_LOCALE != 'en_US') {
		$dlocale = str_replace('_', '-', ACTIVE_LOCALE);
		$v->addFooterItem($html->javascript('i18n/ui.datepicker-' . $dlocale . '.js'));
		$v->addFooterItem('<script type="text/javascript">$(function() { jQuery.datepicker.setDefaults({dateFormat: \'yy-mm-dd\'}); });</script>');
	}
	if (!Config::get('SEEN_INTRODUCTION')) {
		$v->addHeaderItem('<script type="text/javascript">$(function() { ccm_showAppIntroduction(); });</script>');
		Config::save('SEEN_INTRODUCTION', 1);
	}
	$v->addFooterItem($html->javascript('tiny_mce/tiny_mce.js'));
}

$cID = ($c->isAlias()) ? $c->getCollectionPointerOriginalID() : $c->getCollectionID();

$v->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/page_controls_menu_js?cID=' . $cID . '&amp;cvID=' . $cvID . '&amp;btask=' . $_REQUEST['btask'] . '&amp;ts=' . time() . '"></script>');

	}
	
}