<?php

namespace WPForms\Admin\Payments\Views\Coupons;

use WPForms\Admin\Payments\Views\Overview\Helpers;
use WPForms\Admin\Payments\Views\PaymentsViewsInterface;

/**
 * Payments Coupons Education class.
 *
 * @since 1.8.2.2
 */
class Education implements PaymentsViewsInterface {

	/**
	 * Coupons addon data.
	 *
	 * @since 1.8.2.2
	 *
	 * @var array
	 */
	private $addon;

	/**
	 * Initialize class.
	 *
	 * @since 1.8.2.2
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.8.2.2
	 */
	private function hooks() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Get the page label.
	 *
	 * @since 1.8.2.2
	 *
	 * @return string
	 */
	public function get_tab_label() {

		return __( 'Coupons', 'wpforms-lite' );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.8.2.2
	 */
	public function enqueue_scripts() {

		// Lity - lightbox for images.
		wp_enqueue_style(
			'wpforms-lity',
			WPFORMS_PLUGIN_URL . 'assets/lib/lity/lity.min.css',
			null,
			'3.0.0'
		);

		wp_enqueue_script(
			'wpforms-lity',
			WPFORMS_PLUGIN_URL . 'assets/lib/lity/lity.min.js',
			[ 'jquery' ],
			'3.0.0',
			true
		);
	}

	/**
	 * Check if the current user has the capability to view the page.
	 *
	 * @since 1.8.2.2
	 *
	 * @return bool
	 */
	public function current_user_can() {

		if ( ! wpforms_current_user_can() ) {
			return false;
		}

		$this->addon = wpforms()->get( 'addons' )->get_addon( 'coupons' );

		if (
			empty( $this->addon ) ||
			empty( $this->addon['status'] ) ||
			empty( $this->addon['action'] )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Page heading content.
	 *
	 * @since 1.8.2.2
	 */
	public function heading() {

		Helpers::get_default_heading();
	}

	/**
	 * Page content.
	 *
	 * @since 1.8.2.2
	 */
	public function display() {

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render( 'education/admin/payments/coupons', $this->addon, true );
	}
}
