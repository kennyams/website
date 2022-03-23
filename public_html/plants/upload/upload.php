<?php
	$root=$_SERVER["DOCUMENT_ROOT"];
	include "$root/session.php";
	include_once "$root/../phplib/log.php";
	include "$root/../phplib/mariadb.php";
	include "$root/../phplib/plantnet.php";
	include "$root/../phplib/imglocation.php";
	if (GetPermissions($_SESSION["email"])["upload_images"]!=="1"){
		header("Location: /");
		die;
	}
	if(isset($_POST["submit"])) {
		$files=$_FILES['upload'];
		for($i=0;$i<count($files['name']);$i++){
			$name=$files['name'][$i];
			$full_path=$files['full_path'][$i];
			$type=$files['type'][$i];
			$tmp_name=$files['tmp_name'][$i];
			$error=$files['error'][$i];
			$size=$files['size'][$i];
			logs("$name $full_path $type $tmp_name $error $size");
			$check = getimagesize($tmp_name);
			if($check !== false) {
				echo "<p>File is an image - " . $check["mime"] . "</p>";
				logs("is an image $name");
				$path="/pics/Pics/plants/$name";
				$fullname = $_SERVER['DOCUMENT_ROOT'].$path;
				logs("write to $fullname");
				if(move_uploaded_file($tmp_name, $fullname)){
					logs("done");
					list($w,$h) = getimagesize($fullname);
					$location=getLocation($fullname);
					if($location!==null){
						logs("location = $location");
						$newwidth=$w/16;
						$newheight=$h/16;
						$source = imagecreatefromjpeg($fullname);
						$orientation=exif_read_data($fullname)['Orientation'];
						logs("orient = $orientation");
						echo "<p>orient = $orientation</p>";
						$temp=plantdata($path);
						if($temp!=null){
							echo "<p>".print_r($temp->species->scientificNameWithoutAuthor,true)."</p>";
							echo "<p>".print_r($temp->species->genus->scientificNameWithoutAuthor,true)."</p>";
							echo "<p>".print_r($temp->species->family->scientificNameWithoutAuthor,true)."</p>";
							echo "<p>".print_r($temp->species->commonNames,true)."</p>";
							echo "<p>".print_r(count($temp->species->commonNames),true)."</p>";
							putPlants(
								$temp->species->family->scientificNameWithoutAuthor,
								$temp->species->genus->scientificNameWithoutAuthor,
								$temp->species->scientificNameWithoutAuthor,
								$temp->species->commonNames[0],
								$fullname
							);
						}
					}
				}
			} else {
				logs("not an image");
			}
		}
	}
?>
	<script>
		//window.history.back();
	</script>
