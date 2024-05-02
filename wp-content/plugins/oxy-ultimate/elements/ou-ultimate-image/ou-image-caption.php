<?php
namespace Oxygen\OxyUltimate;

Class OUImageCaption extends \OxyUltimateEl {
	
	function name() {
		return __( "Caption Wrapper", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_imgcap";
	}

	function button_place() {
		return "ouimage::comp";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}

	function controls() {
		$this->addCustomControl('<p style="color: #fff">Here have no extra controls. You will adjust colors, font, size, animation etc from Advanced tab.</p>', 'desc');
	}

	function render( $options, $defaults, $content ) {
		if( $content ) {
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
		}
	}
}

new OUImageCaption();