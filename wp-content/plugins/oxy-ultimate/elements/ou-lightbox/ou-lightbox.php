<?php

namespace Oxygen\OxyUltimate;

Class OUAJAXLightbox extends \OxyUltimateEl {
	public $css_added = false;
	public $footer_js = false;
	public $oxy_dirname = '';

	function name() {
		return __( "AJAX Lightbox", 'oxy-ultimate' );
	}

	function oxyu_button_place() {
		return "content";
	}

	function controls() {
		$this->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Enter Template ID', "oxy-ultimate"),
			'slug' 	=> 'lb_template'
		]);

		$this->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Enter Trigger Selector', "oxy-ultimate"),
			'slug' 	=> 'trigger_selector'
		]);

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Data Source', "oxy-ultimate"),
			'slug' 		=> 'data_source',
			'value' 	=> [
				'static' 	=> __('Static', "oxy-ultimate"),
				'repeater' => __('Repeater', "oxy-ultimate")
			],
			'default' 	=> 'repeater'
		])->rebuildElementOnChange();

		$preview = $this->El->addControl("buttons-list", "lb_preview", __( "Builder Preview", "oxyultimate-woo" ) );
		$preview->setValue([ "yes" => __("Enable"), "no" => __("Disable") ]);
		$preview->setValueCSS([ 'yes' => '.ou-lb-content-wrap.lb-builder-preview{display: block; visibility: visible;}' ]);
		$preview->setDefaultValue('no');
		$preview->setParam('description', __('Note: Click on Apply Params button to fix the preview.', "oxyultimate-woo"));
		$preview->rebuildElementOnChange();

		$this->back_drop_control();

		$this->popup_box_control();

		$this->close_button_control();
	}

	function back_drop_control() {
		$bd = $this->addControlSection( 'bdrop_section', __('Backdrop', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.lb-back-drop';

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

		$selector = '.ou-lb-content';

		$box->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Type', "oxy-ultimate"),
			'slug' 		=> 'lb_type',
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
			'condition' => 'lb_type=offcanvas'
		])->setValue(['left', 'right', 'top', 'bottom'])
		->setValueCSS([
			'left' 	=> ".lb-off-canvas{
							right: auto;
							top: 0;
							left: 0;
							height: 100%;
						}
						.lb-off-canvas.lb-ofc {
		                    -webkit-transform: translateX(-100%);
		                    -moz-transform: translateX(-100%);
		                    transform: translateX(-100%);
		                }",
			'right' =>  ".lb-off-canvas{
							right: 0;
							top: 0;
							left: auto;
							height: 100%;
						}
						.lb-off-canvas.lb-ofc {
		                    -webkit-transform: translateX(100%);
		                    -moz-transform: translateX(100%);
		                    transform: translateX(100%);
		                }",
			'top' 	=> ".lb-off-canvas{
							right: 0;
							top: 0;
							left: 0;
							bottom: auto;
							height: var(--panel-height);
							max-width: 100%;
						}
						.lb-off-canvas.lb-ofc {
		                    -webkit-transform: translateY(-100%);
		                    -moz-transform: translateY(-100%);
		                    transform: translateY(-100%);
		                }",
			'bottom' => ".lb-off-canvas {
							top: auto;
							right: 0;
							bottom: 0;
							left: 0;
							height: var(--panel-height);
							max-width: 100%;
						}
						.lb-off-canvas.lb-ofc {
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
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			]
		);

		$box->addStyleControl(
			[
				'name' 		=> __('Width'),
				'selector' 	=> $selector,
				'property' 	=> 'max-width',
				'control_type' => 'slider-measurebox',
				'condition' 	=> 'lb_type=modal||panel_position=left||panel_position=right'
			]
		)->setUnits('px', 'px')->setRange(0, 1000, 5)->setDefaultValue(750);

		$box->addStyleControl(
			[
				'selector' 	=> $selector . '.lb-box-fadein',
				'property' 	=> 'z-index',
				'default' 	=> '10051'
			]
		);

		$box->borderSection( __('Borders', "oxygen"), $selector, $this );
		$box->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
	}

	function close_button_control() {
		$closebtn = $this->addControlSection( 'close_btn', __('Close Button', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = ".close-lightbox";

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
		$closebtn->boxShadowSection( __('Box Shadow', "oxy-ultimate"), $selector, $this );
	}

	function render( $options, $defaults, $content ) {
		$template_id = isset($options['lb_template']) ? $options['lb_template'] : 0;
		$preview = isset( $options['lb_preview'] ) ? $options['lb_preview'] : "no";
		$disable_cb = isset( $options['cb_disable'] ) ? $options['cb_disable'] : "no";
		$data_source = isset( $options['data_source'] ) ? $options['data_source'] : "repeater";
		$trigger_selector = isset( $options['trigger_selector'] ) ? wp_kses_post( $options['trigger_selector'] ) : "no";

		$dataAttr = ' data-lb-tpl="'. $template_id .'" data-oulb-selector="' . $trigger_selector . '" data-cb-disable="'. $disable_cb .'" data-cnt-source="'. $data_source .'"';

		$lbtype = isset( $options['lb_type'] ) ? $options['lb_type'] : "modal";
		$dataAttr .= ' data-lb-type="'. $lbtype .'"';

		if( $disable_cb == "no") {
			$close_icon = isset($options['close_icon']) ? $options['close_icon'] : "Lineariconsicon-cross";

			global $oxygen_svg_icons_to_load;
			$oxygen_svg_icons_to_load[] = $close_icon;

			$dataAttr .= ' data-lb-close-icon="' . $close_icon . '"';
		}

		if( $template_id > 0 ) {
			$slug = get_post_field( 'post_name', $template_id );
			$upload_dir = wp_upload_dir();
			$this->oxy_dirname = str_replace(array('http://','https://'), "//", $upload_dir['baseurl'] ) . '/oxygen/css';
			//$cache_css_file = $slug . '-' . $template_id . '.css?cache=' . time();

			$old_path = $upload_dir['path'] . '/oxygen/css/' . $slug . '-' . $template_id . '.css';
			
			if( file_exists( $old_path ) ) {
				$cache_css_file = $slug . '-' . $template_id . '.css?cache=' . time();
			} else {
				$cache_css_file = $template_id . '.css?cache=' . time();
			}
			
			$dataAttr .= ' data-lbtpl-css="' . $cache_css_file . '"';
		}

		echo '<span class="lb-atts" ' . $dataAttr . '>Lightbox</span>';

		//* frontend work
		if( ! $this->isBuilderEditorActive() && ! $this->footer_js && $template_id > 0 ) {
			add_action( 'wp_footer', array( $this, 'ou_lightbox_script' ) );
			$this->footer_js = true;
		}

		//* backend work
		if( $this->isBuilderEditorActive() && $preview == "yes" && $template_id > 0 ) {
			$class = '';
			if( $lbtype == "offcanvas" )
				$class = ' lb-off-canvas';

			echo '<div class="ou-lb-content-wrap lb-builder-preview">';
				echo '<div class="lb-back-drop"></div>';
				echo '<div class="ou-lb-content' . $class . '">';
				
				if( $disable_cb == "no" ) {
					echo '<div class="close-lightbox"><svg id="oulb-close-icon"><use xlink:href="#' . $close_icon .'"></use></svg></div>';
				}

				echo '<div class="bplb-content-wrap">Please wait...</div>';

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
					var box = $('.ou-lb-content');
					$.ajax({
						type: 'POST',
						url: CtBuilderAjax.ajaxUrl,
						data: {
							'action'	: 'ou_do_ajax_lightbox',
							'postID' 	: 'builderprv',
							'template'	: " . $template_id . ",
							'security' 	: '" . wp_create_nonce( "ou-ajax-lb-nonce" ) . "'
						},
						beforeSend: function (response) {
							//spinner.addClass('show');
						},
						complete: function (response) {
							//spinner.removeClass('show');
						},
						success: function (response) {
							box.find('.bplb-content-wrap').html( response );
						},
						dataType: 'json'
					});

					$.ajaxPrefilter(function( options, original_Options, jqXHR ){options.async = true;});
				});
			");
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';

		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$css .= '.oxy-ajax-lightbox{ --panel-height: 350px}';
			$this->css_added = true;
		}

		return $css;
	}

	function ou_lightbox_script() {
		wp_enqueue_script('ou-imageloaded-script', 'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js',array(), '4',true);
		
		wp_enqueue_script(
			'ou-lightbox',
			OXYU_URL . 'assets/js/ou-lightbox.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/ou-lightbox.js' ),
			true
		);

		wp_localize_script( 
			'ou-lightbox', 
			'OULB', 
			array(	
				'AJAX_URL' 	=> admin_url( 'admin-ajax.php'), 
				'nonce' 	=> wp_create_nonce( "ou-ajax-lb-nonce" ), 
				'cssdir' 	=> $this->oxy_dirname 
			) 
		);
	}
}

new OUAJAXLightbox();