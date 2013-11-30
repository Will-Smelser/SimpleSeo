<?php

require_once(SEO_PATH_ROOT.'/../querypath/src/QueryPath.php');
require_once(SEO_PATH_ROOT.'/../querypath/src/qp.php');

require_once SEO_PATH_WRAPPERS . 'Body.php';

class Body extends Controller{
	
	public function Body($method,$args=null){
		
		parent::__construct($method, $args);
		
		//$content = @file_get_contents($_GET['request']);
        $qp = htmlqp($_GET['request'],'body');
		
		//$parser = new HtmlParser($content, $_GET['request']);
		$html = new \api\Body($qp, $_GET['request']);
		
		$this->exec($html, $method, $args);
	}
}
?>