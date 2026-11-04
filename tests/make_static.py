# Build a static page that mirrors sfrc_render() output, from the PHP field arrays, for a browser smoke test.
import re, html, pathlib, sys
src = pathlib.Path(__file__).parent.parent / 'sheet-flow-rental-property-calculator' / 'sheet-flow-rental-property-calculator.php'
php = src.read_text()
str_part, ltr_part = php.split("return array(", 2)[1], php.split("return array(", 2)[2]
pat = re.compile(r"'(\w+)'\s*=>\s*array\( __\( '([^']+)'[^)]*\), ([\d.]+), '([\d.]+)' \)")
def form(kind, part):
    rows = pat.findall(part)
    out = [f'<div class="sfrc-calculator" data-kind="{kind}"><h3>{kind}</h3><form novalidate><div class="sfrc-grid">']
    for k, label, d, step in rows:
        out.append(f'<label>{html.escape(label)}<input type="number" min="0" step="{step}" name="{k}" value="{d}"></label>')
    out.append('</div></form><table class="sfrc-results"><tbody></tbody></table></div>')
    return ''.join(out), len(rows)
a, na = form('ltr', ltr_part); b, nb = form('str', str_part)
base = 'file://' + str(src.parent / 'assets') + '/'
page = f'<!doctype html><html><head><link rel="stylesheet" href="{base}style.css"></head><body>{a}{b}<script src="{base}calc.js"></script><script src="{base}ui.js"></script></body></html>'
pathlib.Path(sys.argv[1]).write_text(page)
print('fields', na, nb)
