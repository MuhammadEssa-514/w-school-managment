<?php

namespace WPForms\Integrations\Stripe\Admin;

use WPForms\Integrations\Stripe\Helpers;

/**
 * Stripe related admin notices.
 *
 * @since 1.8.2
 */
class Notices {

	/**
	 * Get a notice if a license is insufficient not to be charged a fee.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	public static function get_fee_notice() {

		if ( ! Helpers::is_application_fee_supported() ) {
			return '';
		}

		$is_allowed_license = Helpers::is_allowed_license_type();
		$is_active_license  = Helpers::is_license_active();
		$notice             = '';

		if ( $is_allowed_license && $is_active_license ) {
			return $notice;
		}

		if ( ! $is_allowed_license ) {
			$notice = self::get_non_pro_license_level_notice();
		} elseif ( ! $is_active_license ) {
			$notice = self::get_non_active_license_notice();
		}

		if ( wpforms_is_admin_page( 'builder' ) ) {
			return sprintf( '<p class="wpforms-alert wpforms-alert-info">%s</p>', $notice );
		}

		return sprintf( '<div class="wpforms-stripe-notice-info"><p>%s</p></div>', $notice );
	}

	/**
	 * Get a fee notice for a non-active license.
	 *
	 * If the license is NOT set/activated, show the notice to activate it.
	 * Otherwise, show the notice to renew it.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private static function get_non_active_license_notice() {

		$setting_page_url = add_query_arg(
			[
				'page' => 'wpforms-settings',
				'view' => 'general',
			],
			admin_url( 'admin.php' )
		);

		// The license is not set/activated at all.
		if ( empty( wpforms_get_license_key() ) ) {
			return sprintf(
				wp_kses( /* translators: %s - general admin settings page URL. */
					__( '<strong>Pay as you go pricing:</strong> 3%% fee per-transaction + Stripe fees. <a href="%s">Activate your license</a> to remove additional fees and unlock powerful features.', 'wpforms-lite' ),
					[
						'strong' => [],
						'a'      => [
							'href' => [],
						],
					]
				),
				esc_url( $setting_page_url )
			);
		}

		return sprintf(
			wp_kses( /* translators: %s - general admin settings page URL. */
				__( '<strong>Pay as you go pricing:</strong> 3%% fee per-transaction + Stripe fees. <a href="%s">Renew your license</a> to remove additional fees and unlock powerful features.', 'wpforms-lite' ),
				[
					'strong' => [],
					'a'      => [
						'href' => [],
					],
				]
			),
			esc_url( $setting_page_url )
		);
	}

	/**
	 * Get a fee notice for license levels below the `pro`.
	 *
	 * Show the notice to upgrade to Pro.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private static function get_non_pro_license_level_notice() {

		$utm_content  = 'Stripe Pro - Remove Fees';
		$utm_medium   = wpforms_is_admin_page( 'builder' ) ? 'Payment Settings' : 'Settings - Payments';
		$upgrade_link = wpforms()->is_pro() ? wpforms_admin_upgrade_link( $utm_medium, $utm_content ) : wpforms_utm_link( 'https://wpforms.com/lite-upgrade/', $utm_medium, $utm_content );

		return sprintf(
			wp_kses( /* translators: %s - WPForms.com Upgrade page URL. */
				__( '<strong>Pay as you go pricing:</strong> 3%% fee per-transaction + Stripe fees. <a href="%s" target="_blank">Upgrade to Pro</a> to remove additional fees and unlock powerful features.', 'wpforms-lite' ),
				[
					'strong' => [],
					'a'      => [
						'href'   => [],
						'target' => [],
					],
				]
			),
			esc_url( $upgrade_link )
		);
	}
}
