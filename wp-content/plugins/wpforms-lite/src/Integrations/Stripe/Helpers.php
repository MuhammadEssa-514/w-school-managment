<?php

namespace WPForms\Integrations\Stripe;

/**
 * Stripe related helper methods.
 *
 * @since 1.8.2
 */
class Helpers {

	/**
	 * Stripe connection modes.
	 *
	 * @since 1.8.2
	 */
	const CONNECTION_MODES = [ 'live', 'test' ];

	/**
	 * Get field slug.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	public static function get_field_slug() {

		return self::is_pro() ? wpforms_stripe()->api->get_config( 'field_slug' ) : 'stripe-credit-card';
	}

	/**
	 * Determine whether the Stripe field is in the form.
	 *
	 * @since 1.8.2
	 *
	 * @param array $forms    Form data (e.g. forms on a current page).
	 * @param bool  $multiple Must be 'true' if $forms contain multiple forms.
	 *
	 * @return bool
	 */
	public static function has_stripe_field( $forms, $multiple = false ) {

		$slug = self::get_field_slug();

		if ( empty( $slug ) ) {
			return false;
		}

		return wpforms_has_field_type( $slug, $forms, $multiple ) !== false;
	}

	/**
	 * Determine whether the Stripe is enabled in forms used on the page.
	 *
	 * @since 1.8.2
	 *
	 * @param array $forms Form data (e.g. forms on a current page).
	 *
	 * @return bool
	 */
	public static function has_stripe_enabled( $forms ) {

		foreach ( $forms as $form ) {
			if ( ! empty( $form['payments']['stripe']['enable'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether Stripe keys are configured on the Payments settings page.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode to check the keys for.
	 *
	 * @return bool
	 */
	public static function has_stripe_keys( $mode = '' ) {

		$mode = self::validate_stripe_mode( $mode );

		return wpforms_setting( "stripe-{$mode}-secret-key", false ) && wpforms_setting( "stripe-{$mode}-publishable-key", false );
	}

	/**
	 * Validate Stripe mode name to ensure it's either 'live' or 'test'.
	 * If given mode is invalid, fetches current Stripe mode.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode to validate.
	 *
	 * @return string
	 */
	public static function validate_stripe_mode( $mode ) {

		if ( empty( $mode ) || ! in_array( $mode, self::CONNECTION_MODES, true ) ) {
			return self::get_stripe_mode();
		}

		return $mode;
	}

	/**
	 * Get Stripe mode from the WPForms settings.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	public static function get_stripe_mode() {

		return wpforms_setting( 'stripe-test-mode' ) ? 'test' : 'live';
	}

	/**
	 * Get Stripe key from the WPForms settings.
	 *
	 * @since 1.8.2
	 *
	 * @param string $type Key type (e.g. 'publishable' or 'secret').
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return string
	 */
	public static function get_stripe_key( $type, $mode = '' ) {

		$mode = self::validate_stripe_mode( $mode );

		if ( ! in_array( $type, [ 'publishable', 'secret' ], true ) ) {
			return '';
		}

		$key = wpforms_setting( "stripe-{$mode}-{$type}-key" );

		if ( ! empty( $key ) && is_string( $key ) ) {
			return sanitize_text_field( $key );
		}

		return '';
	}

	/**
	 * Set Stripe key from the WPForms settings.
	 *
	 * @since 1.8.2
	 *
	 * @param string $value Key string to set.
	 * @param string $type  Key type (e.g. 'publishable' or 'secret').
	 * @param string $mode  Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return bool
	 */
	public static function set_stripe_key( $value, $type, $mode = '' ) {

		$mode = self::validate_stripe_mode( $mode );

		if ( ! in_array( $type, [ 'publishable', 'secret' ], true ) ) {
			return false;
		}

		$key              = "stripe-{$mode}-{$type}-key";
		$settings         = (array) get_option( 'wpforms_settings', [] );
		$settings[ $key ] = sanitize_text_field( $value );

		return wpforms_update_settings( $settings );
	}

	/**
	 * Determine whether a license key is active.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public static function is_license_active() {

		$license = (array) get_option( 'wpforms_license', [] );

		return ! empty( wpforms_get_license_key() ) &&
			empty( $license['is_expired'] ) &&
			empty( $license['is_disabled'] ) &&
			empty( $license['is_invalid'] );
	}

	/**
	 * Determine whether a license type is allowed.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public static function is_allowed_license_type() {

		return in_array( wpforms_get_license_type(), [ 'pro', 'elite', 'agency', 'ultimate' ], true );
	}

	/**
	 * Determine whether a license is ok.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public static function is_license_ok() {

		return self::is_license_active() && self::is_allowed_license_type();
	}

	/**
	 * Determine whether the addon is activated.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public static function is_addon_active() {

		return function_exists( 'wpforms_stripe' );
	}

	/**
	 * Determine whether the addon is activated and appropriate license is set.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public static function is_pro() {

		return self::is_addon_active() && self::is_allowed_license_type();
	}

	/**
	 * Get authorization options used for every Stripe transaction as recommended in Stripe official docs.
	 *
	 * @link https://stripe.com/docs/connect/authentication#api-keys
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	public static function get_auth_opts() {

		return [ 'api_key' => self::get_stripe_key( 'secret' ) ];
	}

	/**
	 * Determine whether the Payment element mode is enabled.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public static function is_payment_element_enabled() {

		return wpforms_setting( 'stripe-card-mode' ) === 'payment';
	}

	/**
	 * Determine whether the application fee is supported.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public static function is_application_fee_supported() {

		$mode    = self::get_stripe_mode();
		$country = get_option( "wpforms_stripe_{$mode}_account_country", '' );

		return ! in_array( $country, [ 'br', 'in', 'mx' ], true );
	}
}
