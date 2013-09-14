SeoApi = function(jsLoc, apiLoc, apiKey){
	//find itself and set the namespace attribute
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	if(namespace == null)
		namespace = '_SeoApi_';
	
	window[namespace] = {};
	window[namespace].api = {
		
		key:apiKey,
		
		object:null,
		name:null,
		
		
		api:null,
		
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
		
		init : function(apiObject){
			return this.load(apiObject,apiObject);
		},
		
		extendBase : function(apiObject, name){
			return this.load(apiObject,name,'base');
		},
		
		load : function(apiObject, name, extend){
			
			var self = $.extend(true,{},this);
			
			if(typeof name === "undefined")
				name = Math.floor(Math.random() * 10000);
			
			self.object = apiObject;
			self.name = name;
			
			self.depends(name);
			self.depends(extend);
			self.depends(apiObject);
			
			$.ajax({
				  url: jsLoc + apiObject+'.js',
				  dataType: "script",
				  cache: true,
				  success: function(){
					  self._loadSuccess(apiObject, name, extend);
				  },
				  failure: function(){
					  console.log("Failed to load api object ("+name+")");
					  window[namespace][name] = {};
				  }
			});
			return self;
		},
		
		_extend : function(name, extend, obj){
			
		},
		
		_loadSuccess : function(apiObject, name, extend){
			if(typeof extend === "undefined")
				  window[namespace][name] = $.extend(true, {}, window[namespace][apiObject]);
			  else
				  self.ready(function(){
					  window[namespace][name] = $.extend(true, {}, window[namespace][extend], window[namespace][apiObject]);
				  }, name);
			  
			  //set the api url
			  window[namespace][name].api = apiLoc;
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
			self = this;
			this.ready(function(){
				for(var x in self.methods)
					window[namespace][self.name].addMethod(self.methods[x].m,self.methods[x].t);
				
				//call init function on the loaded object
				if(typeof window[namespace][self.name].init === 'function')
					  window[namespace][self.name].init();
				
				//make api call
				window[namespace][self.name].execute(url+'&key='+self.key, callback, errCallback);
				
			});

			return self;
		}
		
	};
	return window[namespace].api;
};