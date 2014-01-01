<script>
<?php 
require_once(Yii::getPathOfAlias('ext.seo').'/config.php');

function printLoading(){
	echo '<img src="http://' . SEO_HOST . '/' . SEO_URI_REPORTS . 'images/loading.gif" />&nbsp;Loading...';
}

?>

/**
 * Here is an example of using the supplied javascript framework for
 * dynamically loading api classes and making requests.
 */
var url = "<?php echo isset($target) ? urlencode($target):''; ?>";
var api = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>";
//var api = "<?php echo 'http://localhost/simple-seo-api.com/site/'.SEO_URI_API; ?>";

window.SeoReport = "<?php echo Yii::app()->theme->baseUrl; ?>";

/**
 * An example of how to use other namespaces to load content.  This allows for
 * multithreaded download of content.  Should be considered that each request
 * to the API requires the api to download the content again and reparse things.
 *//*
SeoApi2 = new SeoApi('SeoApi2','http://<?php echo SEO_HOST . '/' . SEO_URI_API_JS; ?>');
SeoApi2.load('body').depends('render').addMethod('checkH1','#body-header-tags').exec(url);
*/
<?php

//got to get a valid token
require_once SEO_PATH_HELPERS . 'ClientHash.php';
$token = "TOKEN_GET_FAILED";
try{
	$token = \api\clients\ClientHash::getToken(Yii::app()->params['apiKeyReport'],'report',SEO_HOST);
}catch(Exception $e){
	//do nothing, just everything will fail.
}

?>
var token = "<?php echo $token; ?>";
var seo = new SeoApi('http://<?php echo SEO_HOST . '/' . SEO_URI_API_JS; ?>',api,token);
var data = <?php echo isset($data) ? $data : "null"; ?>;

var _data = function(name){
    return (window.data == null || typeof window.data[name] == "undefined")
        ? null : window.data[name];
}

//loads the 
seo.init('base');
seo.init('render');

var google = seo.load('google').extend('base')
	.addMethod('getPageRank','#google-pr')
	.addMethod('getBacklinks','#google-backlinks')
    .exec(url,null,null,_data("google"));

var body = seo.load('body').extend('base')
	.addMethod('checkH1','#body-header-tags')
	.addMethod('checkH2','#body-header-tags')
	.addMethod('checkH3','#body-header-tags')
	.addMethod('checkH4','#body-header-tags')
	.addMethod('getKeyWords','#body-keywords')
	.addMethod('getPhrases','#body-keywords2')
	.addMethod('checkLinkTags','#body-inline-style')
	.addMethod('checkInlineCSS','#body-inline-style')
	.addMethod('checkInlineStyle','#body-inline-style')
	.addMethod('getInternalAnchor','#body-anchors')
	.addMethod('getExternalAnchors','#body-anchors')
	.addMethod('checkForFrames','#body-bad-stuff')
	.addMethod('checkForIframes','#body-bad-stuff')
	.addMethod('checkForFlash','#body-bad-stuff')
	.addMethod('checkImages','#body-images')
    .exec(url,null,null,_data("body"));

var head = seo.load('head').extend('base')
	.addMethod('all',"#head-info")
    .exec(url,null,null,_data("head"));

var server = seo.load('server').extend('base')
	.addMethod('getWhois','#server-whois')
	.addMethod('getHeaderResponseLine','#server-general-info')
	.addMethod('getLoadTime','#server-general-info')
	.addMethod('isGzip','#server-general-info')
	.addMethod('getServer','#server-general-info')
    .exec(url,null,null,_data("server"));

var w3c = seo.load('w3c').extend('base')
    .addMethod('validateW3C','#w3c-general')
    .addMethod('getValidateW3Cerrors','#w3c-error')
    .addMethod('getValidateW3Cwarnings','#w3c-warning')
    .exec(url,null,null,_data("w3c"));

var moz = seo.load('moz').extend('base')
	.addMethod('getMozLinks','#moz-link')
	.addMethod('getMozJustDiscovered','#moz-disc')
    .exec(url,null,null,_data("moz"));

