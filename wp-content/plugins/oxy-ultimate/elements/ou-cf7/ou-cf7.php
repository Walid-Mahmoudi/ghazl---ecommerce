<?php

namespace Oxygen\OxyUltimate;

if ( ! class_exists( 'WPCF7' ) )
	return;

class OUCF7 extends \OxyUltimateEl {

	function name() {
		return __( "Contact Form 7", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_cf7_styler";
	}

	function oxyu_button_place() {
		return "form";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		/*****************************
		 * Fluent Form Drop Down
		 *****************************/
		$this->addOptionControl( 
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('Contact Form 7' , "oxy-ultimate"),
				'slug' 		=> 'cf7_forms',
				'value' 	=> $this->oxyu_get_cf7_forms(),
				'default' 	=> "no"
			)
		);

		/*****************************
		 * Form Wrapper
		 *****************************/
		$cf7_wrapper_section = $this->addControlSection( "cf7_wrapper_section", __("Form Wrapper", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = '.wpcf7-form';

		$cf7_wrapper_section->addPreset(
			"padding",
			"fw_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$cf7_wrapper_section->addPreset(
			"margin",
			"fw_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$cf7_wrapper_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> $selector,
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker',
				)
			)
		);

		$cf7_wrapper_section->borderSection( __("Border"), $selector, $this );


		/*****************************
		 * Form Label
		 *****************************/
		$selector = '.wpcf7-form label';
		$this->typographySection( __("Form Label"), $selector, $this );


		/*****************************
		 * Input Fields
		 *****************************/
		$cf7_inpsec = $this->addControlSection( "cf7_input", __("Input Fields", "oxy-ultimate"), "assets/icon.png", $this );
		$form_control_selector = '.wpcf7-form-control';
		
		$config = $cf7_inpsec->addControlSection( "inp_config", __("Config", "oxy-ultimate"), "assets/icon.png", $this );
		$config->addStyleControls(
			array(
				array(
					"selector" 			=> $form_control_selector,
					"property" 			=> 'width'
				),
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> $form_control_selector,
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Focus Background Color'),
					"selector" 			=> $form_control_selector . ':focus',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Placeholder Color'),
					"slug" 				=> "cf7_inp_placeholder",
					"selector" 			=> $form_control_selector . '::-webkit-input-placeholder, ' . $form_control_selector . ':-ms-input-placeholder, ' . $form_control_selector . '::-moz-input-placeholder, ' . $form_control_selector . '::-moz-placeholder',
					"property" 			=> 'color'
				),
				array(
					"name" 				=> __('Textarea Height'),
					"selector" 			=> '.wpcf7-textarea',
					"property" 			=> 'height'
				)
			)
		);

		$spacing = $cf7_inpsec->addControlSection( "cf7_inputsp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"cf7inp_padding",
			__("Padding"),
			$form_control_selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cf7inp_margin",
			__("Margin"),
			$form_control_selector
		)->whiteList();

		$cf7_inpsec->typographySection( __("Typography"), $form_control_selector, $this );
		$inpbrd = $cf7_inpsec->borderSection( __("Border"), $form_control_selector, $this );
		$inpbrd->addStyleControl(
			array(
				"name" 				=> __('Focus Border Color'),
				"selector" 			=> $form_control_selector . ':focus',
				"property" 			=> 'border-color'
			)
		);

		$cf7_inpsec->boxShadowSection( __("Shadow", "oxy-ultimate"), $form_control_selector, $this );
		$cf7_inpsec->boxShadowSection( __("Focus Shadow", "oxy-ultimate"), $form_control_selector . ":focus", $this );



		/*****************************
		 * Input Fields Error
		 *****************************/
		$errfield_selector = '.wpcf7-not-valid-tip';
		$err_section = $this->typographySection( __("Inline Error", "oxy-ultimate"), $errfield_selector, $this );



		/*****************************
		 * Submit Button
		 *****************************/
		$btn_selector = ".wpcf7-submit";
		$submit_btn = $this->addControlSection( "cf7_submit_btn", __("Submit Button", "oxy-ultimate"), "assets/icon.png", $this );

		$config = $submit_btn->addControlSection( "btn_config", __("Config", "oxy-ultimate"), "assets/icon.png", $this );
		$config->addStyleControls(
			array(
				array(
					"selector" 			=> $btn_selector,
					"property" 			=> 'width'
				),
				array(
					"name" 				=> __('Text Color', "oxy-ultimate"),
					"selector" 			=> $btn_selector,
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Text Color on Hover', "oxy-ultimate"),
					"selector" 			=> $btn_selector . ':hover',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Background Color', "oxy-ultimate"),
					"selector" 			=> $btn_selector,
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Background Color On Hover', "oxy-ultimate"),
					"selector" 			=> $btn_selector . ':hover',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				)
			)
		);

		$spacing = $submit_btn->addControlSection( "subbtn_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"cf7btn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cf7btn_margin",
			__("Margin"),
			$btn_selector
		)->whiteList();

