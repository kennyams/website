<!DOCTYPE html>
<html>
	<head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
		?>
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
		   integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
		   crossorigin=""/>
		<link href="plants.css" rel="stylesheet"/>
		<script src="plants.js"> </script>

		<!-- Make sure you put this AFTER Leaflet's CSS -->
		 <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
		   integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
		   crossorigin="">
		</script>

	</head>
	<body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
		<!--p id="FilterSettings" class="toggleControl">Settings</p-->
		<main>
			<div id="filtercont">
				<form id="filter" >
					<!--p id="Filter" class="toggleControl" style="grid-row:1;grid-column:1">Filter</p-->
					<label class="dropitem a1" for="familySelected[]">Family</label>
					<select class="dropitem b1" multiple  id="familySelected" name="familySelected[]" form="filter" > </select>
					<label class="dropitem a2" for="genusSelected" >Genus</label>
					<select class="dropitem b2" multiple  id="genusSelected" name="genusSelected[]" form="filter" > </select>
					<label class="dropitem a3" for="speciesSelected">Species</label>
					<select class="dropitem b3" multiple  id="speciesSelected" name="speciesSelected[]" form="filter" > </select>
					
					<!--p id="DateRange" class="toggleControl" style="grid-row:4;grid-column:1">DateRange</p-->
					<label class="c1">From:</label>
					<input class="d1" type="text" id="frompicker"/>
					<label class="c2">To:</label>
					<input  class="d2" type="text" id="topicker"/>
					<!--p id="control" class="toggleControl">Control</p-->
				</form>
				<form id="upload" action="/plants/upload/plantpic.html"> </form>
				<form id="place" > </form>
				<div id="filterOptions">
					<label  for="onmap">Filter on map area</label>
					<input class="filterOptionsOption" id="onmap" form="filter" type="checkbox" value="map"/>
					<label  >Find Place</label>
					<input class="filterOptionsOption" id="i_place" form="place" type="text" value="Surrey"/>
					<div ></div>
					<input class="filterOptionsOption" form="upload" type="submit" value="Upload"/>
				</div>
			</div>
			<!--p id="Selection" class="toggleControl">Selection</p-->
			<div id="piccontainerCont">
				<h2 id="loading" >Loading</h2>
				<div id="piccontainer" >
				</div>
			</div>
			<!--div id="piccontainer"></div-->

			<!--section id="mainarea"-->
				<!--div id="content"-->
					<div id="mainarea" >
						<figure id="picframe">
							<!--img id="mainImage" style="image-orientation: from-image;"/-->
							<img id="mainImage"/>
							<figure-caption id="imageinfo" ></figure-caption>
						</figure>
					</div>
				<!--/div-->
			<!--/section-->
			<div id="mapframe">
				<div id="mapid">
				</div>
			</div>
			<script>
			</script>
		</main>
		<?php
			include "$root/footer.php";
		?>
	</body>
</html>
