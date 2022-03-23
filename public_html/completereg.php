<?php
	$root=$_SERVER["DOCUMENT_ROOT"];
	include "$root/session.php";
	$regok=false;
	$root=$_SERVER['DOCUMENT_ROOT'];
	include("$root/../phplib/mariadb.php");
	$id=session_id();
	if(isset($_GET["id"])){
		$uuid=$_GET["id"];
		if(RegisterUser($uuid)){
			$regok="<p>Registration OK</p>";
		}else{
			$regok="<p>Registration Failed</p>";
		}
		error_log("name $uuid");
	}
	if(isset($_POST['completereg'])){
		header("Location: /");
	}
?>
<html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="homepage/home.css" rel="stylesheet"/>
	<?php
		$root=$_SERVER["DOCUMENT_ROOT"];
		include "$root/head.php";
	?>
		<style>
			form div{
				display:grid;
				grid-template-columns:5em 10em;
			}
		</style>
	</head>
	<body>
<?php
			$root=$_SERVER["DOCUMENT_ROOT"];
			include "$root/header.php";
?>
		<main>
<?php
			echo $regok;
?>
			<form method="post" action="" name="signin">
				<div>
					<button type="submit" name="completereg" value="gohome" >Home</button>
				</div>
				<!--button type="submit" name="login" value="login" onclick="history.back()">Login</button-->
			</form>
		</main>
		<?php
			include "$root/footer.php";
		?>
	</body>
</html>

