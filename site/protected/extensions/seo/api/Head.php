<?php

namespace api;

class Head{
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $parser;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	private $meta;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	private $html; //html or xhtml
	
	/**
	 * @ignore
	 * @var unknown
	 */
	private $version;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	private $type; //transition, strict
	
	/**
	 * @ignore
	 * @param HtmlParser $parser
	 */
	public function __construct($parser){
		$this->parser = $parser;
	}
	
	/**
	 * Get the title element
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":{
        "hash":"00000000205ca01800000000088c5028",
        "host":"www.willsmelser.com",
        "raw":"<title>Mediocre Developer<\/title>",
        "tag":"title",
        "attributes":null,
        "textStart":7,
        "textEnd":25,
        "text":"Mediocre Developer"
    }
}	 
	 </json>
	 </p>
	 */
	public function getTitle(){
		$titles = $this->parser->getTags('title');
		if(count($titles)){
			return $titles[0];
		}
		return null;
	}
	
	/**
	 * Get the Meta Description tag content.
	 * The "content" attribute in the meta tag with attribute "name" or NULL if none exists
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"Get ready to learn SEO. Start with the Beginner's Guide to SEO and work your way through our up-to-date resources on how to perform essential SEO tasks."
}	 
	 </json>
	 </p>

	 */
	public function getMetaDesc(){
		foreach($this->parser->getTags("meta") as $entry){
			if(isset($entry->attributes)){
				if(isset($entry->attributes['name']) && strtolower($entry->attributes['name'])==='description'){
					return isset($entry->attributes['content'])?$entry->attributes['content']:null;
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Get the meta keywords content. The "content" attribute of meta tag with attribute "keywords"
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"HTML,CSS,XML,JavaScript,DOM,jQuery,ASP.NET,PHP,SQL,colors,tutorial,programming,development,training,learning,quiz,primer,lessons,reference,examples,source code,demos,tips,color table,w3c,cascading style sheets,active server pages,Web building,Webmaster"
}	 
	 </json>
	 </p>

	 */
	public function getMetaKeywords(){
		foreach($this->parser->getTags("meta") as $entry){
			if(isset($entry->attributes)){
				if(isset($entry->attributes['name']) && strtolower($entry->attributes['name'])==='keywords'){
					return isset($entry->attributes['content'])?$entry->attributes['content']:null;
				}
			}
		}
				
		return null;
	}

	/**
	 * Determine the doctype.
	 * The doctype in following format :<br/>
	 * <code>
	 * <html> <version> [type]
	 * 
	 * html = "HTML", "XHTML", "XML", etc...
	 * varsion = 5, 4.01, etc...
	 * type = [optional] transitional, strict, Frameset, etc... 
	 * </code> 
	 * 
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"xhtml 1.0 transitional"
}	 
	 </json>
	 </p>

	 */
	public function getDoctype(){
		if(!empty($this->html))
			return $this->html . ' ' . $this->version . ' ' . $this->type;
		
		foreach($this->parser->getTags('!DOCTYPE') as $doc){
			
			//parse the doctype
			$raw = $doc->raw;
			//check HTML 5
			if(preg_match('/html\>$/i',$raw)){
				return 'HTML 5';
			}else{
				//version
				preg_match('@(?P<html>\w+)\s+(?P<version>\d+\.\d+)\s+?(?P<type>\w+)?//@i',$raw,$matches);
				
				$this->html = strtolower($matches['html']);
				$this->version = strtolower($matches['version']);
				$this->type = strtolower($matches['type']);
				
				return $this->html . ' ' . $this->version . ' ' . $this->type;
			}
		}
		
		return null;
	}
	
	/**
	 * Match the meta tag with attribute "http-equiv" and return the charset value
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"UTF-8"
}	 
	 </json>
	 </p>

	 */
	public function getEncoding(){
		foreach($this->getMeta() as $meta){
			if(isset($meta->attributes['http-equiv']) && strtolower($meta->attributes['http-equiv']) === 'content-type'){
				if(isset($meta->attributes['content']) && 
						preg_match('@charset\=(?P<charset>.*);?@i',$meta->attributes['content'],$matches)){
					return $matches['charset'];
				}
			}
		}
		return null;
	}
	
	/**
	 * Attempt to find the lang attribute or xml:lang attribute of document
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"en-US"
}	 
	 </json>
	 </p>

	 */
	public function getLang(){
		//need to check what type this is
		$html = $this->parser->getTags('html');
		
		if(empty($html) || !isset($html[0]))
			return null;
		
		if(isset($html[0]->attributes['lang'])){
			return $html[0]->attributes['lang'];
		}elseif(isset($html[0]->attributes['xml:lang'])){
			return $html[0]->attributes['xml:lang'];
		}
		return null; 
	}
	
	/**
	 * Returns a fully qualified link (http://... included) to the favicon or NULL. Will check
	 * for link tag with the 'rel' attribute for favicon.
	 * 
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"http:\/\/inedo.com\/Resources\/Images\/Icons\/favicon.ico"
}	 
	 </json>
	 </p>

	 */
	public function getFavicon(){
		$result = false;
		
		//check link mechanish
		foreach($this->parser->getTags('link') as $link){
			if(isset($link->attributes['rel']) && $link->attributes['rel'] === 'icon'){
				if(preg_match('@^http@i',$link->attributes['href'])){
					return $link->attributes['href'];
				}else{
					return 'http://' . $link->host . '/' . ltrim($link->attributes['href'],'/\\');	
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Make a request to default favicon location (http://host.tld/favicon).  If this request fails, return null, 
	 * otherwise return the default fully qualified favicon location.  
	 * 
	 * @see getFavicon
	 * 
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/head/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"http:\/\/www.w3schools.com\/favicon.ico"
}	 
	 </json>
	 </p>
 
	 */
	public function getFaviconNoTag(){
		global $SUPRESS_ERROR;

		$SUPRESS_ERROR = true;
		
		@$url = file_get_contents('http://'.$this->parser->host.'/favicon.ico');
		if(!empty($url)){
			return 'http://'.$this->parser->host.'/favicon.ico';
		}
		
		$SUPRESS_ERROR = false;
		
		return null;
	}
	
	/**
	 * Used as a helper function to check for the meta tag.  Used to save
	 * repeated parsing if the meta information has already been parsed.
	 * @ignore
	 */
	private function getMeta(){
		if(empty($this->meta))
			$this->meta = $this->parser->getTags('meta');
		
		return $this->meta;
	}
}