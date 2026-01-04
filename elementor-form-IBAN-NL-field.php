<?php
/**
 * Plugin Name: Elementor Forms IBAN NL field
 * Description: Custom addon that adds a "IBAN NL" field to Elementor Forms Widget.
 * Plugin URI:  https://github.com/eugenebogaart/elementor-form-IBAN-NL-field
 * Version:     1.0.0
 * Author:      Eugene Bogaart
 * Author URI:  https://github.com/eugenebogaart/elementor-form-IBAN-NL-field
 * Text Domain: elementor-form-IBAN-NL-field
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


add_action( 'plugins_loaded', 'myplugin_require_everything_free', 100 );

function myplugin_require_everything_free() {
  // require all free plugin files here
  if (is_file( __DIR__ . '/libraries/php-iban/php-iban.php')) {

  	add_action( 'elementor_pro/forms/fields/register', 'add_new_form_field' );
  } else {
	error_log("Cannot start IBAN-NL Form field plugin.  Please install libraries/php-iban/php-iban.php ");
	error_log("%> cd plugins/elementor-IBAN-NL-field ");
	error_log("%> mkdir libraries ; cd libraries ");
	error_log("%> git clone https://github.com/globalcitizen/php-iban.git ");

	?> <script>
			alert(  "Cannot start IBAN-NL Form field plugin.\n" +
					"Please install libraries/php-iban/php-iban.php.\n" +
					"%> cd plugins/elementor-IBAN-NL-field\n" +
					"%> mkdir libraries ; cd libraries \n" +
					"%> git clone https://github.com/globalcitizen/php-iban.git" );
	   </script>
	<?php 
  }
}
