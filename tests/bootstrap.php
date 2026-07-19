<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'DAY_IN_SECONDS', 86400 );

$GLOBALS['oilpriceapi_test_now'] = 1784475000;
$GLOBALS['oilpriceapi_test_transients'] = array();
$GLOBALS['oilpriceapi_test_http_queue'] = array();
$GLOBALS['oilpriceapi_test_http_calls'] = array();
$GLOBALS['oilpriceapi_test_scripts'] = array();
$GLOBALS['oilpriceapi_test_styles'] = array();
$GLOBALS['oilpriceapi_test_shortcodes'] = array();
$GLOBALS['oilpriceapi_test_blocks'] = array();
$GLOBALS['oilpriceapi_test_options'] = array();

class WP_Error {
    private $code;
    private $message;

    public function __construct( $code, $message ) {
        $this->code    = $code;
        $this->message = $message;
    }

    public function get_error_code() {
        return $this->code;
    }

    public function get_error_message() {
        return $this->message;
    }
}

function is_wp_error( $value ) {
    return $value instanceof WP_Error;
}

function wp_json_encode( $value ) {
    return json_encode( $value );
}

function oilpriceapi_widget_now() {
    return $GLOBALS['oilpriceapi_test_now'];
}

function get_transient( $key ) {
    if ( ! isset( $GLOBALS['oilpriceapi_test_transients'][ $key ] ) ) {
        return false;
    }

    $entry = $GLOBALS['oilpriceapi_test_transients'][ $key ];
    if ( $entry['expires'] <= oilpriceapi_widget_now() ) {
        unset( $GLOBALS['oilpriceapi_test_transients'][ $key ] );
        return false;
    }

    return $entry['value'];
}

function set_transient( $key, $value, $ttl ) {
    $GLOBALS['oilpriceapi_test_transients'][ $key ] = array(
        'value'   => $value,
        'expires' => oilpriceapi_widget_now() + $ttl,
    );
    return true;
}

function wp_remote_get( $url, $args ) {
    $GLOBALS['oilpriceapi_test_http_calls'][] = array( $url, $args );
    if ( empty( $GLOBALS['oilpriceapi_test_http_queue'] ) ) {
        return new WP_Error( 'http_request_failed', 'No response queued' );
    }
    return array_shift( $GLOBALS['oilpriceapi_test_http_queue'] );
}

function wp_remote_retrieve_response_code( $response ) {
    return isset( $response['response']['code'] ) ? $response['response']['code'] : 0;
}

function wp_remote_retrieve_body( $response ) {
    return isset( $response['body'] ) ? $response['body'] : '';
}

function shortcode_atts( $defaults, $atts ) {
    return array_merge( $defaults, is_array( $atts ) ? $atts : array() );
}

function esc_attr( $value ) {
    return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

function esc_html( $value ) {
    return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

function esc_url( $value ) {
    return filter_var( $value, FILTER_SANITIZE_URL );
}

function esc_html__( $value ) {
    return esc_html( $value );
}

function wp_enqueue_style( $handle ) {
    $GLOBALS['oilpriceapi_test_styles'][] = $handle;
}

function wp_register_style() {
    return true;
}

function plugin_dir_path( $file ) {
    return dirname( $file ) . '/';
}

function plugin_dir_url() {
    return 'https://example.test/wp-content/plugins/oilpriceapi-widget/';
}

function add_action() {
    return true;
}

function add_filter() {
    return true;
}

function add_shortcode( $name, $callback ) {
    $GLOBALS['oilpriceapi_test_shortcodes'][ $name ] = $callback;
}

function register_block_type( $path, $args ) {
    $GLOBALS['oilpriceapi_test_blocks'][] = array( $path, $args );
}

function plugin_basename( $file ) {
    return basename( $file );
}

function is_admin() {
    return false;
}

function admin_url( $path ) {
    return 'https://example.test/wp-admin/' . ltrim( $path, '/' );
}

function __( $value ) {
    return $value;
}

function get_option( $key, $default = false ) {
    return isset( $GLOBALS['oilpriceapi_test_options'][ $key ] ) ? $GLOBALS['oilpriceapi_test_options'][ $key ] : $default;
}

function sanitize_text_field( $value ) {
    return trim( strip_tags( (string) $value ) );
}

if ( ! defined( 'OILPRICEAPI_TEST_LOAD_PLUGIN' ) ) {
    function oilpriceapi_widget_get_default( $key ) {
        $defaults = array(
            'theme'      => 'dark',
            'fuels'      => 'diesel,gasoline',
            'base_price' => '2.50',
        );
        return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
    }
}

function oilpriceapi_test_response( $status, $body = '' ) {
    return array(
        'response' => array( 'code' => $status ),
        'body'     => $body,
    );
}

function oilpriceapi_test_payload() {
    return array(
        'status'     => 'success',
        'week_of'    => '2026-07-13',
        'cadence'    => 'weekly',
        'source'     => 'Source: U.S. Energy Information Administration (public domain)',
        'source_url' => 'https://www.eia.gov/petroleum/gasdiesel/',
        'codes'      => array( 'DIESEL_RETAIL_USD', 'GASOLINE_RETAIL_USD' ),
        'prices'     => array(
            array(
                'key'           => 'diesel',
                'code'          => 'DIESEL_RETAIL_USD',
                'label'         => 'US Diesel',
                'price'         => 4.796,
                'formatted'     => '$4.796',
                'currency'      => 'USD',
                'unit'          => 'gallon',
                'eia_series_id' => 'EMD_EPD2DXL0_PTE_NUS_DPG',
            ),
            array(
                'key'           => 'gasoline',
                'code'          => 'GASOLINE_RETAIL_USD',
                'label'         => 'US Gasoline',
                'price'         => 3.855,
                'formatted'     => '$3.855',
                'currency'      => 'USD',
                'unit'          => 'gallon',
                'eia_series_id' => 'EMM_EPMR_PTE_NUS_DPG',
            ),
        ),
    );
}

function oilpriceapi_test_reset() {
    $GLOBALS['oilpriceapi_test_transients'] = array();
    $GLOBALS['oilpriceapi_test_http_queue'] = array();
    $GLOBALS['oilpriceapi_test_http_calls'] = array();
    $GLOBALS['oilpriceapi_test_styles']     = array();
    $GLOBALS['oilpriceapi_test_shortcodes'] = array();
    $GLOBALS['oilpriceapi_test_blocks']     = array();
    $GLOBALS['oilpriceapi_test_options']    = array();
}
