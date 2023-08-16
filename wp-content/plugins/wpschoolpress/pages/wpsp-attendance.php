<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wpdb;
		$current_user_role=$current_user->roles[0];
		if($current_user_role=='administrator' || $current_user_role=='teacher')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
		?>
		<div class="wpsp-card">
						<div class="wpsp-card-head">
							<h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_attendance_heading_detail', esc_html__( 'Attendance Report', 'wpschoolpress' )); ?></h3>
							<hr>
						</div>
						<div class="wpsp-card-body text-black">
							<div class="wpsp-row">
								<?php
								$item =  apply_filters( 'wpsp_student_attendance_title_item',esc_html("Attendance","wpschoolpress"));
								?>
							<div class="wpsp-col-lg-5 wpsp-col-md-5 wpsp-col-sm-12 wpsp-col-xs-12" id="AttendanceEnterForm">
							<h3 class="wpsp-card-title"><?php esc_html_e( 'Attendance', 'wpschoolpress' ); ?></h3>
							<div class="line_box">
									<div class="wpsp-form-group">
										<label for="Class">
											<?php if( isset($item['classid'])){
												esc_html_e($item['classid'],"wpschoolpress");
											}else{
												esc_html_e("Select Class","wpschoolpress");
											}
											?> </label>
											<select name="classid" id="AttendanceClass" class="wpsp-form-control">
												<option value=""><?php echo esc_html("Select Class", "wpschoolpress");?></option>
													<?php
													if(isset($_POST['classid']) && intval($_POST['classid'])!='')
														$selid=intval($_POST['classid']);
													else
														if($current_user_role=='teacher'){
															$current_user_id = get_current_user_id();
														$selid=0;
													$ctname=$wpdb->prefix.'wpsp_class';
													$clt=$wpdb->get_results("select `cid`,`c_name` from `$ctname` where teacher_id = '$current_user_id' ");
													foreach($clt as $cnm){?>
														<option value="<?php echo esc_attr($cnm->cid);?>" <?php if($cnm->cid==$selid) echo esc_html("selected","wpschoolpress");?>><?php echo esc_html($cnm->c_name);?></option>
													<?php }
														} else {
														$selid=0;
													$ctname=$wpdb->prefix.'wpsp_class';
													$clt=$wpdb->get_results("select `cid`,`c_name` from `$ctname`");
													foreach($clt as $cnm){?>
														<option value="<?php echo esc_attr($cnm->cid);?>" <?php if($cnm->cid==$selid) echo esc_html("selected","wpschoolpress");?>><?php echo esc_html($cnm->c_name);?></option>
													<?php } } ?>
											</select>
											<span class="clserror wpsp-text-red"><?php echo esc_html( 'Please select Class', 'wpschoolpress' ); ?></span>
									</div>
									<div class="wpsp-form-group">
										<label for="date">	<?php if( isset($item['entry_date'])){
												esc_html_e($item['entry_date'],"wpschoolpress");
											}else{
												esc_html_e('Date',"wpschoolpress");
											}
											?></label>
										<input type="text" class="wpsp-form-control select_date" id="AttendanceDate" value="<?php if(isset($_POST['entry_date'])) { echo esc_attr($_POST['entry_date']); } else { echo date('m/d/Y'); }?>" name="entry_date">
										<span class="clsdate"><?php echo esc_html( 'Please select Date', 'wpschoolpress' ); ?></span>
									</div>
									<div class="wpsp-row wpsp-text-center">
										<div class="wpsp-col-sm-12">
											<div class="wpsp-form-group">
												<button id="AttendanceEnter" name="attendance" class="wpsp-btn wpsp-btn-success"><?php echo esc_html( 'Add/Update', 'wpschoolpress' ); ?></button>
												<button id="Attendanceview" name="attendanceview" class="wpsp-btn wpsp-btn-primary"><?php echo esc_html( 'View', 'wpschoolpress' ); ?></button>
											</div>
										</div>
									</div>
									<div class="wpsp-row">
										<div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-form-group wpsp-text-red" id="wpsp-error-msg" style="margin-top:0px;">
										</div>
									</div>
								</div>
							</div>
							<div class="wpsp-col-lg-6 wpsp-col-lg-offset-1 wpsp-col-md-5 wpsp-col-md-offset-1 wpsp-col-sm-12 wpsp-col-xs-12 AttendanceView">
								<?php
									$class_names		=	$c_stcount	=	$attendance	=	array();
									$class_table		=	$wpdb->prefix."wpsp_class";
									$student_table		=	$wpdb->prefix."wpsp_student";
									$attendance_table	=	$wpdb->prefix."wpsp_attendance";
									$class_info			=	$wpdb->get_results("select cid,c_name from $class_table");

									$stl = array();
									foreach($class_info as $cls){
										$class_names[$cls->cid]=$cls->c_name;

												$studentlists	=	$wpdb->get_results("select class_id, sid from $student_table");

												foreach ($studentlists as $stu) {
													//$stl = [];
													if(is_numeric($stu->class_id) ){
														if($stu->class_id == $cls->cid){
														 $stl[$cls->cid][$stu->sid] = $stu->sid;
													 }
													}
													else{
														 $class_id_array = unserialize( $stu->class_id );
														 if(!empty($class_id_array)){
														 if(in_array($cls->cid, $class_id_array)){
															 $stl[$cls->cid][$stu->sid] = $stu->sid;
														 }
														}
													}
											}
										}


                                    $newArr = array();
                                    $newArr1 = array();
                                    foreach ($stl as $key => $value) {
                                        $newArr[] = count($value);
                                        $newArr1[$key] = count($value);
                                        }

									$classwise_count	=	$wpdb->get_results("select class_id, count(*) as count from $student_table GROUP BY class_id",ARRAY_A);

									foreach($classwise_count as $clwc){
										$c_stcount[$clwc['class_id']]	=	$clwc['count'];
									}
									$date_today	=	date('Y-m-d');
									$attendance_info	=	$wpdb->get_results("select class_id, absents from $attendance_table where date='$date_today'");

									foreach($attendance_info as $attend){

										foreach($newArr1 as  $key => $value)
											if($key == $attend->class_id){
										 $absents	=	json_decode($attend->absents);

										 if(empty($absents)){}else {
										  $present	=	$value-count($absents);
										}
										$percent	=	round(($present*100)/$value);
										$attendance[$attend->class_id]	=	array('present'=>$present,'percentage'=>$percent);
									}
								}
								?>
								<div class="wpsp-col-sm-12">
									<h3 class="wpsp-card-title"><?php esc_html_e( 'View Attendance Report', 'wpschoolpress' ); ?></h3>
									<div class="line_box">
										<div class="box-body">
											<?php
                                                $i=0;
												foreach($class_names as $clid=>$cln){
												$css_class='';
												if(isset($attendance[$clid])){
													if($attendance[$clid]['percentage']==100)
														$css_class="wpsp-progress-bar-success";
													else if($attendance[$clid]['percentage']<100 && $attendance[$clid]['percentage']>70)
														$css_class="wpsp-progress-bar-warning";
													else if($attendance[$clid]['percentage']<=70)
														$css_class="wpsp-progress-bar-danger";
													else
														$css_class="wpsp-progress-bar-info";
												}

											?>
												<div class="wpsp-progress-group">
														<span class="wpsp-progress-text"><?php echo esc_html($cln);?></span>
														<span class="wpsp-progress-number"><?php if(isset($attendance[$clid])){ echo esc_html($attendance[$clid]['present']); }?>/<?php echo (isset($newArr[$i]))? esc_html($newArr[$i]):'0';?></span>
														<div class="wpsp-progress sm">
															<div class="wpsp-progress-bar <?php echo esc_attr($css_class);?>" style="width: <?php echo (isset($attendance[$clid])) ? esc_attr($attendance[$clid]['percentage']):'0'; ?>%">
															</div>
														</div>
												</div>
											<?php
											$i++;
										} ?>
										</div>
									</div>
								</div>
						</div>
					</div>
		<div class="modal modal-wide" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="AddModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content" id="AddModalContent">
				</div>
			</div>
		</div>
	</div>
