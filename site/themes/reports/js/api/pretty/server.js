(function(){
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	
	window[namespace].server = {
		//ensure render gets loaded
		init:function(){},
		dependencies: ['render'],
		apiController : 'server',
		
		/**
		 * RENDERINGS
		 */
		render_all : function(data, $target){
			var scope = this;
			for(var x in scope){
				if(x !== "render_all" && x.indexOf('render') === 0){
					scope[x](data,$target);
				}
			}
		},
		
		
		render_getWhois : function(data,$target){
			var render = window[namespace].render;
			for(var x in data){
				$target.append(render.newLi(x.replace('_',' '),data[x]));
			}
		},
		
		render_getHeaderResponseLine:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('HTTP Response Code',data));
		},
		
		render_getLoadTime:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Load Time',data+' sec.'));
		},
		
		render_getServer:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Server Info',data));
		},
		
		render_isGzip:function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Gzip Compression',(data?"True":"False")));
		},
		
		render_checkRobots : function(data,$target){
			var render = window[namespace].render;
			$target.append(render.newLi('Robots.txt',data));
		}
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})(/*namespace*/);