<?php 
namespace api\clients;

/**
 * Simple wrapper for making a GET request
 * to api for a new token.
 * 
 * Use:
 * $token = api\clients\ClientHash::getToken('<your key>','<username>');
 * 
 * @author Will
 *
 */
class ClientHash{
	private static $secure = false;
	private static $apiHost = 'simple-seo-api.com';
	private static $api = '/tokens/getToken';
	
	/**
	 * Take user's private key and the username to make a 
	 * request to the api for a token.  The key is never sent
	 * in any request, so this keeps it secure.  You should
	 * never send your key out in any request.
	 * 
	 * @param string $key Your private activation key created when account was initiated.
	 * @param string $username Your username created at registration time
	 * @param string $host [optional] The host to make request against
     * @param string $ip [optional] The IP address token is good for.  This should be the IP
     * of whomever you want to make the API requests.  If this is your webservice, then it would
     * be the IP of your webserver.  If this is someone using your javascript powered report or API
     * usage, then this would be the client's IP address.
	 * @return string
	 * 
	 * @throws Exception An exception is thrown if the request fails to return a valid token.  Some
	 * informaiton about the error may be contained int he error message string.
	 */
	public static function getToken($key,$username, $host=null, $ip=null){

		$nonce = self::nonce();
		$hash = self::hash($nonce,$key);
		
		return self::makeRequest($username, $nonce, $hash, $host, null, $ip);
	}

    /**
     * Calculate a nonce.  You can create nonce however you like, this
     * just provides a simple implamentation; however this is not guranteed
     * to be unique.
     * @return string A nonce.
     */
    public static function nonce(){
        return substr(str_shuffle(MD5(microtime())),0,7);
    }
	
	/**
	 * Create the hash.  Just a wrapper for incase this class is
	 * expanded on in the future.
	 * 
	 * @param string $nonce Should be a one time used random string for a token request
	 * @param unknown $key Your private activation key created when account was initiated.
	 * @return string A hash string for token requests.
	 */
	public static function hash($nonce,$key){
		return sha1($nonce.$key);
	}
	
	/**
	 * Make the actual http request to the token service
	 * @param string $username
	 * @param string $nonce
	 * @param string $hash
	 * @param string $host [optional] The host to make request against
     * @param string $resource [optional] The resource this token will
     *          make requests for.  Just for stats tracking.
	 * @throws Exception If a token was failed to be created then an error will be thrown.
	 * @return string
	 */
	public static function makeRequest($username, $nonce, $hash, $host=null, $resource='/api',$ip=''){
		if(empty($host)) $host = self::$apiHost;
		
		$request = (self::$secure) ? 'https://' : 'http://';
		$request.= $host . self::$api . '?username=%s&nonce=%s&hash=%s&ip=%s';

		$result = file_get_contents(sprintf($request,$username,$nonce,$hash,$ip));
		$result = json_decode($result);
		
		if(isset($result->success) && $result->success === 'true'){
			if(!empty($result->token))
				return $result->token;
		}
		
		$error = (!isset($result->message)) ? 'Unknown' : $result->message;
		throw new \Exception('Failed to get token.  Message: '.$error);
	}
}

?>