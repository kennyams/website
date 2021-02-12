<?php
$root=$_SERVER['DOCUMENT_ROOT'];
include("$root/../phplib/mariadb.php");
include("$root/../phplib/getImgInfoAndThumb.php");

if(array_key_exists('family',$_GET)){
	echo json_encode(getFamily());
}else if(array_key_exists('genus',$_GET)){
	echo json_encode(getGenus(json_encode($_POST['familySelected'])));
}else if(array_key_exists('species',$_GET)){
	echo json_encode(getSpecies(json_encode($_POST['genusSelected'])));
}else if(array_key_exists('images',$_GET)){
	echo json_encode(getImgInfoAndThumb($_POST));
}else if(array_key_exists('firstdate',$_GET)){
	echo json_encode(firstDate());
}
?>
