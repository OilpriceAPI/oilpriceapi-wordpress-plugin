# OilPriceAPI Fuel Widgets for WordPress

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-GPL%20v2-blue)](LICENSE)

Embed source-dated weekly U.S. diesel and gasoline prices in WordPress. The data is delivered by OilPriceAPI from U.S. Energy Information Administration (EIA) retail-fuel series and is labeled with its observation week and native weekly cadence.

| Widget | Shortcode | Output |
| --- | --- | --- |
| Fuel Price Ticker | `[oilpriceapi_ticker]` | National diesel and gasoline averages |
| U.S. Diesel Price | `[oilpriceapi_diesel]` | National diesel average |
| Fuel Surcharge Calculator | `[oilpriceapi_fuel_surcharge]` | Percentage above a configured diesel base price |

The same widgets are available in the Gutenberg block editor. Dark and light themes are included.

## Installation

1. Upload the plugin directory to `/wp-content/plugins/oilpriceapi-widget/`.
2. Activate **OilPriceAPI Fuel Widgets** in the WordPress Plugins screen.
3. Add an OilPriceAPI block or one of the shortcodes above to a page.

Defaults can be changed under **Settings > OilPriceAPI**. No API key is required for these public EIA series.

## Data behavior

- Source: [U.S. Energy Information Administration](https://www.eia.gov/petroleum/gasdiesel/) (public domain at the source).
- Cadence: weekly. Every successful widget includes the exact `Week of` date.
- Refresh: the plugin requests a validated result at most once per hour.
- Recovery: after an upstream error, a last successful result can be shown for up to 48 hours and is visibly labeled **Cached copy**. Older data is not rendered.
- Validation: only `DIESEL_RETAIL_USD` and `GASOLINE_RETAIL_USD` with their expected EIA series IDs, currency, unit, and schema can render.

The plugin calls `https://www.oilpriceapi.com/api/widgets/fuel-prices` from the WordPress server. That request exposes the site server's IP address and standard HTTP request metadata to OilPriceAPI. It does not send the visitor's IP address, WordPress user data, page content, or an API credential. See the [OilPriceAPI privacy policy](https://www.oilpriceapi.com/privacy) and [terms](https://www.oilpriceapi.com/terms).

## Version 1.1 migration

Version 1.1 removes public display of crude and natural-gas series because OilPriceAPI does not hold downstream redistribution rights for those inputs. Existing ticker shortcodes remain functional and now render the allowlisted EIA fuel series. The legacy carbon calculator renders a retirement notice instead of an uncleared crude-price input, and the diesel widget now shows the EIA national average rather than unsupported regional data.

## Development

Run the deterministic integration suite and syntax checks:

```bash
php tests/run.php
find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l
```

The suite covers valid data, fresh caching, 401/403/429/500 responses, timeout, malformed and empty payloads, an encumbered series, explicit stale rendering, cache expiry, and the public recovery state.

Build the release archive, then run a clean WordPress activation and the official Plugin Check CLI against the packaged `oilpriceapi-widget` slug:

```bash
npx --yes @wordpress/env stop
./scripts/build-release.sh
npx --yes @wordpress/env start
npx --yes @wordpress/env run cli wp plugin activate oilpriceapi-widget
npx --yes @wordpress/env run cli wp plugin check oilpriceapi-widget
npx --yes @wordpress/env stop
```

## Related clients

- [API documentation](https://docs.oilpriceapi.com/)
- [PHP SDK](https://packagist.org/packages/oilpriceapi/oilpriceapi)
- [Python SDK](https://pypi.org/project/oilpriceapi/)
- [Node.js SDK](https://www.npmjs.com/package/oilpriceapi)
- [Go SDK](https://github.com/OilpriceAPI/oilpriceapi-go)
- [MCP server](https://github.com/OilpriceAPI/mcp-server)

## License

GPL v2 or later. See [LICENSE](LICENSE).
