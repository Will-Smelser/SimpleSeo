<?php 
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerCssFile('/themes/simple/css/uiselect.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);
$cs->registerScriptFile('/themes/simple/js/jquery.uiselect.min.js',CClientScript::POS_END);
?>
<?php echo $this->renderPartial('../profile/menu'); ?>
<h1>Your Credits</h1>
<?php 

if(empty($this->data)){
	echo "<h2>No Credits</h2>";
}else{
	foreach($this->data as $row){ 

?>

<div>
<div class="span-6">
	<h2><?php echo $row['title']; ?>:</h2>
</div>
<div class="span-5 last">
	<h3><?php echo $row['cnt']; ?></h3>
</div>
<div class="clear"></div>
</div>
<?php
	} 
}
?>

<h1>Purchase More Report Credits</h1>
<h2>$ <?php echo number_format(Apicredits::$types['report']['unit'],2); ?> per Report</h2>
<p>Some information</p>
<div>
<form action="/paypal/buy" method="get">
<input type="hidden" name="type" value="report" />
<select name="count" style="width:150px;">
	<?php 
	$j=1;
	$k=5;
	for($i=5; $i<220; $i+=$j){ 
		$val = $i;
		if($i%$k == 0 && $i > 5){
			$k += 5;
			$j += ($j==1) ? 4 : 5;
		}
		$select = ($i===10) ? 'selected' : '';
		echo "\t<option value='$val' $select>$val Reports</option>\n";
	}
	?>
</select>
<input type="submit" value="Buy Credits w/Paypal" />
</form>
</div>

<br/><br/>

<h1>Purchase More API Credits</h1>
<h2>$ <?php echo number_format(Apicredits::$types['api']['unit']*1000.0,2); ?> per 1000 Requests</h2>
<div>
<p>Some information</p>
<form action="/paypal/buy" method="get">
<input type="hidden" name="type" value="api" />
<select name="count" style="width:150px;">
	<?php 
	for($i=1; $i<15; $i++){ 
		$val = $i*500;
		$select = ($i===2) ? 'selected' : '';
		echo "\t<option value='$val' $select>$val Requests</option>\n";
	}
	?>
</select>
<input type="submit" value="Buy Credits w/Paypal" />
</form>
</div>

<script>
$(document).ready(function(){
	$("input[type='submit']").button();
	$('select').uiselect();
});
</script>