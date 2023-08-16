<?php

if (!defined('ABSPATH')) exit('No Such File');

function get_student_date(){

    $catids = array_map( 'intval', $_POST['data'] );
    $student_id = sanitize_text_field($_POST['student_id']);
	$html = '';
	global $wpdb;
	$class_table = $wpdb->prefix . "wpsp_class";
	$class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";
	$student_table = $wpdb->prefix."wpsp_student";

	$stinfo  =  $wpdb->get_row("select * from $student_table where wp_usr_id='".esc_sql($student_id)."'");
	if( !empty( $stinfo ) ) {
	  $student_index = sanitize_text_field($stinfo->sid);
	}
	// print_r($catids[0]);
	// echo $student_id;
	foreach ($catids[0]['val'] as $catid) {
      $catid = intval($catid);
	  $class_data = $wpdb->get_results("select * from $class_mapping_table where sid='".esc_sql($student_index)."' AND cid= '".esc_sql($catid)."'");
	  $classes = $wpdb->get_results("select c_name from $class_table where cid = '".esc_sql($catid)."'");

		$html .="<tr>
			<td style='border:1px solid;padding:5px;'><strong>".esc_html($classes[0]->c_name)."</strong></td>
			<td style='border:1px solid;padding:5px;'><input type='text' class='someclass datepicker' name='Classdata[]' placeholder='yyyy-mm-dd' ".(($class_data[0]->date != '') ? 'value="'.esc_attr($class_data[0]->date).'"' : '')."></td>
		</tr>";

	}

	header( 'Content-Type: application/json' );
	header( 'Status: 200' );
	echo wp_json_encode( array( 'result' => true, 'data'=> wp_kses_post($html) ) );
	die();
}

