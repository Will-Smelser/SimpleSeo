<?php

require_once SEO_PATH_WRAPPERS . 'Semrush.php';

class Semrush extends Controller{
	
	public $skip = array();
	
	public function Semrush($method,$args=null){
		parent::__construct($method, $args);
		
		$obj = new \api\SemRush($_GET['request']);
		
		$this->exec($obj, $method, $args);
	}

}
?>