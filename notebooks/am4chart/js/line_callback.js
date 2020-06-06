function lineCallback(params, canvasID) {

    return function (am4core, am4charts, am4themes_animated,am4theme=null) {

        let legend = params['legend'];
        let data = params['data'];
        let series = params['series'];
        let stacks = params['stacks'];


        if(am4theme){
            am4core.useTheme(am4theme);
        }
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        let chart = am4core.create(canvasID, am4charts.XYChart);
        chart.numberFormatter.numberFormat = "#.0";

        var tot_colors = Object.keys(chart.colors._list).length;
        if(!am4theme){
            tot_colors = 20;
        }

        var stepColor = Math.ceil(tot_colors / stacks);
        if(params['x_vals'].length<Object.keys(series).length || params['stacks']==1){
            stepColor = 4;
        }

        chart.colors.step = stepColor;

        // Add data
        chart.data = data;

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

        // Create axes
        var axis_type = new am4charts.ValueAxis();
        if(params['is_category']) {
            axis_type = new am4charts.CategoryAxis();
        }

        let xAxis = chart.xAxes.push(axis_type);

        xAxis.dataFields.category = 'x';

        xAxis.renderer.cellStartLocation = 0.1;
        xAxis.renderer.cellEndLocation = 0.9;
        xAxis.renderer.grid.template.location = 0;
        xAxis.renderer.minGridDistance = 1;

        let label = xAxis.renderer.labels.template;
        if(params['hide_x_labels']){
            label.hide()
        }


// Create value axis
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

        // Create series
        function createSeries(value, name, stacked=false) {
            let series = chart.series.push(new am4charts.LineSeries());

            series.name = name;

            series.dataFields.categoryX = "x";
            series.dataFields.valueX  = "x";
            series.dataFields.valueY = value;
            series.tensionX = 0.77;
            //series1.dataFields.value = "aValue";
			
			if(params['stacks']!=1){
				series.stroke = chart.colors.getIndex(colorIndex*2);
				series.fill = chart.colors.getIndex(colorIndex*2);
            }
			
            series.strokeWidth = 2;

            let bullet1 = series.bullets.push(new am4charts.CircleBullet());
            if(0) {
                series.heatRules.push({
                    target: bullet1.circle,
                    min: 3,
                    max: 20,
                    dataField: "value",
                    property: "radius"
                });
            }

            //bullet1.tooltipText = "{valueX} x {valueY}: [bold]{value}[/]";
            bullet1.tooltipText = "{valueY}";

            return series;
        }
		
		
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
            for (index = 0; index < params['range_divider'].length; index++) {
                if((index+1) == params['range_divider'].length || params['range_divider'][index]){
					if((index+1) == params['range_divider'].length){
						var end_val = data[(index+1)*params['stacks']-1]['x'];
					}else{
						var end_val = data[(index)*params['stacks']-1]['x'];
					}

                    var range = xAxis.axisRanges.create();
                    range.category = start_val;
                    range.endCategory = end_val;
                    range.value = start_val;
                    range.endValue = end_val;
                    range.axisFill.fill = chart.colors.getIndex(colorIndex*4);
                    range.axisFill.fillOpacity = 0.15;
                    range.label.disabled = true;

                    if((index) != params['range_divider'].length){
                        start_val = data[(index)*params['stacks']]['x'];
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

        if(params['visible_labels']) {
            xAxis.renderer.labels.template.adapter.add("textOutput", function (text) {
                if(!params['visible_labels'].includes(text)){
                    text = "";
                }
                return text;
            });
        }

        //chart.scrollbarX = new am4core.Scrollbar();
        //chart.scrollbarX.hideGrips = true;
        chart.swipeable = true;
		
		//chart.exporting.menu = new am4core.ExportMenu();


    }

}