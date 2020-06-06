import numbers
from .utils import *
import uuid


class BaseChart:
    chartID = "chart"
    js = "var params = {};"
    title = None
    legend = True
    params = {}
    COL_X = '__am4chart_col_x'
    theme = 'frozen'

    def __init__(self, height=500, data=None, title=None):
        self.params = {}
        self.chartID = f"chartdiv_{str(uuid.uuid1())}"
        self.height = height
        if data:
            self.setData(data)
        if title:
            self.setTitle(title)

    def setTitle(self, title):
        self.title = title
        self.params['title'] = title

    def set_y_label(self, label):
        self.params['y_title'] = label

    def set_x_label(self, label):
        self.params['x_title'] = label

    def x_labels_hide(self, hide=True):
        self.params['hide_x_labels'] = hide

    def y_labels_hide(self, hide=True):
        self.params['hide_y_labels'] = hide

    def showLegend(self, enable=True):
        self.legend = enable
        self.params['legend'] = enable

    def setHeight(self, height):
        self.height = height

    def setData(self, data):
        self.data = data
        self.params['data'] = data

    def _load_params(self):
        for k, v in self.params.items():
            if isinstance(v, str):
                self.js += f'''params['{k}'] = "{v}";'''
            elif v is True:
                self.js += f'''params['{k}'] = true;'''
            elif v is False:
                self.js += f'''params['{k}'] = false;'''
            elif isinstance(v, numbers.Number):
                self.js += f'''params['{k}'] = {v};'''
            elif v:
                self.js += f'''params['{k}'] = {str(v)};'''

    def _groupby(self, df, group_by=None):
        if not group_by:
            return df

        fn = "mean"
        if isinstance(group_by, str):
            col = group_by
        else:
            col = group_by['column']
            fn = group_by['function']

        return getattr(df.groupby(col, as_index=False, axis=0), fn)()

    def _sort_values(self, df, hue=None, sorter="default"):

        if sorter == 'default' or not sorter:
            sort_by = [self.COL_X]
            if hue:
                sort_by += [hue]
        else:
            sort_by = sorter

        df.sort_values(by=sort_by, inplace=True)

    def plot(self):

        if not self.callbackFn:
            return

        inject_js(f'''element.append('<div id="{self.chartID}" style="height: {self.height}px;"></div>');''')
        inject_js(f'''$('#{self.chartID}').parent("div").css("width","100%").css("overflow-y","hidden");''')

        self._load_params()

        if self.theme:
            requires = f'''['amchart_core', 'amchart_charts', 'amcharts4/themes/animated', 'amcharts4/themes/{self.theme}']'''
        else:
            requires = f'''['amchart_core', 'amchart_charts', 'amcharts4/themes/animated']'''

        self.js += f'''
        require({requires}, ''' + self.callbackFn + '''(params,"''' + self.chartID + '''") , function (err) {
            console.log(err);
        });
        '''

        inject_js(self.js)
