<?php 
$root=$_SERVER["DOCUMENT_ROOT"];
include "$root/session.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
		?>
		<script src="../jslib/mapping.js"> </script>
		<script src="allotment.js"> </script>
		<link href="allotment.css" rel="stylesheet"/>
	</head>
	<body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
		<section id="mainarea">
				<h1>Aerial View From Google maps</h1>
			<div id="allotment-content">
				<iframe id="map" src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d165.76837512666376!2d-0.6231220522871603!3d51.212818964898354!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sen!2suk!4v1612951055902!5m2!1sen!2suk" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
				<div id="svg">
					<svg id="allotment" viewBox="0 0 100 75"  xmlns="http://www.w3.org/2000/svg" >
					  	<!--text x="50" y="50">Position</text-->
						<g id="allotgroup" transform="scale(1,-1) translate(0,-75)"> 
					  		<path id="outline"  fill="none" stroke="black" stroke-width="1" /> 
						</g>
					</svg>
				</div>
			</div>
		</section>
	</body>
</html>
