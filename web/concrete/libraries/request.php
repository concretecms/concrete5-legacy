<?php defined('C5_EXECUTE') or die("Access Denied.");
class Request extends Concrete5_Library_Request {
	public function getRequestCollectionPath() {
		// I think the regexps take care of the trimming for us but just to be sure..
		$cPath = trim($this->cPath, '/');
		if ($cPath != '') {
			return '/' . rawurldecode($cPath);
		}
		return '';
	}
}
