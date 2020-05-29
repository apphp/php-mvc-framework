<?php
/**
 * CImage is a helper class that provides a set of helper methods for common image system operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED (static):			PRIVATE:		
 * ----------               ----------                  ----------
 * getImageSize				_getRealExtension
 * resizeImage
 * addWatermark
 * 
 */

class CImage
{
	
	/**
	 * Returns size of the given image
	 * @param string $image
	 * @param string $req
	 * @return array
	 */
	public static function getImageSize($image, $req = '')
	{
		$return = array();
		if (!$image || !is_file($image)) return $return;
		if ($img = getimagesize($image)) {
			$return['width'] = $img[0];
			$return['height'] = $img[1];
			$return['type'] = $img[2];
			$return['attr'] = $img[3];
			$return['bits'] = isset($img['bits']) ? $img['bits'] : '';
			$return['mime'] = $img['mime'];
		}
		return ($req != '' && isset($return[$req])) ? $return[$req] : $return;
	}
	
	/**
	 * Resize given image
	 * @param $imagePath
	 * @param $imageName
	 * @param $resizeWidth
	 * @param $resizeHeight
	 * @return void
	 */
	public static function resizeImage($imagePath, $imageName, $resizeWidth = '', $resizeHeight = '')
	{
		$imagePathName = $imagePath . $imageName;
		if (empty($imagePathName)) { // No Image?
			CDebug::addMessage('errors', A::t('core', 'Uploaded {file} is not a valid image! Please check carefully the file type.', array('{file}' => $imageName)));
		} elseif (!function_exists('imagecreatefromjpeg')) {
			CDebug::addMessage('errors', A::t('core', 'The function {function} does not exist! Please check carefully your server settings.', array('{function}' => 'imagecreatefromjpeg')));
			return $imageName;
		} else { // An Image?
			if ($imagePathName) {
				$size = getimagesize($imagePathName);
				$width = $size[0];
				$height = $size[1];
				$case = '';
				$currExt = strtolower(substr($imagePathName, strrpos($imagePathName, '.') + 1));
				$ext = self::_getRealExtension($imagePathName);
				switch ($ext) {
					case 'png':
						$iTmp = @imagecreatefrompng($imagePathName);
						$case = 'png';
						break;
					case 'gif':
						$iTmp = @imagecreatefromgif($imagePathName);
						$case = 'gif';
						break;
					case 'jpeg':
						$iTmp = @imagecreatefromjpeg($imagePathName);
						$case = 'jpeg';
						break;
					case 'jpg':
						$iTmp = @imagecreatefromjpeg($imagePathName);
						$case = 'jpg';
						break;
				}
				$imagePathNameOld = $imagePath . $imageName;
				$imageName = str_replace('.' . $currExt, '.' . $case, strtolower($imageName));
				$imagePathNameNew = $imagePath . $imageName;
				
				if ($case != '') {
				    // Prevent using of size like 150px or something else
                    $resizeWidth = intval($resizeWidth);
                    $resizeHeight = intval($resizeHeight);

					if ($resizeWidth != '' && $resizeHeight == '') {
						$newWidth = $resizeWidth;
						$newHeight = ($height / $width) * $newWidth;
					} elseif ($resizeWidth == '' && $resizeHeight != '') {
						$newHeight = $resizeHeight;
						$newWidth = ($width / $height) * $newHeight;
					} elseif ($resizeWidth != '' && $resizeHeight != '') {
						$newWidth = $resizeWidth;
						$newHeight = $resizeHeight;
					} else {
						$newWidth = $width;
						$newHeight = $height;
					}
					$iOut = @imagecreatetruecolor(intval($newWidth), intval($newHeight));
					@imagecopyresampled($iOut, $iTmp, 0, 0, 0, 0, intval($newWidth), intval($newHeight), $width, $height);
					if ($case == 'png') {
						@imagepng($iOut, $imagePathNameNew, 0);
					} elseif ($case == 'gif') {
						@imagegif($iOut, $imagePathNameNew);
					} else {
						@imagejpeg($iOut, $imagePathNameNew, 100);
					}
					if ($currExt == 'jpg' && $case != 'jpg') @unlink($imagePathNameOld);
				}
			}
		}
		return $imageName;
	}
	
	/**
	 * Adds watermark text to image
	 * @param $sourceFile
	 * @param $watermarkText
	 * @param $destination_file
	 * @return void
	 */
	public static function addWatermark($sourceFile, $watermarkText, $destinationFile = '')
	{
		if (!$destinationFile) $destinationFile = $sourceFile;
		else @unlink($destinationFile);
		
		$top = getimagesize($sourceFile);
		$top = $top[1] / 15 * 14;
		list($width, $height) = getimagesize($sourceFile);
		$left = 20;
		
		$destImage = imagecreatetruecolor($width, $height);
		
		$ext = self::_getRealExtension($sourceFile);
		switch ($ext) {
			case 'png':
				$sourceImage = imagecreatefrompng($sourceFile);
				break;
			case 'gif':
				$sourceImage = imagecreatefromgif($sourceFile);
				break;
			case 'jpeg':
			case 'jpg':
				$sourceImage = imagecreatefromjpeg($sourceFile);
				break;
		}
		
		imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $width, $height, $width, $height);
		
		// Path to the font file on the server
		$font = APPHP_PATH . DS . 'framework' . DS . 'vendors' . DS . 'fonts' . DS . 'arial.ttf';
		// Font size
		$font_size = 16;
		// Add a white shadow
		$white = imagecolorallocate($destImage, 255, 255, 255);
		imagettftext($destImage, $font_size, 0, $left, $top, $white, $font, $watermarkText);
		// Print in black color
		$black = imagecolorallocate($destImage, 0, 0, 0);
		imagettftext($destImage, $font_size, 0, $left - 2, $top - 1, $black, $font, $watermarkText);
		
		if ($destinationFile != '') {
			if ($ext == 'png') {
				imagepng($destImage, $destinationFile, 0);
			} elseif ($ext == 'gif') {
				imagegif($destImage, $destinationFile);
			} else {
				imagejpeg($destImage, $destinationFile, 100);
			}
		} else {
			header('Content-Type: image/' . $ext);
			imagejpeg($destImage, null, 100);
		}
		
		imagedestroy($sourceImage);
		imagedestroy($destImage);
	}
	
	/**
	 * Return a real extenstion of the file
	 * @param string $file
	 * @return string
	 */
	protected static function _getRealExtension($file)
	{
		if (empty($file)) {
			return '';
		}
		
		$currentExt = strtolower(substr($file, strrpos($file, '.') + 1));
		$imageType = (function_exists('exif_imagetype')) ? exif_imagetype($file) : '';
		
		if ($imageType == '1' && $currentExt != 'gif') $realExt = 'gif';
		elseif ($imageType == '2' && $currentExt != 'jpg' && $currentExt != 'jpeg') $realExt = 'jpg';
		elseif ($imageType == '3' && $currentExt != 'png') $realExt = 'png';
		$realExt = $currentExt;
		
		return $realExt;
	}
	
}
