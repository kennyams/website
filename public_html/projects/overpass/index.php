<?php 
$root=$_SERVER["DOCUMENT_ROOT"];
include_once "$root/session.php";
?>
<!DOCTYPE html>
<html>
    <head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
			include "$root/../phplib/mapping/overpass.php";
		?>
		<script src="/jslib/mapping.js"></script>
		<link href="overpass.css" rel="stylesheet"/>
	<script>
		var ways = 
<?php
		error_log(print_r($_POST,true));
		error_log(print_r($_GET,true));
		$coords = new stdClass();
		if(array_key_exists('x',$_GET) && array_key_exists('y',$_GET)){
			$coords->x=$_GET['x'];
			$coords->y=$_GET['y'];
		}else{
			//$coords->x=51.24658;
			//$coords->y=-0.58364;
		}
		$update_settings=false;
		if(array_key_exists('scale',$_GET)){
			$coords->scale=$_GET['scale'];
			$update_settings=true;
		}else if(array_key_exists('overpass_scale',$settings)){
			$coords->scale=$settings['overpass_scale'];
		}
		if(array_key_exists('place',$_GET)){
			$coords->place=$_GET['place'];
			$update_settings=true;
		}else if(array_key_exists('overpass_place_name',$settings)){
			$coords->place=$settings['overpass_place_name'];
		}
		$place=$coords->place;
		$scale=$coords->scale;
		if($update_settings){
			OverPassCookie($uuid,$scale,$place);
		}
	
		error_log(print_r($coords,true));
		$res = overpass($coords);
		echo "$res"."['paths']";
		
?>
;
	
	$(function(){
		var x=100;
		var y=100;
		//console.log(ways);
		var map = document.getElementById("map");
		var svg = makeSVG("svg",{"id":"svg","viewBox":`0 0 ${x} ${y}`,"font-size":"2px"});
		map.appendChild(svg);
		var w = ways.map(function(c){
			return(c.path);
		});
		m=minmax(w);
		ways.forEach(function(w){
			normalize(w.path,m,x,y);
			var p = createPath(w.path);
			svg.appendChild(makeSVG("path",{"id":w.id,  "d":p, "class":w.highway, "fill":"none", "stroke":"green", "stroke-width":".1"}));
			p = document.getElementById(w.id);
			var plen=p.getTotalLength();
			//console.log(plen);
			var p = createRoute(w);
			svg.appendChild(p);
			var t = document.getElementById(`tp_${w.id}`);
			var tlen = t.getComputedTextLength();
			//console.log(tlen);
			t.setAttribute("startOffset",`${(plen-tlen)/2}px`);
		});
	});

	</script>
    </head>
    <body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include_once "$root/header.php";
		?>
        <main>
			<form id="place" method="GET" > 
				<label>Find Place</label>
<?php
				echo "<input id=\"i_place\" name=\"place\"  form=\"place\" type=\"text\" value=\"$place\">"
?>
				<label>Scale</label>
<?php
				echo "<input id=\"i_scale\" name=\"scale\"  form=\"place\" type=\"text\" value=\"$scale\">"
?>
				<input form="place" type="submit" value="Go">
			</form>
			<div id="findplace"></div>
			<div id="map"></div>
			<div id="debug">
			</div>
        </main>
		<?php
			include "$root/footer.php";
		?>
		<script>
			$("#pagetitle").html("<p>Prototype of SVG mapping using overpass api to the openstreetmap database</p>");
		</script>
    </body>
</html>

