<?php
/**
 * Popover (range) datepicker template.
 *
 * @since 1.8.2
 *
 * @var string $action        The URL that processes the form submission. Ideally points out to the current admin page URL.
 * @var string $id            Identifier to outline the context of where the datepicker will be used. e.g., "entries".
 * @var string $chosen_filter Currently selected filter or date range values. Last "X" days, or i.e. Feb 8, 2023 - Mar 9, 2023.
 * @var array  $choices       A list of date filter options for the datepicker module.
 * @var string $value         Assigned timespan dates.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// An array of allowed HTML elements and attributes for the datepicker choices.
$choices_allowed_html = [
	'li'    => [],
	'label' => [],
	'input' => [
		'type'        => [],
		'name'        => [],
		'value'       => [],
		'checked'     => [],
		'aria-hidden' => [],
	],
];

?>
<form class="wpforms-overview-top-bar-filter-form" method="get" action="<?php echo esc_url( $action ); ?>">
	<input type="hidden" name="page" value="wpforms-<?php echo esc_attr( $id ); ?>" />
	<?php
	// Output hidden fields for the current orderby and order values.

	// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! empty( $_REQUEST['orderby'] ) ) {
		echo '<input type="hidden" name="orderby" value="' . esc_attr( wp_unslash( $_REQUEST['orderby'] ) ) . '" />';
	}
	if ( ! empty( $_REQUEST['order'] ) ) {
		echo '<input type="hidden" name="order" value="' . esc_attr( wp_unslash( $_REQUEST['order'] ) ) . '" />';
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	?>

	<button id="wpforms-datepicker-popover-button" class="button" role="button" aria-haspopup="true">
		<?php echo esc_html( $chosen_filter ); ?>
	</button>
	<div class="wpforms-datepicker-popover">
		<div class="wpforms-datepicker-popover-content">
			<ul class="wpforms-datepicker-choices" aria-label="<?php esc_attr_e( 'Datepicker options', 'wpforms-lite' ); ?>" aria-orientation="vertical">
				<?php echo wp_kses( '<li>' . implode( '</li><li>', (array) $choices ) . '</li>', $choices_allowed_html ); ?>
			</ul>
			<div class="wpforms-datepicker-calendar">
				<input
					type="text"
					name="date"
					tabindex="-1"
					aria-hidden="true"
					id="wpforms-<?php echo esc_attr( $id ); ?>-overview-datepicker"
					value="<?php echo esc_attr( $value ); ?>"
				>
			</div>
			<div class="wpforms-datepicker-action">
				<button class="button-secondary" type="reset"><?php esc_html_e( 'Cancel', 'wpforms-lite' ); ?></button>
				<button class="button-primary" type="submit"><?php esc_html_e( 'Apply', 'wpforms-lite' ); ?></button>
			</div>
		</div>
	</div>
</form>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
