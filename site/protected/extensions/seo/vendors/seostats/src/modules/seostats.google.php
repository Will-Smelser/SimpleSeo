<?php if (!defined('SEOSTATSPATH')) exit('No direct access allowed!');
/**
 *  SEOstats extension for Google data.
 *
 *  @package    SEOstats
 *  @author     Stephan Schmitz <eyecatchup@gmail.com>
 *  @updated    2012/06/07
 */

class SEOstats_Google extends SEOstats implements services, default_settings, api_keys
{
	private $cx = self::GOOGLE_CX;
	private $key = self::GOOGLE_KEY;
	
	
	private function httpSendWrapper($request){
		$request .= (!empty($this->cx)) ? "&cx={$this->cx}&key={$this->key}" : '';
		return HttpRequest::sendRequest($request);
	}
	
    /**
     *  Gets the Google Pagerank
     *
     *  @param    string    $url    String, containing the query URL.
     *  @return   integer           Returns the Google PageRank.
     */
    public function getPageRank($url = false)
    {
        require_once(SEOSTATSPATH . '3rdparty/GTB_PageRank.php');

        $url = false != $url ? $url : self::getUrl();
        $gtb = new GTB_PageRank($url);

        return $gtb->getPageRank();
    }

    /**
     *  Returns the total amount of results for a Google 'site:'-search for the object URL.
     *
     *  @param    string    $url    String, containing the query URL.
     *  @return   integer           Returns the total site-search result count.
     */
    public function getSiteindexTotal($url = false)
    {
        $url = false != $url ? $url : self::getUrl();
        $query = urlencode("site:$url");

        return self::getSearchResultsTotal($query);
    }

    /**
     *  Returns the total amount of results for a Google 'link:'-search for the object URL.
     *
     *  @param    string    $url    String, containing the query URL.
     *  @return   integer           Returns the total link-search result count.
     */
    public function getBacklinksTotal($url = false)
    {
        $url = false != $url ? $url : self::getUrl();
        $query = urlencode("link:$url");
		
        return self::getSearchResultsTotal($query);
    }
    
    private function doPagedRequest($api, $url, $index){
    	$start = "";
    	if($index !== 0)
    		$start = "&start=".$index;
    	
    	return json_decode($this->httpSendWrapper(sprintf($api, $url).$start));
    }
    
    /**
     * Get backlink information from Google
     * @param string $url Optional The url to find back links for
     * @param number $count The max number to return.  Approximate only.
     * @return array of objects in the following format:
     * {code}
     * [0]=>
		  object(stdClass)#6 (8) {
		    ["GsearchResultClass"]=> string(10) "GwebSearch"
		    ["unescapedUrl"]=> string(54) "http://thedailywtf.com/Articles/Coin-Toss-of-Doom.aspx"
		    ["url"]=> string(54) "http://thedailywtf.com/Articles/Coin-Toss-of-Doom.aspx"
		    ["visibleUrl"]=> string(15) "thedailywtf.com"
		    ["cacheUrl"]=> string(65) "http://www.google.com/search?q=cache:s_tatIFSjJkJ:thedailywtf.com"
		    ["title"]=>string(33) "Coin Toss of Doom - The Daily WTF"
		    ["titleNoFormatting"]=> string(33) "Coin Toss of Doom - The Daily WTF"
		    ["content"]=>
		    string(182) "Apr 25, 2013 <b>...</b> The ticket at the top of...in a text box on the<b>...</b>"
		{/code}
     */
    public function getBacklinks($url = false, $total=10)
    {
    	$url = false != $url ? $url : self::getUrl();
    	$url = urlencode("link:").'+'.trim(urlencode($url));
    	
    	$results = array();
    	
    	$resp = $this->doPagedRequest(services::GOOGLE_APISEARCH_URL2, $url, 0);
    	if(!is_object($resp) || !isset($resp->items)) return null;
    	
    	$count = count($resp->items);
    	$results = $resp->items;    	
    	
    	
    	for($i=1; $count < $total && isset($resp->queries->nextPage); $i++){
    		
    		$start = $resp->queries->nextPage[0]->startIndex;
    		$num = $resp->queries->nextPage[0]->count;
    		
    		$temp = $this->doPagedRequest(services::GOOGLE_APISEARCH_URL2, $url, $i);
    		
    		if(!is_object($temp) || !isset($temp->items)) break;
    		
    		$count += count($resp->items);
    		    		
    		$results = array_merge($results, $temp->items);
    	}
    	
    	return $results;
    }
    

    /**
     *  Returns total amount of results for any Google search,
     *  requesting the deprecated Websearch API.
     *
     *  @param    string    $url    String, containing the query URL.
     *  @return   integer           Returns the total search result count.
     */
    public function getSearchResultsTotal($url = false)
    {
        $url = false != $url ? $url : self::getUrl();
        $url = sprintf(services::GOOGLE_APISEARCH_URL2, $url);

        $ret = $this->httpSendWrapper($url);

        $obj = json_decode($ret);
        
        return ! isset($obj->searchInformation->formattedTotalResults)
               ? '0'
               : intval($obj->searchInformation->formattedTotalResults);
    }

