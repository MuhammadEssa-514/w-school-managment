<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
$subjectclassid =	intval($_GET['classid']);
$teacher_table=	$wpdb->prefix."wpsp_teacher";
$teacher_data = $wpdb->get_results("select * from $teacher_table");
$class_table	=	$wpdb->prefix."wpsp_class";
$classQuery		=	$wpdb->get_results("select * from $class_table where cid='".esc_sql($subjectclassid)."'");
foreach($classQuery as $classdata){
	$cid= intval($classdata->cid);
}
?>
<!-- This form is used for Add New Subject Form -->
<div class="formresponse"></div>
<form name="SubjectEntryForm" action="#" id="SubjectEntryForm" method="post">
<?php wp_nonce_field( 'SubjectRegister', 'subregister_nonce', '', true ); ?>
		<div class="wpsp-card">
				<div class="wpsp-card-head">
					<div class="wpsp-row">
						<div class="wpsp-col-xs-12">
						 <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_subject_heading_item', esc_html__( 'New Subject Entry', 'wpschoolpress' )); ?></h3>
						</div>
					</div>
				</div>

				<input type="hidden"  id="wpsp_locationginal1" value="<?php echo esc_url(admin_url());?>"/>
				<div class="wpsp-card-body">
					<div class="wpsp-row">
					<div class="wpsp-col-md-12 line_box">
						<?php wp_nonce_field( 'SubjectRegister', 'subregister_nonce', '', true ); ?>
						<div class="wpsp-row">
							<?php
                  do_action('wpsp_before_subject_detail_fields');
                  /*Required field Hook*/
                  $is_required_item = apply_filters('wpsp_subject_fields_is_required',array());
              ?>
						<div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
							<div class="wpsp-form-group">
								<label class="wpsp-label" for="Name">
                                <?php
                                    esc_html_e("Class","wpschoolpress");
                  /*Check Required Field*/
                  if(isset($is_required_item['SCID'])){
                      $is_required =  esc_html($is_required_item['SCID'],"wpschoolpress");
                  }else{
                      $is_required = true;
                  }
                  ?>
                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
								</label>
								<select name="SCID" data-is_required="<?php echo esc_attr($is_required); ?>" id="SCID" class="wpsp-form-control" required>
								<option value="" ><?php echo esc_html("Please Select Class","wpschoolpress");?></option>
								<?php
								foreach($sel_class as $classes) { $sel_classid = ''; ?>
									<option value="<?php echo esc_attr(intval($classes->cid));?>" <?php if($sel_classid==$classes->cid) echo esc_html("selected","wpschoolpress"); ?>><?php echo esc_html($classes->c_name);?></option>
								<?php } ?>

							</select>
							<!-- <?php foreach($classQuery as $classdata){
								$cid= $classdata->cid; ?>
								<label class="wpsp-labelMain" for="Name">Class Name : <?php if($cid == $subjectclassid) echo esc_attr($classdata->c_name);?></label>
									<input type="hidden" class="wpsp-form-control" id="SCID" name="SCID" value="<?php if($cid == $subjectclassid) echo esc_attr($classdata->cid);?>">
								<?php } ?> -->
							</div>
						</div>
						</div>
						<?php for($i=1;$i<=5;$i++){?>
						<div class="wpsp-row">
								<div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
						<?php
	                    /*Check Required Field*/
	                    if(isset($is_required_item['SNames'])){
	                        $is_required =  esc_html($is_required_item['SNames'],"wpschoolpress");
	                    }else{
	                        $is_required = true;
	                    }
	                    ?>
									<label class="wpsp-label" for="Name"><?php echo esc_html_e("Subject Name","wpschoolpress")." ".intval($i);?><?php if($i=='1') { ?><span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?>
									<?php } ?></label>
									<input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" name="SNames[]">
									</div>
								</div>

								<div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
										<label class="wpsp-label" for="Name"><?php
                      esc_html_e("Subject Code","wpschoolpress");
                      /*Check Required Field*/
                      if(isset($is_required_item['SCodes'])){
                          $is_required =  esc_html($is_required_item['SCodes'],"wpschoolpress");
                      }else{
                          $is_required = false;
                      }
                      ?>
                      <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?>
                    </label>
					<input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" name="SCodes[]">
					</div>
                    </div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                        <label class="wpsp-label" for="Name">
                    <?php
                          esc_html_e("Subject Teacher","wpschoolpress")."<span> (Incharge)</span>";
                      /*Check Required Field*/
                      if(isset($is_required_item['STeacherID'])){
                          $is_required =  esc_html($is_required_item['STeacherID'],"wpschoolpress");
                      }else{
                          $is_required = false;
                      }
                      ?>
                      <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?>
                    </label>
                                <select name="STeacherID[]"  data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control">
                                    <option value=""><?php echo esc_html_e("Please Select Teacher","wpschoolpress");?></option>
                                        <?php
                                        foreach ($teacher_data as $teacher_list) {
                                            $teacherlistid= $teacher_list->wp_usr_id;?>
                                            <option value="<?php echo esc_attr($teacherlistid);?>" ><?php echo esc_html($teacher_list->first_name ." ". $teacher_list->last_name);?></option>
                                            <?php
                                        }
                                        ?>
                                </select>
									</div>
								</div>
								<div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
									<div class="wpsp-form-group">
										<label class="wpsp-label" for="BName">  <?php
                                        esc_html_e("Book Name","wpschoolpress");
                                    /*Check Required Field*/
                                    if(isset($is_required_item['BNames'])){
                                        $is_required =  esc_html($is_required_item['BNames'],"wpschoolpress");
                                    }else{
                                        $is_required = false;
                                    }
                                    ?>
                        <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></label>
						<input type="text" class="wpsp-form-control" name="BNames[]" placeholder="Book Name">
									</div>
								</div>
								<?php if($i!='5') { ?>
								<hr style="border-top:1px solid #5C779E"/>
								<?php }?>

						</div>
						<?php } ?>
						<?php  do_action('wpsp_after_subject_detail_fields'); ?>
					</div>
					<div class="wpsp-col-md-12">
						<button type="submit" class="wpsp-btn wpsp-btn-success" id="s_submit"><?php echo apply_filters( 'wpsp_subject_button_submit_label',esc_html("Submit","wpschoolpress"));?></button>
						 <a href="<?php echo esc_url(wpsp_admin_url().'sch-subject')?>" class="wpsp-btn wpsp-dark-btn" ><?php echo apply_filters( 'wpsp_subject_button_back_label',esc_html("Back","wpschoolpress"));?></a>
					</div>
				</div>
			</div>
		</div>
</form>
<!-- End of Add Subject Form -->
