<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 11/9/13
 * Time: 11:49 AM
 */
class Wloading extends CWidget{
    public $wrapperClass='span-23';
    public $wrapperStyle='position:absolute;top:140px;';
    public $width='500px';
    public $text='Loading...';

    public function init(){}

    public function run(){
        $this->render('loading',array(
            'wrapperStyle'=>$this->wrapperClass,
            'wrapperClass'=>$this->wrapperStyle,
            'width'=>$this->width,
            'text'=>$this->text
        ));
    }
}
?>