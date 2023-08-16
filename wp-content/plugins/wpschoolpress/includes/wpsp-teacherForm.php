<?php if (!defined( 'ABSPATH' ) )exit('No Such File');?>
<!-- This form is used for Add New Teacher -->
<div id="formresponse"></div>
<form name="TeacherEntryForm" id="TeacherEntryForm" method="post">
    <div class="wpsp-row">
        <div class="wpsp-col-sm-12">
            <div class="wpsp-card">
                <div class="wpsp-card-head">
                    <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_teacher_title_personal_detail', esc_html__( 'Personal Details', 'wpschoolpress' )); ?></h3>
                </div>
                <div class="wpsp-card-body"> <?php wp_nonce_field( 'TeacherRegister', 'tregister_nonce', '', true ) ?> <div class="wpsp-row"> <?php
                      do_action('wpsp_before_teacher_personal_detail_fields');
                      $is_required_item = apply_filters('wpsp_teacher_personal_is_required',array());
                     ?> <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label"> <?php esc_html_e("Profile Image","wpschoolpress"); ?> </label>
                                <div class="wpsp-profileUp"><?php $turl = WPSP_PLUGIN_URL . 'img/default_avtar.jpg';?> <img class="wpsp-upAvatar" id="img_preview_teacher" src="<?php echo esc_url($turl);?>">
                                    <div class="wpsp-upload-button"><i class="fa fa-camera"></i><input name="displaypicture" class="wpsp-file-upload" id="displaypicture" type="file" accept="image/jpg, image/jpeg" /></div>
                                </div>
                                <p class="wpsp-form-notes">* <?php echo esc_html("Only JPEG and JPG supported, * Max 3 MB Upload ","wpschoolpress");?></p>
                                <label id="displaypicture-error" class="error" for="displaypicture" style="display: none;"><?php echo esc_html("Please Upload Profile Image","wpschoolpress");?></label>
                                <p id="test" style="color:red"></p>
                            </div>
                        </div>
                        <div class="wpsp-col-lg-9 wpsp-col-md-8 wpsp-col-sm-12 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="gender"> <?php esc_html_e("Gender","wpschoolpress");?></label>
                                <div class="wpsp-radio-inline">
                                    <div class="wpsp-radio">
                                        <input type="radio" name="Gender" value="Male" checked="checked" id="Male">
                                        <label for="Male"><?php echo esc_html_e("Male","wpschoolpress");?></label>
                                    </div>
                                    <div class="wpsp-radio">
                                        <input type="radio" name="Gender" value="Female" id="Female">
                                        <label for="Female"><?php echo esc_html_e("Female","wpschoolpress");?></label>
                                    </div>
                                    <div class="wpsp-radio">
                                        <input type="radio" name="Gender" value="other" id="other">
                                        <label for="other"><?php echo esc_html_e("Other","wpschoolpress");?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix wpsp-ipad-show"></div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="firstname"> <?php esc_html_e("First Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['firstname'])){
                                      $is_required =  esc_html($is_required_item['firstname'],"wpschoolpress");
                                  }else{
                                      $is_required = true;
                                  }
                                  ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="firstname" name="firstname">
                                <input type="hidden" id="wpsp_locationginal" value="<?php echo esc_url(admin_url());?>" />
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="middlename"> <?php esc_html_e("Middle Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['firstname'])){
                                      $is_required =  esc_html($is_required_item['middlename'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }
                                  ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="middlename" name="middlename">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="lastname"> <?php esc_html_e("Last Name","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['lastname'])){
                                      $is_required =  esc_html($is_required_item['lastname'],"wpschoolpress");
                                  }else{
                                      $is_required = true;
                                  }
                                  ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="lastname" name="lastname">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="dateofbirth"><?php esc_html_e("Date of Birth","wpschoolpress");
                                  /*Check Required Field*/
                                  if(isset($is_required_item['Dob'])){
                                      $is_required =  esc_html($is_required_item['Dob'],"wpschoolpress");
                                  }else{
                                      $is_required = false;
                                  }
                                  ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                                <input type="text" class="wpsp-form-control select_date" data-is_required="<?php echo esc_attr($is_required); ?>" id="Dob" name="Dob" placeholder="mm/dd/yyyy" readonly>
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="bloodgroup"> <?php esc_html_e("Blood Group","wpschoolpress");
                                    /*Check Required Field*/
                                    if(isset($is_required_item['Bloodgroup'])){
                                        $is_required =  esc_html($is_required_item['Bloodgroup'],"wpschoolpress");
                                    }else{
                                        $is_required = false;
                                    }
                                    ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                                <select data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Bloodgroup" name="Bloodgroup">
                                    <option value=""><?php echo __("Select Blood Group","wpschoolpress");?></option>
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
                                <label class="wpsp-label" for="phone"><?php esc_html_e("Phone","wpschoolpress");
                                    /*Check Required Field*/
                                    if(isset($is_required_item['Phone'])){
                                        $is_required =  esc_html($is_required_item['Phone'],"wpschoolpress");
                                    }else{
                                        $is_required = false;
                                    }
                                    ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="phone" name="Phone" placeholder="(XXX)-(XXX)-(XXXX)">
                            </div>
                        </div>
                        <div class="wpsp-col-lg-3 wpsp-col-md-8 wpsp-col-sm-8 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="educ"><?php esc_html_e("Qualification","wpschoolpress");

                                    /*Check Required Field*/
                                    if(isset($is_required_item['Qual'])){
                                        $is_required =  esc_html($is_required_item['Qual'],"wpschoolpress");
                                    }else{
                                        $is_required = false;
                                    }
                                    ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Qual" name="Qual">
                            </div>
                        </div>
                        <div class="wpsp-col-xs-12">
                            <hr />
                            <h4 class="card-title mt-5"><?php echo esc_html("Address","wpschoolpress");?></h4>
                        </div>
                        <div class="wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Address"> <?php esc_html_e("Street Address ","wpschoolpress");
                                      /*Check Required Field*/
                                      if(isset($is_required_item['Address'])){
                                          $is_required =  esc_html($is_required_item['Address'],"wpschoolpress");
                                      }else{
                                          $is_required = true;
                                      }
                                      ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" name="Address" class="wpsp-form-control" />
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group ">
                                <label class="wpsp-label" for="CityName"> <?php esc_html_e("City Name","wpschoolpress");
                                      /*Check Required Field*/
                                      if(isset($is_required_item['city'])){
                                          $is_required =  esc_html($is_required_item['city'],"wpschoolpress");
                                      }else{
                                          $is_required = false;
                                      }
                                      ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="CityName" name="city">
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Country"><?php esc_html_e("Country","wpschoolpress");
                                      /*Check Required Field*/
                                      if(isset($is_required_item['country'])){
                                          $is_required =  esc_html($is_required_item['country'],"wpschoolpress");
                                      }else{
                                          $is_required = false;
                                      }
                                      ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label> <?php $countrylist = wpsp_county_list();?> <select data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Country" name="country">
                                    <option value=""><?php echo esc_html("Select Country","wpschoolpress");?></option> <?php foreach( $countrylist as $key=>$value ) { ?> <option value="<?php echo esc_attr($value);?>"><?php echo esc_html($value);?></option> <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="Zipcode"> <?php  esc_html_e("Pin Code","wpschoolpress");
                                        /*Check Required Field*/
                                        if(isset($is_required_item['zipcode'])){
                                            $is_required =  esc_html($is_required_item['zipcode'],"wpschoolpress");
                                        }else{
                                            $is_required = false;
                                        }
                                        ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                </label>
                                <input type="text" class="wpsp-form-control" id="Zipcode" name="zipcode">
                            </div>
                        </div> <?php  do_action('wpsp_after_teacher_personal_detail_fields'); ?> <div class="wpsp-col-xs-12 wpsp-hidden-xs">
                            <button type="submit" class="wpsp-btn wpsp-btn-success" id="teacherform"><?php echo esc_html("Next","wpschoolpress");?></button>&nbsp;&nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpsp-col-md-6 wpsp-col-sm-12">
            <div class="wpsp-card">
                <div class="wpsp-card-head">
                    <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_teacher_title_account_detail', esc_html__( 'Account Information', 'wpschoolpress' )); ?></h3>
                </div>
                <div class="wpsp-card-body"> <?php  do_action('wpsp_before_teacher_account_detail_fields');
              /*Required field Hook*/
              $is_required_parent = apply_filters('wpsp_teacher_account_is_required',array());
              ?> <div class="wpsp-form-group">
                        <label class="wpsp-label" for="Email"><?php esc_html_e("Email Address","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_parent['Email'])){
                            $is_required =  esc_html($is_required_parent['Email'],"wpschoolpress");
                        }else{
                            $is_required = true;
                        }
                        ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                        <input type="email" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Email" name="Email">
                    </div>
                    <div class="wpsp-form-group">
                        <label class="wpsp-label" for="Username"><?php esc_html_e("Username","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_parent['Username'])){
                            $is_required =  esc_html($is_required_parent['Username'],"wpschoolpress");
                        }else{
                            $is_required = true;
                        }
                        ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                        <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Username" name="Username">
                    </div>
                    <div class="wpsp-form-group">
                        <label class="wpsp-label" for="Password"><?php esc_html_e("Password","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_parent['Password'])){
                            $is_required =  esc_html($is_required_parent['Password'],"wpschoolpress");
                        }else{
                            $is_required = true;
                        }
                        ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                        <input type="password" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Password" name="Password">
                    </div>
                    <div class="wpsp-form-group">
                        <label class="wpsp-label" for="ConfirmPassword"> <?php esc_html_e("Confirm Password","wpschoolpress");
                          /*Check Required Field*/
                          if(isset($is_required_parent['ConfirmPassword'])){
                              $is_required =  esc_html($is_required_parent['ConfirmPassword'],"wpschoolpress");
                          }else{
                              $is_required = true;
                          }
                          ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                        <input type="password" class="wpsp-form-control" id="ConfirmPassword" name="ConfirmPassword" placeholder="Confirm Password">
                    </div> <?php  do_action('wpsp_after_teacher_account_detail_fields'); ?> <div class="wpsp-hidden-xs">
                        <button type="submit" class="wpsp-btn wpsp-btn-success" id="teacherform"><?php echo esc_html("Next","wpschoolpress");?></button>&nbsp;&nbsp;
                    </div>
                </div>
            </div>
        </div>
        <div class="wpsp-col-md-6 wpsp-col-sm-12">
            <div class="wpsp-card">
                <div class="wpsp-card-head">
                    <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_teacher_title_school_detail', esc_html__( 'School Details', 'wpschoolpress' )); ?></h3>
                </div>
                <div class="wpsp-card-body"> <?php  do_action('wpsp_before_teacher_school_detail_fields');
                   /*Required field Hook*/
                   $is_required_school = apply_filters('wpsp_teacher_school_is_required',array());
                   ?> <div class="wpsp-form-group">
                        <label class="wpsp-label" for="Doj"><?php esc_html_e("Joining Date","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_school['Doj'])){
                            $is_required =  esc_html($is_required_school['Doj'],"wpschoolpress");
                        }else{
                            $is_required = false;
                        }
                        ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                        <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control select_date Doj" id="Doj" name="Doj" value="" placeholder="mm/dd/yyyy" readonly>
                    </div>
                    <div class="wpsp-form-group">
                        <label class="wpsp-label" for="Dol"><?php esc_html_e("Leaving Date","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_school['dol'])){
                            $is_required =  esc_html($is_required_school['dol'],"wpschoolpress");
                        }else{
                            $is_required = false;
                        }
                        ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                        <input type="text" class="wpsp-form-control select_date Dol" id="Dol" name="dol" value="" placeholder="mm/dd/yyyy" readonly>
                    </div>
                    <div class="wpsp-form-group">
                        <label class="wpsp-label" for="position"><?php esc_html_e("Current Position","wpschoolpress");
                        /*Check Required Field*/
                        if(isset($is_required_school['Position'])){
                            $is_required =  esc_html($is_required_school['Position'],"wpschoolpress");
                        }else{
                            $is_required = false;
                        }
                        ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                        <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" class="wpsp-form-control" id="Position" name="Position">
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-col-md-6 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="empcode"><?php esc_html_e("Employee Code","wpschoolpress");
                                /*Check Required Field*/
                                if(isset($is_required_school['EmpCode'])){
                                    $is_required =  esc_html($is_required_school['EmpCode'],"wpschoolpress");
                                }else{
                                    $is_required = false;
                                }
                                ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                                <input type="text" class="wpsp-form-control" id="EmpCode" name="EmpCode">
                            </div>
                        </div>
                        <div class="wpsp-col-md-6 wpsp-col-xs-12">
                            <div class="wpsp-form-group">
                                <label class="wpsp-label" for="whours"><?php esc_html_e("Working Hours","wpschoolpress");
                                /*Check Required Field*/
                                if(isset($is_required_school['whours'])){
                                    $is_required =  esc_html($is_required_school['whours'],"wpschoolpress");
                                }else{
                                    $is_required = true;
                                }
                                ?>
                                    <!-- <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span> -->
                                </label>
                                <input type="text" class="wpsp-form-control" id="whours" name="whours">
                            </div>
                        </div>
                    </div> <?php  do_action('wpsp_after_teacher_school_detail_fields'); ?> <div class="wpsp-btnsubmit-section">
                        <button type="submit" class="wpsp-btn wpsp-btn-success" id="teacherform"><?php echo esc_html("Submit","wpschoolpress");?></button>&nbsp;&nbsp; <a href="<?php echo esc_url(wpsp_admin_url().'sch-teacher')?>" class="wpsp-btn wpsp-dark-btn"><?php echo esc_html("Back","wpschoolpress");?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- End of Add New Teacher Form -->