var semrush = seo.load('semrush').extend('base').addMethod('getDomainReport','#semrush-domain')
	.addMethod('getKeyWordsReport','#semrush-keywords')
    .exec(url,null,null,_data("semrush"));

var social = seo.load('social').extend('base')
	.addMethod('all','#social')
    .exec(url,null,null,_data("social"));

if(data == null)
    seo.save("/reports/save?token="+token,url,google,body,server,head,w3c,moz,semrush,social);

</script>


<h1>SEO Report</h1>

<div id="all-content">

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/basic.js"></script>


<div style="float:right" id="save-edit-wrap">
	<input id="save" type="button" value="Save" />
	<input id="edit" type="button" value="Edit" />
</div>

<h2 id="report-title">Report - <?php echo $target; ?></h2>

<!-- api/server -->
<h3>Server Information <a class='addComment'>add comment</a></h3>
	<!-- 
		api/server/
			getHeaderResponseLine, getHeaderField, getServer, getServer, isGzip, getLoadTime, getWhois
	-->
	<h4>General Info</h4>
	<div id="server-general-info" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Domain Information</h4>
	<div id="server-whois" class="loading-text"><?php printLoading(); ?></div>
	
<!-- api/head -->
<h3>HTML Head Information <a class='addComment'>add comment</a></h3>
	
<div id="head-info" class="loading-text"><?php printLoading(); ?></div>

<!-- api/body -->
<h3>HTML Body Information <a class='addComment'>add comment</a></h3>
	
	<!-- checkH1, checkH2, checkH3, checkH4 -->
	<h4>Header Tags</h4>
	<div id="body-header-tags" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Keywords</h4>
	<div id="body-keywords" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Inline Styles</h4>
	<div id="body-inline-style" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Link Data</h4>
	<div id="body-anchors" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Frames / Object Tags</h4>
	<div id="body-bad-stuff" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Image Analysis</h4>
	<div id="body-images" class="loading-text"><?php printLoading(); ?></div>

<h3>W3C Validation <a class='addComment'>add comment</a></h3>

	<!-- /api/server/validateW3C -->
	<h4>General</h4>
	<div id="w3c-general" class="loading-text"><?php printLoading(); ?></div>
	
	<!-- api/server/getValidateW3Cerrors -->
	<h4>Errors</h4>
	<div id="w3c-error" class="loading-text"><?php printLoading(); ?></div>

	<!-- /api/server/getValidateW3Cwarnings -->
	<h4>Warnings</h4>
	<div id="w3c-warning" class="loading-text"><?php printLoading(); ?></div>
	
<h3>Social Stats <a class='addComment'>add comment</a></h3>
	
	<div id="social" class="loading-text"><?php printLoading(); ?></div>
	
<h3>Google Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Page Rank: <b id="google-pr" class="loading-text"><?php printLoading(); ?></b></h4>

	<h4>Back Links</h4>
	<div id="google-backlinks" class="loading-text"><?php printLoading(); ?></div>
	
<h3>SEO Moz Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Moz General Information</h4>
	<div id="moz-link" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Moz Just Discovered Backlinks</h4>
	<div id="moz-disc" class="loading-text"><?php printLoading(); ?></div>
	
	
<h3>SEMrush Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Domain Data</h4>
	<div id="semrush-domain" class="loading-text"><?php printLoading(); ?></div>
	
	<h4>Domain Keyword Data</h4>
	<div id="semrush-keywords" class="loading-text"><?php printLoading(); ?></div>

<h3>Keywords (Extended) <a class='addComment'>add comment</a></h3>
	<h4>Contains phrases using listed key words</h4>
	<div id="body-keywords2" class="loading-text"><?php printLoading(); ?></div>


<div id="popup" title="Information">
	<div id="popup-content"></div>
</div>

<?php 
//get the filename, we want this to save as
$filename = str_replace('/','-',preg_replace('@https?://@i','',$target)) . '.html';
?>
<form id="save-form" action="http://<?php echo SEO_HOST; ?>/reports/save/<?php echo $filename; ?>" method="POST" target="_blank" style="display:none">
	<textarea name="data" id="save-form-data"></textarea>
</form>

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/jquery-ui-1.10.3.custom.min.js"></script>

</div>
