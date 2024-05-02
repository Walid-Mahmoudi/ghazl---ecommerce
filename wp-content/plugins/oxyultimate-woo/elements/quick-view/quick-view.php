<?php

class OUWooQuickView extends UltimateWooEl {

	public $css_added 		= false;
	public $footer_js 		= false;
	public $oxy_dirname 	= '';

	function name() {
		return __( "Quick View", 'oxyultimate-woo' );
	}

	function slug() {
		return "quick-view";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function controls() {
		$tpls = array( 0 => __('Select') );

		$rows = ouwoo_oxygen_template_exist('ouwoo_template_quickview', 999);
		if( $rows ){
			foreach ($rows as $key => $obj) {
				$tpls[$obj->ID] = str_replace(' ', '&#8205; ', preg_replace("/[^a-zA-Z0-9\s]+/", "", $obj->post_title ) );
			}
		}

		$qvtpl = $this->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Select Quick View Template', "oxyultimate-woo"),
			'slug' 	=> 'qv_tpl',
			'value' => $tpls
		]);
		$qvtpl->setParam('description', __('Note: Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

		$preview = $this->El->addControl("buttons-list", "qv_preview", __( "Builder Preview", "oxyultimate-woo" ) );
		$preview->setValue([ "yes" => __("Enable"), "no" => __("Disable") ]);
		$preview->setValueCSS([ 'yes' => '.quick-view-content-wrap.qv-builder-preview{display: block; visibility: visible;}' ]);
		$preview->setDefaultValue('no');
		$preview->setParam('description', __('Note: Click on Apply Params button to fix the preview.', "oxyultimate-woo"));
		$preview->rebuildElementOnChange();

		$this->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Trigger Selector', "oxyultimate-woo"),
			'slug' 	=> 'qv_trigger_selector',
			'placeholder' => '.open-qv-popup'
		]);

		$this->back_drop_control();

		$this->popup_box_control();

		$this->close_button_control();

