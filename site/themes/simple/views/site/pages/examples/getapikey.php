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

<h1>Finding Your API Key</h1>

<h2>First Create an Account</h2>
<p>
	Go to <a href="/user/login" target="_blank" title="Create Account">login/register</a> to create and account.
</p>

<h2>Find Your API Key</h2>
<p>
	Go to <a href="/user/profile" target="_blank" title="Get API Key">profile</a> page to get your Api Key.
</p>
<p>
	<img style="border:solid #333 2px;margin-left:10px;" class="shadow" src="/images/apikey.jpg" alt="Api Key Highlighted" title="Find your api key" />
</p>


</div>

<div class="clear"></div>