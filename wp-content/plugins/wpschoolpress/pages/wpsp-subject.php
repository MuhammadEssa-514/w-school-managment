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
			$class_table	=	$wpdb->prefix."wpsp_class";
			$classQuery		=	"select cid,c_name from $class_table Order By cid ASC";
			$msg			=	'Please Add Class Before Adding Subjects';

			$sel_class		=	$wpdb->get_results( $classQuery );
			if(( isset($_GET['classid']) && is_numeric($_GET['classid']))) {
				if($current_user_role=='administrator'){
				$label	=	__( 'Add New Class', 'wpschoolpress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-subjectForm.php';
				}
				include_once ( $filename );
			}elseif(( isset($_GET['id']) && is_numeric($_GET['id']))) {
				if($current_user_role=='administrator'){
				$label	=	__( 'Edit Class', 'wpschoolpress');
				$filename	=	WPSP_PLUGIN_PATH .'includes/wpsp-editsubjectForm.php';
				include_once ( $filename );
				}
			}else{
				if($sel_class[0]->c_name != ''){
				 $sel_classname	=	$sel_class[0]->c_name;
				 //echo $sel_classid	=	$sel_class[0]->cid;
				 if( isset( $_POST['ClassID'] ) && !empty (sanitize_text_field($_POST['ClassID']) ) ) {
					 $sel_classid	= intval($_POST['ClassID']);
					 foreach( $sel_class as $key=>$value ) {
						if( $value->cid	==	$sel_classid ) {
							$sel_classname	=	$value->c_name;
							break;
						}
					 }
				 }
			 }else{
				 $sel_classname = '';
			 }
		?>
		<div class="wpsp-card">
			<?php if( empty( $sel_class ) ) { echo '<div class="alert alert-danger col-lg-2">'.esc_html($msg).'</div>'; } else { ?>
			<div class="wpsp-card-head">
				<div class="subject-inner wpsp-left wpsp-class-filter">
						<form action="" id="SubjectList-Form" name="SubjectList-Form" method="POST">
							<label class="wpsp-labelMain"><?php esc_html_e( 'Select Class Name', 'wpschoolpress' ); ?> *</label>
							<select name="ClassID" id="ClassID" class="wpsp-form-control">
								<?php if($sel_classid == ''){$sel_classid = 'all';} ?>
								<option value="all" <?php if($sel_classid == 'all') echo esc_html("selected","wpschoolpress"); ?>><?php _e( 'All', 'wpschoolpress' ); ?></option>
								<?php
								foreach($sel_class as $classes) { ?>
									<option value="<?php echo esc_attr(intval($classes->cid));?>" <?php if($sel_classid == $classes->cid) echo esc_html("selected","wpschoolpress"); ?>><?php echo esc_html($classes->c_name);?></option>
								<?php } ?>
							</select>
						</form>
					</div>
				</div>
			<div class="wpsp-card-body">
				<table id="subject_table" class="wpsp-table subjectdataTable" cellspacing="0" width="100%" style="width:100%">
					<thead>
						<tr>
							<th class="nosort">#</th>
							<th><?php esc_html_e( 'Subject Code', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Subject Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Faculty', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Book Name', 'wpschoolpress' ); ?></th>
							<?php if( $current_user_role=='administrator') { ?>
							<th class="nosort" ><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
						<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php
						$teacher_table=	$wpdb->prefix."wpsp_teacher";
						$teacher_data = $wpdb->get_results("select wp_usr_id,CONCAT_WS(' ', first_name, last_name ) AS full_name from $teacher_table order by tid");
						$teacherlist	=	array();
						if( !empty( $teacher_data ) ) {
							foreach( $teacher_data  as $value )
								$teacherlist[$value->wp_usr_id] = $value->full_name;
						}
						$subtable=$wpdb->prefix."wpsp_subject";
						$class_id='';
									if( isset($_POST['ClassID'] ) ) {
										$class_id=sanitize_text_field($_POST['ClassID']);
									}else if( !empty( $sel_class ) ) {
										$class_id = 'all';
									}
									$classquery	=	"where class_id='".esc_sql($class_id)."'";
									if($class_id=='NULL'){
										$classquery	=	"";
									}elseif($class_id=='all'){
										$classquery="";
									}
						$wpsp_subjects =$wpdb->get_results("select * from $subtable $classquery order by sub_code desc");
						$sno=1;
					if(!empty($wpsp_subjects)){
						foreach ($wpsp_subjects as $wpsp_subject)
						{
							$teach_id= (int)$wpsp_subject->sub_teach_id;
							$teacher=get_userdata($teach_id);
						?>
							<tr id="<?php echo esc_html($wpsp_subject->id);?>" class="pointer">
								<td><?php echo esc_html($sno);?></td>
								<td><?php echo !empty( $wpsp_subject->sub_code ) ? esc_html($wpsp_subject->sub_code) :'-';	?></td>
								<td><?php echo  esc_html($wpsp_subject->sub_name);?></td>
								<td><?php echo isset( $teacherlist[$teach_id] ) ? esc_html($teacherlist[$teach_id]) : '';?></td>
								<td><?php echo esc_html($wpsp_subject->book_name);?></td>
								<?php if( $current_user_role=='administrator') { ?>
								<td >
									<div class="wpsp-action-col">
									    <?php if($wpsp_subject->id!=1){?>
										 <a href="<?php echo esc_url(wpsp_admin_url().'sch-subject&id='.esc_attr(intval ($wpsp_subject->id)).'&edit=true');?>"><i class="icon wpsp-edit wpsp-edit-icon"></i></a>
										<?php } ?>
										<!-- <a href="<//?php echo esc_url(wpsp_admin_url().'sch-subject&id='.esc_attr(intval ($wpsp_subject->id)).'&edit=true');?>"><i class="icon wpsp-edit wpsp-edit-icon"></i></a> -->
										<a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo esc_attr(intval($wpsp_subject->id));?>" >
											<i class="icon wpsp-trash wpsp-delete-icon" data-id="<?php echo esc_attr(intval($wpsp_subject->id));?>"></i>
										</a>
									</div>
								</td>
							<?php } ?>
							</tr>
						<?php
							$sno++;
						}
					}
						?>
					</tbody>
					<tfoot>
					  	<tr>
							<th class="nosort">#</th>
							<th><?php esc_html_e( 'Subject Code', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Subject Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Faculty', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Book Name', 'wpschoolpress' ); ?></th>
							<?php if( $current_user_role=='administrator') { ?>
							<th class="nosort" ><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
						<?php } ?>
						</tr>
					</tfoot>
				</table>
			</div>
		<?php } ?>
	</div>
<?php }?>
<?php if($current_user_role=='administrator'){?>
		<div class="modal fade" id="InfoModal" tabindex="-1" role="dialog" aria-labelledby="InfoModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="col-md-12">
						<div class="box box-success">
							<div class="box-header">
								<h3 class="box-title" id="InfoModalTitle"></h3>
							</div><!-- /.box-header -->
							<div id="InfoModalBody" class="box-body">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.modal -->
		<?php }
			wpsp_body_end();
			wpsp_footer();
		}
		else if($current_user_role=='parent')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			$parent_id=intval($current_user->ID);
            $ciid = sanitize_text_field(stripslashes($_GET['cid']));
			$class_id = sanitize_text_field(base64_decode($ciid));
			$student_table=$wpdb->prefix."wpsp_student";
			$class_table=$wpdb->prefix."wpsp_class";
			$subject_table=$wpdb->prefix."wpsp_subject";
			$students=$wpdb->get_results("select st.wp_usr_id, st.class_id, st.s_fname, st.sid, CONCAT_WS(' ', st.s_fname, st.s_mname, st.s_lname ) AS full_name,cl.c_name from $student_table st LEFT JOIN $class_table cl ON cl.cid='".esc_sql($class_id)."'  where st.parent_wp_usr_id='".esc_sql($parent_id)."'");
			$child=array();
			foreach($students as $childinfo){
				$child[]=array('student_id'=>$childinfo->wp_usr_id,'fname'=>$childinfo->s_fname,'name'=>$childinfo->full_name,'class_id'=>$childinfo->class_id,'class_name'=>$childinfo->c_name, 'sid'=>$childinfo->sid);
			}
			?>
						<div class="wpsp-card">
							<div class="wpsp-card-body">
								<div class="tabbable-line">
									<div class="tabSec wpsp-nav-tabs-custom" id="verticalTab">
										<div class="tabList">
										<ul class="wpsp-resp-tabs-list">
										<?php $i=0; foreach($child as $ch) {
											if(base64_decode(sanitize_text_field($_GET['sid'])) == $ch['sid']){ ?>
											<li class="wpsp-tabing <?php echo ($i==0)?'active':''?>">
												<?php echo esc_html($ch['name']);?>
											</li>
											<?php } $i++; } ?>
										</ul>
										</div>
									<div class="wpsp-tabBody wpsp-resp-tabs-container">
										<?php
										$teacher_table=	$wpdb->prefix."wpsp_teacher";
									$teacher_data = $wpdb->get_results("select wp_usr_id,CONCAT_WS(' ', first_name, last_name ) AS full_name from $teacher_table order by tid");
									$teacherlist	=	array();
									if( !empty( $teacher_data ) ) {
										foreach( $teacher_data  as $value )
											$teacherlist[$value->wp_usr_id] = $value->full_name;
									}
										$i=0;
										foreach($child as $ch) {
											$ch_class=$ch['class_id'];
											?>
											<div class="tab-pane wpsp-tabMain <?php echo ($i==0)?'active':''?>" id="<?php echo str_replace(" ", "",$ch['fname'].$i);?>">
												<caption><label class="wpsp-labelMain"> <?php esc_html_e( 'Class Name', 'wpschoolpress' ); ?> : </label> <?php echo esc_html($ch['class_name']);?></caption>					<div class="wpsp-table-responsive">
												<table id="subject_table<?php echo $i++; ?>" class="wpsp-table subjectdataTable"  cellspacing="0" width="100%" style="width:100%">
													<thead>
													<tr>
														<th>#</th>
														<th><?php esc_html_e( 'Subject Code', 'wpschoolpress' ); ?></th>
														<th><?php esc_html_e( 'Subject Name', 'wpschoolpress' ); ?></th>
														<th><?php esc_html_e( 'Faculty', 'wpschoolpress' ); ?></th>
														<th><?php esc_html_e( 'Book Name', 'wpschoolpress' ); ?></th>
													</tr>
													</thead>
													<tbody>
													<?php
                                                    $class_id = sanitize_text_field($class_id);
													$cl_subjects=$wpdb->get_results("select * from $subject_table where class_id='".esc_sql($class_id)."'");
													$sno=1;
													foreach($cl_subjects as $cl_sub){
													$teach_id= (int)$cl_sub->sub_teach_id;
													$teacher=get_userdata($teach_id);
													?>
													<tr id="<?php echo esc_attr(intval($cl_sub->id));?>" class="pointer">
														<td><?php echo esc_html($sno);?></td>
														<td><?php echo !empty( $cl_sub->sub_code ) ? esc_html($cl_sub->sub_code) : '-' ; ?></td>
														<td><?php echo  esc_html($cl_sub->sub_name);?></td>
														<td><?php echo isset( $teacherlist[$teach_id] ) ? esc_html($teacherlist[$teach_id]) : '';?></td>
														<td><?php echo esc_html($cl_sub->book_name);?></td>
													</tr>
														<?php
														$sno++;
													}
													?>
													</tbody>
												</table>
											</div>
										</div>
											<?php $i++; } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
			<?php
			wpsp_body_end();
			wpsp_footer();
		}else if($current_user_role=='student')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
            $ciid = sanitize_text_field(stripslashes($_GET['cid']));
			$class_id = sanitize_text_field(base64_decode($ciid));
			$student_id=sanitize_text_field($current_user->ID);
			$student_table=$wpdb->prefix."wpsp_student";
			$class_table=$wpdb->prefix."wpsp_class";
			$subject_table=$wpdb->prefix."wpsp_subject";
			$cl_subjects = $wpdb->get_results("select st.class_id,su.* from $student_table st LEFT JOIN $subject_table su ON su.class_id='".esc_sql($class_id)."' where st.wp_usr_id='".esc_sql($student_id)."'");
			?>
			<section class="wpsp-card">
				<div class="wpsp-card-head">
					<h3 class="wpsp-card-title"><?php esc_html_e( 'List of Subjects', 'wpschoolpress' )?> </h3>
				</div>
				<div class="wpsp-card-body">
				<table class="wpsp-table" id="listofsubjects" cellspacing="0" width="100%" style="width:100%">
					<thead>
					<tr>
						<th>#</th>
						<th><?php esc_html_e( 'Subject Code', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Subject Name', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Faculty', 'wpschoolpress' ); ?></th>
						<th><?php esc_html_e( 'Book Name', 'wpschoolpress' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						$teacher_table=	$wpdb->prefix."wpsp_teacher";
						$teacher_data = $wpdb->get_results("select wp_usr_id,CONCAT_WS(' ', first_name, last_name ) AS full_name from $teacher_table order by tid");
						$teacherlist	=	array();
						if( !empty( $teacher_data ) ) {
							foreach( $teacher_data  as $value )
							$teacherlist[$value->wp_usr_id] = $value->full_name;
						}
						$sno=1;
						foreach($cl_subjects as $cl_sub){
							$teach_id= (int)$cl_sub->sub_teach_id;
							//$teacher=get_userdata($teach_id);
							?>
							<tr id="<?php echo esc_attr(intval($cl_sub->id));?>" class="pointer">
								<td><?php echo esc_html($sno);?></td>
								<td><?php echo !empty( $cl_sub->sub_code ) ? esc_html($cl_sub->sub_code) : '-' ; ?></td>
								<td><?php echo  esc_html($cl_sub->sub_name);?> </td>
								<td><?php echo isset( $teacherlist[$teach_id] ) ? esc_html($teacherlist[$teach_id]) : '';?></td>
								<td><?php echo esc_html($cl_sub->book_name);?></td>
							</tr>
							<?php
							$sno++;
						}
						?>
					</tbody>
				</table>
			</div>
			</section>
			<?php
			wpsp_body_end();
			wpsp_footer();
		}
	}
	else{
		//Include Login Section
		include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');
	}
?>
