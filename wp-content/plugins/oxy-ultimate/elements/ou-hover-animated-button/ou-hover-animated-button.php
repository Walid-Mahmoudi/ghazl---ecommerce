<?php

namespace Oxygen\OxyUltimate;

class OUHoverAnimatedButton extends \OxyUltimateEl
{
	public $css_added = false;
	
	function name() {
		return __( "Hover Animated Button", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_ha_button";
	}

	function oxyu_button_place() {
		return "buttons";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">If changes are not applying on Builder editor, you will click on the <span style="color:#ff7171;">Apply Params</span> button to get the correct preview.</div>'), 
			'description'
		)->setParam('heading', 'Note:');
		
		$btnTxt = $this->addOptionControl(
			array(
				"type" 			=> "textfield",
				"name" 			=> __('Button Text', "oxy-ultimate"),
				"slug" 			=> "btn_text",
				"placeholder" 	=> __( "Button" ),
				"css"			=> false,
			)
		);
		$btnTxt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicHABText">data</div>');
		$btnTxt->setParam( 'description', __('Enter <span style=\"color:#ff7171;\">&amp;apos;</span> for single quote.', "oxy-ultimate") );

		$btnURL = $this->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_ha_button_btn_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_ha_button_btn_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_ha_button_btn_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_ha_button_btn_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_ha_button_btn_url\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicHABUrl">data</div>
			</div>
			',
			"btn_url"
		);
		$btnURL->setParam( 'heading', __('URL') );

