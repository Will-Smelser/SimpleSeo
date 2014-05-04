<?php

$baseUrl = Yii::app()->theme->baseUrl;
$cs = Yii::app()->getClientScript();

//jquery and google scripts
//$cs->registerScriptFile('http://www.google.com/jsapi');
$cs->registerScriptFile('/themes/reports/js/api/libs/aes.js');

$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerCssFile('/themes/simple/css/uiselect.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js',CClientScript::POS_END);
$cs->registerScriptFile('/themes/simple/js/jquery.uiselect.min.js',CClientScript::POS_END);

//api
$cs->registerScriptFile('/themes/reports/js/api/SeoApi.js',CClientScript::POS_HEAD,array('data-seoapi-ns'=>'_SeoApi_'));

//set canonical url
//$cs->registerLinkTag('canonical',null,Yii::app()->createAbsoluteUrl('/site/index'));

//got to get a valid token
require_once SEO_PATH_HELPERS . 'ClientHash.php';
$token = "TOKEN_GET_FAILED";

try{
    $token = \api\clients\ClientHash::getToken(Yii::app()->params['apiKeySample'],'sample',SEO_HOST,$_SERVER['REMOTE_ADDR']);
}catch(Exception $e){
    //do nothing, just everything will fail.
}

$this->pageTitle='Simple Seo Api - Api Play';

$example = 'http://en.wikipedia.org/wiki/Search_engine_optimization';

//build a list
require_once(Yii::getPathOfAlias('application.controllers').'/ApiController.php');

$api = get_class_methods('ApiController');
$defaults = get_class_methods('ApiBaseController');

$cleaned = array();
$methods = array();
foreach($api as $obj){
    if(!in_array($obj,$defaults)){
        $class = preg_replace('/^action/i','',$obj);

        try{
            require_once(Yii::getPathOfAlias('ext.seo.api').'/'.$class.'.php');

            array_push($cleaned,$class);
            $methods[$class] = array();

            $rclass = new ReflectionClass("\\api\\$class");
            foreach($rclass->getMethods(ReflectionMethod::IS_PUBLIC) as $cdata){
                if($cdata->name!=='__construct')
                    array_push($methods[$class],$cdata->name);
            }
        }catch (Exception $e){
            if(!isset($methods[$class])){
                $methods[$class.' - Load Failed'] = array();
                array_push($methods[$class.' - Load Failed'], "Load Failed");
            }
        }

        if(isset($methods[$class]) && count($methods[$class]) == 0)
            array_push($methods[$class], "No Methods");
    }
}

require_once(Yii::getPathOfAlias('ext.captcha')).'/captcha.php';
$captcha = new SimpleCaptcha();
$captchaInfo = $captcha->CreateImage();

require_once(Yii::getPathOfAlias('ext.crypto-aes')).'/aes.php';
$cipher = AesCtr::encrypt($token, $captchaInfo[1], 256);

?>
<style>
    fieldset{border:none;}
    pre {
        background-color: ghostwhite;
        border: 1px solid silver;
        padding: 10px 20px;
        margin: 10px 0px 0px 0px;
        overflow-x: auto;
    }
    .json-key {
        color: brown;
    }
    .json-value {
        color: navy;
    }
    .json-string {
        color: olive;
    }
</style>
<h1>Play with the SEO API</h1>

<p>
    Fill out the form below to make API requests.
</p>

<p>
    <b>NOTE:</b> The methods are derived from the underlying PHP classes
    and are <b>not</b> all accessible via the API.  These requests will result in "400 Bad Request".
</p>

<div class="form">
    <div class="row">
        <label>API Class</label>
        <select id="classSelector">
            <option value="methodDefault">Select API Class</option>
            <?php foreach($methods as $class=>$obj) echo "<option value='$class'>$class</option>"; ?>
        </select>
    </div>
    <div class="row">
        <label>API Method</label>
        <div id="methodWrapper">
            <div id="methodDefault">
                <select><option value="0">Choose API Class</option></select>
            </div>
            <?php
            foreach($methods as $class=>$obj){
                echo "<div id='$class' style='display:none'>";
                echo "<select>\n";
                foreach($obj as $method)
                    echo "\t<option>$method</option>\n";
                echo "</select>\n</div>\n";
            }
            ?>
        </div>
    </div>

    <div class="row">
        <label>Target URL</label>
        <input id="get-url"
               type="text" value="<?php echo $example; ?>"
               style="width: 450px" />

    </div>

    <div class="row">
        <fieldset style="position:relative;">
            <legend>Not a Robot?</legend>
            <label>Enter Text</label>
            <input style="float:left" id="cipherKey"  type="text" />
            <img  style="position:absolute;top:30px;" src="data:image/png;base64,<?php echo $captchaInfo[0]; ?>"/>
        </fieldset>
    </div>

    <button id="get-btn" class="btn btn-black">Make Request</button>


    <div id="jsonResult"></div>
