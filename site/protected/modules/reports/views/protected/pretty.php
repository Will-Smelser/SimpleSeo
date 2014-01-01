
<?php 
require_once(Yii::getPathOfAlias('ext.seo').'/config.php');

//just hardcoding this is, not the best
define('SEO_URI_REPORTS_LOCAL','themes/reports/');

//add js and such
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
$cs->registerScriptFile('http://'. SEO_HOST . '/' . SEO_URI_REPORTS_LOCAL . 'js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_HEAD);

?>

<link rel="stylesheet" type="text/css" href="<?php echo 'http://'. SEO_HOST . '/' . SEO_URI_REPORTS_LOCAL; ?>css/report_pretty.css">

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS_LOCAL; ?>js/api/SeoApi.js" data-seoapi-ns="_SeoApi_" ></script>
<script>

/**
 * Here is an example of using the supplied javascript framework for
 * dynamically loading api classes and making requests.
 */
var url = "<?php echo isset($_GET['target']) ? urlencode($_GET['target']):''; ?>";
var api = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>";

window.SeoReport = "/themes/reports";

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
var seo = new SeoApi('http://<?php echo SEO_HOST . '/'; ?>themes/reports/js/api/pretty/',api,token);
var data = <?php echo isset($data) ? $data : "null"; ?>;

var _data = function(name){
    return (window.data == null || typeof window.data[name] == "undefined")
        ? null : window.data[name];
}

//loads the 
seo.init('base');
seo.init('render');

var fnLoadComplete = function(id, data, ctx){
    console.log("fnLoadComplete",this);
    this.handleSuccess(data,ctx);
    $('#'+id).slideDown().prev('h2').find('span.loading').remove();
}

var google = seo.load('google').extend('base')
	.addMethod('getPageRank','#google-pr')
	.addMethod('getBacklinks','#google-backlinks')
	.exec(url,function(data,ctx){fnLoadComplete.call(this,'google',data,ctx)},null, _data("google"));

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
	.exec(url, function(data,ctx){
        fnLoadComplete.call(this,'body',data,ctx);
        $('#wordsext').slideDown().prev('h2').find('span.loading').remove();
    },null,_data("body"));


var head = seo.load('head').extend('base')
	.addMethod('all',"#head-info")
	.exec(url,function(data,ctx){fnLoadComplete.call(this,'head-info',data,ctx);},null,_data("head"));


var server = seo.load('server').extend('base')
	.addMethod('getWhois','#server-whois')
	.addMethod('getHeaderResponseLine','#server-general-info')
	.addMethod('getLoadTime','#server-general-info')
	.addMethod('isGzip','#server-general-info')
	.addMethod('getServer','#server-general-info')
	.exec(url,function(data,ctx){fnLoadComplete.call(this,'server',data,ctx);},null,_data("server"));

var w3c = seo.load('w3c').extend('base')
    .addMethod('validateW3C','#w3c-general')
    .addMethod('getValidateW3Cerrors','#w3c-error')
    .addMethod('getValidateW3Cwarnings','#w3c-warning')
    .exec(url,function(data,ctx){fnLoadComplete.call(this, 'validateW3C', data, ctx);},null,_data("w3c"));

var moz = seo.load('moz').extend('base')
	.addMethod('getMozLinks','#moz-link')
	.addMethod('getMozJustDiscovered','#moz-disc')
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'moz',data,ctx);},null,_data("moz"));

var semrush = seo.load('semrush').extend('base')
    .addMethod('getDomainReport','#semrush-domain')
	.addMethod('getKeyWordsReport','#semrush-keywords')
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'semrush',data,ctx)},null,_data("semrush"));

var social = seo.load('social').extend('base')
	.addMethod('all','#social')
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'social',data,ctx);},null,_data("social"));

if(data == null)
    seo.save("/reports/save?token="+token,url,google,body,server,head,w3c,moz,semrush,social);

</script>

<div class="container">

<div class="span-4">
    <?php echo $this->renderPartial('application.modules.reports.views.partials.menu'); ?>
</div>

<div class="span-19 last">

<h1 id="top">SEO Report - <?php echo $target; ?></h1>


<div id="all-content">

<!-- api/server -->
<h2 id="info-server" class="first">Server Information <span class="loading">&nbsp;</span></h2>
	<!-- 
		api/server/
			getHeaderResponseLine, getHeaderField, getServer, getServer, isGzip, getLoadTime, getWhois
	-->
    <div id="server" class="title-info subinfo">
        <h4>General Info</h4>
        <div id="server-general-info" class="loading-text"></div>

        <h4>Domain Information</h4>
        <div id="server-whois" class="loading-text"></div>
    </div>
