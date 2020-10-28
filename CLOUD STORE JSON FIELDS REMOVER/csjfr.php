<?php
function str_replace_first($from, $to, $content){
    $from = '/'.preg_quote($from, '/').'/';
    return preg_replace($from, $to, $content, 1);
}

function removeFieldKey($search, $json, $prefix = '', $sufix = '', $replaceComplement = ''){
	$search = '{"'.$search.'":'.$prefix;

	$jsonDiv = explode($search,$json,2);
	while(strpos($json,$search) !== false){
		$value = str_replace_first($sufix.'}',$replaceComplement.'',$jsonDiv[1]);
		$json= $jsonDiv[0].$value;
		$jsonDiv = explode($search,$json,2);
	}
	return $json;
}
//your cloud json response
$jsonResponse = json_encode(json_decode(file_get_contents('orders.txt'),true));

$jsonResponse = removeFieldKey('stringValue',$jsonResponse);
$jsonResponse = removeFieldKey('doubleValue',$jsonResponse);
$jsonResponse = removeFieldKey('booleanValue',$jsonResponse);
$jsonResponse = removeFieldKey('integerValue',$jsonResponse,'"','"');
$jsonResponse = removeFieldKey('timestampValue',$jsonResponse);
$jsonResponse = removeFieldKey('geoPointValue',$jsonResponse);
$jsonResponse = removeFieldKey('nullValue',$jsonResponse);
$jsonResponse = removeFieldKey('referenceValue',$jsonResponse,);
$jsonResponse = removeFieldKey('bytesValue',$jsonResponse);
$jsonResponse = removeFieldKey('mapValue":{"fields',$jsonResponse,'','}');
$jsonResponse = removeFieldKey('arrayValue":{"values',$jsonResponse,'','}]}','}]');



echo $jsonResponse;
?>