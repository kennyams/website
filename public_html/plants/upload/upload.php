<?php
	include $_SERVER['DOCUMENT_ROOT']."/../phplib/mariadb.php";
	include $_SERVER['DOCUMENT_ROOT']."/../phplib/plantnet.php";
	include $_SERVER['DOCUMENT_ROOT']."/../phplib/imglocation.php";
	print_r($_FILES);
	print_r($_POST);
	foreach ($_FILES['fileToUpload'] as $key => $value) {
		echo "<p>$key => $value</p>\n";
	}

	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo "<p>File is an image - " . $check["mime"] . "</p>";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
	}
	//$name = basename($_FILES["fileToUpload"]["name"],".jpg");
	$name = basename($_FILES["fileToUpload"]["name"]);
	$path="/pics/Pics/plants/$name";
	$fullname = $_SERVER['DOCUMENT_ROOT'].$path;
	echo "<p>fullname $fullname</p>";
	echo "<p>document root: ".$_SERVER['DOCUMENT_ROOT']."</p>";
	if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $fullname)){
		echo "<p>done</p>";
		list($w,$h) = getimagesize($fullname);
		$location=getLocation($fullname);
		echo "<p>location = $location</p>";
		$newwidth=$w/16;
	  	$newheight=$h/16;
		$source = imagecreatefromjpeg($fullname);
		$orientation=exif_read_data($fullname)['Orientation'];
		echo "<p>orient = $orientation</p>";
		$temp=plantdata($path);
		
		echo "<p>".print_r($temp->species->scientificNameWithoutAuthor,true)."</p>";
		echo "<p>".print_r($temp->species->genus->scientificNameWithoutAuthor,true)."</p>";
		echo "<p>".print_r($temp->species->family->scientificNameWithoutAuthor,true)."</p>";
		echo "<p>".print_r($temp->species->commonNames,true)."</p>";


		#echo "<p>".$mysqli->connect_errno."<\p>";
		#echo "<p>".$mysqli->connect_error."<\p>";
		putPlants(
			$temp->species->family->scientificNameWithoutAuthor,
			$temp->species->genus->scientificNameWithoutAuthor,
			$temp->species->scientificNameWithoutAuthor,
			$temp->species->commonNames[0],
			$fullname
		);
	}else{
		echo "fail";
	}

?>
	<script>
		//window.history.back();
	</script>