</div>

<div style="clear:both"></div>

<div id="dialog" style="display:none" >
<?php
$this->widget('ext.wloading.Wloading',
    array(
        'wrapperClass'=>'',
        'wrapperStyle'=>'',
        'width'=>'300px',
        'text'=>''
    )
);
?>
</div>

<div id="dialogError" style="display:none">
    <div class="form">
    <h3>Please correct this error:</h3>
    <p  id="errorDetail" class="errorMessage"></p>
    </div>
</div>

<script>
$(document).ready(function(){
    //dropdown menu stuff
    $('select').uiselect();
    $('#classSelector').change(function(){
        var val = $('#classSelector :selected').val();
        $('#methodWrapper >div').hide();
        $('#'+val).show().find('select').uiselect("refresh");
        console.log(val);
    });

    //dialog
    $('#dialog').dialog({
        title:'Sending Request',
        resizable: false,
        height:140,
        width:450,
        autoOpen:false,
        modal: true
    });

    //dialog error
    $('#dialogError').dialog({
        title:'Errors with Form',
        resizable: false,
        height:200,
        width:450,
        autoOpen:false,
        modal: true,
        buttons:{
            Ok: function() {$( this ).dialog( "close" );}
        }
    });


    cipherTxt = "<?php echo $cipher; ?>";
    token = null;
    rawToken = "<?php echo $token; ?>";

    //http://jsfiddle.net/unLSJ/
    var library = {};
    library.json = {
        replacer: function(match, pIndent, pKey, pVal, pEnd) {
            var key = '<span class=json-key>';
            var val = '<span class=json-value>';
            var str = '<span class=json-string>';
            var r = pIndent || '';
            if (pKey)
                r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
            if (pVal)
                r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
            return r + (pEnd || '');
        },
        prettyPrint: function(obj) {
            var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
            return JSON.stringify(obj, null, 3)
                .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
                .replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(jsonLine, library.json.replacer);
        }
    };


    var firstCall = true;

    var seo = null;

    //make api request
    $('#get-btn').click(function(){

        var apiClass = $('#classSelector :selected').val();
        if(apiClass === 'methodDefault'){
            $('#errorDetail').html('No API class selected');
            $('#dialogError').dialog('open');
            return;
        }

        var apiMethod= $('#'+apiClass+' :selected').val();
        if(apiMethod === '0'){
            $('#errorDetail').html('No API method selected');
            $('#dialogError').dialog('open');
            return;
        }

        if($('#cipherKey').val().length == 0){
            $('#errorDetail').html('Please prove you are human by filling out the Captcha.');
            $('#dialogError').dialog('open');
            return;
        }

        $('#dialog').dialog('open');

        //we have to decode the encrypted token
        token = Aes.Ctr.decrypt(cipherTxt,$('#cipherKey').val(), 256);

        if(seo === null){
            seo = new SeoApi('/themes/reports/js/api/','/api/',token);
            seo.init('base');
            seo.init('render');
        }

        var printResults = function(text){
            $('#dialog').dialog('close');

            var $pre = $(document.createElement('pre')).css("display","none");
            $pre.html(text);

            var $wrap = $('#jsonResult');

            if(!firstCall)
                $wrap.prepend(document.createElement('hr'));

            $wrap.prepend($pre);
            $pre.slideDown();

            firstCall = false;
        };

        var seoLocal = seo.load(apiClass.toLowerCase()).extend('base')
            .addMethod(apiMethod).exec($('#get-url').val(),function(data){
                printResults(library.json.prettyPrint(data));
            },function(info, ctx){
                var text = "FAILED - "+info[0].status+" "+info[0].statusText;

                if(info[0].status === 401){
                    text += "\nInvalid Captcha Text or "
                    text += "Access token has expired.  Please refresh the page to gain a new token."
                }
                printResults(text);
            }
        );
    });
});
</script>