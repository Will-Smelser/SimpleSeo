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

			var fontColor = '#FFF';
			
			var options = {
			  fontSize:16,
	          //title: 'Top Words for '+$('#get-url').val(),
	          vAxis: {title: 'Words'}, hAxis: {title:'Word Count'},
	          width: '100%', height:300,
	          backgroundColor:'transparent', 
	          hAxis:{baselineColor:fontColor,gridlines:{color:fontColor},textStyle:{'color':fontColor},titleTextStyle:{color:fontColor}},
	          vAxis:{baselineColor:fontColor,textStyle:{'color':fontColor},titleTextStyle:{color:fontColor}},
	          titleTextStyle:{color:fontColor},
	          legend:{textStyle:{color:fontColor}},
	          colors:['#FF9900'],
	          chartArea:{top:0,left:100, width:"auto", height:"90%"}
	        };
			
			new google.visualization.BarChart($target[0]).draw(gdata, options);
		},
		
	};
})();