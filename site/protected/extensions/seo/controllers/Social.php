<?php

error_reporting(E_ALL);
require_once SEO_PATH_WRAPPERS . 'Social.php';

class Social extends Controller{
	
	public function Social($method, $args=null){
		parent::__construct($method, $args);
		
		$social = new \api\Social($_GET['request']);
		
		$this->exec($social, $method, $args);
	}

}
?>