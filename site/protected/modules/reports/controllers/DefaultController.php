<?php

class DefaultController extends Controller
{
	public $configFile;
	public function beforeAction($action){
		Yii::app()->theme = 'reports';
		require_once(Yii::getPathOfAlias('ext.seo').'/config.php');
		
		return true;
	}
	
	public function actionIndex()
	{
		
		$this->pageTitle = 'Simple SEO Basic Report';
		$this->render('index','reports');
	}
	
	public function missingAction($actionID)
	{
		Yii::app()->theme = 'shadow_dancer';
		throw new CHttpException(404,Yii::t('yii','The system is unable to find the requested action "{action}".',
				array('{action}'=>$actionID==''?$this->defaultAction:$actionID)));
	}
	
	public function actionError()
	{
		Yii::app()->theme = 'shadow_dancer';
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}
	
}