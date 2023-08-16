<?php
/*
Plugin Name: 	WPSchoolPress
Plugin URI: 	http://wpschoolpress.com
Description:    WPSchoolpress is a school management system plugin that makes school activities transparent to parents. For more information please visit our website.
Version: 		2.2.4
Author: 		WPSchoolPress Team
Author URI: 	wpschoolpress.com
Text Domain:	wpschoolpress
Domain Path:    languages
@package WPSchoolPress
*/
// Exit if accessed directly
if (!defined('ABSPATH')) exit;
/**
 * Basic plugin definitions
 *
 * @package WPSchoolPress
 * @since 2.2.4
 */
if (!defined('WPSP_PLUGIN_URL'))
{
	define('WPSP_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('WPSP_PLUGIN_PATH'))
{
	define('WPSP_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WPSP_PLUGIN_VERSION'))
{
	define('WPSP_PLUGIN_VERSION', '2.2.4'); //Plugin version number
}
define('WPSP_PERMISSION_MSG', 'You don\'t have enough permission to access this page');
// Call the  required files when plugin activate
register_activation_hook(__FILE__, 'wpsp_activation');
function wpsp_activation()
{
	include_once (WPSP_PLUGIN_PATH . 'lib/wpsp-activation.php');
}
register_deactivation_hook( __FILE__, 'wpsp_deactivation');
function wpsp_deactivation()
{
	include_once (WPSP_PLUGIN_PATH . 'lib/wpsp-deactivation.php');
}
// add action to load plugin
add_action('plugins_loaded', 'wpsp_plugins_loaded');

function wpsp_plugins_loaded()
{
	$wpsp_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
	load_plugin_textdomain('wpschoolpress', false, $wpsp_lang_dir);
	// initialize settings of plugin Open required files for initialization
	require_once (WPSP_PLUGIN_PATH . 'lib/wpsp-ajaxworks.php');
	require_once (WPSP_PLUGIN_PATH . 'lib/wpsp-ajaxworks-student.php');
	require_once (WPSP_PLUGIN_PATH . 'lib/wpsp-ajaxworks-teacher.php');
	require_once (WPSP_PLUGIN_PATH . 'wpsp-layout.php');
	require_once (WPSP_PLUGIN_PATH . 'includes/wpsp-misc.php');
	wpsp_get_setting();

	global $wpsp_settings_data;
	global $wpsp_admin, $wpsp_public, $paytmClass, $paypalClass;
	// admin class handles most of functionalities of plugin
	include_once (WPSP_PLUGIN_PATH . 'wpsp-class-admin.php');
	$wpsp_admin = new Wpsp_Admin();
	$wpsp_admin->add_hooks();
	// public class handles most of functionalities of plugin
	include_once (WPSP_PLUGIN_PATH . 'wpsp-class-public.php');
	$wpsp_public = new Wpsp_Public();
	$wpsp_public->add_hooks();
}



add_action('admin_init', 'ajax_actions');
function ajax_actions()
{
	add_action('wp_ajax_listdashboardschedule', 'wpsp_listdashboardschedule');
	add_action('wp_ajax_StudentProfile', 'wpsp_StudentProfile');
	add_action('wp_ajax_AddStudent', 'wpsp_AddStudent');
	add_action('wp_ajax_UpdateStudent', 'wpsp_UpdateStudent');
	add_action('wp_ajax_StudentPublicProfile', 'wpsp_StudentPublicProfile');
	add_action('wp_ajax_ParentPublicProfile', 'wpsp_ParentPublicProfile');
	add_action('wp_ajax_TeacherPublicProfile', 'wpsp_TeacherPublicProfile');
	add_action('wp_ajax_bulkDelete', 'wpsp_BulkDelete');
	add_action('wp_ajax_undoImport', 'wpsp_UndoImport');
	add_action('wp_ajax_AddTeacher', 'wpsp_AddTeacher');
	add_action('wp_ajax_AddParent', 'wpsp_AddParent');
	add_action('wp_ajax_AddClass', 'wpsp_AddClass');
	add_action('wp_ajax_UpdateClass', 'wpsp_UpdateClass');
	add_action('wp_ajax_GetClass', 'wpsp_GetClass');
	add_action('wp_ajax_DeleteClass', 'wpsp_DeleteClass');
	add_action('wp_ajax_Updateregisterdeactive', 'wpsp_Updateregisterdeactive');
	add_action('wp_ajax_Updateregisteractive', 'wpsp_Updateregisteractive');
	add_action('wp_ajax_bulkaproverequest', 'wpsp_bulkaproverequest');
	add_action('wp_ajax_bulkdisaproverequest', 'wpsp_bulkdisaproverequest');
	add_action('wp_ajax_AddExam', 'wpsp_AddExam');
	add_action('wp_ajax_UpdateExam', 'wpsp_UpdateExam');
	add_action('wp_ajax_ExamInfo', 'wpsp_ExamInfo');
	add_action('wp_ajax_DeleteExam', 'wpsp_DeleteExam');
	add_action('wp_ajax_getStudentsList', 'wpsp_getStudentsList');
	add_action('wp_ajax_AttendanceEntry', 'wpsp_AttendanceEntry');
	add_action('wp_ajax_deleteAttendance', 'wpsp_DeleteAttendance');
	add_action('wp_ajax_getStudentsAttendanceList', 'wpsp_getStudentsAttendanceList');
	add_action('wp_ajax_getAbsentees', 'wpsp_GetAbsentees');
	add_action('wp_ajax_getAbsentDates', 'wpsp_GetAbsentDates');
	add_action('wp_ajax_getAttReport', 'wpsp_GetAttReport');
	add_action('wp_ajax_AddSubject', 'wpsp_AddSubject');
	add_action('wp_ajax_SubjectInfo', 'wpsp_SubjectInfo');
	add_action('wp_ajax_UpdateSubject', 'wpsp_UpdateSubject');
	add_action('wp_ajax_DeleteSubject', 'wpsp_DeleteSubject');
	add_action('wp_ajax_subjectList', 'wpsp_SubjectList');
	add_action('wp_ajax_save_timetable', 'wpsp_SaveTimetable');
	add_action('wp_ajax_deletsloat', 'wpsp_DeleteTimetablesloat');
	add_action('wp_ajax_deletTimetable', 'wpsp_DeleteTimetable');
	add_action('wp_ajax_addMark', 'wpsp_AddMark');
	add_action('wp_ajax_getMarksubject', 'wpsp_getMarksubject');

	add_action('wp_ajax_GenSetting', 'wpsp_GenSetting');
	add_action('wp_ajax_GenSettingsms', 'wpsp_GenSettingsms');
	add_action('wp_ajax_GenSettingsocial', 'wpsp_GenSettingsocial');
	add_action('wp_ajax_GenSettinglicensing', 'wpsp_GenSettinglicensing');
	add_action('wp_ajax_addSubField', 'wpsp_AddSubField');
	add_action('wp_ajax_updateSubField', 'wpsp_UpdateSubField');
	add_action('wp_ajax_deleteSubField', 'wpsp_DeleteSubField');
	add_action('wp_ajax_manageGrade', 'wpsp_ManageGrade');
	add_action('wp_ajax_addEvent', 'wpsp_AddEvent');
	add_action('wp_ajax_updateEvent', 'wpsp_UpdateEvent');
	add_action('wp_ajax_deleteEvent', 'wpsp_DeleteEvent');
	add_action('wp_ajax_listEvent', 'wpsp_ListEvent');
	add_action('wp_ajax_deleteAllLeaves', 'wpsp_DeleteLeave');
	add_action('wp_ajax_addLeaveDay', 'wpsp_AddLeaveDay');
	add_action('wp_ajax_getLeaveDays', 'wpsp_GetLeaveDays');
	add_action('wp_ajax_getClassYear', 'wpsp_GetClassYear');
	add_action('wp_ajax_addTransport', 'wpsp_AddTransport');
	add_action('wp_ajax_updateTransport', 'wpsp_UpdateTransport');
	add_action('wp_ajax_viewTransport', 'wpsp_ViewTransport');
	add_action('wp_ajax_deleteTransport', 'wpsp_DeleteTransport');
	add_action('wp_ajax_sendMessage', 'wpsp_SendMessage');
	add_action('wp_ajax_sendSubMessage', 'wpsp_sendSubMessage');
	add_action('wp_ajax_viewMessage', 'wpsp_ViewMessage');
	add_action('wp_ajax_deleteMessage', 'wpsp_DeleteMessage');
	add_action('wp_ajax_photoUpload', 'wpsp_UploadPhoto');
	add_action('wp_ajax_deletePhoto', 'wpsp_DeletePhoto');
	add_action('wp_ajax_DeleteStudent', 'wpsp_DeleteStudent');
	add_action('wp_ajax_DeleteTeacher', 'wpsp_DeleteTeacher');
	// Teacher modules
	add_action('wp_ajax_getTeachersList', 'wpsp_getTeachersList');
	add_action('wp_ajax_TeacherAttendanceEntry', 'wpsp_TeacherAttendanceEntry');
	add_action('wp_ajax_TeacherAttendanceDelete', 'wpsp_TeacherAttendanceDelete');
	add_action('wp_ajax_TeacherAttendanceView', 'wpsp_TeacherAttendanceView');
	add_action('wp_ajax_UpdateTeacher', 'wpsp_UpdateTeacher');
	// Notification modules
	add_action('wp_ajax_deleteNotify', 'wpsp_deleteNotify');
	add_action('wp_ajax_getNotify', 'wpsp_getNotifyInfo');
	//add notify
    add_action('wp_ajax_addNotify', 'wpsp_addNotify');
	// Change Password
	add_action('wp_ajax_changepassword', 'wpsp_changepassword');
	// Import Dummy data
	add_action('wp_ajax_ImportContents', 'wpsp_Import_Dummy_contents');
}



// Get error content and update
function wpsp_save_error()
{
	update_option('plugin_error', ob_get_contents());
}
add_action('activated_plugin', 'wpsp_save_error');
//Show Link Plugin Page
function wpsp_add_plugin_links($links)
{
	$plugin_links = array(
		'<a href="'.esc_url('admin.php?page=WPSchoolPress').'"><strong style="color: #11967A; display: inline;">' . __('Settings', 'WPSchoolPress-123') . '</strong></a>'
	);
	return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__) , 'wpsp_add_plugin_links', 20);
// Change login page logo url
function wp_wp_login_url() {  return home_url(); }
add_filter( 'login_headerurl', 'wp_wp_login_url' );

    //
    function wpsp_std_role(){
    $role = get_role( 'student' );
    $role->add_cap( 'edit_posts', true );
    }
    add_action( 'init', 'wpsp_std_role', 11 );
?>