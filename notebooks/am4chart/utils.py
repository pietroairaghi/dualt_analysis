from IPython.display import display, HTML, Javascript


def inject_js(js, cell_level=True):
    if cell_level:
        display(Javascript(js))
    else:
        display(HTML('<script>' + js + '</script>'))


def inject_html(html):
    display(HTML(html))
