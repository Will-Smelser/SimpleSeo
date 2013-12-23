<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 12/14/13
 * Time: 8:35 PM
 */

//load dependencies
require_once SEO_PATH_ROOT . 'apiuser/SeoApi.php';
require_once SEO_PATH_ROOT . 'apiuser/class/PageLoad.php';
require_once SEO_PATH_ROOT . 'apiuser/class/ClientHash.php';

class SeoApiUserAdapter {
    private $config = null;
    private $seo = null;

    public function __construct($config,$key,$user,$host=SEO_HOST){
        $this->config = $config;
        $token = simple_seo_api\ClientHash::getToken($key,$user,$host);
        $thread = new simple_seo_api\PageLoad();
        $this->seo = new simple_seo_api\SeoApi($config, $thread, $token);
    }

    public function execAll($target){
        //foreach($this->config['API_CONTROLLERS'] as $controller)
            //$this->seo->addMethods($controller,'all');

        $this->seo->addMethods('Server','all');
        return $this->parse($this->seo->exec($target));
    }

    private function parse($result){
        $temp = array();
        foreach($result as $key=>$data){
            $temp[$data['info']] = &$result[$key];
        }
        return $temp;
    }
}