<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
    wpsp_header();
?>
<style>
.mes-dedeactivate-block{
    position: relative;
}
.mes-dedeactivate-block #message-license-deactivate{
    position: absolute;
    top: 29px;
    right: 0;
}
.mes-dedeactivate-block #multi-license-deactivate{
    position: absolute;
    top: 29px;
    right: 0;
}
.mes-dedeactivate-block #onlinepay-license-deactivate{
    position: absolute;
    top: 29px;
    right: 0;
}
</style>
<?php
    if( is_user_logged_in() ) {
        global $current_user, $wp_roles, $wpdb;
        $current_user_role=$current_user->roles[0];
        wpsp_topbar();
        wpsp_sidebar();
        wpsp_body_start();
        //$proversion   =   wpsp_check_pro_version();
        $proversion     =   wpsp_check_pro_version('wpsp_sms_version');
        $proclass       =   !$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';
        $protitle       =   !$proversion['status'] && isset( $proversion['message'] )? $proversion['message']   : '';
        $prodisable     =   !$proversion['status'] ? 'disabled="disabled"'  : '';
        $promessage    =    wpsp_check_pro_version('wpsp_message_version');
        $prodisablemessage    =    !$promessage['status'] ? 'notinstalled'    : 'installed';
        $prohistory    =    wpsp_check_pro_version('wpsp_mc_version');
        $prodisablehistory    =    !$prohistory['status'] ? 'notinstalled'    : 'installed';
        $propayment    =    wpsp_check_pro_version('pay_WooCommerce');
        $propayment    =    !$propayment['status'] ? 'notinstalled'    : 'installed';

        if($current_user_role=='administrator') {
            $ex_field_tbl   =   $wpdb->prefix."wpsp_mark_fields";
            $subject_tbl    =   $wpdb->prefix."wpsp_subject";
            $class_tbl      =   $wpdb->prefix."wpsp_class";
        ?>
        <div class="wpsp-card">
                            <?php
                            if(isset($_GET['sc'])&& sanitize_text_field($_GET['sc'])=='subField') {
                                //Fields Edit Section
                                if( isset( $_GET['sid'] ) && intval($_GET['sid'])>0 ) {
                                    $subject_id =   intval($_GET['sid']);
                                    $fields     =   $wpdb->get_results("select f.*,s.sub_name,c.c_name from $ex_field_tbl f LEFT JOIN $subject_tbl s ON s.id=f.subject_id LEFT JOIN $class_tbl c ON c.cid=s.class_id where f.subject_id='".esc_sql($subject_id)."'");
                                    ?>
                                    <div class="wpsp-card-body">
                                <div class="wpsp-row">
                                    <div class="wpsp-col-md-12 line_box wpsp-col-lg-12">
                                        <div class="wpsp-form-group">
                                        <div class="wpsp-row">
                                        <div class="wpsp-col-md-3">
                                            <label class="wpsp-labelMain"><?php _e( 'Class:', 'wpschoolpress'); ?></label> <?php echo esc_html($fields[0]->c_name);?>
                                        </div>
                                        <div class="wpsp-col-md-3">
                                            <label class="wpsp-labelMain"><?php _e( 'Subject:', 'wpschoolpress'); ?></label> <?php echo esc_html($fields[0]->sub_name);?>
                                        </div>
                                        </div>
                                        <?php wp_nonce_field( 'SubjectFields', 'subfields_nonce', '', true ) ?>
                                        <input type="hidden"  id="wpsp_locationginal" value="<?php echo admin_url();?>"/>
                                        </div>
                                        <div class="wpsp-row">
                                        <?php
                                            if(count($fields)>0){
                                                $sno=1;
                                                foreach($fields as $field){ ?>
                                                        <div class="wpsp-col-sm-6 wpsp-col-md-4">
                                                            <div class="wpsp-form-group smf-inline-form">
                                                                <input type="text" id="<?php echo intval($field->field_id);?>SF" value="<?php echo esc_attr($field->field_text);?>" class="wpsp-form-control">
                                                                <button id="sf_update" class="wpsp-btn wpsp-btn-success  SFUpdate" data-id="<?php echo esc_attr(intval($field->field_id));?>"><span class="dashicons dashicons-yes"></span></button>
                                                                <button id="d_teacher" class="wpsp-btn wpsp-btn-danger  popclick" data-pop="DeleteModal" data-id="<?php echo esc_attr(intval($field->field_id));?>"><i class="icon wpsp-trash"></i></button>
                                                          </div>
                                                        </div>
                                                <?php $sno++; }
                                            }else{
                                                echo "<div class='wpsp-col-md-8 wpsp-col-md-offset-4'>".__( 'No data retrived!', 'wpschoolpress')."</div>";
                                            }
                                        ?>
                                        </div>
                                        <a href="<?php echo esc_url(wpsp_admin_url().'sch-settings&sc=subField');?>" class="wpsp-btn wpsp-dark-btn"><?php _e( 'Back', 'wpschoolpress'); ?></a>
                                    </div>
                                    </div>
                                    </div>
                                    <style>
                                    #AddFieldsButton{display:none}
                                    </style>
                                <?php }else{
                                //Subject Mark Extract fields
                                $all_fields =   $wpdb->get_results("select mfields.subject_id, GROUP_CONCAT(mfields.field_text) AS fields,class.c_name,subject.sub_name from $ex_field_tbl mfields LEFT JOIN $subject_tbl subject ON subject.id=mfields.subject_id LEFT JOIN $class_tbl class ON class.cid=subject.class_id group by mfields.subject_id");
                            ?>

                            <div class="wpsp-card-body">
                                <div class="wpsp-row">
                                <div class="wpsp-col-md-12 wpsp-table-responsive">
                                <table id="wpsp_sub_division_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="nosort">#</th>
                                        <th><?php _e( 'Class', 'wpschoolpress'); ?></th>
                                        <th><?php _e( 'Subject', 'wpschoolpress'); ?></th>
                                        <th><?php _e( 'Fields', 'wpschoolpress'); ?></th>
                                        <th><?php _e( 'Action', 'wpschoolpress'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno=1;
                                    foreach($all_fields as $exfield){ ?>
                                        <tr>
                                            <td><?php echo esc_html($sno); ?></td><td><?php echo esc_html($exfield->c_name);?></td><td><?php echo esc_html($exfield->sub_name);?></td><td><?php echo esc_html($exfield->fields);?></td>
                                            <td>
                                                <div class="wpsp-action-col">
                                                <a href="<?php echo esc_url(wpsp_admin_url().'sch-settings&sc=subField&ac=edit&sid='.esc_attr($exfield->subject_id));?>" title="Edit"><i class="icon wpsp-edit wpsp-edit-icon"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php $sno++; } ?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th>#</th>
                                    <th><?php _e( 'Class', 'wpschoolpress'); ?></th>
                                    <th><?php _e( 'Subject', 'wpschoolpress'); ?></th>
                                    <th><?php _e( 'Fields', 'wpschoolpress'); ?></th>
                                    <th><?php _e( 'Action', 'wpschoolpress'); ?></th>
                                  </tr>
                                </tfoot>
                              </table></div>
                              </div>
                            <!--- Add Field Popup -->
                            <div class="wpsp-popupMain" id="addFieldModal" >
                                <div class="wpsp-overlayer"></div>
                                <div class="wpsp-popBody">
                                    <div class="wpsp-popInner">
                                        <a href="javascript:;" class="wpsp-closePopup"></a>
                                        <div class="wpsp-panel-heading">
                                            <h3 class="wpsp-panel-title"><?php echo apply_filters( 'wpsp_subject_mark_field_heading_item',esc_html("Add Subject Mark Fields","wpschoolpress")); ?></h3>
                                            </div>
                                            <div class="wpsp-panel-body">
                                                        <div class="wpsp-row">
                                                <form action="#" method="POST" name="SubFieldsForm" id="SubFieldsForm">
                
                                                <div class="wpsp-col-md-12 line_box">
                                                                    <div class="wpsp-row">
                                                                      <?php
                                                                        $item =  apply_filters( 'wpsp_subject_mark_field_title_item',esc_html("Class Name","wpschoolpress"));
                                                                        $is_required_item = apply_filters('wpsp_subject_mark_field_is_required',array());
                                                                      ?>
                                                                        <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                                            <div class="wpsp-form-group">
                                                                                <?php wp_nonce_field( 'SubjectFields', 'subfields_nonce', '', true ) ?>
                                                                                <label class="wpsp-label" for="Class"><?php
                                                                                      esc_html_e("Class","wpschoolpress");
                                                                                  /*Check Required Field*/
                                                                                  if(isset($is_required_item['ClassID'])){
                                                                                      $is_required =  esc_html($is_required_item['ClassID'],"wpschoolpress");
                                                                                  }else{
                                                                                      $is_required = true;
                                                                                  }
                                                                                  ?>
                                                                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                                                                                <select name="ClassID" data-is_required="<?php echo esc_attr($is_required); ?>" id="SubFieldsClass" class="wpsp-form-control">
                                                                                    <option value=""><?php esc_html_e( 'Select Class', 'wpschoolpress' ); ?></option>
                                                                                    <?php $classes=$wpdb->get_results("select cid,c_name from $class_tbl");
                                                                                        foreach($classes as $class){
                                                                                    ?>
                                                                                        <option value="<?php echo esc_attr(intval($class->cid));?>"><?php echo esc_html($class->c_name);?></option>
                                                                                        <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                                            <div class="wpsp-form-group">
                                                                                <label class="wpsp-label" for="Subject"><?php
                                                                                      esc_html_e("Subject","wpschoolpress");
                                                                                  /*Check Required Field*/
                                                                                  if(isset($is_required_item['SubjectID'])){
                                                                                      $is_required =  esc_html($is_required_item['SubjectID'],"wpschoolpress");
                                                                                  }else{
                                                                                      $is_required = true;
                                                                                  }
                                                                                  ?>
                                                                                <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span></label>
                                                                                <select name="SubjectID" data-is_required="<?php echo esc_attr($is_required); ?>" id="SubFieldSubject" class="wpsp-form-control">
                                                                                    <option value=""><?php esc_html_e( 'Select Subject', 'wpschoolpress' ); ?></option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                                            <div class="wpsp-form-group">
                                                                                <label class="wpsp-label" for="Field"><?php
                                                                                      esc_html_e("Field","wpschoolpress");
                                                                                  /*Check Required Field*/
                                                                                  if(isset($is_required_item['FieldName'])){
                                                                                      $is_required =  esc_html($is_required_item['FieldName'],"wpschoolpress");
                                                                                  }else{
                                                                                      $is_required = true;
                                                                                  }
                                                                                  ?>
                                                                                  <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                                                                                </label>
                                                                                <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" name="FieldName" class="wpsp-form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="wpsp-col-md-12">
                                                                            <button type="submit" class="wpsp-btn wpsp-btn-success"><?php echo apply_filters( 'wpsp_subject_mark_field_button_submit_text',esc_html("Submit","wpschoolpress")); ?></button>
                                                                            <button type="button" class="wpsp-btn wpsp-dark-btn" data-dismiss="modal"><?php echo apply_filters( 'wpsp_subject_mark_field_button_cancel_text',esc_html("Cancel","wpschoolpress")); ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                    </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                            </div>
                                        <!-- End popup -->
                            </div>
                            <?php
                                }
                        } else if(isset($_GET['sc'])&& sanitize_text_field($_GET['sc'])=='WrkHours') {
                                //Class Hours
                                if(isset($_POST['AddHours'])){
                                    if (!isset($_POST['wps_wrkhrs_nonce1']) || !wp_verify_nonce(sanitize_text_field($_POST['wps_wrkhrs_nonce']) , 'WPSwrkhours'))
                                    {
                                        echo "<div class='col-md-12'><div class='alert alert-danger'>".__( 'Unauthorizrd submmison or unknown nonce')."</div></div>";
                                      
                                    }
                                    wpsp_Authenticate();
                                    $workinghour_table  =   $wpdb->prefix."wpsp_workinghours";
                                    if( empty( $_POST['hname'] ) || empty( $_POST['hstart'] ) || empty( $_POST['hend'])  || sanitize_text_field($_POST['htype'])=='' ) {
                                        echo "<div class='col-md-12'><div class='alert alert-danger'>".__( 'Please fill all values.', 'wpschoolpress')."</div></div>";
                                    } elseif( strtotime( $_POST['hend'] ) <= strtotime( $_POST['hstart'] ) ) {
                                        echo "<div class='col-md-12'><div class='alert alert-danger'>".__( 'Invalid Class Time.', 'wpschoolpress')."</div></div>";
                                    } else {
                                        $workinghour_namelist = $wpdb->get_var( $wpdb->prepare( "SELECT count( * ) AS total_hour FROM $workinghour_table WHERE HOUR = %s", $_POST['hname'] ) );
                                        if( $workinghour_namelist > 0 ) {
                                            echo "<div class='col-md-12'><div class='alert alert-danger'>".__( 'Class Hour Name Already exists.', 'wpschoolpress')."</div></div>";
                                        } else {
                                                    $workinghour_table_data = array('hour' =>  sanitize_text_field($_POST['hname']),
                                                    'begintime' =>  sanitize_text_field( $_POST['hstart'] ),
                                                    'endtime'   =>  sanitize_text_field( $_POST['hend'] ),
                                                    'type'      =>  sanitize_text_field( $_POST['htype'] )
                                                    );
                                            $ins=$wpdb->insert( $workinghour_table,$workinghour_table_data);
                                        }
                                    }
                                }
                                if( isset($_GET['ac']) && sanitize_text_field($_GET['ac'])=='DeleteHours' ) {
                                    $workinghour_table=$wpdb->prefix."wpsp_workinghours";
                                    $hid=intval($_GET['hid']);
                                    $del=$wpdb->delete($workinghour_table,array('id'=>$hid));
                                }
                                //Save hours
                            ?>
                            <div class="wpsp-card-body">
                            <form name="working_hour" method="post" action="">
                                <?php wp_nonce_field( 'WPSwrkhours', 'wps_wrkhrs_nonce', '', true ); ?>
                                <div class="wpsp-form-group">
                                            <h3 class="wpsp-card-title"><?php echo apply_filters('wpsp_workinghours_heading_item',esc_html__( 'Class hours', 'wpschoolpress'));?></h3>
                                        </div>
                                            <div class="wpsp-row">
                                              <?php
                                                  do_action("wpsp_workinghours_before");
                                                  $item =  apply_filters( 'wpsp_setting_workinghours_title_item',array());
                                              ?>
                                                <div class="wpsp-col-md-4">
                                                    <div class="wpsp-form-group">
                                                        <label class="wpsp-label"><?php
                                                              esc_html_e("Class Hour Name","wpschoolpress");
                                                      ?></label>
                                                        <input type="text" name="hname" class="wpsp-form-control">
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-2 wpsp-col-sm-6">
                                                    <div class="wpsp-form-group">
                                                        <label class="wpsp-label"><?php
                                                              esc_html_e("From","wpschoolpress");
                                                      ?></label>
                                                        <input type="text" name="hstart" class="wpsp-form-control"  id="timepicker1">
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-2 wpsp-col-sm-6">
                                                    <div class="wpsp-form-group">
                                                        <label class="wpsp-label"><?php
                                                              esc_html_e("To","wpschoolpress");
                                                      ?></label>
                                                        <input type="text" name="hend" class="wpsp-form-control"  id="wp-end-time" data-provide="timepicker">
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-4">
                                                    <div class="wpsp-form-group">
                                                        <label class="wpsp-label"><?php
                                                              esc_html_e("Type","wpschoolpress");
                                                      ?></label>
                                                        <select name="htype" class="wpsp-form-control">
                                                            <option value="1"><?php esc_html_e( 'Teaching', 'wpschoolpress' ); ?></option>
                                                            <option value="0"><?php esc_html_e( 'Break', 'wpschoolpress' ); ?></option>
                                                        </select>
                                                     </div>
                                                </div>
                                                <div class="wpsp-col-md-12">
                                                    <div class="wpsp-form-group">
                                                        <button type="submit" class="wpsp-btn wpsp-btn-success" name="AddHours" value="AddHours"><i class="fa fa-plus"></i>&nbsp; <?php esc_html_e( 'Add Hour', 'wpschoolpress' ); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php do_action("wpsp_workinghours_after"); ?>
                                </form>
                                    <table class="wpsp-table" id="wpsp_class_hours" cellspacing="0" width="100%" style="width:100%">
                                        <thead><tr>
                                            <th><?php esc_html_e( 'Class Hour', 'wpschoolpress' ); ?></th>
                                            <th><?php esc_html_e( 'Begin Time', 'wpschoolpress' ); ?></th>
                                            <th><?php esc_html_e( 'End Time', 'wpschoolpress' ); ?></th>
                                            <th><?php esc_html_e( 'Type', 'wpschoolpress' ); ?></th>
                                            <th class="nosort"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
                                        </tr> </thead>
                                        <tbody>
                                            <?php
                                                $htypes=array('Break','Teaching');
                                                $workinghour_table=$wpdb->prefix."wpsp_workinghours";
                                                $workinghour_list =$wpdb->get_results("SELECT * FROM $workinghour_table") ;
                                                    foreach ($workinghour_list as $single_workinghour) {
                                                        $hourtype=$htypes[$single_workinghour->type]; ?>
                                                    <tr> <td><?php echo esc_html(stripslashes( $single_workinghour->hour) ) ?></td>
                                                            <td><?php echo esc_html($single_workinghour->begintime) ?></td>
                                                            <td><?php echo esc_html($single_workinghour->endtime) ?></td>
                                                            <td><?php echo esc_html($hourtype) ?></td>
                                                            <td>
                                                                <div class="wpsp-action-col">
                                                                    <a href="<?php echo esc_url(wpsp_admin_url().'sch-settings&sc=WrkHours&ac=DeleteHours&hid='.esc_attr(intval($single_workinghour->id))); ?>" class="delete"><i class="icon wpsp-trash wpsp-delete-icon"></i></a>
                                                                </div>
                                                            </td>
                                                            </tr>
                                                <?php   }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th><?php esc_html_e( 'Class Hour', 'wpschoolpress' ); ?> </th>
                                                <th><?php esc_html_e( 'Begin Time', 'wpschoolpress' ); ?></th>
                                                <th><?php esc_html_e( 'End Time', 'wpschoolpress' ); ?></th>
                                                <th><?php esc_html_e( 'Type', 'wpschoolpress' ); ?></th>
                                                <th class="nosort"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
                                            </tr>
                                        </tfoot>
                                </table>
                                </div>
                                <?php
                            }else{
                                //General Settings
                                $wpsp_settings_table    =   $wpdb->prefix."wpsp_settings";
                                $wpsp_settings_edit     =   $wpdb->get_results("SELECT * FROM $wpsp_settings_table" );
                                foreach( $wpsp_settings_edit as $sdat ) {
                                    $settings_data[$sdat->option_name]  =   $sdat->option_value;
                                }
                            ?>
                            <div class="wpsp-card-body">
                            <div class="tabSec wpsp-nav-tabs-custom" id="verticalTab">
                            <div class="tabList">
                                <ul class="wpsp-resp-tabs-list">
                                    <li class="wpsp-tabing" title="Info"><?php echo apply_filters( 'wpsp_settings_tab_info_heading', esc_html__( 'Info', 'wpschoolpress' )); ?></li>
                                    <li class="wpsp-tabing <?php echo esc_attr($proclass); ?>" title="<?php echo esc_attr($protitle);?>" <?php echo esc_html($prodisable); ?> title="An overdose in each drop"><?php echo apply_filters( 'wpsp_settings_tab_sms_heading', esc_html__( 'SMS', 'wpschoolpress' )); ?></li>
                                    <li class="wpsp-tabing"  title="Licensing"><?php echo apply_filters( 'wpsp_settings_tab_info_heading', esc_html__( 'Licensing', 'wpschoolpress' )); ?></li>
                                </ul>
                            </div>
                                <div class="wpsp-tabBody wpsp-resp-tabs-container">
                                    <div class="wpsp-tabMain">
                                        <form name="schinfo_form" id="SettingsInfoForm" class="wpsp-form-horizontal" method="post">
                                          <?php wp_nonce_field( 'SettingsFields', 'setingssub_nonce', '', true ) ?>
                                          <?php do_action('wpsp_before_setting_info');
                                            $item =  apply_filters( 'wpsp_setting_info_title_item',array());
                                          ?>
                                            <div  class="wpsp-row">
                                            <div  class="wpsp-form-group">
                                                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">

                                                      <label class="wpsp-label"><?php
                                                              esc_html_e("School Logo","wpschoolpress");
                                                      ?></label>
                                                        <div class="wpsp-profileUp">
                                                          <?php
                                                          $url = plugins_url( 'img/wpschoolpresslogo.jpg', dirname(__FILE__) );
                                                          ?>
                                                            <img src="<?php  echo isset( $settings_data['sch_logo'] ) ? esc_url($settings_data['sch_logo']) : esc_url($url);?>" id="img_preview" onchange="imagePreview(this);" height="150px" width="150px" class="wpsp-upAvatar" />
                                                            <div class="wpsp-upload-button"><i class="fa fa-camera"></i>
                                                            <input type="hidden" name="old_img" id="old_image" value="<?php  echo isset( $settings_data['sch_logo'] ) ? esc_attr($settings_data['sch_logo']) : esc_attr($url);?>">
                                                            <input name="displaypicture" class="wpsp-file-upload" id="displaypicture" type="file" accept="image/jpg, image/jpeg, image/png" >
                                                            </div>
                                                        </div>
                                                        <p class="wpsp-form-notes">* <?php esc_html_e( 'Only JPEG, JPG and PNG supported, * Max 3 MB Upload', 'wpschoolpress' ); ?> </p>
                                                        <label id="displaypicture-error" class="error" for="displaypicture" style="display: none;"><?php esc_html_e( 'Please Upload Profile Image', 'wpschoolpress' ); ?></label>
                                                        <p id="test" style="color:red"></p>
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-3 wpsp-col-md-8 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                        <label class="wpsp-label"><?php
                                                              esc_html_e("School Name","wpschoolpress");
                                                      ?></label>
                                                        <input type="text" name="sch_name" class="wpsp-form-control" value="<?php echo stripslashes(isset( $settings_data['sch_name'] ) ? esc_attr($settings_data['sch_name']) : '');?>">
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                        <label class="wpsp-label" for="phone"><?php
                                                              esc_html_e("Phone","wpschoolpress");
                                                      ?></label>
                                                        <input type="text" class="wpsp-form-control" id="phone" name="Phone" placeholder="(XXX)-(XXX)-(XXXX)"  value="<?php echo isset( $settings_data['sch_pno'] ) ? esc_attr($settings_data['sch_pno']) : '';?>">
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                        <label class="wpsp-label" for="phone"><?php
                                                              esc_html_e("Email Address","wpschoolpress");
                                                      ?></label>
                                                        <input type="text" class="wpsp-form-control" id="email" name="email" placeholder="Email" value="<?php echo isset( $settings_data['sch_email'] ) ? esc_attr($settings_data['sch_email']) : '';?>">
                                                    </div>
                                                </div>
                                                <div class="wpsp-col-lg-9 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">

                                                        <label class="wpsp-label" for="Address"><?php
                                                            esc_html_e("Address","wpschoolpress");
                                                      ?><!-- <span class="wpsp-required">*</span> --></label>
                                                        <textarea rows="2" cols="45" name="sch_addr" class="wpsp-form-control"><?php echo isset( $settings_data['sch_addr'] ) ? esc_attr($settings_data['sch_addr']) : '';?></textarea>
                                                    </div>
                                                </div>
                                             <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php
                                                            esc_html_e("City","wpschoolpress");
                                                      ?></label>
                                                    <input type="text" name="sch_city"  class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_city'] ) ? esc_attr($settings_data['sch_city']) : '';?>">
                                                </div>
                                            </div>
                                             <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">

                                                    <label class="wpsp-label"><?php
                                                            esc_html_e("State","wpschoolpress");
                                                      ?></label>
                                                    <input type="text" name="sch_state" class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_state'] ) ? esc_attr($settings_data['sch_state']) : '';?>">
                                                </div>
                                            </div>
                                              <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                <label class="wpsp-label" for="Country"><?php
                                                            esc_html_e("Country","wpschoolpress");
                                                      ?></label>
                                                 <select class="wpsp-form-control" id="Country" name="country">
                                                    <option value=""><?php esc_html_e( 'Select Country', 'wpschoolpress' ); ?></option>
                                                <?php $country = wpsp_county_list();
                                                //print_r($country);
                                                foreach ($country as $key => $value) {?>
                                                        <option value=<?php echo esc_attr($key);?><?php  if($key == $settings_data['sch_country']){ echo ' selected';}?>><?php echo esc_html($value); ?></option>
                                                <?php }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                             <div class="wpsp-col-lg-3 wpsp-col-md-6 wpsp-col-sm-6 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php
                                                              esc_html_e("Fax","wpschoolpress");
                                                      ?></label>
                                                    <input type="text" name="sch_fax"  class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_fax'] ) ? esc_attr($settings_data['sch_fax']) :'';?>">
                                                </div>
                                            </div>
                                              <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php
                                                              esc_html_e("Website","wpschoolpress");
                                                      ?></label>
                                                    <input type="text" name="sch_website"   class="wpsp-form-control" value="<?php echo isset( $settings_data['sch_website'] ) ? esc_attr($settings_data['sch_website']) : '';?>">
                                                    <input type="hidden" name="type"  value="info">
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                                    <div class="wpsp-form-group">

                                                    <label class="wpsp-label"><?php
                                                              esc_html_e("Date Format","wpschoolpress");
                                                      ?></label>
                                                    <select name="date_format"  class="wpsp-form-control">
                                                        <option value="m/d/Y" <?php echo  isset( $settings_data['date_format'] ) && ( $settings_data['date_format']=='m/d/Y')?'selected':''?>><?php esc_html_e( 'mm/dd/yyyy', 'wpschoolpress' ); ?></option>
                                                        <option value="Y-m-d" <?php echo  isset( $settings_data['date_format'] ) && ($settings_data['date_format']=='Y-m-d')?'selected':''?> ><?php esc_html_e( 'yyyy-mm-dd', 'wpschoolpress' ); ?></option>
                                                        <option value="d-m-Y" <?php echo  isset( $settings_data['date_format'] ) && ($settings_data['date_format']=='d-m-Y')?'selected':''?>><?php esc_html_e( 'dd-mm-yyyy', 'wpschoolpress' ); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                                                   <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php
                                                             esc_html_e("Marks Type","wpschoolpress");
                                                     ?></label>
                                                    <select name="markstype" class="wpsp-form-control">
                                                        <option value="Number" <?php echo  isset( $settings_data['markstype'] ) && ( $settings_data['markstype']=='Number')?'selected':''?>><?php esc_html_e( 'Number', 'wpschoolpress' ); ?> </option>
                                                        <option value="Grade" <?php echo  isset( $settings_data['markstype'] ) && ($settings_data['markstype']=='Grade')?'selected':''?>><?php esc_html_e( 'Grade', 'wpschoolpress' ); ?> </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                            <div class="wpsp-form-group <?php echo esc_attr($proclass); ?>" title="<?php echo esc_attr($protitle);?>" <?php echo esc_attr($prodisable); ?>>
                                                    <label class="wpsp-label"><?php _e( 'SMS Setting' ,'wpschoolpress'); ?></label>
                                                    <input id="absent_sms_alert" type="checkbox" class="wpsp-checkbox ccheckbox <?php echo esc_attr($proclass); ?> " title="<?php echo esc_attr($protitle);?>" <?php echo esc_html($prodisable); ?> <?php if(isset($settings_data['absent_sms_alert']) && $settings_data['absent_sms_alert']==1) echo "checked"; ?> name="absent_sms_alert" value="1" >
                                                    <label for="absent_sms_alert" class="wpsp-checkbox-label"> <?php _e( 'Send SMS to parent when student absent','wpschoolpress');?></label>
                                                    <input id="notification_sms_alert" type="checkbox" class="wpsp-checkbox ccheckbox <?php echo esc_attr($proclass); ?>" title="<?php echo esc_attr($protitle);?>" <?php echo esc_html($prodisable); ?> <?php if(isset($settings_data['notification_sms_alert']) && $settings_data['notification_sms_alert']==1) echo "checked"; ?> name="notification_sms_alert" value="1" >
                                                    <label for="notification_sms_alert" class="wpsp-checkbox-label"> <?php _e( 'Enable SMS Notification','wpschoolpress');?></label>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-md-12 wpsp-col-sm-12 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                    <button type="submit" class="wpsp-btn wpsp-btn-success" id="setting_submit" name="submit" style="margin-top: 20px;!important" > <?php esc_html_e( 'Save', 'wpschoolpress' ); ?></button>
                                                </div>
                                            </div>
                                    </div>
                                    </div>
                                      <?php
                                        do_action('wpsp_after_setting_info');
                                      ?>
                                        </form>
                                    </div>

                                    <div class="wpsp-tabMain">
                                            <?php
                                        if( isset( $proversion['status'] ) && $proversion['status'] ) {
                                            do_action( 'wpsp_sms_setting_html', $settings_data );
                                        } else {
                                            _e( 'Please Purchase This <a href="https://wpschoolpress.com/downloads/sms-add-on-wpschoolpress/" target="_blank">Add-on</a>', 'wpschoolpress' );
                                        }
                                        ?>
                                    </div>
                                    <div class="wpsp-tabMain">
                                            <form name="Settingslicensing" id="Settingslicensing" class="wpsp-form-horizontal" method="post">
                                            <?php wp_nonce_field( 'SettingsLisence', 'wps_setlisence_nonce', '', true ) ?>
                                            <div class="wpsp-row">
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php  do_action('wpsp_before_license');
                                                //    $item =  apply_filters( 'wpsp_setting_licensing_title_item',array());

                                                    esc_html_e("Import Export","wpschoolpress");
                                                ?></label>
                                                    <input type="text" name="importexport"  class="wpsp-form-control" value="<?php echo isset( $settings_data['importexport'] ) ? esc_attr($settings_data['importexport']) : '';?>">
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php
                                                          esc_html_e("SMS Addons: ","wpschoolpress");?></label>
                                                    <input type="text" name="smsaddons"  class="wpsp-form-control" value="<?php echo isset( $settings_data['smsaddons'] ) ? esc_attr($settings_data['smsaddons']) : '';?>">
                                                </div>
                                            </div>
                                                <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group">
                                                    <label class="wpsp-label"><?php esc_html_e("Front End Registration Addons: ","wpschoolpress");?></label>
                                                    <input type="text" name="feraddons"  class="wpsp-form-control" value="<?php echo isset( $settings_data['feraddons'] ) ? esc_attr($settings_data['feraddons']) : '';?>">
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group mes-dedeactivate-block">

                                                    <label class="wpsp-label"><?php esc_html_e("Dashboard to Dashboard Message Addons : ","wpschoolpress");?></label>
                                                    <input type="text" name="ddma"  class="wpsp-form-control" value="<?php echo isset( $settings_data['ddma'] ) ? esc_attr($settings_data['ddma']) : '';?>">
                                                    <?php if($prodisablemessage == 'installed'){ ?>
                                                    <button type="button" id="message-license-deactivate" class="wpsp-btn wpsp-btn-denger"><?php echo esc_html("Deactivate","wpschoolpress");?></button>
                                                <?php } ?>
                                                </div>
                                            </div>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group mes-dedeactivate-block">
                                                <label class="wpsp-label"><?php esc_html_e("Multi-class Add-on :","wpschoolpress");?></label>
                                                    <input type="text" name="mcaon"  class="wpsp-form-control" value="<?php echo isset( $settings_data['mcaon'] ) ? esc_attr($settings_data['mcaon']) : '';?>">
                                                    <?php if($prodisablehistory == 'installed'){ ?>
                                                    <button type="button" id="multi-license-deactivate" class="wpsp-btn wpsp-btn-denger"><?php echo esc_html("Deactivate","wpschoolpress");?></button>
                                                <?php } ?>
                                                </div>
                                            </div>
                                             <!-- Add Payment Lisenece button -->
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group mes-dedeactivate-block">
                                                    <label class="wpsp-label"><?php esc_html_e("Online Fee Payment Addons :","wpschoolpress");?></label>
                                                    <input type="text" name="onlinepay"  class="wpsp-form-control" value="<?php echo isset( $settings_data['onlinepay'] ) ? esc_attr($settings_data['onlinepay']) : '';?>">

                                                    <?php if($propayment == 'installed'){ ?>

                                                    <!-- <button type="button" id="onlinepay-license-deactivate" class="wpsp-btn wpsp-btn-denger"></?php echo esc_html("Deactivate","wpschoolpress");?></button> -->

                                                <?php } ?>

                                                </div>

                                            </div>

                                            <!-- Add Social Media Lisenece button -->
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-4 wpsp-col-xs-12">
                                                <div class="wpsp-form-group mes-dedeactivate-block">
                                                    <label class="wpsp-label"><?php esc_html_e("Social Posts Addons :","wpschoolpress");?></label>
                                                    <input type="text" name="socialmedia"  class="wpsp-form-control" value="<?php echo isset( $settings_data['socialmedia'] ) ? esc_attr($settings_data['socialmedia']) : '';?>">
                                                    
                                                </div>
                                            </div>
                                            
                                            <?php  do_action('wpsp_after_license'); ?>
                                            <div class="wpsp-col-lg-12 wpsp-col-md-12 wpsp-col-sm-12">
                                                <div class="wpsp-form-group">
                                                    <button type="submit" id="s_save" class="wpsp-btn wpsp-btn-success" name="submit"><?php echo apply_filters( 'wpsp_setting_licensig_button_submit_text',esc_html__('Save','wpschoolpress'));?></button>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--    </div> -->
                            <?php } ?>
                        </div>
            <?php } else if($current_user_role=='parent' || $current_user_role=='student') {
                }
        wpsp_body_end();
        wpsp_footer(); ?>
    <?php
    }else {
        include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');
    }
?>
