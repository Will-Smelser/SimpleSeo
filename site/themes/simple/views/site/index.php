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
$cs->registerScriptFile('/themes/reports/js/api/libs/aes.js');


//set canonical url
$cs->registerLinkTag('canonical',null,Yii::app()->createAbsoluteUrl('/site/index'));

//got to get a valid token
require_once SEO_PATH_HELPERS . 'ClientHash.php';
$token = 'TOKEN_GET_FAILED';
$userToken = 'TOKEN_GET_FAILED';
try{
	$token = \api\clients\ClientHash::getToken(Yii::app()->params['apiKeySample'],'sample',SEO_HOST,$_SERVER['REMOTE_ADDR'],true);
    $userToken = \api\clients\ClientHash::getToken(Yii::app()->params['apiKeySample'],'sample',SEO_HOST,$_SERVER['REMOTE_ADDR'],false);
}catch(Exception $e){
	//do nothing, just everything will fail.
}

require_once(Yii::getPathOfAlias('ext.captcha')).'/captcha.php';
$captcha = new SimpleCaptcha();
$captchaInfo = $captcha->CreateImage();

require_once(Yii::getPathOfAlias('ext.crypto-aes')).'/aes.php';
$cipher = AesCtr::encrypt($userToken, $captchaInfo[1], 256);

$example = 'http://en.wikipedia.org/wiki/Search_engine_optimization';

$this->pageTitle='Simple Seo Api - Reporting Tool';

?>

<style>
    div.dashIcon.span-3{
        width:110px;
    }
</style>

<div class="blue-bg">
	<div class="bg-fade-up">
		<div class="container">

			<h1 style="padding-top: 20px;">A Reporting Tool for Search
				Engine Optimization</h1>
			<div class="span-23 showgrid last form">
				<input id="get-url"
					type="text" value="<?php echo $example;?>"
					style="width: 450px" />
				<button id="get-btn" class="btn btn-black">Get Word Count</button>
			</div>

			<div id="loadingWrapper" style="position:relative;" >
<?php
    $this->widget('ext.wloading.Wloading',
        array(
            'wrapperClass'=>'span-23',
            'wrapperStyle'=>'position:absolute;top:140px;',
            'width'=>'500px'
        )
    );
?>
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

<div id="dialog" style="display:none">
    <div class="form">
        <h3>Are you a robot?</h3>
        <div class="row">
            <input style="float:left" id="cipherKey"  type="text" />
            <img  style="position:absolute;top:30px;" src="data:image/png;base64,<?php echo $captchaInfo[0]; ?>"/>
        </div>
    </div>
</div>

<script type="text/javascript">

var token = "<?php echo $token; ?>";
var cipherTxt = "<?php echo $cipher;?>";

google.load('visualization', '1.0', {'packages':['corechart']});

$(document).ready(function(){



    var seo = new SeoApi('/themes/reports/js/api/charts/','/api/',token);
    seo.load('base');

    var seoBody = seo.load('body').extend('base')
        .addMethod('getKeyWords','#graphWrapper');


    //dialog
    $('#dialog').dialog({
        title:'Sending Request',
        resizable: false,
        height:200,
        width:450,
        autoOpen:false,
        modal: true,
        buttons:{
            'I am Human': function() {
                //we have to decode the encrypted token
                token = Aes.Ctr.decrypt(cipherTxt,$('#cipherKey').val(), 256);
                seo = new SeoApi('/themes/reports/js/api/charts/','/api/',token);
                seo.load('base');
                seoBody = seo.load('body').extend('base')
                    .addMethod('getKeyWords','#graphWrapper');

                $('#get-btn').click();
                $( this ).dialog( "close" );
            }
        }
    });


	SyntaxHighlighter.all();

	var clickNumber = 0;
	$('#get-btn').click(function(){

        if(clickNumber++ === 1){
            $('#dialog').dialog('open');
            return;
        }

        $('#loadingWrapper').show();
        seoBody.exec($('#get-url').val(),
            function(data,ctx){
				$('#loadingWrapper').fadeOut();
				this.handleSuccess(data,ctx);
			},function(error,ctx){
				this.handleError(error,ctx);
				$('#loadingWrapper').fadeOut();
			}
        );
	}).click();
	
});
</script>
