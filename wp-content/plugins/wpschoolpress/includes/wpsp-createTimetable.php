<?php
if (!defined('ABSPATH'))
    exit;

$skip_hours = False;
if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['noh']) && $_POST['noh'] != '') {
    $tt_table      = $wpdb->prefix . "wpsp_timetable";
    $wh_table      = $wpdb->prefix . "wpsp_workinghours";
    $wpsp_class_id = intval($_POST['wpsp_class_name']);
    $noh           = intval($_POST['noh']);
    $sess_template = sanitize_text_field($_POST['sessions_template']);
    $check_tt      = $wpdb->get_row("Select heading from $tt_table where class_id='".esc_sql($wpsp_class_id)."' and heading!=''", ARRAY_N);
    if (!empty($check_tt)) {
        $_POST['sessions_template'] = 'available';
        $skip_hours = TRUE;
    } else {
        $sessions = $wpdb->get_results("SELECT * from $wh_table");
        if (empty($sessions)) {
            echo "<div class='wpsp-text-red'>No Working Hours added. Please add at <a href='".esc_url(admin_url('admin.php?page=sch-settings&sc=WrkHours'))."'>Settings</a></div>";
            return false;
        }
?>
 <div class="box box-info">
    <div class="box-header">
        <h3 class="box-title"><?php esc_html_e( 'Select time for Sessions', 'wpschoolpress' ); ?></h3>
    </div>
    <div class="box-body">
        <form name="" method="post" class="wpsp-form-horizontal">
            <input type="hidden" name="wpsp_class_name" value="<?php echo esc_attr($wpsp_class_id);?>">
            <input type="hidden" name="sessions_template" value="<?php  echo esc_attr($sess_template);?>"> <?php
        for ($i = 1; $i <= $noh; $i++) {
?> <div class="wpsp-row">
                <div class="wpsp-col-md-4">
                    <div class="wpsp-form-group">
                        <label class="wpsp-label"><?php echo esc_html( 'Session', 'wpschoolpress' ); ?><?php echo esc_html($i);?> </label>
                        <select name="session[]" class="wpsp-form-control">
                        <?php
                        foreach ($sessions as $ses) {?> <option value="<?php echo esc_attr($ses->id);?>"><?php  echo esc_html($ses->begintime);?>-<?php  echo esc_html($ses->endtime);?></option> <?php
                        }?>
                        </select>
                    </div>
                </div>
            </div> <?php } ?>
            <div class="wpsp-col-md-12">
                <div class="wpsp-form-group">
                    <input type="submit" name="last-step" value="submit" class="wpsp-btn wpsp-btn-primary">
                </div>
            </div>
        </form>
    </div>
</div> <?php
    }
}
if (($skip_hours === TRUE) || ('POST' == $_SERVER['REQUEST_METHOD'] && sanitize_text_field($_POST['sessions_template']) == 'available' && sanitize_text_field($_POST['template_class']) != '') || ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['last-step']) && sanitize_text_field($_POST['last-step']) == 'submit')) {
    $tt_table      = $wpdb->prefix . "wpsp_timetable";
    $subject_table = $wpdb->prefix . "wpsp_subject";
    $h_table       = $wpdb->prefix . "wpsp_workinghours";
    $class_id      = sanitize_text_field($_POST['wpsp_class_name']);
    $sess_template = sanitize_text_field($_POST['sessions_template']);
?> <div class="wpsp-card">
    <div class="wpsp-card-head">
        <h3 class="wpsp-card-title"><?php esc_html_e( 'Drag and Drop Subjects', 'wpschoolpress' ); ?> </h3>
    </div>
    <div class="wpsp-card-body"> <?php
    if ($sess_template == 'new') {
        $session = sanitize_price_array($_POST['session']);
    } else if ($sess_template == 'available' || $skip_hours === TRUE) {
        if ($skip_hours == TRUE) {
            $template_class_id = $class_id;
        } else {
            $template_class_id = sanitize_text_field($_POST['template_class']);
        }
        $check_tt = $wpdb->get_row("Select heading from $tt_table where class_id='".esc_sql($template_class_id)."' and heading!=''");
        if (!empty($check_tt)) {
            $get_sessions = unserialize($check_tt->heading);
            foreach ($get_sessions as $sesio) {
                $session[] = $sesio;
            }
        } else {
            $error = 1;
            echo "<div class='wpsp-text-red'>Can't fetch template from the selected class</div>";
        }
    }
    if (count($session) > 0) {
        $chck_hd = $wpdb->get_row("SELECT * from $tt_table where class_id='".esc_sql($class_id)."' and time_id='0' and day='0' and heading!=''");
        if (empty($chck_hd)) {
            $ins = $wpdb->insert($tt_table, array(
                'class_id' => $class_id,
                'heading' => serialize($session)
            ));
        } else {
            // echo "<span class='red'>*Sessions already available in order to edit session delete and regenerate timetable.</span>";
        }
    } else {
        $error = 1;
        echo "<div class='wpsp-text-red'>No Sessions Retrieved</div>";
    }
    $wpsp_hours_table    = $wpdb->prefix . "wpsp_workinghours";
    $wpsp_subjects_table = $wpdb->prefix . "wpsp_subject";
    $clt                 = $wpdb->get_results("SELECT * FROM $wpsp_subjects_table WHERE class_id='".esc_sql($class_id)."' or class_id=0 order by class_id desc");
    if (count($clt) == 0) {
        $error = 1;
        echo "<div class='wpsp-text-red'>No Subjects retrieved, Check you have subject for this class at <a href='" . esc_url(admin_url('admin.php?page=sch-subject')) . "'>Subjects</a></div>";
    }
    if ($error == 0) {
        $timetable = array();
        $tt_days   = $wpdb->get_results("select * from $tt_table where class_id='".esc_sql($class_id)."' and time_id !='0' ", ARRAY_A);
        foreach ($tt_days as $ttd) {
            $timetable[$ttd['day']][$ttd['time_id']] = $ttd['subject_id'];
        }
?> <div class="wpsp-row">
            <div class="wpsp-col-md-12">
                <div class="wpsp-form-group">
                    <label class="wpsp-labelMain"> <?php echo esc_html("Class", "wpschoolpress");?>: <?php
        echo wpsp_GetClassName(sanitize_text_field($_POST['wpsp_class_name']));
?> </label>
                </div>
            </div>
        </div>
        <!--  <div class="wpsp-table-responsive">
      <table align="center" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
        <tbody>
          <tr>      --> <?php
        foreach ($clt as $id) {
            /*echo '<td class="removesubject"><div class="item" id="' . $id->id . '" style="width:90px">' . $id->sub_name . '</div> </td>';*/
            echo '<div class="removesubject"><div class="item" id="' . esc_attr($id->id) . '" style="width:auto; color:#5cb85c; font-weight:500;">' . esc_html($id->sub_name) . '</div> </div>';
        }
?>
        <!--  </tr>
        </tbody>
      </table>
    </div>     -->
        <!--  <div class="wpsp-bg-yellow text-right" id="ajax_response_exist" style="background-color: #f39c12 !important;width: auto;float: right;text-align: center;">
    </div>   -->
        <div class="right wpsp-table-responsive" id="TimetableContainer">
            <table class="wpsp-table wpsp-table-bordered" cellspacing="0" width="100%" style="width:100%">
                <thead>
                    <tr>
                        <th> <?php echo esc_html("Week", "wpschoolpress");?>
                            <!--<select class="daytype">
                 <option value="0">Days
                </option>
                <option value="1"> -->
                            <!-- </option>
              </select> -->
                        </th> <?php //print_r($session);
             foreach ($session as $sess) {
?> <th> <?php
        $ses_info = $wpdb->get_row("Select * from $wpsp_hours_table where id='$sess'");
        echo esc_html($ses_info->begintime . " to " . $ses_info->endtime);
?> </th> <?php
    }
?>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $dayname = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
                    for ($j = 1; $j <= 7; $j++) {
                ?> <tr id="<?php echo esc_html($j);?>">
                        <td>
                            <!-- span class="dayval">Day <?php echo esc_html($j);?></span> -->
                            <span class="daynam"> <?php echo esc_html($dayname[$j - 1]);?></span>
                        </td> <?php
      foreach ($session as $key => $ses)  {
            $ses = esc_sql($ses);
            $hour_det = $wpdb->get_row("Select * from $wpsp_hours_table where id='$ses'");
            if ($hour_det->type == "1") {
                $td_class = "drop";
            } else {
                $td_class = "break";
                 /* $sub_name='<div class="item1 assigned wpsp-assigned-item <?php echo $key; ?> <?php echo $ses; ?> <?php echo $j;?>">Break
        </div>';  */

    } $sub_id = ''; $sub_name = '';
        if (isset($timetable[$j][$ses])) $sub_id = $timetable[$j][$ses];
        if ($sub_id > 0) {
            $sub_name_f = $wpdb->get_row("SELECT sub_name from $subject_table where id='".esc_sql($sub_id)."'");
            $sub_name = $sub_name_f->sub_name; }
            if ($sub_name != '') {
                $sub_name='<div class="item1 assigned wpsp-assigned-item">'. esc_html($sub_name).'</div>'; } else { $sub_name = $sub_name; }
                if($td_class == "break"){
                    $sub_name_f1 = $wpdb->get_row("SELECT id from wp_wpsp_subject where sub_name='Break'");
                    $time_table = $wpdb->prefix . "wpsp_timetable";
                    $time_table_data = array( 'class_id' => $class_id, 'time_id' => $ses, 'subject_id' => $sub_name_f1->id, 'session_id' => $key, 'day' => $j );
        $ins = $wpdb->insert($time_table, $time_table_data); } ?> <td class="<?php echo esc_attr($td_class); ?>" tid="<?php echo esc_attr($ses); ?>" data-sessionid="<?php echo esc_attr($key); ?>"><?php if($td_class == "break"){ echo esc_html("Break","wpschoolpress");} else {echo wp_kses_post($sub_name); }?></td> <?php
        }
?> </tr> <?php
    }
?> </tbody>
        </table>
        <div class="wpsp-form-group">
            <div class="wpsp-col-md-offset-10">
                <input type="hidden" name="class_id" id="class_id" value="<?php echo esc_attr($class_id);?>">
                <div id="ajax_response">
                </div>
            </div>
        </div>
        <div class="wpsp-col-md-12 wpsp-col-lg-12">
            <span class="pull-right">
                <a href="javascript:;" class="wpsp-btn wpsp-btn-danger" id="deleteTimetable" data-id="<?php echo esc_attr($class_id);?>"><?php echo esc_html("Delete", "wpschoolpress");?></a>
            </span>
        </div>
    </div> <?php
}
?>
</div>
</div> <?php }
if ('POST' != $_SERVER['REQUEST_METHOD']) {
    $tt_table    = $wpdb->prefix . "wpsp_timetable";
    $class_table = $wpdb->prefix . "wpsp_class";
    $classQuery  = "select cid,c_name from $class_table Order By cid ASC";
    $msg         = esc_html("Please Add Class Before Adding Subjects", "wpschoolpress");
    if ($current_usr_rle == 'teacher') {
        $cuserId    = esc_sql($current_user->ID);
        $classQuery = "select cid,c_name from $class_table where teacher_id='$cuserId'";
    }
    $classes     = $wpdb->get_results($classQuery);
    $class_names = array();
    foreach ($classes as $cnames) {
        $class_names[$cnames->cid] = $cnames->c_name;
    }
?> <div class="wpsp-card">
    <div class="wpsp-card-head">
        <h3 class="wpsp-card-title"><?php echo apply_filters( 'wpsp_timetable_heading_item',esc_html("Add New Timetable","wpschoolpress")); ?> </h3>
    </div>
    <div class="wpsp-card-body">
        <form class="wpsp-form-horizontal" id="timetable_form" action="" method="post">
        <?php wp_nonce_field( 'TimeTableRegister', 'wps_timetable_nonce', '', true ); ?>
            <div class="wpsp-row"> <?php
            $item =  apply_filters( 'wpsp_timetable_title_item',esc_html("Class Name","wpschoolpress"));
            $is_required_item = apply_filters('wpsp_timetable_is_required',array());
          ?> <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
            <div class="wpsp-form-group">
              <label class="wpsp-label"><?php esc_html_e("Select Class","wpschoolpress");
              /*Check Required Field*/
              if(isset($is_required_item['wpsp_class_name'])){
                  $is_required =  esc_html($is_required_item['wpsp_class_name'],"wpschoolpress");
              }else{
                  $is_required = true;
              }
              ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                        </label>
                        <select name="wpsp_class_name" data-is_required="<?php echo esc_attr($is_required); ?>" id="wpsp_class_name" class="wpsp-form-control">
                            <option value=""><?php echo esc_html("Select Class","wpschoolpress");?> </option> <?php
              foreach ($class_names as $cl_id => $cl_name) {
                ?> <option value="<?php echo esc_attr($cl_id);?>"><?php echo esc_html($cl_name);?></option> <?php
                  }
                ?>
                        </select>
                    </div>
                </div>
                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12">
                    <div class="wpsp-form-group">
                <label class="wpsp-label"><?php esc_html_e("Choose Session Template","wpschoolpress");
              /*Check Required Field*/
              if(isset($is_required_item['sessions_template'])){
                  $is_required =  esc_html($is_required_item['sessions_template'],"wpschoolpress");
              }else{
                  $is_required = true;
              }
              ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                        </label>
                        <select name="sessions_template" id="sessions_template" class="wpsp-form-control">
                            <option value="available"><?php echo esc_html("Available Templates","wpschoolpress");?> </option>
                            <option value="new" selected><?php echo esc_html("New Template","wpschoolpress");?> </option>
                        </select>
                    </div>
                </div>
                <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12" id="enter_sessions">
                    <div class="wpsp-form-group">
                <label class="wpsp-label"><?php esc_html_e("Enter No.of Sessions","wpschoolpress");
                /*Check Required Field*/
                if(isset($is_required_item['noh'])){
                    $is_required =  esc_html($is_required_item['noh'],"wpschoolpress");
                }else{
                    $is_required = true;
                }
              ?> <span class="wpsp-required"><?php echo esc_html(($is_required))?"*":""; ?></span>
                        </label>
                        <input type="text" data-is_required="<?php echo esc_attr($is_required); ?>" name="noh" class="wpsp-form-control">
                        <p style="margin-top:6px;">
                            <?php $url = plugins_url( 'img/svg/info-icon.svg', dirname(__FILE__) );?>
                            <img src="<?php echo esc_url($url);?>" width="12" height="12"> <?php echo esc_html("Include breaks & lunch E.g 8 + 3 = 11 Sessions","wpschoolpress");?>
                        </p>
                    </div>
                </div> <?php
    $avail_sess = $wpdb->get_results("SELECT t.class_id from $tt_table t, $class_table c where heading!='' and day=0 and c.cid=t.class_id");
?> <div class="wpsp-col-lg-3 wpsp-col-md-4 wpsp-col-sm-4 wpsp-col-xs-12" id="select_template" style="display:none">
                    <div class="wpsp-form-group">
                        <label class="wpsp-label"><?php esc_html_e("Select Class","wpschoolpress");?>
                        <?php echo esc_html("Select Sesssion","wpschoolpress");?> <span class="wpsp-required"> * </span>
                        </label>
                        <select name="template_class" id="template_class" class="wpsp-form-control"> <?php
    if (count($avail_sess) > 0) {
        foreach ($avail_sess as $avail_clid) {
            $class_id = $avail_clid->class_id;
            echo "<option value='" . esc_attr($class_id) . "'>" . esc_html($class_names[$class_id]) . "</option>";
        }
    } else {
        "<option value=''>No Template Available</option>";
    }
?> </select>
                    </div>
                </div>
                <div class="wpsp-col-xs-12">
                    <input type="submit" Value="Generate" name="stepone" class="wpsp-btn wpsp-btn-success">
                </div>
            </div>
        </form>
    </div>
</div> <?php
}
?>