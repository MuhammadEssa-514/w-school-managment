<?php

function oe_get_metabox_descriptions() {
	$list = apply_filters(
		'oe_metabox_descriptions_text',
		array(
			'post'                => esc_html__( 'Single blog post page', 'ocean-extra' ),
			'page'                => esc_html__( 'Single page', 'ocean-extra' ),
			'ae_global_templates' => esc_html__( 'Default AnyWhere Elementor template item', 'ocean-extra' ),
			'product'             => esc_html__( 'Single product item (WooCommerce & Easy Digital Downloads)', 'ocean-extra' ),
			'elementor_library'   => esc_html__( 'Default Elementor template item', 'ocean-extra' ),
			'oceanwp_library'     => esc_html__( 'OceanWP My Library template item', 'ocean-extra' ),
			'ocean_portfolio'     => esc_html__( 'Single Ocean Portfolio item', 'ocean-extra' ),
		)
	);
	return $list;
}
