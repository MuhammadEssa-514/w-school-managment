<?php

namespace WPForms\Forms\Fields\PaymentSingle;

/**
 * Single item payment field.
 *
 * @since 1.8.2
 */
class Field extends \WPForms_Field {

	/**
	 * User field format.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	const FORMAT_USER = 'user';

	/**
	 * Single field format.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	const FORMAT_SINGLE = 'single';

	/**
	 * Hidden field format.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	const FORMAT_HIDDEN = 'hidden';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.8.2
	 */
	public function init() {

		// Define field type information.
		$this->name     = esc_html__( 'Single Item', 'wpforms-lite' );
		$this->keywords = esc_html__( 'product, store, ecommerce, pay, payment', 'wpforms-lite' );
		$this->type     = 'payment-single';
		$this->icon     = 'fa-file-o';
		$this->order    = 30;
		$this->group    = 'payment';

		$this->hooks();
	}

	/**
	 * Define additional field hooks.
	 *
	 * @since 1.8.2
	 */
	private function hooks() {

		// Define additional field properties.
		add_filter( "wpforms_field_properties_{$this->type}", [ $this, 'field_properties' ], 5, 3 );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.8.2
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Basic IDs.
		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );

		// Set options container (<select>) properties.
		$properties['input_container'] = [
			'class' => [ 'wpforms-payment-price' ],
			'data'  => [],
			'id'    => "wpforms-{$form_id}-field_{$field_id}",
		];

		// User format data and class.
		$field_format = ! empty( $field['format'] ) ? $field['format'] : self::FORMAT_SINGLE;

		if ( $field_format === self::FORMAT_USER ) {
			$properties['inputs']['primary']['data']['rule-currency'] = '["$",false]';

			$properties['inputs']['primary']['class'][] = 'wpforms-payment-user-input';
		}

		$properties['inputs']['primary']['class'][] = 'wpforms-payment-price';

		// Check size.
		if ( ! empty( $field['size'] ) ) {
			$properties['inputs']['primary']['class'][] = 'wpforms-field-' . esc_attr( $field['size'] );
		}

		$required = ! empty( $form_data['fields'][ $field_id ]['required'] );

		if ( $required ) {
			$properties['inputs']['primary']['data']['rule-required-positive-number'] = true;
		}

		// Price.
		if ( ! empty( $field['price'] ) ) {
			$field_value = wpforms_sanitize_amount( $field['price'] );
		} elseif ( $required && $field_format === self::FORMAT_SINGLE ) {
			$field_value = wpforms_format_amount( 0 );
		} else {
			$field_value = '';
		}

		$properties['inputs']['primary']['attr']['value'] = ! empty( $field_value ) ? wpforms_format_amount( $field_value, true ) : $field_value;

		// Single item and hidden format should hide the input field.
		if ( ! empty( $field['format'] ) && $field['format'] === self::FORMAT_HIDDEN ) {
			$properties['container']['class'][] = 'wpforms-field-hidden';
		}

