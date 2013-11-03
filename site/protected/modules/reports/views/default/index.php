<?php require_once(Yii::getPathOfAlias('ext.seo').'/config.php'); ?>
<?php 
$cs = Yii::app()->getClientScript();
$cs->registerCssFile('/themes/simple/css/form.css');
$cs->registerCssFile('/themes/simple/css/buttons.css');

?>

<h1>SEO Report - <?php echo Yii::app()->name; ?></h1>
<p>
Enter a url that you would like to run a report on such as "www.simple-seo-api.com/site/pages/getstarted".
</p>
<div class="form">
<form id="form-run-report" method="GET" action="/reports/protected">
	<div class="row">
	<label for="url" >URL</label>
	<input class="" name="target" type="text" id="url" style="min-width:350px" />
	</div>
	<input id="run-report" class="btn" type="submit" value="Run Report"/>
</form>
</div>