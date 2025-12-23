<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Form Field - Local Tel
 *
 * Add a new "Local Tel" field to Elementor form widget.
 *
 * @since 1.0.0
 */
class Elementor_IBAN_NL_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	public $iban_pattern = 'NL[0-9]{2} [A-Z0-9]{4} [0-9]{4} [0-9]{4} [0-9]{2}';
	public $iban_placeholder = 'XXXX XXXX XXXX XXXX XX';

	/**
	 * Get field type.
	 *
	 * Retrieve local-tel field unique ID.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Field type.
	 */
	public function get_type(): string {
		return 'iban-nl';
	}

	/**
	 * Get field name.
	 *
	 * Retrieve local-tel field label.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Field name.
	 */
	public function get_name(): string {
		return esc_html__( 'IBAN NL', 'elementor-form-IBAN-NL-field' );
	}

	/**
	 * Render field output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param mixed $item
	 * @param mixed $item_index
	 * @param mixed $form
	 * @return void
	 */
	public function render( $item, $item_index, $form ): void {
		$form_id = $form->get_id();

		$field_id = 'form-field-' . $item['custom_id'];
		$form_name = $form->get_settings_for_display()['form_name'];

		$form->add_render_attribute(
			'input' . $item_index,
			[
				'class' => 'elementor-field-textual',
				'for' => $form_id . $item_index,
				'maxlength' => '22',  // Including spaces
				'pattern' => $this->iban_pattern,
				'title' => esc_html__( $this->iban_placeholder, 'elementor-form-IBAN-NL-field' ),
				'placeholder' => $item['IBAN-NL-placeholder2'],
			]
		);

		echo '<input ' . $form->get_render_attribute_string( 'input' . $item_index ) . '>';
		
		// $this->checkAndUpdateField($item, $form); 
	}

	/* 
	 * Before the submit/post happens, this hook rewites a field. 
	 * Unfortunatly this this not work because $ajax_handler has already been executed.
	 * This code should be moved to the $ajax handler some how.
	*/
	function checkAndUpdateField($item, $form) { 
		$field_id = 'form-field-' . $item['custom_id'];
		$form_name = $form->get_settings_for_display()['form_name'];
		?>
			<script>
				// Get the element by its ID
				const formId = document.querySelector('[name=<?php echo $form_name; ?>]');
				const someId = document.getElementById('<?php echo $field_id; ?>');

				function rewriteString() {
					// console.log("Rewriting:",someId.value);
					var newValue = someId.value.replace(/ /g,'').match(/.{1,4}/g).join(" ");
					// console.log("Rewriting result:",newValue);
					someId.value = newValue;
				}
				formId.addEventListener('submit', rewriteString);
		</script>
		<?php
	}



	/**
	 * Field validation.
	 *
	 * Validate local-tel field value to ensure it complies to certain rules.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Field_Base   $field
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 * @return void
	 */
	public function validation( $field, $record, $ajax_handler ): void {
		if ( empty( $field['value'] ) ) {
			return;
		}

		if ( preg_match( '/^' . $this->iban_pattern .'$/', $field['value'] ) !== 1 ) {
			$ajax_handler->add_error(
				$field['id'],
				esc_html__( 'IBAN nummer moet in het formaat ' . $this->iban_placeholder . ' zijn.', 'elementor-form-IBAN-NL-field' )
			);
		} else {
			error_log("Verifying IBAN nummer" . $field['value']);
			if (! $this->verify_iban($field['value'])) {
				$ajax_handler->add_error(
					$field['id'],
					esc_html__( 'IBAN nummer ongeldig.', 'elementor-form-IBAN-NL-field' )
				);
			} else {
				return;
			}
		}
	}


	function verify_iban($iban,$machine_format_only=false) {
		# First convert to machine format.
		if(!$machine_format_only) { $iban = iban_to_machine_format($iban); }

		// error_log("Verifying IBAN nummer, to iban_to_machine_format" . $iban );

		# Get country of IBAN
		$country = iban_get_country_part($iban);

		// error_log("Verifying IBAN nummer, country" . $country ); 

		# Test length of IBAN
		if(strlen($iban)!=iban_country_get_iban_length($country)) { 
			// error_log("Verifying IBAN nummer, length incorrect" ); 
			return false; 
		}

		# Get checksum of IBAN
		$checksum = iban_get_checksum_part($iban);

		# Get country-specific IBAN format regex
		$regex = '/'.iban_country_get_iban_format_regex($country).'/';

		# Check regex
		if(preg_match($regex,$iban)) {
			# Regex passed, check checksum
			if(!iban_verify_checksum($iban)) { 
				// error_log("Verifying IBAN nummer, checksum incorrect" ); 
				return false;
			}
		}
		else {
			// error_log("Verifying IBAN nummer, regex incorrect" ); 
			return false;
		}

		// error_log("Verifying IBAN nummer correct" ); 
		# Otherwise it 'could' exist
		return true;
	}



	/**
	 * Update form widget controls.
	 *
	 * Add input fields to allow the user to customize the IBAN NL field.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \Elementor\Widget_Base $widget The form widget instance.
	 * @return void
	 */
	public function update_controls( $widget ): void {
		$elementor = \ElementorPro\Plugin::elementor();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'IBAN-NL-placeholder2' => [
				'name' => 'IBAN-NL-placeholder2',
				'label' => esc_html__( 'IBAN NL Placeholder', 'elementor-form-IBAN-NL-field' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' =>  $this->iban_placeholder,
				'condition' => [ 'field_type' => $this->get_type() ],
				'placeholder' => $this->iban_placeholder,
			],
		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}
	/**
	 * Field constructor.
	 *
	 * Used to add a script to the Elementor editor preview.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/preview/init', [ $this, 'editor_preview_footer' ] );
	}

	/**
	 * Elementor editor preview.
	 *
	 * Add a script to the footer of the editor preview screen.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function editor_preview_footer(): void {
		add_action( 'wp_footer', [ $this, 'content_template_script' ] );
	}

	/**
	 * Content template script.
	 *
	 * Add content template alternative, to display the field in Elementor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template_script(): void {
		?>
		<script>
		jQuery( document ).ready( () => {

			elementor.hooks.addFilter(
				'elementor_pro/forms/content_template/field/<?php echo $this->get_type(); ?>',
				function ( inputField, item, i ) {
					const fieldType  = 'text';
					const fieldId    = `form_field_${i}`;
					const fieldClass = `elementor-field-textual elementor-field ${item.css_classes}`;
					const size       = '1';
					const pattern    = '<?php $this->iban_pattern;?>';
					const placeholder = item['IBAN-NL-placeholder2'];

					return `<input type="${fieldType}"  id="${fieldId}" class="${fieldClass}" size="${size}" pattern="${pattern}" placeholder="${placeholder}">`;
				}, 10, 3
			);

		});
		</script>
		<?php
	}

}


