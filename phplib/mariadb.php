<?php
$my=null;
include("${_SERVER["DOCUMENT_ROOT"]}/../ssl/creds/dbpass.php");
include("${_SERVER["DOCUMENT_ROOT"]}/../phplib/blogdb.php");
function connect(){
	global $my;
	if($my != null){
		echo "<p>my is not null </p>";
		return $my;
	}

	//$my= new mysqli('localhost', DBUSER, DBPASS, DBDB );
	$my= new mysqli(SERVER, DBUSER, DBPASS, DBDB );
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
		return null;
	}
	return $my;
}
function disconnect(){
	global $my;
	$my->close();
	$my=null;
}
function getPoly($data){
	$x1 = $data->_southWest->lat;
	$y1 = $data->_southWest->lng;
	$x2 = $data->_northEast->lat;
	$y2 = $data->_northEast->lng;
	$g = "POLYGON(($x1 $y1, $x1 $y2, $x2 $y2, $x2 $y1, $x1 $y1))";
	return $g;
}
function putPlants($family,$genus,$species,$common,$file,$my=null){
	echo"<table>";
	echo "<tr><td>$family</td></tr>";
	$my=connect();
	if (
		ctype_space($family) or
		ctype_space($genus) or
		ctype_space($species) or
		ctype_space($common) or
		ctype_space($file)
	){
		return;
	}
	echo "<tr><td>putPlants  family=$family genus=$genus species=$species common=$common </td></tr>";

	$exifdata=exif_read_data($file);
	$orientation=$exifdata['Orientation'];
	$datetime = explode(" ",$exifdata['DateTimeOriginal']);
	$datetime[0] = str_replace(':','-',$datetime[0]);
	$datetime="$datetime[0] $datetime[1]";
	print_r($datetime);
	echo "<tr><td>Family $family</td></tr>";
	$q="INSERT IGNORE INTO Family (name) VALUES ('$family');";
	echo "<tr><td>query $q </td></tr>";
	$code = $my->query($q);
	echo "<tr><td>code=$code</td></tr>";

		if($my->error)
			echo"<tr><td>error: $my->error</td></tr>";
	$family_id=mysqli_insert_id($my);
	echo "<tr><td>Family id=$family_id   $my->insert_id</td></tr>";
	if($family_id){
		echo "<tr><td>New Family</td></tr>";
		$code = $my->query("INSERT IGNORE INTO Genus (name,family_id) VALUES ('$genus',$family_id);");
	}else{
		echo "<tr><td>Old Family</td></tr>";
		$fid = $my->query("SELECT id FROM Family WHERE name = \"$family\";");
		if($my->error)
			echo"<tr><td>error: $my->error</td></tr>";
		$family_id=$fid->fetch_assoc()['id'];
		$code = $my->query("INSERT IGNORE INTO Genus (name,family_id) VALUES ('$genus',$family_id);");
	}
	echo "<tr><td>Family id=$family_id</td></tr>";
	$genus_id=$my->insert_id;
	echo "<tr><td>genus_id $genus_id</td></tr>";
	if($genus_id){
		$q="INSERT IGNORE INTO Species (name,genus_id) VALUES ('$species',$genus_id);";
		echo "<tr><td> tryone $q </td></tr>";
		$code = $my->query($q);
	}else{
		$fid = $my->query("SELECT id FROM Genus WHERE name = \"$genus\";");
		$genus_id=$fid->fetch_assoc()['id'];
		echo "<tr><td> genus_id $genus_id</td></tr>";
		$q="INSERT IGNORE INTO Species (name,genus_id) VALUES ('$species',$genus_id);";
		echo "<tr><td> trytwo $q </td></tr>";
		$code = $my->query($q);
	}
	$species_id=$my->insert_id;
	if(!$species_id){
		$fid = $my->query("SELECT id FROM Species WHERE name = \"$species\";");
		$species_id=$fid->fetch_assoc()['id'];
		echo $species;
	}

	echo "<tr><td> family=$family genus=$genus species=$species </td></tr>";
	$common = str_replace("'","\'",$common);
	echo "<tr><td> $common </td></tr>";
	$q="INSERT IGNORE INTO Plants (family,genus,species,common) VALUES ($family_id,$genus_id,$species_id,'$common');";
	echo "<tr><td> $q </td></tr>";
	$code = $my->query($q);
	$plant_id=$my->insert_id;
	echo "<tr><td> plant insert id = $plant_id </td></tr>";
	if($plant_id==0){
		$q="SELECT id FROM Plants WHERE family=$family_id AND genus=$genus_id AND species=$species_id;";
		echo "<tr><td>$q</td></tr>";
		$fid = $my->query($q);
		echo "<tr><td>hello</td></tr>";
		$plant_id=$fid->fetch_assoc()['id'];
		echo "<tr><td>current plant inserted $plant_id</td></tr>";
	}else{
		echo "<tr><td>new plant inserted $plant_id</td></tr>";
	}
	$q="INSERT IGNORE INTO Images (image,plant_id,orientation,date) VALUES ('$file',$plant_id,$orientation,'$datetime');";
	echo "<tr><td>$q</td></tr>";
	$code = $my->query($q);
	echo "<tr><td> Image insert id = $plant_id</td></tr>";
	echo"<tr><td>myerror $my->error</td></tr>";
	echo"</table>";
	$my->close();
}


function getPictures(){
	$my=connect();
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
	}
	//$my->query("use plants;");
	if($result = $my->query("SELECT * FROM Images;")){
		while($row = $result->fetch_assoc()){
			printf("<p>%s %s</p>\r\n",$row['id'],$row['image']);
		}
	}
	$my->close();
}

