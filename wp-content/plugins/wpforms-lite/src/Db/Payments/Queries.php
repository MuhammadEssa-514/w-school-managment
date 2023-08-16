<?php

namespace WPForms\Db\Payments;

/**
 * Class for the Payments database queries.
 *
 * @since 1.8.2
 */
class Queries extends Payment {

	/**
	 * Check if given payment table column has different values.
	 *
	 * @since 1.8.2
	 *
	 * @param string $column Column name.
	 *
	 * @return bool
	 */
	public function has_different_values( $column ) {

		global $wpdb;

		$subquery[] = "SELECT $column FROM $this->table_name WHERE 1=1";
		$subquery[] = $this->add_secondary_where_conditions();
		$subquery[] = 'LIMIT 1';
		$subquery   = implode( ' ', $subquery );

		$query[] = "SELECT $column FROM $this->table_name WHERE 1=1";
		$query[] = $this->add_secondary_where_conditions();
		$query[] = "AND $column != ( $subquery )";
		$query[] = 'LIMIT 1';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->get_var( implode( ' ', $query ) );

		return ! empty( $result );
	}

	/**
	 * Check if there is a subscription payment.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	public function has_subscription() {

		global $wpdb;

		$subscription_types = wpforms_wpdb_prepare_in( array_keys( ValueValidator::get_allowed_subscription_types() ) );

		$query[] = "SELECT type FROM {$this->table_name} WHERE 1=1";
		$query[] = $this->add_secondary_where_conditions();
		$query[] = "AND type IN ( {$subscription_types} )";
		$query[] = 'LIMIT 1';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->get_var( implode( ' ', $query ) );

		return ! empty( $result );
	}

	/**
	 * Retrieve the number of all payments.
	 *
	 * @since 1.8.2
	 *
	 * @param array $args Redefine query parameters by providing own arguments.
	 *
	 * @return int Number of payments or count of payments.
	 */
	public function count_all( $args = [] ) {

		global $wpdb;

		$query[] = "SELECT COUNT(*) FROM {$this->table_name} as p";

		/**
		 * Add parts to the query for count_all method before the WHERE clause.
		 *
		 * @since 1.8.2
		 *
		 * @param string $where Before the WHERE clause in DB query.
		 * @param array  $args  Query arguments.
		 *
		 * @return string
		 */
		$query[] = apply_filters( 'wpforms_db_payments_queries_count_all_query_before_where', '', $args );
		$query[] = 'WHERE 1=1';
		$query[] = $this->add_secondary_where_conditions( $args );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		return (int) $wpdb->get_var( implode( ' ', $query ) );
	}

	/**
	 * Get next payment.
	 *
	 * @since 1.8.2
	 *
	 * @param int   $payment_id Payment ID.
	 * @param array $args       Where conditions.
	 *
	 * @return object|null Object from DB values or null.
	 */
	public function get_next( $payment_id, $args = [] ) {

		global $wpdb;

		if ( empty( $payment_id ) ) {
			return null;
		}

		$query[] = "SELECT * FROM {$this->table_name}";
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$query[] = $wpdb->prepare( "WHERE $this->primary_key > %d", $payment_id );
		$query[] = $this->add_secondary_where_conditions( $args );
		$query[] = "ORDER BY $this->primary_key LIMIT 1";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_row( implode( ' ', $query ) );
	}

	/**
	 * Get previous payment.
	 *
	 * @since 1.8.2
	 *
	 * @param int   $payment_id Payment ID.
	 * @param array $args       Where conditions.
	 *
	 * @return object|null Object from DB values or null.
	 */
	public function get_prev( $payment_id, $args = [] ) {

		global $wpdb;

		if ( empty( $payment_id ) ) {
			return null;
		}

		$query[] = "SELECT * FROM $this->table_name";
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$query[] = $wpdb->prepare( "WHERE $this->primary_key < %d", $payment_id );
		$query[] = $this->add_secondary_where_conditions( $args );
		$query[] = "ORDER BY $this->primary_key DESC LIMIT 1";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_row( implode( ' ', $query ) );
	}

	/**
	 * Get previous payments count.
	 *
	 * @since 1.8.2
	 *
	 * @param int   $payment_id Payment ID.
	 * @param array $args       Where conditions.
	 *
	 * @return int
	 */
	public function get_prev_count( $payment_id, $args = [] ) {

		global $wpdb;

		if ( empty( $payment_id ) ) {
			return 0;
		}

		$query[] = "SELECT COUNT( $this->primary_key ) FROM $this->table_name";
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$query[] = $wpdb->prepare( "WHERE $this->primary_key < %d", $payment_id );
		$query[] = $this->add_secondary_where_conditions( $args );
		$query[] = "ORDER BY $this->primary_key ASC";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $wpdb->get_var( implode( ' ', $query ) );
	}
}
