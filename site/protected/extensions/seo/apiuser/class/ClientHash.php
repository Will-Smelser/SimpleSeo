<?php

namespace simple_seo_api;
use QueryPath\Exception;


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
	private static $apiHost = 'www.simple-seo-api.com';
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
	 * @return string
	 * 
	 * @throws Exception An exception is thrown if the request fails to return a valid token.  Some
	 * informaiton about the error may be contained int he error message string.
	 */
	public static function getToken($key,$username, $host=null){
		$nonce = substr(str_shuffle(MD5(microtime())),0,7);
		$hash = self::hash($nonce,$key);
		
		return self::makeRequest($username, $nonce, $hash, $host);
	}
	
	/**
	 * Create the hash.  Just a wrapper for incase this class is
	 * expanded on in the future.
	 * 
	 * @param string $nonce Should be a one time used random string for a token request
	 * @param unknown $key Your private activation key created when account was initiated.
	 * @return string A hash string for token requests.
	 */
	private static function hash($nonce,$key){
		return sha1($nonce.$key);
	}
	
	/**
	 * Make the actual http request to the token service
	 * @param string $username
	 * @param string $nonce
	 * @param string $hash
	 * @param string $host [optional] The host to make request against
	 * @throws Exception If a token was failed to be created then an error will be thrown.
	 * @return string
	 */
	private static function makeRequest($username, $nonce, $hash, $host=null){
		if(empty($host)) $host = self::$apiHost;
		
		$request = (self::$secure) ? 'https://' : 'http://';
		$request.= $host . self::$api . '?username=%s&nonce=%s&hash=%s';
        $request = sprintf($request,$username,$nonce,$hash);

        @$result = file_get_contents($request);
		$result = json_decode($result);

        //good state, return the results
		if(isset($result->success) && $result->success === 'true' && !empty($result->token))
			return $result->token;

		$error = (!isset($result->message)) ? 'Unknown' : $result->message;

        throw new \Exception("Failed to get token.\nRequest: $request\nMessage: $error",0,null);

	}
}

?>