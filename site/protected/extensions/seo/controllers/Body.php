<?php

require_once SEO_PATH_CLASS . 'HtmlParser.php';
require_once SEO_PATH_WRAPPERS . 'Body.php';

class Body extends Controller{
	
	public function Body($method,$args=null){
		
		parent::__construct($method, $args);
		
		$content = @file_get_contents($_GET['request']);
		
		$parser = new HtmlParser($content, $_GET['request']);
		$html = new \api\Body($parser, $_GET['request']);
		
		$this->exec($html, $method, $args);
	}
}
?>