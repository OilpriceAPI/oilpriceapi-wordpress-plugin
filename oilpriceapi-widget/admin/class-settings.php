<?php
/**
 * OilPriceAPI Widget Settings Page
 *
 * Admin settings page under Settings > OilPriceAPI.
 *
 * @package OilPriceAPI_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OilPriceAPI_Settings {

    /**
     * Register the settings page and fields.
     */
    public function register() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    }

    /**
     * Add settings page to the Settings menu.
     */
    public function add_settings_page() {
        add_options_page(
            __( 'OilPriceAPI Widgets', 'oilpriceapi-widget' ),
            __( 'OilPriceAPI', 'oilpriceapi-widget' ),
            'manage_options',
            'oilpriceapi-widget',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Enqueue admin styles on our settings page only.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_styles( $hook ) {
        if ( 'settings_page_oilpriceapi-widget' !== $hook ) {
            return;
        }
        wp_enqueue_style(
            'oilpriceapi-admin',
            OILPRICEAPI_WIDGET_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            OILPRICEAPI_WIDGET_VERSION
        );
    }

    /**
     * Register settings, sections, and fields.
     */
    public function register_settings() {
        register_setting(
            'oilpriceapi_widget_settings_group',
            'oilpriceapi_widget_settings',
            array( $this, 'sanitize_settings' )
        );

        add_settings_section(
            'oilpriceapi_defaults_section',
            __( 'Default Settings', 'oilpriceapi-widget' ),
            array( $this, 'render_section_description' ),
            'oilpriceapi-widget'
        );

        add_settings_field(
            'theme',
            __( 'Default Theme', 'oilpriceapi-widget' ),
            array( $this, 'render_theme_field' ),
            'oilpriceapi-widget',
            'oilpriceapi_defaults_section'
        );

        add_settings_field(
            'commodities',
            __( 'Default Commodities', 'oilpriceapi-widget' ),
            array( $this, 'render_commodities_field' ),
            'oilpriceapi-widget',
            'oilpriceapi_defaults_section'
        );

        add_settings_field(
            'base_price',
            __( 'Default Base Fuel Price', 'oilpriceapi-widget' ),
            array( $this, 'render_base_price_field' ),
            'oilpriceapi-widget',
            'oilpriceapi_defaults_section'
        );

        add_settings_field(
            'carbon_price',
            __( 'Default Carbon Price', 'oilpriceapi-widget' ),
            array( $this, 'render_carbon_price_field' ),
            'oilpriceapi-widget',
            'oilpriceapi_defaults_section'
        );
    }

    /**
     * Sanitize settings before save.
     *
     * @param array $input Raw input.
     * @return array Sanitized values.
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        $sanitized['theme'] = isset( $input['theme'] ) && 'light' === $input['theme'] ? 'light' : 'dark';

        if ( isset( $input['commodities'] ) ) {
            $allowed = array( 'BRENT', 'WTI', 'NATGAS' );
            $selected = array_intersect( (array) $input['commodities'], $allowed );
            $sanitized['commodities'] = implode( ',', $selected );
        } else {
            $sanitized['commodities'] = 'BRENT,WTI';
        }

        $sanitized['base_price'] = isset( $input['base_price'] )
            ? sanitize_text_field( $input['base_price'] )
            : '2.50';

        if ( ! is_numeric( $sanitized['base_price'] ) || floatval( $sanitized['base_price'] ) < 0 ) {
            $sanitized['base_price'] = '2.50';
        }

        $sanitized['carbon_price'] = isset( $input['carbon_price'] )
            ? sanitize_text_field( $input['carbon_price'] )
            : '50';

        if ( ! is_numeric( $sanitized['carbon_price'] ) || floatval( $sanitized['carbon_price'] ) < 0 ) {
            $sanitized['carbon_price'] = '50';
        }

        return $sanitized;
    }

    /**
     * Render the section description.
     */
    public function render_section_description() {
        echo '<p>' . esc_html__( 'Configure default values for OilPriceAPI widgets. These can be overridden per-shortcode.', 'oilpriceapi-widget' ) . '</p>';
    }

    /**
     * Render the theme field.
     */
    public function render_theme_field() {
        $value = oilpriceapi_widget_get_default( 'theme' );
        ?>
        <select name="oilpriceapi_widget_settings[theme]">
            <option value="dark" <?php selected( $value, 'dark' ); ?>><?php esc_html_e( 'Dark', 'oilpriceapi-widget' ); ?></option>
            <option value="light" <?php selected( $value, 'light' ); ?>><?php esc_html_e( 'Light', 'oilpriceapi-widget' ); ?></option>
        </select>
        <?php
    }

    /**
     * Render the commodities checkbox field.
     */
    public function render_commodities_field() {
        $current = explode( ',', oilpriceapi_widget_get_default( 'commodities' ) );
        $options = array(
            'BRENT'  => 'Brent Crude',
            'WTI'    => 'WTI Crude',
            'NATGAS' => 'Natural Gas',
        );

        foreach ( $options as $val => $label ) {
            printf(
                '<label style="margin-right: 16px;"><input type="checkbox" name="oilpriceapi_widget_settings[commodities][]" value="%s" %s /> %s</label>',
                esc_attr( $val ),
                checked( in_array( $val, $current, true ), true, false ),
                esc_html( $label )
            );
        }
        echo '<p class="description">' . esc_html__( 'Commodities shown in the Oil Price Ticker widget.', 'oilpriceapi-widget' ) . '</p>';
    }

    /**
     * Render the base price field.
     */
    public function render_base_price_field() {
        $value = oilpriceapi_widget_get_default( 'base_price' );
        printf(
            '<input type="text" name="oilpriceapi_widget_settings[base_price]" value="%s" class="small-text" /> <span>$/gallon</span>',
            esc_attr( $value )
        );
        echo '<p class="description">' . esc_html__( 'Base fuel price for the Fuel Surcharge Calculator.', 'oilpriceapi-widget' ) . '</p>';
    }

    /**
     * Render the carbon price field.
     */
    public function render_carbon_price_field() {
        $value = oilpriceapi_widget_get_default( 'carbon_price' );
        printf(
            '<input type="text" name="oilpriceapi_widget_settings[carbon_price]" value="%s" class="small-text" /> <span>$/tonne CO₂</span>',
            esc_attr( $value )
        );
        echo '<p class="description">' . esc_html__( 'Carbon price for the Carbon Cost Calculator.', 'oilpriceapi-widget' ) . '</p>';
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap oilpriceapi-settings">
            <h1><?php esc_html_e( 'OilPriceAPI Widgets', 'oilpriceapi-widget' ); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'oilpriceapi_widget_settings_group' );
                do_settings_sections( 'oilpriceapi-widget' );
                submit_button();
                ?>
            </form>

            <hr />

            <h2><?php esc_html_e( 'Shortcode Reference', 'oilpriceapi-widget' ); ?></h2>

            <div class="oilpriceapi-shortcode-ref">
                <h3><?php esc_html_e( 'Oil Price Ticker', 'oilpriceapi-widget' ); ?></h3>
                <code id="shortcode-ticker">[oilpriceapi_ticker theme="dark" commodities="BRENT,WTI" layout="horizontal"]</code>
                <button type="button" class="button oilpriceapi-copy-btn" data-target="shortcode-ticker">
                    <?php esc_html_e( 'Copy', 'oilpriceapi-widget' ); ?>
                </button>

                <h3><?php esc_html_e( 'Diesel Price Tracker', 'oilpriceapi-widget' ); ?></h3>
                <code id="shortcode-diesel">[oilpriceapi_diesel theme="dark" regional="true"]</code>
                <button type="button" class="button oilpriceapi-copy-btn" data-target="shortcode-diesel">
                    <?php esc_html_e( 'Copy', 'oilpriceapi-widget' ); ?>
                </button>

                <h3><?php esc_html_e( 'Fuel Surcharge Calculator', 'oilpriceapi-widget' ); ?></h3>
                <code id="shortcode-fuel">[oilpriceapi_fuel_surcharge theme="dark" base_price="2.50"]</code>
                <button type="button" class="button oilpriceapi-copy-btn" data-target="shortcode-fuel">
                    <?php esc_html_e( 'Copy', 'oilpriceapi-widget' ); ?>
                </button>

                <h3><?php esc_html_e( 'Carbon Cost Calculator', 'oilpriceapi-widget' ); ?></h3>
                <code id="shortcode-carbon">[oilpriceapi_carbon theme="dark" carbon_price="50"]</code>
                <button type="button" class="button oilpriceapi-copy-btn" data-target="shortcode-carbon">
                    <?php esc_html_e( 'Copy', 'oilpriceapi-widget' ); ?>
                </button>
            </div>

            <hr />

            <p>
                <?php
                printf(
                    /* translators: %s: link to widget configurator */
                    esc_html__( 'Need help? Visit the %s for interactive previews and configuration.', 'oilpriceapi-widget' ),
                    '<a href="https://www.oilpriceapi.com/widgets" target="_blank" rel="noopener noreferrer">' . esc_html__( 'OilPriceAPI Widget Configurator', 'oilpriceapi-widget' ) . '</a>'
                );
                ?>
            </p>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var buttons = document.querySelectorAll('.oilpriceapi-copy-btn');
            buttons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var target = document.getElementById(this.getAttribute('data-target'));
                    if (target && navigator.clipboard) {
                        navigator.clipboard.writeText(target.textContent).then(function() {
                            btn.textContent = '<?php echo esc_js( __( 'Copied!', 'oilpriceapi-widget' ) ); ?>';
                            setTimeout(function() {
                                btn.textContent = '<?php echo esc_js( __( 'Copy', 'oilpriceapi-widget' ) ); ?>';
                            }, 2000);
                        });
                    }
                });
            });
        });
        </script>
        <?php
    }
}