		$this->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "btn_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self"
			)
		);

		$this->addOptionControl(
			array(
				"name" 			=> __('Follow', "oxy-ultimate"),
				"slug" 			=> "btn_follow",
				"type" 			=> 'radio',
				"value" 		=> ["follow" => __("Follow") , "nofollow" => __("No Follow")],
				"default"		=> "nofollow"
			)
		);

		$btnHvrAnim = $this->addOptionControl(
			array(
				"name" 			=> __('Hover Animation', "oxy-ultimate"),
				"slug" 			=> "btn_hover_effect",
				"type" 			=> 'dropdown',
				"value" 		=> array(
					'none'  		=> __("General"),
					'sweep_top'		=> __("Sweep top"),
					'sweep_bottom'	=> __("Sweep Bottom"),
					'sweep_left'	=> __("Sweep Left"),
					'sweep_right'	=> __("Sweep Right"),
					'bounce_top'	=> __("Bounce top"),
					'bounce_bottom'	=> __("Bounce Bottom"),
					'bounce_left'	=> __("Bounce Left"),
					'bounce_right'	=> __("Bounce Right"),
					'sinh'			=> __("Shutter In Horizontal"),
					'souh'			=> __("Shutter Out Horizontal"),
					'sinv'			=> __("Shutter In Vertical"),
					'souv'			=> __("Shutter Out Vertical"),
				),
				"default" 		=> "sweep_right",
				"css"			=> false
			)
		);
		$btnHvrAnim->rebuildElementOnChange();

		$this->addStyleControl(
			array(
				"name" 			=> __('Transition Duration', "oxy-ultimate"),
				'property' 		=> 'transition-duration',
				'selector' 		=> 'a.ou-ha-button, a.ou-ha-button:before',
				"slug" 			=> "btn_ts",
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '0.2'
			)
		)->setRange('0.2', '10', '0.1')->setUnits("s", "sec");

		/**********************
		 * Button icon section
		 *********************/
		$btnIcon = $this->addControlSection( "habicon_section", __('Icon'), 'assets/icon.png', $this );

		$disp = $btnIcon->addOptionControl(
			array(
				"name" 			=> __('Display Icon', "oxy-ultimate"),
				"slug" 			=> "btn_enable_icon",
				"type" 			=> 'radio',
				"value" 		=> ["yes" => __("Yes") , "no" => __("No")],
				"default"		=> "yes"
			)
		);
		$disp->rebuildElementOnChange();

		$buttonicon = $btnIcon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon'),
				"slug" 			=> 'btn_icon'
			)
		);
		$buttonicon->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_ha_button_btn_enable_icon']=='yes'");
		$buttonicon->rebuildElementOnChange();

		$btnIconPos = $btnIcon->addOptionControl(
			array(
				"name" 			=> __('Icon Position', "oxy-ultimate"),
				"slug" 			=> "btn_icon_pos",
				"type" 			=> 'radio',
				"value" 		=> ["left" => __("Left") , "right" => __("Right")],
				"default"		=> "right"
			)
		);
		$btnIconPos->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_ha_button_btn_enable_icon']=='yes'");
		$btnIconPos->rebuildElementOnChange();
		
		$btnIcon->addStyleControl(
			array(
				"name" 			=> __('Icon Size'),
				"slug" 			=> "btn_icon_size",
				"selector" 		=> '.btn-icon-wrap > svg',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height',
				"condition" 	=> "btn_enable_icon=yes"
			)
		)->setRange(20, 80, 10)->setUnits("px", "px");

		$iconSp = $btnIcon->addControlSection('habicon_space', __("Spacing", "oxy-ultimate"), "assets/icon.png", $this);

		$iconSp->addPreset(
			"padding",
			"iconsp_padding",
			__("Padding"),
			'.btn-icon-wrap'
		)->whiteList();

		$iconSp->addPreset(
			"margin",
			"iconsp_margin",
			__("Margin"),
			'.btn-icon-wrap'
		)->whiteList();

		$btnIcon->borderSection(__("Border"), '.btn-icon-wrap', $this );

		$iconColors = $btnIcon->addControlSection('habicon_color', __("Colors", "oxy-ultimate"), "assets/icon.png", $this);
		$iconColors->addStyleControls([
			[
				'selector' 		=> '.btn-icon-wrap',
				'property' 		=> 'background-color'
			],

			[
				'selector' 		=> '.ou-ha-button:hover .btn-icon-wrap',
				'property' 		=> 'background-color',
				'name' 			=> __('Hover Background Color'),
				'slug' 			=> 'habicon_hbgclr'
			],

			[
				'selector' 		=> '.btn-icon-wrap svg',
				'property' 		=> 'color',
				'slug' 			=> 'habicon_clr'
			],

			[
				'selector' 		=> '.ou-ha-button:hover .btn-icon-wrap svg',
				'property' 		=> 'color',
				'name' 			=> __('Hover Color'),
				'slug' 			=> 'habicon_hclr'
			]
		]);


		$btn_section = $this->addControlSection('btn_style_section', __("Style", "oxy-ultimate"), "assets/icon.png", $this);
		$selector = '.ou-ha-button';

		$btnfonts = $btn_section->typographySection( __("Typography"), $selector, $this );
		$habtxtSp = $btn_section->addControlSection('habtxt_space', __("Text Spacing", "oxy-ultimate"), "assets/icon.png", $this);

		$habtxtSp->addPreset(
			"padding",
			"habtxtsp_padding",
			__("Padding"),
			$selector . ' .btn-text'
		)->whiteList();

		$btnfonts->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color', "oxy-ultimate"),
					"slug" 				=> 'btn_bg_color',
					"selector" 			=> $selector,
					"property" 			=> 'background-color',
					"default" 			=> "#333333"
				),
				array(
					"name" 				=> __('Background Hover Color', "oxy-ultimate"),
					"selector" 			=> $selector . ':hover,' . $selector .':before',
					"slug" 				=> 'btn_hv_bg_color',
					"property" 			=> 'background-color',
					"default" 			=> "#f90c0c"
				),
				array(
					"name" 				=> __('Hover Text Color', "oxy-ultimate"),
					"selector" 			=> $selector . ':hover,' . $selector .':before, .ou-ha-button:hover .btn-icon-wrap',
					"property" 			=> 'color',
					"default" 			=> "#ffffff"
				)
			)
		);

		$btn_section->borderSection(__("Border"), $selector, $this );
		$btn_section->boxShadowSection(__("Box Shadow"), $selector, $this );

		$btn_section->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> '.ou-ha-button',
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox'
			)
		)
		->setUnits("px", "px,%,auto")
		->setRange("0", "500", "10");

		$btn_section->addPreset(
			"padding",
			"btnha_padding",
			__("Padding"),
			'.ou-ha-button'
		)->whiteList();
	}

	function fetchDynamicData( $field ) {
		if( strstr( $field, 'oudata_') ) {
			$field = base64_decode( str_replace( 'oudata_', '', $field ) );
			$shortcode = ougssig( $this->El, $field );
			$field = do_shortcode( $shortcode );
		} elseif( strstr( $field, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($field));
			$field = do_shortcode($shortcode);
		}

		return $field;
	}

	function render( $options, $defaults, $content) {
		$hasIcon = $btn_icon = '';
		$hover_effect = isset( $options['btn_hover_effect'] ) ? $options['btn_hover_effect'] : 'sweep_right';

		if( isset( $options['btn_icon'] ) && $options['btn_enable_icon'] == "yes" ) {
			global $oxygen_svg_icons_to_load;
        	$oxygen_svg_icons_to_load[] = $options['btn_icon'];

			$btn_icon = '<svg id="' . $options['selector'] . '-btn-icon" class="btn-icon"><use xlink:href="#' . $options['btn_icon'] . '"></use></svg>';
			$hasIcon = ' has-btn-icon';
		}
		
		echo '<div class="ou-ha-button-container hover-effect-'. $hover_effect . $hasIcon . '">';
		$btn_url = '#';
		if( isset( $options['btn_url'] ) ) {
			$btn_url = $this->fetchDynamicData( $options['btn_url'] );
		}

		echo '<a href="'. esc_url( $btn_url ) . '" target="'. $options['btn_target'] . '" rel="'. $options['btn_follow'] . '" role="button" aria-label="hidden" class="ou-ha-button ou-button-effect">';

		if( ! empty( $btn_icon) && ! empty( $options['btn_icon_pos'] ) && $options['btn_icon_pos'] === "left" )
			echo '<span class="btn-icon-wrap btn-icon-align' . $options['btn_icon_pos'] . '">' . $btn_icon . '</span>';

		if( ! isset( $options['btn_text'] ) && empty( $btn_icon ) ) {
			$btnText = __('Button');
		} else {
			$btnText = $this->fetchDynamicData( $options['btn_text'] );
		}

		echo '<span class="btn-text">' . $btnText . '</span>';

		if( ! empty( $btn_icon) && ! empty( $options['btn_icon_pos'] ) && $options['btn_icon_pos'] === "right" )
			echo '<span class="btn-icon-wrap btn-icon-align' . $options['btn_icon_pos'] . '">' . $btn_icon . '</span>';

		echo '</a></div>';
	}

	function customCSS( $original, $selector ) {
		$css = '';
		$prefix = $this->El->get_tag();
		$hover_effect = isset( $original[ $prefix . '_btn_hover_effect' ] ) ? $original[ $prefix . '_btn_hover_effect' ] : 'sweep_right';
		$hover_bgclr = isset( $original[ $prefix . '_btn_bg_color' ] ) ? $original[ $prefix . '_btn_bg_color' ] : '#f90c0c';

		if( ! $this->css_added ) {
			$css .= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}
		
		if( ! empty( $hover_bgclr ) ) {
			$css .= $selector . ' .ou-ha-button-container:not(.hover-effect-none) .ou-ha-button:hover{ background-color:'. $hover_bgclr .';}';
		}

		if( ! empty( $hover_effect ) ) {
			$css .= ou_button_hover_effect( $hover_effect );
		}

		return $css;
	}
}

new OUHoverAnimatedButton();