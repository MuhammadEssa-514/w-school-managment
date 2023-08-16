<?php

namespace WPForms\Integrations\Stripe\Fields\Traits;

use WPForms\Integrations\Stripe\Helpers;

/**
 * Stripe credit card field.
 *
 * @since 1.8.2
 */
trait CreditCard {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.8.2
	 */
	public function init() {

		// Define field type information.
		$this->name     = esc_html__( 'Stripe Credit Card', 'wpforms-lite' );
		$this->keywords = esc_html__( 'store, ecommerce, credit card, pay, payment, debit card', 'wpforms-lite' );
		$this->type     = 'stripe-credit-card';
		$this->icon     = 'fa-credit-card';
		$this->order    = 90;
		$this->group    = 'payment';

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_stripe-credit-card', [ $this, 'field_properties' ], 5, 3 );
		add_filter( 'wpforms_builder_fields_options', [ $this, 'pre_fields_options' ] );

		// Set field to required by default.
		add_filter( 'wpforms_field_new_required', [ $this, 'default_required' ], 10, 2 );

		add_action( 'wpforms_builder_enqueues', [ $this, 'builder_enqueues' ] );
		add_filter( 'wpforms_builder_strings', [ $this, 'builder_js_strings' ], 10, 2 );
		add_filter( 'wpforms_builder_field_button_attributes', [ $this, 'field_button_attributes' ], 10, 3 );
		add_filter( 'wpforms_pro_fields_entry_preview_is_field_support_preview_stripe-credit-card_field', [ $this, 'entry_preview_availability' ], 10, 4 );
	}

