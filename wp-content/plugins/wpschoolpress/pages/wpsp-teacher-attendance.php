<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wpdb;
		$current_user_role=$current_user->roles[0];
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
		if($current_user_role=='administrator' || $current_user_role=='teacher') {	?>
		<div class="wpsp-card">
			<div class="wpsp-row">
				<div class="wpsp-col-md-12">
						<div class="wpsp-card-head">
							<h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_teacher_attendance_heading_item',esc_html("Teacher Attendance","wpschoolpress")); ?></h3>
						</div>
						<div class="wpsp-card-body">
							<div class="wpsp-row">
								<div class="wpsp-col-md-3" id="AttendanceEnterForm">
										<div class="wpsp-form-group">
											<label class="control-label"><?php _e( 'Date', 'wpschoolpress' ); ?> </label>
											<input type="text" class="wpsp-form-control select_date" id="AttendanceDate" value="<?php echo isset($_POST['entry_date'])? esc_attr($_POST['entry_date']) : date('m/d/Y'); ?>" name="entry_date">
										</div>
										<div class="wpsp-form-group">
											<?php if($current_user_role=='administrator'){?>
										<button id="AttendanceEnter" name="attendance" class="wpsp-btn wpsp-btn-success"><?php _e( 'Add', 'wpschoolpress'); ?></button>
									<?php }?>
										<button id="AttendanceView" name="attendanceview" class="wpsp-btn wpsp-btn-primary"><?php _e( 'View', 'wpschoolpress'); ?></button>
									</div>
								</div>
							</div>
							<div class="wpsp-row">
								<div class="wpsp-col-lg-12 wpsp-col-md-12 Attendance-Overview MTTen">
									<div class="AttendanceContent">
									</div>
								</div>
							</div>
						</div>
				</div>
			</div>
		</div>
		<?php if($current_user_role=='administrator'){?>
		<div class="modal modal-wide" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="AddModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content" id="AddModalContent">
				</div>
			</div>
		</div><!-- /.modal -->
		<?php	}
		} else {
			echo esc_html("No access to this page","wpschoolpress");
		}
		wpsp_body_end();
		wpsp_footer();
	} else{
		include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');
	}
?>
