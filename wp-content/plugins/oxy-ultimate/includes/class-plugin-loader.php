<?php

class OxyUltimateLoader {
	static function init() {
		self::define_constants();

		register_activation_hook( OXYU_FILE, 	__CLASS__ . '::oxyu_activate' );
		
		add_action( 'admin_init', 				__CLASS__ . '::oxyu_activate' );
		add_action( 'switch_theme', 			__CLASS__ . '::oxyu_activate' );	
		add_action( 'plugins_loaded', 			__CLASS__ . '::oxyu_init' );
	}

	static function define_constants() {
		//* Define constants
		define( 'OXYU_VERSION', 	'1.6.6' );
		define( 'OXYU_FILE', 		trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'oxy-ultimate.php' );
		define( 'OXYU_DIR', 		plugin_dir_path( OXYU_FILE ) );
		define( 'OXYU_URL', 		plugins_url( '/', OXYU_FILE ) );

		global $ou_constant;
		$ou_constant = [
			'base_js' => false
		];
	}

	static function oxyu_activate()
	{
		if ( ! class_exists('OxyEl') )
		{
			add_action( 'admin_notices', 			__CLASS__ . '::oxyu_admin_notice_message' );
			add_action( 'network_admin_notices', 	__CLASS__ . '::oxyu_admin_notice_message' );
		}
	}

	/**
	 * Shows an admin notice if you're not using the Oxygen Builder.
	 */
	static function oxyu_admin_notice_message()
	{
		if ( ! is_admin() ) {
			return;
		}
		else if ( ! is_user_logged_in() ) {
			return;
		}
		else if ( ! current_user_can( 'update_core' ) ) {
			return;
		}

		$error = __( 'Sorry, you can\'t use the Oxy Ultimate addon unless the Oxygen Builder Plugin is active.', 'oxy-ultimate' );

		echo '<div class="error"><p>' . $error . '</p></div>';
	}

	/**
	 * Load files.
	 */ 
	static function oxyu_init()
	{
		if ( ! class_exists('OxyEl') )
			return;

		//* Load textdomain for translation 
		load_plugin_textdomain( 'oxy-ultimate', false, basename( OXYU_DIR ) . '/languages' );

		add_filter( 'all_plugins', __CLASS__ . "::update_branding" );

		self::ou_load_files();
	}

	public static function update_branding( $all_plugins ) {
		$plugin_slug = plugin_basename( OXYU_FILE );
		
		$ouwl = get_option('ouwl');

		if( $ouwl ) {
			$all_plugins[$plugin_slug]['Name'] 		= ! empty( $ouwl['plugin_name'] ) ? esc_html( $ouwl['plugin_name'] ) : $all_plugins[$plugin_slug]['Name'];
			$all_plugins[$plugin_slug]['PluginURI'] = ! empty( $ouwl['plugin_uri'] ) ? esc_html( $ouwl['plugin_uri'] ) : $all_plugins[$plugin_slug]['PluginURI'];
			$all_plugins[$plugin_slug]['Author'] 	= ! empty( $ouwl['author_name'] ) ? esc_html( $ouwl['author_name'] ) : $all_plugins[$plugin_slug]['Author'];
			$all_plugins[$plugin_slug]['AuthorURI'] = ! empty( $ouwl['author_uri'] ) ? esc_html( $ouwl['author_uri'] ) : $all_plugins[$plugin_slug]['AuthorURI'];
			$all_plugins[$plugin_slug]['Description'] = ! empty( $ouwl['plugin_desc'] ) ? esc_html( $ouwl['plugin_desc'] ) : $all_plugins[$plugin_slug]['Description'];
		}

		$all_plugins[$plugin_slug]['Title'] = $all_plugins[$plugin_slug]['Name'];
		
		return $all_plugins;
	}

	static function ou_load_files() {
		//* include files
		require_once OXYU_DIR . 'includes/class-oxyultimate-el.php';
		require_once OXYU_DIR . 'includes/helpers.php';

		if( is_admin() ) {
			require_once OXYU_DIR . 'includes/admin.php';
			OUAdmin::init();

			require_once OXYU_DIR . 'includes/updater.php';
			new OU_Updater( 'https://oxyultimate.com/ouapi/', OXYU_VERSION );
		}
	}
}

OxyUltimateLoader::init();
