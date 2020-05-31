require.config({
  paths: {
	 d3: '//cdnjs.cloudflare.com/ajax/libs/d3/5.15.0/d3.min',
	 amchart_core: 'http://www.amcharts.com/lib/4/core',
	 amchart_charts: 'http://www.amcharts.com/lib/4/charts',
	 'amcharts4/themes/animated': 'http://www.amcharts.com/lib/4/themes/animated',
  },
  shim: {
	 amchart_core: {
		  init: function () {
			  return window.am4core;
		  }
	  },
	  amchart_charts: {
		  deps: ['amchart_core'],
		  exports: 'amchart_charts',
		  init: function () {
			  return window.am4charts;
		  }
	  },
	  'amcharts4/themes/animated': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_animated;
		  }
	  }
  }
});