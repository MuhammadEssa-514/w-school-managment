<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');

 $proversion	=	wpsp_check_pro_version();

	  $proclass		=	!$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';

	  $protitle		=	!$proversion['status'] && isset( $proversion['message'] )? $proversion['message']	: '';

	  $prodisable	=	!$proversion['status'] ? 'disabled="disabled"'	: '';

	  $teacherFieldList =  array(	'empcode'			=>	__('Emp. Code', 'wpschoolpress'),

									'first_name'		=>	__('First Name', 'wpschoolpress'),

									'middle_name'		=>	__('Middle Name', 'wpschoolpress'),

									'last_name'			=>	__('Last Name', 'wpschoolpress'),

									'user_email'		=>	__('Teacher Email', 'wpschoolpress'),

									'zipcode'			=>	__('Zip Code', 'wpschoolpress'),

									'country'			=>	__('Country', 'wpschoolpress'),

									'gender'			=>	__('Gender', 'wpschoolpress'),

									'address'			=>	__('Address', 'wpschoolpress'),

									'dob'				=>	__('Date Of Birth', 'wpschoolpress'),

									'doj'				=>	__('Date Of Join', 'wpschoolpress'),

									'dol'				=>	__('Date Of Releaving', 'wpschoolpress'),

									'phone'				=>	__('Phone Number', 'wpschoolpress'),

									'qualification'	    =>	__('Qualification', 'wpschoolpress'),

									'gender'			=>	__('Gender', 'wpschoolpress'),

									'bloodgrp'			=>	__('Blood Group', 'wpschoolpress'),

									'position'			=>	__('Position', 'wpschoolpress'),

									'whours'			=>	__('Working Hours', 'wpschoolpress'),

							);



$teacher_table	=	$wpdb->prefix."wpsp_teacher";

$class_table	=	$wpdb->prefix."wpsp_class";

$subjects_table =	$wpdb->prefix."wpsp_subject";

$role			=	sanitize_text_field( $current_user->roles);

$sel_classid	=	isset( $_POST['ClassID'] ) ? intval($_POST['ClassID']) : '';

$sub_handling	=	$cincharge	=	$teacher	=	array();

$classquery		=	$teacherQuery	=	'';

if( !empty( $sel_classid ) && $sel_classid!='all' ){
	$classquery	=	" AND c.cid='".esc_sql($sel_classid)."' ";
}

$sub_han		=	$wpdb->get_results("select sub_name,sub_teach_id,c.c_name from $subjects_table s, $class_table c where sub_teach_id>0 AND c.cid=s.class_id $classquery order by c.cid");

foreach($sub_han as $subhan) {

	$sub_handling[$subhan->sub_teach_id][]=$subhan->sub_name.' ('.$subhan->c_name.')';

	$teacher[]	=	$subhan->sub_teach_id;

}



$incharges=$wpdb->get_results("select c.c_name,c.teacher_id from $class_table c LEFT JOIN $teacher_table t ON t.wp_usr_id=c.teacher_id where c.teacher_id>0 $classquery");

foreach($incharges as $incharge){

	$cincharge[$incharge->teacher_id][]=$incharge->c_name;

}

if( !empty( $teacher ) && !empty( $sel_classid ) && $sel_classid!='all' ) {

	$teacherQuery	=	'WHERE wp_usr_id IN ('.implode( "," , $teacher ).") AND";
}
else{
    $teacherQuery	=	'WHERE';
}


$teachers=$wpdb->get_results("select * from $teacher_table $teacherQuery  first_name != 'teacher' order by tid DESC");
// echo "<pre>";print_r($sel_classid);

$plugins_url=plugins_url();

?>

