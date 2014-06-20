<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
* Helpful functions for working with HTML content.
*/
class Concrete5_Helper_Content {

	/** Builds the final HTML code starting from what has been saved in DB
	* @param string $text
	* @return string
	*/
	public function translateFrom($text) {
		// old stuff. Can remove in a later version.
		$text = str_replace('href="{[CCM:BASE_URL]}', 'href="' . BASE_URL . DIR_REL, $text);
		$text = str_replace('src="{[CCM:REL_DIR_FILES_UPLOADED]}', 'src="' . BASE_URL . REL_DIR_FILES_UPLOADED, $text);
		// we have the second one below with the backslash due to a screwup in the
		// 5.1 release. Can remove in a later version.
		$text = preg_replace(
			array(
				'/{\[CCM:BASE_URL\]}/i',
				'/{CCM:BASE_URL}/i'
			),
			array(
				BASE_URL . DIR_REL,
				BASE_URL . DIR_REL
			),
			$text
		);
		// Links to pages
		$text = preg_replace_callback(
			'/{CCM:CID_([0-9]+)}/i',
			array($this, 'replaceCollectionID'),
			$text
		);
		// URLs of images sources
		$text = preg_replace_callback(
			'/<img [^>]*src\s*=\s*"{CCM:FID_([0-9]+)}"[^>]*>/i',
			array($this, 'replaceImageID'),
			$text
		);
		// Links to files to view inline
		$text = preg_replace_callback(
			'/{CCM:FID_([0-9]+)}/i',
			array($this, 'replaceFileID'),
			$text
		);
		// Links to files to download
		$text = preg_replace_callback(
			'/{CCM:FID_DL_([0-9]+)}/i',
			array($this, 'replaceDownloadFileID'),
			$text
		);
		// All done
		return $text;
	}

	/** Builds the HTML code to be used in Edit Mode starting from what has been saved in DB
	* @param string $text
	* @return string
	*/
	public function translateFromEditMode($text) {
		// Links to pages
		$text = preg_replace(
			'/{CCM:CID_([0-9]+)}/i',
			BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=\\1',
			$text
		);
		// Links to files to view inline
		$text = preg_replace_callback(
			'/{CCM:FID_([0-9]+)}/i',
			array($this, 'replaceFileIDInEditMode'),
			$text
		);
		// Links to files to download
		$text = preg_replace_callback(
			'/{CCM:FID_DL_([0-9]+)}/i',
			array($this, 'replaceDownloadFileIDInEditMode'),
			$text
		);
		// All done
		return $text;
	}

	/** Builds the HTML code to be saved in DB starting from what has been inserted in Edit Mode
	* @param string $text
	* @return string
	*/
	public function translateTo($text) {
		// keep links valid
		$url1 = str_replace('/', '\/', BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME);
		$url2 = str_replace('/', '\/', BASE_URL . DIR_REL);
		$url3 = View::url('/download_file', 'view_inline');
		$url3 = str_replace('/', '\/', $url3);
		$url3 = str_replace('-', '\-', $url3);
		$url4 = View::url('/download_file', 'view');
		$url4 = str_replace('/', '\/', $url4);
		$url4 = str_replace('-', '\-', $url4);
		$text = preg_replace(
			array(
				'/' . $url1 . '\?cID=([0-9]+)/i',
				'/' . $url3 . '([0-9]+)\//i',
				'/' . $url4 . '([0-9]+)\//i',
				'/' . $url2 . '/i'
			),
			array(
				'{CCM:CID_\\1}',
				'{CCM:FID_\\1}',
				'{CCM:FID_DL_\\1}',
				'{CCM:BASE_URL}'
			),
			$text
		);
		return $text;
	}

	/** Builds the HTML code to be saved in DB starting from text specified during an import process
	* @param string $text
	* @return string
	*/
	public function import($text) {
		$text = preg_replace_callback(
			'/\{ccm:export:page:(.*?)\}/i',
			array($this, 'replacePagePlaceHolderOnImport'),
			$text
		);
		$text = preg_replace_callback(
			'/\{ccm:export:image:(.*?)\}/i',
			array($this, 'replaceImagePlaceHolderOnImport'),
			$text
		);
		$text = preg_replace_callback(
			'/\{ccm:export:file:(.*?)\}/i',
			array($this, 'replaceFilePlaceHolderOnImport'),
			$text
		);
		$text = preg_replace_callback(
			'/\{ccm:export:define:(.*?)\}/i',
			array($this, 'replaceDefineOnImport'),
			$text
		);
		return $text;
	}

