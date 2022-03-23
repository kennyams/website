<?php
	$root=$_SERVER["DOCUMENT_ROOT"];
	include "$root/session.php";
	include("$root/../ssl/keys/plantnetkey.php");
#echo $api_key;
function plantdata($plant){
	global $api_key;
	$inipath = php_ini_loaded_file();
	if ($inipath) {
		echo 'Loaded php.ini: ' . $inipath;
	} else {
		echo 'A php.ini file is not loaded';
	}
	$base="https://my-api.plantnet.org/v2/identify/all?api-key=$api_key";
	$picurl="https://pub.me.uk$plant";
	echo "<p>plantdata $picurl</p>";
	$url= $base."&images=".$picurl."&organs=flower";
	$encodedUrl=urlencode($url);
	//echo "<p>$url</p>";
	//$jsonstr = file_get_contents($url);
	$ch = curl_init($url); // such as http://example.com/example.xml
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$jsonstr = curl_exec($ch);
	curl_close($ch);
	if(!$jsonstr){
		echo "<p>comms error </p>";
		var_dump($http_response_header);
		return;
	}
	#$filename=$_SERVER['DOCUMENT_ROOT']."/../phplib/testdata.txt";
	#$jsonstr = file_get_contents($filename);
	$respjson = json_decode($jsonstr);
	$results=$respjson->{'results'}[0];
	if($results->{'score'}>0.2){
		return $results;
	}else{
		echo "<p>low confidence</p>";
		return null;
	}
}
?>
