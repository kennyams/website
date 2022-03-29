<?php 
$root=$_SERVER["DOCUMENT_ROOT"];
include "$root/session.php";
include "$root/../phplib/mariadb.php";
	if (GetPermissions($_SESSION["email"])["upload_images"]!=="1"){
		header("Location: /");
		die;
	}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="homepage/home.css" rel="stylesheet"/>
	<?php
		$root=$_SERVER["DOCUMENT_ROOT"];
		include "$root/head.php";
	?>
	</head>
	<body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
		<main>
			<form action="upload.php" method="post" enctype="multipart/form-data" onblur="window.history.back">
			  Select image to upload:
			  <input type="file" name="upload[]" id="fileToUpload" accept="image/jpeg" multiple>
			  <input type="submit" value="Upload Image" name="submit">
			</form>
			<form action="AddLocations.php">
				<input type="submit" value="AddLocations"></input>
			</form>
		</main>
		<?php
			include "$root/footer.php";
		?>
	</body>
</html>

