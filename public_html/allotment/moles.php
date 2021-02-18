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
					<p>Most allotmenter's do not like to see moles, horrid things that leave piles of soil about, mainly amongst the veg.</p>
					<p>We actually quite like them, good for churning the soil and they don't actually eat your crops as they are carnivorous.</p>
					<p>This little chap was wondering about on the allotment, bold a brass, in broad daylight.</p>
					<p>It was very dry after a period of drought and so was probably thirsty. Ended up in the strawberrys, hopefully not resorting to some fruit.</p>
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

