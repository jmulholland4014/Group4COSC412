<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sportsruDB0y3ns');

/** MySQL database username */
define( 'DB_USER', 'sportsruDB0y3ns');

/** MySQL database password */
define( 'DB_PASSWORD', 'ILLRAFioXf');

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', 'dNGCjfYUQIEB3{,^$yrnjbXUQIEB73{<,^yuqnfbXTQIEA7{<.$yuYUQNFB73}>');
define( 'SECURE_AUTH_KEY', 'NF840}|spldZVSKGC85:[|!-wsoldZVROGC84:[|!@wsokdZVRKGC84:[|!IFB7}');
define( 'LOGGED_IN_KEY', '2].+tpmiaWTPLD962]#*+xtlieaWPLHD92;]#~+xtpheaWOZVRNGC84:[|!-wsokd');
define( 'NONCE_KEY', '^yuqjfbXQMIEA3{<^$yuqjfbXPMIEA3{<*$yqmifbTPLIE]#~+tlhaWSLHD95;]');
define( 'AUTH_SALT', 'B40>|!zvrogcYUNJF740},!@zrnkgYUQNFB73}>,@zvryuqmibXPLIE62{<*+yum');
define( 'SECURE_AUTH_SALT', '5:#_~-splhZWSOGC951[|_~wsplhZVSOGC851[|!NJB730>,^@zrnjgYUQMFB73}');
define( 'LOGGED_IN_SALT', ';<.*xtqmiaWTPHDA62]#.*xtpmeaWTdVSOKG851:|!~-solhdVROKG841:|!@-sok');
define( 'NONCE_SALT', 'rjfbUQMIA73{,$yuqjfbXTMIE;].*+xpmieWTPLH962;]_*tpliaHD<.*+uqmibX');

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
