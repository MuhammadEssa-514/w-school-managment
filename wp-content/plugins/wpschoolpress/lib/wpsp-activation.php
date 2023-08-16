<?php

	if ( ! defined( 'ABSPATH' ) )

		exit('No Such File');



	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');



   $dashboard_page				=	array( 'slug' => 'sch-dashboard', 'title' =>'Dashboard' );

   $dashboard_found	=	0;

	$pages = get_pages();

	foreach ($pages as $page) {

		$apage = $page->post_name;

		switch ( $apage ){

			case 'sch-dashboard' :  	$dashboard_found= '1';			break;

			default:					$no_page;

		}

	}

	if( $dashboard_found != '1' ){

		$dashboard_found_data = array('post_title'	=> $dashboard_page['title'],

								'post_type'		=>	'page',

								'post_name'	=>	$dashboard_page['slug'],

								'post_status'	=>	'publish',

								'post_excerpt'	=>	'Dashboard contains all the overview ! ' );

		$page_id = wp_insert_post($dashboard_found_data);

	}





	global $wpdb;

	$teacher_table            = $wpdb->prefix . 'wpsp_teacher';

	$class_table_mapping      = $wpdb->prefix . 'wpsp_class_mapping';

	$student_table            = $wpdb->prefix . 'wpsp_student';

	$class_table              = $wpdb->prefix . 'wpsp_class';

	$exams_table              = $wpdb->prefix . 'wpsp_exam';

	$mark_fields_table        = $wpdb->prefix . 'wpsp_mark_fields';

	$mark_table               = $wpdb->prefix . 'wpsp_mark';

	$mark_extract_table       = $wpdb->prefix . 'wpsp_mark_extract';

	$messages_table           = $wpdb->prefix . 'wpsp_messages';

	$messages_table_delete    = $wpdb->prefix . 'wpsp_messages_delete';

	$time_table               = $wpdb->prefix . 'wpsp_timetable';

	$notification_table       = $wpdb->prefix . 'wpsp_notification';

	$subject_table            = $wpdb->prefix . 'wpsp_subject';

	$workinghours_table       = $wpdb->prefix . 'wpsp_workinghours';

	$transport_table          = $wpdb->prefix . 'wpsp_transport';

	$settings_table           = $wpdb->prefix . 'wpsp_settings';

	$attendance_table         = $wpdb->prefix . 'wpsp_attendance';

	$teacher_attendance_table = $wpdb->prefix . 'wpsp_teacher_attendance';

	$events_table             = $wpdb->prefix . 'wpsp_events';

	$grade_settings_table     = $wpdb->prefix . 'wpsp_grade';

	$import_history_table     = $wpdb->prefix . 'wpsp_import_history';

	$leave_table              = $wpdb->prefix . 'wpsp_leavedays';

	$temp_table            =  $wpdb->prefix . 'wpsp_temp';

	$charset_collate = $wpdb->get_charset_collate();





	$sql_temp = "CREATE TABLE IF NOT EXISTS $temp_table (

					 `t_id` int(11) NOT NULL AUTO_INCREMENT,

					 `t_name` varchar(255) DEFAULT NULL,

					 `t_username` varchar(255) DEFAULT NULL,

					 `t_email` varchar(255) DEFAULT NULL,

					 `t_password` varchar(255) DEFAULT NULL,

					 `t_type` varchar(255) NOT NULL,

					 `t_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

 					  `t_active` int(11) DEFAULT '1',

					 PRIMARY KEY (`t_id`)

					) ENGINE=InnoDB DEFAULT CHARSET=latin1";

        dbDelta($sql_temp);



	 $class_table_mapping_table="CREATE TABLE IF NOT EXISTS $class_table_mapping (

                  `id` bigint(11) NOT NULL AUTO_INCREMENT,

                  `sid` bigint(11) NOT NULL,

                  `cid` bigint(11) NOT NULL,

                  `date` date,

                  PRIMARY KEY (`id`)

                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci" ;

        dbDelta($class_table_mapping_table);

        $messages_table_delete="CREATE TABLE IF NOT EXISTS $messages_table_delete (

                  `id` bigint(11) NOT NULL AUTO_INCREMENT,

                  `m_id` bigint(11) NOT NULL,

                  `user_id` bigint(11) NOT NULL,

                  `delete_status` bigint(11) NOT NULL,

                  PRIMARY KEY (`id`)

                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci" ;

        dbDelta($messages_table_delete);



        $sql_teacher_attendance_table="CREATE TABLE IF NOT EXISTS $teacher_attendance_table (

                  `id` bigint(11) NOT NULL AUTO_INCREMENT,

                  `teacher_id` bigint(11) NOT NULL,

                  `status` VARCHAR(10),

                  `leave_date` date,

                  `reason` VARCHAR(250),

                  `entry_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                  PRIMARY KEY (`id`)

                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci" ;

        dbDelta($sql_teacher_attendance_table);

        $sql_leave_table = "CREATE TABLE IF NOT EXISTS $leave_table (

                  `id` bigint(11) NOT NULL AUTO_INCREMENT,

                  `class_id` int(11) NOT NULL,

                  `leave_date` date,

                  `description` VARCHAR(150),

                  PRIMARY KEY (`id`)

                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

        dbDelta($sql_leave_table);

		$sql_import_history = "CREATE TABLE IF NOT EXISTS $import_history_table (

		  `id` int(11) NOT NULL AUTO_INCREMENT,

		  `type` int(1) NOT NULL,

		  `imported_id` longtext NOT NULL,

		  `time` datetime NOT NULL,

		  `count` int(11) NOT NULL,

		  PRIMARY KEY (`id`)

		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

		dbDelta($sql_import_history);



		$sql_grade = "CREATE TABLE IF NOT EXISTS $grade_settings_table  (

	  `gid` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `g_name` varchar(60),

	  `g_point` varchar(5),

	  `mark_from` int(3),

	  `mark_upto` int(3),

	  `comment` varchar(60)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

	dbDelta($sql_grade);

	$sql_events_table = "CREATE TABLE IF NOT EXISTS $events_table  (

	  `id` bigint(15)  UNSIGNED NOT NULL AUTO_INCREMENT,

	  `start` varchar(50) DEFAULT NULL,

      `end` varchar(50) DEFAULT NULL,

      `type` varchar(10) DEFAULT NULL,

      `title` text,

      `description` longtext,

      `color` varchar(20) DEFAULT NULL,

       PRIMARY KEY (`id`)

     ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

	dbDelta($sql_events_table);





	$sql_attendance_table = "CREATE TABLE IF NOT EXISTS $attendance_table  (

	  `aid` int(15)  UNSIGNED NOT NULL AUTO_INCREMENT,

	  `class_id` varchar(15) DEFAULT NULL,

	  `absents` text DEFAULT NULL,

	  `date` date,

      `entry` timestamp,

       PRIMARY KEY (`aid`)

     ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;



	dbDelta($sql_attendance_table);



	$sql_settings_table = "CREATE TABLE $settings_table  (

	  `id` int(15)  UNSIGNED NOT NULL AUTO_INCREMENT,

	  `option_name` varchar(50) DEFAULT NULL,

      `option_value` text DEFAULT NULL,

       PRIMARY KEY (`id`)

     ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;



	dbDelta($sql_settings_table);



	$sql_transport_table = "CREATE TABLE $transport_table  (

	  `id` int(15)  UNSIGNED NOT NULL AUTO_INCREMENT,

	  `bus_no` varchar(30) DEFAULT NULL,

      `bus_name` varchar(50) DEFAULT NULL,

      `driver_name` varchar(50) DEFAULT NULL,

      `bus_route` mediumtext DEFAULT NULL,

	  `route_fees` varchar(5) DEFAULT NULL,

      `phone_no` varchar(50) DEFAULT NULL,

       PRIMARY KEY (`id`)

     ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;



	dbDelta($sql_transport_table);



	$sql_time_table = "CREATE TABLE IF NOT EXISTS $time_table  (

	  `id` int(15)  UNSIGNED NOT NULL AUTO_INCREMENT,

	  `class_id` int(10) NOT NULL,

      `time_id` int(10) NOT NULL,

      `subject_id` int(10) NOT NULL,

      `session_id` int(10) NOT NULL,

      `day` int(2) NOT NULL,

      `heading` text DEFAULT NULL,

      `is_active` int(10) NOT NULL,

      PRIMARY KEY (`id`)

     ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;

	dbDelta($sql_time_table);



	$sql_workinghours_table="CREATE TABLE IF NOT EXISTS `$workinghours_table` (

	  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,

	  `hour` varchar(20) DEFAULT NULL,

	  `begintime` VARCHAR(10) NOT NULL,

	  `endtime` VARCHAR(10) NOT NULL,

	  `type` varchar(20) NOT NULL,

	  PRIMARY KEY (`id`)

	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_workinghours_table);



	$sql_subject = "CREATE TABLE IF NOT EXISTS $subject_table  (

	  `id` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `sub_code` varchar(8),

	  `class_id` int(15),

	  `sub_name` varchar(60),

	  `sub_teach_id` varchar(15),

	  `book_name` varchar(60),

	  `sub_desc` varchar(250),

	  `max_mark` int(4),

	  `pass_mark` int(4)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

	dbDelta($sql_subject);

	$sql_notification = "CREATE TABLE IF NOT EXISTS $notification_table  (

	  `nid` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `name` varchar(50),

	  `description` varchar(255),

	  `receiver` varchar(255),

	  `type` int(11),

	  `date` datetime,

	  `status` int(11)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_notification);



	$sql_message = "CREATE TABLE IF NOT EXISTS $messages_table  (

	  `mid` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `s_id` int(15),

	  `r_id` int(15),

	  `subject` varchar(250),

	  `msg` longtext,

	  `replay_id` int(11),

  	  `main_m_id` int(11),

	  `del_stat` int(15),

	  `s_read` int(11) DEFAULT 0,

	  `r_read` int(11) DEFAULT 0,

	  `m_date` timestamp

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_message);

	$sql_mark_fields = "CREATE TABLE IF NOT EXISTS $mark_fields_table  (

	  `field_id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `subject_id` int(12),

	  `field_text` varchar(60)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_mark_fields);

	$sql_mark = "CREATE TABLE IF NOT EXISTS $mark_table  (

	  `mid` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `subject_id` varchar(128),

	  `class_id` int(15),

	  `student_id` int(15),

	  `exam_id` int(15),

	  `mark` varchar(60),

	  `remarks` text DEFAULT NULL,

	  `attendance` varchar(60)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_mark);

	$sql_mark_extract = "CREATE TABLE IF NOT EXISTS $mark_extract_table  (

	  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `student_id` bigint(20),

	  `field_id` bigint(20),

	  `exam_id` int(12),

	  `subject_id` int(12),

	  `mark` varchar(10)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_mark_extract);



	$sql_exam = "CREATE TABLE $exams_table  (

	  `eid` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `classid` int(15),

	  `subject_id` varchar(128),

	  `e_name` varchar(128),

	  `e_s_date` date,

	  `e_e_date` date,

	  `entry_date` timestamp

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_exam);





	$sql_class = "CREATE TABLE $class_table  (

	  `cid` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `c_numb` varchar(128),

	  `c_name` varchar(128),

	  `teacher_id` int(15),

	  `c_capacity` int(5),

	  `c_loc` varchar(60),

	   `c_sdate` date,

	  `c_edate` date,

	   `c_fee_type` varchar(60)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_class);





$sql_student = "CREATE TABLE $student_table  (

	  `sid` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `wp_usr_id` bigint(20),

	  `parent_wp_usr_id` int(15),

	  `s_rollno` varchar(15),

	  `s_fname` varchar(30),

	  `s_mname` varchar(30),

	  `s_lname` varchar(30),

	  `s_dob` date,

	  `s_gender` varchar(10),

	  `s_address` varchar(200),

	  `s_paddress` varchar(200),

	  `s_country` varchar(20),

	  `s_zipcode` varchar(10),

	  `s_phone` varchar(25),

	  `s_bloodgrp` varchar(20),

	  `s_doj` date,

	  `class_id` varchar(255),

	  `class_date` varchar(255),

	  `s_pzipcode` varchar(10),

	  `s_pcountry` varchar(20),

	  `s_city` varchar(20),

	  `s_pcity` varchar(20),

	  `p_fname` varchar(30),

	  `p_mname` varchar(30),

	  `p_lname` varchar(30),

	  `p_gender` varchar(10),

	  `p_edu` varchar(50),

	  `p_phone` varchar(25),

	  `p_profession` varchar(60),

	  `p_bloodgrp` varchar(10)

	  )ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_student);



	$sql_teacher = "CREATE TABLE $teacher_table  (

	  `tid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,

	  `wp_usr_id` bigint(20),

	  `first_name` varchar(30),

	  `middle_name` varchar(30),

	  `last_name` varchar(30),

	  `zipcode` varchar(10),

	  `country` varchar(20),

	  `city` varchar(20),

	  `address` varchar(200),

	  `empcode` varchar(60),

	  `dob` date,

	  `doj` date,

	  `dol` date,

	  `phone` varchar(25),

	  `qualification` varchar(25),

	  `gender` varchar(12),

	  `bloodgrp` varchar(5),

	  `position` varchar(50),

	  `whours` varchar(50)

	)ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";

	dbDelta($sql_teacher);



	//Duration after how many times it occurs

	//due time  after that it consider as due date

	$sql_fees = $wpdb->prefix . 'wpsp_fees';

	$sql = "CREATE TABLE IF NOT EXISTS ".$sql_fees." (

			  `fees_id` int(11) NOT NULL AUTO_INCREMENT,

              `class_id` int(11) NOT NULL,


              `order_id` int(11) NOT NULL,

			  `student_id` int(11) NOT NULL,

			  `fees_amount` float NOT NULL,

			  `description` text NOT NULL,

			  `duration` text NOT NULL,

			  `paymentType` text NOT NULL,

			  `due_time` int(2) NOT NULL,

			  `start_date` datetime NOT NULL,

			  `end_date` datetime NOT NULL,

			  `created_date` datetime NOT NULL,

			  `created_by` int(11) NOT NULL,

			  PRIMARY KEY (`fees_id`)

			) DEFAULT CHARSET=utf8";

		dbDelta($sql);



	$sql_fees_payment = $wpdb->prefix . 'wpsp_fees_payment';

	$sql = "CREATE TABLE IF NOT EXISTS ".$sql_fees_payment." (

			  `fees_pay_id` int(11) NOT NULL AUTO_INCREMENT,

			  `class_id` int(11) NOT NULL,

			  `student_id` bigint(20) NOT NULL,

			  `fees_id` int(11) NOT NULL,

			  `fees_paid_amount` float NOT NULL,

			  `payment_status` varchar(10) NOT NULL,

			  `paid_due_date` date NOT NULL,

			  PRIMARY KEY (`fees_pay_id`)

			) DEFAULT CHARSET=utf8";

		dbDelta($sql);

		// 1 for suceess & 2 for fail

	$table_fee_payment_history = $wpdb->prefix . 'wpsp_fee_payment_history';

	$sql = "CREATE TABLE IF NOT EXISTS ".$table_fee_payment_history." (

			  `payment_history_id` bigint(20) NOT NULL AUTO_INCREMENT,

			  `fees_pay_id` int(11) NOT NULL,

			  `amount` float NOT NULL,

			  `payment_method` varchar(50) NOT NULL,

			  `paid_date` date NOT NULL,

			  `paid_by` bigint(20) NOT NULL,

			  `paid_status` int(2) NOT NULL,

			  `paymentdescription` text NOT NULL,

			  PRIMARY KEY (`payment_history_id`)

			) DEFAULT CHARSET=utf8";

		dbDelta($sql);

		$sql1111 = "INSERT INTO ".$subject_table." (`id`, `sub_code`, `class_id`, `sub_name`, `sub_teach_id`, `book_name`, `sub_desc`, `max_mark`, `pass_mark`) VALUES (NULL, NULL, NULL, 'Break', NULL, NULL, NULL, NULL, NULL)";

		 $wpdb->query($sql1111);

	global $wp_rewrite;

    $wp_rewrite->set_permalink_structure('/%postname%/');

    $wp_rewrite->flush_rules();

	// Incresing Maximum Time Execution



	$wpspmax = "

	# WP Increse Maximum Execution Time

	<IfModule mod_php5.c>

		php_value max_execution_time 300

	</IfModule>";

	$htaccess = get_home_path().'.htaccess';

	$contents = @file_get_contents($htaccess);

	if(!strpos($htaccess,$wpspmax))

	file_put_contents($htaccess,$contents.$wpspmax);

	$wp_roles = new WP_Roles();

	$wp_roles->remove_role('subscriber');

    $wp_roles->remove_role('editor');

    $wp_roles->remove_role('contributor');

    $wp_roles->remove_role('author');

	$wp_roles->remove_role('client');



     $teacher_cap = array(

	    'add_student' => true,

	    'upload_mark' => true,

	    'attendance_entry' => true,

		'read' => true,

        'edit_posts'   => true,

        'upload_files' => true,

	);

	$student_cap = array( 'send_message' => true,'read' => true,'edit_posts'   => true,'upload_files' => false);

	$parent_cap  = array( 'send_message' => true,'read' => true,'edit_posts'   => true,'upload_files' => true );

	$admin_cap = array( 'action_all' => true );

	$teacher_role_new = $wp_roles->add_role('teacher', __('Teacher' ),$teacher_cap);

	$student_role_new = $wp_roles->add_role('student',__('Student' ),$student_cap);

	$parent_role_new  = $wp_roles->add_role('parent', 'Parent', $parent_cap);
	?>