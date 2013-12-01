<?php

require_once(SEO_PATH_ROOT.'/../querypath/src/QueryPath.php');
require_once(SEO_PATH_ROOT.'/../querypath/src/qp.php');
require_once(SEO_PATH_CLASS.'HtmlParserAdapter.php');

require_once SEO_PATH_WRAPPERS . 'Body.php';

class Body extends Controller{
	
	public function Body($method,$args=null){
		
		parent::__construct($method, $args);

        $qpa = new \api\HtmlParserAdapter($_GET['request']);
		$html = new \api\Body($qpa, $_GET['request']);
		$this->exec($html, $method, $args);
	}
}
?>