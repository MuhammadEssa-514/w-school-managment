<?php
// phpcs:ignoreFile WPForms.PHP.BackSlash.RemoveBackslash
namespace WPForms\Integrations\Stripe\Api;

use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Subscription;
use WPForms\Integrations\Stripe\Helpers;

/**
 * Common methods for every Stripe API implementation.
 *
 * @since 1.8.2
 */
abstract class Common {

	/**
	 * API configuration.
	 *
	 * @since 1.8.2
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Stripe customer object.
	 *
	 * @since 1.8.2
	 *
	 * @var Customer
	 */
	protected $customer;

	/**
	 * Stripe subscription object.
	 *
	 * @since 1.8.2
	 *
	 * @var Subscription
	 */
	protected $subscription;

	/**
	 * API error message.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	protected $error;

	/**
	 * API exception.
	 *
	 * @since 1.8.2
	 *
	 * @var \Exception
	 */
	protected $exception;

	/**
	 * Get class variable value or its key.
	 *
	 * @since 1.8.2
	 *
	 * @param string $field Name of the variable to retrieve.
	 * @param string $key   Name of the key to retrieve.
	 *
	 * @return mixed
	 */
	protected function get_var( $field, $key = '' ) {

		$var = isset( $this->{$field} ) ? $this->{$field} : null;

		if ( ! $key ) {
			return $var;
		}

		if ( is_object( $var ) ) {
			return isset( $var->{$key} ) ? $var->{$key} : null;
		}

		if ( is_array( $var ) ) {
			return isset( $var[ $key ] ) ? $var[ $key ] : null;
		}

		return $var;
	}

	/**
	 * Get API configuration array or its key.
	 *
	 * @since 1.8.2
	 *
	 * @param string $key Name of the key to retrieve.
	 *
	 * @return mixed
	 */
	public function get_config( $key = '' ) {

		return $this->get_var( 'config', $key );
	}

	/**
	 * Get saved Stripe customer object or its key.
	 *
	 * @since 1.8.2
	 *
	 * @param string $key Name of the key to retrieve.
	 *
	 * @return mixed
	 */
	public function get_customer( $key = '' ) {

		return $this->get_var( 'customer', $key );
	}

	/**
	 * Get saved Stripe subscription object or its key.
	 *
	 * @since 1.8.2
	 *
	 * @param string $key Name of the key to retrieve.
	 *
	 * @return mixed
	 */
	public function get_subscription( $key = '' ) {

		return $this->get_var( 'subscription', $key );
	}

	/**
	 * Get API error message.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	public function get_error() {

		return $this->get_var( 'error' );
	}

	/**
	 * Get API exception.
	 *
	 * @since 1.8.2
	 *
	 * @return \Exception
	 */
	public function get_exception() {

		return $this->get_var( 'exception' );
	}

	/**
	 * Initial Stripe app configuration.
	 *
	 * @since 1.8.2
	 */
	public function setup_stripe() {

		Stripe::setAppInfo(
			'WPForms acct_17Xt6qIdtRxnENqV',
			WPFORMS_VERSION,
			'https://wpforms.com/addons/stripe-addon/',
			'pp_partner_Dw7IkUZbIlCrtq'
		);
	}

	/**
	 * Set a customer object.
	 * Check if a customer exists in Stripe, if not creates one.
	 *
	 * @since 1.8.2
	 *
	 * @param string $email Email to fetch an existing customer.
	 */
	protected function set_customer( $email ) {

		try {
			$customers = Customer::all(
				[ 'email' => $email ],
				Helpers::get_auth_opts()
			);
		} catch ( \Exception $e ) {
			$customers = null;
		}

		if ( isset( $customers->data[0]->id ) ) {
			$this->customer = $customers->data[0];

			return;
		}

		try {
			$customer = Customer::create(
				[ 'email' => $email ],
				Helpers::get_auth_opts()
			);
		} catch ( \Exception $e ) {
			$customer = null;
		}

		if ( ! isset( $customer->id ) ) {
			return;
		}

		$this->customer = $customer;
	}

