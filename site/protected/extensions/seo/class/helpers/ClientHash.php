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
	private static $apiHost = 'simple-seo-api.local';
	private static $api = '/tokens/getToken';
	
	/**
	 * Take user's private key and the username to make a 
	 * request to the api for a token.  The key is never sent
	 * in any request, so this keeps it secure.  You should
	 * never send your key out in any request.
	 * 
	 * @param string $key Your private activation key created when account was initiated.
	 * @param string $username Your username created at registration time
	 * @return string
	 * 
	 * @throws Exception An exception is thrown if the request fails to return a valid token.  Some
	 * informaiton about the error may be contained int he error message string.
	 */
	public static function getToken($key,$username){
		$nonce = substr(str_shuffle(MD5(microtime())),0,7);
		$hash = self::hash($nonce,$key);
		
		return self::makeRequest($username, $nonce, $hash);
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
	 * @param unknown $username
	 * @param unknown $nonce
	 * @param unknown $hash
	 * @throws Exception If a token was failed to be created then an error will be thrown.
	 * @return string
	 */
	private static function makeRequest($username, $nonce, $hash){
		$request = (self::$secure) ? 'https://' : 'http://';
		$request.= self::$apiHost . self::$api . '?username=%s&nonce=%s&hash=%s';
		
		$result = file_get_contents(sprintf($request,$username,$nonce,$hash));
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