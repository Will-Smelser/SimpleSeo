<?php

class TokensController extends RController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters() { return array( 'rights', ); }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','create','update'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Tokens;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Tokens']))
		{
			$model->attributes=$_POST['Tokens'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Tokens']))
		{
			$model->attributes=$_POST['Tokens'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Tokens');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Tokens('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Tokens']))
			$model->attributes=$_GET['Tokens'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Tokens the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Tokens::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Tokens $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tokens-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	public function actionGetToken($nonce, $username, $hash, $resource='/api', $ip=null, $one_time=false){
		$this->layout = 'application.views.layouts.empty';
		
		//hash = sha1(nonce+key)
		
		//lookup user by $key
		$user = User::model()->findByAttributes(array('username' => $username));
		$uHash = Tokens::createHash($nonce, $user->activkey);

		$result = new tokenResult();
		
		//not a valid hash
		if($uHash !== $hash){
			$message = 'Invalid Hash';
			echo json_encode($result->toArray());
			return;
		}
	
		//create token
		$token = str_shuffle(MD5(microtime()));
	
		//save user, token, expire, $resource to Tokens
		$model = $model=new Tokens;
		
		
		$model->user_id = $user->id;
		$model->token = $token;
		$model->resource = $resource;
		$model->expire = $model::getNewExpires();
        $model->ip = $ip;
        $model->one_time = $one_time;
	
		if($model->save()){
			$result->expire = $model->expire;
			$result->scope = $resource;
			$result->token = $token;
			$result->message = 'SUCCESS';
			$result->result = 'true';
		}else{
			$result->message = 'Failed to save token';
		}
		
		echo json_encode($result->toArray());
	}
}

class tokenResult{
	public $token = null;
	public $expire = null;
	public $scope = null;
	public $result = null;
	public $message = null;
    public $ip = null;
	
	function __construct($token=null, $expire=null, $scope=null, $result='false', $ip=null){
		$this->token = $token;
		$this->expire = $expire;
		$this->scope = $scope;
		$this->result = $result;
        $this->ip = $ip;
	}
	
	public function toArray(){
		return array(
			'token'=>$this->token,
            'ip'=>$this->ip,
			'expires'=>$this->expire,
			'scope'=>$this->scope,
			'success'=>$this->result,
			'message'=>$this->message		
		);
	}
}