<div class="wpsp-card">

		<div class="wpsp-card-head">

            <div class="subject-inner wpsp-left wpsp-class-filter">

				<form name="TeacherClass" id="TeacherClass" method="post" action="">

					<label class="wpsp-labelMain"><?php _e( 'Select Class Name', 'wpschoolpress' ); ?></label>

					<select name="ClassID" id="ClassID" class="wpsp-form-control">

						<option value="all" <?php if($sel_classid=='all') echo esc_html("selected","wpschoolpress"); ?>><?php _e( 'All', 'wpschoolpress' ); ?></option>
						<?php

						$class_table	=	$wpdb->prefix."wpsp_class";

						$sel_class		=	$wpdb->get_results("select cid,c_name from $class_table Order By cid ASC");

						foreach( $sel_class as $classes ) {

						?>

						<option value="<?php echo esc_attr(intval($classes->cid));?>" <?php if($sel_classid==$classes->cid) echo esc_html("selected","wpschoolpress"); ?>><?php echo esc_html($classes->c_name);?></option>

						<?php } ?>

					</select>

				</form>

			</div>

			<div class="wpsp-right wpsp-import-export">

				<div class="wpsp-btn-lists" <?php echo esc_html($prodisable);?> title="<?php echo esc_attr($protitle);?>">

					<?php if ( $current_user_role=='teacher') {?>

					<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>

						<div class="wpsp-button-group wpsp-dropdownmain wpsp-left">
							<button type="button" class="wpsp-btn wpsp-btn-success print" id="PrintTeacher" data-toggle="dropdown" <?php echo esc_html($prodisable);?> title="<?php //echo esc_attr($protitle);?>">
								<i class="fa fa-print"></i> <?php _e( 'Print', 'wpschoolpress'); ?>
							</button>
							<button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo esc_html($prodisable);?>  title="<?php //echo $protitle;?>">
								<!-- <span class="sr-only"><?php _e( 'Toggle Dropdown', 'wpschoolpress' );?></span> -->
							</button>
							<div class="wpsp-dropdown wpsp-dropdown-md">

							<ul>

								<li class="wpsp-drop-title wpsp-checkList"><?php _e( 'Select Columns to Print', 'wpschoolpress' );?> </li>

								<form id="TeacherColumnForm" name="TeacherColumnForm" method="POST">

									<?php foreach( $teacherFieldList as $key=>$value ) { ?>

										<li class="wpsp-checkList" >

											<label class="wpsp-label" for="<?php echo "print".esc_attr($key); ?>">
                                            <input type="checkbox" name="TeacherColumn[]" value="<?php echo esc_attr($key); ?>" id="<?php echo "print".esc_attr($key); ?>" checked="checked"><?php echo esc_html($value); ?></label>

										</li>

									<?php } ?>

									<input type="hidden" name="classid" id="classid" value="<?php echo esc_attr($sel_classid);?>">

								</form>

							</ul>
						</div>
						</div>
					</div>
					<?php }?>

					<?php if ( $current_user_role=='administrator') {?>

					<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>

						<div class="wpsp-button-group wpsp-dropdownmain wpsp-left">

							<button type="button" class="wpsp-btn wpsp-btn-success print" id="PrintTeacher" data-toggle="dropdown" <?php echo esc_html($prodisable);?> title="<?php //echo esc_attr($protitle);?>">
								<i class="fa fa-print"></i> <?php _e( 'Print', 'wpschoolpress'); ?>
							</button>
							<button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo esc_html($prodisable);?>  title="<?php //echo esc_attr($protitle);?>">
								<!-- <span class="sr-only"><?php _e( 'Toggle Dropdown', 'wpschoolpress' );?></span> -->
							</button>
							<div class="wpsp-dropdown wpsp-dropdown-md">

							<ul>

								<li class="wpsp-drop-title wpsp-checkList"><?php _e( 'Select Columns to Print', 'wpschoolpress' );?> </li>

								<form id="TeacherColumnForm" name="TeacherColumnForm" method="POST">
									<?php foreach( $teacherFieldList as $key=>$value ) { ?>

										<li class="wpsp-checkList" >

											<label class="wpsp-label" for="<?php echo "print".esc_attr($key); ?>"><input type="checkbox" name="TeacherColumn[]" value="<?php echo esc_attr($key); ?>" id="<?php echo "print".esc_attr($key); ?>" checked="checked"><?php echo esc_html($value); ?></label>

										</li>

									<?php } ?>

									<input type="hidden" name="classid" id="classid" value="<?php echo esc_attr($sel_classid);?>">

								</form>

							</ul>

						</div>

						</div>
					</div>

					<?php }?>

					<?php if($current_user_role=='administrator') { ?>

						<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>
							<button id="ImportTeacher" class="wpsp-btn wpsp-dark-btn impt wpsp-popclick" <?php echo esc_html($prodisable);?>  title="<?php //echo esc_attr($protitle);?>" data-pop="ImportModal">
								<i class="fa fa-upload"></i> <?php echo esc_html("Import","wpschoolpress");?>
							</button>
						</div>
						<?php } ?>

					<?php if ( $current_user_role=='administrator') {?>

					<div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>

						<div class="wpsp-dropdownmain wpsp-button-group">

							<button type="button" class="wpsp-btn  print" id="ExportTeachers" <?php echo esc_html($prodisable);?> title="<?php //esc_attr(echo $protitle);?>">

								<i class="fa fa-download"></i> <?php _e( 'Export', 'wpschoolpress'); ?>

							</button>

							<button type="button" class="wpsp-btn wpsp-btn-blue wpsp-dropdown-toggle" <?php echo esc_html($prodisable);?> title="<?php //echo esc_attr($protitle);?>">
								<!-- <span class="caret"></span>
								<span class="sr-only"><?php _e( 'Toggle Dropdown', 'wpschoolpress' );?></span> -->
							</button>



							 <div id="exportcontainer" style="display:none;"></div>

							<div class="wpsp-dropdown wpsp-dropdown-md wpsp-dropdown-right">
								<ul >
									<li class="wpsp-drop-title wpsp-checkList"><?php _e( 'Select Columns to Export', 'wpschoolpress' );?> </li>
									<form id="ExportColumnForm" name="ExportTeacherColumn" method="POST">

										<?php foreach( $teacherFieldList as $key=>$value ) { ?>

										<li class="wpsp-checkList">

											<label class="wpsp-label" for="<?php echo esc_attr($key); ?>"> <input type="checkbox" name="TeacherColumn[]" value="<?php echo esc_attr($key); ?>" id="<?php echo "export".esc_attr($key); ?>" checked="checked"> <?php echo esc_html($value); ?></label>

										</li>

										<?php } ?>

										<?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?>

										<input type="hidden" name="exportteacher" value="exportteacher">

										<input type="hidden" name="classid" id="export-classid" value="<?php echo esc_attr($currentSelectClass);?>">

									</form>

								</ul>

							</div>

						</div>

					</div>

<?php } ?>

				</div>

			</div>

        </div>

	    <div class="wpsp-card-body">

	    	<?php if($current_user_role=='administrator') { ?>

	    		<div class="wpsp-bulkaction">

					<select name="bulkaction" class="wpsp-form-control" id="bulkaction">

					<option value=""><?php esc_html_e( 'Select Action', 'wpschoolpress' ); ?></option>

					<option value="bulkUsersDelete" id="d_teacher"><?php esc_html_e( 'Delete', 'wpschoolpress' ); ?></option>

					</select>

				</div>

			<?php } ?>

			<table id="teacher_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">

				<thead>

					<tr>

						<th class="nosort">

							<?php if($current_user_role=='administrator') { ?>

							<input type="checkbox" id="selectall" name="selectall" class="ccheckbox">

							<?php  } else if(  $current_user_role=='teacher' ) { ?>

								<?php _e( 'Sr. No.', 'wpschoolpress' ); ?>

							<?php } ?>

						</th>

						<th> <?php _e( 'Employee Code', 'wpschoolpress' );?></th>

						<th> <?php _e( 'Name', 'wpschoolpress' );?> </th>

						<th> <?php _e( 'Incharge Class', 'wpschoolpress' );?></th>

						<th> <?php _e( 'Subjects Handling', 'wpschoolpress' );?></th>

						<th> <?php _e( 'Phone', 'wpschoolpress' );?></th>

						<th align="center" class="nosort"><?php _e( 'Action', 'wpschoolpress' );?></th>

					</tr>

				</thead>

				<tbody>

					<?php

						foreach($teachers as $key=>$tinfo) { ?>
							<tr>
								<td>
									<?php if($current_user_role=='administrator') { ?>
									<input type="checkbox" class="ccheckbox tcrowselect" name="UID[]" value="<?php echo esc_attr($tinfo->wp_usr_id);?>">
									<?php } else if(  $current_user_role=='teacher' ) { echo esc_html($key+1); } ?>
								</td>

								<td><?php echo esc_html($tinfo->empcode); ?></td>

								<td><?php echo esc_html($tinfo->first_name." ".$tinfo->last_name);?></td>

								<td><?php if( isset( $cincharge[$tinfo->wp_usr_id] ) ) { echo esc_html(implode( ", ", $cincharge[$tinfo->wp_usr_id] )); } else { echo '-';} ?></td>

								<td><?php if( isset( $sub_handling[$tinfo->wp_usr_id] ) ) { echo wp_kses_post(implode( "<br> ", $sub_handling[$tinfo->wp_usr_id] )); } else { echo '-';} ?></td>

								<td><?php echo esc_html($tinfo->phone); ?></td>

								<td align="center">

									<div class="wpsp-action-col">

									<a href="<?php echo "?id=".esc_attr($tinfo->wp_usr_id);?>javascript:;" class="wpsp-popclick ViewTeacher"  data-id="<?php echo esc_attr($tinfo->wp_usr_id);?>" data-pop="ViewModal"><i class="icon dashicons dashicons-visibility wpsp-view-icon"></i></a>

									<?php if($current_user_role=='administrator') { ?>

										<a href="<?php echo esc_url(wpsp_admin_url().'sch-teacher&id='.esc_attr($tinfo->wp_usr_id)."&edit=true");?>" title="Edit">
											<i class="icon dashicons dashicons-edit wpsp-edit-icon"></i>
										</a>

										<a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo esc_attr($tinfo->tid);?>" >
											<i class="icon dashicons dashicons-trash wpsp-delete-icon" data-id="<?php echo esc_attr($tinfo->tid);?>"></i>
                                        </a>

									<?php } ?>

								</div>

								</td>

							</tr>

					<?php } ?>

				</tbody>

				<tfoot>

					<tr>

						<th><?php if(  $current_user_role=='teacher' ) { ?>
							<?php _e( 'Sr. No.', 'wpschoolpress' ); ?>
						<?php } ?>

						</th>

						<th><?php _e( 'Employee Code', 'wpschoolpress' );?></th>

						<th><?php _e( 'Name', 'wpschoolpress' );?> </th>

						<th><?php _e( 'Incharge Class', 'wpschoolpress' );?></th>

						<th><?php _e( 'Subjects Handling', 'wpschoolpress' );?></th>

						<th><?php _e( 'Phone', 'wpschoolpress' );?></th>

						<th align="center"><?php _e( 'Action', 'wpschoolpress' );?></th>

					</tr>

				</tfoot>

		 	</table>

		</div>

	</div><!-- /.box-body -->