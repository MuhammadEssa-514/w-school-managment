<?php

namespace WPForms\Admin\Payments\Views\Overview;

use DateTimeImmutable;
// phpcs:ignore WPForms.PHP.UseStatement.UnusedUseStatement
use wpdb;
use WPForms\Db\Payments\ValueValidator;
use WPForms\Admin\Helpers\Chart as ChartHelper;
use WPForms\Admin\Helpers\Datepicker;

/**
 * "Payments" overview page inside the admin, which lists all payments.
 * This page will be accessible via "WPForms" → "Payments".
 *
 * When requested data is sent via Ajax, this class is responsible for exchanging datasets.
 *
 * @since 1.8.2
 */
class Ajax {

	/**
	 * Database table name.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Hooks.
	 *
	 * @since 1.8.2
	 */
	public function hooks() {

		add_action( 'wp_ajax_wpforms_payments_overview_refresh_chart_dataset_data', [ $this, 'get_chart_dataset_data' ] );
		add_action( 'wp_ajax_wpforms_payments_overview_save_chart_preference_settings', [ $this, 'save_chart_preference_settings' ] );
		add_filter( 'wpforms_db_payments_payment_add_secondary_where_conditions_args', [ $this, 'modify_secondary_where_conditions_args' ] );
	}

	/**
	 * Generate and return the data for our dataset data.
	 *
	 * @since 1.8.2
	 */
	public function get_chart_dataset_data() {

		// Verify the nonce.
		check_ajax_referer( 'wpforms_payments_overview_nonce' );

		$report   = ! empty( $_POST['report'] ) ? sanitize_text_field( wp_unslash( $_POST['report'] ) ) : null;
		$dates    = ! empty( $_POST['dates'] ) ? sanitize_text_field( wp_unslash( $_POST['dates'] ) ) : null;
		$fallback = [
			'data'    => [],
			'reports' => [],
		];

		// If the report type or dates for the timespan are missing, leave early.
		if ( ! $report || ! $dates ) {
			wp_send_json_error( $fallback );
		}

		// Validates and creates date objects of given timespan string.
		$timespans = Datepicker::process_string_timespan( $dates );

		// If the timespan is not validated, leave early.
		if ( ! $timespans ) {
			wp_send_json_error( $fallback );
		}

		// Extract start and end timespans in local (site) and UTC timezones.
		list( $start_date, $end_date, $utc_start_date, $utc_end_date ) = $timespans;

		// Payment table name.
		$this->table_name = wpforms()->get( 'payment' )->table_name;

		// Get the payments in the given timespan.
		$results = $this->get_payments_in_timespan( $utc_start_date, $utc_end_date, $report );

		// In case the database's results were empty, leave early.
		if ( empty( $results ) ) {
			wp_send_json_error( $fallback );
		}

		// Process the results and return the data.
		// The first element of the array is the total number of entries, the second is the data.
		list( , $data ) = ChartHelper::process_chart_dataset_data( $results, $start_date, $end_date );

		// Sends the JSON response back to the Ajax request, indicating success.
		wp_send_json_success(
			[
				'data'    => $data,
				'reports' => $this->get_payments_summary_in_timespan( $start_date, $end_date ),
			]
		);
	}

	/**
	 * Save the user's preferred graph style and color scheme.
	 *
	 * @since 1.8.2
	 */
	public function save_chart_preference_settings() {

		// Verify the nonce.
		check_ajax_referer( 'wpforms_payments_overview_nonce' );

		$graph_style = isset( $_POST['graphStyle'] ) ? absint( $_POST['graphStyle'] ) : 2; // Line.

		update_user_meta( get_current_user_id(), 'wpforms_dash_widget_graph_style', $graph_style );

		exit();
	}

