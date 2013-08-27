<?php 
namespace api;

require_once SEO_PATH_CLASS . 'GoogleInfo.php';

class GoogleWrap extends GoogleInfo {
	
	/**
	 * @ignore
	 * @param unknown $url
	 */
	public function GoogleWrap($url){
		$url = preg_replace('@https?://@i','',$url);
		parent::__construct($url);
	}
	
	/**
	 * Get backlinks from google.  This can be a costly request
	 * requiring multiple requests.  Therefor a max of 100, or 10 requests
	 * is allowed.
	 * @param integer $max The max number of backlinks to return.
	 * @return responses\ApiResponseJSON JSON object
	 <p>
	 <b>Sample Request:</b> 
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/google/</api-base><api-var>%method%</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <api-request><api-line><api-rmethod>GET</api-rmethod><api-base>/api/google/</api-base><api-var>%method%</api-var>/<api-var>20</api-var>?request=<api-var>www.willsmelser.com</api-var></api-line></api-request>
	 <b>Sample Response:</b>
	 <json>
{
    "response":"Success",
    "error":false,
    "msg":"Success",
    "data":{
        "domainTotals":10,
        "domainData":{
            "www.westport-news.com":1,
            "www.facebook.com":1,
            "www.linkedin.com":1,
            "www.thepostnewspapers.com":1,
            "www.dispatch.com":1,
            "piranhaswimming.org":1,
            "pinball.wikia.com":1,
            "forums.cpanel.net":1,
            "www.darien-ymca.org":1,
            "boys.staplesswimming.com":1
        },
        "domainComposite":"www.westport-news.com(1), www.facebook.com(1), www.linkedin.com(1), www.thepostnewspapers.com(1), www.dispatch.com(1), piranhaswimming.org(1), pinball.wikia.com(1), forums.cpanel.net(1), www.darien-ymca.org(1), boys.staplesswimming.com(1)",
        "backlinks":[
            {
                "title":"Staples boys swimming edges Darien to snap losing skid - Westport ...",
                "link":"http:\/\/www.westport-news.com\/sports\/article\/Staples-boys-swimming-edges-Darien-to-snap-losing-4276675.php",
                "snippet":"Feb 14, 2013 ... The Blue Wave's 400 free relay of Will Smelser, Weeks, Baker and Nicolai   Ostberg clocked in first at 3:30.75. ... New to the site? To use\u00a0..."
            },
            {
                "title":"Ebon Lurks | Facebook",
                "link":"https:\/\/www.facebook.com\/pages\/Ebon-Lurks\/123817001024421",
                "snippet":"Ebon Lurks shared a link. July 20. My Debut Single PLEASE Share! You will hear   this on There Radio http:\/\/youtu.be\/_VoMNtxJQ5A. Ebon Lurks ... Will Smelser\u00a0..."
            }]}}	 
	 </json>
	 </p>
	 * 
	 * 
	 */
	public function getBacklinks($max=100){
		if(is_array($max))
			if(count($max) > 0)
				$max = array_shift($max)*1.0;
			else
				$max = 100;
		if($max > 100)
			$max = 100;
		
		$uniqueDomains = array();
		$result = array();
		$composite = "";

		$backlinks = $this->google->getBacklinks($this->url, $max);
		
		if(!empty($backlinks)){
			foreach($backlinks as $entry){
				
				array_push($result,
					array(
						'title'=>$entry->title,
						'link'=>$entry->link,
						'snippet'=>$entry->snippet
					)
				);
				
				//track domains
				$domain = parse_url($entry->link);
				$host1 = trim($domain['host']);
				if(!isset($uniqueDomains[$host1]))
					$uniqueDomains[$host1] = 0;
				
				$uniqueDomains[$host1]++;
			}
			
			//make some composite data
			foreach($uniqueDomains as $host=>$count)
				$composite .= $host . "($count), ";
			
			$composite = rtrim($composite,', ');
		};
		return array(
				'domainTotals'=>count($uniqueDomains),
				'domainData'=>$uniqueDomains,
				'domainComposite'=>$composite,
				'backlinks'=>$result
		);
	}
}

?>