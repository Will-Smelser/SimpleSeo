/**
 * <p>
 * Create the api object used for creating all other objects.  This object gets stored
 * in a global namespace using the attribute tag "data-seoapi-ns" in the pages script tag.  If
 * there is not script tag with the "data-seoapi-ns" attribute, then the 
 * default is used (_SeoApi_).
 * </p>
 * <p>
 * For example, this object, by default, will be stored globally at window._SeoApi_.api.
 * </p>
 * <p>
 * All dynamically loaded objects will be loaded to window._SeoApi_.objectName.  Where
 * objectName is the object loaded.
 * </p>
 * <p>
 * In addition, a copy of the dynamically loaded object will be stored to the global
 * window._SeoApi_ namespace for extending the loaded object.
 * </p>
 * <h3>NOTE:</h3>
 * <p>
 * All documentation which refers to "_SeoApi_" can be overriden by declaring the 
 * "data-seoapi-ns" attribute in the &lt;script&gt; tag.
 * </p>
 * <p>
 * This script uses JQuery and requires it has been loaded prior to using.
 * </>
 * @constructor
 * @param {string} jsLoc The base directory location to dynamically load scripts from.
 * @param {string} apiLoc The base URL for making api requests
 * @param {string} apiToken The api token required to make requests.  These
 * are tokens from /tokens/getToken uri.
 * 
 * @return {Object} Return a reference to the window._SeoApi_.api object.
 * 
 */
