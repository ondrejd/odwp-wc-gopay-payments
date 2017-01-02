<?php
/**
 * Plugin Name: GoPay platby pro WooCommerce
 * Plugin URI: https://github.com/ondrejd/odwp-wc-gopay-payments
 * Description: Plugin pro WordPress a WooCommerce který umožňuje platby přes službu GoPay.
 * Version: 1.0.0
 * Author: Ondřej Doněk
 * Author URI: 
 * License: GPLv3
 * Requires at least: 4.7
 * Tested up to: 4.7
 *
 * Text Domain: odwp-wc-gopay-payments
 * Domain Path: /languages/
 *
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-wc-gopay-payments for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-wc-gopay-payments
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin's class.
 * @since 0.0.1
 */
class odwpWpGoPayPaymentsPlugin {
    const SLUG = 'odwp-wc-gopay-payments';
    const FILE = __FILE__;
    const VERSION = '0.0.1';
    
    /**
     * Activates the plugin.
     * @internal
     * @return void
     * @since 0.0.1
     */
    public static function activate() {
        // Nothing to do...
    }
    
    /**
     * Deactivates the plugin directly by updating WP option `active_plugins`.
     * @internal
     * @link https://developer.wordpress.org/reference/functions/deactivate_plugins/
     * @return void
     * @since 0.0.1
     * @todo Check if using `deactivate_plugins` whouldn't be better.
     */
    public static function deactivateRaw() {
        $active_plugins = get_option( 'active_plugins' );
        $out = array();
        foreach( $active_plugins as $key => $val ) {
            if( $val != sprintf( "%$1s/%$1s.php", self::SLUG ) ) {
                $out[$key] = $val;
            }
        }
        update_option( 'active_plugins', $out );
    }
    
    /**
     * Initializes the plugin.
     * @return void
     * @since 0.0.1
     */
    public static function init() {
        register_activation_hook( self::FILE, array( __CLASS__, 'activate' ) );
        register_uninstall_hook( self::FILE, array( __CLASS__, 'uninstall' ) );

        add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );
        add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin' ) );
    }

    /**
     * Initialize localization (attached to "init" action).
     * @return void
     * @since 0.0.1
     * @uses load_plugin_textdomain()
     */
    public static function load_plugin_textdomain() {
        $path = dirname( __FILE__ ) . '/languages';
        load_plugin_textdomain( 'odwpwcgp', false, $path );
    }
    
    /**
     * Loads plugin (attached to "plugins_loaded" action).
     * @return void
     * @since 0.0.1
     */
    public static function load_plugin() {
        // Register our new payment gateway in WooCommerce
        include_once( 'includes/class-wc-gateway-gopay.php' );
        add_filter( 'woocommerce_payment_gateways', function( $methods ) {
            $methods[] = 'WC_Gateway_GoPay';
            return $methods;
        } );
    }
    
    /**
     * Uninstalls the plugin.
     * @internal
     * @return void
     * @since 0.0.1
     */
    private static function uninstall() {
        if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
            return;
        }

        // Nothing to do...
    }
    
    /**
     * Check requirements of the plugin.
     * @internal
     * @link https://developer.wordpress.org/reference/functions/is_plugin_active_for_network/#source-code
     * @return boolean Returns TRUE if requirements are met.
     * @since 0.0.1
     * @todo Current solution doesn't work for WPMU... 
     */
    public static function requirementsCheck() {
        $active_plugins = (array) get_option( 'active_plugins', array() );
        return in_array( 'woocommerce/woocommerce.php', $active_plugins ) ? true : false;
    }
    
    /**
     * Shows error in WP administration that minimum requirements were not met.
     * @internal
     * @return void
     * @since 0.0.1
     */
    public static function requirementsError() {
        include_once( 'includes/class-wc-gateway-gopay-utils.php' );
        WC_Gateway_GoPay_Utils::printError( __( 'Plugin <b>GoPay platby pro WooCommerce</b> vyžaduje, aby byl nejprve nainstalovaný a aktivovaný plugin <b>WooCommerce</b>.', 'odwpwcgp' ), 'error' );
        WC_Gateway_GoPay_Utils::printError( __( 'Plugin <b>GoPay platby pro WooCommerce</b> byl <b>deaktivován</b>.', 'odwpwcgp' ), 'updated' );
    }
} // End of odwpWpGoPayPaymentsPlugin


// Our plug-in is dependant on WooCommerce
if( !odwpWpGoPayPaymentsPlugin::requirementsCheck() ) {
    odwpWpGoPayPaymentsPlugin::deactivateRaw();
    
    if( is_admin() ) {
        add_action( 'admin_head', array( odwpWpGoPayPaymentsPlugin, 'requirementsError ') );
    }
} else {
    // WooCommerce is present so initialize our plugin
    odwpWpGoPayPaymentsPlugin::init();
}
