<?php

/**
 * OUAdmin class.
 *
 * @subpackage  includes
 * @package     oxy-ultimate
 *
 * @author      Paul Chinmoy
 * @link        https://www.paulchinmoy.com
 * @copyright   Copyright (c) 2020 Oxy Ultimate
 *
 * @since       1.0
 */
class OUAdmin {

	/**
	 * Options.
	 *
	 * @author    Paul Chinmoy
	 * @var       array
	 * @access    public
	 */
	static public $options;

	/**
	 * Action added on the init hook.
	 *
	 * @author  Paul Chinmoy
	 * @since   1.0
	 *
	 * @access  public
	 * @return  void
	 */
	static public function init() {

		new OUAdmin();

	}
  
	/**
	 * Get license key data
	 * Create admin menu pages
	 * Create a settings page
	 *
	 * @author  Paul Chinmoy
	 * @since   1.0
	 *
	 * @access  public
	 * @return  void
	 */
	function __construct() {
		self::$options = get_option( 'ouc_options' );

		add_action( 'admin_menu', array( $this, 'ouc_register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'ouc_activate_license_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ouc_admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_ou_activate_components', array(  $this, 'ou_activate_components') );

		add_filter( 'plugin_action_links', array( __CLASS__, 'ou_add_settings_link' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( __CLASS__, 'ou_add_settings_link' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'ou_add_plugin_row_meta' ), 10, 4 );
	}

	/**
	 * Register sub menu page
	 *
	 * @author  Paul Chinmoy
	 * @since   1.0
	 *
	 * @access  public
	 * @return  void
	 */
	public function ouc_register_admin_menu () {
		
		$menu_name = __( 'OxyUltimate', "oxy-ultimate" );

		$this->ouwl_save_white_label_data();

		$ouwl = get_option('ouwl');
		if( $ouwl ) {
			$menu_name = ! empty( $ouwl['menu_name'] ) ? esc_html( $ouwl['menu_name'] ) : $menu_name;
		}

		add_submenu_page( 'ct_dashboard_page', $menu_name , $menu_name , 'manage_options', 'ou_menu', array( $this, 'render_options_form' ), 5 );
	}

	/**
	 * Action on admin_init hook
	 *
	 * @author  Paul Chinmoy
	 * @since   1.0
	 *
	 * @access  public
	 * @return  void
	 */
	function ouc_activate_license_settings() {
		register_setting( 'ouc_activate_license', 'ouc_license' );

		add_settings_section(
			'ouc_license_key_section', 
			'<span class="ouc-lkey-heading">' . __( 'License Settings', "oxy-ultimate" ) . '</span>', 
			array( $this, 'ouc_license_callback' ), 
			'ouc_activate_license'
		);

		add_settings_field( 
			'ouc_license_key', 
			__( 'License Key', "oxy-ultimate" ), 
			array( $this, 'ouc_license_key' ), 
			'ouc_activate_license', 
			'ouc_license_key_section' 
		);
	}

	/** 
	 * Callback function
	 *
	 * @author  Paul Chinmoy
	 *
	 * @since   1.0
	 * @access  public
	 * @return  void    
	 */
	function ouc_license_callback() {
		echo '<p class="description desc">' . "\n";
		echo __( 'The license key is used for automatic upgrades and support.', "oxy-ultimate");
		echo '</p>' . "\n";
	}

	/**
	 * Activate the plugin for auto update & support
	 * Create settings form fields
	 *
	 * @author  Paul Chinmoy
	 * @since   1.0
	 *
	 * @access  public
	 * @return  void
	 */
	function ouc_license_key() {
		$options      = self::$options;
		$license_key  = $options['ouc_license_key'];
		$ouc_nonce    = wp_create_nonce( 'ouc-activate-key' );
		$class= $style = '';
	?>
		<input type="password" class="regular-text code" id="ouc_license_key" name='ouc_options[ouc_license_key]' value="<?php echo esc_attr( $license_key ); ?>" />
		<?php if( ( get_option('ouc_plugin_activate') == 'no' ) || ( get_option('ouc_plugin_activate') == '' ) ) { $class=''; $style=' style="display:none;"'; ?>
			<input type="button" class="button" id="btn-activate-license" value="<?php _e( 'Activate', "oxy-ultimate" ); ?>" onclick="JavaScript: ActivateOUPlugin( 'ouc_license_key', 'activate', '<?php echo $ouc_nonce; ?>');" />
			<div class="spinner" id="actplug"></div>
		<?php } else { ?> 
			<input type="button" class="button" id="btn-deactivate-license" value="<?php _e( 'Deactivate', "oxy-ultimate" ); ?>" onclick="JavaScript: ActivateOUPlugin( 'ouc_license_key', 'deactivate', '<?php echo $ouc_nonce; ?>');" />
			<div class="spinner"></div>
		<?php } if( get_option('ouc_plugin_activate') == 'expired' ) { $class=' error'; $style=' style="display:none;"'; ?>
			<input type="button" class="button" id="btn-reactivate-license" value="<?php _e( 'Reactivate', "oxy-ultimate" ); ?>" onclick="JavaScript: ActivateOUPlugin( 'ouc_license_key', 'reactivate', '<?php echo $ouc_nonce; ?>');" />
			<div class="spinner"></div>
		<?php } ?>
		<span class="ouc-response<?php echo $class; ?>"<?php echo $style; ?>></span>                                      
		<?php if( get_option('ouc_plugin_activate') == 'expired' ) { ?>
			<div class="update-nag" style="color: #900"> <?php _e( 'Invalid or Expired Key : Please make sure you have entered the correct value and that your key is not expired.', "oxy-ultimate"); ?></div>
	<?php }
	}

	/**  
	 * Render options form
	 *
	 * @author  Paul Chinmoy
	 * @since   1.0
	 *
	 * @access  public
	 * @return  void
	 */
	function render_options_form() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : false;

		$user_id = get_current_user_id();
		$permission = [ $user_id ];

		$ouwl = get_option( 'ouwl' );
		if( $ouwl ) {
			$permission = ! empty( $ouwl['tab_permission'] ) ? explode( ",", $ouwl['tab_permission'] ) : $permission;
		}
	?>
		 <div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a href="?page=ou_menu&amp;tab=components" class="nav-tab<?php echo ( $tab === false || $tab == 'editor' || $tab == 'components' ) ? ' nav-tab-active' : '';?>"><?php _e( 'Components', "oxy-ultimate" ); ?></a>
				<a href="?page=ou_menu&amp;tab=designsets" class="nav-tab<?php echo ($tab == 'designsets') ? ' nav-tab-active' : '';?>"><?php _e( 'Design Sets', "oxy-ultimate" ); ?></a>

				<?php if( in_array( $user_id, $permission ) ) : ?>
				<a href="?page=ou_menu&amp;tab=whitelabel" class="nav-tab<?php echo ($tab == 'whitelabel') ? ' nav-tab-active' : '';?>"><?php _e( 'White Label', "oxy-ultimate" ); ?></a>
				<?php endif; ?>

				<a href="?page=ou_menu&amp;tab=license" class="nav-tab<?php echo ($tab == 'license') ? ' nav-tab-active' : '';?>"><?php _e( 'License', "oxy-ultimate" ); ?></a>
			</h2>
			<div class="tab-content">
			 <?php if ( $tab === 'license' ) { ?>
					<div class="wrap ouc-options">
						<h2><?php _e( 'OxyUltimate', "oxy-ultimate" ); ?> v<?php echo OXYU_VERSION; ?></h2>
						<form action='options.php' method='post' class="ouc-options-form" id="ouc-options-form">
							<?php
								settings_fields( 'ouc_activate_license' );
								do_settings_sections( 'ouc_activate_license' );
							?>
						</form>
					</div>
			<?php } else if ( $tab === 'designsets' ) { ?>
				<div class="wrap">
					<h2><?php _e( 'Site Key', "oxy-ultimate" ); ?></h2>
					<p class="site-key">
						aHR0cHM6Ly9kZXNpZ25zZXRzLm94eXVsdGltYXRlLmNvbQpPeHkgVWx0aW1hdGUgU2V0cwo1cUlseVBPZm1RemU=
					</p>
					<h3>How do you enable the design sets on your site?</h3>
					<ol>
						<li>Navigate to <strong>Oxygen -> Settings</strong> page</li>
						<li>Click on the <strong>Library</strong> tab</li>
						<li>Check the <strong>Enable 3rd Party Design Sets</strong> checkbox</li>
						<li>Click on <strong>Update</strong> button</li>
						<li>Click on <strong>+ Add Design Set</strong> link</li>
						<li>Enter the above site key into the <strong>Site key</strong> input field</li>
						<li>Click on <strong>Add Source Site</strong> button</li>
					</ol>

					<h3>How to use the design sets on your builder editor?</h3>
					<ol>
						<li>Open the Oxygen Builder editor</li>
						<li>Go to <strong>Add -> Library -> Design Sets</strong> tab</li>
						<li>Click on the <strong>Oxy Ultimate Sets</strong> tab</li>
					</ol>
				</div>
			<?php } elseif( $tab == 'whitelabel' && in_array( $user_id, $permission ) ) {
					self::ou_white_label_form();
				} else {
					self::ou_components_settings();
				}
			?>
			</div>
		</div>
	<?php
	}

	static function ou_components_settings() {
		
		$data = getAllOUComps();

		if( ! empty( $data ) ) {
		
			$ouuc_nonce = wp_create_nonce( 'ou-disable-unused-components' );

			if ( is_network_admin() ) {
				// Update the site-wide option since we're in the network admin.
				$active_components = get_site_option( '_ou_active_components' );
			} else {
				$active_components = get_option( '_ou_active_components' );
			}
?>
		<h1 class="heading"><?php _e( 'Components Settings', "oxy-ultimate" ); ?></h1>
		<p class="big-notice description">Selected components will be <strong style="border-bottom:3px solid #fff;">activated</strong> and will show on Oxygen Builder editor.</p>
		<div class="ou-comp-wrap">
			<?php foreach( $data as $key => $component ) : ?>
				<div class="ou-col ou-acrd-item">
					<div class="ou-acrd-btn">
						<input type="checkbox" name="<?php echo strtolower( $key ); ?>_comp" class="section-cb" value="<?php echo strtolower( $key ); ?>"/>
						<label for="<?php echo $key; ?> Components"><?php echo $key; ?></label>
					</div>
					<div class="ou-acrd-content">
						<ul>
							<?php foreach ($component as $k => $value): $checked = (!empty($active_components) && in_array( $k, $active_components)) ? 'checked="checked"' : '' ; ?>
							<li>
								<input type="checkbox" name="ou_comps[]" value="<?php echo $k; ?>" class="check-column" <?php echo $checked;?>/>
								<label for="<?php echo $value; ?>"><?php echo $value; ?></label>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endforeach; ?>
			<input type="hidden" name="ouuc_nonce" value="<?php echo $ouuc_nonce; ?>" />
			<input type="hidden" name="active_components" value="<?php echo ( ( (sizeof( (array) $active_components ) - 1 ) <= 0 ) ? '' : join(",", (array) $active_components)); ?>" />
			<div class="clear clearfix div-button"><a href="JavaScript: void(0);" onclick="JavaScript: activateComponents(); return false;" class="page-title-action button-primary">Save Changes</a><span class="spinner"></span></div>
			<div class="notice notice-info ou-comp-notice" style="display: none;"><p><?php _e( 'Selected components are activated successfully.', "oxy-ultimate"); ?></p></div>
		</div>
	<?php } elseif( ! empty( $data ) && isset( $data['error'] ) ) { ?>
		<h1 class="heading"><?php _e( 'Components Settings', "oxy-ultimate" ); ?></h1>
		<p class="big-notice description"><?php echo $data['error']; ?></p>
	<?php } else { ?>
			<h1 class="heading"><?php _e( 'Components Settings', "oxy-ultimate" ); ?></h1>
			<p class="big-notice description">
				At first you will activate the <strong style="border-bottom:3px solid #fff;">License Key</strong>.
			</p>
	<?php
		}
	}

	function ou_activate_components() {
		check_ajax_referer( 'ou-disable-unused-components', 'security' );

		$active_components = $_POST['modules'];

		if( ! empty( $active_components ) ) {
			$components = explode(",", $active_components);
			$components = array_unique($components);

			if ( is_network_admin() ) {
				// Update the site-wide option since we're in the network admin.
				update_site_option( '_ou_active_components', $components );
			} else {
				update_option( '_ou_active_components', $components );
			}

			echo '200';
		} else {
			delete_option( '_ou_active_components' );
		}

		wp_die();
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @author  Paul Chinmoy
	 * @since   1.0
	 *
	 * @access  public
	 * @return  void
	 */
	function ouc_admin_enqueue_scripts( $hook ) {
		if( $hook !== 'oxygen_page_ou_menu' )
			return;

		wp_enqueue_style( 'ouc-admin-css', OXYU_URL . 'assets/css/ouc-admin.css', array(), time() );
		wp_enqueue_script( 'ouc-admin-script', OXYU_URL . 'assets/js/activate-plugin.js', array(), filemtime(OXYU_DIR . 'assets/js/activate-plugin.js'), true );
	}

	public static function ou_add_settings_link( $links, $file ) {

		if ( $file === 'oxy-ultimate/oxy-ultimate.php' && current_user_can( 'install_plugins' ) ) {
			if ( current_filter() === 'plugin_action_links' ) {
				$url = admin_url( 'admin.php?page=ou_menu' );
			} else {
				$url = admin_url( '/network/admin.php?page=ou_menu' );
			}

			$settings = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings' ) );
			array_unshift(
				$links,
				$settings
			);
		}

		return $links;
	}

	public static function ou_add_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( $plugin_file == 'oxy-ultimate/oxy-ultimate.php' && current_user_can( 'install_plugins' ) ) {
			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=oxy-ultimate&section=changelog&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( sprintf( __( 'More information about %s' ), $plugin_data['Name'] ) ),
				esc_attr( $plugin_data['Name'] ),
				__( 'Changelog' )
			);
		}

		return $plugin_meta;
	}

	private static function ou_white_label_form() {
		$plugin_name 	= 'placeholder="OxyUltimate"';
		$plugin_uri 	= 'placeholder="https://oxyultimate.com"';
		$author_name 	= 'placeholder="Chinmoy Paul"';
		$author_uri 	= 'placeholder="https://paulchinmoy.com"';
		$menu_name 		= 'placeholder="OxyUltimate"';
		$plugin_desc 	= '';
		$tab_permission = 'placeholder="Enter user ID. Use comma for multiple users"';

		$ouwl 			= get_option('ouwl');
		if( $ouwl ) {
			$plugin_name 	= ! empty( $ouwl['plugin_name'] ) ? 'value="' . esc_html( $ouwl['plugin_name'] ) . '"' : $plugin_name;
			$plugin_uri 	= ! empty( $ouwl['plugin_uri'] ) ? 'value="' . esc_html( $ouwl['plugin_uri'] ) . '"' : $plugin_uri;
			$author_name 	= ! empty( $ouwl['author_name'] ) ? 'value="' . esc_html( $ouwl['author_name'] ) . '"' : $author_name;
			$author_uri 	= ! empty( $ouwl['author_uri'] ) ? 'value="' . esc_html( $ouwl['author_uri'] ) . '"' : $author_uri;
			$plugin_desc 	= ! empty( $ouwl['plugin_desc'] ) ? esc_html( $ouwl['plugin_desc'] ) : $plugin_desc;
			$menu_name 		= ! empty( $ouwl['menu_name'] ) ? 'value="' . esc_html( $ouwl['menu_name'] ) . '"' : $menu_name;
			$tab_permission = ! empty( $ouwl['tab_permission'] ) ? 'value="' . esc_html( $ouwl['tab_permission'] ) . '"' : $tab_permission;
		}

		$url = add_query_arg( 'tab', 'whitelabel', menu_page_url( 'ou_menu', false ) );
	?>
		<h2><?php _e( 'White Label', 'oxy-ultimate' ); ?></h2>
		<p>It gives you the ability to control and transform the appearance of the back-end.</p>
		<div style="background-color: #f7f7f7; border: 1px solid #ccd0d4; padding: 5px 20px 15px; max-width: 580px;">
			<form method="post" action="<?php echo $url; ?>">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Plugin Name', 'oxy-ultimate' ); ?>
							</th>
							<td>
								<input id="plugin_name" name="ouwl[plugin_name]" type="text" class="regular-text" <?php echo $plugin_name; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Plugin URI', 'oxy-ultimate' ); ?>
							</th>
							<td>
								<input id="plugin_uri" name="ouwl[plugin_uri]" type="url" class="regular-text" <?php echo $plugin_uri; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Author Name', 'oxy-ultimate' ); ?>
							</th>
							<td>
								<input id="author_name" name="ouwl[author_name]" type="text" class="regular-text" <?php echo $author_name; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Author URI', 'oxy-ultimate' ); ?>
							</th>
							<td>
								<input id="author_uri" name="ouwl[author_uri]" type="url" class="regular-text" <?php echo $author_uri; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Plugin Description', 'oxy-ultimate' ); ?>
							</th>
							<td>
								<textarea id="plugin_desc" name="ouwl[plugin_desc]" class="large-text" cols="5" rows="8" ><?php echo $plugin_desc; ?></textarea>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Admin Menu Name', 'oxy-ultimate' ); ?>
							</th>
							<td>
								<input id="menu_name" name="ouwl[menu_name]" type="text" class="regular-text" <?php echo $menu_name; ?> />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Permission', 'oxy-ultimate' ); ?><br/>
								<lebel style="font-weight: normal; color: #999;"><?php _e( 'who can access this page', 'oxy-ultimate' ); ?></lebel>
							</th>
							<td>
								<input id="tab_permission" name="ouwl[tab_permission]" type="text" class="regular-text" <?php echo $tab_permission; ?> />
							</td>
						</tr>
					</tbody>
				</table>
				<?php wp_nonce_field( 'ou_nonce_action', 'ou_nonce_field' ); ?>
				<input type="hidden" name="action" value="save_data" />
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

	private function ouwl_save_white_label_data() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( isset( $_POST['action'] ) && $_POST['action'] == "save_data" ){
			if( isset( $_POST['ouwl'] ) ) {
				update_option('ouwl', $_POST['ouwl']);
			} else {
				delete_option('ouwl');
			}

			printf('<div class="notice notice-info is-dismissible"><p>%s</p></div>', __('Settings saved successfully.', 'oxy-ultimate'));
		}
	}
}