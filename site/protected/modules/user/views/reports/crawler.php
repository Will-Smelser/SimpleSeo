<?php
//jquery ui
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
$cs->registerCssFile('/themes/reports/css/custom-theme/jquery-ui-1.10.3.custom.css');
$cs->registerScriptFile('/themes/reports/js/jquery-ui-1.10.3.custom.min.js');

$cs->registerCssFile('/themes/simple/css/uiselect.css');
$cs->registerScriptFile('/themes/simple/js/jquery.uiselect.min.js');

//$cs->registerCssFile('/themes/simple/css/form.css');
//$cs->registerCssFile('/themes/simple/css/buttons.css');

/* @var $this ReportsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    'Reports',
);

/*
$this->menu=array(
	array('label'=>'Create Reportdata', 'url'=>array('create')),
	array('label'=>'Manage Reportdata', 'url'=>array('admin')),
);
*/
?>

<style>
#loadingTxt{
    font-size:12px;
}
</style>

<h1>Web Crawler</h1>
<h2>About the Crawler</h2>
<p>
    The web crawler will crawl the links of your website from  a given starting point.  In most cases,
    just set this to your landing page.  For example, http://www.google.com, is an example of a
    starting page.
</p>
<div id="crawl-settings">
<h2>Crawler Settings</h2>
<p>
Enter URL entry point you would like the crawler to access your site from.
</p>
<div class="form">
    <form id="crawl-form">
    <div class="row">
	    <label for="url" >Starting URL</label>
	    <input class="" name="url" type="text" id="url" style="width:400px" />
	</div>

    <div class="row">
        <label for="depth" >Depth</label>
        <select class="" name="depth" type="text" id="depth" >
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3" selected>3</option>
            <option value="3">4</option>
            <option value="3">5</option>
        </select>
    </div>

    <div class="row">
        <label for="maxLinks" >Max Links</label>
        <select class="" name="maxLinks" type="select" id="maxLinks">
            <option value="25">25</option>
            <option value="50" selected>50</option>
            <option value="75">75</option>
            <option value="100">100</option>
        </select>
    </div>

    <div class="row">
        <label for="nofollow" style="display:inline-block;">Obey "nofollow" links?&nbsp;&nbsp;</label>
        <input class="" name="nofollow" value="true" type="checkbox" id="nofollow" checked/>
    </div>
    </form>

    <button id="run-crawl" class="btn" >Crawl Links</button>
</div>
</div>

<div id="loadingWrapper" style="display:none;">
    <div>
    <p>This can take several minutes.  Please be patient.</p>
    <p><small>Time to crawl is a factor of download speed of your site relative to this
        web server, depth of crawl, and number of links.  Every resolved link must be downloaded.
        </small></p>
    </div>
    <div>
    <?php
    $this->widget('ext.wloading.Wloading',
        array(
            'wrapperClass'=>'',
            'wrapperStyle'=>'position:relative;',
            'width'=>'100%'
        )
    );
    ?>
    </div>
</div>
<div id="crawl-results" style="display:none">
<h2>Crawl Results</h2>
<div>
    <div>
        <span class="span-5">Total Found Links:</span><span id="crawl-setting-total"></span>
    </div>
    <div>
        <span class="span-5">Starting URL:</span><span id="crawl-setting-start"></span>
    </div>
    <div>
        <span class="span-5">Depth:</span><span id="crawl-setting-depth"></span>
    </div>
    <div>
        <span class="span-5">Max Links:</span><span id="crawl-setting-links"></span>
    </div>
    <div>
        <span class="span-5">Obey No Follow Links:</span><span id="crawl-setting-nofollow"></span>
    </div>

    <div>
        Check: <a id="input-all" href="#">all</a> | <a href="#" id="input-none">none</a>
    </div>
    <hr/>
    <form id="crawl-results-links"></form>
</div>
</div>

<script>
    $(document).ready(function(){

        $('#input-all').click(function(){
            $('#crawl-results input').each(function(){
                document.getElementById($(this).attr('id')).checked=true;
            });
        });
        $('#input-none').click(function(){
            $('#crawl-results input').removeAttr("checked");
        });

        var allowClose = false;
        $('select').uiselect();

        $('#loadingWrapper')
            .dialog({title:'Crawling Site',width:'500px',modal:true,autoOpen:false,
                beforeClose:function(){
                    if(!allowClose){
                        alert('A lot of resources are allocated during a crawl.  Please be patient...');
                        return false;
                    }
                }
            });

        $('#run-crawl').click(function(){
            allowClose = false;
            $('#loadingWrapper').dialog('open');

            $.ajax({
                dataType: "json",
                url: '/user/reports/docrawl',
                data: $('#crawl-form').serialize(),
                error:function(jqXhr,status,error){
                    alert("Error, please try again.\n\nCode: "+$status+"\nMessage: "+error);
                    $('#loadingWrapper').dialog('close');
                },
                complete:function(){allowClose=true;},
                success: function(data){
                    allowClose = true;

                    $('#crawl-setting-total').html(data.length);
                    $('#crawl-setting-start').html($('#url').val());
                    $('#crawl-setting-depth').html($('#depth').val());
                    $('#crawl-setting-links').html($('#maxLinks').val());
                    var nofollow = (document.getElementById('nofollow').checked)?"True":"False";
                    $('#crawl-setting-nofollow').html(nofollow);

                    for(var x in data){
                        var name = 'link-'+x;
                        var $input = $(document.createElement('input'))
                            .attr('type','checkbox')
                            .attr('id',name).attr('name',name)
                            .attr('value',data[x])
                            .attr('checked',true);
                        var $label = $(document.createElement('label'))
                            .attr('for',name).append($input).append('&nbsp;&nbsp;'+data[x]);
                        var $wrap = $(document.createElement('div'))
                            .append($label);
                        $('#crawl-results-links').append($wrap);
                    }

                    $('#crawl-settings').slideUp(function(){
                        $('#crawl-results').slideDown(function(){
                            $('#loadingWrapper').dialog('close');
                        });
                    });


                }
            });

            return false;
        });
    });
</script>