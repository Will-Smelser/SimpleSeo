(function(){
	var namespace = $('script[data-seoapi-ns]').attr('data-seoapi-ns');
	
	window[namespace].body = {
		apiController : 'body',
		dependencies: ['base'],
		render : null,
					
		render_getKeyWords : function(data,$target){
			
			var gdata = new google.visualization.DataTable();
			gdata.addColumn('string','Word');
			gdata.addColumn('number','Word Count');
			
			for(var i=0; i < 10 && i < data.length; i++)
				gdata.addRow([data[i].words[0],data[i].count]);

			var options = {
	          title: 'Top Words for '+$('#get-url').val(),
	          vAxis: {title: 'Words'}, hAxis: {title:'Word Count'},
	          width: 500, height:300,
	        };
			
			new google.visualization.BarChart($target[0]).draw(gdata, options);
		},
		
	};
})();