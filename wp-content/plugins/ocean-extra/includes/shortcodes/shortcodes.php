<?php
/**
 * All shortcodes
 */

/**
 * Logo shortcode for the Custom Header style
 *
 * @since 1.1.1
 */
if ( ! function_exists( 'oceanwp_logo_shortcode' ) ) {

	function oceanwp_logo_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'position' => 'left',
			),
			$atts
		);

		$position = $settings['position'];

		// Add classes
		$classes   = array( 'custom-header-logo', 'clr' );
		$classes[] = $position;
		$classes   = implode( ' ', $classes );

		ob_start();
		?>

		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php get_template_part( 'partials/header/logo' ); ?>
		</div>

		<?php
		return ob_get_clean();
	}
}
add_shortcode( 'oceanwp_logo', 'oceanwp_logo_shortcode' );

/**
 * Nav menu shortcode for the Custom Header style
 *
 * @since 1.1.1
 */
if ( ! function_exists( 'oceanwp_nav_shortcode' ) ) {

	function oceanwp_nav_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'position' => 'left',
			),
			$atts
		);

		$position = $settings['position'];

		// Add classes
		$classes   = array( 'custom-header-nav', 'clr' );
		$classes[] = $position;
		$classes   = implode( ' ', $classes );

		ob_start();
		?>

		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php
			// Navigation
			get_template_part( 'partials/header/nav' );

			// Mobile nav
			get_template_part( 'partials/mobile/mobile-icon' );
			?>
		</div>

		<?php
		return ob_get_clean();
	}
}
add_shortcode( 'oceanwp_nav', 'oceanwp_nav_shortcode' );

/**
 * Dynamic date shortcode
 *
 * @since 1.1.1
 */
if ( ! function_exists( 'oceanwp_date_shortcode' ) ) {

	function oceanwp_date_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'year' => '',
			),
			$atts
		);

		$year = $settings['year'];

		// Var
		$date = '';

		if ( '' != $year ) {
			$date .= $year . ' - ';
		}

		$date .= date( 'Y' );

		return esc_attr( $date );

	}
}
add_shortcode( 'oceanwp_date', 'oceanwp_date_shortcode' );

/**
 * Search form shortcode
 *
 * @since 1.1.9
 */
if ( ! function_exists( 'oceanwp_search_shortcode' ) ) {

	function oceanwp_search_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'width'       => '',
				'height'      => '',
				'placeholder' => esc_html__( 'Search', 'ocean-extra' ),
				'btn_icon'    => 'icon-magnifier',
				'post_type'   => 'any',
			),
			$atts
		);

		$width       = $settings['width'];
		$height      = $settings['height'];
		$placeholder = $settings['placeholder'];
		$btn_icon    = $settings['btn_icon'];
		$post_type   = $settings['post_type'];

		// Styles.
		$style = array();
		if ( ! empty( $width ) ) {
			$style[] = 'width: ' . intval( $width ) . 'px;';
		}
		if ( ! empty( $height ) ) {
			$style[] = 'height: ' . intval( $height ) . 'px;min-height: ' . intval( $height ) . 'px;';
		}
		$style = implode( '', $style );

		if ( $style ) {
			$style = wp_kses( $style, array() );
			$style = ' style="' . esc_attr( $style ) . '"';
		}

		$html      = '<form aria-label="' . oe_lang_strings( 'oe-string-search-form-label', false ) . '" role="search" method="get" class="oceanwp-searchform" id="searchform" action="' . esc_url( home_url( '/' ) ) . '"' . $style . '>';
			$html .= '<input aria-label="' . oe_lang_strings( 'oe-string-search-field', false ) . '" type="text" class="field" name="s" id="s" placeholder="' . esc_attr( $placeholder ) . '">';
		if ( 'any' != $post_type ) {
			$html .= '<input type="hidden" name="post_type" value="' . esc_attr( $post_type ) . '">';
		}
			$html .= '<button aria-label="' . oe_lang_strings( 'oe_string_search_submit', false ) . '" type="submit" class="search-submit" value=""><i class="' . esc_attr( $btn_icon ) . '" aria-hidden="true"></i></button>';
		$html     .= '</form>';

		// Return.
		return $html;

	}
}
add_shortcode( 'oceanwp_search', 'oceanwp_search_shortcode' );

