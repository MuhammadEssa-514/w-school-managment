<?php
/**
 * Social widget.
 *
 * @package OceanWP WordPress theme
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Social_Widget' ) ) {
	class Ocean_Extra_Social_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Declare social services array.
			$this->social_services_array = apply_filters( 'ocean_social_widget_profiles',
				array(
					'twitter' => array(
						'name' => 'Twitter',
						'url'  => '',
					),
					'facebook' => array(
						'name' => 'Facebook',
						'url'  => '',
					),
					'instagram' => array(
						'name' => 'Instagram',
						'url'  => '',
					),
					'linkedin' => array(
						'name' => 'LinkedIn',
						'url'  => '',
					),
					'pinterest' => array(
						'name' => 'Pinterest',
						'url'  => '',
					),
					'yelp' => array(
						'name' => 'Yelp',
						'url'  => '',
					),
					'dribbble' => array(
						'name' => 'Dribbble',
						'url'  => '',
					),
					'flickr' => array(
						'name' => 'Flickr',
						'url'  => '',
					),
					'vk' => array(
						'name' => 'VK',
						'url'  => '',
					),
					'github' => array(
						'name' => 'GitHub',
						'url'  => '',
					),
					'tumblr' => array(
						'name' => 'Tumblr',
						'url'  => '',
					),
					'skype' => array(
						'name' => 'Skype',
						'url'  => '',
					),
					'trello' => array(
						'name' => 'Trello',
						'url'  => '',
					),
					'foursquare' => array(
						'name' => 'Foursquare',
						'url'  => '',
					),
					'xing' => array(
						'name' => 'Xing',
						'url'  => '',
					),
					'vimeo' => array(
						'name' => 'Vimeo',
						'url'  => '',
					),
					'vine' => array(
						'name' => 'Vine',
						'url'  => '',
					),
					'youtube' => array(
						'name' => 'Youtube',
						'url'  => '',
					),
					'rss' => array(
						'name' => 'RSS',
						'url'  => '',
					),
					'telegram'       => array(
						'name'      => 'Telegram',
						'url'       => ''
					),
					'twitch'       => array(
						'name'      => 'Twitch',
						'url'       => ''
					),
					'line'       => array(
						'name'      => 'Line',
						'url'       => ''
					),
					'tripadvisor'       => array(
						'name'      => 'Tripadvisor',
						'url'       => ''
					),
					'medium'       => array(
						'name'      => 'Medium',
						'url'       => ''
					),
					'tiktok'       => array(
						'name'      => 'tiktok',
						'url'       => ''
					),

				)
			);

			// Start up widget.
			parent::__construct(
				'ocean_social',
				esc_html__( '&raquo; Social Icons', 'ocean-extra' ),
				array(
					'classname'   => 'widget-oceanwp-social social-widget',
					'description' => esc_html__( 'Display your social media icons.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
			);

			// Since 1.3.8.
			add_action( 'admin_head-widgets.php', array( $this, 'social_widget_style' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_footer-widgets.php', array( $this, 'print_scripts' ) );
		}

		/**
		 * Custom widget style
		 *
		 * @since 1.3.8
		 *
		 * @param string $hook_suffix
		 */
		public function social_widget_style() {
			?>

			<style>
				.oceanwp-social-widget-services-list { padding-top: 10px; }
				.oceanwp-social-widget-services-list li { cursor: move; background: #fafafa; padding: 10px; border: 1px solid #e5e5e5; margin-bottom: 10px; }
				.oceanwp-social-widget-services-list li p { margin: 0 }
				.oceanwp-social-widget-services-list li label { margin-bottom: 3px; display: block; color: #222; }
				.oceanwp-social-widget-services-list li label span.fa { margin-right: 10px }
				.oceanwp-social-widget-services-list li label span.fab { margin-right: 10px }
				.oceanwp-social-widget-services-list .placeholder { border: 1px dashed #e3e3e3 }
				.oceanwp-widget-select { width: 100% }
				.color-label { display: block; margin-bottom: 5px; }
			</style>
		<?php
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 1.3.8
		 *
		 * @param string $hook_suffix
		 */
		public function enqueue_scripts( $hook_suffix ) {
			if ( 'widgets.php' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'underscore' );
		}

		/**
		 * Print scripts.
		 *
		 * @since 1.3.8
		 */
		public function print_scripts() { ?>
			<script>
				( function( $ ){
					$(document).ajaxSuccess(function(e, xhr, settings) {
						var widget_id_base = 'ocean_social';
						if (typeof(settings.data) !== 'undefined' && typeof(settings.data.search) !== 'undefined') {
							if ( settings.data.search( 'action=save-widget' ) !== 'undefined' && typeof(settings.data.search( 'id_base=' + widget_id_base)) !== 'undefined' ) {
								oceanwpSortServices();
							}
						}
					} );

					function oceanwpSortServices() {
						$( '.oceanwp-social-widget-services-list' ).each( function() {
							var id = $(this).attr( 'id' );
							$( '#'+ id ).sortable( {
								placeholder : "placeholder",
								opacity     : 0.6
							} );
						} );
					}

					oceanwpSortServices();

					function initColorPicker( widget ) {
						widget.find( '.color-picker' ).wpColorPicker( {
							change: _.throttle( function() { // For Customizer.
								$(this).trigger( 'change' );
							}, 3000 )
						});
					}

					function onFormUpdate( event, widget ) {
						initColorPicker( widget );
					}

					$( document ).on( 'widget-added widget-updated', onFormUpdate );

					$( document ).ready( function() {
						$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
							initColorPicker( $( this ) );
						} );
					} );
				}( jQuery ) );
			</script>
		<?php
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 * @since 1.0.0
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			// Get social services.
			$social_services = isset( $instance['social_services'] ) ? $instance['social_services'] : '';

			// Return if no services defined.
			if ( ! $social_services ) {
				return;
			}

			// Define vars.
			$title          = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$style          = isset( $instance['style'] ) ? $instance['style'] : '';
			$transition     = isset( $instance['transition'] ) ? $instance['transition'] : '';
			$target         = isset( $instance['target'] ) ? $instance['target'] : '';
			$nofollow       = isset( $instance['nofollow'] ) ? $instance['nofollow'] : '';
			$size           = isset( $instance['size'] ) ? $instance['size'] : '';
			$font_size      = isset( $instance['font_size'] ) ? $instance['font_size'] : '';
			$border_radius  = isset( $instance['border_radius'] ) ? $instance['border_radius'] : '';

			// Sanitize vars.
			$size          = $size ? $size : '';
			$font_size     = $font_size ? $font_size : '';
			$border_radius = $border_radius ? $border_radius  : '';

			// Inline style
			$add_style     = '';
			if ( $size && 'simple' != $style ) {
				$add_style .= 'height:'. esc_attr( $size ) .';width:'. esc_attr( $size ) .';line-height:'. esc_attr( $size ) .';';
			}
			if ( $font_size ) {
				$add_style .= 'font-size:'. esc_attr( $font_size ) .';';
			}
			if ( $border_radius && 'simple' != $style ) {
				$add_style .= 'border-radius:'. esc_attr( $border_radius ) .';';
			}
			if ( $add_style && 'svg' !== oceanwp_theme_icon_class() ) {
				$add_style = ' style="' . esc_attr( $add_style ) . '"';
			}

			// Before widget hook.
			echo $args['before_widget'];

				// Display title.
				if ( $title ) {
					echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
				}

				// Determine link rel.
				$ocean_srt = '<span class="screen-reader-text">'. esc_html__( 'Opens in a new tab', 'ocean-extra' ) .'</span>';
				
				$results = ocean_link_rel( $ocean_srt, $nofollow, $target );

				$ocean_sr = $results[0];
				$link_rel = $results[1];

				// Display social icons.
				?>

				<ul class="oceanwp-social-icons <?php echo esc_attr( $transition ); ?> style-<?php echo esc_attr( $style ); ?>">
					<?php
					// Original Array.
					$social_services_array = $this->social_services_array;

					// Loop through each item in the array.
					foreach( $social_services as $key => $val ) {
						if (!array_key_exists($key, $social_services_array )) {
							continue;
						}

						$link     = ! empty( $social_services[$key]['url'] ) ? $social_services[$key]['url'] : null;
						$name     = $social_services_array[$key]['name'] ?? null;

						if ( $link ) {
							$key  = 'vimeo-square' === $key ? 'vimeo' : $key;
							$icon = 'youtube' === $key ? 'youtube' : $key;
							$icon = 'pinterest' === $key ? 'pinterest' : $icon;
							$icon = 'bloglovin' === $key ? 'heart' : $icon;
							$icon = 'vimeo' === $key ? 'vimeo' : $icon;

							if ( 'skype' === $key ) {
								$link = 'skype:'. esc_attr( $link ) .'?call';
								$target = 'self';
								if ( $nofollow === 'yes' ) {
									$link_rel = 'rel="nofollow"';
								} else if ( $nofollow === 'no' || $nofollow === '' ) {
									$link_rel = '';
								}
								$ocean_sr = '';
								$ocean_sr_skype = '<span class="screen-reader-text">'. esc_html__( 'Opens in your application', 'ocean-extra' ) .'</span>';
							} else {
								$link = esc_url( $link );
								$ocean_sr_skype = '';
							}

							echo '<li class="oceanwp-'. esc_attr( $key ) .'">';

								echo '<a href="'. $link .'" aria-label="'. esc_attr( $name ) .'" '. wp_kses_post( $add_style ) . ' target="_'. esc_attr( $target ) .'" '. $link_rel .'>';

									Ocean_Extra::oe_svg_icon( $icon );

								echo '</a>';

								// Display screen reader text.
								echo $ocean_sr;
								echo $ocean_sr_skype;

							echo '</li>';
						}
					}
					?>
				</ul>

				<?php $this->colors( $args, $instance ); ?>

			<?php
			// After widget hook
			echo $args['after_widget'];

		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 * @since 1.0.0
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			// Sanitize data.
			$instance = $old_instance;
			$instance['title']              = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : null;
			$instance['style']              = ! empty( $new_instance['style'] ) ? strip_tags( $new_instance['style'] ) : 'light';
			$instance['transition']         = ! empty( $new_instance['transition'] ) ? strip_tags( $new_instance['transition'] ) : 'rotate';
			$instance['target']             = ! empty( $new_instance['target'] ) ? strip_tags( $new_instance['target'] ) : 'blank';
			$instance['nofollow']           = ! empty( $new_instance['nofollow'] ) ? strip_tags( $new_instance['nofollow'] ) : '';
			$instance['size']               = ! empty( $new_instance['size'] ) ? strip_tags( $new_instance['size'] ) : '';
			$instance['border_radius']      = ! empty( $new_instance['border_radius'] ) ? strip_tags( $new_instance['border_radius'] ) : '';
			$instance['bg_color']           = ! empty( $new_instance['bg_color'] ) ? sanitize_hex_color( $new_instance['bg_color'] ) : '';
			$instance['bg_hover_color']     = ! empty( $new_instance['bg_hover_color'] ) ? sanitize_hex_color( $new_instance['bg_hover_color'] ) : '';
			$instance['color']              = ! empty( $new_instance['color'] ) ? sanitize_hex_color( $new_instance['color'] ) : '';
			$instance['color_hover']        = ! empty( $new_instance['color_hover'] ) ? sanitize_hex_color( $new_instance['color_hover'] ) : '';
			$instance['border_color']       = ! empty( $new_instance['border_color'] ) ? sanitize_hex_color( $new_instance['border_color'] ) : '';
			$instance['border_hover_color'] = ! empty( $new_instance['border_hover_color'] ) ? sanitize_hex_color( $new_instance['border_hover_color'] ) : '';
			$instance['font_size']          = ! empty( $new_instance['font_size'] ) ? strip_tags( $new_instance['font_size'] ) : '';
			$instance['social_services']   = $new_instance['social_services'];
			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 * @since 1.0.0
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

			$instance = wp_parse_args( ( array ) $instance,
				array(
					'title'              => esc_attr__( 'Follow Us', 'ocean-extra' ),
					'style'              => esc_html__( 'Light', 'ocean-extra' ),
					'transition'         => esc_html__( 'Rotate', 'ocean-extra' ),
					'font_size'          => '',
					'border_radius'      => '',
					'bg_color'           => '',
					'bg_hover_color'     => '',
					'color'              => '',
					'color_hover'        => '',
					'border_color'       => '',
					'border_hover_color' => '',
					'target'             => 'blank',
					'nofollow'           => esc_html__( 'No', 'ocean-extra' ),
					'size'               => '',
					'social_services'    => $this->social_services_array,
				)
			);
			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ocean-extra' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_html_e( 'Style:', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>">
					<option value="light" <?php selected( $instance['style'], 'light' ) ?>><?php esc_html_e( 'Light', 'ocean-extra' ); ?></option>
					<option value="dark" <?php selected( $instance['style'], 'dark' ) ?>><?php esc_html_e( 'Dark', 'ocean-extra' ); ?></option>
					<option value="colored" <?php selected( $instance['style'], 'colored' ) ?>><?php esc_html_e( 'Colored', 'ocean-extra' ); ?></option>
					<option value="simple" <?php selected( $instance['style'], 'simple' ) ?>><?php esc_html_e( 'Simple', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'transition' ) ); ?>"><?php esc_html_e( 'Transition:', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'transition' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'transition' ) ); ?>">
					<option value="no-transition" <?php selected( $instance['transition'], 'no-transition' ) ?>><?php esc_html_e( 'None', 'ocean-extra' ); ?></option>
					<option value="float" <?php selected( $instance['transition'], 'float' ) ?>><?php esc_html_e( 'Float', 'ocean-extra' ); ?></option>
					<option value="rotate" <?php selected( $instance['transition'], 'rotate' ) ?>><?php esc_html_e( 'Rotate', 'ocean-extra' ); ?></option>
					<option value="zoomout" <?php selected( $instance['transition'], 'zoomout' ) ?>><?php esc_html_e( 'Zoom Out', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Link Target', 'ocean-extra' ); ?>:</label>
				<select class="oceanwp-widget-select" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>">
					<option value="blank" <?php selected( $instance['target'], 'blank' ) ?>><?php esc_html_e( 'Blank', 'ocean-extra' ); ?></option>
					<option value="self" <?php selected( $instance['target'], 'self' ) ?>><?php esc_html_e( 'Self', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'nofollow' ) ); ?>"><?php esc_html_e( 'Add Nofollow Link Rel:', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'nofollow' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'nofollow' ) ); ?>">
					<option value="no" <?php selected( $instance['nofollow'], 'no' ) ?>><?php esc_html_e( 'No', 'ocean-extra' ); ?></option>
					<option value="yes" <?php selected( $instance['nofollow'], 'yes' ) ?>><?php esc_html_e( 'Yes', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_html_e( 'Dimensions', 'ocean-extra' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['size'] ); ?>" />
				<small><?php esc_html_e( 'Example:', 'ocean-extra' ); ?> 40px</small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'font_size' ) ); ?>"><?php esc_html_e( 'Font Size', 'ocean-extra' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'font_size' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['font_size'] ); ?>" />
				<small><?php esc_html_e( 'Example:', 'ocean-extra' ); ?> 18px</small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>"><?php esc_html_e( 'Border Radius', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_radius' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['border_radius'] ); ?>" />
				<small><?php esc_html_e( 'Example:', 'ocean-extra' ); ?> 4px</small>
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>"><?php esc_html_e( 'Background Color', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['bg_color'] ); ?>" />
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'bg_hover_color' ) ); ?>"><?php esc_html_e( 'Background Color Hover', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'bg_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_hover_color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['bg_hover_color'] ); ?>" />
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"><?php esc_html_e( 'Color', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['color'] ); ?>" />
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'color_hover' ) ); ?>"><?php esc_html_e( 'Color Hover', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'color_hover' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'color_hover' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['color_hover'] ); ?>" />
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>"><?php esc_html_e( 'Border Color', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['border_color'] ); ?>" />
			</p>

			<p>
				<label class="color-label" for="<?php echo esc_attr( $this->get_field_id( 'border_hover_color' ) ); ?>"><?php esc_html_e( 'Border Color Hover', 'ocean-extra' ); ?></label>
				<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'border_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_hover_color' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['border_hover_color'] ); ?>" />
			</p>

			<?php
			$field_id_services   = $this->get_field_id( 'social_services' );
			$field_name_services = $this->get_field_name( 'social_services' );
			?>

			<h3 style="margin-top:20px;margin-bottom:0;"><?php esc_html_e( 'Social Links','ocean-extra' ); ?></h3>
			<ul id="<?php echo esc_attr( $field_id_services ); ?>" class="oceanwp-social-widget-services-list">
				<input type="hidden" id="<?php echo esc_attr( $field_name_services ); ?>" value="<?php echo esc_attr( $field_name_services ); ?>">
				<input type="hidden" id="<?php echo esc_attr( wp_create_nonce( 'oceanwp_fontawesome_social_widget_nonce' ) ); ?>">
				<?php
				// Social array.
				$social_services_array = $this->social_services_array;

				// Get current services display.
				$display_services = isset ( $instance['social_services'] ) ? $instance['social_services']: '';

				// Loop through social services to display inputs.
				foreach( $display_services as $key => $val ) {
					$url  = ! empty( $display_services[$key]['url'] ) ? $display_services[$key]['url'] : null;
					$name = $social_services_array[$key]['name'];
					?>

					<li id="<?php echo esc_attr( $field_id_services ); ?>_0<?php echo esc_attr( $key ); ?>">
						<p>
							<label for="<?php echo esc_attr( $field_id_services ); ?>-<?php echo esc_attr( $key ); ?>-name"><?php echo esc_attr( strip_tags( $name ) ); ?>:</label>
							<input type="hidden" id="<?php echo esc_attr( $field_id_services ); ?>-<?php echo esc_attr( $key ); ?>-url" name="<?php echo esc_attr( $field_name_services .'['.$key.'][name]' ); ?>" value="<?php echo esc_attr( $name ); ?>">
							<input type="text" class="widefat" id="<?php echo esc_attr( $field_id_services ); ?>-<?php echo esc_attr( $key ); ?>-url" name="<?php echo esc_attr( $field_name_services .'['.$key.'][url]' ); ?>" value="<?php echo esc_attr( $url ); ?>" />
						</p>
					</li>
				<?php } ?>
			</ul>

		<?php

		}

		/**
		 * Colors
		 *
		 * @since 1.3.8
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function colors( $args, $instance ) {
			// get the widget ID.
			$id = $args['widget_id'] ?? null;;

			// Define vars.
			$bg_color           = isset( $instance['bg_color'] ) ? sanitize_hex_color( $instance['bg_color'] ) : '';
			$bg_hover_color     = isset( $instance['bg_hover_color'] ) ? sanitize_hex_color( $instance['bg_hover_color'] ) : '';
			$color              = isset( $instance['color'] ) ? sanitize_hex_color( $instance['color'] ) : '';
			$color_hover        = isset( $instance['color_hover'] ) ? sanitize_hex_color( $instance['color_hover'] ) : '';
			$border_color       = isset( $instance['border_color'] ) ? sanitize_hex_color( $instance['border_color'] ) : '';
			$border_hover_color = isset( $instance['border_hover_color'] ) ? sanitize_hex_color( $instance['border_hover_color'] ) : '';
			$font_size          = isset( $instance['font_size'] ) ? $instance['font_size'] : ''; ?>

			<?php
			if ( $bg_color || $bg_hover_color
				|| $color || $color_hover
				|| $border_color || $border_hover_color ) :
				?>

				<style>
					#<?php echo $id; ?>.widget-oceanwp-social ul li a {
						<?php if ( $bg_color ) { echo 'background-color:' . $bg_color; } ?>;
						<?php if ( $color ) { echo 'color:' . $color; } ?>;
						<?php if ( $border_color ) { echo 'border-color:' . $border_color; } ?>;
					}

					#<?php echo $id; ?>.widget-oceanwp-social ul li a .owp-icon use {
						<?php if ( $color ) { echo 'stroke:' . $color; } ?>;
					}

					#<?php echo $id; ?>.widget-oceanwp-social ul li a .owp-icon {
						<?php if ( $font_size ) { echo 'width:' . $font_size . '; height:' . $font_size; } ?>;
					}

					#<?php echo $id; ?>.widget-oceanwp-social ul li a:hover {
						<?php if ( $bg_hover_color ) { echo 'background-color:' . $bg_hover_color; } ?>;
						<?php if ( $color_hover ) { echo 'color:' . $color_hover .'!important'; } ?>;
						<?php if ( $border_hover_color ) { echo 'border-color:' . $border_hover_color .'!important'; } ?>;
					}

					#<?php echo $id; ?>.widget-oceanwp-social ul li a:hover .owp-icon use {
						<?php if ( $color_hover ) { echo 'stroke:' . $color_hover .'!important'; } ?>;
					}
				</style>
			<?php endif; ?>

		<?php
		}

	}
}
register_widget( 'Ocean_Extra_Social_Widget' );
