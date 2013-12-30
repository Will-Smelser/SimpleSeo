<?php

/**
 * This is the model class for table "reportdata".
 *
 * The followings are the available columns in table 'reportdata':
 * @property string $id
 * @property string $data
 * @property integer $user_id
 * @property string $domain
 * @property string $uri
 * @property string $created
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class Reportdata extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reportdata';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('data, user_id, domain, uri', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('domain', 'length', 'max'=>255),
			array('uri', 'length', 'max'=>2048),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, data, user_id, domain, uri, created', 'safe', 'on'=>'search'),
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
			'data' => 'Data',
			'user_id' => 'User',
			'domain' => 'Domain',
			'uri' => 'Uri',
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
	public function search($userid=null)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('uri',$this->uri,true);
		$criteria->compare('created',$this->created,true);

       if(!empty($userid))
            $criteria->addCondition('user_id = '.$userid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder' => 'created DESC',
            ),
            'pagination'=>array(
                'pageSize'=>40,
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Reportdata the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
