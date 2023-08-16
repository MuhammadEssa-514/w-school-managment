<?php

namespace WPForms\Db\Payments;

use WPForms_DB;

/**
 * Class for the Payment Meta database table.
 *
 * @since 1.8.2
 */
class Meta extends WPForms_DB {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.8.2
	 */
	public function __construct() {

		$this->table_name  = self::get_table_name();
		$this->primary_key = 'id';
		$this->type        = 'payment_meta';
	}

	/**
	 * Get the table name.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	public static function get_table_name() {

		global $wpdb;

		return $wpdb->prefix . 'wpforms_payment_meta';
	}

	/**
	 * Get table columns.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	public function get_columns() {

		return [
			'id'         => '%d',
			'payment_id' => '%d',
			'meta_key'   => '%s', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => '%s', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		];
	}

	/**
	 * Default column values.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	public function get_column_defaults() {

		return [
			'payment_id' => 0,
			'meta_key'   => '', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => '', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		];
	}

	/**
	 * Create the table.
	 *
	 * @since 1.8.2
	 */
	public function create_table() {

		global $wpdb;

		$charset_collate  = $wpdb->get_charset_collate();
		$max_index_length = self::MAX_INDEX_LENGTH;

		/**
		 * Note: there must be two spaces between the words PRIMARY KEY and the definition of primary key.
		 *
		 * @link https://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
		 */
		$query = "CREATE TABLE $this->table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			payment_id bigint(20) NOT NULL,
			meta_key varchar(255),
			meta_value longtext,
			PRIMARY KEY  (id),
			KEY payment_id (payment_id),
			KEY meta_key (meta_key($max_index_length)),
			KEY meta_value (meta_value($max_index_length))
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $query );
	}

	/**
	 * Insert payment meta's.
	 *
	 * @since 1.8.2
	 *
	 * @param int   $payment_id Payment ID.
	 * @param array $meta       Payment meta to be inserted.
	 */
	public function bulk_add( $payment_id, $meta ) {

		global $wpdb;

		$values = [];

		foreach ( $meta as $meta_key => $meta_value ) {

			// Empty strings are skipped.
			if ( $meta_value === '' ) {
				continue;
			}

			$values[] = $wpdb->prepare(
				'( %d, %s, %s )',
				$payment_id,
				$meta_key,
				maybe_serialize( $meta_value )
			);
		}

		if ( ! $values ) {
			return;
		}

		$values = implode( ', ', $values );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			"INSERT INTO $this->table_name
			( payment_id, meta_key, meta_value )
			VALUES $values"
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Get single payment meta.
	 *
	 * @since 1.8.2
	 *
	 * @param int         $payment_id Payment ID.
	 * @param string|null $meta_key   Payment meta to be retrieved.
	 *
	 * @return mixed Meta value.
	 */
	public function get_single( $payment_id, $meta_key ) {

		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		$meta_value = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_value FROM $this->table_name
				WHERE payment_id = %d AND meta_key = %s ORDER BY id DESC LIMIT 1",
				$payment_id,
				$meta_key
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching

		return maybe_unserialize( $meta_value );
	}

	/**
	 * Get all payment meta.
	 *
	 * @since 1.8.2
	 *
	 * @param int $payment_id Payment ID.
	 *
	 * @return array|null
	 */
	public function get_all( $payment_id ) {

		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key, meta_value as value FROM $this->table_name
				WHERE payment_id = %d ORDER BY id DESC",
				$payment_id
			),
			OBJECT_K
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Retrieve all rows based on meta_key value.
	 *
	 * @since 1.8.2
	 *
	 * @param string $meta_key   Meta key value.
	 * @param int    $payment_id Payment ID.
	 *
	 * @return object|null
	 */
	public function get_all_by( $meta_key, $payment_id ) {

		global $wpdb;

		if ( empty( $meta_key ) ) {
			return null;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_value as value FROM $this->table_name WHERE payment_id = %d AND meta_key = %s ORDER BY id DESC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$payment_id,
				$meta_key
			),
			ARRAY_A
		);
	}
}
