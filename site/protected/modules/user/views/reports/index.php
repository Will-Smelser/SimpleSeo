<?php
/* @var $this ReportsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Reports',
);

/*
$this->menu=array(
	array('label'=>'Create Reportdata', 'url'=>array('create')),
	array('label'=>'Manage Reportdata', 'url'=>array('admin')),
);
*/
?>

<?php echo $this->renderPartial('../profile/menu'); ?>

<h1>Saved Reports</h1>

<div class="search-form" style="display:none">
    <?php $this->renderPartial('_search',array(
        'model' => $model,
    )); ?>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'my-model-grid',
    'dataProvider' => $model->search($userid),
    'filter' => $model,
    'cssFile'=>'/themes/simple/css/grids.css',
    'columns'=>array(
        'id','uri','domain',
        array(            // display 'create_time' using an expression
            'name'=>'created',
            'value'=>'date("M j, Y h:i:s a", strtotime($data->created))',
        ),
        //array(            // display 'author.username' using an expression
        //    'name'=>'authorName',
        //    'value'=>'$data->author->username',
        //),
        array(            // display a column with "view", "update" and "delete" buttons
            'class'=>'CButtonColumn',
            'template'=>'{view}{delete}',
        ),
    ),
));
?>
