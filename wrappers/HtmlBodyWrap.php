<?php
namespace api;

include SEO_PATH_CLASS . 'WordCount.php';

/**
 * <p>These methods focus on parsing a web page's body.</p>  
 * <p>
 * All methods return an array which is converted to appropriate
 * response object (JSON). 
 * </p>
 * @see ApiResponseJSON
 * @author Will
 *
 */
class HtmlBodyWrap{
	
	/**
	 * @ignore
	 * @var unknown
	 */
	protected $anchors;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $parser;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $wc;
	
	/**
	 * Constructor
	 * @param HtmlParser $parser The html parser to use.
	 * @ignore
	 */
	public function __construct($parser){
		$this->parser = $parser;
	}
	
	/**
	 * Get all h1 tags
	 * 
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return responses\ApiResponseJSON JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>{method}</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 * 	<json>
		{
			"response":"Success",
			"error":false,
			"msg":"Success",
			"data":[
				{
					"hash":"0000000031800cf80000000019d4543d",
					"host":"www.willsmelser.com",
					"raw":"<h1 id=\"blog-name\"><a href=\"http:\/\/mediocredeveloper.com\/wp\">Mediocre Developer<\/a><\/h1>",
					"tag":"h1",
					"attributes":{
						"id":"blog-name"
					},
					"textStart":19,
					"textEnd":83,
					"text":"<a href=\"http:\/\/mediocredeveloper.com\/wp\">Mediocre Developer<\/a>"
				}
			]
		}
		</json>
		</p>
	 */
	public function checkH1(){
		return $this->parser->getTags('h1');		
	}
	
	/**
	 * Get all h2 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @api
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>checkH2</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	 {
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "hash":"0000000051935ef8000000003f96a9d5",
            "host":"www.willsmelser.com",
            "raw":"<h2 class=\"post-title\"><a rel=\"bookmark\" title=\"Permanent Link to Simple OAI Repository in PHP\" href=\"http:\/\/mediocredeveloper.com\/wp\/?p=73\">Simple OAI Repository in PHP<\/a><\/h2>",
            "tag":"h2",
            "attributes":{
                "class":"post-title"
            },
            "textStart":23,
            "textEnd":173,
            "text":"<a rel=\"bookmark\" title=\"Permanent Link to Simple OAI Repository in PHP\" href=\"http:\/\/mediocredeveloper.com\/wp\/?p=73\">Simple OAI Repository in PHP<\/a>"
        }
       ]
       }
	   </json>
	   </p>
	 *  
	 */
	public function checkH2(){
		return $this->parser->getTags('h2');
	}

	/**
	 * Get all h3 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	 
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "hash":"0000000007df25d1000000003f5c0339",
            "host":"www.willsmelser.com",
            "raw":"<h3>1. Setup Our Feature Layer<\/h3>",
            "tag":"h3",
            "attributes":null,
            "textStart":4,
            "textEnd":30,
            "text":"1. Setup Our Feature Layer"
        }]}
	 </json>
	 </p>
	 */
	public function checkH3(){
		return $this->parser->getTags('h3');
	}
	
	/**
	 * Get all h4 tags
	 * @see HtmlParser->getTags()
	 * @see Node
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	 
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "hash":"0000000017717e3e000000005f82c306",
            "host":"www.willsmelser.com",
            "raw":"<h4>Getting URL vars in PHP<\/h4>",
            "tag":"h4",
            "attributes":null,
            "textStart":4,
            "textEnd":27,
            "text":"Getting URL vars in PHP"
        }]}
	 </json>
	 </p>
	 
	 */
	public function checkH4(){
		return $this->parser->getTags('h4');
	}
	
	/**
	 * Get a list of key words, top 25
	 * @param integer $count Top X keywords to return. Default is 25
	 * @see Word
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>/<api-var>5</api-var> ?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	 
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "normal":"hash",
            "count":98,
            "words":[
                "hash",
                "-hash"
            ]
        },
        {
            "normal":"function",
            "count":63,
            "words":[
                "function",
                "functions"
            ]
        },
        {
            "normal":"imag",
            "count":56,
            "words":[
                "image",
                "images"
            ]
        },
        {
            "normal":"url",
            "count":42,
            "words":[
                "url"
            ]
        },
        {
            "normal":"char",
            "count":39,
            "words":[
                "char"
            ]
        }
    ]
}
	 </json>
	 </p>
	 */
	public function getKeyWords($count=25){
		if(is_array($count))
			$count = (count($count) < 1) ? 0 : 1 * $count[0];
		if($count < 1)
			$count = 25;
		return array_slice($this->getWC()->getCount(), 0, $count);
	}
	
