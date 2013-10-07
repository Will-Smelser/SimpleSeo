<?php
$this->pageTitle='Purchase Credits';
//$this->breadcrumbs=array('',);

$baseUrl = Yii::app()->theme->baseUrl;
$cs = Yii::app()->getClientScript();

//jquery and google scripts
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');

//jquery ui
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);

foreach(Yii::app()->user->getFlashes() as $key => $message) {
	echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
}


?>



<h1>Report Credits</h1>
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

<h1>API Credits</h1>
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