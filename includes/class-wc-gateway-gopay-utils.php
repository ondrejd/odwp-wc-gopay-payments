<?php
/**
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @link https://github.com/ondrejd/odwp-wc-gopay-payments for the canonical source repository
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-wc-gopay-payments
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin's administration part.
 * @since 0.0.1
 */
class WC_Gateway_GoPay_Utils {
    /**
     * Prints error message in correct WP amin style.
     * @param string $msg Error message.
     * @param string $type (Optional.) One of ['info','updated','error'].
     * @return void
     * @since 0.0.1
     */
    public static function printError( $msg, $type = 'info' ) {
        $avail_types = array( 'error', 'info', 'updated' );
        $_type = in_array( $type, $avail_types ) ? $type : 'info';
        printf( '<div class="%s"><p>%s</p></div>', $_type, $msg );
    }
} // End of WC_Gateway_GoPay_Utils