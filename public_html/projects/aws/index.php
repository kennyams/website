<!DOCTYPE html>
<html>
	<head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
		?>
		<title>AWS Experiments</title>
		<script src="https://sdk.amazonaws.com/js/aws-sdk-2.632.0.min.js"></script>
		<link href="aws.css" rel="stylesheet"/>
	</head>
	<body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
		<main>
			<div id="pan"></div>
			<div id="tilt"></div>
			<button id="snap" onclick="takePic()">click</button>
			<img id="pic" src="https://iotpicbucket.s3.eu-west-2.amazonaws.com/pic.jpg?"+performance.now() style="width:100;image-orientation: from-image;" width="100%" />
			<iframe id="vid" src="https://www.youtube.com/embed/rQw8OsW6o3c" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<!--width="928" height="522"-->
		</main>
	<script>
		$("#pagetitle").html("<p>Internet of things(IOT) demo</p>");
		$("#tilt").slider({
			orientation: "vertical",
			min:-90,
			max:90,
			step:5,
			change:function(event, ui){
				console.log(ui.value);
				var xhttp = new XMLHttpRequest();
				xhttp.open("GET", "camera.php?tilt="+ui.value);
				xhttp.send();
				window.setTimeout(takePic,1000);
				//window.setTimeout(getStatus,300);
			}
		});
		$("#pan").slider({
			min:-90,
			max:90,
			step:5,
			change:function(event, ui){
				console.log(ui.value);
				var xhttp = new XMLHttpRequest();
				xhttp.open("GET", "camera.php?pan="+ui.value);
				xhttp.send();
				window.setTimeout(takePic,1000);
				//window.setTimeout(getStatus,300);
			}
		});
		function loadPic(){
			console.log("loadPic");
			var pic = document.getElementById("pic").src="https://iotpicbucket.s3.eu-west-2.amazonaws.com/pic.jpg?"+performance.now();
		}
		function takePic(){
			console.log("takePic");
			var xhttp = new XMLHttpRequest();
			xhttp.open("GET", "camera.php?click=1");
			xhttp.send();
			window.setTimeout(loadPic,3000);
			//window.setTimeout(getStatus,300);
		}
		function movev(event){
			console.log("move");
			var ang = event.currentTarget.value;
			var id = event.currentTarget.id;
			console.log(ang);
			var xhttp = new XMLHttpRequest();
			if(id=="pan"){
				xhttp.open("GET", "camera.php?pan="+ang);
			}else{
				xhttp.open("GET", "camera.php?tilt="+ang);
			}
			xhttp.send();
			window.setTimeout(takePic,1000);
			//window.setTimeout(getStatus,300);

		}
		function getStatus(){
			var xhttp = new XMLHttpRequest();
			xhttp.addEventListener("load",function(){
				try{
						var pos = JSON.parse(this.responseText);
						console.log(pos.tilt);
						console.log(pos.pan);
						document.getElementById("tilt").value = pos.tilt;
						document.getElementById("pan").value = pos.pan;
					}catch{
					}
			});
			try{
				// TODO var res = xhttp.open("GET", "status.php");
			}catch{
			}
			xhttp.send();
		}

		document.getElementById("pan").addEventListener("change",movev);
		document.getElementById("tilt").addEventListener("change",movev);
	</script>
	</body>
</html>

