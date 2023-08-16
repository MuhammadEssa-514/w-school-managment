<?php

namespace WPForms\Admin\Payments\Views;

use WPForms\Admin\Payments\ScreenOptions;
use WPForms\Admin\Payments\Views\Overview\Helpers;
use WPForms\Db\Payments\ValueValidator;

/**
 * Payments Overview Page class.
 *
 * @since 1.8.2
 */
class Single implements PaymentsViewsInterface {

	/**
	 * Abort. Bail on proceeding to process the page.
	 *
	 * @since 1.8.2
	 *
	 * @var bool
	 */
	private $abort = false;

	/**
	 * The human readable error message.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	private $abort_message;

	/**
	 * Payment object.
	 *
	 * @since 1.8.2
	 *
	 * @var object
	 */
	private $payment;

	/**
	 * Payment meta.
	 *
	 * @since 1.8.2
	 *
	 * @var array
	 */
	private $payment_meta;

	/**
	 * Initialize class.
	 *
	 * @since 1.8.2
	 */
	public function init() {

		$this->setup();
		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.8.2
	 */
	private function hooks() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Get the tab label.
	 *
	 * @since 1.8.2.2
	 *
	 * @return string
	 */
	public function get_tab_label() {

		return '';
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.8.2
	 */
	public function enqueue_assets() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'tooltipster',
			WPFORMS_PLUGIN_URL . 'assets/lib/jquery.tooltipster/jquery.tooltipster.min.css',
			null,
			'4.2.6'
		);

		wp_enqueue_script(
			'tooltipster',
			WPFORMS_PLUGIN_URL . 'assets/lib/jquery.tooltipster/jquery.tooltipster.min.js',
			[ 'jquery' ],
			'4.2.6',
			true
		);

		wp_enqueue_script(
			'wpforms-admin-payments-single',
			WPFORMS_PLUGIN_URL . "assets/js/components/admin/payments/single{$min}.js",
			[ 'tooltipster' ],
			WPFORMS_VERSION,
			true
		);
	}

	/**
	 * Setup data.
	 *
	 * @since 1.8.2
	 */
	private function setup() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$payment_id = ! empty( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : 0;

		if ( ! $payment_id ) {
			$this->abort_message = esc_html__( 'It looks like the provided payment ID is not valid.', 'wpforms-lite' );
			$this->abort         = true;

			return;
		}

		$this->payment = wpforms()->get( 'payment' )->get( $payment_id );

		// No payment was found.
		if ( empty( $this->payment ) ) {
			$this->abort_message = esc_html__( 'It looks like the payment you are trying to access is no longer available.', 'wpforms-lite' );
			$this->abort         = true;

			return;
		}

		$this->payment_meta = wpforms()->get( 'payment_meta' )->get_all( $payment_id );
	}

