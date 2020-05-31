from pandas.core.dtypes.common import is_numeric_dtype
from .BaseChart import BaseChart


class ColumnChart(BaseChart):
    callbackFn = "columnCallback"
    len_y = 1

    def __init__(self, height=500, data=None, title=None):
        super().__init__(height, data, title=title)

    def _multipleX(self, df, cols_x):
        df[self.COL_X] = ""

        if len(cols_x) == 1:
            df[self.COL_X] = df[cols_x[0]]
            return

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

        range_divider = [0]*len(df[divider_col])

        last = ""
        for i,val in enumerate(df[divider_col]):
            if val == last or i == 0:
                last = val
                continue
            elif val != last:
                last = val
                range_divider[i] = 1

        self.params['range_divider'] = range_divider

    def fromSeries(self, col_x, col_y, y_name='y'):
        data = [{'x': x, y_name: y} for x, y in list(zip(col_x, col_y))]
        self.setData(data)
        return data

    def fromDataFrame(self, df, col_x="x", col_y="y", hue=None, hue_prefix="", **configs):

        cols_x = [col_x] if isinstance(col_x, str) else col_x
        cols_y = [col_y] if isinstance(col_y, str) else col_y
        self.len_y = len(cols_y)

        # TODO: extract only necessary columns to avoid performance problems

        df.fillna(0, inplace=True)  # TODO: create a cleaning function

        df = self._groupby(df, configs.get('group_by'))

        # multiple x cols
        self._multipleX(df, cols_x)

        self._sort_values(df, hue, configs.get('sorter'))

        data = [{} for i in df[self.COL_X].unique()]
        if hue:
            for current_hue in df[hue].unique():
                df_hue = (df[df[hue] == current_hue])
                for col_y in cols_y:

                    hue_name = str(hue_prefix) + str(current_hue)
                    if len(cols_y) > 1:
                        hue_name += " - " + col_y

                    current_extract = self.fromSeries(df_hue[self.COL_X], df_hue[col_y], hue_name)
                    data = [{**data[i], **current_extract[i]} for i, v in enumerate(current_extract)]
        else:
            for col_y in cols_y:
                hue_name = str(col_y)
                current_extract = self.fromSeries(df[self.COL_X], df[col_y], hue_name)
                try:
                    data = [{**data[i], **current_extract[i]} for i, v in enumerate(current_extract)]
                except IndexError as e:
                    raise type(e)('Please, consider to user a groupby parameter').with_traceback(e.__traceback__)

        self._calc_divider(df, configs.get('range_divider'))

        self.showLegend(configs.get('show_legend', True))

        self.setData(data)

    def plot(self):

        hues = self.data[0].copy()
        del hues['x']
        hues = list(hues.keys())
        series = {v: int(self.len_y != 1 and bool(i % self.len_y)) for i, v in enumerate(hues, self.len_y)}

        self.params['stacks'] = self.len_y
        self.params['series'] = series

        super().plot()
