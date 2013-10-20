<?php
class ProtectedController extends Controller
{
	private $ipAllowCount = 2;
	
	public function actionIndex()
	{
		
		$this->render('index');
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

		Yii::app()->theme = 'reports';
		
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
					$this->redirect('/site/pages/ipusagelimit');
					return false;
				}else{
					$entry->save();
				}
			}
			return true;
		//logged in user
		}else{
			$creditType = Apicredits::$typeReport;
			if(Apicredits::hasCredit($creditType)){
				Apicredits::useCredit(Yii::app()->user->id, $creditType);
				return true;
			}else{
				$this->redirect('/site/pages/noreportcredits');
				return false;
			}
		}
	}
}