	/**
	 * Set an error message from a Stripe API exception.
	 *
	 * @since 1.8.2
	 *
	 * @param \Exception|\Stripe\Exception\ApiErrorException $e Stripe API exception to process.
	 */
	protected function set_error_from_exception( $e ) {

		/**
		 * WPForms set Stripe error from exception.
		 *
		 * @since 1.8.2
		 *
		 * @param \Exception|\Stripe\Exception\ApiErrorException $e Stripe API exception to process.
		 */
		do_action( 'wpformsstripe_api_common_set_error_from_exception', $e ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

		if ( is_a( $e, '\Stripe\Exception\CardException' ) ) {
			$body        = $e->getJsonBody();
			$this->error = $body['error']['message'];

			return;
		}

		$errors = [
			'\Stripe\Exception\RateLimitException'      => esc_html__( 'Too many requests made to the API too quickly.', 'wpforms-lite' ),
			'\Stripe\Exception\InvalidRequestException' => esc_html__( 'Invalid parameters were supplied to Stripe API.', 'wpforms-lite' ),
			'\Stripe\Exception\AuthenticationException' => esc_html__( 'Authentication with Stripe API failed.', 'wpforms-lite' ),
			'\Stripe\Exception\ApiConnectionException'  => esc_html__( 'Network communication with Stripe failed.', 'wpforms-lite' ),
			'\Stripe\Exception\ApiErrorException'       => esc_html__( 'Unable to process Stripe payment.', 'wpforms-lite' ),
			'\Exception'                                => esc_html__( 'Unable to process payment.', 'wpforms-lite' ),
		];

		foreach ( $errors as $error_type => $error_message ) {

			if ( is_a( $e, $error_type ) ) {
				$this->error = $error_message;

				return;
			}
		}
	}

	/**
	 * Set an exception from a Stripe API exception.
	 *
	 * @since 1.8.2
	 *
	 * @param \Exception $e Stripe API exception to process.
	 */
	protected function set_exception( $e ) {

		$this->exception = $e;
	}

	/**
	 * Handle Stripe API exception.
	 *
	 * @since 1.8.2
	 *
	 * @param \Exception $e Stripe API exception to process.
	 */
	protected function handle_exception( $e ) {

		$this->set_exception( $e );
		$this->set_error_from_exception( $e );
	}

	/**
	 * Get data for every subscription period.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	protected function get_subscription_period_data() {

		return [
			'daily'      => [
				'name'     => 'daily',
				'interval' => 'day',
				'count'    => 1,
				'desc'     => esc_html__( 'Daily', 'wpforms-lite' ),
			],
			'weekly'     => [
				'name'     => 'weekly',
				'interval' => 'week',
				'count'    => 1,
				'desc'     => esc_html__( 'Weekly', 'wpforms-lite' ),
			],
			'monthly'    => [
				'name'     => 'monthly',
				'interval' => 'month',
				'count'    => 1,
				'desc'     => esc_html__( 'Monthly', 'wpforms-lite' ),
			],
			'quarterly'  => [
				'name'     => 'quarterly',
				'interval' => 'month',
				'count'    => 3,
				'desc'     => esc_html__( 'Quarterly', 'wpforms-lite' ),
			],
			'semiyearly' => [
				'name'     => 'semiyearly',
				'interval' => 'month',
				'count'    => 6,
				'desc'     => esc_html__( 'Semi-Yearly', 'wpforms-lite' ),
			],
			'yearly'     => [
				'name'     => 'yearly',
				'interval' => 'year',
				'count'    => 1,
				'desc'     => esc_html__( 'Yearly', 'wpforms-lite' ),
			],
		];
	}

	/**
	 * Create Stripe plan.
	 *
	 * @since 1.8.2
	 *
	 * @param string $id     ID of a plan to create.
	 * @param array  $period Subscription period data.
	 * @param array  $args   Additional arguments.
	 *
	 * @return Plan|null
	 */
	protected function create_plan( $id, $period, $args ) {

		$name = sprintf(
			'%s (%s %s)',
			! empty( $args['settings']['name'] ) ? $args['settings']['name'] : $args['form_title'],
			$args['amount'],
			$period['desc']
		);

		$plan_args = [
			'amount'         => $args['amount'],
			'interval'       => $period['interval'],
			'interval_count' => $period['count'],
			'product'        => [
				'name' => sanitize_text_field( $name ),
			],
			'nickname'       => sanitize_text_field( $name ),
			'currency'       => strtolower( wpforms_get_currency() ),
			'id'             => $id,
			'metadata'       => [
				'form_name' => sanitize_text_field( $args['form_title'] ),
				'form_id'   => $args['form_id'],
			],
		];

		try {
			$plan = Plan::create( $plan_args, Helpers::get_auth_opts() );
		} catch ( \Exception $e ) {
			$plan = null;
		}

		return $plan;
	}

	/**
	 * Get Stripe plan ID.
	 * Check if a plan exists in Stripe, if not creates one.
	 *
	 * @since 1.8.2
	 *
	 * @param array $args Arguments needed for getting a valid plan ID.
	 *
	 * @return string
	 */
	protected function get_plan_id( $args ) {

		$period_data = $this->get_subscription_period_data();

		$period = array_key_exists( $args['settings']['period'], $period_data ) ? $period_data[ $args['settings']['period'] ] : $period_data['yearly'];

		if ( ! empty( $args['settings']['name'] ) ) {
			$slug = preg_replace( '/[^a-z0-9\-]/', '', strtolower( str_replace( ' ', '-', $args['settings']['name'] ) ) );
		} else {
			$slug = 'form' . $args['form_id'];
		}

		$plan_id = sprintf(
			'%s_%s_%s',
			$slug,
			$args['amount'],
			$period['name']
		);

		try {
			$plan = Plan::retrieve( $plan_id, Helpers::get_auth_opts() );
		} catch ( \Exception $e ) {
			$plan = $this->create_plan( $plan_id, $period, $args );
		}

		return isset( $plan->id ) ? $plan->id : '';
	}
}
