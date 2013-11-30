<?php

require_once SEO_PATH_HELPERS . 'ApiResponse.php';
require_once SEO_PATH_HELPERS . 'Vars.php';
require_once SEO_PATH_LANG . 'Loader.php';

$FATAL_ERROR = true;
$SUPRESS_ERROR = false;

class Controller{
	public $skip = array();
	
	private $error = false;

    private $lang;
	
	public function __construct(){
		global $FATAL_ERROR;
		set_error_handler('Controller::handleError');
		register_shutdown_function('Controller::shutdown');

        $this->lang = \api\lang\Loader::getLoader(get_class($this),'en');
	}
	
	public function __destruct(){
		restore_error_handler();
	}
	
	public static function shutdown(){
		global $FATAL_ERROR;
		if($FATAL_ERROR){
			$api = new \api\responses\ApiResponseJSON();
			echo $api->failure("Fatal Internal System Error - No Trace Available",\api\responses\ApiCodes::$systemError)->doPrint();
		}
	}
	
	private function execGroup(&$obj, $method, $args){
		$results = array();
		
		foreach(explode('|', $method) as $mthd){
			$api = new \api\responses\ApiResponseJSON();
			
			try{
				if($this->isValidMethod($obj, $mthd, $this->skip)){
					$temp = $obj->$mthd($args);
					$temp2= $api->success("Success", $temp);
					$results[$mthd] = $temp2->toArray();

                    $results[$mthd]['lang'] = $this->lang->toArray($mthd,$temp);

				}else{
					throw new BadMethodCallException("No Method - $mthd");
				}
			}catch(Exception $e){
				$this->error = true;
				$temp = new \api\responses\ApiResponseJSON();
				$err = Controller::errMsg($e->getMessage(),$e->getLine(),$e->getFile());
				$results[$mthd] = $temp->failure($err)->toArray();
                $results[$mthd]['lang'] = $this->lang->toArray($mthd,null);
			}
		}
			
		return $results;
	}
	
	public function execAll(&$obj, $method, $args){
		$results = array();
		$api = new \api\responses\ApiResponseJSON();
		
		foreach(get_class_methods($obj) as $mthd){
			if(stripos($method,'~'.$mthd) === false && $this->isValidMethod($obj, $mthd, $this->skip) && $mthd !== '__construct'){
				try{
					$temp = $obj->$mthd($args);
					$results[$mthd] = $api->success("Success", $temp)->toArray();
                    $results[$mthd]['lang'] = $this->lang->toArray($mthd,$temp);
				}catch(Exception $e){
					$this->error = true;
					$temp = new \api\responses\ApiResponseJSON();
					$err = Controller::errMsg($e->getMessage(),$e->getLine(),$e->getFile());
					$results[$mthd] = $temp->failure($err)->toArray();
                    $results[$mthd]['lang'] = $this->lang->toArray($mthd,null);
				}
			}
		}
		
		return $results;
	}
	
	
	public function execWrapper(&$obj, $method, $args){
		
		if(strstr($method, '|'))
			return $this->execGroup($obj, $method, $args);
			
		//run all api methods
		else if(stripos($method,'all')!==false){
			return $this->execAll($obj, $method, $args);
				
		//method doesnt exist, or is a skip method
		}else if(!$this->isValidMethod($obj, $method, $this->skip))
			throw new BadMethodCallException("No Method - $method");
			
		//just run method, wrapped in pai response
        $api = new \api\responses\ApiResponseJSON();
        //TODO enter lang stuff here
        $temp = $obj->$method($args);
		$result[$method]= $api->success("Success", $temp)->toArray();
        $result[$method]['lang'] = $this->lang->toArray($method,$temp);
        return $result;
	}
	
	public function exec(&$obj, $method, $type='json', $args=null){
		global $FATAL_ERROR;
		
		$result = null;
		
		$api = null;
		switch($type){
			case 'jsonp':
				$api = new \api\responses\ApiResponseJSONP();
				break;
			case 'json':
			default:
				$api = new \api\responses\ApiResponseJSON();
				break;
		}
		try{
			
			$result = $this->execWrapper($obj, $method, $args);
			
		//if the exception made its way up here then it is a top level error
		//meaning it is most likely the failure of a single method call
		}catch(Exception  $e){
			
			$this->error = true;
			
			$api->setData($result);
			echo $api->failure(Controller::errMsg($e->getMessage(),$e->getLine(),$e->getFile()))->doPrint();
			
			$FATAL_ERROR = false;
			return;
		}

		//let the shutdown function know there were no untrapped errors
		$FATAL_ERROR = false;

		echo $api->success("Success", $result, $this->error)->doPrint();
	}

	/**
	 * Just pretty print an error into a single string.
	 * @param unknown $msg
	 * @param unknown $line
	 * @param unknown $file
	 * @return string
	 */
	public static function errMsg($msg, $line, $file){
		return 'CLASS: '.str_replace('.php','',basename($file)).', LINE: '.$line.', MSG: '.$msg;
	}
	
	/**
	 * This error handler exists only to capture warnings and ensure they get
	 * passed to user.
	 * 
	 * Otherwise warnings would either get hidden or printed before returning JSON object
	 * and breaking the api.  This allows the api to function regardless.  
	 * 
	 * Also, we are still in BETA and we want information about
	 * errors, not just to hide them.  Normal exceptions are passed on as usual.
	 * 
	 * @param unknown $errno
	 * @param unknown $errstr
	 * @param unknown $errfile
	 * @param unknown $errline
	 * @throws ErrorException
	 * @return boolean
	 */
	public static function handleError($errno, $errstr, $errfile, $errline){
		global $SUPRESS_ERROR;
		if($SUPRESS_ERROR) return false;
		
		//trap warnings also
		switch($errno){
			case E_WARNING:
			case E_NOTICE:
			case E_USER_NOTICE:
			case E_USER_WARNING:
				throw new ErrorException(Controller::errMsg($errstr,$errline,$errfile), 0, $errno, $errfile, $errline);
		}
		
		return false;
	}
	
	/**
	 * Check if the method exists
	 * @param unknown $obj
	 * @param unknown $method
	 * @param unknown $skip
	 * @return boolean
	 */
	public function isValidMethod($obj, $method, &$skip){
		if(get_class($obj) === $method){
			return false;
		}else if(!method_exists($obj, $method))
			return false;
		else if(isset($skip) && in_array($method, $skip))
			return false;
		else
			return true;
	}
}