	/**
	 * Check if the current user has the capability to view the page.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public function current_user_can() {

		return wpforms_current_user_can();
	}

	/**
	 * Page heading.
	 *
	 * @since 1.8.2
	 */
	public function heading() {

		if ( $this->abort ) {
			return;
		}

		$payment_prev = wpforms()->get( 'payment_queries' )->get_prev( $this->payment->id, [ 'mode' => $this->payment->mode ] );
		$payment_next = wpforms()->get( 'payment_queries' )->get_next( $this->payment->id, [ 'mode' => $this->payment->mode ] );
		$prev_url     = ! empty( $payment_prev ) ? add_query_arg(
			[
				'page'       => 'wpforms-payments',
				'view'       => 'single',
				'payment_id' => (int) $payment_prev->id,
			],
			admin_url( 'admin.php' )
		) : '';
		$next_url     = ! empty( $payment_next ) ? add_query_arg(
			[
				'page'       => 'wpforms-payments',
				'view'       => 'single',
				'payment_id' => (int) $payment_next->id,
			],
			admin_url( 'admin.php' )
		) : '';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			'admin/payments/single/heading-navigation',
			[
				'count'        => (int) wpforms()->get( 'payment_queries' )->count_all( [ 'mode' => $this->payment->mode ] ),
				'prev_count'   => (int) wpforms()->get( 'payment_queries' )->get_prev_count( $this->payment->id, [ 'mode' => $this->payment->mode ] ),
				'prev_url'     => $prev_url,
				'prev_class'   => empty( $payment_prev ) ? 'inactive' : '',
				'next_url'     => $next_url,
				'next_class'   => empty( $payment_next ) ? 'inactive' : '',
				'overview_url' => add_query_arg(
					[
						'page' => 'wpforms-payments',
					],
					admin_url( 'admin.php' )
				),
			],
			true
		);
	}

	/**
	 * Page content.
	 *
	 * @since 1.8.2
	 */
	public function display() {

		if ( $this->abort ) {

			echo '<div class="wpforms-admin-content">';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpforms_render(
					'admin/payments/single/no-payment',
					[
						'message' => $this->abort_message,
					],
					true
				);
			echo '</div>';

			return;
		}

		$screen_options = ScreenOptions::get_single_page_options();

		echo '<div id="poststuff"><div id="post-body" class="metabox-holder columns-2">';
			echo '<div id="post-body-content">';

				$this->payment_details();
				$this->subscription_details();
				$this->education_details();

				if ( ! empty( $screen_options['advanced'] ) ) {
					$this->advanced_details();
				}

				$this->entry_details();
			echo '</div>';
			echo '<div id="postbox-container-1" class="postbox-container">';
				$this->details();

				if ( ! empty( $screen_options['log'] ) ) {
					$this->log();
				}
		echo '</div></div></div>';
	}

	/**
	 * Payment details output.
	 *
	 * @since 1.8.2
	 */
	private function payment_details() {

		$payment_type_class = ! empty( $this->payment->subscription_id ) ? 'subscription' : 'one-time';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			'admin/payments/single/payment-details',
			[
				'title'               => __( 'Payment Details', 'wpforms-lite' ),
				'payment_id'          => "#{$this->payment->id}",
				'gateway_link'        => $this->get_gateway_transaction_link(),
				'gateway_text'        => sprintf( /* translators: %s - payment gateway name. */
					__( 'View in %s', 'wpforms-lite' ),
					$this->get_gateway_name()
				),
				'gateway_action_text' => __( 'Refund', 'wpforms-lite' ),
				'gateway_action_link' => $this->get_gateway_action_link( 'refund' ),
				'status'              => $this->payment->status,
				'stat_cards'          => [
					'total'  => [
						'label'          => esc_html__( 'Total', 'wpforms-lite' ),
						'value'          => wpforms_format_amount( $this->payment->total_amount, true ),
						'button_classes' => [
							'total',
							'is-amount',
						],
					],
					'type'   => [
						'label'          => esc_html__( 'Type', 'wpforms-lite' ),
						'value'          => $this->payment->subscription_id ? __( 'Subscription', 'wpforms-lite' ) : __( 'One-time', 'wpforms-lite' ),
						'button_classes' => [
							$payment_type_class,
						],
					],
					'method' => [
						'label'          => esc_html__( 'Method', 'wpforms-lite' ),
						'value'          => $this->get_payment_method(),
						'button_classes' => [
							'method',
						],
						'tooltip'        => $this->get_payment_method_details(),
					],
					'coupon' => [
						'label'          => esc_html__( 'Coupon', 'wpforms-lite' ),
						'value'          => $this->get_coupon_value(),
						'button_classes' => [
							'coupon',
							'upsell',
						],
						'tooltip'        => nl2br( $this->get_coupon_info() ),
					],
				],
			],
			true
		);
	}

	/**
	 * Subscription details output.
	 *
	 * @since 1.8.2
	 */
	private function subscription_details() {

		if ( empty( $this->payment->subscription_id ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			'admin/payments/single/payment-details',
			[
				'title'               => __( 'Subscription Details', 'wpforms-lite' ),
				'gateway_link'        => $this->get_gateway_subscription_link(),
				'gateway_text'        => sprintf( /* translators: %s - payment gateway name. */
					__( 'View in %s', 'wpforms-lite' ),
					$this->get_gateway_name()
				),
				'gateway_action_text' => __( 'Cancel', 'wpforms-lite' ),
				'gateway_action_link' => $this->get_gateway_action_link( 'cancel' ),
				'status'              => $this->payment->subscription_status,
				'stat_cards'          => [
					'total'   => [
						'label'          => esc_html__( 'Lifetime Total', 'wpforms-lite' ),
						'value'          => wpforms_format_amount( $this->payment->total_amount, true ),
						'button_classes' => [
							'lifetime-total',
							'is-amount',
						],
					],
					'cycle'   => [
						'label'          => esc_html__( 'Billing Cycle', 'wpforms-lite' ),
						'value'          => $this->get_subscription_cycle(),
						'button_classes' => [
							'cycle',
						],
					],
					'billed'  => [
						'label'          => esc_html__( 'Times Billed', 'wpforms-lite' ),
						'value'          => Helpers::get_placeholder_na_text( false ), // Hard code to 'N/A' as we don't support webhooks for now.
						'button_classes' => [
							'cycle',
						],
					],
					'renewal' => [
						'label'          => esc_html__( 'Renewal Date', 'wpforms-lite' ),
						'value'          => $this->get_renewal_date(),
						'button_classes' => [
							'date',
						],
					],
				],
			],
			true
		);
	}

	/**
	 * Get Subscription cycle.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_subscription_cycle() {

		$allowed_intervals = ValueValidator::get_allowed_subscription_intervals();

		if ( ! isset( $this->payment_meta['subscription_period']->value, $allowed_intervals[ $this->payment_meta['subscription_period']->value ] ) ) {
			return '';
		}

		return wpforms_format_amount( $this->payment->total_amount, true ) . ' / ' . $allowed_intervals[ $this->payment_meta['subscription_period']->value ];
	}

	/**
	 * Get Subscription renewal date.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_renewal_date() {

		$converted_periods = [
			'daily'      => '+1 day',
			'weekly'     => '+1 week',
			'monthly'    => '+1 month',
			'quarterly'  => '+3 month',
			'semiyearly' => '+6 month',
			'yearly'     => '+1 year',
		];

		if ( ! isset( $this->payment_meta['subscription_period']->value, $converted_periods[ $this->payment_meta['subscription_period']->value ] ) ) {
			return '';
		}

		return gmdate( 'M d, Y', strtotime( $this->payment->date_updated_gmt . $converted_periods[ $this->payment_meta['subscription_period']->value ] ) );
	}

	/**
	 * Get payment method type.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_payment_method() {

		$method = isset( $this->payment_meta['credit_card_method'] ) ? ucfirst( $this->payment_meta['credit_card_method']->value ) : '';

		if ( $method ) {
			return $method;
		}

		return isset( $this->payment_meta['method_type'] ) ? ucfirst( $this->payment_meta['method_type']->value ) : Helpers::get_placeholder_na_text( false );
	}

	/**
	 * Get payment method details.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_payment_method_details() {

		if (
			! isset( $this->payment_meta['method_type'] ) ||
			$this->payment_meta['method_type']->value !== 'card' ||
			empty( $this->payment_meta['credit_card_last4'] ) ||
			empty( $this->payment_meta['credit_card_expires'] )
		) {
			return '';
		}

		$credit_card_last = 'xxxx xxxx xxxx ' . $this->payment_meta['credit_card_last4']->value;
		$expires_in       = sprintf( /* translators: %s - credit card expiry date. */
			__( 'Expires %s', 'wpforms-lite' ),
			$this->payment_meta['credit_card_expires']->value
		);

		$output = '<div>';

		if ( ! empty( $this->payment_meta['credit_card_name'] ) ) {
			$output .= '<span>' . esc_html( $this->payment_meta['credit_card_name']->value ) . '</span></br>';
		}

		$output .= '<span>' . esc_html( $credit_card_last ) . '</span></br>';
		$output .= '<span>' . esc_html( $expires_in ) . '</span>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Get coupon info.
	 *
	 * @since 1.8.2.2
	 *
	 * @return string
	 */
	private function get_coupon_info() {

		$coupon_info = ! empty( $this->payment_meta['coupon_info']->value ) ? $this->payment_meta['coupon_info']->value : '';

		/**
		 * Allow modifying coupon info.
		 *
		 * @since 1.8.2.2
		 *
		 * @param string $coupon_info  Coupon info.
		 * @param object $payment      Payment object.
		 * @param array  $payment_meta Payment meta.
		 */
		return apply_filters( 'wpforms_admin_payments_views_single_get_coupon_info', $coupon_info, $this->payment, $this->payment_meta );
	}

	/**
	 * Get coupon value.
	 *
	 * @since 1.8.2.2
	 *
	 * @return string
	 */
	private function get_coupon_value() {

		return ! empty( $this->payment_meta['coupon_value']->value ) ? sprintf( '-%s', $this->payment_meta['coupon_value']->value ) : '';
	}

	/**
	 * Education notice for lite users output.
	 *
	 * @since 1.8.2
	 */
	private function education_details() {

		if ( in_array( wpforms_get_license_type(), [ 'pro', 'elite', 'agency', 'ultimate' ], true ) ) {
			return;
		}

		$dismissed = get_user_meta( get_current_user_id(), 'wpforms_dismissed', true );

		if ( ! empty( $dismissed['edu-single-payment'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render( 'education/admin/payments/single-page' );
	}

	/**
	 * Advanced details output.
	 *
	 * @since 1.8.2
	 */
	private function advanced_details() {

		/**
		 * Allow to modify a single payment page advanced details list.
		 *
		 * @since 1.8.2
		 *
		 * @param array  $list    Advanced details to show.
		 * @param object $payment Payment object.
		 */
		$details_list = apply_filters(
			'wpforms_admin_payments_views_single_advanced_details_list',
			[
				'transaction_id'  => [
					'label' => __( 'Transaction ID', 'wpforms-lite' ),
					'link'  => $this->get_gateway_transaction_link(),
					'value' => $this->payment->transaction_id,
				],
				'subscription_id' => [
					'label' => __( 'Subscription ID', 'wpforms-lite' ),
					'link'  => $this->get_gateway_subscription_link(),
					'value' => $this->payment->subscription_id,
				],
				'customer_id'     => [
					'label' => __( 'Customer ID', 'wpforms-lite' ),
					'link'  => $this->get_gateway_customer_link(),
					'value' => $this->payment->customer_id,
				],
				'customer_ip'     => [
					'label' => __( 'Customer IP Address', 'wpforms-lite' ),
					'value' => ! empty( $this->payment_meta['ip_address']->value ) ? $this->payment_meta['ip_address']->value : false,
				],
				'payment_method'  => [
					'label' => __( 'Payment Method', 'wpforms-lite' ),
					'value' => $this->get_payment_method_details(),
				],
				'coupon_info'     => [
					'label' => __( 'Coupon', 'wpforms-lite' ),
					'value' => $this->get_coupon_info(),
				],
			],
			$this->payment
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			'admin/payments/single/advanced-details',
			[
				'details_list' => $details_list,
			],
			true
		);
	}

	/**
	 * Entry details output.
	 *
	 * @since 1.8.2
	 */
	private function entry_details() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$entry_id_title = '';
		$fields         = '';

		// Grab submitted values from the entry if it exists.
		if ( ! empty( $this->payment->entry_id ) && wpforms()->is_pro() ) {
			$entry = wpforms()->get( 'entry' )->get( $this->payment->entry_id );

			if ( $entry ) {
				$fields          = wpforms_decode( $entry->fields );
				$entry_id_title .= "#{$this->payment->entry_id}";
			}
		}

		// Otherwise, grab submitted values from the payment meta if it exists.
		if ( empty( $fields ) && ! empty( $this->payment_meta['fields'] ) ) {
			$fields = wpforms_decode( $this->payment_meta['fields']->value );
		}

		// Bail early if there are submitted values.
		if ( empty( $fields ) ) {
			return;
		}

		$form_data = wpforms()->get( 'form' )->get( $this->payment->form_id, [ 'content_only' => true ] );

		add_filter( 'wp_kses_allowed_html', [ $this, 'modify_allowed_tags_payment_field_value' ], 10, 2 );

		$entry_output = wpforms_render(
			'admin/payments/single/entry-details',
			[
				'entry_fields'   => $this->prepare_entry_fields( $fields, $form_data ),
				'form_data'      => $form_data,
				'entry_id_title' => $entry_id_title,
				'entry_id'       => $this->payment->entry_id,
				'entry_url'      => add_query_arg(
					[
						'page'     => 'wpforms-entries',
						'view'     => 'details',
						'entry_id' => $this->payment->entry_id,
					],
					admin_url( 'admin.php' )
				),
			],
			true
		);

		remove_filter( 'wp_kses_allowed_html', [ $this, 'modify_allowed_tags_payment_field_value' ] );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $entry_output;
	}

	/**
	 * Prepare entry fields.
	 *
	 * @since 1.8.2
	 *
	 * @param array $fields    Entry fields.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 */
	private function prepare_entry_fields( $fields, $form_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.CyclomaticComplexity.TooHigh

		if ( empty( $fields ) ) {
			return [];
		}

		$prepared_fields = [];

		// Display the fields and their values.
		foreach ( $fields as $key => $field ) {

			if ( empty( $field['type'] ) ) {
				continue;
			}

			$field_type = $field['type'];

			// phpcs:disable WPForms.PHP.ValidateHooks.InvalidHookName
			/** This filter is documented in /src/Pro/Admin/Entries/Edit.php */
			if ( $this->payment->entry_id && ! (bool) apply_filters( "wpforms_pro_admin_entries_edit_is_field_displayable_{$field_type}", true, $field, $form_data ) ) {
				continue;
			}

			$field_value = isset( $field['value'] ) ? $field['value'] : '';
			/** This filter is documented in src/SmartTags/SmartTag/FieldHtmlId.php.*/
			$prepared_fields[ $key ]['field_value'] = apply_filters( 'wpforms_html_field_value', wp_strip_all_tags( $field_value ), $field, $form_data, 'payment-single' );
			// phpcs:enable WPForms.PHP.ValidateHooks.InvalidHookName

			$prepared_fields[ $key ]['field_class'] = sanitize_html_class( 'wpforms-field-' . $field_type );
			$prepared_fields[ $key ]['field_name']  = ! empty( $field['name'] )
				? $field['name']
				: sprintf( /* translators: %d - field ID. */
					esc_html__( 'Field ID #%d', 'wpforms-lite' ),
					absint( $field['id'] )
				);

			if ( wpforms_is_empty_string( $field_value ) ) {
				$prepared_fields[ $key ]['field_value']  = esc_html__( 'Empty', 'wpforms-lite' );
				$prepared_fields[ $key ]['field_class'] .= ' empty';
			}
		}

		return $prepared_fields;
	}

	/**
	 * Allow additional tags for the wp_kses_post function.
	 *
	 * @since 1.8.2
	 *
	 * @param array  $allowed_html List of allowed HTML.
	 * @param string $context      Context name.
	 *
	 * @return array
	 */
	public function modify_allowed_tags_payment_field_value( $allowed_html, $context ) {

		if ( $context !== 'post' ) {
			return $allowed_html;
		}

		$allowed_html['iframe'] = [
			'data-src' => [],
			'class'    => [],
		];

		return $allowed_html;
	}

	/**
	 * Details metabox output.
	 *
	 * @since 1.8.2
	 */
	private function details() {

		$date = sprintf( /* translators: %1$s - date, %2$s - time when item was created, e.g. "Oct 22 at 11:11am". */
			__( '%1$s at %2$s', 'wpforms-lite' ),
			wpforms_datetime_format( $this->payment->date_created_gmt, 'M j, Y', true ),
			wpforms_datetime_format( $this->payment->date_created_gmt, get_option( 'time_format' ), true )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			'admin/payments/single/details',
			[
				'payment'        => $this->payment,
				'submitted'      => $date,
				'gateway_name'   => $this->get_gateway_name(),
				'gateway_link'   => $this->get_gateway_dashboard_link(),
				'form_edit_link' => add_query_arg(
					[
						'page'    => 'wpforms-builder',
						'view'    => 'fields',
						'form_id' => $this->payment->form_id,
					],
					admin_url( 'admin.php' )
				),
				'test_mode'      => $this->payment->mode === 'test',
				'delete_link'    => wp_nonce_url(
					add_query_arg(
						[
							'page'       => 'wpforms-payments',
							'action'     => 'delete',
							'payment_id' => $this->payment->id,
						],
						admin_url( 'admin.php' )
					),
					'bulk-wpforms_page_wpforms-payments'
				),
			],
			true
		);
	}

	/**
	 * Logs metabox output.
	 *
	 * @since 1.8.2
	 */
	private function log() {

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render(
			'admin/payments/single/log',
			[
				'logs' => wpforms()->get( 'payment_meta' )->get_all_by( 'log', $this->payment->id ),
			],
			true
		);
	}

	// TODO: Remove hardcoded values in methods below after all payment addons updated to use new filters.
	/**
	 * Get gateway transaction link.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_gateway_transaction_link() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		/**
		 * Allow to modify a single payment page gateway transaction link.
		 *
		 * @since 1.8.2
		 *
		 * @param string $link    Gateway transaction link.
		 * @param object $payment Payment object.
		 */
		$link = apply_filters( 'wpforms_admin_payments_views_single_gateway_transaction_link', '', $this->payment );

		if ( $link ) {
			return $link;
		}

		switch ( $this->payment->gateway ) {
			case 'stripe':
				$link = 'payments/';
				break;

			case 'paypal_standard':
			case 'paypal_commerce':
				$link = 'activity/payment/';
				break;

			case 'square':
				$link = 'sales/transactions/';
				break;

			default:
				$link = '';
				break;
		}

		if ( ! $link ) {
			return $this->get_gateway_dashboard_link();
		}

		return $this->get_gateway_dashboard_link() . $link . $this->payment->transaction_id;
	}

	/**
	 * Get gateway subscription link.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_gateway_subscription_link() {

		/**
		 * Allow to modify a single payment page gateway subscription link.
		 *
		 * @since 1.8.2
		 *
		 * @param string $link    Gateway subscription link.
		 * @param object $payment Payment object.
		 */
		$link = apply_filters( 'wpforms_admin_payments_views_single_gateway_subscription_link', '', $this->payment );

		if ( $link ) {
			return $link;
		}

		switch ( $this->payment->gateway ) {
			case 'stripe':
				$link = 'subscriptions/';
				break;

			case 'paypal_commerce':
				$link = 'billing/subscriptions/';
				break;

			default:
				$link = '';
				break;
		}

		if ( ! $link ) {
			return $this->get_gateway_dashboard_link();
		}

		return $this->get_gateway_dashboard_link() . $link . $this->payment->subscription_id;
	}

	/**
	 * Get gateway customer link.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_gateway_customer_link() {

		/**
		 * Allow to modify a single payment page gateway customer link.
		 *
		 * @since 1.8.2
		 *
		 * @param string $link    Gateway customer link.
		 * @param object $payment Payment object.
		 */
		$link = apply_filters( 'wpforms_admin_payments_views_single_gateway_customer_link', '', $this->payment );

		if ( $link ) {
			return $link;
		}

		switch ( $this->payment->gateway ) {
			case 'stripe':
				$link = 'customers/';
				break;

			case 'square':
				$link = 'customers/directory/customer/';
				break;

			default:
				$link = '';
				break;
		}

		if ( ! $link ) {
			return $this->get_gateway_dashboard_link();
		}

		return $this->get_gateway_dashboard_link() . $link . $this->payment->customer_id;
	}

	/**
	 * Get gateway dashboard link.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_gateway_dashboard_link() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.CyclomaticComplexity.TooHigh

		/**
		 * Allow to modify a single payment page gateway dashboard link.
		 *
		 * @since 1.8.2
		 *
		 * @param string $link    Gateway dashboard link.
		 * @param object $payment Payment object.
		 */
		$link = apply_filters( 'wpforms_admin_payments_views_single_gateway_dashboard_link', '', $this->payment );

		if ( $link ) {
			return $link;
		}

		$is_test_mode = $this->payment->mode === 'test';

		// Backward compatibility until all addons has been updated.
		switch ( $this->payment->gateway ) {
			case 'stripe':
				$link = $is_test_mode ? 'https://dashboard.stripe.com/test/' : 'https://dashboard.stripe.com/';
				break;

			case 'paypal_standard':
			case 'paypal_commerce':
				$link = $is_test_mode ? 'https://www.sandbox.paypal.com/myaccount/summary/' : 'https://www.paypal.com/myaccount/summary/';
				break;

			case 'authorize_net':
				$link = $is_test_mode ? 'https://sandbox.authorize.net/' : 'https://account.authorize.net/';
				break;

			case 'square':
				$link = $is_test_mode ? 'https://squareupsandbox.com/dashboard/' : 'https://squareup.com/dashboard/';
				break;

			default:
				$link = '';
				break;
		}

		return $link;
	}

	/**
	 * Get gateway action link.
	 *
	 * @since 1.8.2
	 *
	 * @param string $action Action.
	 *
	 * @return string
	 */
	private function get_gateway_action_link( $action ) {

		/**
		 * Allow to modify a single payment page gateway action link.
		 *
		 * @since 1.8.2
		 *
		 * @param string $link    Gateway action link.
		 * @param string $action  Action to perform.
		 * @param object $payment Payment object.
		 */
		$link = apply_filters( 'wpforms_admin_payments_views_single_gateway_action_link', '', $action, $this->payment );

		if ( $link ) {
			return $link;
		}

		// Backward compatibility until all addons has been updated.
		if ( $action === 'refund' ) {
			return $this->get_gateway_transaction_link();
		}

		return $this->get_gateway_subscription_link();
	}

	/**
	 * Retrieve a readable payment gateway name.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_gateway_name() {

		$gateway_name = Helpers::get_placeholder_na_text( false );

		if ( isset( $this->payment->gateway ) && ValueValidator::is_valid( $this->payment->gateway, 'gateway' ) ) {
			$gateway_name = ValueValidator::get_allowed_gateways()[ $this->payment->gateway ];
		}

		return $gateway_name;
	}
}
