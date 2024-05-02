<?php

namespace Oxygen\OxyUltimate;

class OUOffCanvas extends \OxyUltimateEl {
	public $off_canvas_frontend_js = false;
	public $off_canvas_frontend_css = false;

    function name() {
		return __( "Off Canvas", "oxy-ultimate" );
	}

	function slug() {
		return "ou_off_canvas";
	}

	function oxyu_button_place() {
		return "content";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();

		add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
	}

	function allowedEmptyOptions($options) {
        $options_to_add = array(
        	"oxy-ou_off_canvas_close_text",
            "oxy-ou_off_canvas_close_icon",
            "oxy-ou_off_canvas_unique_id"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }

	function panel() {
		$panel = $this->addControlSection( 'panel_sec', __('Panel', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = '.ou-off-canvas-panel';

		$config = $panel->addControlSection( 'panel_conf', __('Config', "oxy-ultimate"), "assets/icon.png", $this );

		$config->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Position'),
			'slug' 		=> 'panel_position'
		])->setValue(['left', 'right', 'top', 'bottom'])
		->setValueCSS([
			'left' 	=> "$selector{
							right: auto;
							top: 0;
							left: 0;
							height: 100%;
						}
						$selector.ofc-front {
		                    -webkit-transform: translateX(-100%);
		                    -moz-transform: translateX(-100%);
		                    transform: translateX(-100%);
		                }",
			'right' =>  "$selector{
							right: 0;
							top: 0;
							left: auto;
							height: 100%;
						}
						$selector.ofc-front {
		                    -webkit-transform: translateX(100%);
		                    -moz-transform: translateX(100%);
		                    transform: translateX(100%);
		                }",
			'top' 	=> "$selector{
							right: 0;
							top: 0;
							left: 0;
							bottom: auto;
							height: var(--panel-height);
							width: 100%;
						}
						$selector.ofc-front {
		                    -webkit-transform: translateY(-100%);
		                    -moz-transform: translateY(-100%);
		                    transform: translateY(-100%);
		                }",
			'bottom' => "$selector {
							top: auto;
							right: 0;
							bottom: 0;
							left: 0;
							height: var(--panel-height);
							width: 100%;
						}
						$selector.ofc-front {
		                    -webkit-transform: translateY(100%);
		                    -moz-transform: translateY(100%);
		                    transform: translateY(100%);
		                }",
		])->setDefaultValue('right')->setParam("description", __("Click on Apply Params button and apply changes.", "oxy-ultimate"));

		$config->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> '--panel-width',
			'slug' 			=> 'panel_width',
			'default' 		=> 300,
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px',
			'condition' 	=> 'panel_position=left||panel_position=right'
		]);

		$config->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> '--panel-height',
			'slug' 			=> 'panel_height',
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px',
			'default' 		=> 350,
			'condition' 	=> 'panel_position=top||panel_position=bottom'
		]);

		$config->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$config->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox',
			'slug' 			=> 'panel_td'
		])->setUnits('s', 'sec')->setRange(0,5,0.1)->setDefaultValue(0.5);

		$config->addStyleControl(
			array(
				'property' 	=> 'z-index',
				'selector' 	=> $selector,
				'default' 	=> 2147483640
			)
		);

		$spacing = $panel->addControlSection( 'panel_sp', __('Spacing', "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"panel_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$layout = $panel->addControlSection("layout_sec", __("Layout"), "assets/icon.png", $this);
        $layout->flex($selector, $this);

        $panel->borderSection(__('Border'), $selector, $this );
		$panel->boxShadowSection(__('Box Shadow'), $selector, $this );

		$others = $panel->addControlSection( 'others_sec', __('Others', "oxy-ultimate"), "assets/icon.png", $this );
		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Site Scrolling', "oxy-ultimate"),
			'slug' 		=> 'disable_scroll'
		])->setValue(['no' => __('No'), 'yes' => __('Yes')])
		->setDefaultValue('no');

		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Push Body Content', "oxy-ultimate"),
			'slug' 		=> 'push_content'
		])->setValue(['no' => __('No'), 'yes' => __('Yes')])
		->setDefaultValue('no');

		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Reveal Panel', "oxy-ultimate"),
			'slug' 		=> 'reveal_panel'
		])->setValue(['no' => __('No'), 'yes' => __('Yes')])
		->setDefaultValue('no');

		$others->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Delay In', "oxy-ultimate"),
			'slug' 		=> 'delay_in',
			'condition' => 'reveal_panel=yes'
		])->setUnits('ms', 'ms')->setRange(0,5000,50)->setDefaultValue(1200);
	}

	function backdrop() {
		$backdrop = $this->addControlSection( 'bd_sec', __('Backdrop', "oxy-ultimate"), "assets/icon.png", $this );
		$visibility = $backdrop->addControl( 'buttons-list', 'backdrop_visible', __( 'Hide Backdrop', "oxyultimate-woo" ));

		$visibility->setValue( array( "No", "Yes" ) );
		$visibility->setValueCSS( array( "Yes" 	=> ".ou-off-canvas-backdrop{display: none;}" ));
		$visibility->setDefaultValue("No");

		$backdrop->addStyleControls([
			array(
				'property' 	=> 'background-color',
				'selector' 	=> '.ou-off-canvas-backdrop',
				'default' 	=> 'rgba(0,0,0,.5)'
			),
			array(
				'property' 	=> 'z-index',
				'selector' 	=> '.ou-off-canvas-backdrop',
				'default' 	=> 2147483640
			)
		]);

		$backdrop->addStyleControl(
			array(
				"selector" 		=> ".ou-off-canvas-backdrop",
				'property' 		=> 'transition-duration',
				"control_type" 	=> 'slider-measurebox'
			)
		)->setRange("0", "5", "0.1")->setUnits("s", "sec")->setDefaultValue(0.45);
	}

	function scrollbar() {
		$sb = $this->addControlSection( 'scrollbar', __('Scrollbar', "oxy-ultimate"), "assets/icon.png", $this );

		 $sb->addOptionControl([
            'type'  	=> 'radio',
            'name'  	=> __('Customize Scrollbar', "oxy-ultimate"),
            'slug'  	=> 'custom_scrollbar',
        ])->setValue(['no' => __('No'), 'yes' => __('Yes')])
		->setDefaultValue('yes')
		->setParam("description", __("Click on Apply Params button and apply changes.", "oxy-ultimate"));

		$sb->addStyleControl(
			array(
				"selector" 		=> ".ou-off-canvas-panel::-webkit-scrollbar",
				'property' 		=> 'width',
				"control_type" 	=> 'slider-measurebox'
			)
		)->setRange("0", "20", "1")->setUnits("px", "px")->setDefaultValue(6);

		$sb->addStyleControls([
			array(
				'name' 			=> __("Light Bar Color", "oxy-ultimate"),
				"selector" 		=> " ",
				'property' 		=> '--sb-light-color',
				"control_type" 	=> 'colorpicker',
				"default" 		=> 'rgba(0,0,0,0.3)'
			),
			array(
				"name" 			=> __("Dark Bar Color", "oxy-ultimate"),
				"selector" 		=> " ",
				'property' 		=> '--sb-dark-color',
				"control_type" 	=> 'colorpicker',
				"default" 		=> 'rgba(0,0,0,0.5)'
			)
		]);
	}

	function closeButton() {
		$btn = $this->addControlSection( 'close_button', __('Close Button', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = ".ocp-close-button";

		$btn->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Disable Close Button', "oxy-ultimate"),
			'slug' 		=> 'disable_btn'
		])->setValue(['no', 'yes'])->setValueCSS(['yes' => "$selector{display: none;}", 'no' => "$selector{display: flex;}"])->setDefaultValue('no')->setParam('description', __("You can add custom close button after disabling the inbuilt one.", "oxy-ultimate"));

		$btn->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Button Text'),
			'slug' 	=> 'close_text',
			'default' => 'CLOSE'
		])->setParam('description', __("Click on Apply Params button and apply changes.", "oxy-ultimate"));

		$icon = $btn->addControlSection( 'icon_sec', __('Icon', "oxy-ultimate"), "assets/icon.png", $this );
		$icon->addOptionControl(
			array(
				"type" 		=> 'icon_finder',
				"name" 		=> __('Select Icon', "oxy-ultimate"),
				"slug" 		=> 'close_icon',
				"default" 	=> 'Lineariconsicon-cross'
			)
		)->setParam('description', __("Click on Apply Params button and apply changes.", "oxy-ultimate"));

		$btn->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Remove Icon', "oxy-ultimate"),
			'slug' 		=> 'remove_icon'
		])->setValue(['no', 'yes'])->setValueCSS(['yes' => "$selector svg{display: none;}"])->setDefaultValue('no');

		$icon->addStyleControl([
			'name' 		=> __('Size', "oxy-ultimate"),
			'selector' 	=> $selector . " svg",
			'property' 	=> "width|height",
			"control_type" => 'slider-measurebox'
		])->setUnits('px', 'px,em,%')->setDefaultValue(20);

		$icon->addOptionControl([
			'type' 			=> 'dropdown',
			'name' 			=> __('Position'),
			'slug' 			=> 'icon_pos'
		])->setValue([
			'before' => __('Before Text'), 
			'after' => __('After Text')
		])->setValueCSS([
			'before' 	=> $selector . '{flex-direction: row-reverse;}',
			'after' 	=> $selector . '{flex-direction: row;}'
		])->setDefaultValue('after');

		$icon->addStyleControl([
			'selector' 	=> $selector . " svg",
			'property' 	=> "margin-left",
			"control_type" => 'measurebox'
		])->setUnits('px', 'px,em,%')->setParam('hide_wrapper_end', true);

		$icon->addStyleControl([
			'selector' 	=> $selector . " svg",
			'property' 	=> "margin-right",
			"control_type" => 'measurebox'
		])->setUnits('px', 'px,em,%')->setParam('hide_wrapper_start', true);

		$icon->addStyleControls([
			[
				'selector' 	=> $selector . " svg",
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Color on Hover', "oxy-ultimate"),
				'selector' 	=> $selector . ":hover svg",
				'property' 	=> "color"
			]
		]);

		$color = $btn->addControlSection( 'color_sec', __('Style', "oxy-ultimate"), "assets/icon.png", $this );

		$color->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color on Hover', "oxy-ultimate"),
				'selector' 	=> $selector . ":hover",
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Text Color on Hover', "oxy-ultimate"),
				'selector' 	=> $selector . ":hover .btn-text",
				'property' 	=> "color"
			]
		]);

		$color->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> "width",
			"control_type" => 'slider-measurebox'
		])->setUnits('px', 'px,em,%');

		$color->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> "height",
			"control_type" => 'slider-measurebox'
		])->setUnits('px', 'px,em,%');

		$color->addControl(
			"buttons-list",
			"close_btn_align",
			__('Button Alignment', "oxy-ultimate")
		)->setValue([
			'left' 	=> __('Left'), 
			'right' => __('Right')
		])->setValueCSS([
			'left' 	=> $selector . '{margin-right: auto; margin-left: 0;}',
			'right' => $selector . '{margin-left: auto; margin-right: 0;}'
		])->setDefaultValue('right');


		$spacing = $btn->addControlSection( 'close_sp', __('Spacing', "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"cb_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cb_margin",
			__("Margin"),
			'.ou-off-canvas-panel ' . $selector
		)->whiteList();

		$btn->typographySection( __('Typography'), $selector . ' .btn-text', $this );

		$btn->borderSection(__('Border'), $selector, $this);
		$btn->borderSection(__('Hover Border'), $selector . ":hover", $this);

		$btn->boxShadowSection(__('Shadow'), $selector, $this);
		$btn->boxShadowSection(__('Hover Shadow'), $selector . ":hover", $this);
	}

	function controls() {
        $this->addOptionControl([
            'type'  	=> 'buttons-list',
            'name'  	=> __('Show Peview in Builder Editor', "oxy-ultimate"),
            'slug'  	=> 'builder_preview'
        ])->setValue(['yes' => 'Yes', 'no' => 'No'])
        ->setDefaultValue('yes')
        ->setValueCSS([
        	'yes' => ' {display: block}',
            'no' => ' {display: none}'
        ]);

        $this->addOptionControl([
        	'type' 		=> 'textfield',
        	'name' 		=> __('Trigger Selector', "oxy-ultimate"),
        	'slug' 		=> 'trigger_selector'
        ]);

        $this->panel();

        $this->backdrop();

        $this->scrollbar();

        $this->closeButton();
	}

	function render( $options, $defaults, $content ) {
        $offcanvas = '';

        $dataArr = $classes = array();
        $position = isset($options['panel_position']) ? $options['panel_position'] : 'right';
        $classes[] = ($this->isBuilderEditorActive() == true ) ? '' : 'ofc-front';

        if( isset( $options['trigger_selector'] ) ) {
        	$dataArr[] = 'data-trigger-selector="' . $options['trigger_selector'] .'"';
        }

        $dataArr[] = 'data-disable-scroll="' . (isset($options['disable_scroll']) ? $options['disable_scroll'] : 'no' ) .'"';
        $dataArr[] = 'data-reveal="' . (isset($options['reveal_panel']) ? $options['reveal_panel'] : 'no' ) .'"';

        if( isset($options['push_content']) && $options['push_content'] == "yes") {
        	$selector = 'pc-' . $options['selector'];
        	$dataArr[] = 'data-content-td="'. (isset($options['panel_td']) ? $options['panel_td'] : '0.5' ) .'"';
        	$dataArr[] = 'data-pc-id="'. $selector .'"';
        	$dataArr[] = 'data-panel-position="'. $position .'"';
        	$classes[] = $selector . ' ou-push-content';
        }

        if( isset( $options['reveal_panel'] ) && $options['reveal_panel'] == "yes" ) {
        	$dataArr[] = 'data-delay-in="' . (isset($options['delay_in']) ? $options['delay_in'] : 1200 ) .'"';
    	}

        $offcanvas .= '<div class="ou-off-canvas-backdrop"></div>';

        $offcanvas .= '<div class="ou-off-canvas-inner-wrap ou-off-canvas-panel '. implode(" ", $classes) .'"';
        $offcanvas .= ' ' . implode(" ", $dataArr);
        $offcanvas .= '>';

        $offcanvas .= $this->addCloseButton($options);
        $offcanvas .= '<div class="oxy-inner-content">';

        if( $content ) {
			if( function_exists('do_oxygen_elements') )
				$offcanvas .= do_oxygen_elements( $content );
			else
				$offcanvas .= do_shortcode( $content );
        }

        $offcanvas .= '</div>';
        $offcanvas .= '</div>';

        echo $offcanvas;

        if( ! $this->isBuilderEditorActive() ) {
        	if( ! $this->off_canvas_frontend_js ) {
        		add_action( 'wp_footer', array( $this, 'ou_off_canvas_script') );
        		$this->off_canvas_frontend_js = true;
        	}
        }
    }

    function addCloseButton( $options ) {
    	if( isset($options['disable_btn']) && $options['disable_btn'] == "yes" )
    		return '';

    	ob_start();

    	global $oxygen_svg_icons_to_load;

    	$icon = isset( $options['close_icon'] ) ? esc_attr($options['close_icon']) : "";
    	$oxygen_svg_icons_to_load[] = $icon;
    ?>
    	<a href="JavaScript: void(0);" class="ocp-close-button" role="button" title="<?php echo __('Close', "oxy-ultimate");  ?>">
    		<?php if( isset( $options['close_text'] ) ) : $btnText = wp_kses_post( $options['close_text'] ); ?>
    			<span class="btn-text"><?php echo esc_attr( $btnText ); ?></span>
    		<?php endif; ?>
    		<?php if( ! empty( $icon ) ) : ?>
    			<svg id="svg-<?php echo esc_attr($options['selector']); ?>-<?php echo get_the_ID(); ?>" class="ocp-close-icon"><use xlink:href="#<?php echo $icon; ?>"></use></svg>
    		<?php endif; ?>
    	</a>
    <?php

    	return ob_get_clean();
    }

    function customCSS( $original, $selector ) {
        $css = '';

        if( ! $this->off_canvas_frontend_css ) {
        	$css .= 'body.ou-disable-scroll {
						overflow: hidden;
					}
					.ou-remove-spacing {
						margin: 0!important;
						top: 0!important;
						padding: 0!important;
					}
					.oxy-ou-off-canvas {
					    --panel-width: 300px;
					    --panel-height: 350px;
					    --sb-light-color: rgba(0,0,0,0.3);
					    --sb-dark-color: rgba(0,0,0,0.5);
					    pointer-events: none;
					}
					.ou-off-canvas-panel {
						background-color: #fff;
						display: flex;
						height: 100%;
						flex-direction: column;
						position: fixed;
						top: 0;
					    right: 0;
						padding: 10px;
					    width: var(--panel-width);
					    overflow-x: hidden;
					    pointer-events: auto;
					    -webkit-transition: all 0.5s ease;
					    -o-transition: all 0.5s ease;
					    -moz-transition: all 0.5s ease;
					    transition: all 0.5s ease;
					    z-index: 2147483640;
					}
					.oxy-ou-off-canvas .ou-off-canvas-backdrop {
						background-color: rgba(0,0,0,.5);
						height: 100%;
					    top: 0;
					    left: 0;
					    right: 0;
					    bottom: 0;
					    position: fixed;
					    pointer-events: auto;
					    opacity: 0;
					    visibility: hidden;
					    -webkit-transition: all .45s ease;
					    -o-transition: all .45s ease;
					    -moz-transition: all .45s ease;
					    transition: all .45s ease;
					    width: 100%;
					    z-index: 2147483640;
					}
					body.oxygen-builder-body .oxy-ou-off-canvas .ou-off-canvas-backdrop,
					.oxy-ou-off-canvas.ou-panel-active .ou-off-canvas-backdrop {
						opacity:1;
						visibility: visible;
					}
					body:not(.oxygen-builder-body) .oxy-ou-off-canvas .ou-off-canvas-panel {
						visibility: hidden;
					}
					body:not(.oxygen-builder-body) .oxy-ou-off-canvas.ou-panel-active .ou-off-canvas-panel{
						visibility: visible;
					}
					body.oxygen-builder-body .oxy-ou-off-canvas .ct-component {
						opacity: 1!important;
						transform: none!important;
					}
					body:not(.oxygen-builder-body) .oxy-ou-off-canvas {
						display: block!important;
					}
					body:not(.oxygen-builder-body) .oxy-ou-off-canvas.ou-panel-active .ou-off-canvas-panel.ofc-front:not(.ou-push-content),
					body.oxygen-builder-body .oxy-ou-off-canvas .ou-off-canvas-panel {
						-webkit-transform: none!important;
						-ms-transform: none!important;
						-moz-transform: none!important;
						transform: none!important;
					}
					.ofc-front {
						-webkit-transform: translateX(100%);
	                    -moz-transform: translateX(100%);
	                    transform: translateX(100%);
					}
					.ocp-close-button {
						background-color: #e2e2e2;
						display: flex;
						color: #555;
						flex-direction: row;
						padding: 10px;
						margin-left: auto;
						margin-top: -10px;
						margin-right: -10px;
						align-items: center;
						justify-content: center;
						width: auto;

						-webkit-transition: all 0.3s;
        				-ms-transition: all 0.3s;
        				transition: all 0.3s;
					}
					.ocp-close-button svg {
						fill: currentColor;
						width: 20px;
						height: 20px;
					}
					body:not(.oxygen-builder-body) .ocp-close-button {
						cursor: pointer;
					}
					.ocp-close-button .btn-text {
						line-height: 1;
					}
					.ou-off-canvas-panel::-webkit-scrollbar {
						width: 6px;
					}
					.ou-off-canvas-panel::-webkit-scrollbar-track {
						background-color: var(--sb-light-color);
						border-radius: 0px;
					}
					.ou-off-canvas-panel::-webkit-scrollbar-thumb {
						border-radius: 0px;
						background-color: var(--sb-dark-color);
					}
					';

			$this->off_canvas_frontend_css = true;
        }

        $prefix = $this->El->get_tag();

        if( isset($original[$prefix . '_push_content']) && $original[$prefix . '_push_content'] == "yes" ) {
        	$td = isset($original[$prefix . '_panel_td']) ? $original[$prefix . '_panel_td'] : "0.5";
			$unit = isset( $original[$prefix . '_panel_width-unit'] ) ? $original[$prefix . '_panel_width-unit'] : 'px';
			$panel_width = (isset($original[$prefix . '_panel_width']) ? $original[$prefix . '_panel_width'] : '300' ) . $unit;
			$phunit = isset( $original[$prefix . '_panel_height-unit'] ) ? $original[$prefix . '_panel_height-unit'] : 'px';
			$panel_height = (isset($original[$prefix . '_panel_height']) ? $original[$prefix . '_panel_height'] : '350' ) . $phunit;
        	$bodySel = str_replace('#', '', $selector);
        	$css .= 'body.pc-' . $bodySel . '{
        				-webkit-transition: transform '. $td . 's;
        				-ms-transition: transform '. $td . 's;
        				transition: transform '. $td . 's;
        				overflow-x: hidden;
        			}
					body.pc-' . $bodySel . '-right {
						transform: translateX(-'.$panel_width.'); 
						-webkit-transform: translateX(-'.$panel_width.'); 
						s-ms-transform: translateX(-'.$panel_width.');
					}
					body.pc-' . $bodySel . '-top {
						transform: translateY('.$panel_height.'); 
						-webkit-transform: translateY('.$panel_height.'); 
						s-ms-transform: translateY('.$panel_height.');
					}
					body.pc-' . $bodySel . '-left {
						transform: translateX('.$panel_width.'); 
						-webkit-transform: translateX('.$panel_width.'); 
						s-ms-transform: translateX('.$panel_width.');
					}
					body.pc-' . $bodySel . '-bottom {
						transform: translateY(-'.$panel_height.'); 
						-webkit-transform: translateY(-'.$panel_height.'); 
						s-ms-transform: translateY(-'.$panel_height.');
					}';
        }

        return $css;
    }

    function ou_off_canvas_script() {
    ?>
    	<script type="text/javascript">
    		"use strict";var ouOffCanvasPanel=function(){document.querySelectorAll(".oxy-ou-off-canvas").forEach(function(t){var e=t.querySelector(".ou-off-canvas-panel"),n=t.querySelector(".ocp-close-button"),o=e.getAttribute("data-trigger-selector"),a=e.getAttribute("data-disable-scroll"),s=e.getAttribute("data-reveal"),i=0,c=parseInt(e.getAttribute("data-delay-in")),u=t.querySelector(".ou-off-canvas-backdrop");window.addEventListener("load",function(t){clearTimeout(i),i=setTimeout(function(){d()},20,!1),v()}),document.addEventListener("scroll",function(){t.classList.contains("ou-panel-active")||(clearTimeout(i),i=setTimeout(function(){d()},300,!1))}),window.addEventListener("keyup",function(t){27===t.keyCode&&r()}),["click","touchstart"].forEach(function(t){u.addEventListener(t,function(t){t.preventDefault(),r()}),null!=n&&n.addEventListener(t,function(t){t.preventDefault(),r()})},!1);var d=function(){var t=e.querySelectorAll(".aos-init");t.length&&t.forEach(function(t){t.classList.remove("aos-animate")})},l=function(){if(e.classList.contains("ou-push-content")&&(document.body.classList.add(e.getAttribute("data-pc-id")),document.body.classList.contains("admin-bar"))){let t=document.querySelector("#wpadminbar").offsetHeight;document.body.style.paddingTop=t+"px"}var n;if(d(),t.classList.add("ou-panel-active"),(n=e.querySelectorAll(".aos-init")).length&&n.forEach(function(t){t.classList.add("aos-animate")}),e.classList.contains("ou-push-content")){document.body.classList.contains("admin-bar")&&document.getElementsByTagName("html")[0].classList.add("ou-remove-spacing");let t=e.getAttribute("data-panel-position");document.body.classList.add(e.getAttribute("data-pc-id")+"-"+t)}},r=function(){if(t.classList.remove("ou-panel-active"),d(),"yes"==a&&f(),e.classList.contains("ou-push-content")){let t=1e3*parseFloat(e.getAttribute("data-content-td"))+20,n=e.getAttribute("data-panel-position");document.body.classList.remove(e.getAttribute("data-pc-id")+"-"+n),clearTimeout(i),i=setTimeout(function(){document.body.classList.contains("admin-bar")&&document.getElementsByTagName("html")[0].classList.remove("ou-remove-spacing"),document.body.classList.remove(e.getAttribute("data-pc-id")),document.body.classList.contains("admin-bar")&&(document.body.style.paddingTop="0px")},t,!1)}},m=function(e){e.preventDefault(),t.classList.contains("ou-panel-active")?r():l(),"yes"==a&&f()},f=function(){t.classList.contains("ou-panel-active")?document.body.classList.add("ou-disable-scroll"):document.body.classList.remove("ou-disable-scroll")},v=function(){"yes"==s&&(clearTimeout(i),i=setTimeout(function(){l()},c))};void 0!==o&&null!=o&&o.toString().split(",").forEach(function(t){document.querySelector(t).addEventListener("click",m),document.querySelector(t).addEventListener("touchstart",m)})})};document.addEventListener("DOMContentLoaded",function(){ouOffCanvasPanel()});
    	</script>
    <?php
    }

    function enableFullPresets() {
		return true;
	}
}

new OUOffCanvas();