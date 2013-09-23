<?php
/* @var $this TokensController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Tokens',
);

$this->menu=array(
	array('label'=>'Create Tokens', 'url'=>array('create')),
	array('label'=>'Manage Tokens', 'url'=>array('admin')),
);
?>

<h1>Tokens</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