		$this->notice_control();
	}

	function back_drop_control() {
		$bd = $this->addControlSection( 'bdrop_section', __('Backdrop', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.qv-back-drop';

		$disable_bd = $bd->addControl("buttons-list", "bd_disable", __( "Disable Backdrop", "oxyultimate-woo" ) );
		$disable_bd->setValue([ "yes" => __("Yes"), "no" => __("No") ]);
		$disable_bd->setValueCSS([ 'yes' => $selector . '{display: none;}' ]);
		$disable_bd->setDefaultValue('no');

		$bd->addStyleControl(
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			]
		);

		$bd->addStyleControl(
			[
				'selector' 	=> $selector,
				'property' 	=> 'z-index',
				'default' 	=> '10050'
			]
		);
	}

	function popup_box_control() {
		$box = $this->addControlSection( 'box_section', __('Lightbox', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.quick-view-content';

		$box->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Type', "oxy-ultimate"),
			'slug' 		=> 'qv_type',
			'value' 	=> [
				'modal' 	=> __('Modal', "oxy-ultimate"),
				'offcanvas' => __('Off Canvas', "oxy-ultimate")
			],
			'default' 	=> 'modal'
		])->rebuildElementOnChange();

		$box->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Position'),
			'slug' 		=> 'panel_position',
			'condition' => 'qv_type=offcanvas'
		])->setValue(['left', 'right', 'top', 'bottom'])
		->setValueCSS([
			'left' 	=> ".qv-off-canvas{
							right: auto;
							top: 0;
							left: 0;
							height: 100%;
						}
						.qv-off-canvas.qv-ofc {
		                    -webkit-transform: translateX(-100%);
		                    -moz-transform: translateX(-100%);
		                    transform: translateX(-100%);
		                }",
			'right' =>  ".qv-off-canvas{
							right: 0;
							top: 0;
							left: auto;
							height: 100%;
						}
						.qv-off-canvas.qv-ofc {
		                    -webkit-transform: translateX(100%);
		                    -moz-transform: translateX(100%);
		                    transform: translateX(100%);
		                }",
			'top' 	=> ".qv-off-canvas{
							right: 0;
							top: 0;
							left: 0;
							bottom: auto;
							height: var(--panel-height);
							max-width: 100%;
						}
						.qv-off-canvas.qv-ofc {
		                    -webkit-transform: translateY(-100%);
		                    -moz-transform: translateY(-100%);
		                    transform: translateY(-100%);
		                }",
			'bottom' => ".qv-off-canvas {
							top: auto;
							right: 0;
							bottom: 0;
							left: 0;
							height: var(--panel-height);
							max-width: 100%;
						}
						.qv-off-canvas.qv-ofc {
		                    -webkit-transform: translateY(100%);
		                    -moz-transform: translateY(100%);
		                    transform: translateY(100%);
		                }",
		])->setDefaultValue('right');

		$box->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> '--panel-height',
			'slug' 			=> 'panel_height',
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px',
			'default' 		=> 350,
			'condition' 	=> 'panel_position=top||panel_position=bottom'
		]);

		$box->addStyleControl(
			[
				'name' 		=> __('Width'),
				'selector' 	=> $selector,
				'property' 	=> 'max-width',
				'control_type' => 'slider-measurebox',
				'condition' 	=> 'qv_type=modal||panel_position=left||panel_position=right'
			]
		)->setUnits('px', 'px')->setRange(0, 1000, 5)->setDefaultValue(750);

		$box->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'selector' 	=> '.quick-view-content.qv-box-fadein',
				'property' 	=> 'z-index',
				'default' 	=> '10052'
			]
		]);

		$box->borderSection( __('Borders', "oxygen"), $selector, $this );
		$box->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
	}

	function close_button_control() {
		$closebtn = $this->addControlSection( 'close_btn', __('Close Button', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = ".close-quick-view";

		$disable = $closebtn->addControl("buttons-list", "cb_disable", __( "Disable Button", "oxyultimate-woo" ) );
		$disable->setValue([ "yes" => __("Yes"), "no" => __("No") ]);
		$disable->setValueCSS([ 'yes' => $selector . '{display: none;}' ]);
		$disable->setDefaultValue('no');

		$closebtn->addStyleControls([
			[
				'name' 		=> __('Background Color', "oxyultimate-woo"),
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Hover Background Color', "oxyultimate-woo"),
				'selector' 	=> $selector . ':hover',
				'property' 	=> 'background-color'
			],
			[
				'selector' 	=> $selector,
				'property' 	=> 'z-index',
				'default' 	=> '10052'
			]
		]);

		$icon = $closebtn->addControlSection( 'cb_icon', __('Icon'), "assets/icon.png", $this );

		$icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxy-ultimate"),
				"slug" 			=> 'close_icon',
				"value" 		=> 'Lineariconsicon-cross',
				"default" 		=> 'Lineariconsicon-cross',
				'css' 			=> false
			)
		)->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

		$icon->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxy-ultimate"),
				"selector" 		=> $selector . ' svg',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> 16,
				"property" 		=> 'width|height'
			)
		)
		->setRange(10, 50, 1)
		->setUnits("px", "px");

		$icon->addStyleControl(
			array(
				"name" 			=> __('Icon Wrapper Size', "oxy-ultimate"),
				"selector" 		=> $selector,
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> 35,
				"property" 		=> 'width|height|line-height'
			)
		)
		->setRange(10, 100, 1)
		->setUnits("px", "px");

		$icon->addStyleControls([
			[
				'name' 		=> __('Icon Color', "oxyultimate-woo"),
				'selector' 	=> $selector . ' svg',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Icon Hover Color', "oxyultimate-woo"),
				'selector' 	=> $selector . ':hover svg',
				'property' 	=> 'color'
			]
		]);

		$pos = $closebtn->addControlSection( 'cb_pos', __('Position'), "assets/icon.png", $this );

		$pos->addStyleControl([
			'name' 			=> __('Top'),
			'selector' 		=> $selector,
			'property' 		=> 'top',
			'default' 		=> -10
		])->setParam('hide_wrapper_end', true);

		$pos->addStyleControl([
			'name' 			=> __('Bottom'),
			'selector' 		=> $selector,
			'property' 		=> 'bottom'
		])->setParam('hide_wrapper_start', true);

		$pos->addStyleControl([
			'name' 			=> __('Left'),
			'selector' 		=> $selector,
			'property' 		=> 'left'
		])->setParam('hide_wrapper_end', true);

		$pos->addStyleControl([
			'name' 			=> __('Right'),
			'selector' 		=> $selector,
			'property' 		=> 'right',
			'default' 		=> -10
		])->setParam('hide_wrapper_start', true);

		$closebtn->borderSection( __('Borders', "oxygen"), $selector, $this );
		$closebtn->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
	}

	function notice_control() {
		$notice = $this->addControlSection( 'notice_sec', __('Notice', "oxyultimate-woo"), "assets/icon.png", $this );

		$notice->addCustomControl(
			sprintf(
				'<div class="oxygen-option-default" style="color: #c3c5c7;font-size: 13px; line-height: 1.325">%s</div>',
				__('Notice will show when a product will add to your cart.', "oxyultimate-woo")
			), 
			'description'
		)->setParam('heading', 'Note:');

		$preview = $notice->addControl("buttons-list", "notice_preview", __( "Builder Preview", "oxyultimate-woo" ) );
		$preview->setValue([ "yes" => __("Enable"), "no" => __("Disable") ]);
		$preview->setValueCSS([ 'yes' => '.nbox-builder-preview.qv-notice-box{transform: translateY(0); opacity: 1;}' ]);
		$preview->setDefaultValue('no');
		$preview->setParam('description', __('Note: Click on Apply Params button to fix the preview.', "oxyultimate-woo"));

		$disable = $notice->addControl("buttons-list", "notice_disable", __( "Disable the notice box?", "oxyultimate-woo" ) );
		$disable->setValue([ "yes" => __("Yes"), "no" => __("No") ]);
		$disable->setValueCSS([ 'yes' => '.qv-notice-box{display: none;}' ]);
		$disable->setDefaultValue('no');
		$disable->whiteList();

		$notice->addOptionControl([
			'type' 	=> 'textarea',
			'name' 	=> __('Message', "oxyultimate-woo"),
			'slug' 	=> 'notice_msg',
			'default' => '{product_title} has been added to your cart.'
		]);

		$selector = '.qv-notice-box';

		$clr = $notice->addControlSection( 'msg_clr', __('Message Box', "oxyultimate-woo"), "assets/icon.png", $this );

		$clr->addPreset(
			"padding",
			"msgb_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$clr->addStyleControls([
			[
				'name' 		=> __('Width'),
				'selector' 	=> $selector,
				'property' 	=> 'max-width'
			],
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'selector' 	=> $selector,
				'property' 	=> 'z-index',
				'default' 	=> 10055
			]
		]);

		$notice->typographySection( __('Typography'), $selector, $this );

		$pos = $notice->addControlSection( 'nt_pos', __('Position'), "assets/icon.png", $this );

		$pos->addStyleControl([
			'name' 			=> __('Bottom'),
			'selector' 		=> $selector,
			'property' 		=> 'bottom'
		])->setParam('hide_wrapper_end', true);

		$pos->addStyleControl([
			'name' 			=> __('Right'),
			'selector' 		=> $selector,
			'property' 		=> 'right'
		])->setParam('hide_wrapper_start', true);	

		$notice->borderSection( __('Borders', "oxygen"), $selector, $this );
		$notice->boxShadowSection( __('Box Shadow', "oxygen"), $selector, $this );
	}

	function render($options, $defaults, $content) {
		$template_id = ( isset($options['qv_tpl']) && absint( $options['qv_tpl'] ) > 0 ) ? absint( $options['qv_tpl'] ) : 0;
		$preview = isset( $options['qv_preview'] ) ? $options['qv_preview'] : "no";
		$disable_nb = isset( $options['notice_disable'] ) ? $options['notice_disable'] : "no";
		$disable_cb = isset( $options['cb_disable'] ) ? $options['cb_disable'] : "no";
		$trigger_selector = isset( $options['qv_trigger_selector'] ) ? wp_kses_post( $options['qv_trigger_selector'] ) : "no";

		$dataAttr = 'data-qvtpl="'. $template_id .'" data-cb-disable="'. $disable_cb .'" data-nb-disable="'. $disable_nb .'" data-qv-selector="' . $trigger_selector . '" data-qv-added="no"';

		$qv_type = isset( $options['qv_type'] ) ? $options['qv_type'] : "modal";
		$dataAttr .= ' data-qv-type="'. $qv_type .'"';

		if( $disable_cb == "no") {
			$close_icon = isset($options['close_icon']) ? $options['close_icon'] : "Lineariconsicon-cross";

			global $oxygen_svg_icons_to_load;
			$oxygen_svg_icons_to_load[] = $close_icon;

			$dataAttr .= ' data-qv-close-icon="' . $close_icon . '"';
		}

		if( $template_id > 0 ) {
			$slug = get_post_field( 'post_name', $template_id );
			$upload_dir = wp_upload_dir();
			$this->oxy_dirname = str_replace(array('http://','https://'), "//", $upload_dir['baseurl'] ) . '/oxygen/css';
			$old_path = $upload_dir['path'] . '/oxygen/css/' . $slug . '-' . $template_id . '.css';
			
			if( file_exists( $old_path ) ) {
				$cache_css_file = $slug . '-' . $template_id . '.css?cache=' . time();
			} else {
				$cache_css_file = $template_id . '.css?cache=' . time();
			}
			
			$dataAttr .= ' data-qvtpl-css="' . $cache_css_file . '"';
		}

		if( $disable_nb == "no") {
			$msg = isset( $options['notice_msg'] ) ? wp_kses_post($options['notice_msg']) : '{product_title} has been added to your cart.';
			$dataAttr .= ' data-nb-msg="' . $msg . '"';
		}

		echo '<span class="qv-pc" ' . $dataAttr . '>Quick View</span>';

		//* frontend work
		if( ! UltimateWooEl::isBuilderEditorActive() && ! $this->footer_js && $template_id > 0 ) {
			//$this->El->footerJS( $this->ouwoo_quick_view_scripts( $options ) );
			add_action( 'wp_footer', array( $this, 'ouwoo_quick_view_scripts' ) );
			$this->footer_js = true;
		}

		//* backend work
		if( UltimateWooEl::isBuilderEditorActive() ) {
			$product = UltimateWooEl::ouwoo_get_latest_product();

			if( $preview == "yes" && $template_id > 0 ) {
				$class = '';
				if( $qv_type == "offcanvas" )
					$class = ' qv-off-canvas';

				echo '<div class="quick-view-content-wrap qv-builder-preview">';
					echo '<div class="qv-back-drop"></div>';
					echo '<div class="quick-view-content' . $class . '">';
					
					if( $disable_cb == "no" ) {
						echo '<div class="close-quick-view"><svg id="quick-view-close-icon"><use xlink:href="#' . $close_icon .'"></use></svg></div>';
					}

					echo '<div class="qv-content-wrap"></div>';

					echo '</div>';
				echo '</div>';

				printf(
					"<link rel='stylesheet' id='oxygen-cache-%s-css' href='%s/%s' type='text/css' media='all' />", 
					$template_id,
					$this->oxy_dirname,
					$cache_css_file
				);

				$this->El->builderInlineJS("
					jQuery(document).ready(function($){
						var box = $('.quick-view-content');

						$.ajax({
							type: 'POST',
							url: CtBuilderAjax.ajaxUrl,
							data: {
								'action'	: 'ouwoo_quick_view_content',
								'productID' : " . $product->get_id() . ",
								'template'	: " . $template_id . ",
								'security' 	: '" . wp_create_nonce( "ouwoo-quick-view-nonce" ) . "'
							},
							beforeSend: function (response) {
								box.block({
									message: null,
									overlayCSS: {
										opacity: 0.6
									}
								});
							},
							complete: function (response) {
								box.unblock();
							},
							success: function (response) {
								box.find('.qv-content-wrap').html( response );
							},
							dataType: 'json'
						});

						$.ajaxPrefilter(function( options, original_Options, jqXHR ){options.async = true;});
					});
				");
			}

			//*notice box
			$msg = isset( $options['notice_msg'] ) ? wp_kses_post($options['notice_msg']) : '{product_title} has been added to your cart.';
			$msg = str_replace( '{product_title}', $product->get_name(), $msg );

			printf( '<div class="qv-notice-box nbox-builder-preview">%s</div>', $msg );
		}
	}

	public static function ouwoo_quick_view_content() {
		check_ajax_referer( 'ouwoo-quick-view-nonce', 'security' );

		ob_start();

		do_action( 'wp_enqueue_scripts' );
		
		global $wp_styles, $OxygenConditions;
		if( ! is_a( $OxygenConditions, 'OxygenConditions' ) ) {
			require_once( CT_FW_PATH . "/includes/conditions.php");
			$OxygenConditions = new OxygenConditions();
		}

		foreach( $wp_styles->queue as $style ) {
			wp_dequeue_style($wp_styles->registered[$style]->handle);
		}

		if ( isset( $_POST['productID'] ) && isset( $_POST['template'] ) ) {

			global $product, $oxy_vsb_use_query, $post;

			$product_id = absint( $_POST['productID'] );
			$quick_view_tpl = absint( $_POST['template'] );

			$old_query = false;
			$temp = $post;

			if($oxy_vsb_use_query) {
				$old_query = $oxy_vsb_use_query;
			}

			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'p' 		 	=>  $product_id
			);

			$query = new WP_Query( $args );
			$oxy_vsb_use_query = $query;
			if( $query->have_posts() ) {
				while( $query->have_posts() ) {
					$query->the_post();

					$title = get_the_title();

					$product_content = get_post_meta( $quick_view_tpl, "ct_builder_shortcodes", true );

					echo ct_do_shortcode( $product_content );
				}
			}
			wp_reset_postdata();

			if($old_query) {
				$oxy_vsb_use_query = $old_query;
				$oxy_vsb_use_query->reset_postdata();
			}

			$post = $temp;

		} else {
			echo __('Invalid product.', "oxyultimate-woo");
		}

		wp_footer();

		echo "<script type='text/javascript'>
			jQuery(document).ready(function($){
				if( $('.qv-notice-box').length > 0 ) {
					var txt = $('.qv-notice-box').text().replace(\"{product_title}\", \"{$title}\");
				}
				$( document.body ).on( 'added_to_cart', function() { 
					$( '.qv-back-drop' ).trigger('click');
					if( $('.qv-notice-box').length > 0 && $('.quick-view-content-wrap').length > 0 ) {
						$('.qv-notice-box').html( txt ).addClass('on');
						var qvTimeOut = setTimeout(function(){
							$('.qv-notice-box').removeClass('on');
							clearTimeout(qvTimeOut);
						}, 2502);
					}
				});
			});
		</script>";

		wp_send_json( ob_get_clean() );
		wp_die();
	}

	function customCSS( $original, $selector ) {
		$css = '';

		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		return $css;
	}

	function ouwoo_quick_view_scripts() {
		wp_enqueue_script('ou-imageloaded-script', 'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js',array(), '4',true);
		
		wp_enqueue_script(
			'ouwoo-quick-view',
			OUWOO_URL . 'assets/js/ouwoo-quick-view.min.js',
			array(),
			filemtime( OUWOO_DIR . 'assets/js/ouwoo-quick-view.min.js' ),
			true
		);

		wp_localize_script( 'ouwoo-quick-view', 'QVPARAMS', array( 'nonce' => wp_create_nonce( "ouwoo-quick-view-nonce" ), 'cssdir' => $this->oxy_dirname ) );
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooQuickView();

add_action('wp_ajax_ouwoo_quick_view_content', array( 'OUWooQuickView', 'ouwoo_quick_view_content' ) );
add_action('wp_ajax_nopriv_ouwoo_quick_view_content', array( 'OUWooQuickView', 'ouwoo_quick_view_content' ) );