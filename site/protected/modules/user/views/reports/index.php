<?php
//jquery ui
$cs = Yii::app()->getClientScript();
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js');

$cs->registerCssFile('/themes/simple/css/uiselect.css');
$cs->registerScriptFile('/themes/simple/js/jquery.uiselect.min.js');

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
            'buttons' => array(
                'view' => array(
                    //'label'=>'hello',
                    'url'=>'$data->id',
                    'click' => 'function(evt){showDialog.call(this,evt);return false;}',

                    'options' => array(
                        'onclick' => 'return false;',
                    ),
                )
            ),
        ),
    ),
));
?>
<div id="dialog" title="Choose Template">
    <p>Choose which template you would like to view data in:
    <div style="position:relative">

    <select id="template_select">
        <?php
        foreach($templates as $id=>$tpl)
            echo "\n\t\t<option value='$id'>{$tpl['name']}</option>";
        ?>
    </select>

    <input type="hidden" value="" id="viewID" />
    <input type="button" value="Submit" onclick="showView()" id="viewSubmit" />

    </div>
    </p>
</div>
<script>
var showDialog = function(evt){
    $('#viewID').val($(this).attr('href'));
    viewDialog.dialog("open");
};

var showView = function(){
    var id=$('#viewID').val();
    var template = $('#template_select').val();
    window.open("/user/reports/view?template="+template+"&id="+id);
    viewDialog.dialog("close");
}

//$("#menu").menu({ position: { my: "left top", at: "left bottom" } });
var viewDialog = $('#dialog').dialog({ autoOpen: false });
$('select').uiselect();
$('#viewSubmit').button();
</script>
