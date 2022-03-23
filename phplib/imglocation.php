<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/../phplib/log.php";
function getLocation($file){
	$lat = 51.505;
	$lath = 'N';
	$lon = -0.09;
	$lonh = 'E';
	logs(print_r($file,true));
	if (is_file($file)){
		$exif = exif_read_data($file,"ANY_TAG");
		//logs(print_r($exif,true));
		if(isset($exif["GPSLatitude"])){
			$lath = $exif["GPSLatitudeRef"]=='N'?1:-1;
			$deglat=(double)$exif["GPSLatitude"][0];
			$minlat=explode('/',$exif["GPSLatitude"][1]);
			$seclat=explode('/',$exif["GPSLatitude"][2]);
			$lat=$deglat + ((double)($minlat[0])/(double)($minlat[1]))/60.0;
			$lat=$lat + ((double)($seclat[0])/(double)($seclat[1]))/3600.0;
			$lat=$lat*$lath;

			$lonh=$exif["GPSLongitudeRef"]=='E'?1:-1;
			$deglon=(float)$exif["GPSLongitude"][0];
			$minlon=explode('/',$exif["GPSLongitude"][1]);
			$seclon=explode('/',$exif["GPSLongitude"][2]);
			$lon=$deglon + ((double)($minlon[0])/(double)($minlon[1]))/60.0;
			$lon= $lon + ((double)($seclon[0])/(double)($seclon[1]))/3600.0;
			$lon=$lon*$lonh;
			$myObj=new stdClass();
			$myObj->lat=$lat;
			$myObj->lon=$lon;
			$myObj->lath=$lath;
			$myObj->lonh=$lonh;
			$myObj->orientation=$exif['Orientation'];

			return json_encode($myObj);
		}
	}
	return null;
}
if(array_key_exists("pic",$_GET)) {
	echo getLocation($_GET['pic']);
}else{
	//echo "<p>error</p>";
}
?>

