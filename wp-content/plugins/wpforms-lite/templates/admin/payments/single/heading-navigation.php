<?php
/**
 * Single Payment page - Heading navigation.
 *
 * @since 1.8.2
 *
 * @var int    $count        Count of all payments.
 * @var int    $prev_count   Count of previous payments.
 * @var string $prev_url     Previous payment URL.
 * @var string $prev_class   Previous payment class.
 * @var string $next_url     Next payment URL.
 * @var string $next_class   Next payment class.
 * @var string $overview_url Payments Overview page URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<a href="<?php echo esc_url( $overview_url ); ?>" class="page-title-action wpforms-btn wpforms-btn-orange">
	<svg class="page-title-action-icon" viewBox="0 0 13 12" xmlns="http://www.w3.org/2000/svg"><path d="M12.5978 5.20112V6.79888H3.1648L6.29888 9.93296L5.5 11.5L0 6L5.5 0.5L6.29888 2.06704L3.1648 5.20112H12.5978Z"/></svg>
	<span class="page-title-action-text"><?php esc_html_e( 'Back to All Payments', 'wpforms-lite' ); ?></span>
</a>

<div class="wpforms-admin-single-navigation">
	<div class="wpforms-admin-single-navigation-text">
		<?php
		printf( /* translators: %1$d - current number of payment, %2$d - total number of payments. */
			esc_html__( 'Payment %1$d of %2$d', 'wpforms-lite' ),
			(int) $prev_count + 1,
			(int) $count
		);
		?>
	</div>
	<div class="wpforms-admin-single-navigation-buttons">
		<a
			href="<?php echo esc_url( $prev_url ); ?>"
			title="<?php esc_attr_e( 'Previous payment', 'wpforms-lite' ); ?>"
			id="wpforms-admin-single-navigation-prev-link"
			class="wpforms-btn-grey <?php echo sanitize_html_class( $prev_class ); ?>">
			<span class="dashicons dashicons-arrow-left-alt2"></span>
		</a>
		<span
			class="wpforms-admin-single-navigation-current"
			title="<?php esc_attr_e( 'Current payment', 'wpforms-lite' ); ?>">
			<?php echo (int) $prev_count + 1; ?>
		</span>
		<a
			href="<?php echo esc_url( $next_url ); ?>"
			title="<?php esc_attr_e( 'Next payment', 'wpforms-lite' ); ?>"
			id="wpforms-admin-single-navigation-next-link"
			class="wpforms-btn-grey <?php echo sanitize_html_class( $next_class ); ?>">
			<span class="dashicons dashicons-arrow-right-alt2"></span>
		</a>
	</div>
</div>
