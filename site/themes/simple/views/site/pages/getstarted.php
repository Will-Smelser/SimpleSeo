<?php  
$baseUrl = Yii::app()->theme->baseUrl;
$cs = Yii::app()->getClientScript();

//jquery and google scripts
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');

//jquery ui
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);

//syntax highlighting
$cs->registerCssFile('/syntaxhighlighter/styles/shCore.css');
$cs->registerCssFile('/syntaxhighlighter/styles/shThemeDefault.css');
$cs->registerScriptFile('/syntaxhighlighter/scripts/shCore.js',CClientScript::POS_END);
$cs->registerScriptFile('/syntaxhighlighter/scripts/shAutoloader.js',CClientScript::POS_END);
$cs->registerScriptFile('/syntaxhighlighter/scripts/shBrushJScript.js',CClientScript::POS_END);
$cs->registerScriptFile('/syntaxhighlighter/scripts/shBrushXml.js',CClientScript::POS_END);
$cs->registerScriptFile('/syntaxhighlighter/scripts/shBrushPhp.js',CClientScript::POS_END);



?>

<div class="span-23 final" style="overflow:hidden">
<h1>Getting Started</h1>

<h2>Play with API</h2>
<p>Don't want to read and just see things in action, then go <a href="/site/page?view=apiplay">here</a>.</p>

<h2>Create an Account</h2>
<p>Create an <a href="/user/login" target="_blank">account</a> and login.  You will need an
<b>API Key</b> in order to access the api.
</p>
<p>Once you have your account setup, find your <a href="/images/apikey.jpg" target="_blank">API Key</a>.
</p>


<h2>Adding Necessary Files</h2>
<p>Add the following to the &lt;head&gt; portion of your HTML document.</p>
<pre class="brush: xml; gutter: true; toolbar: false; tab-size: 2;">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://simple-seo-api.com/themes/reports/js/api/SeoApi.js" data-seoapi-ns="_SeoApi_"></script>

</pre>

<p><b>ALERT:</b> The "data-seoapi-ns" attribute is necessary.  This is what
the loader uses for a global namespace.  If this is not set, then the XHR loaded
scripts cannot be communicated with.</p>

<p>The javascript api uses some Jquery internally, so this is necessary.</p>

<h2>Get an Access Token</h2>
<p>It is recommended that you do not make your API Key public.  So to facilitate this
you make a request, server side, to get an access token.  Access tokens give access to
your account to make api calls for a short period of time.</p>

<h2 style="margin:0px;">Make Request w/ PHP for Token</h2>

<pre class="brush: php; gutter: true; toolbar: false; tab-size: 2;"> 
  
//basic setup for request
$username = "[your account username]";
$key = "[your API Key]";
$nonce = substr(str_shuffle(MD5(microtime())),0,7);
$hash = sha1($nonce.$key);	

//build request
$request = "http://www.simple-seo-api.com/api/tokens/getToken?username=%s&nonce=%s&hash=%s";
$result = file_get_contents(sprintf($request,$username,$nonce,$hash));
$result = json_decode($result);

$token = null;
if(isset($result->success) && $result->success === 'true' && !empty($result->token)){
	$token = $result->token;
}
  
</pre>

<h2>Ready!  Lets Query the API</h2>
<p style="margin:0px;">Add this javascript anywhere to your page.</p>
<pre class="brush: javascript; gutter: true; toolbar: false; tab-size: 2;"> 
  

//setup the api
var seo = new SeoApi(
		'http://simple-seo-api.com/themes/reports/js/api/',
		'http://simple-seo-api.com/api/',
		'&lt;?php echo $token; ?>'
);
	
//in almost all cases, we use the base class
seo.init('base');

//this is the default renderer
seo.init('render')

//run api request for body and render results
seo.load('body').extend('base')

	//using callback
	.addMethod('getKeyWords',function(data){
		console.log(data);
	})
	
	//using base render to add content directly into html 
	.addMethod('checkH1','#some_document_id')

    //run seo on your URL
	.exec('http://some/url/to/run/seo/on');  
  
	
</pre>
</div>

<div class="clear"></div>

<script>
$(document).ready(function(){
	SyntaxHighlighter.all();
});
</script>