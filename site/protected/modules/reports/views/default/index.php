<script>
<?php 

if(isset($_GET['data'])){
	$vars = explode('/', $_GET['data']);
	
	if(isset($vars[0]) && $vars[0] === 'dosave'){
		include 'save.php';
		exit;
	}
}

function printLoading(){
	echo '<img src="http://' . SEO_HOST . '/' . SEO_URI_REPORTS . 'images/loading.gif" />&nbsp;Loading...';
}

?>

<?php if(isset($_GET['target'])){ ?>

/**
 * Here is an example of using the supplied javascript framework for
 * dynamically loading api classes and making requests.
 */
var url = "<?php echo isset($_GET['target']) ? urlencode($_GET['target']):''; ?>";
var api = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>";

window.SeoReport = "<?php echo Yii::app()->theme->baseUrl; ?>";

/**
 * An example of how to use other namesapces to load content.  This allows for
 * multithreaded download of content.  Should be considered that each request
 * to the API requires the api to download the content again and reparse things.
 *//*
SeoApi2 = new SeoApi('SeoApi2','http://<?php echo SEO_HOST . '/' . SEO_URI_API_JS; ?>');
SeoApi2.load('body').depends('render').addMethod('checkH1','#body-header-tags').exec(url);
*/
var key = '0d49b3910216d445ab7ae098cbdc6adf';
seo = new SeoApi('http://<?php echo SEO_HOST . '/' . SEO_URI_API_JS; ?>',key);

//loads the 
seo.load(api,'base');
seo.load('render');


seo.load('google').depends('render')
	.addMethod('getPageRank','#google-pr')
	.addMethod('getBacklinks','#google-backlinks')
	.exec(url);

seo.load('body').depends('render')
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
	.exec(url);

seo.load('head').depends('render')
	.addMethod('all',"#head-info")
	.exec(url);

seo.load('server').depends('render')
	.addMethod('getWhois','#server-whois')
	.addMethod('getHeaderResponseLine','#server-general-info')
	.addMethod('getLoadTime','#server-general-info')
	.addMethod('isGzip','#server-general-info')
	.addMethod('getServer','#server-general-info')
	.addMethod('validateW3C','#w3c-general')
	.addMethod('getValidateW3Cerrors','#w3c-error')
	.addMethod('getValidateW3Cwarnings','#w3c-warning')
	.exec(url);

seo.load('moz').depends('render')
	.addMethod('getMozLinks','#moz-link')
	.addMethod('getMozJustDiscovered','#moz-disc')
	.exec(url);

seo.load('semrush').depends('render')
	.addMethod('getDomainReport','#semrush-domain')
	.addMethod('getKeyWordsReport','#semrush-keywords')
	.exec(url);

seo.load('social').depends('render')
	.addMethod('all','#social')
	.exec(url);

<?php } ?>
	
</script>
<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/basic.js"></script>

<link rel="stylesheet" type="text/css" href="http://www.w3.org/StyleSheets/Core/parser.css?family=5&doc=Sampler" />
<link rel="stylesheet" type="text/css" href="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>css/custom-theme/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>css/report_basic.css" />

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>js/jquery-ui-1.10.3.custom.min.js"></script>

</head>

<body>
<div id="all-content">
<h1>SEO Report <span class="by-author">by Will Smelser</span></h1>
<form id="form-run-report" method="GET" action="/reports">
	<label for="url">URL <input name="target" type="text" id="url" /></label>
	<input id="run-report" type="submit" value="Run Report" />
</form>

<?php if(isset($_GET['target'])){ ?>

<div style="float:right" id="save-edit-wrap">
	<button id="save" >Save</button>
	<button id="edit" >Edit</button>
</div>

<h2 id="report-title">Report - <?php echo $_GET['target']; ?></h2>

<!-- api/server -->
<h3>Server Information <a class='addComment'>add comment</a></h3>
	<!-- 
		api/server/
			getHeaderResponseLine, getHeaderField, getServer, getServer, isGzip, getLoadTime, getWhois
	-->
	<h4>General Info</h4>
	<p id="server-general-info" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Domain Information</h4>
	<p id="server-whois" class="loading-text"><?php printLoading(); ?></p>
	
<!-- api/head -->
<h3>HTML Head Information <a class='addComment'>add comment</a></h3>
	
<p id="head-info" class="loading-text"><?php printLoading(); ?></p>

<!-- api/body -->
<h3>HTML Body Information <a class='addComment'>add comment</a></h3>
	
	<!-- checkH1, checkH2, checkH3, checkH4 -->
	<h4>Header Tags</h4>
	<p id="body-header-tags" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Keywords</h4>
	<p id="body-keywords" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Inline Styles</h4>
	<p id="body-inline-style" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Link Data</h4>
	<p id="body-anchors" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Frames / Object Tags</h4>
	<p id="body-bad-stuff" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Image Analysis</h4>
	<p id="body-images" class="loading-text"><?php printLoading(); ?></p>

<h3>W3C Validation <a class='addComment'>add comment</a></h3>

	<!-- /api/server/validateW3C -->
	<h4>General</h4>
	<p id="w3c-general" class="loading-text"><?php printLoading(); ?></p>
	
	<!-- api/server/getValidateW3Cerrors -->
	<h4>Errors</h4>
	<p id="w3c-error" class="loading-text"><?php printLoading(); ?></p>

	<!-- /api/server/getValidateW3Cwarnings -->
	<h4>Warnings</h4>
	<p id="w3c-warning" class="loading-text"><?php printLoading(); ?></p>
	
<h3>Social Stats <a class='addComment'>add comment</a></h3>
	
	<p id="social" class="loading-text"><?php printLoading(); ?></p>
	
<h3>Google Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Page Rank: <b id="google-pr" class="loading-text"><?php printLoading(); ?></b></h4>

	<h4>Back Links</h4>
	<p id="google-backlinks" class="loading-text"><?php printLoading(); ?></p>
	
<h3>SEO Moz Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Moz General Information</h4>
	<p id="moz-link" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Moz Just Discovered Backlinks</h4>
	<p id="moz-disc" class="loading-text"><?php printLoading(); ?></p>
	
	
<h3>SEMrush Stats <a class='addComment'>add comment</a></h3>
	
	<h4>Domain Data</h4>
	<p id="semrush-domain" class="loading-text"><?php printLoading(); ?></p>
	
	<h4>Domain Keyword Data</h4>
	<p id="semrush-keywords" class="loading-text"><?php printLoading(); ?></p>

<h3>Keywords (Extended) <a class='addComment'>add comment</a></h3>
	<h4>Contains phrases using listed key words</h4>
	<p id="body-keywords2" class="loading-text"><?php printLoading(); ?></p>


<div id="popup" title="Information">
	<div id="popup-content"></div>
</div>

<?php 
//get the filename, we want this to save as
$filename = str_replace('/','-',preg_replace('@https?://@i','',$_GET['url'])) . '.html';
?>
<form id="save-form" action="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS; ?>dosave/<?php echo $filename; ?>" method="POST" target="_blank" style="display:none">
	<textarea name="data" id="save-form-data"></textarea>
</form>

</div>

<?php } ?>