/**
 * Site url shortcode
 *
 * @since 1.1.9
 */
if ( ! function_exists( 'oceanwp_site_url_shortcode' ) ) {

	function oceanwp_site_url_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'target' => 'self',
			),
			$atts
		);

		$target = $settings['target'];

		$html = '<a href="' . esc_url( home_url( '/' ) ) . '" target="_' . esc_attr( $target ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';

		// Return
		return $html;

	}
}
add_shortcode( 'oceanwp_site_url', 'oceanwp_site_url_shortcode' );

/**
 * Login/logout link
 *
 * @since 1.1.9
 */
if ( ! function_exists( 'oceanwp_login_shortcode' ) ) {

	function oceanwp_login_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'custom_url'      => '',
				'login_text'      => esc_html__( 'Login', 'ocean-extra' ),
				'logout_text'     => esc_html__( 'Log Out', 'ocean-extra' ),
				'target'          => 'self',
				'logout_redirect' => '',
			),
			$atts
		);

		$custom_url      = $settings['custom_url'];
		$login_text      = $settings['login_text'];
		$logout_text     = $settings['logout_text'];
		$target          = $settings['target'];
		$logout_redirect = $settings['logout_redirect'];

		// Custom login url.
		if ( ! empty( $custom_url ) ) {
			$login_url = $custom_url;
		} else {
			$login_url = wp_login_url();
		}

		// Logout redirect.
		if ( ! empty( $logout_redirect ) ) {
			$current = get_permalink();
			if ( 'current' == $logout_redirect
				&& $current ) {
				$logout_redirect = $current;
			} else {
				$logout_redirect = $logout_redirect;
			}
		} else {
			$logout_redirect = home_url( '/' );
		}

		// Logout link.
		$logout_url = wp_logout_url( $logout_redirect );

		// Logged in link.
		if ( is_user_logged_in() ) {
			return '<a href="' . esc_url( $logout_url ) . '" class="oceanwp-logout">' . esc_html( $logout_text ) . '</a>';
		}

		// Logged out link.
		else {
			return '<a href="' . esc_url( $login_url ) . '" class="oceanwp-login" target="_' . esc_attr( $target ) . '">' . esc_html( $login_text ) . '</a>';
		}

	}
}
add_shortcode( 'oceanwp_login', 'oceanwp_login_shortcode' );

/**
 * Current User Shortcode
 *
 * @since 1.2.1
 */
if ( ! function_exists( 'oceanwp_current_user_shortcode' ) ) {

	function oceanwp_current_user_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'text'    => esc_html__( 'Welcome back', 'ocean-extra' ),
				'display' => 'display_name',
			),
			$atts
		);

		$text    = $settings['text'];
		$display = $settings['display'];

		// Get current user
		$current_user = wp_get_current_user();

		// Text
		if ( ! empty( $text ) ) {
			$text = $text . ' ';
		}

		// If logged in
		if ( is_user_logged_in() ) {
			return esc_html( $text ) . $current_user->$display;
		}

		// Return if not logged in
		else {
			return;
		}

	}
}
add_shortcode( 'oceanwp_current_user', 'oceanwp_current_user_shortcode' );

/**
 * WooCommerce fragments
 *
 * @since 1.2.2
 */
