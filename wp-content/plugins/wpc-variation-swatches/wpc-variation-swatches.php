<?php
/*
Plugin Name: WPC Variation Swatches for WooCommerce
Plugin URI: https://wpclever.net/
Description: WPC Variation Swatches is a beautiful color, image, radio and buttons variation swatches for WooCommerce product attributes.
Version: 4.0.3
Author: WPClever
Author URI: https://wpclever.net
Text Domain: wpc-variation-swatches
Domain Path: /languages/
Requires Plugins: woocommerce
Requires at least: 4.0
Tested up to: 6.4
WC requires at least: 3.0
WC tested up to: 8.7
*/

defined( 'ABSPATH' ) || exit;

! defined( 'WPCVS_VERSION' ) && define( 'WPCVS_VERSION', '4.0.3' );
! defined( 'WPCVS_LITE' ) && define( 'WPCVS_LITE', __FILE__ );
! defined( 'WPCVS_FILE' ) && define( 'WPCVS_FILE', __FILE__ );
! defined( 'WPCVS_URI' ) && define( 'WPCVS_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WPCVS_DIR' ) && define( 'WPCVS_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WPCVS_REVIEWS' ) && define( 'WPCVS_REVIEWS', 'https://wordpress.org/support/plugin/wpc-variation-swatches/reviews/?filter=5' );
! defined( 'WPCVS_CHANGELOG' ) && define( 'WPCVS_CHANGELOG', 'https://wordpress.org/plugins/wpc-variation-swatches/#developers' );
! defined( 'WPCVS_DISCUSSION' ) && define( 'WPCVS_DISCUSSION', 'https://wordpress.org/support/plugin/wpc-variation-swatches' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WPCVS_URI );

include 'includes/dashboard/wpc-dashboard.php';
include 'includes/kit/wpc-kit.php';
include 'includes/hpos.php';

