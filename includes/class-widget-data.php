<?php
/**
 * Validated access to the public OilPriceAPI fuel-widget endpoint.
 *
 * @package OilPriceAPI_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OilPriceAPI_Widget_Data {

    const ENDPOINT         = 'https://www.oilpriceapi.com/api/widgets/fuel-prices';
    const FRESH_CACHE_KEY  = 'oilpriceapi_widget_fresh_v2';
    const LAST_SUCCESS_KEY = 'oilpriceapi_widget_last_success_v2';
    const FRESH_TTL        = 3600;
    const LAST_SUCCESS_TTL = 172800;
    const SOURCE_URL       = 'https://www.eia.gov/petroleum/gasdiesel/';
    const SOURCE_LABEL     = 'U.S. Energy Information Administration';

    /**
     * The only series permitted on this keyless public-display surface.
     *
     * @var array
     */
    private $allowed_series = array(
        'diesel' => array(
            'code'          => 'DIESEL_RETAIL_USD',
            'label'         => 'US Diesel',
            'eia_series_id' => 'EMD_EPD2DXL0_PTE_NUS_DPG',
        ),
        'gasoline' => array(
            'code'          => 'GASOLINE_RETAIL_USD',
            'label'         => 'US Gasoline',
            'eia_series_id' => 'EMM_EPMR_PTE_NUS_DPG',
        ),
    );

    /**
     * Return validated widget data and its delivery state.
     *
     * @return array{state:string,payload:?array}
     */
    public function get_prices() {
        $fresh = get_transient( self::FRESH_CACHE_KEY );
        $fresh_payload = $this->payload_from_envelope( $fresh );
        if ( null !== $fresh_payload ) {
            return array(
                'state'   => 'fresh',
                'payload' => $fresh_payload,
            );
        }

        $response = wp_remote_get(
            self::ENDPOINT,
            array(
                'timeout'     => 8,
                'redirection' => 2,
                'headers'     => array(
                    'Accept' => 'application/json',
                ),
                'user-agent'  => 'OilPriceAPI-WordPress/' . ( defined( 'OILPRICEAPI_WIDGET_VERSION' ) ? OILPRICEAPI_WIDGET_VERSION : 'development' ),
            )
        );

        if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
            $decoded = json_decode( wp_remote_retrieve_body( $response ), true );
            $payload = $this->normalize_payload( $decoded );

            if ( null !== $payload ) {
                $envelope = array(
                    'payload'    => $payload,
                    'fetched_at' => $this->now(),
                );
                set_transient( self::FRESH_CACHE_KEY, $envelope, self::FRESH_TTL );
                set_transient( self::LAST_SUCCESS_KEY, $envelope, self::LAST_SUCCESS_TTL );

                return array(
                    'state'   => 'fresh',
                    'payload' => $payload,
                );
            }
        }

        $last_success = get_transient( self::LAST_SUCCESS_KEY );
        $stale_payload = $this->payload_from_envelope( $last_success );
        if ( null !== $stale_payload && ( $this->now() - $last_success['fetched_at'] ) <= self::LAST_SUCCESS_TTL ) {
            return array(
                'state'   => 'stale',
                'payload' => $stale_payload,
            );
        }

        return array(
            'state'   => 'unavailable',
            'payload' => null,
        );
    }

    /**
     * Validate a cached envelope before using it.
     *
     * @param mixed $envelope Cached value.
     * @return array|null
     */
    private function payload_from_envelope( $envelope ) {
        if ( ! is_array( $envelope ) || ! isset( $envelope['payload'], $envelope['fetched_at'] ) || ! is_numeric( $envelope['fetched_at'] ) ) {
            return null;
        }

        return $this->normalize_payload( $envelope['payload'] );
    }

    /**
     * Fail-closed normalization prevents unexpected or encumbered data from rendering.
     *
     * @param mixed $payload Decoded JSON or cached payload.
     * @return array|null
     */
    private function normalize_payload( $payload ) {
        if ( ! is_array( $payload ) || 'success' !== ( isset( $payload['status'] ) ? $payload['status'] : '' ) ) {
            return null;
        }

        if ( 'weekly' !== ( isset( $payload['cadence'] ) ? $payload['cadence'] : '' ) || self::SOURCE_URL !== ( isset( $payload['source_url'] ) ? $payload['source_url'] : '' ) ) {
            return null;
        }

        $week_of = isset( $payload['week_of'] ) ? $payload['week_of'] : '';
        $date    = DateTimeImmutable::createFromFormat( '!Y-m-d', $week_of );
        $errors  = DateTimeImmutable::getLastErrors();
        if ( false === $date || ( false !== $errors && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) || $date->format( 'Y-m-d' ) !== $week_of ) {
            return null;
        }

        if ( ! isset( $payload['prices'] ) || ! is_array( $payload['prices'] ) ) {
            return null;
        }

        $prices = array();
        foreach ( $payload['prices'] as $item ) {
            if ( ! is_array( $item ) || ! isset( $item['key'] ) || ! isset( $this->allowed_series[ $item['key'] ] ) ) {
                return null;
            }

            $expected = $this->allowed_series[ $item['key'] ];
            if (
                $expected['code'] !== ( isset( $item['code'] ) ? $item['code'] : '' ) ||
                $expected['eia_series_id'] !== ( isset( $item['eia_series_id'] ) ? $item['eia_series_id'] : '' ) ||
                'USD' !== ( isset( $item['currency'] ) ? $item['currency'] : '' ) ||
                'gallon' !== ( isset( $item['unit'] ) ? $item['unit'] : '' ) ||
                ! isset( $item['price'] ) || ! is_numeric( $item['price'] )
            ) {
                return null;
            }

            $price = (float) $item['price'];
            if ( ! is_finite( $price ) || $price <= 0 ) {
                return null;
            }

            $prices[ $item['key'] ] = array(
                'key'           => $item['key'],
                'code'          => $expected['code'],
                'label'         => $expected['label'],
                'price'         => $price,
                'formatted'     => '$' . number_format( $price, 3, '.', '' ),
                'currency'      => 'USD',
                'unit'          => 'gallon',
                'eia_series_id' => $expected['eia_series_id'],
            );
        }

        if ( count( $prices ) !== count( $this->allowed_series ) ) {
            return null;
        }

        return array(
            'status'     => 'success',
            'week_of'    => $week_of,
            'cadence'    => 'weekly',
            'source'     => self::SOURCE_LABEL,
            'source_url' => self::SOURCE_URL,
            'prices'     => $prices,
        );
    }

    /**
     * Isolated clock for deterministic tests.
     *
     * @return int
     */
    private function now() {
        return function_exists( 'oilpriceapi_widget_now' ) ? oilpriceapi_widget_now() : time();
    }
}
