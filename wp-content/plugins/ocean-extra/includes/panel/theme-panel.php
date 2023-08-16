<?php
/**
 * Theme Panel
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_Extra_Theme_Panel {

	/**
	 * Start things up
	 */
	public function __construct() {

		// Display notice if the Sticky Header is not activated
		add_action( 'admin_notices', array( 'Ocean_Extra_Theme_Panel', 'sticky_notice' ) );
		add_action( 'admin_init', array( 'Ocean_Extra_Theme_Panel', 'dismiss_sticky_notice' ) );
		add_action( 'admin_enqueue_scripts', array( 'Ocean_Extra_Theme_Panel', 'sticky_notice_css' ) );
	}

	/**
	 * Display notice if the Sticky Header is not activated
	 *
	 * @since 1.4.12
	 */
	public static function sticky_notice() {
		global $pagenow;
		global $owp_fs;
		$need_to_upgrade = ! empty( $owp_fs ) ? $owp_fs->is_pricing_page_visible() : false;

		if ( ! $need_to_upgrade
			|| '1' === get_option( 'owp_dismiss_sticky_notice' )
			|| true === apply_filters( 'oceanwp_licence_tab_enable', false )
			|| ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$page_obj = null;
		if ( isset( $_GET['page'] ) ) {
			$page_obj = sanitize_text_field( wp_unslash( $_GET['page'] ) );
		}
		// Display on the plugins and Theme Panel pages
		if ( 'plugins.php' === $pagenow || ( 'admin.php' === $pagenow && 'oceanwp' === $page_obj ) ) {
			wp_enqueue_style( 'oe-admin-notice', plugins_url( '/assets/css/notice.min.css', __FILE__ ) );

			$dismiss = wp_nonce_url( add_query_arg( 'owp_sticky_notice', 'dismiss_btn' ), 'dismiss_btn' );
			?>

			<div class="notice notice-success ocean-extra-notice owp-sticky-notice">
				<div class="notice-inner">
					<span class="icon-side">
						<span class="owp-notification-icon">
							<img src="<?php echo esc_attr ( OE_URL . 'includes/themepanel/assets/img/themepanel-icon.svg'); ?>">
						</span>
					</span>
					<div class="notice-content">
					<h2><?php echo esc_html__( 'Lovely jubbly! Your website is starting to look fabulous!','ocean-extra' ); ?></h2>
					<h3 class="notice-subheading">
					<?php
					echo sprintf(
						esc_html__( 'But you know what would make your website look stunning and leave your visitors in awe? The  %1$sOcean Core Extensions Bundle%2$s features.', 'ocean-extra' ),
						'<a href="https://oceanwp.org/core-extensions-bundle/" target="_blank">',
						'</a>'
					);
					?>
					</h3>
					<p><?php echo esc_html__( 'You\'ll get:', 'ocean-extra' ); ?></p>

							<ul>
								<li> <?php echo esc_html__('access to premium website template demos,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('sticky header,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('royalty free images and icons with templates,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('Elementor widgets','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('Gutenberg blocks,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('images and icons library,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('and so much more.','ocean-extra' ); ?> </li>
							</ul>
						<p><a href="<?php echo esc_url('https://oceanwp.org/core-extensions-bundle/' ); ?>" class="btn button-primary" target="_blank"><span class="dashicons dashicons-external"></span><span><?php _e( 'Yes! I want the Upgrade', 'ocean-extra' ); ?></span></a></p>
					</div>
					<a href="<?php echo $dismiss; ?>" class="dismiss"><span class="dashicons dashicons-dismiss"></span></a>
				</div>
			</div>

			<?php
		}
	}

	/**
	 * Dismiss Sticky Header admin notice
	 *
	 * @since 1.4.12
	 */
	public static function dismiss_sticky_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! isset( $_GET['owp_sticky_notice'] ) ) {
			return;
		}

		if ( 'dismiss_btn' === $_GET['owp_sticky_notice'] ) {
			check_admin_referer( 'dismiss_btn' );
			update_option( 'owp_dismiss_sticky_notice', '1' );
		}

		wp_redirect( remove_query_arg( 'owp_sticky_notice' ) );
		exit;
	}

	/**
	 * Sticky Header CSS
	 *
	 * @since 1.4.19
	 */
	public static function sticky_notice_css( $hook ) {
		global $pagenow;
		global $owp_fs;
		$need_to_upgrade = ! empty( $owp_fs ) ? $owp_fs->is_pricing_page_visible() : false;

		if ( ! $need_to_upgrade
			|| '1' === get_option( 'owp_dismiss_sticky_notice' )
			|| true == apply_filters( 'oceanwp_licence_tab_enable', false )
			|| ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'toplevel_page_oceanwp' != $hook && 'plugins.php' != $pagenow ) {
			return;
		}

		// CSS
		wp_enqueue_style( 'oe-rating-notice', plugins_url( '/assets/css/notice.min.css', __FILE__ ) );
	}

	/**
	 * Return customizer panels
	 *
	 * @since 1.0.8
	 */
	private static function get_panels() {

		$panels = array(
			'oe_general_panel'        => array(
				'label' => esc_html__( 'General Panel', 'ocean-extra' ),
			),
			'oe_typography_panel'     => array(
				'label' => esc_html__( 'Typography Panel', 'ocean-extra' ),
			),
			'oe_topbar_panel'         => array(
				'label' => esc_html__( 'Top Bar Panel', 'ocean-extra' ),
			),
			'oe_header_panel'         => array(
				'label' => esc_html__( 'Header Panel', 'ocean-extra' ),
			),
			'oe_blog_panel'           => array(
				'label' => esc_html__( 'Blog Panel', 'ocean-extra' ),
			),
			'oe_sidebar_panel'        => array(
				'label' => esc_html__( 'Sidebar Panel', 'ocean-extra' ),
			),
			'oe_footer_widgets_panel' => array(
				'label' => esc_html__( 'Footer Widgets Panel', 'ocean-extra' ),
			),
			'oe_footer_bottom_panel'  => array(
				'label' => esc_html__( 'Footer Bottom Panel', 'ocean-extra' ),
			),
			'oe_custom_code_panel'    => array(
				'label' => esc_html__( 'Custom CSS/JS Panel', 'ocean-extra' ),
			),
		);

		// Apply filters and return
		return apply_filters( 'oe_theme_panels', $panels );
	}

	/**
	 * Return customizer options
	 *
	 * @since 1.0.8
	 */
	private static function get_options() {

		$options = array(
			'custom_logo'            => array(
				'label' => esc_html__( 'Upload your logo', 'ocean-extra' ),
				'desc'  => esc_html__( 'Add your own logo and retina logo used for retina screens.', 'ocean-extra' ),
			),
			'site_icon'              => array(
				'label' => esc_html__( 'Add your favicon', 'ocean-extra' ),
				'desc'  => esc_html__( 'The favicon is used as a browser and app icon for your website.', 'ocean-extra' ),
			),
			'ocean_primary_color'    => array(
				'label' => esc_html__( 'Choose your primary color', 'ocean-extra' ),
				'desc'  => esc_html__( 'Replace the default primary and hover color by your own colors.', 'ocean-extra' ),
			),
			'ocean_typography_panel' => array(
				'label' => esc_html__( 'Choose your typography', 'ocean-extra' ),
				'desc'  => esc_html__( 'Choose your own typography for any parts of your website.', 'ocean-extra' ),
				'panel' => true,
			),
			'ocean_top_bar'          => array(
				'label' => esc_html__( 'Top bar options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Enable/Disable the top bar, add your own paddings and colors.', 'ocean-extra' ),
			),
			'ocean_header_style'     => array(
				'label' => esc_html__( 'Header options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Choose the style, the height and the colors for your site header.', 'ocean-extra' ),
			),
			'ocean_footer_widgets'   => array(
				'label' => esc_html__( 'Footer widgets options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Choose the columns number, paddings and colors for the footer widgets.', 'ocean-extra' ),
			),
			'ocean_footer_bottom'    => array(
				'label' => esc_html__( 'Footer bottom options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Add your copyright, paddings and colors for the footer bottom.', 'ocean-extra' ),
			),
		);

		// Apply filters and return
		return apply_filters( 'oe_customizer_options', $options );
	}

	/**
	 * Get settings.
	 *
	 * @since 1.2.2
	 */
	public static function get_setting( $option = '' ) {

		$defaults = self::get_default_settings();

		$settings = wp_parse_args( get_option( 'oe_panels_settings', $defaults ), $defaults );

		return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
	}

	/**
	 * Get default settings value.
	 *
	 * @since 1.2.2
	 */
	public static function get_default_settings() {

		// Get panels array
		$panels = self::get_panels();

		// Add array
		$default = array();

		foreach ( $panels as $key => $val ) {
			$default[ $key ] = 1;
		}

		// Return
		return apply_filters( 'oe_default_panels', $default );
	}


}

new Ocean_Extra_Theme_Panel();
