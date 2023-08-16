<?php
/**
 * Coupons education template.
 *
 * @since 1.8.2.2
 *
 * @var string $action       Is plugin installed?
 * @var string $path         Plugin file.
 * @var string $url          URL for download plugin.
 * @var bool   $plugin_allow Allow using plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$features = [
	__( 'Custom Coupon Codes', 'wpforms-lite' ),
	__( 'Maximum Usage Limit', 'wpforms-lite' ),
	__( 'Percentage or Fixed Discounts', 'wpforms-lite' ),
	__( 'Once Per Email Address Limit', 'wpforms-lite' ),
	__( 'Start and End Dates', 'wpforms-lite' ),
	__( 'Usage Statistics', 'wpforms-lite' ),
];

$images_url = WPFORMS_PLUGIN_URL . 'assets/images/coupons-education/';

$images = [
	[
		'url'   => 'coupons-addon-thumbnail-01.png',
		'url2x' => 'coupons-addon-screenshot-01.png',
		'title' => __( 'Coupons Overview', 'wpforms-lite' ),
	],
	[
		'url'   => 'coupons-addon-thumbnail-02.png',
		'url2x' => 'coupons-addon-screenshot-02.png',
		'title' => __( 'Coupon Settings', 'wpforms-lite' ),
	],
];

$utm_medium  = 'Payments - Coupons';
$utm_content = 'Coupons Addon';

$upgrade_link = $action === 'upgrade'
	? sprintf(
		' <strong><a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a></strong>',
		esc_url( wpforms_admin_upgrade_link( $utm_medium, $utm_content ) ),
		esc_html__( 'Upgrade to WPForms Pro', 'wpforms-lite' )
	)
	: '';
?>

<div class="coupons-education-page">
	<div class="coupons-education-page-heading">
		<p>
			<?php
			printf( /* translators: %s - WPForms.com Upgrade page URL. */
				esc_html__( 'With the Coupons addon, you can offer customers discounts using custom coupon codes. Create your own percentage or fixed rate discount, then add the Coupon field to any payment form. When a customer enters your unique code, they’ll receive the specified discount. You can also add limits to restrict when coupons are available and how often they can be used. The Coupons addon requires a license level of Pro or higher.%s', 'wpforms-lite' ),
				wp_kses(
					$upgrade_link,
					[
						'a'      => [
							'href'   => [],
							'rel'    => [],
							'target' => [],
						],
						'strong' => [],
					]
				)
			);
			?>
		</p>
	</div>

	<div class="coupons-education-page-media">
		<div class="coupons-education-page-images">
			<?php foreach ( $images as $image ) : ?>
			<figure>
				<div class="coupons-education-page-images-image">
					<img src="<?php echo esc_url( $images_url . $image['url'] ); ?>" alt="<?php echo esc_attr( $image['title'] ); ?>" />
					<a href="<?php echo esc_url( $images_url . $image['url2x'] ); ?>" class="hover" data-lity data-lity-desc="<?php echo esc_attr( $image['title'] ); ?>"></a>
				</div>
				<figcaption><?php echo esc_html( $image['title'] ); ?></figcaption>
			</figure>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="coupons-education-page-caps">
		<p><?php esc_html_e( 'Easy to Use, Yet Powerful', 'wpforms-lite' ); ?></p>
		<ul>
			<?php foreach ( $features as $feature ) : ?>
				<li>
					<i class="fa fa-solid fa-check"></i>
					<?php echo esc_html( $feature ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="coupons-education-page-button">
		<?php
			wpforms_edu_get_button(
				$action,
				$plugin_allow,
				$path,
				$url,
				[
					'medium'  => $utm_medium,
					'content' => $utm_content,
				]
			);
		?>
	</div>
</div>
