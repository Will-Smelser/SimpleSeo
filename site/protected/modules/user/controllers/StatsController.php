<?php

class StatsController extends RController
{
	public $data;
	public $start;
	public $stop;
	
	private $queries = array(
		'total'=>'SELECT DATE_FORMAT(created,"%%Y-%%m-%%d" ) AS `interval` , COUNT( * ) as `cnt` FROM tokens where user_id=%d and created > \'%s\' and created < \'%s\' GROUP BY `interval`'
	);
	
	public function actionIndex()
	{
		$start = $this->getTime(null,true);
		$stop = $this->getTime(null, false);
		$id = intval(Yii::app()->user->id);
		
		$sql = sprintf($this->queries['total'],$id,$start,$stop);

		$this->data = Yii::app()->db->createCommand($sql)->queryAll();
		$this->start = $start;
		$this->stop = $stop;

		$this->render('index');
	}
	
	public function actionTotals($start,$stop){
		$this->layout = 'application.views.layouts.empty';
		
		$start = $this->getTime(null,true);
		$stop = $this->getTime(null, false);
		
		$id = intval(Yii::app()->user->id);
		
		$sql = sprintf($this->queries['total'],$id,$start,$stop);
		
		echo json_encode(Yii::app()->db->createCommand($sql)->queryAll());
		
		$this->render('ajax');
	}
	
	private function getTime($time, $first=true){
		$result = strtotime($time);
		if($result === false)
			$result = time();
		
		$temp = $first ? $result - 30*60*60*24 : $result;
		return date("Y-m-d H:i:s", $temp);
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
}