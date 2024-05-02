<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://oxymade.com
 * @since      1.0.0
 *
 * @package    Oxymade
 * @subpackage Oxymade/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Oxymade
 * @subpackage Oxymade/includes
 * @author     Anvesh <support@oxymade.com>
 */
class Oxymade
{
  const PREFIX = "oxymade_";
  const TITLE = "OxyMade";

  static function init()
  {
   OxyMadeLicense::init(
    self::PREFIX,
    self::TITLE,
    OXYMADE_STORE_URL,
    OXYMADE_ITEM_ID
   );
   add_action(
    "activate_" . plugin_basename(__FILE__),
    [__CLASS__, "activate"],
    10,
    2
   );

   if (OxyMadeLicense::is_activated_license() === true) {
    // add_action("wp_enqueue_scripts", [__CLASS__, "scripts"], 11);
   }

   add_action("admin_menu", [__CLASS__, "admin_menu"], 11);
   add_action("admin_init", [__CLASS__, "plugin_updater"], 0);

   // =============================================== //
   //Disabling & syncing Gutenberg colors & OxyMade colors
   // =============================================== //
   $oxyberg_color_palette_status = get_option("oxymade_gutenberg_color_palette_status");
   $oxymade_blogzine = get_option("oxymade_blogzine");
   
   if ((isset($oxymade_blogzine) && $oxymade_blogzine == "Disable") || (isset($oxyberg_color_palette_status) && $oxyberg_color_palette_status == "Disable")) {
    
     } else {
   // Disable Gutenberg Custom Colors
   add_theme_support("disable-custom-colors");

   // Disable Gutenberg Custom Gradients
   add_theme_support("disable-custom-gradients");

   $oxyberg_color_palette = get_option("oxymade_gutenberg_colors");

   // Editor Color Palette
   add_theme_support("editor-color-palette", $oxyberg_color_palette);
   
   function oxyberg_frontend_css () { ?>
     <style>
       .has-primary-color-color{color:var(--primary-color)}.has-primary-color-background-color{background-color:var(--primary-color)}.has-dark-color-color{color:var(--dark-color)}.has-dark-color-background-color{background-color:var(--dark-color)}.has-paragraph-color-color{color:var(--paragraph-color)}.has-paragraph-color-background-color{background-color:var(--paragraph-color)}.has-border-color-color{color:var(--border-color)}.has-border-color-background-color{background-color:var(--border-color)}.has-placeholder-color-color{color:var(--placeholder-color)}.has-placeholder-color-background-color{background-color:var(--placeholder-color)}.has-background-color-color{color:var(--background-color)}.has-background-color-background-color{background-color:var(--background-color)}.has-secondary-color-color{color:var(--secondary-color)}.has-secondary-color-background-color{background-color:var(--secondary-color)}.has-light-color-color{color:var(--light-color)}.has-light-color-background-color{background-color:var(--light-color)}.has-paragraph-alt-color-color{color:var(--paragraph-alt-color)}.has-paragraph-alt-color-background-color{background-color:var(--paragraph-alt-color)}.has-border-alt-color-color{color:var(--border-alt-color)}.has-border-alt-color-background-color{background-color:var(--border-alt-color)}.has-placeholder-alt-color-color{color:var(--placeholder-alt-color)}.has-placeholder-alt-color-background-color{background-color:var(--placeholder-alt-color)}.has-background-alt-color-color{color:var(--background-alt-color)}.has-background-alt-color-background-color{background-color:var(--background-alt-color)}.has-tertiary-color-color{color:var(--tertiary-color)}.has-tertiary-color-background-color{background-color:var(--tertiary-color)}.has-black-color-color{color:var(--black-color)}.has-black-color-background-color{background-color:var(--black-color)}.has-white-color-color{color:var(--white-color)}.has-white-color-background-color{background-color:var(--white-color)}.has-success-color-color{color:var(--success-color)}.has-success-color-background-color{background-color:var(--success-color)}.has-warning-color-color{color:var(--warning-color)}.has-warning-color-background-color{background-color:var(--warning-color)}.has-error-color-color{color:var(--error-color)}.has-error-color-background-color{background-color:var(--error-color)}.has-success-light-color-color{color:var(--success-light-color)}.has-success-light-color-background-color{background-color:var(--success-light-color)}.has-warning-light-color-color{color:var(--warning-light-color)}.has-warning-light-color-background-color{background-color:var(--warning-light-color)}.has-error-light-color-color{color:var(--error-light-color)}.has-error-light-color-background-color{background-color:var(--error-light-color)}.has-extra-color-1-color{color:var(--extra-color-1)}.has-extra-color-1-background-color{background-color:var(--extra-color-1)}.has-extra-color-2-color{color:var(--extra-color-2)}.has-extra-color-2-background-color{background-color:var(--extra-color-2)}.has-extra-color-3-color{color:var(--extra-color-3)}.has-extra-color-3-background-color{background-color:var(--extra-color-3)}.has-extra-color-4-color{color:var(--extra-color-4)}.has-extra-color-4-background-color{background-color:var(--extra-color-4)}.has-primary-hover-color-color{color:var(--primary-hover-color)}.has-primary-hover-color-background-color{background-color:var(--primary-hover-color)}.has-secondary-hover-color-color{color:var(--secondary-hover-color)}.has-secondary-hover-color-background-color{background-color:var(--secondary-hover-color)}.has-primary-alt-color-color{color:var(--primary-alt-color)}.has-primary-alt-color-background-color{background-color:var(--primary-alt-color)}.has-secondary-alt-color-color{color:var(--secondary-alt-color)}.has-secondary-alt-color-background-color{background-color:var(--secondary-alt-color)}.has-primary-alt-hover-color-color{color:var(--primary-alt-hover-color)}.has-primary-alt-hover-color-background-color{background-color:var(--primary-alt-hover-color)}.has-secondary-alt-hover-color-color{color:var(--secondary-alt-hover-color)}.has-secondary-alt-hover-color-background-color{background-color:var(--secondary-alt-hover-color)}.has-transparent-color-color{color:var(--transparent-color)}.has-transparent-color-background-color{background-color:var(--transparent-color)}.has-dark-rgb-vals-color{color:var(--dark-rgb-vals)}.has-dark-rgb-vals-background-color{background-color:var(--dark-rgb-vals)}.has-paragraph-rgb-vals-color{color:var(--paragraph-rgb-vals)}.has-paragraph-rgb-vals-background-color{background-color:var(--paragraph-rgb-vals)}.has-tertiary-rgb-vals-color{color:var(--tertiary-rgb-vals)}.has-tertiary-rgb-vals-background-color{background-color:var(--tertiary-rgb-vals)}.has-black-rgb-vals-color{color:var(--black-rgb-vals)}.has-black-rgb-vals-background-color{background-color:var(--black-rgb-vals)}.has-success-rgb-vals-color{color:var(--success-rgb-vals)}.has-success-rgb-vals-background-color{background-color:var(--success-rgb-vals)}.has-warning-rgb-vals-color{color:var(--warning-rgb-vals)}.has-warning-rgb-vals-background-color{background-color:var(--warning-rgb-vals)}.has-error-rgb-vals-color{color:var(--error-rgb-vals)}.has-error-rgb-vals-background-color{background-color:var(--error-rgb-vals)}.has-extra-color-1-rgb-vals-color{color:var(--extra-color-1-rgb-vals)}.has-extra-color-1-rgb-vals-background-color{background-color:var(--extra-color-1-rgb-vals)}.has-extra-color-2-rgb-vals-color{color:var(--extra-color-2-rgb-vals)}.has-extra-color-2-rgb-vals-background-color{background-color:var(--extra-color-2-rgb-vals)}.has-extra-color-3-rgb-vals-color{color:var(--extra-color-3-rgb-vals)}.has-extra-color-3-rgb-vals-background-color{background-color:var(--extra-color-3-rgb-vals)}.has-extra-color-4-rgb-vals-color{color:var(--extra-color-4-rgb-vals)}.has-extra-color-4-rgb-vals-background-color{background-color:var(--extra-color-4-rgb-vals)}
     </style>
   <?php }
   add_action( 'wp_head', 'oxyberg_frontend_css' );
  }
   
  }

