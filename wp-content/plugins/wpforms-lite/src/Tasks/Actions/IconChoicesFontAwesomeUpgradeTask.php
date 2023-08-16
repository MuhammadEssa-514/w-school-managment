<?php

namespace WPForms\Tasks\Actions;

use WPForms\Tasks\Task;

/**
 * Class Font Awesome Upgrade task.
 *
 * @since 1.8.3
 */
class IconChoicesFontAwesomeUpgradeTask extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 1.8.3
	 */
	const ACTION = 'wpforms_process_font_awesome_upgrade';

	/**
	 * Status option name.
	 *
	 * @since 1.8.3
	 */
	const STATUS = 'wpforms_process_font_awesome_upgrade_status';

	/**
	 * Start status.
	 *
	 * @since 1.8.3
	 */
	const START = 'start';

	/**
	 * In progress status.
	 *
	 * @since 1.8.3
	 */
	const IN_PROGRESS = 'in_progress';

	/**
	 * Completed status.
	 *
	 * @since 1.8.3
	 */
	const COMPLETED = 'completed';

	/**
	 * Constructor.
	 *
	 * @since 1.8.3
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Process the task.
	 *
	 * @since 1.8.3
	 */
	public function init() {

		// Bail out if migration is not started or completed.
		$status = get_option( self::STATUS );

		// This task is run in \WPForms\Pro\Migrations\Upgrade183::run(),
		// and started in \WPForms\Migrations\UpgradeBase::run_async().
		// Bail out if a task is not started or completed.
		if ( ! $status || $status === self::COMPLETED ) {
			return;
		}

		// Mark that migration is in progress.
		update_option( self::STATUS, self::IN_PROGRESS );

		$this->hooks();

		$tasks = wpforms()->get( 'tasks' );

		// Add new if none exists.
		if ( $tasks->is_scheduled( self::ACTION ) !== false ) {
			return;
		}

		$tasks->create( self::ACTION )->async()->register();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.8.3
	 */
	private function hooks() {

		add_action( self::ACTION, [ $this, 'upgrade' ] );
	}

	/**
	 * Upgrade.
	 *
	 * @since 1.8.3
	 */
	public function upgrade() {

		$icon_choices    = wpforms()->get( 'icon_choices' );
		$icons_data_file = $icon_choices->get_icons_data_file();

		if ( ! file_exists( $icons_data_file ) ) {
			$this->log( 'Library is not present, nothing to upgrade.' );
			update_option( self::STATUS, self::COMPLETED );

			return;
		}

		$upload_dir      = wpforms_upload_dir();
		$tmp_base_path   = $upload_dir['path'] . '/icon-choices-tmp';
		$cache_base_path = $upload_dir['path'] . '/icon-choices';

		require_once ABSPATH . 'wp-admin/includes/file.php';

		WP_Filesystem();

		global $wp_filesystem;

		$wp_filesystem->rmdir( $tmp_base_path, true );
		$icon_choices->run_install( $tmp_base_path );

		if ( is_dir( $tmp_base_path ) ) {
			// Remove old cache.
			$this->log( 'Removing existing instance of the library.' );
			$wp_filesystem->rmdir( $cache_base_path, true );

			// Rename temporary directory.
			$this->log( 'Renaming temporary directory.' );
			$wp_filesystem->move( $tmp_base_path, $cache_base_path );

			// Mark that migration is finished.
			$this->log( 'Finished upgrading.' );
			update_option( self::STATUS, self::COMPLETED );

			return;
		}

		$this->log( 'Something went wrong, library was not upgraded.' );
	}

	/**
	 * Add log record.
	 *
	 * @since 1.8.3
	 *
	 * @param string $message Error message.
	 */
	private function log( $message ) {

		wpforms_log( 'Migration', 'Font Awesome Upgrade: ' . $message, [ 'type' => 'log' ] );
	}
}
