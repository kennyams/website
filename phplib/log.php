<?php
//phpinfo();

	function Logs($msg){
		$msg=print_r($msg,true);
		$bt = debug_backtrace();
		$caller = array_shift($bt);
		error_log(sprintf("[%10s][%5s] %s",$caller['file'],$caller['line'],$msg));
	}
?>
