<?php
	include $_SERVER['DOCUMENT_ROOT']."/../phplib/mariadb.php";
	include $_SERVER['DOCUMENT_ROOT']."/../phplib/imglocation.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/../phplib/log.php";
	function AddLocation(){
		Logs("AddLocation()");
		global $my;
		$my=connect();
		if(mysqli_connect_errno()){
			printf("Connection Error: %s\n" , mysqli_connect_error());
		}
		$query="CALL GetImagesWithoutLocation();";
		$images=array();
		if($result = $my->query($query,MYSQLI_STORE_RESULT)){
			while($row = $result->fetch_assoc()){
				Logs(implode(",",$row));
				$loc=json_decode(getLocation($row['image']));
				Logs($loc);
				echo "<p>".$loc->lat.",".$loc->lon."</p>";
				$params=$row['id'].",POINT($loc->lat,$loc->lon)";
				echo "$params";
				$q2="CALL AddLocationToImage($params);";
				array_push($images,$q2);
				echo "<p>$q2</p>";
			}

			$result->close();
		}else{
			printf("Connection Error: %s\n" , $my->error);
		}

		disconnect();
		$my->close();
		$my=null;
		$my=connect();
		print_r($my);
		foreach($images as $q){
			echo "<p>$q</p>";
			if($res = $my->query($q)){
				echo "<p>done</p>";
				print_r($my);
			}else{
				print_r(mysqli_error($result));
				echo "<p>problem $my->error</p>";
			}
		}
		print_r("AddLocation");
		$my->close();
	}
	AddLocation();
?>
