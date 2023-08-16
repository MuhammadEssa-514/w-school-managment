<?php
/**
 * Single Payment page - Payment details template for single and subscription data.
 *
 * @since 1.8.2
 *
 * @var array  $entry_fields   Entry object.
 * @var array  $form_data      Form data.
 * @var int    $entry_id       Entry ID.
 * @var string $entry_id_title Entry title id.
 * @var string $entry_url      Entry page URL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="wpforms-payment-entry-fields" class="postbox">

	<div class="postbox-header">
		<h2 class="hndle">
			<span><?php echo esc_html__( 'Entry Summary', 'wpforms-lite' ); ?></span>
			<?php if ( ! empty( $entry_id_title ) ) : ?>
			<span class="wpforms-payment-entry-id"><?php echo esc_html( $entry_id_title ); ?></span>
			<?php endif; ?>
		</h2>
	</div>

	<div class="inside">

		<?php foreach ( $entry_fields as $key => $field ) : ?>

			<div class="wpforms-payment-entry-field <?php echo wpforms_sanitize_classes( $field['field_class'] ); ?>" >

				<p class="wpforms-payment-entry-field-name">
					<?php echo esc_html( wp_strip_all_tags( $field['field_name'] ) ); ?>
				</p>

				<div class="wpforms-payment-entry-field-value">
					<?php echo wp_kses_post( nl2br( make_clickable( $field['field_value'] ) ) ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $entry_id_title ) : ?>
		<div class="wpforms-payment-actions">
			<div class="status"></div>
			<div class="actions">
				<a class="button" href="<?php echo esc_url( $entry_url ); ?>">
					<?php echo esc_html__( 'View Entry', 'wpforms-lite' ); ?>
				</a>
				<div class="clear"></div>
			</div>
		</div>
	<?php endif; ?>
</div>
