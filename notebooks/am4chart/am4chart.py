import os
from .utils import *
from .BaseChart import BaseChart
from .ColumnChart import ColumnChart
from .LineChart import LineChart

class PieSeries(BaseChart):
    callbackFn = "pieCallback"

    def __init__(self, height=500, data=None, title=None):
        super().__init__(height, data, title=title)

    def setData(self, col_x=None, col_y=None, asIt=False):
        if asIt:
            self.data = asIt
        else:
            data = [{'x': x, 'y': y} for x, y in zip(col_x, col_y)]
            self.setData(data)



class LineDateChart(BaseChart):
    callbackFn = "lineDateCallback"

    def __init__(self, height=500, data=None, title=None):
        super().__init__(height, data, title=title)

    def fromDataFrame(self, df, col_x="x", col_y="y", hue=None, hue_prefix=""):
        data = [{} for i in df[col_x].unique()]
        for current_hue in df[hue].unique():
            df_hue = (df[df[hue] == current_hue])
            hue_name = str(hue_prefix) + str(current_hue)
            current_extract = [
                {'x': f"new Date({time.mktime(x.timetuple())*100})", hue_name: y}
                for x, y in list(zip(df_hue[col_x], df_hue[col_y]))
                ]
            data = [{**data[i], **current_extract[i]} for i, v in enumerate(current_extract)]

        self.setData(data)

    def plot(self):
        hues = self.data[0].copy()
        del hues['x']
        hues = list(hues.keys())

        self.js += f'''
        params['series'] = {str(hues)};
        '''

        super().plot()


class Amchart():
    js_dir = "./js/"
    charts = {}

    def __init__(self):
        js_list = ['load_ES6', 'column_callback', 'line_callback', 'linedate_callback', 'pie_callback']

        js = ""

        for filename in js_list:
            file_path = os.path.join(os.path.dirname(__file__), self.js_dir + filename + ".js")
            f = open(file_path, "r")
            js += f.read()

        inject_js(js, cell_level=False)

    def PieSeries(self):
        chart = PieSeries()
        chartID = chart.chartID

        self.charts[chartID] = chart
        return self.charts[chartID]

    def ColumnChart(self):
        chart = ColumnChart()
        chartID = chart.chartID

        self.charts[chartID] = chart
        return self.charts[chartID]

    def LineChart(self):
        chart = LineChart()
        chartID = chart.chartID

        self.charts[chartID] = chart
        return self.charts[chartID]
