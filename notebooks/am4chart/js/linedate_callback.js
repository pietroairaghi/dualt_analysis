function lineDateCallback(params,canvasID){

	return function(am4core,am4charts,am4themes_animated){
	
		var data   = params['data'];
		var series = params['series'];
		
		
		am4core.useTheme(am4themes_animated);

		// Create chart instance
		var chart = am4core.create(canvasID, am4charts.XYChart);
		chart.colors.step = 5;

		// Add data
		chart.data = data

		// Create axes
		var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
		dateAxis.renderer.grid.template.location = 0;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

		chart.scrollbarX = new am4core.Scrollbar();
		chart.cursor = new am4charts.XYCursor();

		// Create series
		function createSeries(field, name) {
			var series = chart.series.push(new am4charts.LineSeries());
			series.dataFields.valueY = field;
			series.dataFields.dateX = "x";
			series.name = name;
			series.tooltipText = "{dateX}: [b]{valueY}[/]";
			series.strokeWidth = 2;

			var bullet = series.bullets.push(new am4charts.CircleBullet());
			bullet.circle.stroke = am4core.color("#fff");
			bullet.circle.strokeWidth = 2;

			// Add scrollbar
			//chart.scrollbarX.series.push(series);

			// Add cursor
			chart.cursor.xAxis = dateAxis;
			chart.cursor.snapToSeries = series;

			return series;
		}

		series.forEach(element => createSeries(element, element));

		chart.legend = new am4charts.Legend();
		chart.cursor = new am4charts.XYCursor();
	}

}