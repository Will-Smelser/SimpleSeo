<?php  
$baseUrl = Yii::app()->theme->baseUrl;
$cs = Yii::app()->getClientScript();

//jquery and google scripts
$cs->registerScriptFile('http://www.google.com/jsapi');
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');

//jquery ui
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);

//api
$cs->registerScriptFile('/themes/reports/js/api/SeoApi.js',CClientScript::POS_HEAD,array('data-seoapi-ns'=>'_SeoApi_'));

//syntax highlighting
$cs->registerCssFile('/syntaxhighlighter/styles/shCore.css');
$cs->registerCssFile('/syntaxhighlighter/styles/shThemeDefault.css');
$cs->registerScriptFile('/syntaxhighlighter/scripts/shCore.js',CClientScript::POS_END);
$cs->registerScriptFile('/syntaxhighlighter/scripts/shAutoloader.js',CClientScript::POS_END);
$cs->registerScriptFile('/syntaxhighlighter/scripts/shBrushJScript.js',CClientScript::POS_END);


//got to get a valid token
require_once SEO_PATH_HELPERS . 'ClientHash.php';
$token = "TOKEN_GET_FAILED";
try{
	$token = \api\clients\ClientHash::getToken('80997ad7db55c92b61a3ef907a67ef28','sample');
}catch(Exception $e){
	//do nothing, just everything will fail.
}

$example = 'http://www.inedo.com';

?>
<?php $this->pageTitle='Simple Seo Api - Reporting Tool'; ?>

<div class="blue-bg" >
<div class="bg-fade-up">
<div class="container">

<h1 style="padding-top:20px;">A Reporting Tool for Search Engine Optimization</h1>
<div class="span-23 showgrid last">
	<input id="get-url" class="inputfield ui-button ui-corner-all" type="text" value="<?php echo $example; ?>" style="width: 240px" />
	<button id="get-btn">Get Word Count</button>
</div>

<div class="span-23" style="overflow:hidden;padding-top:20px;height:330px" id="test">	
	<h3 style="padding:40px 0px 0px 40px;">Loading...</h3>		
</div>

<div class="clear"></div>

</div>
</div>
</div>

<div class="container">
<div id="content">
<div class="span-14 showgrid" style="overflow:hidden">
<pre class="brush: javascript; gutter: true; toolbar: false; tab-size: 2;">		
//initialize the api
seo = new SeoApi('/themes/reports/js/api/charts/','/api/',
	'<?php echo $token; ?>');
seo.load('base');

//run api request and render results
seo.load('body').extend('base')
	.addMethod('getKeyWords','#test')
	.exec('<?php echo $example; ?>');
</pre>
</div>

<div class="span-7 last">
	<h2>Example Code</h2>
	<p>
		The sample code to the left shows how simple it is to use the
		javascript api to embed SEO reporting contents into your
		document.
	</p>
</div>
<div class="clear"></div>		
<script>

google.load('visualization', '1.0', {'packages':['corechart']});

var token = "<?php echo $token; ?>";

seo = new SeoApi('/themes/reports/js/api/charts/','/api/',token);
seo.load('base');
$(document).ready(function(){
	SyntaxHighlighter.all();
	
	$('#get-btn').button().click(function(){
		$('#test').html('<h3 style="padding:40px 0px 0px 40px;">Loading...</h3>');
		seo.load('body').extend('base')
			.addMethod('getKeyWords','#test')
			.exec($('#get-url').val());
	}).click();
});
</script>

<hr/>

<div style="span-23">
	<div class="dashIcon span-3">
		<a href="/user/login"><img
			src="/themes/shadow_dancer/images/big_icons/icon-people.png"
			alt="Sign Up" /> </a>
		<div class="dashIconText">
			<a href="/user/login">Sign Up</a>
		</div>
	</div>
	<div class="dashIcon span-3">
		<a href="/reports"><img
			src="/themes/shadow_dancer/images/big_icons/icon-chart.png"
			alt="SEO Reports" /> </a>
		<div class="dashIconText">
			<a href="/reports">Reports</a>
		</div>
	</div>
	
	<div class="dashIcon span-3">
		<a href="/api-docs/namespaces/api.html" target="_blank">
			<img src="/themes/shadow_dancer/images/big_icons/icon-book3.png"
				alt="SEO API Documentatoin" /> </a>
		<div class="dashIconText">
			<a href="/api-docs/namespaces/api.html">API Docs</a>
		</div>
	</div>
	<div class="dashIcon span-3">
		<a href="/api-docs-js/SeoApi.html" target="_blank">
			<img src="/themes/shadow_dancer/images/big_icons/icon-book3.png"
				alt="SEO API JS Documentation" />
		</a>
		<div class="dashIconText">
			<a href="/api-docs-js/SeoApi.html">JS API Docs</a>
		</div>
	</div>
</div>

<div class="clear"></div>


</div>
</div>