<?php

if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
    if( is_user_logged_in() ) {
        global $current_user, $wp_roles, $wpdb;
        $current_user_role=$current_user->roles[0];
        if($current_user_role=='administrator' || $current_user_role=='teacher') {
            wpsp_topbar();
            wpsp_sidebar();
            wpsp_body_start();
            ?>

    <div class="wpsp-card">
        <div class="wpsp-card-head">
            <div class="subject-inner wpsp-left wpsp-class-filter">

                <form name="StudentClass" id="StudentClass" method="post" action="">
                <label class="wpsp-labelMain"><?php _e( 'Select Role:', 'wpschoolpress' ); ?></label>
                <select name="ClassID" id="ClassID" class="wpsp-form-control">
                    <?php
                    $sel_classid    =   isset( $_POST['ClassID'] ) ? intval($_POST['ClassID']) : '';
                    $temp_table =   $wpdb->prefix."wpsp_temp";
                    $sel_class      =   $wpdb->get_results("select t_type from $temp_table Order By t_id ASC");
                    $usertype = array();
                    ?>
                    <?php if($current_user_role=='administrator' ) { ?>
                    <option value="all" <?php if($sel_classid=='all') echo esc_html("selected","wpschoolpress"); ?>><?php _e( 'All', 'wpschoolpress' ); ?></option>
                    <?php } foreach( $sel_class as $classes ) {
                        if (in_array($classes->t_type, $usertype)) {} else { ?>
                        <option value="<?php echo esc_attr($classes->t_type);?>" <?php if($sel_classid==$classes->t_type) echo esc_html("selected","wpschoolpress"); ?>><?php echo ucfirst($classes->t_type);?></option>
                    <?php } array_push($usertype, $classes->t_type); }  ?>
                </select>
                </form>
            </div>
        </div>
    <div class="wpsp-card-body">
                <div class="subject-head">
                    <div class="wpsp-bulkaction">
                        <select name="bulkaction" class="wpsp-form-control" id="bulkactionreqest">
                            <option value=""><?php esc_html_e( 'Select Action', 'wpschoolpress' ); ?></option>
                            <option value="bulkUsersApprove"><?php esc_html_e( 'Approve', 'wpschoolpress' ); ?></option>
                            <option value="bulkUsersDisapprove"><?php esc_html_e( 'Disapprove', 'wpschoolpress' ); ?></option>
                        </select>
                    </div>
                <table id="request_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
                <thead>
                    <tr>
                        <th class="nosort">
                        <input type="checkbox" id="selectall" name="selectall" class="ccheckbox">
                        </th>
                        <th><?php esc_html_e( 'Full Name', 'wpschoolpress' ); ?></th>
                        <th class="sort"><?php esc_html_e( 'Email', 'wpschoolpress' ); ?> </th>
                        <th><?php esc_html_e( 'User Role', 'wpschoolpress' ); ?> </th>
                        <th><?php esc_html_e( 'Date Of Registration', 'wpschoolpress' ); ?></th>
                        <th  align="center" class="nosort"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $student_table  =   $wpdb->prefix."wpsp_temp";
                            $class_id='';
                            if( isset($_POST['ClassID'] ) ) {
                                $class_id= sanitize_text_field($_POST['ClassID']);
                            }else if( !empty( $sel_class ) ) {
                                $class_id = 'all';
                            }
                            $classquery =   " where t_type='".esc_sql($class_id)."' ";
                            if($class_id=='NULL'){
                                $classquery =   "";
                            }elseif($class_id=='all'){
                                $classquery="";
                            }
                    $students   =   $wpdb->get_results("select * from $student_table $classquery Order By t_id DESC");
                    foreach($students as $stinfo)
                    {   ?>
                        <tr <?php if($stinfo->t_active == 0) {echo "style='background-color:#fcdddd'";}?>>
                            <td>
                            <input type="checkbox" class="ccheckbox strowselect" name="UID[]" value="<?php echo esc_attr($stinfo->t_id);?>">
                            </td>
                            <td><?php echo esc_html($stinfo->t_name);?></td>
                            <td><?php echo esc_html($stinfo->t_email);?></td>
                            <td><?php echo esc_html(ucfirst($stinfo->t_type));?></td>
                            <td><?php echo esc_html($stinfo->t_date);?></td>
                            <td align="center">
                                <div class="wpsp-action-col">
                                    <?php $nonce =  wp_create_nonce( 'WPSRegRequest'); ?>
                                    <a href="javascript:;" data-nonce="<?php echo $nonce;?>"  id="approved_is" data-pop="ViewModal" data-id="<?php echo esc_attr($stinfo->t_id);?>" title="Approve"><?php esc_html_e( 'Approve', 'wpschoolpress' ); ?> |</a>
                                    <a href="javascript:;" data-nonce="<?php echo $nonce;?>" id="d_teacher" class="wpsp-popclick" data-pop="DisapproveModal" title="Disapprove" data-id="<?php echo esc_attr($stinfo->t_id);?>" ><?php esc_html_e( 'Disapprove', 'wpschoolpress' ); ?></a>
                                </div>
                            </td>
                        </tr>
                    <?php }?>
                </tbody>
                <tfoot>
                  <tr>
                    <th></th>
                    <th><?php esc_html_e( 'Full Name', 'wpschoolpress' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'wpschoolpress' ); ?> </th>
                        <th><?php esc_html_e( 'User Role', 'wpschoolpress' ); ?> </th>
                        <th><?php esc_html_e( 'Date Of Registration', 'wpschoolpress' ); ?></th>
                    <th  align="center"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
                  </tr>
                </tfoot>
              </table>
              </div>
            </div><!-- /.box-body -->
        </div>

            <?php wpsp_body_end();
            wpsp_footer();  } ?>
    <div class="wpsp-popupMain" id="ViewModal">
      <div class="wpsp-overlayer"></div>
      <div class="wpsp-popBody">
        <div class="wpsp-popInner">
            <a href="javascript:;" class="wpsp-closePopup"></a>
            <div id="ViewModalContent"></div>
        </div>
      </div>
    </div>
<div class="wpsp-popupMain wpsp-popVisible" id="DisapproveModal" data-pop="DisapproveModal" style="display:none;">
  <div class="wpsp-overlayer"></div>
  <div class="wpsp-popBody wpsp-alert-body">
    <div class="wpsp-popInner">
        <a href="javascript:;" class="wpsp-closePopup"></a>
        <div class="wpsp-popup-cont wpsp-alertbox wpsp-alert-danger">
            <div class="wpsp-alert-icon-box">
                <i class="icon wpsp-icon-question-mark"></i>
            </div>
            <div class="wpsp-alert-data">
                <h4><?php esc_html_e( 'Confirmation Needed', 'wpschoolpress' ); ?></h4>
                <p><?php esc_html_e( 'Are you sure want to disapprove?', 'wpschoolpress' ); ?></p>
            </div>
            <div class="wpsp-alert-btn">
                <input type="hidden" name="teacherid" id="teacherid">
                <!-- <//?php wp_nonce_field( 'WPSDecativateReg', 'wps_deacreg_nonce', '', true ); ?> -->
                <a class="wpsp-btn wpsp-btn-danger ClassDeleteBt">Ok</a>
                <a href="javascript:;" class="wpsp-btn wpsp-dark-btn wpsp-popup-cancel"><?php esc_html_e( 'Cancel', 'wpschoolpress' ); ?></a>
            </div>
        </div>
    </div>
  </div>
</div>
    <?php
    }
    else {
        include_once( WPSP_PLUGIN_PATH.'/includes/wpsp-login.php');//Include login
    }
?>
