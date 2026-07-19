<?php

require __DIR__ . '/bootstrap.php';
require dirname( __DIR__ ) . '/includes/class-widget-data.php';
require dirname( __DIR__ ) . '/includes/class-shortcodes.php';

$tests = array();

function test_case( $name, $callback ) {
    global $tests;
    $tests[ $name ] = $callback;
}

function assert_true( $condition, $message ) {
    if ( ! $condition ) {
        throw new RuntimeException( $message );
    }
}

function assert_same( $expected, $actual, $message ) {
    if ( $expected !== $actual ) {
        throw new RuntimeException( $message . '\nExpected: ' . var_export( $expected, true ) . '\nActual: ' . var_export( $actual, true ) );
    }
}

function assert_contains( $needle, $haystack, $message ) {
    assert_true( false !== strpos( $haystack, $needle ), $message . '\nMissing: ' . $needle );
}

function assert_not_contains( $needle, $haystack, $message ) {
    assert_true( false === strpos( $haystack, $needle ), $message . '\nUnexpected: ' . $needle );
}

test_case( 'valid weekly response is normalized and cached', function () {
    oilpriceapi_test_reset();
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, wp_json_encode( oilpriceapi_test_payload() ) );
    $client = new OilPriceAPI_Widget_Data();
    $result = $client->get_prices();

    assert_same( 'fresh', $result['state'], 'Successful responses must be fresh.' );
    assert_same( 4.796, $result['payload']['prices']['diesel']['price'], 'Diesel value must be preserved.' );
    assert_same( 1, count( $GLOBALS['oilpriceapi_test_http_calls'] ), 'One upstream request expected.' );
    assert_true( isset( $GLOBALS['oilpriceapi_test_transients']['oilpriceapi_widget_fresh_v2'] ), 'Fresh cache must be written.' );
    assert_true( isset( $GLOBALS['oilpriceapi_test_transients']['oilpriceapi_widget_last_success_v2'] ), 'Last-success cache must be written.' );
} );

test_case( 'fresh cache avoids a second request', function () {
    oilpriceapi_test_reset();
    $payload = oilpriceapi_test_payload();
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, wp_json_encode( $payload ) );
    $client = new OilPriceAPI_Widget_Data();
    $client->get_prices();
    $client->get_prices();
    assert_same( 1, count( $GLOBALS['oilpriceapi_test_http_calls'] ), 'Fresh data should be reused for one hour.' );
} );

foreach ( array( 401, 403, 429, 500 ) as $status ) {
    test_case( 'HTTP ' . $status . ' fails closed without fabricated data', function () use ( $status ) {
        oilpriceapi_test_reset();
        $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( $status, '{"error":"do not expose"}' );
        $result = ( new OilPriceAPI_Widget_Data() )->get_prices();
        assert_same( 'unavailable', $result['state'], 'Failure must not become a price.' );
        assert_true( empty( $result['payload'] ), 'Failure must not include a payload.' );
        assert_not_contains( 'do not expose', wp_json_encode( $result ), 'Raw upstream bodies must not escape.' );
    } );
}

test_case( 'timeout fails closed', function () {
    oilpriceapi_test_reset();
    $GLOBALS['oilpriceapi_test_http_queue'][] = new WP_Error( 'http_request_failed', 'Operation timed out with api key secret' );
    $result = ( new OilPriceAPI_Widget_Data() )->get_prices();
    assert_same( 'unavailable', $result['state'], 'Timeout must not become a price.' );
    assert_not_contains( 'secret', wp_json_encode( $result ), 'Transport details must not escape.' );
} );

test_case( 'stale last success is explicit after a failure', function () {
    oilpriceapi_test_reset();
    $payload = oilpriceapi_test_payload();
    $GLOBALS['oilpriceapi_test_transients']['oilpriceapi_widget_last_success_v2'] = array(
        'value'   => array( 'payload' => $payload, 'fetched_at' => oilpriceapi_widget_now() - 7200 ),
        'expires' => oilpriceapi_widget_now() + HOUR_IN_SECONDS,
    );
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 429 );
    $result = ( new OilPriceAPI_Widget_Data() )->get_prices();
    assert_same( 'stale', $result['state'], 'Last success should be labeled stale.' );

    $html = ( new OilPriceAPI_Shortcodes( new OilPriceAPI_Widget_Data() ) )->render_ticker( array() );
    assert_contains( 'Cached copy', $html, 'Stale output requires a visible label.' );
    assert_true( 0 === preg_match( '/\blive\b/i', $html ), 'Stale output must never be called live.' );
} );

