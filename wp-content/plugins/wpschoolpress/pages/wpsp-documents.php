<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wpdb;
		$current_user_role=$current_user->roles[0];
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
		$filename	=	'';
		$header	=	'Documents';

		do_action('wpspsmpro_documents_list_html');

		wpsp_body_end();
		wpsp_footer();
	} else {
		include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
	}
?>