if ( ! function_exists( 'wpcvs_init' ) ) {
	add_action( 'plugins_loaded', 'wpcvs_init', 11 );

	function wpcvs_init() {
		// load text-domain
		load_plugin_textdomain( 'wpc-variation-swatches', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'wpcvs_notice_wc' );

			return null;
		}

		if ( ! class_exists( 'WPCleverWpcvs' ) && class_exists( 'WC_Product' ) ) {
			class WPCleverWpcvs {
				protected static $settings = [];
				protected static $localization = [];
				protected static $instance = null;

				public static function instance() {
					if ( is_null( self::$instance ) ) {
						self::$instance = new self();
					}

					return self::$instance;
				}

				function __construct() {
					self::$settings     = (array) get_option( 'wpcvs_settings', [] );
					self::$localization = (array) get_option( 'wpcvs_localization', [] );

					add_action( 'init', [ $this, 'init' ] );
					add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
					add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

					// add field for attributes
					add_filter( 'product_attributes_type_selector', [ $this, 'type_selector' ] );

					// add groups
					add_action( 'woocommerce_after_add_attribute_fields', [ $this, 'show_groups' ] );
					add_action( 'woocommerce_after_edit_attribute_fields', [ $this, 'show_groups' ] );
					add_action( 'woocommerce_attribute_added', [ $this, 'save_groups' ] );
					add_action( 'woocommerce_attribute_updated', [ $this, 'save_groups' ] );

					$attribute_taxonomies = wc_get_attribute_taxonomies();

					foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
						add_action( 'pa_' . $attribute_taxonomy->attribute_name . '_add_form_fields', [
							$this,
							'show_field'
						] );
						add_action( 'pa_' . $attribute_taxonomy->attribute_name . '_edit_form_fields', [
							$this,
							'show_field'
						] );
						add_action( 'create_pa_' . $attribute_taxonomy->attribute_name, [ $this, 'save_field' ] );
						add_action( 'edited_pa_' . $attribute_taxonomy->attribute_name, [ $this, 'save_field' ] );
						add_filter( "manage_edit-pa_{$attribute_taxonomy->attribute_name}_columns", [
							$this,
							'custom_columns'
						] );
						add_filter( "manage_pa_{$attribute_taxonomy->attribute_name}_custom_column", [
							$this,
							'custom_columns_content'
						], 10, 3 );
					}

					add_filter( 'woocommerce_post_class', [ $this, 'post_class' ], 99 );
					add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [
						$this,
						'variation_attribute_options_html'
					], 199, 2 );

					// settings page
					add_action( 'admin_init', [ $this, 'register_settings' ] );
					add_action( 'admin_menu', [ $this, 'admin_menu' ] );

					// settings link
					add_filter( 'plugin_action_links', [ $this, 'wpcvs_action_links' ], 10, 2 );
					add_filter( 'plugin_row_meta', [ $this, 'wpcvs_row_meta' ], 10, 2 );

					// archive page
					if ( self::get_setting( 'archive_enable', 'no' ) === 'yes' ) {
						if ( self::get_setting( 'archive_position', 'before' ) === 'before' ) {
							add_action( 'woocommerce_after_shop_loop_item', [ $this, 'archive' ], 9 );
						} elseif ( self::get_setting( 'archive_position', 'before' ) === 'after' ) {
							add_action( 'woocommerce_after_shop_loop_item', [ $this, 'archive' ], 11 );
						}
					}

					// stacked template
					if ( self::get_setting( 'single_layout', 'default' ) === 'stacked' ) {
						add_filter( 'wc_get_template', [ $this, 'stacked_template' ], 99, 2 );
					}

					// ajax add to cart
					add_action( 'wp_ajax_wpcvs_add_to_cart', [ $this, 'ajax_add_to_cart' ] );
					add_action( 'wp_ajax_nopriv_wpcvs_add_to_cart', [ $this, 'ajax_add_to_cart' ] );

					// variation
					add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'add_fields' ], 10, 3 );
					add_action( 'woocommerce_save_product_variation', [ $this, 'save_fields' ], 10, 2 );
					add_filter( 'woocommerce_available_variation', [ $this, 'available_variation' ], 100, 3 );

					// WPC Smart Messages
					add_filter( 'wpcsm_locations', [ $this, 'wpcsm_locations' ] );
				}

				public static function get_settings() {
					return apply_filters( 'wpcvs_get_settings', self::$settings );
				}

				public static function get_setting( $name, $default = false ) {
					if ( ! empty( self::$settings ) && isset( self::$settings[ $name ] ) ) {
						$setting = self::$settings[ $name ];
					} else {
						$setting = get_option( 'wpcvs_' . $name, $default );
					}

					return apply_filters( 'wpcvs_get_setting', $setting, $name, $default );
				}

				public static function localization( $key = '', $default = '' ) {
					$str = '';

					if ( ! empty( $key ) && ! empty( self::$localization[ $key ] ) ) {
						$str = self::$localization[ $key ];
					} elseif ( ! empty( $default ) ) {
						$str = $default;
					}

					return apply_filters( 'wpcvs_localization_' . $key, $str );
				}

				function init() {
					add_shortcode( 'wpcvs_archive', [ $this, 'shortcode_archive' ] );
				}

				function shortcode_archive( $attrs ) {
					$attrs = shortcode_atts( [
						'id'   => null,
						'hide' => '',
						'link' => 'false'
					], $attrs, 'wpcvs_archive' );

					ob_start();
					$this->archive( $attrs['id'], $attrs['hide'], wc_string_to_bool( $attrs['link'] ) );

					return ob_get_clean();
				}

				function ajax_add_to_cart() {
					if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wpcvs-security' ) ) {
						die( 'Permissions check failed!' );
					}

					$product_id   = (int) $_POST['product_id'];
					$variation_id = (int) $_POST['variation_id'];
					$quantity     = (float) $_POST['quantity'];
					$variation    = (array) json_decode( stripslashes( $_POST['attributes'] ) );

					if ( $product_id && $variation_id ) {
						$item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );

						if ( ! empty( $item_key ) ) {
							WC_AJAX::get_refreshed_fragments();
						}
					}

					$data = [
						'error'       => true,
						'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
					];

					wp_send_json( $data );
				}

				function add_fields( $loop, $variation_data, $variation ) {
					echo '<div class="form-row form-row-full wpcvs-variation-settings">';
					echo '<label>' . esc_html__( 'WPC Variation Swatches', 'wpc-variation-swatches' ) . '</label>';
					echo '<div class="wpcvs-variation-wrap">';

					woocommerce_wp_text_input( [
						'id'    => 'wpcvs_name_' . $variation->ID,
						'label' => esc_html__( 'Custom name', 'wpc-variation-swatches' ),
						'name'  => 'wpcvs_name[' . $variation->ID . ']',
						'value' => get_post_meta( $variation->ID, 'wpcvs_name', true ) ?: '',
					] );

					echo '</div></div>';
				}

				function save_fields( $post_id ) {
					if ( isset( $_POST['wpcvs_name'][ $post_id ] ) ) {
						update_post_meta( $post_id, 'wpcvs_name', sanitize_text_field( $_POST['wpcvs_name'][ $post_id ] ) );
					}
				}

				function available_variation( $available, $variable, $variation ) {
					$thumbnail_id   = $available['image_id'];
					$thumbnail_size = apply_filters( 'woocommerce_thumbnail_size', 'woocommerce_thumbnail' );
					$thumbnail_src  = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size );

					if ( $thumbnail_id ) {
						$available['image']['wpcvs_src']    = $thumbnail_src[0];
						$available['image']['wpcvs_srcset'] = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $thumbnail_size ) : false;
						$available['image']['wpcvs_sizes']  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $thumbnail_size ) : false;
					}

					$available['wpcvs_name'] = get_post_meta( $variation->get_id(), 'wpcvs_name', true ) ?: '';
					$available['wpcvs_desc'] = $variation->get_description();

					return $available;
				}

				function scripts() {
					if ( self::get_setting( 'tooltip_position', 'top' ) !== 'no' ) {
						if ( self::get_setting( 'tooltip_library', 'tippy' ) === 'hint' ) {
							wp_enqueue_style( 'hint', WPCVS_URI . 'assets/libs/hint/hint.css' );
						}

						if ( self::get_setting( 'tooltip_library', 'tippy' ) === 'tippy' ) {
							wp_enqueue_script( 'popper', WPCVS_URI . 'assets/libs/tippy/popper.min.js', [ 'jquery' ], WPCVS_VERSION );
							wp_enqueue_script( 'tippy', WPCVS_URI . 'assets/libs/tippy/tippy-bundle.umd.min.js', [ 'jquery' ], WPCVS_VERSION );
						}
					}

					if ( self::get_setting( 'archive_enable', 'no' ) === 'yes' ) {
						wp_enqueue_script( 'wc-add-to-cart-variation' );
					}

					wp_enqueue_style( 'wpcvs-frontend', WPCVS_URI . 'assets/css/frontend.css', [], WPCVS_VERSION );
					wp_enqueue_script( 'wpcvs-frontend', WPCVS_URI . 'assets/js/frontend.js', [ 'jquery' ], WPCVS_VERSION, true );

					// single selector
					if ( empty( $single_product = apply_filters( 'wpcvs_single_product_selector', '' ) ) ) {
						$single_product = self::get_setting( 'single_product' );
					}

					if ( empty( $single_name = apply_filters( 'wpcvs_single_name_selector', '' ) ) ) {
						$single_name = self::get_setting( 'single_name' );
					}

					if ( empty( $single_price = apply_filters( 'wpcvs_single_price_selector', '' ) ) ) {
						$single_price = self::get_setting( 'single_price' );
					}

					if ( empty( $single_desc = apply_filters( 'wpcvs_single_desc_selector', '' ) ) ) {
						$single_desc = self::get_setting( 'single_desc' );
					}

					// archive selector
					if ( empty( $archive_product = apply_filters( 'wpcvs_archive_product_selector', '' ) ) ) {
						$archive_product = self::get_setting( 'archive_product' );
					}

					if ( empty( $archive_image = apply_filters( 'wpcvs_archive_image_selector', '' ) ) ) {
						$archive_image = self::get_setting( 'archive_image' );
					}

					if ( empty( $archive_name = apply_filters( 'wpcvs_archive_name_selector', '' ) ) ) {
						$archive_name = self::get_setting( 'archive_name' );
					}

					if ( empty( $archive_price = apply_filters( 'wpcvs_archive_price_selector', '' ) ) ) {
						$archive_price = self::get_setting( 'archive_price' );
					}

					if ( empty( $archive_atc = apply_filters( 'wpcvs_archive_atc_selector', '' ) ) ) {
						$archive_atc = self::get_setting( 'archive_atc' );
					}

					if ( empty( $archive_atc_text = apply_filters( 'wpcvs_archive_atc_text_selector', '' ) ) ) {
						$archive_atc_text = self::get_setting( 'archive_atc_text' );
					}

					wp_localize_script( 'wpcvs-frontend', 'wpcvs_vars', apply_filters( 'wpcvs_vars', [
							'ajax_url'           => admin_url( 'admin-ajax.php' ),
							'nonce'              => wp_create_nonce( 'wpcvs-security' ),
							'tooltip_library'    => self::get_setting( 'tooltip_library', 'tippy' ),
							'second_click'       => self::get_setting( 'second_click', 'no' ),
							'is_product'         => apply_filters( 'wpcvs_is_product', is_product() ? 'yes' : 'no' ),
							'single_change_url'  => self::get_setting( 'single_change_url', 'no' ),
							'single_replacement' => self::get_setting( 'single_replacement', 'enable' ),
							'single_product'     => ! empty( $single_product ) ? esc_attr( $single_product ) : '.product.product-type-variable',
							'single_name'        => ! empty( $single_name ) ? esc_attr( $single_name ) : '.summary > .product_title',
							'single_price'       => ! empty( $single_price ) ? esc_attr( $single_price ) : '.summary > .price',
							'single_desc'        => ! empty( $single_desc ) ? esc_attr( $single_desc ) : '.summary > .woocommerce-product-details__short-description',
							'archive_enable'     => self::get_setting( 'archive_enable', 'no' ),
							'archive_product'    => ! empty( $archive_product ) ? esc_attr( $archive_product ) : '.product',
							'archive_image'      => ! empty( $archive_image ) ? esc_attr( $archive_image ) : '.attachment-woocommerce_thumbnail',
							'archive_name'       => ! empty( $archive_name ) ? esc_attr( $archive_name ) : '.woocommerce-loop-product__title',
							'archive_price'      => ! empty( $archive_price ) ? esc_attr( $archive_price ) : '.price',
							'archive_atc'        => ! empty( $archive_atc ) ? esc_attr( $archive_atc ) : '.add_to_cart_button',
							'archive_atc_text'   => ! empty( $archive_atc_text ) ? esc_attr( $archive_atc_text ) : '.add_to_cart_button',
							'add_to_cart'        => apply_filters( 'wpcvs_add_to_cart', self::localization( 'add_to_cart', esc_html__( 'Add to cart', 'wpc-variation-swatches' ) ) ),
							'select_options'     => apply_filters( 'wpcvs_select_options', self::localization( 'select_options', esc_html__( 'Select options', 'wpc-variation-swatches' ) ) ),
							'view_cart'          => apply_filters( 'wpcvs_view_cart', '<a href="' . wc_get_cart_url() . '" class="added_to_cart wc-forward" title="' . esc_attr( self::localization( 'view_cart', esc_html__( 'View cart', 'wpc-variation-swatches' ) ) ) . '">' . esc_html( self::localization( 'view_cart', esc_html__( 'View cart', 'wpc-variation-swatches' ) ) ) . '</a>' ),
						] )
					);
				}

				function admin_scripts() {
					wp_enqueue_style( 'wpcvs-backend', WPCVS_URI . 'assets/css/backend.css', [ 'woocommerce_admin_styles' ], WPCVS_VERSION );
					wp_enqueue_script( 'wpcvs-backend', WPCVS_URI . 'assets/js/backend.js', [
						'jquery',
						'wp-color-picker',
						'wc-enhanced-select',
						'selectWoo',
					], WPCVS_VERSION, true );
					wp_localize_script( 'wpcvs-backend', 'wpcvs_vars', [
						'placeholder_img' => wc_placeholder_img_src()
					] );
				}

				function register_settings() {
					// settings
					register_setting( 'wpcvs_settings', 'wpcvs_settings' );
					// localization
					register_setting( 'wpcvs_localization', 'wpcvs_localization' );
				}

				function admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Variation Swatches', 'wpc-variation-swatches' ), esc_html__( 'Variation Swatches', 'wpc-variation-swatches' ), 'manage_options', 'wpclever-wpcvs', [
						$this,
						'admin_menu_content'
					] );
				}

				function admin_menu_content() {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'settings';
					?>
                    <div class="wpclever_settings_page wrap">
                        <h1 class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Variation Swatches', 'wpc-variation-swatches' ) . ' ' . esc_html( WPCVS_VERSION ); ?></h1>
                        <div class="wpclever_settings_page_desc about-text">
                            <p>
								<?php printf( /* translators: stars */ esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'wpc-variation-swatches' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
                                <br/>
                                <a href="<?php echo esc_url( WPCVS_REVIEWS ); ?>" target="_blank"><?php esc_html_e( 'Reviews', 'wpc-variation-swatches' ); ?></a> |
                                <a href="<?php echo esc_url( WPCVS_CHANGELOG ); ?>" target="_blank"><?php esc_html_e( 'Changelog', 'wpc-variation-swatches' ); ?></a> |
                                <a href="<?php echo esc_url( WPCVS_DISCUSSION ); ?>" target="_blank"><?php esc_html_e( 'Discussion', 'wpc-variation-swatches' ); ?></a>
                            </p>
                        </div>
						<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
                            <div class="notice notice-success is-dismissible">
                                <p><?php esc_html_e( 'Settings updated.', 'wpc-variation-swatches' ); ?></p>
                            </div>
						<?php } ?>
                        <div class="wpclever_settings_page_nav">
                            <h2 class="nav-tab-wrapper">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpcvs&tab=settings' ) ); ?>" class="<?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Settings', 'wpc-variation-swatches' ); ?>
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-wpcvs&tab=localization' ) ); ?>" class="<?php echo esc_attr( $active_tab === 'localization' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Localization', 'wpc-variation-swatches' ); ?>
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-kit' ) ); ?>" class="nav-tab">
									<?php esc_html_e( 'Essential Kit', 'wpc-variation-swatches' ); ?>
                                </a>
                            </h2>
                        </div>
                        <div class="wpclever_settings_page_content">
							<?php if ( $active_tab === 'settings' ) {
								$button_default     = self::get_setting( 'button_default', 'no' );
								$second_click       = self::get_setting( 'second_click', 'no' );
								$tooltip_position   = self::get_setting( 'tooltip_position', 'top' );
								$tooltip_library    = self::get_setting( 'tooltip_library', 'tippy' );
								$style              = self::get_setting( 'style', 'square' );
								$group              = self::get_setting( 'group', 'yes' );
								$archive_enable     = self::get_setting( 'archive_enable', 'no' );
								$archive_position   = self::get_setting( 'archive_position', 'before' );
								$archive_limit      = self::get_setting( 'archive_limit', '10' );
								$single_layout      = self::get_setting( 'single_layout', 'default' );
								$single_change_url  = self::get_setting( 'single_change_url', 'no' );
								$single_replacement = self::get_setting( 'single_replacement', 'enable' );
								?>
                                <form method="post" action="options.php">
                                    <table class="form-table">
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'General', 'wpc-variation-swatches' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Button swatch by default', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[button_default]">
                                                    <option value="yes" <?php selected( $button_default, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="no" <?php selected( $button_default, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-variation-swatches' ); ?></option>
                                                </select> <span class="description">
                                                    <?php esc_html_e( 'Turn the default type to button type.', 'wpc-variation-swatches' ); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Enable second click to undo?', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[second_click]">
                                                    <option value="yes" <?php selected( $second_click, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="no" <?php selected( $second_click, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-variation-swatches' ); ?></option>
                                                </select> <span class="description">
                                                    <?php esc_html_e( 'Enable/disable click again to undo the selection on current attribute.', 'wpc-variation-swatches' ); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Tooltip library', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[tooltip_library]">
                                                    <option value="hint" <?php selected( $tooltip_library, 'hint' ); ?>><?php esc_html_e( 'Hint.css', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="tippy" <?php selected( $tooltip_library, 'tippy' ); ?>><?php esc_html_e( 'Tippy.js', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="none" <?php selected( $tooltip_library, 'none' ); ?>><?php esc_html_e( 'None (Disable)', 'wpc-variation-swatches' ); ?></option>
                                                </select>
                                                <span class="description">Read more about <a href="https://kushagra.dev/lab/hint/" target="_blank">Hint.css</a> and <a href="https://atomiks.github.io/tippyjs/v6/getting-started/" target="_blank">Tippy.js</a>. Use Tippy.js if you want to show the attribute's name, description, image or color on the tooltip.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Tooltip position', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[tooltip_position]">
                                                    <option value="top" <?php selected( $tooltip_position, 'top' ); ?>><?php esc_html_e( 'Top', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="right" <?php selected( $tooltip_position, 'right' ); ?>><?php esc_html_e( 'Right', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="bottom" <?php selected( $tooltip_position, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="left" <?php selected( $tooltip_position, 'left' ); ?>><?php esc_html_e( 'Left', 'wpc-variation-swatches' ); ?></option>
                                                </select> <span class="description">For Hint.css only.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Style', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[style]">
                                                    <option value="square" <?php selected( $style, 'square' ); ?>><?php esc_html_e( 'Square', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="rounded" <?php selected( $style, 'rounded' ); ?>><?php esc_html_e( 'Rounded', 'wpc-variation-swatches' ); ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Term groups', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[group]">
                                                    <option value="yes" <?php selected( $group, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="no" <?php selected( $group, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-variation-swatches' ); ?></option>
                                                </select>
                                                <span class="description">Enable/disable using group for attribute terms.</span>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'Single Product', 'wpc-variation-swatches' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Layout', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[single_layout]">
                                                    <option value="default" <?php selected( $single_layout, 'default' ); ?>><?php esc_html_e( 'Table (default)', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="stacked" <?php selected( $single_layout, 'stacked' ); ?>><?php esc_html_e( 'Stacked', 'wpc-variation-swatches' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Choose the layout for swatches on the single product page.', 'wpc-variation-swatches' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Change URL', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[single_change_url]">
                                                    <option value="yes" <?php selected( $single_change_url, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="no" <?php selected( $single_change_url, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-variation-swatches' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Change the URL on address bar when choosing attribute term.', 'wpc-variation-swatches' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Replacement', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[single_replacement]">
                                                    <option value="enable" <?php selected( $single_replacement, 'enable' ); ?>><?php esc_html_e( 'Enable', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="disable" <?php selected( $single_replacement, 'disable' ); ?>><?php esc_html_e( 'Disable', 'wpc-variation-swatches' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Enable it to replace the product name/price/description when choosing variations.', 'wpc-variation-swatches' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Selector', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <ul class="wpcvs-selectors">
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product wrapper selector. Default: %s', 'wpc-variation-swatches' ), '<code>.product.product-type-variable</code>' ); ?></label>
														<?php $single_product = apply_filters( 'wpcvs_single_product_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[single_product]" value="<?php echo esc_attr( ! empty( $single_product ) ? $single_product : self::get_setting( 'single_product' ) ); ?>"
															<?php echo( ! empty( $single_product ) ? 'readonly' : 'placeholder=".product.product-type-variable"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product name selector. Default: %s', 'wpc-variation-swatches' ), '<code>.summary > .product_title</code>' ); ?></label>
														<?php $single_name = apply_filters( 'wpcvs_single_name_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[single_name]" value="<?php echo esc_attr( ! empty( $single_name ) ? $single_name : self::get_setting( 'single_name' ) ); ?>"
															<?php echo( ! empty( $single_name ) ? 'readonly' : 'placeholder=".summary > .product_title"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product price selector. Default: %s', 'wpc-variation-swatches' ), '<code>.summary > .price</code>' ); ?></label>
														<?php $single_price = apply_filters( 'wpcvs_single_price_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[single_price]" value="<?php echo esc_attr( ! empty( $single_price ) ? $single_price : self::get_setting( 'single_price' ) ); ?>"
															<?php echo( ! empty( $single_price ) ? 'readonly' : 'placeholder=".summary > .price"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product short description selector. Default: %s', 'wpc-variation-swatches' ), '<code>.summary > .woocommerce-product-details__short-description</code>' ); ?></label>
														<?php $single_desc = apply_filters( 'wpcvs_single_desc_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[single_desc]" value="<?php echo esc_attr( ! empty( $single_desc ) ? $single_desc : self::get_setting( 'single_desc' ) ); ?>"
															<?php echo( ! empty( $single_desc ) ? 'readonly' : 'placeholder=".summary > .woocommerce-product-details__short-description"' ); ?>/>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2">
												<?php esc_html_e( 'Product Archive', 'wpc-variation-swatches' ); ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Enable', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[archive_enable]">
                                                    <option value="yes" <?php selected( $archive_enable, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="no" <?php selected( $archive_enable, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-variation-swatches' ); ?></option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Enable swatches for product archive page.', 'wpc-variation-swatches' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Position', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <select name="wpcvs_settings[archive_position]">
                                                    <option value="before" <?php selected( $archive_position, 'before' ); ?>><?php esc_html_e( 'Before add to cart button', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="after" <?php selected( $archive_position, 'after' ); ?>><?php esc_html_e( 'After add to cart button', 'wpc-variation-swatches' ); ?></option>
                                                    <option value="none" <?php selected( $archive_position, 'none' ); ?>><?php esc_html_e( 'None', 'wpc-variation-swatches' ); ?></option>
                                                </select> <span class="description">
                                                    <?php printf( /* translators: shortcode */ esc_html__( 'Swatches position on product archive page. You also can use the shortcode: %s', 'wpc-variation-swatches' ), '<code>[wpcvs_archive]</code>' ); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Limit', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <input type="number" min="0" max="500" name="wpcvs_settings[archive_limit]" value="<?php echo esc_attr( $archive_limit ); ?>"/>
                                                <span class="description">
													<?php esc_html_e( 'Maximum terms of each attribute will be shown on product archive page.', 'wpc-variation-swatches' ); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Selector', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <ul class="wpcvs-selectors">
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product wrapper selector. Default: %s', 'wpc-variation-swatches' ), '<code>.product</code>' ); ?></label>
														<?php $archive_product = apply_filters( 'wpcvs_archive_product_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[archive_product]" value="<?php echo esc_attr( ! empty( $archive_product ) ? $archive_product : self::get_setting( 'archive_product' ) ); ?>"
															<?php echo( ! empty( $archive_product ) ? 'readonly' : 'placeholder=".product"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product image selector. Default: %s', 'wpc-variation-swatches' ), '<code>.attachment-woocommerce_thumbnail</code>' ); ?></label>
														<?php $archive_image = apply_filters( 'wpcvs_archive_image_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[archive_image]" value="<?php echo esc_attr( ! empty( $archive_image ) ? $archive_image : self::get_setting( 'archive_image' ) ); ?>"
															<?php echo( ! empty( $archive_image ) ? 'readonly' : 'placeholder=".attachment-woocommerce_thumbnail"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product name selector. Default: %s', 'wpc-variation-swatches' ), '<code>.woocommerce-loop-product__title</code>' ); ?></label>
														<?php $archive_name = apply_filters( 'wpcvs_archive_name_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[archive_name]" value="<?php echo esc_attr( ! empty( $archive_name ) ? $archive_name : self::get_setting( 'archive_name' ) ); ?>"
															<?php echo( ! empty( $archive_name ) ? 'readonly' : 'placeholder=".woocommerce-loop-product__title"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Product price selector. Default: %s', 'wpc-variation-swatches' ), '<code>.price</code>' ); ?></label>
														<?php $archive_price = apply_filters( 'wpcvs_archive_price_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[archive_price]" value="<?php echo esc_attr( ! empty( $archive_price ) ? $archive_price : self::get_setting( 'archive_price' ) ); ?>"
															<?php echo( ! empty( $archive_price ) ? 'readonly' : 'placeholder=".price"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Add to cart button selector. Default: %s', 'wpc-variation-swatches' ), '<code>.add_to_cart_button</code>' ); ?></label>
														<?php $archive_atc = apply_filters( 'wpcvs_archive_atc_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[archive_atc]" value="<?php echo esc_attr( ! empty( $archive_atc ) ? $archive_atc : self::get_setting( 'archive_atc' ) ); ?>"
															<?php echo( ! empty( $archive_atc ) ? 'readonly' : 'placeholder=".add_to_cart_button"' ); ?>/>
                                                    </li>
                                                    <li>
                                                        <label><?php printf( /* translators: selector */ esc_html__( 'Add to cart button text selector. Default: %s', 'wpc-variation-swatches' ), '<code>.add_to_cart_button</code>' ); ?></label>
														<?php $archive_atc_text = apply_filters( 'wpcvs_archive_atc_text_selector', '' ); ?>
                                                        <input type="text" class="text large-text" name="wpcvs_settings[archive_atc_text]" value="<?php echo esc_attr( ! empty( $archive_atc_text ) ? $archive_atc_text : self::get_setting( 'archive_atc_text' ) ); ?>"
															<?php echo( ! empty( $archive_atc_text ) ? 'readonly' : 'placeholder=".add_to_cart_button"' ); ?>/>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th colspan="2"><?php esc_html_e( 'Suggestion', 'wpc-variation-swatches' ); ?></th>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                To display custom engaging real-time messages on any wished positions, please install
                                                <a href="https://wordpress.org/plugins/wpc-smart-messages/" target="_blank">WPC Smart Messages</a> plugin. It's free!
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                Wanna save your precious time working on variations? Try our brand-new free plugin
                                                <a href="https://wordpress.org/plugins/wpc-variation-bulk-editor/" target="_blank">WPC Variation Bulk Editor</a> and
                                                <a href="https://wordpress.org/plugins/wpc-variation-duplicator/" target="_blank">WPC Variation Duplicator</a>.
                                            </td>
                                        </tr>
                                        <tr class="submit">
                                            <th colspan="2">
												<?php settings_fields( 'wpcvs_settings' ); ?><?php submit_button(); ?>
                                            </th>
                                        </tr>
                                    </table>
                                </form>
							<?php } elseif ( $active_tab === 'localization' ) { ?>
                                <form method="post" action="options.php">
                                    <table class="form-table">
                                        <tr class="heading">
                                            <th scope="row"><?php esc_html_e( 'General', 'wpc-variation-swatches' ); ?></th>
                                            <td>
												<?php esc_html_e( 'Leave blank to use the default text and its equivalent translation in multiple languages.', 'wpc-variation-swatches' ); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Add to cart', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text" name="wpcvs_localization[add_to_cart]" value="<?php echo esc_attr( self::localization( 'add_to_cart' ) ); ?>" placeholder="<?php esc_attr_e( 'Add to cart', 'wpc-variation-swatches' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Select options', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text" name="wpcvs_localization[select_options]" value="<?php echo esc_attr( self::localization( 'select_options' ) ); ?>" placeholder="<?php esc_attr_e( 'Select options', 'wpc-variation-swatches' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'View cart', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text" name="wpcvs_localization[view_cart]" value="<?php echo esc_attr( self::localization( 'view_cart' ) ); ?>" placeholder="<?php esc_attr_e( 'View cart', 'wpc-variation-swatches' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'More', 'wpc-variation-swatches' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text" name="wpcvs_localization[more]" value="<?php echo esc_attr( self::localization( 'more' ) ); ?>" placeholder="<?php /* translators: count */
												esc_attr_e( '+%d More', 'wpc-variation-swatches' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr class="submit">
                                            <th colspan="2">
												<?php settings_fields( 'wpcvs_localization' ); ?><?php submit_button(); ?>
                                            </th>
                                        </tr>
                                    </table>
                                </form>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}

				function wpcvs_action_links( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$settings = '<a href="' . esc_url( admin_url( 'admin.php?page=wpclever-wpcvs&tab=settings' ) ) . '">' . esc_html__( 'Settings', 'wpc-variation-swatches' ) . '</a>';
						array_unshift( $links, $settings );
					}

					return (array) $links;
				}

				function wpcvs_row_meta( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$row_meta = [
							'support' => '<a href="' . esc_url( WPCVS_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-variation-swatches' ) . '</a>',
						];

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function type_selector( $types ) {
					global $pagenow;

					if ( ( $pagenow === 'post-new.php' ) || ( $pagenow === 'post.php' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
						return $types;
					} else {
						$types['select'] = esc_html__( 'Select', 'wpc-variation-swatches' );
						$types['button'] = esc_html__( 'Button', 'wpc-variation-swatches' );
						$types['color']  = esc_html__( 'Color', 'wpc-variation-swatches' );
						$types['image']  = esc_html__( 'Image', 'wpc-variation-swatches' );
						$types['radio']  = esc_html__( 'Radio', 'wpc-variation-swatches' );

						return $types;
					}
				}

				function show_groups() {
					if ( ! empty( $_GET['edit'] ) ) {
						$id         = absint( $_GET['edit'] );
						$wrap_start = '<tr class="form-field wpcvs-form-field"><th><label>';
						$wrap_mid   = '</label></th><td>';
						$wrap_end   = '</td></tr>';
					} else {
						$id         = 0;
						$wrap_start = '<div class="form-field wpcvs-form-field"><label>';
						$wrap_mid   = '</label>';
						$wrap_end   = '</div>';
					}

					echo $wrap_start . esc_html__( 'Term groups', 'wpc-variation-swatches' );
					echo $wrap_mid;
					echo '<select name="wpcvs_groups[]" class="wpcvs_groups_selector" multiple>';

					if ( $id && ( $groups = get_option( 'wpcvs_groups_' . $id ) ) ) {
						foreach ( (array) $groups as $group ) {
							echo '<option value="' . esc_attr( $group ) . '" selected>' . esc_html( $group ) . '</option>';
						}
					}

					echo '</select>';
					echo '<p class="description">' . esc_html__( 'Fill in group labels then press Enter or select from the dropdown.', 'wpc-variation-swatches' ) . '</p>';
					echo $wrap_end;
				}

				function save_groups( $id ) {
					if ( ! empty( $_REQUEST['wpcvs_groups'] ) ) {
						update_option( 'wpcvs_groups_' . $id, (array) $_REQUEST['wpcvs_groups'] );
					} else {
						delete_option( 'wpcvs_groups_' . $id );
					}
				}

				function show_field( $term_or_tax ) {
					if ( is_object( $term_or_tax ) ) {
						// is term
						$term_id    = $term_or_tax->term_id;
						$attr_id    = wc_attribute_taxonomy_id_by_name( $term_or_tax->taxonomy );
						$attr       = wc_get_attribute( $attr_id );
						$wrap_start = '<tr class="form-field wpcvs-form-field"><th><label>';
						$wrap_mid   = '</label></th><td>';
						$wrap_end   = '</td></tr>';
					} else {
						// is taxonomy
						$term_id    = 0;
						$attr_id    = wc_attribute_taxonomy_id_by_name( $term_or_tax );
						$attr       = wc_get_attribute( $attr_id );
						$wrap_start = '<div class="form-field wpcvs-form-field"><label>';
						$wrap_mid   = '</label>';
						$wrap_end   = '</div>';
					}

					// group
					$wpcvs_group = get_term_meta( $term_id, 'wpcvs_group', true );
					echo $wrap_start . esc_html__( 'Group', 'wpc-variation-swatches' ) . $wrap_mid;

					if ( $groups = get_option( 'wpcvs_groups_' . $attr_id ) ) {
						echo '<select id="wpcvs_group" name="wpcvs_group">';
						echo '<option value="">' . esc_html__( 'Select a group', 'wpc-variation-swatches' ) . '</option>';

						foreach ( (array) $groups as $group ) {
							echo '<option value="' . esc_attr( $group ) . '" ' . selected( esc_attr( $group ), esc_attr( $wpcvs_group ), false ) . '>' . esc_html( $group ) . '</option>';
						}

						echo '</select>';
					}

					echo $wrap_end;

					$wpcvs_tooltip = get_term_meta( $term_id, 'wpcvs_tooltip', true );

					switch ( $attr->type ) {
						case 'button':
							$wpcvs_val = get_term_meta( $term_id, 'wpcvs_button', true );
							echo $wrap_start . esc_html__( 'Button', 'wpc-variation-swatches' ) . $wrap_mid . '<input id="wpcvs_button" name="wpcvs_button" value="' . esc_attr( $wpcvs_val ) . '" type="text"/>' . $wrap_end;
							echo $wrap_start . esc_html__( 'Tooltip', 'wpc-variation-swatches' ) . $wrap_mid . '<input id="wpcvs_tooltip" name="wpcvs_tooltip" value="' . esc_attr( $wpcvs_tooltip ) . '" type="text"/>' . $wrap_end;

							break;
						case 'color':
							$wpcvs_val = get_term_meta( $term_id, 'wpcvs_color', true );
							echo $wrap_start . esc_html__( 'Color', 'wpc-variation-swatches' ) . $wrap_mid . '<input class="wpcvs_color" id="wpcvs_color" name="wpcvs_color" value="' . esc_attr( $wpcvs_val ) . '" type="text"/>' . $wrap_end;
							echo $wrap_start . esc_html__( 'Tooltip', 'wpc-variation-swatches' ) . $wrap_mid . '<input id="wpcvs_tooltip" name="wpcvs_tooltip" value="' . esc_attr( $wpcvs_tooltip ) . '" type="text"/>' . $wrap_end;

							break;
						case 'image':
							wp_enqueue_media();
							$wpcvs_val = get_term_meta( $term_id, 'wpcvs_image', true );

							if ( $wpcvs_val ) {
								$image = wp_get_attachment_thumb_url( $wpcvs_val );
							} else {
								$image = wc_placeholder_img_src();
							}

							echo $wrap_start . esc_html__( 'Image', 'wpc-variation-swatches' ) . $wrap_mid; ?>
                            <div id="wpcvs_image_thumbnail" style="float: left; margin-right: 10px;">
                                <img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px"/></div>
                            <div style="line-height: 60px;">
                                <input type="hidden" id="wpcvs_image" name="wpcvs_image" value="<?php echo esc_attr( $wpcvs_val ); ?>"/>
                                <button id="wpcvs_upload_image" type="button" class="wpcvs_upload_image button"><?php esc_html_e( 'Upload/Add image', 'wpc-variation-swatches' ); ?>
                                </button>
                                <button id="wpcvs_remove_image" type="button" class="wpcvs_remove_image button"><?php esc_html_e( 'Remove image', 'wpc-variation-swatches' ); ?>
                                </button>
                            </div>
							<?php
							echo $wrap_end;
							echo $wrap_start . esc_html__( 'Tooltip', 'wpc-variation-swatches' ) . $wrap_mid . '<input id="wpcvs_tooltip" name="wpcvs_tooltip" value="' . esc_attr( $wpcvs_tooltip ) . '" type="text"/>' . $wrap_end;

							break;
						case 'radio':
							$wpcvs_val = get_term_meta( $term_id, 'wpcvs_radio', true );
							echo $wrap_start . esc_html__( 'Label', 'wpc-variation-swatches' ) . $wrap_mid . '<input id="wpcvs_radio" name="wpcvs_radio" value="' . esc_attr( $wpcvs_val ) . '" type="text"/>' . $wrap_end;
							echo $wrap_start . esc_html__( 'Tooltip', 'wpc-variation-swatches' ) . $wrap_mid . '<input id="wpcvs_tooltip" name="wpcvs_tooltip" value="' . esc_attr( $wpcvs_tooltip ) . '" type="text"/>' . $wrap_end;

							break;
						default:
							echo '';
					}
				}

				function save_field( $term_id ) {
					if ( isset( $_POST['wpcvs_group'] ) ) {
						update_term_meta( $term_id, 'wpcvs_group', sanitize_text_field( $_POST['wpcvs_group'] ) );
					}

					if ( isset( $_POST['wpcvs_color'] ) ) {
						update_term_meta( $term_id, 'wpcvs_color', sanitize_text_field( $_POST['wpcvs_color'] ) );
					}

					if ( isset( $_POST['wpcvs_button'] ) ) {
						update_term_meta( $term_id, 'wpcvs_button', sanitize_text_field( $_POST['wpcvs_button'] ) );
					}

					if ( isset( $_POST['wpcvs_image'] ) ) {
						update_term_meta( $term_id, 'wpcvs_image', sanitize_text_field( $_POST['wpcvs_image'] ) );
					}

					if ( isset( $_POST['wpcvs_radio'] ) ) {
						update_term_meta( $term_id, 'wpcvs_radio', sanitize_text_field( $_POST['wpcvs_radio'] ) );
					}

					if ( isset( $_POST['wpcvs_tooltip'] ) ) {
						update_term_meta( $term_id, 'wpcvs_tooltip', sanitize_text_field( $_POST['wpcvs_tooltip'] ) );
					}
				}

				function post_class( $classes ) {
					$classes[] = 'wpcvs-active';
					$classes[] = 'wpcvs-single-replacement-' . self::get_setting( 'single_replacement', 'enable' );

					return $classes;
				}

				function variation_attribute_options_html( $options_html, $args ) {
					$options          = $args['options'];
					$product          = $args['product'];
					$attribute        = $args['attribute'];
					$count            = 0;
					$limit            = absint( isset( $args['limit'] ) ? $args['limit'] : 0 );
					$tooltip_position = self::get_setting( 'tooltip_position', 'top' );
					$tooltip_library  = self::get_setting( 'tooltip_library', 'tippy' );
					$style            = self::get_setting( 'style', 'square' );
					$attr_id          = wc_attribute_taxonomy_id_by_name( $attribute );
					$tooltip_class    = '';

					if ( $tooltip_position !== 'no' ) {
						if ( $tooltip_library === 'hint' ) {
							$tooltip_class = 'hint-tooltip hint--' . $tooltip_position;
						}

						if ( $tooltip_library === 'tippy' ) {
							$tooltip_class = 'wpcvs-tippy-tooltip tippy--' . $tooltip_position;
						}
					}

					ob_start();

					if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
						$attributes = $product->get_variation_attributes();
						$options    = $attributes[ $attribute ];
					}

					if ( $attr_id ) {
						$attr          = wc_get_attribute( $attr_id );
						$attr_type     = isset( $attr->type ) ? $attr->type : 'select';
						$groups        = get_option( 'wpcvs_groups_' . $attr_id ) ?: [];
						$grouped_terms = [];
						$terms         = wc_get_product_terms(
							$product->get_id(),
							$attribute,
							[
								'fields' => 'all',
							]
						);

						if ( ! empty( $terms ) ) {
							foreach ( $terms as $term ) {
								$group = get_term_meta( $term->term_id, 'wpcvs_group', true ) ?: 'wpcvs_no_group';

								if ( ( self::get_setting( 'group', 'yes' ) === 'yes' ) && in_array( $group, $groups ) ) {
									$grouped_terms[ $group ][] = $term;
								} else {
									$grouped_terms['wpcvs_no_group'][] = $term;
								}
							}
						}

						if ( ( $attr_type === 'select' ) && ( self::get_setting( 'button_default', 'no' ) === 'yes' ) ) {
							$attr_type = 'button';
						}

						// insert wpcvs_no_group to the last
						if ( apply_filters( 'wpcvs_no_group_first', true ) ) {
							array_unshift( $groups, 'wpcvs_no_group' );
						} else {
							$groups[] = 'wpcvs_no_group';
						}

						if ( ( $attr_type !== '' ) && ( $attr_type !== 'select' ) ) {
							do_action( 'wpcvs_terms_above', $args );
							echo '<div class="' . esc_attr( apply_filters( 'wpcvs_terms_class', 'wpcvs-terms wpcvs-type-' . $attr_type . ' wpcvs-style-' . $style, $terms, $args ) ) . '" data-attribute="' . esc_attr( $attribute ) . '">';
							do_action( 'wpcvs_terms_before', $args );

							foreach ( $groups as $group ) {
								if ( ! isset( $grouped_terms[ $group ] ) ) {
									continue;
								}

								if ( $group !== 'wpcvs_no_group' ) {
									echo '<div class="wpcvs-group" data-group="' . esc_attr( $group ) . '">' . esc_html( $group ) . '</div>';
								}

								if ( ! empty( $grouped_terms[ $group ] ) ) {
									foreach ( $grouped_terms[ $group ] as $term ) {
										switch ( $attr_type ) {
											case 'button' :
												if ( ! $limit || ( $count < $limit ) ) {
													$val     = get_term_meta( $term->term_id, 'wpcvs_button', true ) ?: $term->name;
													$tooltip = get_term_meta( $term->term_id, 'wpcvs_tooltip', true ) ?: $val;
													$class   = apply_filters( 'wpcvs_term_class', 'wpcvs-term ' . $tooltip_class, $term, $args );

													if ( $tooltip_library === 'tippy' ) {
														$tooltip_content = 'data-tippy-content="' . esc_attr( htmlentities( '<span class="wpcvs-tippy wpcvs-tippy-' . esc_attr( $term->term_id ) . '"><span class="wpcvs-tippy-inner"><span class="wpcvs-tippy-title">' . esc_html( $tooltip ) . '</span>' . ( ! empty( $term->description ) ? '<span class="wpcvs-tippy-desc">' . esc_html( $term->description ) . '</span>' : '' ) . '</span></span>' ) ) . '"';
													} elseif ( $tooltip_library === 'hint' ) {
														$tooltip_content = 'aria-label="' . esc_attr( $tooltip ) . '"';
													} else {
														$tooltip_content = '';
													}

													do_action( 'wpcvs_term_before', $term );
													echo apply_filters( 'wpcvs_term_html', '<span class="' . esc_attr( $class ) . '" ' . $tooltip_content . ' title="' . esc_attr( $tooltip ) . '" data-group="' . esc_attr( $group ) . '" data-label="' . esc_attr( $term->name ) . '" data-term="' . esc_attr( $term->slug ) . '"><span>' . esc_html( $val ) . '</span></span>', $term, $args );
													do_action( 'wpcvs_term_after', $term );
												}

												$count ++;

												break;
											case 'color':
												if ( ! $limit || ( $count < $limit ) ) {
													$val     = get_term_meta( $term->term_id, 'wpcvs_color', true ) ?: '';
													$tooltip = get_term_meta( $term->term_id, 'wpcvs_tooltip', true ) ?: $term->name;
													$class   = apply_filters( 'wpcvs_term_class', 'wpcvs-term ' . $tooltip_class, $term, $args );

													if ( $tooltip_library === 'tippy' ) {
														$tooltip_content = 'data-tippy-content="' . esc_attr( htmlentities( '<span class="wpcvs-tippy wpcvs-tippy-' . esc_attr( $term->term_id ) . '"><span class="wpcvs-tippy-inner"><span class="wpcvs-tippy-title">' . esc_html( $tooltip ) . '</span><span class="wpcvs-tippy-swatches"><span class="wpcvs-tippy-swatches--color" ' . ( ! empty( $val ) ? 'style="background-color: ' . esc_attr( $val ) . '"' : '' ) . '>' . esc_html( $val ) . '</span></span>' . ( ! empty( $term->description ) ? '<span class="wpcvs-tippy-desc">' . esc_html( $term->description ) . '</span>' : '' ) . '</span></span>' ) ) . '"';
													} elseif ( $tooltip_library === 'hint' ) {
														$tooltip_content = 'aria-label="' . esc_attr( $tooltip ) . '"';
													} else {
														$tooltip_content = '';
													}

													do_action( 'wpcvs_term_before', $term );
													echo apply_filters( 'wpcvs_term_html', '<span class="' . esc_attr( $class ) . '" ' . $tooltip_content . ' title="' . esc_attr( $tooltip ) . '" data-group="' . esc_attr( $group ) . '" data-label="' . esc_attr( $term->name ) . '" data-term="' . esc_attr( $term->slug ) . '"><span ' . ( ! empty( $val ) ? 'style="background-color: ' . esc_attr( $val ) . '"' : '' ) . '>' . esc_html( $val ) . '</span></span>', $term, $args );
													do_action( 'wpcvs_term_after', $term );
												}

												$count ++;

												break;
											case 'image':
												if ( ! $limit || ( $count < $limit ) ) {
													$val     = get_term_meta( $term->term_id, 'wpcvs_image', true ) ? wp_get_attachment_thumb_url( get_term_meta( $term->term_id, 'wpcvs_image', true ) ) : wc_placeholder_img_src();
													$tooltip = get_term_meta( $term->term_id, 'wpcvs_tooltip', true ) ?: $term->name;
													$class   = apply_filters( 'wpcvs_term_class', 'wpcvs-term ' . $tooltip_class, $term, $args );

													if ( $tooltip_library === 'tippy' ) {
														$val_full        = get_term_meta( $term->term_id, 'wpcvs_image', true ) ? wp_get_attachment_image_url( get_term_meta( $term->term_id, 'wpcvs_image', true ), 'full' ) : wc_placeholder_img_src();
														$tooltip_content = 'data-tippy-content="' . esc_attr( htmlentities( '<span class="wpcvs-tippy wpcvs-tippy-' . esc_attr( $term->term_id ) . '"><span class="wpcvs-tippy-inner"><span class="wpcvs-tippy-title">' . esc_html( $tooltip ) . '</span><span class="wpcvs-tippy-swatches"><span class="wpcvs-tippy-swatches--image"><img src="' . esc_url( $val_full ) . '" alt="' . esc_attr( $term->name ) . '"/></span></span>' . ( ! empty( $term->description ) ? '<span class="wpcvs-tippy-desc">' . esc_html( $term->description ) . '</span>' : '' ) . '</span></span>' ) ) . '"';
													} elseif ( $tooltip_library === 'hint' ) {
														$tooltip_content = 'aria-label="' . esc_attr( $tooltip ) . '"';
													} else {
														$tooltip_content = '';
													}

													do_action( 'wpcvs_term_before', $term );
													echo apply_filters( 'wpcvs_term_html', '<span class="' . esc_attr( $class ) . '" ' . $tooltip_content . ' title="' . esc_attr( $tooltip ) . '" data-group="' . esc_attr( $group ) . '" data-label="' . esc_attr( $term->name ) . '" data-term="' . esc_attr( $term->slug ) . '"><span><img src="' . esc_url( $val ) . '" alt="' . esc_attr( $term->name ) . '"/></span></span>', $term, $args );
													do_action( 'wpcvs_term_after', $term );
												}

												$count ++;

												break;
											case 'radio':
												$name = uniqid( 'wpcvs_radio_' );

												if ( ! $limit || ( $count < $limit ) ) {
													$val     = get_term_meta( $term->term_id, 'wpcvs_radio', true ) ?: $term->name;
													$tooltip = get_term_meta( $term->term_id, 'wpcvs_tooltip', true ) ?: $term->name;
													$class   = apply_filters( 'wpcvs_term_class', 'wpcvs-term ' . $tooltip_class, $term, $args );

													if ( $tooltip_library === 'tippy' ) {
														$tooltip_content = 'data-tippy-content="' . esc_attr( htmlentities( '<span class="wpcvs-tippy wpcvs-tippy-' . esc_attr( $term->term_id ) . '"><span class="wpcvs-tippy-inner"><span class="wpcvs-tippy-title">' . esc_html( $tooltip ) . '</span>' . ( ! empty( $term->description ) ? '<span class="wpcvs-tippy-desc">' . esc_html( $term->description ) . '</span>' : '' ) . '</span></span>' ) ) . '"';
													} elseif ( $tooltip_library === 'hint' ) {
														$tooltip_content = 'aria-label="' . esc_attr( $tooltip ) . '"';
													} else {
														$tooltip_content = '';
													}

													do_action( 'wpcvs_term_before', $term );
													echo apply_filters( 'wpcvs_term_html', '<span class="' . esc_attr( $class ) . '" ' . $tooltip_content . ' title="' . esc_attr( $tooltip ) . '" data-group="' . esc_attr( $group ) . '" data-label="' . esc_attr( $term->name ) . '" data-term="' . esc_attr( $term->slug ) . '"><span><input type="radio" name="' . esc_attr( $name ) . '" value="' . esc_attr( $term->slug ) . '"/> ' . esc_html( $val ) . '</span></span>', $term, $args );
													do_action( 'wpcvs_term_after', $term );
												}

												$count ++;

												break;
											default:
												break;
										}
									}
								}
							}

							if ( $limit && ( $count > $limit ) ) {
								echo apply_filters( 'wpcvs_more_html', '<span class="wpcvs-more"><a href="' . esc_url( $product->get_permalink() ) . '">' . sprintf( self::localization( 'more', /* translators: count */ esc_html__( '+%d More', 'wpc-variation-swatches' ) ), ( $count - $limit ) ) . '</a></span>', ( $count - $limit ) );
							}

							do_action( 'wpcvs_terms_after', $args );
							echo '</div><!-- /wpcvs-terms -->';
							do_action( 'wpcvs_terms_below', $args );
						}
					} else {
						// custom attribute
						if ( self::get_setting( 'button_default', 'no' ) === 'yes' ) {
							do_action( 'wpcvs_terms_above', $args );
							echo '<div class="' . esc_attr( apply_filters( 'wpcvs_terms_class', 'wpcvs-terms wpcvs-type-button wpcvs-style-' . $style, $options, $args ) ) . '" data-attribute="' . esc_attr( wc_sanitize_taxonomy_name( $attribute ) ) . '">';
							do_action( 'wpcvs_terms_before', $args );

							foreach ( $options as $option ) {
								if ( ! $limit || ( $count < $limit ) ) {
									$class = apply_filters( 'wpcvs_term_class', 'wpcvs-term ' . $tooltip_class, $option, $args );

									if ( $tooltip_library === 'tippy' ) {
										$tooltip_content = 'data-tippy-content="' . esc_attr( htmlentities( '<span class="wpcvs-tippy"><span class="wpcvs-tippy-inner"><span class="wpcvs-tippy-title">' . esc_html( $option ) . '</span></span></span>' ) ) . '"';
									} elseif ( $tooltip_library === 'hint' ) {
										$tooltip_content = 'aria-label="' . esc_attr( $option ) . '"';
									} else {
										$tooltip_content = '';
									}

									do_action( 'wpcvs_term_before', $option );
									echo apply_filters( 'wpcvs_term_html', '<span class="' . esc_attr( $class ) . '"  ' . $tooltip_content . ' title="' . esc_attr( $option ) . '" data-group="wpcvs_no_group" data-term="' . esc_attr( $option ) . '"><span>' . esc_html( $option ) . '</span></span>', $option, $args );
									do_action( 'wpcvs_term_after', $option );
								}

								$count ++;
							}

							do_action( 'wpcvs_terms_after', $args );
							echo '</div>';
							do_action( 'wpcvs_terms_below', $args );
						}
					}

					return apply_filters( 'wpcvs_terms_html', ob_get_clean(), $args ) . $options_html;
				}

				function custom_columns( $columns ) {
					$columns['wpcvs_group']   = esc_html__( 'Group', 'wpc-variation-swatches' );
					$columns['wpcvs_value']   = esc_html__( 'Value', 'wpc-variation-swatches' );
					$columns['wpcvs_tooltip'] = esc_html__( 'Tooltip', 'wpc-variation-swatches' );

					return $columns;
				}

				function custom_columns_content( $columns, $column, $term_id ) {
					if ( $column === 'wpcvs_group' ) {
						echo esc_html( get_term_meta( $term_id, 'wpcvs_group', true ) );
					}

					if ( $column === 'wpcvs_value' ) {
						$term    = get_term( $term_id );
						$attr_id = wc_attribute_taxonomy_id_by_name( $term->taxonomy );
						$attr    = wc_get_attribute( $attr_id );

						switch ( $attr->type ) {
							case 'image':
								$val = get_term_meta( $term_id, 'wpcvs_image', true );
								echo '<img style="display: inline-block; border-radius: 3px; width: 40px; height: 40px; background-color: #eee; box-sizing: border-box; border: 1px solid #eee;" src="' . esc_url( $val ? wp_get_attachment_thumb_url( $val ) : wc_placeholder_img_src() ) . '"/>';

								break;
							case 'color':
								$val = get_term_meta( $term_id, 'wpcvs_color', true );
								echo '<span style="display: inline-block; border-radius: 3px; width: 40px; height: 40px; background-color: ' . esc_attr( $val ) . '; box-sizing: border-box; border: 1px solid #eee;"></span>';

								break;
							case 'button':
								$val = get_term_meta( $term_id, 'wpcvs_button', true );
								echo '<span style="display: inline-block; border-radius: 3px; height: 40px; line-height: 40px; padding: 0 15px; border: 1px solid #eee; background-color: #fff; min-width: 44px; box-sizing: border-box;">' . esc_html( $val ) . '</span>';

								break;
						}
					}

					if ( $column === 'wpcvs_tooltip' ) {
						echo esc_html( get_term_meta( $term_id, 'wpcvs_tooltip', true ) );
					}
				}

				function archive( $product_id = null, $hide = '', $link = false ) {
					global $product;
					$global_product = $product;

					if ( $product_id ) {
						$product = wc_get_product( $product_id );
					}

					if ( ! $product || ! $product->is_type( 'variable' ) ) {
						return;
					}

					$attributes           = $product->get_variation_attributes();
					$available_variations = $product->get_available_variations();
					$variations_json      = wp_json_encode( $available_variations );
					$variations_attr      = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
					$hide_attrs           = ! empty( $hide ) ? explode( ',', $hide ) : [];
					$form_class           = $link ? 'variations_form wpcvs_archive wpcvs_link' : 'variations_form wpcvs_archive';

					if ( is_array( $attributes ) && ( count( $attributes ) > 0 ) ) {
						do_action( 'wpcvs_archive_variations_form_above', $product );
						echo '<div class="' . esc_attr( $form_class ) . '" data-product_id="' . absint( $product->get_id() ) . '" data-product_variations="' . esc_attr( $variations_attr ) . '" data-link="' . esc_url( $product->get_permalink() ) . '">';
						do_action( 'wpcvs_archive_variations_form_before', $product );
						echo '<div class="variations">';
						do_action( 'wpcvs_archive_variations_before', $product );

						foreach ( $attributes as $attribute_name => $options ) {
							if ( in_array( $attribute_name, $hide_attrs ) ) {
								continue;
							}
							?>
                            <div class="variation">
                                <div class="label">
									<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>
                                </div>
                                <div class="select">
									<?php
									$attr     = 'attribute_' . sanitize_title( $attribute_name );
									$selected = isset( $_REQUEST[ $attr ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ $attr ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
									wc_dropdown_variation_attribute_options( [
										'options'          => $options,
										'attribute'        => $attribute_name,
										'product'          => $product,
										'limit'            => self::get_setting( 'archive_limit', '10' ),
										'selected'         => $selected,
										'show_option_none' => esc_html__( 'Choose', 'wpc-variation-swatches' ) . ' ' . wc_attribute_label( $attribute_name )
									] );
									?>
                                </div>
                            </div>
						<?php }

						echo '<div class="reset">' . apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'wpc-variation-swatches' ) . '</a>' ) . '</div>';
						do_action( 'wpcvs_archive_variations_after', $product );
						echo '</div>';
						do_action( 'wpcvs_archive_variations_form_after', $product );
						echo '</div>';
						do_action( 'wpcvs_archive_variations_form_below', $product );
					}

					$product = $global_product;
				}

				function stacked_template( $located, $template_name ) {
					if ( $template_name === 'single-product/add-to-cart/variable.php' ) {
						return WPCVS_DIR . 'templates/variable.php';
					}

					return $located;
				}

				function wpcsm_locations( $locations ) {
					$locations['WPC Variation Swatches'] = [
						'wpcvs_terms_above'  => esc_html__( 'Above terms container', 'wpc-variation-swatches' ),
						'wpcvs_terms_below'  => esc_html__( 'Below terms container', 'wpc-variation-swatches' ),
						'wpcvs_terms_before' => esc_html__( 'Before terms container', 'wpc-variation-swatches' ),
						'wpcvs_terms_after'  => esc_html__( 'After terms container', 'wpc-variation-swatches' ),
						'wpcvs_term_before'  => esc_html__( 'Before term', 'wpc-variation-swatches' ),
						'wpcvs_term_after'   => esc_html__( 'After term', 'wpc-variation-swatches' ),
					];

					return $locations;
				}
			}

			return WPCleverWpcvs::instance();
		}

		return null;
	}
}

if ( ! function_exists( 'wpcvs_notice_wc' ) ) {
	function wpcvs_notice_wc() {
		?>
        <div class="error">
            <p><strong>WPC Variation Swatches</strong> requires WooCommerce version 3.0 or greater.</p>
        </div>
		<?php
	}
}
