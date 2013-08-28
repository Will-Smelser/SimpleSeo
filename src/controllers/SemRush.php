<?php

require_once SEO_PATH_WRAPPERS . 'SemRush.php';

class SemRush extends Controller{
	
	public $skip = array();
	
	public function SemRush($method,$args=null){
		parent::__construct($method, $args);
		
		$obj = new \api\SemRush($_GET['request']);
		
		$this->exec($obj, $method, $args);
	}

}
?>

