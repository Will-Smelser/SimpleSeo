(function(ns){
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	
	window[namespace].social = {
		//ensure render gets loaded
		init:function(){
			this.render = window[namespace].render;
		},
		dependencies: ['render'],
		apiController : 'social',
		
		render:null,
		
		/**
		 * RENDERINGS
		 */
		
		render_all : function(data, $target){
            $target.empty();
			var self = this;
			for(var x in self){
				if(x.indexOf("render_") >= 0 && x !== "render_apiMethod" && x !== "render_all" && typeof self[x] === "function"){
					self[x](data[x.replace('render_','')].data,$target);
				}
			}
		},
		
		render_googlePlus : function(data, $target){
			$target.append(this.render.newLi('Google Plus',data));
		},
		
		render_getFbLikeCount : function(data, $target){
			$target.append(this.render.newLi('fb Likes',data));
		},
		
		render_getFbShareCount : function(data, $target){
			$target.append(this.render.newLi('fb Shares', data));
		},
		
		render_getFbCommentCount : function(data, $target){
			$target.append(this.render.newLi('fb Comments',data));
		},
		
		render_getTwitterShares : function(data, $target){
			$target.append(this.render.newLi('Twitter',data));
		},
		
		render_getDeliciousShares : function(data, $target){
			$target.append(this.render.newLi('Delicious',data));
		},
		
		render_getDiggShares : function(data, $target){
			$target.append(this.render.newLi('Digg',data));
		},
		
		render_getLinkedInShares : function(data, $target){
			$target.append(this.render.newLi('LinkedIn',data));
		},
		
		render_getPinterestShares : function(data, $target){
			$target.append(this.render.newLi('Pinterest',data));
		},
		
		render_getStumbleUponShares : function(data, $target){
			$target.append(this.render.newLi('StumbleUpon',data));
		},
		
		render_getVKontakteShares : function(data, $target){
			$target.append(this.render.newLi('VKontakte',data));
		}
		/*
		render_getTitle : function(data, $target){
			console.log(data);
		}*/
	};
})();