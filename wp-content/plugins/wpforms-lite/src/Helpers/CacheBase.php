<?php

namespace WPForms\Helpers;

/**
 * Remote data cache handler.
 *
 * Usage example in `WPForms\Admin\Addons\AddonsCache` and `WPForms\Admin\Builder\TemplatesCache`.
 *
 * @since 1.6.8
 */
abstract class CacheBase {

	/**
	 * Indicates whether the cache was updated during the current run.
	 *
	 * @since 1.6.8
	 *
	 * @var bool
	 */
	protected static $updated = false;

	/**
	 * Settings.
	 *
	 * @since 1.6.8
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Cache key.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	private $cache_key;

	/**
	 * Cache dir.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	private $cache_dir;

	/**
	 * Cache file.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	private $cache_file;

	/**
	 * Determine if the class is allowed to load.
	 *
	 * @since 1.6.8
	 *
	 * @return bool
	 */
	abstract protected function allow_load();

	/**
	 * Initialize.
	 *
	 * @since 1.6.8
	 */
	public function init() {

		// Init settings before allow_load() as settings are used in get().
		$this->update_settings();

		$this->cache_key  = $this->settings['cache_file'];
		$this->cache_dir  = $this->get_cache_dir(); // See comment in the method.
		$this->cache_file = $this->cache_dir . $this->settings['cache_file'];

		if ( ! $this->allow_load() ) {
			return;
		}

		// Quit if settings wasn't provided.
		if (
			empty( $this->settings['remote_source'] ) ||
			empty( $this->settings['cache_file'] )
		) {
			return;
		}

		$this->hooks();
	}

	/**
	 * Base hooks.
	 *
	 * @since 1.6.8
	 */
	private function hooks() {

		add_action( 'shutdown', [ $this, 'cache_dir_complete' ] );

		if ( empty( $this->settings['update_action'] ) ) {
			return;
		}

		// Schedule recurring updates.
		add_action( 'admin_init', [ $this, 'schedule_update_cache' ] );
		add_action( $this->settings['update_action'], [ $this, 'update' ] );
	}

	/**
	 * Set up settings.
	 *
	 * @since 1.6.8
	 */
	private function update_settings() {

		$default_settings = [

			// Remote source URL.
			// For instance: 'https://wpforms.com/wp-content/addons.json'.
			'remote_source' => '',

			// Cache file.
			// Just file name. For instance: 'addons.json'.
			'cache_file'    => '',

			// Cache time to live in seconds.
			'cache_ttl'     => WEEK_IN_SECONDS,

			// Scheduled update action.
			// For instance: 'wpforms_admin_addons_cache_update'.
			'update_action' => '',
		];

		$this->settings = wp_parse_args( $this->setup(), $default_settings );
	}

	/**
	 * Provide settings.
	 *
	 * @since 1.6.8
	 *
	 * @return array Settings array.
	 */
	abstract protected function setup();

	/**
	 * Get cache directory path.
	 *
	 * @since 1.6.8
	 */
	protected function get_cache_dir() {

		static $cache_dir;

		if ( $cache_dir ) {
			/**
			 * Since wpforms_upload_dir() relies on hooks, and hooks can be added unpredictably,
			 * we need to cache the result of this method.
			 * Otherwise, it is the risk to save cache file to one dir and try to get from another.
			 */
			return $cache_dir;
		}

		$upload_dir  = wpforms_upload_dir();
		$upload_path = ! empty( $upload_dir['path'] )
			? trailingslashit( wp_normalize_path( $upload_dir['path'] ) )
			: trailingslashit( WP_CONTENT_DIR ) . 'uploads/wpforms/';

		$cache_dir = $upload_path . 'cache/';

		return $cache_dir;
	}

	/**
	 * Get data from cache or from API call.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	public function get() {

		if ( $this->is_invalid_cache() || $this->is_expired_cache() ) {
			$this->update();
		}

		return $this->get_from_cache();
	}

	/**
	 * Determine if the cache is expired.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	private function is_expired_cache() {

		return $this->cache_time() + $this->settings['cache_ttl'] < time();
	}

	/**
	 * Determine if the cache is expired.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	private function is_invalid_cache() {

		return empty( $this->get_from_cache() );
	}

	/**
	 * Get cache creation time.
	 *
	 * @since 1.8.2
	 *
	 * @return int
	 */
	private function cache_time() {

		return (int) Transient::get( $this->cache_key );
	}