	/**
	 * Pre Builder Field Options.
	 *
	 * @since 1.8.2
	 *
	 * @param array $form Current form post data.
	 */
	public function pre_fields_options( $form ) {

		if ( ! isset( $form->post_content ) ) {
			$this->form_data = [];

			return;
		}

		$this->form_data = $form ? wpforms_decode( $form->post_content ) : [];

		if ( ! is_array( $this->form_data ) ) {
			$this->form_data = [];
		}
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.8.2
	 *
	 * @param array $field Field settings.
	 */
	public function field_options( $field ) {

		$this->field_option( 'basic-options', $field, [ 'markup' => 'open' ] );

		// Label.
		$this->field_option( 'label', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		$this->field_option( 'basic-options', $field, [ 'markup' => 'close' ] );

		$this->field_option( 'advanced-options', $field, [ 'markup' => 'open' ] );

		// Size.
		$this->field_option( 'size', $field );

		$this->advanced_options( $field );

		// Hide Label.
		$this->field_option( 'label_hide', $field );

		// Hide sub labels.
		$this->field_option( 'sublabel_hide', $field );

		$this->field_option( 'advanced-options', $field, [ 'markup' => 'close' ] );
	}

	/**
	 * Disallow dynamic population.
	 *
	 * @since 1.8.2
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Current field specific data.
	 *
	 * @return bool
	 */
	public function is_dynamic_population_allowed( $properties, $field ) {

		return false;
	}

	/**
	 * Disallow fallback population.
	 *
	 * @since 1.8.2
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Current field specific data.
	 *
	 * @return bool
	 */
	public function is_fallback_population_allowed( $properties, $field ) {

		return false;
	}

	/**
	 * Default is required.
	 *
	 * @since 1.8.2
	 *
	 * @param bool  $required Required status, true is required.
	 * @param array $field    Field settings.
	 *
	 * @return bool
	 */
	public function default_required( $required, $field ) {

		return $field['type'] === $this->type ? true : $required;
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 1.8.2
	 *
	 * @param string $view Current view.
	 */
	public function builder_enqueues( $view ) {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-builder-stripe-card-field',
			WPFORMS_PLUGIN_URL . "assets/css/integrations/stripe/builder-stripe{$min}.css",
			[],
			WPFORMS_VERSION
		);

		wp_enqueue_script(
			'wpforms-builder-stripe-card-field',
			WPFORMS_PLUGIN_URL . "assets/js/integrations/stripe/admin-builder-stripe-card-field{$min}.js",
			[ 'jquery', 'wpforms-builder' ],
			WPFORMS_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-builder-stripe-card-field',
			'wpforms_builder_stripe_card_field',
			[
				'field_slug' => Helpers::get_field_slug(),
			]
		);
	}

	/**
	 * Add our localized strings to be available in the form builder.
	 *
	 * @since 1.8.2
	 *
	 * @param array $strings Form builder JS strings.
	 * @param array $form    Form data.
	 *
	 * @return array
	 */
	public function builder_js_strings( $strings, $form ) {

		$strings['stripe_ajax_required'] = wp_kses(
			__( '<p>AJAX form submissions are required when using the Stripe Credit Card field.</p><p>To proceed, please go to <strong>Settings » General » Advanced</strong> and check <strong>Enable AJAX form submission</strong>.</p>', 'wpforms-lite' ),
			[
				'p'      => [],
				'strong' => [],
			]
		);

		$strings['stripe_keys_required'] = wp_kses(
			__( '<p>Stripe account connection is required when using the Stripe Credit Card field.</p><p>To proceed, please go to <strong>WPForms Settings » Payments » Stripe</strong> and press <strong>Connect with Stripe</strong> button.</p>', 'wpforms-lite' ),
			[
				'p'      => [],
				'strong' => [],
			]
		);

		$strings['payments_enabled_required'] = wp_kses(
			__( '<p>Stripe Payments must be enabled when using the Stripe Credit Card field.</p><p>To proceed, please go to <strong>Payments » Stripe</strong> and check <strong>Enable Stripe payments</strong>.</p>', 'wpforms-lite' ),
			[
				'p'      => [],
				'strong' => [],
			]
		);

		return $strings;
	}

	/**
	 * Define additional "Add Field" button attributes.
	 *
	 * @since 1.8.2
	 *
	 * @param array $attributes Button attributes.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_button_attributes( $attributes, $field, $form_data ) {

		if ( Helpers::get_field_slug() !== $field['type'] ) {
			return $attributes;
		}

		if ( Helpers::has_stripe_field( $form_data ) ) {
			$attributes['atts']['disabled'] = 'true';

			return $attributes;
		}

		if ( ! Helpers::has_stripe_keys() ) {
			$attributes['class'][] = 'warning-modal';
			$attributes['class'][] = 'stripe-keys-required';
		}

		return $attributes;
	}

	/**
	 * Currently validation happens on the front end. We do not do
	 * generic server-side validation because we do not allow the card
	 * details to POST to the server.
	 *
	 * @since 1.8.2
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {
	}

	/**
	 * Format field.
	 *
	 * @since 1.8.2
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		// Define data.
		$name = ! empty( $form_data['fields'][ $field_id ]['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';

		// Set final field details.
		wpforms()->get( 'process' )->fields[ $field_id ] = [
			'name'  => sanitize_text_field( $name ),
			'value' => '',
			'id'    => absint( $field_id ),
			'type'  => $this->type,
		];
	}

	/**
	 * The field value availability for the entry preview field.
	 *
	 * @since 1.8.2
	 *
	 * @param bool   $is_supported The field availability.
	 * @param string $value        The submitted Credit Card detail.
	 * @param array  $field        Field data.
	 * @param array  $form_data    Form data.
	 *
	 * @return bool
	 */
	public function entry_preview_availability( $is_supported, $value, $field, $form_data ) {

		return ! empty( $value ) && $value !== '-';
	}

	/**
	 * Maybe display errors before field.
	 *
	 * @since 1.8.2
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return bool
	 */
	private function field_display_errors( $form_data ) {

		// Display warning for non SSL pages.
		if ( ! is_ssl() ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
			esc_html_e( 'This page is insecure. Credit Card field should be used for testing purposes only.', 'wpforms-lite' );
			echo '</div>';
		}

		if ( ! Helpers::has_stripe_keys() ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
			esc_html_e( 'Credit Card field is disabled, Stripe keys are missing.', 'wpforms-lite' );
			echo '</div>';

			return true;
		}

		if ( ! Helpers::has_stripe_enabled( [ $form_data ] ) ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
			esc_html_e( 'Credit Card field is disabled, Stripe payments are not enabled in the form settings.', 'wpforms-lite' );
			echo '</div>';

			return true;
		}

		return false;
	}

	/**
	 * Checking if block editor is loaded.
	 *
	 * @since 1.8.2
	 *
	 * @return bool True if is block editor.
	 */
	private function is_block_editor() {

		// phpcs:disable WordPress.Security.NonceVerification
		$is_gutenberg = defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && $_REQUEST['context'] === 'edit';
		$is_elementor = ( ! empty( $_POST['action'] ) && $_POST['action'] === 'elementor_ajax' ) || ( ! empty( $_GET['action'] ) && $_GET['action'] === 'elementor' );
		$is_divi      = ! empty( $_GET['et_fb'] ) || ( ! empty( $_POST['action'] ) && $_POST['action'] === 'wpforms_divi_preview' );
		// phpcs:enable WordPress.Security.NonceVerification

		return $is_gutenberg || $is_elementor || $is_divi;
	}
}
