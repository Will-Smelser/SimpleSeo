<?php
require_once(Yii::getPathOfAlias('ext.seo').'/config.php');

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
.ui-progressbar {
    position: relative;
}
#progress-label {
    position: absolute;
    margin-top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
    width:100%;
    text-align: center;
}
.myicon{
    margin-left:4px;
    padding-left:18px;
    background-repeat:no-repeat;
}
.myicon.good{
    background-image:url("/themes/simple/images/small_icons/accept.png");
}
.myicon.bad{
    background-image:url("/themes/simple/images/small_icons/exclamation.png");
}
.myicon.warn{
    background-image:url("/themes/simple/images/small_icons/error.png");
}
.myicon.wait{
    background-image:url("/themes/simple/images/small_icons/hourglass.png");
}
</style>

<h1>Web Crawler for Reports</h1>
<h2>About the Crawler</h2>
<p>
    The web crawler will crawl the links of your website from  a given starting point.  In most cases,
    just set this to your landing page.  For example, http://www.google.com, is an example of a
    starting page.
</p>
<p>
    Once the crawler has created a list of URLs you will be able to choose which URLs you would
    like to run reports on.
</p>
<div id="crawl-settings">
<h2>Crawler Settings</h2>
<p>
Enter URL entry point you would like the crawler to access your site from.
</p>
<div class="form">
    <div id="error-summary" class="errorSummary" style="display:none"></div>
    <form id="crawl-form">
    <div class="row">
	    <label for="url" class="required" >Starting URL <span class="required">*</span></label>
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
        <label title="Links with rel='nofollow' attribute" for="nofollow" style="display:inline-block;">Obey "nofollow" links?&nbsp;&nbsp;</label>
        <input class="" name="nofollow" value="true" type="checkbox" id="nofollow" checked/>
    </div>
    </form>

    <button id="run-crawl" class="btn" >Crawl Links</button>
</div>
</div>

<div id="crawl-results" style="display:none">
<h2>Crawl Results</h2>
<div>
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
        <span class="span-5">Total Found Links:</span><span id="crawl-setting-total"></span>
    </div>
    <div>
        <span class="span-5">Obey No Follow Links:</span><span id="crawl-setting-nofollow"></span>
    </div>

    <div style="padding-top:20px;">
        Check: <a id="input-all" style="cursor:pointer">all</a> | <a style="cursor:pointer" id="input-none">none</a>
    </div>
    <hr/>
    <form id="crawl-results-links"></form>
    <br/>
    <button id="run-reports" class="btn" >Run Reports</button>
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

<div id="runningWrapper" style="display:none;">
    <div>
        <p>This can take quite some time.  Please do not refresh or leave this page until
                the reports have finished running.  Leaving this page before completed will
            stop the execution of your reports.
            </p>
    </div>
    <p>
        <div id="progressbar"><div id="progress-label">Loading...</div></div>
    </p>
    <div id="report-complete-message" style="display:none">
        <p>Reports have completed!  You can view your reports in your "Reports" page under
            the user interface or click <a href="/user/reports">here</a>.</p>
        <p><a class="btn" href="/user/reports">&nbsp;&nbsp;View Reports&nbsp;&nbsp;</a></p>
        <p>
            <div style="max-height:100px;overflow:auto" class="grid-view">
            <table id="report-stats" class="items"></table>
            </div>
        </p>
    </div>
</div>

<div id="waitOnRunningWrapper" style="display:none">
    <p>Current running reports cannot be cancelled.  Please wait till
    these are completed.</p>
    <p>Currently running reports:
    <ul id="runningReportsList"></ul>
    </p>
</div>