test_case( 'expired last success is not rendered', function () {
    oilpriceapi_test_reset();
    $payload = oilpriceapi_test_payload();
    $GLOBALS['oilpriceapi_test_transients']['oilpriceapi_widget_last_success_v2'] = array(
        'value'   => array( 'payload' => $payload, 'fetched_at' => oilpriceapi_widget_now() - ( 49 * HOUR_IN_SECONDS ) ),
        'expires' => oilpriceapi_widget_now() + HOUR_IN_SECONDS,
    );
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 500 );
    $result = ( new OilPriceAPI_Widget_Data() )->get_prices();
    assert_same( 'unavailable', $result['state'], 'Old data must expire after 48 hours.' );
} );

foreach ( array( '', '{}', '[]', '{bad json' ) as $body ) {
    test_case( 'empty or malformed body fails closed: ' . md5( $body ), function () use ( $body ) {
        oilpriceapi_test_reset();
        $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, $body );
        $result = ( new OilPriceAPI_Widget_Data() )->get_prices();
        assert_same( 'unavailable', $result['state'], 'Malformed payload must be unavailable.' );
    } );
}

test_case( 'unknown or encumbered series is rejected', function () {
    oilpriceapi_test_reset();
    $payload = oilpriceapi_test_payload();
    $payload['prices'][0]['code'] = 'WTI_USD';
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, wp_json_encode( $payload ) );
    $result = ( new OilPriceAPI_Widget_Data() )->get_prices();
    assert_same( 'unavailable', $result['state'], 'Only the EIA allowlist may render.' );
} );

test_case( 'ticker renders source date, cadence, unit, and attribution', function () {
    oilpriceapi_test_reset();
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, wp_json_encode( oilpriceapi_test_payload() ) );
    $html = ( new OilPriceAPI_Shortcodes( new OilPriceAPI_Widget_Data() ) )->render_ticker( array() );
    assert_contains( 'Week of July 13, 2026', $html, 'Observation date is required.' );
    assert_contains( 'Updated weekly', $html, 'Native cadence is required.' );
    assert_contains( 'per gallon', $html, 'Unit is required.' );
    assert_contains( 'U.S. Energy Information Administration', $html, 'Source attribution is required.' );
    assert_not_contains( 'Brent', $html, 'Encumbered crude must not render.' );
    assert_not_contains( 'WTI', $html, 'Encumbered crude must not render.' );
} );

test_case( 'unavailable widget gives a recovery path', function () {
    oilpriceapi_test_reset();
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 429 );
    $html = ( new OilPriceAPI_Shortcodes( new OilPriceAPI_Widget_Data() ) )->render_ticker( array() );
    assert_contains( 'temporarily unavailable', $html, 'Failure must be visible.' );
    assert_contains( 'status.oilpriceapi.com', $html, 'Failure must include a next action.' );
    assert_not_contains( '429', $html, 'Internal status must not leak into public UI.' );
} );

test_case( 'requests are keyless and bounded', function () {
    oilpriceapi_test_reset();
    $GLOBALS['oilpriceapi_test_http_queue'][] = oilpriceapi_test_response( 200, wp_json_encode( oilpriceapi_test_payload() ) );
    ( new OilPriceAPI_Widget_Data() )->get_prices();
    $call = $GLOBALS['oilpriceapi_test_http_calls'][0];
    assert_same( 'https://www.oilpriceapi.com/api/widgets/fuel-prices', $call[0], 'Only the allowlisted endpoint may be called.' );
    assert_same( 8, $call[1]['timeout'], 'Request timeout must be bounded.' );
    assert_true( ! isset( $call[1]['headers']['Authorization'] ), 'No credential should be sent or stored.' );
} );

$failures = 0;
foreach ( $tests as $name => $test ) {
    try {
        $test();
        echo "PASS: {$name}\n";
    } catch ( Throwable $error ) {
        ++$failures;
        fwrite( STDERR, "FAIL: {$name}\n{$error->getMessage()}\n" );
    }
}

echo count( $tests ) . " tests, {$failures} failures\n";
exit( $failures > 0 ? 1 : 0 );
