<?php
$my=null;
include("$root/../ssl/cred/dbpass.php");
function connect(){
	global $my;
	if($my != null){
		echo "<p>my is not null </p>";
		return $my;
	}

	$my= new mysqli('localhost', DBUSER, DBPASS, DBDB );
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
		return null;
	}
	#$my->query("use plants;");
	//$my->query("use pubmeuk_wp789;");

	//error_log("my connect out");
	return $my;
}
function disconnect(){
	global $my;
	$my->close();
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
function create_sps(){
	$my=connect();
	$my->query("CREATE DEFINER=`ken`@`%` PROCEDURE `plants`.`FirstDate`() SELECT min(Images.`date` ) from Images;");
	if($my->error)
		echo"<td>create proc: $my->error</td>";
	$my->close();
}
function create_tables(){
	echo "<style>
	table, th, td {
  	border: 1px solid black;
  	border-collapse: collapse;
	</style>";
	$my=connect();
	echo "<p>create_tables</p>";

	#$code = $my->query("DROP DATABASE plants");
	#$code = $my->query("CREATE DATABASE IF NOT EXISTS plants");

	$my->query("DROP TABLE IF EXISTS Images;");
	if($my->error)
		echo"<td>images: $my->error</td>";
	$my->query("DROP TABLE IF EXISTS Plants;");
	if($my->error)
		echo"<td>family: $my->error</td>";
	$my->query("DROP TABLE IF EXISTS Species;");
	if($my->error)
		echo"<td>error: $my->error</td>";
	$my->query("DROP TABLE IF EXISTS Genus;");
	if($my->error)
		echo"<td>species: $my->error</td>";
	$my->query("DROP TABLE IF EXISTS Family;");
	if($my->error)
		echo"<td>genus: $my->error</td>";

	$code = $my->query("CREATE TABLE IF NOT EXISTS Family (
		id INT NOT NULL AUTO_INCREMENT,
		name VARCHAR(100),
		PRIMARY KEY(id),
		CONSTRAINT unique_family UNIQUE(name) 
	);");
	if($my->error)
		echo"<p>myerror $my->error</p>";

	$code = $my->query("CREATE TABLE IF NOT EXISTS Genus (
		id INT NOT NULL AUTO_INCREMENT,
		family_id INT,
		name VARCHAR(100),
		PRIMARY KEY(id),
		FOREIGN KEY ( family_id ) REFERENCES Family(id) ON DELETE CASCADE,
		CONSTRAINT unique_genus UNIQUE(name) 
	);");
	if($my->error)
		echo"<p>myerror $my->error</p>";

	$code = $my->query("CREATE TABLE IF NOT EXISTS Species (
		id INT NOT NULL AUTO_INCREMENT,
		genus_id INT,
		name VARCHAR(100),
		PRIMARY KEY(id),
		FOREIGN KEY ( genus_id ) REFERENCES Genus(id) ON DELETE CASCADE,
		CONSTRAINT unique_genus UNIQUE(name) 
	);");
	if($my->error)
		echo"<p>myerror $my->error</p>";

	$code = $my->query("CREATE TABLE IF NOT EXISTS Plants (
		id INT NOT NULL AUTO_INCREMENT,
		family INT NOT NULL,
		genus INT NOT NULL, 
		species INT NOT NULL,
		common VARCHAR(100),
		PRIMARY KEY(id),
		FOREIGN KEY ( family ) REFERENCES Family(id) ON DELETE CASCADE,
		FOREIGN KEY ( genus ) REFERENCES Genus(id) ON DELETE CASCADE,
		FOREIGN KEY ( species ) REFERENCES Species(id) ON DELETE CASCADE,
		CONSTRAINT UNIQUE KEY( family, genus, species) 
	);");
	if($my->error)
		echo"<p>myerror $my->error</p>";

	$code = $my->query("CREATE TABLE IF NOT EXISTS Images (
		id INT NOT NULL AUTO_INCREMENT,
		image VARCHAR(100) UNIQUE,
		plant_id INT,
		orientation INT,
		date DATETIME,
		PRIMARY KEY(id),
		FOREIGN KEY ( plant_id ) REFERENCES Plants(id) ON DELETE CASCADE
	);");
	if($my->error)
		echo"<p>myerror $my->error</p>";


	echo"<p>$my->error</p>";
	#Family 	Genus 	Species
	
	echo"<br/>";
	print_r($code);
	echo"<br/>";
	$root=$_SERVER['DOCUMENT_ROOT'];
	#echo "$root/../phplib/image.php";

	include("$root/../phplib/image.php");
	$glo=array();
	echo $root;
	$glo=array_merge(glob("$root/pics/Pics/plants/*.jpg"),$glo);
	
	echo "<p>start loop</p>";
	echo "<table>";
	foreach($glo as $key => $value){
				echo "<tr>";
				echo "<td>image is $value</td>";
				$cats = GetTags($value);
				$c=print_r($cats,true);
				echo "<td>catagories $c</td>";
				if($cats[2]==""){
					echo "<td>No info</td>";
					#$code = $my->query("INSERT IGNORE INTO Images (image) VALUES ('$value');");
					continue;
				}

				putPlants($cats[2],$cats[3],$cats[4],$cats[5],$value,$my);
				echo "</tr>";
	}
	echo "</table>";
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
	$map=$post['map'];
	unset($post['map']);
	$poly = json_decode($map);
	$poly = getPoly($poly);
	$p=json_encode($post);
	if(mysqli_connect_errno()){
		printf("Connection Error: %s\n" , mysqli_connect_error());
	}
	$pinfo=new stdClass();
	$plants=array();
	//print_r($poly);
	$query="CALL GetPictures('$p', (ST_GeomFromText('$poly')));";
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
?>