</div>
	 	<?php
			wpsp_body_end();
			wpsp_footer();
		}
		else if($current_user_role=='parent')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			$parent_id=intval($current_user->ID);

			$student_table=$wpdb->prefix."wpsp_student";
			$class_table=$wpdb->prefix."wpsp_class";
			$att_table=$wpdb->prefix."wpsp_attendance";
			$students=$wpdb->get_results("select wp_usr_id, class_id, s_fname, s_mname, s_lname, sid from $student_table where parent_wp_usr_id='".esc_sql($parent_id)."'");
			$child=array();
			foreach($students as $childinfo){
				$child[]=array('student_id'=>$childinfo->wp_usr_id,'s_fname'=>$childinfo->s_fname,'class_id'=>$childinfo->class_id, 's_mname'=>$childinfo->s_mname, 's_lname'=>$childinfo->s_lname, 'sid'=>$childinfo->sid);
			}
			?>
			<div class="wpsp-card">
			<div class="wpsp-card-body">

					<div class="tabSec wpsp-nav-tabs-custom" id="verticalTab">
						<div class="tabList">
							<ul class="wpsp-resp-tabs-list">
						<?php $i=0; foreach($child as $ch) {
						if(base64_decode(sanitize_text_field($_GET['sid'])) == $ch['sid']){ ?>
							<li class="wpsp-tabing <?php echo ($i==0)?'wpsp-resp-tab-active':''?>">
								<?php echo esc_html($ch['s_fname'].' '.$ch['s_mname'].' '. $ch['s_lname']);?></li>
							<?php }$i++; } ?>
					</ul>
					</div>
					<section class="content">
				<?php

					global $wpdb;
					$id = sanitize_text_field($_GET['cid']);
					$class_id = sanitize_text_field(base64_decode($id));
					$st_id = base64_decode(sanitize_text_field($_GET['sid']));
					$stl =[];
					$att_table = $wpdb->prefix . "wpsp_attendance";
					$st_table = $wpdb->prefix . "wpsp_student";
					$class_table = $wpdb->prefix . "wpsp_class";
					$ser = '%' . $st_id . '%';
					$stinfo = $wpdb->get_row("select st.class_id, st.s_rollno, st.wp_usr_id, CONCAT_WS(' ', st.s_fname, st.s_mname, st.s_lname ) AS full_name, c.c_name, c.c_sdate, c.c_edate from $st_table st LEFT JOIN $class_table c ON c.cid='".esc_sql($class_id)."' where st.sid='".esc_sql($st_id)."'");
					if(is_numeric($stinfo->class_id) ){
						if($stinfo->class_id == $class_id){
						 $stl[] = $st->sid;
					 }
					}else{
						 $class_id_array = unserialize($stinfo->class_id);
						 if(in_array($class_id, $class_id_array)){
							 $stl[] = $class_id;
						 }
					}
					$att_info = $wpdb->get_row("select count(*) as count from $att_table WHERE absents LIKE '$ser'");
					$stinfo->c_edate = wpsp_ViewDate($stinfo->c_edate);
					$stinfo->c_sdate = wpsp_ViewDate($stinfo->c_sdate);
					$loc_avatar = get_user_meta($stinfo->wp_usr_id, 'simple_local_avatar', true);
					$img_url = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL . 'img/avatar.png';
					$attendance_days = $wpdb->get_results("select * from $att_table where class_id='$class_id'");
					$present_days = 0;
					$classid_array = unserialize($stinfo->class_id);
					$classname_array = [];
					foreach ($classid_array as $id ) {
						$clasname = $wpdb->get_var("SELECT c_name FROM $class_table where cid='$class_id'");
						$classname_array[] = $clasname;
					}
					foreach($attendance_days as $days => $attendance)
					{
						if ($attendance->absents == 'Nil')
						{
							$present_days++;
						}
						else
						{
							$absents = json_decode($attendance->absents, true);
							if (array_search($stinfo->wp_usr_id, array_column($absents, 'sid')) !== False)
							{
							}
							else
							{
								$present_days++;
							}
						}
					}
					$working_days = $present_days + $att_info->count;
					$content = "<div class='wpsp-panel-body'>
					<div class='wpsp-userpic'style='margin-top: 0;'>
						 <img src=".esc_url($img_url)." height='150px' width='150px' class='img img-circle'/>
					</div>
					<div class='wpsp-userDetails'>
						<table class='wpsp-table'>
							<tbody>
								<tr>
									<td colspan='2'><strong>Name: </strong>".esc_html($stinfo->full_name)."</td>
								</tr>
								<tr>
									<td><strong>Class: </strong>".esc_html($stinfo->c_name)."</td>
									<td><strong>Roll No. : </strong>".esc_html($stinfo->s_rollno)."</td>
								</tr>
								<tr>
									<td><strong>Class Start: </strong> ".esc_html($stinfo->c_sdate)." </td>
									<td><strong>Class End : </strong>".esc_html($stinfo->c_edate)."</td>
								</tr>
								<tr>
									<td><strong>Number of Absent days: </strong>".esc_html($att_info->count)."</td>
									<td><strong>Number of Present days: </strong>".esc_html($present_days)."</td>
								</tr>
								<tr>
									<td colspan='2'><strong>Number of Attendance days: </strong>".esc_html($working_days)."</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>";
            $allowed_tags = wp_kses_allowed_html('post');
            echo wp_kses(stripslashes_deep($content), $allowed_tags);
	        // echo $content;
			?>
			</section>
