function pieCallback(params,canvasID) {
	return function(am4core, am4charts, am4themes_animated) {
		var data = params['data'];
		
		am4core.useTheme(am4themes_animated);

		 // Create chart instance
		var chart = am4core.create(canvasID, am4charts.PieChart);

		// Add data
		chart.data = data;

		// Add and configure Series
		var pieSeries = chart.series.push(new am4charts.PieSeries());
		pieSeries.dataFields.value = "y";
		pieSeries.dataFields.category = "x";

		// Add export
		chart.exporting.menu = new am4core.ExportMenu();
		
		
	};
}