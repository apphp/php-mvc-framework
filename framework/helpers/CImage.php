<?php
/**
 * CImage is a helper class that provides a set of helper methods for common image system operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2015 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * getImageSize
 * resizeImage
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
        if(!$image || !is_file($image)) return $return;
        if($img = getimagesize($image)){
            $return['width']  = $img[0];
            $return['height'] = $img[1];
            $return['type']   = $img[2];
            $return['attr']   = $img[3];
            $return['bits']   = $img['bits'];
            $return['mime']   = $img['mime'];            
        }
        return ($req != '' && isset($return[$req])) ? $return[$req] : $return;        
    }

    /**
     * Resize given image
     * @param $imagePath
     * @param $imageName
     * @param $resizeWidth
     * @param $resizeHeight
     */
    public static function resizeImage($imagePath, $imageName, $resizeWidth = '', $resizeHeight = '')
	{
        $imagePathName = $imagePath.$imageName;        
        if(empty($imagePathName)){ // No Image?    
            CDebug::addMessage('errors', A::t('core', 'Uploaded {file} is not a valid image! Please check carefully the file type.', array('{file}'=>$imageName)));
		}else if(!function_exists('imagecreatefromjpeg')){
            CDebug::addMessage('errors', A::t('core', 'The function {function} does not exist! Please check carefully your server settings.', array('{function}'=>'imagecreatefromjpeg')));
			return $imageName;
        }else{ // An Image?
			if($imagePathName){
                $size = getimagesize($imagePathName);
                $width = $size[0];
                $height = $size[1];                
                $case = '';
                $currExt = strtolower(substr($imagePathName,strrpos($imagePathName,'.')+1));
				$imagetype = (function_exists('exif_imagetype')) ? exif_imagetype($imagePathName) : '';	
                if($imagetype == '1' && $currExt != 'gif') $ext = 'gif';
				else if($imagetype == '2' && $currExt != 'jpg' && $currExt != 'jpeg') $ext = 'jpg';
                else if($imagetype == '3' && $currExt != 'png') $ext = 'png';
				else $ext = $currExt;
                switch($ext){
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
                $imagePathNameOld = $imagePath.$imageName;        
				$imageName = str_replace('.'.$currExt, '.'.$case, strtolower($imageName));
				$imagePathNameNew = $imagePath.$imageName;        

				if($case != ''){
					if($resizeWidth != '' && $resizeHeight == ''){
						$newWidth = $resizeWidth;
						$newHeight = ($height/$width) * $newWidth;                
					}else if($resizeWidth == '' && $resizeHeight != ''){
						$newHeight = $resizeHeight;
						$newWidth=($width/$height)*$newHeight;
					}else if($resizeWidth != '' && $resizeHeight != ''){
						$newWidth = $resizeWidth;
						$newHeight = $resizeHeight;                    
					}else{
						$newWidth = $width;  
						$newHeight = $height;
					}
					$iOut = @imagecreatetruecolor(intval($newWidth), intval($newHeight));     
					@imagecopyresampled($iOut,$iTmp,0,0,0,0,intval($newWidth), intval($newHeight), $width, $height);
					if($case == 'png'){
						@imagepng($iOut,$imagePathNameNew,0);
					}else if($case == 'gif'){
						@imagegif($iOut,$imagePathNameNew); 
					}else{
						@imagejpeg($iOut,$imagePathNameNew,100);	
					}
					if($currExt == 'jpg' && $case != 'jpg') @unlink($imagePathNameOld);
				}
            }            
        }
		return $imageName;
    }

}