	/**
	 * Get phrases for the top words
	 * @param integer $count The top X words to search, defaul is 5
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>/<api-var>3</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	 {
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":{
        "hash":[
            "giving us a simple hash",
            "us a simple hash table",
            "hash table",
            "is that so lets create a hash",
            "create a hash of the url",
            "a hash of the url and use",
            "hashcode function str"],
          "function":[
            "my pain im not against functional",
            "im not against functional languages",
            "not against functional languages but really",
            "to implement the basic functionality",
            "the basic functionality",
            "that will handle some of the basic functionality",
            "some of the basic functionality we will be using"
           ],
           "imag":[
            "creating an image preloader",
            "creating an image preloader",
            "creating an image preloader",
            "an image preloader",
            "an image preloader",
            "an image preloader"
            ]
         }
        }
	 </json>
	 </p>  
	 */
	public function getPhrases($count=5){
		if(is_array($count))
			$count = (count($count) < 1) ? 0 : 1 * $count[0];
		if($count < 1)
			$count = 5;
		
		$result = array();
		foreach($this->getKeyWords($count) as $word){
			$result[$word->normal] = $this->getWC()->getPhrasesWithWord($word->normal);
		}
		
		return $result;
	}
	
	/**
	 * @ignore
	 * @param unknown $arg
	 * @param unknown $arg1
	 * @param unknown $argIndex
	 * @param unknown $default
	 * @return int
	 */
	private function getDefault($arg, $arg1, $argIndex, $default){
		//came from api, arguments are an array
		if(is_array($arg1)){
			return (count($arg1) > $argIndex && $arg1[$argIndex] > 0) ? $arg1[$argIndex]*1 : $default;
		}
		return ($arg*1 > 0) ? $arg*1 : $default;
	}
	
	/**
	 * Look at top X words and get phrases in the document which
	 * match the given word.
	 * 
	 * @param integer $count Number of results to return in result set
	 * 
	 * @param integer $thresh Default is 3, Max is 3.
	 * 
	 * The threshold for minimum number of words
	 * that must exist for it to be considered a phrase.  This is becuase
	 * some single words, like a navigation link, are seen as a single
	 * word phrase.  This is because phrases stop at block boundries such
	 * as &lt;li&gt; elements.  So if a navigation element contained in a list
	 * had innerHtml or "this is a link", the normalized word count would be 2.  So
	 * a threshold of 3 will not include this phrase.<br>
	 * 
	 * Threshold must be considered delicately.  Phrases are determined by their
	 * normalized word count.  For example, "today is a good day", normalized becomes
	 * "today good day".  The threshold will look at this normalized phrase.<br>
	 * 
	 * <b>NOTICE:</b><br>It should also be considered that internally phrases are built with a normalized
	 * phrase length of 3.  So this means there will be no normalized phrases longer
	 * than 3, but there can be phrases shorted.  Future implementations will hopefully
	 * not have this constraint.
	 * 
	 * @see Phrase
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>/<api-var>2</api-var>/<api-var>3</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "normal":"hash hash char",
            "actual":[
                "hash -hash char",
                "hash -hash char",
                "hash -hash char",
                "hash -hash char",
                "hash -hash char",
                "hash - hash char",
                "or hash hash - char",
                "or hash hash char",
                "hash -hash char",
                "hash hash char",
                "hash hash char",
                "hash hash char",
                "hash hash char",
                "hash hash char",
                "hash hash char",
                "or hash hash char"
            ],
            "count":16
        },
        {
            "normal":"hash hash hash",
            "actual":[
                "i hash hash -hash",
                "hash hash hash",
                "hash hash -hash",
                "hash hash hash",
                "i hash hash -hash",
                "hash hash hash",
                "hash hash -hash",
                "hash hash -hash",
                "as hash hash - hash",
                "i hash hash -hash",
                "hash hash hash"
            ],
            "count":11
        }
    ]
} 
	 </json>
	 </p>
	 */
	public function getTopPhrases($count=10, $thresh = 3){
		$count = $this->getDefault($count, $count, 0, 10);
		$thresh = $this->getDefault($thresh, $count, 1, 2);
		
		if($thresh > 6) $thresh = 6;
		
		$result = array();
		$temp = $this->getWC()->getSortedPhrases();
		
		$size = 0;
		foreach($temp as $entry){
			if(str_word_count($entry->normal) >= $thresh){
				array_push($result,$entry);
				$size++;
			}
			if($size >= $count) break;
		}
		return $result;
	}
	
