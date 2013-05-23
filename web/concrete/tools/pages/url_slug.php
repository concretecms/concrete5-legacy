<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
	Loader::library('3rdparty/urlify');
	$lang = LANGUAGE;
	if (isset($_REQUEST['parentID']) && $multilingual = Package::getByHandle('multilingual') ) {
		$ms = MultilingualSection::getBySectionOfSite(Page::getByID($_REQUEST['parentID']));
		if (is_object($ms)) {
			$lang = $ms->getLanguage();
		}
	}
	$name = URLify::filter($_REQUEST['name'], 60, $lang);

	$ret = Events::fire('on_page_urlify', $_REQUEST['name']);
	if ($ret) {
  		$name = $ret;
	}

	echo $name;
}
