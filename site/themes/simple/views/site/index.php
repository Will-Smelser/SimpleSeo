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
	$token = \api\clients\ClientHash::getToken(Yii::app()->params['apiKeySample'],'sample',SEO_HOST);
}catch(Exception $e){
	//do nothing, just everything will fail.
}

$example = 'http://en.wikipedia.org/wiki/Search_engine_optimization';

$this->pageTitle='Simple Seo Api - Reporting Tool';

?>

<div class="blue-bg">
	<div class="bg-fade-up">
		<div class="container">

			<h1 style="padding-top: 20px;">A Reporting Tool for Search
				Engine Optimization</h1>
			<div class="span-23 showgrid last">
				<input id="get-url" class="inputfield ui-button ui-corner-all"
					type="text" value="<?php echo $example;?>"
					style="width: 300px" />
				<button id="get-btn">Get Word Count</button>
			</div>

			<div id="loadingWrapper" style="position:relative;" >
<div  class="span-23" style="position:absolute;top:140px;">					
<div id="noTrespassingOuterBarG" style="width:500px;margin:0px auto;z-index:9999;">
<div id="loadingTxt">Loading...</div>
<div id="noTrespassingFrontBarG" class="noTrespassingAnimationG">
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
<div class="noTrespassingBarLineG"></div>
</div>
</div>
</div>
			</div>
			
			<div class="span-23" style="overflow: hidden; padding-top: 20px; height: 330px" id="graphWrapper"></div>

			<div class="clear"></div>

		</div>
	</div>
</div>
<div class="container">
	<div class="content">
	<div class="span-16 last">
		
		<div>
			<h2>Example Code</h2>
			<p>The sample code below shows how simple it is to use the
				javascript api to embed SEO reporting contents into your document.</p>
		</div>
		<pre
			class="brush: javascript; gutter: true; toolbar: false; tab-size: 2;">		
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
		<div class="dashIcon right span-3 last">
			<a href="/user/login"><img
				src="/themes/shadow_dancer/images/big_icons/icon-people.png"
				alt="Sign Up" /> </a>
			<div class="dashIconText">
				<a href="/user/login">Sign Up</a>
			</div>
		</div>
		<div class="dashIcon right span-3 last">
			<a href="/reports"><img
				src="/themes/shadow_dancer/images/big_icons/icon-chart.png"
				alt="SEO Reports" /> </a>
			<div class="dashIconText">
				<a href="/reports">Reports</a>
			</div>
		</div>

		<div class="dashIcon right span-3 last">
			<a href="/api-docs/namespaces/api.html" target="_blank"> <img
				src="/themes/shadow_dancer/images/big_icons/icon-book3.png"
				alt="SEO API Documentatoin" />
			</a>
			<div class="dashIconText">
				<a href="/api-docs/namespaces/api.html">API Docs</a>
			</div>
		</div>
		<div class="dashIcon right span-3 last">
			<a href="/api-docs-js/SeoApi.html" target="_blank"> <img
				src="/themes/shadow_dancer/images/big_icons/icon-book3.png"
				alt="SEO API JS Documentation" />
			</a>
			<div class="dashIconText">
				<a href="/api-docs-js/SeoApi.html">JS API Docs</a>
			</div>
		</div>
		
	</div>
	<div id="load-temp" class="clear"></div>
	</div>
	
</div>

<div id="loading" style="display:none;"></div>

<script type="text/javascript">

google.load('visualization', '1.0', {'packages':['corechart']});

var token = "<?php echo $token; ?>";

seo = new SeoApi('/themes/reports/js/api/charts/','/api/',token);
seo.load('base');
$(document).ready(function(){

	SyntaxHighlighter.all();
	
	$('#get-btn').button().click(function(){
		$('#loadingWrapper').show();
		seo.load('body').extend('base')
			.addMethod('getKeyWords','#graphWrapper')
			.exec($('#get-url').val(),function(data){
				$('#loadingWrapper').fadeOut();
				this.handleSuccess(data);
			},function(){
				this.handleError();
				$('#loadingWrapper').fadeOut();
			});
	}).click();

	
});
</script>