  public function oxymade_color_palette()
  {
   $oxymade_colors = get_option("oxymade_colors");
  }

  public function oxymadeberg()
  {
   global $pagenow;
   if (is_admin() && ($pagenow == "post.php" || $pagenow == "post-new.php")) {
    $post_id = isset($_REQUEST["post"]) ? $_REQUEST["post"] : 0;
    $editor_enabled = true;
    if (isset($_REQUEST["post_type"])) {
      if (!post_type_supports($_REQUEST["post_type"], "editor")) {
       $editor_enabled = false;
      }
    } elseif ($post_id > 0) {
      $post = get_post($post_id);
      if ($post != null && !post_type_supports($post->post_type, "editor")) {
       $editor_enabled = false;
      }
    }
    if ($editor_enabled) {
      add_action("admin_enqueue_scripts", [$this, "oxymadeberg_styles"]);
      $user_level = get_option("oxymade_user_level");
      
      if($user_level == "pro"){
      $oxymade_blogzine_status = get_option("oxymade_blogzine");
      if ($oxymade_blogzine_status && $oxymade_blogzine_status != "Enable") {
      } else {
       add_filter("admin_body_class", [
        $this,
        "oxymade_blogzine_body_class",
       ]);

       add_action("admin_enqueue_scripts", [
        $this,
        "oxymade_gb_blogzine_styles",
       ]);
      }
    }
    }
   }
  }

