<?php

require_once 'ExtController.php';

class ProtectedController extends ExtController
{
	private $ipAllowCount = 2;
	
	public function actionIndex(){
		Yii::app()->theme = 'reports';
		$this->render('index');
	}
	
	public function actionPretty(){
		Yii::app()->theme = 'simple';
		$this->render('pretty');
	}

    public function actionServer($target){
        require_once(Yii::getPathOfAlias('ext.seo').'/config.php');
        require_once(Yii::getPathOfAlias('ext.seo.class').'/SeoApiUserAdapter.php');

        $config = require_once(Yii::getPathOfAlias('ext.seo.apiuser').'/config.php');
        $config['API_HOST'] = SEO_HOST;

        $key = Yii::app()->params['apiKeyReport'];
        $seo = new SeoApiUserAdapter($config,$key,'report');

        $data = $seo->execAll($target);

        $this->render('server',array('data'=>$data));
    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
	
	public function beforeAction($view){

		
		
		//guest or not logged in user
		if (!Yii::app()->user->id) {
			$ip = $_SERVER['REMOTE_ADDR'];
			$entry = Ipfilter::model()->findByAttributes(array('ip'=>$ip));
				
			if(empty($entry)){
				
				$ipFilter = new Ipfilter();
				$ipFilter->ip = $ip;
				$ipFilter->cnt = 1;
				$ipFilter->save();
			}else{
				$entry->cnt++;
		
				if($entry->cnt > $this->ipAllowCount){
                    Yii::app()->user->setFlash('error','Free report usage limit.  Please register or login to get more reports.  '.
                        'Anonymous users are limited on a 24 hour period.');
					$this->redirect('/user/login');
					return false;
				}else{
					$entry->save();
				}
			}
			return true;
		//logged in user
		}else{
			$creditType = Apicredits::$typeReport;
			$userid = Yii::app()->user->id;
			if(Apicredits::hasCredit($userid,$creditType)){
				Apicredits::useCredit($userid, $creditType);
				
				//record stats
				$stats = new Reportstats();
				$stats->user = $userid;
				$stats->type = $view->getId();
				$stats->request = $_GET['target'];
				$stats->save();
				
				return true;
			}else{
				$this->redirect('/site/pages/noreportcredits');
				return false;
			}
		}
	}
}