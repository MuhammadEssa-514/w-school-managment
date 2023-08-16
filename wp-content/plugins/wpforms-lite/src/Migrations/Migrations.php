<?php

namespace WPForms\Migrations;

use WPForms\Tasks\Meta;
use WPForms\Db\Payments\Payment;
use WPForms\Db\Payments\Meta as PaymentMeta;

/**
 * Class Migrations handles Lite plugin upgrade routines.
 *
 * @since 1.7.5
 */
class Migrations extends Base {

	/**
	 * WP option name to store the migration version.
	 *
	 * @since 1.5.9
	 */
	const MIGRATED_OPTION_NAME = 'wpforms_versions_lite';

	/**
	 * Name of the core plugin used in log messages.
	 *
	 * @since 1.7.5
	 */
	const PLUGIN_NAME = 'WPForms';

	/**
	 * Upgrade classes.
	 *
	 * @since 1.7.5
	 */
	const UPGRADE_CLASSES = [
		'Upgrade159',
		'Upgrade1672',
		'Upgrade168',
		'Upgrade175',
		'Upgrade1751',
		'Upgrade177',
		'Upgrade182',
		'Upgrade183',
	];

	/**
	 * Custom table handler classes.
	 *
	 * @since 1.7.6
	 * @since 1.8.2 Registered payment tables.
	 */
	const CUSTOM_TABLE_HANDLER_CLASSES = [
		Meta::class,
		Payment::class,
		PaymentMeta::class,
	];
}
