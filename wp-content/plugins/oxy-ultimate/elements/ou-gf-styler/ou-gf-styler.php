<?php

namespace Oxygen\OxyUltimate;

if ( ! class_exists( 'GFForms' ) )
	return;

class OUGFStyler extends \OxyUltimateEl {
	
	public $css_added = false;

	function name() {
		return __( "Gravity Form Styler", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_gf_styler";
	}

	function oxyu_button_place() {
		return "form";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		/*****************************
		 * Gravity Form Drop Down
		 *****************************/
		$gform = $this->addOptionControl( 
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('Gravity Form' , "oxy-ultimate"),
				'slug' 		=> 'gform',
				'value' 	=> $this->oxyu_get_gf_forms(),
				'default' 	=> "no",
				'css' 		=> false
			)
		);
		$gform->rebuildElementOnChange();

		$gform_title = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Show Title' , "oxy-ultimate"),
				'slug' 		=> 'gform_title',
				'value' 	=> array( "yes" => __("Yes"), "no" => __("No") ),
				'default' 	=> "yes",
				'css' 		=> false
			)
		);
		$gform_title->setParam('hide_wrapper_end', true);
		$gform_title->rebuildElementOnChange();

		$gform_desc = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Show Description' , "oxy-ultimate"),
				'slug' 		=> 'gform_desc',
				'value' 	=> array( "yes" => __("Yes"), "no" => __("No") ),
				'default' 	=> "yes",
				'css' 		=> false
			)
		);
		$gform_desc->setParam('hide_wrapper_start', true);
		$gform_desc->rebuildElementOnChange();

		$gform_ajax = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Enable Ajax' , "oxy-ultimate"),
				'slug' 		=> 'gform_ajax',
				'value' 	=> array( "yes" => __("Yes"), "no" => __("No") ),
				'default' 	=> "no",
				'css' 		=> false
			)
		);
		$gform_ajax->setParam('hide_wrapper_end', true);

		$gform_tabi = $this->addOptionControl( 
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Tab Index' , "oxy-ultimate"),
				'slug' 		=> 'gform_tabi',
				'value' 	=> "10",
				'css' 		=> false
			)
		);
		$gform_tabi->setParam('hide_wrapper_start', true);

		/*****************************
		 * Form Wrapper
		 *****************************/
		$fwrapper_section = $this->addControlSection( "fwrapper_section", __("Form Wrapper", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = '.gform_wrapper';

		$fwrapper_section->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		$fwrapper_brd = $fwrapper_section->addControlSection( "fwrapper_brd", __("Border", "oxy-ultimate"), "assets/icon.png", $this );

		$fwrapper_brd->addPreset(
			"border",
			"fw_border",
			__("Border"),
			$selector
		)->whiteList();

		$fwrapper_brd->addPreset(
			"border-radius",
			"fw_border_radius",
			__("Border Radius"),
			$selector
		)->whiteList();

		$fwrapper_sp = $fwrapper_section->addControlSection( "fwrapper_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$fwrapper_sp->addPreset(
			"padding",
			"fw_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$fwrapper_sp->addPreset(
			"margin",
			"fw_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$this->typographySection(__("Form Title", "oxy-ultimate"),".gform_title", $this);
		$this->typographySection(__("Form Description", "oxy-ultimate"),".gform_description", $this);		
		$gf_fd_section = $this->typographySection(__("Fields Description", "oxy-ultimate"),".gform_wrapper .gfield_description", $this);

		$gf_fd_section->addPreset(
			"padding",
			"gfd_padding",
			__("Padding"),
			".gfield_description"
		)->whiteList();

		$labels = $this->typographySection(__("Fields Label", "oxy-ultimate"), ".gform_wrapper .gfield_label", $this);
		$gf_hide_labels = $labels->addControl('buttons-list', 'gf_hide_labels', __('Hide Labels'));
		$gf_hide_labels->setValue(['No', 'Yes']);
		$gf_hide_labels->setValueCSS(['Yes' => '.gform_wrapper .top_label .gfield_label{display:none}']);
		$gf_hide_labels->setDefaultValue('No');
		$gf_hide_labels->whiteList();

		$sublabels = $labels->addControlSection( 'sublabels', __("Sub Labels", "oxy-ultimate"), "assets/icon.png", $this);
		$sublabels->addStyleControls(
			array(
				array(
					'selector' 	=> '.ginput_container span label, .gfield_time_hour label, .gfield_time_minute label',
					'property' 	=> 'color',
					'slug' 		=> 'sublb_color'
				),
				array(
					'selector' 	=> '.ginput_container span label, .gfield_time_hour label, .gfield_time_minute label',
					'property' 	=> 'font-size',
					'slug' 		=> 'sublb_fs'
				),
				array(
					'selector' 	=> '.ginput_container span label, .gfield_time_hour label, .gfield_time_minute label',
					'property' 	=> 'font-weight',
					'slug' 		=> 'sublb_fw'
				),
				array(
					'selector' 	=> '.ginput_container span label, .gfield_time_hour label, .gfield_time_minute label',
					'property' 	=> 'text-transform',
					'slug' 		=> 'sublb_tt'
				)
			)
		);
		
		/*****************************
		 * Input Fields
		 *****************************/
		$gf_input_section = $this->addControlSection( "gf_input", __("Input Fields", "oxy-ultimate"), "assets/icon.png", $this );
		$gfinp_selector = '.gform_wrapper .gfield input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), .gform_wrapper .gfield select, .gform_wrapper .gfield textarea';

		$gf_input_section->addStyleControl(
			array(
				"name" 				=> __('Textarea Height'),
				"selector" 			=> '.gfield textarea',
				"property" 			=> 'height'
			)
		);

		$gf_input_sp = $gf_input_section->addControlSection( "gf_input_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$gf_input_sp->addPreset(
			"padding",
			"gfinp_padding",
			__("Padding"),
			$gfinp_selector
		)->setUnits("px", "px,em")->whiteList();

		$hgapFields = $gf_input_sp->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			"selector" 		=> '.gform_wrapper li.gfield.gf_left_half, .gform_wrapper li.gf_right_half, div.ginput_container_name span, .gform_wrapper .ginput_complex.ginput_container.ginput_container_email .ginput_left, .gform_wrapper .ginput_complex.ginput_container.ginput_container_email .ginput_right',
			'name' 			=> __('Gap Between Two Horizontal Fields', "oxy-ultimate"),
			'slug' 			=> 'ouacfg_hgapfields',
			'property' 		=> 'padding-right'
		]);
		$hgapFields->setUnits('px','px,%,em');
		$hgapFields->setRange('0', '50', '5');
		$hgapFields->setDefaultValue('16');

		$gf_input_sp->addStyleControls(
			array(
				array(
					"name" 				=> __('Space Between Two Vertical Fields'),
					"selector" 			=> '.gform_wrapper ul li.gfield',
					"property" 			=> 'margin-top',
					'control_type' 		=> 'slider-measurebox',
					"units"				=> "px",
					"value"				=> '10'
				)
			)
		);

		$gf_input_clr = $gf_input_section->addControlSection( "gf_input_clr", __("Color", "oxy-ultimate"), "assets/icon.png", $this );
		$gf_input_clr->addStyleControls(
			array(
				array(
					"name" 				=> __('Asterix Color'),
					"selector" 			=> '.gfield_required',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> $gfinp_selector,
					"property" 			=> 'background-color',
					"slug" 				=> 'ougf_bgc'
				),
				array(
					"name" 				=> __('Focus Background Color'),
					"selector" 			=> '.gform_wrapper .gfield input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, .gform_wrapper .gfield textarea:focus',
					"property" 			=> 'background-color',
					"slug" 				=> 'ougf_fbgc'
				),
				array(
					"name" 				=> __('Focus Text Color'),
					"selector" 			=> '.gform_wrapper .gfield input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, .gform_wrapper .gfield textarea:focus',
					"property" 			=> 'color',
					"slug" 				=> 'ougf_ftxtc'
				),
				array(
					"name" 				=> __('Placeholder Color'),
					"slug" 				=> "gf_inp_placeholder",
					"selector" 			=> '::placeholder',
					"property" 			=> 'color',
					"css" 				=> false
				)
			)
		);

		$gf_inputbrd = $gf_input_section->addControlSection( "gf_inputbrd", __("Border", "oxy-ultimate"), "assets/icon.png", $this );
		$gf_inputbrd->addPreset(
			"border",
			'gfinp_border',
			__("Border"),
			$gfinp_selector
		)->whiteList();

		$gf_inputbrd->addStyleControl(
			array(
				"name" 				=> __('Focus Border Color'),
				"selector" 			=> '.gform_wrapper .gfield input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, .gfield textarea:focus',
				"property" 			=> 'border-color',
				"slug" 				=> 'ougf_fbrdc'
			)
		);

		$gf_inputbrd->addPreset(
			"border-radius",
			"gfinp_border_radius",
			__("Border Radius"),
			$gfinp_selector
		)->whiteList();

		$gf_input_section->typographySection(__("Typography", "oxy-ultimate"), $gfinp_selector . ', .gform_wrapper .gfield select', $this );


		/*****************************
		 * Radio & Checkbox Field
		 *****************************/

		$gfrc_section = $this->addControlSection( "gfrc_section", __("Checkbox & Radio", "oxy-ultimate"), "assets/icon.png", $this );
		$cr_selector = '.gfield_checkbox input[type=checkbox]:after, .gfield_radio input[type=radio]:after';
		$gfrc_section->addOptionControl(
			array(
				"type"			=> "radio",
				"name" 			=> __('Enable Smart UI'),
				"slug" 			=> "gfrc_smart_ui",
				"value" 		=> [ "yes" => __("Yes"), "no" => __("No") ],
				"default" 		=> "no",
				'css' 		=> false
			)
		)->rebuildElementOnChange();

		$gfrc_section->addStyleControls(
			array(
				array(
					'selector' 			=> '.gfield_checkbox label, .gfield_radio label',
					'property' 			=> 'color',
					'name' 				=> __('Text Color'),
					'slug' 				=> 'gfcr_clr'
				),
				array(
					'selector' 			=> '.gfield_checkbox label, .gfield_radio label',
					'property' 			=> 'font-size',
					"control_type" 		=> 'slider-measurebox',
					'name' 				=> __('Text Font Size'),
					'unit' 				=> 'px',
					'slug' 				=> 'gfcr_fs'
				),
				array(
					'selector' 			=> $cr_selector,
					'name'				=> __("Width"),
					"property" 			=> "width|height",
					"control_type" 		=> 'slider-measurebox',
					"unit" 				=> 'px',
					"value" 			=> 15,
					'slug' 				=> 'gfcr_wd',
					"condition" 		=> "gfrc_smart_ui=yes"
				),
				array(
					"selector" 			=> $cr_selector,
					"property" 			=> 'background-size',
					"control_type" 		=> 'measurebox',
					"unit" 				=> 'px',
					"value" 			=> 9,
					'slug' 				=> 'gfcr_bgc',
					"condition" 		=> "gfrc_smart_ui=yes"
				),
				array(
					"selector" 			=> $cr_selector,
					"property" 			=> 'background-color',
					"slug" 				=> "gfcr_bg_color",
					"condition" 		=> "gfrc_smart_ui=yes"
				),
				array(
					"selector" 			=> $cr_selector,
					"property" 			=> 'border-color',
					"slug" 				=> "cr_brd_color",
					"condition" 		=> "gfrc_smart_ui=yes"
				),
				array(
					"selector" 			=> $cr_selector . ", .gfield_checkbox input[type=checkbox]:checked:after, .gfield_radio input[type=radio]:checked:after",
					"property" 			=> 'border-width',
					"condition" 		=> "gfrc_smart_ui=yes",
					'slug' 				=> 'gfcr_brdw'
				),
				array(
					"name" 				=> __('Checked Background Color'),
					"selector" 			=> '.gfield_checkbox input[type=checkbox]:checked:after, .gfield_radio input[type=radio]:checked:after',
					"property" 			=> 'background-color|border-color',
					"slug" 				=> "gfrc_bg_color",
					"control_type" 		=> 'colorpicker',
					"condition" 		=> "gfrc_smart_ui=yes"
				)
			)
		);

		$gfrc_section->addPreset(
			"margin",
			'gfrc_margin',
			__("Field Margin"),
			'.gfield_checkbox input[type=checkbox], .gfield_radio input[type=radio]'
		)->whiteList();

		$gfrc_section->addPreset(
			"padding",
			'gfrcl_padding',
			__("Label Spacing"),
			'.gfield_checkbox label, .gfield_radio label'
		)->whiteList();


		/*****************************
		 * File Upload Field
		 *****************************/
		$gf_fup_section = $this->addControlSection( "gf_fup", __("File Upload Field", "oxy-ultimate"), "assets/icon.png", $this );
		$gfup_selector = '.gfield .ginput_container_fileupload > input[type=file]';
		$gf_fup_section->addStyleControls(
			array(
				array(
					"selector" 			=> $gfup_selector,
					"property" 			=> 'background-color',
					"slug" 				=> 'ougf_filebgc'
				),
				array(
					"selector" 			=> $gfup_selector,
					"property" 			=> 'width',
					"slug" 				=> 'ougf_filewidth'
				)
			)
		);

		$gf_fup_section->typographySection( __('Typography'), $gfup_selector, $this );
		$gf_fup_section->borderSection(__("Border"), $gfup_selector, $this);
		$gf_fup_section->addPreset(
			"padding",
			"gffup_padding",
			__("Padding"),
			$gfup_selector
		)->whiteList();


		/*****************************
		 * Section Field
		 *****************************/
		$gfs_section = $this->addControlSection( "gf_section", __("Section Field", "oxy-ultimate"), "assets/icon.png", $this );
		$gfs_section->typographySection(__("Section Title", "oxy-ultimate"),".gsection_title", $this);
		$gfs_section->typographySection(__("Section Description", "oxy-ultimate"),".gsection_description", $this);
		$sec_selector = ".gform_fields > li.gsection";

		$gfs_section->addStyleControl(
			array(
				"selector" 			=> $sec_selector,
				"property" 			=> 'background-color'
			)
		);

		$gfs_section->addStyleControl(
			array(
				"selector" 			=> $sec_selector,
				"property" 			=> 'border-color'
			)
		)->setParam('hide_wrapper_end', true);

		$gfs_section->addStyleControl(
			array(
				"selector" 			=> $sec_selector,
				"property" 			=> 'border-bottom-width'
			)
		)->setParam('hide_wrapper_start', true);

		$gfs_section->addPreset(
			"padding",
			"gsection_padding",
			__("Padding"),
			$sec_selector
		)->whiteList();
		
		$gfs_section->addPreset(
			"margin",
			"gsection_margin",
			__("Margin"),
			$sec_selector
		)->whiteList();


		/*****************************
		 * Submit Button
		 *****************************/
		$btn_selector = ".gform_button";
		$gf_submit_btn = $this->addControlSection( "gf_submit_btn", __("Submit Button", "oxy-ultimate"), "assets/icon.png", $this );
		
		$gf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'width'
			)
		);

		$gf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Text Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'color',
			)
		)->setParam('hide_wrapper_end', true);

		$gf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'color',
			)
		)->setParam('hide_wrapper_start', true);

		$gf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker'
			)
		)->setParam('hide_wrapper_end', true);

		$gf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker'
			)
		)->setParam('hide_wrapper_start', true);

		$gf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> $btn_selector . ":hover",
				"property" 			=> 'border-color'
			)
		);

		$gf_submit_btn->addPreset(
			"padding",
			"gfbtn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$gf_submit_btn->typographySection(__("Typography", "oxy-ultimate"), $btn_selector, $this );
		$gf_submit_btn->borderSection( __( "Border", "oxy-ultimate" ), $btn_selector, $this );
		$gf_submit_btn->boxShadowSection( __("Box Shadow"), $btn_selector, $this );

		/*****************************
		 * Errors
		 *****************************/
		$errors = $this->addControlSection( "gf_errors", __("Errors Message"), "assets/icon.png", $this );
		$vr_selector = '.validation_error';
		$fr_selector = ".gfield_error";
		$validation_error = $errors->addStyleControl(
			array(
				"name" 		=> __('Validation Error', "oxy-ultimate"),
				"selector" 	=> $vr_selector,
				"property" 	=> "display"
			)
		);

		$validation_error->setValue(["none" => __("Hide", "oxy-ultimate"), "block" => __("Show", "oxy-ultimate")]);

		$err_msg = $errors->addStyleControl(
			array(
				"name" 		=> __('Error Field Message', "oxy-ultimate"),
				"selector" 	=> '.validation_message',
				"property" 	=> "display"
			)
		);

		$err_msg->setValue(["none" => __("Hide", "oxy-ultimate"), "block" => __("Show", "oxy-ultimate")]);

		$errors->addStyleControls(
			array(
				array(
					'name' 			=> __("Validation Background Color", "oxy-ultimate"),
					"selector" 		=> $vr_selector,
					"property" 		=> 'background-color',
					"control_type" 	=> 'colorpicker'
				),
				array(
					'name' 			=> __("Validation Text Color", "oxy-ultimate"),
					"selector" 		=> $vr_selector,
					"property" 		=> 'color'
				),
				array(
					'name' 			=> __("Validation Border Color", "oxy-ultimate"),
					"selector" 		=> $vr_selector,
					"property" 		=> 'border-color'
				),
				array(
					'name' 			=> __("Error Field Border Color", "oxy-ultimate"),
					"selector" 		=> $fr_selector,
					"property" 		=> 'border-color'
				),
				array(
					'name' 			=> __("Error Field Background Color", "oxy-ultimate"),
					"selector" 		=> $fr_selector,
					"property" 		=> 'background-color',
					"control_type" 	=> 'colorpicker'
				),
				array(
					'name' 			=> __("Error Field Label Color", "oxy-ultimate"),
					"selector" 		=> $fr_selector . " label.gfield_label",
					"property" 		=> 'color'
				),
				array(
					'name' 			=> __("Error Field Input Border Color", "oxy-ultimate"),
					"selector" 		=> ".gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file])",
					"property" 		=> 'border-color',
					"slug" 			=> 'ougf_errbc'
				),
				array(
					'name' 			=> __("Error Field Input Border Width", "oxy-ultimate"),
					"selector" 		=> ".gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file])",
					"property" 		=> 'border-width',
					"slug" 			=> 'ougf_errbw'
				),
				array(
					'name' 			=> __("Error Field Message Color", "oxy-ultimate"),
					"selector" 		=> '.validation_message',
					"property" 		=> 'color'
				)
			)
		);



		/*****************************
		 * Success Message
		 *****************************/
		$suc_msg = $this->addControlSection( "gf_smsg", __("Success Message"), "assets/icon.png", $this );
		$suc_msg->typographySection(__("Typography", "oxy-ultimate"),".gform_confirmation_message", $this);
		$suc_msg->borderSection(__("Border", "oxy-ultimate"),".gform_confirmation_message", $this);
		$suc_msg->boxShadowSection(__("Box Shadow", "oxy-ultimate"),".gform_confirmation_message", $this);

		$suc_msg->addPreset(
			"padding",
			"gfsm_padding",
			__("Padding"),
			".gform_confirmation_message"
		)->whiteList();
	}

	function render( $options, $defaults, $content ) {
		if( $options['gform'] == "no" ) {
			echo '<h5 class="form-missing">' . __("Select a form", 'oxy-ultimate') . '</h5>';
			return;
		} else {

			$title = ( !empty($options['gform_title']) && $options['gform_title'] == "no" ) ? "false" : "true";
			$desc = ( !empty($options['gform_desc']) && $options['gform_desc'] == "no" ) ? "false" : "true";
			$ajax = ( !empty($options['gform_ajax']) && $options['gform_ajax'] == "no" ) ? "false" : "true";
			$tab_index = ! empty($options['gform_tabi']) ? $options['gform_tabi'] : 10;

			if ( defined('OXY_ELEMENTS_API_AJAX') && isset($options['gform']) ) {
				$form = \GFAPI::get_form( $options['gform']);
				\GFFormDisplay::print_form_scripts( $form, $ajax );
			}

			echo do_shortcode('[gravityform id='. $options['gform'] .' title="' . $title . '" description="' . $desc . '" ajax="' . $ajax . '" tabindex="' . $tab_index . '"]' );
		}
	}

	function oxyu_get_gf_forms() {
		$options = array();
		$options['no'] = esc_html__( 'Select a form', 'oxy-ultimate' );

		if ( class_exists( 'GFForms' ) ) {
			$forms = \RGFormsModel::get_forms( null, 'title' );
			if ( count( $forms ) ) {
				foreach ( $forms as $form ) {
					$options[ $form->id ] = str_replace(' ', '&#8205; ', preg_replace("/[^a-zA-Z0-9\s]+/", "", $form->title ) );
				}
			} else {
				$options['no'] = esc_html__( 'No forms found!', 'oxy-ultimate' );
			}
		}

		return $options;
	}

	function customCSS($original, $selector) {
		$css = $defaultCSS = '';
		if( ! $this->css_added ) {
			$defaultCSS = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = 'oxy-' . $this->slug();
		$placeholder = $original[$prefix . '_gf_inp_placeholder'];
		if( $placeholder ){
			$css.= "::placeholder{color:".$placeholder.";}";
			$css.= "::-webkit-input-placeholder{color:".$placeholder.";}";
			$css.= "::-moz-input-placeholder{color:".$placeholder.";}";
			$css.= ":-ms-input-placeholder{color:".$placeholder.";}";
			$css.= ":-moz-placeholder{color:".$placeholder.";}";
		}

		if( isset( $original[$prefix . '_gfrc_smart_ui'] ) && $original[$prefix . '_gfrc_smart_ui'] == "yes" ) {
			$css .= $selector ." .gfield_checkbox input[type=checkbox]:after, ". $selector  ." .gfield_radio input[type=radio]:after {content: \" \";display: inline-block;}";
			$css .= $selector ." .gfield_checkbox input[type=checkbox], .gfield_radio input[type=radio]{width: 1px;}";
		} else {
			$css .= $selector ." .gfield_checkbox input[type=checkbox]:after, ". $selector  ." .gfield_radio input[type=radio]:after {content: none;}";
		}

		return $defaultCSS . $css;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUGFStyler();