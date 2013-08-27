<?php
require_once SEO_PATH_CLASS . 'HtmlParser.php';
require_once SEO_PATH_WRAPPERS . 'HtmlHeadWrap.php';

class Head extends Controller{
	
	public function Head($method, $args=null){
		
		parent::__construct($method, $args);

		$content = file_get_contents($_GET['request']);

		error_reporting(E_ALL);
		$parser = new HtmlParser($content, $_GET['request']);
		$head = new \api\HtmlHeadWrap($parser);
		
		$this->exec($head, $method, $args);
	}
}
?>