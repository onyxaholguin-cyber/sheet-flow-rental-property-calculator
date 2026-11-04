=== Sheet & Flow Rental Property Calculator ===
Contributors: sheetandflow
Tags: rental property calculator, real estate, cap rate, cash flow, airbnb calculator
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Rental property and vacation rental calculators for any page: cash flow, cap rate, cash-on-cash return, DSCR, NOI and break-even occupancy.

== Description ==

Give your visitors a real investment-property calculator instead of a plain mortgage calculator. Built for real estate agents, investor blogs, property managers and short-term rental hosts.

**Two shortcodes:**

* `[sheetflow_rental_calculator]` – long-term rental: monthly and annual cash flow, cash-on-cash return, cap rate, NOI, DSCR, loan payment (P&I), total cash invested, expense ratio, gross rent multiplier, rent-to-price (the "1% rule") and break-even occupancy. Multi-unit ready.
* `[sheetflow_str_calculator]` – short-term / vacation rental (Airbnb, VRBO style): cash flow, gross revenue, RevPAR, cash-on-cash, cap rate, DSCR, break-even occupancy and break-even nightly rate, with cleaning fees, platform fees and owner-blocked nights.

**Features**

* Results update as the visitor types.
* Set your own starting values per page: `[sheetflow_rental_calculator price="325000" rent="2400" taxes="4200" title="Run the numbers on this listing"]`. Every input name works as an attribute (lowercase), e.g. `downpct`, `vacancypct`, `mgmtpct`, `adr`, `occupancy`.
* Private: all math runs in the visitor's browser. No tracking, no cookies, no external requests, no data stored.
* Lightweight: about 10 KB of plain JavaScript and CSS, loaded only on pages that use a shortcode.
* No branding on your pages unless you switch on the optional credit link in Settings > Rental Calculator.

**How the numbers are calculated**

* Loan payment: standard amortization, P&I only.
* Vacancy is a share of all scheduled income; repairs and CapEx reserves are a share of scheduled rent; management is a share of collected income.
* NOI = effective gross income − operating expenses (no debt service). Cap rate = NOI ÷ price. Cash-on-cash = annual cash flow ÷ (down payment + closing + up-front repairs + points). DSCR = NOI ÷ annual debt service.
* Short-term rental break-even occupancy is the occupancy where cash flow is zero at the nightly rate you enter.

The formulas are the same ones used in the open-source Sheet & Flow Landlord Math tools and are cross-checked against them by an automated test (thousands of random cases).

Results are year-one estimates for education only, not financial, tax or legal advice.

== Installation ==

1. Install from Plugins > Add New (search "Sheet & Flow Rental Property Calculator"), or upload the zip.
2. Activate.
3. Add `[sheetflow_rental_calculator]` or `[sheetflow_str_calculator]` to a page with the Shortcode block.

== Frequently Asked Questions ==

= Does it send visitor data anywhere? =

No. The calculator runs entirely in the browser and makes no network requests.

= Can I change the default numbers? =

Yes, with shortcode attributes. Percent fields take percents: `downpct="20"` means 20%.

= Does it include taxes and depreciation? =

No. It is a pre-tax, year-one view. Sheet & Flow sells spreadsheet versions with multi-year projections and IRR (links on the settings page), but the plugin itself is complete and free.

== Screenshots ==

1. Long-term rental calculator.
2. Short-term rental calculator.

== Changelog ==

= 1.0.0 =
* First release: long-term and short-term rental calculators.
