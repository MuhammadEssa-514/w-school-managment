<?php

namespace WPForms\Integrations\Stripe;

use Stripe\Exception\ApiErrorException;

/**
 * Stripe payment processing.
 *
 * @since 1.8.2
 */
class Process {

	/**
	 * Payment amount.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	public $amount = '';

	/**
	 * Form ID.
	 *
	 * @since 1.8.2
	 *
	 * @var int
	 */
	public $form_id = 0;

	/**
	 * Form Stripe payment settings.
	 *
	 * @since 1.8.2
	 *
	 * @var array
	 */
	public $settings = [];

	/**
	 * Sanitized submitted field values and data.
	 *
	 * @since 1.8.2
	 *
	 * @var array
	 */
	public $fields = [];

	/**
	 * Form data and settings.
	 *
	 * @since 1.8.2
	 *
	 * @var array
	 */
	public $form_data = [];

	/**
	 * Rate Limit object.
	 *
	 * @since 1.8.2
	 *
	 * @var RateLimit
	 */
	private $rate_limit;

	/**
	 * Api interface.
	 *
	 * @since 1.8.2
	 *
	 * @var Api\ApiInterface
	 */
	private $api;

	/**
	 * Whether the payment has been processed.
	 *
	 * @since 1.8.3
	 *
	 * @var bool
	 */
	private $is_payment_processed = false;