  function oxymade_blogzine_body_class($classes)
  {
   //get current page
   global $pagenow;

   //check if the current page is post.php and if the post parameteris set
   if ($pagenow === "post.php" && isset($_GET["post"])) {
    //get the post type via the post id from the URL
    $postType = get_post_type($_GET["post"]);
    //append the new class
    $classes .= " blogzine mx-auto";
    // $classes .= "1single-" . $postType;
   }
   //next check if this is a new post
   elseif ($pagenow === "post-new.php") {
    //check if the post_type parameter is set
    if (isset($_GET["post_type"])) {
      //in this case you can get the post_type directly from the URL
      $classes .= " blogzine mx-auto";
      // $classes .= "2single-" . urldecode($_GET["post_type"]);
    } else {
      //if post_type is not set, a 'post' is being created
      $classes .= " blogzine mx-auto";
      // $classes .= "3single-post";
    }
   }
   return $classes;
  }

  function oxymade_color_system_gutenberg()
  {
   $oxymade_color_system = get_option("oxymade_custom_css");
   echo "<style id='oxymade-color-system-gb'>";
   echo $oxymade_color_system;
   echo "</style>";
  }

  function oxymadeberg_styles()
  {    
   // echo "<style id='oxymade-base-font-size-gb'>";
   // echo "html { font-size: 62.5%; } body { font-size: 1.6rem; } .blogzine .editor-styles-wrapper { margin-left: auto; margin-right: auto; } input[type=checkbox], input[type=radio] {height: 1.6rem; width: 1.6rem; margin: 0.4rem 0.4rem 0 0; min-width: 1.6rem; }";
   // echo "</style>";
   // 
   $oxymade_html_font_size = get_option("oxymade_html_font_size");
   $oxymade_body_font_size = get_option("oxymade_body_font_size");
   $oxymade_body_font_size_calc = get_option("oxymade_body_font_size_calc");
   if (empty($oxymade_html_font_size) || !isset($oxymade_html_font_size)) {
     $oxymade_html_font_size = 62.5;
   }
   if (empty($oxymade_body_font_size) || !isset($oxymade_body_font_size)) {
     $oxymade_body_font_size = 1.6;
   }
   $oxymade_base_font_size_custom_css =
     "<style id='oxyberg-wrapper-styles'>html { font-size: " .
     $oxymade_html_font_size .
     "%; } body { font-size: " .
     $oxymade_body_font_size .
     "rem; } .blogzine .editor-styles-wrapper { margin-left: auto; margin-right: auto; } input[type=checkbox], input[type=radio] {height: 1.6rem; width: 1.6rem; margin: 0.4rem 0.4rem 0 0; min-width: 1.6rem; }</style>";
     echo $oxymade_base_font_size_custom_css;
  }

  function oxymade_gb_blogzine_styles()
  {
   $oxymade_gb_blogzine_css = get_option("oxymade_gb_blogzine_css");
   echo "<style id='oxymade-blogzine-css-gb'>";
   echo $oxymade_gb_blogzine_css;
   echo "</style>";
  }

