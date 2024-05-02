<?php

namespace Oxygen\OxyUltimate;

Class OUDualColorText extends \OxyUltimateEl {

	function name() {
		return __( "Dual Color Text", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_dual_color_text";
	}

	function oxyu_button_place() {
		return "text";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function tag() {
		$tags = array('default' => 'h4', 'choices' => 'h1,h2,h3,h4,h5,h6,div,p' );
		return $tags;
	}

	function controls() {
		$this->addTagControl();

		$text1 = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('First Text', "oxy-ultimate"),
				"slug" 		=> 'oudct_text1',
				"value" 	=> __("This is", "oxy-ultimate"),
				"css" 		=> false
			)
		);

		$text2 = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Second Text', "oxy-ultimate"),
				"slug" 		=> 'oudct_text2',
				"value" 	=> __("dual color text component.", "oxy-ultimate"),
				"css" 		=> false
			)
		);

		$this->addStyleControl(
			array(
				'name' 			=> __('Space between two texts', "oxy-ultimate"),
				"control_type" 	=> 'slider-measurebox',
				'property' 		=> 'margin-left',
				"selector"		=> '.oudct-text2',
				"default" 		=> '0'
			)
		);

		$this->typographySection(__("Style for First Text", "oxy-ultimate"), '.oudct-text1', $this );
		$this->typographySection(__("Style for Second Text", "oxy-ultimate"), '.oudct-text2', $this );
	}

	function render() {
		return '<span class="oudct-text1">%%oudct_text1%%</span> <span class="oudct-text2">%%oudct_text2%%</span>';
	}

	function options() {
		return array(
			'server_side_render' => false
		);
	}
}

new OUDualColorText();