    public function getSearchResults($q)
    {
    	$url = sprintf(services::GOOGLE_APISEARCH_URL2, $q);
    	
    	$ret = $this->httpSendWrapper($url);
    
    	
    	return json_decode($ret);
    }
    
    /**
     *  Returns total amount of results for any Google search,
     *  requesting the deprecated Websearch API.
     *
     *  @param    string    $url    String, containing the query URL.
     *  @return   integer           Returns a total count.
     */
    public function getPagespeedAnalysis($url = false)
    {
        $url = false != $url ? $url : self::getUrl();
        $url = sprintf(services::GOOGLE_PAGESPEED_URL, $url);

        $ret = $this->httpSendWrapper($url);

        return json_decode($ret);
    }

    public function getPagespeedScore($url = false)
    {
        $url = false != $url ? $url : self::getUrl();
        $ret = self::getPagespeedAnalysis($url);

        return intval($ret->results->score);
    }

    /**
     * Returns array, containing detailed results for any Google search.
     *
     * @param     string    $query  String, containing the search query.
     * @param     string    $tld    String, containing the desired Google top level domain.
     * @return    array             Returns array, containing the keys 'URL', 'Title' and 'Description'.
     */
    public function getSerps($query, $maxResults=100, $domain=false)
    {
        $q = rawurlencode($query);
        $maxResults = ($maxResults/10)-1;
        $result = array ();
        $pages = 1;
        $delay = 0;
        for ($start=0; $start<$pages; $start++) {
            $ref = 0 == $start ? 'ncr' : sprintf('search?q=%s&hl=en&prmd=imvns&start=%s0&sa=N', $q, $start);
            $nextSerp =  0 == $start ? sprintf('search?q=%s&filter=0', $q) : sprintf('search?q=%s&filter=0&start=%s0', $q, $start);

            $curledSerp = utf8_decode( self::gCurl($nextSerp, $ref) );

            if (preg_match("#answer=86640#i", $curledSerp)) {
                print('Please read: http://www.google.com/support/websearch/bin/answer.py?&answer=86640&hl=en');
                exit();
            }
            else {
                $matches = array();
                preg_match_all('#<h3 class="?r"?>(.*?)</h3>#', $curledSerp, $matches);
                if (!empty($matches[1])) {
                    $c = 0;
                    foreach ($matches[1] as $link) {
                        if (preg_match('#<a\s+[^>]*href=[\'"]?([^\'" ]+)[\'"]?[^>]*>(.*?)</a>#', $link, $match)) {
                            if (!preg_match('#^https?://www.google.com/(?:intl/.+/)?webmasters#', $match[1])) {
                                $c++;
                                $resCnt = ($start * 10) + $c;
                                if (FALSE !== $domain) {
                                    if (preg_match("#^$domain#i", $match[1])) {
                                        $result[] = array(
                                            'position' => $resCnt,
                                            'url' => $match[1],
                                            'headline' => trim(strip_tags($match[2]))
                                        );
                                    }
                                } else {
                                    $result[$resCnt] = array(
                                        'url' => $match[1],
                                        'headline' => trim(strip_tags($match[2]))
                                    );
                                }
                            }
                        }
                    }
                    if ( preg_match('#id="?pnnext"?#', $curledSerp) ) {
                        // Found 'Next'-link on currect page
                        $pages += 1;
                        $delay += 200000;
                        usleep($delay);
                    } else {
                        // No 'Next'-link on currect page
                        $pages -= 1;
                    }
                } else {
                    // No [@id="rso"]/li/h3 on currect page
                    $pages -= 1;
                }
            }
            if ($start == $maxResults) {
                $pages -= 1;
            }
        }
        return $result;
    }

    private function gCurl($path, $ref, $useCookie = default_settings::ALLOW_GOOGLE_COOKIES)
    {
        $url = sprintf('https://www.google.%s/', default_settings::GOOGLE_TLD);
        $referer = $ref == '' ? $url : $ref;
        $url .= $path;
        $url .= (!empty($this->cx)) ? "&cx={$this->cx}" : '';

        $ua = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11";
        if (isset($_SERVER["HTTP_USER_AGENT"]) && 0 < strlen($_SERVER["HTTP_USER_AGENT"])) {
            $ua = $_SERVER["HTTP_USER_AGENT"];
        }

        $header = array(
            'Host: www.google.' . default_settings::GOOGLE_TLD,
            'Connection: keep-alive',
            'Cache-Control: max-age=0',
            'User-Agent: ' . $ua,
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Referer: ' . $referer,
            'Accept-Language: ' . default_settings::HTTP_HEADER_ACCEPT_LANGUAGE,
            'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        if ($useCookie == 1) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt');
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return ($info['http_code']!=200) ? false : $result;
    }
}

/* End of file seostats.google.php */
/* Location: ./src/modules/seostats.google.php */