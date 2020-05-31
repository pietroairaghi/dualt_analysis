from pandas.core.dtypes.common import is_numeric_dtype
from .ColumnChart import ColumnChart


class LineChart(ColumnChart):
    callbackFn = "lineCallback"
