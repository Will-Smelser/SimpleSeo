<?php
include_once SEO_PATH_CLASS.'PageLoad.php';
include_once SEO_PATH_CLASS.'HtmlParser.php';
include_once SEO_PATH_CLASS.'CrawlerUtils.php';

error_reporting(E_ALL);
$resp = new PageLoadResponse();

//get start
$start = microtime(true);

$result = '';
if(isset($_GET['arg0'])){
    $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

    //make a curl request
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $_GET['arg0']);
    curl_setopt($curl, CURLOPT_USERAGENT, $agent);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($curl);
    curl_close($curl);
}

//get finish
$finish = microtime(true);
$total_time = round(($finish - $start), 4);

$resp->url = $_GET['arg0'];
$resp->start = $start;
$resp->finish = $finish;
$resp->totalSeconds = $total_time;

//no follow?
$obeyNoFollow = true;
if(isset($_GET['arg1']) && $_GET['arg1'] == 'false')
    $obeyNoFollow = false;

//host
$host = CrawlerUtils::getBaseHost($_GET['arg0']);

//parse the page
$parser = new HtmlParser($result,$_GET['arg0']);
$links = $parser->getTags('a');

$resp->links = array();
foreach($links as $node){
    if(
        !empty($node->attributes['href']) &&
        !(isset($node->attributes['rel']) && strtolower($node->attributes['rel']) === 'nofollow' && $obeyNoFollow) &&
        strpos($node->attributes['href'],'mailto:') !== 0
    ){
        $link = $node->attributes['href'];

        //validate hosts
        $lHost = CrawlerUtils::getBaseHost($link);
        if(empty($lHost))
            $lHost = $host;

        if($lHost === $host){
            $normal = CrawlerUtils::normalizePath($link,$_GET['arg0']);

            if(!in_array($normal,$resp->links)){
                array_push($resp->links, $normal);
            }
        }

    }
}
echo json_encode($resp);

?>