<!DOCTYPE html>
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
		<?php
			include "$root/homepage/index.php";
		?>
		</main>
		<?php
			include "$root/footer.php";
		?>
	</body>
</html>

