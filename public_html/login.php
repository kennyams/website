<?php 
	$root=$_SERVER["DOCUMENT_ROOT"];
	include "$root/session.php";
	error_log("login.php");
	$password_fail=false;
	$ss=session_status();
	if($ss!==PHP_SESSION_ACTIVE){
		session_start();
		session_set_cookie_params(60);
		error_log("session started");
	}
	include("$root/../phplib/mariadb.php");
	$name="";
	$email="";
	if(isset($_SESSION["name"])){
		header('Location: /profile.php');
		die();
	}
	if(isset($_POST['login'])){
		error_log("logging in");
		$name=$_POST['name'];
		$email=$_POST['email'];
		$password=$_POST['password'];
		$hash=password_hash($password,null);
		$h=CheckUser($email,$hash);
		error_log($h);
		$ok=password_verify($password,$h);
		error_log($ok);
		if($ok){
			$_SESSION["name"]=$name;
			$_SESSION["email"]=$email;
			error_log("ok");
			header('Location: /');
			die();
		}else{
			$password_fail=true;
			error_log("nok");
		}
	}else if(isset($_POST['register'])){
		error_log("register");
		$name=$_POST['name'];
		$email=$_POST['email'];
		$password=$_POST['password'];
		if(IsUser($email)){
			error_log("user exists");
			echo "<p>User Exists</p>";
			exit();
		}
		$to="admin@pub.me.uk";
		$subject="Confirm Registration";
		$uuid=bin2hex(random_bytes(16));

		$message = "
		<html>
			<head>
				<title>HTML email</title>
			</head>
			<body>
				<p>Please follow link to complete registration</p>
				<a href=\"https://pub.me.uk/completereg.php?id=$uuid\">Register</a>
				<!--a href=\"/completereg.php?id=$uuid\">Register</a-->
			</body>
		</html>
		";
		//echo $message;
		$hash=password_hash($password,null);
		AddUser($name,$email,$hash,$uuid);
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .="From: admin@pub.me.uk"."\r\n";

		$res=mail($email,$subject,$message,$headers);
		if($res){
			error_log("emailed ok $res");
		}else{
			error_log("emailed nok");
		}
	}	
?>
<!DOCTYPE html>
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
			<h2>Please login or register</h2>
			<form method="post" action="" name="signin">
				<div>
					<label>Name</label>
					<input type="text" name="name" value="<?php print_r($name)?>" pattern="[a-zA-Z0-9]+" required />
					<label>Email</label>
					<input type="email" name="email" value="<?php print_r($email)?>" required />
					<label>Password</label>
					<input type="password" name="password" required />
				</div>
<?php
	if($password_fail){
				echo "<p>Password wrong, try again</p>";
	}
?>
				<!--button type="submit" name="login" value="login" onclick="history.back()">Login</button-->
				<button type="submit" name="login" value="login" >Login</button>
				<button type="submit" name="register" value="register" >Register</button>
			</form>
		</main>
		<?php
			include "$root/footer.php";
		?>
	</body>
</html>


