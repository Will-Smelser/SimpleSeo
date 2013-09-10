SeoApi = function(jsLoc, apiLoc, apiKey){
	//find itself and set the namespace attribute
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	if(namespace == null)
		namespace = '_SeoApi_';
	
	window[namespace] = {};
	window[namespace].api = {
		noExtend:'base,render,',//trailing comma is a must
		key:apiKey,
		currentApiObject:null,
		methods:[],
			
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
		load : function(apiObject, callback, name){
			
			if(typeof name === "undefined")
				name = apiObject;
			
			var self = $.extend(true,{},this);
			
			if(self.noExtend.indexOf(self.currentApiObject+',')<0)
				self.currentApiObject = name;
			
			//has to wait on itself to load
			self.depends(apiObject);
			
			self._downloadSelf(name);
			
			return self;
		},
		
		_downloadSelf : function(name){
			var scope = this;
			$.ajax({
				  url: jsLoc + scope.currentApiObject+'.js',
				  dataType: "script",
				  cache: true,
				  success: function(){
					  //use the ready function to execute script once the load is complete
					  scope.ready(function(){
						  
						  //some object should not be extending base class
						  if(scope.noExtend.indexOf(scope.currentApiObject+',')<0){
							  
							  window[namespace][name] = $.extend(true, {}, window[namespace].base, window[namespace][scope.currentApiObject]);
							  console.log('Extending ',name,scope,window[namespace][name]);
						  }else{
							  console.log('Not Extending', name);
						  }
							  
						  //set the api url
						  window[namespace][name].api = apiLoc;
						  
						  if(typeof window[namespace][name].init === 'function')
							  window[namespace][name].init();
						  
						  if(typeof callback === "function") callback();
						  
					  },scope.currentApiObject);
					  
				  },
				  failure: function(){
					  console.log("Failed to load api object ("+name+")");
					  window[namespace][name] = {};
				  }
			});
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
		
		//calling exec without everything being loaded is a problem.
		addApiMethod : function(method,target){
			console.log("Adding",method,target,this.methods);
			//save method to local copy
			this.methods.push({'m':method,'t':target});
			
			return this;
		},
		
		exec : function(url, callback, errCallback){
			var scope = this;
			
			this.ready(function(){
				for(var x in scope.methods)
					window[namespace][scope.currentApiObject].addMethod(scope.methods[x].m,scope.methods[x].t);
				window[namespace][scope.currentApiObject].execute(url+'&key='+scope.key, callback, errCallback);
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
		 * @param except A dependecy to skip, usually called by itself 
		 * when waiting on its dependencies to load.
		 * @returns self
		 */
		ready : function(callback, except){
			var scope = this;
			var ready = true;
			
			for(dep in scope.dependencies){
				var temp = scope.dependencies[dep];
				
				//skip this dependency
				if(typeof except !== "undefined" && except === temp)
					continue;
				
				if(typeof window[namespace][temp] === "undefined"
					|| typeof window[namespace][temp].isReady !== "function"
				){
					console.log("Dependency not ready ",except, temp, window[namespace][temp]);
					ready = false;
					break;
				}
				
				try{
					if(window[namespace][temp].isReady()) continue;
				}catch(e){
					//the object might have existed, but fully loaded
					ready = false;
					break;
				}
			}
			
			if(!ready)
				setTimeout(function(){scope.ready(callback,except);},50);
			else if(typeof callback === "function"){
				console.log("READY ",scope.currentApiObject," all dependencies loaded ",scope.dependencies);
				callback();
			}else{
				console.log("READY ",scope.currentApiObject," all dependencies loaded ",scope.dependencies);
			}
			
			return scope;
		}
			
	};
	return window[namespace].api;
};