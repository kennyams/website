<?php
	define("km",0.00898);
	if(!function_exists("p")){
		function p($msg){
			error_log(print_r("$msg",true));
		}
	}
	function overpass($params){
		$x1=$params->x-$params->scale*km;
		$x2=$params->x+$params->scale*km;
		$y1=$params->y-$params->scale*km;
		$y2=$params->y+$params->scale*km;
		if(property_exists($params,'place') && $params->place != null ){
			error_log(sprintf("place is %s",$params->place));
			$place=$params->place;
			$nominurl="https://nominatim.openstreetmap.org/search?q=$place&format=json";


			$opts = array('http'=>array('header'=>"User-Agent: pub.me.uk php script\r\n"));
			$context = stream_context_create($opts);
			$file = file_get_contents($nominurl, false, $context);


			error_log($file);
			$respjson = json_decode($file);
			error_log(print_r($respjson[0]->boundingbox,true));
			$bb=$respjson[0]->boundingbox;
			$x=($bb[1]+$bb[0])/2;
			$y=($bb[3]+$bb[2])/2;
			$x1=$x-$params->scale*km;
			$x2=$x+$params->scale*km;
			$y1=$y-$params->scale*km;
			$y2=$y+$params->scale*km;
		}

		$payload ="[out:json][bbox:$x1,$y1,$x2,$y2];
		(
			way[\"highway\"=\"trunk\"];
			way[\"highway\"=\"path\"];
			way[\"highway\"=\"residential\"];
			way[\"highway\"=\"tertiary\"];
			way[\"highway\"=\"primary\"];
			way[\"highway\"=\"pedestrian\"];
			way[\"highway\"=\"footway\"];
			way[\"highway\"=\"unclassified\"];
			way[\"highway\"=\"service\"];
			way[\"highway\"=\"cycleway\"];
			way[\"highway\"=\"secondary\"];
			way[\"highway\"=\"bridleway\"];
			way[\"railway\"=\"rail\"];
		);(._;>;);out body;";
		//$payload ="[out:json][bbox:$x1,$y1,$x2,$y2];node;way[\"highway\"](bn);(._;>;);out body;";
		//$payload ="[out:json][bbox:$x1,$y1,$x2,$y2];node;way[\"railway\"](bn);(._;>;);out body;";
		//$payload ='[out:json];( node(51.4824,-0.01,51.4944,0.01); <;); out;';
		//$base="https://lz4.overpass-api.de/api/interpreter";
		$base="https://maps.mail.ru/osm/tools/overpass/api/interpreter";
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
		if($respjson==null){
			error_log($jsonstr);
		}
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
				else if(property_exists($value,'tags') && property_exists($value->tags,'railway')){
					$ids['way'][$value->id]=$value;
					$tags = print_r($value->tags,true);
					//$res="$res<p>$tags</p>";
					$hw=$value->tags->railway;
					$res="$res<p>$hw</p>";
				}
				else if(property_exists($value,'tags') && property_exists($value->tags,'waterway')){
					$ids['way'][$value->id]=$value;
					$tags = print_r($value->tags,true);
					//$res="$res<p>$tags</p>";
					$hw=$value->tags->waterway;
					$res="$res<p>$hw</p>";
				}
				else{
					$tags = print_r($value,true);
				}
			}else if($value->type === 'relation'){
				$ids['relation'][$value->id]=$value;
				//$res="$res<p>relation $v</p>";
			}
		}
		$mapdata = new stdClass();
		$mapdata->paths = array();
		foreach($ids['way'] as $way){
			if(property_exists($way->tags,'highway')){
				$hw=$way->tags->highway;
			}else if(property_exists($way->tags,'railway')){
				$hw=$way->tags->railway;
			}else{
				$hw=$way->tags->waterway;
			}
			$mappath = new stdClass();
			$mappath->highway=$hw;
			$mappath->name="";
			$mappath->path=array();
			$mappath->tags=$tags;
			$mappath->id=$way->id;
			if(property_exists($way->tags,'name')){
				$mappath->name=$way->tags->name;
			}
			array_push($mapdata->paths,$mappath);
			foreach($way->nodes as $n){
				if(array_key_exists($n,$ids['node'])){
					$nn=$ids['node'][$n];
					if(property_exists($nn,'lat')){
						if($nn->lat>$x1 && $nn->lat<$x2
						&& $nn->lon>$y1 && $nn->lon<$y2){
							array_push($mappath->path,$nn);
						}
					}
				}
			}
		}

		return json_encode($mapdata);
	}
?>
