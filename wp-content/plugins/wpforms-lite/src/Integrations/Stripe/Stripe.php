<?php

namespace WPForms\Integrations\Stripe;

use WPForms\Integrations\IntegrationInterface;

/**
 * Integration of the Stripe payment gateway.
 *
 * @since 1.8.2
 */
final class Stripe implements IntegrationInterface {

	/**
	 * Determine if the integration is allowed to load.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public function allow_load() {

		// Determine whether the Stripe addon version is compatible with the WPForms plugin version.
		$addon_compat = new StripeAddonCompatibility();

		if (
			Helpers::is_pro() &&
			! $addon_compat->is_supported_version()
		) {
			$addon_compat->hooks();

			return false;
		}

		/**
		 * Whether the integration is allowed to load.
		 *
		 * @since 1.8.2
		 *
		 * @param bool $is_allowed Integration loading state.
		 */
		return (bool) apply_filters( 'wpforms_integrations_stripe_allow_load', true );
	}

	/**
	 * Load the integration.
	 *
	 * @since 1.8.2
	 */
	public function load() {

		if ( wpforms_is_admin_page( 'builder' ) ) {
			( new Admin\Builder\Enqueues() )->init();
		}

		// Bail early for paid users with active Stripe addon.
		if ( Helpers::is_pro() ) {
			return;
		}

		$api = new Api\PaymentIntents();

		$api->init();

		( new Process() )->init( $api );
		( new Frontend() )->init( $api );

		if ( wpforms_is_admin_page( 'settings', 'payments' ) ) {
			( new Admin\Settings() )->init();
		}

		if ( wpforms_is_admin_page( 'builder' ) ) {
			( new Admin\Builder\Settings() )->init();
		}
	}
}
