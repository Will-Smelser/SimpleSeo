<?php

require_once SEO_PATH_WRAPPERS . 'W3c.php';

class W3c extends Controller{
	
	public function W3c($method, $args=null){

		parent::__construct($method, $args);

		$w3c = new \api\W3c($_GET['request']);

		$this->exec($w3c, $method, $args);
	}

}
?>