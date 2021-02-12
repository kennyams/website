<?php

function make_thumb($src, $desired_width) {
	//thanks stack overflow

    $source_image = imagecreatefromjpeg($src);
    $width = imagesx($source_image);
    $height = imagesy($source_image);

    $desired_height = floor($height * ($desired_width / $width));
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

	return $virtual_image;
}

function getImgInfoAndThumb($post){
	global $root;
	$payload=new stdClass();
	$payload->name="images";
	$payload->pics=array();
	$pinfo = GetImagesM($post);
	$plants = $pinfo->plants;
	foreach($plants as $key => $plant){
		$value=$plant->image;
		$orientation=$plant->orientation;
		$imagestr = exif_thumbnail($value, $width, $height, $type);

		$image = imagecreatefromstring($imagestr);
		if( $image===false){
			$image=make_thumb($value,384);
		}
		if ($image!==false) {
			switch($orientation){
			case 3:
				$image=imagerotate($image,90,0);
				break;
			case 8:
				$image=imagerotate($image,180,0);
				break;
			case 6:
				$image=imagerotate($image,-90,0);
				break;
			default:
				break;
			}
			ob_start();
			imagejpeg($image);
			$imageString = ob_get_clean();
			$picinstance=new stdClass();
			$picinstance->family=$plant->family;
			$picinstance->genus=$plant->genus;
			$picinstance->species=$plant->species;
			$picinstance->common=$plant->common;
			$picinstance->date=$plant->date;
			$picinstance->name=str_replace($root,"",$value);
			$picinstance->cats=base64_encode(json_encode($plant));
			$picinstance->loc=$plant->location;
			$picinstance->pic=base64_encode($imageString);
			array_push($payload->pics,$picinstance);
			imagedestroy($image);
			#exit;
		} else {
			// no thumbnail available, handle the error here
			//echo 'No Thumb';
			//$imagick = new Imagick(realpath($value));
		}
	}
	return $payload;
	echo json_encode($payload);
}
?>