	/** Builds the HTML code to be exported starting from what has been saved in DB
	* @param string $text
	* @return string
	*/
	public function export($text) {
		$text = preg_replace_callback(
			'/{CCM:CID_([0-9]+)}/i',
			array('ContentExporter', 'replacePageWithPlaceHolderInMatch'),
			$text
		);
		$text = preg_replace_callback(
			'/{CCM:FID_([0-9]+)}/i',
			array('ContentExporter', 'replaceImageWithPlaceHolderInMatch'),
			$text
		);
		$text = preg_replace_callback(
			'/{CCM:FID_DL_([0-9]+)}/i',
			array('ContentExporter', 'replaceFileWithPlaceHolderInMatch'),
			$text
		);
		return $text;
	}

	protected function replaceCollectionID($match) {
		$cID = $match[1];
		if ($cID > 0) {
			$c = Page::getByID($cID, 'ACTIVE');
			return Loader::helper('navigation')->getLinkToCollection($c);
		}
	}

	protected function replaceImageID($match) {
		$fID = $match[1];
		if ($fID > 0) {
			preg_match('/width\s*="([0-9]+)"/',$match[0],$matchWidth);
			preg_match('/height\s*="([0-9]+)"/',$match[0],$matchHeight);
			$file = File::getByID($fID);
			if (is_object($file) && (!$file->isError())) {
				$imgHelper = Loader::helper('image');
				$maxWidth = ($matchWidth[1]) ? $matchWidth[1] : $file->getAttribute('width');
				$maxHeight = ($matchHeight[1]) ? $matchHeight[1] : $file->getAttribute('height');
				if ($file->getAttribute('width') > $maxWidth || $file->getAttribute('height') > $maxHeight) {
					$thumb = $imgHelper->getThumbnail($file, $maxWidth, $maxHeight);
					return preg_replace('/{CCM:FID_([0-9]+)}/i', $thumb->src, $match[0]);
				}
			}
			return $match[0];
		}
	}

	protected function replaceFileID($match) {
		$fID = $match[1];
		if ($fID > 0) {
			$path = File::getRelativePathFromID($fID);
			return $path;
		}
	}

	protected function replaceFileIDInEditMode($match) {
		$fID = $match[1];
		return View::url('/download_file', 'view_inline', $fID);
	}

	protected function replaceDownloadFileID($match) {
		$fID = $match[1];
		if ($fID > 0) {
			$c = Page::getCurrentPage();
			if (is_object($c)) {
				return View::url('/download_file', 'view', $fID, $c->getCollectionID());
			} else {
				return View::url('/download_file', 'view', $fID);
			}
		}
	}

	protected function replaceDownloadFileIDInEditMode($match) {
		$fID = $match[1];
		if ($fID > 0) {
			return View::url('/download_file', 'view', $fID);
		}
	}

	protected static function replacePagePlaceHolderOnImport($match) {
		$cPath = $match[1];
		if ($cPath) {
			$pc = Page::getByPath($cPath);
			if(is_object($pc) && (!$pc->isError())) {
				return '{CCM:CID_' . $pc->getCollectionID() . '}';
			}
		}
		return '{CCM:CID_1}';
	}

	protected static function replaceImagePlaceHolderOnImport($match) {
		$filename = $match[1];
		$db = Loader::db();
		$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($filename));
		return '{CCM:FID_' . $fID . '}';
	}

	protected static function replaceFilePlaceHolderOnImport($match) {
		$filename = $match[1];
		$db = Loader::db();
		$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($filename));
		return '{CCM:FID_DL_' . $fID . '}';
	}

	protected static function replaceDefineOnImport($match) {
		$define = $match[1];
		if (defined($define)) {
			$r = get_defined_constants();
			return $r[$define];
		}
	}

}
