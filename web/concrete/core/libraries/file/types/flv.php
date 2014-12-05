<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Library_FlvFileTypeInspector extends Concrete5_Library_VideoFileTypeInspector {

	/**
	 * @param $fv FileVersion
	 * @return bool
	 */
	public function inspect($fv) {
		$result = parent::inspect($fv);
		if(!$result) {
			$path = $fv->getPath();
			$fp = @fopen($path, 'r');
			if($fp !== false) {
				if(@fseek($fp, 27) !== -1) {
					$onMetaData = fread($fp, 10);
					// if ($onMetaData != 'onMetaData') exit('No meta data available in this file! Fix it using this tool: http://www.buraks.com/flvmdi/');
					if(@fseek($fp, 16, SEEK_CUR) !== -1) {
						$duration = array_shift(unpack('d', strrev(fread($fp, 8))));
						if(@fseek($fp, 8, SEEK_CUR) !== -1) {
							$width = array_shift(unpack('d', strrev(fread($fp, 8))));
							if(@fseek($fp, 9, SEEK_CUR) !== -1) {
								$height = array_shift(unpack('d', strrev(fread($fp, 8))));
									$result = true;
									$atDuration = FileAttributeKey::getByHandle('duration');
									if(is_object($atDuration)) {
										$fv->setAttribute($atDuration, $duration);
									}
									$atWidth = FileAttributeKey::getByHandle('width');
									if(is_object($atWidth)) {
										$fv->setAttribute($atWidth, $width);
									}
									$atHeight = FileAttributeKey::getByHandle('height');
									if(is_object($atHeight)) {
										$fv->setAttribute($atHeight, $height);
									}
							}
						}
					}
				}
			}
		}
		return $result;
	}

}
