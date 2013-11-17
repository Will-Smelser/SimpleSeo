
<?php 
require_once(Yii::getPathOfAlias('ext.seo').'/config.php');

//just hardcoding this is, not the best
define('SEO_URI_REPORTS_LOCAL','themes/reports/');

//add js and such
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_HEAD);

?>

<style>
    .title-info{
        display: none;
    }
    #info-index{
        margin:0px;
        padding:0px;
    }
    #info-index-wrap{
        position:relative;
    }
    #info-index li{
        list-style: none;
        display: block;
        text-align: center;
        margin-left:0px;
    }
    .info-icon{
        margin:10px;
        padding:5px;
        background-color:#efefef;
        border:solid #ccc 1px;
        border-radius:5px;
        width:100px;
        opacity:.8;
    }
    #info-index a{
        text-decoration: none;
    }
    .info-icon:hover{
        opacity:1;
        background-color:#DDD;
    }
    .info-icon h3{
        font-size:12px;
        padding:5px 0px 0px 0px;
        margin:0px;
        color:#555;
    }
    .loading{
        display:inline-block;
        width:30px;
        background-image:url("/themes/reports/images/loading.gif");
        background-position: right;
        background-repeat: no-repeat;
    }
    #all-content h2{
        border-left:solid #CCC 4px;
        border-bottom:dotted #CCC 1px;
        padding-left:2px;
        font-size:24px;
    }
    #all-content h2.selected{
        border-left-color: #ffb506;
        color:#000;
    }
    .info-icon.selected{
        opacity: 1.0;
        background-color: #ffb506;
        color:#FFF;
    }
    .info-icon.selected h3{
        /*color:#FFF;*/
    }

    /* content section */
    #all-content .subinfo{
        padding-left:20px;
    }
    #all-content .subinfo li{
        list-style: none;
    }
    #all-content h2{
        margin-bottom:5px;
    }
    #all-content h4{
        margin: 0px;
    }

    /* list styles */
    .title-info li{
        margin-left:10px;
    }
    .title-info li .list-key{
        width:125px;
        font-weight:bold;
        display: inline-block;
    }
    h2{
        margin-top:20px;
    }
    h2.first{
        margin-top:0px;
    }

</style>

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS_LOCAL; ?>js/api/SeoApi.js" data-seoapi-ns="_SeoApi_" ></script>
<script>

/**
 * Here is an example of using the supplied javascript framework for
 * dynamically loading api classes and making requests.
 */
var url = "<?php echo isset($_GET['target']) ? urlencode($_GET['target']):''; ?>";
var api = "<?php echo 'http://'.SEO_HOST.'/'.SEO_URI_API; ?>";
//var api = "<?php echo 'http://localhost/simple-seo-api.com/site/'.SEO_URI_API; ?>";

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

var fnLoadComplete = function(id, data, ctx){
    console.log("fnLoadComplete",this);
    this.handleSuccess(data,ctx);
    $('#'+id).slideDown().prev('h2').find('span.loading').remove();
}

var seo = new SeoApi('http://<?php echo SEO_HOST . '/'; ?>themes/reports/js/api/pretty/',api,token);

//loads the 
seo.init('base');
seo.init('render');


seo.load('google').extend('base')
	.addMethod('getPageRank','#google-pr')
	.addMethod('getBacklinks','#google-backlinks')
	.exec(url,function(data,ctx){fnLoadComplete.call(this,'google',data,ctx)});

seo.load('body').extend('base')
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
    });


seo.load('head').extend('base')
	.addMethod('all',"#head-info")
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'head-info',data,ctx);});


seo.load('server').extend('base')

	.addMethod('getWhois','#server-whois')
	.addMethod('getHeaderResponseLine','#server-general-info')
	.addMethod('getLoadTime','#server-general-info')
	.addMethod('isGzip','#server-general-info')
	.addMethod('getServer','#server-general-info')
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'server',data,ctx);});

seo.load('w3c').extend('base')
    .addMethod('validateW3C','#w3c-general')
    .addMethod('getValidateW3Cerrors','#w3c-error')
    .addMethod('getValidateW3Cwarnings','#w3c-warning')
    .exec(url,function(data,ctx){fnLoadComplete.call(this, 'validateW3C', data, ctx);});

seo.load('moz').extend('base')
	.addMethod('getMozLinks','#moz-link')
	.addMethod('getMozJustDiscovered','#moz-disc')
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'moz',data,ctx);});

seo.load('semrush').extend('base')
    .addMethod('getDomainReport','#semrush-domain')
	.addMethod('getKeyWordsReport','#semrush-keywords')
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'semrush',data,ctx)});

seo.load('social').extend('base')
	.addMethod('all','#social')
	.exec(url, function(data,ctx){fnLoadComplete.call(this,'social',data,ctx);});

</script>

<div class="container">

<div class="span-4">
    <div style="position:relative">
        <div id="info-index-wrap">
            <h1>Contents</h1>
            <ul id="info-index" >
                <li>
                    <a href="#info-server">
                    <div class="info-icon">
                        <img src="/themes/reports/images/server.png" />
                        <h3>Server Info.</h3>
                    </div>
                    </a>
                </li>
                <li>
                    <a href="#info-head">
                        <div class="info-icon">
                            <img src="/themes/reports/images/head.png" />
                            <h3>Page Head</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#info-body">
                        <div class="info-icon">
                            <img src="/themes/reports/images/body.png" />
                            <h3>Page Body</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#info-w3c">
                    <div class="info-icon">
                        <img src="/themes/reports/images/w3c.png" />
                        <h3>W3C Validation</h3>
                    </div>
                    </a>
                </li>
                <li>
                    <a href="#info-social">
                    <div class="info-icon">
                        <img src="/themes/reports/images/social.png" />
                        <h3>Social Stats</h3>
                    </div>
                    </a>
                </li>
                <li>
                    <a href="#info-google">
                        <div class="info-icon">
                            <img src="/themes/reports/images/google.png" />
                            <h3>Google Stats</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#info-moz">
                    <div class="info-icon">
                        <img src="/themes/reports/images/moz.png" />
                        <h3>MOZ Stats</h3>
                    </div>
                    </a>
                </li>
                <li>
                    <a href="#info-semrush">
                    <div class="info-icon">
                        <img src="/themes/reports/images/semrush.png" />
                        <h3>SEM Rush Stats</h3>
                    </div>
                    </a>
                </li>
                <li>
                    <a href="#info-keywords">
                    <div class="info-icon">
                        <img src="/themes/reports/images/words2.png" />
                        <h3>Key Words <br><span style="font-size:10px">(extended)</span></h3>
                    </div>
                    </a>
                </li>
            </ul>

            <div id="save-edit-wrap">
                <input id="save" style="width:130px" class="btn btn-large" type="button" value="Save" />
            </div>
        </div>
    </div>
    &nbsp;&nbsp;&nbsp;&nbsp;
</div>

<div class="span-19 last">

<h1 id="top">SEO Report - <?php echo $_GET['target']; ?></h1>


<div id="all-content">

<script src="http://<?php echo SEO_HOST . '/' . SEO_URI_REPORTS_LOCAL; ?>js/basic.js"></script>

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
$filename = str_replace('/','-',preg_replace('@https?://@i','',$_GET['target'])) . '.html';
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
                /*
                $($list).animate({
                    top: target.offset().top - posTop
                }, 1000);
                */
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
})
</script>
