<?php

namespace Oxygen\OxyUltimate;

class OUDualButton extends \OxyUltimateEl
{
	public $css_added = false;
	
	function name() {
		return __( "Dual Button", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_dual_button";
	}

	function oxyu_button_place() {
		return "buttons";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on the <span style="color:#ff7171;">Apply Params</span> button to see the changes.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$btn1_section = $this->addControlSection( "btn1_section", __("Button 1", "oxy-ultimate"), "assets/icon.png", $this );
		$btn1txt = $btn1_section->addOptionControl(
			array(
				"type" 			=> "textfield",
				"name" 			=> __('Text', "oxy-ultimate"),
				"slug" 			=> "btn1content",
				"value" 		=> "Button 1",
				"base64" 		=> true
			)
		);
		$btn1txt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicDBFText">data</div>');
		$btn1txt->setParam("description", __("Enter <span style=\"color:#ff7171;\">&amp;apos;</span> for single quote.", "oxy-ultimate"));

		$btn1URL = $btn1_section->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_dual_button_btn1_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_dual_button_btn1_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_dual_button_btn1_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_dual_button_btn1_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_dual_button_btn1_url\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicDBFUrl">data</div>
			</div>
			',
			"btn1_url",
			$btn1_section
		);
		$btn1URL->setParam( 'heading', __('URL') );
		$btn1URL->setParam( 'css', false );

		$btn1_section->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "btn1_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self",
				"css"			=> false,
			)
		);

		$btn1_section->addOptionControl(
			array(
				"name" 			=> __('Hover Animation', "oxy-ultimate"),
				"slug" 			=> "hover1_effect",
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
				"default" 		=> "none",
				"css"			=> false
			)
		);

		$btn1_section->addOptionControl(
			array(
				"name" 			=> __('Transition Speed', "oxy-ultimate"),
				"slug" 			=> "btn1ts",
				"type" 			=> 'measurebox',
				"value" 		=> 0.2,
				"css"			=> false
			)
		)->setUnits("sec", "sec");

		//* Button 1 Style
		$btn1_selector = '.ou-dual-button-1 .ou-dual-button';
		$btn1_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color', "oxy-ultimate"),
					"slug" 				=> 'btn1_bg_color',
					"selector" 			=> $btn1_selector,
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Background Hover Color', "oxy-ultimate"),
					"selector" 			=> $btn1_selector . ':hover,' . $btn1_selector .':before',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Text Color', "oxy-ultimate"),
					"selector" 			=> $btn1_selector,
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Hover Text Color', "oxy-ultimate"),
					"selector" 			=> $btn1_selector . ':hover,' . $btn1_selector .':before',
					"property" 			=> 'color'
				)
			)
		);

		$btn1_section->typographySection( __("Typography", "oxy-ultimate"), $btn1_selector, $this );
		$btn1_section->borderSection(__("Border"), $btn1_selector, $this );
		$btn1_section->boxShadowSection(__("Box Shadow"), $btn1_selector, $this );

		
		/**************************
		 * Button 2
		 *************************/

		$btn2_section = $this->addControlSection( "btn2_section", __("Button 2", "oxy-ultimate"), "assets/icon.png", $this );
		$btn2txt = $btn2_section->addOptionControl(
			array(
				"type" 			=> "textfield",
				"name" 			=> __('Text', "oxy-ultimate"),
				"slug" 			=> "btn2content",
				"value" 		=> "Button 2",
				"base64" 		=> true
			)
		);
		$btn2txt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicDBSText">data</div>');
		$btn2txt->setParam("description", __("Enter <span style=\"color:#ff7171;\">&amp;apos;</span> for single quote.", "oxy-ultimate"));

		$btn2URL = $btn2_section->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_dual_button_btn2_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_dual_button_btn2_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_dual_button_btn2_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_dual_button_btn2_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_dual_button_btn2_url\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicDBSUrl">data</div>
			</div>
			',
			"btn2_url",
			$btn2_section
		);
		$btn2URL->setParam( 'heading', __('URL') );
		$btn2URL->setParam( 'css', false );

		$btn2_section->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "btn2_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self",
				"css"			=> false
			)
		);

		$btn2_section->addOptionControl(
			array(
				"name" 			=> __('Hover Animation', "oxy-ultimate"),
				"slug" 			=> "hover2_effect",
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
				"default" 		=> "none",
				"css"			=> false
			)
		);

		$btn2_section->addOptionControl(
			array(
				"name" 			=> __('Transition Speed', "oxy-ultimate"),
				"slug" 			=> "btn2ts",
				"type" 			=> 'measurebox',
				"value" 		=> 0.2,
				"css"			=> false
			)
		)
		->setUnits("sec", "sec");
		

		//* Button 2 Style
		$btn2_selector = '.ou-dual-button-2 .ou-dual-button';
		$btn2_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color', "oxy-ultimate"),
					"slug" 				=> 'btn2_bg_color',
					"selector" 			=> $btn2_selector,
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker',
				),
				array(
					"name" 				=> __('Background Hover Color', "oxy-ultimate"),
					"selector" 			=> $btn2_selector . ':hover,' . $btn2_selector .':before',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker',
				),
				array(
					"name" 				=> __('Text Color', "oxy-ultimate"),
					"selector" 			=> $btn2_selector,
					"property" 			=> 'color'
				),
				array(
					"name" 				=> __('Hover Text Color', "oxy-ultimate"),
					"selector" 			=> $btn2_selector . ':hover,' . $btn2_selector .':before',
					"property" 			=> 'color'
				)
			)
		);

		$btn2_section->typographySection( __("Typography", "oxy-ultimate"), $btn2_selector, $this );
		$btn2_section->borderSection(__("Border"), $btn2_selector, $this );
		$btn2_section->boxShadowSection(__("Box Shadow"), $btn2_selector, $this );

		/**************************
		 * Middle Text
		 *************************/

		$middleText = $this->addControlSection( "middleText", __("Middle Text", "oxy-ultimate"), "assets/icon.png", $this );
		$showMT = $middleText->addOptionControl(
			array(
				"type" 			=> "radio",
				"name" 			=> __('Show Middle Text', "oxy-ultimate"),
				"slug" 			=> "show_mt",
				"value" 		=> ["show" => __("Show", "oxy-ultimate") , "hide" => __("Hide", "oxy-ultimate")],
				"default"		=> "hide",
				"css"			=> false
			)
		);
		$showMT->rebuildElementOnChange();

		$mtxt = $middleText->addOptionControl(
			array(
				"type" 			=> "textfield",
				"name" 			=> __('Text', "oxy-ultimate"),
				"slug" 			=> "middle_text",
				"value" 		=> "OR",
				"css"			=> false,
			)
		);
		$mtxt->rebuildElementOnChange();

		$widthMT = $middleText->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> '.middle-text',
				"slug" 				=> "middle_text_width",
				"property" 			=> 'width|height|line-height',
				"control_type" 		=> 'slider-measurebox',
				"value" 			=> '32',
				"min"				=> "0",
				"max"				=> "100"
			)
		);
		$widthMT->setUnits("px", "px,%,em");
		$widthMT->rebuildElementOnChange();

		$middleText->addStyleControls(
			array(
				array(
					"selector" 			=> '.middle-text',
					"property" 			=> "background-color"
				),
				array(
					"selector" 			=> '.middle-text',
					"property" 			=> "color"
				),
				array(
					"selector" 			=> '.middle-text',
					"property" 			=> "font-family"
				),
				array(
					"selector" 			=> '.middle-text',
					"property" 			=> "font-size"
				),
				array(
					"selector" 			=> '.middle-text',
					"property" 			=> "font-weight"
				),
				array(
					"selector" 			=> '.middle-text',
					"property" 			=> "border-radius"
				),
			)
		);
				

		/**************************
		 * Width & Space
		 *************************/

		$other_section = $this->addControlSection( "width_space", __("Width & Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$other_section->addPreset(
			"padding",
			"btn_padding",
			__("Padding"),
			'.ou-dual-button'
		)->whiteList();

		$other_section->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> '.ou-dual-button',
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox',
				"value" 			=> 220,
			)
		)
		->setUnits("px", "px")
		->setRange("0", "500", "10");

		$gap = $other_section->addStyleControl(	
			array(
				"name" 				=> __('Gap'),
				"selector" 			=> '.ou-space',
				"property" 			=> 'margin-left',
				"control_type" 		=> 'slider-measurebox',
				"value" 			=> '10',
				"min"				=> "0",
				"max"				=> "50",
			)
		);
		$gap->setUnits("px", "px");

		$resp = $other_section->addOptionControl(
			array(
				"name" 				=> __('Responsive Breakpoint(px)', "oxy-ultimate"),
				"desc" 				=> __('Buttons will be stacked on top of each other.'),
				"slug" 				=> "btn_breakpoint",
				"type" 				=> 'textfield',
				"default" 			=> 580
			)
		);
		$resp->setParam("description", __('Second button will stack below the first button at your selected breakpoint.', "oxy-ultimate"));
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

	function render($options, $defaults, $content ) {
		$btn1_url = $btn2_url = '#';

		if( isset( $options['btn1content'] ) ) {
			$btn1content = $this->fetchDynamicData( $options['btn1content'] );
		}

		if( isset( $options['btn1_url'] ) ) {
			$btn1_url = $this->fetchDynamicData( $options['btn1_url'] );
		}

		if( isset( $options['btn2content'] ) ) {
			$btn2content = $this->fetchDynamicData( $options['btn2content'] );
		}

		if( isset( $options['btn2_url'] ) ) {
			$btn2_url = $this->fetchDynamicData( $options['btn2_url'] );
		}
	?>

		<div class="ou-dual-button-content clearfix" data-breakpoint="<?php echo $options['btn_breakpoint']; ?>">
			<div class="ou-dual-button-inner">
				<?php if( ! empty( $btn1content ) ) : ?>
				<div class="ou-dual-button-1 hover-effect-<?php echo $options['hover1_effect']; ?> ou-dual-button-wrap">
					<a href="<?php echo esc_url( $btn1_url ); ?>" class="ou-dual-button ou-button-effect" aria-label="hidden" role="button" target="<?php echo $options['btn1_target']; ?>">
						<span class="ou-button-1-text ou-button-text"><?php echo $btn1content; ?></span>
					</a>
				</div>
				<?php endif; ?>
				<?php if( isset( $options['middle_text'] ) ) : ?>				
					<div class="mt-<?php echo $options['show_mt']; ?> middle-text"><?php echo $options['middle_text']; ?></div>
				<?php endif; ?>
				<?php if( ! empty( $btn2content ) ) : ?>
				<div class="ou-dual-button-2 ou-dual-button-wrap hover-effect-<?php echo $options['hover2_effect']; ?> ou-space">
					<a href="<?php echo esc_url( $btn2_url ); ?>" class="ou-dual-button ou-button-effect" aria-label="hidden" role="button" target="<?php echo $options['btn2_target']; ?>">
						<span class="ou-button-2-text ou-button-text"><?php echo $btn2content; ?></span>
					</a>
				</div>
				<?php endif; ?>
			</div>
		</div>
	<?php
	}

	function customCSS($original, $selector) {
		$css = '';

		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();

		if( isset( $original[ $prefix . '_hover1_effect' ] ) ) {
			$css .= ou_button_hover_effect( $original[ $prefix . '_hover1_effect' ] );
		}

		if( isset( $original[ $prefix . '_hover2_effect' ] ) ) {
			$css .= ou_button_hover_effect( $original[ $prefix . '_hover2_effect' ] );
		}

		if( isset( $original[ $prefix . "_middle_text_width"] ) ) {
			$mtwidth = $original[ $prefix . "_middle_text_width"] . 'px' ;
			$css .= $selector . ' .middle-text{ top: calc( 50% - ( ' . $mtwidth . ' / 2 ) ); left: calc( 50% - ( ' . $mtwidth . ' / 2 ) ); }';
		}

		if( isset( $original[ $prefix . "_btn1ts"] ) ) {
			$css .= $selector . " .ou-dual-button-1 .ou-dual-button, " . $selector . " .ou-dual-button-1 .ou-dual-button:before{ transition-duration:". $original[ $prefix . "_btn1ts" ] . "s }";
		}

		if( isset( $original[ $prefix . "_btn2ts"] ) ) {
			$css .= $selector . " .ou-dual-button-2 .ou-dual-button," . $selector . " .ou-dual-button-2 .ou-dual-button:before{ transition-duration:". $original[ $prefix . "_btn2ts" ] . "s }";
		}

		if( isset( $original[ $prefix . "_btn1_bg_color"] ) ) {
			$css .= $selector . " .ou-dual-button-1:not(.hover-effect-none) .ou-dual-button:hover { background-color:". $original[ $prefix . "_btn1_bg_color" ] . "; }";
		}

		if( isset( $original[ $prefix . "_btn2_bg_color"] ) ) {
			$css .= $selector . " .ou-dual-button-2:not(.hover-effect-none) .ou-dual-button:hover { background-color:". $original[ $prefix . "_btn2_bg_color"] . "; }";
		}

		if( isset( $original[ $prefix . '_btn_breakpoint'] ) ) {
			$css .= "@media (max-width: " . $original[ $prefix . '_btn_breakpoint'] . "px){ 
						" . $selector . " .ou-space {
							margin-left: 0;
							margin-top: ". $original['oxy-ou_dual_button_slug_ouspace_margin_left'] . "px ;
						}

						" . $selector . " .ou-dual-button-wrap{float: none; clear: both;}
					}";
		}

		return $css;
	}
}

new OUDualButton();