add_action('wp_ajax_get_student_date', 'get_student_date');
/* This function is used for Add Student */
function wpsp_AddStudent(){
	wpsp_Authenticate();
	if (!isset($_POST['sregister_nonce']) ||  ! wp_verify_nonce( $_POST['sregister_nonce'], 'StudentRegister' ) )
	{
		echo esc_html("Unauthorized Submission!!", "wpschoolpress");
		exit;
	}
	$username = sanitize_user($_POST['Username']);
	if (wpsp_CheckUsername($username, true) === true)
	{
		echo esc_html("Given Student User Name Already Exists!", "wpschoolpress");
		exit;
	}
	if (email_exists(sanitize_email($_POST['email'])))
	{
		echo esc_html("Student Email ID Already Exists!", "wpschoolpress");
		exit;
	}
	if (strtolower(sanitize_user($_POST['Username'])) == strtolower(sanitize_user($_POST['pUsername'])))
	{
		echo esc_html("Both USer Name Should Not Be same", "wpschoolpress");
		exit;
	}
	if (strtolower(sanitize_email($_POST['pEmail'])) == strtolower(sanitize_email($_POST['Email'])))
	{
		echo  esc_html("Both Email Address Should Not Be same", "wpschoolpress");
		exit;
	}

  if(($_POST['pEmail'] == '') AND (($_POST['pPassword'] != '') OR ($_POST['pConfirmPassword'] != '') OR ($_POST['pUsername'] != ''))){
    echo  esc_html("Please enter parent email", "wpschoolpress");
    exit;
  }

  if(($_POST['pUsername'] == '') AND (($_POST['pEmail'] != '') OR ($_POST['pConfirmPassword'] != '') OR ($_POST['pPassword'] != ''))){
    echo esc_html("Please enter parent username", "wpschoolpress");
    exit;
  }



	global $wpdb;
	$wpsp_student_table = $wpdb->prefix . "wpsp_student";
	$wpsp_class_table = $wpdb->prefix . "wpsp_class";
	$wpsp_class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";


	if (isset($_POST['Class']) && !empty($_POST['Class'])){

		$classID = array_map( 'intval', $_POST['Class'] );
        $classarray = array();

        if(is_numeric($classID) ){
            $classarray[] = $classID;
        }else{
            $class_id_array = $classID;
        foreach ($class_id_array as $id) {
            $classarray[] = $id;
        }
        }

    $messages = '';
    foreach ($classarray as $id) {
      $c = esc_sql($id);
      $capacity = $wpdb->get_var("SELECT c_capacity FROM $wpsp_class_table where cid='$c'");
      $class_array = $wpdb->get_results("SELECT c_name, cid FROM $wpsp_class_table where cid='$c'");
      $classname = $class_array[0]->c_name;
      foreach ($class_array as $value) {
        $class_id_array = $value->cid;
      }

      $classes = $wpdb->get_results("SELECT class_id FROM $wpsp_student_table");
      $classarray = array();
      foreach ($classes as $id) {
        if(is_numeric($id->class_id) ){
          $classarray[] = $id->class_id;
        }else{
          $class_id_array = unserialize($id->class_id);
          foreach ($class_id_array as $cid) {
            $classarray[] = $cid;
           }
        }
      }
      $capacity_array = array_count_values($classarray);

      if (!empty($capacity)){
        if(isset($capacity_array[intval($c)])){
          if (($capacity_array[intval($c)]) >= $capacity){
            $messages .= $classname.", ";
          }
        }
      }

    }
    if ($messages != ''){
      echo '<strong>'.esc_html($messages).'</strong> can not be assigned as it is full. Please remove it and submit.';
      exit;
    }
	}

	global $wpdb;
	$parentMsg = '';
	$parentSendmail = false;
	$wpsp_student_table = $wpdb->prefix . "wpsp_student";
	$firstname = sanitize_text_field($_POST['s_fname']);
	$parent_id = isset($_POST['Parent']) ? sanitize_text_field($_POST['Parent']) : '0';
	$email = sanitize_email($_POST['Email']);
	$pfirstname = sanitize_text_field($_POST['p_fname']);
	$pmiddlename = sanitize_text_field($_POST['p_mname']);
	$plastname = sanitize_text_field($_POST['p_lname']);
	$pgender = sanitize_text_field($_POST['p_gender']);
	$pedu = sanitize_text_field($_POST['p_edu']);
	$pprofession = sanitize_text_field($_POST['p_profession']);
	$pbloodgroup = sanitize_text_field($_POST['p_bloodgrp']);
	$s_p_phone = sanitize_text_field($_POST['s_p_phone']);
	$email = empty($email) ? wpsp_EmailGen($username) : $email;
	$userInfo = array(
		'user_login' => $username,
		'user_pass' => sanitize_text_field($_POST['Password']) ,
		'user_nicename' => sanitize_text_field($_POST['s_fname']) ,
		'first_name' => $firstname,
		'user_email' => $email,
		'role' => 'student'
	);

  $user_id = wp_insert_user($userInfo);

	if (!empty($_POST['pEmail'])){
		$response = getparentInfo(sanitize_email($_POST['pEmail'])); //check for parent email id
		if (isset($response['parentID']) && !empty($response['parentID'])){
			//Use data of existing user
			$parent_id = $response['parentID'];
			$pfirstname = $response['data']->p_fname;
			$pmiddlename = $response['data']->p_mname;
			$plastname = $response['data']->p_lname;
			$pgender = $response['data']->p_gender;
			$pedu = $response['data']->p_edu;
			$pprofession = $response['data']->p_profession;
			$pbloodgroup = $response['data']->p_bloodgrp;
		}
		else{
			if(($_POST['pPassword'] == '') AND (($_POST['pEmail'] != '') OR ($_POST['pConfirmPassword'] != '') OR ($_POST['pUsername'] != ''))){
        echo esc_html("Please enter parent password", "wpschoolpress");
        exit;
      }
      if(($_POST['pConfirmPassword'] == '') AND (($_POST['pEmail'] != '') OR ($_POST['pPassword'] != '') OR ($_POST['pUsername'] != ''))){
        echo esc_html("Please enter parent confirm password", "wpschoolpress");
        exit;
      }

      if(($_POST['pConfirmPassword'] != '') AND ($_POST['pEmail'] != '') AND ($_POST['pPassword'] != '') AND ($_POST['pUsername'] != '')){
        if($_POST['p_fname'] == ''){
          echo esc_html("Please enter parent first name", "wpschoolpress");
          exit;
        }
      }

      if (wpsp_CheckUsername(sanitize_user($_POST['pUsername']) , true) === true)
      {
				$parentMsg = esc_html("Parent UserName Already Exists", "wpschoolpress");
			}
			else
			{
				$parentInfo = array(
					'user_login' => sanitize_user($_POST['pUsername']) ,
					'user_pass' => sanitize_text_field($_POST['pPassword']) ,
					'user_nicename' => sanitize_user($_POST['pUsername']) ,
					'first_name' => sanitize_text_field($_POST['pfirstname']) ,
					'user_email' => sanitize_email($_POST['pEmail']) ,
					'role' => 'parent'
				);

				$parent_id = wp_insert_user($parentInfo); //Creating parent
				$msg = 'Hello ' . sanitize_text_field($_POST['pfirstname']);
				$msg.= '<br />Your are registered as parent at <a href="' . esc_url(site_url()) . '">School</a><br /><br />';
				$msg.= 'Your Login details are below.<br />';
				$msg.= 'Your User Name is : ' . sanitize_user($_POST['pUsername']) . '<br />';
				$msg.= 'Your Password is : ' . sanitize_text_field($_POST['pPassword']) . '<br /><br />';
				$msg.= 'Please Login by clicking <a href="' . esc_url(site_url() . '/sch-dashboard').'">Here </a><br /><br />';
				$msg.= 'Thanks,<br />' . get_bloginfo('name');
				wpsp_send_mail(sanitize_email($_POST['pEmail']) , 'User Registered', $msg);
				if (!is_wp_error($parent_id) && !empty($_FILES['pdisplaypicture']['name']))
				{
					$parentSendmail = true;
					$avatar = uploadImage('pdisplaypicture');
					if (isset($avatar['url']))
					{ //Update parent's profile image
						update_user_meta($parent_id, 'displaypicture', array(
							'full' => $avatar['url']
						));
						update_user_meta($parent_id, 'simple_local_avatar', array(
							'full' => $avatar['url']
						));
					}
				}
				else
				if (is_wp_error($parent_id))
				{
					$parentMsg = $parent_id->get_error_message();
					$parent_id = '';
					$pfirstname = $pmiddlename = $plastname = $pgender = $pedu = $pprofession = $pbloodgroup = '';
				}
			}
		}
	}

	if (!is_wp_error($user_id))
	{

		$studenttable = array(
			'wp_usr_id' => intval($user_id),
			'parent_wp_usr_id' => intval($parent_id),
			'class_id' => isset($_POST['Class']) ? serialize(sanitize_price_array($_POST['Class'])) : '0',
			'class_date' => isset($_POST['Classdata']) ? serialize(sanitize_price_array($_POST['Classdata'])) : '0',
			's_rollno' => isset($_POST['s_rollno']) ? intval($_POST['s_rollno']) : '',
			's_fname' => $firstname,
			's_mname' => isset($_POST['s_mname']) ? sanitize_text_field($_POST['s_mname']) : '',
			's_lname' => isset($_POST['s_lname']) ? sanitize_text_field($_POST['s_lname']) : '',
			's_zipcode' => isset($_POST['s_zipcode']) ? intval($_POST['s_zipcode']) : '',
			's_country' => isset($_POST['s_country']) ? sanitize_text_field($_POST['s_country']) : '',
			's_gender' => isset($_POST['s_gender']) ? sanitize_text_field($_POST['s_gender']) : '',
			's_address' => isset($_POST['s_address']) ? sanitize_text_field($_POST['s_address']) : '',
			's_bloodgrp' => isset($_POST['s_bloodgrp']) ? sanitize_text_field($_POST['s_bloodgrp']) : '',
			's_dob' => isset($_POST['s_dob']) && !empty($_POST['s_dob']) ? wpsp_StoreDate(sanitize_text_field($_POST['s_dob'])) : '',
			's_doj' => isset($_POST['s_doj']) && !empty($_POST['s_doj']) ? wpsp_StoreDate(sanitize_text_field($_POST['s_doj'])) : '',
			's_phone' => isset($_POST['s_phone']) ? sanitize_text_field($_POST['s_phone']) : '',
			'p_fname' => $pfirstname,
			'p_mname' => $pmiddlename,
			'p_lname' => $plastname,
			'p_gender' => $pgender,
			'p_edu' => $pedu,
			'p_profession' => $pprofession,
			's_paddress' => isset($_POST['s_paddress']) ? sanitize_text_field($_POST['s_paddress']) : '',
			'p_bloodgrp' => $pbloodgroup,
			's_city' => isset($_POST['s_city']) ? sanitize_text_field($_POST['s_city']) : '',
			's_pcountry' => isset($_POST['s_pcountry']) ? sanitize_text_field($_POST['s_pcountry']) : '',
			's_pcity' => isset($_POST['s_pcity']) ? sanitize_text_field($_POST['s_pcity']) : '',
			's_pzipcode' => isset($_POST['s_pzipcode']) ? intval($_POST['s_pzipcode']) : '',
			'p_phone'  =>  isset($_POST['s_p_phone']) ? sanitize_text_field($_POST['s_p_phone']) : ''

		);

		$msg = 'Hello ' . sanitize_text_field($_POST['s_fname']);

		$msg.= '<br />Your are registered as student at <a href="' . esc_url(site_url()) . '">School</a><br /><br />';

		$msg.= 'Your Login details are below.<br />';

		$msg.= 'Your User Name is : ' . esc_html($username) . '<br />';

		$msg.= 'Your Password is : ' . sanitize_text_field($_POST['Password']) . '<br /><br />';

		$msg.= 'Please Login by clicking <a href="' . esc_url(site_url() . '/sch-dashboard').'">Here </a><br /><br />';

		$msg.= 'Thanks,<br />' . get_bloginfo('name');



		wpsp_send_mail($email, 'User Registered', $msg);
        $sp_stu_ins = $wpdb->insert($wpsp_student_table, $studenttable);
		$lastid = $wpdb->insert_id;
		do_action('wpsp_student_data_field',$lastid);

		$classidlist	=	array();
	    $classidc_sdate	=	array();

        if (isset($_POST['Class']) && !empty($_POST['Class'])) {
			foreach ($_POST['Class'] as $key => $value) {
				$classidlist[] = $value;
			}

			$classidc_sdate[] = wpsp_StoreDate('0000-00-00');
			//$classidc_sdate[] = $value;

			if(count($classidlist) == count($classidc_sdate)){
				$c = array_combine($classidlist, $classidc_sdate);
				// echo "<pre>";print_r($c);
				$classmapping = $wpdb->prefix . "wpsp_class_mapping";
				do_action('wpsp_student_data_field', $lastid);
				foreach ($c as $key => $value) {
					$wpdb->insert($classmapping, array("sid" => $lastid, "cid" => $key, "date" => wpsp_StoreDate($value)));
				}
			}
		}
		if ($sp_stu_ins){
			do_action('wpsp_student_created', $user_id, $studenttable);
		}

		// send registration mail
		wpsp_send_user_register_mail($userInfo, $user_id);

		if (!empty($_FILES['displaypicture']['name'])){

			$avatar = uploadImage('displaypicture');

			if (isset($avatar['url'])){

				update_user_meta($user_id, 'displaypicture', array(

					'full' => $avatar['url']

				));

				update_user_meta($user_id, 'simple_local_avatar', array(

					'full' => $avatar['url']

				));

			}

		}

		$msg = $sp_stu_ins ? esc_html("success", "wpschoolpress") : esc_html("Oops! Something went wrong try again.","wpschoolpress");

	}

	else

	if (is_wp_error($user_id))

	{

		$msg = $user_id->get_error_message();

	}

	echo wp_kses_post($msg);

	wp_die();

}