if ( ! function_exists( 'oceanwp_woo_fragments' ) ) {

	function oceanwp_woo_fragments( $fragments ) {
		$fragments['.wcmenucart-shortcode .wcmenucart-total'] = '<span class="wcmenucart-total">' . WC()->cart->get_total() . '</span>';
		$fragments['.wcmenucart-shortcode .wcmenucart-count'] = '<span class="wcmenucart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
		$fragments['.oceanwp-woo-total']                      = '<span class="oceanwp-woo-total">' . WC()->cart->get_total() . '</span>';
		$fragments['.oceanwp-woo-cart-count']                 = '<span class="oceanwp-woo-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'oceanwp_woo_fragments', 10, 1 );

/**
 * WooCommerce cart icon
 *
 * @since 1.4.4
 */
if ( ! function_exists( 'oceanwp_woo_cart_icon_shortcode' ) ) {

	function oceanwp_woo_cart_icon_shortcode( $atts ) {

		// Return if WooCommerce is not enabled or if admin to avoid error
		if ( ! class_exists( 'WooCommerce' )
			|| is_admin() ) {
			return;
		}

		// Return if is in the Elementor edit mode, to avoid error
		if ( class_exists( 'Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return esc_html__( 'This shortcode only works in front end', 'ocean-extra' );
		}

		$settings = shortcode_atts(
			array(
				'class'             => '',
				'style'             => 'drop_down',
				'custom_link'       => '',
				'total'             => false,
				'cart_style'        => 'compact',
				'hide_if_empty'     => false,
				'color'             => '',
				'hover_color'       => '',
				'count_color'       => '',
				'count_hover_color' => '',
			),
			$atts
		);

		$class             = $settings['class'];
		$style             = $settings['style'];
		$custom_link       = $settings['custom_link'];
		$total             = $settings['total'];
		$cart_style        = $settings['cart_style'];
		$hide_if_empty     = $settings['hide_if_empty'];
		$color             = $settings['color'];
		$hover_color       = $settings['hover_color'];
		$count_color       = $settings['count_color'];
		$count_hover_color = $settings['count_hover_color'];

		// Return items if "hide if empty cart" is checked (for mobile)
		if ( true == $hide_if_empty
			&& ! WC()->cart->cart_contents_count > 0 ) {
			return;
		}

		// Toggle class
		$toggle_class = 'toggle-cart-widget';

		// Define classes to add to li element
		$classes = array( 'woo-menu-icon', 'bag-style', 'woo-cart-shortcode' );

		// Add style class
		$classes[] = 'wcmenucart-toggle-' . $style;

		// Cart style
		if ( 'compact' != $cart_style ) {
			$classes[] = $cart_style;
		}

		// Prevent clicking on cart and checkout
		if ( 'custom_link' != $style && ( is_cart() || is_checkout() ) ) {
			$classes[] = 'nav-no-click';
		}

		// Add toggle class
		else {
			$classes[] = $toggle_class;
		}

		// If custom class
		if ( ! empty( $class ) ) {
			$classes[] = $class;
		}

		// Turn classes into string
		$classes = implode( ' ', $classes );

		// URL
		if ( 'custom_link' == $style && $custom_link ) {
			$url = esc_url( $custom_link );
		} else {
			$cart_id = wc_get_page_id( 'cart' );
			if ( function_exists( 'icl_object_id' ) ) {
				$cart_id = icl_object_id( $cart_id, 'page' );
			}
			$url = get_permalink( $cart_id );
		}

		// Style
		if ( ! empty( $color )
			|| ! empty( $hover_color )
			|| ! empty( $count_color )
			|| ! empty( $count_hover_color ) ) {

			// Vars
			$css    = '';
			$output = '';

			if ( ! empty( $color ) ) {
				$css .= '.woo-cart-shortcode .wcmenucart-cart-icon .wcmenucart-count {color:' . esc_attr( $color ) . '; border-color:' . esc_attr( $color ) . ';}';
				$css .= '.woo-cart-shortcode .wcmenucart-cart-icon .wcmenucart-count:after {border-color:' . esc_attr( $color ) . ';}';
			}

			if ( ! empty( $hover_color ) ) {
				$css .= '.woo-cart-shortcode.bag-style:hover .wcmenucart-cart-icon .wcmenucart-count, .show-cart .wcmenucart-cart-icon .wcmenucart-count {background-color: ' . esc_attr( $hover_color ) . '; border-color:' . esc_attr( $hover_color ) . ';}';
				$css .= '.woo-cart-shortcode.bag-style:hover .wcmenucart-cart-icon .wcmenucart-count:after, .show-cart .wcmenucart-cart-icon .wcmenucart-count:after {border-color:' . esc_attr( $hover_color ) . ';}';
			}

			if ( ! empty( $count_color ) ) {
				$css .= '.woo-cart-shortcode .wcmenucart-cart-icon .wcmenucart-count {color:' . esc_attr( $count_color ) . ';}';
			}

			if ( ! empty( $count_hover_color ) ) {
				$css .= '.woo-cart-shortcode.bag-style:hover .wcmenucart-cart-icon .wcmenucart-count, .show-cart .wcmenucart-cart-icon .wcmenucart-count {color:' . esc_attr( $count_hover_color ) . ';}';
			}

			// Add style
			if ( ! empty( $css ) ) {
				echo "<style type=\"text/css\">\n" . wp_strip_all_tags( oceanwp_minify_css( $css ) ) . "\n</style>";
			}
		}

		ob_start();
		?>

		<div class="<?php echo esc_attr( $classes ); ?>">
			<a href="<?php echo esc_url( $url ); ?>" class="wcmenucart-shortcode">
				<?php
				if ( true == $total ) {
					?>
					<span class="wcmenucart-total"><?php WC()->cart->get_total(); ?></span>
				<?php } ?>
				<span class="wcmenucart-cart-icon">
					<span class="wcmenucart-count"><?php WC()->cart->get_cart_contents_count(); ?></span>
				</span>
			</a>
			<?php
			if ( 'drop_down' == $style
				&& ! is_cart()
				&& ! is_checkout() ) {
				?>
				<div class="current-shop-items-dropdown owp-mini-cart clr">
					<div class="current-shop-items-inner clr">
						<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
					</div>
				</div>
			<?php } ?>
		</div>

		<?php
		return ob_get_clean();

	}
}
add_shortcode( 'oceanwp_woo_cart', 'oceanwp_woo_cart_icon_shortcode' );

/**
 * WooCommerce total cart
 *
 * @since 1.2.2
 */
if ( ! function_exists( 'oceanwp_woo_total_cart_shortcode' ) ) {

	function oceanwp_woo_total_cart_shortcode() {

		// Return if WooCommerce is not enabled or if admin to avoid error
		if ( ! class_exists( 'WooCommerce' )
			|| is_admin() ) {
			return;
		}

		// Return if is in the Elementor edit mode, to avoid error
		if ( class_exists( 'Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return esc_html__( 'This shortcode only works in front end', 'ocean-extra' );
		}

		$html  = '<span class="oceanwp-woo-total">';
		$html .= WC()->cart->get_total();
		$html .= '</span>';

		return $html;

	}
}
add_shortcode( 'oceanwp_woo_total_cart', 'oceanwp_woo_total_cart_shortcode' );

/**
 * WooCommerce items cart
 *
 * @since 1.2.2
 */
if ( ! function_exists( 'oceanwp_woo_cart_items_shortcode' ) ) {

	function oceanwp_woo_cart_items_shortcode() {

		// Return if WooCommerce is not enabled or if admin to avoid error
		if ( ! class_exists( 'WooCommerce' )
			|| is_admin() ) {
			return;
		}

		// Return if is in the Elementor edit mode, to avoid error
		if ( class_exists( 'Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return esc_html__( 'This shortcode only works in front end', 'ocean-extra' );
		}

		$html  = '<span class="oceanwp-woo-cart-count">';
		$html .= WC()->cart->get_cart_contents_count();
		$html .= '</span>';

		return $html;

	}
}
add_shortcode( 'oceanwp_woo_cart_items', 'oceanwp_woo_cart_items_shortcode' );

/**
 * WooCommerce free shipping left
 *
 * @since 1.2.2
 */
if ( ! function_exists( 'oceanwp_woo_free_shipping_left' ) ) {

	function oceanwp_woo_free_shipping_left( $content, $content_reached, $multiply_by = 1 ) {

		// Return if WooCommerce is not enabled
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Return if is in the Elementor edit mode, to avoid error
		if ( class_exists( 'Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		if ( empty( $content ) ) {
			$content = esc_html__( 'Buy for %left_to_free% more and get free shipping', 'ocean-extra' );
		}

		if ( empty( $content_reached ) ) {
			$content_reached = esc_html__( 'You have Free delivery!', 'ocean-extra' );
		}

		$min_free_shipping_amount = 0;

		$legacy_free_shipping = new WC_Shipping_Legacy_Free_Shipping();
		if ( 'yes' === $legacy_free_shipping->enabled ) {
			if ( in_array( $legacy_free_shipping->requires, array( 'min_amount', 'either', 'both' ) ) ) {
				$min_free_shipping_amount = $legacy_free_shipping->min_amount;
			}
		}
		if ( 0 == $min_free_shipping_amount ) {
			if ( function_exists( 'WC' ) && ( $wc_shipping = WC()->shipping ) && ( $wc_cart = WC()->cart ) ) {
				if ( $wc_shipping->enabled ) {
					if ( $packages = $wc_cart->get_shipping_packages() ) {
						$shipping_methods = $wc_shipping->load_shipping_methods( $packages[0] );
						foreach ( $shipping_methods as $shipping_method ) {
							if ( 'yes' === $shipping_method->enabled && 0 != $shipping_method->instance_id ) {
								if ( 'WC_Shipping_Free_Shipping' === get_class( $shipping_method ) ) {
									if ( in_array( $shipping_method->requires, array( 'min_amount', 'either', 'both' ) ) ) {
										$min_free_shipping_amount = $shipping_method->min_amount;
										break;
									}
								}
							}
						}
					}
				}
			}
		}

		if ( 0 != $min_free_shipping_amount ) {
			if ( isset( WC()->cart->cart_contents_total ) ) {
				$total = ( WC()->cart->prices_include_tax ) ? ( WC()->cart->cart_contents_total + WC()->cart->get_cart_contents_tax() ) : WC()->cart->cart_contents_total;
				if ( $total >= $min_free_shipping_amount ) {
					return do_shortcode( wp_kses_post( $content_reached ) );
				} else {
					$content = str_replace( '%left_to_free%', '<span class="oceanwp-woo-left-to-free">' . wc_price( ( $min_free_shipping_amount - $total ) * $multiply_by ) . '</span>', wp_kses_post( $content ) );
					$content = str_replace( '%free_shipping_min_amount%', '<span class="oceanwp-woo-left-to-free">' . wc_price( ( $min_free_shipping_amount ) * $multiply_by ) . '</span>', wp_kses_post( $content ) );
					return $content;
				}
			}
		}

	}
}

if ( ! function_exists( 'oceanwp_woo_free_shipping_left_shortcode' ) ) {

	function oceanwp_woo_free_shipping_left_shortcode( $atts, $content ) {

		// Return if WooCommerce is not enabled
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Call the script
		wp_enqueue_script( 'owp-free-shipping' );

		// Initiation data on data attr on span
		$content_data    = '';
		$content_reached = '';
		if ( ! empty( $atts ) ) {
			if ( isset( $atts['content'] ) ) {
				$content_data = $atts['content'];
			}
			if ( isset( $atts['content_reached'] ) ) {
				$content_reached = $atts['content_reached'];
			}
		}

		$x = str_replace( '%', '+', $content_data );

		$settings = shortcode_atts(
			array(
				'content'         => esc_html__( 'Buy for %left_to_free% more and get free shipping', 'ocean-extra' ),
				'content_reached' => esc_html__( 'You have Free delivery!', 'ocean-extra' ),
				'multiply_by'     => 1,
			),
			$atts
		);

		$content         = $settings['content'];
		$content_reached = $settings['content_reached'];
		$multiply_by     = $settings['multiply_by'];

		return oceanwp_woo_free_shipping_left( "<span class='oceanwp-woo-free-shipping' data-content='" . esc_attr($x ) . "' data-reach='" . esc_attr( $content_reached ) . "'>" . wp_kses_post( $content ) . '</span>', '<span class="oceanwp-woo-free-shipping">' . wp_kses_post( $content_reached ) . '</span>', esc_attr( $multiply_by ) );

	}
}
add_shortcode( 'oceanwp_woo_free_shipping_left', 'oceanwp_woo_free_shipping_left_shortcode' );

/**
 * Ajax replay the refresh fragemnt
 *
 * @since 1.4.24
 */
if ( ! function_exists( 'update_oceanwp_woo_free_shipping_left_shortcode' ) ) {

	function update_oceanwp_woo_free_shipping_left_shortcode() {
		$atts = array();

		if ( ( isset( $_POST['content'] )
			&& ( $_POST['content'] !== '' ) )
				|| ( isset( $_POST['content_rech_data'] )
					&& ( $_POST['content_rech_data'] !== '' ) ) ) {

			$atts['content_reached'] = $_POST['content_rech_data'];
			$content                 = str_replace( '+', '%', $_POST['content'] );
			$atts['content']         = $content;
			$returnShortCodeValue    = oceanwp_woo_free_shipping_left_shortcode( $atts, '' );
			wp_send_json( $returnShortCodeValue );

		} else {

			$returnShortCodeValue = oceanwp_woo_free_shipping_left_shortcode( $atts, '' );
			wp_send_json( $returnShortCodeValue );

		}
	}
}
add_action( 'wp_ajax_update_oceanwp_woo_free_shipping_left_shortcode', 'update_oceanwp_woo_free_shipping_left_shortcode' );
add_action( 'wp_ajax_nopriv_update_oceanwp_woo_free_shipping_left_shortcode', 'update_oceanwp_woo_free_shipping_left_shortcode' );

/**
 * Add js code
 *
 * @since 1.4.24
 */
function oceanwp_woo_free_shipping_left_script() {
	wp_register_script( 'owp-free-shipping', plugins_url( '/js/shortcode.min.js', __FILE__ ), false, true );
}
add_action( 'wp_enqueue_scripts', 'oceanwp_woo_free_shipping_left_script' );

/**
 * Breadcrumb shortcode
 *
 * @since 1.3.3
 */
if ( ! function_exists( 'oceanwp_breadcrumb_shortcode' ) ) {

	function oceanwp_breadcrumb_shortcode( $atts ) {

		// Return if is in the Elementor edit mode, to avoid error
		if ( class_exists( 'Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return esc_html__( 'This shortcode only works in front end', 'ocean-extra' );
		}

		// Return if is in the admin, to avoid conflict with Yoast SEO
		if ( is_admin() ) {
			return;
		}

		// Return if OceanWP_Breadcrumb_Trail doesn't exist
		if ( ! class_exists( 'OceanWP_Breadcrumb_Trail' ) ) {
			return;
		}

		$settings = shortcode_atts(
			array(
				'class'       => '',
				'color'       => '',
				'hover_color' => '',
			),
			$atts
		);

		$class       = $settings['class'];
		$color       = $settings['color'];
		$hover_color = $settings['hover_color'];

		$args = '';

		// Add a space for the beginning of the class attr
		if ( ! empty( $class ) ) {
			$class = ' ' . $class;
		}

		// Style
		if ( ! empty( $color ) || ! empty( $hover_color ) ) {

			// Vars
			$css    = '';
			$output = '';

			if ( ! empty( $color ) ) {
				$css .= '.oceanwp-breadcrumb .site-breadcrumbs, .oceanwp-breadcrumb .site-breadcrumbs a {color:' . esc_attr( $color ) . ';}';
			}

			if ( ! empty( $hover_color ) ) {
				$css .= '.oceanwp-breadcrumb .site-breadcrumbs a:hover {color:' . esc_attr( $hover_color ) . ';}';
			}

			// Add style
			if ( ! empty( $css ) ) {
				echo "<style type=\"text/css\">\n" . wp_strip_all_tags( oceanwp_minify_css( $css ) ) . "\n</style>";
			}
		}

		// Yoast breadcrumbs
		if ( function_exists( 'yoast_breadcrumb' ) && current_theme_supports( 'yoast-seo-breadcrumbs' ) ) {
			$classes = 'site-breadcrumbs clr';
			if ( $breadcrumbs_position = get_theme_mod( 'ocean_breadcrumbs_position' ) ) {
				$classes .= ' position-' . esc_attr( $breadcrumbs_position );
			}
			return yoast_breadcrumb( '<nav class="' . esc_attr( $classes ) . '">', '</nav>' );
		}

		$breadcrumb = apply_filters( 'breadcrumb_trail_object', null, $args );

		if ( ! is_object( $breadcrumb ) ) {
			$breadcrumb = new OceanWP_Breadcrumb_Trail( $args );
		}

		return '<span class="oceanwp-breadcrumb' . esc_attr( $class ) . '">' . $breadcrumb->get_trail() . '</span>';

	}
}
add_shortcode( 'oceanwp_breadcrumb', 'oceanwp_breadcrumb_shortcode' );

/**
 * Last Modified Date Shortcode
 *
 * @since 1.7.1
 */
if ( ! function_exists( 'oceanwp_last_modified_shortcode' ) ) {
	function oceanwp_last_modified_shortcode( $atts ) {
		$settings = shortcode_atts(
			array(
				'olm_text'        => esc_html__( 'Last Updated on:', 'ocean-extra' ),
				'olm_date_format' => '',
			),
			$atts
		);

		$olm_text        = $settings['olm_text'];
		$olm_date_format = $settings['olm_date_format'];

		if ( ! empty( $olm_date_format ) ) {
			$olm_date = get_the_modified_date( $olm_date_format );
		} else {
			$olm_date = get_the_modified_date( 'F j, Y' );
		}

		$olm_shortcode = '<p class="ocean-last-modified">' . esc_html( $olm_text . ' ' . $olm_date ) . '</p>';

		// Return.
		return $olm_shortcode;
	}
}
add_shortcode( 'oceanwp_last_modified', 'oceanwp_last_modified_shortcode' );

/**
 * SVG icon shortcode
 *
 * @param array $atts    An associative array of attributes.
 * @param obj   $content The enclosed content.
 *
 * @since 1.7.6
 */
if ( ! function_exists( 'oceanwp_svg_icon_shortcode' ) ) {

	function oceanwp_svg_icon_shortcode( $atts, $content = null ) {

		$owp_icon = '';

		// Extract attributes.
		$attr = shortcode_atts(
			array(
				'icon'        => 'Add an icon class',
				'class'       => '',
				'title'       => '',
				'desc'        => '',
				'area_hidden' => true,
				'fallback'    => false,
			),
			$atts
		);

		if ( function_exists( 'ocean_svg' ) ) {
			$owp_icon = ocean_svg( $attr['icon'], false, $attr['class'], $attr['title'], $attr['desc'], $attr['area_hidden'], $attr['fallback'] );
		}

		return $owp_icon;
	}
}
add_shortcode( 'oceanwp_icon', 'oceanwp_svg_icon_shortcode' );