<script>
    $('#waitOnRunningWrapper').dialog(
        {title:'Reports Running',width:'500px',modal:true,autoOpen:false,draggable:false,
        buttons: [ { text: "Ok", click: function() {
            $(this).dialog('close');
        } } ]
    });
    var Report = {
        threads : 3, //this really just number of parellel ajax calls to make
        links : null,
        running : {},
        addStatsRow : function(url,msg){
            //remove the no errors row
            $('#report-stats-failures').remove();

            $tr = $(document.createElement("tr"));
            $td1 = $(document.createElement("td"));
            $td2 = $(document.createElement("td"));

            $td2.html(msg);
            $td1.html(url);
            $tr.append($td1).append($td2);
            $('#report-stats').append($tr);

            $('#runningWrapper').dialog({position:{ my: "center", at: "center", of: window }});
        },
        isRunning : function(){
            for(var x in this.running)
                if(this.running[x]) return true;
            return false;
        },
        getCheckedLinks : function(){
            return $('#crawl-results-links input:checked');
        },
        $progressBar : null,
        $progressLabel : null,
        allowClose : false,
        init : function(){
            this.curIndex = 0;
            this.ajaxReqs = [];
            this.running = {};
            this.allowClose = false;
            this.ajaxCancelled = false;
            $('#report-complete-message').hide();
            $('.myicon').removeClass('good').removeClass('bad').removeClass('warn').removeClass('wait');

            //cleanup error stats
            var str = '<thead><tr style="color:#FFF;"><th style="border-right:solid #FFF 1px">URL</th><th>Message</th></tr><tr id="report-stats-failures"><td colspan="2">No Failures</td></tr></thead>';
            $('#report-stats').html(str);


            var obj = this;
            obj.links = this.getCheckedLinks();

            console.log(this.curIndex,this.links.length);

            obj.$progressLabel = $('#progress-label');
            obj.$progressBar = $('#progressbar').progressbar({
                value: 0,
                complete: function() {
                    obj.$progressLabel.text( "Complete!" );
                }
            });

            $('#runningWrapper')
                .dialog({title:'Running Reports',width:'500px',modal:true,autoOpen:true,draggable:false,
                    beforeClose:function(){
                        if(!obj.allowClose){
                            alert('A lot of resources are allocated during report generation.  Please be patient...');
                            return false;
                        }
                    },
                    buttons: [ { text: "Cancel", click: function() {
                        //Report.cancelAjax();
                        Report.ajaxCancelled = true;

                        var $list = $('#runningReportsList').empty();
                        for(var x  in obj.running){
                            if(obj.running[x])
                                $list.append($(document.createElement('li')).html(x));
                        }

                        $('#waitOnRunningWrapper').dialog('open');
                        Report.waitOnNotRunning(function(){
                            Report.allowClose = true;
                            $('#runningWrapper').dialog('close');
                            $('#waitOnRunningWrapper').dialog('close');
                        });
                    } } ]
                });

            obj.updateLoading();
        },
        waitOnNotRunning : function(callback){
            var scope = this;
            if(scope.isRunning()){
                setTimeout(function(){scope.waitOnNotRunning.call(scope,callback)},100);
                return;
            }
            callback();
        },
        updateLoading : function(){
            var total = 0;
            for(var x in this.running)
                if(!this.running[x]) total++;

            this.$progressLabel.text( total + " of " + this.links.length + " completed" );
            this.$progressBar.progressbar("value",Math.floor((total/this.links.length)*100));

            //completed
            if(total >= this.links.length){
                this.allowClose = true;
                this.$progressLabel.text( "Completed!" );
                $('#report-complete-message').slideDown();
            //still waiting
            }else{
                console.log('Loading '+total+' of '+this.links.length);
            }
        },
        cancelAjax : function(){
            this.ajaxCancelled = true;
            for(var x in this.ajaxReqs)
                this.ajaxReqs[x].abort();
        },
        ajaxCancelled : false,
        ajaxReqs : [],
        ajax : function(link){
            var $link = $(link);
            var url = $link.val();
            this.running[url] = true;
            var scope = this;

            this.curIndex++;

            console.log("Ajax request on "+url);


            $link.parent().find('span').addClass('wait');

            this.ajaxReqs.push($.ajax({
                dataType: "json",
                url: '/user/reports/processReport',
                data: {"url":url},
                error:function(jqXhr,status,error){
                    //alert("Error, please try again.\n\nCode: "+status+"\nMessage: "+error);
                    scope.addStatsRow(url,error);
                    $link.addClass('error');
                    $link.parent().find('span').removeClass('wait').addClass('bad');
                },
                complete:function(){
                    scope.running[url] = false;
                    scope.updateLoading();
                    if(scope.curIndex < scope.links.length && !scope.ajaxCancelled){
                        scope.ajax(scope.links[scope.curIndex]);
                        return;
                    }
                    //$('#runningWrapper').dialog({position:{ my: "center", at: "center", of: window }});
                },
                success: function(data){
                    if(!data.result){
                        scope.addStatsRow(url,"Report failed.");
                        $link.parent().find('span').removeClass('wait').addClass('warn');
                        $link.addClass('warn');
                    }else{
                        $link.parent().find('span').removeClass('wait').addClass('good');
                    }
                }
            }));
        },
        curIndex : 0,
        runReports : function(){

            this.updateLoading();

            for(var i=0; i<this.threads && i < this.links.length; i++){
                this.ajax(this.links[i]);
            }
        }
    };
    $(document).ready(function(){
        $('#run-reports').click(function(){
            Report.init();
            Report.runReports();
        });

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
            //check the url
            var url = $('#url').val();

            var pattern = /https?:\/\/[^ "]+$/;
            if(!pattern.test(url)){
                $('#url').addClass("error");
                $('#error-summary')
                    .html("Please enter a valid URL.<br/>For example, http://www.google.com")
                    .slideDown();
                return;
            }else{
                $('#error-summary').slideUp();
                $('#url').removeClass("error")
            }

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
                        var $icon = $(document.createElement('span'))
                            .addClass("myicon");
                        var $label = $(document.createElement('label'))
                            .attr('for',name).append($input).append($icon)
                            .append('&nbsp;&nbsp;'+data[x]);
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