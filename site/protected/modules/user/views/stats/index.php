<?php
/* @var $this StatsController */

$this->breadcrumbs=array(
	'Stats',
);

$cs = Yii::app()->getClientScript();

//jquery and google scripts
$cs->registerScriptFile('http://www.google.com/jsapi');
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');

//jquery ui
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);


?>

<h1>API Usage Stats</h1>


<?php
$this->beginWidget('zii.widgets.CPortlet', array(
	'title'=>'Line Chart',
));
?>
<div id="mainGraph">Loading...</div>
<?php $this->endWidget();?>


<script>
google.load('visualization', '1.0', {'packages':['corechart']});

var graphdata = <?php echo json_encode($this->data); ?>;

$(document).ready(function(){
	var gdata = new google.visualization.DataTable();
	gdata.addColumn('string','Date');
	gdata.addColumn('number','Count');
	
	for(var x in graphdata){
		console.log(graphdata[x].interval);
		gdata.addRow([graphdata[x].interval, parseInt(graphdata[x].cnt)]);
	}

	var options = {
      title: 'Total Usage Stats',
      vAxis: {title: 'Date'}, hAxis: {title:'Total API Requests'},
      width: 500, height:300,
    };

	target = $('#mainGraph')[0];
	new google.visualization.BarChart(target).draw(gdata, options);
});
</script>
