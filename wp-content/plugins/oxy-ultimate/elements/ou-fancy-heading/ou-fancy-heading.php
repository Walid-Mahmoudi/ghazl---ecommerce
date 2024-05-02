<?php

namespace Oxygen\OxyUltimate;

Class OUFancyHeading extends \OxyUltimateEl {

	public $css_added = false; 

	function name() {
		return __( "Fancy Heading", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_fancy_heading";
	}

	function oxyu_button_place() {
		return "text";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function tag() {
		return $this->headingTagChoices();
	}
	
	function afterInit() {
        $this->removeApplyParamsButton();
        //$this->removeAddButton();
    }

	function controls() {
		$tfc = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Heading'),
				"slug" 		=> 'ou_fancy_text',
				"value" 	=> __("Fancy Headline!", "oxy-ultimate"),
				"base64" 	=> true
			)
		);
		$tfc->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicFHText">data</div>');
		$tfc->setParam("description", __("Enter <span style=\"color:#ff7171;\">&amp;apos;</span> for single quote.", "oxy-ultimate"));
		$tfc->rebuildElementOnChange();
		
		$this->addTagControl();

		$animc = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Animation Type' , "oxy-ultimate"),
				'slug' 		=> 'oufhanimation_type',
				'value' 	=> array(
					"anm_solid" 	=> __("Color", 'oxy-ultimate'),
					"anm_gradient" 	=> __("Gradient", 'oxy-ultimate'),		
					"anm_fade" 		=> __("Fade", 'oxy-ultimate'),
					"anm_rotate" 	=> __("Rotate", 'oxy-ultimate')
				),
				'default' 	=> "anm_fade",
				"css" 		=> false
			)
		);
		$animc->rebuildElementOnChange();

		$this->addOptionControl(
			array(
				"type" 		=> 'slider-measurebox',
				"name" 		=> __('Animation Speed'),
				"slug" 		=>'ou_anm_speed',
				"value" 	=> 2,
				"min"		=> 0,
				"max" 		=> 50
			)
		)->setUnits("sec", "sec");
		

		$selector = '.oufh-text';

		$pc = $this->addStyleControl(
			array(
				"name" 				=> __('Primary Color'),
				"selector" 			=> $selector,
				"value" 			=> "09a23d",
				"property" 			=> 'color'
			)
		);
		$pc->rebuildElementOnChange();
		
		$sc = $this->addStyleControl(
			array(
				"name" 				=> __('Secondary Color'),
				"slug" 				=> "oufhtext_sc_color",
				"property" 			=> 'color',
				"value" 			=> "343434"
			)
		);
		$sc->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_fancy_heading_oufhanimation_type'] == 'anm_gradient'");
		$sc->rebuildElementOnChange();

		$this->addStyleControls(
			array(
				array(
					"selector" 			=> $selector,
					"property" 			=> 'font-family',
					"css" 				=> false
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'font-weight'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'font-size',
					"value" 			=> 28,
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'line-height'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'letter-spacing'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'text-decoration'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'text-transform'
				)
			)
		);
	}

	function render( $options, $defaults, $content ) {
		$fancy_text = $options['ou_fancy_text'];
		if( strstr( $fancy_text, 'oudata_') ) {
			$fancy_text = base64_decode( str_replace( 'oudata_', '', $fancy_text ) );
			$shortcode = ougssig( $this->El, $fancy_text );
			$fancy_text = do_shortcode( $shortcode );
		} elseif( strstr( $fancy_text, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($fancy_text));
			$fancy_text = do_shortcode($shortcode);
		}

		echo '<span class="oufh-text">' . $fancy_text . '</span>';
	}

	function customCSS($original, $selector) {
		$css = $defaultCSS = '';
		
		if( ! $this->css_added ) {	
			$defaultCSS = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = 'oxy-' . $this->slug();
		$animation_type = ! empty($original[$prefix . '_oufhanimation_type']) ? $original[$prefix . '_oufhanimation_type'] : 'anm_fade';
		$animation_speed = ! empty($original[$prefix . '_ou_anm_speed']) ? $original[$prefix . '_ou_anm_speed'] : 2; 

		if( $animation_type === 'anm_fade' ) {
			$css = '-webkit-animation: oufh-fade ' . $animation_speed .'s infinite linear; animation: oufh-fade ' . $animation_speed .'s infinite linear;';
		}

		if( $animation_type === 'anm_solid' ) {
			$css = '-webkit-animation: oufh-hue ' . $animation_speed .'s infinite linear; animation: oufh-hue ' . $animation_speed .'s infinite linear;';
		}

		if( $animation_type === 'anm_rotate' ) {
			$css = '-webkit-animation: oufh-rotate ' . $animation_speed .'s infinite linear; animation: oufh-rotate ' . $animation_speed .'s infinite linear;';
		}

		if( $animation_type === 'anm_gradient' ) {
			$css = 'background-image: -webkit-linear-gradient(92deg, ' . $original[$prefix . '_slug_oufhtext_color'] .', ' . $original['oufhtext_sc_color'] .');-webkit-background-clip: text;-webkit-text-fill-color: transparent;-webkit-animation: oufh-hue ' . $animation_speed .'s infinite linear; animation: oufh-hue ' . $animation_speed .'s infinite linear;';
		}

		if( ! empty( $css ) ) {
			$css = $selector . ' .oufh-text{' . $css . '}';
		}

		return $defaultCSS . $css;
	}
}

new OUFancyHeading();