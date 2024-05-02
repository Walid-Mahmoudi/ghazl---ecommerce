<?php

/**
 * OUAdmin class.
 *
 * @subpackage  includes
 * @package     oxyultimate-woo
 *
 * @author      Paul Chinmoy
 * @link        https://www.paulchinmoy.com
 * @copyright   Copyright (c) 2020 Oxy Ultimate
 *
 * @since       1.0
 */
class OUWooAdmin {

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
		new OUWooAdmin();
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
		self::$options = get_option( 'ouwoo_options' );

		add_action( 'admin_menu', array( $this, 'ouwoo_register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'ouwoo_activate_license_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ouwoo_admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_ouwoo_active_components', array(  $this, 'ouwoo_active_components') );

		add_filter( 'plugin_action_links', array( __CLASS__, 'ouwoo_add_settings_link' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( __CLASS__, 'ouwoo_add_settings_link' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'ouwoo_add_plugin_row_meta' ), 10, 4 );

		add_filter( 'manage_shop_order_posts_columns', array( $this, 'ouwoo_filter_shop_order_posts_columns' ), 99 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'ouwoo_preview_link_column' ), 99, 2);
		
		add_action( 'admin_print_scripts-post.php', array( $this, 'ouwoo_enqueue_admin_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'ouwoo_enqueue_admin_scripts' ) );
		add_action( 'admin_print_scripts-edit.php', array( $this, 'ouwoo_enqueue_admin_scripts' ) );

		add_action( 'save_post', array( $this, 'ouwoo_save_meta_box_data' ), 20 );
	}

	/**
	 * Adding a column at Order page
	 *
	 * @author  Paul Chinmoy
	 * @since   1.2.5
	 *
	 * @return  void
	 */
	function ouwoo_filter_shop_order_posts_columns( $columns ) {
		$columns['preview_link'] = __( 'Received Link', 'oxyultimate-woo' );
		return $columns;
	}

	/**
	 * Display preview link
	 *
	 * @author  Paul Chinmoy
	 * @since   1.2.5
	 *
	 * @return  void
	 */
	function ouwoo_preview_link_column( $column, $post_id ) {

		if ( 'preview_link' === $column ) {
			$order = wc_get_order( $post_id );
			if( ! $order ) return;

			printf( 
				'<a href="%s" alt="Receipt of #%s" target="_blank">%s #%s<a/>', 
				$order->get_checkout_order_received_url(), 
				$post_id, 
				__('Receipt of', 'oxyultimate-woo'), 
				$post_id 
			);
		}
	}

	/**
	 * Save metabox data
	 *
	 * @author  Paul Chinmoy
	 * @since   1.2.5
	 *
	 * @return  void
	 */
	function ouwoo_save_meta_box_data( $post_id ) {
		// Check if our nonce is set
		if ( ! isset( $_POST['ct_view_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid
		if ( ! wp_verify_nonce( $_POST['ct_view_meta_box_nonce'], 'ct_view_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( !oxygen_vsb_current_user_can_access() ) {
			return;
		}

		if( isset($_POST['ouwoo_template_thankyou']) ) {
			$template_thankyou 	= isset($_POST['ouwoo_template_thankyou']) ? sanitize_text_field($_POST['ouwoo_template_thankyou']) : false;
			update_post_meta( $post_id, 'ouwoo_template_thankyou', $template_thankyou );
		} else {
			delete_post_meta( $post_id, 'ouwoo_template_thankyou' );
		}

		if( isset($_POST['ouwoo_template_quickview']) ) {
			$template_quickview = isset($_POST['ouwoo_template_quickview']) ? sanitize_text_field($_POST['ouwoo_template_quickview']) : false;
			update_post_meta( $post_id, 'ouwoo_template_quickview', $template_quickview );
			update_post_meta( $post_id, 'ct_template_post_types', ['product'] );
		} else {
			delete_post_meta( $post_id, 'ouwoo_template_quickview' );
		}

		return $post_id;
	}

	/**
	 * Add an option to Oxygen metabox
	 *
	 * @author  Paul Chinmoy
	 * @since   1.2.5
	 *
	 * @return  void
	 */
	function ouwoo_enqueue_admin_scripts() {
		global $post, $pagenow;

		if( is_object( $post ) && 'ct_template' == $post->post_type ) {

			$template_thankyou = get_post_meta( $post->ID, 'ouwoo_template_thankyou', true );
			$template_quickview = get_post_meta( $post->ID, 'ouwoo_template_quickview', true );
	?>
		<script type="text/javascript">
			document.addEventListener( 'DOMContentLoaded', function(){
				var parentdiv = document.getElementById('oxygen-template-application-other');
				parentdiv.querySelector('.oxygen-metabox-control-group').insertAdjacentHTML( 'beforeend',
					'<br/><label>' +
					'<input type="checkbox" name="ouwoo_template_thankyou" value="true" <?php if ( $template_thankyou ) echo "checked=checked"; ?>>' +
					'Thank You Page (from OxyUltimate Woo)</label>' +
					'<br/><label>' +
					'<input type="checkbox" name="ouwoo_template_quickview" value="true" <?php if ( $template_quickview ) echo "checked=checked"; ?>>' +
					'Quick View (from OxyUltimate Woo)</label>'
				);
			});
		</script>
	<?php
		}
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
	function ouwoo_register_admin_menu () {
		$menu_name = __( 'OxyUltimate Woo', "oxyultimate-woo" );

		$this->ouwoo_save_white_label_data();

		$ouwoowl = get_option('ouwoowl');
		if( $ouwoowl ) {
			$menu_name = ! empty( $ouwoowl['menu_name'] ) ? esc_html( $ouwoowl['menu_name'] ) : $menu_name;
			//$menu_slug = ! empty( $ouwoowl['menu_slug'] ) ? esc_html( $ouwoowl['menu_slug'] ) : $menu_slug;
		}

		add_submenu_page( 'ct_dashboard_page', $menu_name, $menu_name, 'manage_options', 'ouwoo_menu', array( $this, 'render_options_form' ) );
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
	function ouwoo_activate_license_settings() {
		register_setting( 'ouwoo_activate_license', 'ouwoo_license' );

		add_settings_section(
			'ouwoo_license_key_section', 
			'<span class="ouwoo-lkey-heading">' . __( 'License Settings', "oxyultimate-woo" ) . '</span>', 
			array( $this, 'ouwoo_license_callback' ), 
			'ouwoo_activate_license'
		);

		add_settings_field( 
			'ouwoo_license_key', 
			__( 'License Key', "oxyultimate-woo" ), 
			array( $this, 'ouwoo_license_key' ), 
			'ouwoo_activate_license', 
			'ouwoo_license_key_section' 
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
	function ouwoo_license_callback() {
		echo '<p class="description desc">' . "\n";
		echo __( 'The license key is used for automatic upgrades and support.', "oxyultimate-woo");
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
	function ouwoo_license_key() {
		$options      = self::$options;
		$license_key  = isset( $options['ouwoo_license_key'] ) ? $options['ouwoo_license_key'] : '';
		$ouwoo_nonce    = wp_create_nonce( 'ouwoo-activate-key' );
		$class= $style = '';
	?>
		<input type="password" class="regular-text code" id="ouwoo_license_key" name='ouwoo_options[ouwoo_license_key]' value="<?php echo esc_attr( $license_key ); ?>" />
		<?php if( ( get_option('ouwoo_plugin_activate') == 'no' ) || ( get_option('ouwoo_plugin_activate') == '' ) ) { $class=''; $style=' style="display:none;"'; ?>
			<input type="button" class="button" id="btn-activate-license" value="<?php _e( 'Activate', "oxyultimate-woo" ); ?>" onclick="JavaScript: ActivateOUWooPlugin( 'ouwoo_license_key', 'activate', '<?php echo $ouwoo_nonce; ?>');" />
			<input type="button" class="button" style="display:none;" id="btn-deactivate-license" value="<?php _e( 'Deactivate', "oxyultimate-woo" ); ?>" onclick="JavaScript: ActivateOUWooPlugin( 'ouwoo_license_key', 'deactivate', '<?php echo $ouwoo_nonce; ?>');" />
			<div class="spinner" id="actplug"></div>
		<?php } else { ?> 
			<input type="button" class="button" style="display:none;" id="btn-activate-license" value="<?php _e( 'Activate', "oxyultimate-woo" ); ?>" onclick="JavaScript: ActivateOUWooPlugin( 'ouwoo_license_key', 'activate', '<?php echo $ouwoo_nonce; ?>');" />
			<input type="button" class="button" id="btn-deactivate-license" value="<?php _e( 'Deactivate', "oxyultimate-woo" ); ?>" onclick="JavaScript: ActivateOUWooPlugin( 'ouwoo_license_key', 'deactivate', '<?php echo $ouwoo_nonce; ?>');" />
			<div class="spinner" id="actplug"></div>
		<?php } if( get_option('ouwoo_plugin_activate') == 'expired' ) { $class=' error'; $style=' style="display:none;"'; ?>
			<input type="button" class="button" id="btn-reactivate-license" value="<?php _e( 'Reactivate', "oxyultimate-woo" ); ?>" onclick="JavaScript: ActivateOUWooPlugin( 'ouwoo_license_key', 'reactivate', '<?php echo $ouwoo_nonce; ?>');" />
			<div class="spinner" id="actplug"></div>
		<?php } ?>
		<span class="ouwoo-response<?php echo $class; ?>"<?php echo $style; ?>></span>                                      
		<?php if( get_option('ouwoo_plugin_activate') == 'expired' ) { ?>
			<div class="update-nag" style="color: #900"> <?php _e( 'Invalid or Expired Key : Please make sure you have entered the correct value and that your key is not expired.', "oxyultimate-woo"); ?></div>
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

		$ouwoowl = get_option( 'ouwoowl' );
		if( $ouwoowl ) {
			$permission = ! empty( $ouwoowl['tab_permission'] ) ? explode( ",", $ouwoowl['tab_permission'] ) : $permission;
		}
	?>
		 <div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a href="?page=ouwoo_menu&amp;tab=components" class="nav-tab<?php echo ( $tab === false || $tab == 'editor' || $tab == 'components' ) ? ' nav-tab-active' : '';?>"><?php _e( 'Components', "oxyultimate-woo" ); ?></a>
				<a href="?page=ouwoo_menu&amp;tab=designsets" class="nav-tab<?php echo ($tab == 'designsets') ? ' nav-tab-active' : '';?>"><?php _e( 'Design Sets', "oxyultimate-woo" ); ?></a>

				<?php if( in_array( $user_id, $permission ) ) : ?>
					<a href="?page=ouwoo_menu&amp;tab=whitelabel" class="nav-tab<?php echo ($tab == 'whitelabel') ? ' nav-tab-active' : '';?>"><?php _e( 'White Label', "oxyultimate-woo" ); ?></a>
				<?php endif; ?>

				<a href="?page=ouwoo_menu&amp;tab=license" class="nav-tab<?php echo ($tab == 'license') ? ' nav-tab-active' : '';?>"><?php _e( 'License', "oxyultimate-woo" ); ?></a>
			</h2>

			 <?php if ( $tab === 'license' ) { ?>
					<div class="wrap ouwoo-options">
						<h2><?php _e( 'OxyUltimate Woo', "oxyultimate-woo" ); ?> v<?php echo OUWOO_VERSION; ?></h2>
						<form action='options.php' method='post' class="ouwoo-options-form" id="ouwoo-options-form">
							<?php
								settings_fields( 'ouwoo_activate_license' );
								do_settings_sections( 'ouwoo_activate_license' );
							?>
						</form>
					</div>
			<?php } elseif( $tab === 'designsets' ) { ?>
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
					self::ouwoo_white_label();
				} else {
					self::ouwoo_components_settings();
				}
			?>
		</div>
	<?php
	}

	static function ouwoo_components_settings() {
		$ouuc_nonce = wp_create_nonce( 'ouwoo-disable-unused-components' );

		$compsList = getAllOuWooComps();

		if( ! empty( $compsList ) ) {

			if ( is_network_admin() ) {
				// Update the site-wide option since we're in the network admin.
				$deactivated_components = get_site_option( '_ouwoo_disabled_components' );
				$active_components = get_site_option( '_ouwoo_active_components' );
			} else {
				$deactivated_components = get_option( '_ouwoo_disabled_components' );
				$active_components = get_option( '_ouwoo_active_components' );
			}
?>
		<h1 class="heading"><?php _e( 'Components Settings', "oxyultimate-woo" ); ?></h1>
		<p class="big-notice description">Selected components will be <strong style="border-bottom:3px solid #fff;">activated</strong> and will show on Oxygen Builder editor.</p>
		<div class="ouwoo-comp-wrap">
			<?php foreach( $compsList as $key => $component ) : ?>
				<div class="ouwoo-col ouwoo-acrd-item">
					<div class="ouwoo-acrd-btn">
						<input type="checkbox" name="<?php echo strtolower( $key ); ?>_comp" class="section-cb" value="<?php echo strtolower( $key ); ?>"/>
						<label for="<?php echo $key; ?> Components"><?php echo $key; ?></label>
					</div>
					<div class="ouwoo-acrd-content">
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
			<input type="hidden" name="ouwoo_nonce" value="<?php echo $ouuc_nonce; ?>" />
			
			<input type="hidden" name="disable_components" value="<?php echo ( ( (sizeof( (array) $deactivated_components ) - 1 ) <= 0 ) ? '' : join(",", (array) $deactivated_components)); ?>" />

			<input type="hidden" name="active_components" value="<?php echo ( ( (sizeof( (array) $active_components ) - 1 ) <= 0 ) ? '' : join(",", (array) $active_components)); ?>" />

			<div class="clear clearfix div-button"><a href="JavaScript: void(0);" onclick="JavaScript: activateComponents(); return false;" class="page-title-action button-primary">Save Changes</a><span class="spinner"></span></div>
			<div class="notice notice-info ouwoo-comp-notice" style="display: none;"><p><?php _e( 'Selected components are activated successfully.', "oxyultimate-woo"); ?></p></div>
		</div>
	<?php
		} else { ?>
			<h1 class="heading"><?php _e( 'Components Settings', "oxy-ultimate" ); ?></h1>
			<p class="big-notice description">
				At first you will activate the <strong style="border-bottom:3px solid #fff;">License Key</strong>.
			</p>
	<?php
		}
	}

	function ouwoo_active_components() {
		check_ajax_referer( 'ouwoo-disable-unused-components', 'security' );

		$active_components = $_POST['modules'];

		if( ! empty( $active_components ) ) {
			$components = explode(",", $active_components);
			$components = array_unique($components);

			if ( is_network_admin() ) {
				// Update the site-wide option since we're in the network admin.
				update_site_option( '_ouwoo_active_components', $components );
			} else {
				update_option( '_ouwoo_active_components', $components );
			}

			echo '200';
		} else {
			delete_option( '_ouwoo_active_components' );
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
	function ouwoo_admin_enqueue_scripts( $hook ) {
		if( $hook !== 'oxygen_page_ouwoo_menu' )
			return;

		wp_enqueue_style( 'ouwoo-admin-css', OUWOO_URL . 'assets/css/ouwoo-admin.css', array(), time() );
		wp_enqueue_script( 'ouwoo-admin-script', OUWOO_URL . 'assets/js/activate-plugin.js', array(), filemtime(OUWOO_DIR . 'assets/js/activate-plugin.js'), true );
	}

	public static function ouwoo_add_settings_link( $links, $file ) {

		if ( $file === 'oxyultimate-woo/oxyultimate-woo.php' && current_user_can( 'install_plugins' ) ) {
			if ( current_filter() === 'plugin_action_links' ) {
				$url = admin_url( 'admin.php?page=ouwoo_menu' );
			} else {
				$url = admin_url( '/network/admin.php?page=ouwoo_menu' );
			}

			$settings = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings' ) );
			array_unshift(
				$links,
				$settings
			);
		}

		return $links;
	}

	public static function ouwoo_add_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( $plugin_file == 'oxyultimate-woo/oxyultimate-woo.php' && current_user_can( 'install_plugins' ) ) {
			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=oxyultimate-woo&section=description&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( sprintf( __( 'More information about %s' ), $plugin_data['Name'] ) ),
				esc_attr( $plugin_data['Name'] ),
				__( 'View Details' )
			);

			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=oxyultimate-woo&section=changelog&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( sprintf( __( 'More information about %s' ), $plugin_data['Name'] ) ),
				esc_attr( $plugin_data['Name'] ),
				__( 'Changelog' )
			);
		}

		return $plugin_meta;
	}

	private static function ouwoo_white_label() {
		$plugin_name 	= 'placeholder="OxyUltimate Woo"';
		$plugin_uri 	= 'placeholder="https://oxyultimate.com"';
		$author_name 	= 'placeholder="Chinmoy Paul"';
		$author_uri 	= 'placeholder="https://paulchinmoy.com"';
		$menu_name 		= 'placeholder="OxyUltimate Woo"';
		//$menu_slug 		= 'placeholder="ouwoo_menu"';
		//$menuslug 		= 'ouwoo_menu';
		$plugin_desc 	= '';
		$tab_permission = 'placeholder="Enter user ID. Use comma for multiple users"';

		$ouwoowl 			= get_option('ouwoowl');
		if( $ouwoowl ) {
			$plugin_name 	= ! empty( $ouwoowl['plugin_name'] ) ? 'value="' . esc_html( $ouwoowl['plugin_name'] ) . '"' : $plugin_name;
			$plugin_uri 	= ! empty( $ouwoowl['plugin_uri'] ) ? 'value="' . esc_html( $ouwoowl['plugin_uri'] ) . '"' : $plugin_uri;
			$author_name 	= ! empty( $ouwoowl['author_name'] ) ? 'value="' . esc_html( $ouwoowl['author_name'] ) . '"' : $author_name;
			$author_uri 	= ! empty( $ouwoowl['author_uri'] ) ? 'value="' . esc_html( $ouwoowl['author_uri'] ) . '"' : $author_uri;
			$plugin_desc 	= ! empty( $ouwoowl['plugin_desc'] ) ? esc_html( $ouwoowl['plugin_desc'] ) : $plugin_desc;
			$menu_name 		= ! empty( $ouwoowl['menu_name'] ) ? 'value="' . esc_html( $ouwoowl['menu_name'] ) . '"' : $menu_name;
			//$menu_slug 		= ! empty( $ouwoowl['menu_slug'] ) ? 'value="' . esc_html( $ouwoowl['menu_slug'] ) . '"' : $menu_slug;
			//$menuslug 		= ! empty( $ouwoowl['menu_slug'] ) ? esc_html( $ouwoowl['menu_slug'] ) : $menuslug;
			$tab_permission = ! empty( $ouwoowl['tab_permission'] ) ? 'value="' . esc_html( $ouwoowl['tab_permission'] ) . '"' : $tab_permission;
		}

		$url = add_query_arg( 'tab', 'whitelabel', menu_page_url( 'ouwoo_menu', false ) );
	?>
		<h2><?php _e( 'White Label', 'oxyultimate-woo' ); ?></h2>
		<p>It gives you the ability to control and transform the appearance of the back-end.</p>
		<div style="background-color: #f7f7f7; border: 1px solid #ccd0d4; padding: 5px 20px 15px; max-width: 580px;">
			<form method="post" action="<?php echo $url; ?>">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Plugin Name', 'oxyultimate-woo' ); ?>
							</th>
							<td>
								<input id="plugin_name" name="ouwoowl[plugin_name]" type="text" class="regular-text" <?php echo $plugin_name; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Plugin URI', 'oxyultimate-woo' ); ?>
							</th>
							<td>
								<input id="plugin_uri" name="ouwoowl[plugin_uri]" type="url" class="regular-text" <?php echo $plugin_uri; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Author Name', 'oxyultimate-woo' ); ?>
							</th>
							<td>
								<input id="author_name" name="ouwoowl[author_name]" type="text" class="regular-text" <?php echo $author_name; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Author URI', 'oxyultimate-woo' ); ?>
							</th>
							<td>
								<input id="author_uri" name="ouwoowl[author_uri]" type="url" class="regular-text" <?php echo $author_uri; ?> />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Plugin Description', 'oxyultimate-woo' ); ?>
							</th>
							<td>
								<textarea id="plugin_desc" name="ouwoowl[plugin_desc]" class="large-text" cols="5" rows="8" ><?php echo $plugin_desc; ?></textarea>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Admin Menu Name', 'oxyultimate-woo' ); ?>
							</th>
							<td>
								<input id="menu_name" name="ouwoowl[menu_name]" type="text" class="regular-text" <?php echo $menu_name; ?> />
							</td>
						</tr>
						<!--<tr valign="top">
							<th scope="row" valign="top">
								<?php //_e( 'Admin Menu Link Slug', 'oxyultimate-woo' ); ?>
							</th>
							<td>
								<input id="menu_slug" name="ouwoowl[menu_slug]" type="text" class="regular-text" <?php //echo $menu_slug; ?> />
							</td>
						</tr>-->
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Permission', 'oxyultimate-woo' ); ?><br/>
								<lebel style="font-weight: normal; color: #999;"><?php _e( 'who can access this page', 'oxyultimate-woo' ); ?></lebel>
							</th>
							<td>
								<input id="tab_permission" name="ouwoowl[tab_permission]" type="text" class="regular-text" <?php echo $tab_permission; ?> />
							</td>
						</tr>
					</tbody>
				</table>
				<?php wp_nonce_field( 'ouwoo_nonce_action', 'ouwoo_nonce_field' ); ?>
				<input type="hidden" name="action" value="save_data" />
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

	private function ouwoo_save_white_label_data() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( isset( $_POST['action'] ) && $_POST['action'] == "save_data" ){
			if( isset( $_POST['ouwoowl'] ) ) {
				update_option('ouwoowl', $_POST['ouwoowl']);
			} else {
				delete_option('ouwoowl');
			}

			printf('<div class="notice notice-info is-dismissible"><p>%s</p></div>', __('Settings saved successfully.', 'oxyultimate-woo'));
		}
	}
}