</div>
</div>
</div>
			<?php
			wpsp_body_end();
			wpsp_footer();
		}else if($current_user_role=='student'){
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			$st_id=$current_user->ID;
			?>
<div class="wpsp-card">
			<div class="wpsp-card-body">
			<section class="content">
				<?php
					global $wpdb;
					$id = sanitize_text_field(stripslashes($_GET['cid']));
					$class_id = sanitize_text_field(base64_decode($id));
					$stl =array();
					$att_table = $wpdb->prefix . "wpsp_attendance";
					$st_table = $wpdb->prefix . "wpsp_student";
					$class_table = $wpdb->prefix . "wpsp_class";
					$ser = '%' . $st_id . '%';
					$stinfo = $wpdb->get_row("select st.class_id, st.s_rollno , CONCAT_WS(' ', st.s_fname, st.s_mname, st.s_lname ) AS full_name, c.c_name, c.c_sdate, c.c_edate from $st_table st LEFT JOIN $class_table c ON c.cid='".esc_sql($class_id)."' where st.wp_usr_id='".esc_sql($st_id)."'");

					if(is_numeric($stinfo->class_id) ){
						if($stinfo->class_id == $class_id){
						 $stl[] = $st->sid;
					 }
					}else{
						 $class_id_array = unserialize($stinfo->class_id);
						 if(in_array($class_id, $class_id_array)){
							 $stl[] = $class_id;
						 }
					}
					$att_info = $wpdb->get_row("select count(*) as count from $att_table WHERE absents LIKE '$ser'");
					$stinfo->c_edate = wpsp_ViewDate($stinfo->c_edate);
					$stinfo->c_sdate = wpsp_ViewDate($stinfo->c_sdate);
					$loc_avatar = get_user_meta($st_id, 'simple_local_avatar', true);
					$img_url = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL . 'img/avatar.png';
					$attendance_days = $wpdb->get_results("select * from $att_table where class_id='$class_id'");
					$present_days = 0;
					$classid_array = unserialize($stinfo->class_id);
					$classname_array = array();
					foreach ($classid_array as $id ) {
						$clasname = $wpdb->get_var("SELECT c_name FROM $class_table where cid='$class_id'");
						$classname_array[] = $clasname;
					}
					foreach($attendance_days as $days => $attendance)
					{
						if ($attendance->absents == 'Nil')
						{
							$present_days++;
						}
						else
						{
							$absents = json_decode($attendance->absents, true);
							if (array_search($st_id, array_column($absents, 'sid')) !== False)
							{
							}
							else
							{
								$present_days++;
							}
						}
					}
					$working_days = $present_days + $att_info->count;
					$content = "<div class='wpsp-panel-body'>
					<div class='wpsp-userpic'style='margin-top: 0;'>
						 <img src=".esc_url($img_url)." height='150px' width='150px' class='img img-circle'/>
					</div>
					<div class='wpsp-userDetails'>
						<table class='wpsp-table'>
							<tbody>
								<tr>
									<td colspan='2'><strong>Name: </strong>".esc_html($stinfo->full_name)."</td>
								</tr>
								<tr>
									<td><strong>Class: </strong>".esc_html($stinfo->c_name)."</td>
									<td><strong>Roll No. : </strong>".esc_html($stinfo->s_rollno)."</td>
								</tr>
								<tr>
									<td><strong>Class Start: </strong> ".esc_html($stinfo->c_sdate)." </td>
									<td><strong>Class End : </strong>".esc_html($stinfo->c_edate)."</td>
								</tr>
								<tr>
									<td><strong>Number of Absent days: </strong>".esc_html($att_info->count)."</td>
									<td><strong>Number of Present days: </strong>".esc_html($present_days)."</td>
								</tr>
								<tr>
									<td colspan='2'><strong>Number of Attendance days: </strong>".esc_html($working_days)."</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>";
            $allowed_tags = wp_kses_allowed_html('post');
            echo wp_kses(stripslashes_deep($content), $allowed_tags);
	        //   echo $content;
				?>
			</section>
</div>
</div>
			<?php
			wpsp_body_end();
			wpsp_footer();
		}
	}else{
		include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
	}
?>
