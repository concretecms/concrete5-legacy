<?php defined('C5_EXECUTE') or die('Access Denied.');

foreach(PackageList::get()->getPackages() as $p) {
	if($p->isPackageInstalled()) {
		$pkg = Loader::package($p->getPackageHandle());
		if(is_object($pkg)) {
			if(method_exists($pkg, 'warmUp')) {
				$pkg->warmUp();
			}
		}
	}
}