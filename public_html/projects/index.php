<!DOCTYPE html>
<html>
	<head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
		?>
		<title>AWS Experiments</title>
		<!--link href="aws.css" rel="stylesheet"/-->
	</head>
	<body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
		<main>
			<ul>
				<li>AWS IOT<span>
				<p>Experiments controlling a raspberry pi camera with AWS IOT</p></span>
				</li>
				<li>SVG Mapping</li>
				<p>Using opensteetmap data to create a map on an SVG canvas</p>
			</ul>
		</main>
		<?php
			include "$root/footer.php";
		?>
		<script>
			$("#pagetitle").html("<p>Project I am working on</p>");
		</script>
	</body>
</html>

