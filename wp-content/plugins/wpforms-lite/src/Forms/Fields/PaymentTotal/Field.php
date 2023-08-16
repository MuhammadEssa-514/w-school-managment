<?php

namespace WPForms\Forms\Fields\PaymentTotal;

/**
 * Total payment field.
 *
 * @since 1.8.2
 */
class Field extends \WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.8.2
	 */
	public function init() {

		// Define field type information.
		$this->name     = esc_html__( 'Total', 'wpforms-lite' );
		$this->keywords = esc_html__( 'store, ecommerce, pay, payment, sum', 'wpforms-lite' );
		$this->type     = 'payment-total';
		$this->icon     = 'fa-money';
		$this->order    = 110;
		$this->group    = 'payment';

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.8.2
	 */
	private function hooks() {

		// Define additional field properties.
		add_filter( "wpforms_field_properties_{$this->type}", [ $this, 'field_properties' ], 5, 3 );

		// Recalculate total for a form.
		add_filter( 'wpforms_process_filter', [ $this, 'calculate_total' ], 10, 3 );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.8.2
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field data and settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Input Primary: initial total is always zero.
		$properties['inputs']['primary']['attr']['value'] = '0';

		// Input Primary: add class for targeting calculations.
		$properties['inputs']['primary']['class'][] = 'wpforms-payment-total';

		// Input Primary: add data attribute if total is required.
		if ( ! empty( $field['required'] ) ) {
			$properties['inputs']['primary']['data']['rule-required-payment'] = true;
		}

		return $properties;
	}

	/**
	 * Whether current field can be populated dynamically.
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
	 * Whether current field can be populated dynamically.
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
	 * Do not trust the posted total since that relies on javascript.
	 *
	 * Instead we re-calculate server side.
	 *
	 * @since 1.8.2
	 *
	 * @param array $fields    List of fields with their data.
	 * @param array $entry     Submitted form data.
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function calculate_total( $fields, $entry, $form_data ) {

		// At this point we have passed processing and validation, so we know
		// the amounts in $fields are safe to use.
		$total  = wpforms_get_total_payment( $fields );
		$amount = wpforms_sanitize_amount( $total );

		foreach ( $fields as $id => $field ) {
			if ( ! empty( $field['type'] ) && $field['type'] === $this->type ) {
				$fields[ $id ]['value']      = wpforms_format_amount( $amount, true );
				$fields[ $id ]['amount']     = wpforms_format_amount( $amount );
				$fields[ $id ]['amount_raw'] = $amount;
			}
		}

		return $fields;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.8.2
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		// Options open markup.
		$args = [
			'markup' => 'open',
		];

		$this->field_option( 'basic-options', $field, $args );

		// Label.
		$this->field_option( 'label', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$args = [
			'markup' => 'close',
		];

		$this->field_option( 'basic-options', $field, $args );

		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$args = [
			'markup' => 'open',
		];

		$this->field_option( 'advanced-options', $field, $args );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Hide label.
		$this->field_option( 'label_hide', $field );

		// Options close markup.
		$args = [
			'markup' => 'close',
		];

		$this->field_option( 'advanced-options', $field, $args );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.8.2
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_preview( $field ) {

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary field.
		echo '<div>' . esc_html( wpforms_format_amount( 0, true ) ) . '</div>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.8.2
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated, not used parameter.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		$primary = $field['properties']['inputs']['primary'];
		$type    = ! empty( $field['required'] ) ? 'text' : 'hidden';
		$attrs   = $primary['attr'];

		if ( ! empty( $field['required'] ) ) {
			$attrs['style']    = 'position:absolute!important;clip:rect(0,0,0,0)!important;height:1px!important;width:1px!important;border:0!important;overflow:hidden!important;padding:0!important;margin:0!important;';
			$attrs['readonly'] = 'readonly';
		}

		// This displays the total the user sees.
		echo '<div class="wpforms-payment-total">' . esc_html( wpforms_format_amount( 0, true ) ) . '</div>';

		// Hidden input for processing.
		printf(
			'<input type="%s" %s>',
			esc_attr( $type ),
			wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $attrs ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	/**
	 * Validate field on form submit.
	 *
	 * @since 1.8.2
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Field value submitted by a user.
	 * @param array  $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		// Basic required check - If field is marked as required, check for entry data.
		if ( ! empty( $form_data['fields'][ $field_id ]['required'] ) && ( empty( $field_submit ) || wpforms_sanitize_amount( $field_submit ) <= 0 ) ) {
			wpforms()->get( 'process' )->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Payment is required.', 'wpforms-lite' );
		}
	}

	/**
	 * Format and sanitize field.
	 *
	 * @since 1.8.2
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Field value submitted by a user.
	 * @param array  $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		// Define data.
		$name   = ! empty( $form_data['fields'][ $field_id ]['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';
		$amount = wpforms_sanitize_amount( $field_submit );

		// Set final field details.
		wpforms()->get( 'process' )->fields[ $field_id ] = [
			'name'       => sanitize_text_field( $name ),
			'value'      => wpforms_format_amount( $amount, true ),
			'amount'     => wpforms_format_amount( $amount ),
			'amount_raw' => $amount,
			'id'         => absint( $field_id ),
			'type'       => sanitize_key( $this->type ),
		];
	}
}
