<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wpdb;
		$current_user_role=$current_user->roles[0];
		if( $current_user_role=='administrator' || $current_user_role=='teacher')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			$filename	=	'';
			$header	=	'Classes';
			if( isset($_GET['tab'] ) && sanitize_text_field($_GET['tab']) == 'addclass' ) {
				if($current_user_role=='administrator'){
				$header	=	$label	=	__( 'Add New Class', 'wpschoolpress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-classForm.php';
				}
			}elseif((isset($_GET['id']) && is_numeric($_GET['id'])))  {
				if($current_user_role=='administrator'){
				$header	=	$label	=	__( 'Update Class', 'wpschoolpress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-classForm.php';
				}
			}
		?>
		<?php
		if( !empty( $filename) ) {
			include_once ( $filename );
		} else {
		?>
		<div class="wpsp-card">
			<div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php esc_html_e( 'Class List', 'wpschoolpress' )?></h3>
				<?php  if( $current_user_role=='administrator' ) { ?>

				<?php } ?>
            </div>
			<div class="wpsp-card-body">
				<table class="wpsp-table" id="class_table" cellspacing="0" width="100%" style="width:100%">
					<thead>
					<tr>
						<th class="nosort">#</th>
						<th><?php esc_html_e( 'Class Number', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Class Name', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Teacher Incharge', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Number of Students', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Capacity', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Location', 'wpschoolpress' ); ?></th>
						<?php  if( $current_user_role=='administrator' ) { ?> <th class="nosort" align="center"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th> <?php } ?>
					</tr>
					</thead>
					<tbody>
									<?php
									$ctable=$wpdb->prefix."wpsp_class";
									$stable=$wpdb->prefix."wpsp_student";
									$wpsp_classes =$wpdb->get_results("select * from $ctable order by cid DESC");
									$sno=1;
									$teacher_table=	$wpdb->prefix."wpsp_teacher";
									$teacher_data = $wpdb->get_results("select wp_usr_id,CONCAT_WS(' ', first_name, middle_name, last_name ) AS full_name from $teacher_table order by tid");
									$teacherlist	=	array();
									if( !empty( $teacher_data ) ) {
										foreach( $teacher_data  as $value )
											$teacherlist[$value->wp_usr_id] = $value->full_name;
									}

									foreach ($wpsp_classes as $wpsp_class) {
										$cid=intval($wpsp_class->cid);


										$studentlists	=	$wpdb->get_results("select class_id, sid from $stable");
										$stl = [];
										foreach ($studentlists as $stu) {
											if(is_numeric($stu->class_id) ){
												if($stu->class_id == $cid){
												 $stl[] = $stu->sid;
											 }
											}
											else{
												 $class_id_array = unserialize( $stu->class_id );
												// print_r($class_id_array);
												 if(!empty($class_id_array)){
												 if(in_array($cid, $class_id_array)){
													 $stl[] = $stu->sid;
												 }
												}
											}
										}
										$class_students_count = count($stl);

										$teach_id= intval($wpsp_class->teacher_id);
										$teachername	=	'';
									?>
										<tr id="<?php echo esc_attr($wpsp_class->cid);?>" class="pointer">
											<td><?php echo esc_html($sno);?><td><?php echo esc_html($wpsp_class->c_numb);?> </td>
											<td><?php echo esc_html($wpsp_class->c_name);?></td>
											<td><?php echo isset( $teacherlist[$teach_id] ) ? esc_html($teacherlist[$teach_id]) : '';?></td>
											<td><?php echo esc_html($class_students_count);?></td>
											<td><?php echo esc_html($wpsp_class->c_capacity);?></td>
											<td><?php echo esc_html($wpsp_class->c_loc);?></td>
											<?php  if( $current_user_role=='administrator' ) { ?>
												<td align="center">
													<div class="wpsp-action-col">
													<a href="<?php echo esc_url(wpsp_admin_url().'sch-class&id='.esc_attr($wpsp_class->cid)."&edit=true");?>" title="Edit">
													<i class="icon dashicons dashicons-edit wpsp-edit-icon"></i></a>

													<a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo esc_attr($wpsp_class->cid);?>" >
	                                				<i class="icon dashicons dashicons-trash wpsp-delete-icon" data-id="<?php echo esc_attr($wpsp_class->cid);?>"></i>
	                                				</a>
	                                				</div>

												</td>
											<?php } ?>
										</tr>
									<?php
										$sno++;
									}
									?>
								</tbody>
				</table>
			</div>
		</div>

		<?php  } if( $current_user_role=='administrator' ) { ?>

		<?php  } ?>
		<?php
			//include_once ( $filename );
			wpsp_body_end();
			wpsp_footer();
		}
		else if($current_user_role=='parent' || $current_user_role='student')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			?>

				<div class="wpsp-row">
					<div class="wpsp-col-md-12">
						<div class="wpsp-card">
						<div class="wpsp-card-head ui-sortable-handle">
                                    <h3 class="wpsp-card-title"><?php esc_html_e( 'Classe Details', 'wpschoolpress' )?> </h3>
                                </div>
							<div class="wpsp-card-body">

								<table id="class_table" class="wpsp-table wpsp-table-bordered wpsp-table-striped" cellspacing="0" width="100%" style="width:100%">
									<thead>
									<tr>
										<th class="nosort">#</th>
										<th><?php esc_html_e( 'Class Number', 'wpschoolpress' ); ?></th>
										<th><?php esc_html_e( 'Class Name', 'wpschoolpress' ); ?></th>
										<th><?php esc_html_e( 'Teacher Incharge', 'wpschoolpress' ); ?></th>
										<th><?php esc_html_e( 'Number of Students', 'wpschoolpress' ); ?></th>
										<th><?php esc_html_e( 'Location', 'wpschoolpress' ); ?></th>
									</tr>
									</thead>
									<tbody>
									<?php
									$ctable=$wpdb->prefix."wpsp_class";
									$stable=$wpdb->prefix."wpsp_student";
									$teacher_table=	$wpdb->prefix."wpsp_teacher";
									$teacher_data = $wpdb->get_results("select wp_usr_id,CONCAT_WS(' ', first_name, middle_name, last_name ) AS full_name from $teacher_table order by tid");
									$teacherlist	=	array();
									if( !empty( $teacher_data ) ) {
										foreach( $teacher_data  as $value )
											$teacherlist[$value->wp_usr_id] = $value->full_name;
									}
									if( $current_user_role=='student' ) {
										$wpsp_classes =$wpdb->get_results("SELECT cls.* FROM $ctable cls, $stable st where st.wp_usr_id = '$current_user->ID' AND st.class_id=cls.cid");
									} else {
										$wpsp_classes =$wpdb->get_results("SELECT DISTINCT cls.* FROM $ctable cls, $stable st where st.parent_wp_usr_id = '$current_user->ID' AND st.class_id=cls.cid");
									}
									$sno=1;
									foreach ($wpsp_classes as $wpsp_class)
									{
										$cid = intval($wpsp_class->cid);
										$class_students_count = $wpdb->get_var( "SELECT COUNT(`wp_usr_id`) FROM $stable WHERE class_id = '".esc_sql($cid)."'" );
										$teach_id= intval($wpsp_class->teacher_id);
										$teacher=get_userdata($teach_id);
										?>
										<tr id="<?php echo  esc_attr($wpsp_class->cid);?>" class="pointer">
											<td><?php echo esc_html($sno);?><td><?php echo  esc_html($wpsp_class->c_numb);?> </td>
											<td><?php echo esc_html($wpsp_class->c_name);?></td>
										    <td><?php echo isset( $teacherlist[$teach_id] ) ? esc_html($teacherlist[$teach_id]) : '';?></td>
											<td><?php echo esc_html($class_students_count);?></td>
											<td><?php echo esc_html($wpsp_class->c_loc);?></td>
										</tr>
										<?php
										$sno++;
									}
									?>
									</tbody>
								</table>
								</div>
							</div>
						</div>
					</div>

			<?php
			wpsp_body_end();
			wpsp_footer();
		}
	}
	else{

		include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
	}
?>
