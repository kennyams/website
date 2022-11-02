<h1>Welcome to pub.me.uk</h1>
<p>This site is run as a hobby and for personal development, if you have any comment please feel free to send an email</p>
<h2>The site</h2>
<p>The plants section has images of various flowers.The pictures are of plants that I have come accoss whilst out walking.There is a map, courtesy of <span><a href="https://www.openstreetmap.org">OpenStreetMap</a></span> and identifications courtesy of <span> <a href="https://plantnet.org/en/">Plantnet</a></span> </p>
<?php
	$row = GetRandomImage();
	$r=$row['image'];
	$x=explode("/",$r);
	function cat($carry,$item){
		$carry=$carry."/".$item;
		return $carry;
	}
	$x=array_slice($x,4);
	$x=array_reduce($x,"cat");
	print_r('<img src="'."$x".'" width="200" alt="Random plant picture">');
?>
