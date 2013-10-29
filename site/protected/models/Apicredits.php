<?php

/**
 * This is the model class for table "apicredits".
 *
 * The followings are the available columns in table 'apicredits':
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property integer $cnt
 * @property string $created
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class Apicredits extends CActiveRecord
{
	
	public static $defaultApiCredits = 500;
	public static $defaultReportCredits = 2;
	
	/**
	 * Valid product types
	 * @var unknown
	 */
	public static $typeReport = 'report';
	public static $typeApi = 'api';
	public static $types = array(
			'report'=>array(
					'unit'=>0.50,'caseSize'=>1.0,'desc'=>'Report Credit (%d reports)','title'=>'Report Credit(s)'
			),
			'api'=>array(
					'unit'=>.01,'caseSize'=>500.0,'desc'=>'Api Credits (%d requests)','title'=>'Api Credit(s)'
			)
	);
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'apicredits';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, type, cnt', 'required'),
			array('user_id, cnt', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, type, cnt, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'type' => 'Type',
			'cnt' => 'Cnt',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('cnt',$this->cnt);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Apicredits the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function useCredit($userid, $type){
		if(empty($type)) return false;
		if(!isset(Apicredits::$types[$type])) return false;
		
		
		$sql = 'update `apicredits` set `cnt` = `cnt` - 1 where user_id = :userid and type = :type limit 1';
		
		$connection=Yii::app()->db;
		$command=$connection->createCommand($sql);
		$command->bindParam(":userid",$userid,PDO::PARAM_INT);
		$command->bindParam(":type",$type,PDO::PARAM_STR);
		
		return ($command->execute() === 1);
	}
	
	public static function hasCredit($userid,$type){
		if(empty($type)) return false;
		if(!isset(Apicredits::$types[$type])) return false;
		
		$credit = Apicredits::model()->findByAttributes(array('user_id'=>$userid,'type'=>$type));
		
		return ($credit->cnt > 0);
	}
	
	public static function addDefaultCredits($userid){
		$sql = 'insert into `apicredits` (user_id,type,cnt) values (:userid,"api",:apiCnt),(:userid,"report",:reportCnt)';
		
		$connection=Yii::app()->db;
		$command=$connection->createCommand($sql);
		$command->bindParam(":userid",$userid,PDO::PARAM_INT);
		$command->bindParam(":apiCnt",self::$defaultApiCredits,PDO::PARAM_INT);
		$command->bindParam(":reportCnt",self::$defaultReportCredits,PDO::PARAM_INT);
		$command->execute();
	}
}
