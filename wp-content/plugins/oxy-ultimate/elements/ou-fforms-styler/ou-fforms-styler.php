<?php

namespace Oxygen\OxyUltimate;

if ( ! class_exists( 'FrmForm' ) )
	return;

class OUFFormsStyler extends \OxyUltimateEl {
	
	public $css_added = false;

	function name() {
		return __( "Formidable Forms Styler", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_fforms_styler";
	}

	function oxyu_button_place() {
		return "form";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/ou-cf7.svg';
	}

	/*****************************
	 * Form General Settings
	 *****************************/
	function frmformsSettings() {
		$frmforms = $this->addOptionControl( 
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('Formidable Forms' , "oxy-ultimate"),
				'slug' 		=> 'frmform',
				'value' 	=> $this->oxyu_get_frmforms(),
				'default' 	=> "no"
			)
		);
		$frmforms->rebuildElementOnChange();

		$frmform_title = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Show Title' , "oxy-ultimate"),
				'slug' 		=> 'frmform_title',
				'value' 	=> array( "yes" => __("Yes"), "no" => __("No") ),
				'default' 	=> "yes"
			)
		);
		$frmform_title->setParam('hide_wrapper_end', true);
		$frmform_title->rebuildElementOnChange();

		$frmform_desc = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Show Description' , "oxy-ultimate"),
				'slug' 		=> 'frmform_desc',
				'value' 	=> array( "yes" => __("Yes"), "no" => __("No") ),
				'default' 	=> "yes"
			)
		);
		$frmform_desc->setParam('hide_wrapper_start', true);
		$frmform_desc->rebuildElementOnChange();

		$frmform_ajax = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Enable Ajax' , "oxy-ultimate"),
				'slug' 		=> 'frmform_ajax',
				'value' 	=> array( "yes" => __("Yes"), "no" => __("No") ),
				'default' 	=> "yes"
			)
		);
	}

	/*****************************
	 * Form Wrapper
	 *****************************/
	function frmformsWrapperSettings() {
		$fwrapper_section = $this->addControlSection( "frmwrap_section", __("Form Wrapper", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = '.frm_forms';

		$fwrapper_section->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		$frmspacing = $fwrapper_section->addControlSection( "frmspacing", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$frmspacing->addPreset(
			"padding",
			"frmw_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$frmspacing->addPreset(
			"margin",
			"frmw_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$fwrapper_section->borderSection(__('Border'), $selector, $this );
		$fwrapper_section->boxShadowSection(__('Box Shadow'), $selector, $this );

		$frmTitle = $this->typographySection(__("Form Title", "oxy-ultimate"), ".frm_form_title", $this);
		$frmsTtlepacing = $frmTitle->addControlSection( "frmttle_spacing", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$frmsTtlepacing->addPreset(
			"padding",
			"frmttle_padding",
			__("Padding"),
			'.frm_form_title'
		)->whiteList();

		$frmsTtlepacing->addPreset(
			"margin",
			"frmttle_margin",
			__("Margin"),
			'.frm_form_title'
		)->whiteList();

		$frmDesc = $this->typographySection(__("Form Description", "oxy-ultimate"), ".frm_description p", $this);
		$frmDescSpacing = $frmDesc->addControlSection( "frmdesc_spacing", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$frmDescSpacing->addPreset(
			"padding",
			"frmdesc_padding",
			__("Padding"),
			'.frm_description p'
		)->whiteList();

		$frmDescSpacing->addPreset(
			"margin",
			"frmdesc_margin",
			__("Margin"),
			'.frm_description p'
		)->whiteList();
	}


	/*****************************
	 * Input Fields Labels
	 *****************************/
	function frmformsFieldsLabel() {
		$labels = $this->typographySection(__("Fields Label", "oxy-ultimate"), ".frm_form_field > label, .frm_form_field .frm_primary_label, .frm_scale label", $this);
		$sublabels = $labels->addControlSection( 'sublabels', __("Sub Labels", "oxy-ultimate"), "assets/icon.png", $this);
		$sublabels->addStyleControls(
			array(
				array(
					'selector' 	=> '.frm_form_field .frm_description',
					'property' 	=> 'color',
					'slug' 		=> 'sublb_color'
				),
				array(
					'selector' 	=> '.frm_form_field .frm_description',
					'property' 	=> 'font-size',
					'slug' 		=> 'sublb_fs'
				),
				array(
					'selector' 	=> '.frm_form_field .frm_description',
					'property' 	=> 'font-weight',
					'slug' 		=> 'sublb_fw'
				),
				array(
					'selector' 	=> '.frm_form_field .frm_description',
					'property' 	=> 'text-transform',
					'slug' 		=> 'sublb_tt'
				),
				array(
					"name" 				=> __('Spacing'),
					"selector" 			=> '.frm_form_field .frm_description',
					"property" 			=> 'margin-top',
					'control_type' 		=> 'measurebox',
					"units"				=> "px",
					"value"				=> 5
				)
			)
		);

		$cbr = $labels->addControlSection( 'checkboxradio', __("Checkbox & Radio", "oxy-ultimate"), "assets/icon.png", $this);
		$cbr->addStyleControls(
			array(
				array(
					'selector' 	=> '.frm_radio label, .frm_checkbox label',
					'property' 	=> 'color',
					'slug' 		=> 'cbrop_color'
				),
				array(
					'selector' 	=> '.frm_radio label, .frm_checkbox label',
					'property' 	=> 'font-size',
					'slug' 		=> 'cbrop_fs'
				),
				array(
					'selector' 	=> '.frm_radio label, .frm_checkbox label',
					'property' 	=> 'font-weight',
					'slug' 		=> 'cbrop_fw'
				),
				array(
					'selector' 	=> '.frm_radio label, .frm_checkbox label',
					'property' 	=> 'text-transform',
					'slug' 		=> 'cbrop_tt'
				),
				array(
					'name' 		=> __('Link Color'),
					'selector' 	=> '.frm_radio label a, .frm_radio label a:hover, .frm_checkbox label a, .frm_checkbox label a:hover',
					'property' 	=> 'color',
					'slug' 		=> 'cbrop_lc'
				)
			)
		);
	}


	/*****************************
	 * Input Fields
	 *****************************/
	function frmformsFields() {
		$frmform_input = $this->addControlSection( "frmforms_input", __("Input Fields", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = '.with_frm_style input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), .with_frm_style select, .with_frm_style textarea';

		$frmform_input->addPreset(
			"padding",
			"frmfldinp_padding",
			__("Padding"),
			$selector
		)->setUnits("px", "px,em")->whiteList();

		$colors = $frmform_input->addControlSection( "frmforms_input_colors", __("Colors & Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$colors->addStyleControls(
			array(
				array(
					"name" 				=> __('Asterisk Color'),
					"selector" 			=> '.frm_form_field .frm_required',
					"property" 			=> 'color',
					"slug" 				=> 'frmfoms_astc'
				),
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> $selector,
					"property" 			=> 'background-color',
					"slug" 				=> 'frmfoms_bgc'
				),
				array(
					"name" 				=> __('Focus Background Color'),
					"selector" 			=> '.with_frm_style input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, .with_frm_style textarea:focus',
					"property" 			=> 'background-color',
					"slug" 				=> 'frmfoms_fbgc'
				),
				array(
					"name" 				=> __('Focus Text Color'),
					"selector" 			=> '.with_frm_style input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, .with_frm_style textarea:focus',
					"property" 			=> 'color',
					"slug" 				=> 'frmfoms_ftxtc'
				),
				array(
					"name" 				=> __('Placeholder Color'),
					"slug" 				=> "frminp_placeholder",
					"selector" 			=> '::placeholder',
					"property" 			=> 'color',
					"css" 				=> false
				),
				array(
					"name" 				=> __('Textarea Height'),
					"selector" 			=> '.with_frm_style textarea',
					"property" 			=> 'height'
				),
				array(
					"name" 				=> __('Margin Bottom'),
					"selector" 			=> '.with_frm_style .form-field',
					"property" 			=> 'margin-bottom',
					'control_type' 		=> 'measurebox',
					"units"				=> "px",
					"value"				=> 20
				)
			)
		);

		$frm_inputbrd = $frmform_input->addControlSection( "frm_inputbrd", __("Border", "oxy-ultimate"), "assets/icon.png", $this );

		$frm_inputbrd->addPreset(
			"border",
			'frminp_border',
			__("Border"),
			$selector
		)->whiteList();

		$frm_inputbrd->addStyleControl(
			array(
				"name" 				=> __('Focus Border Color'),
				"selector" 			=> '.with_frm_style input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, .with_frm_style textarea:focus',
				"property" 			=> 'border-color',
				"slug" 				=> 'frmfoms_fbrdc'
			)
		);

		$frm_inputbrd->addPreset(
			"border-radius",
			"frminp_border_radius",
			__("Border Radius"),
			$selector
		)->whiteList();

		$frmform_input->typographySection(__("Typography", "oxy-ultimate"), $selector . ', .with_frm_style select', $this );

		$starRating = $frmform_input->addControlSection( "star_rating", __("Star Rating Field", "oxy-ultimate"), "assets/icon.png", $this );
		$starRating->addStyleControls(
			array(
				array(
					"selector" 		=> '.frm-star-group input + label:before, .frm-star-group .star-rating:before',
					"property" 		=> 'color',
					"slug" 			=> 'star_nomc'
				),
				array(
					"name" 			=> __('Hover Color', 'oxy-ultimate'),
					"selector" 		=> '.frm-star-group input + label:hover:before, .frm-star-group .star-rating-hover:before',
					"property" 		=> 'color',
					"slug" 			=> 'star_hvc'
				),
				array(
					"name" 			=> __('Active Color', 'oxy-ultimate'),
					"selector" 		=> '.frm-star-group .star-rating-on:before',
					"property" 		=> 'color',
					"slug" 			=> 'star_actvc'
				)				
			)
		);

		$toggle = $frmform_input->addControlSection( "toggle_fld", __("Toggle Field", "oxy-ultimate"), "assets/icon.png", $this );
		$toggle->addStyleControls(
			array(
				array(
					"name" 			=> __('Circle Color', 'oxy-ultimate'),
					"selector" 		=> '.frm_switch .frm_slider:before',
					"property" 		=> 'background-color',
					"slug" 			=> 'toggle_circ'
				),
				array(
					"name" 			=> __('Turn Off Color', 'oxy-ultimate'),
					"selector" 		=> '.with_frm_style .frm_slider',
					"property" 		=> 'background-color',
					"slug" 			=> 'toggle_toffc'
				),
				array(
					"name" 			=> __('Turn On Color', 'oxy-ultimate'),
					"selector" 		=> '.with_frm_style input:checked + .frm_slider',
					"property" 		=> 'background-color',
					"slug" 			=> 'toggle_tonc'
				)				
			)
		);
	}

	function frmformsFileUploadField() {
		$fupFld = $this->addControlSection( "frmforms_fup", __("File Upload Field", "oxy-ultimate"), "assets/icon.png", $this );

		$desc = $fupFld->addCustomControl(__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">CSS will not apply on Builder Editor. You will see the orginal output at frontend.</div>'), 'up_desc');
		$desc->setParam('heading', __('Caution', "oxy-ultimate"));

		$fupFld->addStyleControls(
			array(
				array(
					"selector" 		=> '.frm_dropzone .dz-message, .with_frm_style .frm_dropzone',
					"property" 		=> 'background-color'
				),
				array(
					"selector" 		=> '.frm_dropzone .dz-message, .with_frm_style .frm_dropzone',
					"property" 		=> 'border-color'
				),
				array(
					"selector" 		=> '.frm_dropzone .dz-message, .with_frm_style .frm_dropzone',
					"property" 		=> 'border-width'
				),
				array(
					"selector" 		=> '.frm_dropzone .dz-message, .with_frm_style .frm_dropzone',
					"property" 		=> 'border-radius'
				),
				array(
					"selector" 		=> '.frm_dropzone.frm_single_upload',
					"property" 		=> 'max-width',
					'control_type' 	=> 'measurebox',
					'units' 		=> 'px',
					'default' 		=> 200
				)		
			)
		);

		$fupIcon = $fupFld->addControlSection( "fup_icon", __("Icon", "oxy-ultimate"), "assets/icon.png", $this );
		$fupIcon->addStyleControls(
			array(
				array(
					"selector" 		=> '.frm_dropzone .frm_upload_icon:before',
					"property" 		=> 'color'
				),
				array(
					"selector" 		=> '.frm_dropzone .frm_upload_icon:before',
					"property" 		=> 'font-size'
				),
				array(
					"selector" 		=> '.frm_upload_icon',
					"property" 		=> 'margin-bottom',
					'control_type' 	=> 'measurebox',
					"units"			=> "px"
				)
			)
		);

		$fupText = $fupFld->addControlSection( "fup_text", __("Upload Text", "oxy-ultimate"), "assets/icon.png", $this );
		$fupText->addStyleControls(
			array(
				array(
					"selector" 		=> '.frm_dropzone .dz-message, .frm_upload_text',
					"property" 		=> 'color'
				),
				array(
					"selector" 		=> '.frm_dropzone .dz-message, .frm_upload_text',
					"property" 		=> 'font-size'
				),
				array(
					"selector" 		=> '.frm_upload_text',
					"property" 		=> 'margin-bottom',
					'control_type' 	=> 'measurebox',
					"units"			=> "px"
				)
			)
		);

		$fupInfo = $fupFld->addControlSection( "fup_inftext", __("Bottom Info Text", "oxy-ultimate"), "assets/icon.png", $this );
		$fupInfo->addStyleControls(
			array(
				array(
					"selector" 		=> '.frm_small_text',
					"property" 		=> 'color'
				),
				array(
					"selector" 		=> '.frm_small_text',
					"property" 		=> 'font-size'
				)
			)
		);

		$fupSpacing = $fupFld->addControlSection( "fup_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$fupSpacing->addPreset(
			"padding",
			"fup_padding",
			__("Padding"),
			'.frm_dropzone .dz-message'
		)->whiteList();

		$fupSpacing->addPreset(
			"margin",
			"fup_margin",
			__("Margin"),
			'.frm_dropzone .dz-message'
		)->whiteList();
	}

	function frmformsSectionField() {
		$selector = ".frm_section_heading";
		$sectionFld = $this->addControlSection( "frmforms_section", __("Section Field", "oxy-ultimate"), "assets/icon.png", $this );
		$sectionFld->addStyleControls(
			array(
				array(
					"name" 			=> __('Seperator Color', "oxy-ultimate"),
					"selector" 		=> $selector . ' .frm_pos_top',
					"property" 		=> 'border-color'
				),
				array(
					"name" 			=> __('Seperator Width', "oxy-ultimate"),
					"selector" 		=> $selector . ' .frm_pos_top',
					"property" 		=> 'border-width'
				)			
			)
		);

		$secSpacing = $sectionFld->addControlSection( "section_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$secSpacing->addPreset(
			"padding",
			"frmsec_padding",
			__("Padding"),
			$selector
		)->whiteList();
		$secSpacing->addPreset(
			"margin",
			"frmsec_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$sectionFld->typographySection(__("Section Title", "oxy-ultimate"), $selector . ' .frm_pos_top', $this );
		$sectionFld->typographySection(__("Section Description", "oxy-ultimate"), $selector . ' > .frm_description', $this );
		$sectionFld->borderSection( __( "Border", "oxy-ultimate" ), $selector, $this );
		$sectionFld->boxShadowSection( __("Box Shadow"), $selector, $this );
	}

	/*****************************
	 * Previous Button
	 *****************************/
	function frmformsPreviousButton() {
		$btn_selector = ".frm_submit .frm_prev_page";
		$frmforms_prev_btn = $this->addControlSection( "frmforms_prev_btn", __("Previous Button", "oxy-ultimate"), "assets/icon.png", $this );

		$frmforms_prev_btn->addStyleControl(
			array(
				"selector" 			=> $btn_selector,
				"property" 			=> 'width'
			)
		)->setParam('hide_wrapper_end', true);

		$frmforms_prev_btn->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'color',
			)
		)->setParam('hide_wrapper_start', true);

		$frmforms_prev_btn->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_end', true);

		$frmforms_prev_btn->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_start', true);

		$frmforms_prev_btn->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> $btn_selector . ":hover",
				"property" 			=> 'border-color'
			)
		);

		$frmforms_prev_btn->addPreset(
			"padding",
			"frmsorevbtn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$frmforms_prev_btn->typographySection(__("Typography", "oxy-ultimate"), $btn_selector, $this );
		$frmforms_prev_btn->borderSection( __( "Border", "oxy-ultimate" ), $btn_selector, $this );
		$frmforms_prev_btn->boxShadowSection( __("Box Shadow"), $btn_selector, $this );
		$frmforms_prev_btn->boxShadowSection( __("Hover Box Shadow"), $btn_selector . ':hover', $this );
	}


	/*****************************
	 * Submit Button
	 *****************************/
	function frmformsSubmitButton() {
		$btn_selector = ".frm_button_submit";
		$frmforms_submit_btn = $this->addControlSection( "frmforms_submit_btn", __("Submit/Next Button", "oxy-ultimate"), "assets/icon.png", $this );
		
		$btnAlign = $frmforms_submit_btn->addControl('buttons-list', 'btn_align', __('Alignment') );
		$btnAlign->setValue([ 'Left', 'Center', 'Right' ]);
		$btnAlign->setValueCSS([ 
			'Center' 	=> '.with_frm_style .frm_submit{text-align: center}',
			'Right' 	=> '.with_frm_style .frm_submit{text-align: right}' 
		]);
		$btnAlign->setDefaultValue("Left");
		$btnAlign->whiteList();

		$frmforms_submit_btn->addStyleControl(
			array(
				"selector" 			=> $btn_selector,
				"property" 			=> 'width'
			)
		)->setParam('hide_wrapper_end', true);

		$frmforms_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'color',
			)
		)->setParam('hide_wrapper_start', true);

		$frmforms_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_end', true);

		$frmforms_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_start', true);

		$frmforms_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> $btn_selector . ":hover",
				"property" 			=> 'border-color'
			)
		);

		$frmforms_submit_btn->addPreset(
			"padding",
			"frmsbtn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$frmforms_submit_btn->typographySection(__("Typography", "oxy-ultimate"), $btn_selector, $this );
		$frmforms_submit_btn->borderSection( __( "Border", "oxy-ultimate" ), $btn_selector, $this );
		$frmforms_submit_btn->boxShadowSection( __("Box Shadow"), $btn_selector, $this );
		$frmforms_submit_btn->boxShadowSection( __("Hover Box Shadow"), $btn_selector . ':hover', $this );
	}

	function frmformsErrorMessageBox() {
		$selector = '.with_frm_style .frm_error_style';
		$errorBox = $this->addControlSection( "top_error_box", __("Top Error Box", "oxy-ultimate"), "assets/icon.png", $this );
		$showErrBox = $errorBox->addControl('buttons-list', 'show_box', __('Show Error Message Box') );
		$showErrBox->setValue([ 'Yes', 'No' ]);
		$showErrBox->setValueCSS([ 'No' => '.with_frm_style .frm_error_style{display: none}' ]);
		$showErrBox->setDefaultValue("Yes");
		$showErrBox->whiteList();

		$errorBox->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		$errboxSpacing = $errorBox->addControlSection( "errbox_spacing", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$errboxSpacing->addPreset(
			"padding",
			"errbox_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$errboxSpacing->addPreset(
			"margin",
			"errbox_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$errorBox->typographySection(__("Typography", "oxy-ultimate"), $selector, $this );
		$errorBox->borderSection( __( "Border", "oxy-ultimate" ), $selector, $this );
	}

	function frmformsValidationError() {
		$selector = '.with_frm_style .frm_error';
		$vErr = $this->addControlSection( "validation_error", __("Validation Error", "oxy-ultimate"), "assets/icon.png", $this );

		$showErrMsg = $vErr->addControl('buttons-list', 'show_errmsg', __('Show Field\'s Error Message') );
		$showErrMsg->setValue([ 'Yes', 'No' ]);
		$showErrMsg->setValueCSS([ 'No' => '.with_frm_style .frm_error{display: none}' ]);
		$showErrMsg->setDefaultValue("Yes");
		$showErrMsg->whiteList();

		$vErr->addStyleControls(
			array(
				array(
					'name' 			=> __("Error Field's Label Color", "oxy-ultimate"),
					"selector" 		=> '.with_frm_style .frm_form_field.frm_blank_field label',
					"property" 		=> 'color',
					"slug" 			=> 'errfld_labelc'
				),
				array(
					'name' 			=> __("Error Field's Border Color", "oxy-ultimate"),
					"selector" 		=> '.with_frm_style .frm_blank_field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), .with_frm_style .frm_blank_field textarea',
					"property" 		=> 'border-color',
					"slug" 			=> 'errfld_brd'
				),
				array(
					"name" 			=> __('Margin Top(for Error Message)'),
					"selector" 		=> $selector,
					"property" 		=> 'margin-top',
					'control_type' 	=> 'measurebox',
					"units"			=> "px",
					"value"			=> 8
				)
			)
		);

		$vErr->typographySection(__("Typography", "oxy-ultimate"), $selector, $this );
	}

	function frmformsSuccessMessage() {
		$selector = '.with_frm_style .frm_message';
		$sucMsg = $this->addControlSection( "suc_msg", __("Success Message", "oxy-ultimate"), "assets/icon.png", $this );

		$sucMsg->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		$sucMsgSpacing = $sucMsg->addControlSection( "sucmsg_spacing", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$sucMsgSpacing->addPreset(
			"padding",
			"sucmsg_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$sucMsgSpacing->addPreset(
			"margin",
			"sucmsg_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$sucMsg->typographySection(__("Typography", "oxy-ultimate"), $selector, $this );
		$sucMsg->borderSection( __( "Border", "oxy-ultimate" ), $selector, $this );
		$sucMsg->boxShadowSection( __( "Box Shadow", "oxy-ultimate" ), $selector, $this );
	}

	function controls() {
		$this->frmformsSettings();
		$this->frmformsWrapperSettings();
		$this->frmformsFieldsLabel();
		$this->frmformsFields();
		$this->frmformsFileUploadField();
		$this->frmformsSectionField();
		$this->frmformsPreviousButton();
		$this->frmformsSubmitButton();
		$this->frmformsErrorMessageBox();
		$this->frmformsValidationError();
		$this->frmformsSuccessMessage();
	}

	function render( $options, $defaults, $content ) {
		if( $options['frmform'] == "no" ) {
			echo '<h5 class="form-missing">' . __("Select a form", 'oxy-ultimate') . '</h5>';
			return;
		} else {

			$title = ( !empty($options['frmform_title']) && $options['frmform_title'] == "no" ) ? "false" : "true";
			$desc = ( !empty($options['frmform_desc']) && $options['frmform_desc'] == "no" ) ? "false" : "true";
			$ajax = ( !empty($options['frmform_ajax']) && $options['frmform_ajax'] == "no" ) ? "false" : "true";

			echo do_shortcode('[formidable id='. $options['frmform'] .' title="' . $title . '" description="' . $desc . '" ajax="' . $ajax . '"]' );
		}
	}

	function customCSS( $original, $selector ) {
		$css = '.frm_style_formidable-style.with_frm_style .form-field input:not([type=file]):focus, .frm_style_formidable-style.with_frm_style select:focus, .frm_style_formidable-style.with_frm_style textarea:focus, .frm_style_formidable-style.with_frm_style .frm_focus_field input[type=text], .frm_style_formidable-style.with_frm_style .frm_focus_field input[type=password], .frm_style_formidable-style.with_frm_style .frm_focus_field input[type=email], .frm_style_formidable-style.with_frm_style .frm_focus_field input[type=number], .frm_style_formidable-style.with_frm_style .frm_focus_field input[type=url], .frm_style_formidable-style.with_frm_style .frm_focus_field input[type=tel], .frm_style_formidable-style.with_frm_style .frm_focus_field input[type=search], .frm_form_fields_active_style, .frm_style_formidable-style.with_frm_style .frm_focus_field .frm-card-element.StripeElement, .frm_style_formidable-style.with_frm_style .chosen-container-single.chosen-container-active .chosen-single, .frm_style_formidable-style.with_frm_style .chosen-container-active .chosen-choices{box-shadow: none}
			.form-missing {background: #fc5020;color: #fff;display: inline-block;clear: both;font-size: 25px;font-weight: 400;padding: 20px 40px;margin: 30px 0;width: 100%;}
			.oxy-ou-fforms-styler{width: 100%}';

		return $css;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}

	function oxyu_get_frmforms() {
		$options = array();
		$options['no'] = esc_html__( 'Select a form', 'oxy-ultimate' );

		if( class_exists('FrmForm') ) {
            $forms = \FrmForm::get_published_forms( array(), 999, 'exclude' );
            if ( count( $forms ) ) {
                foreach ( $forms as $form )
                	$options[$form->id] = str_replace(' ', '&#8205; ', preg_replace("/[^a-zA-Z0-9\s]+/", "", $form->name ) );
            } else {
				$options['no'] = esc_html__( 'No forms found!', 'oxy-ultimate' );
			}
        }

		return $options;
	}
}

new OUFFormsStyler();