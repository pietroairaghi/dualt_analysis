require.config({
  paths: {
	 d3: '//cdnjs.cloudflare.com/ajax/libs/d3/5.15.0/d3.min',
	 amchart_core: 'http://www.amcharts.com/lib/4/core',
	 amchart_charts: 'http://www.amcharts.com/lib/4/charts',
	 'amcharts4/themes/animated': 'http://www.amcharts.com/lib/4/themes/animated',
	 'amcharts4/themes/amcharts': 'http://www.amcharts.com/lib/4/themes/amcharts',
	 'amcharts4/themes/dataviz': 'http://www.amcharts.com/lib/4/themes/dataviz',
	 'amcharts4/themes/frozen': 'http://www.amcharts.com/lib/4/themes/frozen',
	 'amcharts4/themes/kelly': 'http://www.amcharts.com/lib/4/themes/kelly',
	 'amcharts4/themes/material': 'http://www.amcharts.com/lib/4/themes/material',
	 'amcharts4/themes/moonrisekingdom': 'http://www.amcharts.com/lib/4/themes/moonrisekingdom',
	 'amcharts4/themes/spiritedaway': 'http://www.amcharts.com/lib/4/themes/spiritedaway',
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
	  },
	  'amcharts4/themes/dataviz': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_dataviz;
		  }
	  },
	  'amcharts4/themes/frozen': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_frozen;
		  }
	  },
	  'amcharts4/themes/kelly': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_kelly;
		  }
	  },
	  'amcharts4/themes/material': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_material;
		  }
	  },
	  'amcharts4/themes/moonrisekingdom': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_moonrisekingdom;
		  }
	  },
	  'amcharts4/themes/amcharts': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_amcharts;
		  }
	  },
	  'amcharts4/themes/spiritedaway': {
		  deps: ['amchart_core'],
		  init: function () {
			  return window.am4themes_spiritedaway;
		  }
	  }
  }
});