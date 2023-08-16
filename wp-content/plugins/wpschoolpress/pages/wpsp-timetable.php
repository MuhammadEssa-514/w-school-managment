<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');

wpsp_header();
if (is_user_logged_in()) {
	global $current_user, $wp_roles, $wpdb;
		$current_usr_rle=$current_user->roles[0];
    if( $current_usr_rle == 'administrator' || $current_usr_rle == 'teacher' ) {
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
		$wpsp_teacher_table =	$wpdb->prefix . 'wpsp_teacher';
		$class_table		=	$wpdb->prefix."wpsp_class";
		$classQuery			=	"select cid,c_name from $class_table Order By cid ASC";
		$msg				=	'Please Add Class Before Adding Subjects';
		if( $current_usr_rle=='teacher' ) {
			$cuserId		=	intval($current_user->ID);
			$classQuery		=	"select cid,c_name from $class_table where teacher_id='".esc_sql($cuserId)."'";
			$msg			=	'Please Ask Principal To Assign Class';
		}
		$sel_class		=	$wpdb->get_results( $classQuery );

		$sel_classid	=	isset( $_POST['ClassID'] ) ? intval($_POST['ClassID']) : 'all';
		$wpsp_class_name	=	isset( $_POST['wpsp_class_name'] ) ? sanitize_text_field($_POST['wpsp_class_name']) : '';
		$sel_classname	=	$ctablename = '';
		foreach( $sel_class as $key=>$value ) {
			if( $value->cid	==	$sel_classid ) {
				$sel_classname	=	$value->c_name;
			}
			if( $wpsp_class_name	== $value->cid ){
				$ctablename	= ' For Class '.$value->c_name;
			}
		}
        ?>

		<?php
		if( isset($_GET['ac']) && sanitize_text_field($_GET['ac'])=='add' && !empty( $sel_classid ) ) { ?>
				<div class="wpsp-row">
					<div class="wpsp-col-md-12">
						<?php include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-createTimetable.php'); ?>
					</div>
				</div>
		<?php } else if( isset( $_GET['timetable'] )  && intval($_GET['timetable']) > 0 ) {
				include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-editTimetable.php');
		} else { ?>
		<?php if( !empty( $sel_classid ) ) {
			include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-viewTimetable.php');
			$response	=	wpsp_ViewTimetable($sel_classid);
		?>
		<div class="wpsp-card">
        <div class="wpsp-card-body">
		<form name="TimetableClass" id="TimetableClass" method="post" action="" class="wpsp-form-horizontal class-filter">
			<div class="wpsp-row">
				<div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-9">
					<div class="wpsp-form-group">
						<label class="wpsp-label"><?php echo esc_html("Select Class Name","wpschoolpress");?></label>
						<select name="ClassID" id="ClassID" class="wpsp-form-control">
								<option value="0" <?php if($sel_classid == 'all') echo esc_html("selected","wpschoolpress"); ?>><?php echo "Select";?></option>
							<?php foreach($sel_class as $classes) {	?>
							<option value="<?php echo esc_attr(intval($classes->cid));?>" <?php if($sel_classid==$classes->cid) echo esc_html("selected","wpschoolpress"); ?>><?php echo esc_html($classes->c_name);?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<?php if( $current_usr_rle == 'administrator'){?>
				<div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-3s">
					<div class="wpsp-form-group">
						<?php if( isset( $response['status'] ) && $response['status']==2 ) { ?>
								<a href="?page=sch-timetable&timetable=<?php echo esc_attr($sel_classid); ?>" title="Delete" data-id="<?php echo esc_attr($sel_classid); ?>" class="gap-all wp-edit-timetable wpsp-timetable-btn">
									<i class="icon wpsp-edit wpsp-edit-icon icn-gap"></i>
								</a>
								<a href="javascript:void(0);" title="Delete" data-id="<?php echo esc_attr($sel_classid); ?>" class="wp-delete-timetable wpsp-timetable-btn">
									<i class="icon wpsp-trash wpsp-delete-icon  ClassDeleteBt icn-gap " data-id="<?php echo esc_attr($sel_classid); ?>"></i>
								</a>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			</div>
		</form>
		<?php
		//echo $response['msg'];
		if($sel_classid == 'all'){} else {echo isset( $response['msg'] ) ? $response['msg'] :'';
	}
		} else {
			echo '<div class="wpsp-text-red col-lg-2">'.esc_html($msg).'</div>';
		}?>
			<?php
		}
		wpsp_body_end();
		wpsp_footer();
	} elseif ($current_usr_rle == 'student') {
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
			include_once(  WPSP_PLUGIN_PATH.'/includes/wpsp-viewTimetable.php');
			$wpsp_student_table = $wpdb->prefix . "wpsp_student";

			$class_id = base64_decode(sanitize_text_field($_GET['cid']));

			$result = wpsp_ViewTimetable($class_id);
			echo wpsp_kses_filter_allowed_html($result['msg']);?>
		 <div class="wpsp-wrapper">
                <div class="wpsp-container">
            <?php wpsp_body_end();
		wpsp_footer();?>
		</div></div><?php
	} else if ($current_usr_rle == 'parent') {
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
			include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-viewTimetable.php');
			$wpsp_student_table = $wpdb->prefix . "wpsp_student";
			$child_info = $wpdb->get_results("select * from $wpsp_student_table where parent_wp_usr_id='".esc_sql($current_user->ID)."'");
			?>
			<!-- <section class="wpsp-container"> -->
				<div class="wpsp-row">
					<div class="wpsp-col-md-12">
						<div class="wpsp-card">
							<div class="wpsp-card-body">
								<div class="tabbable-line">
									<div class="tabSec wpsp-nav-tabs-custom" id="verticalTab">
										<div class="tabList">
										<ul class="wpsp-resp-tabs-list">
										<?php
										$i=0;
										foreach ($child_info as $child_inf) {
											if(base64_decode(sanitize_text_field($_GET['sid'])) == $child_inf->sid){
										?>
										<li class="wpsp-tabing <?php echo ($i==0)?'active':''?>">
										<?php echo esc_html($child_inf->s_fname.' '. $child_inf->s_mname.' '. $child_inf->s_lname); ?>
										</li>
										<?php } $i++; } ?>

										</ul>
										</div>
									<div class="wpsp-tabBody wpsp-resp-tabs-container">
										<?php
										$i=0;
										foreach ($child_info as $child_inf) {
										?>
											<div class="tab-pane wpsp-tabMain <?php echo ($i==0)?'active':''?>" id="<?php echo 'child-'.esc_attr($i); ?>">
												<?php
												if ($child_inf->class_id != '') {
														//$class_id = $child_inf->class_id;
														$class_id = base64_decode(sanitize_text_field($_GET['cid']));
														$result = wpsp_ViewTimetable($class_id);
														echo wpsp_kses_filter_allowed_html($result['msg']);
												} else {
													echo "<div class='wpsp-col-md-12'><div class='wpsp-text-red'>Class missing..</div></div>";
												}  ?>
											</div>
										<?php $i++; } ?>
									</div>
								</div>

		<?php
		wpsp_body_end();
		wpsp_footer();
		}
} else{
	include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');
}?>
