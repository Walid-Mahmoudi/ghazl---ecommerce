<?php
class OxyUltimateWooLoader {
	static function init() {
		self::define_constants();

		register_activation_hook( OUWOO_FILE, 	__CLASS__ . '::ouwoo_activate' );
		
		add_action( 'before_woocommerce_init', function() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', OUWOO_FILE, true );
			}
		} );
		
		add_action( 'admin_init', 				__CLASS__ . '::ouwoo_activate' );
		add_action( 'switch_theme', 			__CLASS__ . '::ouwoo_activate' );	
		add_action( 'plugins_loaded', 			__CLASS__ . '::ouwoo_text_domain' );
		add_action( 'init', 					__CLASS__ . '::ouwoo_load_files', 15 );
		add_action( 'wp_loaded', 				__CLASS__ . '::ouwoo_buy_now_action', 20 );
	}

	static function define_constants() {
		//* Define constants
		define( 'OUWOO_VERSION', 	'1.5.4' );
		define( 'OUWOO_FILE', 		trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'oxyultimate-woo.php' );
		define( 'OUWOO_DIR', 		plugin_dir_path( OUWOO_FILE ) );
		define( 'OUWOO_URL', 		plugins_url( '/', OUWOO_FILE ) );

		global $ouwoo_constant, $item_data;
		$ouwoo_constant = [
			'swiper_css' 	=> false
		];

		$item_data = array();
	}

	static function ouwoo_activate()
	{
		if ( ! class_exists('OxyEl') || ! class_exists( 'WooCommerce' ) )
		{
			add_action( 'admin_notices', 			__CLASS__ . '::ouwoo_admin_notice_message' );
			add_action( 'network_admin_notices', 	__CLASS__ . '::ouwoo_admin_notice_message' );
		}
	}

	/**
	 * Shows an admin notice if you're not using the Oxygen Builder.
	 */
	static function ouwoo_admin_notice_message()
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

		$error = __( 'Sorry, you can\'t use the OxyUltimate Woo unless the Oxygen Builder & WooCommerce Plugin is active.', 'oxyultimate-woo' );

		echo '<div class="error"><p>' . $error . '</p></div>';
		if ( isset( $_GET['activate'] ) )
		{
			unset( $_GET['activate'] );
		}
	}

	/**
	 * Load textdomain for translation
	 */ 
	static function ouwoo_text_domain()
	{
		load_plugin_textdomain( 'oxyultimate-woo', false, basename( OUWOO_DIR ) . '/languages' );

		add_filter( 'all_plugins', __CLASS__ . '::update_branding' );
	}

	static function ouwoo_load_files() {
		if( ! class_exists( 'OxyEl' ) )
			return;

		if( ! class_exists( 'WooCommerce' ) )
			return;

		//* include files
		require_once OUWOO_DIR . 'includes/UltimateWooEl.php';
		require_once OUWOO_DIR . 'includes/helpers.php';
		require_once OUWOO_DIR . 'includes/components-init.php';

		include_once OUWOO_DIR . 'smart-php-func/ou-woo.php';
		include_once OUWOO_DIR . 'includes/conditions.php';
		

		if( is_admin() ) {
			require_once OUWOO_DIR . 'includes/admin.php';
			OUWooAdmin::init();

			require_once OUWOO_DIR . 'includes/updater.php';
			new OUWOO_Updater( 'https://oxyultimate.com/ouwooapi/', OUWOO_VERSION );
		}
	}

	/**
	 * Update branding.
	 *
	 * @since 1.3.0
	 * @return array
	 */
	public static function update_branding( $all_plugins ) {
		$plugin_slug = plugin_basename( OUWOO_FILE );
		
		$ouwoowl = get_option('ouwoowl');

		if( $ouwoowl ) {
			$all_plugins[$plugin_slug]['Name'] 		= ! empty( $ouwoowl['plugin_name'] ) ? esc_html( $ouwoowl['plugin_name'] ) : $all_plugins[$plugin_slug]['Name'];
			$all_plugins[$plugin_slug]['PluginURI'] = ! empty( $ouwoowl['plugin_uri'] ) ? esc_html( $ouwoowl['plugin_uri'] ) : $all_plugins[$plugin_slug]['PluginURI'];
			$all_plugins[$plugin_slug]['Author'] 	= ! empty( $ouwoowl['author_name'] ) ? esc_html( $ouwoowl['author_name'] ) : $all_plugins[$plugin_slug]['Author'];
			$all_plugins[$plugin_slug]['AuthorURI'] = ! empty( $ouwoowl['author_uri'] ) ? esc_html( $ouwoowl['author_uri'] ) : $all_plugins[$plugin_slug]['AuthorURI'];
			$all_plugins[$plugin_slug]['Description'] = ! empty( $ouwoowl['plugin_desc'] ) ? esc_html( $ouwoowl['plugin_desc'] ) : $all_plugins[$plugin_slug]['Description'];
		}

		$all_plugins[$plugin_slug]['Title'] = $all_plugins[$plugin_slug]['Name'];
		
		return $all_plugins;
	}

	public static function ouwoo_buy_now_action() {
		if ( isset( $_GET['ou_empty_cart'] ) && 'yes' === esc_html( $_GET['ou_empty_cart'] ) ) {
			WC()->cart->empty_cart();

			if( isset( $_GET['ou_redirect'] ) && 'yes' === esc_html( $_GET['ou_redirect'] ) ) {
				$referer  = esc_url( remove_query_arg( [ 'ou_empty_cart', 'ou_redirect' ] ) );
				wp_safe_redirect( $referer ); 
				exit();
			}
		}

		if( isset( $_GET['ou_buy_now'] ) && $_GET['ou_buy_now'] == 'yes' ) {
			if( isset( $_GET['keep_cart_items'] ) && $_GET['keep_cart_items'] == 'no' ) { WC()->cart->empty_cart(); }
			
			$product_id = absint( $_GET['add_to_cart'] );

			WC()->cart->add_to_cart( $product_id, 1 );

			$referer  = esc_url( remove_query_arg( [ 'add_to_cart', 'ou_buy_now', 'keep_cart_items' ] ) );
			wp_safe_redirect( $referer ); 
			exit();
		}

		if( isset( $_POST['ou_buy_now'] ) && $_POST['ou_buy_now'] == 'yes' ) {
			$flag 				= false;
			$keep_cart_items 	= isset( $_POST['keep_cart_items'] ) ? $_POST['keep_cart_items'] : 'no';
			$quantities 		= $_POST['quantity'];

			//* grouped product
			if( is_array( $quantities ) ) {
				
				$qtys = array_flip( $quantities );
				
				foreach( $qtys as $quantity => $product_id ) {
					if( ! empty( $quantity ) )  {
						if( $keep_cart_items == 'no' && $flag === false ) { WC()->cart->empty_cart(); }
						if( $keep_cart_items == 'no' ){ WC()->cart->add_to_cart( absint( $product_id ), absint( $quantity ) ); }
						$flag = true;
					}
				}

			} else {
				
				$flag = true;

				//* remove the existing cart items and add new item
				if( $keep_cart_items == 'no' )
					WC()->cart->empty_cart();
				
				$product_id = absint( $_POST['product_id'] );
				$quantity = absint( $_POST['quantity'] );

				if ( isset( $_POST['variation_id'] ) ) {
					$variation_id = absint( $_POST['variation_id'] );
					WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
				} else {
					WC()->cart->add_to_cart( $product_id, $quantity );
				}
			}

			if( $flag ) {
				//* clear the all notices
				wc_clear_notices();

				wp_safe_redirect( esc_url( $_POST['ou_redirect_url'] ) ); 
				exit();
			}
		}
	}
}

OxyUltimateWooLoader::init();