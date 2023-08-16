<?php

namespace WPForms\Admin\Payments\Views\Overview;

use WPForms\Db\Payments\ValueValidator;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Payments Overview Table class.
 *
 * @since 1.8.2
 */
class Table extends \WP_List_Table {

	/**
	 * Payment type: one-time.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	const ONE_TIME = 'one-time';

	/**
	 * Payment status: trash.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	const TRASH = 'trash';

	/**
	 * Total number of payments.
	 *
	 * @since 1.8.2
	 *
	 * @var array
	 */
	private $counts;

	/**
	 * Retrieve the table columns.
	 *
	 * @since 1.8.2
	 *
	 * @return array $columns Array of all the list table columns.
	 */
	public function get_columns() {

		static $columns;

		if ( ! empty( $columns ) ) {
			return $columns;
		}

		$columns = [
			'cb'    => '<input type="checkbox" />',
			'title' => esc_html__( 'Payment', 'wpforms-lite' ),
			'date'  => esc_html__( 'Date', 'wpforms-lite' ),
		];

		if ( wpforms()->get( 'payment_queries' )->has_different_values( 'gateway' ) ) {
			$columns['gateway'] = esc_html__( 'Gateway', 'wpforms-lite' );
		}

		if ( wpforms()->get( 'payment_queries' )->has_different_values( 'type' ) ) {
			$columns['type'] = esc_html__( 'Type', 'wpforms-lite' );
		}

		$columns['total'] = esc_html__( 'Total', 'wpforms-lite' );

		if ( wpforms()->get( 'payment_queries' )->has_subscription() ) {
			$columns['subscription'] = esc_html__( 'Subscription', 'wpforms-lite' );
		}

		$columns['form']   = esc_html__( 'Form', 'wpforms-lite' );
		$columns['status'] = esc_html__( 'Status', 'wpforms-lite' );

		/**
		 * Filters the columns in the Payments Overview table.
		 *
		 * @since 1.8.2
		 *
		 * @param array $columns Array of columns.
		 */
		return (array) apply_filters( 'wpforms_admin_payments_views_overview_table_get_columns', $columns );
	}

	/**
	 * Determine whether it is a trash view.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	private function is_trash_view() {

		return $this->is_current_view( 'trash' );
	}

	/**
	 * Define the table's sortable columns.
	 *
	 * @since 1.8.2
	 *
	 * @return array Array of all the sortable columns.
	 */
	protected function get_sortable_columns() {

		return [
			'title' => [ 'id', false ],
			'date'  => [ 'date', false ],
			'total' => [ 'total', false ],
		];
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements.
	 *
	 * @since 1.8.2
	 */
	public function prepare_items() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$page      = $this->get_pagenum();
		$order     = isset( $_GET['order'] ) ? sanitize_key( $_GET['order'] ) : 'DESC';
		$orderby   = $this->get_order_by();
		$per_page  = $this->get_items_per_page( 'wpforms_payments_per_page', 20 );
		$data_args = [
			'number'            => $per_page,
			'offset'            => $per_page * ( $page - 1 ),
			'order'             => $order,
			'orderby'           => $orderby,
			'search'            => $this->get_search_query(),
			'search_conditions' => $this->get_search_conditions(),
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			'is_published'      => $this->is_trash_view() ? 0 : 1,
		];
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$this->items = wpforms()->get( 'payment' )->get_payments( $data_args );

		$this->setup_counts( $data_args );

		// Finalize pagination.
		$this->set_pagination_args(
			[
				'total_items' => $this->counts['total'],
				'total_pages' => ceil( $this->counts['total'] / $per_page ),
				'per_page'    => $per_page,
			]
		);
	}

