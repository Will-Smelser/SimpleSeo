<?php
/* @var $this TokensController */
/* @var $model Tokens */

$this->breadcrumbs=array(
	'Tokens'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Tokens', 'url'=>array('index')),
	array('label'=>'Manage Tokens', 'url'=>array('admin')),
);
?>

<h1>Create Tokens</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>