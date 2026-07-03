# OilPriceAPI Widgets — WordPress Plugin

[![WordPress](https://img.shields.io/badge/WordPress-6.0%E2%80%937.0-21759b)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-GPL%20v2-blue)](LICENSE)

Embed live oil, diesel, and natural gas prices on any WordPress site. Four widgets, available as shortcodes or a Gutenberg block. **No API key or account required.**

| Widget                    | Shortcode                      | What it shows                                               |
| ------------------------- | ------------------------------ | ----------------------------------------------------------- |
| Oil Price Ticker          | `[oilpriceapi_ticker]`         | Live Brent, WTI, and Natural Gas prices with 24h change     |
| Diesel Price Tracker      | `[oilpriceapi_diesel]`         | U.S. national + regional retail diesel prices (EIA, weekly) |
| Fuel Surcharge Calculator | `[oilpriceapi_fuel_surcharge]` | Industry-standard surcharge from current diesel price       |
| Carbon Cost Calculator    | `[oilpriceapi_carbon]`         | CO2 emissions and carbon cost from EPA emission factors     |

## Installation

1. Download this repo as a zip (or clone it) and upload the folder to `/wp-content/plugins/oilpriceapi-widget/`
2. Activate the plugin through the WordPress Plugins menu
3. Drop a shortcode into any post/page, or add the "OilPriceAPI" Gutenberg block

Defaults (theme, commodities, base fuel price, carbon price) are configurable under **Settings > OilPriceAPI** and can be overridden per shortcode.

## How it works

The plugin renders a container div and enqueues a small (<30KB) async script from `oilpriceapi.com/widgets/`. The scripts fetch prices from OilPriceAPI's public, keyless widget endpoints (`api.oilpriceapi.com/v1/prices/widget`) and refresh every 5 minutes. No data is collected from your site or visitors.

## Need more than widgets?

The full [OilPriceAPI](https://www.oilpriceapi.com/?utm_source=wordpress-plugin&utm_medium=github-readme) offers live and historical prices for crude, refined products, natural gas, and carbon — free tier includes 200 requests/month. Try it in the [interactive API explorer](https://api.oilpriceapi.com/swagger) or read the [docs](https://docs.oilpriceapi.com/?utm_source=wordpress-plugin&utm_medium=github-readme).

### OilPriceAPI ecosystem

| Platform                     | Package                                                                                              |
| ---------------------------- | ---------------------------------------------------------------------------------------------------- |
| Python                       | [`pip install oilpriceapi`](https://pypi.org/project/oilpriceapi/)                                   |
| Node.js / TypeScript         | [`npm install oilpriceapi`](https://www.npmjs.com/package/oilpriceapi)                               |
| PHP                          | [`composer require oilpriceapi/oilpriceapi`](https://packagist.org/packages/oilpriceapi/oilpriceapi) |
| Go                           | [github.com/OilpriceAPI/oilpriceapi-go](https://github.com/OilpriceAPI/oilpriceapi-go)               |
| MCP (Claude / AI assistants) | [github.com/OilpriceAPI/mcp-server](https://github.com/OilpriceAPI/mcp-server)                       |

## Development

Plugin structure:

- `oilpriceapi-widget.php` — plugin bootstrap, block render callback, defaults
- `includes/class-shortcodes.php` — the four shortcodes and script enqueueing
- `admin/class-settings.php` — Settings > OilPriceAPI page
- `blocks/oil-price-widget/` — Gutenberg block
- `readme.txt` — wordpress.org plugin directory readme

Minimum requirements: WordPress 6.0, PHP 7.4.

## License

GPL v2 or later. See [LICENSE](LICENSE).