	/**
	 * Retrieve and create payment entries from the database within the specified time frame (timespan).
	 *
	 * @global wpdb $wpdb Instantiation of the wpdb class.
	 *
	 * @since 1.8.2
	 *
	 * @param DateTimeImmutable $start_date Start date for the timespan preferably in UTC.
	 * @param DateTimeImmutable $end_date   End date for the timespan preferably in UTC.
	 * @param string            $report     Payment summary stat card name. i.e. "total_payments".
	 *
	 * @return array
	 */
	private function get_payments_in_timespan( $start_date, $end_date, $report ) {

		// Ensure given timespan dates are in UTC timezone.
		list( $utc_start_date, $utc_end_date ) = Datepicker::process_timespan_mysql( [ $start_date, $end_date ] );

		// If the time period is not a date object, leave early.
		if ( ! ( $start_date instanceof DateTimeImmutable ) || ! ( $end_date instanceof DateTimeImmutable ) ) {
			return [];
		}

		// Get the database instance.
		global $wpdb;

		// Additional (optional) where clause query arguments.
		$where_args = [];

		// SELECT clause to construct the SQL statement.
		$column_clause = $this->get_stats_column_clause( $report );

		// Determine whether the query has to be considered with a payment type.
		if ( isset( Chart::stat_cards()[ $report ]['type'] ) ) {
			$where_args['type'] = Chart::stat_cards()[ $report ]['type'];
		}

		// WHERE clauses for items query statement.
		$where_clause = $this->get_stats_where_clause( $report, $where_args );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT date_created_gmt as day, $column_clause as count FROM $this->table_name WHERE 1=1 $where_clause AND date_created_gmt BETWEEN %s AND %s GROUP BY day ORDER BY day ASC",
				[
					$utc_start_date->format( Datepicker::DATETIME_FORMAT ),
					$utc_end_date->format( Datepicker::DATETIME_FORMAT ),
				]
			),
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Fetch and generate payment summary reports from the database.
	 *
	 * @global wpdb $wpdb Instantiation of the wpdb class.
	 *
	 * @since 1.8.2
	 *
	 * @param DateTimeImmutable $start_date Start date for the timespan preferably in UTC.
	 * @param DateTimeImmutable $end_date   End date for the timespan preferably in UTC.
	 *
	 * @return array
	 */
	private function get_payments_summary_in_timespan( $start_date, $end_date ) {

		// Ensure given timespan dates are in UTC timezone.
		list( $utc_start_date, $utc_end_date ) = Datepicker::process_timespan_mysql( [ $start_date, $end_date ] );

		// If the time period is not a date object, leave early.
		if ( ! ( $start_date instanceof DateTimeImmutable ) || ! ( $end_date instanceof DateTimeImmutable ) ) {
			return [];
		}

		// Get the database instance.
		global $wpdb;

		list( $clause, $query ) = $this->prepare_sql_summary_reports( $utc_start_date, $utc_end_date );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$group_by = Chart::ACTIVE_REPORT;
		$results  = $wpdb->get_row(
			"SELECT $clause FROM (SELECT $query) AS results GROUP BY $group_by",
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// Further modifications to the result array.
		$results['total_sales'] = $this->format_amount( $results['total_sales'] );

		// Format total subscriptions.
		if ( isset( $results['total_subscription'] ) ) {
			$results['total_subscription'] = $this->format_amount( $results['total_subscription'] );
		}

		return $results;
	}

	/**
	 * Generate SQL statements to create a derived (virtual) table for the report stat cards.
	 *
	 * @global wpdb $wpdb Instantiation of the wpdb class.
	 *
	 * @since 1.8.2
	 *
	 * @param DateTimeImmutable $start_date Start date for the timespan.
	 * @param DateTimeImmutable $end_date   End date for the timespan.
	 *
	 * @return array
	 */
	private function prepare_sql_summary_reports( $start_date, $end_date ) {

		// Get the report stat cards.
		$reports = Chart::stat_cards();

		// In case there are no report stat cards defined, leave early.
		if ( empty( $reports ) ) {
			return [ '', '' ];
		}

		global $wpdb;

		$clause = []; // SELECT clause.
		$query  = []; // Query statement for the derived table.

		// Validates and creates date objects for the previous time spans.
		$prev_timespans = Datepicker::get_prev_timespan_dates( $start_date, $end_date );

		// If the timespan is not validated, leave early.
		if ( ! $prev_timespans ) {
			return [ '', '' ];
		}

		list( $prev_start_date, $prev_end_date ) = $prev_timespans;

		// Get the default number of decimals for the payment currency.
		$current_currency  = wpforms_get_currency();
		$currency_decimals = wpforms_get_currency_decimals( $current_currency );

		// Loop through the reports and create the SQL statements.
		foreach ( $reports as $report => $attributes ) {

			// Skip stat card, if it's not supposed to be displayed or disabled (upsell).
			if (
				( isset( $attributes['condition'] ) && ! $attributes['condition'] )
				|| in_array( 'disabled', $attributes['button_classes'], true )
			) {
				continue;
			}

			// Determine whether the number of rows has to be counted.
			$has_count = isset( $attributes['has_count'] ) && $attributes['has_count'];

			// Additional (optional) where clause query arguments.
			$where_args = [];

			// SELECT clause to construct the SQL statement.
			$column_clause = $this->get_stats_column_clause( $report, $has_count );

			// Update WHERE clauses for specific items in the query statement.
			if ( isset( $attributes['type'] ) ) {
				// If the report is a subscription report, use the subscription WHERE clause.
				$where_args['type'] = $attributes['type'];
			}

			// WHERE clauses for items query statement.
			$where_clause = $this->get_stats_where_clause( $report, $where_args );

			// Get the current and previous values for the report.
			$current_value = "TRUNCATE($report,$currency_decimals)";
			$prev_value    = "TRUNCATE({$report}_prev,$currency_decimals)";

			// Add the current and previous reports to the SELECT clause.
			$clause[] = $report;
			$clause[] = "ROUND( ( ( $current_value - $prev_value ) / $current_value ) * 100 ) AS {$report}_delta";

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.MissingReplacements
			$query[] = $wpdb->prepare(
				"(
					SELECT $column_clause
					FROM $this->table_name
					WHERE 1=1 $where_clause AND date_created_gmt BETWEEN %s AND %s
				) AS $report,
				(
					SELECT $column_clause
					FROM $this->table_name
					WHERE 1=1 $where_clause AND date_created_gmt BETWEEN %s AND %s
				) AS {$report}_prev",
				[
					$start_date->format( Datepicker::DATETIME_FORMAT ),
					$end_date->format( Datepicker::DATETIME_FORMAT ),
					$prev_start_date->format( Datepicker::DATETIME_FORMAT ),
					$prev_end_date->format( Datepicker::DATETIME_FORMAT ),
				]
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.MissingReplacements
		}

		return [
			implode( ',', $clause ),
			implode( ',', $query ),
		];
	}

	/**
	 * Helper method to build where clause used to construct the SQL statement.
	 *
	 * @global wpdb $wpdb Instantiation of the wpdb class.
	 *
	 * @since 1.8.2
	 *
	 * @param string $report Payment summary stat card name. i.e. "total_payments".
	 * @param array  $args   Array of arguments to filter the query.
	 *
	 * @return string
	 */
	private function get_stats_where_clause( $report, $args = [] ) {

		// Get the database instance.
		global $wpdb;

		// Get the default WHERE clause from the Payments database class.
		$clause = wpforms()->get( 'payment' )->add_secondary_where_conditions( $args );

		// If it's a valid type, add it to a WHERE clause.
		if ( isset( $args['type'] ) && ValueValidator::is_valid( $args['type'], 'type' ) ) {
			$clause .= $wpdb->prepare( ' AND type = %s', $args['type'] );
		}

		// If the coupon stats are being viewed, then add it to a WHERE clause.
		if ( $report === 'total_coupons' ) {
			$table_name = wpforms()->get( 'payment_meta' )->table_name;

			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
			$clause .= $wpdb->prepare( ' AND id IN ( SELECT payment_id FROM %1$s WHERE meta_key = "coupon_id" )', $table_name );
		}

		return $clause;
	}

	/**
	 * Helper method to build column clause used to construct the SQL statement.
	 *
	 * @since 1.8.2
	 *
	 * @param string $report     Stats card chart type (name). i.e. "total_payments".
	 * @param bool   $with_count Whether to concatenate the count to the clause.
	 *
	 * @return string
	 */
	private function get_stats_column_clause( $report, $with_count = false ) {

		// Default column clause.
		// Count the number of rows as fast as possible.
		$default = 'COUNT(*)';

		/**
		 * Filters the column clauses for the stat cards.
		 *
		 * @since 1.8.2
		 *
		 * @param array $clauses Array of column clauses.
		 */
		$clauses = (array) apply_filters(
			'wpforms_admin_payments_views_overview_ajax_stats_column_clauses',
			[
				'total_payments'     => "FORMAT({$default},0)",
				'total_sales'        => 'IFNULL(SUM(total_amount),0)',
				'total_subscription' => 'IFNULL(SUM(total_amount),0)',
				'total_coupons'      => "FORMAT({$default},0)",
			]
		);

		$clause = isset( $clauses[ $report ] ) ? $clauses[ $report ] : $default;

		// Several stat cards might include the count of payment records.
		if ( $with_count ) {
			$clause = "CONCAT({$clause}, ' (', {$default}, ')')";
		}

		return $clause;
	}

	/**
	 * Modify arguments of secondary where clauses.
	 *
	 * @since 1.8.2
	 *
	 * @param array $args Query arguments.
	 *
	 * @return array
	 */
	public function modify_secondary_where_conditions_args( $args ) {

		// Set a current mode.
		if ( ! isset( $args['mode'] ) ) {
			$args['mode'] = Page::get_mode();
		}

		return $args;
	}

	/**
	 * Format the amount for the stat card.
	 *
	 * @since 1.8.2
	 *
	 * @param string $input The input to be formatted.
	 *
	 * @return string
	 */
	private function format_amount( $input ) {

		// If the input is empty, leave early.
		if ( empty( $input ) ) {
			return '';
		}

		// Format the given amount and split the input by space.
		$amount    = wpforms_format_amount( $input, true );
		$input_arr = (array) explode( ' ', $input );

		// If the input is an array with more than one element,
		// format the amount with the concatenation of count in parentheses.
		// Example: 2185.52000000 (79).
		if ( isset( $input_arr[1] ) ) {
			return sprintf(
				'%s <span>%s</span>',
				esc_html( $amount ),
				esc_html( $input_arr[1] ) // 1: Would be count of the records.
			);
		}

		return $amount;
	}
}
