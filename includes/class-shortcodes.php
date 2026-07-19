<?php
/**
 * OilPriceAPI widget shortcodes.
 *
 * @package OilPriceAPI_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OilPriceAPI_Shortcodes {

    /** @var OilPriceAPI_Widget_Data */
    private $data;

    /**
     * @param OilPriceAPI_Widget_Data|null $data Data client, injectable for tests.
     */
    public function __construct( $data = null ) {
        $this->data = $data ? $data : new OilPriceAPI_Widget_Data();
    }

    /** Register shortcodes and the local stylesheet. */
    public function register() {
        wp_register_style(
            'oilpriceapi-widgets',
            OILPRICEAPI_WIDGET_PLUGIN_URL . 'assets/css/widgets.css',
            array(),
            OILPRICEAPI_WIDGET_VERSION
        );

        add_shortcode( 'oilpriceapi_ticker', array( $this, 'render_ticker' ) );
        add_shortcode( 'oilpriceapi_diesel', array( $this, 'render_diesel' ) );
        add_shortcode( 'oilpriceapi_fuel_surcharge', array( $this, 'render_fuel_surcharge' ) );
        add_shortcode( 'oilpriceapi_carbon', array( $this, 'render_carbon' ) );
    }

    /**
     * Render weekly national diesel and gasoline prices.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_ticker( $atts ) {
        $atts = shortcode_atts(
            array(
                'theme'  => oilpriceapi_widget_get_default( 'theme' ),
                'fuels'  => oilpriceapi_widget_get_default( 'fuels' ),
                'layout' => 'horizontal',
            ),
            $atts,
            'oilpriceapi_ticker'
        );

        $result = $this->data->get_prices();
        if ( empty( $result['payload'] ) ) {
            return $this->render_unavailable( $atts['theme'], 'Fuel Price Ticker' );
        }

        $selected = $this->selected_fuels( $atts['fuels'] );
        $items    = '';
        foreach ( $selected as $key ) {
            $price  = $result['payload']['prices'][ $key ];
            $items .= '<div class="oilpriceapi-widget__price">';
            $items .= '<span class="oilpriceapi-widget__label">' . esc_html( $price['label'] ) . '</span>';
            $items .= '<strong>' . esc_html( $price['formatted'] ) . '</strong>';
            $items .= '<span class="oilpriceapi-widget__unit">per gallon</span>';
            $items .= '</div>';
        }

        $layout = 'vertical' === $atts['layout'] ? 'vertical' : 'horizontal';
        $body   = '<div class="oilpriceapi-widget__prices oilpriceapi-widget__prices--' . esc_attr( $layout ) . '">' . $items . '</div>';
        $body  .= $this->render_source( $result );

        return $this->render_shell( $body, $atts['theme'], 'fuel-ticker' );
    }

    /**
     * Render the weekly national diesel price.
     *
     * @param array $atts Shortcode attributes. The legacy regional attribute is ignored.
     * @return string
     */
    public function render_diesel( $atts ) {
        $atts = shortcode_atts(
            array( 'theme' => oilpriceapi_widget_get_default( 'theme' ) ),
            $atts,
            'oilpriceapi_diesel'
        );
        $result = $this->data->get_prices();
        if ( empty( $result['payload'] ) ) {
            return $this->render_unavailable( $atts['theme'], 'U.S. Diesel Price' );
        }

        $price = $result['payload']['prices']['diesel'];
        $body  = '<h3 class="oilpriceapi-widget__title">U.S. Diesel Price</h3>';
        $body .= '<div class="oilpriceapi-widget__featured"><strong>' . esc_html( $price['formatted'] ) . '</strong><span>per gallon, national average</span></div>';
        $body .= $this->render_source( $result );

        return $this->render_shell( $body, $atts['theme'], 'diesel' );
    }

    /**
     * Render a percentage above the configured diesel base price.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_fuel_surcharge( $atts ) {
        $atts = shortcode_atts(
            array(
                'theme'      => oilpriceapi_widget_get_default( 'theme' ),
                'base_price' => oilpriceapi_widget_get_default( 'base_price' ),
            ),
            $atts,
            'oilpriceapi_fuel_surcharge'
        );
        $result = $this->data->get_prices();
        if ( empty( $result['payload'] ) ) {
            return $this->render_unavailable( $atts['theme'], 'Fuel Surcharge Calculator' );
        }

        $base_price = is_numeric( $atts['base_price'] ) ? (float) $atts['base_price'] : 2.50;
        if ( $base_price <= 0 ) {
            $base_price = 2.50;
        }
        $diesel    = $result['payload']['prices']['diesel']['price'];
        $surcharge = max( 0, ( ( $diesel - $base_price ) / $base_price ) * 100 );

        $body  = '<h3 class="oilpriceapi-widget__title">Fuel Surcharge Calculator</h3>';
        $body .= '<dl class="oilpriceapi-widget__calculation">';
        $body .= '<div><dt>Weekly diesel average</dt><dd>$' . esc_html( number_format( $diesel, 3, '.', '' ) ) . '/gal</dd></div>';
        $body .= '<div><dt>Configured base price</dt><dd>$' . esc_html( number_format( $base_price, 2, '.', '' ) ) . '/gal</dd></div>';
        $body .= '<div class="oilpriceapi-widget__result"><dt>Percentage above base</dt><dd>' . esc_html( number_format( $surcharge, 1, '.', '' ) ) . '%</dd></div>';
        $body .= '</dl>';
        $body .= $this->render_source( $result );

        return $this->render_shell( $body, $atts['theme'], 'fuel-surcharge' );
    }

    /**
     * Preserve old embeds without continuing the unsupported crude-price calculation.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_carbon( $atts ) {
        $atts = shortcode_atts(
            array( 'theme' => oilpriceapi_widget_get_default( 'theme' ) ),
            $atts,
            'oilpriceapi_carbon'
        );

        $body  = '<h3 class="oilpriceapi-widget__title">Carbon Cost Calculator</h3>';
        $body .= '<p class="oilpriceapi-widget__message">This legacy widget was retired because its crude-price input was not cleared for public redistribution.</p>';
        $body .= '<p class="oilpriceapi-widget__action"><a href="https://www.oilpriceapi.com/docs/redistribution?utm_source=wordpress-plugin&amp;utm_medium=legacy-widget">Review data-use guidance</a></p>';

        return $this->render_shell( $body, $atts['theme'], 'retired' );
    }

    /** @return array */
    private function selected_fuels( $raw ) {
        $selected = array();
        foreach ( explode( ',', strtolower( (string) $raw ) ) as $key ) {
            $key = trim( $key );
            if ( in_array( $key, array( 'diesel', 'gasoline' ), true ) && ! in_array( $key, $selected, true ) ) {
                $selected[] = $key;
            }
        }
        return empty( $selected ) ? array( 'diesel', 'gasoline' ) : $selected;
    }

    /** @return string */
    private function render_source( $result ) {
        $date = DateTimeImmutable::createFromFormat( '!Y-m-d', $result['payload']['week_of'] );
        $html = '<div class="oilpriceapi-widget__source">';
        $html .= '<span>Week of ' . esc_html( $date->format( 'F j, Y' ) ) . ' &middot; Updated weekly</span>';
        if ( 'stale' === $result['state'] ) {
            $html .= '<strong class="oilpriceapi-widget__stale">Cached copy; service temporarily unavailable</strong>';
        }
        $html .= '<span>Source: <a href="' . esc_url( OilPriceAPI_Widget_Data::SOURCE_URL ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( OilPriceAPI_Widget_Data::SOURCE_LABEL ) . '</a> (public domain)</span>';
        $html .= '<a href="https://www.oilpriceapi.com/from-widget?utm_source=wordpress-plugin&amp;utm_medium=widget">Delivered by OilPriceAPI</a>';
        $html .= '</div>';
        return $html;
    }

    /** @return string */
    private function render_unavailable( $theme, $title ) {
        $body  = '<h3 class="oilpriceapi-widget__title">' . esc_html( $title ) . '</h3>';
        $body .= '<p class="oilpriceapi-widget__message">Fuel price data is temporarily unavailable.</p>';
        $body .= '<p class="oilpriceapi-widget__action"><a href="https://status.oilpriceapi.com/" target="_blank" rel="noopener noreferrer">Check service status</a></p>';
        return $this->render_shell( $body, $theme, 'unavailable' );
    }

    /** @return string */
    private function render_shell( $body, $theme, $modifier ) {
        wp_enqueue_style( 'oilpriceapi-widgets' );
        $theme = 'light' === $theme ? 'light' : 'dark';
        return '<section class="oilpriceapi-widget oilpriceapi-widget--' . esc_attr( $theme ) . ' oilpriceapi-widget--' . esc_attr( $modifier ) . '" aria-label="OilPriceAPI widget">' . $body . '</section>';
    }
}
