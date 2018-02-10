<?php
/**
 * CGeoLocation is a helper class that provides a set of helper methods for common geo location operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2018 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * coordinatesByAddress
 * distanceByCoordinats
 * 
 */	  

class CGeoLocation
{

    /**
     * Find coordinates by given adsress
     * @param string $address
     * @param string $region
     */
    public static function coordinatesByAddress($address = '', $region = '')
    {
		$coordinates = array('longitude'=>'', 'latitude'=>'');
		
		if(!empty($address)){
			$address = str_replace(' ', '+', $address);					
			$json = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false&region='.$region);
			$json = json_decode($json);
			if(!empty($json) && $json->{'results'} != false){
				$longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
				$latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			}
			
			if(!empty($longitude) && !empty($latitude)){
				$coordinates['longitude'] = $longitude;
				$coordinates['latitude'] = $latitude;
			}			
		}		
	
        return $coordinates;
    }

    /**
     * Calculate distance by coordinates two points for Earth
     * @param float $ltdA latitude point 'A'
     * @param float $lngA longitude point 'A'
     * @param float $ltdB latitude point 'B'
     * @param float $lngB longitude point 'B'
     * @param bool $distansUnits
     * @return float
     */
    public static function getDistanceByCoordinats($ltdA, $lngA, $ltdB, $lngB, $distansUnits = 'km')
    {
        if($distansUnits == 'miles'){
            // Radius Earth (in miles)
            $earthRadius = 3963;
        }else{
            // Radius Earth (km)
            $earthRadius = 6371;
        }

        // Translate coordinates in radians
        $ltdA = $ltdA * M_PI / 180;
        $ltdB = $ltdB * M_PI / 180;
        $lngA = $lngA * M_PI / 180;
        $lngB = $lngB * M_PI / 180;

        $cl1 = cos($ltdA);
        $cl2 = cos($ltdB);
        $sl1 = sin($ltdA);
        $sl2 = sin($ltdB);
        $delta = $lngB - $lngA;
        $cDelta = cos($delta);
        $sDelta = sin($delta);

        $y = sqrt(pow($cl2 * $sDelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cDelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cDelta;

        $ad = atan2($y, $x);
        $dist = $ad * $earthRadius;

        return $dist;
    }

}
