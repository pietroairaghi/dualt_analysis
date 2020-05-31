function columnCallback(params, canvasID) {

    return function (am4core, am4charts, am4themes_animated) {

        let legend = params['legend'];
        let data   = params['data'];
        let series = params['series'];
        let stacks = params['stacks'];

        if (stacks == 1){
            stacks = 5 // to differentiate colors
        }

        if (legend === undefined) {
            legend = true;
        }

        /* Chart code */
        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end


        let chart = am4core.create(canvasID, am4charts.XYChart);

        chart.colors.step =  Math.ceil(20/stacks);

        if (params['title']) {
            let title = chart.titles.create();
            title.text = params['title'];
            title.fontSize = 18;
            title.marginBottom = 10;
        }

        if (legend) {
            chart.legend = new am4charts.Legend();
            chart.legend.position = 'bottom';
            chart.legend.paddingBottom = 20;
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

        let yAxis = chart.yAxes.push(new am4charts.ValueAxis());
        yAxis.min = 0;

        function createSeries(value, name, stacked=false) {
            let series = chart.series.push(new am4charts.ColumnSeries());
            series.name = name;
            series.dataFields.valueY = value;
            series.dataFields.categoryX = 'x';

            series.stacked = stacked;

            series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
            series.tooltip.pointerOrientation = "down";
            series.columns.template.tooltipY = -3;

            let bullet = false;

            if (bullet) {
                let bullet = series.bullets.push(new am4charts.LabelBullet());
                bullet.dy = -10;
                bullet.label.text = '{valueY}';
                bullet.label.truncate = false;
                bullet.label.fill = am4core.color('#000000')
            }

            return series;
        }

        chart.data = data;

        Object.keys(series).forEach(function (key, index) {
            createSeries(key, key, this[key])
        }, series);

        // check for dividers
        if(params['range_divider']) {

            var startCategory = data[0]['x'];
            colorIndex = 0;
            for (index = 0; index < params['range_divider'].length; ++index) {
                if(params['range_divider'][index+1] || (index+1) == params['range_divider'].length){
                    var endCategory = data[index]['x'];

                    var range = xAxis.axisRanges.create();
                    range.category = startCategory;
                    range.endCategory = endCategory;
                    range.axisFill.fill = chart.colors.getIndex(colorIndex*4);
                    range.axisFill.fillOpacity = 0.3;
                    range.label.disabled = true;


                    startCategory = data[index+1]['x'];
                    colorIndex++;
                }
            }
        }

        if(params['range_divider']){
            console.log(data);
        }


    }

}