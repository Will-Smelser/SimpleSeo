SeoApi = function(jsLoc, apiKey){
	//find itself and set the namespace attribute
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	window[namespace] = {};
	window[namespace].api = {
		noExtend:'base,render',
		key:apiKey,
		currentApiObject:null,
			
		/**
		 * Can be overwritten, called after
		 * the object has finished loading
		 */
		init:function(){},
		
		/**
		 * Takes 1 or 2 parameters
		 * apiObject is null, then api variable is copied 
		 * to apiObject and api is set to null.
		 * @param api  The url of seo api
		 * @param apiObject The api controller to load
		 * @param callback A callback to be executed after load and merge
		 */
		load : function(api, apiObject, callback){
			var scope = this;
			if(typeof apiObject == "undefined"){
				apiObject = api;
				api = null;
			}
			
			//dont bother attempting to reload things, jquery takes care of this too
			//but short circuit things that have already been loaded
			console.log('load called',apiObject,namespace,window[namespace]);
			if(typeof window[namespace][apiObject] !== "undefined") return;
			
			$.ajax({
			  url: jsLoc + apiObject+'.js',
			  dataType: "script",
			  cache: true,
			  success: function(){
				  //use the ready function to execute script once the load is complete
				  scope.ready(function(){
					  //some object should not be extending base class
					  if(scope.noExtend.indexOf(apiObject)<0)
						  window[namespace][apiObject] = $.extend(true, {}, window[namespace].base, window[namespace][apiObject]);

					  if(api !== null)
						  window[namespace][apiObject].api = api;
					  
					  if(typeof window[namespace][apiObject].init === 'function')
						  window[namespace][apiObject].init();
					  
					  if(typeof callback === "function") callback();
					  
				  }());
				  
			  },
			  failure: function(){
				  console.log("Failed to load api object ("+apiObject+")");
				  window[namespace][apiObject] = {};
			  }
			});
			
			var self = $.extend(true,{},this);
			self.currentApiObject = apiObject;
			
			//has to wait on itself to load
			self.depends(apiObject);
			
			return self;
		},
		/**
		 * 
		 * @param apiObject
		 * @param targetObj Should be "base"
		 * @param object
		 */
		extend : function(apiObject, targetObj, object){
			var scope = this;
			if(typeof window[namespace][targetObj] === "undefined"){
				setTimeout(function(){scope.extend(apiObject, targetObj, object);},50);
			}else{
				if(typeof window[namespace][apiObject] === "undefined")
					window[namespace][apiObject] = {};
				
				$.extend(true,window[namespace][apiObject],window[namespace][targetObj],object);				
			}
		},
		
		addMethod : function(method,target){
			var apiObject = this.currentApiObject;
			this.ready(function(){
				console.log('Adding method',namespace,apiObject,method,target);
				window[namespace][apiObject].addMethod(method,target);
			});
			
			return this;
		},
		
		exec : function(url, callback, errCallback){
			var scope = this;
			this.ready(function(){
				window[namespace][scope.currentApiObject].exec(url+'&key='+scope.key, callback, errCallback);
			});
			
			return this;
		},
		
		dependencies : ['base'],
		depends : function(apiObject){
			this.dependencies.push(apiObject);
			return this;
		},
		
		/**
		 * Wait till all dependencies are ready
		 * @param callback Callback to fire one all the dependencies are loaded
		 * @returns self
		 */
		ready : function(callback){
			var scope = this;
			var ready = true;
			for(dep in this.dependencies){
				if(typeof window[namespace][scope.dependencies[dep]] === "undefined"
					|| typeof window[namespace][scope.dependencies[dep]].isReady !== "function"
				){
					ready = false;
					break;
				}
				
				try{
					if(window[namespace][scope.dependencies[dep]].isReady()) continue;
				}catch(e){
					//the object might have existed, but fully loaded
					ready = false;
					break;
				}
			}
			
			if(!ready)
				setTimeout(function(){scope.ready(callback);},50);
			else if(typeof callback === "function")
				callback();
			
			return scope;
		}
			
	};
	return window[namespace].api;
};