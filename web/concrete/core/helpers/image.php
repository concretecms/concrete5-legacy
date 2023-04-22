<?php
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

	public $jpegCompression = 80;

	/**
	 * Resets the compression level to the system default
	 * This method is automatically run when Loader::helper invokes this class
	 * @return void
	 */
	function reset() {
		$this->jpegCompression = $this->defaultJpegCompression();
	}

	/** 
	 * Returns the default system value for JPEG image compression
	 * @return int from 1-100
	 */
	public function defaultJpegCompression(){
		return defined('AL_THUMBNAIL_JPEG_COMPRESSION') ? AL_THUMBNAIL_JPEG_COMPRESSION : 80;	
	}
		
	/**
	 * Overrides the default or defined JPEG compression level per instance
	 * of the image helper. This allows for a single-use for a particularly
	 * low or high compression value. Passing a non-integer value will reset
	 * to the default system setting (DEFINE or 80)
	 * @param int $level the level of compression
	 * @return self
	 */
	public function setJpegCompression($level) {
		if (is_int($level)) {
			$this->jpegCompression = min(max($level, 0), 100);
		} else {
			$this->reset();
		}
		return $this;
	}

	/**
	 * Creates a new image given an original path, a new path, a target width and height.
	 * Optionally crops image to exactly match given width and height.
	 * @param string $originalPath The path to the original image
	 * @param string $newpath The path to the new image to be created
	 * @param int $width The maximum width of the new image
	 * @param int $height The maximum height of the new image
	 * @param bool $crop = false Set to true to resize and crop the image, set to false to just resize the image 
	 * @return bool
	 * @example Resizing from 200x200 to 100x50 with $crop = false will result in a 50 x 50 image (same aspect ratio as source, scaled down to a quarter of size)
	 * @example Resizing from 200x200 to 100x50 with $crop = true will result in a 100 x 50 image (same aspect ratio as source, scaled down to a half of size and cropped in height)
	 * @example Resizing from 200x200 to 1000x1000 with either $crop = false or $crop = true in a copy of the original image (200x200)
	 */
	public function create($originalPath, $newPath, $width, $height, $crop = false) {
		// first, we grab the original image. We shouldn't ever get to this function unless the image is valid
		$imageSize = @getimagesize($originalPath);
		if($imageSize === false) {
			return false;
		}
		$oWidth = $imageSize[0];
		$oHeight = $imageSize[1];
		$finalWidth = 0; //For cropping, this is really "scale to width before chopping extra height"
		$finalHeight = 0; //For cropping, this is really "scale to height before chopping extra width"
		$do_crop_x = false;
		$do_crop_y = false;
		$crop_src_x = 0;
		$crop_src_y = 0;

		// first, if what we're uploading is actually smaller than width and height, we do nothing
		if ($oWidth < $width && $oHeight < $height) {
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
			$width = $oWidth;
			$height = $oHeight;
		} else if ($crop && ($height >= $oHeight && $width <= $oWidth)) {
			//crop to width only -- don't scale anything
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
			$height = $oHeight;
			$do_crop_x = true;
		} else if ($crop && ($width >= $oWidth && $height <= $oHeight)) {
			//crop to height only -- don't scale anything
			$finalHeight = $oHeight;
			$finalWidth = $oWidth;
			$width = $oWidth;
			$do_crop_y = true;
		} else {
			// otherwise, we do some complicated stuff
			// first, we divide original width and height by new width and height, and find which difference is greater
			$wDiff = $oWidth / $width;
			$hDiff = ($height != 0 ? $oHeight / $height : 0);
			
			if (!$crop && ($wDiff > $hDiff)) {
				//no cropping, just resize down based on target width
				$finalWidth = $width;
				$finalHeight = ($wDiff != 0 ? $oHeight / $wDiff : 0);
			} else if (!$crop) {
				//no cropping, just resize down based on target height
				$finalWidth = ($hDiff != 0 ? $oWidth / $hDiff : 0);
				$finalHeight = $height;
			} else if ($crop && ($wDiff > $hDiff)) {
				//resize down to target height, THEN crop off extra width
				$finalWidth = ($hDiff != 0 ? $oWidth / $hDiff : 0);
				$finalHeight = $height;
				$do_crop_x = true;
			} else if ($crop) {
				//resize down to target width, THEN crop off extra height
				$finalWidth = $width;
				$finalHeight = ($wDiff != 0 ? $oHeight / $wDiff : 0);
				$do_crop_y = true;
			}
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
		$processed = false;
		if(class_exists('Imagick')) {
			try {
				$image = new Imagick();
				$imageRead = false;
				if ($crop) {
					$image->setSize($finalWidth, $finalHeight);
					if($image->readImage($originalPath) === true) {
						$image->cropThumbnailImage($width, $height);
						/*
						 * Remove the canvas
						 * See: http://php.net/manual/en/imagick.cropthumbnailimage.php#106710
						 */
						$image->setImagePage(0, 0, 0, 0);
						$imageRead = true;
					}
					
				} else {
					$image->setSize($width, $height);
					if($image->readImage($originalPath) === true) {
						$image->thumbnailImage($finalWidth, $finalHeight);
						$imageRead = true;
					}
				}
				if($imageRead) {
					if($image->getCompression() == imagick::COMPRESSION_JPEG) {
						$image->setCompressionQuality($this->jpegCompression);
					}
					if($image->writeImage($newPath) === true) {
						$processed = true;
					}
				}
			}
			catch(Exception $x) {
			}
		}
		if(!$processed) {
			//create "canvas" to put new resized and/or cropped image into
			if ($crop) {
				$image = @imageCreateTrueColor($width, $height);
			} else {
				$image = @imageCreateTrueColor($finalWidth, $finalHeight);
			}
			if($image === false) {
				return false;
			}
			$im = false;
			switch($imageSize[2]) {
				case IMAGETYPE_GIF:
					$im = @imageCreateFromGIF($originalPath);
					break;
				case IMAGETYPE_JPEG:
					$im = @imageCreateFromJPEG($originalPath);
					break;
				case IMAGETYPE_PNG:
					$im = @imageCreateFromPNG($originalPath);
					break;
				default:
					@imagedestroy($image);
					return false;
			}
			if($im === false) {
				@imagedestroy($image);
				return false;
			}
			// Better transparency - thanks for the ideas and some code from mediumexposure.com
			if (($imageSize[2] == IMAGETYPE_GIF) || ($imageSize[2] == IMAGETYPE_PNG)) {
				$trnprt_indx = imagecolortransparent($im);
				
				// If we have a specific transparent color
				if ($trnprt_indx >= 0 && $trnprt_indx < imagecolorstotal($im)) {
			
					// Get the original image's transparent color's RGB values
					$trnprt_color = imagecolorsforindex($im, $trnprt_indx);
					
					// Allocate the same color in the new image resource
					$trnprt_indx = imagecolorallocate($image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					
					// Completely fill the background of the new image with allocated color.
					imagefill($image, 0, 0, $trnprt_indx);
					
					// Set the background color for new image to transparent
					imagecolortransparent($image, $trnprt_indx);
					
				
				} else if ($imageSize[2] == IMAGETYPE_PNG) {
				
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
			
			$res = @imageCopyResampled($image, $im, 0, 0, $crop_src_x, $crop_src_y, $finalWidth, $finalHeight, $oWidth, $oHeight);
			@imagedestroy($im);
			if($res === false) {
				@imagedestroy($image);
				return false;
			}
			$res2 = false;
			switch($imageSize[2]) {
				case IMAGETYPE_GIF:
					$res2 = @imageGIF($image, $newPath);
					break;
				case IMAGETYPE_JPEG:
					$res2 = @imageJPEG($image, $newPath, $this->jpegCompression);
					break;
				case IMAGETYPE_PNG:
					$res2 = @imagePNG($image, $newPath);
					break;
			}
			@imagedestroy($image);
			if($res2 === false) {
				return false;
			}
		}
		
		@chmod($newPath, FILE_PERMISSIONS_MODE);
		return true;
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
		
		$fh = Loader::helper('file');
		$prefix  = $this->jpegCompression . ':'; // Add prefix for compression level to serve the properly compressed images
		$prefix .= ($crop ? 'cropped:' : ''); // Name cropped images different from resized images so they don't get mixed up in the cache
		if (file_exists($path) && $fID) {
			$filename = md5($prefix . $path . ':' . $maxWidth . ':' . $maxHeight . ':' . filemtime($path)) . '_f' . $fID . '/' . basename($path);
		} else if (file_exists($path)){
			$filename = md5($prefix . $path . ':' . $maxWidth . ':' . $maxHeight . ':' . filemtime($path)) . '/' . basename($path);
		} else if ($fID){
			// This may be redundant - don't know it can actually ever occur
			$filename = md5($prefix . $path . ':' . $maxWidth . ':' . $maxHeight . ':') . '_f' . $fID . '/' . basename($path);
		} else {
			$filename = md5($prefix . $path . ':' . $maxWidth . ':' . $maxHeight . ':') . '/' . basename($path);
		}

		if (!file_exists(DIR_FILES_CACHE . '/' . $filename)) {
			// create image there
			$this->create($path, DIR_FILES_CACHE . '/' . $filename, $maxWidth, $maxHeight, $crop);
		}
		
		$src = REL_DIR_FILES_CACHE . '/' . $filename;
		$abspath = DIR_FILES_CACHE . '/' . $filename;
		$thumb = new stdClass;
		$dir = dirname($abspath);
		if (!file_exists($dir)) {
			mkdir($dir);
		}		
		if (isset($abspath) && file_exists($abspath)) {			
			$thumb->src = $src;
			$dimensions = getimagesize($abspath);
			$thumb->width = $dimensions[0];
			$thumb->height = $dimensions[1];
			return $thumb;
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
