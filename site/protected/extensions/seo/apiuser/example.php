<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 12/12/13
 * Time: 8:52 PM
 */

require_once 'SeoApi.php';
require_once 'class/PageLoad.php';
require_once 'class/ClientHash.php';

error_reporting(E_ALL);

try{
    $config = include 'config.php';
    $token = simple_seo_api\ClientHash::getToken('<your key>','sample','www.simple-seo-api.local');
    $thread = new simple_seo_api\PageLoad();
    $seo = new simple_seo_api\SeoApi($config, $thread, $token);

    $seo->addMethods('Body','checkH1','checkH2');
    $seo->addMethods('Server','all');

    $result = $seo->exec('www.kafekerouac.com');
}catch(Exception $e){
    echo $e;
    exit;
}

var_dump($result);