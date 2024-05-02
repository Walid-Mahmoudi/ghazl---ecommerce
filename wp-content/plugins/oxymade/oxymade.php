<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://oxymade.com
 * @since             1.0.0
 * @package           Oxymade
 *
 * @wordpress-plugin 
 * Plugin Name:       OxyMade
 * Plugin URI:        https://oxymade.com
 * Description:       Tailwind CSS Based, Utility class powered CSS Framework & Tools for Oxygen Builder.
 * Version:           1.5.9
 * Author:            Anvesh
 * Author URI:        https://oxymade.com
 * Text Domain:       oxymade
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined("WPINC")) {
  die();
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define("OXYMADE_VERSION", "1.5.9");
define("OXYMADE_PATH", plugin_dir_path(__FILE__));
define("OXYMADE_ASSETS", plugin_dir_url(__FILE__) . "public");
define("OXYMADE_ADMIN_ASSETS", plugin_dir_url(__FILE__) . "admin");
define("OXYMADE_STORE_URL", "https://oxymade.com");
define("OXYMADE_ITEM_ID", 20);
define("OXYMADE_PLUGIN_FILE", __FILE__);
define("OXYMADE_PLUGIN_DIR", plugin_dir_path(OXYMADE_PLUGIN_FILE));
define("OXYMADE_PLUGIN_URL", plugin_dir_url(OXYMADE_PLUGIN_FILE));
define("OXYMADE_URI", plugin_dir_url(__FILE__) . "admin");

include dirname(__FILE__) . "/access/OxyMade_Plugin_Updater.php";
require_once "access/OxyMade_License.php";

// add_filter(
//   "doing_it_wrong_trigger_error",
//   function () {
//     return false;
//   },
//   10,
//   0
// );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-oxymade-activator.php
 */
function activate_oxymade()
{
  require_once plugin_dir_path(__FILE__) .
    "includes/class-oxymade-activator.php";
  Oxymade_Activator::activate();
}

function checkRequired($key, $hover_effects)
{
  // global $hover_effects;

  if (isset($hover_effects[$key]["required"])) {
    $required = implode(" ", $hover_effects[$key]["required"]);
  }

  if (isset($hover_effects[$key]["optional"])) {
    $optional = implode(" ", $hover_effects[$key]["optional"]);
  }

  if (isset($required)) {
    $req = "data-required='" . $required . "'";
  } else {
    $req = "";
  }

  if (isset($optional)) {
    $opt = "data-optional='" . $optional . "'";
  } else {
    $opt = "";
  }

  $current = "data-hover='" . $key . "'";

  echo $req . $opt . $current;
}


// Returns Next Post or Previous Post
function lit_prevs_next_headings() {
  $previous_post = get_previous_post();
  $prev_id = empty($previous_post) ? null : $previous_post->ID;
  global $wp;
  $current_url = home_url(add_query_arg($_GET,$wp->request));
  $current_ID = url_to_postid($current_url);
  return ( $prev_id == $current_ID ) ? 'Next Post' : 'Previous Post' ;  
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-oxymade-deactivator.php
 */
function deactivate_oxymade()
{
  require_once plugin_dir_path(__FILE__) .
    "includes/class-oxymade-deactivator.php";
  Oxymade_Deactivator::deactivate();
}

register_activation_hook(__FILE__, "activate_oxymade");
register_deactivation_hook(__FILE__, "deactivate_oxymade");

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . "includes/class-oxymade.php";

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_oxymade()
{
  $plugin = new Oxymade();
  $plugin->run();
}
run_oxymade();
