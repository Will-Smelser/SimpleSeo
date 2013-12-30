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

    /**
     * The dependencies this object has.
     * @type {array} An array of strings denoting each dependency, such as 'base' for base.js dependency.
     * @memberof! window._SeoApi_.base
     * @private
     */
	dependencies : [],
	
	/**
	 * Once the data is loaded ensure body is loaded before calling callbacks
	 * @type {boolean}
	 * @memberof! window._SeoApi_.base
	 */
	waitOnLoad : true,

    /**
     * As results from api come in they are stored here.  Use this.completed
     * object to determine when all requests are complete for this controller.
     * There is no global callback for everything being completed.
     */
    data : null,

    /**
     * A list of key=>value pairs where the key
     * is the method name from api and value is a boolean
     * for wether or not it has completed loading.  If multiple
     * attempts fail, this will be set to true on the last failure.
     */
    completed : {},

    /**
     * Merge the data, a response from api, to this.data
     * @param data
     * @private
     */
    _mergeData : function(data){
        if(this.data == null){
            this.data = data;
            for(var x in data.data)
                this.completed[x] = !data.data[x].error;
            if(this.completed)
            return;
        }

        for(var x in data.data){
            this.data.data[x] = data.data[x];
            this.completed[x] = !data.data[x].error;
        }
    },

    /**
     * Store state from execute() call for retry attempts.  Makes copies of local objects:
     * <ul><li>methods</li><li>targetMap</li><li>methodAll</li></ul>
     * @param {function} callback The success callback for api request completion.  Passed in from execute() function.
     * @param {function} callbackErr The error callback for api request failure.  Passed in from execute() function.
     * @returns {{methods: Array, targetMap: (extend|*), callback: *, callbackErr: *, maxRetries: number, errorRetries: Array, requestRetries: number}}
     * @constructor
     * @memberof! window._SeoApi_.base
     * @private
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
     * @private
	 */
	methodAll : false,
	
	/**
	 * All methods that need to be made for api call.
     * @memberof! window._SeoApi_.base
     * @private
     * @see this.Context()
	 */
	methods : [],

    /**
     * A map of method=&gt;dom object.  This holds what dom target to pass
     * to render_&lt;ApiMethod&gt; for a given api method.
     * @memberof! window._SeoApi_.base
     * @private
     */
	targetMap : [],

    /**
     * Builds the default DOM elements that are set when defaultRenderErr function which is called after an api
     * method call fails repeatedly.
     * @param msg The detailed message about the error.
     * @returns {*|jQuery|HTMLElement}
     * @memberof! window._SeoApi_.base
     */
	failObj : function(msg){
        var $obj = $("<div class='seo-fail'><span>Failed to load (</span><a style='font-size:90%'>more info</a>)<div class='reason' style='display:none'>"+msg+"</div></div>");
        $obj.find('a').click(function(){$obj.find('.reason').slideToggle();});
        return $obj;
    },

    /**
     * Creates the DOM element that is inserted by default when there was an error loading
     * an api method.
     * @returns {*|jQuery|HTMLElement}
     * @memberof! window._SeoApi_.base
     */
    retryObj : function(){
        return $("<div class='seo-retry'><span>Error,</span> <span>Retrying...</span></div>");
    },

    /**
     * Build a request url with appropriate parameters set
     * @param {string} url The url to make request against
     * @param {array} methodList An array of methods (strings)
     * @returns {object} An object with 2 attributes: "url" and "methods".
     * "url" => A string for api request
     * "methods" => An array of method names
     * @private
     * @memberof! window._SeoApi_.base
     */
	buildRequest : function(url,methodList){
        if(!this.checkApi())throw "No api url set.";

        var method = null;
        if(typeof methodList == 'object'){
            method = methodList.join('|');
        }else{
            method = (this.methodAll) ? 'all' : this.methods.join('|');
        }

		return {
            "url": this.api+this.apiController+'/'+method+'?request='+url+'&type=jsonp',
            "methods": method.split("|")
        };
	},

    /**
     * Check wether the api variable has been set.
     * @see this.api
     * @memberof! window._SeoApi_.base
     * @private
     * @returns {boolean}
     */
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
        else
            this.completed[method] = false;
	},

    handleSuccess : function(data, ctx){
        if(typeof ctx == 'undefined' || ctx == null)
            throw "No context given.  See Context() method.";

		//tracks dom targets which have been used by a method
		var targets = "";

        this._exMethodAll(data,ctx);
		
		//clear everything
		this.targetMap = [];
		this.methods = [];
		this.methodAll = false;
	},

    /**
     * Performs a re-attempt for a failed method request.
     * @param ctx {Context} The contect
     * @param method {string} The method that failed
     * @param target {object} The dom ID target or jquery DOM target
     * @private
     */
    _retry : function(ctx,method,target){
        ctx.errorRetries[method]++;
        var req = this.buildRequest(this.url,[method]);

        if(typeof target === "function")
            target(data);
        else
            $(target).html(this.retryObj());

        this.makeRequest(req,ctx);
    },

    /**
     * Decides on the render function to use and call it.  This will
     * render errors or success
     * @param ctx {Context} The context to call render from.
     * @param method {string} The method to render, I think "all" is supported
     * @param target {object} The dom ID target or jquery DOM target
     * @param data {object} The JSON object returned from api.
     * @private
     */
    _doRender : function(ctx,method,target,data){
        //different data layout for "all" method
        var temp = (data.data == null)?[]:data.data;

        var userMethod = (data.error)?"renderErr_":"render_";
        var defaultMethod = (data.error)?"defaultRenderErr":"defaultRender";

        //there is a defined render function
        if(typeof this[userMethod+method] == "function"){
            this[userMethod+method](temp, $(target), ctx);
            //no defined render function, call the default
        }else{
            this[defaultMethod](data, $(target), ctx);
        }
    },

    /**
     * Handle success for a given method.  This means
     * that the execute callback for success has been
     * triggered.  However, errors can still occur at the method
     * level and are dealt with here.<br/><br/>
     * If there was an error with the request then either
     * the error callback function will be triggered or the error
     * rendering function will be triggered.
     * @param {string} method The api request method.
     * @param {JSON} data The data returned from API.
     * @param {Context} The context object that execute() was called with.
     */
	handleSuccessMethod : function(method, data, ctx){
        var target = ctx.targetMap[method];

        //at this point context could have changed, we are now
        //in a specific method, so we need to change ctx
        var newCtx = this.Context(ctx.callback,ctx.callbackErr, ctx);
        newCtx.methods = [method];
        newCtx.targetMap[method] = ctx.targetMap[method];
        newCtx.errorRetries[method] = (typeof ctx.errorRetries[method] == 'undefined') ?
            0 : ctx.errorRetries[method];

        //we may want to re-attempt
        if(data.error && newCtx.errorRetries[method] < newCtx.maxRetries){
            this._retry(ctx,method,target);
        }else{
            this._doRender(ctx,method,target,data);
        }
	},

    /**
     * Build a response error string from XHR request.
     * @param err {jqXHR} The error object
     * @returns {string} Returns the jqXHR error information if it can, or a default message.
     * @private
     */
    _getAjaxErrMsg : function(err){
        var msg = "Ajax Request Failure.";
        if(typeof err[0] == "object"){
            try{
                msg = err[0].getResponseHeader('Message-Info');
            }catch(e){
                msg = "Ajax Request Failure.";
            }
        }
        return msg;
    },

    /**
     * An error handler for when api request failed.  This captures ajax request
     * errors, and not result errors.
     * @param {[jqXHR jqXHR, String textStatus, String errorThrown]} The ajax exception information from jquery/browser.
     * @param {Context} The context that execute() was called with.
     */
    handleError : function(err, ctx){
        if(typeof ctx == 'undefined' || ctx == null)
            throw "No context given.  See Context() method.";

        for(var x in ctx.methods)
            $(ctx.targetMap[ctx.methods[x]]).html(this.failObj(this._getAjaxErrMsg(err)));
    },

    /**
     * Default error renderer called when a method
     * api request failed.
     * @param data
     * @param $target
     * @param {Context} The context for this api method
     */
    defaultRenderErr : function(data, $target, ctx){
        if(ctx.methodAll)
            $target.append(this.failObj(data.msg))
        else
            $target.html(this.failObj(data.msg));
    },
    /**
     * The default render if no method_<api method> function exists.
     * Uses prettyPrint.js which is more designed for diagnostics.
     * @param data
     * @param $target
     * @param {Context} The context for this api method
     */
	defaultRender : function(data, $target, ctx){
		var rUrl = '/themes/reports/js/api/prettyPrint.js';
		$.ajax({
		  url: rUrl,
		  dataType: "script",
		  cache: true,
		  success: function(){
              if(ctx.methodAll)
                  $target.append(prettyPrint(data))
              else
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
     * @param {object} data The json data to load instead of making api calls.
	 * @memberof! window._SeoApi_.base
	 */
	execute : function(url, callback, errCallback, data){

        this.url = url;
		
		//make sure we have callbacks defined
		if(typeof callback != "function")
			callback = this.handleSuccess;
		
		if(typeof errCallback != "function")
			errCallback = this.handleError;

        //build the context
        var ctx = this.Context(callback, errCallback);

        //polymorphism, override methods
        this._exWaitOnLoad = (ctx.waitOnLoad)?this._exWaitOnLoad_True:this._exWaitOnLoad_False;
        this._exMethodAll  = (ctx.methodAll) ?this._exMethodAll_True :this._exMethodAll_False;

        if(data == null){
            var req = this.buildRequest(url);
            this.makeRequest(req, ctx);
        }else{
            this._exWaitOnLoad(this,data.data,ctx,ctx.callback);
        }
	},

    /**
     * All the "_ex" are "extend" the current Object with
     * one of the "_ex" versions of the function.
     */
    _exMethodAll : null,
    _exMethodAll_True:function(data, ctx){
        if(typeof ctx.targetMap.all === "function"){
            this.targetMap.all(data);
        }else{
            $(ctx.targetMap.all).empty();
            for(var x in data){
                if(typeof ctx.targetMap[x] === "undefined")
                    ctx.targetMap[x] = this.targetMap.all;
                this.handleSuccessMethod(x, data[x], ctx);
            }
        }
    },
    _exMethodAll_False:function(data,ctx){
        //tracks dom targets which have been used by a method
        var targets = "";

        for(var method in data){

            //function given as target
            if(typeof ctx.targetMap[method] == "function"){
                ctx.targetMap[method](data[method]);

                //dom target given for method
            }else{
                //clear the contents, if they have not already
                //been cleared
                if(targets.indexOf(ctx.targetMap[method]) < 0)
                    $(ctx.targetMap[method]).empty();

                targets += ctx.targetMap[method];

                //render this
                this.handleSuccessMethod(method, data[method], ctx);
            }
        }
    },

    _exWaitOnLoad : null,
    _exWaitOnLoad_True : function(scope, data, ctx, cb){
        $(document).ready(function(){
            cb.call(scope,data,ctx);
        });
    },
    _exWaitOnLoad_False : function(scope, data, ctx, cb){
        cb.call(scope,data,ctx);
    },

    /**
     * Make the actual AJAX request to api.
     * @param {object} req  Object with 2 attributes: url, methods.  url is
     * a string and methods is an array of strings representing api method names.
     * @param {Context} ctx The context to make requests within.
     */
    makeRequest : function(req, ctx){
        var scope = this;
        //get the data, crossdomain using jsonp
        $.ajax({
            'url':req.url,
            'dataType':'jsonp',
            'success':function(data){
                scope._mergeData(data);
                scope._exWaitOnLoad(scope,data.data,ctx,ctx.callback);
            },
            'error':function(jqXHR, status, msg){
                if(ctx.errorRetries < ctx.maxRetries){
                    ctx.errorRetries++;
                    return scope.makeRequest(req, ctx);
                }
                scope._exWaitOnLoad(scope,[jqXHR,status,msg],ctx,ctx.callbackErr);

                //mark all methods in the failed request as completed
                for(var x in req.methods)
                    scope.completed[x] = true;
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