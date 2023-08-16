<?php if (!defined('ABSPATH')) exit('No Such File');
$teacher_table = $wpdb->prefix . "wpsp_teacher";
$class_table = $wpdb->prefix . "wpsp_class";
$users_table = $wpdb->prefix . "users";
$tid = intval($_GET['id']);
$msg = '';
if (isset($_GET['edit']) && sanitize_text_field($_GET['edit']) == 'true')
{
    if ($current_user_role == 'administrator' || ($current_user_role == 'teacher' && sanitize_text_field($current_user->ID) == $tid))
    {
        $edit = true;
    }
    else
    {
        $edit = false;
    }
    if (isset($_POST['tedit_nonce']) && wp_verify_nonce(sanitize_text_field($_POST['tedit_nonce']) , 'TeacherEdit'))
    {
        ob_start();
        wpsp_UpdateTeacher();
        $msg = ob_get_clean();
    }
}
else
{
    $edit = false;
}
$tinfo = $wpdb->get_row("select teacher.*,user.user_email from $teacher_table teacher LEFT JOIN $users_table user ON user.ID=teacher.wp_usr_id where teacher.wp_usr_id='".esc_sql($tid)."'");
if (!empty($tinfo))
{ ?> <div id="formresponse"> <?php echo esc_html($msg); ?> </div>
<div class="wpsp-row"> <?php if ($edit)
    { ?> <form name="TeacherEditForm" id="TeacherEditForm" method="POST" enctype="multipart/form-data"> <?php
    } ?> <div class="wpsp-col-xs-12">
      <div class="wpsp-card">
        <div class="wpsp-card-head">
          <h3 class="wpsp-card-title"><?php esc_html_e( 'Personal Details', 'wpschoolpress' ); ?></h3>
            <?php /*
			<h5 class="wpsp-card-subtitle"><?php echo esc_html($tinfo->first_name.' '.$tinfo->middle_name.' '.$tinfo->last_name);?> </h5> */ ?>
        </div>
        <div class="wpsp-card-body">
          <div class="wpsp-row">
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label"><?php esc_html_e( 'Profile Image', 'wpschoolpress' ); ?></label>
                <div class="wpsp-profileUp">
                <?php $loc_avatar = get_user_meta($tid, 'simple_local_avatar', true);
                $img_url = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL . 'img/default_avtar.jpg'; ?>
                <img class="wpsp-upAvatar" id="img_preview_teacher" src="<?php echo esc_url($img_url); ?>">
                <div class="wpsp-upload-button"> <?php if ($edit){ ?> <i class="fa fa-camera"></i>
                <input type="file" name="displaypicture" class="wpsp-file-upload" id="displaypicture"> <?php } ?>
                  </div>
                </div>
                <p class="wpsp-form-notes">*<?php esc_html_e( 'Only JPEG and JPG supported, * Max 3 MB Upload', 'wpschoolpress' ); ?></p>
                <label id="displaypicture-error" class="error" for="displaypicture" style="display: none;"><?php esc_html_e( 'Please Upload Profile Image', 'wpschoolpress' ); ?></label>
                <p id="test" style="color:red"></p>
              </div>
            </div>
            <div class="wpsp-col-lg-9 wpsp-col-md-8 wpsp-col-sm-12 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="gender"><?php esc_html_e( 'Gender', 'wpschoolpress' ); ?></label>
                <div class="wpsp-radio-inline"> <?php if ($edit){ ?> <div class="wpsp-radio">
                    <input type="radio" name="Gender" <?php if ($tinfo->gender == 'Male') echo "checked"; ?> value="Male">
                    <label for="Male"><?php esc_html_e( 'Male', 'wpschoolpress' ); ?></label>
                  </div>
                  <div class="wpsp-radio">
                    <input type="radio" name="Gender" <?php if ($tinfo->gender == 'Female') echo "checked"; ?> value="Female">
                    <label for="Female"><?php esc_html_e( 'Female', 'wpschoolpress' ); ?></label>
                  </div>
                  <div class="wpsp-radio">
                    <input type="radio" name="Gender" <?php if ($tinfo->gender == 'other') echo "checked"; ?> value="other">
                    <label for="other"><?php esc_html_e( 'Other', 'wpschoolpress' ); ?></label>
                  </div> <?php } else { echo esc_html($tinfo->gender); } ?>
                </div>
              </div>
            </div> <?php wp_nonce_field('TeacherRegister', 'tregister_nonce', '', true) ?> <div class="clearfix wpsp-ipad-show"></div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="firstname"><?php esc_html_e( 'First Name', 'wpschoolpress' ); ?>
                <span class="wpsp-required">*</span>
                </label>
                <input type="text" class="wpsp-form-control" value="<?php echo esc_attr($tinfo->first_name); ?>" id="firstname" name="firstname" placeholder="First Name">
                <input type="hidden" id="wpsp_locationginal" value="<?php echo esc_url(admin_url()); ?>" />
                <input type="hidden" id="UserID" name="UserID" value="<?php echo esc_attr($tinfo->wp_usr_id); ?>">
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="middlename"><?php esc_html_e( 'Middle Name', 'wpschoolpress' ); ?></label>
                <input type="text" class="wpsp-form-control" id="name" name="middlename" value="<?php echo esc_attr($tinfo->middle_name); ?>" placeholder="Middle Name">
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="lastname"><?php esc_html_e( 'Last Name', 'wpschoolpress' ); ?>
                <?php if ($edit){ ?> <span class="wpsp-required">*</span> <?php } ?> </span>
                </label>
                <input type="text" class="wpsp-form-control" id="name" name="lastname" value="<?php echo esc_attr($tinfo->last_name); ?>" placeholder="Last Name">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="dateofbirth"><?php esc_html_e( 'Date of Birth', 'wpschoolpress' ); ?></label> <?php if ($edit) { ?>
                <input type="text" class="wpsp-form-control select_date datepicker" value="<?php if ($tinfo->dob == "0000-00-00"){ }else{ echo wpsp_viewDate(esc_attr($tinfo->dob));} ?>" id="Dob" name="Dob" placeholder="Date of Birth"> <?php } else { echo wpsp_viewDate(esc_html($tinfo->dob)); } ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Email"><?php esc_html_e( 'Email Address', 'wpschoolpress' ); ?>
                <span class="wpsp-required"> *</span>
                </label> <?php if ($edit) { ?>
                <input type="email" class="wpsp-form-control" id="Email" name="Email" value="<?php echo esc_attr($tinfo->user_email); ?>" placeholder="Teacher Email">
                <?php } else echo esc_html($tinfo->user_email); ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="address"><?php esc_html_e( 'Current Address', 'wpschoolpress' ); ?>
                <span class="wpsp-required"> * </label>
                <?php if ($edit){ ?> <textarea name="Address" class="wpsp-form-control" rows="1"><?php echo esc_textarea($tinfo->address); ?></textarea>
                <?php } else echo esc_html($tinfo->address); ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
            <div class="wpsp-form-group">
            <label class="wpsp-label" for="CityName"><?php esc_html_e( 'City Name', 'wpschoolpress' ); ?></label>
            <?php if ($edit) { ?>
            <input type="text" class="wpsp-form-control" id="CityName" name="city" placeholder="City Name" value="<?php echo esc_attr($tinfo->city); ?>"> <?php } else echo esc_html($tinfo->city); ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Country"><?php esc_html_e( 'Country', 'wpschoolpress' ); ?></label>
                <?php if ($edit){ $countrylist = wpsp_county_list(); ?>
                  <select class="wpsp-form-control" id="Country" name="country">
                  <option value=""><?php esc_html_e( 'Select Country', 'wpschoolpress' ); ?></option>
                  <?php foreach ($countrylist as $key => $value) { ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php echo selected($tinfo->country, $value); ?>>
                    <?php echo esc_html($value); ?> </option> <?php } ?>
                </select> <?php } else echo esc_html($stinfo->country); ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Zip Code"><?php esc_html_e( 'Pin Code', 'wpschoolpress' ); ?>
                <span class="wpsp-required"> * </label>
                </label> <?php if ($edit){ ?>
                <input type="text" name="zipcode" class="wpsp-form-control" value="<?php echo esc_attr($tinfo->zipcode); ?>"> <?php } else echo esc_html($stinfo->zipcode); ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Phone"><?php esc_html_e( 'Phone Number', 'wpschoolpress' ); ?></label>
                <?php if ($edit) { ?>
                 <input type="text" class="wpsp-form-control" id="Phone" name="Phone" value="<?php echo esc_attr($tinfo->phone); ?>" placeholder="(XXX)-(XXX)-(XXXX)"> <?php } else echo esc_html($tinfo->phone); ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Blood"><?php esc_html_e( 'Blood Group', 'wpschoolpress' ); ?></label>
                <?php if ($edit) { ?>
                <select class="wpsp-form-control" id="Bloodgroup" name="Bloodgroup">
                  <option value=""><?php esc_html_e( 'Select Blood Group', 'wpschoolpress' ); ?></option>
                  <option <?php if ($tinfo->bloodgrp == 'O+') echo esc_html("selected","wpschoolpress"); ?> value="O+"> <?php echo __("O +","wpschoolpress");?> </option>
                  <option <?php if ($tinfo->bloodgrp == 'O-') echo esc_html("selected","wpschoolpress"); ?> value="O-"><?php echo __("O -","wpschoolpress");?> </option>
                  <option <?php if ($tinfo->bloodgrp == 'A+') echo esc_html("selected","wpschoolpress"); ?> value="A+"><?php echo __("A +","wpschoolpress");?> </option>
                  <option <?php if ($tinfo->bloodgrp == 'A-') echo esc_html("selected","wpschoolpress"); ?> value="A-"><?php echo __("A -","wpschoolpress");?> </option>
                  <option <?php if ($tinfo->bloodgrp == 'B+') echo esc_html("selected","wpschoolpress"); ?> value="B+"><?php echo __("B +","wpschoolpress");?> </option>
                  <option <?php if ($tinfo->bloodgrp == 'B-') echo esc_html("selected","wpschoolpress"); ?> value="B-"><?php echo __("B -","wpschoolpress");?> </option>
                  <option <?php if ($tinfo->bloodgrp == 'AB+') echo esc_html("selected","wpschoolpress"); ?> value="AB+"><?php echo __("AB +","wpschoolpress");?> </option>
                  <option <?php if ($tinfo->bloodgrp == 'AB-') echo esc_html("selected","wpschoolpress"); ?> value="AB-"><?php echo __("AB -","wpschoolpress");?> </option>
                </select> <?php } else echo esc_html($tinfo->bloodgrp); ?>
              </div>
            </div>
            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
            <label class="wpsp-label" for="Qualification"><?php esc_html_e( 'Qualification', 'wpschoolpress' ); ?></label>
            <?php if ($edit){ ?>
            <input type="text" class="wpsp-form-control" id="Qual" name="Qual" value="<?php echo esc_attr($tinfo->qualification); ?>" placeholder="Qualification"> <?php } else echo esc_html($tinfo->qualification); ?>
              </div>
            </div>
            <div class="wpsp-col-xs-12"> <?php if ($edit) { ?> <button type="submit" id="u_teacher" class="wpsp-btn wpsp-btn-success"><?php esc_html_e( 'Next', 'wpschoolpress' ); ?></button>
              <!--  <a href='
            <?php echo wpsp_admin_url(); ?>sch-teacher' class="wpsp-btn wpsp-dark-btn">Back</a> --> <?php } else{ ?>
            <a href="?id=<?php echo esc_attr($tinfo->wp_usr_id); ?>&edit=true" type="button" class="wpsp-btn wpsp-btn-sm wpsp-btn-warning"><i class="icon dashicons dashicons-edit wpsp-edit-icon"></i></a>
            <a data-original-title="Remove this user" type="button" class="wpsp-btn wpsp-btn-sm wpsp-btn-danger"> <i class="icon dashicons dashicons-trash wpsp-delete-icon"></i></a> <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="wpsp-col-xs-12">
      <div class="wpsp-card">
        <div class="wpsp-card-head">
          <h3 class="wpsp-card-title"><?php esc_html_e( 'School Details', 'wpschoolpress' ); ?></h3>
        </div>
        <div class="wpsp-card-body">
          <div class="wpsp-row">
            <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Join"><?php esc_html_e( 'Joining Date (mm/dd/yyyy)', 'wpschoolpress' ); ?></label>
                <?php if ($edit) { ?>
                    <input type="text" class="wpsp-form-control select_date" value="<?php if (wpsp_viewDate($tinfo->doj) == "0000-00-00"){}else{ echo wpsp_viewDate(esc_attr($tinfo->doj));} ?>" id="Doj" name="Doj" placeholder="Date of Join"> <?php } else echo wpsp_viewDate(esc_html($tinfo->doj)); ?>
              </div>
            </div>
            <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Releaving"><?php esc_html_e( 'Leaving Date (mm/dd/yyyy)', 'wpschoolpress' ); ?></label>
                <?php if ($edit){ ?>
                <input type="text" class="wpsp-form-control select_date" value="<?php if (wpsp_viewDate($tinfo->dol) == "0000-00-00"){ }else{ echo wpsp_viewDate(esc_attr($tinfo->dol)); } ?>" id="Dol" name="dol" placeholder="Date of Leave"> <?php } else echo wpsp_viewDate(esc_html($tinfo->dol)); ?>
              </div>
            </div>
            <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Working"><?php esc_html_e( 'Working Hours', 'wpschoolpress' ); ?></label>
                <?php if ($edit){ ?>
                <input type="text" name="whours" class="wpsp-form-control" value="<?php echo esc_attr($tinfo->whours); ?>"> <?php } else echo esc_html($tinfo->whours); ?>
              </div>
            </div>
            <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
                <label class="wpsp-label" for="Position"><?php esc_html_e( 'Current Position', 'wpschoolpress' ); ?></label> <?php if ($edit) { ?>
                <input type="text" class="wpsp-form-control" id="Position" name="Position" value="<?php echo esc_attr($tinfo->position); ?>" placeholder="Position"><?php } else echo esc_html($tinfo->position); ?>
              </div>
            </div>
            <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
              <div class="wpsp-form-group">
            <label class="wpsp-label" for="Employee"><?php esc_html_e( 'Employee Code', 'wpschoolpress' ); ?></label>
            <?php if ($edit){ if ($current_user_role == 'administrator'){ ?>
            <input type="text" class="wpsp-form-control" id="Empcode" name="Empcode" value="<?php echo esc_attr($tinfo->empcode); ?>" placeholder="Empcode"> <?php } } else { echo esc_html($tinfo->empcode); } ?>
              </div>
            </div>
            <div class="wpsp-col-xs-12">
            <?php if ($edit) { ?>
            <button type="submit" id="u_teacher" class="wpsp-btn wpsp-btn-success"><?php esc_html_e( 'Update', 'wpschoolpress' ); ?></button>
            <a href='<?php echo esc_url(wpsp_admin_url()."sch-teacher")?>' class="wpsp-btn wpsp-dark-btn"><?php esc_html_e( 'Back', 'wpschoolpress' ); ?></a> <?php
            }
            else
            { ?>
            <a href="?id=<?php echo esc_attr($tinfo->wp_usr_id); ?>&edit=true" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
            <a data-original-title="Remove this user" type="button" class="btn btn-sm btn-danger">
            <i class="glyphicon glyphicon-remove"></i></a> <?php
            } ?>
  </form>
</div>
</div>
</div>
</div>
</div>
</div> <?php
}
else
{
    echo esc_html("Sorry! No Data Retriverd","wpschoolpress");
} ?>
<!--<a href="javascript:;" id="sucess_teacher" class="wpsp-popclick" data-pop="SuccessModal" title="Delete" style="display:none;">a</a> -->
<div class="wpsp-popupMain wpsp-popVisible" id="SuccessModal" data-pop="SuccessModal" style="display:none;">
  <div class="wpsp-overlayer"></div>
  <div class="wpsp-popBody wpsp-alert-body">
    <div class="wpsp-popInner">
      <a href="javascript:;" class="wpsp-closePopup"></a>
      <div class="wpsp-popup-cont wpsp-alertbox wpsp-alert-success">
        <div class="wpsp-alert-icon-box">
          <i class="icon wpsp-icon-tick-mark"></i>
        </div>
        <div class="wpsp-alert-data">
          <input type="hidden" name="teacherid" id="teacherid">
          <h4><?php echo esc_html("Success","wpschoolpress");?></h4>
          <p><?php echo esc_html("Data Saved Successfully.","wpschoolpress");?></p>
        </div>
      </div>
    </div>
  </div>
</div>