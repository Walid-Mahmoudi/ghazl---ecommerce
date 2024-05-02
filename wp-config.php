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
define( 'DB_NAME', 'ghazl' );

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
define( 'AUTH_KEY',         '%bH/]$&wrZ~-xL!R?LaIt93|n^K%(&,[/`xVASumIm/oD{dd]SA!FB K@5jgI@py' );
define( 'SECURE_AUTH_KEY',  '+nbhk?Lmg0{j@dG6KjH4UiT*hBxS>]Nu}pzajl6M#pN5`>O[OiS5Z3JN>VRxcGj.' );
define( 'LOGGED_IN_KEY',    'l]S+|18t)`X2ZP{Fu.sLpMi,8H6=+d[;P8yW(#r-}.%Zc?eK{!</_WeT[z0Dn1AA' );
define( 'NONCE_KEY',        'yV[d8hD+iB^aozt9|E{0Z[2+(C%T/Z]nRk)}nTq> ,>RUtL/QQbHeD#bRi_;)`/J' );
define( 'AUTH_SALT',        'cr{71`2X!%NRFr?y6aDMpVZ+o%lb!ncT7S,t<BN5$A2*=)z5zge}R`1@Fp6ACo>&' );
define( 'SECURE_AUTH_SALT', 'i9V5_NDw!BZlK83@twP)avCu4Q.m**~xJ(;LLSSMNPFWr/rdeP50McO2vNQ<WEfX' );
define( 'LOGGED_IN_SALT',   ',8nom*_l}BU3cT]r%9x(RW4Vu2RZ[NG_u$nrg)1z5Um96GKYwX6Of0&9XPf=dn-c' );
define( 'NONCE_SALT',       'p5fU[j85s?iZY!RxE>?HnhcCej4:^(PPRV=>kpmYgr::W%a8A5hcWR*%k^@Iij`I' );

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
