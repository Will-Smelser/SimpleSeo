<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 12/15/13
 * Time: 11:06 AM
 */

class ServerEntryWidget extends CWidget {
    public $data;
    public function run(){
        if($this->data->error){
            echo 'Error: ' . $this->data->msg;
        }else{
            echo Yii::app()->controller->renderInput($this->data->data);
        }
    }
} 