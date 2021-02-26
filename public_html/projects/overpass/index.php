<?php
		function p($msg){
			//print_r("<p>$msg</p>");
		}
		function overpass($params){
			$inipath = php_ini_loaded_file();
			if ($inipath) {
				p('Loaded php.ini: ' . $inipath);
			} else {
				p('A php.ini file is not loaded');
			}
			//$payload ='[out:json];( node(50.746,7.154,50.748,7.157); <;); out;';
			$x=51.23658;$y=-0.58346;
			//$x=51.50476;$y=0.00411;
			$x1=$params->x-$params->scale;
			$x2=$params->x+$params->scale;
			$y1=$params->y-$params->scale;
			$y2=$params->y+$params->scale;

			$payload ="[out:json];( node($x1,$y1,$x2,$y2); <;); out;";
			//$payload ='[out:json];( node(51.4824,-0.01,51.4944,0.01); <;); out;';
			$base="https://lz4.overpass-api.de/api/interpreter";
			$url= $base;
			$encodedUrl=urlencode($url);
			$ch = curl_init($url); // such as http://example.com/example.xml
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type'=> 'text/plain',
				'Content-Length'=> strlen($payload)),
			);
			$jsonstr = curl_exec($ch);
			curl_close($ch);
			if(!$jsonstr){
				var_dump($http_response_header);
				return;
			}
			$respjson = json_decode($jsonstr);
			$results=$respjson->elements;
			$ids = array();
			$ids['node']=array();
			$ids['way']=array();
			$ids['relation']=array();
			$res="";

			foreach($results as $value){
				$v=print_r($value,true);
				if($value->type === 'node'){
					$ids['node'][$value->id]=$value;
					//$res="$res<p>node $v</p>";
				}else if($value->type === 'way'){
					if(property_exists($value,'tags') && property_exists($value->tags,'highway')){
						$ids['way'][$value->id]=$value;
						$tags = print_r($value->tags,true);
						//$res="$res<p>$tags</p>";
						$hw=$value->tags->highway;
						$res="$res<p>$hw</p>";
					}
					else{
						//$tags = print_r($value->tags,true);
						//$res="$res<p>$tags</p>";
					}
				}else if($value->type === 'relation'){
					$ids['relation'][$value->id]=$value;
					//$res="$res<p>relation $v</p>";
				}
			}
			foreach($ids['way'] as $way){
				//$v = print_r($way,true);
				$hw=$way->tags->highway;
				echo "{highway:\"$hw\",path:";

				echo"[";
				foreach($way->nodes as $n){
					if(array_key_exists($n,$ids['node'])){
						$nn=$ids['node'][$n];
						if(property_exists($nn,'lat')){
							echo "{x:$nn->lat,y:$nn->lon},";
						}
					}
				}
				echo"]},";
			}

			return $res;
		}
?>

<!DOCTYPE html>
<html>
    <head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
		?>
		<script src="/jslib/mapping.js"></script>
		<link href="overpass.css" rel="stylesheet"/>
	<script>
		var ways = [
<?php
		//print_r($_GET);
		$coords = new stdClass();
		if(array_key_exists('x',$_GET) && array_key_exists('y',$_GET)){
			$coords->x=$_GET['x'];
			$coords->y=$_GET['y'];
		}else{
			$coords->x=51.24658;
			$coords->y=-0.58364;
		}
		if(array_key_exists('scale',$_GET)){
			$coords->scale=$_GET['scale'];
		} else{
			$coords->scale=0.01;
		}
		$res = overpass($coords);
?>
];

	$(function(){
		var map = document.getElementById("map");
		//var svg = makeSVG("svg",{"transform":"rotate(-90) translate(100,-50)"});
		var svg = makeSVG("svg",{"id":"svg","viewBox":"0 0 100 75"});
		map.appendChild(svg);
		var w = ways.map(function(c){
			return(c.path);
		});
		m=minmax(w);
		ways.forEach(function(w){
			normalize(w.path,m);
			var p = createPath(w.path);
			svg.appendChild(makeSVG("path",{"d":p, "class":w.highway, "fill":"none", "stroke":"green", "stroke-width":".1"}));
		});
	});

	</script>
    </head>
    <body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
        <main>
			<div id="map"></div>
			<div id="debug">
<?php
			//print_r($res);
			foreach($res as $value){
				if($value->type === 'node'){
				}else if($value->type === 'way'){
					$p=print_r($value,true);
					print_r($value->tags);
				}else if($value->type === 'relation'){
				}
			}
?>
			</div>
        </main>
		<script>
			$("#pagetitle").html("<p>Prototype of SVG mapping using overpass api to the openstreetmap database</p>");
		</script>
    </body>
</html>

