(function(){
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	
	/**
	 * This is the base class that handles making requests to the api.  Intended to be
	 * a base class for specific api controller rendering objects.  See head.js for
	 * an example of an object which is intended to extend this base class.
	 * 
	 * @namespace {Object} base
	 * @memberof! window._SeoApi_
	 */
	window[namespace].base = {
	
	/**
	 * Url that api requests are being made for.  The url
	 * that report data is being generated for.
	 * 
	 * @type {string}
	 * @memberof! window._SeoApi_.base
	 */
	url : '',
	
	/**
	 * The api url where request should be made.
	 * 
	 * @type {string}
	 * @memberof! window._SeoApi_.base
	 */
	api : '',

	
	dependencies : [],
	
	/**
	 * Once the data is load ensure body is loaded before calling callbacks
	 * @type {boolean}
	 * @memberof! window._SeoApi_.base
	 */
	waitOnLoad : true,

    /**
     * Store state from execute() call for retry attempts.
     * @param {function} callback The success callback for api request completion.  Passed in from execute() function.
     * @param {function} callbackErr The error callback for api request failure.  Passed in from execute() function.
     * @returns {{methods: Array, targetMap: (extend|*), callback: *, callbackErr: *, maxRetries: number, errorRetries: Array, requestRetries: number}}
     * @constructor
     * @memberof! window._SeoApi_.base
     */
    Context : function(callback, callbackErr){
        var scope = this;
        return {
            "methods" : scope.methods.slice(0),//create copy
            "targetMap" : $.extend({},scope.targetMap),//copy the dom targets
            "callback" : callback,
            "callbackErr" : callbackErr,
            "maxRetries" : 5,
            "errorRetries" : [],
            "requestRetries" : 0,
            "methodAll":scope.methodAll
        }
    },
	
	/** 
	 * The speciall "all" method was given.
	 * @type {boolean}
	 * @memberof! window._SeoApi_.base
	 */
	methodAll : false,
	
	/**
	 * All methods, exluding "all", which requests will be made for.
	 */
	methods : [],
	targetMap : [],
	
	
	failObj : (function(){return $("<div class='seo-fail'><span>Failed</span><div class='reason'></div></div>");})(),

    /**
     * Build a request url with appropriate parameters set
     * @param url
     * @param methodList
     * @returns {string}
     */
	buildRequest : function(url,methodList){
        if(!this.checkApi())throw "No api url set.";

        var method = null;
        if(typeof methodList == 'object'){
            method = methodList.join('|');
        }else{
            method = (this.methodAll) ? 'all' : this.methods.join('|');
        }
        console.log('buildRequest',method);
		return this.api+this.apiController+'/'+method+'?request='+url+'&type=jsonp';
	},
	checkApi : function(){
		if(this.api == ''){
			console.log('local api variable has not been set.');
			return false;
		}
		return true;
	},
	/**
	 * Add a method for request
	 * @param method The API method to call
	 * @param target Either dom object or function to callback
	 * @memberof! window._SeoApi_.base
	 */
	addMethod : function(method, target){
		this.methods.push(method);
		this.targetMap[method] = target;
		if(method === "all")
			this.methodAll = true;
	},

	setMethodToAll : function(){this.methodAll = true;},

    handleSuccess : function(data, ctx){
        if(typeof ctx == 'undefined' || ctx == null)
            throw "No context given.  See Context() method.";

		//empty content
		var targets = "";

        if(ctx.methodAll){
            console.log("LOOK",data);
            this.handleSuccessMethod('all', data, ctx.targetMap.all, ctx);
        }else{
            for(var method in data){
                console.log("handleSuccess, looking at method:"+method,ctx);
                //function given as target
                if(typeof ctx.targetMap[method] == "function"){
                    ctx.targetMap[method](data[method]);

                //dom target given for method
                }else{
                    //clear the contents
                    if(targets.indexOf(ctx.targetMap[method]) < 0)
                        $(ctx.targetMap[method]).html("");

                    targets += ctx.targetMap[method];

                    //render this
                    this.handleSuccessMethod(method, data[method], ctx.targetMap[method], ctx);
                }
            }
        }
		
		//clear everything
		this.targetMap = [];
		this.methods = [];
		this.methodAll = false;
	},

    /**
     * Handle success for a given method.  This means
     * that the execute callback for success has been
     * triggered.  However, errors can still occur at the method
     * level and are dealt with here.
     * @param {string} method The api request method.
     * TODO: what if the method is special case "all"
     * @param {JSON} data The data returned from API.
     * @param {string} target The target given from addMethod invocation.
     * This can be a function callback or string in '#<domId>' format
     * where <domId> is the target dome elements id attribute.
     */
	handleSuccessMethod : function(method, data, target, ctx){
        //at this point context could have changed, we are now
        //in a specific method, so we need to change ctx
        var newCtx = this.Context(ctx.callback,ctx.callbackErr);
        newCtx.methods = [method];

        if(typeof ctx.errorRetries[method] == 'undefined')
            newCtx.errorRetries[method] = 0;
        else
            newCtx.errorRetries[method] = ctx.errorRetries[method];

        //we may want to re-attempt
        if(data.error && newCtx.errorRetries[method] < newCtx.maxRetries){
            newCtx.errorRetries[method]++;
            var req = this.buildRequest(this.url,[method]);
            $(target).html('Error. Retrying...');
            return this.makeRequest(req,newCtx);
        }

        var temp = (data.data == null)?[]:data.data;
        var goodMethod = (data.error)?"renderErr_":"render_";
        var defaultMethod = (data.error)?"defaultRenderErr":"defaultRender";

        console.log("Success!!",typeof this[goodMethod+method],goodMethod+method,defaultMethod,target,ctx,newCtx);

        if(typeof this[goodMethod+method] == "function"){
            console.log('About to execute: '+goodMethod+method,this,target);
            this[goodMethod+method](temp, $(target));
        }else{
            this[defaultMethod](data, $(target));
        }
	},
	handleError : function(ctx){
		for(var x in ctx.methods){
			$(ctx.targetMap[ctx.methods[x]]).html(this.failObj.find('.reason').html('Ajax Request Failure'));
		}
	},
    /**
     * Default error renderer called when a method
     * api request failed.
     * @param data
     * @param $target
     */
    defaultRenderErr : function(data, $target){
        $target.html("Failed ("+data.msg+")");
    },
    /**
     * The default render if no method_<api method> function exists.
     * Uses prettyPrint.js which is more designed for diagnostics.
     * @param data
     * @param $target
     */
	defaultRender : function(data, $target){
		console.log('loading pretty print',data);
		var rUrl = '/themes/reports/js/api/prettyPrint.js';
		$.ajax({
		  url: rUrl,
		  dataType: "script",
		  cache: true,
		  success: function(){
			  console.log('About to set html content of target',$target,data,$target.html());
			  $target.html(prettyPrint(data));
		  }
		});
	},
	
	/**
	 * Make request to api for url for all methods given in the {@link addMethod} call(s).
	 * @param {string} url The url to collect SEO data on.
	 * @param {function} callback A callback function to run once request is complete.
	 * @param {function} errCallback A callback function to run once request is complete 
	 * and an error was detected
	 * @memberof! window._SeoApi_.base
	 */
	execute : function(url, callback, errCallback){
		this.url = url;
		var req = this.buildRequest(url);
		
		//make sure we have callbacks defined
		if(typeof callback != "function")
			callback = this.handleSuccess;
		
		if(typeof errCallback != "function")
			errCallback = this.handleError;

        //build the context
        var ctx = this.Context(callback, errCallback);
        this.makeRequest(req, ctx);
	},

    /**
     * Make the actual AJAX request to api.
     * @param {string} req  The url to make ajax request against.
     * @param {Context} ctx The context to make requests within.
     */
    makeRequest : function(req, ctx){
        var scope = this;
        //get the data, crossdomain using jsonp
        $.ajax({
            'url':req,
            'dataType':'jsonp',
            'success':function(data){
                console.log('Initialize request success');

                var resp = (ctx.methodAll) ? data : data.data;

                if(scope.waitOnLoad){
                    $(document).ready(function(){
                        ctx.callback.call(scope, resp, ctx);
                    });
                }else{
                    ctx.callback.call(scope, resp, ctx);
                }
            },
            'error':function(){
                if(ctx.errorRetries < ctx.maxRetries){
                    ctx.errorRetries++;
                    return scope.makeRequest(req, ctx);
                }
                if(scope.waitOnLoad){
                    $(document).ready(function(){
                        ctx.errCallback.call(scope, ctx);
                    });
                }else{
                    ctx.errCallback.call(scope, ctx);
                }
            }
        });
    },

	/**
	 * Overwrite all the below with your own handler and controller.
	 */
	
	/**
	 * Which controller the API should use.  This should be
	 * overwritten by extending this object.
	 * @type {string}
	 * @memberof! window._SeoApi_.base
	 */
	apiController : 'controller',
	
	/**
	 * This class is intended to be extended.  So just extend
	 * this class with $.extend and overwrite your api method
	 * call with the following format:<br/>
	 * <code>
	 * $.extend(SeoApiBase, {render_apiMethod:function(data,$target){
	 * 		//do some stuff with data
	 *      //$target.html('Request done');
	 * }});
	 * </code>
	 * 
	 * If you do not set a render for a given api method, then the
	 * default handler will be used.  This handler uses prettyPrint.js
	 * http://james.padolsey.com/javascript/prettyprint-for-javascript/.
	 * 
	 * This is more like a diagnostic.
	 * @param {JSON} data The api returned json object
	 * @param {Object} $target The jquery target initially given to this api request.
	 * @memberof! window._SeoApi_.base
	 */
	render_apiMethod : function(data, $target){},

    /**
     * This class is intended to be extended.  So just extend
     * this class with $.extend and overwrite your api method
     * call with the following format:<br/>
     * <code>
     * $.extend(SeoApiBase, {render_apiMethod:function(data,$target){
     * 		//do some stuff with data
     *      //$target.html('Request done');
     * }});
     * </code>
     *
     * If you do not set a render for a given api method, then the
     * default handler will be used.  This handler uses prettyPrint.js
     * http://james.padolsey.com/javascript/prettyprint-for-javascript/.
     *
     * This is more like a diagnostic.
     * @param {JSON} data The api returned json object
     * @param {Object} $target The jquery target initially given to this api request.
     * @memberof! window._SeoApi_.base
     */
    renderErr_apiMethod : function(data, $target){}
};
})();