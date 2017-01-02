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


require_once( 'gopay-php-api-2.5/api/country_code.php' );
require_once( 'gopay-php-api-2.5/api/gopay_helper.php' );
require_once( 'gopay-php-api-2.5/api/gopay_soap.php' );
require_once( 'gopay-php-api-2.5/api/gopay_config.php' );

defined( 'LANG' ) || define( 'LANG', substr( get_bloginfo( 'language' ), 0, 2 ) );


/**
 * WC_Gateway_GoPay class.
 */
class WC_Gateway_GoPay extends WC_Payment_Gateway {
    /** @var bool Is logging enabled? */
    public static $log_enabled = false;
    
    /** @var WC_Logger Logger instance */
    public static $log = false;
    
    /**
     * Constructor.
     * @return void
     */
    public function __construct() {
        $this->id                 = 'gopay';
        $this->has_fields         = false;
        $this->order_button_text  = __( 'Platit pomocí GoPay', 'odwpwcgp' );
        $this->method_title       = __( 'GoPay', 'odwpwcgp' );
        $this->method_description = __( 'Placení pomocí služby GoPay.', 'odwpwcgp' );
        $this->supports           = array( 'products', 'refunds' );
        
        // Load the settings
        $this->init_form_fields();
        $this->init_settings();
        
        // Define user settings
        $this->title         = $this->get_option( 'title' );
        $this->description   = $this->get_option( 'description' );
        $this->instructions  = $this->get_option( 'instructions' );
        $this->thank_you     = $this->get_option( 'thank_you' );
        $this->client_id     = $this->get_option( 'client_id' );
        $this->client_secret = $this->get_option( 'client_secret' );
        $this->api_url       = $this->get_option( 'api_url' );
        $this->testmode      = 'yes' === $this->get_option( 'testmode', 'no' );
        $this->testmode_url  = $this->get_option( 'testmode_url' );
        $this->debug         = 'yes' === $this->get_option( 'debug', 'no' );
        
        self::$log_enabled   = $this->debug;
        
        if( $this->testmode === true ) {
            GopayConfig::init( GopayConfig::TEST );
        } else {
            GopayConfig::init( GopayConfig::PROD );
        }
        
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        
        if ( ! $this->is_valid_for_use() ) {
			$this->enabled = 'no';
		} else {
			/*include_once( 'includes/class-wc-gateway-paypal-ipn-handler.php' );
			new WC_Gateway_Paypal_IPN_Handler( $this->testmode, $this->receiver_email );

			if ( $this->identity_token ) {
				include_once( 'includes/class-wc-gateway-paypal-pdt-handler.php' );
				new WC_Gateway_Paypal_PDT_Handler( $this->testmode, $this->identity_token );
			}*/
		}
    }

	/**
	 * Logging method.
	 * @param string $message
     * @return void
	 */
	public static function log( $message ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}
			self::$log->add( 'paypal', $message );
		}
	}
    
    /**
     * @return string
     */
    public function get_icon() {
        $html  = '';
        $html .= '<img src="https://account.gopay.com/download/gopay_bannery-modre.png" alt="' . esc_attr__( 'Logo GoPay', 'odwpwcgp' ) . '" />';
        $html .= sprintf( '<a href="%1$s" class="about_gopay" target="blank" title="%2$s">%2$s</a>', 'https://www.gopay.com/', esc_attr__( 'Více o GoPay', 'odwpwcgp' ) );

        return apply_filters( 'woocommerce_gateway_icon', $html, $this->id );
    }

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 * @return bool
	 */
	public function is_valid_for_use() {
		return in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_gopay_supported_currencies', array( 'MXN', 'EUR', 'CZK', 'PLN' ) ) );
	}

	/**
	 * Admin Panel Options.
	 * - Options for bits like 'title' and availability on a country-by-country basis.
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
		if ( $this->is_valid_for_use() ) {
			parent::admin_options();
		} else {
?>
	<div class="inline error">
        <p>
            <strong><?php _e( 'Platební brána je zakázána', 'odwpwcgp' ); ?></strong>: 
            <?php _e( 'GoPay nepodporuje měnu, ve které je tento obchod provozován.', 'odwpwcgp' ); ?>
        </p>
    </div>
<?php
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include( 'settings-gopay.php' );
	}

	/**
	 * Get the transaction URL.
	 * @param  WC_Order $order
	 * @return string
	 */
	public function get_transaction_url( $order ) {
		if ( $this->testmode ) {
			$this->view_transaction_url = 'https://gw.sandbox.gopay.com/api/';
		} else {
			$this->view_transaction_url = 'https://gate.gopay.cz/api/';
		}
		return parent::get_transaction_url( $order );
	}

	/**
	 * Process the payment and return the result.
	 * @param  int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
        include_once( 'class-wc-gateway-paypal-request.php' );
        
        $order         = wc_get_order( $order_id );
        $gopay_request = new WC_Gateway_GoPay_Request( $this );
        
        return array(
            'result'     => 'success',
            'redirect' => $gopay_request->get_request_url( $order, $this->testmode )
        );
    }

	/**
	 * Can the order be refunded via PayPal?
	 * @param  WC_Order $order
	 * @return bool
	 */
	public function can_refund_order( $order ) {
		return $order && $order->get_transaction_id();
	}

	/**
	 * Process a refund if supported.
	 * @param  int    $order_id
	 * @param  float  $amount
	 * @param  string $reason
	 * @return bool True or false based on success, or a WP_Error object
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
        // ...
        return new WP_Error( 'Není ještě naimplementováno!', 'odwpwcgp' );
    }
}

