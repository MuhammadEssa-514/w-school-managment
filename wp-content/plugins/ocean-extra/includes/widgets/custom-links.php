<?php
/**
 * Custom Links Widget.
 *
 * @package OceanWP WordPress theme
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Custom_Links_Widget' ) ) {
	class Ocean_Extra_Custom_Links_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'ocean_custom_links',
				esc_html__( '&raquo; Custom Links', 'ocean-extra' ),
				array(
					'classname'                   => 'widget-oceanwp-custom-links custom-links-widget',
					'description'                 => esc_html__( 'Displays custom links.', 'ocean-extra' ),
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

			$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$count    = isset( $instance['count'] ) ? $instance['count'] : '';
			$target   = isset( $instance['target'] ) ? $instance['target'] : '';
			$nofollow = isset( $instance['nofollow'] ) ? $instance['nofollow'] : '';

			// Before widget WP hook.
			echo $args['before_widget'];

				// Show widget title.
			if ( $title ) {
				echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
			}

				// Determine link rel.
				$ocean_srt = '<span class="screen-reader-text">' . esc_html__( 'Opens in a new tab', 'ocean-extra' ) . '</span>';

				$results = ocean_link_rel( $ocean_srt, $nofollow, $target );

				$ocean_sr = $results[0];
				$link_rel = $results[1];

				// Display custom links.
				echo '<ul class="oceanwp-custom-links">';
			if ( $count !== '0' ) {
				for ( $i = 1; $i <= $count; $i++ ) {
					$url  = isset( $instance[ 'url_' . $i ] ) ? $instance[ 'url_' . $i ] : '';
					$text = isset( $instance[ 'text_' . $i ] ) ? $instance[ 'text_' . $i ] : '';

					echo '<li>';
						echo '<a href="' . esc_url( $url ) . '" target="_' . esc_attr( $target ) . '" ' . $link_rel . '>' . esc_attr( $text ) . '</a>';
						echo $ocean_sr;
					echo '</li>';

				}
			}
				echo '</ul>';

			// After widget WP hook.
			echo $args['after_widget'];

		}

		/**
		 * Updates the widget control options for the particular instance of the widget.
		 *
		 * @since 1.0.0
		 */
		public function update( $new_instance, $old_instance ) {
			$instance             = $old_instance;
			$instance['title']    = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['count']    = ! empty( $new_instance['count'] ) ? strip_tags( $new_instance['count'] ) : '';
			$instance['target']   = ! empty( $new_instance['target'] ) ? strip_tags( $new_instance['target'] ) : '';
			$instance['nofollow'] = ! empty( $new_instance['nofollow'] ) ? strip_tags( $new_instance['nofollow'] ) : '';
			for ( $i = 1;$i <= $instance['count'];$i++ ) {
				$instance[ 'url_' . $i ]  = ! empty( $new_instance[ 'url_' . $i ] ) ? strip_tags( $new_instance[ 'url_' . $i ] ) : '';
				$instance[ 'text_' . $i ] = ! empty( $new_instance[ 'text_' . $i ] ) ? strip_tags( $new_instance[ 'text_' . $i ] ) : '';
			}
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

			$settings = wp_parse_args(
				(array) $instance,
				array(
					'title'    => esc_attr__( 'Useful Links', 'ocean-extra' ),
					'count'    => '5',
					'target'   => esc_html__( 'Blank', 'ocean-extra' ),
					'nofollow' => esc_html__( 'No', 'ocean-extra' ),
				)
			);

			$title    = $settings['title'];
			$count    = $settings['count'];
			$target   = $settings['target'];
			$nofollow = $settings['nofollow'];

			for ( $i = 1;$i <= 15;$i++ ) {
				$url   = 'url_' . $i;
				$$url  = isset( $instance[ $url ] ) ? $instance[ $url ] : '';
				$text  = 'text_' . $i;
				$$text = isset( $instance[ $text ] ) ? $instance[ $text ] : '';
			}
			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ocean-extra' ); ?>:</label>
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Link Target:', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>">
					<option value="blank" <?php selected( $target, 'blank' ); ?>><?php esc_html_e( 'Blank', 'ocean-extra' ); ?></option>
					<option value="self" <?php selected( $target, 'self' ); ?>><?php esc_html_e( 'Self', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'nofollow' ) ); ?>"><?php esc_html_e( 'Add Nofollow Link Rel:', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'nofollow' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'nofollow' ) ); ?>">
					<option value="no" <?php selected( $nofollow, 'no' ); ?>><?php esc_html_e( 'No', 'ocean-extra' ); ?></option>
					<option value="yes" <?php selected( $nofollow, 'yes' ); ?>><?php esc_html_e( 'Yes', 'ocean-extra' ); ?></option>
				</select>
			</p>


			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of Custom Links:', 'ocean-extra' ); ?></label>
				<select class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
					<option value="1" <?php selected( $count, '1' ); ?>><?php esc_html_e( '1', 'ocean-extra' ); ?></option>
					<option value="2" <?php selected( $count, '2' ); ?>><?php esc_html_e( '2', 'ocean-extra' ); ?></option>
					<option value="3" <?php selected( $count, '3' ); ?>><?php esc_html_e( '3', 'ocean-extra' ); ?></option>
					<option value="4" <?php selected( $count, '4' ); ?>><?php esc_html_e( '4', 'ocean-extra' ); ?></option>
					<option value="5" <?php selected( $count, '5' ); ?>><?php esc_html_e( '5', 'ocean-extra' ); ?></option>
					<option value="6" <?php selected( $count, '6' ); ?>><?php esc_html_e( '6', 'ocean-extra' ); ?></option>
					<option value="7" <?php selected( $count, '7' ); ?>><?php esc_html_e( '7', 'ocean-extra' ); ?></option>
					<option value="8" <?php selected( $count, '8' ); ?>><?php esc_html_e( '8', 'ocean-extra' ); ?></option>
					<option value="9" <?php selected( $count, '9' ); ?>><?php esc_html_e( '9', 'ocean-extra' ); ?></option>
					<option value="10" <?php selected( $count, '10' ); ?>><?php esc_html_e( '10', 'ocean-extra' ); ?></option>
					<option value="11" <?php selected( $count, '11' ); ?>><?php esc_html_e( '11', 'ocean-extra' ); ?></option>
					<option value="12" <?php selected( $count, '12' ); ?>><?php esc_html_e( '12', 'ocean-extra' ); ?></option>
					<option value="13" <?php selected( $count, '13' ); ?>><?php esc_html_e( '13', 'ocean-extra' ); ?></option>
					<option value="14" <?php selected( $count, '14' ); ?>><?php esc_html_e( '14', 'ocean-extra' ); ?></option>
					<option value="15" <?php selected( $count, '15' ); ?>><?php esc_html_e( '15', 'ocean-extra' ); ?></option>
				</select>
			</p>

			<div class="custom_links_wrap">
				<?php
				for ( $i = 1;$i <= 15;$i++ ) :
					$url  = 'url_' . $i;
					$text = 'text_' . $i;
					?>
				<div class="custom_links_<?php echo esc_attr( $i ); ?>" 
													<?php
													if ( $i > $count ) :
														?>
					style="display:none;"<?php endif; ?> style="padding-bottom:30px">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $url ) ); ?>">
							<?php printf( '#%s URL:', esc_attr( $i ) ); ?>
						</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $url ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $url ) ); ?>" type="text" value="<?php echo esc_attr( $$url ); ?>" />
					</p>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $text ) ); ?>">
							<?php printf( '#%s Text:', esc_attr( $i ) ); ?>
						</label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $text ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $text ) ); ?>" type="text" value="<?php echo esc_attr( $$text ); ?>" />
					</p>
				</div>
				<?php endfor; ?>
			</div>

			<?php

		}

	}
}
register_widget( 'Ocean_Extra_Custom_Links_Widget' );
