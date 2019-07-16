<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'infinint_manual_meds');

/** MySQL database username */
define('DB_USER', 'infinint_sagana');

/** MySQL database password */
define('DB_PASSWORD', 'dun480can');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ljdkpx4xf2nsvlpwutms2cxlohxv54fmzmtj3p1tx3kwfrzlbrgerxsg9sbwckkt');
define('SECURE_AUTH_KEY',  'pfn3v20nmu1foi1eapygtx1uvx7xyw6nylocwmpavo4uualcawqlvngujzmr2xe1');
define('LOGGED_IN_KEY',    'zxyhkpc2yoqcuot6tnkjlgytos3eplgysupkpexlcoj0e2wfmie7hwcseukw3o6x');
define('NONCE_KEY',        '03jk3nswp7hqukoyeorqbc2zcamtj0njrgyndbmpmxxmacuvjh9pgqoohdgfhsec');
define('AUTH_SALT',        'laveajg07jgwzuxjiaajtju0jydy3nwuexlbaqlnhp2mx7voujzhcdxdntjoybkc');
define('SECURE_AUTH_SALT', 'lknbothtfnihlqphkxgun2oifesfvmq4fta6ppm8ozwefcwl4m9vpesfvbkodkwv');
define('LOGGED_IN_SALT',   'vfumxlelia12nwgxvlfkmaxyqr36xsfzbmp0eajzpwkbkldt5jfrsbyqg36tt81o');
define('NONCE_SALT',       'd9foh0msfdlbtlw23b56tiahesnixageo4qh0hhtsw0bar1nmvsqpmu7bzvag3zt');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
