<?php

namespace WPForms\Integrations\Stripe;

/**
 * Compatibility with the Stripe addon.
 *
 * @since 1.8.2
 */
class StripeAddonCompatibility {

	/**
	 * Minimum compatible version of the Stripe addon.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	const MIN_COMPAT_VERSION = '3.0.0';

	/**
	 * Register hooks.
	 *
	 * @since 1.8.2
	 */
	public function hooks() {

		// Warn the user about the fact that the not supported addon has been installed.
		add_action( 'admin_notices', [ $this, 'display_legacy_addon_notice' ] );
	}

	/**
	 * Check if the supported Stripe addon is active.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public function is_supported_version() {

		return defined( 'WPFORMS_STRIPE_VERSION' )
			&& version_compare( WPFORMS_STRIPE_VERSION, self::MIN_COMPAT_VERSION, '>=' );
	}

	/**
	 * Display wp-admin notification saying user first have to update addon to the latest version.
	 *
	 * @since 1.8.2
	 */
	public function display_legacy_addon_notice() {

		echo '<div class="notice notice-error"><p>';
			esc_html_e( 'The WPForms Stripe addon is out of date. To avoid payment processing issues, please upgrade the Stripe addon to the latest version.', 'wpforms-lite' );
		echo '</p></div>';
	}

	/**
	 * Get documentation link.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_documentation_link() {

		return wpforms_utm_link(
			'https://wpforms.com/how-to-accept-payments-with-stripe/',
			'wp-admin',
			'admin-notice',
			'stripe-addon-compatibility'
		);
	}
}
