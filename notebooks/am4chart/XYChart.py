from pandas.core.dtypes.common import is_numeric_dtype
from .BaseChart import BaseChart


class XYChart(BaseChart):
    callbackFn = "XYCallback"
    len_y = 1

    def __init__(self, height=500, data=None, title=None):
        super().__init__(height, data, title=title)

    def setData(self, data):
        super().setData(data)
        self._calc_x_vals(data)
        self._calc_series()

    def _multipleX(self, df, cols_x):
        df[self.COL_X] = ""

        if len(cols_x) == 1:
            df[self.COL_X] = df[cols_x[0]]
            self.is_multi_x = False
            return

        self.is_multi_x = True
        self.params['is_category'] = 1

        for i, col in enumerate(cols_x, 1):
            prefix = "_"
            if i == 1:
                prefix = ""

            if is_numeric_dtype(df[col]):
                tmp_col = df[col].astype(str)
                max_len = tmp_col.str.len().max()
                df[self.COL_X] += prefix + tmp_col.str.zfill(max_len)
            else:
                df[self.COL_X] += prefix + df[col].astype(str)

    def _calc_divider(self, df, divider_col=None):
        if not divider_col:
            return

        range_divider = [0] * len(df[divider_col])

        last = ""
        for i, val in enumerate(df[divider_col]):
            if val == last or i == 0:
                last = val
                continue
            elif val != last:
                last = val
                range_divider[i] = 1

        self.params['range_divider'] = range_divider

    def _calc_names_map(self, df, names_col=None):
        if not names_col:
            self.params['x_names_map'] = 0
            return

        # create map
        self.params['x_names_map'] = df[[self.COL_X, names_col]].astype(str).set_index(self.COL_X).to_dict()[names_col]

    def _calc_x_vals(self, data):
        x_vals = []
        for serie in data:
            for i, v in serie.items():
                if i == 'x':
                    x_vals += [v]
        self.params['x_vals'] = x_vals
        return x_vals
        
    def _patch_log_amchart_problem(self):
        x_vals = []
        for serie in self.data:
            for i, v in serie.items():
                if i != 'x' and v == 0:
                    serie[i] = 0.00000001

    def _calc_series(self):
        if self.separate_series:
            hues = []
            for serie in self.data:
                for i, _ in serie.items():
                    if i != 'x':
                        hues += [i]
            hues = list(dict.fromkeys(hues))
        else:
            hues = self.data[0].copy()
            del hues['x']
            hues = list(hues.keys())

        series = {v: int(self.len_y != 1 and bool(i % self.len_y)) for i, v in enumerate(hues, self.len_y)}

        self.params['stacks'] = self.len_y
        self.params['series'] = series

    def is_category(self, is_category=True):
        self.params['is_category'] = 1 if is_category else 0

    def fromSeries(self, col_x, col_y, y_name='y'):
        data = [{'x': x, y_name: y} for x, y in list(zip(col_x, col_y))]
        self.setData(data)
        return data

    def fromDataFrame(self, df, col_x="x", col_y="y", hue=None, hue_prefix="", **configs):

        cols_x = [col_x] if isinstance(col_x, str) else col_x
        cols_y = [col_y] if isinstance(col_y, str) else col_y
        self.len_y = len(cols_y)

        # TODO: extract only necessary columns to avoid performance problems

        df = df.fillna(0)  # TODO: create a cleaning function

        df = self._groupby(df, configs.get('group_by'))

        # multiple x cols
        self._multipleX(df, cols_x)

        self._sort_values(df, hue, configs.get('sorter'))

        divider_column = configs.get('range_divider')

        self.separate_series = False
        if hue and (configs.get('separate_series') or divider_column == hue):
            self.separate_series = True

        data = []
        all_xs = df[self.COL_X].unique()
        if not self.separate_series:
            data = [{} for i in all_xs]
        if hue:
            hues = df[hue].unique().tolist()
            self.params['hues'] = hues
            for current_hue in hues:
                df_hue = (df[df[hue] == current_hue])
                for col_y in cols_y:

                    hue_name = str(hue_prefix) + str(current_hue)
                    if len(cols_y) > 1:
                        hue_name += " - " + col_y

                    current_extract = self.fromSeries(df_hue[self.COL_X], df_hue[col_y], hue_name)
                            
                    if self.separate_series:
                        data += current_extract
                    else:
                        extracted_xs = [ sub['x'] for sub in current_extract ]
                        for x_index, check_x in enumerate(all_xs):
                            if not check_x in extracted_xs:
                                current_extract.insert(x_index,{'x': check_x, hue_name: 0})
                        data = [{**data[i], **current_extract[i]} for i, v in enumerate(current_extract)]
        else:
            for col_y in cols_y:
                hue_name = str(col_y)
                current_extract = self.fromSeries(df[self.COL_X], df[col_y], hue_name)
                try:
                    data = [{**data[i], **current_extract[i]} for i, v in enumerate(current_extract)]
                except IndexError as e:
                    raise type(e)('Please, consider to user a groupby parameter').with_traceback(e.__traceback__)

        self._calc_divider(df, divider_column)
        self._calc_names_map(df, configs.get('x_axis_names'))
        self.x_labels_hide(configs.get('hide_x_labels'))  # TODO: create config function (and not all in from DF)
        self.y_labels_hide(configs.get('hide_y_labels'))
        self.params['visible_labels'] = configs.get('visible_labels')
        self.params['log_scale'] = configs.get('log_scale')  # TODO: create function to set logarithmic scale by checking minimum value and set zeroes to 1/10 of the latest
        self.params['min_y'] = configs.get('min_y')
        self.params['max_y'] = configs.get('max_y')

        self.showLegend(configs.get('show_legend', True))

        self.setData(data)
        
        if self.params['log_scale']:
            self._patch_log_amchart_problem()

    def plot(self):
        super().plot()
