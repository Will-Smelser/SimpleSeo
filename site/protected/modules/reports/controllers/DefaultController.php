<?php

class DefaultController extends Controller
{
	public $configFile;
	public function beforeAction($action){
		$this->configFile = Yii::getPathOfAlias('ext.seo').'/config.php';
		return true;
	}
	
	public function actionIndex()
	{
		$this->pageTitle = 'Simple SEO Basic Report';
		
		$this->render('index');
	}
}