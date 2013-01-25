<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for working with images.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * Now includes cropping functionality (thanks to Jordan Lev and Kirk Roberts)
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Image {
	
	private $options = null;
	private $newAbsPath = null;
	private $newRelPath = null;
	
	/**
	 * Creates a new image given an original path, a new path, a target width and height.
	 * Optionally crops image to exactly match given width and height.
	 * @params string $originalPath, string $newpath, int $width, int $height, bool $crop
	 * @return void
	 */
	
	public function create($originalPath, $newPath = null, $width, $height, $crop = false) {
		
		if ( !file_exists($originalPath) ) return false;
		
		//build the file object before GD processing to determine if file already exists
		$this->options = array_merge($this->options, array(
			'width' => $width,
			'height' => $height,
			'resized' => true,
			'cropped' => $crop,
			'imageSize' => getimagesize($originalPath),
			'originalPath' => $originalPath
		));
		
		//generate new filepath
		$exists = $this->path();
		
		//check if file already exists
		if( $exists ) {
			
			return $this->newAbsPath;
			
		} else {
			
			$res = $this->process();
			$res = $this->resize($res);
			
			if($crop){
				$res = $this->crop($res);
			}
			
			return $this->cache($res) ? $this->newAbsPath : false;
			
		}
	}
	
	/**
	 * Create cache file path if no path set and return false if file already exists
	 * @return bool
	 */
	 
	protected function path( $fID = false ) {
	
		if( isset($this->newAbsPath) ){
			$path = $this->newAbsPath;
		} else {
			$filename = md5(serialize(sort($this->options)));
			
			$path = DIR_FILES_CACHE . '/' . $filename;
			$relpath = REL_DIR_FILES_CACHE . '/' . $filename;
			
			if ( isset($this->options['fID']) ) {
				$relpath .= '_fID-'.$this->options['fID'];
				$path .= '_fID-'.$this->options['fID'];
			}
			
			$path .= image_type_to_extension($this->options['imageSize'][2]);
			$relpath .= image_type_to_extension($this->options['imageSize'][2]);
			
			$this->newAbsPath = $path;
			$this->newRelPath = $relpath;
		}
		
		return file_exists($path);
		
	}
	
	/**
	 * Generates a cached version of the image resource, optionally with an fID associated.
	 * @param resource $res
	 * @return bool
	 */
	
	protected function cache($res) {
		
		switch($this->options['imageSize'][2]) {
			case IMAGETYPE_GIF:
				$file = imagegif($res, $this->newAbsPath);
				break;
			case IMAGETYPE_JPEG:
				$compression = defined('AL_THUMBNAIL_JPEG_COMPRESSION') ? AL_THUMBNAIL_JPEG_COMPRESSION : 80;
				$file = imagejpeg($res, $this->newAbsPath, $compression);
				break;
			case IMAGETYPE_PNG:
				$file = imagepng($res, $this->newAbsPath);
				break;
		}
		
		@chmod($this->newAbsPath, FILE_PERMISSIONS_MODE);
		imagedestroy($res);
		
		return file_exists($this->newAbsPath);
		
	}
	
	/**
	 * Create a image resource from a file path.
	 * @return resource $res
	 */
	protected function process() {

		switch($this->options['imageSize'][2]) {
			case IMAGETYPE_GIF:
				$res = @imagecreatefromgif($this->options['originalPath']);
				break;
			case IMAGETYPE_JPEG:
				$res = @imagecreatefromjpeg($this->options['originalPath']);
				break;
			case IMAGETYPE_PNG:
				$res = @imagecreatefrompng($this->options['originalPath']);
				break;
		}
		
		return $res;
		
	}
	
	/**
	 * Resizes an image resource, defaults to the maximum length
	 * @params resource $res, bool $max
	 * @return resource $image
	 */
	protected function resize($res, $max = true) {
		
		$oWidth = imagesx($res);
		$oHeight = imagesy($res);
		
		$width = min($oWidth, $this->options['width']);
		$height = min($oHeight, $this->options['height']);
		
		$finalWidth = 0;
		$finalHeight = 0;
		
		$x_ratio = $oWidth / $oHeight; 
		$y_ratio = $oHeight / $oWidth;
		
		if ($oWidth == $width && $oHeight == $height) {
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
		} else {
			if ($max) {
				if ( $width < $height ) {
					$finalHeight = $height;
					$finalWidth = round($height * $x_ratio);
				} else {
					$finalWidth = $width;
					$finalHeight = round($width * $y_ratio);
				}
			} else {
				if ( $width < $height ) {
					$finalWidth = $width;
					$finalHeight = round($width * $y_ratio);
				} else {
					$finalHeight = $height;
					$finalWidth = round($height * $x_ratio);
				}
			}
		}
		
		//create "canvas" to put new resized image into
		$image = imagecreatetruecolor($finalWidth, $finalHeight);
		
		// Better transparency - thanks for the ideas and some code from mediumexposure.com
		if (($type == IMAGETYPE_GIF) || ($type == IMAGETYPE_PNG)) {
			$trnprt_indx = imagecolortransparent($res);
			
			// If we have a specific transparent color
			if ($trnprt_indx >= 0 && $trnprt_indx < imagecolorstotal($im)) {
		
				// Get the original image's transparent color's RGB values
				$trnprt_color = imagecolorsforindex($image, $trnprt_indx);
				
				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate($image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				
				// Completely fill the background of the new image with allocated color.
				imagefill($image, 0, 0, $trnprt_indx);
				
				// Set the background color for new image to transparent
				imagecolortransparent($image, $trnprt_indx);
				
			
			} else if ($type == IMAGETYPE_PNG) {
				
				// Turn off transparency blending (temporarily)
				imagealphablending($image, false);
				
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($image, 0, 0, 0, 127);
				
				// Completely fill the background of the new image with allocated color.
				imagefill($image, 0, 0, $color);
				
				// Restore transparency blending
				imagesavealpha($image, true);
				
			}
		}
		
		imagecopyresampled($image, $res, 0, 0, 0, 0, $finalWidth, $finalHeight, $oWidth, $oHeight);
		
		return $image;
		
	}
		
	/**
	 * Crops an image
	 * @param resource $res
	 * @return resource $image
	 */
	protected function crop($res) {
	
		$width = $this->options['width'];
		$height = $this->options['height'];
		$oWidth = imagesx($res);
		$oHeight = imagesy($res);
		$finalWidth = 0; //For cropping, this is really "scale to width before chopping extra height"
		$finalHeight = 0; //For cropping, this is really "scale to height before chopping extra width"
		$do_crop_x = false;
		$do_crop_y = false;
		$crop_src_x = 0;
		$crop_src_y = 0;
		
		if ($oWidth < $width && $oHeight < $height) {
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
			$width = $oWidth;
			$height = $oHeight;
		} else if ($height >= $oHeight && $width <= $oWidth) {
			//crop to width only -- don't scale anything
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
			$height = $oHeight;
			$do_crop_x = true;
		} else if ($width >= $oWidth && $height <= $oHeight) {
			//crop to height only -- don't scale anything
			$finalHeight = $oHeight;
			$finalWidth = $oWidth;
			$width = $oWidth;
			$do_crop_y = true;
		} else {
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
			$width = $oWidth;
			$height = $oHeight;
			$do_crop_x = true;
			$do_crop_y = true;
		}
		
		//Calculate cropping to center image
		if ($do_crop_x) {
			/*
			//Get half the difference between scaled width and target width,
			// and crop by starting the copy that many pixels over from the left side of the source (scaled) image.
			$nudge = ($width / 10); //I have *no* idea why the width isn't centering exactly -- this seems to fix it though.
			$crop_src_x = ($finalWidth / 2.00) - ($width / 2.00) + $nudge;
			*/
			$crop_src_x = round(($oWidth - ($width * $oHeight / $height)) * 0.5);
		}
		if ($do_crop_y) {
			/*
			//Calculate cropping...
			//Get half the difference between scaled height and target height,
			// and crop by starting the copy that many pixels down from the top of the source (scaled) image.
			$crop_src_y = ($finalHeight / 2.00) - ($height / 2.00);
			*/
			$crop_src_y = round(($oHeight - ($height * $oWidth / $width)) * 0.5);
		}
		
		//create "canvas" to put new resized and/or cropped image into
		$image = imagecreatetruecolor($width, $height);
		
		// Better transparency - thanks for the ideas and some code from mediumexposure.com
		if (($this->options['imageSize'][2] == IMAGETYPE_GIF) || ($this->options['imageSize'][2] == IMAGETYPE_PNG)) {
			$trnprt_indx = imagecolortransparent($res);
			
			// If we have a specific transparent color
			if ($trnprt_indx >= 0 && $trnprt_indx < imagecolorstotal($im)) {
		
				// Get the original image's transparent color's RGB values
				$trnprt_color = imagecolorsforindex($image, $trnprt_indx);
				
				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate($image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				
				// Completely fill the background of the new image with allocated color.
				imagefill($image, 0, 0, $trnprt_indx);
				
				// Set the background color for new image to transparent
				imagecolortransparent($image, $trnprt_indx);
				
			
			} else if ($this->options['imageSize'][2] == IMAGETYPE_PNG) {
				
				// Turn off transparency blending (temporarily)
				imagealphablending($image, false);
				
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($image, 0, 0, 0, 127);
				
				// Completely fill the background of the new image with allocated color.
				imagefill($image, 0, 0, $color);
				
				// Restore transparency blending
				imagesavealpha($image, true);
				
			}
		}
		
		imagecopyresampled($image, $res, 0, 0, $crop_src_x, $crop_src_y, $finalWidth, $finalHeight, $oWidth, $oHeight);
		
		return $image;
	} 
	
	/** 
	 * Returns a path to the specified item, resized and/or cropped to meet max width and height. $obj can either be
	 * a string (path) or a file object. 
	 * Returns an object with the following properties: src, width, height
	 * @params mixed $obj, int $maxWidth, int $maxHeight, bool $crop
	 */
	public function getThumbnail($obj, $maxWidth, $maxHeight, $crop = false) {
	
		if ($obj instanceof File) {
			$path = $obj->getPath();
			$this->options['fID'] = $obj->getFileID();
		} else {
			$path = $obj;
		}
		
		$image = $this->create($path, null, $maxWidth, $maxHeight, $crop);
		
		if ($image) {
			
			$thumb = new stdClass;
			$thumb->src = $this->newRelPath;
			$dimensions = getimagesize($image);
			$thumb->width = $dimensions[0];
			$thumb->height = $dimensions[1];
			
			return $thumb;
				
		} else {
		
			return $image;
			
		}
	}
	
	/** 
	 * Runs getThumbnail on the path, and then prints it out as an XHTML image
	 */
	public function outputThumbnail($obj, $maxWidth, $maxHeight, $alt = null, $return = false, $crop = false) {
		$thumb = $this->getThumbnail($obj, $maxWidth, $maxHeight, $crop);
		$html = '<img class="ccm-output-thumbnail" alt="' . $alt . '" src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" />';
		if ($return) {
			return $html;
		} else {
			print $html;
		}
	}
	
	public function output($obj, $alt = null, $return = false) {
		$s = @getimagesize($obj->getPath());
		$html = '<img class="ccm-output-image" alt="' . $alt . '" src="' . $obj->getRelativePath() . '" width="' . $s[0] . '" height="' . $s[1] . '" />';
		if ($return) {
			return $html;
		} else {
			print $html;
		}
	}
}
