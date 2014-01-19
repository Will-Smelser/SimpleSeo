<?php

/**
 * The actual ApiController
 * @author Will
 *
 */
class ApiBaseController extends RController
{
	private $apiController = null;
	public $layout = 'application.views.layouts.empty';
	
	protected $tokenUserId;
	
	//public function filters() { return array( 'rights', ); }
	
	public function init(){
		require_once(Yii::getPathOfAlias('ext.seo').'/config.php');
		
		$user = null;
		if(isset($_GET['token']))
			//lookup the token
			$token = Tokens::model()->findByAttributes(array('token'=>$_GET['token']));

		//without a token, no login/access allowed.  Handled by RController (Rights module)
		if(empty($token) || $token::isExpired($token->expire))
			return $this->accessDenied('No valid token given.');

			
		if(!isset($token->user_id))
			return $this->accessDenied('Token had no user id.');
			
		$this->tokenUserId = $token->user_id;
		$user = User::model()->findByAttributes(array('id' => $token->user_id));

		//no user
		if(empty($user))
			return $this->accessDenied('Token did not have a valid user.');
	}
	
	/**
	 * Default behavior would send us to login
	 * @see RController::accessDenied()
	 */
	public function accessDenied($message='Access Denied.'){
		
		require_once SEO_PATH_HELPERS . 'ApiResponse.php';
		
		$code = \api\responses\ApiCodes::$accessDenied;
		
		$response = null;
		if(isset($_GET['type']) && $_GET['type'] === 'jsonp'){
			$response = (new \api\responses\ApiResponseJSONP());
		}else{
			$response = (new \api\responses\ApiResponseJSON());
		}
		$response->failure($message,$code);
		echo $response->doPrint();

        Yii::app()->end();
	}

	public function beforeAction($action){

		if($action->id === 'thread') return true;
		
		if(empty($this->tokenUserId))
			return $this->accessDenied('Failed to lookup user.');
		
		if(!Apicredits::hasCredit($this->tokenUserId,Apicredits::$typeApi))
			return $this->accessDenied('User does not have enough credits.');
				
		$url = $params['url'] = str_replace('\\','/',$_GET['url']);
		$_VARS = explode('/',$url);
		
		//remove the "api"
		@array_shift($_VARS);
		
		$_CONTROLLER = ucwords($action->id);
		
		$_METHOD = isset($_VARS[1]) ? $_VARS[1] : 'no_method';
		
		@array_shift($_VARS);
		@array_shift($_VARS);

		
		//verify the controller and method exist
		if(!file_exists(SEO_PATH_CONTROLLERS . $_CONTROLLER . '.php'))
			$_CONTROLLER = 'Error';
		
		//cleanup the request var
		if(!preg_match('@^(https?://)@i',$_GET['request']))
			$_GET['request'] = 'http://'.ltrim($_GET['request'],'/');

		require_once SEO_PATH_CONTROLLERS . 'Controller.php';
		require_once SEO_PATH_CONTROLLERS . $_CONTROLLER . '.php';

		//controller will handle actual work and call method
		$type = (isset($_GET['type'])) ? $_GET['type'] : 'json';

		//save the stats
		$apiStats = new Apistats();
		$apiStats->request = $_GET['request'];
		$apiStats->user = $this->tokenUserId;
		$apiStats->controller = $_CONTROLLER;
		$apiStats->method = $_METHOD;
		$apiStats->save();
		
		//use a credit
		Apicredits::useCredit($this->tokenUserId,Apicredits::$typeApi);

        //possible, error can be thrown at object instantiation
        try{
		    $this->apiController = new $_CONTROLLER($_METHOD, $type, $_VARS);
        }catch(Exception $e){
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
            $msg = Controller::errMsg($e->getMessage(),$e->getLine(),$e->getFile());
            $code = api\responses\ApiCodes::$systemError;
            echo $api->failure($msg,$code)->doPrint();
            return false;
        }
		
		return true;
	}
	
	public function afterAction($action){
		$this->render('api');
	}
	
	public function afterRender($view, &$output){
		
		//Yii::app()->end();
	}

	/* FOR PHP THREADED REQUESTS */
	public function actionThread(){
		if (!($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])){
			return $this->accessDenied();
		}
		
		$file = basename($this->actionParams['url']);
		require_once SEO_PATH_HELPERS . $file;
	}
}