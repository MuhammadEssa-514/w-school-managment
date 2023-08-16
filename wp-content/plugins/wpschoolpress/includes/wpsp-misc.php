<?php


// Exit if accessed directly


if ( !defined( 'ABSPATH' ) ) exit;


/**


 * Initilize SchoolPress


 *


 * Handle to initilize Settings of SchoolPress


 *


 * @package WPSchoolPress


 * @since 2.0.0


 */


function wpsp_get_setting() {


	global $wpsp_settings_data, $wpdb;


	$wpsp_settings_table	=	$wpdb->prefix."wpsp_settings";


	$wpsp_settings_edit		=	$wpdb->get_results("SELECT * FROM $wpsp_settings_table" );


	foreach($wpsp_settings_edit as $sdat) {


		$wpsp_settings_data[$sdat->option_name]	=	$sdat->option_value;


	}


}


/*


* Send mail when new user register


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_send_user_register_mail( $userInfo = array(), $user_id ) {


	if( !empty( $user_id ) && $user_id > 0 ) {


		wp_new_user_notification( $user_id, '', 'user');


	}


}


/*


* Check current user is authorized or not


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_Authenticate() {


	global $current_user;


	if($current_user->roles[0]!='administrator' && $current_user->roles[0]!='teacher' ) {


		echo esc_html("Unauthorized Access!","wpschoolpress");


		exit;


	}


}

function wpsp_StudentAuthenticate() {

	global $current_user;
	
	if($current_user->roles[0]!='student' && $current_user->roles[0]!='parent' ) {
		echo esc_html("Unauthorized Access!","wpschoolpress");
		exit;
	}

}

function wpsp_TeacherAuthenticate() {

	global $current_user;
	
	if($current_user->roles[0]!='teacher' ) {
		echo esc_html("Unauthorized Access!","wpschoolpress");
		exit;
	}

}

function wpsp_AllAuthenticate() {

	global $current_user;
	
	if($current_user->roles[0]!='student' && $current_user->roles[0]!='parent' && $current_user->roles[0]!='administrator' && $current_user->roles[0]!='teacher' ) {
		echo esc_html("Unauthorized Access!","wpschoolpress");
		exit;
	}

}


/*


* Check current user has update access or not


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_UpdateAccess($role,$id){


	global $current_user;


	$current_user_role=$current_user->roles[0];


	if( $current_user_role=='administrator' || ( $current_user_role==$role && $current_user->ID==$id ) || $current_user_role=='teacher'  ) {


		return true;


	} else {


		return false;


	}


}


/*


* Get role of current user


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_CurrentUserRole(){


	global $current_user;


	return isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';


}


/*


* Get add as per given setting


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_ViewDate($date){





	global $wpdb, $wpsp_settings_data;


	$date_format	=	isset( $wpsp_settings_data['date_format'] ) ? $wpsp_settings_data['date_format'] : '';


	$dformat		=	empty( $date_format ) ? 'm/d/Y' : $date_format;


	return ( !empty( $date ) && $date!='0000-00-00' ) ? date( $dformat,strtotime($date) ) : $date;


}


/*


* Store date as per given setting


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_StoreDate($date) {


	return ( !empty ( $date ) && $date!='0000-00-00' ) ? date('Y-m-d',strtotime($date)) : $date;


}


/*


* Check for username exists or not


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_CheckUsername($username='',$return=false){


	$username	=	empty( $username ) ? sanitize_user($_POST['username'] ) : $username ;


	if ( username_exists( $username ) ) {


        if ($return)


            return true;


        else{


            echo esc_html("true","wpschoolpress");


            wp_die();


        }


    } else {


        if ($return)


            return false;


        else {


            echo esc_html("false","wpschoolpress");


            wp_die();


        }


    }


}


/*


* Check for emailID exists or not


* @package WPSchoolPress


* @since 2.0.0


*/


