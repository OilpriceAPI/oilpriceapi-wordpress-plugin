<?php
/**
 * Plugin Name: OilPriceAPI Widgets
 * Plugin URI: https://www.oilpriceapi.com/widgets
 * Description: Embed live oil prices, diesel prices, fuel surcharge calculator, and carbon cost calculator on your WordPress site.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: OilPriceAPI
 * Author URI: https://www.oilpriceapi.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: oilpriceapi-widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'OILPRICEAPI_WIDGET_VERSION', '1.0.0' );
define( 'OILPRICEAPI_WIDGET_URL', 'https://www.oilpriceapi.com/widgets/' );
define( 'OILPRICEAPI_WIDGET_PATH', plugin_dir_path( __FILE__ ) );
define( 'OILPRICEAPI_WIDGET_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin dependencies.
 */
require_once OILPRICEAPI_WIDGET_PATH . 'includes/class-shortcodes.php';
require_once OILPRICEAPI_WIDGET_PATH . 'admin/class-settings.php';

/**
 * Initialize shortcodes.
 */
function oilpriceapi_widget_init() {
    $shortcodes = new OilPriceAPI_Shortcodes();
    $shortcodes->register();
}
add_action( 'init', 'oilpriceapi_widget_init' );

/**
 * Initialize admin settings.
 */
function oilpriceapi_widget_admin_init() {
    if ( is_admin() ) {
        $settings = new OilPriceAPI_Settings();
        $settings->register();
    }
}
add_action( 'init', 'oilpriceapi_widget_admin_init' );

/**
 * Register the Gutenberg block.
 */
function oilpriceapi_widget_register_block() {
    register_block_type(
        OILPRICEAPI_WIDGET_PATH . 'blocks/oil-price-widget',
        array(
            'render_callback' => 'oilpriceapi_widget_block_render',
        )
    );
}
add_action( 'init', 'oilpriceapi_widget_register_block' );

/**
 * Render callback for the Gutenberg block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered HTML.
 */
function oilpriceapi_widget_block_render( $attributes ) {
    $widget_type  = isset( $attributes['widgetType'] ) ? $attributes['widgetType'] : 'ticker';
    $theme        = isset( $attributes['theme'] ) ? $attributes['theme'] : oilpriceapi_widget_get_default( 'theme' );
    $commodities  = isset( $attributes['commodities'] ) ? $attributes['commodities'] : oilpriceapi_widget_get_default( 'commodities' );
    $layout       = isset( $attributes['layout'] ) ? $attributes['layout'] : 'horizontal';
    $regional     = isset( $attributes['regional'] ) ? $attributes['regional'] : 'true';
    $base_price   = isset( $attributes['basePrice'] ) ? $attributes['basePrice'] : oilpriceapi_widget_get_default( 'base_price' );
    $carbon_price = isset( $attributes['carbonPrice'] ) ? $attributes['carbonPrice'] : oilpriceapi_widget_get_default( 'carbon_price' );

    $shortcodes = new OilPriceAPI_Shortcodes();

    switch ( $widget_type ) {
        case 'diesel':
            return $shortcodes->render_diesel( array(
                'theme'    => $theme,
                'regional' => $regional,
            ) );
        case 'fuel-surcharge':
            return $shortcodes->render_fuel_surcharge( array(
                'theme'      => $theme,
                'base_price' => $base_price,
            ) );
        case 'carbon':
            return $shortcodes->render_carbon( array(
                'theme'        => $theme,
                'carbon_price' => $carbon_price,
            ) );
        default:
            return $shortcodes->render_ticker( array(
                'theme'       => $theme,
                'commodities' => $commodities,
                'layout'      => $layout,
            ) );
    }
}

/**
 * Get a default setting value.
 *
 * @param string $key Setting key.
 * @return string Default value.
 */
function oilpriceapi_widget_get_default( $key ) {
    $options = get_option( 'oilpriceapi_widget_settings', array() );

    $defaults = array(
        'theme'        => 'dark',
        'commodities'  => 'BRENT,WTI',
        'base_price'   => '2.50',
        'carbon_price' => '50',
    );

    if ( isset( $options[ $key ] ) && '' !== $options[ $key ] ) {
        return $options[ $key ];
    }

    return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Add settings link on plugin page.
 *
 * @param array $links Existing links.
 * @return array Modified links.
 */
function oilpriceapi_widget_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'options-general.php?page=oilpriceapi-widget' ) . '">' . __( 'Settings', 'oilpriceapi-widget' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'oilpriceapi_widget_settings_link' );
