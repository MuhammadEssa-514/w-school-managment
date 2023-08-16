<?php
if (!defined('ABSPATH'))
exit('No Such File');
$promessage    =    wpsp_check_pro_version('wpsp_message_version');
$prodisablemessage    =    !$promessage['status'] ? 'notinstalled'    : 'installed';
 if($prodisablemessage == 'notinstalled'){
  exit();
 }
wpsp_header();

$currentusetstatu = '';
if (is_user_logged_in()) {
  global $current_user, $wpdb;
  $current_user_role = $current_user->roles[0];

  $messages_table = $wpdb->prefix . "wpsp_messages";
  $messages_delete_table = $wpdb->prefix . "wpsp_messages_delete";


  if (isset($_GET['mid'])){
    $mid = sanitize_text_field($_GET['mid']);

  }

  $adminargs = array( 'role'    => 'administrator' );
  $adminusers = get_users( $adminargs );
  $admin_count = count($adminusers);

  $teacherargs = array( 'role'    => 'teacher' );
  $teacherusers = get_users( $teacherargs );
  $teacher_count = count($teacherusers);

  $parentargs = array( 'role'    => 'parent' );
  $parentusers = get_users( $parentargs );
  $parent_count = count($parentusers);

  $studentargs = array( 'role'    => 'student' );
  $studentusers = get_users( $studentargs );
  $student_count = count($studentusers);
  $delete_msg_count = '';
  if ($current_user_role == 'administrator' || $current_user_role == 'teacher' || $current_user_role == 'parent' || $current_user_role = 'student') {
    wpsp_topbar();
    wpsp_sidebar();
    wpsp_body_start();
    $messages_table = $wpdb->prefix . "wpsp_messages";
    $messages_delete_table = $wpdb->prefix . "wpsp_messages_delete";
    $currentTab     = isset($_GET['tab']) && !empty(sanitize_text_field($_GET['tab'])) ? sanitize_text_field($_GET['tab']) : 'inbox';
    if (isset($_GET['mid'])){
    $sender_id = $wpdb->get_results("select s_id from $messages_table where mid = '".esc_sql($mid)."'");
    $ss_id = $sender_id[0]->s_id;
    $delete_msg_count = $wpdb->get_var("select count(*) from $messages_delete_table where m_id = '".esc_sql($mid)."'  AND delete_status = 1");

    if($sender_id[0]->s_id == $current_user->ID){
      $currentusetstatu = 'sender';
      $unread_msg_count = $wpdb->get_results("select count(*) from $messages_table where main_m_id= '".esc_sql($mid)."' AND r_read = 0 AND r_id = '".esc_sql($current_user->ID)."'");
      if($unread_msg_count != 0){
        $m_data = array(
          's_read' => 1
        );

        $wpdb->update($messages_table, $m_data, array(
          'main_m_id' => $mid,
          'r_id' => $current_user->ID
        ));
      }
    }else{
      $currentusetstatu = 'reciver';
      $unread_msg_count = $wpdb->get_results("select count(*) from $messages_table where main_m_id= '".esc_sql($mid)."' AND r_read = 0 AND r_id = '".esc_sql($current_user->ID)."'");
      if($unread_msg_count != 0){
        $m_data = array(
          'r_read' => 1
        );

        $wpdb->update($messages_table, $m_data, array(
          'main_m_id' => $mid,
          'r_id' => $current_user->ID
        ));
      }
    }
  }
?>
    <section class="content">
      <div class="wpsp-row">
        <div class="wpsp-col-md-12">
          <div class="box box-solid bg-blue-gradient">
            <style>
            .wpsp-card-head{
              position: relative;
              z-index: 22;
            }
            .messages-sidebar ul li{
              padding: 10px;
              border:1px solid #777;
              margin-bottom: 0;
            }
            .messages-sidebar ul li a{
              color:#000;
              display: block;
            }
            p{
              font-size: 18px;
              margin-top: 0;
            }
            #viewMessageContainer ul ul{
              padding-left: 3.5%;
              margin-top: 25px;
              margin-bottom: 25px;
            }
            .m-date{
              font-size: 14px;
              font-weight: 400;
              color:#555;
              padding-left: 10px;
            }
            .wpsp-table thead + tbody tr.unread  {
              background: #e6e6e6 !important;
            }
            .wpsp-table thead + tbody tr.unread td,
            .wpsp-table thead + tbody tr.unread th{
              font-weight: 700;
            }
            .wpsp-table thead + tbody tr:nth-of-type(odd) {
              background: #fff;
            }
            .wpsp-receiver-links li{
              display: inline-block;

              border:1px solid #00bdda;

            }
            .wpsp-receiver-links li a{
              color: #000;
              display: block;
              padding:05px 15px;
            }
            .wpsp-receiver-links li.active {
              background-color: #00bdda;
            }
            .wpsp-receiver-links li.active a{
              color: #FFF;
            }
            #message-resposive.successmessage{
              text-align: center;
              padding-top:15px;
              padding-bottom:15px;
              color:green;
            }
            #message-resposive.errormessage{
              text-align: center;
              padding-top:15px;
              padding-bottom:15px;
              color:red;
            }

          </style>
          <div class="box-footer text-black">
            <div class="wpsp-row">
              <div class="wpsp-col-md-2 messages-sidebar">
                <div class="mailbox-header">
                  <div class="wpsp-card">
                    <div class="wpsp-card-head">
                      <button id="createMessage" class="wpsp-btn btn btn-primary btn-block" data-pop="ViewModal"  data-toggle="modal" data-target="#newMessage"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php esc_html_e( 'Compose', 'wpschoolpress' );?> </button>
                    </div>
                    <div class="wpsp-card-body">
                      <ul class="list-unstyled mailbox-nav">
                        <li class="active">
                          <a href="<?php echo esc_url(wpsp_admin_url().'sch-messages&tab=inbox');?>">
                            <i class="fa fa-inbox"></i> <?php esc_html_e( 'Inbox', 'wpschoolpress' );?><span class="badge badge-success pull-right btn-primary"><?php echo wpsp_UnreadCount(); ?></span>
                          </a>
                        </li>

                        <li><a href="<?php echo esc_url(wpsp_admin_url().'sch-messages&tab=sentbox');?>">
                          <i class="fa fa-sign-out"></i><?php esc_html_e( 'Sent', 'wpschoolpress' );?></a>
                        </li>
                        <li>
                          <a href="<?php echo esc_url(wpsp_admin_url().'sch-messages&tab=trash');?>">
                            <i class="fa fa-trash"></i><?php esc_html_e( 'Trash', 'wpschoolpress' );?>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <div class="wpsp-col-md-10 wpsp-mail-title">
                <div class="wpsp-card">
                  <div class="wpsp-card-head">
                    <h3 class="wpsp-card-title">
                      <?php
                      $columnFirstTitle = __('From', 'wpschoolpress');
                      $multi_datele_status = 0;
                      if (!isset($_REQUEST['tab']) || (sanitize_text_field($_REQUEST['tab']) == 'inbox')) {
                        echo esc_html(__('Inbox Items', 'wpschoolpress'));
                        $columnFirstTitle = __('From', 'wpschoolpress');
                      } else if (isset($_REQUEST['tab']) && sanitize_text_field($_REQUEST['tab']) == 'sentbox') {
                        echo esc_html(__('Sent Items', 'wpschoolpress'));
                        $columnFirstTitle = __('To', 'wpschoolpress');
                      } else if (isset($_REQUEST['tab']) && sanitize_text_field($_REQUEST['tab']) == 'trash') {
                        $multi_datele_status = 1;
                        echo esc_html(__('Trash Items', 'wpschoolpress'));
                        $columnFirstTitle = __('To', 'wpschoolpress');
                      }else{

                        echo esc_html(__('View Message', 'wpschoolpress'));
                      }
                      ?>
                    </h3>
                    <?php
                    if (!isset($_GET['mid']) && empty(sanitize_text_field($_GET['mid']))) {
                      ?>
                      <div class="wpsp-row">
                        <div class="wpsp-col-md-3">
                          <select name="bulkaction" class="wpsp-form-control" id="bulkaction" <?php echo (($multi_datele_status == 1)? 'data-trash="1"' : 'data-trash="0"') ?>>
                            <option value=""><?php esc_html_e( 'Select Action', 'wpschoolpress' );?></option>
                            <option value="bulkUsersDelete"><?php esc_html_e( 'Delete', 'wpschoolpress' );?></option>
                          </select>
                        </div>
                      </div>
                      <?php
                    }
                    ?>
                  </div>
                  <div class="wpsp-card-body <?php echo ((!isset($_GET['mid']) && empty(sanitize_text_field($_GET['mid'])))? 'message-card-body ': '') ?>">
                    <div class="wpsp-row">
                      <div class="wpsp-col-md-12 table-responsive">
                        <?php

                        if (isset($_GET['mid']) && !empty(sanitize_text_field($_GET['mid']))){
                          echo '<div id="viewMessageContainer">'.wpsp_ViewMessage(sanitize_text_field($_GET['mid']), true);
                          if (isset($_REQUEST['tab']) && sanitize_text_field($_REQUEST['tab']) == 'inbox'){

                            if($delete_msg_count == 0){
                            ?>

                            <hr style="margin-bottom:15px;"/>
                            <div class="wpsp-row">
                              <div class="wpsp-col-md-12">
                                <h3 style="margin-top:0;margin-bottom:5px;font-weight: 500;"><?php esc_html_e( 'Leave a Reply', 'wpschoolpress' );?></h3>
                                <form name="replyMessage" action="javascript:;" id="replyMessageForm" method="post">
                                  <div class="form-group bubble">
                                    <input type="hidden" name="mid" value="<?php echo esc_attr($_GET['mid']); ?>">
                                    <textarea id="message" name="message" style="width:100%;height:100%;background:transparent" class="wpsp-form-control" placeholder="Enter Message"></textarea>
                                  </div>
                                </br>
                                <span class="form-group">
                                  <input type="submit" class="wpsp-btn wpsp-btn-success" id="sendReply" style="margin:10px" value="SEND">
                                </span>
                              </form>
                            </div>
                          </div>
                          <?php
                        }else{
                          echo "<strong>This thread has been closed, you can start a new conversation.</strong>";
                          echo '<style>.wpsp-replay-message-btn{display: none;}</style>';
                        }
                       }
                        echo '</div>';
                      } else {
                        ?>
                        <table class="table table-bordered wpsp-table" id="message-list">
                          <thead>
                            <tr>
                              <th class="nosort"><input type="checkbox" id="checkAll" class="ccheckbox"></th>
                              <th><?php echo esc_html($columnFirstTitle); ?></th>
                              <th><?php esc_html_e( 'Subject', 'wpschoolpress' );?></th>
                              <th><?php esc_html_e( 'Date', 'wpschoolpress' );?></th>
                              <th class="nosort" style="max-width:100px;"><?php esc_html_e( 'Action', 'wpschoolpress' );?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if ($currentTab == 'trash') {
                              $mid = $wpdb->get_results("select distinct main_m_id from $messages_table where ( r_id='$current_user->ID' or s_id='".esc_sql($current_user->ID)."' ) and main_m_id IN (SELECT m_id FROM $messages_delete_table WHERE user_id = '$current_user->ID' AND delete_status = 0 )  order by mid DESC");

                            } else if ($currentTab == 'inbox') {

                              $mid = $wpdb->get_results("select distinct main_m_id from $messages_table where ( r_id='$current_user->ID' ) and main_m_id NOT IN (SELECT distinct m_id FROM $messages_delete_table WHERE user_id = '".esc_sql($current_user->ID)."' ) order by mid DESC");


                            } else {

                              $mid = $wpdb->get_results("select distinct main_m_id from $messages_table where ( s_id='$current_user->ID' ) and main_m_id NOT IN (SELECT distinct m_id FROM $messages_delete_table WHERE user_id = '".esc_sql($current_user->ID)."' ) order by mid DESC");
                            }
                            if(!empty($mid)){
                              foreach ($mid as $id) {

                                $sub_msg_count = 0;
                                $m = esc_sql($id->main_m_id);
                                $read_mcount_q = $wpdb->get_results("select mid from $messages_table where main_m_id= '$m' AND r_read = 0 AND r_id = '".esc_sql($current_user->ID)."'");

                                $sender_id = $wpdb->get_results("select s_id from $messages_table where mid = '$m'");

                                if (!isset($_REQUEST['tab']) OR sanitize_text_field($_REQUEST['tab']) == 'inbox'){
                                  if($sender_id[0]->s_id == $current_user->ID){
                                    $read_mcount = $wpdb->get_var("select count(*) from $messages_table where main_m_id= '$m' AND s_read = 0 AND r_id = '".esc_sql($current_user->ID)."'");
                                  }else{
                                    $read_mcount = $wpdb->get_var("select count(*) from $messages_table where main_m_id= '$m' AND r_read = 0 AND r_id = '".esc_sql($current_user->ID)."'");
                                  }
                                }else{
                                  $read_mcount = 0;
                                }


                                if($id->main_m_id != 0){
                                  if ($currentTab == 'trash') {
                                    $msgb = $wpdb->get_results("select * from $messages_table where main_m_id = '$m'");
                                  }else{
                                    $msgb = $wpdb->get_results("select * from $messages_table where (del_stat IS NULL OR del_stat != '$current_user->ID') and main_m_id = '$m'");
                                  }
                                }else{
                                  $msgb = $wpdb->get_results("select * from $messages_table where ( r_id = '$current_user->ID' ) and de(del_stat IS NULL OR del_stat != '$current_user->ID') and main_m_id = '$m'");
                                }

                                if(!empty($msgb)){
                                  $msg_id  = esc_sql($msgb[0]->main_m_id);
                                  if (isset($_REQUEST['tab']) && sanitize_text_field($_REQUEST['tab']) == 'inbox'){
                                    $sub_msg_count = $wpdb->get_var("select count(*) from $messages_table where main_m_id= '$msg_id' AND r_id = '$current_user->ID'");
                                  }elseif (isset($_REQUEST['tab']) && sanitize_text_field($_REQUEST['tab']) == 'sentbox'){
                                    $sub_msg_count = $wpdb->get_var("select count(*) from $messages_table where main_m_id= '$msg_id' AND s_id = '$current_user->ID'");
                                  }else if (isset($_REQUEST['tab']) && sanitize_text_field($_REQUEST['tab']) == 'trash'){
                                    $sub_msg_count = $wpdb->get_var("select count(*) from $messages_table where main_m_id= '$msg_id' AND del_stat = '$current_user->ID' AND (s_id = '$current_user->ID' OR r_id = '$current_user->ID')");
                                  }else{
                                    $sub_msg_count = $wpdb->get_var("select count(*) from $messages_table where main_m_id= '$msg_id' AND del_stat!='$current_user->ID'");
                                  }

                                  $rep         = json_decode($msgb[0]->msg);
                                  $senderlist  = json_decode($msgb[0]->msg, true);
                                  $rep         = (is_array($rep)) ? end($rep) : array();
                                  $s_mid       = $currentTab == 'inbox' ? $msgb[0]->s_id : $msgb[0]->r_id;
                                  $msgs_n      = get_userdata($s_mid);
                                  $role        = isset($msgs_n->roles[0]) ? sanitize_text_field($msgs_n->roles[0]) : '';
                                  $stat        = isset($rep->stat) ? sanitize_text_field($rep->stat) : '';
                                  $rep_sID     = isset($rep->s_id) ? sanitize_text_field($rep->s_id) : '';
                                  $mstat       = $stat != $current_user->ID && $rep_sID != $current_user->ID && $currentTab == 'inbox' ? "unread" : "read";
                                  $msg_text    = substr($msgb[0]->subject, 0, 50);
                                  $display     = true;

                                  $nickname    = isset($msgs_n->user_nicename) ? $msgs_n->user_nicename : $msgs_n->user_email;

                                  // print_r($msgs_n);
                                  if ($display) {
                                    ?>
                                    <tr class="<?php echo ((esc_attr($read_mcount) != 0)? "unread": "read");?>">
                                      <td><input type="checkbox" class="mid_checkbox ccheckbox" name="mid[]" value="<?php echo esc_attr(intval($msgb[0]->mid)); ?>" /></td>
                                      <td class="name"><?php echo esc_html($nickname); ?> (<span class="label label-role-msg label-role-<?php echo esc_attr($role); ?>"><?php echo esc_html($role);?></span>)</td>
                                      <td class="subject"><?php echo (($sub_msg_count != 0)? "<strong>(".esc_html($sub_msg_count).")</strong> " : ""); ?><?php echo esc_html($msg_text); ?></td>
                                      <td class="time"><?php echo wpsp_ViewDate(esc_html($msgb[0]->m_date)); ?></td>
                                      <td class="small-col">
                                        <?php
                                        if (sanitize_text_field($_REQUEST['tab']) != 'trash') {
                                        ?>
                                        <a class="pointer text-blue viewMess" title="view" href="<?php echo wpsp_admin_url(); ?>sch-messages&tab=<?php echo esc_attr($currentTab);?>&mid=<?php echo esc_attr(intval($msgb[0]->mid)); ?>">
                                          <i class="fa fa-eye btn btn-success"></i>
                                        </a>
                                      <?php } ?>
                                        <a href="javascript:;" title="Delete"  class="delete_messages" <?php
                                          if(isset($_REQUEST['tab'])) {
                                            if($_REQUEST['tab'] == 'trash') { echo 'data-trash="1"';}else{echo 'data-trash="0"';}
                                          }else{echo 'data-trash="0"';}
                                         ?> data-id="<?php echo esc_attr(intval($msgb[0]->main_m_id)); ?>"  mid="<?php echo esc_attr(intval($msgb[0]->main_m_id));?>">
                                          <i class="fa fa-trash btn btn-danger" style="color:#f00;"></i>
                                        </a>
                                      </td>
                                    </tr>
                                    <?php
                                  }
                                }
                              }
                            }
                            ?>
                          </tbody>
                        </table>
                        <?php
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>

<!--Model html  -->
<div class="wpsp-popupMain wpsp-message-modal" id="ViewModal">
  <div class="wpsp-overlayer"></div>
  <div class="wpsp-popBody">
    <div class="wpsp-popInner">
      <a href="javascript:;" class="wpsp-closePopup"></a>
      <div id="ViewModalContent" class="wpsp-text-left">
        <div class="wpsp-container" style="margin-top: 0px;">
          <div class="wpsp-row">
            <div class="wpsp-col-md-12 wpsp-text-center">
              <h1 style="text-align:center"><strong><?php esc_html_e( 'New Message', 'wpschoolpress' );?></strong></h1>
            </div>
          </div>
          <form class="form-horizontal group-border-dashed" action="" id="newMessageForm" style="border-radius: 0px;" method="post">
             <?php
             $class_table  = $wpdb->prefix . "wpsp_class";
             $studenttable = $wpdb->prefix . 'wpsp_student';
             $class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";
             $teacher_table = $wpdb->prefix . "wpsp_teacher";


             if ($current_user_role == 'administrator'){
               $student_list = $wpdb->get_results("SELECT wp_usr_id from $studenttable order by sid DESC");

               $students = array();
               foreach($student_list as $student){
    			    $students[] = $student->wp_usr_id;
    			}

             }elseif ( $current_user_role == 'teacher') {
               $students = [];
               $studentData = [];
               $studentData_2 = [];

              $class_list = $wpdb->get_results("SELECT DISTINCT cid  FROM $class_table WHERE teacher_id = '$current_user->ID'");
              foreach ($class_list as $cid) {

                $s_lists = $wpdb->get_results("select wp_usr_id,class_id from $studenttable");
                foreach ($s_lists as $studentdata) {
                    if(is_numeric($studentdata->class_id) ){
                    if($studentdata->class_id == $cid->cid){
                        $studentData[] = $studentdata->wp_usr_id;
                    }
      				}else{
                    $class_id_array = unserialize($studentdata->class_id);
                    if(!empty($class_id_array)){
                      if(in_array($cid->cid, $class_id_array)){
      						$studentData[] = $studentdata->wp_usr_id;
                      }
                    }
      						}
    				  	}
              }


              $students = array_unique($studentData);

             } elseif ($current_user_role == 'parent') {
               $parent_id = intval($current_user->ID);

               $student_list = $wpdb->get_results("SELECT wp_usr_id from $studenttable WHERE sid in ( select DISTINCT st.sid from $studenttable st WHERE st.parent_wp_usr_id = '".esc_sql($parent_id)."') ");

               $students = [];
               foreach($student_list as $student){
                $students[] = $student->wp_usr_id;
               }


            }else{
              $students = [];
            }

            if ($current_user_role == 'administrator'){
              $parents = [];
              $parent_list = $wpdb->get_results("SELECT DISTINCT parent_wp_usr_id from $studenttable where parent_wp_usr_id != 0 ");

              foreach($parent_list as $parent){
               $parents[] = $parent->parent_wp_usr_id;
              }
            }elseif ( $current_user_role == 'teacher') {
              $parents = [];
              $parents_data = [];

              $parent_list = $wpdb->get_results("select DISTINCT parent_wp_usr_id, class_id from $studenttable where parent_wp_usr_id != 0");
              $class_list = $wpdb->get_results("SELECT cid FROM $class_table where teacher_id = '$current_user->ID'");


              foreach ($class_list as $cid) {

                foreach ($parent_list as $parentdata) {
                  if(is_numeric($parentdata->class_id) ){
                    if($parentdata->class_id == $cid->cid){
                    $parents_data[] = $parentdata->parent_wp_usr_id;
                  }
                }else{
                   $class_id_array = unserialize($parentdata->class_id);
                   if(!empty($class_id_array)){
                     if(in_array($cid->cid, $class_id_array)){
                       $parents_data[] = $parentdata->parent_wp_usr_id;
                     }
                  }
                }
              }
              }


              $parents =  array_unique($parents_data);


            }elseif ($current_user_role == 'student') {
              $parents = [];
              $parent_list = $wpdb->get_results("SELECT CONCAT_WS(' ', p_fname, p_mname, p_lname) AS full_name, sid, parent_wp_usr_id from $studenttable WHERE parent_wp_usr_id != 0 AND sid in ( select DISTINCT st.sid from $studenttable st WHERE st.wp_usr_id = '$current_user->ID') ");

              foreach($parent_list as $parent){
               $parents[] = $parent->parent_wp_usr_id;
              }

            }else{
              $parents = [];
            }

            if ($current_user_role == 'administrator'){
              $teachers_data = $wpdb->get_results("select wp_usr_id from $teacher_table order by tid DESC");
              foreach($teachers_data as $teacher){
               $teachers[] = $teacher->wp_usr_id;
              }
            }elseif ( $current_user_role == 'teacher') {
              $teachers_data = $wpdb->get_results("select wp_usr_id from $teacher_table WHERE wp_usr_id !='$current_user->ID' order by tid DESC");
              foreach($teachers_data as $teacher){
               $teachers[] = $teacher->wp_usr_id;
              }
            }elseif ($current_user_role == 'parent') {
              $parent_id = intval($current_user->ID);

              $s_data = $wpdb->get_results("SELECT class_id FROM $studenttable where parent_wp_usr_id = '".esc_sql($parent_id)."'");
              $class_data = array();

              foreach ($s_data as $sid) {

                if(is_numeric($sid->class_id) ){
                    $class_data[] = $sid->class_id;
                }else{
                   $c_id = unserialize($sid->class_id);

                   foreach ($c_id as $id) {

                     $class_data[] = $id;
                   }
                }

              }
              $class_data_array = array_map('intval', $class_data);
              foreach ($class_data_array as $cid ) {
                $cid = esc_sql($cid);
                $teachers_data = $wpdb->get_results("SELECT DISTINCT wp_usr_id FROM $teacher_table WHERE wp_usr_id in ( SELECT teacher_id FROM $class_table WHERE cid = '$cid' ) order by tid DESC" );

                foreach($teachers_data as $teacher){
                  $teacher_data[] = $teacher->wp_usr_id;
                }
              }

              if(!empty($teacher_data)){
                $teachers = array_unique($teacher_data);
              }else{
                $teachers = [];
              }


            }elseif ($current_user_role == 'student') {
              $student_id = intval($current_user->ID);
              $s_data = $wpdb->get_results("SELECT class_id FROM $studenttable where wp_usr_id = '".esc_sql($student_id)."'");

              $class_data = [];
              foreach ($s_data as $sid) {
                if(is_numeric($sid->class_id) ){
                    $class_data[] = $sid->class_id;
                }else{
                   $class_data = unserialize($sid->class_id);
                }

              }
              $class_data_array = array_map('intval', $class_data);
              foreach ($class_data_array as $cid ) {
                  $cid = esc_sql($cid);
                $teachers_data = $wpdb->get_results("SELECT DISTINCT wp_usr_id FROM $teacher_table WHERE wp_usr_id in ( SELECT teacher_id FROM $class_table WHERE cid = '$cid' ) order by tid DESC" );
                foreach($teachers_data as $teacher){
                 $teachers[] = $teacher->wp_usr_id;
                }
              }
              if(!empty($teachers)){
                $teachers = array_unique($teachers);
              }else{
                $teachers = [];
              }
            }else{
              $teachers = [];
            }
            // NOTE:End Teacher Array
            ?>
            <div id="message_response"></div>
            <div class="wpsp-form-group">
              <div class="wpsp-row">
                <label class="wpsp-col-sm-3 control-label"><?php esc_html_e( 'Receiver', 'wpschoolpress' );?></label>
                <div class="wpsp-col-sm-9">
                  <ul class="wpsp-receiver-links">
                    <li class="active">
                      <a href="javascript:;" id="showGroup"><?php esc_html_e( 'Select Group', 'wpschoolpress' );?></a>
                    </li>
                    <?php if(!empty($teachers)) { ?>
                    <li>
                      <a href="javascript:;" id="showTeachers"><?php esc_html_e( 'Select Teacher', 'wpschoolpress' );?></a>
                    </li>
                  <?php }
                  if(!empty($students)){
                    if ($current_user_role != 'student'){
                      echo '<li><a href="javascript:;" id="showStudents">Select Students</a></li>';
                    }
                  }
                  if(!empty($parents)){
                    if ($current_user_role != 'parent') {
                      echo '<li><a href="javascript:;" id="showParents">Select Parents</a></li>';
                    }
                  }
                  ?>
                  </ul>
                </div>
              </div>
            </div>

            <div class="wpsp-form-group" id="receiverTeachers">
              <div class="wpsp-row">
                <label class="wpsp-col-sm-3 control-label"><?php esc_html_e( 'Select Teacher', 'wpschoolpress' );?></label>
                <div class="wpsp-col-sm-9" >

                  <select name="r_id[]" data-icon-base="fa" data-tick-icon="fa-check" data-live-search="true"  multiple="multiple" class="r_id wpsp-form-control teacher_multi_select1 selectpicker">
                    <?php
                      if (!empty($teachers)) {
                        echo '<optgroup label="All Teachers">';
                        foreach ($teachers as $id) {
                          $id = esc_sql($id);
                          $teachers_data = $wpdb->get_results("SELECT CONCAT_WS(' ', first_name, middle_name, last_name) AS full_name, wp_usr_id FROM $teacher_table WHERE wp_usr_id= '$id'");
                          ?>
                          <option value="<?php echo esc_attr(intval($teachers_data[0]->wp_usr_id)); ?>"><?php echo esc_html($teachers_data[0]->full_name); ?></option>
                          <?php
                        }
                        echo '</optgroup>';
                      }
                      ?>
                    </select>
                    </div>
                  </div>
                </div>
                <?php
                if ( $current_user_role != 'student') {
                 ?>
                <div class="wpsp-form-group" id="receiverStudents">
                  <div class="wpsp-row">
                    <label class="wpsp-col-sm-3 control-label"><?php esc_html_e( 'Select Students', 'wpschoolpress' );?></label>
                    <div class="wpsp-col-sm-9" >

                      <select name="r_id[]" data-icon-base="fa" data-tick-icon="fa-check" data-live-search="true"  multiple="multiple" class="r_id wpsp-form-control student_multi_select1 selectpicker">
                        <?php

                          if (!empty($students)) {
                            echo '<optgroup label="Select Students">';
                            foreach ($students as $sinfo) {
                              $sinfo = esc_sql($sinfo);
                              $student_list = $wpdb->get_results("SELECT CONCAT_WS(' ', s_fname, s_mname, s_lname) AS full_name from $studenttable where wp_usr_id = '$sinfo'  order by sid DESC");
                              ?>
                                <option value="<?php echo esc_attr(intval($sinfo)); ?>"><?php echo esc_html($student_list[0]->full_name); ?></option>
                              <?php

                            }
                            echo '</optgroup>';
                          }
                          ?>
                        </select>
                        </div>
                      </div>
                    </div>
                    <?php
                  }
                  if ($current_user_role != 'parent') {
                   ?>
                  <div class="wpsp-form-group" id="receiverParents">
                    <div class="wpsp-row">
                      <label class="wpsp-col-sm-3 control-label"><?php esc_html_e( 'Select Parents', 'wpschoolpress' );?></label>
                      <div class="wpsp-col-sm-9" >

                        <select name="r_id[]" data-icon-base="fa" data-tick-icon="fa-check" data-live-search="true"  multiple="multiple" class="r_id wpsp-form-control parent_multi_select1 selectpicker">
                          <?php
                            if (!empty($parents)) {
                              echo '<optgroup label="All Parents">';
                              $parent_name = '';
                              foreach ($parents as $key => $pinfo) {
                                $pinfo = esc_sql($pinfo);
                                $parent_list = $wpdb->get_results("SELECT CONCAT_WS(' ', p_fname, p_mname, p_lname) AS full_name, sid, parent_wp_usr_id from $studenttable WHERE parent_wp_usr_id = '$pinfo'");
                                if(intval(intval($parent_list[0]->parent_wp_usr_id)) != 0 ){
                                  if($parent_list[0]->full_name != ''){
                                    $parent_obj = get_user_by('id', intval($parent_list[0]->parent_wp_usr_id));
                                    $parent_name = $parent_obj->user_nicename;
                                  }else{
                                    $parent_name = $parent_list[0]->full_name;
                                  }
                                  ?>
                                  <option value="<?php echo esc_attr(intval($parent_list[0]->parent_wp_usr_id)); ?>"><?php echo esc_html($parent_name); ?></option>
                                  <?php
                                }
                              }
                              echo '</optgroup>';
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <?php
                    }
                  ?>

                <div class="wpsp-form-group" id="receiverGroups">
                  <div class="wpsp-row">
                    <label class="wpsp-col-sm-3 control-label"><?php esc_html_e( 'Select Group', 'wpschoolpress' );?></label>
                    <div class="wpsp-col-lg-9 wpsp-col-md-9 none" >
                      <select name="group" id="group" class="wpsp-form-control">
                        <option value="" selected disabled><?php esc_html_e( 'Select Group', 'wpschoolpress' );?></option>
                        <?php
                        if(($current_user_role == 'administrator') ){
                          if(($admin_count != 1)){
                            echo '<option value="admins">All Admins</option>';
                          }
                        }else{
                          echo '<option value="admins">All Admins</option>';
                        }

                        if (($current_user_role != 'student') && ($current_user_role != 'parent')) {
                            if(!empty($parents)){
                        ?>
                        <option value="parents"><?php esc_html_e( 'All Parents', 'wpschoolpress' );?></option>
                        <?php }

                        if(!empty($students)){ ?>
                          <option value="students"><?php esc_html_e( 'All Students', 'wpschoolpress' );?></option>
                      <?php }
                      if(!empty($teachers)){
                        if(($current_user_role == 'teacher') ){
                          if(($teacher_count != 1)){
                            echo '<option value="teachers">All Teachers</option>';
                          }
                        }else{
                          echo '<option value="teachers">All Teachers</option>';
                        }
                      } ?>
                        <optgroup label="Class Students">
                          <?php
                          $class_table = $wpdb->prefix . "wpsp_class";
                          $class_ids   = $wpdb->get_results("select cid,c_name from $class_table");
                          foreach ($class_ids as $clss) {
                            ?>
                            <option value="s.<?php echo esc_attr(intval($clss->cid)); ?>"> <?php esc_html_e( 'Class', 'wpschoolpress' );?> <?php echo esc_html($clss->c_name); ?> <?php esc_html_e( 'Students', 'wpschoolpress' );?> </option>
                            <?php
                          }
                          ?>
                        </optgroup>

                        <optgroup label="Class Teachers">
                          <?php
                          $class_table = $wpdb->prefix . "wpsp_class";
                          $class_ids   = $wpdb->get_results("select cid,c_name from $class_table");
                          foreach ($class_ids as $clss) {
                            ?>
                            <option value="t.<?php echo esc_attr(intval($clss->cid)); ?>"> <?php esc_html_e( 'Class', 'wpschoolpress' );?> <?php echo esc_html($clss->c_name); ?> <?php esc_html_e( 'Teachers', 'wpschoolpress' );?> </option>
                            <?php
                          }
                          ?>
                        </optgroup>


                        <optgroup label="Class Parents">
                          <?php
                          foreach ($class_ids as $clss) {
                            ?>
                            <option value="p.<?php echo esc_attr(intval($clss->cid)); ?>"> <?php esc_html_e( 'Class', 'wpschoolpress' );?> <?php echo esc_html($clss->c_name); ?> <?php esc_html_e( 'Parents', 'wpschoolpress' );?> </option>
                            <?php
                          }
                          ?>
                        </optgroup>
                        <?php
                      }
                        ?>
                      </select>

                    </div>
                  </div>
                </div>
                <div class="wpsp-form-group">
                  <div class="wpsp-row">
                    <label class="wpsp-col-sm-3 control-label"><?php esc_html_e( 'Subjects', 'wpschoolpress' );?></label>
                      <div class="wpsp-col-sm-9">
                        <input type="text" class="wpsp-form-control" name="subject" id="subject">
                      </div>
                    </div>
                </div>
                <div class="wpsp-form-group">
                  <div class="wpsp-row">
                    <label class="wpsp-col-sm-3 control-label"><?php esc_html_e( 'Message', 'wpschoolpress' );?></label>
                      <div class="wpsp-col-sm-9">
                        <textarea class="wpsp-form-control" name="message" id="message" rows="5" ></textarea>
                      </div>
                    </div>
                </div>
                <div class="wpsp-form-group">
                  <div class="wpsp-row">
                    <div class="wpsp-col-sm-offset-4 wpsp-col-sm-8">
                        <input type="submit" class="wpsp-btn wpsp-btn-primary" name="send" id="send" value="Send"/>
                    </div>
                  </div>
                  <div class="formresponse"></div>
                </div>
                <div id="message-resposive"></div>
              </form>

              </div>
            </div>
          </div>
        </div>
      </div>

      <?php
      wpsp_body_end();
      wpsp_footer();
    }
  } else {
    //Include Login Section
    include_once(WPSP_PLUGIN_PATH . '/includes/wpsp-login.php');
  }
?>
