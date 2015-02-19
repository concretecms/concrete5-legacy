<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Library_VideoFileTypeInspector extends FileTypeInspector {

	/**
	 * @param $fv FileVersion
	 */
	public function inspect($fv) {
		$result = false;
		try {
			Loader::library('3rdparty/getid3/getid3');
			$gi3 = new getID3();
			$info = $gi3->analyze($fv->getPath());
			if(is_array($info) && empty($info['error'])) {
				$duration = $this->getNumber($info, 'playtime_seconds');
				if((!is_null($duration)) && ($duration > 0)) {
					$atDuration = FileAttributeKey::getByHandle('duration');
					if(is_object($atDuration)) {
						$fv->setAttribute($atDuration, $duration);
					}
					$result = true;
				}
				$width = $this->getNumber($info, array('video', 'display_x'));
				if(is_null($width) || ($width <= 0) || ($info['video']['display_unit'] !== 'pixels')) {
					$width = $this->getNumber($info, array('video', 'resolution_x'));
				}
				if((!is_null($width)) && ($width > 0)) {
					$atWidth = FileAttributeKey::getByHandle('width');
					if(is_object($atWidth)) {
						$fv->setAttribute($atWidth, $width);
					}
					$result = true;
				}
				$height = $this->getNumber($info, array('video', 'display_y'));
				if(is_null($height) || ($height <= 0) || ($info['video']['display_unit'] !== 'pixels')) {
					$height = $this->getNumber($info, array('video', 'resolution_y'));
				}
				if((!is_null($height)) && ($height > 0)) {
					$atHeight = FileAttributeKey::getByHandle('height');
					if(is_object($atHeight)) {
						$fv->setAttribute($atHeight, $height);
					}
					$result = true;
				}
			}
		}
		catch(Exception $x) {
		}
		return $result;
	}

	protected function getNumber($info, $keys) {
		if(!is_array($keys)) {
			$keys = array($keys);
		}
		for(;;) {
			$key = array_shift($keys);
			if(is_array($info) && array_key_exists($key, $info)) {
				$info = $info[$key];
				if(empty($keys)) {
					if(is_int($info) || is_float($info)) {
						return $info;
					}
					if(is_string($info) && is_numeric($info)) {
						return floatval($info);
					}
					return null;
				}
			}
			else {
				return null;
			}
		}
	}
}
