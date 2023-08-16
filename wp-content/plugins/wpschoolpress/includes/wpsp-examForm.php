<?php if(!defined('ABSPATH')) exit;
$extable = $wpdb->prefix."wpsp_exam";
$examname = $examsdate = $examedate = $classid = $examid = '';
$subjectid = array();
if(isset($_GET['id'])){
	$examid = intval($_GET['id']);
	$wpsp_exams = $wpdb->get_results( "select * from $extable where eid='".esc_sql($examid)."'");
	foreach($wpsp_exams as $examdata){
	 $classid = $examdata->classid;
	$examname = $examdata->e_name;
	$examsdate = $examdata->e_s_date;
	$examedate = $examdata->e_e_date;
	$subjectid = explode( ",",$examdata->subject_id);
	}
}
$label = isset($_GET['id']) ? apply_filters( 'wpsp_exam_update_heading_item', esc_html__( 'Update Exam Information' , 'wpschoolpress' )): apply_filters( 'wpsp_exam_add_heading_item', esc_html__('Add Exam Information' , 'wpschoolpress' ));
$formname = isset($_GET['id']) ? 'ExamEditForm' : 'ExamEntryForm';
$buttonname = isset($_GET['id']) ? apply_filters( 'wpsp_exam_update_button_text', esc_html__( 'Update' , 'wpschoolpress' )) : apply_filters( 'wpsp_exam_submit_button_text', esc_html__('Submit' , 'wpschoolpress' ));
?>
<!-- This form is used for Add/Update New Exam Information -->
<div id="formresponse"></div>
<form name="<?php echo esc_attr($formname);?>" action="#"
	id="<?php echo esc_attr($formname);?>" method="post">
    <?php wp_nonce_field( 'ExamAction', 'wps_exam_nonce', '', true ) ?>
	<div class="wpsp-row">
	<div class="wpsp-col-xs-12">
		<div class="wpsp-card">
			<div class="wpsp-card-head">
				<h3 class="wpsp-card-title">
					<?php echo $label; ?>
				</h3>
			</div>
			<div class="wpsp-card-body">
				<div class="wpsp-row">
					<?php  do_action('wpsp_before_exam_fields');
            $is_required_item = apply_filters('wpsp_exam_fields_is_required',array());
           ?>
					<div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<input type="hidden"  id="wpsp_locationginal" value="<?php echo esc_url(admin_url());?>"/>
                            <?php
                            /*Check Required Field*/
                            if(isset($is_required_item['class_name'])){
                                $is_required =  esc_html($is_required_item['class_name'],"wpschoolpress");
                            }else{
                                $is_required = true;
                            }
                            ?>
								<label class="wpsp-label" for="Name"><?php esc_html_e("Class Name","wpschoolpress"); ?>
									<span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
									<?php if($current_user_role=='teacher') {} else {?>
								</label>
							<?php }?>
								<?php
								$classQuery	=	"select cid,c_name from $ctable";
								if($current_user_role=='teacher') {
								$cuserId		=	intval($current_user->ID);
								$classQuery		=	"select cid,c_name from $ctable where teacher_id='".esc_sql($cuserId)."'";
								}
								$wpsp_classes 	=	$wpdb->get_results( $classQuery );

								if($current_user_role=='teacher') {
								echo ' : '.esc_html($wpsp_classes[0]->c_name);
								echo '<input type="hidden" name="class_name" id="class_name" value="'.esc_attr($wpsp_classes[0]->cid).'">';
								echo '</label>';
										}
								else {	?>
									<select name="class_name" data-is_required="<?php echo esc_attr($is_required); ?>" id="class_name" class="wpsp-form-control">
										<option value=""><?php echo esc_html("Select Class","wpschoolpress");?></option>
										<?php	foreach($wpsp_classes as $value) {
											$classlistid = intval($value->cid);?>
										<option value="<?php echo esc_attr(intval($value->cid));?>"
											<?php if($classlistid == $classid) echo esc_html("selected","wpschoolpress"); ?>>
											<?php echo esc_html($value->c_name);?>
										</option>
										<?php }	?>
									</select>
									<?php } ?>
								</div>
							</div>
							<div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
								<div class="wpsp-form-group">
                  <?php
                   /*Check Required Field*/
                   if(isset($is_required_item['ExName'])){
                       $is_required =  esc_html($is_required_item['ExName'],"wpschoolpress");
                   }else{
                       $is_required = true;
                   }
                   ?>
                    <label class="wpsp-label" for="Name"><?php esc_html_e("Exam Name","wpschoolpress"); ?>
                            <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?>
                    </label>
                    <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" ID="ExName" name="ExName" value="<?php echo esc_attr($examname); ?>">
                    </div>
                </div>
				<div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
				<div class="wpsp-form-group">
                    <?php
                     /*Check Required Field*/
                     if(isset($is_required_item['ExStart'])){
                         $is_required =  esc_html($is_required_item['ExStart'],"wpschoolpress");
                     }else{
                         $is_required = true;
                     }
                     ?>
                        <label class="wpsp-label" for="Name"><?php esc_html_e("Exam Start Date","wpschoolpress"); ?>
                            <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?>
                        </label>
                        <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control hasDatepicker" ID="ExStart" name="ExStart" value="<?php echo esc_attr($examsdate); ?>">
                        </div>
                    </div>
                    <div class="wpsp-col-lg-4 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                      <?php
                       /*Check Required Field*/
                       if(isset($is_required_item['ExEnd'])){
                           $is_required =  esc_html($is_required_item['ExEnd'],"wpschoolpress");
                       }else{
                           $is_required = true;
                       }
                       ?>
                            <label class="wpsp-label" for="Name"><?php esc_html_e("Exam End date","wpschoolpress"); ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?>
                            </label>
                            <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control ExEnd hasDatepicker" ID="ExEnd" name="ExEnd" value="<?php echo esc_attr($examedate); ?>">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-8 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                            <div class="wpsp-form-group exam-subject-list">
                        <?php
                         /*Check Required Field*/
                         if(isset($is_required_item['subjectall'])){
                             $is_required =  esc_html($is_required_item['subjectall'],"wpschoolpress");
                         }else{
                             $is_required = false;
                         }
                         ?>
						<label class="wpsp-label" for="Name"><?php esc_html_e("Subject Name","wpschoolpress"); ?>
                        <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></label>
                            <input type="checkbox" data-is_required="<?php echo esc_attr($is_required); ?>" name="subjectall" value="All" class="exam-all-subjects wpsp-checkbox" id="all">
                                <label for="all" class="wpsp-checkbox-label">All</label>
                                <div class="exam-class-list">
                                    <?php $sub_table = $wpdb->prefix."wpsp_subject";
                                    if($current_user_role=='teacher') {
                                        $classid = esc_sql($wpsp_classes[0]->cid);
                                    }
                                    if(!empty($classid)){
                                        $subjectlist	=	$wpdb->get_results("select sub_name,id from $sub_table where class_id='$classid'");
                                        foreach($subjectlist as $svalue){ ?>
                                    <input type="checkbox" name="subjectid[]" value="<?php echo esc_attr($svalue->id); ?>" class="exam-subjects wpsp-checkbox" id="subject-<?php echo esc_attr($svalue->id);?>"
                                        <?php if(in_array($svalue->id, $subjectid)){ ?> checked
                                        <?php } ?> >
                                        <label for="subject-<?php echo esc_attr($svalue->id);?>" class="wpsp-checkbox-label">
                                            <?php echo esc_html($svalue->sub_name);?>
                                        </label>
                                        <?php } } ?>
                                    </div>
									</div>
								</div>
									<?php  do_action('wpsp_after_exam_fields'); ?>
									</div>
											<?php if(!empty($examid)){ ?>
											<input type="hidden" ID="ExamID" name="ExamID" value="<?php echo esc_attr($examid); ?>">
											<?php } ?>
												<div class="wpsp-row">
													<div class="wpsp-col-xs-12">
														<button type="submit" class="wpsp-btn wpsp-btn-success" id="e_submit">
															<?php echo esc_html($buttonname); ?>
														</button>
														<a href="<?php echo esc_url(wpsp_admin_url().'sch-exams')?>" class="wpsp-btn wpsp-dark-btn" ><?php echo apply_filters( 'wpsp_exam_back_button_text', esc_html__( 'Back' , 'wpschoolpress' )); ?>
														</a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								</form>
								<!-- End of Add/Update Exam Form -->
