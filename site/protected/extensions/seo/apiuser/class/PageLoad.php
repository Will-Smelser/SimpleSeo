<?php

namespace simple_seo_api;

require_once __DIR__ . '/../interfaces.php';

set_time_limit(120);

/**
 * This class is used for making a Threaded curl requests.  Since PHP
 * doesn't support threading, this can be used to simulate that.
 * 
 * Designed explicitly for downloading internet content.
 * @author Will
 *
 */
class PageLoad implements ThreadRequests{
	private $loadPage;
	
	private $mh;

	private $curls = array();
    private $requests = array();
    private $info = array();
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->mh = curl_multi_init();
	}

    public function reset(){
        $this->curls = array();
        $this->requests = array();
        $this->info = array();
    }

	public function addPage($page, $info=null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $page);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_multi_add_handle($this->mh, $ch);
		
		array_push($this->curls, $ch);
        array_push($this->requests, $page);
        array_push($this->info, $info);
	}
	
	/**
	 * Actually perform the request
	 * @return returns an array of page request bodies once all
	 * pages have finished loading.
	 */
	public function exec(){
		// execute the handles
		$running = null;
		do {
			curl_multi_exec($this->mh, $running);
		} while($running > 0);
		
		$result = array();
		
		// get content and remove handles
		foreach($this->curls as $key=>$ch) {
			array_push($result, array(
                'request'=>$this->requests[$key],
                'info'=>$this->info[$key],
                'result' => curl_multi_getcontent($ch)
            ));
			curl_multi_remove_handle($this->mh, $ch);
		}
			
		// all done
        $this->curls = array();
		curl_multi_close($this->mh);
		
		return $result;
	}
	
}

?>