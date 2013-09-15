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
	 * that report data is being generated for
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
	 * The speciall "all" method was given.
	 * @type {boolean}
	 * @memberof! window._SeoApi_.base
	 */
	methodAll : false,
	
	/**
	 * All methods, exluding "all", which requests will be made for.
	 */
	methods : [],
	targetMap : {},
	
	
	failObj : (function(){return $("<div class='seo-fail'><span>Failed</span><div class='reason'></div></div>");})(),
	buildRequest : function(url){
		this.checkApi();
		var method = (this.methodAll) ? 'all' : this.methods.join('|');
		return this.api+this.apiController+'/'+method+'?request='+url;
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
	handleSuccess : function(data){
		//success does not mean we got a good response,
		//just that it wasnt an HTTP error
		//TODO handle error cases
		
		//empty content
		var targets = "";
		
		for(var method in data){
			if(typeof this.targetMap[method] == "function"){
				this.targetMap[method](data[method]);
			}else{
				//clear the contents
				if(targets.indexOf(this.targetMap[method]) < 0)
					$(this.targetMap[method]).html("");
				
				targets += this.targetMap[method];
				//render this
				this.handleSuccessTrue(method, data[method].data,this.targetMap[method]);
			}
		}
		
		//clear everything
		this.targetMap = [];
		this.methods = [];
		this.methodAll = false;
	},
	handleSuccessTrue : function(method, data, target){
		$target = $(target);
		if(typeof this['render_'+method] != "function"){
			console.log('Custom handler render_'+method+' did not exist. Using default...');
			this.defaultRender(data, $target);
		}else{
			this['render_'+method](data, $target);
		}
	},
	handleError : function(){
		for(var x in this.methods){
			$(this.targetMap[this.methods[x]]).html(this.failObj.find('.reason').html('Ajax Request Failure'));
		}
	},
	//need to handle this better
	defaultRender : function(data, $target){
		console.log('loading pretty print',data);
		var rUrl = (typeof window.SeoReport == "undefined") ? "" : "js/";
		rUrl += 'prettyPrint.js';
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
		var scope = this;
		this.url = url;
		var req = this.buildRequest(url);
		
		//make sure we have callbacks defined
		if(typeof callback != "function")
			callback = this.handleSuccess;
		
		if(typeof errCallback != "function")
			errCallback = this.handleError;
		
		//get data
		$.getJSON(req,function(data){
			
			//we dont get nested responses when
			//only a single method is requested,
			//this will give us same response regardless
			var resp = {};
			if(scope.methods.length === 1){
				resp[scope.methods[0]] = {
						data : data.data
				};
			}else
				resp = data.data;
			
			if(scope.waitOnLoad){
				$(document).ready(function(){
					callback.call(scope, resp);
				});
			}else{
				callback.call(scope, resp);
			}
		})
		.fail(function(){
			if(scope.waitOnLoad){
				$(document).ready(function(){
					errCallback.call(scope);
				});
			}else{
				errCallback.call(scope);
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
	 * @param {JSON} data The api returned json objbect
	 * @param {Object} $target The jquery target initially given to this api request.
	 * @memberof! window._SeoApi_.base
	 */
	render_apiMethod : function(data, $target){
				
	}
};
})();