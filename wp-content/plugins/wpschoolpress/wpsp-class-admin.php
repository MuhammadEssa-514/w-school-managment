<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;
/**
 * Admin Class
 *
 * Handles generic Admin functionality.
 *
 * @package WPSchoolPress
 * @since 2.0.0
 */
class Wpsp_Admin
{
	public
	function __construct()
	{
	}
	/*
	* Add menu for manage license code
	* @package WPSchoolPress
	* @since 2.0.0
	*/
function wpsp_admin_menu()
	{
		global $current_user;
		$current_user_role=$current_user->roles[0];
		$proversion1    =    wpsp_check_pro_version('wpsp_addon_version');
  		$prodisable1    =    !$proversion1['status'] ? 'notinstalled'    : 'installed';
  		 $propayment    =    wpsp_check_pro_version('pay_WooCommerce');
   		$propayment    =    !$propayment['status'] ? 'notinstalled'    : 'installed';

		$promessage2    =    wpsp_check_pro_version('wpsp_message_version');
    	$prodisablemessage2    =    !$promessage2['status'] ? 'notinstalled'    : 'installed';

		add_menu_page(__('WPSchoolPress', 'wpschoolpress') , __('WPSchoolPress', 'wpschoolpress') , 'manage_options', 'wpschoolpress', array(
			$this,
			'wpsp_admin_details'
		) , WPSP_PLUGIN_URL . 'img/favicon.png');
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-dashboard icon"></i>&nbsp; Dashboard', 'edit_posts', 'sch-dashboard', array(
			$this,
			'wpsp_callback_dashboard'
		));

		/* Social Posts */
		if( $current_user_role=='administrator' || $current_user_role=='teacher' || $current_user_role=='student' ||$current_user_role=='parent' )
		{
			add_submenu_page('WPSchoolPress', 'WPSchoolPress', '<i class="dashicons-before dashicons-admin-post"></i>&nbsp; Posts', 'edit_posts', 'sch-posts', array(
				$this,
				'wpsp_callback_posts'
			));

			add_submenu_page('WPSchoolPress', 'WPSchoolPress', '<i class="dashicons-before dashicons-admin-post"></i>&nbsp; Social Profile', 'edit_posts', 'sch-postsprofile', array(
				$this,
				'wpsp_callback_postsprofile'
			));
				if( $current_user_role=='administrator'){
					add_submenu_page('WPSchoolPress', 'WPSchoolPress', '<i class="dashicons-before dashicons-admin-post"></i>&nbsp; Reported Posts', 'edit_posts', 'sch-reported-posts', array(
							$this,
							'wpsp_callback_reported_post'
					));
				}
				//if( $current_user_role=='administrator'){
					add_submenu_page('WPSchoolPress', 'WPSchoolPress', '<i class="dashicons-before dashicons-admin-post"></i>&nbsp; Notification', 'edit_posts', 'sch-posts-notification', array(
							$this,
							'wpsp_callback_posts_notification'
					));
				//}
				add_submenu_page('WPSchoolPress', 'WPSchoolPress', '<i class="dashicons-before dashicons-admin-post"></i>&nbsp; Documents', 'edit_posts', 'sch-documents', array(
							$this,
							'wpsp_callback_documents'
				));
		}

			add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-inbox"></i>&nbsp; Messages', 'edit_posts', 'sch-messages', array(
				$this,
				'wpsp_callback_messages'
			));

		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-id icon"></i>&nbsp; Students', 'edit_posts', 'sch-student', array(
			$this,
			'wpsp_callback_students'
		));
		if($proversion1['status']){
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-welcome-write-blog icon"></i>&nbsp; Registration Request', 'edit_posts', 'sch-request', array(
			$this,
			'wpsp_callback_request'
		));
		}
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-groups icon"></i>&nbsp; Teachers', 'edit_posts', 'sch-teacher', array(
			$this,
			'wpsp_callback_teachers'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-businessman icon"></i>&nbsp; Parents', 'edit_posts', 'sch-parent', array(
			$this,
			'wpsp_callback_parents'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-welcome-widgets-menus icon"></i>&nbsp; Classes', 'edit_posts', 'sch-class', array(
			$this,
			'wpsp_callback_classes'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-clipboard icon"></i>&nbsp; Attendance', 'edit_posts', 'sch-attendance', array(
			$this,
			'wpsp_callback_attendance'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-book"></i>&nbsp; Subjects', 'edit_posts', 'sch-subject', array(
			$this,
			'wpsp_callback_subject'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-edit"></i>&nbsp; Exams', 'edit_posts', 'sch-exams', array(
			$this,
			'wpsp_callback_exams'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-check-square-o"></i>&nbsp; Marks', 'edit_posts', 'sch-marks', array(
			$this,
			'wpsp_callback_marks'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-calendar-alt"></i>&nbsp; Events', 'edit_posts', 'sch-events', array(
			$this,
			'wpsp_callback_events'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-clock-o"></i>&nbsp; Time Table', 'edit_posts', 'sch-timetable', array(
			$this,
			'wpsp_callback_timetable'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-upload"></i>&nbsp; Import History', 'edit_posts', 'sch-importhistory', array(
			$this,
			'wpsp_callback_importhistory'
		));
		if( $current_user_role=='administrator' || $current_user_role=='teacher')
		{
			add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-bullhorn"></i>&nbsp; Notify', 'edit_posts', 'sch-notify', array(
				$this,
				'wpsp_callback_notify'
			));
		}
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-road"></i>&nbsp; Transport', 'edit_posts', 'sch-transport', array(
			$this,
			'wpsp_callback_transport'
		));
		if($current_user_role=='administrator' || $current_user_role=='teacher'){
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-signal"></i>&nbsp; Teacher Attendance', 'edit_posts', 'sch-teacherattendance', array(
			$this,
			'wpsp_callback_teacherattendance'
		));
	}
	if($current_user_role=='administrator' || $current_user_role=='teacher'){
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-cog"></i>&nbsp; General Settings', 'edit_posts', 'sch-settings', array(
			$this,
			'wpsp_callback_settings'
		));
	}
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-check-square-o"></i>&nbsp; Subject Mark Fields', 'edit_posts', 'sch-settings&sc=subField', array(
			$this,
			'wpsp_callback_subfield'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-clock-o"></i>&nbsp; Working Hours', 'edit_posts', 'sch-settings&sc=WrkHours', array(
			$this,
			'wpsp_callback_wrkhours'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-key fa-fw"></i>&nbsp; Change Password', 'edit_posts', 'sch-changepassword', array(
			$this,
			'wpsp_callback_changepassword'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-strikethrough"></i>&nbsp; Leave Calendar', 'edit_posts', 'sch-leavecalendar', array(
			$this,
			'wpsp_callback_leavecalendar'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-key fa-fw"></i>&nbsp; Edit Profile', 'edit_posts', 'sch-editprofile', array(
			$this,
			'wpsp_callback_editprofile'
		));
		add_submenu_page('wpschoolpress', 'wpschoolpress',  '<i class="fa fa-key fa-fw"></i>&nbsp; History', 'edit_posts', 'sch-history', array(
			$this,
			'wpsp_callback_schhistory'
		));
		if($propayment == "installed"){
			add_submenu_page('wpschoolpress', 'wpschoolpress', '<i class="fa fa-strikethrough"></i>&nbsp; Payment', 'edit_posts', 'sch-payment', array(
			$this,
			'wpsp_callback_payment'
			));
		}
		add_submenu_page( 'wpschoolpress', 'wpschoolpress', '<i class="dashicons dashicons-welcome-add-page"></i>&nbsp; Addons',
    'manage_options', 'addons',
		array(
			$this,
			'wpsp_addons'
		));
	}
	/*
	* Call html of purchase code validation and contact
	* @package WPSchoolPress
	* @since 2.0.0
	*/
	function wpsp_admin_details()
	{
		require_once (WPSP_PLUGIN_PATH . 'lib/wpsp-admin-options.php');
	}
	function wpsp_addons()
	{
		require_once (WPSP_PLUGIN_PATH . 'lib/wpsp-admin-addons.php');
	}
	function wpsp_callback_dashboard()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-dashboard.php');
	}
	function wpsp_callback_payment()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-payment.php');
	}
	function wpsp_callback_editprofile()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-editprofile.php');
	}
	function wpsp_callback_students()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-student.php');
	}
	function wpsp_callback_request()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-registration-request.php');
	}
	function wpsp_callback_teachers()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-teacher.php');
	}
	function wpsp_callback_messages()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-messages.php');
	}
	function wpsp_callback_parents()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-parent.php');
	}
	function wpsp_callback_classes()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-class.php');
	}
	function wpsp_callback_attendance()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-attendance.php');
	}
	function wpsp_callback_subject()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-subject.php');
	}
	function wpsp_callback_marks()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-marks.php');
	}
	function wpsp_callback_exams()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-exams.php');
	}
	function wpsp_callback_events()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-events.php');
	}
	function wpsp_callback_timetable()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-timetable.php');
	}
	function wpsp_callback_importhistory()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-importhistory.php');
	}
	function wpsp_callback_notify()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-notify.php');
	}
	function wpsp_callback_transport()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-transport.php');
	}
	function wpsp_callback_teacherattendance()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-teacher-attendance.php');
	}
	function wpsp_callback_settings()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-settings.php');
	}
	function wpsp_callback_changepassword()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-changepassword.php');
	}
	function wpsp_callback_leavecalendar()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-leavecalendar.php');
	}
	function wpsp_callback_schhistory()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-history.php');
	}

	function wpsp_callback_posts()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-posts.php');
	}
    function wpsp_callback_postsprofile()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-postsprofile.php');
	}
	function wpsp_callback_reported_post()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-reportedposts.php');
	}
	function wpsp_callback_posts_notification()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-posts-notification.php');
	}
	function wpsp_callback_documents()
	{
		require_once (WPSP_PLUGIN_PATH . 'pages/wpsp-documents.php');
	}
	/*
	* Add required css and js for purchase code validation page
	* @package WPSchoolPress
	* @since 2.0.0
	*/
	function wpsp_add_admin_scripts($hook)
	{

		$prohistory    =    wpsp_check_pro_version('wpsp_mc_version');
    	$prodisablehistory    =    !$prohistory['status'] ? 'notinstalled'    : 'installed';

		wp_register_style('wpsp_wp_admin_font_awesome', WPSP_PLUGIN_URL . 'css/font-awesome.min.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_font_awesome');
		wp_register_style('wpsp_wp_admin_ionicons', WPSP_PLUGIN_URL . 'css/ionicons.min.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_ionicons');
		wp_register_style('wpsp_wp_admin_wpsp-grid', WPSP_PLUGIN_URL . 'css/wpsp-grid.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_wpsp-grid');
		// wp_register_style('wpsp_wp_admin_pnotify', WPSP_PLUGIN_URL . 'css/pnotify.min.css', false, '1.0.0');
		// wp_enqueue_style('wpsp_wp_admin_pnotify');
		if (is_user_logged_in())
		{
			wp_register_style('wpsp_wp_admin_dataTablesresp', WPSP_PLUGIN_URL . 'css/datepicker.min.css', false, '1.0.0');
			wp_enqueue_style('wpsp_wp_admin_dataTablesresp');
			wp_register_style('wpsp_wp_admin_dataTablesbootresp2', WPSP_PLUGIN_URL . 'plugins/datatables/responsive.bootstrap.min.css', false, '1.0.0');
			wp_enqueue_style('wpsp_wp_admin_dataTablesbootresp2');
		}
		if ($hook == 'wpschoolpress_page_sch-student')
		{
			// wp_register_style('wpsp_wp_admin_blueimp-gallery', WPSP_PLUGIN_URL . 'plugins/gallery/blueimp-gallery.min.css', false, '1.0.0');
			// wp_enqueue_style('wpsp_wp_admin_blueimp-gallery');
		}
		if ($hook == 'wpschoolpress_page_sch-dashboard')
		{
			wp_register_style('wpsp_wp_admin_fullcalendar', WPSP_PLUGIN_URL . 'plugins/fullcalendar/fullcalendar.min.css', false, '1.0.0');
			wp_enqueue_style('wpsp_wp_admin_fullcalendar');
		}
		if ($hook == 'wpschoolpress_page_sch-events')
		{
			wp_register_style('wpsp_wp_admin_fullcalendar', WPSP_PLUGIN_URL . 'plugins/fullcalendar/fullcalendar.min.css', false, '1.0.0');
			wp_enqueue_style('wpsp_wp_admin_fullcalendar');
			wp_register_style('wpsp_wp_admin_timepicker', WPSP_PLUGIN_URL . 'plugins/timepicker/bootstrap-timepicker.css', false, '1.0.0');
			wp_enqueue_style('wpsp_wp_admin_timepicker');
		}

		if ($hook == 'wpschoolpress_page_sch-settings' || $hook == 'wpschoolpress_page_sch-teacher')
		{
			wp_register_style('wpsp_wp_admin_boottimepicker', WPSP_PLUGIN_URL . 'plugins/timepicker/bootstrap-timepicker.css', false, '1.0.0');
			wp_enqueue_style('wpsp_wp_admin_boottimepicker');
		}
        wp_enqueue_script('wpsp_wp_admin_jquery2', WPSP_PLUGIN_URL . 'js/lib/bootstrap.min.js', array(
			'jquery'
		) , '1.0.0', true);

		wp_register_style('wpsp_wp_admin_wpsp-icons', WPSP_PLUGIN_URL . 'css/wpsp-icons.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_wpsp-icons');
		wp_register_style('wpsp_wp_admin_wpsp-widget', WPSP_PLUGIN_URL . 'css/wpsp-widget.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_wpsp-widget');
		wp_register_style('wpsp_wp_admin_wpsp-style', WPSP_PLUGIN_URL . 'css/wpsp-style.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_wpsp-style');
		wp_register_style('wpsp_wp_admin_wpsp-style-resposive', WPSP_PLUGIN_URL . 'css/wpsp-resposive.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_wpsp-style-resposive');
		wp_register_style('wpsp_wp_admin_wpsp-style-select', WPSP_PLUGIN_URL . 'css/bootstrap-select.min.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_wpsp-style-select');
		wp_register_style('wpsp_wp_admin_wpsp-style-multiselect', WPSP_PLUGIN_URL . 'css/bootstrap-multiselect.css', false, '1.0.0');
		wp_enqueue_style('wpsp_wp_admin_wpsp-style-multiselect');
		// wp_enqueue_script('wpsp_wp_admin_jquery302', WPSP_PLUGIN_URL . 'js/bootstrap-select.min.js', array(
		// 	'jquery'
		// ) , '1.0.0', true);
		wp_enqueue_script('wpsp_wp_admin_jquery303', WPSP_PLUGIN_URL . 'js/bootstrap-multiselect.js', array(
			'jquery'
		) , '1.0.0', true);
		wp_enqueue_script('wpsp_wp_admin_jquery1', WPSP_PLUGIN_URL . 'plugins/otherjs/wpsp.validate.min.js', array(
			'jquery'
		) , '1.0.0', true);
		wp_enqueue_script('wpsp_wp_admin_jquery90', WPSP_PLUGIN_URL . 'js/wpsp-wp-admin.js', array(
				'jquery'
		) , '1.0.0', true);

		// wp_enqueue_script('wpsp_wp_admin_jquery3', WPSP_PLUGIN_URL . 'plugins/fastclick/fastclick.min.js', array(
		// 	'jquery'
		// ) , '1.0.0', true);
		wp_enqueue_script('wpsp_wp_admin_jquery4', WPSP_PLUGIN_URL . 'js/lib/app.js', array(
			'jquery'
		) , '1.0.0', true);
		// wp_enqueue_script('wpsp_wp_admin_jquery5', WPSP_PLUGIN_URL . 'js/lib/pnotify.min.js', array(
		// 	'jquery'
		// ) , '1.0.0', true);
		wp_enqueue_script('wpsp_wp_admin_jquery6', WPSP_PLUGIN_URL . 'plugins/slimScroll/jquery.slimscroll.min.js', array(
			'jquery'
		) , '1.0.0', true);
		wp_enqueue_script('wpsp_wp_admin_jquery7', WPSP_PLUGIN_URL . 'js/bootstrap-datepicker.min.js', array(
			'jquery'
		) , '1.0.0', true);
		wp_enqueue_script('wpsp_wp_admin_jquery100', WPSP_PLUGIN_URL . 'js/wpsp-settingtab.js', array(
				'jquery'
			) , '1.0.0', true);
		if ($hook == 'toplevel_page_WPSchoolPress'){
			wp_enqueue_script('wpsp_wp_admin_jquery90', WPSP_PLUGIN_URL . 'js/wpsp-wp-admin.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if (is_user_logged_in())
		{
			wp_enqueue_script('wpsp_wp_admin_jquery8', WPSP_PLUGIN_URL . 'plugins/datatables/jquery.datatables.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery999', WPSP_PLUGIN_URL . 'js/wpsp-custome.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if($hook == 'wpschoolpress_page_sch-request')
				{
				wp_enqueue_script('wpsp_wp_admin_jquery1011', WPSP_PLUGIN_URL . 'js/wpsp-sch-request.js', array(
				'jquery'
			) , '1.0.0', true);
			}
		if ($hook == 'wpschoolpress_page_sch-dashboard')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery10', WPSP_PLUGIN_URL . 'plugins/fullcalendar/moment.min.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery11', WPSP_PLUGIN_URL . 'plugins/fullcalendar/fullcalendar.min.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery12', WPSP_PLUGIN_URL . 'plugins/timepicker/bootstrap-timepicker.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery13', WPSP_PLUGIN_URL . 'js/wpsp-dashboard.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-student' )
		{
			// wp_enqueue_script('wpsp_wp_admin_jquery15', WPSP_PLUGIN_URL . 'plugins/fileupload/jquery.iframe-transport.js', array(
			// 	'jquery'
			// ) , '1.0.0', true);
			// wp_enqueue_script('wpsp_wp_admin_jquery16', WPSP_PLUGIN_URL . 'plugins/gallery/jquery.blueimp-gallery.min.js', array(
			// 	'jquery'
			// ) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery171', WPSP_PLUGIN_URL . 'js/wpsp-student.js', array(
				'jquery'
			) , '1.0.0', true);

			if($prodisablehistory == 'installed'){
			 	wp_enqueue_script('wpsp_wp_admin_jquery007', WPSP_PLUGIN_URL . 'js/wpsp-custom-multicls.js', array(
			 	'jquery'
			 ) , '1.0.0', true);


			}
		}
		if ($hook == 'wpschoolpress_page_sch-editprofile' )
		{
			wp_enqueue_script('wpsp_wp_admin_jquery17', WPSP_PLUGIN_URL . 'js/wpsp-editprofile.js', array(
				'jquery'
			) ,'1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-teacher')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery18', WPSP_PLUGIN_URL . 'js/wpsp-teacher.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-parent')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery19', WPSP_PLUGIN_URL . 'js/wpsp-parent.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-class')
		{
			// wp_deregister_script('jquery-ui-datepicker');
			wp_enqueue_script('wpsp_wp_admin_jquery20', WPSP_PLUGIN_URL . 'js/wpsp-class.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-attendance')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery21', WPSP_PLUGIN_URL . 'js/wpsp-attendance.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-subject')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery22', WPSP_PLUGIN_URL . 'js/wpsp-subject.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-exams')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery23', WPSP_PLUGIN_URL . 'js/wpsp-exam.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-marks')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery24', WPSP_PLUGIN_URL . 'js/wpsp-mark.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-timetable')
		{
			wp_deregister_script( 'jquery-ui-core' );
			wp_enqueue_script('wpsp_wp_admin_jquery25', WPSP_PLUGIN_URL . 'plugins/otherui/wpsp_easyui.min.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery201', WPSP_PLUGIN_URL . 'js/lib/jquery.draggable.js', array(
			'jquery'
		) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery26', WPSP_PLUGIN_URL . 'js/wpsp_timetable.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-settings')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery27', WPSP_PLUGIN_URL . 'plugins/timepicker/bootstrap-timepicker.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery28', WPSP_PLUGIN_URL . 'js/wpsp-settings.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-importhistory')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery29', WPSP_PLUGIN_URL . 'js/wpsp-importhistory.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-transport')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery30', WPSP_PLUGIN_URL . 'js/wpsp-transport.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-teacherattendance')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery31', WPSP_PLUGIN_URL . 'js/wpsp-teacherattendance.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-notify')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery32', WPSP_PLUGIN_URL . 'js/wpsp-notify.js', array(
				'jquery'
			) , '1.0.0', true);

		}
		if ($hook == 'wpschoolpress_page_sch-events')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery33', WPSP_PLUGIN_URL . 'plugins/fullcalendar/moment.min.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery34', WPSP_PLUGIN_URL . 'plugins/fullcalendar/fullcalendar.min.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery35', WPSP_PLUGIN_URL . 'plugins/timepicker/bootstrap-timepicker.js', array(
				'jquery'
			) , '1.0.0', true);
			wp_enqueue_script('wpsp_wp_admin_jquery36', WPSP_PLUGIN_URL . 'js/wpsp-events.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-notify')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery37', WPSP_PLUGIN_URL . 'js/wpsp-leavecalendar.js', array(
				'jquery'
			) , '1.0.0', true);

		}
		if ($hook == 'wpschoolpress_page_sch-messages' || $hook == 'wpschoolpress_page_sch-parent')
		{
			 wp_register_style('wpsp_wp_admin_multiselect', WPSP_PLUGIN_URL . 'plugins/multiselect/jquery.multiselect.css', false, '1.0.0');
			 wp_enqueue_style('wpsp_wp_admin_multiselect');
			wp_enqueue_script('wpsp_wp_admin_jquery39', WPSP_PLUGIN_URL . 'js/wpsp-messages.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-changepassword')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery40', WPSP_PLUGIN_URL . 'js/wpsp-changepassword.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-payment')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery41', WPSP_PLUGIN_URL . 'plugins_url("plugins/multiselect/jquery.multiselect.js', array(
				'jquery'
			) , '1.0.0', true);
		}
		if ($hook == 'wpschoolpress_page_sch-leavecalendar')
		{
			wp_enqueue_script('wpsp_wp_admin_jquery42', WPSP_PLUGIN_URL . 'js/wpsp-leavecalendar.js', array(
				'jquery'
			) , '1.0.0', true);
		}
	}
	/*
	* Add pages in menu default
	* @package WPSchoolPress
	* @since 2.0.0
	*/
	function wpsp_add_adminbar()
	{
		global $wp_admin_bar;
		$wpsp_wpschooldashboard_url = site_url() . '/wp-admin/admin.php?page=sch-dashboard';
		$wpsp_wpschoolstudent_url = site_url() . '/wp-admin/admin.php?page=sch-student';
		$wpsp_wpschoolteacher_url = site_url() . '/wp-admin/admin.php?page=sch-teacher';
		$wpsp_wpschoolclass_url = site_url() . '/wp-admin/admin.php?page=sch-class';
		$wpsp_wpschoolparent_url = site_url() . '/wp-admin/admin.php?page=sch-parent';
		$wp_admin_bar->add_menu(array(
			'parent' => false,
			'id' => 'dashboard',
			'title' => _('WPSchoolPress Dashboard') ,
			'href' => $wpsp_wpschooldashboard_url
		));
		$wp_admin_bar->add_menu(array(
			'parent' => 'dashboard',
			'id' => 'teacher',
			'title' => _('Teacher') ,
			'href' => $wpsp_wpschoolteacher_url
		));
		$wp_admin_bar->add_menu(array(
			'parent' => 'dashboard',
			'id' => 'student',
			'title' => _('Student') ,
			'href' => $wpsp_wpschoolstudent_url
		));
		$wp_admin_bar->add_menu(array(
			'parent' => 'dashboard',
			'id' => 'class',
			'title' => _('Class') ,
			'href' => $wpsp_wpschoolclass_url
		));
		$wp_admin_bar->add_menu(array(
			'parent' => 'dashboard',
			'id' => 'parent',
			'title' => _('Parent') ,
			'href' => $wpsp_wpschoolparent_url
		));
	}
	function add_hooks()
	{
		function wpsp_custom_loginlogo()
		{
            global $current_user, $wpdb, $wpsp_settings_data,$post,$current_user_name;
            $url = plugin_dir_url( __FILE__ ).'img/wpschoolpresslogo.jpg';
            $imglogo  = isset( $wpsp_settings_data['sch_logo'] ) ? sanitize_text_field($wpsp_settings_data['sch_logo']) : $url;
            echo '<style type="text/css">
                        .login h1 a {background-image: url(' . $imglogo . ') !important; }
				  </style>';
		}
		// Add menu page for purchase code validation
		add_action('admin_menu', array(
			$this,
			'wpsp_admin_menu'
		));
		add_action('login_enqueue_scripts', 'wpsp_custom_loginlogo');
		// Add css and js
		add_action('admin_enqueue_scripts', array(
			$this,
			'wpsp_add_admin_scripts'
		));
		// Add pages into admin menu
		add_action('wp_before_admin_bar_render', array(
			$this,
			'wpsp_add_adminbar'
		));
	}
}