	/**
	 * Message to be displayed when there are no payments.
	 *
	 * @since 1.8.2
	 */
	public function no_items() {

		if ( $this->is_trash_view() ) {
			esc_html_e( 'No payments found in the trash.', 'wpforms-lite' );

			return;
		}

		if ( $this->is_current_view( 'search' ) ) {
			esc_html_e( 'No payments found, please try a different search.', 'wpforms-lite' );

			return;
		}

		esc_html_e( 'No payments found.', 'wpforms-lite' );
	}

	/**
	 * Column default values.
	 *
	 * @since 1.8.2
	 *
	 * @param array  $item        Item data.
	 * @param string $column_name Column name.
	 *
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {

		if ( method_exists( $this, "get_column_{$column_name}" ) ) {
			return $this->{"get_column_{$column_name}"}( $item );
		}

		if ( isset( $item[ $column_name ] ) ) {
			return esc_html( $item[ $column_name ] );
		}

		/**
		 * Allow to filter default column value.
		 *
		 * @since 1.8.2
		 *
		 * @param string $value       Default column value.
		 * @param array  $item        Item data.
		 * @param string $column_name Column name.
		 */
		return apply_filters( 'wpforms_admin_payments_views_overview_table_column_default_value', '', $item, $column_name );
	}

	/**
	 * Define the checkbox column.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item The current item.
	 *
	 * @return string
	 */
	protected function column_cb( $item ) {

		return '<input type="checkbox" name="payment_id[]" value="' . absint( $item['id'] ) . '" />';
	}

	/**
	 * Prepare the items and display the table.
	 *
	 * @since 1.8.2
	 */
	public function display() {

		?>
		<form id="wpforms-payments-table" method="GET" action="<?php echo esc_url( Page::get_url() ); ?>">
			<?php
			$this->display_hidden_fields();
			$this->show_reset_filter();
			$this->views();
			$this->search_box( esc_html__( 'Search Payments', 'wpforms-lite' ), 'wpforms-payments-search-input' );
			parent::display();
			?>
		</form>
		<?php
	}

	/**
	 * Display the search box.
	 *
	 * @since 1.8.2
	 *
	 * @param string $text     The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 */
	public function search_box( $text, $input_id ) {

		$search_where = $this->get_search_where_key();
		$search_mode  = $this->get_search_mode_key();
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="search_where"><?php esc_html_e( 'Select which field to use when searching for payments', 'wpforms-lite' ); ?></label>
			<select name="search_where">
				<option value="<?php echo esc_attr( Search::TITLE ); ?>" <?php selected( $search_where, Search::TITLE ); ?> ><?php echo esc_html( $this->get_search_where( Search::TITLE ) ); ?></option>
				<option value="<?php echo esc_attr( Search::TRANSACTION_ID ); ?>" <?php selected( $search_where, Search::TRANSACTION_ID ); ?> ><?php echo esc_html( $this->get_search_where( Search::TRANSACTION_ID ) ); ?></option>
				<option value="<?php echo esc_attr( Search::SUBSCRIPTION_ID ); ?>" <?php selected( $search_where, Search::SUBSCRIPTION_ID ); ?> ><?php echo esc_html( $this->get_search_where( Search::SUBSCRIPTION_ID ) ); ?></option>
				<option value="<?php echo esc_attr( Search::EMAIL ); ?>" <?php selected( $search_where, Search::EMAIL ); ?> ><?php echo esc_html( $this->get_search_where( Search::EMAIL ) ); ?></option>
				<option value="<?php echo esc_attr( Search::CREDIT_CARD ); ?>" <?php selected( $search_where, Search::CREDIT_CARD ); ?> ><?php echo esc_html( $this->get_search_where( Search::CREDIT_CARD ) ); ?></option>
				<option value="<?php echo esc_attr( Search::ANY ); ?>" <?php selected( $search_where, Search::ANY ); ?> ><?php echo esc_html( $this->get_search_where( Search::ANY ) ); ?></option>
			</select>
			<label class="screen-reader-text" for="search_mode"><?php esc_html_e( 'Select which comparison method to use when searching for payments', 'wpforms-lite' ); ?></label>
			<select name="search_mode">
				<option value="<?php echo esc_attr( Search::MODE_EQUALS ); ?>" <?php selected( $search_mode, Search::MODE_EQUALS ); ?> ><?php echo esc_html( $this->get_search_mode( Search::MODE_EQUALS ) ); ?></option>
				<option value="<?php echo esc_attr( Search::MODE_STARTS ); ?>" <?php selected( $search_mode, Search::MODE_STARTS ); ?> ><?php echo esc_html( $this->get_search_mode( Search::MODE_STARTS ) ); ?></option>
				<option value="<?php echo esc_attr( Search::MODE_CONTAINS ); ?>" <?php selected( $search_mode, Search::MODE_CONTAINS ); ?> ><?php echo esc_html( $this->get_search_mode( Search::MODE_CONTAINS ) ); ?></option>
			</select>
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?></label>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php echo esc_attr( $this->get_search_query() ); ?>" />
			<input type="submit" class="button" value="<?php echo esc_attr( $text ); ?>" />
		</p>
		<?php
	}

	/**
	 * Get bulk actions to be displayed in bulk action dropdown.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {

		if ( $this->is_trash_view() ) {
			return [
				'restore' => esc_html__( 'Restore', 'wpforms-lite' ),
				'delete'  => esc_html__( 'Delete Permanently', 'wpforms-lite' ),
			];
		}

		return [
			'trash' => esc_html__( 'Move to Trash', 'wpforms-lite' ),
		];
	}

	/**
	 * Generates the table navigation above or below the table.
	 *
	 * @since 1.8.2
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {

		if ( $this->has_items() ) {
			parent::display_tablenav( $which );

			return;
		}

		echo '<div class="tablenav ' . esc_attr( $which ) . '">';

		if ( $this->is_trash_view() ) {
			echo '<div class="alignleft actions bulkactions">';
			$this->bulk_actions();
			echo '</div>';
		}

		echo '<br class="clear" />';
		echo '</div>';
	}

	/**
	 * List of CSS classes for the "WP_List_Table" table tag.
	 *
	 * @global string $mode List table view mode.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	protected function get_table_classes() {

		global $mode;

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$mode       = get_user_setting( 'posts_list_mode', 'list' );
		$mode_class = esc_attr( 'table-view-' . $mode );
		$classes    = [
			'widefat',
			'striped',
			'wpforms-table-list',
			'wpforms-table-list-payments',
			$mode_class,
		];

		// For styling purposes, we'll add a dedicated class name for determining the number of visible columns.
		// The ideal threshold for applying responsive styling is set at "5" columns based on the need for "Tablet" view.
		$columns_class = $this->get_column_count() > 5 ? 'many' : 'few';

		$classes[] = "has-{$columns_class}-columns";

		return $classes;
	}

	/**
	 * Get valid status from request.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_valid_status_from_request() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		return ! empty( $_REQUEST['status'] ) && ( ValueValidator::is_valid( $_REQUEST['status'], 'status' ) || $_REQUEST['status'] === self::TRASH ) ? $_REQUEST['status'] : '';
	}

	/**
	 * Get search where value.
	 *
	 * @since 1.8.2
	 *
	 * @param string $search_key Search where key.
	 *
	 * @return string Return default search where value if not valid key provided.
	 */
	private function get_search_where( $search_key ) {

		$allowed_values = $this->get_allowed_search_where();

		return $search_key && isset( $allowed_values[ $search_key ] ) ? $allowed_values[ $search_key ] : $allowed_values[ Search::TITLE ];
	}

	/**
	 * Get search where key.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_search_where_key() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$where_key = isset( $_GET['search_where'] ) ? sanitize_key( $_GET['search_where'] ) : '';

		return isset( $this->get_allowed_search_where()[ $where_key ] ) ? $where_key : Search::TITLE;
	}

	/**
	 * Get allowed search where values.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	private function get_allowed_search_where() {

		static $search_values;

		if ( ! $search_values ) {

			$search_values = [
				Search::TITLE           => __( 'Payment Title', 'wpforms-lite' ),
				Search::TRANSACTION_ID  => __( 'Transaction ID', 'wpforms-lite' ),
				Search::EMAIL           => __( 'Customer Email', 'wpforms-lite' ),
				Search::SUBSCRIPTION_ID => __( 'Subscription ID', 'wpforms-lite' ),
				Search::CREDIT_CARD     => __( 'Last 4 digits of credit card', 'wpforms-lite' ),
				Search::ANY             => __( 'Any payment field', 'wpforms-lite' ),
			];
		}

		return $search_values;
	}

	/**
	 * Get search where value.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode_key Search mode key.
	 *
	 * @return string Return default search mode value if not valid key provided.
	 */
	private function get_search_mode( $mode_key ) {

		$allowed_modes = $this->get_allowed_search_modes();

		return $mode_key && isset( $allowed_modes[ $mode_key ] ) ? $allowed_modes[ $mode_key ] : $allowed_modes[ Search::MODE_EQUALS ];
	}

	/**
	 * Get search mode key.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_search_mode_key() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$where_mode = isset( $_GET['search_mode'] ) ? sanitize_key( $_GET['search_mode'] ) : '';

		return isset( $this->get_allowed_search_modes()[ $where_mode ] ) ? $where_mode : Search::MODE_EQUALS;
	}

	/**
	 * Get allowed search mode params.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	private function get_allowed_search_modes() {

		static $search_modes;

		if ( ! $search_modes ) {

			$search_modes = [
				Search::MODE_EQUALS   => __( 'equals to', 'wpforms-lite' ),
				Search::MODE_STARTS   => __( 'starts with', 'wpforms-lite' ),
				Search::MODE_CONTAINS => __( 'contains', 'wpforms-lite' ),
			];
		}

		return $search_modes;
	}

	/**
	 * Prepare counters.
	 *
	 * @since 1.8.2
	 *
	 * @param array $args Query data arguments.
	 */
	private function setup_counts( $args ) {

		$this->counts = [
			'total'     => 0,
			'published' => wpforms()->get( 'payment_queries' )->count_all( array_merge( $args, [ 'is_published' => 1 ] ) ),
			'trash'     => wpforms()->get( 'payment_queries' )->count_all( array_merge( $args, [ 'is_published' => 0 ] ) ),
		];

		if ( $this->is_trash_view() ) {
			$this->counts['total'] = $this->counts['trash'];

			return;
		}

		$this->counts['total'] = $this->counts['published'];
	}

	/**
	 * Get the orderby value.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_order_by() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['orderby'] ) ) {
			return 'id';
		}

		if ( $_GET['orderby'] === 'date' ) {
			return 'date_updated_gmt';
		}

		if ( $_GET['orderby'] === 'total' ) {
			return 'total_amount';
		}

		return 'id';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get payment column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_title( array $item ) {

		$title      = $this->get_payment_title( $item );
		$na_status  = empty( $title ) ? sprintf( '<span class="payment-title-is-empty">- %s</span>', Helpers::get_placeholder_na_text() ) : '';
		$single_url = add_query_arg(
			[
				'page'       => 'wpforms-payments',
				'view'       => 'single',
				'payment_id' => absint( $item['id'] ),
			],
			admin_url( 'admin.php' )
		);

		return sprintf( '<a href="%1$s">#%2$d %3$s</a> %4$s', esc_url( $single_url ), $item['id'], esc_html( $title ), $na_status );
	}

	/**
	 * Get date column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_date( $item ) {

		$date      = $item['date_updated_gmt'];
		$timestamp = strtotime( $date );

		/* translators: %s - relative time difference, e.g. "5 minutes", "12 days". */
		$human = sprintf( esc_html__( '%s ago', 'wpforms-lite' ), human_time_diff( $timestamp ) );

		return sprintf( '<span title="%s">%s</span>', gmdate( 'Y-m-d H:i', $timestamp ), $human );
	}

	/**
	 * Get gateway column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_gateway( array $item ) {

		if ( ! isset( $item['gateway'] ) || ! ValueValidator::is_valid( $item['gateway'], 'gateway' ) ) {
			return '';
		}

		return ValueValidator::get_allowed_gateways()[ $item['gateway'] ];
	}

	/**
	 * Get total column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_total( array $item ) {

		return esc_html( $this->get_formatted_amount_from_item( $item ) );
	}

	/**
	 * Get form column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_form( array $item ) {

		// Display "N/A" placeholder text if the form is not found or not published.
		if ( empty( $item['form_id'] ) || get_post_status( $item['form_id'] ) !== 'publish' ) {
			return Helpers::get_placeholder_na_text();
		}

		// Display the form name with a link to the form builder.
		$form = get_post( $item['form_id'] );
		$name = ! empty( $form->post_title ) ? $form->post_title : $form->post_name;
		$url  = add_query_arg(
			[
				'form_id' => absint( $form->ID ),
				'page'    => 'wpforms-builder',
				'view'    => 'fields',
			],
			Page::get_url()
		);

		return sprintf( '<a href="%s">%s</a>', esc_url( $url ), wp_kses_post( $name ) );
	}

	/**
	 * Get status column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_status( array $item ) {

		if ( ! isset( $item['status'] ) || ! ValueValidator::is_valid( $item['status'], 'status' ) ) {
			return Helpers::get_placeholder_na_text();
		}

		return sprintf(
			wp_kses(
				'<span class="wpforms-payment-status status-%1$s">%2$s</span>',
				[
					'span' => [
						'class' => [],
					],
					'i'    => [
						'class' => [],
						'title' => [],
					],
				]
			),
			strtolower( $item['status'] ),
			ValueValidator::get_allowed_statuses()[ $item['status'] ]
		);
	}

	/**
	 * Get subscription column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_subscription( array $item ) {

		if ( $item['type'] === self::ONE_TIME ) {
			return Helpers::get_placeholder_na_text();
		}

		$amount      = $this->get_formatted_amount_from_item( $item );
		$description = Helpers::get_subscription_description( $item['id'], $amount );

		return sprintf(
			'<span class="wpforms-subscription-status status-%1$s">%2$s</span>',
			sanitize_html_class( $item['subscription_status'] ),
			$description
		);
	}

	/**
	 * Get type column value.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_column_type( array $item ) {

		if ( ! isset( $item['type'] ) || ! ValueValidator::is_valid( $item['type'], 'type' ) ) {
			return Helpers::get_placeholder_na_text();
		}

		return ValueValidator::get_allowed_types()[ $item['type'] ];
	}

	/**
	 * Get payment title.
	 *
	 * @param array $item Payment item.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_payment_title( array $item ) {

		if ( empty( $item['title'] ) ) {
			return '';
		}

		return ' - ' . $item['title'];
	}

	/**
	 * Get subscription icon.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_subscription_status_icon( array $item ) {

		if ( empty( $item['subscription_id'] ) ) {
			return '';
		}

		return '<span class="dashicons dashicons-marker"></span>';
	}

	/**
	 * Get formatted amount from item.
	 *
	 * @since 1.8.2
	 *
	 * @param array $item Payment item.
	 *
	 * @return string
	 */
	private function get_formatted_amount_from_item( $item ) {

		if ( empty( $item['total_amount'] ) ) {
			return '';
		}

		return wpforms_format_amount( wpforms_sanitize_amount( $item['total_amount'] ), true );
	}

	/**
	 * Show reset filter box.
	 *
	 * @since 1.8.2
	 */
	private function show_reset_filter() {

		if ( ! Search::is_search() ) {
			return;
		}

		$search_where = $this->get_search_where( $this->get_search_where_key() );
		$search_mode  = $this->get_search_mode( $this->get_search_mode_key() );
		?>
			<div id="wpforms-reset-filter" class="wpforms-reset-filter">
				<?php
				printf(
					wp_kses( /* translators: %d - number of payments found. */
						_n(
							'Found <strong>%d payment</strong> where',
							'Found <strong>%d payments</strong> where',
							$this->counts['total'],
							'wpforms-lite'
						),
						[
							'strong' => [],
						]
					),
					(int) $this->counts['total']
				);

				// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				printf(
					' <em>%s</em> %s "<em>%s</em>" <a href="%s" class="reset fa fa-times-circle" title="%s"></a>',
					esc_html( $search_where ),
					esc_html( $search_mode ),
					esc_html( wp_unslash( $_GET['s'] ) ),
					esc_url( Page::get_url() ),
					esc_attr__( 'Reset search', 'wpforms-lite' )
				);
				// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				?>
			</div>
		<?php
	}

	/**
	 * Get selectors which will be displayed over the bulk action menu.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	protected function get_views() {

		$base          = remove_query_arg( [ 'type', 'status', 'paged' ] );
		$is_trash_view = $this->is_trash_view();

		$views = [
			'all' => sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( $base ),
				$this->is_current_view( 'all' ) ? ' class="current"' : '',
				esc_html__( 'All', 'wpforms-lite' ),
				(int) $this->counts['published']
			),
		];

		/** This filter is documented in \WPForms\Admin\Payments\Views\Overview\Table::display_tablenav(). */
		if ( $this->counts['trash'] || $is_trash_view ) {
			$views['trash'] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( add_query_arg( [ 'status' => 'trash' ], $base ) ),
				$is_trash_view ? ' class="current"' : '',
				esc_html__( 'Trash', 'wpforms-lite' ),
				(int) $this->counts['trash']
			);
		}

		return $views;
	}

	/**
	 * Determine whether it is a passed view.
	 *
	 * @since 1.8.2
	 *
	 * @param string $view Current view to validate.
	 *
	 * @return bool
	 */
	private function is_current_view( $view ) {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( $view === 'trash' && isset( $_GET['status'] ) && $_GET['status'] === self::TRASH ) {
			return true;
		}

		if ( $view === 'search' && Search::is_search() ) {
			return true;
		}

		return $view === 'all';
	}

	/**
	 * Get value provided in search field.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_search_query() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		return Search::is_search() ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	}

	/**
	 * Get search conditions.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	private function get_search_conditions() {

		if ( ! Search::is_search() ) {
			return [];
		}

		return [
			'search_where' => $this->get_search_where_key(),
			'search_mode'  => $this->get_search_mode_key(),
		];
	}

	/**
	 * Display table form's hidden fields.
	 *
	 * @since 1.8.2
	 */
	private function display_hidden_fields() {
		?>
		<input type="hidden" name="page" value="wpforms-payments">
		<input type="hidden" name="paged" value="1">
		<?php

		$this->display_status_hidden_field();
		$this->display_order_hidden_fields();
	}

	/**
	 * Display hidden field with status value.
	 *
	 * @since 1.8.2
	 */
	private function display_status_hidden_field() {

		$status = $this->get_valid_status_from_request();

		if ( $status ) {
			printf( '<input type="hidden" name="status" value="%s">', esc_attr( wp_unslash( $status ) ) );
		}
	}

	/**
	 * Display hidden fields with order and orderby values.
	 *
	 * @since 1.8.2
	 */
	private function display_order_hidden_fields() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( [ 'orderby', 'order' ] as $param ) {
			if ( ! empty( $_GET[ $param ] ) ) {
				printf(
					'<input type="hidden" name="%s" value="%s">',
					esc_attr( $param ),
					esc_attr( wp_unslash( $_GET[ $param ] ) )
				);
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}
}