add_action('wp_ajax_check_parent_info', 'wpsp_check_parent_info');





/* This function is used for Check Parent Information */

function wpsp_check_parent_info()
{
	$response = array();
	$response['status'] = 0; //Fail status
	if (isset($_POST['parentEmail']) && !empty($_POST['parentEmail']))
	{
		$parentEmail = sanitize_email($_POST['parentEmail']);
		$response = getparentInfo($parentEmail);
	}
	echo wp_json_encode($response);
	exit();

}

/* This function is used for Get Parent Information */

function getparentInfo($parentEmail)
{

	$parentInfo = get_user_by('email', $parentEmail);
	$response['status'] = 0;

	if (!empty($parentInfo))
	{
		global $wpdb;
		$student_table = $wpdb->prefix . "wpsp_student";
		$roles = $parentInfo->roles;
		$parentID = esc_sql($parentInfo->ID);
		$chck_parent = $wpdb->get_row("SELECT p_fname,p_mname,p_lname,p_gender,p_edu,s_phone,p_profession,p_bloodgrp from $student_table where parent_wp_usr_id='$parentID'");
		$response['parentID'] = $parentID;

		if (!empty($chck_parent))

		{

			$response['data'] = $chck_parent;

			$response['status'] = 1;

			$response['username'] = $parentInfo->data->user_login;

		}

	}

	return $response;

}

