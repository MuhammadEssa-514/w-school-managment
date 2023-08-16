<?php if (!defined( 'ABSPATH' ) )exit('No Such File');
?>
<!-- This form is used for Add New Student -->
<div id="formresponse"></div>
<form name="StudentEntryForm" id="StudentEntryForm" method="POST" enctype="multipart/form-data"><div class="wpsp-col-xs-12">
    <div class="wpsp-row">
    <div class="wpsp-card">
                <div class="wpsp-card-head">
                    <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_title_personal_detail', esc_html__( 'Personal Details', 'wpschoolpress' )); ?></h3>
                </div>
                <div class="wpsp-card-body">
                     <?php wp_nonce_field('StudentRegister', 'sregister_nonce', '', true) ?>
                    <div class="wpsp-row">

                        <?php
                          do_action('wpsp_before_student_personal_detail_fields');
                          /*Required field Hook*/
                          $is_required_item = apply_filters('wpsp_student_personal_is_required',array());
                        ?>

                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                <div class="wpsp-form-group">
                                    <label class="wpsp-label displaypicture">
                                      <?php esc_html_e("Profile Image","wpschoolpress");?>
                                    </label>
                                    <div class="wpsp-profileUp">
                                        <img class="wpsp-upAvatar" id="img_preview1"  src="<?php echo esc_url(plugins_url( 'img/default_avtar.jpg', dirname(__FILE__) ))?>">
                                        <div class="wpsp-upload-button"><i class="fa fa-camera"></i>

                                        <input name="displaypicture"  class="wpsp-file-upload" id="displaypicture"  type="file" accept="image/jpg, image/jpeg" />
                                        </div>
                                    </div>
                                    <p class="wpsp-form-notes">* <?php echo esc_html("Only JPEG and JPG supported, * Max 3 MB Upload","wpschoolpress");?> </p>
                                    <!-- <label id="displaypicture-error" class="error" for="displaypicture" style="display: none;">Please Upload Profile Image</label> -->
                                    <p id="test" style="color:red"></p>
                                </div>
                        </div>
                        <div class="wpsp-col-lg-9 wpsp-col-md-8 wpsp-col-sm-12 wpsp-col-xs-12">
                                <div class="wpsp-form-group">
                                    <label class="wpsp-label" for="gender">
                                      <?php esc_html_e("Gender","wpschoolpress");?>
                                    </label>
                                    <div class="wpsp-radio-inline">
                                        <div class="wpsp-radio">
                                            <input type="radio" name="s_gender" value="Male" checked="checked" id="Male">
                                            <label for="Male"><?php echo esc_html_e("Male","wpschoolpress");?></label>
                                        </div>
                                        <div class="wpsp-radio">
                                            <input type="radio" name="s_gender" value="Female" id="Female">
                                            <label for="Female"><?php echo esc_html_e("Female","wpschoolpress");?></label>
                                        </div>
                                        <div class="wpsp-radio">
                                            <input type="radio" name="s_gender" value="other" id="other">
                                            <label for="other"><?php echo esc_html_e("Other","wpschoolpress");?></label>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="clearfix wpsp-ipad-show"></div>
                        <input type="hidden"  id="wpsp_locationginal1" value="<?php echo esc_url(admin_url());?>"/>
                        <div class="clearfix wpsp-ipad-show"></div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="firstname">
                                  <?php esc_html_e("First Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_fname'])){
                                      $is_required =  esc_html($is_required_item['s_fname'],"wpschoolpress");
                                  }else{
                                      $is_required = true;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                                <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="firstname" name="s_fname" >
                                <input type="hidden"  id="wpsp_locationginal" value="<?php echo admin_url();?>"/>
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="middlename">
                                  <?php esc_html_e("Middle Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['middlename'])){
                                      $is_required =  esc_html($is_required_item['middlename'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="middlename" name="middlename">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="lastname">
                                  <?php esc_html_e("Last Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_lname'])){
                                      $is_required =  esc_html($is_required_item['s_lname'],"wpschoolpress");
                                  }else{
                                      $is_required = true;
                                  }
                                  ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                              </label>
                                <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="lastname" name="s_lname">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="dateofbirth">
                                  <?php esc_html_e("Date of Birth","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_dob'])){
                                      $is_required =  esc_html($is_required_item['s_dob'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                  </label>
                                <input type="text" class="wpsp-form-control select_date" data-is_required="<?php echo esc_attr($is_required); ?>"  id="Dob" name="s_dob" placeholder="mm/dd/yyyy" readonly>
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="bloodgroup">
                                  <?php esc_html_e("Blood Group","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_bloodgrp'])){
                                      $is_required =  esc_html($is_required_item['s_bloodgrp'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                  </label>
                                <select class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="Bloodgroup" name="s_bloodgrp">
                                    <option value=""><?php echo esc_html("Select Blood Group","wpschoolpress");?></option>
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
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                        <label class="wpsp-label"  for="s_p_phone">
                                          <?php esc_html_e("Phone Number","wpschoolpress");
                                          /*Check Required Field*/
                                          if(isset($is_required_item['s_p_phone'])){
                                              $is_required =  esc_html($is_required_item['s_p_phone'],"wpschoolpress");
                                          }else{
                                              $is_required = false;
                                          }

                                          ?>
                                          <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                        </label>
                                        <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="s_p_phone" name="s_p_phone" onkeypress='return event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39 || event.charCode >= 48 && event.charCode <= 57'>
                                        <small><?php echo esc_html("(Please enter country code with mobile number)","wpschoolpress");?></small>
                                    </div>
                                </div>
                        <div class="wpsp-col-xs-12">
                            <hr />
                            <h4 class="card-title mt-5"><?php echo esc_html("Address","wpschoolpress");?></h4>
                        </div>
                        <div class="wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Address">
                                  <?php esc_html_e("Current Address","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_address'])){
                                      $is_required =  esc_html($is_required_item['s_address'],"wpschoolpress");
                                  }else{
                                      $is_required = true;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" name="s_address" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" rows="4" id="current_address" />
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                           <div class="wpsp-form-group ">
                                <label class="wpsp-label" for="CityName">
                                  <?php esc_html_e("City Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_city'])){
                                      $is_required =  esc_html($is_required_item['s_city'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                  </label>
                                <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="current_city" name="s_city">
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Country">
                                  <?php esc_html_e("Country","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_country'])){
                                      $is_required =  esc_html($is_required_item['s_country'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                  </label>
                                <?php $countrylist = wpsp_county_list();?>
                                <select class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="current_country" name="s_country" >
                                    <option value=""><?php echo esc_html("Select Country","wpschoolpress");?></option>
                                    <?php
                                        foreach( $countrylist as $key=>$value ) { ?>
                                    <option value="<?php echo esc_attr($value);?>"><?php echo esc_html($value);?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                              <?php
                                /*Check Required Field*/
                                if(isset($is_required_item['s_zipcode'])){
                                    $is_required =  esc_html($is_required_item['s_zipcode'],"wpschoolpress");
                                }else{
                                    $is_required = false;
                                }

                                ?>
                                <label class="wpsp-label" for="Zipcode"><?php esc_html_e("Pin Code","wpschoolpress"); ?><span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                                <input type="text" class="wpsp-form-control" id="current_pincode" name="s_zipcode" data-is_required="<?php echo esc_attr($is_required); ?>">
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <input type="checkbox"  id="sameas" value="1" class="wpsp-checkbox"> <label for="sameas"> <?php echo esc_html("Same as Above","wpschoolpress");?> </label>
                            </div>
                        </div>
                        <div class="wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <div class="wpsp-form-group">
                                  <?php
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_paddress'])){
                                      $is_required =  esc_html($is_required_item['s_paddress'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                    <label for="Address"><?php esc_html_e("Permanent Address","wpschoolpress");?><span class="wpsp-required"><?php echo ($is_required)?"*":""; ?></span></label>
                                    <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" rows="5" id="permanent_address" name="s_paddress">
                                </div>
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Zipcode">
                                  <?php esc_html_e("City Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_pcity'])){
                                      $is_required =  esc_html($is_required_item['s_pcity'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                  </label>
                                <input type="text " class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="permanent_city" name="s_pcity">
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Zipcode">
                                  <?php esc_html_e("Country","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_pcountry'])){
                                      $is_required =  esc_html($is_required_item['s_pcountry'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                  </label>
                                <select class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="permanent_country"  name="s_pcountry">
                                    <option value=""><?php echo esc_html("Select Country","wpschoolpress");?></option>
                                    <?php foreach ($countrylist as $key => $value) { ?>
                                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($value); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Zipcode">
                                  <?php esc_html_e("Pin Code","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['s_pzipcode'])){
                                      $is_required =  esc_html($is_required_item['s_pzipcode'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }

                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                 </label>
                                   <input type="text" class="wpsp-form-control" id="permanent_pincode" name="s_pzipcode"  data-isrequired="<?php echo esc_attr($is_required); ?>">
                            </div>
                        </div>

                          <?php

                              do_action( 'wpsp_after_student_personal_detail_fields' );
                          ?>

                        <div class="wpsp-col-xs-12 wpsp-hidden-xs">
                            <button type="submit" class="wpsp-btn wpsp-btn-success" id="studentform1"><?php echo esc_html("Next","wpschoolpress");?></button>&nbsp;&nbsp;
                           <!--  <a href="<?php echo esc_url(wpsp_admin_url());?>sch-student" class="wpsp-btn wpsp-dark-btn">Back</a> -->
                        </div>
                    </div>
                </div>
        </div>
      <div class="wpsp-row">
       <div class="wpsp-col-xs-12">
        <div class="wpsp-card">
            <div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_title_parent_detail', esc_html__( 'Parent Detail', 'wpschoolpress' )); ?></h3>
            </div>
            <div class="wpsp-card-body">
                <div class="wpsp-row">

                        <?php
                            do_action('wpsp_before_student_parent_detail_fields');

                            /*Required field Hook*/
                            $is_required_parent = apply_filters('wpsp_student_parent_is_required',array());
                        ?>

                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="customUpload btnUpload  wpsp-label">
                                  <?php esc_html_e("Profile Image","wpschoolpress");?>
                                  </label>
                                <div class="wpsp-profileUp">
                                    <img class="wpsp-upAvatar" id="img_preview1"  src="<?php echo esc_url(plugins_url( 'img/default_avtar.jpg', dirname(__FILE__) ))?>">

                                    <div class="wpsp-upload-button"><i class="fa fa-camera"></i>
                                    <input name="p_displaypicture" class="wpsp-file-upload" id="p_displaypicture" type="file" accept="image/jpg, image/jpeg" />
                                    </div>
                                </div>
                                <p class="wpsp-form-notes">* <?php echo esc_html("Only JPEG and JPG supported, * Max 3 MB Upload","wpschoolpress");?> </p>
                                <label id="pdisplaypicture-error" class="error" for="pdisplaypicture" style="display: none;">
                                <?php echo esc_html("Please Upload Profile Image","wpschoolpress");?></label>
                                <p id="test" style="color:red"></p>
                            </div>
                    </div>
                    <div class="wpsp-col-lg-9 wpsp-col-md-8 wpsp-col-sm-12 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="p_gender">
                                  <?php esc_html_e("Gender","wpschoolpress"); ?>
                                  </label>
                                <div class="wpsp-radio-inline">
                                    <div class="wpsp-radio">
                                        <input type="radio" name="p_gender" value="Male" checked="checked" id="p_Male">
                                        <label for="Male"><?php echo esc_html_e("Male","wpschoolpress");?></label>
                                    </div>
                                    <div class="wpsp-radio">
                                        <input type="radio" name="p_gender" value="Female" id="p_Female">
                                        <label for="Female"><?php echo esc_html_e("Female","wpschoolpress");?></label>
                                    </div>
                                    <div class="wpsp-radio">
                                        <input type="radio" name="p_gender" value="other" id="p_other">
                                        <label for="other"><?php echo esc_html_e("Other","wpschoolpress");?></label>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="clearfix wpsp-ipad-show"></div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="firstname">
                              <?php esc_html_e("First Name","wpschoolpress");
                              /*Check Required Field*/
                              if(isset($is_required_parent['p_fname'])){
                                  $is_required =  esc_html($is_required_parent['p_fname'],"wpschoolpress");
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                            <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="p_firstname" name="p_fname">
                        </div>
                    </div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="middlename"><?php esc_html_e("Middle Name","wpschoolpress");
                              /*Check Required Field*/
                              if(isset($is_required_parent['p_mname'])){
                                  $is_required =  esc_html($is_required_parent['p_mname'],"wpschoolpress");
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                            <input type="text" class="wpsp-form-control" <?php echo esc_attr($is_required); ?> id="p_middlename" name="p_mname">
                        </div>
                    </div>
                    <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="lastname"><?php esc_html_e("Last Name","wpschoolpress");
                              /*Check Required Field*/
                              if(isset($is_required_parent['p_mname'])){
                                  $is_required =  esc_html($is_required_parent['p_mname'],"wpschoolpress");
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                            </label>
                            <input type="text" class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="p_lastname" name="p_lname">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="Username"><?php esc_html_e("Username","wpschoolpress");
                              /*Check Required Field*/
                              if(isset($is_required_parent['pUsername'])){
                                  $is_required =  esc_html($is_required_parent['pUsername'],"wpschoolpress");
                              }else{
                                  $is_required = false;
                              }
                              ?>
                              <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                            </label>
                            <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control chk-username" id="p_username" name="pUsername">
                        </div>
                    </div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="Password">
                              <?php esc_html_e("Password","wpschoolpress");
                                /*Check Required Field*/
                                if(isset($is_required_parent['pPassword'])){
                                    $is_required =  esc_html($is_required_parent['pPassword'],"wpschoolpress");
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                              </label>
                            <input type="password" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="p_password" name="pPassword">
                        </div>
                    </div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="ConfirmPassword">
                              <?php esc_html_e("Confirm Password","wpschoolpress");
                                /*Check Required Field*/
                                if(isset($is_required_parent['pConfirmPassword'])){
                                    $is_required =  esc_html($is_required_parent['pConfirmPassword'],"wpschoolpress");
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                              </label>
                            <input type="password" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="p_confirmpassword" name="pConfirmPassword" >
                        </div>
                    </div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="pbloodgroup"><?php esc_html_e("Blood Group","wpschoolpress");
                                    /*Check Required Field*/
                                    if(isset($is_required_parent['p_bloodgroup'])){
                                        $is_required =  esc_html($is_required_parent['p_bloodgroup'],"wpschoolpress");
                                    }else{
                                        $is_required = false;
                                    }
                                    ?>
                                    <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                    </label>
                                <select class="wpsp-form-control" data-is_required="<?php echo esc_attr($is_required); ?>" id="p_bloodgroup" name="p_bloodgroup">
                                    <option value=""><?php echo esc_html("Select Blood Group","wpschoolpress");?></option>
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
                    <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="pEmail">
                              <?php esc_html_e("Email Address","wpschoolpress");
                                /*Check Required Field*/
                                if(isset($is_required_parent['pEmail'])){
                                    $is_required =  esc_html($is_required_parent['pEmail'],"wpschoolpress");
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                              </label>
                            <input  data-required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control chk-email" id="pEmail" name="pEmail" type="email">
                        </div>
                    </div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="phone">
                                  <?php esc_html_e("Phone","wpschoolpress");
                                    /*Check Required Field*/
                                    if(isset($is_required_parent['s_phone'])){
                                        $is_required =  esc_html($is_required_parent['s_phone'],"wpschoolpress");
                                    }else{
                                        $is_required = false;
                                    }
                                    ?>
                                    <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                  </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="s_phone" name="s_phone">
                            </div>
                    </div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-6 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="p_edu">
                              <?php esc_html_e("Education","wpschoolpress");
                                /*Check Required Field*/
                                if(isset($is_required_parent['p_edu'])){
                                    $is_required =  esc_html($is_required_parent['p_edu'],"wpschoolpress");
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                              </label>
                            <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" name="p_edu" id="p_edu">
                        </div>
                    </div>
                    <div class="wpsp-col-md-3 wpsp-col-sm-6 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="p_profession">
                              <?php esc_html_e("Profession","wpschoolpress");
                                /*Check Required Field*/
                                if(isset($is_required_parent['p_profession'])){
                                    $is_required =  esc_html($is_required_parent['p_profession'],"wpschoolpress");
                                }else{
                                    $is_required = false;
                                }
                                ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                              </label>
                            <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" name="p_profession" id="p_profession">
                        </div>
                    </div>

                        <?php
                            do_action('wpsp_after_student_parent_detail_fields');
                        ?>

                    <div class="wpsp-col-xs-12 wpsp-hidden-xs">
                        <button type="submit" class="wpsp-btn wpsp-btn-success" id="studentform2">Next</button>&nbsp;&nbsp;
                        <!-- <a href="<?php echo esc_url(wpsp_admin_url());?>sch-student" class="wpsp-btn wpsp-dark-btn">Back</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
      <div class="wpsp-row">
    <div class="wpsp-col-lg-6 wpsp-col-md-6  wpsp-col-sm-6 wpsp-col-xs-12">
        <div class="wpsp-card">
            <div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_title_account_information', esc_html__( 'Account Information', 'wpschoolpress' )); ?></h3>
            </div>
            <div class="wpsp-card-body">
              <div class="wpsp-form-group">
                <?php
                    do_action('wpsp_before_student_account_detail_fields');
                    /*Required field Hook*/
                    $is_required_account = apply_filters('wpsp_student_account_is_required',array());
                ?>
              </div>
               <div class="wpsp-form-group">
                    <label class="wpsp-label" for="Email"><?php esc_html_e("Email Address","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_account['Email'])){
                            $is_required =  esc_html($is_required_account['Email'],"wpschoolpress");
                        }else{
                            $is_required = true;
                        }
                        ?>
                        <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                      </label>
                    <input type="email" data-is_required="<?php echo esc_attr($is_required); ?>" class="chk-email wpsp-form-control" id="Email" name="Email">
                </div>
                <div class="wpsp-form-group">
                    <label class="wpsp-label" for="Username">
                      <?php esc_html_e("Username","wpschoolpress");
                          /*Check Required Field*/
                          if(isset($is_required_account['section']) && $is_required_account['section'] == "account" && isset($is_required_account['Username'])){
                              $is_required =  esc_html($is_required_account['Username'],"wpschoolpress");
                          }else{
                              $is_required = true;
                          }
                          ?>
                          <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                        </label>
                    <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control chk-username" id="Username" name="Username">
                </div>
                <div class="wpsp-form-group">
                    <label class="wpsp-label" for="Password"><?php esc_html_e("Password","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_account['section']) && $is_required_account['section'] == "account" && isset($is_required_account['Password'])){
                            $is_required =  esc_html($is_required_account['Password'],"wpschoolpress");
                        }else{
                            $is_required = true;
                        }
                        ?>
                        <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                      </label>
                    <input type="password" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Password" name="Password" >
                </div>
                <div class="wpsp-form-group">
                    <label class="wpsp-label" for="ConfirmPassword"><?php esc_html_e("Confirm Password","wpschoolpress");

                        /*Check Required Field*/
                        if(isset($is_required_account['section']) && $is_required_account['section'] == "account" && isset($is_required_account['ConfirmPassword'])){
                            $is_required =  esc_html($is_required_account['ConfirmPassword'],"wpschoolpress");
                        }else{
                            $is_required = true;
                        }
                        ?>
                        <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                      </label>
                    <input type="password" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="ConfirmPassword" name="ConfirmPassword" >
                </div>
                <div class="wpsp-form-group">
                  <?php
                      do_action('wpsp_after_student_account_detail_fields');
                  ?>
                </div>
                <div class="wpsp-hidden-xs">
                    <button type="submit" class="wpsp-btn wpsp-btn-success" id="studentform3"><?php echo esc_html('Next','wpschoolpress');?></button>&nbsp;&nbsp;
                    <!-- <a href="<?php echo esc_url(wpsp_admin_url());?>sch-student" class="wpsp-btn wpsp-dark-btn">Back</a> -->
                </div>
            </div>
        </div>
    </div>
    <div class="wpsp-col-lg-6 wpsp-col-md-6  wpsp-col-sm-6 wpsp-col-xs-12">
        <div class="wpsp-card">
            <div class="wpsp-card-head">
                <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_student_title_school_detail', esc_html__( 'School Details', 'wpschoolpress' )); ?></h3>
            </div>
            <div class="wpsp-card-body">
                  <?php
                          do_action('wpsp_before_student_school_detail_fields');
                          /*Required field Hook*/
                          $is_required_school = apply_filters('wpsp_student_school_is_required',array());
                      ?>
                <div class="wpsp-form-group">
                    <label class="wpsp-label" for="Doj"><?php esc_html_e("Joining Date","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_school['section']) && $is_required_school['section'] == "school" && isset($is_required_school['s_doj'])){
                            $is_required =  esc_html($is_required_school['s_doj'],"wpschoolpress");
                        }else{
                            $is_required = false;
                        }
                        ?>
                        <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                      </label>
                    <input type="text" class="wpsp-form-control select_date Doj" id="Doj" name="s_doj" value="<?php echo date('m/d/Y'); ?>" placeholder="mm/dd/yyyy" readonly>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-col-md-12 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="empcode">
                              <?php esc_html_e("Class","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_school['section']) && $is_required_school['section'] == "school" && isset($is_required_school['Class'])){
                                      $is_required =  esc_html($is_required_school['Class'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }
                                  ?>
                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                            <?php
                              $class_table = $wpdb->prefix . "wpsp_class";
                              $classes = $wpdb->get_results("select cid,c_name from $class_table");
                              $prohistory    =    wpsp_check_pro_version('wpsp_mc_version');
                              $prodisablehistory    =    !$prohistory['status'] ? 'notinstalled'    : 'installed';
                              if($prodisablehistory == 'installed'){
                                echo '<select class="selectpicker wpsp-form-control data-is_required="'.esc_attr($is_required).'"multiselect" name="Class[]" data-icon-base="fa" data-tick-icon="fa-check" multiple data-live-search="true">';
                              }else{
                                echo '<select class="wpsp-form-control" data-is_required="'.esc_attr($is_required).'"  name="Class[]">';
                                echo '<option value="" disabled selected>Select Class</option>';
                              }
                              foreach($classes as $class)
                              {
                             ?>
                              <option value="<?php echo esc_attr($class->cid); ?>"><?php echo esc_html($class->c_name); ?></option>
                          <?php
                            }
                           ?>
                          </select>
                           <div class="date-input-block">
                             <table class="table table-bordered" width="100%" cellspacing="0" cellpadding="5"></table>
                          </div>
                        </div>

                    </div>
                    <div class="wpsp-col-md-12 wpsp-col-xs-12">
                        <div class="wpsp-form-group">
                            <label class="wpsp-label" for="dateofbirth">
                              <?php esc_html_e("Roll Number","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_school['section']) && $is_required_school['section'] == "school" && isset($is_required_school['s_rollno'])){
                                      $is_required =  esc_html($is_required_school['s_rollno'],"wpschoolpress");
                                  }else{
                                      $is_required = true;
                                  }
                              ?>
                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                            <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Rollno" name="s_rollno">
                        </div>
                    </div>
                </div>
                <?php
                  do_action('wpsp_after_student_school_detail_fields');
                ?>
                <div class="wpsp-btnsubmit-section">
                  <button type="submit" class="wpsp-btn wpsp-btn-success" id="studentform4"><?php echo esc_html("Submit","wpschoolpress");?></button>&nbsp;&nbsp;
                  <a href="<?php echo esc_url(wpsp_admin_url().'sch-student');?>" class="wpsp-btn wpsp-dark-btn"><?php echo esc_html("Back","wpschoolpress");?></a>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>
<!-- End of Add New Student Form -->
