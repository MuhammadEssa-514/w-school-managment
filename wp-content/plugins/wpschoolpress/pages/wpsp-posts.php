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
		$header	=	'Posts';

		if( isset($_GET['tab'] ) && sanitize_text_field($_GET['tab']) == 'addposts' ) {
			$header	=	$label	=	__( 'Add New Post', 'WPSchoolPress');
			//$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-classForm.php';
			do_action('wpspsmpro_add_posts_html');
		}elseif((isset($_GET['id']) && is_numeric($_GET['id'])))  {
			$header	=	$label	=	__( 'Update Post', 'WPSchoolPress');
			// $filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-classForm.php';
			do_action('wpspsmpro_edit_posts_html');
		} else {
			do_action('wpspsmpro_list_posts_html');
		}

		wpsp_body_end();
		wpsp_footer();
	} else {
		include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
	}
?>