/* This function is used for Upload Image */

function uploadImage($file)
{
	if (!empty($_FILES[$file]['name']))
	{
		$mimes = array(

			'jpg|jpeg|jpe' => 'image/jpeg',

			'gif' => 'image/gif',

			'png' => 'image/png',

			'bmp' => 'image/bmp',

			'tif|tiff' => 'image/tiff'

		);

		if (!function_exists('wp_handle_upload')) require_once (ABSPATH . 'wp-admin/includes/file.php');
		$avatar = wp_handle_upload($_FILES[$file], array(
			'mimes' => $mimes,
			'test_form' => false
		));

		if (empty($avatar['file']))
		{

			switch ($avatar['error'])
			{

			case 'File type does not meet security guidelines. Try another.':

				add_action('user_profile_update_errors', create_function('$a', '$a->add("avatar_error",__("Please upload a valid image file for the avatar.","kv_student_photo_edit"));'));

				break;

			default:

				add_action('user_profile_update_errors', create_function('$a', '$a->add("avatar_error","<strong>".__("There was an error uploading the avatar:","kv_student_photo_edit")."</strong> ' . $avatar['error'] . '");'));

			}
			return;
		}
		return $avatar;
	}

}


/* This function is used for Update Student */

function wpsp_UpdateStudent(){

	if (!isset($_POST['sregister_nonce']) ||  ! wp_verify_nonce( $_POST['sregister_nonce'], 'StudentRegister' ) )
	{
		echo esc_html("Unauthorized Submission1!!", "wpschoolpress");
		exit;
	}
	
	$user_id = intval($_POST['wp_usr_id']);
	global $wpdb;
	$wpsp_student_table = $wpdb->prefix . "wpsp_student";
	$errors = wpsp_validation(array(
		sanitize_text_field($_POST['s_fname']) => 'required',
		sanitize_text_field($_POST['s_lname']) => 'required'
	));
	if (is_array($errors))
	{
		echo "<div class='col-md-12'><div class='alert alert-danger'>";
		foreach($errors as $error)
		{
			echo "<li>" . esc_html($error) . "</li>";
		}
		echo "</div></div>";
		return false;
	}

	$wpsp_student_table = $wpdb->prefix . "wpsp_student";
	$wpsp_class_table = $wpdb->prefix . "wpsp_class";
	$wpsp_class_mapping_table = $wpdb->prefix . "wpsp_class_mapping";
	if (isset($_POST['Class']) && !empty($_POST['Class']) && intval($_POST['Class']) != intval($_POST['prev_select_class'])){

  	$classID = array_map( 'intval', $_POST['Class'] );
    $classarray = array();

    if(is_numeric($classID) ){
      $classarray[] = $classID;
    }else{
      $class_id_array = $classID;
      foreach ($class_id_array as &$id) {
        $classarray[] = $id;
      }
    }

    $messages = '';
    foreach ($classarray as $id) {
      $c = esc_sql($id);
      $capacity = $wpdb->get_var("SELECT c_capacity FROM $wpsp_class_table where cid='$c'");
      $class_array = $wpdb->get_results("SELECT c_name, cid FROM $wpsp_class_table where cid='$c'");
      $classname = $class_array[0]->c_name;
      foreach ($class_array as $value) {
        $class_id_array = $value->cid;
      }

      $classes = $wpdb->get_results("SELECT class_id FROM $wpsp_student_table");
      $classarray = [];
      foreach ($classes as $id) {
        if(is_numeric($id->class_id) ){
          $classarray[] = $id->class_id;
        }else{
          $class_id_array = unserialize($id->class_id);
          foreach ($class_id_array as $cid) {
            $classarray[] = $cid;
           }
        }
      }
      $capacity_array = array_count_values($classarray);


      if (!empty($capacity)){
        if(isset($capacity_array[intval($c)])){
          if (($capacity_array[intval($c)]) >= $capacity){
            $messages .= $classname.", ";
          }
        }
      }

    }
    if ($messages != ''){
      echo '<strong>'.esc_html($messages).'</strong>can not be assigned as it is full. Please remove it and submit.';
      exit;
    }
	}

	$response = getparentInfo(sanitize_email($_POST['pEmail'])); //check for parent email id
	//print_r($response);
	if ($_POST['parentid'] != $response['parentID']) {
	//	echo "aaaa";
		if (isset($response['parentID']) && !empty($response['parentID']))
		{
        $parent_id = intval($_POST['parentid']);
		$pfirstname = sanitize_text_field($_POST['p_fname']);
  		$pmiddlename = sanitize_text_field($_POST['p_mname']);
  		$plastname = sanitize_text_field($_POST['p_lname']);
  		$pgender = sanitize_text_field($_POST['p_gender']);
  		$pedu = sanitize_text_field($_POST['p_edu']);
  		$pprofession = sanitize_text_field($_POST['p_profession']);
  		$pbloodgroup = sanitize_text_field($_POST['p_bloodgrp']);
  		$phone = sanitize_text_field($_POST['s_phone']);
		}

		else {
        $parent_id = intval($_POST['parentid']);
		$pfirstname = sanitize_text_field($_POST['p_fname']);
		$pmiddlename = sanitize_text_field($_POST['p_mname']);
  		$plastname = sanitize_text_field($_POST['p_lname']);
  		$pgender = sanitize_text_field($_POST['p_gender']);
  		$pedu = sanitize_text_field($_POST['p_edu']);
  		$pprofession = sanitize_text_field($_POST['p_profession']);
  		$pbloodgroup = sanitize_text_field($_POST['p_bloodgrp']);
  		$phone = sanitize_text_field($_POST['s_phone']);
		}
	}
	else {
		$parent_id = intval($_POST['parentid']);
		$pfirstname = sanitize_text_field($_POST['p_fname']);
		$pmiddlename = sanitize_text_field($_POST['p_mname']);
		$plastname = sanitize_text_field($_POST['p_lname']);
		$pgender = sanitize_text_field($_POST['p_gender']);
		$pedu = sanitize_text_field($_POST['p_edu']);
		$pprofession = sanitize_text_field($_POST['p_profession']);
		$pbloodgroup = sanitize_text_field($_POST['p_bloodgrp']);
		$phone = sanitize_text_field($_POST['s_phone']);
	}
	$studenteditprofile = sanitize_text_field($_POST['studenteditprofile']);
	$studentprofileparentnew = sanitize_text_field($_POST['studentprofileparentnew']);

	if ($studenteditprofile == 'studenteditprofile'){

		wpsp_StudentAuthenticate();

		if(! isset( $_POST['sregister_nonce'] ) || ! wp_verify_nonce( $_POST['sregister_nonce'], 'StudentRegister' ) ){
			echo esc_html("Unauthorized Submission or nonce!!", "wpschoolpress");
		    exit;
		}else{
			$studenttable = array(
			's_fname' => isset($_POST['s_fname']) ? sanitize_text_field($_POST['s_fname']) : '',
			// 'class_id' => isset($_POST['Class']) ? serialize(sanitize_price_array($_POST['Class'])) : '0',
			'class_date' => isset($_POST['Classdata']) ? serialize(sanitize_price_array($_POST['Classdata'])) : '0',
			's_mname' => isset($_POST['s_mname']) ? sanitize_text_field($_POST['s_mname']) : '',
			's_lname' => isset($_POST['s_lname']) ? sanitize_text_field($_POST['s_lname']) : '',
			's_zipcode' => isset($_POST['s_zipcode']) ? intval($_POST['s_zipcode']) : '',
			's_country' => isset($_POST['s_country']) ? sanitize_text_field($_POST['s_country']) : '',
			's_gender' => isset($_POST['s_gender']) ? sanitize_text_field($_POST['s_gender']) : '',
			's_address' => isset($_POST['s_address']) ? sanitize_text_field($_POST['s_address']) : '',
			's_bloodgrp' => isset($_POST['s_bloodgrp']) ? sanitize_text_field($_POST['s_bloodgrp']) : '',
			's_dob' => isset($_POST['s_dob']) && !empty($_POST['s_dob']) ? wpsp_StoreDate(sanitize_text_field($_POST['s_dob'])) : '',
			's_city' => isset($_POST['s_city']) ? sanitize_text_field($_POST['s_city']) : '',
			'p_phone'  =>  isset($_POST['s_p_phone']) ? sanitize_text_field($_POST['s_p_phone']) : ''
			);

			$stu_upd = $wpdb->update($wpsp_student_table, $studenttable, array(
			'wp_usr_id' => $user_id
			));
		}

	} elseif ($studentprofileparentnew == 'studentprofileparentnew'){

		wpsp_Authenticate();

		$studenttable = array(

		'class_id' => isset($_POST['Class']) ? serialize(sanitize_price_array($_POST['Class'])) : '0',
        'class_date' => isset($_POST['Classdata']) ? serialize(sanitize_price_array($_POST['Classdata'])) : '0',
		's_rollno' => isset($_POST['s_rollno']) ? sanitize_text_field($_POST['s_rollno']) : '',
		's_fname' => isset($_POST['s_fname']) ? sanitize_text_field($_POST['s_fname']) : '',
		's_mname' => isset($_POST['s_mname']) ? sanitize_text_field($_POST['s_mname']) : '',
		's_lname' => isset($_POST['s_lname']) ? sanitize_text_field($_POST['s_lname']) : '',
		's_zipcode' => isset($_POST['s_zipcode']) ? intval($_POST['s_zipcode']) : '',
		's_country' => isset($_POST['s_country']) ? sanitize_text_field($_POST['s_country']) : '',
		's_gender' => isset($_POST['s_gender']) ? sanitize_text_field($_POST['s_gender']) : '',
		's_address' => isset($_POST['s_address']) ? sanitize_text_field($_POST['s_address']) : '',
		's_bloodgrp' => isset($_POST['s_bloodgrp']) ? sanitize_text_field($_POST['s_bloodgrp']) : '',
		's_dob' => isset($_POST['s_dob']) && !empty($_POST['s_dob']) ? wpsp_StoreDate(sanitize_text_field($_POST['s_dob'])) : '',
		's_doj' => isset($_POST['s_doj']) && !empty($_POST['s_doj']) ? wpsp_StoreDate(sanitize_text_field($_POST['s_doj'])) : '',
		's_paddress' => isset($_POST['s_paddress']) ? sanitize_text_field($_POST['s_paddress']) : '',
		's_city' => isset($_POST['s_city']) ? sanitize_text_field($_POST['s_city']) : '',
		's_pcountry' => isset($_POST['s_pcountry']) ? sanitize_text_field($_POST['s_pcountry']) : '',
		's_pcity' => isset($_POST['s_pcity']) ? sanitize_text_field($_POST['s_pcity']) : '',
		's_pzipcode' => isset($_POST['s_pzipcode']) ? intval($_POST['s_pzipcode']) : '',
		'p_phone'  =>  isset($_POST['s_p_phone']) ? sanitize_text_field($_POST['s_p_phone']) : ''

	);

		// Update date Details onclass mapping table
		updateStudentClassDate($user_id);

		//End Update date Details onclass mapping table


		$stu_upd = $wpdb->update($wpsp_student_table, $studenttable, array(
			'wp_usr_id' => $user_id
		));

       $pusername = sanitize_user($_POST['pUsername']);

        if (wpsp_CheckUsername($pusername, true) === true){
				echo esc_html("Given Parent User Name Already Exists!", "wpschoolpress");
				exit;
			}

			if (email_exists(sanitize_email($_POST['pEmail'])))
			{

				echo esc_html("Parent Email ID Already Exists!", "wpschoolpress");

				exit;

			}

			if (!empty($_POST['pEmail']))
				{
					$response = getparentInfo(sanitize_email($_POST['pEmail'])); //check for parent email id
					if (isset($response['parentID']) && !empty($response['parentID']))

					{
						//Use data of existing user
						$parent_id = $response['parentID'];
						$pfirstname = $response['data']->p_fname;
						$pmiddlename = $response['data']->p_mname;
						$plastname = $response['data']->p_lname;
						$pgender = $response['data']->p_gender;
						$pedu = $response['data']->p_edu;
						$pprofession = $response['data']->p_profession;
						$pbloodgroup = $response['data']->p_bloodgrp;
					}
					else
					{
						if (wpsp_CheckUsername(sanitize_user($_POST['pUsername']) , true) === true)
						{
							$parentMsg = esc_html("Parent UserName Already Exists", "wpschoolpress");
						}
						else
						{
							$parentInfo = array(
								'user_login' => sanitize_user($_POST['pUsername']) ,
								'user_pass' => sanitize_text_field($_POST['pPassword']) ,
								'user_nicename' => sanitize_user($_POST['pUsername']) ,
								'first_name' => sanitize_text_field($_POST['pfirstname']) ,
								'user_email' => sanitize_email($_POST['pEmail']) ,
								'role' => 'parent'
							);

							$parent_id = wp_insert_user($parentInfo); //Creating parent
							$msg = 'Hello ' . sanitize_text_field($_POST['pfirstname']);
							$msg.= '<br />Your are registered as parent at <a href="' . esc_url(site_url()) . '">School</a><br /><br />';
							$msg.= 'Your Login details are below.<br />';
							$msg.= 'Your User Name is : ' . sanitize_user($_POST['pUsername']) . '<br />';
							$msg.= 'Your Password is : ' . sanitize_text_field($_POST['pPassword']) . '<br /><br />';
							$msg.= 'Please Login by clicking <a href="' . esc_url(site_url() . '/sch-dashboard').'">Here </a><br /><br />';
							$msg.= 'Thanks,<br />' . get_bloginfo('name');
							wpsp_send_mail(sanitize_email($_POST['pEmail']) , 'User Registered', $msg);
							if (!is_wp_error($parent_id) && !empty($_FILES['p_displaypicture']['name']))
							{
								$parentSendmail = true;
								$avatar = uploadImage('p_displaypicture');
								if (isset($avatar['url']))
								{ //Update parent's profile image
									update_user_meta($parent_id, 'p_displaypicture', array(
										'full' => $avatar['url']
									));
									update_user_meta($parent_id, 'simple_local_avatar', array(
										'full' => $avatar['url']
									));
								}
							}
							else
							if (is_wp_error($parent_id))
							{
								$parentMsg = $parent_id->get_error_message();
								$parent_id = '';
								$pfirstname = $pmiddlename = $plastname = $pgender = $pedu = $pprofession = $pbloodgroup = '';
							}
						}
					}
				}
		 	if (!is_wp_error($user_id)){
				$parenttable = array(
				'parent_wp_usr_id' => $parent_id,
				'p_fname' => $pfirstname,
				'p_mname' => $pmiddlename,
				'p_lname' => $plastname,
				'p_gender' => $pgender,
				'p_edu' => $pedu,
				'p_profession' => $pprofession,
				'p_bloodgrp' => $pbloodgroup,
				's_phone' => $phone
				);


				// Update date Details onclass mapping table
				updateStudentClassDate($user_id);
				//End Update date Details onclass mapping table

				 $sp_stu_ins = $wpdb->update($wpsp_student_table, $parenttable, array(
			 	'wp_usr_id' => $user_id
			 		));
					do_action('wpsp_student_data_field',$user_id);
				if (is_wp_error($user_id))
				{
					$msg1 = $user_id->get_error_message();
				}
				echo wp_kses_post($msg1);
			}
	} else {

		wpsp_Authenticate();

		$studenttable = array(
		'class_id' => isset($_POST['Class']) ? serialize(sanitize_price_array($_POST['Class'])) : '0',
        'class_date' => isset($_POST['Classdata']) ? serialize(sanitize_price_array($_POST['Classdata'])) : '0',
		's_rollno' => isset($_POST['s_rollno']) ? sanitize_text_field($_POST['s_rollno']) : '',
		's_fname' => isset($_POST['s_fname']) ? sanitize_text_field($_POST['s_fname']) : '',
		's_mname' => isset($_POST['s_mname']) ? sanitize_text_field($_POST['s_mname']) : '',
		's_lname' => isset($_POST['s_lname']) ? sanitize_text_field($_POST['s_lname']) : '',
		's_zipcode' => isset($_POST['s_zipcode']) ? intval($_POST['s_zipcode']) : '',
		's_country' => isset($_POST['s_country']) ? sanitize_text_field($_POST['s_country']) : '',
		's_gender' => isset($_POST['s_gender']) ? sanitize_text_field($_POST['s_gender']) : '',
		's_address' => isset($_POST['s_address']) ? sanitize_text_field($_POST['s_address']) : '',
		's_bloodgrp' => isset($_POST['s_bloodgrp']) ? sanitize_text_field($_POST['s_bloodgrp']) : '',
		's_dob' => isset($_POST['s_dob']) && !empty($_POST['s_dob']) ? wpsp_StoreDate(sanitize_text_field($_POST['s_dob'])) : '',
		's_doj' => isset($_POST['s_doj']) && !empty($_POST['s_doj']) ? wpsp_StoreDate(sanitize_text_field($_POST['s_doj'])) : '',
		's_phone' => $phone,
		'p_fname' => $pfirstname,
		'p_mname' => $pmiddlename,
		'p_lname' => $plastname,
		'p_gender' => $pgender,
		'p_edu' => $pedu,
		'p_profession' => $pprofession,
		's_paddress' => isset($_POST['s_paddress']) ? sanitize_text_field($_POST['s_paddress']) : '',
		'p_bloodgrp' => $pbloodgroup,
		's_city' => isset($_POST['s_city']) ? sanitize_text_field($_POST['s_city']) : '',
		's_pcountry' => isset($_POST['s_pcountry']) ? sanitize_text_field($_POST['s_pcountry']) : '',
		's_pcity' => isset($_POST['s_pcity']) ? sanitize_text_field($_POST['s_pcity']) : '',
		's_pzipcode' => isset($_POST['s_pzipcode']) ? intval($_POST['s_pzipcode']) : '',
		'p_phone'  =>  isset($_POST['s_p_phone']) ? sanitize_text_field($_POST['s_p_phone']) : ''
	);

		$parenttable = array(
		'parent_wp_usr_id' => $parent_id,
		'p_fname' => $pfirstname,
		'p_mname' => $pmiddlename,
		'p_lname' => $plastname,
		'p_gender' => $pgender,
		'p_edu' => $pedu,
		'p_profession' => $pprofession,
		'p_bloodgrp' => $pbloodgroup,
		's_phone' => $phone
		);

		// Update date Details onclass mapping table
		updateStudentClassDate($user_id);
		//End Update date Details onclass mapping table

	
		$stu_upd = $wpdb->update($wpsp_student_table, $parenttable, array(
			'parent_wp_usr_id' => $_POST['parentid']
		));

		$stu_upd = $wpdb->update($wpsp_student_table, $studenttable, array(
			'wp_usr_id' => $user_id
		));
	
     do_action('wpsp_student_data_field',$user_id);
	 // Send Email if class assign and Addon Purchased

	 if($stu_upd){
		 $proversion1  = wpsp_check_pro_version('wpsp_addon_version');
		 $prodisable1  = !$proversion1['status'] ? 'notinstalled'    : 'installed';
		 if($proversion1['status']){
		 	$wpsp_c_table = $wpdb->prefix . "wpsp_class";
			$wpsp_u_table = $wpdb->prefix . "users";
			$useremail    = $wpdb->get_var("SELECT user_email FROM $wpsp_u_table where ID = '".esc_sql($user_id)."'");
		 	$parentemail  = $wpdb->get_var("SELECT user_email FROM $wpsp_u_table where ID = '".esc_sql($parent_id)."'");
		 	$uname = sanitize_text_field($_POST['s_fname']);
		 	$upname = sanitize_text_field($_POST['p_fname']);
		 	$classid_array =  array_map( 'intval', $_POST['Class'] );
		 	$classIDArray = array();
			foreach ($classIDArray as $id ) {
                $c = esc_sql($id);
				$clasname = $wpdb->get_var("SELECT c_name FROM $wpsp_c_table where cid='$c'");
				$classIDArray[] = $clasname;
			}

			$clasnames = implode(", ",$classIDArray);
	 		if( $classnameid != "") {
	 			// Student email
	 			$smsg = 'Hi ' . esc_html($uname);
				$smsg.= '<br /><br />It is to notify you that you are assigned to '.esc_html($classnames).'. Please <a href="' . esc_url(site_url() . '/sch-dashboard').'">click here</a> to login and get more details of your class.<br /><br />';
				$smsg.= 'Regards,<br />' . get_bloginfo('name');
				wpsp_send_mail($useremail, 'Your class details', $smsg);
				// Parent email
				$pmsg = 'Hi ' . esc_html($upname);
				$pmsg.= '<br /><br />It is to notify you that your child '.esc_html($uname).' are assigned to '.esc_html($clasnames).'. Please <a href="' . esc_url(site_url() . '/sch-dashboard').'">click here</a> to login and get more details of class.<br /><br />';
				$pmsg.= 'Regards,<br />' . get_bloginfo('name');
				wpsp_send_mail($parentemail, 'Your child class details', $pmsg);
	 		} else {
			}
		 }
	 }
   // End of the class assign email
	}

	if ($stu_upd)
	{
		do_action('wpsp_UpdateStudent', $user_id, $studenttable);
	}
	if (!empty($_FILES['displaypicture']['name']))
	{
		$avatar = uploadImage('displaypicture');
		if (isset($avatar['url']))
		{
			update_user_meta($user_id, 'displaypicture', array(
				'full' => $avatar['url']
			));
			update_user_meta($user_id, 'simple_local_avatar', array(
				'full' => $avatar['url']
			));
		}
	}
	// Update Parents Profile Picture
	if (!empty($_FILES['p_displaypicture']['name']))
    {
        $p_avatar = uploadImage('p_displaypicture');
        $parentid_img = intval($_POST['parentid']);
        if (isset($p_avatar['url']))
        {
            update_user_meta($parentid_img, 'displaypicture', array(
                'full' => $p_avatar['url']
            ));
            update_user_meta($parentid_img, 'simple_local_avatar', array(
                'full' => $p_avatar['url']
            ));
        }
    }
	 if (is_wp_error($stu_upd))
	{
		$msg =  $stu_upd->get_error_message() ;
	}
	else
	{
		$msg = esc_html("success", "wpschoolpress");
	}
	echo wp_kses_post($msg);
}

