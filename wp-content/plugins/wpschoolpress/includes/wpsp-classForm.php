<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');

$c_fee_type = '';
$ctable=$wpdb->prefix."wpsp_class";
$teacher_table=	$wpdb->prefix."wpsp_teacher";
$teacher_data = $wpdb->get_results("select wp_usr_id,CONCAT_WS(' ', first_name, middle_name, last_name ) AS full_name from $teacher_table order by tid");
$classname	= $classnumber	= $classcapacity = $classlocation = $classstartingdate = $classendingdate= $teacherid = '';
if( isset( $_GET['id']) ) {
	$classid =	intval($_GET['id']);
	$wpsp_classes =$wpdb->get_results("select * from $ctable where cid='".esc_sql($classid)."'");

	foreach ($wpsp_classes as $wpsp_editclass) {
		$classname=$wpsp_editclass->c_name;
		$classnumber=$wpsp_editclass->c_numb;
		$classcapacity=$wpsp_editclass->c_capacity;
		$classlocation=$wpsp_editclass->c_loc;
		$classstartingdate1=$wpsp_editclass->c_sdate;

		$classstartingdate = date("m/d/Y", strtotime($classstartingdate1));
		$classendingdate1=$wpsp_editclass->c_edate;
		$classendingdate = date("m/d/Y", strtotime($classendingdate1));
		$teacherid=$wpsp_editclass->teacher_id;
		if($wpsp_editclass->c_fee_type != ''){
			$c_fee_type =$wpsp_editclass->c_fee_type  ;
		}
	}
}
// $label	=	isset( $_GET['id'] ) ? apply_filters( 'wpsp_class_main_heading_update', esc_html__( 'Update Class Information', 'wpschoolpress' )) : apply_filters( 'wpsp_class_main_heading_add', esc_html__( 'Add Class Information', 'wpschoolpress' ));
$formname		=	isset( $_GET['id'] ) ? 'ClassEditForm' : 'ClassAddForm';
$buttonname	=	isset( $_GET['id'] ) ? 'Update' : 'Submit';
$propayment = wpsp_check_pro_version('pay_WooCommerce');
$propayment = !$propayment['status'] ? 'notinstalled'    : 'installed';
?>
<!-- This form is used for Add/Update Class -->
<div id="formresponse"></div>
<form name="<?php echo esc_attr($formname);?>" id="<?php echo esc_attr($formname); ?>" method="post">
	<?php if( isset( $_GET['id']) ) { ?>
		<input type="hidden" name="cid" value="<?php echo esc_attr($classid);?>">
	<?php } ?>
	<div class="wpsp-row">
	<div class="wpsp-col-xs-12">
		<div class="wpsp-card">
			<div class="wpsp-card-head">
				<h3 class="wpsp-card-title"><?php echo isset( $_GET['id'] ) ? apply_filters( 'wpsp_class_main_heading_update', esc_html__( 'Update Class Information', 'wpschoolpress' )) : apply_filters( 'wpsp_class_main_heading_add', esc_html__( 'Add Class Information', 'wpschoolpress' ));; ?></h3>
			</div>
			<div class="wpsp-card-body">
				 <?php wp_nonce_field( 'ClassAction', 'caction_nonce', '', true ) ?>
				<div class="wpsp-row">
					<?php  do_action('wpsp_before_class_detail_fields');
					  $is_required_item = apply_filters('wpsp_class_is_required',array());
					  $item =  apply_filters( 'wpsp_class_title_item',esc_html("Class Name","wpschoolpress"));
					?>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group ">
							<label class="wpsp-label" for="Name"><?php esc_html_e("Class Name","wpschoolpress");
								/*Check Required Field*/
								if(isset($is_required_item['Name'])){
									$is_required =  esc_html($is_required_item['Name'],"wpschoolpress");
								}else{
									$is_required = true;
								}
								?>
							<span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
							</label>
							<input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control"  name="Name"  value="<?php echo esc_attr($classname); ?>">
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
					   <div class="wpsp-form-group">
							<label class="wpsp-label" for="Number"><?php esc_html_e("Class Number","wpschoolpress");
								/*Check Required Field*/
								if(isset($is_required_item['Number'])){
									$is_required =  esc_html($is_required_item['Number'],"wpschoolpress");
								}else{
									$is_required = true;
								}
								?>
							<span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
							<input data-is_required="<?php echo esc_attr($is_required); ?>" type="text" class="wpsp-form-control"  name="Number"  value="<?php echo esc_attr($classnumber); ?>">
							<input type="hidden"  id="wpsp_locationginal" value="<?php echo esc_url(admin_url());?>"/>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="Capacity"><?php esc_html_e("Class Capacity","wpschoolpress");
								/*Check Required Field*/
								if(isset($is_required_item['capacity'])){
									$is_required =  esc_html($is_required_item['capacity'],'wpschoolpress');
								}else{
									$is_required = true;
								}
								?><span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
							<input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" pattern="[0-9]*" class="wpsp-form-control numbers"  name="capacity" id="c_capacity" value="<?php echo esc_attr($classcapacity); ?>" min="0">
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
						   <label class="wpsp-label" for="Selectteacher"><?php esc_html_e("Class Teacher","wpschoolpress")."<span> (Incharge)</span>";
 								/*Check Required Field*/
 								if(isset($is_required_item['ClassTeacherID'])){
 									$is_required =  esc_html($is_required_item['ClassTeacherID'],'wpschoolpress');
 								}else{
 									$is_required = false;
 								}
 								?>
								<span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
							<select data-is_required="<?php echo esc_attr($is_required); ?>" name="ClassTeacherID" class="wpsp-form-control">
								<option value=""><?php echo esc_html("Select Teacher", "wpschoolpress");?></option>
								<?php
								if(!empty($teacher_data)){
								foreach ($teacher_data as $teacher_list) {
									$teacherlistid= $teacher_list->wp_usr_id;
									?>
								<option value="<?php echo esc_attr($teacherlistid);?>" <?php if($teacherlistid == $teacherid) echo esc_html("selected","wpschoolpress"); ?> ><?php echo esc_html($teacher_list->full_name);?></option>
								<?php }
								}?>
							</select>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="Starting"><?php esc_html_e("Class Starting on","wpschoolpress");
							 /*Check Required Field*/
							 if(isset($is_required_item['Sdate'])){
									 $is_required =  esc_html($is_required_item['Sdate'],"wpschoolpress");
							 }else{
									 $is_required = true;
							 }
							 ?>
							 <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
							<input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control select_date wpsp-start-date" name="Sdate" value="<?php echo esc_attr($classstartingdate); ?>" readonly>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="Ending"><?php esc_html_e("Class Ending on","wpschoolpress");
							 /*Check Required Field*/
							 if(isset($is_required_item['Edate'])){
									 $is_required =  esc_html($is_required_item['Edate'],"wpschoolpress");
							 }else{
									 $is_required = true;
							 }
							 ?>
							 <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
							<input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control select_date wpsp-end-date" name="Edate"  value="<?php echo esc_attr($classendingdate); ?>" readonly>
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
						<div class="wpsp-form-group">
								<label class="wpsp-label" for="Location"><?php esc_html_e("Class Location","wpschoolpress");
								 /*Check Required Field*/
								 if(isset($is_required_item['Location'])){
										 $is_required =  esc_html($is_required_item['Location'],"wpschoolpress");
								 }else{
										 $is_required = false;
								 }
								 ?>
								 <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
								<input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" name="Location"  value="<?php echo esc_attr($classlocation); ?>">
						</div>
					</div>
					<div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
					<div class="wpsp-form-group">
							<label class="wpsp-label" for="Location"><?php esc_html_e("Class Fee Type","wpschoolpress");
							 /*Check Required Field*/
							 if(isset($is_required_item['classfeetype'])){
								$is_required =  esc_html($is_required_item['classfeetype'],"wpschoolpress");
							 }else{
								$is_required = true;
							 }
							 ?>
							 <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
							<select data-is_required="<?php echo esc_attr($is_required); ?>" name="classfeetype" class="wpsp-form-control">
								<option value="" selected disabled><?php echo esc_html("Select Class Fee Type", "wpschoolpress");?></option>
								 <?php if($propayment == "installed"){
								 	echo esc_html($c_fee_type);?>
								<option value="paid" <?php if($c_fee_type == "paid") echo esc_html("selected","wpschoolpress"); ?>><?php echo esc_html("Paid", "wpschoolpress");?></option>
							<?php } ?>
								<option value="free" <?php if($c_fee_type == "free") echo esc_html("selected","wpschoolpress"); ?>><?php echo esc_html("Free", "wpschoolpress");?></option>
                            </select>

						</div>
					</div>
					<?php  do_action('wpsp_after_class_detail_fields'); ?>
					<div class="wpsp-col-xs-12 wpsp-btnsubmit-section">
						<button type="submit" class="wpsp-btn wpsp-btn-success" id="c_submit"><?php echo esc_html($buttonname); ?></button>
						<a href="<?php echo esc_url(wpsp_admin_url().'sch-class')?>" class="wpsp-btn wpsp-dark-btn" ><?php echo esc_html("Back", "wpschoolpress");?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</form>
<!-- End of Add/Update New Class Form -->
