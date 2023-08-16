<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* This function used in wpsp_header() call this function */
function wpsp_customcss(){
  global $current_user, $wp_roles, $current_user_name;
  if (isset($current_user->roles[0])) {
    $current_user_role=$current_user->roles[0];
  }else {
      $current_user_role='';
  }
  if($current_user_role=='administrator'){
  echo "<style>
    .owncls { display:none !important;}
    .content-wrapper, .right-side, .main-footer{margin-left:0px;}
    #wpfooter{position: relative !important;}
    #adminmenumain{display:none !important;}
    #wpadminbar{display:none !important;}
    </style>";
  }else {
    echo "<style>
    .update-nag {display:none !important;}
    #wpadminbar{display:none !important;}
    #adminmenumain{display:none !important;}
    #wpcontent, #wpfooter{margin-left: 0;}
    #wpcontent{padding-left:0px;}
    #wpfooter{position: relative !important;}
    </style>";
  }
}
/* This function used Header print custom css. */
function wpsp_header(){
  echo "<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src='https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js'></script>
  <script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
  <![endif]-->";
  echo "</head>";
  wpsp_customcss();
}
/* This function used for Topbar Return School Quote,Sologan,Number of Messages,Notification,User Photo, User basic settings. */
function wpsp_topbar(){
  global $current_user, $wpdb, $wpsp_settings_data,$post,$current_user_name;
  $loc_avatar = get_user_meta( $current_user->ID,'simple_local_avatar',true);
  $role   = isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';
  $url = plugin_dir_url( __FILE__ ).'img/wpschoolpresslogo.jpg';
  $img_url  = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';
  $schoolname = isset( $wpsp_settings_data['sch_name'] ) && !empty( $wpsp_settings_data['sch_name'] ) ? $wpsp_settings_data['sch_name'] : __( 'WPSchoolPress','wpschoolpress' );
  $imglogo  = isset( $wpsp_settings_data['sch_logo'] ) ? sanitize_text_field($wpsp_settings_data['sch_logo']) : $url;
  $schoolyear = isset( $wpsp_settings_data['sch_wrkingyear'] ) ? sanitize_text_field($wpsp_settings_data['sch_wrkingyear']) : '';
  $postname = isset( $post->post_name ) ? sanitize_text_field($post->post_name) :'';
  $roles    = sanitize_price_array($current_user->roles);
  $query      = '';
  $current_user_name  = $current_user->user_login;
  if( in_array( 'teacher', $roles ) ) {
    $table  = $wpdb->prefix."wpsp_teacher";
    $query  = "SELECT CONCAT_WS(' ', first_name, middle_name, last_name ) AS full_name FROM $table WHERE wp_usr_id=$current_user->ID";
  } else if( in_array( 'student', $roles ) ) {
    $table  =   $wpdb->prefix."wpsp_student";
    $query  = "SELECT CONCAT_WS(' ', s_fname, s_mname, s_lname ) AS full_name FROM $table WHERE wp_usr_id=$current_user->ID";
  } else if( in_array( 'parent', $roles ) ) {
    $table  =   $wpdb->prefix."wpsp_student";
    $query  = "SELECT CONCAT_WS(' ', p_fname, p_mname, p_lname ) AS full_name FROM $table WHERE parent_wp_usr_id=$current_user->ID";
  }
  if( !empty( $query ) ) {
    $full_name = $wpdb->get_var( $query );
    $current_user_name  = !empty( $full_name ) ? $full_name : sanitize_text_field($current_user_name);
  }
  ?> <div class="wpsp-body <?php if($roles[0]=='administrator') {echo "mainadmin";}?><?php echo esc_html($postname);?>"> <?php /*<div class="wpsp-loader"><img class="wpsp-loader-img" id="img_preview1" src="<?php echo plugins_url( 'img/wpsp-loading.png', __FILE__  ); ?>"></div>*/?> <div class="wpsp-preLoading">
    <div class="wpsp-bookshelf_wrapper">
        <ul class="wpsp-books_list">
            <li class="wpsp-book_item wpsp-first"></li>
            <li class="wpsp-book_item wpsp-second"></li>
            <li class="wpsp-book_item wpsp-third"></li>
            <li class="wpsp-book_item wpsp-fourth"></li>
            <li class="wpsp-book_item wpsp-fifth"></li>
            <li class="wpsp-book_item wpsp-sixth"></li>
        </ul>
        <div class="wpsp-shelf"></div>
    </div>
</div>
<header class='wpsp-header'>
    <a href='<?php esc_url(site_url('wp-admin/admin.php?page=sch-dashboard')); ?>' class='wpsp-logo'>
        <span class='wpsp-logo-mini'>
            <img src="<?php echo esc_url($imglogo); ?>" class="img wpsp-school-logo" width="45px" height="40px">
        </span>
        <span class='wpsp-schoolname'><?php echo stripslashes(esc_html($schoolname));?></span>
    </a>
    <div class="wpsp-head">
        <div class="wpsp-menuIcon"><span></span></div>
        <h3 class="wpsp-customeMsg"><?php echo esc_html(" “Live as if you were to die tomorrow. Learn as if you were to live forever.” ","wpschoolpress");?></h3>
        <div class="wpsp-righthead">
            <div class="wpsp-head-action"></div>
            <div class="wpsp-userMain wpsp-dropdownmain ">
                <div class="wpsp-profile-pic wpsp-dropdown-toggle">
                    <img src='<?php echo esc_url($img_url); ?>' class='wpsp-userPic' alt='User Image' />
                    <span class="wpsp-username"><?php echo esc_html($current_user_name);?></span>
                </div>
                <div class="wpsp-dropdown">
                    <ul> <?php if($roles[0]=='administrator') {?> <li class='wpsp-back-wp'><a href='<?php echo esc_url( admin_url() ); ?>'><?php echo esc_html( 'Back to wp-admin', 'wpschoolpress' );?></a></li><?php }?> <?php if($roles[0]!='administrator') {?><?php echo "<li class='wpsp-back-wp-editprofile'><a href='".esc_url(site_url('wp-admin/admin.php?page=sch-editprofile'))."'>".__('Edit Profile','wpschoolpress')."</a></li>"; }

              echo "<li class='wpsp-back-wp-changepassword'><a href='".esc_url(site_url('wp-admin/admin.php?page=sch-changepassword'))."'>".__('Change Password','wpschoolpress')."</a></li>"; ?> <li><a href='<?php echo esc_url(wp_logout_url());?>'><?php echo esc_html( 'Sign Out', 'wpschoolpress' );?></a></li> <?php if ( !empty($schoolyear ) ) { ?> <button class="btn"><?php echo esc_html( 'Academic year', 'wpschoolpress' );?> <span class="badge"> <?php echo esc_html($schoolyear); ?></span></button> <?php } ?> </ul>
                </div>
            </div>
        </div>
    </div>
</header> <?php
}
/* This function used for Left-Sidebar */
function wpsp_sidebar(){
  global $current_user, $wp_roles, $current_user_name;
  $current_user_role=$current_user->roles[0];
  $page = $_GET['page'] ?  sanitize_text_field(ltrim(strstr($_GET['page'],'-'),'-')) : '';
  $dashboard_page=$message_page=$student_page=$teacher_page=$parent_page=$class_page=$attendance_page=$subject_page=$mark_page=$exam_page=$event_page=$timetable_page=$import_page=$notify_page=$sms_page=$transport_page=$settings_page=$settings_general_page=$settings_wrkhours_page=$settings_subfield_page=$leave_page=$teacher_attendance_page=$settings_chgpw_page      = $viewpayment= $addpayment= $history_page =$payment_page_main='';
  $proversion1       =    wpsp_check_pro_version('wpsp_addon_version');
  $prodisable1       =    !$proversion1['status'] ? 'notinstalled'    : 'installed';
  $prohistory        =    wpsp_check_pro_version('wpsp_mc_version');
  $prodisablehistory =    !$prohistory['status'] ? 'notinstalled'    : 'installed';
  $promessage        =    wpsp_check_pro_version('wpsp_message_version');
  $prodisablemessage =    !$promessage['status'] ? 'notinstalled'    : 'installed';
  $propayment        =    wpsp_check_pro_version('pay_WooCommerce');
  $propayment        =    !$propayment['status'] ? 'notinstalled'    : 'installed';

  $prosocial        =    wpsp_check_pro_version('wpsp_sm_version');
  $prosocial        =    !$prosocial['status'] ? 'notinstalled'    : 'installed';

  include(WPSP_PLUGIN_PATH . 'includes/filter_menu.php');

    if($current_user_role=='administrator' || $current_user_role=='teacher'){
    switch( $page ){
      case 'dashboard':
      $dashboard_page="active";
      break;
      case 'posts':
      $posts_page="active";
      break;
      case 'postsprofile':
      $posts_profile_page="active";
      break;
      case 'reported-posts':
      $posts_reported_page="active";
      break;
      //   case 'documents':
      //   $documents_page="active";
      // break;
      case 'messages':
      $message_page="active";
      break;
      case 'student':
      $student_page="active";
      break;
      case 'request':
      $request_page="active";
      break;
      case 'teacher':
      $teacher_page="active";
      break;
      case 'parent':
      $parent_page="active";
      break;
      case 'class':
      $class_page="active";
      break;
      case 'attendance':
      $attendance_page="active";
      break;
      case 'subject':
      $subject_page="active";
      break;
      case 'exams':
      $exam_page="active";
      break;
      case 'marks':
      $mark_page="active";
      break;
      case 'importhistory':
      $import_page="active";
      break;
      case 'notify':
      $notify_page="active";
      break;
      case 'payment':
      if( isset( $_GET['type'] ) && $_GET['type'] =='addpayment' )
      $addpayment="active";
      else
      $viewpayment="active";
      $payment_page_main = "class='treeview active'";
      break;
      case 'events':
      $event_page="active";
      break;
      case 'transport':
      $transport_page="active";
      break;
      case 'leavecalendar':
      $leave_page="active";
      break;
      case 'timetable' :
      $timetable_page='active';
      break;
      case 'settings':
      $settings_page="class='treeview active'";
      if(isset($_GET['sc']) && $_GET['sc']=='subField')
        $settings_subfield_page="active";
      else if(isset($_GET['sc']) && $_GET['sc']=='WrkHours')
        $settings_wrkhours_page="active";
      else
        $settings_general_page="active";
      break;
      case 'changepassword' :
      $settings_chgpw_page="active";
      break;
      case 'teacherattendance':
      $teacher_attendance_page="active";
      break;
      case 'history' :
        $history_page='active';
        break;
    }



 // Outputs: Full URL
 if($prodisablehistory == "installed"){
   if(isset($_SERVER['QUERY_STRING'])){
     $query = $_SERVER['QUERY_STRING'];
     $query1 = explode("=",$query);
     $query1[1];
     if(!isset($query1[2])){
       $query1[2] = '';
     }

     if($query1[1] == 'sch-class' || $query1[1] == 'sch-subject' ||
        $query1[2] == 'subField' || $query1[1] == 'sch-marks' || $query1[1] == 'sch-exams' || $query1[1] == 'sch-timetable' ){
        $class_page_our = 'active';
      }else {
        $class_page_our = '';
      }
      if($query1[1] == 'sch-attendance' || $query1[1] == 'sch-teacherattendance'){
        $attendance_page_main = 'active';
      } else {
        $attendance_page_main = '';
      }
      if($query1[1] == 'sch-settings' || $query1[2] == 'WrkHours' || $query1[1] == 'sch-leavecalendar' || $query1[1] == 'sch-importhistory'){
        $settings_page_main = 'active';
      }else {
        $settings_page_main = '';
      }
    }
  }
  $loc_avatar=get_user_meta($current_user->ID,'simple_local_avatar',true);
  if( $current_user->ID == 1 )
    $img_url  =  WPSP_PLUGIN_URL.'img/admin.png';
  else
    $img_url  = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';

    echo "<!-- Left side column. contains the logo and sidebar -->
          <div class='wpsp-overlay'></div>
          <aside class='wpsp-sidebar ifnotadmin'>
          <div class='sidebarScroll'>
          <ul class='wpsp-navigation'>
          <li class='".esc_attr($dashboard_page)."'>
          <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-dashboard'))."'>
          <i class='dashicons dashicons-dashboard icon'></i>
          <span>".$sch_dashboard."</span>
          </a>
          </li>";
          if($prosocial == 'installed'){
            if($current_user_role=='administrator' || $current_user_role=='teacher'){
             echo "<li class='has-submenu ".((isset($posts_page_main)? $posts_page_main : ''))."'>
  
              <a href='".site_url('wp-admin/admin.php?page=sch-posts')."'>
  
                <i class='dashicons dashicons-admin-post'></i>
  
                <span>".$sch_posts."</span>
  
              </a>
  
              <ul class='sub-menu'>
  
                <li class='".$posts_page."'><a href='".site_url('wp-admin/admin.php?page=sch-posts')."'>
  
                  <span>".$sch_posts."</span>
  
                </a></li>
  
                <li class='".$posts_profile_page."'><a href='".site_url('wp-admin/admin.php?page=sch-postsprofile')."'>
  
                  <span>".$sch_posts_profile."</span>
  
                </a></li>";
              if($current_user_role=='administrator'){
                echo "<li class='".$posts_reported_page."'><a href='".site_url('wp-admin/admin.php?page=sch-reported-posts')."'>
  
                  <span>".$sch_reported_posts."</span>
  
                </a></li>";
              }
              // echo "<li class='".$documents_page."'><a href='".site_url('wp-admin/admin.php?page=sch-documents')."'>
  
              //     <span>".$sch_documents."</span>
  
              //   </a></li>";
  
           } 
           echo"</ul>
  
            </li>";
          }

          if($prodisablemessage == 'installed'){
            echo "<li class=".esc_attr($message_page).">
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-messages'))."'>
            <i class='dashicons dashicons-email icon'></i>
            <span>".$sch_message."</span><span class='pull-right label label-primary pull-right'></span>
            </a>
            </li>";
          }

          echo "<li class='".esc_attr($teacher_page)."'>
                <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-teacher'))."'>
                <i class='dashicons dashicons-businessman icon'></i>
                <span>".$sch_teacher."</span>
                </a>
                </li>
                <li class='".esc_attr($student_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-student'))."'>
                    <i class='dashicons dashicons-id icon'></i>
                    <span>".$sch_student."</span>
                  </a>
                </li>";
          if($current_user_role == 'administrator'){
            if($proversion1['status']){
              echo "<li class='".esc_attr($request_page)."'>
              <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-request'))."'>
              <i class='dashicons dashicons-welcome-write-blog icon'></i>
              <span>".$sch_registration."</span>
              </a></li>";
            }
          }

          echo "<li class='".esc_attr($parent_page)."'>
          <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-parent'))."'>
          <i class='dashicons dashicons-groups icon'></i>
          <span>".$sch_parent."</span>
          </a>
          </li>
          <li class='has-submenu ".((isset($class_page_our)? esc_attr($class_page_our) : ''))."'>
          <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-class'))."'>
          <i class='dashicons dashicons-welcome-widgets-menus icon'></i>
          <span>".$sch_classes."</span>
          </a>
          <ul class='sub-menu'>
          <li class='".esc_attr($class_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-class'))."'>
              <span>".$sch_classes."</span>
            </a>
          </li>
          <li class='".esc_attr($subject_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-subject'))."'>
              <span>".$sch_subject."</span>
            </a>
          </li>";

          if($current_user_role=='administrator'){
            echo "<li class='".esc_attr($settings_subfield_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-settings&sc=subField'))."'>".$sch_subject_mark_field."</a>
            </li>";
          }

          echo "<li class='".esc_attr($mark_page)."'>
          <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-marks'))."'>
          <span>".$sch_marks."</span>
          </a>
                </li>
                <li class='".esc_attr($exam_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-exams'))."'>
                    <span>".$sch_exams."</span>
                  </a>
                </li>
                <li class='".esc_attr($timetable_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-timetable'))."'>
                    <span>".$sch_timetable."</span>
                  </a>
                </li>
              </ul>
          </li>
          <li class='has-submenu ".((isset($attendance_page_main)? esc_attr($attendance_page_main) : ''))."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-attendance'))."'>
              <i class='dashicons dashicons-clipboard icon'></i>
              <span>".$sch_attendance."</span>
            </a>
            <ul class='sub-menu'>
              <li class='".esc_attr($attendance_page)."'><a href='".esc_url(site_url('wp-admin/admin.php?page=sch-attendance'))."'>
                <span>".$sch_student_attendance."</span>
              </a></li>";
            if($current_user_role=='administrator' || $current_user_role=='teacher'){
              echo "<li class='".esc_attr($teacher_attendance_page)."'>
                <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-teacherattendance'))."'>
                 <span>".$sch_teacher_attendance."</span>
                </a>
               </li>";
            }
          echo"</ul>
          </li>
          <li class='".esc_attr($event_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-events'))."'>
              <i class='dashicons dashicons-calendar-alt icon'></i><span>".$sch_events."</span>
            </a>
                </li>
           ";
        if($current_user_role=='administrator' || $current_user_role=='teacher') {
            echo "
            <li class='".esc_attr($notify_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-notify'))."'>
              <i class='icon wpsp-notify'></i><span>".$sch_notfiy."</span>
            </a>
          </li>";
        }
         echo "<li class='".esc_attr($transport_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-transport'))."'>
              <i class='icon wpsp-school-bus'></i><span>".$sch_transport."</span>
            </a>
           </li>";
      echo "<li class='has-submenu ".((isset($settings_page_main)? esc_attr($settings_page_main) : '' ))."'>
          <a href='#'>
          <i class='dashicons dashicons-admin-generic'></i>
          <span>".$sch_gen_setting."</span>
          </a>
          <ul class='sub-menu'>";
           if($current_user_role=='administrator'){
          echo "<li class='".esc_attr($settings_general_page)."'>
              <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-settings'))."'>
              <span>".$sch_setting."</span></a>
              </li>";
          echo "<li class='".esc_attr($settings_wrkhours_page)."'>
              <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-settings&sc=WrkHours'))."'>
              <span>".$sch_workinghours."</span></a>
              </li>";
         }
      echo "<li class='".$leave_page."'>
          <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-leavecalendar'))."'>
          <span>".$sch_leavecalendar."</span></a></li>";
         if($current_user_role=='administrator' || $current_user_role=='teacher') {
          echo "<li class='" . esc_attr($import_page) . "'>
              <a href='" . esc_url(site_url('wp-admin/admin.php?page=sch-importhistory')) . "'>
                <span>". $sch_import_history . "</span>
              </a>
               </li>
             ";
          }
            echo "</ul>
                </li>";

      echo "</ul>
            </div>
          </aside>";
  }  elseif($current_user_role=='student'){
      switch( $page )
      {
        case 'dashboard':
        $dashboard_page="active";
        break;
        case 'payment':
        $payment_page_main="active";
        break;
        case 'Messages':
        $message_page="active";
        break;
        case 'student':
        $student_page="active";
        break;
        case 'teacher':
        $teacher_page="active";
        break;
        case 'parent':
        $parent_page="active";
        break;
        // case 'class':
        // $class_page="active";
        // break;
        case 'attendance':
        $attendance_page="active";
        break;
        case 'subject':
        $subject_page="active";
        break;
        case 'exams':
        $exam_page="active";
        break;
        case 'marks':
        $mark_page="active";
        break;
        case 'events':
        $event_page="active";
        break;
        case 'transport':
        $transport_page="active";
        break;
        case 'leavecalendar':
        $leave_page="active";
        break;
        // case 'timetable' :
        // $timetable_page='active';
        // break;
        case 'history' :
        $history_page='active';
        break;
      }
       // Outputs: Full URL

$query = $_SERVER['QUERY_STRING'];
$query1 = explode("=",$query);
 $query1[1];
$query = $_SERVER['QUERY_STRING'];
$query1 = explode("=",$query);
 $query1[1];
 if(!isset($query1[2])){
  $query1[2] = '';
}
if($query1[1] == 'sch-timetable&cid' || $query1[1] == 'sch-teacher&cid' || $query1[1] == 'sch-subject&cid' || $query1[1] == 'sch-exams&cid' || $query1[1] == 'sch-marks&cid' || $query1[1] == 'sch-attendance&cid' || $query1[1] == 'sch-leavecalendar&cid'|| $query1[1] == 'sch-student&cid')
{
$class_innercls1 = 'active';
}else{
  $class_innercls1 = '';

}


if($propayment == "installed"){
global $wpdb;
  $user_id = esc_sql($current_user->ID);
$stable=$wpdb->prefix."wpsp_student";
$wpsp_stud =$wpdb->get_results("SELECT s.class_id FROM wp_wpsp_student s
INNER JOIN wp_wpsp_class c  where s.wp_usr_id = '".$user_id."' and c.c_fee_type = 'paid'");
//$clsid = $wpsp_stud[0]->class_id;
$clsid = [];
if(is_numeric($wpsp_stud[0]->class_id) ){
  $clsid[] = $wpsp_stud[0]->class_id;
}else{
  $class_id_array = unserialize( $wpsp_stud[0]->class_id );
  $clsid[] = $class_id_array;
}
$courses = get_user_meta( $user_id, '_pay_woocommerce_enrolled_class_access_counter', true );
    if ( ! empty( $courses ) ) {
      $courses = maybe_unserialize( $courses );
    } else {
      $courses = array();
    }

foreach($courses as $key => $value) {
      //if($key == $clsid)
  $keyvakye[] =  $key;
    }
}


      $loc_avatar=get_user_meta($current_user->ID,'simple_local_avatar',true);
      if( $current_user->ID == 1 )
      $img_url  =   WPSP_PLUGIN_URL.'img/admin.png';
      else
      $img_url  = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';
     global $current_user, $wpdb;
     $ctable= $wpdb->prefix."wpsp_class";
     $stable= $wpdb->prefix."wpsp_student";
     // $wpsp_classes =$wpdb->get_results("SELECT cls.* FROM $ctable cls, $stable st where st.wp_usr_id = $current_user->ID AND st.class_id=cls.cid");
      $wpsp_classes =$wpdb->get_results("SELECT class_id FROM $stable where wp_usr_id = '$current_user->ID'");
       // print_r($wpsp_classes);
          echo "<!-- Left side column. contains the logo and sidebar -->
          <div class='wpsp-overlay'></div>
            <aside class='wpsp-sidebar ifnotadmin'>
          <div class='sidebarScroll'>
            <ul class='wpsp-navigation'>
                <li class='".esc_attr($dashboard_page)."'>
                 <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-dashboard'))."'>
                    <i class='dashicons dashicons-dashboard icon'></i>
                    <span>".$sch_dashboard."</span>
                  </a>
                </li>";
                if($prosocial == 'installed'){
                  echo "<li class='has-submenu ".((isset($posts_page_main)? $posts_page_main : ''))."'>
       
                   <a href='".site_url('wp-admin/admin.php?page=sch-posts')."'>
       
                     <i class='dashicons dashicons-admin-post'></i>
       
                     <span>".$sch_posts."</span>
       
                   </a>
       
                   <ul class='sub-menu'>
       
                     <li class='".$posts_page."'><a href='".site_url('wp-admin/admin.php?page=sch-posts')."'>
       
                       <span>".$sch_posts."</span>
       
                     </a></li>
       
                     <li class='".$posts_profile_page."'><a href='".site_url('wp-admin/admin.php?page=sch-postsprofile')."'>
       
                       <span>".$sch_posts_profile."</span>
       
                     </a></li></ul>
                     </li>";
               }
              if($prodisablemessage == 'installed'){
               echo "<li class=".esc_attr($message_page).">
             <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-messages'))."'>
               <i class='dashicons dashicons-email icon'></i>
        <span>".$sch_message."</span><span class='pull-right label label-primary pull-right'></span>
              </a>
            </li>";
          }
                echo "
            <li class='".esc_attr($parent_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-parent'))."'>
                    <i class='dashicons dashicons-groups icon'></i>

                    <span>". $sch_parent."</span>
                  </a>
                </li>
             <li class='has-submenu ".esc_attr($class_innercls1)."'>
                <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-class'))."'>
                  <i class='dashicons dashicons-welcome-widgets-menus icon'></i><span>".$sch_enrolled_class."</span>
                </a>";
             if (is_numeric($wpsp_classes[0]->class_id)){
                  $classIDArray[] = $wpsp_classes[0]->class_id;
              }else{
                  $classIDArray = unserialize($wpsp_classes[0]->class_id);
              }
              $classname_array = [];

              if ((!empty($classIDArray)) && ($classIDArray[0] != 0)) {
              foreach ($classIDArray as $id ) {
               $clasname = $wpdb->get_row("SELECT * FROM $ctable where cid=$id");

               $classname1 = $clasname->c_name;
               $classname_array[] = $clasname;

               if($propayment == "installed"){
               if($clasname->c_fee_type == 'free'){
                 $nonemenu = ' nopayment1';
               } else {
                 if(!empty($keyvakye)){
                   if(in_array($id, $keyvakye)){
                     $nonemenu = ' nopayment1';
                   } else {
                     $nonemenu = ' nopayment';
                   }
                 }else{
                   $nonemenu = ' nopayment';
                 }
               }
             }

               $getid = base64_decode($query1[2]);
               $id = base64_encode(intval($id));
               $clsgetid = base64_decode(sanitize_text_field($id));
               if($getid == base64_decode(stripslashes($id))){
                 $timetable_page = 'active';
               }

               if($clsgetid != 0)
               {

                echo "<ul class='sub-menu has-submenu ".((isset($nonemenu) ? $nonemenu : ''))." '>";
            if(trim($clsgetid) == trim($getid)){

              echo "<li class='has-submenu active'>";
            } else {
              echo "<li class='has-submenu'>";
            }

            echo "<a href='".esc_url(site_url('wp-admin/admin.php?page=sch-class'))."'>
                  <i class='dashicons dashicons-welcome-widgets-menus icon'></i><span>".esc_html($classname1)."</span>
                </a>
                <ul class='sub-menu'>
                <li class='".esc_attr($timetable_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-timetable&cid=').esc_attr($id))."'>
                    <span>".$sch_timetable."</span>
                  </a>
                </li>
                <li class='".esc_attr($teacher_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-teacher&cid=').esc_attr($id))."'>
                    <span>".$sch_teacher."</span>
                  </a>
                </li>
                <li class='".esc_attr($student_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-student&cid=').esc_attr($id))."'>
                    <span>".$sch_student."</span>
                  </a>
                </li>
                <li class='".esc_attr($subject_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-subject&cid=').esc_attr($id))."'>
                    <span>".$sch_subject."</span>
                  </a>
                </li>
                <li class='".esc_attr($exam_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-exams&cid=').esc_attr($id))."'>
                    <span>".$sch_exams."</span>
                  </a>
                </li>
                <li class='".esc_attr($mark_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-marks&cid=').esc_attr($id))."'>
                    <span>".$sch_marks."</span>
                  </a>
                </li>
                <li class='".esc_attr($attendance_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-attendance&cid=').esc_attr($id))."'>
                    <span>".$sch_attendance."</span>
                  </a>
                </li>
                <li class='".esc_attr($leave_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-leavecalendar&cid=').esc_attr($id))."'>
                 <span>".$sch_leavecalendar."</span></a>
                </li>
              </ul>
          </li></li></ul>";
          }
        }
          }
         echo "<li class='".esc_attr($event_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-events'))."'>
                    <i class='dashicons dashicons-calendar-alt icon'></i><span>".$sch_events."</span>
                  </a>
                </li>

            <li class='".esc_attr($transport_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-transport'))."'>
              <i class='icon wpsp-school-bus'></i><span>".$sch_transport."</span>
            </a>
           </li>";
        if($prohistory['status']){
           echo "<li class='".esc_attr($history_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-history'))."'>
              <i class='icon  fa fa-history'></i><span>".$sch_class_history."</span>
            </a>
           </li>";
         }
          if($propayment == "installed"){
              if($current_user_role=='student'){

      echo "<li class='".esc_attr($payment_page_main)."'>
        <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-payment'))."'>
          <i class='icon fa fa-money'></i><span>".$sch_payment."</span>
        </a>
       </li>";
       }}
         echo "</ul>
            </div>
          </aside>";
  }elseif($current_user_role=='parent'){
      switch( $page )
      {
        case 'dashboard':
        $dashboard_page="active";
        break;
         case 'Messages':
      $message_page="active";
       break;
        case 'student':
        $student_page="active";
        break;
        case 'teacher':
        $teacher_page="active";
        break;
        case 'parent':
        $parent_page="active";
        break;
        // case 'class':
        // $class_page="active";
        // break;
        case 'attendance':
        $attendance_page="active";
        break;
        case 'subject':
        $subject_page="active";
        break;
        case 'exams':
        $exam_page="active";
        break;
        case 'marks':
        $mark_page="active";
        break;
        case 'events':
        $event_page="active";
        break;
        case 'transport':
        $transport_page="active";
        break;
        case 'leavecalendar':
        $leave_page="active";
        break;
        case 'timetable' :
        $timetable_page='active';
        break;
        case 'history' :
        $history_page='active';
        break;
      }
      $query = $_SERVER['QUERY_STRING'];
      $query1 = explode("=",$query);
      $query1[1];
      $query = $_SERVER['QUERY_STRING'];
      $query1 = explode("=",$query);
      $query1[1];
      if(!isset($query1[2])){
        $query1[2] = '';
      }
      if($query1[1] == 'sch-timetable&cid' || $query1[1] == 'sch-teacher&cid' || $query1[1] == 'sch-subject&cid' || $query1[1] == 'sch-exams&cid' || $query1[1] == 'sch-marks&cid' || $query1[1] == 'sch-attendance&cid' || $query1[1] == 'sch-leavecalendar&cid'|| $query1[1] == 'sch-student&cid')
      {
        $class_innercls1 = 'active';
      }else {
        $class_innercls1 = '';
      }

      if($propayment == "installed"){
        global $wpdb;
        $stable=$wpdb->prefix."wpsp_student";

        $user_id = $wpdb->get_var("SELECT wp_usr_id FROM $stable where parent_wp_usr_id = '$current_user->ID'");

        $wpsp_stud =$wpdb->get_results("SELECT s.class_id FROM wp_wpsp_student s INNER JOIN wp_wpsp_class c  where s.wp_usr_id = '".$user_id."' and c.c_fee_type = 'paid'");

        $clsid = [];
        if(is_numeric($wpsp_stud[0]->class_id) ){
          $clsid[] = $wpsp_stud[0]->class_id;
        }else{
          $class_id_array = unserialize( $wpsp_stud[0]->class_id );
          foreach ($class_id_array as $value) {
            $clsid[] = $value;
          }
        }
        $courses = get_user_meta( $user_id, '_pay_woocommerce_enrolled_class_access_counter', true );
        if ( ! empty( $courses ) ) {
          $courses = maybe_unserialize( $courses );
        } else {
          $courses = array();
        }
        foreach($courses as $key => $value) {
          $keyvakye[] =  $key;
        }
      }

      $loc_avatar=get_user_meta($current_user->ID,'simple_local_avatar',true);
      if( $current_user->ID == 1 )
        $img_url  =   WPSP_PLUGIN_URL.'img/admin.png';
      else
        $img_url  = $loc_avatar ? $loc_avatar['full'] : WPSP_PLUGIN_URL.'img/avatar.png';
      global $current_user, $wpdb;
      $ctable= $wpdb->prefix."wpsp_class";
      $stable= $wpdb->prefix."wpsp_student";
      $wpsp_classes =$wpdb->get_results("SELECT class_id,sid,s_fname,s_mname,s_lname FROM $stable where parent_wp_usr_id = '$current_user->ID'");
      echo "<!-- Left side column. contains the logo and sidebar -->
          <div class='wpsp-overlay'></div>
          <aside class='wpsp-sidebar ifnotadmin ".((isset($nonemenu)? esc_attr($nonemenu) : ''))."'>
          <div class='sidebarScroll'>
          <ul class='wpsp-navigation'>
            <li class='".esc_attr($dashboard_page)."'>
              <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-dashboard'))."'>
                <i class='dashicons dashicons-dashboard icon'></i>
                  <span>".$sch_dashboard."</span>
              </a>
            </li>";
            if($prosocial == 'installed'){
            
              echo "<li class='has-submenu ".((isset($posts_page_main)? $posts_page_main : ''))."'>
   
               <a href='".site_url('wp-admin/admin.php?page=sch-posts')."'>
   
                 <i class='dashicons dashicons-admin-post'></i>
   
                 <span>".$sch_posts."</span>
   
               </a>
   
               <ul class='sub-menu'>
   
                 <li class='".$posts_page."'><a href='".site_url('wp-admin/admin.php?page=sch-posts')."'>
   
                   <span>".$sch_posts."</span>
   
                 </a></li>
   
                 <li class='".$posts_profile_page."'><a href='".site_url('wp-admin/admin.php?page=sch-postsprofile')."'>
   
                   <span>".$sch_posts_profile."</span>
   
                 </a></li></ul>
                 </li>";
            }

            if($prodisablemessage == 'installed'){
              echo "<li class=".esc_attr($message_page).">
                <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-messages'))."'>
                <i class='dashicons dashicons-email icon'></i>
                <span>".$sch_message."</span>
                </a>
                </li>";
              }
              echo "<li class='".esc_attr($student_page)." has-submenu ".esc_attr($class_innercls1)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-student'))."'>
                    <i class='dashicons dashicons-id icon'></i>
                    <span>".$sch_student."</span>
                  </a>";

              foreach ($wpsp_classes as $sclas) {
                echo "<ul class='sub-menu ".esc_attr($class_innercls1)."'>
                 <li class='".esc_attr($student_page)." has-submenu ".esc_attr($class_innercls1)."'>
                  <a href=''>
                  <i class='dashicons dashicons-id icon'></i>
                  <span>".esc_html($sclas->s_fname.' '. $sclas->s_mname.' '. $sclas->s_lname)."</span>
                  </a>
                  <ul class='sub-menu ".esc_attr($class_innercls1)."'><li class='has-submenu ".esc_attr($class_innercls1)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-class'))."'>
                    <i class='dashicons dashicons-welcome-widgets-menus icon'></i><span>".$sch_enrolled_class."</span>
                  </a>";

              $sid = base64_encode(intval($sclas->sid));

             if (is_numeric($sclas->class_id)){
               $classIDArray[] = $sclas->class_id;
              }else{
                $classArray = unserialize($sclas->class_id);
                foreach ($classArray as $value) {
                  $classIDArray[] = $value;
                }
              }

              $classname_array = [];

              foreach ($classIDArray as $id ) {
                $id =esc_sql(intval($id));
               $clasname = $wpdb->get_row("SELECT * FROM $ctable where cid='$id'");
              $classname_array[] = $clasname;
              if($clasname->c_fee_type == 'free'){
                $nonemenu = ' nopayment1';
              } else {
               if($propayment == "installed"){
                if(in_array($id, $keyvakye)){
                  $nonemenu = ' nopayment1';
               }
               else {
                  $nonemenu = ' nopayment';
               }
             }
            }
              $id = base64_encode(intval($id));
              $getid = base64_decode($query1[2]);
              $clsgetid = base64_decode(sanitize_text_field(stripslashes($id)));
          if($getid == base64_decode($id)){
            $timetable_page = 'active';
          }
            echo "<ul class='sub-menu has-submenu ".((isset($nonemenu)? esc_attr($nonemenu) : ''))." '>";
          if(trim($clsgetid) == trim($getid)){

            echo "<li class='has-submenu active'>";
          }
            else {
              echo "<li class='has-submenu'>";
            }


               echo "<a href='".esc_url(site_url('wp-admin/admin.php?page=sch-class'))."'>
                  <i class='dashicons dashicons-welcome-widgets-menus icon'></i><span>".esc_html($clasname->c_name)."</span>
                </a>
                <ul class='sub-menu ".((isset($nonemenu)? esc_attr($nonemenu) : ''))."'>
                <li class='".esc_attr($timetable_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-timetable&cid='.esc_attr($id).'&sid=').esc_attr($sid))."'>
                    <span>".$sch_timetable."</span>
                  </a>
                </li>
                <li class='".esc_attr($teacher_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-teacher&cid='.esc_attr($id).'&sid=').esc_attr($sid))."'>
                    <span>".$sch_teacher."</span>
                  </a>
                </li>
                <li class='".esc_attr($subject_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-subject&cid='.esc_attr($id).'&sid=').esc_attr($sid))."'>
                    <span>".$sch_subject."</span>
                  </a>
                </li>
                <li class='".esc_attr($exam_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-exams&cid='.esc_attr($id).'&sid=').esc_attr($sid))."'>
                    <span>".$sch_exams."</span>
                  </a>
                </li>
                <li class='".esc_attr($mark_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-marks&cid='.esc_attr($id).'&sid=').esc_attr($sid))."'>
                    <span>".$sch_marks."</span>
                  </a>
                </li>
                <li class='".esc_attr($attendance_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-attendance&cid='.esc_attr($id).'&sid=').esc_attr($sid))."'>
                    <span>".$sch_attendance."</span>
                  </a>
                </li>
                <li class='".esc_attr($leave_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-leavecalendar&cid='.esc_attr($id).'&sid=').esc_attr($sid))."'>
                 <span>".$sch_leavecalendar."</span></a>
                </li>
              </ul>
          </li></ul>";
          }
          echo "</li></ul></li></ul>";
           $classIDArray = array();
        }

         echo "</li><li class='".esc_attr($event_page)."'>
                  <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-events'))."'>
                    <i class='dashicons dashicons-calendar-alt icon'></i><span>".$sch_events."</span>
                  </a>
                </li>

            <li class='".esc_attr($transport_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-transport'))."'>
              <i class='icon wpsp-school-bus'></i><span>".$sch_transport."</span>
            </a>
           </li>";
         if($prohistory['status']){
           echo"<li class='".esc_attr($history_page)."'>
            <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-history'))."'>
              <i class='icon fa fa-history'></i><span>".$sch_class_history."</span>
            </a>
           </li>";
         }
         if($propayment == "installed"){


      echo "<li class='".esc_attr($payment_page_main)."'>
        <a href='".esc_url(site_url('wp-admin/admin.php?page=sch-payment'))."'>
          <i class='icon fa fa-money'></i><span>".$sch_payment."</span>
        </a>
       </li>";

       }
         echo "</ul>
            </div>
          </aside>";
  }
}
/* This function used for Header Breadcrumb */
function wpsp_body_start()
{
  $result = $_GET['page'] ?  sanitize_text_field(ltrim(strstr($_GET['page'],'-'),'-')) : ''; // This variable return URL Part. for ex: dashboard
  $base_url  = wpsp_admin_url(); // This Variable Print Base URL
  global $current_user;
  $current_user_role = $current_user->roles[0];
  include(WPSP_PLUGIN_PATH . 'includes/filter_menu.php');
  switch($result)
  {
    case 'dashboard':
      $pagetitle = 'Dashboard';
      $pagetitle = $sch_dashboard;
      break;
    case 'posts':
        $pagetitle = 'Posts';
        $pagetitle = $sch_posts;
        $addurl = $base_url.'sch-posts&tab=addposts';
        break;
        case 'postsprofile':
        $pagetitle = $sch_posts_profile;
        break;
        case 'reported-posts':
        // $pagetitle = 'Reported Posts';
        $pagetitle = $sch_reported_posts;
        break;
        case 'posts-notification':
        // $pagetitle = 'Reported Posts';
        $pagetitle = $sch_posts_notification;
      break;
    case 'messages':
      $pagetitle = 'Messages';
      $pagetitle = $sch_dashboard;
      $addurl = $base_url.'sch-message&tab=addmessage';
      break;
    case 'teacher':
      $pagetitle = 'Teacher';
      $pagetitle = $sch_teacher;
      $addurl = $base_url.'sch-teacher&tab=addteacher';
      break;
    case 'student':
      $pagetitle = 'Student';
      $pagetitle = $sch_student;
      $addurl = $base_url.'sch-student&tab=addstudent';
      break;
    case 'request':
      $pagetitle = 'Registration Request';
      $pagetitle = $sch_registration;
      break;
    case 'parent':
      $pagetitle = apply_filters( 'wpsp_parent_title_menu', esc_html__( 'Parents', 'wpschoolpress' ));
      $pagetitle = $sch_parent;
      $addurl = $base_url.'sch-student&tab=addstudent';
      break;
  case 'payment':
      $pagetitle = 'payment';
      $pagetitle = $sch_payment;
    //  $addurl = $base_url.'sch-class&tab=addclass';
      break;
    case 'class':
      $pagetitle = 'Class';
      $pagetitle = $sch_classes;
      $addurl = $base_url.'sch-class&tab=addclass';
      break;
    case 'attendance':
      $pagetitle = 'Attendance';
      $pagetitle = $sch_attendance;
      $addurl = $base_url.'sch-attendance';
      break;
    case 'teacherattendance':
      $pagetitle = 'Teacher Attendance';
      $pagetitle = $sch_teacher_attendance;
      $addurl = $base_url.'sch-teacherattendance';
      break;
    case 'subject':
      $pagetitle = 'Subject';
      $pagetitle = $sch_subject;
      $addurl = $base_url.'sch-subject&tab=addsubject&classid=1';
      break;
    case 'exams':
      $pagetitle = 'Exam';
      $pagetitle = $sch_exams;
      $addurl = $base_url.'sch-exams&tab=addexam';
      break;
    case 'marks':
      $pagetitle = 'Marks';
      $pagetitle = $sch_marks;
      $addurl = $base_url.'sch-marks';
      break;
    case 'importhistory':
      $pagetitle = 'Import History';
      $pagetitle = $sch_import_history;
      break;
    case 'notify':
      $pagetitle = 'Notify';
      $pagetitle = $sch_notfiy;
      $addurl = $base_url.'sch-notify&ac=add';
      break;
      case 'payment':
      if(isset($_GET['type']) && $_GET['type'] =='addpayment'):
          $pagetitle = 'Payment';
          $pagetitle = $sch_payment;
      endif;
      break;
    case 'events':
      $pagetitle = 'Events';
      $pagetitle = $sch_events;
      break;
    case 'transport':
      $pagetitle = 'Transport';
      $pagetitle = $sch_transport;
      $breadcum = $base_url.'sch-transport';
      //$addurl = $base_url.'sch-transport';
      //$addurl ='';
      break;
    case 'leavecalendar':
      $pagetitle = 'Leave Calendar';
      $pagetitle = $sch_leavecalendar;
      $addurl = $base_url.'sch-transport';
      break;
    case 'timetable' :
      $pagetitle = 'Timetable';
      $pagetitle = $sch_timetable;
      $breadcum = $base_url.'sch-timetable';
      $addurl = $base_url.'sch-timetable&ac=add';
      break;
    case 'subField':
      $pagetitle = 'SubField';
      $pagetitle = $sch_subject_mark_field;
      $addurl = $base_url.'sch-transport';
      break;
    case 'WrkHours':
      $pagetitle = 'WorkHours';
      $pagetitle = $sch_workinghours;
    //  $addurl = $base_url.'sch-settings&sc=WrkHours';
      break;
    case 'settings':
      $pagetitle = 'Settings';
      $pagetitle = $sch_setting;
     // $addurl = $base_url.'sch-settings';
      break;
    case 'changepassword' :
      $pagetitle = 'Change Password';
      break;
       case 'editprofile' :
    $pagetitle="Edit Profile";
    break;
     case 'history' :
    $pagetitle="History";
    break;
  }
  // echo $addurl;
   //echo $result;
  if((isset($_GET['sc'])) && ($_GET['sc'] == 'subField'))
  {
   $pagetitle=esc_html("Subject Mark Fields","wpschoolpress");
   $pagetitle = esc_html($sch_subject_mark_field);
              }
   else if((isset($_GET['sc'])) && ($_GET['sc'] == 'WrkHours'))
   {
   $pagetitle=esc_html("Working Hours", "wpschoolpress");
   $pagetitle = esc_html($sch_workinghours);
              } else {
               esc_html($pagetitle);
            }
// Get class name
if(isset($_GET['cid'])){
  $ciid = sanitize_text_field(stripslashes($_GET['cid']));
  $cid = esc_sql(base64_decode($ciid));
  global $wpdb;
  $wpsp_class_table = $wpdb->prefix . "wpsp_class";
  $classdata = $wpdb->get_row("SELECT c_name from $wpsp_class_table where cid='$cid'");
  $c_name = sanitize_text_field($classdata->c_name);
}
  echo "<!-- Content Wrapper. Contains page content -->
      <div class='wpsp-wrapper'>
        <!-- Main content -->
        <section class='wpsp-container'>
      <div class='wpsp-pageHead'>
        <h1 class='wpsp-pagetitle'>".esc_html($pagetitle)."".((isset($_GET['cid']))? " - ".esc_html($c_name) : '')."</h1>
        <div class='wpsp-right'>
          <ol class='wpsp-breadcrumb'> <div class='".esc_attr($result)."'></div>";
            if(!empty($result == 'dashboard')):
              echo "<li><a href='".esc_url($base_url."sch-dashboard")."'>Home</a></li>";
            endif;
            echo "<li><a href='".esc_url($base_url."sch-dashboard")."'>".esc_html($sch_dashboard)."</a></li>";
            if(!empty($result != 'dashboard')):
              if((isset($_GET['sc'])) && ($_GET['sc'] == 'subField')){
              echo "<li><span class='active'>".esc_html($sch_subject_mark_field)."</span></li>";
              }
              else if((isset($_GET['sc'])) && ($_GET['sc'] == 'WrkHours'))
              {
                echo "<li><span class='active'>".esc_html($sch_workinghours)."</span></li>";
              }
              else if($result == 'timetable'){
                echo "<li><a href='".esc_url($breadcum)."'><span class='active'>".esc_html($pagetitle)."</span></a></li>";
              }else {
                echo "<li><a href='#'><span class='active'>".esc_html($pagetitle)."</span></a></li>";
              }
            endif;
          echo '</ol>';
          //echo $result;
            if(!empty($addurl)):
              if(!isset($_GET['tab'])){
                $_GET['tab'] = '';
              }
              if(!isset($_GET['ac'])){
                $_GET['ac'] = '';
              }

              if(($current_user_role == 'teacher')):
                if($result == 'notify' || $result == 'settings' ){
                  echo "<a class=' wpsp-btn wpsp-popclick' href='".esc_url($addurl)."'><i class='fa fa-plus-circle'></i> Create New</a>";
                }
              endif;
              if(($current_user_role=='administrator')):
                if($result == 'teacherattendance' || $result == 'attendance' || $result == 'marks' || $_GET['tab'] == 'addteacher' || $_GET['tab'] == 'addstudent'  || $_GET['tab'] == 'addclass' || $_GET['tab'] == 'addsubject' || $result == 'leavecalendar' || $_GET['tab'] == 'addexam' || $_GET['ac'] == 'add' ||  $result == 'settings' || $result == 'messages'){} else{
                    echo "<a class='wpsp-btn ".esc_attr($current_user_role)."' href='".esc_url($addurl)."'><i class='fa fa-plus-circle'></i> Create New</a>";
                }
              endif;
              //Social-Posts Add-on
              if(($current_user_role == 'student' || $current_user_role == 'parent' || $current_user_role == 'teacher')):
                if($result == 'posts' ){
                  echo "<a class=' wpsp-btn wpsp-popclick' href='".esc_url($addurl)."'><i class='fa fa-plus-circle'></i> Create New</a>";
                }
              endif;
            endif;
            if(empty($addurl) && ($current_user_role == 'administrator') && ($result == 'notify' ||  $result == 'settings' || $result =='transport' )):
              if($result == 'settings'){
                if((isset($_GET['sc'])) && ($_GET['sc'] == 'WrkHours')){} elseif((isset($_GET['sc'])) && ($_GET['sc'] == 'subField')) {
                echo "<a class='wpsp-popclick wpsp-btn' data-pop='addFieldModal' id='AddFieldsButton'><i class='fa fa-plus-circle'></i> Create New</a>";
                }
              } else{
                echo "<a class='wpsp-btn wpsp-popclick' data-pop='ViewModal' id='AddNew'><i class='fa fa-plus-circle'></i> Create New</a>";
              }
            endif;
        echo "</div>
      </div>";
}
/* This function used for footer copyright section */
function wpsp_body_end()
{
  echo "<footer class='wpsp-footer'>
        <p>Copyright &copy;".date('Y')." <a href='http://wpschoolpress.com' target='_blank'>WPSchoolPress</a>. All rights reserved. <span class='wpsp-right'>WPSchoolPress Version ".WPSP_PLUGIN_VERSION."</span></p></footer>
    <!-- Control Sidebar -->
    </section><!-- /.wpsp-container -->
  </div><!-- /.wpsp-wrapper -->
</div><!-- ./wrapper -->";
}
/* This function used for return footer script */
function wpsp_footer()
{
   echo "<script>
      jQuery(function($) {
        ajax_url ='".admin_url( 'admin-ajax.php' )."';
        date_format='mm/dd/yy';
        $('.content-wrapper').on('click',function(){
          $('.control-sidebar').removeClass('control-sidebar-open');
        });
        $('body').addClass('wpschoolpress');
    $('html').removeClass('wp-toolbar');
      });
    </script>";
      do_action( 'wpsp_footer_script' );
      require_once (WPSP_PLUGIN_PATH . 'includes/wpsp-popup.php');
      echo "</div>
        <div id='overlay'>
        </div>
    </body>
    </html>";
}
/* This function used for print Admin URL */
function wpsp_admin_url(){
 $admin_link = site_url('wp-admin/admin.php?page=');
    return $admin_link;
}
?>