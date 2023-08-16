<?php

namespace WPForms\Admin\Payments\Views\Overview;

use WPForms\Admin\Helpers\Datepicker;

/**
 * Payment Overview Chart class.
 *
 * @since 1.8.2
 */
class Chart {

	/**
	 * Default payments summary report stat card.
	 *
	 * @since 1.8.2
	 */
	const ACTIVE_REPORT = 'total_payments';

	/**
	 * Whether the chart should be displayed.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	private function allow_load() {

		// Avoid displaying the chart when search request is performed.
		if ( Search::is_search() ) {
			return false;
		}

		return true;
	}

	/**
	 * Display the chart.
	 *
	 * @since 1.8.2
	 */
	public function display() {

		// If the chart should not be displayed, leave early.
		if ( ! $this->allow_load() ) {
			return;
		}

		// Output HTML elements on the page.
		$this->output_top_bar();
		$this->output_test_mode_banner();
		$this->output_chart();
	}

	/**
	 * Handles output of the overview page top-bar.
	 *
	 * Includes:
	 * 1. Heading.
	 * 2. Datepicker filter.
	 * 3. Chart theme customization settings.
	 *
	 * @since 1.8.2
	 */
	private function output_top_bar() {

		list( $choices, $chosen_filter, $value ) = Datepicker::process_datepicker_choices();

		?>
		<div class="wpforms-overview-top-bar">
			<div class="wpforms-overview-top-bar-heading">
				<h2><?php esc_html_e( 'Payments Summary', 'wpforms-lite' ); ?></h2>
			</div>

			<div class="wpforms-overview-top-bar-filters">
				<?php
				// Output "Mode Toggle" template.
				( new ModeToggle() )->display();

				// Output "Datepicker" form template.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpforms_render(
					'admin/components/datepicker',
					[
						'id'            => 'payments',
						'action'        => Page::get_url(),
						'chosen_filter' => $chosen_filter,
						'choices'       => $choices,
						'value'         => $value,
					],
					true
				);
				?>
				<div class="wpforms-overview-chart-settings">
					<?php
					// Output "Settings" template.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wpforms_render(
						'admin/dashboard/widget/settings',
						array_merge( $this->get_chart_settings(), [ 'enabled' => true ] ),
						true
					);
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display a banner when viewing test data.
	 *
	 * @since 1.8.2
	 *
	 * @return void
	 */
	private function output_test_mode_banner() {

		// Determine if we are viewing test data.
		if ( Page::get_mode() !== 'test' ) {
			return;
		}
		?>
		<div class="wpforms-payments-viewing-test-mode">
			<p>
				<?php esc_html_e( 'Viewing Test Data', 'wpforms-lite' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Handles output of the overview page chart (graph).
	 *
	 * @since 1.8.2
	 */
	private function output_chart() {

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="wpforms-payments-overview-stats">';

		echo wpforms_render(
			'admin/components/chart',
			[
				'id'     => 'payments',
				'notice' => [
					'heading'     => esc_html__( 'No payments for selected period', 'wpforms-lite' ),
					'description' => esc_html__( 'Please select a different period or check back later.', 'wpforms-lite' ),
				],
			],
			true
		);

		echo wpforms_render(
			'admin/payments/reports',
			[
				'current'   => self::ACTIVE_REPORT,
				'statcards' => self::stat_cards(),
			],
			true
		);

		echo '</div>';
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get the user’s preferences for displaying of the graph.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	public function get_chart_settings() {

		$graph_style = get_user_meta( get_current_user_id(), 'wpforms_dash_widget_graph_style', true );

		return [
			'graph_style' => $graph_style ? absint( $graph_style ) : 2, // Line.
		];
	}

	/**
	 * Get the stat cards for the payment summary report.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	public static function stat_cards() {

		return [
			'total_payments'     => [
				'label'          => esc_html__( 'Total Payments', 'wpforms-lite' ),
				'button_classes' => [
					'total-payments',
				],
			],
			'total_sales'        => [
				'label'          => esc_html__( 'Total Sales', 'wpforms-lite' ),
				'button_classes' => [
					'total-sales',
					'is-amount',
				],
			],
			'total_subscription' => [
				'label'          => esc_html__( 'Subscriptions', 'wpforms-lite' ),
				'condition'      => wpforms()->get( 'payment_queries' )->has_subscription(),
				'type'           => 'subscription',
				'has_count'      => true,
				'button_classes' => [
					'total-subscription',
					'is-amount',
				],
			],
			'total_coupons'      => [
				'label'          => esc_html__( 'Coupons', 'wpforms-lite' ),
				'button_classes' => [
					'total-coupons',
				],
			],
		];
	}
}
