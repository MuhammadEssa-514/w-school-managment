<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ecommerse' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'O,mqxW=Hw1S)&jHjeRgXy5eeFY+b=(A-|A1DMZfO6.>Z6AcUm(ZexADclMK#TZ&x' );
define( 'SECURE_AUTH_KEY',  '5*i$ZLX4Z/l(sXip*oBD^+r>{m;O-~KdA_{LUg|DDr~/9*l!!q}(~Og^l9C(5*p|' );
define( 'LOGGED_IN_KEY',    'L5Atg{Nv0SY|<)iG=5`<OFA6s;o=-2P?w|?&F}G:TAb]TPI5),p9@b)%]sksD0_6' );
define( 'NONCE_KEY',        '#oi)wB^RN>Fd4TB9-i1FR>eGSQ Z&_fF)E7o~B7+e(b^EgYlZ/IJgt=2?.o>pOlJ' );
define( 'AUTH_SALT',        'uF%K>.,zcHeD1s##Yve@O4v{kaDftL0+DN4cJ]Kc=)Y8b-G#VmC~&C%!]ri!X;c9' );
define( 'SECURE_AUTH_SALT', '+zp`|a{PnTk2z-%}szho}<>%peF-UzMKFE4p&dz,5;M?GCDz(QY{*9{[UD9Vz$<(' );
define( 'LOGGED_IN_SALT',   'QUMp]<-N3`9Uy DDVu%Q`ED)Q9d`[V&;k3rqpDj_Ga0HBa>R!4JnmD._.8Kj:%^[' );
define( 'NONCE_SALT',       'H=``#hS_K9QhXa[*7mPM>sln^*vQ,OdJC8=(Wz<T`C< Io~O~x{@NTfM-g}2)vT@' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
