<?php  
$baseUrl = Yii::app()->theme->baseUrl;
$cs = Yii::app()->getClientScript();

//jquery and google scripts
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');

//jquery ui
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);

//syntax highlighting
$cs->registerCssFile('http://alexgorbatchev.com/pub/sh/current/styles/shThemeDefault.css');
$cs->registerScriptFile('http://alexgorbatchev.com/pub/sh/current/scripts/shCore.js',CClientScript::POS_END);
$cs->registerScriptFile('http://alexgorbatchev.com/pub/sh/current/scripts/shAutoloader.js',CClientScript::POS_END);
$cs->registerScriptFile('http://agorbatchev.typepad.com/pub/sh/3_0_83/scripts/shBrushJScript.js',CClientScript::POS_END);
$cs->registerScriptFile('http://agorbatchev.typepad.com/pub/sh/3_0_83/scripts/shBrushXml.js',CClientScript::POS_END);
$cs->registerScriptFile('http://agorbatchev.typepad.com/pub/sh/3_0_83/scripts/shBrushPhp.js',CClientScript::POS_END);



?>
<div class="span-23 final" style="overflow:hidden">

<h1>Basic Tutorials / Examples</h1>

<ul>
	<li><a href="/site/pages/getstarted">Getting Started</a></li>
	<li><a href="/site/pages/examples/getapikey">Get API Key</a></li>
	<li><a href="/site/pages/examples/getapitoken">Get API Token</a></li>
</ul>

</div>
<div class="clear"></div>