<?php if (!defined( 'ABSPATH' ) )
exit('No Such File');
$subjectid=intval($_GET['id']);
$teacher_table=	$wpdb->prefix."wpsp_teacher";
$classnumber =	$wpdb->prefix."wpsp_class";
$teacher_data = $wpdb->get_results("select * from $teacher_table");
$subtable=$wpdb->prefix."wpsp_subject";
$wpsp_subjects =$wpdb->get_results("select * from $subtable where id='".esc_sql($subjectid)."'");
foreach ($wpsp_subjects as $subject_data) {
	$subid = intval($subject_data->id);
	$classid = intval($subject_data->class_id);
	$subname = $subject_data->sub_name;
	$subcode = $subject_data->sub_code;
	$subteacherid = $subject_data->sub_teach_id;
	$subbookname = $subject_data->book_name;
}
?>
<!-- This form is used for Edit Subject Details -->
<div class="formresponse"></div>
<form action="" name="SubjectEditForm"  id="SEditForm" method="post">
<?php wp_nonce_field( 'SubjectRegister', 'subregister_nonce', '', true ); ?>
	<div class="wpsp-col-xs-12">
		<div class="wpsp-card">
			<div class="wpsp-card-head">
				<h3 class="wpsp-card-title"><?php esc_html_e( 'Edit Subject Details', 'wpschoolpress' ); ?></h3>
			</div>
			<div class="wpsp-card-body">
				<div class="wpsp-col-md-12 line_box">
					<div class="wpsp-row">
                    <?php $wpsp_class =$wpdb->get_results("select c_name from $classnumber where cid='".esc_sql($classid)."'");
                    ?>
                <label class="wpsp-labelMain" for="Name"><?php echo esc_html("Class Name","wpschoolpress");?>: <?php if(!empty($wpsp_class)){ echo esc_html($wpsp_class[0]->c_name); }else{
                    $wpsp_class[0]->c_name = '';
                };?></label>
                </div></div></div>
			<input type="hidden" name="cid" value="<?php echo esc_attr($subid);?>">
			<div class="wpsp-card-body">
				<div class="wpsp-col-md-12 line_box">
					<div class="wpsp-row">
					<?php  do_action('wpsp_before_subject_detail_fields'); ?>
						<div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
							<div class="wpsp-form-group">
								<label class="wpsp-label" for="Name"><?php esc_html_e("Subject","wpschoolpress");?> <span class="wpsp-required"> *</span></label>
								<input type="text"   class="wpsp-form-control" ID="EditSName" name="EditSName" placeholder="Subject Name" value="<?php echo esc_attr($subname);?>">
								<input type="hidden" class="wpsp-form-control" value="<?php echo esc_attr($subid);?>" id="SRowID" name="SRowID">
								<input type="hidden" class="wpsp-form-control" value="" id="ESClassID" name="ClassID">
								<input type="hidden" id="wpsp_locationginal1" value="<?php echo esc_attr(admin_url());?>"/>
							</div>
						</div>
						<div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
							<div class="wpsp-form-group">
								<label class="wpsp-label" for="Name"><?php esc_html_e("Subject Code","wpschoolpress");?></label>
								<input type="text" class="wpsp-form-control" ID="EditSCode" name="EditSCode" placeholder="Subject Code" value="<?php echo esc_attr($subcode);?>"></div>
							</div>
							<div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">							<div class="wpsp-form-group">
								<label class="wpsp-label" for="Name"><?php esc_html_e("Subject Teacher (Incharge)","wpschoolpress");?></label>
								<select name="EditSTeacherID" id="EditSTeacherID" class="wpsp-form-control">
									<option value=""><?php echo esc_html("Select Teacher","wpschoolpress");?> </option>
									<?php foreach ($teacher_data as $teacher_list) {
                                        $teacherlistid= intval($teacher_list->wp_usr_id);?>
                                        <option value="<?php echo esc_attr($teacherlistid);?>"
										<?php if($teacherlistid == $subteacherid) echo esc_html("selected","wpschoolpress"); ?> >
										<?php echo esc_html($teacher_list->first_name ." ". $teacher_list->last_name);?>

										</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                <div class="wpsp-form-group">
								<label class="wpsp-label" for="BName"><?php esc_html_e("Book Name","wpschoolpress");?></label>
								<input type="text" class="wpsp-form-control" name="EditBName" id="EditBName" placeholder="Book Name" value="<?php echo esc_attr($subbookname);?>">
							</div>
						</div>
						<?php  do_action('wpsp_after_subject_detail_fields'); ?>
					</div>
				</div>
				<div class="wpsp-col-md-12">
					<input type="submit" id="SEditSave" class="wpsp-btn wpsp-btn-success" value="Update">
                    <a href="<?php echo esc_url(wpsp_admin_url().'sch-subject')?>" class="wpsp-btn wpsp-dark-btn" ><?php echo esc_html("Back","wpschoolpress");?></a>
				</div>
			</div>
		</div>
	</div>
</form><!-- End of Edit Subject Details Form -->