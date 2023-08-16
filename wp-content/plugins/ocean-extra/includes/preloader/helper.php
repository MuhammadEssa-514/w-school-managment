<?php
/**
 * Preloader
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preloader Icon list
 */
function oe_preloader_icon_list() {

    $icon_array = array(
        'roller'        => '<div class="preloader-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>',
        'circle'        => '<div class="preloader-circle"><div></div></div>',
        'ring'          => '<div class="preloader-ring"><div></div><div></div><div></div><div></div></div>',
        'dual-ring'     => '<div class="preloader-dual-ring"></div>',
        'ripple-plain'  => '<div class="preloader-ripple-plain"><div></div><div></div><div></div></div>',
        'ripple-circle' => '<div class="preloader-ripple-circle"><div></div><div></div></div>',
        'heart'         => '<div class="preloader-heart"><div></div></div>',
        'ellipsis'      => '<div class="preloader-ellipsis"><div></div><div></div><div></div><div></div></div>',
        'spinner-dot'   => '<div class="preloader-spinner-dot"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>',
        'spinner-line'  => '<div class="preloader-spinner-line"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>',

    );

    $icon_array = apply_filters( 'ocean_preloader_icon_list', $icon_array );

    return $icon_array;

}

/**
 * Preloader icon
 */
function oe_preloader_icon( $icon = '' ) {

    if ( empty( $icon ) ) {
        return;
    }

    $icon_array = oe_preloader_icon_list();

    $content = $icon_array[$icon];
    $content = apply_filters( 'ocean_preloader_icon_html', $content );

    return $content;

}

/**
 * Returns Preloader image
 */
function oe_preloader_image_html() {

    $html = '';

    $img_url = get_theme_mod( 'ocean_preloader_icon_image' );

    $img_data = array(
        'url'    => '',
        'width'  => '',
        'height' => '',
        'alt'    => '',
    );

    if ( $img_url ) {

        $img_data['url'] = $img_url;

        $img_attachment_data = oceanwp_get_attachment_data_from_url( $img_url );

        if ( $img_attachment_data ) {
            $img_data['width']  = $img_attachment_data['width'];
            $img_data['height'] = $img_attachment_data['height'];
            $img_data['alt']    = $img_attachment_data['alt'];
        }

        // Output image.
        $html = sprintf(
            '<img src="%1$s" class="preloader-attachment" width="%2$s" height="%3$s" alt="%4$s" />',
            esc_url( $img_data['url'] ),
            esc_attr( $img_data['width'] ),
            esc_attr( $img_data['height'] ),
            esc_attr( $img_data['alt'] )
        );

    }

    // Return image.
    return apply_filters( 'ocean_preloader_image', $html );

}

/**
 * Echo Preloader image
 */
function oe_preloader_image() {
    echo wp_kses_post( oe_preloader_image_html() );
}
