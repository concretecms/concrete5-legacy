<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Library_ImageFileTypeInspector extends FileTypeInspector {

	/**
	 * @param $fv FileVersion
	 * @return bool
	 */
	public function inspect($fv) {
		$result = false;
		$path = $fv->getPath();
		$size = @getimagesize($path);
		if($size) {
			if($size[0] > 0) {
				$atWidth = FileAttributeKey::getByHandle('width');
				if(is_object($atWidth)) {
					$fv->setAttribute($atWidth, $size[0]);
				}
			}
			if($size[1] > 0) {
				$atHeight = FileAttributeKey::getByHandle('height');
				if(is_object($atHeight)) {
					$fv->setAttribute($atHeight, $size[1]);
				}
			}
			// create a level one and a level two thumbnail
			// load up image helper
			$hi = Loader::helper('image');
			/* @var $hi ImageHelper */
			// Use image helper to create thumbnail at the right size
			$fv->createThumbnailDirectories();
			$hi->create($fv->getPath(), $fv->getThumbnailPath(1), AL_THUMBNAIL_WIDTH, AL_THUMBNAIL_HEIGHT);
			$hi->create($fv->getPath(), $fv->getThumbnailPath(2), AL_THUMBNAIL_WIDTH_LEVEL2, AL_THUMBNAIL_HEIGHT_LEVEL2);
			$result = true;
		}
		return $result;
	}

}
