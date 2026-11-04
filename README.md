# Sheet & Flow Rental Property Calculator (WordPress plugin)

Free WordPress plugin that adds two investment calculators to any page with a shortcode:

- `[sheetflow_rental_calculator]` – long-term rental: cash flow, cash-on-cash, cap rate, NOI, DSCR, P&I, GRM, 1% rule, break-even occupancy.
- `[sheetflow_str_calculator]` – short-term / vacation rental: cash flow, RevPAR, cash-on-cash, cap rate, DSCR, break-even occupancy and nightly rate.

All math runs in the visitor's browser. No tracking, no cookies, no external requests.

## Install

Download `sheet-flow-rental-property-calculator.zip` from the [latest release](../../releases/latest), then in WordPress: Plugins > Add New > Upload Plugin. Activate and add a shortcode to a page.

Starting values can be set per page, e.g. `[sheetflow_rental_calculator price="325000" rent="2400" taxes="4200"]` (percent fields take percents: `downpct="20"`).

## Tests

- `node tests/crosscheck.test.js` – 4,000 random cases checked against the [Sheet & Flow Landlord Math MCP server](https://github.com/onyxaholguin-cyber/sheet-and-flow-mcp) formulas, plus fixed checks.
- `php tests/render_test.php` – shortcode output, attribute handling and escaping, with minimal WordPress stubs.
- CI also runs the official WordPress Plugin Check.

Year-one estimates for education only, not financial advice.

## License

GPLv2 or later. Made by [Sheet & Flow](https://onyxaholguin-cyber.github.io/sheet-and-flow/).
