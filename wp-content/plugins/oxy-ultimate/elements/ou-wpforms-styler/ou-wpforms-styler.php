<?php

namespace Oxygen\OxyUltimate;

if ( ! function_exists( 'wpforms' ) )
	return;

class OUWPFStyler extends \OxyUltimateEl {
	public $css_added = false;

	function name() {
		return __( "WPForms Styler", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_wpf_styler";
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
		$wpform = $this->addOptionControl( 
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('WPForms' , "oxy-ultimate"),
				'slug' 		=> 'wpform',
				'value' 	=> $this->oxyu_get_wpforms(),
				'default' 	=> 0
			)
		);
		$wpform->rebuildElementOnChange();

		$wpforms_title = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Show Title' , "oxy-ultimate"),
				'slug' 		=> 'wpforms_title',
				'value' 	=> array( "true" => __("Yes"), "false" => __("No") ),
				'default' 	=> "false"
			)
		);
		$wpforms_title->setParam('hide_wrapper_end', true);
		$wpforms_title->rebuildElementOnChange();

		$wpforms_desc = $this->addOptionControl( 
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Show Description' , "oxy-ultimate"),
				'slug' 		=> 'wpforms_desc',
				'value' 	=> array( "true" => __("Yes"), "false" => __("No") ),
				'default' 	=> "false"
			)
		);
		$wpforms_desc->setParam('hide_wrapper_start', true);
		$wpforms_desc->rebuildElementOnChange();

		/*****************************
		 * Form Wrapper
		 *****************************/
		$fwrapper_section = $this->addControlSection( "fwrapper_section", __("Form Wrapper", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = '.wpforms-container';

		$fwrapper_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> $selector,
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker',
				)
			)
		);

		$fwrapper_section->addPreset(
			"border",
			"fw_border",
			__("Border"),
			$selector
		)->whiteList();

		$fwrapper_section->addPreset(
			"border-radius",
			"fw_border_radius",
			__("Border Radius"),
			$selector
		)->whiteList();

		$fwrapper_section->addPreset(
			"padding",
			"fw_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$fwrapper_section->addPreset(
			"margin",
			"fw_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$this->typographySection(__("Form Title", "oxy-ultimate"), ".wpforms-title", $this);
		$this->typographySection(__("Form Description", "oxy-ultimate"), ".wpforms-description", $this);

		$labels = $this->typographySection(__("Fields Label", "oxy-ultimate"), ".wpforms-field-label", $this);
		$labels->addStyleControls(
			array(
				array(
					'name' 		=> __('Sub Label'),
					'selector' 	=> '.wpforms-field-sublabel',
					'property' 	=> 'color'
				),
				array(
					'name' 		=> __('Sub Label Font Size'),
					'selector' 	=> '.wpforms-field-sublabel',
					'property' 	=> 'font-size'
				),
				array(
					'name' 		=> __('Sub Label Font Weight'),
					'selector' 	=> '.wpforms-field-sublabel',
					'property' 	=> 'font-weight'
				),
				array(
					'name' 		=> __('Sub Label Text Transform'),
					'selector' 	=> '.wpforms-field-sublabel',
					'property' 	=> 'text-transform'
				)
			)
		);

		$wpf_fd_section = $this->typographySection(__("Fields Description", "oxy-ultimate"),".wpforms-field-description", $this);

		$wpf_fd_section->addPreset(
			"padding",
			"wpfd_padding",
			__("Padding"),
			".wpforms-field-description"
		)->whiteList();


		/*****************************
		 * Input Fields
		 *****************************/
		$wpf_input_section = $this->addControlSection( "wpf_input", __("Input Fields", "oxy-ultimate"), "assets/icon.png", $this );
		$wpfinp_selector = '.wpforms-form .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), .wpforms-field-textarea textarea, .wpforms-field-select select, .wpforms-form .choices__list--single .choices__item, .wpforms-form .choices__list--dropdown .choices__item, select.wpforms-payment-price';
		$focus_selector = '.wpforms-form .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, .wpforms-field-textarea textarea:focus';

		$wpf_input_section->addStyleControl(
			array(
				"name" 				=> __('Textarea Height'),
				"selector" 			=> '.wpforms-field-textarea textarea',
				"property" 			=> 'height'
			)
		);

		$wpf_inputsp = $wpf_input_section->addControlSection( "wpf_inputsp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$wpf_inputsp->addPreset(
			"padding",
			"wpfinp_padding",
			__("Padding"),
			$wpfinp_selector
		)->setUnits("px", "px,em")->whiteList();
		$wpf_inputsp->addStyleControls(
			array(
				array(
					"name" 				=> __('Vertical Gap Between Two Fields'),
					"selector" 			=> '.wpforms-form .wpforms-field',
					"property" 			=> 'margin-bottom',
					"units"				=> "px",
					"value"				=> 10
				)
			)
		);

		$wpf_inputclr = $wpf_input_section->addControlSection( "wpf_inputclr", __("Color", "oxy-ultimate"), "assets/icon.png", $this );
		$wpf_inputclr->addStyleControls(
			array(
				array(
					"name" 				=> __('Asterisk Color'),
					"selector" 			=> '.wpforms-required-label',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Text Color'),
					"selector" 			=> $wpfinp_selector,
					"property" 			=> 'color',
				),
				array(
					"selector" 			=> $wpfinp_selector,
					"property" 			=> 'background-color',
				),
				array(
					"name" 				=> __('Focus Background Color'),
					"selector" 			=> $focus_selector,
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Placeholder Color'),
					"slug" 				=> "wpf_inp_placeholder",
					"selector" 			=> '::placeholder',
					"css" 				=> false
				)
			)
		);

		$wpf_input_section->typographySection(__("Typography", "oxy-ultimate"), $wpfinp_selector, $this );

		$wpf_inputbrd = $wpf_input_section->addControlSection( "wpf_inputbrd", __("Borders", "oxy-ultimate"), "assets/icon.png", $this );
		$wpf_inputbrd->addPreset(
			"border",
			'wpfinp_border',
			__("Border"),
			$wpfinp_selector
		)->whiteList();

		$wpf_inputbrd->addStyleControl(
			array(
				"name" 				=> __('Focus Border Color'),
				"selector" 			=> $focus_selector,
				"property" 			=> 'border-color'
			)
		);

		$wpf_inputbrd->addPreset(
			"border-radius",
			"wpfinp_border_radius",
			__("Border Radius"),
			$wpfinp_selector
		)->whiteList();


		/*****************************
		 * Checkbox & Radio Fields
		 *****************************/
		$wpf_cbr_section = $this->addControlSection( "wpfcbr_input", __("Checkbox & Radio Fields", "oxy-ultimate"), "assets/icon.png", $this );

		$wpf_cbr_section->addStyleControl(
			array(
				"name" 			=> __('Radio/Checkbox Field Size', "oxy-ultimate"),
				"slug" 			=> "wpfcbr_size",
				"selector" 		=> '.wpforms-form input[type=checkbox], .wpforms-form input[type=radio]',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '14',
				"property" 		=> 'width|height'
			)
		)
		->setRange(14, 30, 2)
		->setUnits("px", "px");
		

		$wpf_cbr_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Label Font Size'),
					"selector" 			=> '.wpforms-field-checkbox li label, .wpforms-field-radio li label, label.wpforms-field-label-inline',
					"property" 			=> 'font-size',
					"slug" 				=> 'wpfcbr_lblfs'
				),
				array(
					"selector" 			=> '.wpforms-field-checkbox li label, .wpforms-field-radio li label, label.wpforms-field-label-inline',
					"property" 			=> 'color',
					"slug" 				=> 'wpfcbr_lblclr'
				),
			)
		);


		/*****************************
		 * File Upload Field
		 *****************************/
		$wpf_fup_section = $this->addControlSection( "wpf_fup", __("File Upload Field", "oxy-ultimate"), "assets/icon.png", $this );
		$wpfup_selector = 'div.wpforms-uploader';
		$wpf_fup_section->addStyleControls(
			array(
				array(
					"selector" 			=> $wpfup_selector,
					"property" 			=> 'background-color'
				),
				array(
					"selector" 			=> $wpfup_selector,
					"property" 			=> 'width'
				),
				array(
					"name" 				=> __('Icon Color', "oxy-ultimate"),
					"selector" 			=> $wpfup_selector . " svg",
					"property" 			=> 'fill',
					'control_type' 		=> 'colorpicker'
				)
			)
		);

		$wpf_fup_section->typographySection( __('Typography'), $wpfup_selector, $this );
		$wpf_fup_section->borderSection(__("Border"), $wpfup_selector, $this);
		$wpf_fup_section->addPreset(
			"padding",
			"gffup_padding",
			__("Padding"),
			$wpfup_selector
		)->whiteList();

		/*****************************
		 * Section Field
		 *****************************/
		$wpfs_section = $this->addControlSection( "wpf_section", __("Section Divider", "oxy-ultimate"), "assets/icon.png", $this );
		
		$sec_selector = ".wpforms-field-divider";

		$wpfs_section->addStyleControl(
			array(
				"selector" 			=> $sec_selector,
				"property" 			=> 'background-color',
			),
			array(
				"selector" 			=> $sec_selector,
				"property" 			=> 'width',
			)
		);

		$wpfs_section->addPreset(
			"padding",
			"wpfsection__padding",
			__("Padding"),
			$sec_selector
		)->whiteList();
		
		$wpfs_section->addPreset(
			"margin",
			"wpfsection__margin",
			__("Margin"),
			$sec_selector
		)->whiteList();

		$wpfs_section->typographySection(__("Section Title", "oxy-ultimate"), ".wpforms-field-divider h3", $this);
		$wpfs_section->typographySection(__("Section Description", "oxy-ultimate"),".wpforms-field-divider .wpforms-field-description", $this);
		$wpfs_section->borderSection(__("Border"),".wpforms-field-divider .wpforms-field-description", $this);
		$wpfs_section->boxShadowSection(__("Box Shadow"),".wpforms-field-divider .wpforms-field-description", $this);

		/*****************************
		 * Submit Button
		 *****************************/
		$btn_selector = ".wpforms-submit";
		$wpf_submit_btn = $this->addControlSection( "wpf_submit_btn", __("Submit Button", "oxy-ultimate"), "assets/icon.png", $this );
		
		$wpf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'width'
			)
		);

		$wpf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Text Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'color',
			)
		)->setParam('hide_wrapper_end', true);

		$wpf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'color',
			)
		)->setParam('hide_wrapper_start', true);

		$wpf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker'
			)
		)->setParam('hide_wrapper_end', true);

		$wpf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker'
			)
		)->setParam('hide_wrapper_start', true);

		$wpf_submit_btn->addPreset(
			"padding",
			"gfbtn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$wpf_submit_btn->typographySection(__("Typography", "oxy-ultimate"), $btn_selector, $this );
		$wpf_submit_btn->borderSection( __("Border"), $btn_selector, $this );
		$wpf_submit_btn->boxShadowSection( __("Box Shadow"), $btn_selector, $this );

		/*****************************
		 * Validation Error
		 *****************************/

		$wpfer_section = $this->addControlSection( "wpfer_section", __("Validation Error", "oxy-ultimate"), "assets/icon.png", $this );
		$wpfer_section->addStyleControls(
			array(
				array(
					"selector" 			=> '.wpforms-error',
					"property" 			=> 'color',
					"value" 			=> "#990000"
				),
				array(
					"selector" 			=> '.wpforms-error',
					"property" 			=> 'font-size',
					"value" 			=> 12
				),
				array(
					"selector" 			=> '.wpforms-error',
					"property" 			=> 'font-weight'
				),
				array(
					"selector" 			=> '.wpforms-error',
					"property" 			=> 'margin-top',
					"value" 			=> 4
				),
			)
		);


		/*****************************
		 * Success Message
		 *****************************/
		$wpfsuc_section = $this->addControlSection( "wpfsuc_section", __("Success Message", "oxy-ultimate"), "assets/icon.png", $this );

		$wpfsuc_section->addStyleControl(
			array(
				'selector' 		=> '.wpforms-confirmation-container-full',
				'value' 		=> 100,
				'property' 		=> 'width'
			)
		)->setUnits("%", "px,%");

		$wpfsuc_section->addStyleControl(
			array(
				'selector' 		=> '.wpforms-confirmation-container-full',
				'property' 		=> 'background-color'
			)
		);

		$wpfsuc_section->typographySection( __("Typography"), '.wpforms-confirmation-container-full p', $this );
		$wpfsuc_section->borderSection( __("Border"), '.wpforms-confirmation-container-full', $this );
		$wpfsuc_section->boxShadowSection( __("Box Shadow"), '.wpforms-confirmation-container-full', $this );

		$wpfsuc_section->addPreset(
			"padding",
			"wpfsuc_padding",
			__("Padding"),
			'.wpforms-confirmation-container-full'
		)->whiteList();
	}

	function render( $options, $defaults, $content ) {
		if( $options['wpform'] == "no" ) {
			echo '<h5 class="form-missing">' . __("Select a form", 'oxy-ultimate') . '</h5>';
			return;
		} else {
			echo do_shortcode('[wpforms id='. $options['wpform'] .' title="' . $options['wpforms_title'] . '" description="' . $options['wpforms_desc'] . '"]' );
		}
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$this->css_added = true;

			return 'div.wpforms-container-full .wpforms-form input[type=date], 
				div.wpforms-container-full .wpforms-form input[type=datetime], 
				div.wpforms-container-full .wpforms-form input[type=datetime-local], 
				div.wpforms-container-full .wpforms-form input[type=email], 
				div.wpforms-container-full .wpforms-form input[type=month], 
				div.wpforms-container-full .wpforms-form input[type=number], 
				div.wpforms-container-full .wpforms-form input[type=password], 
				div.wpforms-container-full .wpforms-form input[type=range], 
				div.wpforms-container-full .wpforms-form input[type=search], 
				div.wpforms-container-full .wpforms-form input[type=tel], 
				div.wpforms-container-full .wpforms-form input[type=text], 
				div.wpforms-container-full .wpforms-form input[type=time], 
				div.wpforms-container-full .wpforms-form input[type=url], 
				div.wpforms-container-full .wpforms-form input[type=week], 
				div.wpforms-container-full .wpforms-form select {
				  height: auto!important;
				}
				.wpforms-container, .oxy-ou-wpf-styler {width: 100%}
				.form-missing {
					background: #fc5020;
					color: #fff;
					display: inline-block;
					clear: both;
					font-size: 25px;
					font-weight: 400;
					padding: 20px 40px;
					margin: 30px 0;
					width: 100%;
				}';
		}
	}

	function oxyu_get_wpforms() {
		$options = array();
		$options[0] = esc_html__( 'Select a form', 'oxy-ultimate' );

		if ( class_exists( 'WPForms_Pro' ) || class_exists( 'WPForms_Lite' ) ) {

			$args               = array(
				'post_type'      => 'wpforms',
				'posts_per_page' => -1,
			);
			$forms              = get_posts( $args );

			if ( $forms ) {
				foreach ( $forms as $form ) {
					$options[ $form->ID ] = str_replace(' ', '&#8205; ', preg_replace("/[^a-zA-Z0-9\s]+/", "", $form->post_title ) );
				}
			}
		}

		if ( empty( $options ) ) {
			$options = array(
				'-1' => __( 'You have not added any WPForms yet.', 'oxy-ultimate' ),
			);
		}

		return $options;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}

	function init() {
		$this->El->useAJAXControls();
		if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'oxyu_wpf_enqueue_scripts' ) );
		}
	}

	function oxyu_wpf_enqueue_scripts() {
		wp_enqueue_style(
			'wpforms-full',
			WPFORMS_PLUGIN_URL . 'assets/css/wpforms-full.css',
			array(),
			WPFORMS_VERSION
		);
	}
}

new OUWPFStyler();