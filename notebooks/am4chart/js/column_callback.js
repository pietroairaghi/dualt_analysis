function new_columnCallback(params, canvasID) {

    return function (am4core, am4charts, am4themes_animated) {

        // Create chart instance
        var chart = am4core.create(canvasID, am4charts.XYChart);
        chart.numberFormatter.numberFormat = "#.0";

// Add data
        chart.data = [{
            "year": "2003",
            "europe": 2.5,
            "namerica": 2.5,
            "asia": 2.1,
            "lamerica": 1.2,
            "meast": 0.2,
            "africa": 0.1
        }, {
            "year": "2004",
            "europe": 2.6,
            "namerica": 2.7,
            "asia": 2.2,
            "lamerica": 1.3,
            "meast": 0.3,
            "africa": 0.1
        }, {
            "year": "2005",
            "europe": 2.8,
            "namerica": 2.9,
            "asia": 2.4,
            "lamerica": 1.4,
            "meast": 0.3,
            "africa": 0.1
        }];

// Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "year";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 20;
        categoryAxis.renderer.inside = true;
        categoryAxis.renderer.labels.template.valign = "top";
        categoryAxis.renderer.labels.template.fontSize = 20;
        categoryAxis.renderer.cellStartLocation = 0.1;
        categoryAxis.renderer.cellEndLocation = 0.9;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        valueAxis.title.text = "Expenditure (M)";

// Create series
        function createSeries(field, name) {
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = field;
            series.dataFields.categoryX = "year";
            series.name = name;
            series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
            series.columns.template.width = am4core.percent(95);

            var bullet = series.bullets.push(new am4charts.LabelBullet);
            bullet.label.text = "{name}";
            bullet.label.rotation = 90;
            bullet.label.truncate = false;
            bullet.label.hideOversized = false;
            bullet.label.horizontalCenter = "left";
            bullet.locationY = 1;
            bullet.dy = 10;
        }

        chart.paddingBottom = 150;
        chart.maskBullets = false;

        createSeries("europe", "Europe", false);
        createSeries("namerica", "North America", true);
        createSeries("asia", "Asia", false);
        createSeries("lamerica", "Latin America", true);
        createSeries("meast", "Middle East", true);
        createSeries("africa", "Africa", true);

    }
}


function columnCallback(params, canvasID) {

    return function (am4core, am4charts, am4themes_animated,am4theme=null) {

        let legend = params['legend'];
        let data = params['data'];
        let series = params['series'];
        let stacks = params['stacks'];

        if (stacks == 1) {
            stacks = 5; // to better differentiate colors
        }

        if (legend === undefined) {
            legend = true;
        }

        /* Chart code */
        // Themes begin
        if(am4theme){
            am4core.useTheme(am4theme);
        }
        am4core.useTheme(am4themes_animated);
        // Themes end


        let chart = am4core.create(canvasID, am4charts.XYChart);

        var tot_colors = Object.keys(chart.colors._list).length;
        if(!am4theme){
            tot_colors = 20;
        }

        var stepColor = Math.ceil(tot_colors / stacks);
        if(params['x_vals'].length<Object.keys(series).length && params['stacks']==1){
            stepColor = 1;
        }

        chart.colors.step = stepColor;

        if (params['title']) {
            let title = chart.titles.create();
            title.text = params['title'];
            title.fontSize = 18;
            title.marginBottom = 10;
        }

        if (legend) {
            chart.legend = new am4charts.Legend();
            chart.legend.position = 'bottom';
            chart.legend.paddingLeft = 60;
            chart.legend.maxWidth = undefined;
            chart.legend.valueLabels.template.align = "center";
            chart.legend.valueLabels.template.textAlign = "center";
        }

        let xAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        // Create value axis
        xAxis.dataFields.category = 'x';
        xAxis.renderer.cellStartLocation = 0.1;
        xAxis.renderer.cellEndLocation = 0.9;
        xAxis.renderer.grid.template.location = 0;
        xAxis.renderer.minGridDistance = 1;

        let label = xAxis.renderer.labels.template;
        label.wrap = true;
        label.maxWidth = 120;
        if(params['hide_x_labels']){
            label.hide()
        }

        let yAxis = chart.yAxes.push(new am4charts.ValueAxis());
        if(params['log_scale']){
			yAxis.min = 1;
			yAxis.logarithmic = true;
		}
		
		if(params['min_y']){
			yAxis.min = params['min_y'];
		}
		if(params['max_y']){
			yAxis.max = params['max_y'];
		}



        function createSeries(value, name, stacked = false, colorIndex = 0) {
            let series = chart.series.push(new am4charts.ColumnSeries());
            series.name = name;
            series.dataFields.valueY = value;
            series.dataFields.categoryX = 'x';

            series.stacked = stacked;

            series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
            series.tooltip.pointerOrientation = "down";
            series.columns.template.tooltipY = -3;

            if(params['stacks']!=1){
                series.columns.template.fill = chart.colors.getIndex(colorIndex*2);
                series.columns.template.stroke =chart.colors.getIndex(colorIndex*2);
            }

            let stocazzo = false;

            if (stocazzo) {
                let bullet = series.bullets.push(new am4charts.LabelBullet());
                bullet.dy = -10;
                bullet.label.text = '{valueY}';
                bullet.label.truncate = false;
                bullet.label.fill = am4core.color('#000000')
            }

            return series;
        }

        chart.data = data;

        var colorIndex = 0
        Object.keys(series).forEach(function (key, index) {
            if(this[key]){
                colorIndex++;
            }else{
                colorIndex=0;
            }
            createSeries(key, key, this[key],colorIndex)
        }, series);

        // check for dividers
        if(params['range_divider']) {

            var start_val = data[0]['x'];
            colorIndex = 0;
            for (index = 0; index < params['range_divider'].length; ++index) {
                if((index+1) == params['range_divider'].length || params['range_divider'][index+1]){
                    var end_val = data[index]['x'];

                    var range = xAxis.axisRanges.create();
                    range.category = start_val;
                    range.endCategory = end_val;
                    range.value = start_val;
                    range.endValue = end_val;
                    range.axisFill.fill = chart.colors.getIndex(colorIndex*4);
                    range.axisFill.fillOpacity = 0.15;
                    range.label.disabled = true;

                    if((index+1) != params['range_divider'].length){
                        start_val = data[index+1]['x'];
                        colorIndex++;
                    }

                }
            }
        }

        if(params['y_title']){
            yAxis.title.text = params['y_title'];
        }

        if(params['x_title']){
            xAxis.title.text = params['x_title'];
        }

        if(params['is_category'] && params['x_names_map']) {
            xAxis.renderer.labels.template.adapter.add("textOutput", function (text) {
                var new_text = params['x_names_map'][text];
                return new_text;
            });
        }
		
		//chart.exporting.menu = new am4core.ExportMenu();


    }

}