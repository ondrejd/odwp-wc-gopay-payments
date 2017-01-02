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

/*
Test GoID: 8433565679
Test Secure Key: C9YdfSUxd7hswdZ3mbPxGFHw
Client ID: 1171941848
Client Secret: 5F6CYbAp
Test uživatelské jméno: testUser8433565679
Test heslo: P8337528
*/

/**
 * Settings for GoPay payment gateway.
 */
return array(
    'enabled' => array(
        'title'   => __( 'Povolit / Zakázat', 'odwpwcgp' ),
        'type'    => 'checkbox',
        'label'   => __( 'Povolit platbu pomocí služby GoPay', 'odwpwcgp' ),
        'default' => 'yes'
    ),
    'title' => array(
        'title'       => __( 'Titulek', 'odwpwcgp' ),
        'type'        => 'text',
        'description' => __( 'Titulek, který uživatel vidí během placení.', 'odwpwcgp' ),
        'default'     => __( 'GoPay', 'odwpwcgp' ),
        'desc_tip'    => true,
    ),
    'description' => array(
        'title'       => __( 'Popis', 'odwpwcgp' ),
        'type'        => 'textarea',
        'description' => __( 'Popis, který uživatel uvidí při placení u pokladny.', 'odwpwcgp' ),
        'default'     => __( 'Platby pomocí oblíbené služby GoPay.', 'odwpwcgp' ),
        'desc_tip'    => true,
    ),
    'instructions' => array(
        'title'       => __( 'Instrukce', 'odwpwcgp' ),
        'type'        => 'textarea',
        'description' => __( 'Instrukce pro uživatele, které budou přidány do stránky "Děkujeme" a do emailů.', 'odwpwcgp' ),
        'default'     => '',
        'desc_tip'    => true,
    ),
    'thank_you' => array(
        'title'       => __( 'Poděkování', 'odwpwcgp' ),
        'type'        => 'textarea',
        'description' => __( 'Zpráva s poděkováním, která se objeví poté, co je objednávka odeslána.', 'odwpwcgp' ),
        'default'     => '',
        'desc_tip'    => true,
    ),
    /*'order_status' => array(
        'title'       => __( 'Order Status', 'odwpwcgp' ),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __( 'Choose whether status you wish after checkout.', 'odwpwcgp' ),
        'default'     => 'wc-completed',
        'desc_tip'    => true,
        'options'     => wc_get_order_statuses()
    ),*/
	'api_details' => array(
		'title'       => __( 'Přístupy API', 'odwpwcgp' ),
		'type'        => 'title',
		'description' => sprintf( __( 'Vložte vaše přihlašovací údaje k API služby GoPay. Více informací najdete na %stéto stránce%s.', 'odwpwcgp' ), '<a href="https://help.gopay.com/cs/tema/integrace-platebni-brany/technicky-popis-integrace-platebni-brany/postup-integrace-platebni-brany">', '</a>' ),
	),
    'client_id' => array(
        'title'       => __( 'ClientID', 'odwpwcgp' ),
        'type'        => 'text',
        'description' => __( 'Klientský identifikátor, který vám byl přidělen.','odwpwcgp' ),
        'default'     => '',
        'desc_tip'    => false,
    ),
    'client_secret' => array(
        'title'       => __( 'ClientSecret', 'odwpwcgp' ),
        'type'        => 'text',
        'description' => __( 'Klientská bezpečnostní fráze, která vám byla přidělena.', 'odwpwcgp' ),
        'default'     => '',
        'desc_tip'    => false,
    ),
    'api_url' => array(
        'title'       => __( 'API', 'odwpwcgp' ),
        'type'        => 'text',
        'description' => __( 'URL adresa, na které se nachází provozní prostředí služby GoPay. EDITUJTE JEN PO UVÁŽENÍ!', 'odwpwcgp' ),
        'default'     => 'https://gate.gopay.cz/api/',
        'desc_tip'    => true,
    ),
    'testmode' => array(
        'title'       => __( 'Testovací prostředí', 'odwpwcgp' ),
        'type'        => 'checkbox',
        'description' =>  __( 'Používat testovací prostředí služby GoPay.', 'odwpwcgp' ),
        'default'     => 'no',
        'desc_tip'    => false,
    ),
    'testmode_url' => array(
        'title'       => __( 'Test API', 'odwpwcgp' ),
        'type'        => 'text',
        'description' => __( 'URL adresa, na které se nachází testovací prostředí služby GoPay. EDITUJTE JEN PO UVÁŽENÍ!', 'odwpwcgp' ),
        'default'     => 'https://gw.sandbox.gopay.com/api/',
        'desc_tip'    => true,
    ),
	'api_details' => array(
		'title'       => __( 'Ostatní', 'odwpwcgp' ),
		'type'        => 'title',
		'description' => __( 'Ostatní nastavení.', 'odwpwcgp' ),
	),
    'debug' => array(
        'title'       => __( 'Log', 'odwpwcgp' ),
        'type'        => 'checkbox',
        'description' =>  __( 'Zapnout logování použití této platební brány (určeno pro vývoj).', 'odwpwcgp' ),
        'default'     => 'no',
        'desc_tip'    => false,
    ),
);