/* This function is used for View Student Profile Popup */
function wpsp_StudentPublicProfile()
{
	global $wpdb;
	$student_table = $wpdb->prefix . "wpsp_student";
	$class_table = $wpdb->prefix . "wpsp_class";
	$users_table = $wpdb->prefix . "users";
	$sid = intval($_POST['id']);
	$stinfo = $wpdb->get_row("select a.*,b.c_name,d.user_email from $student_table a LEFT JOIN $class_table b ON a.class_id=b.cid LEFT JOIN $users_table d ON d.ID=a.wp_usr_id where a.wp_usr_id='".esc_sql($sid)."'");
	if (!empty($stinfo)){
		if (is_numeric($stinfo->class_id)){
			$classIDArray[] = sanitize_text_field($stinfo->class_id);
		}else{
			$classIDArray = unserialize($stinfo->class_id);
		}
	$classname_array = [];

	foreach ($classIDArray as $id ) {
        $id = esc_sql($id);
		$clasname = $wpdb->get_var("SELECT c_name FROM $class_table where cid='$id'");
		$classname_array[] = $clasname;
	}
		$loc_avatar = get_user_meta($stinfo->wp_usr_id, 'simple_local_avatar', true);
		$img_url = isset($loc_avatar['full']) && !empty($loc_avatar['full']) ? $loc_avatar['full'] : WPSP_PLUGIN_URL . 'img/avatar.png';
		$stinfo->imgurl = $img_url;
		$parentID = $stinfo->parent_wp_usr_id;
		$parentEmail = '';
		if (!empty($parentID))
		{
			$parentInfo = get_userdata($parentID);
			$parentEmail = isset($parentInfo->data->user_email) ? sanitize_email($parentInfo->data->user_email) : '';
		}

		$profile = "<div class='wpsp-panel-body'>
					<div class='wpsp-userpic'>
						<img src='".esc_url($img_url)."' height='150px' width='150px' class='wpsp-img-round'/>
					</div>
					<div class='wpsp-userDetails'>
						<table class='wpsp-table'>
							<tbody>
								<tr>
								    <td><strong>Full Name:</strong> ".esc_html($stinfo->s_fname)." ".esc_html($stinfo->s_mname)." ".esc_html($stinfo->s_lname)."</td>
									<td><strong>Gender: </strong>".esc_html($stinfo->s_gender)."</td>
								</tr>
								<tr>
									<td><strong>Date of Birth: </strong>" . wpsp_ViewDate(esc_html($stinfo->s_dob)) . "</td>
									<td><strong>Blood Group: </strong>".esc_html($stinfo->s_bloodgrp)."</td>
								</tr>
								<tr>
									<td><strong>City: </strong>".esc_html($stinfo->s_pcity)."</td>
									<td><strong>Country: </strong>".esc_html($stinfo->s_country)."</td>
								</tr>

								<tr>
									<td><strong>Date of Join: </strong>" . wpsp_ViewDate(esc_html($stinfo->s_doj)) . "</td>
									<td><strong>Street Address: </strong>".esc_html($stinfo->s_address)."</td>
								</tr>
								<tr>
									<td><strong>Pin Code: </strong>".esc_html($stinfo->s_zipcode)."</td>
									<td><strong>Email: </strong>".esc_html($stinfo->user_email)."</td>
								</tr>
								<tr>
									<td><strong> ". apply_filters( 'wpsp_parent_popupvalue', esc_html__( 'Parent Name:', 'wpschoolpress' ))."</strong>
										".esc_html($stinfo->p_fname. $stinfo->p_mname.  $stinfo->p_lname)."
									</td>
									<td><strong>". apply_filters( 'wpsp_parent_popupvalue_gender', esc_html__( 'Parent Gender:', 'wpschoolpress' ))." </strong>".esc_html($stinfo->p_gender)."</td>
								</tr>
								<tr>
									<td><strong>". apply_filters( 'wpsp_parent_popupvalue_email', esc_html__( 'Parent Email:', 'wpschoolpress' ))." </strong>".esc_html($parentEmail)."</td>
									<td><strong>". apply_filters( 'wpsp_parent_popupvalue_parentprofession', esc_html__( ' Parent Profession:', 'wpschoolpress' ))." </strong>".esc_html($stinfo->p_profession)."</td>
								</tr>
								<tr>
									<td colspan='2'><strong>Phone Number: </strong>".esc_html($stinfo->s_phone)."</td>
								</tr>
								<tr>
									<td><strong>Roll No: </strong>".esc_html($stinfo->s_rollno)."</td>
									<td><strong>Class: </strong>".esc_html(implode(", ",$classname_array))."</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>";
	}
	else
	{
		$profile = esc_html("No data retrived", "wpschoolpress");
	}
	echo apply_filters('wpsp_student_profile', wp_kses_post($profile), intval($sid));
	wp_die();
}



