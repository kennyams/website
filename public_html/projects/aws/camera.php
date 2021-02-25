<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AWS Experiments</title>

</head>
<body>
	<header>
		<div id="right">
			<h1>Experiments with AWS API</h1>
		</div>
	</header>
<?php
	$root=$_SERVER["DOCUMENT_ROOT"];
	require("$root/../ssl/endpoints/iot.php");
	require "$root/../phplib/aws/sigv4.php";
  	echo "<textarea rows=\"50\" cols=\"180\">";
	$move=False;
	$pan=0;
	$tilt=0;
	$path="";
	$payload="{";
	if(array_key_exists('pan',$_GET)){
		$pan=$_GET['pan'];
		$path="/topics/sdk/pantilt";
		$payload.="\"pan\":".$pan.",";
		$move=True;
	}
	if(array_key_exists('tilt',$_GET)){
		$tilt=$_GET['tilt'];
		$path="/topics/sdk/pantilt";
		$payload.="\"tilt\":".$tilt.",";
		$move=True;
	}
	if(array_key_exists('click',$_GET)){
		print("click\n");
		$path="/topics/sdk/takePic";
		$payload.="\"takePic\":1,";
	}

	if($move){
	}
	$payload[strlen($payload)-1]="}";
	Send($path,$payload);
	echo "</textarea>";
?>
</body>
</html>
