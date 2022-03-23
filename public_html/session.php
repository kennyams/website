<?php
	$ss=session_status();
	if($ss!==PHP_SESSION_ACTIVE){
		ini_set( 'session.cookie_httponly', 1 );
		session_start();
	}
?>
