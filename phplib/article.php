<?php
	function article($name){
		$a=getArticle($name);
		foreach($a as $k => $p){
			echo "<p>$p</p>";
		}
	}
?>
