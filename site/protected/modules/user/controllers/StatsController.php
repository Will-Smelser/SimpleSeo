<?php

class StatsController extends RController
{
	public $data;
	public $start;
	public $stop;
	public $startDetail = array();
	public $stopDetail = array();
	
	private $queries = array(
		'total'=>'SELECT DATE_FORMAT(created,"%%Y-%%m-%%d" ) AS `interval` , COUNT( * ) as `cnt` FROM apistats where user=%d and created > \'%s\' and created < \'%s\' GROUP BY `interval` order by created asc',
		'method'=>'SELECT DATE_FORMAT(created,"%%Y-%%m-%%d" ) AS `interval` , COUNT( * ) as `cnt`, controller, method FROM apistats where user=%d and created > \'%s\' and created < \'%s\' GROUP BY `interval`,`controller`,`method` order by controller, method asc',
		'report'=>'SELECT DATE_FORMAT(created,"%%Y-%%m-%%d" ) AS `interval` , COUNT( * ) as `cnt` FROM reportstats where user=%d and created > \'%s\' and created < \'%s\' GROUP BY `interval` order by created asc'
	);
	
	public function actionCredits(){
		$id = intval(Yii::app()->user->id);
		$credits = Apicredits::model()->findAll(array("condition"=>"user_id =  $id"));

		$this->data = array();
		foreach($credits as $cr){
			$temp = Apicredits::$types[$cr->type];
			$temp['cnt'] = $cr->cnt;
			array_push($this->data,$temp);
		}
		$this->render('credits');
	}
	
	public function actionIndex()
	{
		$start = $this->getTime(null,30*24*3600) . ' 00:00:00';
		$stop = $this->getTime(null, 0) . ' 23:59:59';
		$id = intval(Yii::app()->user->id);
		
		
		$this->start = $start;
		$this->stop = $stop;
		
		$this->updateDetails();

		$this->render('index');
	}
	
	public function actionTotals($start,$stop,$query='total'){
		$this->layout = 'application.views.layouts.empty';
		
		$start = $this->getTime($start,false);
		$stop = $this->getTime($stop, false);
		
		$id = intval(Yii::app()->user->id);
		
		$sql = sprintf($this->queries[$query],$id,$start,$stop);
		$data = Yii::app()->db->createCommand($sql)->queryAll();
		
		$json .= "[[\"Date\",\"Count\"]";
		if(count($data) == 0){
			$json .= ",[\"$start\",0]";
		}else{
			foreach($data as $row){
				$json .= ",[\"{$row['interval']}\",{$row['cnt']}]";
			}
		}
		$json .= ']';
		
		echo $json;
		
		$this->render('ajax');
	}
	
	public function actionMethods($start,$stop){
		$this->layout = 'application.views.layouts.empty';
		
		$start = $this->getTime($start,0) . ' 00:00:00';
		$stop = $this->getTime($stop, 0) . ' 23:59:59';
		
		$id = intval(Yii::app()->user->id);
		
		$sql = sprintf($this->queries['method'],$id,$start,$stop);
		
		//echo json_encode();
		$response = array();
		$methods = array();
		$result = Yii::app()->db->createCommand($sql)->queryAll();
		
		
		$json = '[["Date"';
		
		//2 pass, get all method, 
		foreach($result as $row){
			$temp = $row['controller'] . ':' . $row['method'];
			if(!in_array($temp,$methods)){
				$json .= ",\"{$temp}\"";
				array_push($methods,$temp);
			}
		}
		$json.= ']';
		
		//array_push($response,$methods);
		//array_unshift($response[0],'Date');
		
		$date;
		foreach($result as $row){
			//new data row
			if($row['interval'] !== $date){
				
				$date = $row['interval'];
				$response[$date] = array();
				foreach($methods as $m){
					$response[$date][$m] = 0;	
				}
			}
			
			$temp = $row['controller'] . ':' . $row['method'];
			$response[$date][$temp] = $row['cnt'];
		}
		
		foreach($response as $date=>$info){
			$json.=",[\"$date\"";
			foreach($info as $count){
				$json.=",$count";
			}
			$json.=']';
		}
		
		$json .= ']';
		
		echo $json;
		
		
		//echo json_encode($response);
		
		$this->render('ajax');
	}
	
	private function updateDetails(){
		$parts = explode(' ',$this->stop);
		$parts = explode('-',$parts[0]);
		$this->stopDetail = array('year'=>$parts[0],'month'=>$parts[1],'day'=>$parts[2]);
		
		$parts = explode(' ',$this->start);
		$parts = explode('-',$parts[0]);
		$this->startDetail = array('year'=>$parts[0],'month'=>$parts[1],'day'=>$parts[2]);
	}
	
	private function getTime($time, $offset=0){
		$result = strtotime($time);
		if($result === false)
			$result = time();
		
		$temp = $result - $offset;
		$temp = date("Y-m-d", $temp);
		 
		return $temp;
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