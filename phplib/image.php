<?php
$catagories=array();
$depth=0;
function godown($sxe){
	global $catagories;
	global $depth;
	foreach($sxe as $k => $v){
		array_push($catagories,$v->__ToString());
		$depth++;
		godown($v);
	}
}
function start_element_handler ( $parser , $name , $attribs ){
	if($name=="RDF:DESCRIPTION"){
		$cats=$attribs['ACDSEE:CATEGORIES'];
		$cats = str_replace('&','&amp;',$cats);
		$xml = new SimpleXMLElement($cats);
		godown($xml);
	}
}
function end_element_handler ( $parser , $name ){
}
function GetTags($file){
	global $catagories;
	$catagories=array();
	$depth=0;
	$fp=fopen($file,"r");
	$go=true;
	while($go && !feof($fp)){
		$segment=bin2hex(fread($fp,2));
		switch($segment){
		case "ffd8":
			break;
		case "ffe1":
		case "ffed":
			$segment=fread($fp,2);
			$l=hexdec(bin2hex($segment));
			$segment=fread($fp,$l-2);
			$ex=explode("\x00",$segment);

			try{
			$parser = xml_parser_create();
			xml_set_element_handler( $parser, 'start_element_handler' ,'end_element_handler');
			$status = xml_parse($parser,$ex[1]);
			$status = xml_parse($parser, '', true);
			}catch(Exception $e){
				echo "Error $e";
			}
			break;
		case "ffe4":
			$segment=fread($fp,2);
			$l=hexdec(bin2hex($segment));
			fseek($fp,$l-2,SEEK_CUR);
			break;
		case "ffc0":
			$segment=fread($fp,2);
			$l=hexdec(bin2hex($segment));
			fseek($fp,$l-2,SEEK_CUR);
			$go=False;
			break;
		default:
			$go=False;
			break;
		}
	}
	fclose($fp);
	return $catagories;
}
?>