function wpsp_CheckEmail(){

	$email=sanitize_email($_POST['email']);

	echo email_exists( $email ) ? esc_html("true","wpschoolpress") : esc_html("false","wpschoolpress");

	wp_die();

}

/*

* Create dynamic email id if not specified

* @package WPSchoolPress

* @since 2.0.0

*/

function wpsp_EmailGen($username){
	return $username."@wpschoolpress.com";
}


/* This function is used for send mail */


function wpsp_send_mail( $to, $subject, $body, $attachment='' ) {

	global $wpsp_settings_data;

	$email			=	$wpsp_settings_data['sch_email'];

	$from			=	$wpsp_settings_data['sch_name'];

	$admin_email	=	get_option( 'admin_email' );

	$email		=	!empty( $email ) ? wp_unslash($email) : wp_unslash($admin_email);

	$from		=	!empty( $from ) ? $from : get_option( 'blogname'  );

	$headers	=	 array();

	if( !empty( $email ) && !empty( $from ) ) {

		$headers[]	=	"From: $from <$email>";

		$headers[] 	=	'Content-Type: text/html; charset=UTF-8';

	}

	if( wp_mail( $to, $subject, $body, $headers, $attachment )) return true;

	else return false;

}


/* This function is return country list */

function wpsp_county_list() {

	return array(

	'AF' => __( 'Afghanistan', 'wpschoolpress' ),


	'AX' => __( '&#197;land Islands', 'wpschoolpress' ),


	'AL' => __( 'Albania', 'wpschoolpress' ),


	'DZ' => __( 'Algeria', 'wpschoolpress' ),


	'AD' => __( 'Andorra', 'wpschoolpress' ),


	'AO' => __( 'Angola', 'wpschoolpress' ),


	'AI' => __( 'Anguilla', 'wpschoolpress' ),


	'AQ' => __( 'Antarctica', 'wpschoolpress' ),


	'AG' => __( 'Antigua and Barbuda', 'wpschoolpress' ),


	'AR' => __( 'Argentina', 'wpschoolpress' ),


	'AM' => __( 'Armenia', 'wpschoolpress' ),


	'AW' => __( 'Aruba', 'wpschoolpress' ),


	'AU' => __( 'Australia', 'wpschoolpress' ),


	'AT' => __( 'Austria', 'wpschoolpress' ),


	'AZ' => __( 'Azerbaijan', 'wpschoolpress' ),


	'BS' => __( 'Bahamas', 'wpschoolpress' ),


	'BH' => __( 'Bahrain', 'wpschoolpress' ),


	'BD' => __( 'Bangladesh', 'wpschoolpress' ),


	'BB' => __( 'Barbados', 'wpschoolpress' ),


	'BY' => __( 'Belarus', 'wpschoolpress' ),


	'BE' => __( 'Belgium', 'wpschoolpress' ),


	'PW' => __( 'Belau', 'wpschoolpress' ),


	'BZ' => __( 'Belize', 'wpschoolpress' ),


	'BJ' => __( 'Benin', 'wpschoolpress' ),


	'BM' => __( 'Bermuda', 'wpschoolpress' ),


	'BT' => __( 'Bhutan', 'wpschoolpress' ),


	'BO' => __( 'Bolivia', 'wpschoolpress' ),


	'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'wpschoolpress' ),


	'BA' => __( 'Bosnia and Herzegovina', 'wpschoolpress' ),


	'BW' => __( 'Botswana', 'wpschoolpress' ),


	'BV' => __( 'Bouvet Island', 'wpschoolpress' ),


	'BR' => __( 'Brazil', 'wpschoolpress' ),


	'IO' => __( 'British Indian Ocean Territory', 'wpschoolpress' ),


	'VG' => __( 'British Virgin Islands', 'wpschoolpress' ),


	'BN' => __( 'Brunei', 'wpschoolpress' ),


	'BG' => __( 'Bulgaria', 'wpschoolpress' ),


	'BF' => __( 'Burkina Faso', 'wpschoolpress' ),


	'BI' => __( 'Burundi', 'wpschoolpress' ),


	'KH' => __( 'Cambodia', 'wpschoolpress' ),


	'CM' => __( 'Cameroon', 'wpschoolpress' ),


	'CA' => __( 'Canada', 'wpschoolpress' ),


	'CV' => __( 'Cape Verde', 'wpschoolpress' ),


	'KY' => __( 'Cayman Islands', 'wpschoolpress' ),


	'CF' => __( 'Central African Republic', 'wpschoolpress' ),


	'TD' => __( 'Chad', 'wpschoolpress' ),


	'CL' => __( 'Chile', 'wpschoolpress' ),


	'CN' => __( 'China', 'wpschoolpress' ),


	'CX' => __( 'Christmas Island', 'wpschoolpress' ),


	'CC' => __( 'Cocos (Keeling) Islands', 'wpschoolpress' ),


	'CO' => __( 'Colombia', 'wpschoolpress' ),


	'KM' => __( 'Comoros', 'wpschoolpress' ),


	'CG' => __( 'Congo (Brazzaville)', 'wpschoolpress' ),


	'CD' => __( 'Congo (Kinshasa)', 'wpschoolpress' ),


	'CK' => __( 'Cook Islands', 'wpschoolpress' ),


	'CR' => __( 'Costa Rica', 'wpschoolpress' ),


	'HR' => __( 'Croatia', 'wpschoolpress' ),


	'CU' => __( 'Cuba', 'wpschoolpress' ),


	'CW' => __( 'Cura&Ccedil;ao', 'wpschoolpress' ),


	'CY' => __( 'Cyprus', 'wpschoolpress' ),


	'CZ' => __( 'Czech Republic', 'wpschoolpress' ),


	'DK' => __( 'Denmark', 'wpschoolpress' ),


	'DJ' => __( 'Djibouti', 'wpschoolpress' ),


	'DM' => __( 'Dominica', 'wpschoolpress' ),


	'DO' => __( 'Dominican Republic', 'wpschoolpress' ),


	'EC' => __( 'Ecuador', 'wpschoolpress' ),


	'EG' => __( 'Egypt', 'wpschoolpress' ),


	'SV' => __( 'El Salvador', 'wpschoolpress' ),


	'GQ' => __( 'Equatorial Guinea', 'wpschoolpress' ),


	'ER' => __( 'Eritrea', 'wpschoolpress' ),


	'EE' => __( 'Estonia', 'wpschoolpress' ),


	'ET' => __( 'Ethiopia', 'wpschoolpress' ),


	'FK' => __( 'Falkland Islands', 'wpschoolpress' ),


	'FO' => __( 'Faroe Islands', 'wpschoolpress' ),


	'FJ' => __( 'Fiji', 'wpschoolpress' ),


	'FI' => __( 'Finland', 'wpschoolpress' ),


	'FR' => __( 'France', 'wpschoolpress' ),


	'GF' => __( 'French Guiana', 'wpschoolpress' ),


	'PF' => __( 'French Polynesia', 'wpschoolpress' ),


	'TF' => __( 'French Southern Territories', 'wpschoolpress' ),


	'GA' => __( 'Gabon', 'wpschoolpress' ),


	'GM' => __( 'Gambia', 'wpschoolpress' ),


	'GE' => __( 'Georgia', 'wpschoolpress' ),


	'DE' => __( 'Germany', 'wpschoolpress' ),


	'GH' => __( 'Ghana', 'wpschoolpress' ),


	'GI' => __( 'Gibraltar', 'wpschoolpress' ),


	'GR' => __( 'Greece', 'wpschoolpress' ),


	'GL' => __( 'Greenland', 'wpschoolpress' ),


	'GD' => __( 'Grenada', 'wpschoolpress' ),


	'GP' => __( 'Guadeloupe', 'wpschoolpress' ),


	'GT' => __( 'Guatemala', 'wpschoolpress' ),


	'GG' => __( 'Guernsey', 'wpschoolpress' ),


	'GN' => __( 'Guinea', 'wpschoolpress' ),


	'GW' => __( 'Guinea-Bissau', 'wpschoolpress' ),


	'GY' => __( 'Guyana', 'wpschoolpress' ),


	'HT' => __( 'Haiti', 'wpschoolpress' ),


	'HM' => __( 'Heard Island and McDonald Islands', 'wpschoolpress' ),


	'HN' => __( 'Honduras', 'wpschoolpress' ),


	'HK' => __( 'Hong Kong', 'wpschoolpress' ),


	'HU' => __( 'Hungary', 'wpschoolpress' ),


	'IS' => __( 'Iceland', 'wpschoolpress' ),


	'IN' => __( 'India', 'wpschoolpress' ),


	'ID' => __( 'Indonesia', 'wpschoolpress' ),


	'IR' => __( 'Iran', 'wpschoolpress' ),


	'IQ' => __( 'Iraq', 'wpschoolpress' ),


	'IE' => __( 'Republic of Ireland', 'wpschoolpress' ),


	'IM' => __( 'Isle of Man', 'wpschoolpress' ),


	'IL' => __( 'Israel', 'wpschoolpress' ),


	'IT' => __( 'Italy', 'wpschoolpress' ),


	'CI' => __( 'Ivory Coast', 'wpschoolpress' ),


	'JM' => __( 'Jamaica', 'wpschoolpress' ),


	'JP' => __( 'Japan', 'wpschoolpress' ),


	'JE' => __( 'Jersey', 'wpschoolpress' ),


	'JO' => __( 'Jordan', 'wpschoolpress' ),


	'KZ' => __( 'Kazakhstan', 'wpschoolpress' ),


	'KE' => __( 'Kenya', 'wpschoolpress' ),


	'KI' => __( 'Kiribati', 'wpschoolpress' ),


	'KW' => __( 'Kuwait', 'wpschoolpress' ),


	'KG' => __( 'Kyrgyzstan', 'wpschoolpress' ),


	'LA' => __( 'Laos', 'wpschoolpress' ),


	'LV' => __( 'Latvia', 'wpschoolpress' ),


	'LB' => __( 'Lebanon', 'wpschoolpress' ),


	'LS' => __( 'Lesotho', 'wpschoolpress' ),


	'LR' => __( 'Liberia', 'wpschoolpress' ),


	'LY' => __( 'Libya', 'wpschoolpress' ),


	'LI' => __( 'Liechtenstein', 'wpschoolpress' ),


	'LT' => __( 'Lithuania', 'wpschoolpress' ),


	'LU' => __( 'Luxembourg', 'wpschoolpress' ),


	'MO' => __( 'Macao S.A.R., China', 'wpschoolpress' ),


	'MK' => __( 'Macedonia', 'wpschoolpress' ),


	'MG' => __( 'Madagascar', 'wpschoolpress' ),


	'MW' => __( 'Malawi', 'wpschoolpress' ),


	'MY' => __( 'Malaysia', 'wpschoolpress' ),


	'MV' => __( 'Maldives', 'wpschoolpress' ),


	'ML' => __( 'Mali', 'wpschoolpress' ),


	'MT' => __( 'Malta', 'wpschoolpress' ),


	'MH' => __( 'Marshall Islands', 'wpschoolpress' ),


	'MQ' => __( 'Martinique', 'wpschoolpress' ),


	'MR' => __( 'Mauritania', 'wpschoolpress' ),


	'MU' => __( 'Mauritius', 'wpschoolpress' ),


	'YT' => __( 'Mayotte', 'wpschoolpress' ),


	'MX' => __( 'Mexico', 'wpschoolpress' ),


	'FM' => __( 'Micronesia', 'wpschoolpress' ),


	'MD' => __( 'Moldova', 'wpschoolpress' ),


	'MC' => __( 'Monaco', 'wpschoolpress' ),


	'MN' => __( 'Mongolia', 'wpschoolpress' ),


	'ME' => __( 'Montenegro', 'wpschoolpress' ),


	'MS' => __( 'Montserrat', 'wpschoolpress' ),


	'MA' => __( 'Morocco', 'wpschoolpress' ),


	'MZ' => __( 'Mozambique', 'wpschoolpress' ),


	'MM' => __( 'Myanmar', 'wpschoolpress' ),


	'NA' => __( 'Namibia', 'wpschoolpress' ),


	'NR' => __( 'Nauru', 'wpschoolpress' ),


	'NP' => __( 'Nepal', 'wpschoolpress' ),


	'NL' => __( 'Netherlands', 'wpschoolpress' ),


	'AN' => __( 'Netherlands Antilles', 'wpschoolpress' ),


	'NC' => __( 'New Caledonia', 'wpschoolpress' ),


	'NZ' => __( 'New Zealand', 'wpschoolpress' ),


	'NI' => __( 'Nicaragua', 'wpschoolpress' ),


	'NE' => __( 'Niger', 'wpschoolpress' ),


	'NG' => __( 'Nigeria', 'wpschoolpress' ),


	'NU' => __( 'Niue', 'wpschoolpress' ),


	'NF' => __( 'Norfolk Island', 'wpschoolpress' ),


	'KP' => __( 'North Korea', 'wpschoolpress' ),


	'NO' => __( 'Norway', 'wpschoolpress' ),


	'OM' => __( 'Oman', 'wpschoolpress' ),


	'PK' => __( 'Pakistan', 'wpschoolpress' ),


	'PS' => __( 'Palestinian Territory', 'wpschoolpress' ),


	'PA' => __( 'Panama', 'wpschoolpress' ),


	'PG' => __( 'Papua New Guinea', 'wpschoolpress' ),


	'PY' => __( 'Paraguay', 'wpschoolpress' ),


	'PE' => __( 'Peru', 'wpschoolpress' ),


	'PH' => __( 'Philippines', 'wpschoolpress' ),


	'PN' => __( 'Pitcairn', 'wpschoolpress' ),


	'PL' => __( 'Poland', 'wpschoolpress' ),


	'PT' => __( 'Portugal', 'wpschoolpress' ),


	'QA' => __( 'Qatar', 'wpschoolpress' ),


	'RE' => __( 'Reunion', 'wpschoolpress' ),


	'RO' => __( 'Romania', 'wpschoolpress' ),


	'RU' => __( 'Russia', 'wpschoolpress' ),


	'RW' => __( 'Rwanda', 'wpschoolpress' ),


	'BL' => __( 'Saint Barth&eacute;lemy', 'wpschoolpress' ),


	'SH' => __( 'Saint Helena', 'wpschoolpress' ),


	'KN' => __( 'Saint Kitts and Nevis', 'wpschoolpress' ),


	'LC' => __( 'Saint Lucia', 'wpschoolpress' ),


	'MF' => __( 'Saint Martin (French part)', 'wpschoolpress' ),


	'SX' => __( 'Saint Martin (Dutch part)', 'wpschoolpress' ),


	'PM' => __( 'Saint Pierre and Miquelon', 'wpschoolpress' ),


	'VC' => __( 'Saint Vincent and the Grenadines', 'wpschoolpress' ),


	'SM' => __( 'San Marino', 'wpschoolpress' ),


	'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'wpschoolpress' ),


	'SA' => __( 'Saudi Arabia', 'wpschoolpress' ),


	'SN' => __( 'Senegal', 'wpschoolpress' ),


	'RS' => __( 'Serbia', 'wpschoolpress' ),


	'SC' => __( 'Seychelles', 'wpschoolpress' ),


	'SL' => __( 'Sierra Leone', 'wpschoolpress' ),


	'SG' => __( 'Singapore', 'wpschoolpress' ),


	'SK' => __( 'Slovakia', 'wpschoolpress' ),


	'SI' => __( 'Slovenia', 'wpschoolpress' ),


	'SB' => __( 'Solomon Islands', 'wpschoolpress' ),


	'SO' => __( 'Somalia', 'wpschoolpress' ),


	'ZA' => __( 'South Africa', 'wpschoolpress' ),


	'GS' => __( 'South Georgia/Sandwich Islands', 'wpschoolpress' ),


	'KR' => __( 'South Korea', 'wpschoolpress' ),


	'SS' => __( 'South Sudan', 'wpschoolpress' ),


	'ES' => __( 'Spain', 'wpschoolpress' ),


	'LK' => __( 'Sri Lanka', 'wpschoolpress' ),


	'SD' => __( 'Sudan', 'wpschoolpress' ),


	'SR' => __( 'Suriname', 'wpschoolpress' ),


	'SJ' => __( 'Svalbard and Jan Mayen', 'wpschoolpress' ),


	'SZ' => __( 'Swaziland', 'wpschoolpress' ),


	'SE' => __( 'Sweden', 'wpschoolpress' ),


	'CH' => __( 'Switzerland', 'wpschoolpress' ),


	'SY' => __( 'Syria', 'wpschoolpress' ),


	'TW' => __( 'Taiwan', 'wpschoolpress' ),


	'TJ' => __( 'Tajikistan', 'wpschoolpress' ),


	'TZ' => __( 'Tanzania', 'wpschoolpress' ),


	'TH' => __( 'Thailand', 'wpschoolpress' ),


	'TL' => __( 'Timor-Leste', 'wpschoolpress' ),


	'TG' => __( 'Togo', 'wpschoolpress' ),


	'TK' => __( 'Tokelau', 'wpschoolpress' ),


	'TO' => __( 'Tonga', 'wpschoolpress' ),


	'TT' => __( 'Trinidad and Tobago', 'wpschoolpress' ),


	'TN' => __( 'Tunisia', 'wpschoolpress' ),


	'TR' => __( 'Turkey', 'wpschoolpress' ),


	'TM' => __( 'Turkmenistan', 'wpschoolpress' ),


	'TC' => __( 'Turks and Caicos Islands', 'wpschoolpress' ),


	'TV' => __( 'Tuvalu', 'wpschoolpress' ),


	'UG' => __( 'Uganda', 'wpschoolpress' ),


	'UA' => __( 'Ukraine', 'wpschoolpress' ),


	'AE' => __( 'United Arab Emirates', 'wpschoolpress' ),


	'GB' => __( 'United Kingdom (UK)', 'wpschoolpress' ),


	'US' => __( 'United States (US)', 'wpschoolpress' ),


	'UY' => __( 'Uruguay', 'wpschoolpress' ),


	'UZ' => __( 'Uzbekistan', 'wpschoolpress' ),


	'VU' => __( 'Vanuatu', 'wpschoolpress' ),


	'VA' => __( 'Vatican', 'wpschoolpress' ),


	'VE' => __( 'Venezuela', 'wpschoolpress' ),


	'VN' => __( 'Vietnam', 'wpschoolpress' ),


	'WF' => __( 'Wallis and Futuna', 'wpschoolpress' ),


	'EH' => __( 'Western Sahara', 'wpschoolpress' ),


	'WS' => __( 'Western Samoa', 'wpschoolpress' ),


	'YE' => __( 'Yemen', 'wpschoolpress' ),


	'ZM' => __( 'Zambia', 'wpschoolpress' ),


	'ZW' => __( 'Zimbabwe', 'wpschoolpress' )


	);


}


/* This Function is Check Pro Version */


function wpsp_check_pro_version( $class='wpsp_pro_version' ) {





	$response = array();


	$response['status']	 =true;


	if( !empty( $class ) && !class_exists( $class ) ) {


		$response['status']		=	false;


		$response['class']		=	'upgrade-to-wpsp-version';


		$response['message']	=	'Please Purchase This Add-on';


		return $response;


	}


	return $response;


}


?>