  static function admin_menu()
  {
   // add_submenu_page(
   //   "oxymade",
   //   "OxyMade",
   //   "OxyMade",
   //   "manage_options",
   //   self::PREFIX . "menu",
   //   [__CLASS__, "menu_item"]
   // );

   // add_submenu_page(
   //   "oxymade",
   //   "License",
   //   "License",
   //   "manage_options",
   //   "oxymade&tab=license",
   //   [__CLASS__, "menu_item"]
   // );
  }

  static function menu_item()
  {
   $tab = isset($_GET["tab"]) ? sanitize_text_field($_GET["tab"]) : false; ?>
   <div class="wrap">
    
   <?php if (OxyMadeLicense::is_activated_license() === true) {
    if ($tab === "license") {
      OxyMadeLicense::license_page();
    }
   } else {
    OxyMadeLicense::license_page();
   } ?>
   </div>
   <?php
  }

  static function plugin_updater()
  {
   // require( 'includes/plugin_updater.php' );
   // retrieve our license key from the DB.
   $license_key = trim(get_option(self::PREFIX . "license_key"));

   // setup the updater.
   $edd_updater = new OxyMade_Plugin_Updater(
    OXYMADE_STORE_URL,
    OXYMADE_PLUGIN_FILE,
    [
      "version" => OXYMADE_VERSION, // current version number
      "license" => $license_key, // license key (used get_option above to retrieve from DB)
      "item_id" => OXYMADE_ITEM_ID, // ID of the product
      "item_name" => self::TITLE,
      "author" => "OxyMade", // author of this plugin
      "url" => home_url(),
      "beta" => false,
    ]
   );
  }

  /**
  * The loader that's responsible for maintaining and registering all hooks that power
  * the plugin.
  *
  * @since    1.0.0
  * @access   protected
  * @var      Oxymade_Loader    $loader    Maintains and registers all hooks for the plugin.
  */
  protected $loader;

  /**
  * The unique identifier of this plugin.
  *
  * @since    1.0.0
  * @access   protected
  * @var      string    $plugin_name    The string used to uniquely identify this plugin.
  */
  protected $plugin_name;

  /**
  * The current version of the plugin.
  *
  * @since    1.0.0
  * @access   protected
  * @var      string    $version    The current version of the plugin.
  */
  protected $version;

  /**
  * Define the core functionality of the plugin.
  *
  * Set the plugin name and the plugin version that can be used throughout the plugin.
  * Load the dependencies, define the locale, and set the hooks for the admin area and
  * the public-facing side of the site.
  *
  * @since    1.0.0
  */
  public function __construct()
  {
   if (defined("OXYMADE_VERSION")) {
    $this->version = OXYMADE_VERSION;
   } else {
    $this->version = "1.0.0";
   }
   $this->plugin_name = "oxymade";

   $this->load_dependencies();
   $this->set_locale();
   $this->define_admin_hooks();
   $this->define_public_hooks();
   
   $oxyberg_color_palette_status = get_option("oxymade_gutenberg_color_palette_status");
   $oxymade_blogzine = get_option("oxymade_blogzine");
   
   if (isset($oxymade_blogzine) && $oxymade_blogzine == "Disable" && isset($oxyberg_color_palette_status) && $oxyberg_color_palette_status == "Disable") {
     } else {
      
   if (class_exists("Oxygen_Gutenberg")) {
    $this->oxymadeberg();
   }
   // $this->oxymade_color_system_gutenberg();
   add_action("admin_enqueue_scripts", [
    $this,
    "oxymade_color_system_gutenberg",
   ]);
  }
  }

  /**
  * Load the required dependencies for this plugin.
  *
  * Include the following files that make up the plugin:
  *
  * - Oxymade_Loader. Orchestrates the hooks of the plugin.
  * - Oxymade_i18n. Defines internationalization functionality.
  * - Oxymade_Admin. Defines all hooks for the admin area.
  * - Oxymade_Public. Defines all hooks for the public side of the site.
  *
  * Create an instance of the loader which will be used to register the hooks
  * with WordPress.
  *
  * @since    1.0.0
  * @access   private
  */
  private function load_dependencies()
  {
   /**
    * The class responsible for orchestrating the actions and filters of the
    * core plugin.
    */
   require_once plugin_dir_path(dirname(__FILE__)) .
    "includes/class-oxymade-loader.php";

   /**
    * The class responsible for defining internationalization functionality
    * of the plugin.
    */
   require_once plugin_dir_path(dirname(__FILE__)) .
    "includes/class-oxymade-i18n.php";

   /**
    * The class responsible for defining all actions that occur in the admin area.
    */
   require_once plugin_dir_path(dirname(__FILE__)) .
    "admin/class-oxymade-admin.php";

   /**
    * The class responsible for defining all actions that occur in the public-facing
    * side of the site.
    */
   require_once plugin_dir_path(dirname(__FILE__)) .
    "public/class-oxymade-public.php";

   $this->loader = new Oxymade_Loader();
  }

