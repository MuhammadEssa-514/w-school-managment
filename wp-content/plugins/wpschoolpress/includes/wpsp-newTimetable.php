<?php
if ( !defined( 'ABSPATH' ) ) { exit; }
$skip_hours=False;
if('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['noh']) && $_POST['noh'] != ''){
    $tt_table=$wpdb->prefix . "wpsp_timetable";
    $wh_table = $wpdb->prefix . "wpsp_workinghours";
    $wpsp_class_id = sanitize_text_field($_POST['wpsp_class_name']);
    $noh = sanitize_text_field($_POST['noh']);
    $sess_template = sanitize_text_field($_POST['sessions_template']);
    $check_tt = $wpdb->get_row("Select heading from $tt_table where class_id='".esc_sql($wpsp_class_id)."' and heading!=''");
    if (count($check_tt) > 0) {
        $_POST['sessions_template']='available';
        $skip_hours=TRUE;
    }
    else{
        $sessions = $wpdb->get_results("SELECT * from $wh_table");
        if(empty($sessions)){
            echo "<div class='wpsp-text-red'>No Working Hours added. Please add at <a href='".esc_url('sch-settings/?sc=WrkHours')."'>Settings</a></div>";
            return false;
        }?>
<div class="wpsp-card">
    <div class="wpsp-card-head">
        <h3 class="wpsp-card-title"><?php echo esc_html("Select time for Sessions","wpschoolpress");?> </h3>
    </div>
    <div class="wpsp-card-body">
        <form name="gen_table" method="post" class="wpsp-form-horizontal">
            <input type="hidden" name="wpsp_class_name" value="<?php echo esc_attr($wpsp_class_id); ?>">
            <input type="hidden" name="sessions_template" value="<?php echo esc_attr($sess_template); ?>">
            <?php for($i = 1; $i <= $noh; $i++) { ?>
            <div class="wpsp-form-group">
                <label class="wpsp-label"><?php echo esc_html("Session","wpschoolpress");?><?php echo esc_html($i); ?></label>
                <div class="wpsp-col-md-4">
                    <select name="session[]" class="wpsp-form-control">
                        <?php
                        foreach ($sessions as $ses) { ?>
                        <option value="<?php echo esc_attr($ses->id); ?>"><?php echo esc_html($ses->begintime); ?>-<?php echo esc_html($ses->endtime); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="wpsp-form-group">
                <div class="wpsp-col-md-offset-4">
                <input type="submit" name="last-step" value="submit" class="wpsp-btn wpsp-btn-primary">
            </div>
            </div>
        </form>
    </div>
</div>
<?php } }
if (($skip_hours===TRUE)||('POST' == $_SERVER['REQUEST_METHOD'] && sanitize_text_field($_POST['sessions_template']) == 'available' && sanitize_text_field($_POST['template_class']) != '') || ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['last-step']) && sanitize_text_field($_POST['last-step']) == 'submit'))
    {
    $tt_table = $wpdb->prefix . "wpsp_timetable";
    $subject_table = $wpdb->prefix . "wpsp_subject";
    $h_table = $wpdb->prefix . "wpsp_workinghours";
    $class_id = sanitize_text_field($_POST['wpsp_class_name']);
    $sess_template = sanitize_text_field($_POST['sessions_template']);  ?>
<div class="wpsp-card">
    <div class="wpsp-card-head">
        <h3 class="wpsp-card-title"><?php esc_html_e("Drag and Drop Subjects","wpschoolpress");?></h3>
    </div>
    <div class="wpsp-card-body">
        <?php
        if ($sess_template == 'new')
        {
        $session = sanitize_text_field($_POST['session']);
        }
        else if ($sess_template == 'available' || $skip_hours===TRUE)
        {
            if($skip_hours==TRUE){
            $template_class_id=$class_id;
            }
            else
            {
            $template_class_id = sanitize_text_field($_POST['template_class']);
            }
            $check_tt = $wpdb->get_row("Select heading from $tt_table where class_id='".esc_sql($template_class_id)."' and heading!=''");
                if (count($check_tt) > 0) {
                    $get_sessions = unserialize($check_tt->heading);
                    foreach ($get_sessions as $sesio)
                    {
                    $session[] = $sesio;
                }
                } else
                {
                    $error = 1;
                    echo "<div class='wpsp-text-red'>Can't fetch template from the selected class</div>";
                }
        }
        if (count($session) > 0) {
            $chck_hd = $wpdb->get_row("SELECT * from $tt_table where class_id='".esc_sql($class_id)."' and time_id='0' and day='0' and heading!=''");
            if(count($chck_hd) == null)
            {
                $ins = $wpdb->insert($tt_table, array('class_id' => $class_id,'heading' => serialize($session)));
            }
            else
            {
                echo "<span class='red'>*Sessions already available in order to edit session delete and regenerate timetable.</span>";
            }
        }
        else
        {
        $error = 1;
        echo "<div class='wpsp-text-red'>No Sessions Retrieved</div>";         }
        $wpsp_hours_table = $wpdb->prefix . "wpsp_workinghours";
        $wpsp_subjects_table = $wpdb->prefix . "wpsp_subject";
         $clt = $wpdb->get_results("SELECT * FROM $wpsp_subjects_table WHERE class_id='".esc_sql($class_id)."' or class_id=0 order by class_id desc");
            if(count($clt)==0) {
                $error = 1;
                echo "<div class='wpsp-text-red'>No Subjects retrieved, Check you have subject for this class at <a href='".esc_url(site_url())."/sch-subject'>Subjects</a></div>";
            }
            if ($error == 0) {
                   $timetable=array();
                   $tt_days=$wpdb->get_results("select * from $tt_table where class_id='".esc_sql($class_id)."' and time_id !='0' ",ARRAY_A);
                foreach($tt_days as $ttd){
                    $timetable[$ttd['day']][$ttd['time_id']]=$ttd['subject_id'];
                }  ?>
        <div class="wpsp-col-md-6 text-blue"><?php esc_html_e("Class","wpschoolpress");?> :<span class="text-black"> <?php echo wpsp_GetClassName(sanitize_text_field($_POST['wpsp_class_name'])); ?></span></div>
            <div style="width: 100%;">
                <!-- <table align="center" class="table">
                    <tbody>
                        <tr>    -->
                        <?php
                        foreach ($clt as $id) {
                            /*echo '<td class="removesubject"><div class="item" id="' . $id->id . '" style="width:80px">' . $id->sub_name . '</div>   </td>'; */
                            echo '<div class="removesubject"><div class="item" id="' . esc_attr($id->id) . '" style="width:auto; color:#5cb85c; font-weight:500;">' . esc_html($id->sub_name) . '</div>   </div>';
                        }?>
                        <!-- </tr>
                    </tbody>
                </table> -->
            </div>
        <div class="bg-yellow text-right" id="ajax_response_exist" style="background-color: #f39c12 !important;width: auto;float: right;text-align: center;"></div>
        <div class="right wpsp-table-responsive" id="TimetableContainer">
            <table class="wpsp-table table-bordered">
                <thead>
                    <tr>
                        <th>
                            <select class="daytype">
                                <option value="0"><?php echo esc_html("Days","wpschoolpress");?></option>
                                <option value="1"><?php echo esc_html("Week","wpschoolpress");?></option>
                            </select>
                        </th>
                        <?php
                        foreach ($session as $sess) { ?>
                            <th>
                                <?php $sess = esc_sql($sess);$ses_info = $wpdb->get_row("Select * from $wpsp_hours_table where id='$sess'");
                                echo esc_html($ses_info->begintime . " to " . $ses_info->endtime) ?>
                            </th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $dayname = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
                    for ($j = 1; $j <= 7; $j++) {?>
                    <tr id="<?php echo esc_attr($j); ?>">
                        <td><span class="dayval"><?php echo esc_html("Day","wpschoolpress");?><?php echo esc_html($j); ?></span>
                            <span class="daynam" style="display:none">
                            <?php echo esc_html($dayname[$j - 1]); ?></span>
                        </td>
                        <?php
                        foreach ($session as $ses) {
                        $ses = esc_sql($ses);
                        $hour_det = $wpdb->get_row("Select * from $wpsp_hours_table where id='$ses'");
                        if ($hour_det->type == "1") {
                            $td_class = "drop";
                        }else {
                            $td_class = "break";  }
                             $sub_id='';
                             $sub_name='';
                             if(isset($timetable[$j][$ses]))
                             $sub_id=$timetable[$j][$ses];
                             if($sub_id >0){
                                 $sub_name_f = $wpdb->get_row("SELECT sub_name from $subject_table where id='$sub_id'");
                                 $sub_name = $sub_name_f->sub_name;
                                  }
                                  if($sub_name!=''){
                                      $sub_name='<div class="item assigned">'. esc_html($sub_name).'</div>'; }else{       $sub_name='';                                   }                                   ?>
                            <td class="<?php echo esc_attr($td_class); ?>" tid="<?php echo esc_attr($ses); ?>">
                            <?php echo esc_html($sub_name); ?> </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="wpsp-form-group">
                <div class="wpsp-col-md-offset-10">
                    <input type="hidden" name="class_id" id="class_id" value="<?php echo esc_attr($class_id); ?>">
                    <div class="bg-green" id="ajax_response"></div>
                </div>
            </div>
            <div class="wpsp-col-md-12 wpsp-col-lg-12">
                <span class="pull-right"><a href="javascript:;" id="deleteTimetable" data-id="<?php echo esc_attr($class_id); ?>"><?php echo esc_html("Delete","wpschoolpress");?></a></span>
            </div>
        </div>
        <?php  } ?>
    </div>
</div>
<?php   }
if( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
$tt_table = $wpdb->prefix . "wpsp_timetable";
$class_table = $wpdb->prefix . "wpsp_class";?>
<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title"><?php esc_html_e("New Timetable","wpschoolpress");?></h3>
    </div>
    <div class="box-body">
        <form class="wpsp-form-horizontal" id="timetable_form" action="" method="post">
            <div class="wpsp-form-group">
                <label class="wpsp-col-sm-4 wpsp-label"><?php esc_html_e("Select Class","wpschoolpress");?><span class="wpsp-required">*</span> </label>
                <div class="wpsp-col-md-4">
                    <select name="wpsp_class_name"  id="wpsp_class_name" class="wpsp-form-control">
                        <option value=""><?php esc_html_e("Select Class","wpschoolpress");?></option>
                        <?php
                        foreach ($class_names as $cl_id=>$cl_name) {?>
                        <option value="<?php echo esc_attr($cl_id); ?>"><?php echo esc_html($cl_name); ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="wpsp-form-group">
                <label class="wpsp-label "><?php esc_html_e("Choose Session Template","wpschoolpress");?> <span class="wpsp-required">*</span></label>
                <div class="wpsp-col-md-4">
                    <select name="sessions_template" id="sessions_template"class="wpsp-form-control">
                        <option value="available"><?php esc_html_e("Available Templates","wpschoolpress");?></option>
                        <option value="new" selected><?php esc_html_e("New Template","wpschoolpress");?></option>
                    </select>
                </div>
            </div>
            <?php $avail_sess = $wpdb->get_results("SELECT t.class_id from $tt_table t, $class_table c where heading!='' and day=0 and c.cid=t.class_id"); ?>
            <div class="wpsp-form-group" id="select_template" style="display:none">
                <label class="wpsp-col-md-4 wpsp-label "><?php esc_html_e("Select Session","wpschoolpress");?><span class="wpsp-required">*</span></label>
                <div class="wpsp-col-md-3">
                    <select name="template_class" id="template_class" class="wpsp-form-control">
                    <?php
                    if (count($avail_sess) > 0) {
                        foreach ($avail_sess as $avail_clid) {
                            $class_id = $avail_clid->class_id;
                            echo "<option value='" . esc_attr($class_id) . "'>" . esc_html($class_names[$class_id]) . "</option>";
                            }
                    }
                    else {
                        echo "<option value=''>No Template Available</option>";
                    }?>
                    </select>
                </div>
            </div>
            <div class="wpsp-form-group" id="enter_sessions">
                <label class="wpsp-col-md-4 wpsp-label"><?php esc_html_e("Enter No.of Sessions","wpschoolpress");?>
                <span class="wpsp-required">*</span> </label>
                <div class="wpsp-col-md-3" >
                    <input type="text" name="noh" class="wpsp-form-control">
                </div>
                <div class="wpsp-col-md-3" >
                    <span class="text-muted"><?php esc_html_e("Include breaks and lunch <br> E.g 8 + 3 = 11 Sessions","wpschoolpress");?></span></div>
            </div>
            <div class="wpsp-col-md-4 wpsp-col-md-offset-4">
            <input type="submit" Value="Generate" name="stepone" class="wpsp-btn wpsp-btn-primary"> </div>
        </form>
    </div>
</div>
<?php }?>