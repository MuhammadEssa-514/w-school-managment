<?php

/**
 * This class provides the list of system status values
 *
 */

/**
 * Show list of system critical data
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 */
if (!class_exists('OceanWP_Theme_Panel_System_Status')) {
	class OceanWP_Theme_Panel_System_Status
	{
		/**
		 * OceanWP_Theme_Panel_System_Status constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct()
		{
			add_action('wp_ajax_oceanwp_cp_system_status', array($this, 'ajax_handler'));
		}

		/**
		 * Handles AJAX requests.
		 *
		 * @since 1.3.0
		 */
		public function ajax_handler()
		{
			OceanWP_Theme_Panel::check_ajax_access( $_REQUEST['nonce'], 'oceanwp_theme_panel' );

			$type = $_POST['type'];

			if (!$type) {
				wp_send_json_error(esc_html__('Type param is missing.', 'ocean-extra'));
			}

			$this->$type();

			wp_send_json_error(
				sprintf(esc_html__('Type param (%s) is not valid.', 'ocean-extra'), $type)
			);
		}


		/**
		 * Checks whether HTTP requests are blocked.
		 *
		 * @see test_http_requests() in Health Check plugin.
		 * @since 1.3.0
		 */
		private function http_requests()
		{
			$blocked = false;
			$hosts   = [];

			if (defined('WP_HTTP_BLOCK_EXTERNAL')) {
				$blocked = true;
			}

			if (defined('WP_ACCESSIBLE_HOSTS')) {
				$hosts = explode(',', WP_ACCESSIBLE_HOSTS);
			}

			if ($blocked && 0 === sizeof($hosts)) {
				wp_send_json_error(esc_html__('HTTP requests have been blocked by the WP_HTTP_BLOCK_EXTERNAL constant, with no allowed hosts.', 'ocean-extra'));
			}

			if ($blocked && 0 < sizeof($hosts)) {
				wp_send_json_error(
					sprintf(
						esc_html__('HTTP requests have been blocked by the WP_HTTP_BLOCK_EXTERNAL constant, with some hosts whitelisted: %s.', 'ocean-extra'),
						implode(',', $hosts)
					)
				);
			}

			if (!$blocked) {
				wp_send_json_success();
			}
		}

		/**
		 * Checks whether artbees.net is accessible.
		 *
		 * @since 1.3.0
		 */
		private function oceanwp_server()
		{
			$response = wp_remote_get('https://oceanwp.org', array(
				'timeout' => 10,
			));

			if (is_wp_error($response)) {
				wp_send_json_error($response->get_error_message());
			}

			wp_send_json_success();
		}

		/**
		 * Create an array of system status
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 */
		public static function compile_system_status()
		{
			global $wpdb;

			$sysinfo    = array();
			$upload_dir = wp_upload_dir();

			$sysinfo['home_url'] = esc_url(home_url('/'));
			$sysinfo['site_url'] = esc_url(site_url('/'));

			$sysinfo['wp_content_url']      = WP_CONTENT_URL;
			$sysinfo['wp_upload_dir']       = $upload_dir['basedir'];
			$sysinfo['wp_upload_url']       = $upload_dir['baseurl'];
			$sysinfo['wp_ver']              = get_bloginfo('version');
			$sysinfo['wp_multisite']        = is_multisite();
			$sysinfo['front_page_display']  = get_option('show_on_front');
			if ('page' === $sysinfo['front_page_display']) {
				$front_page_id = get_option('page_on_front');
				$blog_page_id  = get_option('page_for_posts');

				$sysinfo['front_page'] = 0 !== $front_page_id ? get_the_title($front_page_id) . ' (#' . $front_page_id . ')' : 'Unset';
				$sysinfo['posts_page'] = 0 !== $blog_page_id ? get_the_title($blog_page_id) . ' (#' . $blog_page_id . ')' : 'Unset';
			}

			$sysinfo['wp_mem_limit']['raw']  = OceanWP_Theme_Panel_Helpers::let_to_num(WP_MEMORY_LIMIT);
			$sysinfo['wp_mem_limit']['size'] = size_format($sysinfo['wp_mem_limit']['raw']);

			$sysinfo['wp_debug'] = 'false';
			if (defined('WP_DEBUG') && WP_DEBUG) {
				$sysinfo['wp_debug'] = 'true';
			}

			$sysinfo['wp_writable']         = get_home_path();
			$sysinfo['wp_content_writable'] = WP_CONTENT_DIR;
			$sysinfo['wp_uploads_writable'] = $sysinfo['wp_upload_dir'];
			$sysinfo['wp_plugins_writable'] = WP_PLUGIN_DIR;
			$sysinfo['wp_themes_writable']  = get_theme_root();

			$sysinfo['server_info'] = esc_html($_SERVER['SERVER_SOFTWARE']);
			$sysinfo['localhost']   = OceanWP_Theme_Panel_Helpers::make_bool_string(OceanWP_Theme_Panel_Helpers::is_localhost());
			$sysinfo['php_ver']     = function_exists('phpversion') ? esc_html(phpversion()) : 'phpversion() function does not exist.';

			if (function_exists('ini_get')) {
				$sysinfo['php_mem_limit']['raw']      = OceanWP_Theme_Panel_Helpers::let_to_num(ini_get('memory_limit'));
				$sysinfo['php_mem_limit']['size']     = size_format($sysinfo['php_mem_limit']['raw']);
				$sysinfo['php_post_max_size']         = size_format(OceanWP_Theme_Panel_Helpers::let_to_num(ini_get('post_max_size')));
				$sysinfo['php_time_limit']            = ini_get('max_execution_time');
				$sysinfo['php_upload_max_filesize']   = ini_get('upload_max_filesize');
				$sysinfo['php_max_input_var']         = ini_get('max_input_vars');
				$sysinfo['php_display_errors']        = OceanWP_Theme_Panel_Helpers::make_bool_string(ini_get('display_errors'));
			}

			$sysinfo['mysql_ver']         = $wpdb->db_version();
			$sysinfo['max_upload_size']   = size_format(OceanWP_Theme_Panel_Helpers::let_to_num(ini_get('upload_max_filesize')));
			if (is_multisite()) {
				$sysinfo['network_upload_limit'] = get_site_option('fileupload_maxk') . ' KB';
			}

			$sysinfo['fsockopen_curl'] = 'false';
			if (function_exists('fsockopen') || function_exists('curl_init')) {
				$sysinfo['fsockopen_curl'] = 'true';
			}

			$sysinfo['soap_client'] = 'false';
			if (class_exists('SoapClient')) {
				$sysinfo['soap_client'] = 'true';
			}

			$sysinfo['dom_document'] = 'false';
			if (class_exists('DOMDocument')) {
				$sysinfo['dom_document'] = 'true';
			}

			$sysinfo['gzip'] = 'false';
			if (is_callable('gzopen')) {
				$sysinfo['gzip'] = 'true';
			}

			$sysinfo['mbstring'] = 'false';

			if (extension_loaded('mbstring') && function_exists('mb_eregi') && function_exists('mb_ereg_match')) {
				$sysinfo['mbstring'] = 'true';
			}

			$sysinfo['simplexml'] = 'false';

			if (class_exists('SimpleXMLElement') && function_exists('simplexml_load_string')) {
				$sysinfo['simplexml'] = 'true';
			}

			$sysinfo['phpxml'] = 'false';

			if (function_exists('xml_parse')) {
				$sysinfo['phpxml'] = 'true';
			}

			$active_plugins = (array) get_option('active_plugins', array());

			if (is_multisite()) {
				$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
			}

			$sysinfo['plugins'] = array();

			foreach ($active_plugins as $plugin) {
				$plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
				$plugin_name = esc_html($plugin_data['Name']);

				$sysinfo['plugins'][$plugin_name] = $plugin_data;
			}

			$active_theme = wp_get_theme();

			$sysinfo['theme']['name']       = $active_theme->Name;
			$sysinfo['theme']['version']    = $active_theme->Version;
			$sysinfo['theme']['author_uri'] = $active_theme->{'Author URI'};
			$sysinfo['theme']['is_child']   = OceanWP_Theme_Panel_Helpers::make_bool_string(is_child_theme());

			if (is_child_theme()) {
				$parent_theme = wp_get_theme($active_theme->Template);

				$sysinfo['theme']['parent_name']       = $parent_theme->Name;
				$sysinfo['theme']['parent_version']    = $parent_theme->Version;
				$sysinfo['theme']['parent_author_uri'] = $parent_theme->{'Author URI'};
			}

			return $sysinfo;
		}
	}
	new OceanWP_Theme_Panel_System_Status();
}
