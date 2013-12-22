
<div class="container">

    <div class="span-4">
        <?php //echo $this->renderPartial('../partials/menu'); ?>
    </div>


    <div class="span-19 last">

        <h1 id="top">SEO Report - <?php echo $_GET['target']; ?></h1>

        <div id="all-content">

            <!-- api/server -->
            <h2 id="info-server" class="first">Server Information </h2>
            <!--
                api/server/
                    getHeaderResponseLine, getHeaderField, getServer, getServer, isGzip, getLoadTime, getWhois
            -->
            <div id="server" class="title-info subinfo">
                <h4>General Info</h4>
                <div id="server-general-info">
                    <?php echo $this->renderPartial('/partials/server/general',
                        array(
                            'data'=>$data['Server'],
                            'methods'=>array('getLoadTime','isGzip','getServer','getHeaderResponseLine','checkRobots')
                        )
                    ); ?>
                </div>

                <h4>Domain Information</h4>
                <div id="server-whois" class="loading-text">
                    <?php echo $this->renderPartial('/partials/server/general',
                        array(
                            'data'=>$data['Server'],
                            'methods'=>array('getWhois')
                        )
                    );
                    ?>
                </div>
            </div>
            <!-- api/head -->
            <h2 id="info-head">HTML Head Information </h2>

            <div id="head-info" class="loading-text title-info">

            </div>

            <!-- api/body -->
            <h2 id="info-body">HTML Body Information </h2>
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

            <h2 id="info-w3c">W3C Validation </h2>
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

            <h2 id="info-social">Social Stats </h2>
            <div id="social" class="loading-text title-info subinfo"></div>

            <h2 id="info-google">Google Stats </h2>
            <div id="google" class="title-info subinfo">
                <h4>Page Rank: <b id="google-pr" class="loading-text"></b></h4>

                <h4>Back Links</h4>
                <div id="google-backlinks" class="loading-text"></div>
            </div>

            <h2 id="info-moz">SEO Moz Stats </h2>
            <div id="moz" class="title-info subinfo">
                <h4>Moz General Information</h4>
                <div id="moz-link" class="loading-text"></div>

                <h4>Moz Just Discovered Backlinks</h4>
                <div id="moz-disc" class="loading-text"></div>
            </div>

            <h2 id="info-semrush">SEMrush Stats </h2>
            <div id="semrush" class="title-info subinfo">
                <h4>Domain Data</h4>
                <div id="semrush-domain" class="loading-text"></div>

                <h4>Domain Keyword Data</h4>
                <div id="semrush-keywords" class="loading-text"></div>
            </div>

            <h2 id="info-keywords">Keywords (Extended) </h2>
            <div id="wordsext" class="title-info subinfo">
                <h4>Contains phrases using listed key words</h4>
                <div id="body-keywords2" class="loading-text"></div>
            </div>

        </div>


</div>

</div>