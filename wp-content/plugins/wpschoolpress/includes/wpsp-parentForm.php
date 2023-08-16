<?php if (!defined( 'ABSPATH' ) )exit('No Such File');
	global $current_user, $wpdb;
	$current_user_role=$current_user->roles[0];
?>
<!-- This form is used for Add New Parent -->
<div id="formresponse"></div>
<form name="ParentEntryForm" id="ParentEntryForm" method="post">
	<div class="wpsp-card">
		<div class="wpsp-card-head">
			<h3 class="wpsp-card-title"><?php esc_html_e("New Parent entry","wpschoolpress");?></h3>
		</div>
		<div class="wpsp-card-body">
		<div class="wpsp-col-md-6">
			<?php wp_nonce_field( 'ParentRegister', 'pregister_nonce', '', true ) ?>
			<div class="wpsp-row">
					<div class="wpsp-col-md-4">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="firstname"><?php echo esc_html_e("Firstname","wpschoolpress");?> <span class="wpsp-required">*</span></label>
							<input type="text" class="wpsp-form-control" id="firstname" name="firstname" placeholder="First Name">
						</div>
					</div>
					<div class="wpsp-col-md-4">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="middlename"><?php echo esc_html_e("Middlename","wpschoolpress");?> <span class="wpsp-required">*</span></label>
							<input type="text" class="wpsp-form-control" id="middlename" name="middlename" placeholder="Middle Name">
						</div>
					</div>
					<div class="wpsp-col-md-4">
						<div class="wpsp-form-group">
							<label class="wpsp-label" for="lastname"><?php echo esc_html_e("Lastname","wpschoolpress");?> <span class="wpsp-required">*</span></label>
							<input type="text" class="wpsp-form-control" id="lastname" name="lastname" placeholder="Last Name">
						</div>
					</div>
			</div>

			<div class="wpsp-form-group">
				<label class="wpsp-label" for="Username"><?php echo esc_html_e("Username","wpschoolpress");?> <span class="wpsp-required">*</span></label>
				<input type="text" class="wpsp-form-control" id="Username" name="Username" placeholder="Parent Username">
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="Email"><?php echo esc_html_e("Email address","wpschoolpress");?> <span class="wpsp-required">*</span></label>
				<input type="email" class="wpsp-form-control" id="Email" name="Email" placeholder="parent Email">
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="Password"><?php echo esc_html_e("Password","wpschoolpress");?> <span class="wpsp-required">*</span></label>
				<input type="password" class="wpsp-form-control" id="Password" name="Password" placeholder="Password">
			</div>
			<div class="wpsp-form-group">
				<label for="ConfirmPassword"><?php echo esc_html_e("Confirm Password","wpschoolpress");?> <span class="wpsp-required">*</span></label>
				<input type="password" class="wpsp-form-control" id="ConfirmPassword" name="ConfirmPassword" placeholder="Confirm Password">
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="educ"><?php echo esc_html_e("Education","wpschoolpress");?></label>
				<input type="text" class="wpsp-form-control" id="Qual" name="Qual" placeholder="Highest Education Degree">
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="dateofbirth"><?php echo esc_html_e("Date of Birth","wpschoolpress");?></label>
				<input type="text" class="wpsp-form-control select_date" id="Dob" name="Dob" placeholder="Date of Birth">
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="displaypicture"><?php echo esc_html_e("Profile Image","wpschoolpress");?></label>
				<input type="file" name="displaypicture" id="displaypicture">
				<p id="test" style="color:red"></p>
			</div>
		</div>
		<div class="wpsp-col-md-6">
			<div class="wpsp-form-group parent-student-list">
				<label class="wpsp-label" for="position"><?php echo esc_html_e("Select Student","wpschoolpress");?></label>
				<?php
				$class_table	=	$wpdb->prefix."wpsp_class";
				$classQuery		=	"select cid,c_name from $class_table";
				if( $current_user_role=='teacher' ) {
					$cuserId	=	intval($current_user->ID);
					$classQuery	=	"select cid,c_name from $class_table where teacher_id='".esc_sql($cuserId)."'";
				}
				$classList		=	$wpdb->get_results( $classQuery );
				?>
				 <select name="child_list[]" id="child_list" data-icon-base="fa" data-tick-icon="fa-check" multiple data-live-search="true" class="selectpicker wpsp-form-control">
					<?php foreach( $classList as $classkey=>$classvalue ) { ?>
						<optgroup label="Class Name:<?php echo esc_attr($classvalue->c_name); ?>">
							<?php
								$student_table		=	$wpdb->prefix."wpsp_student";
								$studentList		=	$wpdb->get_results("select wp_usr_id,s_fname from $student_table where class_id='$classvalue->cid'");
								foreach( $studentList as $studentkey=> $studentvalue ) {
							?>
							<option value="<?php echo esc_attr(intval($studentvalue->wp_usr_id)); ?>"><?php echo esc_html($studentvalue->s_fname); ?></option>
								<?php } ?>
						</optgroup>
					<?php } ?>
				</select>
			</div>
			<div class="wpsp-form-group wpsp-gender-field">
				<label class="wpsp-label" for="Class"><?php echo esc_html_e("Gender","wpschoolpress");?></label> <br/>
				<div class="radio">
					<input type="radio" name="Gender" value="Male" checked="checked">
					<label for="Male"><?php echo esc_html_e("Male","wpschoolpress");?></label>
				</div>
				<div class="radio">
					<input type="radio" name="Gender" value="Female">
					<label for="Female"><?php echo esc_html_e("Female","wpschoolpress");?></label>
				</div>
				<div class="radio">
					<input type="radio" name="Gender" value="Other">
					<label for="Female"><?php echo esc_html_e("Other","wpschoolpress");?></label>
				</div>
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="position"><?php echo esc_html_e("Profession","wpschoolpress");?></label>
				<input type="text" class="wpsp-form-control" id="profession" name="Profession" placeholder="profession">
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="Address" ><?php echo esc_html_e("Current Address","wpschoolpress");?></label>
				<textarea name="Address" class="wpsp-form-control" rows="4"></textarea>
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="Address" ><?php echo esc_html_e("Permanent Address","wpschoolpress");?></label>
				<textarea name="pAddress" class="wpsp-form-control" rows="5"></textarea>
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="Country"><?php echo esc_html_e("Country","wpschoolpress");?></label>
				<?php $countrylist = wpsp_county_list();?>
				<select class="wpsp-form-control" id="Country" name="country">
					<option value=""><?php echo esc_html("Country","wpschoolpress");?></option>
					<?php
					foreach( $countrylist as $key=>$value ) { ?>
						<option value="<?php echo esc_attr($value);?>"><?php echo esc_html_e($value);?></option>
					<?php
					}
					?>
				</select>
			</div>

			<div class="wpsp-row">
				<div class="wpsp-col-md-6">
					<div class="wpsp-form-group">
						<label class="wpsp-label" for="Zipcode"><?php echo esc_html_e("Zipcode","wpschoolpress");?></label>
						<input type="text" class="wpsp-form-control" id="Zipcode" name="zipcode" placeholder="Zipcode">
					</div>
				</div>
				<div class="wpsp-col-md-6">
					<div class="wpsp-form-group">
						<label class="wpsp-label" for="phone"><?php echo esc_html_e("Phone","wpschoolpress");?></label>
						<input type="text" class="wpsp-form-control" id="phone" name="Phone" placeholder="Phone Number">
					</div>
				</div>
			</div>
			<div class="wpsp-form-group">
				<label class="wpsp-label" for="bloodgroup"><?php echo esc_html_e("Blood Group","wpschoolpress");?></label>
				<select class="wpsp-form-control" id="Bloodgroup" name="Bloodgroup">
					<option value=""><?php echo esc_html("Blood Group","wpschoolpress");?></option>
					<option value="O+"><?php echo __("O +","wpschoolpress");?></option>
                    <option value="O-"><?php echo __("O -","wpschoolpress");?></option>
                    <option value="A+"><?php echo __("A +","wpschoolpress");?></option>
                    <option value="A-"><?php echo __("A -","wpschoolpress");?></option>
                    <option value="B+"><?php echo __("B +","wpschoolpress");?></option>
                    <option value="B-"><?php echo __("B -","wpschoolpress");?></option>
                    <option value="AB+"><?php echo __("AB +","wpschoolpress");?></option>
                    <option value="AB-"><?php echo __("AB -","wpschoolpress");?></option>
				</select>
			</div>
		</div>
		<div class="wpsp-col-md-12">
			<button type="submit" class="wpsp-btn wpsp-btn-primary" id="parentform"><?php echo esc_html("Submit","wpschoolpress");?></button>
		</div>
	</div>
	</div>
</form>
<!-- End of Add New Parent Form -->