<?php

class SaveController extends Controller
{
	public $configFile;
	public function beforeAction($action){
		return true;
	}
	
	public function actionIndex()
	{
		Yii::app()->theme = 'shadow_dancer';
		throw new CHttpException(404,Yii::t('yii','Expected filename, but none given.',
				array('{action}'=>$actionID==''?$this->defaultAction:$actionID)));
	}
	
	public function missingAction($actionID)
	{
		$this->layout = 'application.views.layouts.empty';
		Yii::app()->theme = 'reports';
		require_once(Yii::getPathOfAlias('ext.seo').'/config.php');
		
		$this->pageTitle = 'Simple SEO Basic Report';
		$this->render('save');
	}
}