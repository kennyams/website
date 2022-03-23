<!DOCTYPE html>
<?php
	$root=$_SERVER["DOCUMENT_ROOT"];
	include "$root/session.php";
	foreach($_SESSION as $key => $value){
		error_log( "$key, $value");
	}
	foreach($_POST as $key => $value){
		error_log( "$key, $value");
	}
	if(isset($_POST['logout'])){
		session_unset();
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
			<p>Profile</p>
			<form method="post" action="" name="signin">
				<div>
					<label>Logout</label>
					<button type="submit" name="logout" value="logout" >Logout</button>
				</div>
				<!--button type="submit" name="login" value="login" onclick="history.back()">Login</button-->
			</form>
		</main>
		<?php
			include "$root/footer.php";
		?>
	</body>
</html>

