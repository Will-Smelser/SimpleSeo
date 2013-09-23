<?php
/* @var $this TokensController */
/* @var $model Tokens */

$this->breadcrumbs=array(
	'Tokens'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Tokens', 'url'=>array('index')),
	array('label'=>'Create Tokens', 'url'=>array('create')),
	array('label'=>'Update Tokens', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Tokens', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Tokens', 'url'=>array('admin')),
);
?>

<h1>View Tokens #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'user_id',
		'token',
		'resource',
		'expire',
		'created',
	),
)); ?>
