<?php 

defined('C5_EXECUTE') or die("Access Denied.");

if (REDIRECT_TO_BASE_URL == true && C5_ENVIRONMENT_ONLY == false) {
	$protocol = 'http://';
	$base_url = BASE_URL;
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
		$protocol = 'https://';
		if (defined('BASE_URL_SSL')) {
			$base_url_ssl = BASE_URL_SSL;
		} else {
			// No real way to get around this: we want to check the URL before the page cache check, but
			// we also want to be able to grab the URL.
			// So right now we are getting rid of this config check. @TODO –split these checks into separate
			// files so that they can be checked properly, one before the full page cache check, one after when we have
			// the database.
//			$base_url_ssl = Config::get('BASE_URL_SSL');
		}
		if (isset($base_url_ssl)) {
			$base_url = $base_url_ssl;
		}
	}

	$uri = Loader::helper('security')->sanitizeURL($_SERVER['REQUEST_URI']);
	if (strpos($uri, '%7E') !== false) {
		$uri = str_replace('%7E', '~', $uri);
	}

	if (($base_url != $protocol . $_SERVER['HTTP_HOST']) && ($base_url . ':' . $_SERVER['SERVER_PORT'] != 'https://' . $_SERVER['HTTP_HOST'])) {
		header('HTTP/1.1 301 Moved Permanently');  
		header('Location: ' . $base_url . $uri);
		exit;
	}

}