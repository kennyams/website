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
				$paragraph=$row['text'];
				array_push($articles,$paragraph);
			}
		}
		$my->close();
		return $articles;
	}
?>
