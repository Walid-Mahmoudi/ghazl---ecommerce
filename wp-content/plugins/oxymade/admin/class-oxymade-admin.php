<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://oxymade.com
 * @since      1.0.0
 *
 * @package    Oxymade
 * @subpackage Oxymade/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Oxymade
 * @subpackage Oxymade/admin
 * @author     Anvesh <support@oxymade.com>
 */
class Oxymade_Admin
{
  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {
    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Oxymade_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Oxymade_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */
    $current_screen = get_current_screen();

    if (strpos($current_screen->base, "oxymade") === false) {
      return;
    } else {
      wp_enqueue_style(
        $this->plugin_name,
        plugin_dir_url(__FILE__) . "css/oxymade-admin.css",
        [],
        $this->version,
        "all"
      );
    }
  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts()
  {
    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Oxymade_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Oxymade_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_script(
      $this->plugin_name,
      plugin_dir_url(__FILE__) . "js/oxymade-admin.js",
      ["jquery"],
      $this->version,
      false
    );
    wp_enqueue_script(
      "oxymade-alpine-js",
      "https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js",
      [],
      "defer",
      false
    );

    wp_enqueue_script(
      "oxymonster-spectrum-color",
      plugin_dir_url(__FILE__) . "js/spectrum.min.js",
      ["jquery"],
      $this->version,
      false
    );
    wp_enqueue_script(
      "oxymonster-spectrum-color-extras",
      plugin_dir_url(__FILE__) . "js/spectrumextras.js",
      ["jquery"],
      $this->version,
      false
    );
  }

  static function init()
  {
    add_action("wp_ajax_oxy_builder_active", [__CLASS__, "active"]);
  }

  /**
   * Register Overview Page (Top-Level Page)
   */
  public function register_dashboard_pages()
  {
    add_submenu_page(
      "ct_dashboard_page",
      __("OxyMade Framework Dashboard", $this->plugin_name),
      __("OxyMade", $this->plugin_name),
      "read",
      "oxymade",
      [$this, "include_dashboard_partials"],
      9999
    );
  }

  /**
   * Include Overview Partial
   */
  public function include_dashboard_partials()
  {
    include_once OXYMADE_PATH . "admin/partials/oxymade-admin-dashboard.php";
    include_once OXYMADE_PATH . "admin/partials/oxymade-framework-data.php";
  }
}
