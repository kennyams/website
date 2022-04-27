<?php
	$ss=session_status();
	if($ss!==PHP_SESSION_ACTIVE){
		ini_set( 'session.cookie_httponly', 1 );
		ini_set('session.use_only_cookies', 1);
		ini_set('session.cookie_secure', 1);
		session_start();
	}
	$root=$_SERVER["DOCUMENT_ROOT"];
	include_once "$root/../phplib/mariadb.php";
	if(!isset($_COOKIE['settings'])){
		$options = array (
			'expires' => time()+60*60*24*30,
			'path' => '',
			'domain' => '', // leading dot for compatibility or use subdomain
			'secure' => true,     // or false
			'httponly' => true,    // or false
			'samesite' => 'Strict' // None || Lax  || Strict
			);
		$uuid=bin2hex(random_bytes(16));
		$ok = setcookie('settings',$uuid,$options);
		if($ok){
			NewCookie($uuid);
		}
	}else{
		$uuid=$_COOKIE['settings'];
	}
	$settings = Cookie($uuid);
	$dbg=print_r($settings,true);
	error_log("settings are $dbg");
	error_log("login details are $dbg");
	if($settings==null or count($settings)==0){
		error_log("Warning, cookie not in database");
		NewCookie($uuid);
		$settings = Cookie($uuid);
		$dbg=print_r($settings,true);
		error_log("default settings are $dbg");
	}
	$name="";
	$email="";
	if(isset($settings['user_id'])){
		$loginDetails = GetLoginDetails($settings['user_id']);
		$dbg=print_r($loginDetails,true);
		$name=$loginDetails['user'];
		$email=$loginDetails['email'];
	}
?>