		return $properties;
	}

	/**
	 * Get field populated single property value.
	 *
	 * @since 1.8.2
	 *
	 * @param string $raw_value  Value from a GET param, always a string.
	 * @param string $input      Represent a subfield inside the field. May be empty.
	 * @param array  $properties Field properties.
	 * @param array  $field      Current field specific data.
	 *
	 * @return array Modified field properties.
	 */
	protected function get_field_populated_single_property_value( $raw_value, $input, $properties, $field ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		if ( ! is_string( $raw_value ) ) {
			return $properties;
		}

		// Allow to redefine the value for user-defined price only.
		$field_format = ! empty( $field['format'] ) ? $field['format'] : self::FORMAT_SINGLE;

		if ( $field_format !== self::FORMAT_USER ) {
			return $properties;
		}

		$get_value           = stripslashes( sanitize_text_field( $raw_value ) );
		$get_value           = ! empty( $get_value ) ? wpforms_sanitize_amount( $get_value ) : '';
		$get_value_formatted = ! empty( $get_value ) ? wpforms_format_amount( $get_value ) : '';

		// `primary` by default.
		if (
			! empty( $input ) &&
			isset( $properties['inputs'][ $input ] )
		) {
			$properties['inputs'][ $input ]['attr']['value'] = $get_value_formatted;
		}

		return $properties;
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

		$this->field_option( 'basic-options', $field, [ 'markup' => 'open' ] );
		$this->field_option( 'label', $field );
		$this->field_option( 'description', $field );

		// Item Price.
		$price   = ! empty( $field['price'] ) ? wpforms_format_amount( wpforms_sanitize_amount( $field['price'] ) ) : '';
		$tooltip = esc_html__( 'Enter the price of the item, without a currency symbol.', 'wpforms-lite' );

		$output = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'price',
				'value'   => esc_html__( 'Item Price', 'wpforms-lite' ),
				'tooltip' => $tooltip,
			],
			false
		);

		$output .= $this->field_element(
			'text',
			$field,
			[
				'slug'        => 'price',
				'value'       => $price,
				'class'       => 'wpforms-money-input',
				'placeholder' => wpforms_format_amount( 0 ),
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'price',
				'content' => $output,
			]
		);

		// Item Format option.
		$format  = ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'date-time';
		$tooltip = esc_html__( 'Select the item type.', 'wpforms-lite' );
		$options = [
			self::FORMAT_SINGLE => esc_html__( 'Single Item', 'wpforms-lite' ),
			self::FORMAT_USER   => esc_html__( 'User Defined', 'wpforms-lite' ),
			self::FORMAT_HIDDEN => esc_html__( 'Hidden', 'wpforms-lite' ),
		];

		$output = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'format',
				'value'   => esc_html__( 'Item Type', 'wpforms-lite' ),
				'tooltip' => $tooltip,
			],
			false
		);

		$output .= $this->field_element(
			'select',
			$field,
			[
				'slug'    => 'format',
				'value'   => $format,
				'options' => $options,
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'format',
				'content' => $output,
			]
		);

		$this->field_option( 'required', $field );
		$this->field_option( 'basic-options', $field, [ 'markup' => 'close' ] );

		/*
		 * Advanced field options.
		 */

		$this->field_option( 'advanced-options', $field, [ 'markup' => 'open' ] );
		$this->field_option( 'size', $field );

		$visibility = ! empty( $field['format'] ) && $field['format'] === self::FORMAT_USER ? '' : 'wpforms-hidden';
		$this->field_option( 'placeholder', $field, [ 'class' => $visibility ] );

		$this->field_option( 'css', $field );
		$this->field_option( 'label_hide', $field );
		$this->field_option( 'advanced-options', $field, [ 'markup' => 'close' ] );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.8.2
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_preview( $field ) {

		$price       = ! empty( $field['price'] ) ? wpforms_format_amount( wpforms_sanitize_amount( $field['price'] ), true ) : wpforms_format_amount( 0, true );
		$placeholder = ! empty( $field['placeholder'] ) ? $field['placeholder'] : wpforms_format_amount( 0 );
		$format      = ! empty( $field['format'] ) ? $field['format'] : self::FORMAT_SINGLE;
		$value       = ! empty( $field['price'] ) ? wpforms_format_amount( wpforms_sanitize_amount( $field['price'] ) ) : '';

		$this->field_preview_option( 'label', $field );

		echo '<div class="format-selected-' . esc_attr( $format ) . ' format-selected">';

		echo '<p class="item-price">';
		printf(
		/* translators: %s - price amount. */
			esc_html__( 'Price: %s', 'wpforms-lite' ),
			'<span class="price">' . esc_html( $price ) . '</span>'
		);
		echo '</p>';

		printf(
			'<input type="text" placeholder="%s" class="primary-input" value="%s" readonly>',
			esc_attr( $placeholder ),
			esc_attr( $value )
		);

		$this->field_preview_option( 'description', $field );

		echo '<p class="item-price-hidden">';
		esc_html_e( 'Note: Item type is set to hidden and will not be visible when viewing the form.', 'wpforms-lite' );
		echo '</p>';

		echo '</div>';
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.8.2
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated field attributes.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Shortcut for easier access.
		$primary = $field['properties']['inputs']['primary'];

		$field_format = ! empty( $field['format'] ) ? $field['format'] : self::FORMAT_SINGLE;

		// Placeholder attribute is only applicable to password, search, tel, text and url inputs, not hidden.
		if ( $field_format !== self::FORMAT_USER ) {
			unset( $primary['attr']['placeholder'] );
		}

		switch ( $field_format ) {
			case self::FORMAT_SINGLE:
			case self::FORMAT_HIDDEN:
				if ( $field_format === self::FORMAT_SINGLE ) {
					$price = ! empty( $field['price'] ) ? $field['price'] : 0;

					echo '<div class="wpforms-single-item-price">';
					printf(
					/* translators: %s - price amount. */
						esc_html__( 'Price: %s', 'wpforms-lite' ),
						'<span class="wpforms-price">' . esc_html( wpforms_format_amount( wpforms_sanitize_amount( $price ), true ) ) . '</span>'
					);
					echo '</div>';
				}

				// Primary price field.
				printf(
					'<input type="hidden" %s>',
					wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
				break;

			case self::FORMAT_USER:
				printf(
					'<input type="text" %s>',
					wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
				break;

			default:
				break;
		}
	}

	/**
	 * Validate field on form submit.
	 *
	 * @since 1.8.2
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Field data submitted by a user.
	 * @param array  $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		// If field is required, check for data.
		if (
			empty( $field_submit ) &&
			! empty( $form_data['fields'][ $field_id ]['required'] )
		) {
			wpforms()->get( 'process' )->errors[ $form_data['id'] ][ $field_id ] = wpforms_get_required_label();

			return;
		}

		// If field format is not user provided, validate the amount posted.
		if (
			! empty( $field_submit ) &&
			$form_data['fields'][ $field_id ]['format'] !== self::FORMAT_USER
		) {

			$price  = wpforms_sanitize_amount( $form_data['fields'][ $field_id ]['price'] );
			$submit = wpforms_sanitize_amount( $field_submit );

			if ( $price !== $submit ) {
				wpforms()->get( 'process' )->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Amount mismatch', 'wpforms-lite' );
			}
		}

		if (
			! empty( $field_submit ) &&
			$form_data['fields'][ $field_id ]['format'] === self::FORMAT_USER
		) {
			$submit = wpforms_sanitize_amount( $field_submit );

			if ( $submit < 0 ) {
				wpforms()->get( 'process' )->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Amount can\'t be negative' , 'wpforms-lite' );
			}
		}
	}

	/**
	 * Format and sanitize field.
	 *
	 * @since 1.8.2
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Field data submitted by a user.
	 * @param array  $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$field = $form_data['fields'][ $field_id ];
		$name  = ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';

		// Only trust the value if the field is user format.
		if ( $field['format'] === self::FORMAT_USER ) {
			$amount = wpforms_sanitize_amount( $field_submit );
		} else {
			$amount = wpforms_sanitize_amount( $field['price'] );
		}

		wpforms()->get( 'process' )->fields[ $field_id ] = [
			'name'       => $name,
			'value'      => wpforms_format_amount( $amount, true ),
			'amount'     => wpforms_format_amount( $amount ),
			'amount_raw' => $amount,
			'currency'   => wpforms_get_currency(),
			'id'         => absint( $field_id ),
			'type'       => sanitize_key( $this->type ),
		];
	}
}
