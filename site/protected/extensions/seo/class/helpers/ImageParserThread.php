<?php
include SEO_PATH_CLASS.'ImageParser.php';
require_once SEO_PATH_HELPERS . 'ApiResponse.php';
require_once SEO_PATH_HELPERS . 'FileGetContentsAdapter.php';

//get start
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;


$url = urldecode($_GET['arg0']);
$hash = $_GET['arg1'];
$width= $_GET['arg2'];
$height = $_GET['arg3'];

$image = false;

try{
	//This is a normal url
	if(preg_match('@^https?://@i',$url)){
		$turl = str_replace('https://','http://',$url);
        $image = imagecreatefromstring(\api\FileGetContentsAdapter::get_content($turl));
		//$image = imagecreatefromstring(file_get_contents($turl));
		
	//this was a data string...might be problems with request string being too long
	}elseif(preg_match('/^data/',$url)){
		$image = imagecreatefromstring($url);
		
	}
}catch(Exception $e){
	//do nothing
}

if($image !== false){
	//get finish
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	
	$x = imagesx($image);
	$y = imagesy($image);
	
	//print response
	$resp = new ImageLoadResponse();
	$resp->hash = $hash;
	$resp->url = $url;
	$resp->result = ImageParser::respond($image, $width, $height, $total_time);
	$resp->time = $total_time;
	$resp->htmlWidth = $width;
	$resp->htmlHeight = $height;
	$resp->actualWidth = $x;
	$resp->actualHeight = $y;
	
	
	$code = \api\responses\ApiCodes::$success;

	$response = $response = (new \api\responses\ApiResponseJSON())->success('Image load complete.', $resp);
	echo $response->doPrint();
	
}else{
	$code = \api\responses\ApiCodes::$notFound;
	$response = $response = (new \api\responses\ApiResponseJSON())->failure('Failed to load image.',$code);
	echo $response->doPrint();
}

?>