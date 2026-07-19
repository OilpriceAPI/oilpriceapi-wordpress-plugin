=== OilPriceAPI Fuel Widgets ===
Contributors: oilpriceapi
Tags: diesel price, gasoline price, fuel surcharge, eia, widget
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.1.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Source-dated weekly U.S. diesel and gasoline price widgets using public-domain EIA retail-fuel series.

== Description ==

OilPriceAPI Fuel Widgets adds source-dated U.S. retail-fuel data to WordPress as shortcodes or a Gutenberg block.

= Fuel Price Ticker =

National U.S. diesel and regular gasoline averages. Each result shows its EIA observation week, weekly cadence, unit, and source.

= U.S. Diesel Price =

The national U.S. diesel retail average, quoted in USD per gallon and labeled with its observation week.

= Fuel Surcharge Calculator =

Calculates the percentage by which the weekly national diesel average exceeds a base price configured by the site owner. It does not describe the result as an industry standard or a contractual surcharge.

= Features =

* Source date, weekly cadence, currency, unit, and EIA attribution in every successful widget
* Dark and light themes
* Shortcodes and Gutenberg block
* Server-side requests with no remote executable code
* One-hour validated cache
* Last successful result for up to 48 hours after an error, visibly labeled as a cached copy
* Fail-closed handling for unavailable, rate-limited, timed-out, empty, or malformed responses
* No API key required for the public EIA series

== External service ==

This plugin uses OilPriceAPI to retrieve the EIA retail-fuel series displayed by the widgets.

The WordPress server sends a GET request to `https://www.oilpriceapi.com/api/widgets/fuel-prices` at most once per hour while a page containing a widget is requested. OilPriceAPI receives the site server's IP address and standard HTTP request metadata. The plugin does not send the visitor's IP address, WordPress user data, page content, or an API credential.

The underlying retail-fuel observations come from the [U.S. Energy Information Administration](https://www.eia.gov/petroleum/gasdiesel/) and are public domain at the source. OilPriceAPI delivers and validates the response; it does not grant rights in underlying data.

* [OilPriceAPI privacy policy](https://www.oilpriceapi.com/privacy)
* [OilPriceAPI terms](https://www.oilpriceapi.com/terms)
* [OilPriceAPI service status](https://status.oilpriceapi.com/)

== Installation ==

1. Upload the `oilpriceapi-widgets` folder to `/wp-content/plugins/`.
2. Activate **OilPriceAPI Fuel Widgets** through the Plugins screen.
3. Add an OilPriceAPI block or one of the shortcodes below to a page.

= Quick Start =

* `[oilpriceapi_ticker]` - weekly national diesel and gasoline prices
* `[oilpriceapi_diesel]` - weekly national diesel price
* `[oilpriceapi_fuel_surcharge base_price="2.50"]` - percentage above the configured base

Configure default theme, fuels, and base price under **Settings > OilPriceAPI**.

== Frequently Asked Questions ==

= Do I need an API key? =

No. These widgets use a public OilPriceAPI route restricted to two EIA public-domain retail-fuel series.

= How current is the data? =

EIA publishes these retail-fuel series weekly. Every successful widget shows the source observation as `Week of` followed by a date and says `Updated weekly`.

= What happens if the service is unavailable? =

The plugin can show its last successful response for up to 48 hours. That state is visibly labeled `Cached copy; service temporarily unavailable`. After 48 hours, the price is removed and the widget links to the service status page.

= What does the plugin cache? =

Only the validated fuel-price payload, its EIA source date, and the time it was retrieved. The fresh cache lasts one hour. The last-success cache lasts no more than 48 hours.

= Can I display crude oil or natural gas prices? =

Not with this public widget. OilPriceAPI does not hold downstream display or redistribution rights for the crude and natural-gas inputs previously shown by the ticker. Review the [data-use guidance](https://www.oilpriceapi.com/docs/redistribution) before publishing API data to third parties.

= What happened to the carbon calculator and regional diesel display? =

Version 1.1 retired the carbon calculator's uncleared crude-price input and removed unsupported regional diesel output. An existing carbon shortcode shows a retirement notice instead of silently displaying a fabricated or uncleared value.

= Where can developers get programmatic access? =

See the [API documentation](https://docs.oilpriceapi.com/) and the official [PHP SDK](https://packagist.org/packages/oilpriceapi/oilpriceapi).

== Screenshots ==

1. Fuel Price Ticker with weekly national diesel and gasoline prices, source date, and EIA attribution
2. U.S. Diesel Price widget with national average and observation week
3. Fuel Surcharge Calculator showing the configured base and percentage above base
4. Gutenberg block controls for widget type, theme, fuel selection, layout, and base price

== Changelog ==

= 1.1.1 =

* Matched the package folder and text domain to the approved WordPress.org directory slug `oilpriceapi-widgets`

= 1.1.0 =

* Replaced crude and natural-gas public display with allowlisted EIA diesel and gasoline series
* Added visible source date, weekly cadence, unit, and EIA attribution
* Moved rendering server-side and removed remote executable widget scripts
* Added strict response validation and fail-closed recovery states
* Added one-hour fresh caching and an explicitly labeled 48-hour last-success fallback
* Retired the carbon widget's uncleared crude-price dependency
* Removed unsupported regional diesel output and unsupported marketing claims
* Added deterministic failure-path tests and CI

= 1.0.1 =

* Confirmed WordPress compatibility and expanded directory documentation

= 1.0.0 =

* Initial plugin release

== Upgrade Notice ==

= 1.1.1 =

This directory-compatible source-accuracy release replaces previously displayed crude and natural-gas data with source-dated weekly EIA retail-fuel series. Existing ticker shortcodes continue to render; existing carbon shortcodes show a retirement notice.
