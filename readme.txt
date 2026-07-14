=== OilPriceAPI Widgets ===
Contributors: oilpriceapi
Tags: oil price, diesel, fuel surcharge, commodity prices, widget
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Live oil, diesel, and natural gas price widgets plus fuel surcharge and carbon calculators. No API key or account required.

== Description ==

Give your visitors live energy prices without writing a line of code. OilPriceAPI Widgets adds four production-ready widgets to any WordPress site, as shortcodes or Gutenberg blocks:

= Oil Price Ticker =
Live Brent Crude, WTI, and Natural Gas prices with 24-hour change indicators. Horizontal or vertical layouts, dark or light theme.

= Diesel Price Tracker =
Current U.S. national and regional retail diesel prices — East Coast, Midwest, Gulf Coast, Rocky Mountain, West Coast, and California — updated weekly from EIA data.

= Fuel Surcharge Calculator =
Industry-standard fuel surcharge calculation from the current diesel price and your configurable base price. Used by trucking and logistics companies to publish surcharge schedules.

= Carbon Cost Calculator =
CO2 emissions and carbon cost estimates based on EPA emission factors and a configurable carbon price. Useful for ESG reporting and sustainability content.

All four widgets work immediately after install — **no API key, no account, no configuration required**. Prices come from [OilPriceAPI](https://www.oilpriceapi.com/?utm_source=wordpress-plugin&utm_medium=readme&utm_campaign=description), the commodity price API used by energy, logistics, and fintech teams.

= Features =

* **No API key required** — widgets work immediately after install
* **Dark and light themes** — match your site design
* **Lightweight** — each widget is under 30KB, loaded async, only on pages that use it
* **Auto-updating** — prices refresh every 5 minutes
* **Shortcodes and Gutenberg block** — use either method
* **Configurable defaults** — set once in Settings > OilPriceAPI, override per widget
* **Responsive** — works on mobile and desktop

= For developers =

Need the raw data behind the widgets — historical prices, more commodities, or programmatic access?

* [Get a free API key](https://www.oilpriceapi.com/signup?utm_source=wordpress-plugin&utm_medium=readme&utm_campaign=developers) — the free tier includes 200 requests/month
* PHP SDK: `composer require oilpriceapi/oilpriceapi`
* [API documentation](https://docs.oilpriceapi.com/?utm_source=wordpress-plugin&utm_medium=readme)
* [Interactive API explorer](https://api.oilpriceapi.com/swagger)

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

= Where does the price data come from? =

Oil and natural gas prices are collected from public market sources and delivered through the OilPriceAPI API — we are the delivery route, not the origin. U.S. diesel retail prices are EIA (U.S. Energy Information Administration) series: public domain at the source, so no permission is needed from us, and none is ours to give. Carbon calculations use EPA emission factors. OilPriceAPI grants no rights in any of the underlying data.

= How often do prices update? =

Prices refresh automatically every 5 minutes while the page is open. Oil prices update during market hours (Sunday 5pm ET to Friday 5pm ET). Diesel prices update weekly from EIA data.

= Can I customize the appearance? =

Yes. Each widget supports dark and light themes. The ticker widget also supports horizontal and vertical layouts. Configure defaults under Settings > OilPriceAPI, or override per shortcode.

= Will this slow down my site? =

No. Widget scripts load asynchronously and only on pages where widgets are used. Each script is under 30KB with no external dependencies.

= What commodities are supported? =

The ticker widget supports Brent Crude, WTI Crude, and Natural Gas. Many more commodities (gasoline, heating oil, coal, EU carbon allowances, and others) are available through the full [OilPriceAPI](https://www.oilpriceapi.com/?utm_source=wordpress-plugin&utm_medium=readme&utm_campaign=faq).

= Can I use multiple widgets on the same page? =

Yes. You can place different widget types on the same page. Each widget operates independently.

= I need historical data or programmatic access — what are my options? =

Sign up for a [free API key](https://www.oilpriceapi.com/signup?utm_source=wordpress-plugin&utm_medium=readme&utm_campaign=faq) (200 requests/month included). PHP developers can install the official SDK with `composer require oilpriceapi/oilpriceapi`, and you can try every endpoint in the [interactive API explorer](https://api.oilpriceapi.com/swagger).

== Screenshots ==

1. Oil Price Ticker — configure theme, commodities, and layout with live preview
2. Diesel Price Tracker — national average and 6 regional U.S. diesel prices
3. Fuel Surcharge Calculator — real-time diesel-based surcharge calculation
4. Carbon Cost Calculator — CO2 emissions and carbon cost from oil consumption

== Changelog ==

= 1.0.1 =
* Compatibility: tested up to WordPress 7.0
* Documentation: expanded FAQ, developer resources, and ecosystem links
* No functional changes

= 1.0.0 =
* Initial release
* Four widgets: Oil Price Ticker, Diesel Price Tracker, Fuel Surcharge Calculator, Carbon Cost Calculator
* Shortcode support with customizable attributes
* Gutenberg block with sidebar controls
* Settings page with default configuration
* Dark and light theme support

== Also Available As ==

OilPriceAPI is available across many platforms:

* **Python SDK**: [pypi.org/project/oilpriceapi](https://pypi.org/project/oilpriceapi/) — Python client with Pandas integration
* **Node.js SDK**: [npmjs.com/package/oilpriceapi](https://www.npmjs.com/package/oilpriceapi) — TypeScript/JavaScript SDK
* **PHP SDK**: [packagist.org/packages/oilpriceapi/oilpriceapi](https://packagist.org/packages/oilpriceapi/oilpriceapi) — `composer require oilpriceapi/oilpriceapi`
* **Go SDK**: [github.com/OilpriceAPI/oilpriceapi-go](https://github.com/OilpriceAPI/oilpriceapi-go) — Go client
* **MCP Server**: [github.com/OilpriceAPI/mcp-server](https://github.com/OilpriceAPI/mcp-server) — oil prices in Claude and other AI assistants
* **Google Sheets Add-on**: Custom functions like =OILPRICE("BRENT_CRUDE_USD")
* **Excel Add-in**: Energy price comparison in Excel

Get your free API key at [oilpriceapi.com/signup](https://www.oilpriceapi.com/signup?utm_source=wordpress-plugin&utm_medium=readme&utm_campaign=also-available).

== Upgrade Notice ==

= 1.0.1 =
WordPress 7.0 compatibility confirmation and documentation updates. No functional changes.

= 1.0.0 =
Initial release with four free commodity price widgets.
