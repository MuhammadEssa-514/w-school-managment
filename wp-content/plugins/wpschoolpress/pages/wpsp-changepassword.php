<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
if( is_user_logged_in() ) {
		global $current_user, $wpdb;
		$current_user_role=$current_user->roles[0];
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
?>
<div id="message_response"></div>
<form class="form-horizontal group-border-dashed" action="" id="changepassword">
    <?php wp_nonce_field( 'WPSChangePass', 'wps_chnagepass_nonce', '', true ); ?>
	<div class="wpsp-card">
		<div class="wpsp-card-head">
			<h3 class="wpsp-card-title"><?php esc_html_e( 'Change Password', 'wpschoolpress' )?></h3>
		</div>

		<div class="wpsp-card-body">
				<div class="wpsp-row">
					<div class="wpsp-col-md-3">
						<div class="wpsp-form-group">
							<label class="wpsp-label"><?php _e( 'Current Password', 'wpschoolpress' ); ?></label>
							<input class="wpsp-form-control" name="oldpw" id="oldpw" type="password" required>
						</div>
					</div>
				</div>

				<div class="wpsp-row">
					<div class="wpsp-col-md-3">
						<div class="wpsp-form-group">
							<label class="wpsp-label"><?php _e( 'New Password', 'wpschoolpress' ); ?></label>
							<input class="wpsp-form-control" name="newpw" id="newpw" type="password" required>
						</div>
					</div>
				</div>

				<div class="wpsp-row">
					<div class="wpsp-col-md-3">
						<div class="wpsp-form-group">
							<label class="wpsp-label"><?php _e( 'Confirm  New Password', 'wpschoolpress' ); ?></label>
							<input class="wpsp-form-control" name="newrpw" id="newrpw" type="password" required>
						</div>
					</div>
				</div>

				<div class="wpsp-row">
					<div class="wpsp-col-md-12">
						<div class="wpsp-form-group">
							<input class="wpsp-btn wpsp-btn-primary" name="Change" id="Change" value="Change" type="submit">
						</div>
					</div>
				</div>

		</div>
	</div>
</form>
		<?php
			wpsp_body_end();
			wpsp_footer();
} else {
		//Include Login Section
	include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
}
?>