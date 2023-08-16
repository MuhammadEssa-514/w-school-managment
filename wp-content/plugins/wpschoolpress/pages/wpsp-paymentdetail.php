<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
    if( is_user_logged_in() ) {
        wpsp_topbar();
        wpsp_sidebar();
        wpsp_body_start();
        $proversion     =   wpsp_check_pro_version( 'wpsp_payment_version' );
        $proclass       =   !$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';
        $protitle       =   !$proversion['status'] && isset( $proversion['message'] )? $proversion['message']   : '';

        ?>
        <section class="content-header">
            <h1><?php esc_html_e( 'Payment', 'wpschoolpress' ); ?></h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo esc_url(site_url('sch-dashboard')); ?> "><i class="fa fa-dashboard"></i> <?php esc_html_e( 'Dashboard', 'wpschoolpress' ); ?></a></li>
                <li><a href="<?php echo esc_url(site_url('sch-payment')); ?>"><?php esc_html_e( 'Payment', 'wpschoolpress' ); ?></a></li>
            </ol>
        </section>


        <?php
            if( !empty( $protitle ) ){
                echo '<h2>To use this feature upgrade to pro version</h2>';
            } else {
                do_action( 'wpsp_payment_service' );
            }
        wpsp_body_end();
        wpsp_footer();
    }
    else{
        //Include Login Section
        include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
    }
?>