	/**
	 * Get phrases which contain the give normalized word.  Will default to top word if empty.
	 * @param string $word The word to normalize and lookup matching phrases on.
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>/<api-var>hash</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	 {
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        "giving us a simple hash",
        "us a simple hash table",
        "hash table",
        "is that so lets create a hash",
        "create a hash of the url",
        "a hash of the url and use",
        "hashcode function str",
        "str var hash",
        "str var hash",
        "var hash if str",
        "var hash if str",
        "hash if str length",
        "hash if str length",
        "hash for i i str length"
        ]
        }
	 </json>
	 </p>
	 */
	public function getPhrasesSpecific($word=''){
		if(is_array($word))
			$word = (count($word) < 1) ? '' : $word[0];
		
		$temp = $this->getWC();
		if(empty($word)){
			$temp2 = $temp->getCount();
			$word = $temp2[0]->normal;
		}
		
		
		return $temp->getPhrasesWithWord($word);
	}
	
	/**
	 * @ignore
	 * @return unknown
	 */
	private function getWC(){
		if(!is_object($this->wc))
			$this->wc = new \WordCount($this->parser->dom);
		
		return $this->wc;
	}
	
	/**
	 * Check for all inline css
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
	 {
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        "style=\"line-height: 12px;\"",
        "style=\"line-height: 12px;\"",
        "style=\"line-height: 12px;\"",
        "style=\"text-decoration: underline;\"",
        "style=\"line-height: 12px;\"",
        "style=\"line-height: 12px;\"",
        "style=\"color: #ccc;\"",
        "style=\"color: #ccc;\"",
        "style=\"color: #ccc;\"",
        "style=\"color: #ccc;\"",
        "style=\"line-height: 1.6;\"",
        "style=\"line-height: 1.6;\"",
        "style=\"line-height: 13px;\"",
        "style=\"line-height: 13px;\"",
        "style=\"border:none;\"",
        "style=\"line-height: 13px;\""
    ]
}
	 </json>
	 </p>
	 */
	public function checkInlineCSS(){
		preg_match_all('@style[\s+]?=[\s+]?[\'|"].*?[\'|"]@i', $this->parser->dom, $matches);
		return $matches[0];
	}
	
	/**
	 * Get an array of stylesheet link tag Node grouped by host
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>

{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":{
        "willsmelser.com":[
            {
                "hash":"00000000298f0cb8000000000ae2d340",
                "host":"willsmelser.com",
                "raw":"<link rel=\"stylesheet\" type=\"text\/css\" href=\"http:\/\/mediocredeveloper.com\/wp\/wp-content\/themes\/simpledark\/style.css\" media=\"screen\" \/>",
                "tag":"link",
                "attributes":{
                    "rel":"stylesheet",
                    "type":"text\/css",
                    "href":"http:\/\/mediocredeveloper.com\/wp\/wp-content\/themes\/simpledark\/style.css",
                    "media":"screen"
                },
                "textStart":135,
                "textEnd":false,
                "text":""
            }]}}	 
	 </json>
	 </p>
	 */
	public function checkLinkTags(){
		//check link tags
		$links = array();
		foreach($this->parser->getTags('link') as $node){
			if(isset($node->attributes['rel']) && $node->attributes['rel'] === 'stylesheet'){
				if(!isset($links[$node->host]))
					$links[$node->host] = array();
				
				array_push($links[$node->host], $node);
			}
		}
		return $links;
	}
	
	/**
	 * Get an array of style Nodes.
	 * 
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "hash":"000000005815ad570000000030f6915d",
            "host":"willsmelser.com",
            "raw":"<style type=\"text\/css\">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}<\/style>",
            "tag":"style",
            "attributes":{
                "type":"text\/css"
            },
            "textStart":23,
            "textEnd":109,
            "text":".recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}"
        },
        {
            "hash":"000000005815ad540000000030f6915d",
            "host":"willsmelser.com",
            "raw":"<style type=\"text\/css\" id=\"syntaxhighlighteranchor\"><\/style>",
            "tag":"style",
            "attributes":{
                "type":"text\/css",
                "id":"syntaxhighlighteranchor"
            },
            "textStart":52,
            "textEnd":52,
            "text":""
        }
    ]
}	 
	 </json>
	 </p>
	 */
	public function checkInlineStyle(){
		return $this->parser->getTags('style');		
	}
	
	/**
	 * returns true, if there are frames
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>

{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":false
}	 
	 </json>
	 </p>
	 */
	public function checkForFrames(){
		return (count($this->parser->getTags('frame')) > 0);
	}
	
	/**
	 * returns true f there are iframes
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>

{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":false
} 
	 </json>
	 </p>
	 */
	public function checkForIframes(){
		return (count($this->parser->getTags('iframe')) > 0);
	}
	
	/**
	 * Return true if there is flash
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>

{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":false
}	 
	 </json>
	 </p>
	 */
	public function checkForFlash(){
		$object = $this->parser->getTags('object');
		if(empty($object)) return false;
		
		return preg_match($object->raw, '/shockwave\-flash/i');
	}
	
