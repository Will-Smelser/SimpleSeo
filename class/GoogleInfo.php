<?php
namespace api;

require_once SEO_PATH_VENDORS . 'seostats/src/seostats.php';

/**
 * This class focuses on google api calls.
 * @author Will
 *
 */
class GoogleInfo{
	/**
	 * @ignore
	 * @var unknown
	 */
	public $cx = "AIzaSyA_wkenQWzwHcxuaozcfIc_gcuMo9E09TM";//google for SEO project key
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $url;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $stats;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $google;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public function __construct($url){
		
		if(empty($url)) return;
		$url = preg_replace('@https?://@i','',$url);
		$this->stats = new \SEOstats($url);
		$this->google = $this->stats->Google();
		$this->url = $url;
	}
	
	/**
	 * @ignore
	 * @param unknown $obj
	 */
	public function setStats($obj){
		$this->stats = $obj;
		$this->url = $obj->getUrl();
	}
	
	/**
	 * @ignore
	 * @param unknown $obj
	 */
	public function setGoogle($obj){
		$this->google = $obj;
	}
	
	/**
	 * Get the google page rank.
	 * 
	 * @return responses\ApiResponseJSON JSON object
	 *  <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/google/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":"4"
}	 
	 </json>
	 </p>
	 */
	public function getPageRank(){
		return $this->google->getPageRank();
	}

	/**
	 * @ignore
	 */
	public function getBacklinks($max=10){
		return $this->google->getBacklinks($this->url, $max);
	}
	
	/**
	 * @ignore
	 */
	public function getBacklinksTotal(){
		return $this->google->getBacklinksTotal($this->url);
	}
	
	/**
	 * @ignore
	 * @return unknown
	 */
	public function getUrl(){
		return $this->url;
	}
	
	/**
	 * @ignore
	 */
	public function setUrl(){
		$this->obj->setUrl($this->url);
	}
	
	/**
	 * Get the google page speed score.
	 * 
	 * @return responses\ApiResponseJSON JSON object
	 *  <p>
	 * <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/google/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":75
}	 
	 </json>
	 </p>
	 */
	public function getPagespeedScore(){
		return $this->google->getPagespeedScore($this->url);
	}
	
	/**
	 * Run a google query and get the search results.
	 *
	 * @param string $query An url encoded search string
	 * @param integer $count The number of results to return
	 * @return responses\ApiResponseJSON JSON object
	 * <p>
	 * <b>Sample Request:</b>
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/google/</api-base><api-var>%method%</api-var>/<api-var>some_url_encoded_search</api-var>/<api-var>10</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>

{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":[
        {
            "title":"45 SEO APIs: Google AdSense, Alexa and Yahoo Site Explorer",
            "link":"http:\/\/blog.programmableweb.com\/2012\/10\/09\/45-seo-apis-google-adsense-alexa-and-yahoo-site-explorer\/",
            "displayLink":"blog.programmableweb.com",
            "htmlSnippet":"Oct 9, 2012 <b>...<\/b> Google AdSense Our API directory now includes 45 <b>SEO APIs<\/b>. The newest is the <br>  SEOlytics API. The most popular, in terms of mashups, is the&nbsp;<b>...<\/b>",
            "mime":null
        },
        {
            "title":"A Beginner's Guide To SEO APIs - YouMoz - Moz",
            "link":"http:\/\/moz.com\/ugc\/api-blog-post-10970",
            "displayLink":"moz.com",
            "htmlSnippet":"Sep 16, 2010 <b>...<\/b> Back in April 2010, Will Critchlow set himself a challenge, to learn enough <br>  appengine, python, yql and xpath in 2 hours to build a useful <b>SEO<\/b>&nbsp;<b>...<\/b>",
            "mime":null
        }]}	
	 </json>
	 </p>
	 */
	public function getSerps($query, $count=10){
		$temp = $this->google->getSearchResults(urlencode($query[0]), $count);
		
		if(!isset($temp->items)) return array();
		
		$items = $temp->items;
		
		$result = array();
		
		foreach($items as $res){
			$temp = array(
				'title'=>$res->title,
				'link'=>$res->link,
				'displayLink'=>$res->displayLink,
				'htmlSnippet'=>$res->htmlSnippet,
				'mime'=>(isset($res->mime)?$res->mime:null)	
			);
			array_push($result, $temp);
		}
		
		return $result;
		
	}
}

?>