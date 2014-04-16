<?php defined('C5_EXECUTE') or die("Access Denied.");
class Request extends Concrete5_Library_Request {

	/** 
	 * Gets the current collection path as contained in the current request
	 * @return string
	 */
	public function getRequestCollectionPath() {
		$cPath = parent::getRequestCollectionPath();
		return rawurldecode($cPath);
	}

}
