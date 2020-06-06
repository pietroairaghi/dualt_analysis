from pandas.core.dtypes.common import is_numeric_dtype
from .XYChart import XYChart


class ColumnChart(XYChart):
    callbackFn = "columnCallback"