  /**
  * Define the locale for this plugin for internationalization.
  *
  * Uses the Oxymade_i18n class in order to set the domain and to register the hook
  * with WordPress.
  *
  * @since    1.0.0
  * @access   private
  */
  private function set_locale()
  {
   $plugin_i18n = new Oxymade_i18n();

   $this->loader->add_action(
    "plugins_loaded",
    $plugin_i18n,
    "load_plugin_textdomain"
   );
  }

  /**
  * Register all of the hooks related to the admin area functionality
  * of the plugin.
  *
  * @since    1.0.0
  * @access   private
  */
  private function define_admin_hooks()
  {
   $plugin_admin = new Oxymade_Admin(
    $this->get_plugin_name(),
    $this->get_version()
   );

   $this->loader->add_action(
    "admin_enqueue_scripts",
    $plugin_admin,
    "enqueue_styles"
   );
   $this->loader->add_action(
    "admin_enqueue_scripts",
    $plugin_admin,
    "enqueue_scripts"
   );

   $this->loader->add_action(
    "admin_menu",
    $plugin_admin,
    "register_dashboard_pages"
   );
  }

  /**
  * Register all of the hooks related to the public-facing functionality
  * of the plugin.
  *
  * @since    1.0.0
  * @access   private
  */
  private function define_public_hooks()
  {
   $plugin_public = new Oxymade_Public(
    $this->get_plugin_name(),
    $this->get_version()
   );

   $this->loader->add_action(
    "wp_enqueue_scripts",
    $plugin_public,
    "enqueue_styles"
   );
   
   $this->loader->add_action(
    "wp_enqueue_scripts",
    $plugin_public,
    "enqueue_scripts"
   );
   
   $this->loader->add_action(
    "oxygen_enqueue_ui_scripts",
    $plugin_public,
    "oxy_enqueue_scripts"
   );
   
   
   
   $user_level = get_option("oxymade_user_level");
   
   
   if($user_level == "pro"){
   $oxymade_hoversPanel = get_option("oxymade_hoverstyles");
   if (isset($oxymade_hoversPanel) && $oxymade_hoversPanel == "Disable") {
   } else {
    $this->loader->add_action(
      "oxygen_after_toolbar",
      $plugin_public,
      "oxymade_hovers_panel"
    );
   }
  }

   $oxymade_gridhelpers = get_option("oxymade_gridhelpers");
   if (isset($oxymade_gridhelpers) && $oxymade_gridhelpers == "Disable") {
    } else {
   $this->loader->add_action(
    "oxygen_add_plus_oxymade_helpers",
    $plugin_public,
    "om_side_helpers"
   );
  }


   // $this->loader->add_action(
   //   "oxygen_after_toolbar",
   //   $plugin_public,
   //   "checkRequired"
   // );
   //TODO: chek if it is necessary above.
  }

  /**
  * Run the loader to execute all of the hooks with WordPress.
  *
  * @since    1.0.0
  */
  public function run()
  {
   $this->loader->run();
  }

  /**
  * The name of the plugin used to uniquely identify it within the context of
  * WordPress and to define internationalization functionality.
  *
  * @since     1.0.0
  * @return    string    The name of the plugin.
  */
  public function get_plugin_name()
  {
   return $this->plugin_name;
  }

  /**
  * The reference to the class that orchestrates the hooks with the plugin.
  *
  * @since     1.0.0
  * @return    Oxymade_Loader    Orchestrates the hooks of the plugin.
  */
  public function get_loader()
  {
   return $this->loader;
  }

  /**
  * Retrieve the version number of the plugin.
  *
  * @since     1.0.0
  * @return    string    The version number of the plugin.
  */
  public function get_version()
  {
   return $this->version;
  }
}

OxyMade::init();
?>