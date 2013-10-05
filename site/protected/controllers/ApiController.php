<?php
error_reporting(E_ALL);
/**
 * Have to create a dummy identity class in order to
 * login user
 * @author Will
 *
 */
class SwitchIdentity extends CBaseUserIdentity {
	private $_id;
	private $_name;

	public function SwitchIdentity( $userId, $userName ) {
		$this->_id = $userId;
		$this->_name = $userName;
	}

	public function getId() {
		return $this->_id;
	}

	public function getName() {
		return $this->_name;
	}
	
	public function authenticate(){
		return true;
	}
}

/**
 * The actual ApiController
 * @author Will
 *
 */
class ApiController extends RController
{
	private $apiController = null;
	public $layout = 'application.views.layouts.empty';
	
	private $origionalUserId;
	
	private $apiStats;
	
	public function filters() { return array( 'rights', ); }
	
	public function init(){
		
		require_once(Yii::getPathOfAlias('ext.seo').'/config.php');
		
		$this->origionalUserId = Yii::app()->user->id;
		
		$user = null;
		if(isset($_GET['token']))
			//lookup the token
			$token = Tokens::model()->findByAttributes(array('token'=>$_GET['token']));
					
		//without a token, no login/access allowed.  Handled by RController (Rights module)
		if(empty($token) || $token::isExpired($token->expire)) return;
	
		if(isset($token->user_id))
			$user = User::model()->findByAttributes(array('id' => $token->user_id));
		
		//no user
		if(empty($user)) return;
		
		//we will have some stats
		$this->apiStats = new Apistats();
		$this->apiStats->user = $token->user_id;
		
		$newIdentity = new SwitchIdentity( $user->id, $user->username );
		Yii::app()->user->login( $newIdentity );
		
	}
	
	/**
	 * Default behavior would send us to login
	 * @see RController::accessDenied()
	 */
	public function accessDenied($message=null){
		require_once SEO_PATH_HELPERS . 'ApiResponse.php';
		
		$code = \api\responses\ApiCodes::$accessDenied;
		
		$response = null;
		if(isset($_GET['type']) && $_GET['type'] === 'jsonp'){
			$response = (new \api\responses\ApiResponseJSONP());
		}else{
			$response = (new \api\responses\ApiResponseJSON());
		}
		
		$response->failure("Access Denied",$code);
		echo $response->doPrint();
	}
	
	public function beforeAction($action){
		if($action->id === 'thread') return true;
		
		$user = Yii::app()->user;
		
		//if the user was origionally logged in, keep them logged in
		if(!empty($this->origionalUserId)){
			
			$user = User::model()->findByAttributes(array('id' => $this->origionalUserId));
			
			//special username that site uses for examples
			if($user->username !== 'sample'){
				$newIdentity = new SwitchIdentity( $user->id, $user->username );
				Yii::app()->user->login( $newIdentity );
			}else{
				Yii::app()->user->logout();
			}

		//before anything can go wrong, lets ensure the user is no longer logged in
		}elseif(!empty($user) && !(strtolower($user->getName()) === 'guest')){
			Yii::app()->user->logout();
		}
				
		$url = $params['url'] = str_replace('\\','/',$_GET['url']);
		$_VARS = explode('/',$url);
		
		//remove the "api"
		@array_shift($_VARS);
		
		$_CONTROLLER = $action->id;
		
		$_METHOD = isset($_VARS[1]) ? $_VARS[1] : 'no_method';
		
		@array_shift($_VARS);
		@array_shift($_VARS);
		
		
		//verify the controller and method exist
		if(!file_exists(SEO_PATH_CONTROLLERS . $_CONTROLLER . '.php'))
			$_CONTROLLER = 'Error';
		
		//cleanup the request var
		if(!preg_match('@^(https?://)@i',$_GET['request']))
			$_GET['request'] = 'http://'.ltrim($_GET['request'],'/');
		
		$this->apiStats->request = $_GET['request'];
		
		
		require_once SEO_PATH_CONTROLLERS . 'Controller.php';
		require_once SEO_PATH_CONTROLLERS . $_CONTROLLER . '.php';
		
		//controller will handle actual work and call method
		$type = (isset($_GET['type'])) ? $_GET['type'] : 'json';
		
		$this->apiController = new $_CONTROLLER($_METHOD, $type, $_VARS);
		
		//save the stats
		$this->apiStats->controller = $_CONTROLLER;
		$this->apiStats->method = $_METHOD;
		$this->apiStats->save();
		
		return true;
	}
	
	public function afterAction($action){
		$this->render('api');
	}
	
	public function afterRender($view, &$output){
		
		//Yii::app()->end();
	}
	
	/* All the "Controller" for the api */
	
	
	public function actionBody(){}

	public function actionGoogle(){}

	public function actionHead(){}

	public function actionMoz(){}

	public function actionSemrush(){}

	public function actionServer(){}

	public function actionSocial(){}
	
	/* FOR PHP THREADED REQUESTS */
	public function actionThread(){
		if (!($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])){
			return $this->accessDenied();
		}
		
		$file = basename($this->actionParams['url']);
		require_once SEO_PATH_HELPERS . $file;
	}
}