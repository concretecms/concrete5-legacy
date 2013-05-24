<<<<<<< HEAD
<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
	$text = Loader::helper('text');
	$name = $text->urlify($_REQUEST['name']);
	$ret = Events::fire('on_page_urlify', $_REQUEST['name']);
	if ($ret) {
  		$name = $ret;
	}

	echo $name;
}
=======
<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
	Loader::library('3rdparty/urlify');
	print URLify::filter($_REQUEST['name'],60,LANGUAGE);
}
>>>>>>> 058572c68a6a45ab50bde7e1041ff39e54279f4c
