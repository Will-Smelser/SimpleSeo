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

<?php echo $this->renderPartial('../profile/menu'); ?>

<h1>Usage Stats</h1>


<!-- BASE GRAPH -->
<?php
$this->beginWidget('zii.widgets.CPortlet', array(
	'title'=>'API Usage Totals',
));
?>
<div id="mainForm">
	<input id="start" type="text" value="<?php echo $this->start; ?>" />
	<input id="stop" type="text" value="<?php echo $this->stop; ?>" />
	<input id="mainBtn" type="button" value="Update" />
	<div id="mainGraph">Loading...</div> 
</div>

<div id="detailForm" style="display:none">
	<input id="detailBtn" type="button" value="<< Back" />
	<div id="details" style="width:100%;">Loading...</div>
</div>
	
<?php $this->endWidget();?>

<!-- BASE GRAPH -->
<?php
$this->beginWidget('zii.widgets.CPortlet', array(
	'title'=>'Report Usage Totals',
));
?>
<div id="mainFormr">
	<input id="startr" type="text" value="<?php echo $this->start; ?>" />
	<input id="stopr" type="text" value="<?php echo $this->stop; ?>" />
	<input id="mainBtnr" type="button" value="Update" />
	<div id="mainGraphr">Loading...</div> 
</div>

	
<?php $this->endWidget();?>


<script>
google.load('visualization', '1.0', {'packages':['corechart']});

var chart1 = null;
var chart1Data = null;
var chart2 = null;
var chart3 = null;
var chart3Data = null;

var chart1Options = {
	title: 'Total Usage Stats',
    vAxis: {title: 'Date'}, hAxis: {title:'Total API Requests'},
    hAxis: {viewWindow:{min:0}},
    width: '100%', height:300, 
    backgroundColor: { fill:'transparent' },
    chartArea:{width:"70%",height:"50%"}
   };
   
var chart3Options = {
	title: 'Total Usage Stats',
    vAxis: {title: 'Date'}, hAxis: {title:'Total Report Requests'},
    hAxis: {viewWindow:{min:0}},
    width: '100%', height:300, 
    backgroundColor: { fill:'transparent' },
    chartArea:{width:"70%",height:"50%"}
   };

var initForms = function(){
	var start = "<?php echo implode('-',$this->startDetail); ?>";
	var stop = "<?php echo implode('-',$this->stopDetail); ?>";

	var format =  "yy-mm-dd";
	$( "#start" ).datepicker()
		.datepicker("option","dateFormat",format)
		.datepicker( "setDate", start );
	
	$( "#stop" ).datepicker()
	.datepicker("option","dateFormat",format)
	.datepicker( "setDate", stop );

	$('#mainBtn').button().click(function(){
		start = $('#start').val();
		stop = $('#stop').val();

		$.getJSON('/user/stats/totals?start='+start+'&stop='+stop)
			.done(function(data){
				
				chart1Data = google.visualization.arrayToDataTable(data);

				if(chart1 === null){
					chart1 = new google.visualization.BarChart($('#mainGraph')[0]);
					addListener();
				}else
					chart1.clearChart();

				chart1Options.height =  getHeight(data.length);
				chart1Options.chartArea.height = chart1Options.height - 75;
				chart1.draw(chart1Data,chart1Options);
			})
			.fail(function(){
				alert('Sorry, but the request failed.  Try again.');
			});		
	});

	$('#detailBtn').button().click(function(){
		$('#mainForm').slideDown();
		$('#detailForm').slideUp();
	});
	
	//This is the reports one
	$( "#startr" ).datepicker()
		.datepicker("option","dateFormat",format)
		.datepicker( "setDate", start );
	
	$( "#stopr" ).datepicker()
	.datepicker("option","dateFormat",format)
	.datepicker( "setDate", stop );
	
	$('#mainBtnr').button().click(function(){
		var start = $('#startr').val();
		var stop = $('#stopr').val();

		$.getJSON('/user/stats/totals?start='+start+'&stop='+stop+'&query=report')
			.done(function(data){
				
				chart3Data = google.visualization.arrayToDataTable(data);

				if(chart3 === null){
					chart3 = new google.visualization.BarChart($('#mainGraphr')[0]);
				}else
					chart3.clearChart();

				chart3Options.height =  getHeight(data.length);
				chart3Options.chartArea.height = chart3Options.height - 75;
				chart3.draw(chart3Data,chart3Options);
			})
			.fail(function(){
				alert('Sorry, but the request failed.  Try again.');
			});		
	});
	
};

var addListener = function(){
	google.visualization.events.addListener(chart1, 'select', function(){
		var item = chart1.getSelection()[0];
		if(item){
			
			$('#mainForm').slideUp();
			
			var columnDate = chart1Data.getValue(item.row, 0);
			var url = '/user/stats/methods?start='+columnDate+'&stop='+columnDate;
			$.getJSON(url)
				.done(function(data){
					
					var gdata = google.visualization.arrayToDataTable(data);
					
					var ht = getHeight(data[0].length);
					$('#detailForm').css('height',ht+25+'px').slideDown();
					
					if(chart2 === null)
						chart2 = new google.visualization.BarChart($('#details')[0]);
					else
						chart2.clearChart();

					
					chart1Options.height = ht;
					chart1Options.chartArea.height = ht - 75;
					chart2.draw(gdata, chart1Options);
					
				})
				.fail(function(){
					alert('Sorry, but the request failed.  Try again.');
					$('#mainForm').slideDown();
				});
		}
	});
	
};

var getHeight = function(dataLength){
	return 100 + dataLength * 20;
}

$(document).ready(function(){
	initForms();
	$('#mainBtn').click();
	$('#mainBtnr').click();
});
</script>
