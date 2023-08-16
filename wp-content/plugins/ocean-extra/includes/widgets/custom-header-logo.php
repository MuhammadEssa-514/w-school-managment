<?php
/**
 * Custom Header logo widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Custom_Header_Logo_Widget' ) ) {
	class Ocean_Extra_Custom_Header_Logo_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'ocean_custom_header_logo',
				esc_html__( '&raquo; Custom Header Logo', 'ocean-extra' ),
				array(
					'classname'   => 'widget-oceanwp-custom-header-logo custom-header-logo-widget',
					'description' => esc_html__( 'Display the logo for the Custom Header style.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
			);
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

			// Define vars
			$position 		= isset( $instance['position'] ) ? $instance['position'] : 'left';

			// Add classes
			$classes 		= array( 'custom-header-logo', 'clr' );
			$classes[] 		= $position;
			$classes 		= implode( ' ', $classes );

			// Before widget WP hook
			echo $args['before_widget']; ?>

				<div class="<?php echo esc_attr( $classes ); ?>">

					<?php
					// Logo
					get_template_part( 'partials/header/logo' ); ?>

				</div>

			<?php
			// After widget WP hook
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
			$instance            	= $old_instance;
			$instance['position']  	= strip_tags( $new_instance['position'] );
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

			$instance = wp_parse_args( ( array ) $instance, array(
				'position' => 'left',
			) ); ?>

			<p>
				<?php esc_html_e( 'This widget is to display with your page builder the logo for the Custom Header style.', 'ocean-extra' ); ?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id('position') ); ?>"><?php esc_html_e( 'Position:', 'ocean-extra' ); ?></label>
				<br />
				<select class='widget-select widefat' name="<?php echo esc_attr( $this->get_field_name('position') ); ?>" id="<?php echo esc_attr( $this->get_field_id('position') ); ?>">
					<option value="left" <?php selected( $instance['position'], 'left' ) ?>><?php esc_html_e( 'Left', 'ocean-extra' ); ?></option>
					<option value="right" <?php selected( $instance['position'], 'right' ) ?>><?php esc_html_e( 'Right', 'ocean-extra' ); ?></option>
					<option value="center" <?php selected( $instance['position'], 'center' ) ?>><?php esc_html_e( 'Center', 'ocean-extra' ); ?></option>
				</select>
			</p>

		<?php

		}

	}
}
register_widget( 'Ocean_Extra_Custom_Header_Logo_Widget' );