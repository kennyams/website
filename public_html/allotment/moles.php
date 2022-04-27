<?php 
$root=$_SERVER["DOCUMENT_ROOT"];
include "$root/session.php";
include "$root/../phplib/article.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
		?>
		<!--script src="allotment.js"> </script-->
		<link href="allotment.css" rel="stylesheet"/>
		<style>
			#allotment-content{
				display:flex;
				flex-direction:row;
				margin-left:20pt;
			}
		</style>
	</head>
	<body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
		<section id="mainarea">
			<div id="allotment-content">
				<div class="article">
					<?php
						article("moles");
					?>
				</div>
				<figure>
					<video controls width="500" style="width:100%">
						<source src="/Videos/mole.webm" type="video/webm">
						Sorry, your browser doesn't support embedded videos.
					</video>
					<figcaption>Mole</figcaption>
					<figcaption>Seems a bit drunk !!!</figcaption>
				</figure>
			</div>
		</section>
	</body>
</html>