	/**
	 * Determine if the cache file exists.
	 *
	 * @since 1.8.2
	 *
	 * @return bool
	 */
	private function exists() {

		return is_file( $this->cache_file ) && is_readable( $this->cache_file );
	}

	/**
	 * Get cache from cache file.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	private function get_from_cache() {

		if ( ! $this->exists() ) {
			return [];
		}

		return (array) json_decode( file_get_contents( $this->cache_file ), true );
	}

	/**
	 * Update cache.
	 *
	 * @since 1.8.2
	 *
	 * @param bool $force Force update.
	 *
	 * @return bool
	 */
	public function update( $force = false ) {

		if (
			! $force &&
			time() < $this->cache_time() + 15 * MINUTE_IN_SECONDS
		) {
			return false;
		}

		Transient::set( $this->cache_key, time(), $this->settings['cache_ttl'] );

		if ( ! wp_mkdir_p( $this->cache_dir ) ) {
			return false;
		}

		$data = $this->perform_remote_request();

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		if ( file_put_contents( $this->cache_file, wp_json_encode( $data ) ) === false ) {
			return false;
		}

		self::$updated = true;

		return true;
	}

	/**
	 * Get cached data.
	 *
	 * @since 1.6.8
	 * @deprecated 1.8.2
	 *
	 * @return array Cached data.
	 * @noinspection PhpUnused
	 */
	public function get_cached() {

		_deprecated_function( __METHOD__, '1.8.2 of the WPForms plugin', __CLASS__ . '::get()' );

		return $this->get();
	}

	/**
	 * Update cached data with actual data retrieved from the remote source.
	 *
	 * @since 1.6.8
	 * @deprecated 1.8.2
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function update_cache() {

		_deprecated_function( __METHOD__, '1.8.2 of the WPForms plugin' );

		$this->update();

		return $this->get();
	}

	/**
	 * Get data from API.
	 *
	 * @since 1.8.2
	 *
	 * @return array
	 */
	private function perform_remote_request() {

		$wpforms_key = wpforms()->is_pro() ? wpforms_get_license_key() : 'lite';

		$request = wp_remote_get(
			add_query_arg( 'tgm-updater-key', $wpforms_key, $this->settings['remote_source'] ),
			[
				'timeout'    => 10,
				'user-agent' => wpforms_get_default_user_agent(),
			]
		);

		if ( is_wp_error( $request ) ) {
			return [];
		}

		$json = wp_remote_retrieve_body( $request );

		if ( empty( $json ) ) {
			return [];
		}

		return $this->prepare_cache_data( json_decode( $json, true ) );
	}

	/**
	 * Schedule updates.
	 *
	 * @since 1.6.8
	 */
	public function schedule_update_cache() {

		// Just skip if not need to register scheduled action.
		if ( empty( $this->settings['update_action'] ) ) {
			return;
		}

		$tasks = wpforms()->get( 'tasks' );

		if ( $tasks->is_scheduled( $this->settings['update_action'] ) !== false ) {
			return;
		}

		$tasks->create( $this->settings['update_action'] )
			  ->recurring( time() + $this->settings['cache_ttl'], $this->settings['cache_ttl'] )
			  ->params()
			  ->register();
	}

	/**
	 * Complete the cache directory.
	 *
	 * @since 1.6.8
	 */
	public function cache_dir_complete() {

		if ( ! self::$updated ) {
			return;
		}

		wpforms_create_upload_dir_htaccess_file();
		wpforms_create_index_html_file( $this->cache_dir );
	}

	/**
	 * Prepare data to store in a local cache.
	 *
	 * @since 1.6.8
	 *
	 * @param array $data Raw data received by the remote request.
	 *
	 * @return array Prepared data for caching.
	 */
	protected function prepare_cache_data( $data ) {

		if ( empty( $data ) || ! is_array( $data ) ) {
			return [];
		}

		return $data;
	}
}
