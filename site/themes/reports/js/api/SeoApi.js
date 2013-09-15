SeoApi = function(jsLoc, apiLoc, apiKey){
	//find itself and set the namespace attribute
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	if(namespace == null)
		namespace = '_SeoApi_';
	
	window[namespace] = {};
	window[namespace].api = {
		/* User api key */
		key:apiKey,	
		
		/* The loaded apiObject */
		object:null,
		
		/* name in namespace where this is globally stored */
		name:null,
		
		/* The api location to make requests to */
		api:null,
		
		extended:[],
		_addExtend : function(extend){
			if($.inArray(extend, this.extended) < 0)
				this.extended.push(extend);
		},
		
		dependencies:[],
		depends : function(apiObject){
			if($.inArray(apiObject, this.dependencies) < 0)
				this.dependencies.push(apiObject);
			return this;
		},
		
		methods : [],
		addMethod : function(method,target){
			console.log("Adding",method,target,this.methods);
			//save method to local copy
			this.methods.push({'m':method,'t':target});

			return this;
		},
		
		_createId : function(){
			var name = Math.floor(Math.random() * 10000);
			return (typeof window[namespace][name] === "undefined")?name:this._createId();
		},
		
		init : function(apiObject){
			return this.load(apiObject,apiObject);
		},
		
		load : function(apiObject, name){
			
			var self = $.extend(true,{},this);
			
			if(typeof name === "undefined")
				name = self._createId();
			
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
		
		extend : function(extend){
			var self = this;
			self.depends(extend);
			self._addExtend(extend);
						
			return self;
		},
		
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
		
		exec : function(url, callback, errCallback){
			console.log("CALLED EXEC ON", this);
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
				console.log("CALLED EXECUTE ON",window[namespace][self.name],self.name);
				window[namespace][self.name].execute(url+'&key='+self.key, callback, errCallback);
				
			});

			return self;
		}
		
	};
	return window[namespace].api;
};