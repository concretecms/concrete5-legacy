<?php
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helpful functions for working with navigating Concrete and other sites.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
class NavigationHelper extends Concrete5_Helper_Navigation {
	public function getLinkToCollection(&$cObj, $appendBaseURL = false, $ignoreUrlRewriting = false) {
		// basically returns a link to a collection, based on whether or we have 
		// mod_rewrite enabled, and the collection has a path
		$dispatcher = '';
		if (!defined('URL_REWRITING_ALL') || URL_REWRITING_ALL == false) {
			if ((!URL_REWRITING) || $ignoreUrlRewriting) {
				$dispatcher = '/' . DISPATCHER_FILENAME;
			}
		}
		if ($cObj->isExternalLink() && $appendBaseURL == false) {
			$link = $cObj->getCollectionPointerExternalLink();
			return $link;
		}
		
		if ($cObj->getCollectionPath() != null) {
			$link = DIR_REL . $dispatcher . $cObj->getCollectionPath() . '/';
		} else {
			$_cID = ($cObj->getCollectionPointerID() > 0) ? $cObj->getCollectionPointerOriginalID() : $cObj->getCollectionID();
			if( $cObj->isExternalLink() && $appendBaseURL == false){
				$link = $cObj->getCollectionPointerExternalLink();
			}else if ($_cID > 1) {
				$link = DIR_REL . $dispatcher . '?cID=' . $_cID;
			} else {
				$link = DIR_REL . '/';
			}
		}
		
		if ($appendBaseURL) {
			$link = BASE_URL . $link;
		}
		return $link;
	}
}