<!-- api/head -->
<h2 id="info-head">HTML Head Information <span class="loading">&nbsp;</span></h2>
	
<div id="head-info" class="loading-text title-info"></div>

<!-- api/body -->
<h2 id="info-body">HTML Body Information <span class="loading">&nbsp;</span></h2>
    <div id="body" class="title-info subinfo">
        <!-- checkH1, checkH2, checkH3, checkH4 -->
        <h4>Header Tags</h4>
        <div id="body-header-tags" class="loading-text"></div>

        <h4>Keywords</h4>
        <div id="body-keywords" class="loading-text"></div>

        <h4>Inline Styles</h4>
        <div id="body-inline-style" class="loading-text"></div>

        <h4>Link Data</h4>
        <div id="body-anchors" class="loading-text"></div>

        <h4>Frames / Object Tags</h4>
        <div id="body-bad-stuff" class="loading-text"></div>

        <h4>Image Analysis</h4>
        <div id="body-images" class="loading-text"></div>
    </div>

<h2 id="info-w3c">W3C Validation <span class="loading">&nbsp;</span></h2>
    <div id="validateW3C" class="title-info subinfo">
        <!-- /api/server/validateW3C -->
        <h4>General</h4>
        <div id="w3c-general" class="loading-text"></div>

        <!-- api/server/getValidateW3Cerrors -->
        <h4>Errors</h4>
        <div id="w3c-error" class="loading-text"></div>

        <!-- /api/server/getValidateW3Cwarnings -->
        <h4>Warnings</h4>
        <div id="w3c-warning" class="loading-text"></div>
    </div>

<h2 id="info-social">Social Stats <span class="loading">&nbsp;</span></h2>
	<div id="social" class="loading-text title-info subinfo"></div>
	
<h2 id="info-google">Google Stats <span class="loading">&nbsp;</span></h2>
    <div id="google" class="title-info subinfo">
        <h4>Page Rank: <b id="google-pr" class="loading-text"></b></h4>

        <h4>Back Links</h4>
        <div id="google-backlinks" class="loading-text"></div>
    </div>

<h2 id="info-moz">SEO Moz Stats <span class="loading">&nbsp;</span></h2>
    <div id="moz" class="title-info subinfo">
        <h4>Moz General Information</h4>
        <div id="moz-link" class="loading-text"></div>

        <h4>Moz Just Discovered Backlinks</h4>
        <div id="moz-disc" class="loading-text"></div>
    </div>
	
<h2 id="info-semrush">SEMrush Stats <span class="loading">&nbsp;</span></h2>
    <div id="semrush" class="title-info subinfo">
        <h4>Domain Data</h4>
        <div id="semrush-domain" class="loading-text"></div>

        <h4>Domain Keyword Data</h4>
        <div id="semrush-keywords" class="loading-text"></div>
    </div>

<h2 id="info-keywords">Keywords (Extended) <span class="loading">&nbsp;</span></h2>
    <div id="wordsext" class="title-info subinfo">
        <h4>Contains phrases using listed key words</h4>
        <div id="body-keywords2" class="loading-text"></div>
    </div>

</div>

</div>

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

</div>

<script>
$(document).ready(function(){

    var $list = $('#info-index');//.empty();
    var $index = $('#info-index-wrap');
    var posTop = $index.offset().top;
    var indexHeight = $index.height();

    $('a[href*=#]:not([href=#])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            $(this).children().addClass('selected');
            $(this).parent().siblings().children().children().removeClass('selected');

            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            target.siblings().removeClass('selected');
            target.addClass('selected');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 1000);

                return false;
            }
        }
    });


    $(window).scroll(function () {
        var s = $(this).scrollTop();
        if (s > posTop) {
            if((s-posTop)+indexHeight < $('#all-content').height())
                $index.css('margin-top',(s-posTop)+'px');
        } else {
            $index.css('margin-top','0px');
        }
    });

    $('#save').click(function(evt){
        $('h2').removeClass('selected');
        var content = '<!DOCTYPE html><html><head>' +
            '<link rel="stylesheet" type="text/css" href="<?php echo 'http://'. SEO_HOST . '/' . SEO_URI_REPORTS_LOCAL; ?>/css/report_pretty.css">'+
            '</head><body><h1>SEO Report <span class="by-author">by <a href="http://simple-seo-api.com">simple-seo-api.com</a></span></h1>'+
            '<div id="all-content">' + $('#all-content').html() + '</div></body></html>';
        $('#save-form textarea:first').val(content).parent().submit();

    });

    $('#edit').button({icons:{primary:"ui-icon-pencil"}}).click(function(){
        (editing) ? editOff() : editOn();
    });
})
</script>
