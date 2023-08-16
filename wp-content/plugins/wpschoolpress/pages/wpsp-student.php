<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wp_roles, $wpdb;
			$current_user_role=$current_user->roles[0];
		if($current_user_role=='administrator' || $current_user_role=='teacher') {
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-studentList.php';
			if( isset( $_GET['tab'] ) && $_GET['tab'] == 'addstudent' ) {
				if($current_user_role=='administrator'){
				$label	=	__( 'Add New Student', 'wpschoolpress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-studentForm.php';
				}
			} else if( isset($_GET['id']) && is_numeric($_GET['id']) ) {
				if($current_user_role=='administrator'){
				$label	=	__( 'Update Student', 'wpschoolpress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-studentProfile.php';
				}
			} else if( isset( $_POST['ClassID'] ) && empty( $_POST['ClassID'] ) ) {
				 $label	=	esc_html('List Of Unassigned Students','wpschoolpress');
			} else if( isset( $_POST['ClassID']  ) && $_POST['ClassID']=='all' ) {
				 $label	=	esc_html('List Of All Students','wpschoolpress');
			}
			else if( isset( $_POST['ClassID'] ) && !empty( $_POST['ClassID'] ) ){
				 $where =    ' where  cid='.esc_sql($_POST['ClassID']);
                $class_table    =    $wpdb->prefix."wpsp_class";
                $sel_class        =    $wpdb->get_var("select c_name from $class_table $where Order By cid ASC");
                $label    =    'List of class ' .esc_html($sel_class). ' Students';
			}
            else  {
                 $label    =    esc_html('List Of All Students','wpschoolpress');
            }
			?>
			<?php
			include_once ( $filename );
			if(isset($_GET['tab'])){
				if($_GET['tab'] != 'addstudent' ) {
					do_action('wpsp_student_import_html');
				}
			}
			wpsp_body_end();
			wpsp_footer();
	}else if($current_user_role=='parent') {
		/* Parents can View their children's class students */
		wpsp_topbar();
		wpsp_sidebar();
		wpsp_body_start();
		global $wpdb;
		$parent_id		=	intval($current_user->ID);
		$student_table	=	$wpdb->prefix."wpsp_student";
		$class_table	=	$wpdb->prefix."wpsp_class";
		$users_table 	= 	$wpdb->prefix."users";
		$students		=	$wpdb->get_results("select st.wp_usr_id, st.class_id, st.s_fname AS full_name,cl.c_name from $student_table st LEFT JOIN $class_table cl ON cl.cid=st.class_id where st.parent_wp_usr_id='".esc_sql($parent_id)."'");
		$child=array();
		foreach($students as $childinfo){
			$child[]=array('student_id'=>$childinfo->wp_usr_id,'name'=>$childinfo->full_name,'class_id'=>$childinfo->class_id,'class_name'=>$childinfo->c_name);
		}
		?>
			<?php if( count( $child ) > 0 ) { ?>
			<div class="wpsp-card">
			<div class="wpsp-card-body">
			<div class="tabbable-line">
				<div class="tabSec wpsp-nav-tabs-custom" id="verticalTab">
				<div class="tabList">
				<ul class="wpsp-resp-tabs-list">
					<?php $i=0; foreach($child as $ch) { ?>
						<li class="wpsp-tabing <?php echo ($i==0)?'active':''?>"><!-- <a href="#<?php echo str_replace(' ', '', $ch['name'].$i );?>"  data-toggle="tab"> --><?php echo $ch['name'];?><!-- </a> --></li>
					<?php $i++; } ?>
				</ul>
				</div>
				<div class="wpsp-tabBody wpsp-resp-tabs-container">
				<!-- <div class="wpsp-tabMain"> -->
					<?php
					$i=0;
					foreach($child as $ch) {
						$ch_class=$ch['class_id'];
						?>
					<div class="tab-pane wpsp-tabMain <?php echo ($i==0)?' active':''?>" id="<?php echo esc_attr(str_replace(' ', '', $ch['name'].$i ));?>">
						<?php // echo $ch['class_name'];?></caption>
						<div class="studentProfile">
						<?php
						$sid=sanitize_text_field($ch['student_id']);
						$stinfo=$wpdb->get_row("select a.*,b.c_name,CONCAT_WS(' ', a.s_fname, a.s_mname, a.s_lname ) AS full_name,d.user_email from $student_table a LEFT JOIN $class_table b ON a.class_id=b.cid LEFT JOIN $users_table d ON d.ID=a.wp_usr_id where a.wp_usr_id='".esc_sql($sid)."'");

						if (is_numeric($stinfo->class_id)){
							$classIDArray[] = $stinfo->class_id;
						}else{
							$classIDArray = unserialize($stinfo->class_id);
						}

						$classname_array = [];
                        $classIDArray_Sanitiz = array_map('intval',$classIDArray);
						foreach ($classIDArray_Sanitiz as $id ) {
                            $id = esc_sql($id);
							$clasname = $wpdb->get_var("SELECT c_name FROM $class_table where cid='$id'");
							$classname_array[] = $clasname;
						}


						if(!empty($stinfo)) {
						?>
							<div class="wpsp-row">
								<div class="wpsp-col-xs-12 wpsp-col-sm-12 wpsp-col-md-12 wpsp-col-lg-12">
									<div class="wpsp-panel wpsp-panel-info">

												<div class="wpsp-row">
													<div class="wpsp-col-md-3 wpsp-col-lg-2">
														<?php
														$loc_avatar=get_user_meta($sid,'simple_local_avatar',true);
														$img_url= $loc_avatar ? sanitize_text_field($loc_avatar['full']) : WPSP_PLUGIN_URL.'img/avatar.png';
														?>
														<img src="<?php echo esc_url($img_url);?>" height="150px" width="150px" class="img wpsp-img-circle"/>
													</div>
													<div class="wpsp-col-md-9 wpsp-col-lg-10">
														<br>
														<h3 class="wpsp-card-title"><?php echo esc_html($stinfo->full_name); ?></h3>
														<div class="wpsp-table-responsive">
														<table id="studentchildInfo" class="wpsp-table table-user-information" cellspacing="0" width="100%" style="width:100%">
															<tbody>
															<tr>
																<td class="bold" width="200px"><?php _e( 'Roll No.', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->s_rollno);	?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Class', 'wpschoolpress'); ?> </td>
																<td><?php echo esc_html(implode(", ",$classname_array));	?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Gender', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->s_gender);	?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Date of Birth', 'wpschoolpress' );?></td>
																<td><?php echo wpsp_ViewDate($stinfo->s_dob);	?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Date of Join', 'wpschoolpress'); ?></td>
																<td><?php echo wpsp_ViewDate($stinfo->s_doj);	?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Permanent Address', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->s_paddress); ?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Permanent Country', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->s_pcountry); ?></td>
															</tr><tr>
																<td class="bold"><?php _e( 'Permanent Zipcode', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->s_pzipcode); ?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Email', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->user_email); ?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Phone Number', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->s_phone); ?></td>
															</tr>
															<tr>
																<td class="bold"><?php _e( 'Blood Group', 'wpschoolpress'); ?></td>
																<td><?php echo esc_html($stinfo->s_bloodgrp); ?></td>
															</tr>
															</tbody>
														</table>
													</div>
													</div>
												</div>
											</div>
									</div>
							</div>
						</div>
					<?php } else {
				_e( 'Sorry! No data retrieved', 'wpschoolpress');
			}
			?>
					</div>
					<?php $i++; } ?>
				</div>
			</div>
			</div>
		</div>
	</div>
			<?php
			} else {
				 _e('No Child Added, Please contact teacher/admin to add your child');
			} ?>
		<?php
		wpsp_body_end();
		wpsp_footer();
	} else if($current_user_role=='student') {
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			global $wpdb;
			$student_id=intval($current_user->ID);
			$student_table=$wpdb->prefix."wpsp_student";
			$class_table=$wpdb->prefix."wpsp_class";

      $student=$wpdb->get_row("select st.class_id,  CONCAT_WS(' ', st.s_fname, st.s_mname, st.s_lname ) AS full_name,cl.c_name from $student_table st LEFT JOIN $class_table cl ON cl.cid=st.class_id where st.wp_usr_id='".esc_sql($student_id)."'");

			?>
		<div class="wpsp-card">
			<div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php
					$st_class=$student->class_id;
					if( isset( $student->c_name ) && !empty( $student->c_name ) )
						_e( 'Your Current Class is '.esc_html($student->c_name), 'wpschoolpress' );
					?> </h3>
            </div>
			<div class="wpsp-card-body">
				<table class="wpsp-table">
					<thead>
					<tr>
						<th>#</th>
						<th><?php echo apply_filters( 'wpsp_student_table_rollno_heading',esc_html__('Roll No.','wpschoolpress'));?></th>
						<th><?php echo apply_filters( 'wpsp_student_table_fullname_heading',esc_html__('Student Name','wpschoolpress'));?></th>
						<th><?php echo apply_filters( 'wpsp_student_table_parent_heading',esc_html__('Parent Name','wpschoolpress'));?></th>
						<th><?php echo apply_filters( 'wpsp_student_table_streetaddress_heading',esc_html__('Permanent Address','wpschoolpress'));?></th>
					</tr>
					</thead>
					<tbody>
					<?php
				            $class_id = base64_decode(sanitize_text_field(stripslashes($_GET['cid'])));

							$cl_students=$wpdb->get_results("select wp_usr_id, class_id, CONCAT_WS(' ', s_fname, s_mname, s_lname ) AS full_name,parent_wp_usr_id, CONCAT_WS(' ', p_fname, p_mname, p_lname ) AS p_full_name, CONCAT_WS(' ', s_paddress, s_pcity, s_pcountry ) AS peraddress, s_rollno from $student_table");

					$sno=1;
					$jsonArray=[];
					foreach($cl_students as $cl_st) {
						$jsonArray=[];
                        $jsondata = $cl_st->class_id;
                        if (is_numeric($jsondata)){
                            $jsonArray[] = $jsondata;
                        }else{
                            $jsonArray = unserialize($jsondata);

                            if(!empty($jsonArray)){
                            $jsonvalue1 = in_array($class_id, $jsonArray);
                        }
                        }

                        if(!empty($jsonvalue1)){
						?>
						<tr>
							<td><?php echo esc_html($sno);?></td>
							<td><?php echo esc_html($cl_st->s_rollno);?></td>
							<td><?php echo esc_html($cl_st->full_name);?></td>
							<td><?php echo esc_html($cl_st->p_full_name);?></a>
							<!--<td><a href="javascript:;" class="ViewParent" data-id="<?php echo esc_attr($cl_st->parent_wp_usr_id);?>"><?php echo esc_html($cl_st->p_full_name);?></a></td>-->
							<td><?php echo esc_html($cl_st->peraddress);?>&nbsp;</td>
						</tr>
						<?php
					}
						$sno++;
					}

					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		wpsp_body_end();
		wpsp_footer();
	}
	?>
	<div class="wpsp-popupMain" id="ViewModal">
	  <div class="wpsp-overlayer"></div>
	  <div class="wpsp-popBody">
		<div class="wpsp-popInner">
			<a href="javascript:;" class="wpsp-closePopup"></a>
			<div id="ViewModalContent"></div>
		</div>
	  </div>
	</div>
	<?php
	}
	else {
		include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');//Include login
	}
?>
