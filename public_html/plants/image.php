<?php
	$imageurl=$_GET['url'];
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/head.php";
		?>
		<link href="plants_v1.css" rel="stylesheet"/>
		<style>
			img#zoompic{
				overflow:clip;
				width:100vw;
				height:100vh;
				object-fit:contain;
			}
			
			main{
				display:block;
			}
			#picframe{
				width:100%;
				overflow:hidden;
			}
		</style>
	</head>
	<body>
		<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
		?>
		<main>
				<div id="picframe">
					<img draggable="false" id="zoompic" src="<?=$imageurl?>">
				</div>
		</main>
		<?php
			include "$root/footer.php";
		?>
	</body>
	<script>
		var scale=1;
		var dragging=false;
		var x=0;
		var y=0;
		$(document).ready(function(){
			$("img#zoompic").on("mousemove",function(e){
				e.preventDefault();
				//var x=e.originalEvent.layerX;
				//var y=e.originalEvent.layerY;
				if(dragging){
					x=x+e.originalEvent.movementX;
					y=y+e.originalEvent.movementY;
					$("img#zoompic").css("transform",`matrix(${scale},0,0,${scale},${x},${y})`);
				}
			});
			$("img#zoompic").on("mousedown",function(e){
				dragging=true;
			});
			$("img#zoompic").on("mouseup",function(e){
				dragging=false;
			});
			$("img#zoompic").bind("mousewheel DOMMouseScroll",function(e){
				e.preventDefault();
				dragging=false;
				var t = e.target;
				
				if(e.detail<0){
					scale=scale*1.1;
				}else if(e.detail>0){
					scale=scale*.9;
				}
				//$("img#zoompic").css("transform-origin",`${x}px ${y}px`);
				//$("img#zoompic").css("transform-origin",`${0}px ${0}px`);
				//$("img#zoompic").css("transform",`scale(${scale})`);
				$("img#zoompic").css("transform",`matrix(${scale},0,0,${scale},${x},${y})`);
			});
		});
	</script>
</html>
