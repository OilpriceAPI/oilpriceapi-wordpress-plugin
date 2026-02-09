<?php
/**
 * OilPriceAPI Widget Shortcodes
 *
 * Handles registration and rendering of all 4 widget shortcodes.
 *
 * @package OilPriceAPI_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OilPriceAPI_Shortcodes {

    /**
     * Track which scripts need to be enqueued.
     *
     * @var array
     */
    private $enqueued_scripts = array();

    /**
     * Widget definitions.
     *
     * @var array
     */
    private $widgets = array(
        'ticker' => array(
            'container_id' => 'oilpriceapi-ticker',
            'script_file'  => 'ticker.js',
            'handle'       => 'oilpriceapi-ticker',
        ),
        'diesel' => array(
            'container_id' => 'oilpriceapi-diesel',
            'script_file'  => 'diesel.js',
            'handle'       => 'oilpriceapi-diesel',
        ),
        'fuel-surcharge' => array(
            'container_id' => 'oilpriceapi-fuel-surcharge',
            'script_file'  => 'fuel-surcharge.js',
            'handle'       => 'oilpriceapi-fuel-surcharge',
        ),
        'carbon' => array(
            'container_id' => 'oilpriceapi-carbon',
            'script_file'  => 'carbon.js',
            'handle'       => 'oilpriceapi-carbon',
        ),
    );

    /**
     * Register all shortcodes and script handles.
     */
    public function register() {
        // Register external scripts (not enqueued yet — only when shortcode is used).
        foreach ( $this->widgets as $key => $widget ) {
            wp_register_script(
                $widget['handle'],
                OILPRICEAPI_WIDGET_URL . $widget['script_file'],
                array(),
                OILPRICEAPI_WIDGET_VERSION,
                array(
                    'strategy' => 'async',
                    'in_footer' => true,
                )
            );
        }

        add_shortcode( 'oilpriceapi_ticker', array( $this, 'render_ticker' ) );
        add_shortcode( 'oilpriceapi_diesel', array( $this, 'render_diesel' ) );
        add_shortcode( 'oilpriceapi_fuel_surcharge', array( $this, 'render_fuel_surcharge' ) );
        add_shortcode( 'oilpriceapi_carbon', array( $this, 'render_carbon' ) );
    }

    /**
     * Enqueue a widget script only once.
     *
     * @param string $widget_key Widget identifier.
     */
    private function enqueue_widget_script( $widget_key ) {
        if ( ! in_array( $widget_key, $this->enqueued_scripts, true ) ) {
            wp_enqueue_script( $this->widgets[ $widget_key ]['handle'] );
            $this->enqueued_scripts[] = $widget_key;
        }
    }

    /**
     * Render the Oil Price Ticker widget.
     *
     * @param array $atts Shortcode attributes.
     * @return string Widget HTML.
     */
    public function render_ticker( $atts ) {
        $atts = shortcode_atts(
            array(
                'theme'       => oilpriceapi_widget_get_default( 'theme' ),
                'commodities' => oilpriceapi_widget_get_default( 'commodities' ),
                'layout'      => 'horizontal',
            ),
            $atts,
            'oilpriceapi_ticker'
        );

        $this->enqueue_widget_script( 'ticker' );

        $data_attrs = sprintf(
            ' data-theme="%s" data-commodities="%s" data-layout="%s"',
            esc_attr( $atts['theme'] ),
            esc_attr( $atts['commodities'] ),
            esc_attr( $atts['layout'] )
        );

        return '<div id="' . esc_attr( $this->widgets['ticker']['container_id'] ) . '"' . $data_attrs . '></div>';
    }

    /**
     * Render the Diesel Price Tracker widget.
     *
     * @param array $atts Shortcode attributes.
     * @return string Widget HTML.
     */
    public function render_diesel( $atts ) {
        $atts = shortcode_atts(
            array(
                'theme'    => oilpriceapi_widget_get_default( 'theme' ),
                'regional' => 'true',
            ),
            $atts,
            'oilpriceapi_diesel'
        );

        $this->enqueue_widget_script( 'diesel' );

        $data_attrs = sprintf(
            ' data-theme="%s" data-regional="%s"',
            esc_attr( $atts['theme'] ),
            esc_attr( $atts['regional'] )
        );

        return '<div id="' . esc_attr( $this->widgets['diesel']['container_id'] ) . '"' . $data_attrs . '></div>';
    }

    /**
     * Render the Fuel Surcharge Calculator widget.
     *
     * @param array $atts Shortcode attributes.
     * @return string Widget HTML.
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

        $this->enqueue_widget_script( 'fuel-surcharge' );

        $data_attrs = sprintf(
            ' data-theme="%s" data-base-price="%s"',
            esc_attr( $atts['theme'] ),
            esc_attr( $atts['base_price'] )
        );

        return '<div id="' . esc_attr( $this->widgets['fuel-surcharge']['container_id'] ) . '"' . $data_attrs . '></div>';
    }

    /**
     * Render the Carbon Cost Calculator widget.
     *
     * @param array $atts Shortcode attributes.
     * @return string Widget HTML.
     */
    public function render_carbon( $atts ) {
        $atts = shortcode_atts(
            array(
                'theme'        => oilpriceapi_widget_get_default( 'theme' ),
                'carbon_price' => oilpriceapi_widget_get_default( 'carbon_price' ),
            ),
            $atts,
            'oilpriceapi_carbon'
        );

        $this->enqueue_widget_script( 'carbon' );

        $data_attrs = sprintf(
            ' data-theme="%s" data-carbon-price="%s"',
            esc_attr( $atts['theme'] ),
            esc_attr( $atts['carbon_price'] )
        );

        return '<div id="' . esc_attr( $this->widgets['carbon']['container_id'] ) . '"' . $data_attrs . '></div>';
    }
}
