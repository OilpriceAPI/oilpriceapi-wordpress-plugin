<?php

require __DIR__ . '/bootstrap.php';
require dirname( __DIR__ ) . '/includes/class-widget-data.php';
require dirname( __DIR__ ) . '/includes/class-shortcodes.php';

$body = stream_get_contents( STDIN );
if ( '' === trim( $body ) ) {
    throw new RuntimeException( 'Expected production JSON on STDIN.' );
}

$GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, $body );
$html = ( new OilPriceAPI_Shortcodes( new OilPriceAPI_Widget_Data() ) )->render_ticker( array() );

foreach ( array( 'US Diesel', 'US Gasoline', 'Updated weekly', 'U.S. Energy Information Administration' ) as $required ) {
    if ( false === strpos( $html, $required ) ) {
        throw new RuntimeException( 'Production render is missing: ' . $required );
    }
}

if ( preg_match( '/\b(BRENT|WTI|live)\b/i', $html ) ) {
    throw new RuntimeException( 'Production render contains a retired series or freshness claim.' );
}

echo "PASS: production endpoint validated and rendered through the plugin\n";