SeoApi = function(jsLoc, apiLoc, apiToken){
	//find itself and set the namespace attribute
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	if(namespace == null)
		namespace = '_SeoApi_';
	
	/**
	 * Global window object
	 * @namespace window
	 */
	
	/** 
	 * This is the default namespace which all objects are loaded into.  Override
	 * the name by setting a "data-seoapi-ns" attribute in the &lt;script&gt; tag
	 * used for loading this script.
	 * @namespace {Object} _SeoApi_
	 * @memberof! window
	 */
	window[namespace] = {};
	
	/**
	 * Stores the SeoApi object glabally to this variable.
	 * @namespace {Object} api
	 * @memberof! window._SeoApi_ 
	 */
	window[namespace].api = {
			
		/**
		 * Global namespace where all objects are stored.
		 * @type {string}
		 */
		namespace:namespace,
			
		/**
		 * Store the api token required for each request
		 * @type {string}
		 */
		token:apiToken,	
		
		/**
		 * Store the name of object to be loaded
		 * @type {string}
		 */
		object:null,
		
		/**
		 * The global namespace where this object
		 * is loaded to.  For example, by default the "base" object
		 * is loaded to<ul><li>window._SeoApi_.base</li></ul>
		 * @type {string}
		 */
		name:null,
		
		/**
		 * The api url to make ajax requests to.
		 * @type {string}
		 */
		api:null,
		
		/**
		 * Holds a list of loaded objects that will
		 * be extended once all dependencies are loaded.
		 * @type {Array.<string>}
		 */
		extended:[],
		
		/**
		 * Adds extend objects to {@link window._SeoApi_.api.extended}
		 * @private
		 * @method
		 */
		_addExtend : function(extend){
			if($.inArray(extend, this.extended) < 0)
				this.extended.push(extend);
		},
		
		dependencies:[],
		
		/**
		 * Add a dependency to this object.  All this does
		 * is forces this object to finish its construction
		 * once all dependencies have loaded.<br/><br/>
		 * Using {@link window._SeoApi_.api.extend} will
		 * force a dependency as well.
		 * 
		 * @param {string} apiObject The loaded object to wait for before
		 * object can be considered ready ({@link window._SeoApi_.api.ready}).
		 * @returns {object} this A reference to itself.
		 * @memberof! window._SeoApi_.api
		 */
		depends : function(apiObject){
			if($.inArray(apiObject, this.dependencies) < 0)
				this.dependencies.push(apiObject);
			return this;
		},
		
		methods : [],
		
		/**
		 * Add api methods to be called once the {@link window._SeoApi_.api.exec} method
		 * is called.
		 * 
		 * @param {string} method A method that is defined in the seo api.  For example, if
		 * you are loading "body" api object, then you could add method "checkH1".
		 * @param {string|Callback} target <ul>
		 *
		 * <li>String - If target is a string it should be an 
		 * id (JQuery form of '#id' or jquery object) to an object in the DOM that is passed to render_"method".  See the base class
		 * that should be extended for more information on this.<br/><br/></li>
		 * 
		 * <li>Function - If target is a callback function, then it will be called once the method call 
		 * against the api function is complete.  The first parameter passed in is the JSON data returned
		 * from the api.
		 * </ul>
		 * 
		 * @returns {object} A reference to this.  Useful for chaining.
		 * @memberof! window._SeoApi_.api
		 */
		addMethod : function(method,target){
			console.log("Adding",method,target,this.methods);
			//save method to local copy
			this.methods.push({'m':method,'t':target});

			return this;
		},
		
		_createId : function(){
            //mozilla injects missing indexes if you use an integer (string or number)
			var name = 'R'+Math.floor(Math.random() * 1000);
			return (typeof window[namespace][name] === "undefined")?name:this._createId();
		},
		
		/**
		 * Load an object into the window._SeoApi_[apiObject] namespace.  Very similar to
		 * load except it does not force any dependencies.
		 * 
		 * @param {string} apiObject The api object to load via xhr
		 * @returns {Object} a copy of the window._SeoApi_.api object.  This is needed to
		 * preserve references to dependecies and extending classes.
		 * 
		 * @memberof! window._SeoApi_.api
		 */
		init : function(apiObject){
			return this.load(apiObject,apiObject);
		},
		
		/**
		 * Load an api object into a declared name in the window._SeoApi_ namespace.  This will
		 * load the object using xhr and add the apiObject as a dependency.
		 * @param {string} apiObject The api object to load via xhr
		 * @param {string} [name=Random Generated Name] The name to save a copy of this object into.  Can be
		 * found at window._SeoApi_[name].
		 * @returns {Object} A reference to a new copy of {@link window._SeoApi_.api} object.
		 * @memberof! window._SeoApi_.api
		 */
		load : function(apiObject, name){
			
			var self = $.extend(true,{},this);
			
			if(typeof name === "undefined"){
				name = self._createId();
                //this works, because js is single threaded, and thus,
                //this createId and set operation are atomic.
                window[namespace][name] = null;
            }
			
			self.object = apiObject;
			self.name = name;
			
			self.depends(name);
			self.depends(apiObject);
			
			$.ajax({
				  url: jsLoc + apiObject+'.js',
				  dataType: "script",
				  cache: true,
				  success: function(){
					  //copy the loaded api object into the namespace
					  if(name !== apiObject)
						  window[namespace][name] = $.extend(true, {}, window[namespace][apiObject]);
					  
					  window[namespace][name].api = apiLoc;
				  },
				  failure: function(){
					  console.log("Failed to load api object ("+name+")");
					  window[namespace][name] = {};
				  }
			});
			
			return self;
		},
		
		/**
		 * Extend a loaded object.  Used for inheriting methods and
		 * parameters of another object.  This does <b>NOT</b> attempt
		 * to load the extended object.  Just forces object to wait till
		 * object is loaded into namespace so it can be extended.
		 * 
		 * @memberof! window._SeoApi_.api
		 * @method
		 * @param {string} extend The object that should be loaded into window._SeoApi_
		 * namespace for extending.
		 * @returns {Object} this A reference to itself.  Useful for chaining.
		 * @memberof! window._SeoApi_.api
		 */
		extend : function(extend){
			var self = this;
			self.depends(extend);
			self._addExtend(extend);
						
			return self;
		},
		
		/**
		 * Wait till all dependencies for this object have finished loading.  Used internally
		 * by the {@link window._SeoApi_.api.exec} method.
		 * 
		 * @param {requestCallback} func The function to be executed once all dependencies have
		 * completed loading.
		 * 
		 * @param {string} ignore An object to skip waiting for in its dependencies.  This is used 
		 * internally since {@link window._SeoApi_.api.load} adds itself as a dependency.  This resolves
		 * chicken/egg dependency problem.
		 * 
		 * @returns {Object} A reference to itself.  Useful for chaining.
		 * @memberof! window._SeoApi_.api
		 */
		ready : function(func, ignore){
			var self = this;
			var ready = true;
			
			console.log("Checking if ready",this.dependencies);
			
			for(var x in self.dependencies){
				var dep = self.dependencies[x];
				
				console.log("Checking",dep);
				
				if(typeof ignore !== "undefined" && dep === ignore){
					console.log("Skiping",dep);
					continue;
				}
				
				if(typeof window[namespace][dep] === "undefined"){
					ready = false;
					console.log("Not ready",dep);
					break;
				}
			}
			
			if(!ready)
				setTimeout(function(){self.ready(func,ignore);},50);
			else if(typeof func === "function")
				func();
			
			console.log("READY ",self.name,ignore);
			
			return self;
			
		},
		
		/**
		 * Call the fully loaded api object once it is ready to execute
		 * all methods created using {@link window._SeoApi_.api.addMethod}
		 * against the api.  In most use cases, the loaded api object has
		 * extended "base" which does the heavy lifting of making actual api
		 * calls.
		 * 
		 * @param {string} url The url of website api methods should get information
		 * on.  For example, http://www.google.com or google.com.
		 * 
		 * @param {Callback} [callback] An optional callback to execute once the
		 * execute function called on api object is complete.  This removes default
		 * behavior of calling all the render_&lt;method&gt; functions.  However,
		 * this scope of callback is the loaded object.  So for example you can
		 * call this.handleSuccess() within your callback.
		 *  
		 * @param {Callback} [errCallback] An optional callback to execute once the
		 * execute function called on api object is complete and an error was detected.
         *
         * @param {object} [data] An optional data json object to use to load api responses
         * instead of making actual api calls.  This allows for loading data saved data.  Requires
         * "callback" and "errCallback" have had values set.  For example,
         * <code>exec("http://somedomain.com",null,null,{"code":200,"response":"Success",...});</code>
		 * 
		 * @returns {Object} A reference to this.  Useful for chaining.
		 * 
		 * @memberof! window._SeoApi_.api
		 */
		exec : function(url, callback, errCallback, data){
            if(typeof data == "undefined")
                data = null;

			var self = this;
			self.ready(function(){
				//make sure all extends happen
				for(var x in self.extended)
					window[namespace][self.name] = $.extend(true, {}, window[namespace][self.extended[x]], window[namespace][self.name]);
				
				//make sure we have added all methods for api request
				for(var x in self.methods)
					window[namespace][self.name].addMethod(self.methods[x].m,self.methods[x].t);
				
				//call init function on the loaded object
				if(typeof window[namespace][self.name].init === 'function')
					  window[namespace][self.name].init();
				
				//make api call
                window[namespace][self.name]
                    .execute(url+'&token='+self.token,callback,errCallback,data);
				
			});

			return self;
		}
		
	};
	return window[namespace].api;
};