	/**
	 * Get internal anchor tags
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "hash":"00000000116c2efd0000000076e8b33b",
            "host":"willsmelser.com",
            "raw":"<a class=\"top\" href=\"#header\"><\/a>",
            "tag":"a",
            "attributes":{
                "class":"top",
                "href":"#header"
            },
            "textStart":30,
            "textEnd":30,
            "text":""
        },
        {
            "hash":"00000000116c2efa0000000076e8b33b",
            "host":"willsmelser.com",
            "raw":"<a class=\"bottom\" href=\"#footer\"><\/a>",
            "tag":"a",
            "attributes":{
                "class":"bottom",
                "href":"#footer"
            },
            "textStart":33,
            "textEnd":33,
            "text":""
        }
    ]
}	 
	 </json>
	 </p>
	 */
	public function getInternalAnchor(){
		$anchors = $this->getAnchors();
		$result = array();
		foreach($anchors as $a){
			if(isset($a->attributes['href'])){
				$href = $a->attributes['href'];
				$info = parse_url($href);
				
				//relative internal link
				if(!isset($info['host']) || empty($info['host'])){
					array_push($result, $a);
				}else if($a->host === $info['host'])
					array_push($result, $a);
			}
		}
		return $result;
	}
	
	/**
	 * get external anchor tags
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>

{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "hash":"00000000693cfc0d0000000074b2379f",
            "host":"willsmelser.com",
            "raw":"<a href=\"http:\/\/mediocredeveloper.com\/wp\">Mediocre Developer<\/a>",
            "tag":"a",
            "attributes":{
                "href":"http:\/\/mediocredeveloper.com\/wp"
            },
            "textStart":42,
            "textEnd":60,
            "text":"Mediocre Developer"
        },
        {
            "hash":"00000000693cfc0e0000000074b2379f",
            "host":"willsmelser.com",
            "raw":"<a href=\"http:\/\/mediocredeveloper.com\/wp\">Home<\/a>",
            "tag":"a",
            "attributes":{
                "href":"http:\/\/mediocredeveloper.com\/wp"
            },
            "textStart":42,
            "textEnd":46,
            "text":"Home"
        }]}	 
	 </json>
	 </p>
	 */
	public function getExternalAnchors(){
		$anchors = $this->getAnchors();
		$result = array();
		foreach($anchors as $a){
			if(isset($a->attributes['href'])){
				$href = $a->attributes['href'];
				$info = parse_url($href);
		
				//relative internal link
				if(!isset($info['host']) || empty($info['host'])){
					//do nothing
				}else if($a->host !== $info['host'])
					array_push($result, $a);
				
			}
		}
		return $result;
	}
	
	/**
	 * Get a list of all anchors
	 * @return Array An array of Node elements
	 * @uses Node
	 * @ignore
	 */
	private function getAnchors(){
		if(!empty($this->anchors))
			return $this->anchors;
		else
			return $this->parser->getTags('a');
	}
	
	/**
	 * Check document image dimensions are set and good.  This will take
	 * as long as the longest image takes to load
	 * 
	 * <code>
	 * class ImageLoadResponse{
	 * 	public $url; //url of image requested.  Should be able to use hash to find Node
	 * 	public $result; //1=good, -1=failed to check, 0=sizes did not match
	 * 	public $hash; //Node hash
	 *  public $time; //in seconds
	 * }
	 * </code>
	 * 
	 * 
	 * @see ImageLoadResponse
	 * @see Node
	 * @return object JSON object
	 * <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/body/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":{
        "0000000075deb572000000002dc7a631":{
            "url":"http:\/\/mediocredeveloper.com\/wp\/wp-content\/uploads\/2013\/03\/me2.jpg",
            "result":0,
            "hash":"0000000075deb572000000002dc7a631",
            "time":0.481,
            "actualWidth":48,
            "actualHeight":48,
            "htmlWidth":"50",
            "htmlHeight":"50",
            "alt":"willsmelser",
            "title":null
        },
        "0000000075deb571000000002dc7a631":{
            "url":"http:\/\/mediocredeveloper.com\/wp\/wp-content\/uploads\/2013\/03\/me2.jpg",
            "result":0,
            "hash":"0000000075deb571000000002dc7a631",
            "time":0.5196,
            "actualWidth":48,
            "actualHeight":48,
            "htmlWidth":"50",
            "htmlHeight":"50",
            "alt":"willsmelser",
            "title":null
        }}}	 
	 </json>
	 </p>
	 */
	public function checkImages(){
		require_once "../class/ImageParser.php";
		
		$imgs = $this->parser->getTags('img');
		return \ImageParser::checkActualDimsThreaded($imgs);
	}
}
?>