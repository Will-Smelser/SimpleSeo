<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 12/10/13
 * Time: 6:24 PM
 */

namespace simple_seo_api;

require_once __DIR__ . '/interfaces.php';
require_once __DIR__ . '/class/PageLoad.php';

final class SeoApi {
    private $config;

    private $load = array();
    private $pageLoader = null;
    private $token = null;

    public function __construct($config, ThreadRequests $pageLoader, $token){
        $this->config = $config;
        $this->pageLoader = $pageLoader;
        $this->token = $token;
    }

    public function addMethods($controller, $varArgsMethods){
        //validate controller
        if(!in_array($controller,$this->config['API_CONTROLLERS']))
            throw new \InvalidArgumentException('Controller does not exist.  Check $config.');

        $args = func_get_args();
        array_shift($args);

        if(!isset($this->load[$controller]))
            $this->load[$controller] = array();

        if(count($this->load[$controller]) > 0)
            $args = array_merge($this->load[$controller],$args);

        $this->load[$controller] = $args;

        return $this;
    }

    public function exec($request){
        if(count($this->load) == 0)
            throw new RuntimeException('Invalid State. No Controllers loaded.');

        reset($this->load);
        foreach($this->load as $controller=>$methods){
            if(empty($methods) || count($methods) < 1)
                throw new \RuntimeException('Invalid State. No methods for controller('.$controller.')');

            $url = $this->createUrl($controller, $request, $methods);

            $this->pageLoader->addPage($url,$controller);
        }

        $result = $this->pageLoader->exec();
        $this->reset();

        return $result;
    }

    private function createUrl($controller, $request, array $methods){
        if(empty($controller))
            throw new \InvalidArgumentException('Invalid controller.');
        if(empty($methods) || count($methods) == 0)
            throw new \InvalidArgumentException('No methods.');
        if(!in_array($controller,$this->config['API_CONTROLLERS']))
            throw new \InvalidArgumentException('Controller does not exist.  Check $config.');

        return $this->config['API_PROTOCOL'] . '://' .
                $this->config['API_HOST'] . $this->config['API_PATH'] . '/' .
                $controller . '/' . implode('|',$methods) .
                '?token=' . $this->token . '&request=' . urlencode($request);
    }

    public function reset(){
        $this->load = array();
        $this->pageLoader->reset();
    }
} 