		$submit_btn->typographySection( __("Typography"), $btn_selector, $this );
		$submit_btn->borderSection( __("Border"), $btn_selector, $this );
		$submit_btn->borderSection( __("Hover Border", "oxy-ultimate"), $btn_selector . ":hover", $this );
		$submit_btn->boxShadowSection( __("Box Shadow"), $btn_selector, $this );
		$submit_btn->boxShadowSection( __("Hover Box Shadow", "oxy-ultimate"), $btn_selector . ":hover", $this );



		/*****************************
		 * Error Message
		 *****************************/
		$msg = $this->addControlSection( "cf7_msg", __("Message", "oxy-ultimate"), "assets/icon.png", $this );
		
		$spacing = $msg->addControlSection( "msg_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"sucmsg_padding",
			__("Padding"),
			'.wpcf7-response-output'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"sucmsg_margin",
			__("Margin"),
			'.wpcf7-response-output'
		)->whiteList();

		$msg->addStyleControl(
			array(
				"selector" 			=> '.wpcf7-response-output',
				"property" 			=> 'background-color'
			)
		);

		$verr = $msg->typographySection( __("Validation Errors", "oxy-ultimate"), ".wpcf7-validation-errors,.wpcf7-acceptance-missing", $this );

		$verr->addStyleControl(
			array(
				"selector" 			=> '.wpcf7-response-output.wpcf7-validation-errors',
				"property" 			=> 'border-color'
			)
		);

		$verr->addStyleControl(
			array(
				"selector" 			=> '.wpcf7-response-output.wpcf7-validation-errors',
				"property" 			=> 'background-color'
			)
		);

		$err = $msg->typographySection( __("Errors"), ".wpcf7-mail-sent-ng,.wpcf7-aborted", $this );
		$err->addStyleControl(
			array(
				"selector" 			=> '.wpcf7-response-output.wpcf7-mail-sent-ng',
				"property" 			=> 'border-color'
			)
		);

		$err->addStyleControl(
			array(
				"selector" 			=> '.wpcf7-response-output.wpcf7-mail-sent-ng',
				"property" 			=> 'background-color'
			)
		);


		$msg->typographySection( __("Success Message"), ".wpcf7-mail-sent-ok", $this );

		$msg->borderSection( __("Border"), '.wpcf7-response-output', $this );
		$msg->boxShadowSection( __("Box Shadow"), '.wpcf7-response-output', $this );

		/*$ffrc_section = $this->addControlSection( "ffrc_section", __("Checkbox & Radio", "oxy-ultimate"), "assets/icon.png", $this );

		$ffrc_section->addOptionControl(
			array(
				"type"			=> "checkbox",
				"name" 			=> __('Enable Smart UI'),
				"slug" 			=> "rc_smart_ui"
			)
		);

		$ffrc_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Border Color'),
					"selector" 			=> '.ff-el-group input[type=checkbox]:after,.ff-el-group input[type=radio]:after',
					"property" 			=> 'border-color',					
					"control_type" 		=> 'colorpicker',
					"condition" 		=> "rc_smart_ui=true"
				),
				array(
					"name" 				=> __('Checked Background Color'),
					"selector" 			=> '.ff-el-group input[type=checkbox]:checked:after, .ff-el-group input[type=radio]:checked:after',
					"property" 			=> 'background-color|border-color',
					"control_type" 		=> 'colorpicker',
					"condition" 		=> "rc_smart_ui=true"
				),
			)
		);*/
	}

	function render( $options, $defaults, $content ) {
		if( $options['cf7_forms'] == "no" ) {
			echo '<h5 class="form-missing">Select a form</h5>';
			return;
		}

		echo do_shortcode('[contact-form-7 id='. $options['cf7_forms'] .']' );
	}

	function oxyu_cf7_load_scripts() {
		if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] ) {
			add_action( 'wp_enqueue_scripts', 'wpcf7_enqueue_styles');
			add_action( 'wp_enqueue_scripts', 'wpcf7_html5_fallback');
		}
	}

	// Get all forms of Contact Form 7 plugin
	function oxyu_get_cf7_forms() {
		$options = array();

		if ( class_exists( 'WPCF7' ) ) {
			$args = array(
				'posts_per_page' 	=> -1,
				'orderby' 			=> 'date',
				'order' 			=> 'DESC',
				'post_type' 		=> 'wpcf7_contact_form',
				'post_status' 		=> 'publish'
			);

			$forms = new \WP_Query($args);
			if( $forms->have_posts() ) {
				$options['no'] = esc_html__( 'Select a form', 'oxy-ultimate' );
				foreach ($forms->posts as $form){
					$options[$form->ID] = $form->post_title;
				}
			} else {
				$options['no'] = esc_html__( 'No forms found!', 'oxy-ultimate' );
			}
		}

		return $options;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}

	function customCSS( $original, $selector ) {
		return '.wpcf7-form-control:focus{outline: none;}.wpcf7-submit {cursor: pointer;}.form-missing{background: #fc5020;color: #fff;display: inline-block;clear: both;font-size: 25px;font-weight: 400;padding: 20px 40px;margin: 30px 0;width: 100%;}';
	}
}

$cf7_style = new OUCF7();
$cf7_style->oxyu_cf7_load_scripts();