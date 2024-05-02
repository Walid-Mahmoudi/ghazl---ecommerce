<?php

/**
 * 3rd Party Addon for Oxygen Builder.
 * 
 * @wordpress-plugin
 * Plugin Name: 	OxyUltimate Woo
 * Plugin URI: 		https://www.oxyultimate.com
 * Description: 	Oxygen Components for WooCommerce
 * Author: 			Paul Chinmoy
 * Author URI: 		https://www.paulchinmoy.com
 *
 * Tested up to:    6.2
 * Version: 		1.5.4
 * WC tested up to: 7.6
 *
 * License: 		GPLv2 or later
 * License URI: 	http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: 	oxyultimate-woo
 * Domain Path: 	languages  
 */

/**
 * Copyright (c) 2023 Paul Chinmoy. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 */

//* Prevent direct access to the plugin
if ( !defined( 'ABSPATH' ) ) {
	wp_die( __( "Sorry, you are not allowed to access this page directly.", 'oxyultimate-woo' ) );
}

require_once 'includes/class-ouwoo-loader.php';