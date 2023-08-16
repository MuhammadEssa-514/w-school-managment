<?php

namespace WPForms\Integrations\Stripe\Admin\Builder\Traits;

use WPForms\Integrations\Stripe\Helpers;
use WPForms\Integrations\Stripe\Admin\Notices;

/**
 * Payment builder settings content trait.
 *
 * @since 1.8.2
 */
trait ContentTrait {

	/**
	 * Display content inside the panel content area.
	 *
	 * @since 1.8.2
	 */
	public function builder_content() {

		$content = $this->no_keys_alert();

		if ( ! $content ) {
			$content  = $this->stripe_credit_card_alert();
			$content .= $this->enable_payments_toggle();
			$content .= $this->content_section_body();
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content;
	}

	/**
	 * Display alert if Stripe keys are not set.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function no_keys_alert() {

		if ( Helpers::has_stripe_keys() ) {
			return '';
		}

		$content  = '<p class="wpforms-alert wpforms-alert-info">';
		$content .= sprintf(
			wp_kses( /* translators: %s - admin area Payments settings page URL. */
				__( 'Heads up! Stripe payments can\'t be enabled yet. First, please connect to your Stripe account on the <a href="%s">WPForms Settings</a> page.', 'wpforms-lite' ),
				[
					'a' => [
						'href' => [],
					],
				]
			),
			esc_url( admin_url( 'admin.php?page=wpforms-settings&view=payments' ) )
		);
		$content .= '</p>';

		return $content;
	}

	/**
	 * Display alert if Stripe Credit Card field is not added to the form.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function stripe_credit_card_alert() {

		return sprintf(
			'<p class="wpforms-alert wpforms-alert-info" id="stripe-credit-card-alert">%s</p>',
			esc_html__( 'To use Stripe payments you need to add the Stripe Credit Card field to the form', 'wpforms-lite' )
		);
	}

	/**
	 * Display toggle to enable Stripe payments.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function enable_payments_toggle() {

		return wpforms_panel_field(
			'toggle',
			'stripe',
			'enable',
			$this->form_data,
			esc_html__( 'Enable Stripe payments', 'wpforms-lite' ),
			[
				'parent'  => 'payments',
				'default' => '0',
			],
			false
		);
	}

	/**
	 * Display content inside the panel content section.
	 *
	 * @since 1.8.2
	 *
	 * @return string Stripe settings builder content section.
	 */
	private function content_section_body() {

		$content  = '<div class="wpforms-panel-content-section-stripe-body">';
		$content .= Notices::get_fee_notice();

		$content .= wpforms_panel_field(
			'text',
			'stripe',
			'payment_description',
			$this->form_data,
			esc_html__( 'Payment Description', 'wpforms-lite' ),
			[
				'parent'  => 'payments',
				'tooltip' => esc_html__( 'Enter your payment description. Eg: Donation for the soccer team. Only used for standard one-time payments.', 'wpforms-lite' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			'stripe',
			'receipt_email',
			$this->form_data,
			esc_html__( 'Stripe Payment Receipt', 'wpforms-lite' ),
			[
				'parent'      => 'payments',
				'field_map'   => [ 'email' ],
				'placeholder' => esc_html__( '--- Select Email ---', 'wpforms-lite' ),
				'tooltip'     => esc_html__( 'If you would like to have Stripe send a receipt after payment, select the email field to use. This is optional but recommended. Only used for standard one-time payments.', 'wpforms-lite' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			'stripe',
			'customer_email',
			$this->form_data,
			esc_html__( 'Customer Email', 'wpforms-lite' ),
			[
				'parent'      => 'payments',
				'field_map'   => [ 'email' ],
				'placeholder' => esc_html__( '--- Select Email ---', 'wpforms-lite' ),
				'tooltip'     => esc_html__( 'Select the field that contains the customer\'s email address. This is optional but recommended.', 'wpforms-lite' ),
			],
			false
		);

		$content .= $this->single_payments_conditional_logic_section();
		$content .= sprintf( '<h2>%s</h2>', esc_html__( 'Subscriptions', 'wpforms-lite' ) );

		$content .= wpforms_panel_field(
			'toggle',
			'stripe',
			'enable',
			$this->form_data,
			esc_html__( 'Enable recurring subscription payments', 'wpforms-lite' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'default'    => '0',
			],
			false
		);

		$content .= wpforms_panel_field(
			'text',
			'stripe',
			'name',
			$this->form_data,
			esc_html__( 'Plan Name', 'wpforms-lite' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'tooltip'    => esc_html__( 'Enter the subscription name. Eg: Email Newsletter. Subscription period and price are automatically appended. If left empty the form name will be used.', 'wpforms-lite' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			'stripe',
			'period',
			$this->form_data,
			esc_html__( 'Recurring Period', 'wpforms-lite' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'default'    => 'yearly',
				'options'    => [
					'daily'      => esc_html__( 'Daily', 'wpforms-lite' ),
					'weekly'     => esc_html__( 'Weekly', 'wpforms-lite' ),
					'monthly'    => esc_html__( 'Monthly', 'wpforms-lite' ),
					'quarterly'  => esc_html__( 'Quarterly', 'wpforms-lite' ),
					'semiyearly' => esc_html__( 'Semi-Yearly', 'wpforms-lite' ),
					'yearly'     => esc_html__( 'Yearly', 'wpforms-lite' ),
				],
				'tooltip'    => esc_html__( 'How often you would like the charge to recur.', 'wpforms-lite' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			'stripe',
			'email',
			$this->form_data,
			esc_html__( 'Customer Email', 'wpforms-lite' ),
			[
				'parent'      => 'payments',
				'subsection'  => 'recurring',
				'field_map'   => [ 'email' ],
				'placeholder' => esc_html__( '--- Select Email ---', 'wpforms-lite' ),
				'tooltip'     => esc_html__( "Select the field that contains the customer's email address. This field is required.", 'wpforms-lite' ),
			],
			false
		);

		$content .= $this->recurring_payments_conditional_logic_section();
		$content .= '</div>';

		return $content;
	}
}