	/**
	 * Initialize.
	 *
	 * @since 1.8.2
	 *
	 * @param Api\ApiInterface $api Api interface.
	 */
	public function init( $api ) {

		$this->api = $api;

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.8.2
	 */
	private function hooks() {

		add_action( 'wpforms_process', [ $this, 'process_entry' ], 10, 4 );
		add_action( 'wpforms_process_payment_saved', [ $this, 'process_payment_saved' ], 10, 3 );
		add_action( 'wpformsstripe_api_common_set_error_from_exception', [ $this, 'process_card_error' ] );
		add_filter( 'wpforms_forms_submission_prepare_payment_data', [ $this, 'prepare_payment_data' ] );
		add_filter( 'wpforms_forms_submission_prepare_payment_meta', [ $this, 'prepare_payment_meta' ], 10, 3 );
		add_filter( 'wpforms_entry_email_process', [ $this, 'process_email' ], 70, 4 );
		add_action( 'wpforms_process_complete', [ $this, 'process_entry_data' ], 10, 4 );
		add_filter( 'wpforms_process_bypass_captcha', [ $this, 'bypass_captcha' ] );
	}

	/**
	 * Check if a payment exists with an entry, if so validate and process.
	 *
	 * @since 1.8.2
	 *
	 * @param array $fields    Final/sanitized submitted field data.
	 * @param array $entry     Copy of original $_POST.
	 * @param array $form_data Form data and settings.
	 */
	public function process_entry( $fields, $entry, $form_data ) {

		// Check if payment method exists and is enabled.
		if ( ! Helpers::has_stripe_enabled( [ $form_data ] ) ) {
			return;
		}

		$this->form_id    = (int) $form_data['id'];
		$this->fields     = $fields;
		$this->form_data  = $form_data;
		$this->settings   = $form_data['payments']['stripe'];
		$this->amount     = wpforms_get_total_payment( $this->fields );
		$this->rate_limit = new RateLimit();

		$this->rate_limit->init();

		if ( $this->is_process_entry_error() ) {
			return;
		}

		$this->api->set_payment_tokens( $entry );

		$error = $this->get_entry_errors();

		// Before proceeding, check if any basic errors were detected.
		if ( $error ) {
			$this->log_error( $error );
			$this->display_error( $error );

			return;
		}

		// Set payment processing flag.
		$this->is_payment_processed = true;

		$this->process_payment();
	}

	/**
	 * Bypass captcha if payment has been processed.
	 *
	 * @since 1.8.3
	 *
	 * @param bool $bypass_captcha Whether to bypass captcha.
	 *
	 * @return bool
	 */
	public function bypass_captcha( $bypass_captcha ) {

		if ( $bypass_captcha ) {
			return $bypass_captcha;
		}

		return $this->is_payment_processed;
	}

	/**
	 * Check on process entry errors.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	protected function is_process_entry_error() {

		// Check for processing errors.
		if ( ! empty( wpforms()->get( 'process' )->errors[ $this->form_id ] ) || ! $this->is_card_field_visibility_ok() ) {
			return true;
		}

		// Check rate limit.
		if ( ! $this->is_rate_limit_ok() ) {
			wpforms()->get( 'process' )->errors[ $this->form_id ]['footer'] = esc_html__( 'Unable to process payment, please try again later.', 'wpforms-lite' );

			return true;
		}

		return false;
	}

	/**
	 * Add meta for a successful payment.
	 *
	 * @since 1.8.2
	 *
	 * @param array $payment_meta Payment meta.
	 * @param array $fields       Final/sanitized submitted field data.
	 * @param array $form_data    Form data and settings.
	 */
	public function prepare_payment_meta( $payment_meta, $fields, $form_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$payment = $this->api->get_payment();

		if ( empty( $payment->id ) ) {
			return $payment_meta;
		}

		$charge_details = $this->api->get_charge_details( [ 'type', 'name', 'last4', 'brand', 'exp_month', 'exp_year' ] );

		$payment_meta['method_type']    = $this->get_payment_type( $charge_details );
		$payment_meta['customer_name']  = $this->get_customer_name();
		$payment_meta['customer_email'] = $this->get_customer_email();

		$subscription = $this->api->get_subscription();

		if ( ! empty( $subscription->id ) ) {
			$payment_meta['subscription_period'] = sanitize_text_field( $this->settings['recurring']['period'] );
		}

		if ( ! empty( $charge_details['brand'] ) ) {
			$payment_meta['credit_card_method'] = $charge_details['brand'];
		}

		if ( ! empty( $charge_details['name'] ) ) {
			$payment_meta['credit_card_name'] = $charge_details['name'];
		}

		if ( ! empty( $charge_details['last4'] ) ) {
			$payment_meta['credit_card_last4'] = $charge_details['last4'];
		}

		if ( ! empty( $charge_details['exp_month'] ) && ! empty( $charge_details['exp_year'] ) ) {
			$payment_meta['credit_card_expires'] = sprintf( '%s/%s', $charge_details['exp_month'], $charge_details['exp_year'] );
		}

		$log = [
			'value' => $payment->object === 'payment_intent' ? sprintf( 'Stripe payment intent created. (Payment Intent ID: %s)', $payment->id ) : 'Stripe payment was created.',
			'date'  => gmdate( 'Y-m-d H:i:s' ),
		];

		$payment_meta['log'] = wp_json_encode( $log );

		return $payment_meta;
	}

	/**
	 * Get payment method type.
	 *
	 * @since 1.8.2.1
	 *
	 * @param array $charge_details Get details from a saved Charge object.
	 *
	 * @return string
	 */
	private function get_payment_type( $charge_details ) {

		if ( empty( $charge_details['last4'] ) ) {
			return 'link';
		}

		if ( ! empty( $charge_details['type'] ) ) {
			return sanitize_text_field( $charge_details['type'] );
		}

		return 'card';
	}

	/**
	 * Add payment info for successful payment.
	 *
	 * @since 1.8.2
	 *
	 * @param int   $payment_id Payment ID.
	 * @param array $fields     Final/sanitized submitted field data.
	 * @param array $form_data  Form data and settings.
	 */
	public function process_payment_saved( $payment_id, $fields, $form_data ) {

		$payment = $this->api->get_payment();

		if ( empty( $payment->id ) ) {
			return;
		}

		$payment_url = add_query_arg(
			[
				'page'       => 'wpforms-payments',
				'view'       => 'single',
				'payment_id' => $payment_id,
			],
			admin_url( 'admin.php' )
		);

		// Update the Stripe charge metadata to include the Payment ID.
		$payment->metadata['payment_id']  = $payment_id;
		$payment->metadata['payment_url'] = esc_url_raw( $payment_url );

		/**
		 * Allow to add additional payment metadata to the Stripe payment.
		 *
		 * @since 1.8.2.2
		 *
		 * @param array $additional_meta Additional metadata.
		 * @param int   $payment_id      Payment ID.
		 * @param array $fields          Final/sanitized submitted field data.
		 * @param array $form_data       Form data and settings.
		 */
		$additional_meta = (array) apply_filters( 'wpforms_integrations_stripe_process_additional_metadata', [], $payment_id, $fields, $form_data );

		array_walk(
			$additional_meta,
			static function( $meta, $key ) use ( &$payment ) {
				$payment->metadata[ $key ] = $meta;
			}
		);

		$payment->save();

		$subscription = $this->api->get_subscription();

		// Update the Stripe subscription metadata to include the Payment ID.
		if ( ! empty( $subscription->id ) ) {
			$subscription->metadata['payment_id']  = $payment_id;
			$subscription->metadata['payment_url'] = esc_url_raw( $payment_url );

			$subscription->save();
		}

		wpforms()->get( 'payment_meta' )->add(
			[
				'payment_id' => $payment_id,
				'meta_key'   => 'log', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => wp_json_encode( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					[
						'value' => sprintf(
							'Stripe charge complete. (Charge ID: %s)',
							isset( $payment->latest_charge ) ? $payment->latest_charge : $payment->id
						),
						'date'  => gmdate( 'Y-m-d H:i:s' ),
					]
				),
			]
		);

		/**
		 * Fire when processing is complete.
		 *
		 * @since 1.8.2
		 *
		 * @param array $fields       Final/sanitized submitted field data.
		 * @param array $form_data    Form data and settings.
		 * @param int   $payment_id   Payment ID.
		 * @param mixed $payment      Stripe payment object.
		 * @param mixed $subscription Stripe subscription object.
		 * @param mixed $customer     Stripe customer object.
		 */
		do_action( 'wpforms_stripe_process_complete', $fields, $form_data, $payment_id, $payment, $subscription, $this->api->get_customer() ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
	}

	/**
	 * Add details to payment data.
	 *
	 * @since 1.8.2
	 *
	 * @param array $payment_data Payment data args.
	 *
	 * @return array
	 */
	public function prepare_payment_data( $payment_data ) {

		$payment = $this->api->get_payment();

		if ( empty( $payment->id ) ) {
			return $payment_data;
		}

		$customer     = $this->api->get_customer();
		$subscription = $this->api->get_subscription();

		$payment_data['status']         = 'processed';
		$payment_data['gateway']        = 'stripe';
		$payment_data['mode']           = Helpers::get_stripe_mode();
		$payment_data['transaction_id'] = sanitize_text_field( $payment->id );
		$payment_data['customer_id']    = ! empty( $customer->id ) ? sanitize_text_field( $customer->id ) : '';
		$payment_data['title']          = $this->get_payment_title();

		if ( ! empty( $subscription->id ) ) {
			$payment_data['subscription_id']     = sanitize_text_field( $subscription->id );
			$payment_data['subscription_status'] = 'not-synced';
		}

		return $payment_data;
	}

	/**
	 * Get Payment title.
	 *
	 * @since 1.8.2
	 *
	 * @return string Payment title.
	 */
	private function get_payment_title() {

		$customer_name = $this->get_customer_name();

		if ( $customer_name ) {
			return $customer_name;
		}

		$customer_email = $this->get_customer_email();

		if ( $customer_email ) {
			return $customer_email;
		}

		return '';
	}

	/**
	 * Get Customer name.
	 *
	 * @since 1.8.2
	 *
	 * @return string Customer name.
	 */
	private function get_customer_name() {

		$customer_name = $this->api->get_customer( 'name' );

		if ( $customer_name ) {
			return $customer_name;
		}

		$charge_details = $this->api->get_charge_details( [ 'name' ] );

		if ( ! empty( $charge_details['name'] ) ) {
			return $charge_details['name'];
		}

		return '';
	}

	/**
	 * Get Customer email.
	 *
	 * @since 1.8.2
	 *
	 * @return string Customer email.
	 */
	private function get_customer_email() {

		$customer_email = $this->api->get_customer( 'email' );

		if ( $customer_email ) {
			return $customer_email;
		}

		$charge_details = $this->api->get_charge_details( [ 'email' ] );

		if ( ! empty( $charge_details['email'] ) ) {
			return $charge_details['email'];
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['wpforms']['payment_link_email'] ) ) {
			return sanitize_email( wp_unslash( $_POST['wpforms']['payment_link_email'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return '';
	}

	/**
	 * Logic that helps decide if we should send completed payments notifications.
	 *
	 * @since 1.8.2
	 *
	 * @param bool  $process         Whether to process or not.
	 * @param array $fields          Form fields.
	 * @param array $form_data       Form data.
	 * @param int   $notification_id Notification ID.
	 *
	 * @return bool
	 */
	public function process_email( $process, $fields, $form_data, $notification_id ) {

		if ( ! $process ) {
			return false;
		}

		if ( ! Helpers::has_stripe_enabled( [ $form_data ] ) ) {
			return $process;
		}

		if ( empty( $form_data['settings']['notifications'][ $notification_id ]['stripe'] ) ) {
			return $process;
		}

		if ( empty( $this->api->get_payment() ) ) {
			return false;
		}

		return empty( $this->api->get_error() );
	}

	/**
	 * Update entry details for a successful payment.
	 *
	 * @since 1.8.2
	 *
	 * @param array  $fields    Final/sanitized submitted field data.
	 * @param array  $entry     Copy of original $_POST.
	 * @param array  $form_data Form data and settings.
	 * @param string $entry_id  Entry ID.
	 */
	public function process_entry_data( $fields, $entry, $form_data, $entry_id ) {

		$payment = $this->api->get_payment();

		if ( empty( $payment->id ) || empty( $entry_id ) ) {
			return;
		}

		wpforms()->get( 'entry' )->update(
			$entry_id,
			[
				'type' => 'payment',
			],
			'',
			'',
			[ 'cap' => false ]
		);
	}

	/**
	 * Get general errors before payment processing.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	protected function get_entry_errors() {

		// Check for Stripe payment tokens (card token or payment id).
		$error = $this->api->get_error();

		// Check for Stripe keys.
		if ( ! Helpers::has_stripe_keys() ) {
			return esc_html__( 'Stripe payment stopped, missing keys.', 'wpforms-lite' );
		}

		// Check that, despite how the form is configured, the form and
		// entry actually contain payment fields, otherwise no need to proceed.
		if ( ! wpforms_has_payment( 'form', $this->form_data ) || ! wpforms_has_payment( 'entry', $this->fields ) ) {
			return esc_html__( 'Stripe payment stopped, missing payment fields.', 'wpforms-lite' );
		}

		// Check total charge amount.
		if ( empty( $this->amount ) || wpforms_sanitize_amount( 0 ) === $this->amount ) {
			return esc_html__( 'Stripe payment stopped, invalid/empty amount.', 'wpforms-lite' );
		}

		if ( 50 > ( $this->amount * 100 ) ) {
			return esc_html__( 'Stripe payment stopped, amount less than minimum charge required.', 'wpforms-lite' );
		}

		return $error;
	}

	/**
	 * Process a payment.
	 *
	 * @since 1.8.2
	 */
	public function process_payment() {

		$this->api->setup_stripe();

		$error = $this->api->get_error();

		if ( $error ) {
			$this->process_api_error( 'general' );

			return;
		}

		// Proceed to executing the purchase.
		if ( empty( $this->settings['recurring']['enable'] ) ) {
			$this->process_payment_single();

			return;
		}

		$this->process_payment_subscription();
	}

	/**
	 * Process a single payment.
	 *
	 * @since 1.8.2
	 */
	public function process_payment_single() {

		$amount_decimals = $this->get_decimals_amount();

		// Define the basic payment details.
		$args = [
			'amount'   => $this->amount * $amount_decimals,
			'currency' => strtolower( wpforms_get_currency() ),
			'metadata' => [
				'form_name' => sanitize_text_field( $this->form_data['settings']['form_title'] ),
				'form_id'   => $this->form_id,
			],
		];

		if ( ! Helpers::is_license_ok() && Helpers::is_application_fee_supported() ) {
			$args['application_fee_amount'] = (int) ( round( $this->amount * 0.03, 2 ) * $amount_decimals );
		}

		// Payment description.
		if ( ! empty( $this->settings['payment_description'] ) ) {
			$args['description'] = html_entity_decode( $this->settings['payment_description'], ENT_COMPAT, 'UTF-8' );
		}

		// Receipt email.
		if ( ! empty( $this->settings['receipt_email'] ) && ! empty( $this->fields[ $this->settings['receipt_email'] ]['value'] ) ) {
			$args['receipt_email'] = sanitize_email( $this->fields[ $this->settings['receipt_email'] ]['value'] );
		}

		// Customer email.
		if ( ! empty( $this->settings['customer_email'] ) && ! empty( $this->fields[ $this->settings['customer_email'] ]['value'] ) ) {
			$args['customer_email'] = sanitize_email( $this->fields[ $this->settings['customer_email'] ]['value'] );
		}

		$this->api->process_single( $args );

		$this->update_credit_card_field_value();

		$this->process_api_error( 'single' );
	}

	/**
	 * Process a subscription payment.
	 *
	 * @since 1.8.2
	 */
	public function process_payment_subscription() {

		$error = '';

		// Check subscription settings are provided.
		if ( empty( $this->settings['recurring']['period'] ) || empty( $this->settings['recurring']['email'] ) ) {
			$error = esc_html__( 'Stripe subscription payment stopped, missing form settings.', 'wpforms-lite' );
		}

		// Check for required customer email.
		if ( empty( $this->fields[ $this->settings['recurring']['email'] ]['value'] ) ) {
			$error = esc_html__( 'Stripe subscription payment stopped, customer email not found.', 'wpforms-lite' );
		}

		// Before proceeding, check if any basic errors were detected.
		if ( $error ) {
			$this->log_error( $error );
			$this->display_error( $error );

			return;
		}

		$amount_decimals = $this->get_decimals_amount();

		$args = [
			'form_id'    => $this->form_id,
			'form_title' => sanitize_text_field( $this->form_data['settings']['form_title'] ),
			'amount'     => $this->amount * $amount_decimals,
			'email'      => sanitize_email( $this->fields[ $this->settings['recurring']['email'] ]['value'] ),
			'settings'   => $this->settings['recurring'],
		];

		if ( ! Helpers::is_license_ok() && Helpers::is_application_fee_supported() ) {
			$args['application_fee_percent'] = 3;
		}

		$this->api->process_subscription( $args );

		// Update the credit card field value to contain basic details.
		$this->update_credit_card_field_value();

		$this->process_api_error( 'subscription' );
	}

	/**
	 * Update the credit card field value to contain basic details.
	 *
	 * @since 1.8.2
	 */
	public function update_credit_card_field_value() {

		foreach ( $this->fields as $field_id => $field ) {

			if ( empty( $field['type'] ) || $this->api->get_config( 'field_slug' ) !== $field['type'] ) {
				continue;
			}

			$details = $this->api->get_charge_details( [ 'name', 'last4', 'brand' ] );

			if ( ! empty( $details['last4'] ) ) {
				$details['last4'] = 'xxxx xxxx xxxx ' . $details['last4'];
			}

			if ( ! empty( $details['brand'] ) ) {
				$details['brand'] = ucfirst( $details['brand'] );
			}

			$details = is_array( $details ) && ! empty( $details ) ? implode( "\n", array_filter( $details ) ) : '-';

			/**
			 * This filter allows to overwrite a Style Credit Card value in saved entry.
			 *
			 * @since 1.8.2
			 *
			 * @param array  $details Card details.
			 * @param object $payment Stripe payment objects.
			 */
			wpforms()->get( 'process' )->fields[ $field_id ]['value'] = apply_filters( 'wpforms_stripe_creditcard_value', $details, $this->api->get_payment() ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
		}
	}

	/**
	 * Check if there is at least one visible (not hidden by conditional logic) card field in the form.
	 *
	 * @since 1.8.2
	 */
	protected function is_card_field_visibility_ok() {

		// If the form contains no fields with conditional logic the card field is visible by default.
		if ( empty( $this->form_data['conditional_fields'] ) ) {
			return true;
		}

		foreach ( $this->fields as $field ) {

			if ( $this->api->get_config( 'field_slug' ) !== $field['type'] ) {
				continue;
			}

			// if the field is NOT in array of conditional fields, it's visible.
			if ( ! in_array( $field['id'], $this->form_data['conditional_fields'], true ) ) {
				return true;
			}

			// if the field IS in array of conditional fields and marked as visible, it's visible.
			if ( ! empty( $field['visible'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Log payment error.
	 *
	 * @since 1.8.2
	 *
	 * @param string $title   Error title.
	 * @param string $message Error message.
	 * @param string $level   Error level to add to 'payment' error level.
	 */
	protected function log_error( $title, $message = '', $level = 'error' ) {

		if ( $message instanceof ApiErrorException ) {
			$body    = $message->getJsonBody();
			$message = isset( $body['error']['message'] ) ? $body['error'] : $message->getMessage();
		}

		wpforms_log(
			$title,
			$message,
			[
				'type'    => [ 'payment', $level ],
				'form_id' => $this->form_id,
			]
		);
	}

	/**
	 * Collect errors from API and turn it into form errors.
	 *
	 * @since 1.8.2
	 *
	 * @param string $type Payment time (e.g. 'single' or 'subscription').
	 */
	protected function process_api_error( $type ) {

		$message = $this->api->get_error();

		if ( empty( $message ) ) {
			return;
		}

		$message = sprintf(
			/* translators: %s - error message. */
			esc_html__( 'Payment Error: %s', 'wpforms-lite' ),
			$message
		);

		$this->display_error( $message );

		if ( $type === 'subscription' ) {
			$title = esc_html__( 'Stripe subscription payment stopped by error', 'wpforms-lite' );
		} else {
			$title = esc_html__( 'Stripe payment stopped by error', 'wpforms-lite' );
		}

		$this->log_error( $title, $this->api->get_exception() );
	}

	/**
	 * Display form error.
	 *
	 * @since 1.8.2
	 *
	 * @param string $error Error to display.
	 */
	private function display_error( $error ) {

		if ( ! $error ) {
			return;
		}

		$field_slug = $this->api->get_config( 'field_slug' );

		// Check if the form contains a required credit card. If it does
		// and there was an error, return the error to the user and prevent
		// the form from being submitted. This should not occur under normal
		// circumstances.
		foreach ( $this->form_data['fields'] as $field ) {

			if ( empty( $field['type'] ) || $field_slug !== $field['type'] ) {
				continue;
			}

			if ( ! empty( $field['required'] ) ) {
				wpforms()->get( 'process' )->errors[ $this->form_id ]['footer'] = $error;

				return;
			}
		}
	}

	/**
	 * Process card error from Stripe API exception and adds rate limit tracking.
	 *
	 * @since 1.8.2
	 *
	 * @param ApiErrorException $e Stripe API exception to process.
	 */
	public function process_card_error( $e ) {

		if ( Helpers::get_stripe_mode() === 'test' ) {
			return;
		}

		if ( ! is_a( $e, '\Stripe\Exception\CardException' ) ) {
			return;
		}

		/**
		 * Allow to filter Stripe process card error.
		 *
		 * @since 1.8.2
		 *
		 * @param bool $flag True if any error.
		 */
		if ( ! apply_filters( 'wpforms_stripe_process_process_card_error', true ) ) { // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
			return;
		}

		$this->rate_limit->increment_attempts();
	}

	/**
	 * Check if rate limit is under threshold and passes.
	 *
	 * @since 1.8.2
	 */
	protected function is_rate_limit_ok() {

		return $this->rate_limit->is_ok();
	}

	/**
	 * Get decimals amount.
	 *
	 * @since 1.8.2
	 *
	 * @return int
	 */
	private function get_decimals_amount() {

		return (int) str_pad( 1, wpforms_get_currency_decimals( strtolower( wpforms_get_currency() ) ) + 1, 0, STR_PAD_RIGHT );
	}
}
