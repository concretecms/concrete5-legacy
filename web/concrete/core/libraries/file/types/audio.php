<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Library_AudioFileTypeInspector extends FileTypeInspector {

	/**
	 * @param $fv FileVersion
	 * @return bool
	 */
	public function inspect($fv) {
		$result = false;
		try {
			Loader::library('3rdparty/getid3/getid3');
			$gi3 = new getID3();
			$info = $gi3->analyze($fv->getPath());
			if(is_array($info) && empty($info['error'])) {
				if(
					is_int($info['playtime_seconds'])
					||
					is_float($info['playtime_seconds'])
					||
					(is_string($info['playtime_seconds']) && is_numeric($info['playtime_seconds']))
				) {
					$duration = floatval($info['playtime_seconds']);
					if($duration > 0) {
						$atDuration = FileAttributeKey::getByHandle('duration');
						if(is_object($atDuration)) {
							$fv->setAttribute($atDuration, $duration);
						}
						$result = true;
					}
				}
			}
		}
		catch(Exception $x) {
		}
		return $result;
	}
}
