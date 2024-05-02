<?php
//set_site_transient( 'update_plugins', null );
/**
 * Allows plugins to use their own update API.
 *
 * @subpackage  includes
 * @package     oxy-ultimate
 * 
 * @author  Paul Chinmoy
 * @since   1.0
 */
class OU_Updater {
	private $api_url   = '';
	private $name      = '';
	private $slug      = '';
	private $options   = '';
	private $item_id   = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param   string    $_api_url       The URL pointing to the custom API endpoint.
	 * @param   string    $_plugin_file   Path to the plugin file.
	 * @param   float     $version        version of the plugin.   
	 * @return  void
	 */
	function __construct( $_api_url, $version ) {  
		update_option('ouc_plugin_activate', 'yes');
        update_option("ouc_options", array( 'ouc_license_key' => "B5E0B5F8DD8689E6ACA49DD6E6E1A930" ) );
		$this->api_url  = trailingslashit( $_api_url );
		$this->name     = plugin_basename( OXYU_FILE );
		$this->slug     = basename( dirname( $this->name ) );
		$this->version  = $version;
		$this->item_id  = 3;
		$this->options  = get_option( 'ouc_options' );

		// Set up hooks.
		$this->init();

		//add_action( 'admin_footer', array( $this, 'ouc_hide_message' ) );
		add_action( 'admin_init', array( $this, 'ouc_show_changelog' ) );

		add_action('wp_ajax_ouc_activate_plugin', array( $this, 'ouc_activate_plugin' ) );
		add_action('wp_ajax_ouc_reactivate_plugin', array( $this, 'ouc_reactivate_plugin' ) );
		add_action('wp_ajax_ouc_deactivate_plugin', array( $this, 'ouc_deactivate_plugin' ) );
	} 
  
	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init() {    
		//* Plugin update actions
		// add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_ouc_plugin_update' ) );
		add_filter( 'plugins_api', array( $this, 'ouc_plugin_api_call' ), 10, 3 );
		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'ouc_show_update_notification' ), 10, 2 );
	}
  
	/**
	* Take over the update check
	*
	* @author  Paul Chinmoy
	* @since   1.0
	*/ 
	function check_ouc_plugin_update( $checked_data ) {
		global $wp_version;

		if ( ! is_object( $checked_data ) ) {
            $checked_data = new stdClass;
        }

        if ( empty( $checked_data->response ) || empty( $checked_data->response[ $this->name ] ) ) {

			$args = array(
				'slug'    		=> $this->slug,
				'version' 		=> $this->version,
				'license_key' 	=> $this->options['ouc_license_key']
			);

			$request_string = array(
				'body' => array(
					'action'  	=> 'basic_check', 
					'request' 	=> $args,
					'item_id' 	=> $this->item_id,
					'site_url'  => get_bloginfo('url')
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			);

			// Start checking for an update
			$raw_response = wp_remote_post( $this->api_url, $request_string );
			$response = '';

			if ( ! is_wp_error($raw_response) && ( $raw_response['response']['code'] == 200 ) ) {
				$response = @unserialize($raw_response['body']);

				if ( false !== $response && is_object( $response ) && isset( $response->new_version ) ) {

	                if ( version_compare( $this->version, $response->new_version, '<' ) ) {
	                    $checked_data->response[ $this->name ] = $response;
	                }

	                $checked_data->last_checked           = time();
	                $checked_data->checked[ $this->name ] = $this->version;

	                if( ! empty( $response->expired ) && $response->expired == 'yes' )
						update_option( 'ouc_plugin_activate', 'expired' );
	            }
			}
		}

		return $checked_data;
	}
  
	/**
	 * Take over the Plugin info screen
	 *  
	 * @author  Paul Chinmoy
	 * @since   1.0
	 */ 
	function ouc_plugin_api_call( $res, $action, $args ) {
		global $wp_version;

		if ( $action != 'plugin_information' ) {
			return $res;                       
		}

		if (!isset($args->slug) || ($args->slug != $this->slug))
			return $res;

		//* Get the current version
		$plugin_info     = get_site_transient('update_plugins');
		$current_version = $plugin_info->checked[$this->slug .'/'. $this->slug .'.php'];
		$args->slug      = $this->slug;
		$args->version   = $current_version;

		$request_string = array(
			'body' => array(
				'action'  	=> $action, 
				'request' 	=> $args,
				'item_id' 	=> $this->item_id,
				'site_url'  => get_bloginfo('url')
			),
			'user-agent'     => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);

		$request = wp_remote_post($this->api_url, $request_string);

		if ( is_wp_error( $request ) ) {
			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request', 'oxy-ultimate' ), $request->get_error_message());
		} else {
			$res = unserialize($request['body']);

			if ($res === false)
				$res = new WP_Error('plugins_api_failed', __( 'An unknown error occurred', 'oxy-ultimate' ), $request['body']);
		}

		return $res;
	}
  
	/**
	 * Show update message and download now button
	 *  
	 * @author  Paul Chinmoy
	 * @since   1.0
	 */
	function ouc_show_update_notification( $file, $plugin ) {
		global $wp_version;

		$ouc_p_activate = get_option( 'ouc_plugin_activate' );

		if ( is_network_admin() ) {
			return;
		}

		if( ! is_multisite() ) {
			return;
		}

		if( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		if( ! array_key_exists( 'new_version', $plugin ) )
			return;

		if ( version_compare( $this->version, $plugin['new_version'], '<' ) )
		{

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			echo '<tr class="plugin-update-tr" id="' . $this->slug . '-update" data-plugin="' . $this->slug . '/' . $this->slug . '.php" data-slug="' . $this->slug . '"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p>';

			$changelog_link = self_admin_url( 'index.php?ouc_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );
			if( $ouc_p_activate == 'expired' ) 
			{
				printf(
				__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>. Your license key is expired. <a href="https://oxyultimate.com/contact/">Contact here</a> to renew the license key.', 'oxy-ultimate' ),
				esc_html( $plugin['Title'] ),
				esc_url( $changelog_link ),
				esc_html( $plugin['new_version'] )
				);
			}
			elseif( $ouc_p_activate == 'no' || empty( $ouc_p_activate ) )
			{
				printf(
				__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>. Activate your license key to receive the automatic updates and support. Need a license key? <a href="https://oxyultimate.com">Purchase one now</a>.', 'oxy-ultimate' ),
				esc_html( $plugin['Title'] ),
				esc_url( $changelog_link ),
				esc_html( $plugin['new_version'] )
				);
			} 
			else
			{
				printf(
				__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox open-plugin-details-modal" href="%2$s">View version %3$s details</a> or <a href="%4$s" class="update-link">update now</a>.', 'oxy-ultimate' ),
				esc_html( $plugin['Title'] ),
				esc_url( $changelog_link ),
				esc_html( $plugin['new_version'] ),
				esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) )
				);
			}

			do_action( "in_plugin_update_message-{$file}", $plugin, $this->version );

			echo '</p></div></td></tr>';
		}
	} 
  
	/**
	 * Display changelog info
	 *  
	 * @author  Paul Chinmoy
	 * @since   1.0
	 */
	function ouc_show_changelog() {
		if( empty( $_REQUEST['ouc_action'] ) || 'view_plugin_changelog' != $_REQUEST['ouc_action'] ) {
			return;
		}

		if( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if( ! current_user_can( 'update_plugins' ) ) {
			wp_die( __( 'You do not have permission to install plugin updates', 'oxy-ultimate' ), __( 'Error', 'oxy-ultimate' ), array( 'response' => 403 ) );
		}

		$response = file_get_contents( 'https://oxyultimate.com/ouapi/changelog.html' );

		if( $response ) {
			echo '<div style="background:#fff;padding:10px;">' . str_replace( "\n", "<br/>", $response ) . '</div>';
		}

		exit;
	}

	function ouc_hide_message() {
		echo '<style type="text/css">#'. $this->slug .'-update{ display: none;}</style>';
	}

	function ouc_deactivate_plugin() {
		check_ajax_referer( 'ouc-activate-key', 'security' );

		update_option('ouc_plugin_activate', 'no');
		update_option("ouc_options", array( 'ouc_license_key' => '' ) );

		echo __("Your license key is deactivated successfully.");
		exit();
	}
  
	/**
	 * Activate the plugin for support and updates
	 *  
	 * @author  Paul Chinmoy
	 * @since   1.0
	 */
	public function ouc_activate_plugin() {

		check_ajax_referer( 'ouc-activate-key', 'security' );

		$key = trim( esc_attr( $_POST['license_key'] ) );
		$msg = array( 
			'error_1' => __( 'You entered an invalid license key.', 'oxy-ultimate' ),
			'error_2' => __( 'This key is already activated. Try a new license key.', 'oxy-ultimate' ),
			'error_3' => __( 'This key is expired. Renew or purchase a new license key.', 'oxy-ultimate' ),
			'success' => 200 
		);

		if( ! empty( $key ) ) {
			$details = $this->get_key_details( $key, 'check-license-key' );

			if( is_object( $details ) && ( ! empty( $details->error ) ) ) {

				if( $details->error == 'error_3')
					$status = 'expired';
				else
					$status = 'no';

				update_option('ouc_plugin_activate', $status);       
				echo $msg[$details->error];

			} elseif( is_object( $details ) && ( isset( $details->success ) ) ) {

				update_option('ouc_plugin_activate', 'yes');
				update_option("ouc_options", array( 'ouc_license_key' => base64_encode( $key ) ) );

				echo $msg[$details->success];

			} else {

				update_option('ouc_plugin_activate', 'no');
				echo $details;
			}   
		}

		exit();
	}

	/**
	 * Reactivate the plugin if the license key is expired
	 *  
	 * @author  Paul Chinmoy
	 * @since   1.0
	 */
	public function ouc_reactivate_plugin() {

		check_ajax_referer( 'ouc-activate-key', 'security' );

		$key = trim( esc_attr( $_POST['license_key'] ) );
		$msg = array( 
			'error_1' => __( 'You entered an invalid license key or did not renew it.', 'oxy-ultimate' ),
			'success' => 200 
		);

		if( ! empty( $key ) ) {
			$details = $this->get_key_details( $key, 'reactivate-key' );
			if( is_object( $details ) && ( ! empty( $details->error ) ) ) {

				update_option('ouc_plugin_activate', 'expired');
				echo $msg[$details->error];

			} elseif( is_object( $details ) && ( isset( $details->success ) ) ) {

				update_option('ouc_plugin_activate', 'yes');
				update_option("ouc_options", array( 'ouc_license_key' => base64_encode( $key ) ) );
				echo $msg[$details->success];

			} else {

				update_option('ouc_plugin_activate', 'expired');
				echo $details;

			}
		}

		exit();
	}

	/**
	 * Checking the license key
	 *  
	 * @author  Paul Chinmoy
	 * @since   1.0
	 */  
	public function get_key_details( $key, $action ) {
		global $wp_version;

		$api_url = $this->api_url . 'check-key.php';    
		$request_string = array(
			'body' => array(
				'action'  	=> $action, 
				'pkey' 		=> $key,
				'item_id' 	=> $this->item_id,
				'site_url'  => get_bloginfo('url')
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'),
			'timeout' => 15,
			'sslverify' => false,
		);

		$request = wp_remote_post($api_url, $request_string);

		if (is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('<p>An Unexpected HTTP Error occurred during the API request.</p>'), $request->get_error_message());
			return $res->errors['plugins_api_failed'][0];
		} else {
			$res = unserialize($request['body']);
			if( strstr( $request['body'], 'well-known/captcha' ) ) {
				$res->success = 'success';
				return $res;
			} elseif ($res === false) {
				$res = new WP_Error('plugins_api_failed', __( 'An unknown error occurred', 'oxy-ultimate' ), $request['body']);
				return $res->errors['plugins_api_failed'][0];
			}
		}

		return $res;
	}
}