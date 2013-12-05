<?php

namespace api\responses;

class ApiCodes {
	static $success = array("200 OK","Success",200);
	static $badRequest = array("400 Bad Request","Invalid Request",400);
	static $systemError = array("500 Internal Server Error", "Internal Error",500);
	static $accessDenied = array("401 Unauthorized","Invalid key or other access error",401);
	static $notFound = array("404 Not Found","Not found",404);
	
	public static function getCodeByNumber($code){
		$apiCode = self::$systemError;
		switch($code){
			case 200:
				$apiCode = self::$success;
				break;
			case 400:
				$apiCode = self::$badRequest;
				break;
			case 500:
				$apiCode = self::$systemError;
				break;
			case 401:
				$apiCode = self::$accessDenied;
				break;
			case 404:
				$apiCode = self::$notFound;
		}
		return $apiCode;
	}
}


class ApiResponse{
	protected $apiCode;//=ApiCodes::success;
	protected $error=false;
	protected $msg="Default Response";
	protected $data; //should be an associative array
	protected $code;
	
	public function success($msg, $data, $error=false, $apiCode=null){
		$this->apiCode = (empty($apiCode))?ApiCodes::$success:$apiCode;
		$this->data = $data;
		$this->msg = $msg;
		$this->error = $error;
		$this->header();
		return $this;
	}
	
	public function failure($msg, $apiCode=null){
		$this->apiCode = (empty($apiCode))?ApiCodes::$badRequest:$apiCode;
		$this->error = true;
		$this->msg = $msg;
		$this->data = null;
		$this->header();
		return $this;
	}
	
	public function setData($data){
		$this->data = $data;
	}
	
	private function header(){
		@header("HTTP/1.1 ".$this->apiCode[0]);
	}
	
	function doPrint(){
		print_r($this->toArray());
	}
	
	public function toArray(){
		return array(
			'code'=>$this->apiCode[2],
			'response'=>$this->apiCode[1],
			'error'=>$this->error,
			'msg'=>$this->msg,
			'data'=>$this->data,
		);
	}
	
	/**
	 * jsonpp - Pretty print JSON data
	 *
	 * In versions of PHP < 5.4.x, the json_encode() function does not yet provide a
	 * pretty-print option. In lieu of forgoing the feature, an additional call can
	 * be made to this function, passing in JSON text, and (optionally) a string to
	 * be used for indentation.
	 *
	 * @param string $json  The JSON data, pre-encoded
	 * @param string $istr  The indentation string
	 *
	 * @return string
	 */
	public function jsonpp($json, $istr="    "){
		$result = '';
		for($p=$q=$i=0; isset($json[$p]); $p++){
			$json[$p] == '"' && ($p>0?$json[$p-1]:'') != '\\' && $q=!$q;
			if(strchr('}]', $json[$p]) && !$q && $i--){
				strchr('{[', $json[$p-1]) || $result .= "\n".str_repeat($istr, $i);
			}
			$result .= $json[$p];
			if(strchr(',{[', $json[$p]) && !$q){
				$i += strchr('{[', $json[$p])===FALSE?0:1;
				strchr('}]', $json[$p+1]) || $result .= "\n".str_repeat($istr, $i);
			}
		}
		return $result;
	}
}

/**
 * Response wrapper for json requests.
 * @author Will
 *
 */
class ApiResponseJSON extends ApiResponse{
	function doPrint(){
		return $this->jsonpp(json_encode($this->toArray()));
	}
}

/**
 * Response wrapper for JSONP requests. Assumes, a "callback"
 * get parameter has been set. For example:
 * <code>
 * //PHP example
 * $callback = $_GET['callback'];
 * echo $callback . '(' . $json_data . ')';
 * </code>
 * @author Will
 *
 */
class ApiResponseJSONP extends ApiResponse{
	function doPrint(){
		$json = $this->jsonpp(json_encode($this->toArray()));
		
		//complain about no callback
		if(!isset($_GET['callback'])) throw new \Exception('Callback GET parameter expected, but none given.');
		
		echo $_GET['callback'].'('.$json.');';
	}
}

?>