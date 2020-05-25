from IPython.display import display, HTML, Javascript
import uuid
import pandas as pd
import numpy as np

def injectJS(js,cellLevel = True):
    if cellLevel:
        display(Javascript(js))
    else:
        display(HTML('<script>'+js+'</script>'))
        
def injectHTML(html):
    display(HTML(html))


class BaseChart():
    chartID   = "chart"
    js        = "var params = {};"
    title     = None
    
    def __init__(self,height=500, data = None, title=None):
        self.chartID = f"chartdiv_{str(uuid.uuid1())}"
        self.height    = height
        if data:
            self.setData(data)
        if title:
            self.setTitle(title)
        
    def setTitle(self,title):
        self.title = title
    
    def setHeight(self, height):
        self.height = height
        
    def setData(self, data):
        self.data = data
        
    def plot(self):
        
        if not self.callbackFn:
            return
        
        injectJS(f'''element.append('<div id="{self.chartID}" class="am4jy" style="height: {self.height}px;"></div>');''')
        injectJS(f'''$('#{self.chartID}').parent("div").css("width","100%").css("overflow-y","hidden");''')
        
        if self.title:
            self.js += '''
        params['title'] = "'''+str(self.title)+'''";
            '''
        
        self.js += '''
        
        params['data'] = ''' + str(self.data) + ''';
        
        require(['amchart_core', 'amchart_charts', 'amcharts4/themes/animated'], '''+self.callbackFn+'''(params,"'''+self.chartID+'''") , function (err) {
            console.log(err);
        });
        '''
        
        injectJS(self.js)

class PieSeries(BaseChart):
    callbackFn = "pieCallback"
    
    def __init__(self,height=500, data = None, title=None):
        super().__init__(height,data,title=title)
        
    def setData(self, col_x=None, col_y=None, asIt = False):
        if asIt:
            self.data = asIt
        else:
            self.data = [{'x':x,'y':y} for x,y in zip(col_x,col_y)]
        
class ColumnChart(BaseChart):
    callbackFn = "columnCallback"
    len_y      = 1
    
    def __init__(self,height=500, data = None, title=None):
        super().__init__(height,data,title=title)
        
    def fromSeries(self,col_x,col_y):
        self.data = [{'x':x,'y':y} for x,y in list(zip(col_x,col_y))]
        
    def fromDataFrame(self,df,col_x="x",col_y="y",hue=None,hue_prefix=""):
        
        df.fillna(0,inplace=True) # TODO: creare funzione di cleaning
        
        cols_y   = [col_y] if isinstance(col_y, str) else col_y
        self.len_y = len(cols_y)
        
        sort_by = [col_x]
        if hue:
            sort_by = sort_by+[hue]
        
        df.sort_values(by=sort_by,inplace=True)
        if not hue:
            self.fromSeries(df[col_x],df[cols_y[0]]) # TODO: accettare uno stack anche in fromSeries 
            return
        
        data = [{} for i in df[col_x].unique()]
        for current_hue in df[hue].unique():
            df_hue = (df[df[hue]==current_hue])
            for col_y in cols_y:
                
                hue_name = str(hue_prefix) + str(current_hue)
                if len(cols_y) > 1:
                    hue_name += " - " + col_y
                
                current_extract = [{'x':x,hue_name:y} for x,y in list(zip(df_hue[col_x],df_hue[col_y]))]
                data = [{**data[i],**current_extract[i]} for i,v in enumerate(current_extract)]
            
        self.data = data
        
    def plot(self):
        
        hues = self.data[0].copy()
        del hues['x']
        hues = list(hues.keys())
        series = {v:int(self.len_y!=1 and bool(i%self.len_y)) for i,v in enumerate(hues,self.len_y)}
        
        self.js += f'''
        params['series'] = {str(series)};
        '''
        
        super().plot()

        
class LineDateChart(BaseChart):
    callbackFn = "lineDateCallback"
    
    def __init__(self,height=500, data = None, title=None):
        super().__init__(height,data,title=title)
        
    def fromDataFrame(self,df,col_x="x",col_y="y",hue=None,hue_prefix=""):
        data = [{} for i in df[col_x].unique()]
        for current_hue in df[hue].unique():
            df_hue = (df[df[hue]==current_hue])
            hue_name = str(hue_prefix) + str(current_hue)
            current_extract = [
                {'x':f"new Date({time.mktime(x.timetuple())*100})",hue_name:y} 
                    for x,y in list(zip(df_hue[col_x],df_hue[col_y]))
            ]
            data = [{**data[i],**current_extract[i]} for i,v in enumerate(current_extract)]
            
        self.data = data
        
    def plot(self):
        
        hues = self.data[0].copy()
        del hues['x']
        hues = list(hues.keys())
        
        self.js += f'''
        params['series'] = {str(hues)};
        '''
        
        super().plot()

class Amchart():
    def __init__(self):        
        js = """         
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
        
        
        function columnCallback(params,canvasID){
            
            return function(am4core, am4charts, am4themes_animated) {
            
                var data   = params['data'];
                var series = params['series'];
                var cTitle = params['title'];
                
                /* Chart code */
                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end




                let chart = am4core.create(canvasID, am4charts.XYChart)
                chart.colors.step = 5;
                
                if(cTitle){
                    let title = chart.titles.create();
                    title.text = cTitle;
                    title.fontSize = 18;
                    title.marginBottom = 10;
                }

                chart.legend = new am4charts.Legend()
                chart.legend.position = 'bottom'
                chart.legend.paddingBottom = 20
                chart.legend.maxWidth = undefined;
                chart.legend.valueLabels.template.align = "center";
                chart.legend.valueLabels.template.textAlign = "center"; 

                let xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
                xAxis.dataFields.category = 'x'
                xAxis.renderer.cellStartLocation = 0.1
                xAxis.renderer.cellEndLocation = 0.9
                xAxis.renderer.grid.template.location = 0;
                xAxis.renderer.minGridDistance = 1;
                
                let label = xAxis.renderer.labels.template;
                label.wrap = true;
                label.maxWidth = 120;

                let yAxis = chart.yAxes.push(new am4charts.ValueAxis());
                yAxis.min = 0;

                function createSeries(value, name,stacked=false) {
                    let series = chart.series.push(new am4charts.ColumnSeries())
                    series.name = name
                    series.dataFields.valueY = value
                    series.dataFields.categoryX = 'x'
                    
                    series.stacked = stacked;
                    //series.columns.template.width = am4core.percent(95);
                    
                    series.columns.template.tooltipText = "{name}: [bold]{valueY}[/]";
                    series.tooltip.pointerOrientation = "down";
                    series.columns.template.tooltipY = -3;

                    var bullet = false

                    if(bullet) {
                        let bullet = series.bullets.push(new am4charts.LabelBullet())
                        bullet.dy = -10;
                        bullet.label.text = '{valueY}'
                        bullet.label.truncate = false;
                        bullet.label.fill = am4core.color('#000000')
                    }                    

                    return series;
                }

                chart.data = data
                
                Object.keys(series).forEach(function(key, index) {
                    createSeries(key, key,this[key])
                }, series);
            }
        
        }
        
        
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
        
        
        """
        injectJS(js,cellLevel = False)

    def PieSeries(self):
        return PieSeries()

    def ColumnChart(self):
        return ColumnChart()
        