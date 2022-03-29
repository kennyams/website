<?php
	$ss=session_status();
	if($ss!==PHP_SESSION_ACTIVE){
		ini_set( 'session.cookie_httponly', 1 );
		ini_set('session.use_only_cookies', 1);
		ini_set('session.cookie_secure', 1);
		session_start();
	}
?>
