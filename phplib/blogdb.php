<?php
	function getArticle($article){
		$my=connect();
		if(mysqli_connect_errno()){
			printf("Connection Error: %s\n" , mysqli_connect_error());
		}
		$query="CALL GetArticle('$article');";
		$articles=array();
		if($result = $my->query($query)){
			while($row = $result->fetch_assoc()){
				//$paragraph=new stdClass();
				//$paragraph->paragraph=$row['text'];
				$paragraph=$row['text'];
				error_log($paragraph);
				array_push($articles,$paragraph);
			}
		}
		$my->close();
		return $articles;
	}
?>
