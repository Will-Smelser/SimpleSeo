(function(){
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	
	window[namespace].w3c = {
		//ensure render gets loaded
		init:function(){},
		dependencies: ['render'],
		apiController : 'w3c',
		
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
		
		render_validateW3C : function(data, $target){
			$target.append('The HTML document is '+(data?'<b>VALID</b>':'<b style="color:red">INVALID</b>'));
		},
		
		render_getValidateW3Cerrors : function(data,$target){
			var render = window[namespace].render;
			if(data.length > 0){
				$target.append(render.newTbl(this.cleanW3c(data)));
			}else{
				$target.append('No Errors');
			}
		},
		
		render_getValidateW3Cwarnings : function(data,$target){
			var render = window[namespace].render;
            if(data.length > 0){
                $target.append(render.newTbl(this.cleanW3c(data)));
            }else{
                $target.append('No Warnings');
            }
		},

        cleanW3c : function(data){
            var result = [];
            for(var x in data){
                var temp = {};
                for(var y in data[x]){
                    if(y != 'source' && y != 'messageid' && y != 'explanation'){
                        temp[y] = data[x][y];
                    }
                }
                result.push(temp);
            }
            return result;
        }
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})(/*namespace*/);