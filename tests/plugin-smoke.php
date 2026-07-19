<?php

define( 'OILPRICEAPI_TEST_LOAD_PLUGIN', true );
require __DIR__ . '/bootstrap.php';

$GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, wp_json_encode( oilpriceapi_test_payload() ) );

require dirname( __DIR__ ) . '/oilpriceapi-widget.php';

oilpriceapi_widget_init();
oilpriceapi_widget_register_block();

if ( ! isset( $GLOBALS['oilpriceapi_test_shortcodes']['oilpriceapi_ticker'] ) ) {
    throw new RuntimeException( 'Ticker shortcode was not registered.' );
}

if ( 1 !== count( $GLOBALS['oilpriceapi_test_blocks'] ) ) {
    throw new RuntimeException( 'Gutenberg block was not registered.' );
}

$html = oilpriceapi_widget_block_render(
    array(
        'widgetType' => 'ticker',
        'theme'      => 'light',
        'fuels'      => 'diesel,gasoline',
    )
);

foreach ( array( 'US Diesel', 'US Gasoline', 'Week of July 13, 2026', 'U.S. Energy Information Administration' ) as $required ) {
    if ( false === strpos( $html, $required ) ) {
        throw new RuntimeException( 'Clean plugin render is missing: ' . $required );
    }
}

if ( ! in_array( 'oilpriceapi-widgets', $GLOBALS['oilpriceapi_test_styles'], true ) ) {
    throw new RuntimeException( 'Local widget stylesheet was not enqueued.' );
}

$settings  = new OilPriceAPI_Settings();
$sanitized = $settings->sanitize_settings(
    array(
        'theme'      => 'unexpected',
        'fuels'      => array( 'diesel', 'WTI', '<script>' ),
        'base_price' => '0',
    )
);

if ( array( 'theme' => 'dark', 'fuels' => 'diesel', 'base_price' => '2.50' ) !== $sanitized ) {
    throw new RuntimeException( 'Invalid settings were not reduced to the safe allowlist and defaults.' );
}

echo "PASS: clean plugin load, registration, settings sanitation, and one widget render\n";
