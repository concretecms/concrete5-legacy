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
	
	public $fileobj = null;
	
	/**
	 * Creates a new image given an original path, a new path, a target width and height.
	 * Optionally crops image to exactly match given width and height.
	 * @params string $originalPath, string $newpath, int $width, int $height, bool $crop
	 * @return void
	 */
	
	public function create($originalPath, $newPath, $width, $height, $crop = false) {
	
		$imageSize = getimagesize($originalPath);
		
		$res = $this->process($originalPath);
		$res = $this->resize($res, $imageSize[2], $maxWidth, $maxHeight);
		if($crop){
			$res = $this->crop($res, $imageSize[2], $maxWidth, $maxHeight);
		}
		
		$this->cache($res, $imageSize[2]);
		
	}
	
	/**
	 * Generates a cached version of the image resource, optionally with an fID associated.
	 * @params resource $res, int $type, string $fID
	 * @return bool|string
	 */
	
	private function cache($res, $type, $fID = false) {
	
		//take the file object and convert it to an MD5
		$filename = md5(serialize($this->fileobj));
		
		if ($fID) {
			$filename = $filename . '_f' . $fID . image_type_to_extension($type);
		} else {
			$filename = $filename . image_type_to_extension($type);
		}
		
		$filepath = DIR_FILES_CACHE . '/' . $filename;
		
		$file = false;
		
		if (!file_exists($filepath)) {
			switch($type) {
				case IMAGETYPE_GIF:
					$file = imagegif($res, $filepath);
					break;
				case IMAGETYPE_JPEG:
					$compression = defined('AL_THUMBNAIL_JPEG_COMPRESSION') ? AL_THUMBNAIL_JPEG_COMPRESSION : 80;
					$file = imagejpeg($res, $filepath, $compression);
					break;
				case IMAGETYPE_PNG:
					$file = imagepng($res, $filepath);
					break;
			}
			@chmod($filepath, FILE_PERMISSIONS_MODE);
		}
		
		imagedestroy($res);
		
		return ( file_exists($filepath) ) ? $filename : $file;
		
	}
	
	/**
	 * Create a image resource from a file path.
	 * @param string $path
	 * @return resource $res
	 */
	private function process($path) {
	
		$imageSize = getimagesize($path);

		switch($imageSize[2]) {
			case IMAGETYPE_GIF:
				$res = @imagecreatefromgif($path);
				break;
			case IMAGETYPE_JPEG:
				$res = @imagecreatefromjpeg($path);
				break;
			case IMAGETYPE_PNG:
				$res = imagecreatefrompng($path);
				break;
		}
		
		$this->fileobj = new stdClass();
		
		$this->fileobj->width = imagesx($res);
		$this->fileobj->height = imagesy($res);
		$this->fileobj->type = $imageSize[2];
		$this->fileobj->path = $path;
		
		return $res;
		
	}
	
	/**
	 * Resizes an image, defaults to the maximum length
	 * @params resource $res, int $type, int $width, int $height, bool $max
	 * @return resource $image
	 */
	private function resize($res, $type, $width, $height, $max = true) {
		
		$oWidth = imagesx($res);
		$oHeight = imagesy($res);
		
		$width = min($oWidth, $width);
		$height = min($oHeight, $height);
		
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
		
		$this->fileobj->width = imagesx($image);
		$this->fileobj->height = imagesy($image);
		$this->fileobj->resized = 10;
		
		return $image;
		
	}
		
	/**
	 * Crops an image
	 * @params resource $res, int $type, int $width, int $height
	 * @return resource $image
	 */
	private function crop($res, $type, $width, $height) {
	
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
		
		imagecopyresampled($image, $res, 0, 0, $crop_src_x, $crop_src_y, $finalWidth, $finalHeight, $oWidth, $oHeight);
		
		$this->fileobj->width = imagesx($image);
		$this->fileobj->height = imagesy($image);
		$this->fileobj->cropped = true;
		
		return $image;
	} 
	
	/** 
	 * Returns a path to the specified item, resized and/or cropped to meet max width and height. $obj can either be
	 * a string (path) or a file object. 
	 * Returns an object with the following properties: src, width, height
	 * @param mixed $obj
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @param bool $crop
	 */
	public function getThumbnail($obj, $maxWidth, $maxHeight, $crop = false) {
		$fID = false;
		
		if ($obj instanceof File) {
			$path = $obj->getPath();
			$fID = $obj->getFileID();
		} else {
			$path = $obj;
		}
				
		$imageSize = getimagesize($path);
		$res = $this->process($path);
		$res = $this->resize($res, $imageSize[2], $maxWidth, $maxHeight);
		
		if($crop){
			$res = $this->crop($res, $imageSize[2], $maxWidth, $maxHeight);
		}
		
		$image = $this->cache($res, $imageSize[2], $fID);
		
		if ($image) {
			$src = REL_DIR_FILES_CACHE . '/' . $image;
			$abspath = DIR_FILES_CACHE . '/' . $image;
			
			$thumb = new stdClass;
			$thumb->src = $src;
			$dimensions = getimagesize($abspath);
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
