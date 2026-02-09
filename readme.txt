=== OilPriceAPI Widgets ===
Contributors: oilpriceapi
Tags: oil price, commodity, widget, diesel, fuel surcharge, carbon calculator, energy
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed live oil prices, diesel prices, fuel surcharge calculator, and carbon cost calculator on your WordPress site. Free, no API key required.

== Description ==

Display real-time commodity prices on your WordPress site with four free embeddable widgets from [OilPriceAPI](https://www.oilpriceapi.com/widgets).

= Oil Price Ticker =
Live Brent Crude, WTI, and Natural Gas prices with change indicators. Available in horizontal or vertical layouts.

= Diesel Price Tracker =
Current U.S. national and regional diesel prices updated weekly from EIA data. Shows East Coast, Midwest, Gulf Coast, Rocky Mountain, West Coast, and California prices.

= Fuel Surcharge Calculator =
Industry-standard fuel surcharge calculation using current diesel prices and a configurable base price. Used by trucking and logistics companies.

= Carbon Cost Calculator =
Calculate carbon costs based on EPA emission factors and configurable carbon pricing. Useful for ESG reporting and sustainability content.

= Features =

* **No API key required** — widgets work immediately after install
* **Dark and light themes** — match your site design
* **Lightweight** — each widget is under 30KB
* **Auto-updating** — prices refresh every 5 minutes
* **Shortcodes and Gutenberg block** — use either method
* **Configurable defaults** — set once in Settings, override per widget
* **Responsive** — works on mobile and desktop

== Installation ==

1. Upload the `oilpriceapi-widget` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins menu
3. Use shortcodes in your posts/pages or add the Gutenberg block

= Quick Start =

Add any of these shortcodes to a post or page:

* `[oilpriceapi_ticker]` — Oil price ticker
* `[oilpriceapi_diesel]` — Diesel prices
* `[oilpriceapi_fuel_surcharge]` — Fuel surcharge calculator
* `[oilpriceapi_carbon]` — Carbon cost calculator

Or search for "OilPriceAPI" in the Gutenberg block inserter.

== Frequently Asked Questions ==

= Do I need an API key? =

No. The widgets are completely free and work without any API key or account. They use the public OilPriceAPI widget endpoints.

= How often do prices update? =

Prices refresh automatically every 5 minutes while the page is open. Oil prices update during market hours (Sunday 5pm ET to Friday 5pm ET). Diesel prices update weekly from EIA data.

= Can I customize the appearance? =

Yes. Each widget supports dark and light themes. The ticker widget also supports horizontal and vertical layouts. Configure defaults under Settings > OilPriceAPI, or override per shortcode.

= Will this slow down my site? =

No. Widget scripts load asynchronously and only on pages where widgets are used. Each script is under 30KB with no external dependencies.

= What commodities are supported? =

The ticker widget supports Brent Crude, WTI Crude, and Natural Gas. Additional commodities are available through the full OilPriceAPI.

= Can I use multiple widgets on the same page? =

Yes. You can place different widget types on the same page. Each widget operates independently.

== Screenshots ==

1. Oil Price Ticker widget (dark theme, horizontal layout)
2. Diesel Price Tracker widget with regional prices
3. Fuel Surcharge Calculator widget
4. Carbon Cost Calculator widget
5. Gutenberg block with sidebar controls
6. Settings page with shortcode reference

== Changelog ==

= 1.0.0 =
* Initial release
* Four widgets: Oil Price Ticker, Diesel Price Tracker, Fuel Surcharge Calculator, Carbon Cost Calculator
* Shortcode support with customizable attributes
* Gutenberg block with sidebar controls
* Settings page with default configuration
* Dark and light theme support

== Upgrade Notice ==

= 1.0.0 =
Initial release with four free commodity price widgets.