function getFamily(){
	$my=connect();
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
	}
	$query="CALL GetFamilies();";
	$families=array();
	if($result = $my->query($query)){
		while($row = $result->fetch_assoc()){
			$family=new stdClass();
			$family->family=$row['name'];
			array_push($families,$family);
		}
	}
	$my->close();
	return $families;
}

function getGenus($families){
	$genuses=array();
	$my=connect();
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
	}
	$query="CALL GetGenus ('$families');";
	if($result = $my->query($query)){
		while($row = $result->fetch_assoc()){
			$genus=new stdClass();
			$genus->genus=$row['name'];
			array_push($genuses,$genus);
		}
	}else{
		printf("genus error");
	}
	$my->close();
	return $genuses;
}

function getSpecies($genus){
	$speciess=array();
	$my=connect();
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
	}
	$query="CALL GetSpecies ('$genus');";
	if($result = $my->query($query)){
		while($row = $result->fetch_assoc()){
			$species=new stdClass();
			$species->species=$row['name'];
			array_push($speciess,$species);
		}
	}else{
		printf("species error");
	}
	$my->close();
	return $speciess;
}


function firstDate(){
	$my=connect();
	$query="CALL FirstDate();";
	if($result = $my->query($query)){
		$row=mysqli_fetch_row($result);
		$my->close();
		return $row[0];
	}else{
		echo "<p> -----| fail |----- </p>";
	}
}

function GetImagesM($post){
	$my=connect();
	$poly='';
	if(array_key_exists('map',$post)){
		$map=$post['map'];
		unset($post['map']);
		$poly = json_decode($map);
		$poly = getPoly($poly);
	}
	$p=json_encode($post);
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
	}
	$pinfo=new stdClass();
	$plants=array();
	//print_r($poly);
	$query="CALL GetPictures('$p', (ST_GeomFromText('$poly')),0,0,0);";
	if($result = $my->query($query)){
		while($row = $result->fetch_assoc()){
			//$p=print_r($row['fname'],True);
			$plant=new stdClass();
			$plant->family=$row['fname'];
			$plant->genus=$row['gname'];
			$plant->species=$row['sname'];
			$plant->common=$row['common'];
			$plant->image=$row['image'];
			$plant->location=$row['location'];
			$plant->orientation=$row['orientation'];
			$plant->date=$row['date'];
			array_push($plants,$plant);

			//print_r($row);
		}
		$pinfo->plants=$plants;
	}else{
		printf("get plants error");
	}
	$my->close();
	return $pinfo;
}

function AddUser($name,$email, $hash, $uuid){
	error_log("AddUser");
	$my=connect();
	$query="CALL AddUser('$name','$email','$hash','$uuid');";
	if($result = $my->query($query)){
	}else{
		error_log("AddUser error");
	}
	disconnect();
}
function RegisterUser($uuid){
	error_log("RegisterUser");
	$my=connect();
	$query="CALL RegisterUser('$uuid');";
	if($result = $my->query($query)){
		$row = $result->fetch_assoc();
		disconnect();
		return($row['result']=='success');
	}else{
		error_log("RegisterUser error");
	}
	disconnect();
}
function IsUser($email){
	error_log("IsUser");
	$my=connect();
	$query="CALL IsUser('$email');";
	if($result = $my->query($query)){
		$row = $result->fetch_assoc();
		disconnect();
		return($row['result']=='exists');
	}else{
		error_log("IsUser error");
	}
	disconnect();
}
function CheckUser($email, $hash, $cookieid){
	error_log("CheckUser");
	$my=connect();
	$query="CALL CheckUser('$email', '$hash', '$cookieid');";
	if($result = $my->query($query)){
		error_log("CheckUser ok");
		while($row = $result->fetch_assoc()){
			disconnect();
			return($row['result']);
		}
	}else{
		error_log("CheckUser error");
	}
	disconnect();
}
function SetUserIdOnCookie($email,$cookieid){
	error_log("SetUserIdOnCookie");
	$my=connect();
	$query="CALL SetUserIdOnCookie('$email', '$cookieid');";
	if($result = $my->query($query)){
		error_log("SetUserIdOnCookie ok");
		while($row = $result->fetch_assoc()){
			disconnect();
			return($row['result']);
		}
	}else{
		error_log("SetUserIdOnCookie error");
	}
	disconnect();

}
function GetPermissions($email){
	error_log("GetPermissions");
	$my=connect();
	$query="CALL GetPermissions('$email');";
	if($result = $my->query($query)){
		$row = $result->fetch_assoc();
			disconnect();
			return($row);
	}
	disconnect();
}
function NewCookie($uuid){
	error_log("NewCookie");
	$my=connect();
	$query="CALL NewCookie('$uuid');";
	if($result = $my->query($query)){
		disconnect();
		return($result);
	}
	disconnect();
}
function Cookie($uuid){
	error_log("Cookie");
	$my=connect();
	$query="CALL Cookie('$uuid');";
	if($result = $my->query($query)){
		$row = $result->fetch_assoc();
			disconnect();
			return($row);
	}
	disconnect();
}

function GetLoginDetails($id){
	error_log("GetLoginDetails");
	$my=connect();
	$query="CALL GetLoginDetails('$id');";
	if($result = $my->query($query)){
		$row = $result->fetch_assoc();
			disconnect();
			return($row);
	}
	disconnect();
}

function OverPassCookie($uuid,$scale,$place){
	error_log("OverPassCookie");
	$my=connect();
	$query="CALL OverPassCookie('$uuid','$scale','$place');";
	if($result = $my->query($query)){
		disconnect();
		return($result);
	}
	disconnect();
}
function deleteOldCookies(){
	error_log("Cookie");
	$my=connect();
	$query="CALL DeleteOldCookies();";
	if($result = $my->query($query)){
			disconnect();
			return($result);
	}
	disconnect();
}
?>
