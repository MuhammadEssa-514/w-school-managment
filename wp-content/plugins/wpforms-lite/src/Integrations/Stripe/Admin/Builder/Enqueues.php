<?php

namespace WPForms\Integrations\Stripe\Admin\Builder;

/**
 * Script enqueues for the Stripe Builder settings panel.
 *
 * @since 1.8.2
 */
class Enqueues {

	/**
	 * Initialize.
	 *
	 * @since 1.8.2
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.8.2
	 */
	private function hooks() {

		add_filter( 'wpforms_builder_strings', [ $this, 'javascript_strings' ], 10, 2 );
		add_action( 'wpforms_builder_enqueues', [ $this, 'enqueues' ] );
	}

	/**
	 * Add our localized strings to be available in the form builder.
	 *
	 * @since 1.8.2
	 *
	 * @param array $strings Form builder JS strings.
	 * @param array $form    Form data and settings.
	 *
	 * @return array
	 */
	public function javascript_strings( $strings, $form = [] ) {

		$strings['stripe_recurring_email'] = esc_html__( 'When recurring subscription payments are enabled, the Customer Email is required. Please go to the Stripe payment settings and select a Customer Email.', 'wpforms-lite' );

		return $strings;
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 1.8.2
	 *
	 * @param string|null $view Current view.
	 */
	public function enqueues( $view = null ) {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-builder-stripe',
			WPFORMS_PLUGIN_URL . "assets/js/integrations/stripe/admin-builder-stripe{$min}.js",
			[ 'conditionals' ],
			WPFORMS_VERSION,
			false
		);

		/**
		 * Allow to filter builder stripe script data.
		 *
		 * @since 1.8.2
		 *
		 * @param array $data Script data.
		 */
		$script_data = apply_filters( 'wpforms_integrations_stripe_admin_builder_enqueues_data', [ 'field_slugs' => [ 'stripe-credit-card' ] ] );

		wp_localize_script(
			'wpforms-builder-stripe',
			'wpforms_builder_stripe',
			$script_data
		);
	}
}
