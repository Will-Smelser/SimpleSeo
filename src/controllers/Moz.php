<?php

require_once SEO_PATH_WRAPPERS . 'Moz.php';

class Moz extends Controller{
	
	public $skip = array();
	
	public function Moz($method, $args=null){
		parent::__construct($method, $args);
		
		$moz = new \api\Moz($_GET['request']);
		
		$this->exec($moz, $method, $args);
	}

}
?>
