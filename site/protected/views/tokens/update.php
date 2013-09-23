<?php
/* @var $this TokensController */
/* @var $model Tokens */

$this->breadcrumbs=array(
	'Tokens'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Tokens', 'url'=>array('index')),
	array('label'=>'Create Tokens', 'url'=>array('create')),
	array('label'=>'View Tokens', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Tokens', 'url'=>array('admin')),
);
?>

<h1>Update Tokens <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>