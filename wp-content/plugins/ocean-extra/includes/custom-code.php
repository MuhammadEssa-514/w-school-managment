<?php
/**
 * Custom Code Customizer Options
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OceanWP_Custom_Code_Customizer' ) ) :

	/**
	 * Custom CSS / JS Customizer Class
	 */
	class OceanWP_Custom_Code_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'customize_register', array( $this, 'customizer_options' ) );
			add_action( 'ocean_footer_js', array( $this, 'output_custom_js' ), 9999 );

		}

		/**
		 * Customizer options
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function customizer_options( $wp_customize ) {

			$section = 'ocean_custom_code_panel';
			$wp_customize->add_section(
				$section,
				array(
					'title'    => esc_html__( 'Custom CSS/JS', 'ocean-extra' ),
					'priority' => 210,
				)
			);

			/**
			 * Custom JS
			 */
			$wp_customize->add_setting(
				'ocean_custom_js',
				array(
					'transport'         => 'postMessage',
					'sanitize_callback' => false,
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'ocean_custom_js',
					array(
						'label'       => esc_html__( 'Custom JS', 'ocean-extra' ),
						'description' => esc_html__( 'You need to reload to see the changes. No need to add the <script> tags.', 'ocean-extra' ),
						'type'        => 'textarea',
						'section'     => $section,
						'settings'    => 'ocean_custom_js',
						'priority'    => 10,
					)
				)
			);

		}

		/**
		 * Outputs the custom JS
		 *
		 * @since 1.0.0
		 *
		 * @param string $output Custom JS output.
		 */
		public function output_custom_js( $output ) {

			$js = get_theme_mod( 'ocean_custom_js', false );
			if ( $js ) {
				$output .= $js;
			}
			return $output;

		}

	}

endif;

return new OceanWP_Custom_Code_Customizer();
