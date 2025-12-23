<?php
/**
 * Plugin Name: Elementor Forms IBAN NL field
 * Description: Custom addon that adds a "IBAN NL" field to Elementor Forms Widget.
 * Plugin URI:  https://elementor.com/
 * Version:     1.0.0
 * Author:      Elementor Developer
 * Author URI:  https://developers.elementor.com/
 * Text Domain: elementor-form-local-tel-field
 *
 * Requires Plugins: elementor
 * Elementor tested up to: 3.25.0
 * Elementor Pro tested up to: 3.25.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add new `local-tel` field to Elementor form widget.
 *
 * @since 1.0.0
 * @param \ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar $form_fields_registrar
 * @return void
 */
function add_new_form_field( $form_fields_registrar ) {

	require_once( __DIR__ . '/form-fields/IBAN-NL.php' );
	require_once( __DIR__ . '/libraries/php-iban/php-iban.php');

	$form_fields_registrar->register( new \Elementor_IBAN_NL_Field() );

}
add_action( 'elementor_pro/forms/fields/register', 'add_new_form_field' );


