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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db_wordpress' );

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
define( 'AUTH_KEY',         'xtV@v0,a/FumZQC&/6%#rl0?=qeIFUzH-no8xm2{,NJtqhC7REyA^=ZJfM)N$b#F' );
define( 'SECURE_AUTH_KEY',  'd&LXxI0}qT/^niX0T.OeVk:W $J%K!6RfW4pA#bzkM;)54Mzh.ps<J)8>3W@ _ie' );
define( 'LOGGED_IN_KEY',    's%YN.6@qRu&ykKk;V.T4i2i4<9w*E89>w$4%PX]?w22KEU*A(^tde*4].Jqx-F=!' );
define( 'NONCE_KEY',        '9|pLWz-0rKJN[z&iRMV.E,QcGEoQC#Q6A<XY>xxk!X7b`x|emh*mjgR,?^VA1V;-' );
define( 'AUTH_SALT',        ';i~!{NPc+<q:mk8Tyc</;cU+yB=l+ ^E1wX;bBSH8oI3j?6_Sn+T:<fRqyTq:8mz' );
define( 'SECURE_AUTH_SALT', 'x!-jGBMo(=.sk2FMsZd-<g~,+Ahmv|+w[xdP7TibD@GfvPHPB5;w_)%;}wT#hr1-' );
define( 'LOGGED_IN_SALT',   'c)@E?*KxBH HN}O>,kow%jvg*AXepcsPl-18^l{(|0Ny48|ZMKb>`J~qKM=CZY[t' );
define( 'NONCE_SALT',       '8??T;&9>NVA>OziRIVq& C6Oe`$s]#_Wxta[*Vb!J^5C=fG[7fdw)89=! 8g}94>' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