function updateStudentClassDate($user_id){
  $user_id = intval($user_id);
  // Update date Details onclass mapping table
  global $wpdb;
  $classidlist	=	array();
  $classidc_sdate	=	array();
  $student_index = '';
  $student_table = $wpdb->prefix."wpsp_student";
  $classmapping = $wpdb->prefix . "wpsp_class_mapping";
  foreach($_POST['Class'] as $key => $value ){
    $classidlist[] = $value;
  }

  foreach($_POST['Classdata'] as $key => $value ){
    if($value == ''){
      $classidc_sdate[] = wpsp_StoreDate('0000-00-00');
    }else{
      $classidc_sdate[] = $value;
    }
  }

  if(count($classidlist) == count($classidc_sdate)){
    $c = array_combine($classidlist,$classidc_sdate);
  }


  $stinfo  =  $wpdb->get_row("select * from $student_table where wp_usr_id='".esc_sql($user_id)."'");
  if( !empty( $stinfo ) ) {
    $student_index = $stinfo->sid;
  }


  $wpdb->delete( $classmapping, array("sid" => $student_index));

  foreach($c as $key => $value){
    $wpdb->insert( $classmapping, array("sid" => $student_index, "cid" => $key,"date" => wpsp_StoreDate($value)));
  }

}
//End Update date Details onclass mapping table
?>