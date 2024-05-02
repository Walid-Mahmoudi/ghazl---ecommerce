<?php

namespace Oxygen\OxyUltimate;

class OUCommentForm extends \OxyUltimateEl {
	public $com_options;
	public $css_added = false;

	function name() {
		return __( "OU Comment Form", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_comment_form";
	}

	function oxyu_button_place() {
		return "form";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {

		$this->addOptionControl(
			array(
				'name' 		=> __('Title Reply HTML Tag', "oxy-ultimate"),
				"slug" 		=> 'oupcf_trtag',
				"type" 		=> "radio",
				"value" 	=> [ "h1" => __("H1"), "h2" => __("H2"), "h3" => __("H3"), "h4" => __("H4"), "h5" => __("H5"), "h6" => __("H6"), "div" => __("DIV") ],
				"default" 	=> "h3",
				"css" 		=> false
			)
		);

		$this->addOptionControl(
			array(
				'name' 		=> __('Title Reply Text', "oxy-ultimate"),
				"slug" 		=> 'oupcf_trt',
				"type" 		=> "textfield",
				"value" 	=> __( 'Leave a Reply' ),
				"base64" 	=> true
			)
		);

		$this->addOptionControl(
			array(
				'name' 		=> __('Title Reply To Text', "oxy-ultimate"),
				"slug" 		=> 'oupcf_trtt',
				"type" 		=> "textfield",
				"value" 	=> __( 'Leave a Reply to %s' ),
				"base64" 	=> true
			)
		);

		$this->addOptionControl(
			array(
				'name' 		=> __('Cancel Reply Text', "oxy-ultimate"),
				"slug" 		=> 'oupcf_crt',
				"type" 		=> "textfield",
				"value" 	=> __( 'Cancel reply' ),
				"base64" 	=> true
			)
		);

		$this->addOptionControl(
			array(
				'name' 		=> __('Comment After Notes', "oxy-ultimate"),
				"slug" 		=> 'oupcf_afternote',
				"type" 		=> "textarea",
				"base64" 	=> true
			)
		);

		/*****************************
		 * Form Wrapper
		 *****************************/
		$fwrapper_section = $this->addControlSection( "fwrapper_section", __("Form Wrapper", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = '.comment-respond';

		$fwrapper_section->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		$formBorder = $fwrapper_section->addControlSection( "fw_border", __("Border"), "assets/icon.png", $this );
		$formBorder->addPreset(
			"border",
			"oucfw_border",
			__("Border"),
			$selector
		)->whiteList();

		$formBorder->addPreset(
			"border-radius",
			"oucfw_border_radius",
			__("Border Radius"),
			$selector
		)->whiteList();

		$formSP = $fwrapper_section->addControlSection( "fw_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$formSP->addPreset(
			"padding",
			"oucfw_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$formSP->addPreset(
			"margin",
			"oucfw_margin",
			__("Margin"),
			$selector
		)->whiteList();


		/*****************************
		 * Form Heading
		 *****************************/

		$heading = $this->addControlSection( "heading_section", __("Heading", "oxy-ultimate"), "assets/icon.png", $this );
		$heading->addStyleControl(
			array(
				"selector" 			=> '.comment-reply-title',
				"property" 			=> 'background-color'
			)
		);

		$headingSP = $heading->addControlSection( "heading_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$headingSP->addPreset(
			"padding",
			"headingsp_padding",
			__("Padding"),
			'.comment-reply-title'
		)->whiteList();

		$headingSP->addPreset(
			"margin",
			"headingsp_margin",
			__("Margin"),
			'.comment-reply-title'
		)->whiteList();

		$heading->borderSection( __( "Border" ), '.comment-reply-title', $this );

		$heading->typographySection( __( "Typography" ), '.comment-reply-title', $this );



		/*****************************
		 * Form Notes
		 *****************************/
		$desc = $this->addControlSection( "desc_section", __("Description", "oxy-ultimate"), "assets/icon.png", $this );
		$desc->addStyleControl(
			array(
				"selector" 			=> '.comment-notes',
				"property" 			=> 'background-color'
			)
		);

		$descSP = $desc->addControlSection( "desc_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$descSP->addPreset(
			"padding",
			"oucdsp_padding",
			__("Padding"),
			'.comment-notes'
		)->whiteList();

		$descSP->addPreset(
			"margin",
			"oucdsp_margin",
			__("Margin"),
			'.comment-notes'
		)->whiteList();

		$desc->borderSection( __( "Border", "oxy-ultimate" ), '.comment-notes', $this );
		$desc->typographySection( __( "Typography" ), '.comment-notes', $this );



		/*****************************
		 * Form Labels
		 *****************************/
		$formLabel = $this->addControlSection( "cfl_section", __("Form Labels", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = 'p label';
		$formLabel->addOptionControl(
			array(
				'name' 		=> __('Hide Label', "oxy-ultimate"),
				"slug" 		=> 'oupcf_label',
				"type" 		=> "radio",
				"value" 	=> [ "yes" => __("Yes"), "no" => __( "No") ],
				"default" 	=> "no",
				"css" 		=> false
			)
		)->rebuildElementOnChange();

		$formLabel->addOptionControl(
			array(
				'name' 		=> __('Display Label as Placeholder', "oxy-ultimate"),
				'description' => __('This effect will show at frontend'),
				"slug" 		=> 'oupcf_phlabel',
				"type" 		=> "radio",
				"value" 	=> [ "yes" => __("Yes"), "no" => __( "No") ],
				"default" 	=> "no",
				"css" 		=> false
			)
		)->rebuildElementOnChange();

		$formLabel->addStyleControls([
			array(
				'name' 		=> __('Asterisk Color', 'oxy-ultimate'),
				'selector' 	=> '.required',
				'property' 	=> 'color',
				'value' 	=> '#990000'
			),
			array(
				'name' 		=> __('Space Top', 'oxy-ultimate'),
				'selector' 	=> $selector,
				'property' 	=> 'margin-top'
			),
			array(
				'name' 		=> __('Space Bottom', 'oxy-ultimate'),
				'selector' 	=> $selector,
				'property' 	=> 'margin-bottom'
			)
		]);

		$formLabel->typographySection( __( "Typography", "oxy-ultimate" ), $selector, $this );

		$this->typographySection( __( "Checkbox Label", "oxy-ultimate" ), '.comment-form-cookies-consent label', $this );

		$formFields = $this->addControlSection( "cff_section", __("Form Fields", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = 'input:not([type="checkbox"]):not([type="submit"]), textarea';
		$selectorFocus = 'input:not([type="checkbox"]):not([type="submit"]):focus, textarea:focus';

		$formFields->addOptionControl(
			array(
				'name' 		=> __('Remove Website Field', "oxy-ultimate"),
				"slug" 		=> 'oupcf_rweb',
				"type" 		=> "radio",
				"value" 	=> [ "yes" => __("Yes"), "no" => __( "No") ],
				"default" 	=> "no",
				"css" 		=> false
			)
		)->rebuildElementOnChange();

		$formFields->addStyleControl(
			array(
				"name" 				=> __('Textarea Height'),
				"selector" 			=> 'textarea',
				"property" 			=> 'height'
			)
		);

		$ffColors = $formFields->addControlSection( "fields_colors", __("Colors", "oxy-ultimate"), "assets/icon.png", $this );
		$ffColors->addStyleControls([
			array(
				'property' 	=> 'background-color',
				'selector' 	=> $selector,
			),
			array(
				'name' 		=> __( 'Focus Background Color', "oxy-ultimate"),
				'property' 	=> 'background-color',
				'selector' 	=> $selectorFocus,
			),
			array(
				'name' 		=> __( 'Focus Text Color', "oxy-ultimate"),
				'property' 	=> 'color',
				'selector' 	=> $selectorFocus,
			),
			array(
				'name' 		=> __( 'Focus Border Color', "oxy-ultimate"),
				'property' 	=> 'border-color',
				'selector' 	=> $selectorFocus,
			),
			array(
				"name" 				=> __('Placeholder Color'),
				"slug" 				=> "oucf_inp_placeholder",
				"selector" 			=> '::placeholder',
				"property" 			=> 'color',
				"css" 				=> false
			)
		]);

		$fields_sp = $formFields->addControlSection( "fields_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$fields_sp->addPreset(
			"padding",
			"cff_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$formFields->typographySection( __( "Typography", "oxy-ultimate" ), $selector, $this );
		$formFields->borderSection( __( "Border", "oxy-ultimate" ), $selector, $this );
		$formFields->boxShadowSection( __( "Box Shadow", "oxy-ultimate" ), $selector, $this );

		/*****************************
		 * Submit Button
		 *****************************/
		$btn_selector = ".comment-form .submit";
		$oucf_submit_btn = $this->addControlSection( "oucf_submit_btn", __("Submit Button", "oxy-ultimate"), "assets/icon.png", $this );

		$oucf_submit_btn->addOptionControl(
			array(
				'name' 		=> __('Submit Button Text', "oxy-ultimate"),
				"slug" 		=> 'oupcf_btnt',
				"type" 		=> "textfield",
				"value" 	=> __( 'Post Comment' ),
				"css" 		=> false
			)
		);
		
		$oucf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'width'
			)
		);

		$oucf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Text Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'color',
				"slug" 				=> 'cfmbtn_clr'
			)
		)->setParam('hide_wrapper_end', true);

		$oucf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $btn_selector . ':hover,' . $btn_selector . ':before',
				"property" 			=> 'color',
				"slug" 				=> 'cfmbtn_hclr'
			)
		)->setParam('hide_wrapper_start', true);

		$oucf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $btn_selector . ', p.form-submit:not(.hover-effect-none) #submit:hover',
				"property" 			=> 'background-color',
				"slug" 				=> 'cfmbtn_bg'
			)
		)->setParam('hide_wrapper_end', true);

		$oucf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $btn_selector . ':hover,' . $btn_selector . ':before',
				"property" 			=> 'background-color',
				"slug" 				=> 'cfmbtn_hbg'
			)
		)->setParam('hide_wrapper_start', true);

		$oucf_submit_btn->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> $btn_selector . ':hover,' . $btn_selector . ':before',
				"property" 			=> 'border-color',
				"slug" 				=> 'cfmbtn_hbrd'
			)
		);

		$oucf_submit_btn->addOptionControl(
			array(
				"name" 			=> __('Hover Animation', "oxy-ultimate"),
				"slug" 			=> "cfbtn_hover_effect",
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

		$oucf_submit_btn->addStyleControl(
			array(
				"name" 			=> __('Transition Duration', "oxy-ultimate"),
				'property' 		=> 'transition-duration',
				"slug" 			=> "btn_ts",
				"control_type" 	=> 'slider-measurebox',
				'selector' 		=> '#submit,#submit:before',
				"value" 		=> '0.3'
			)
		)->setRange('0.1', '10', '0.1')->setUnits("s", "sec");

		$btnAlgn = $oucf_submit_btn->addControl( 'buttons-list', 'oucfm_btnalgn', __('Button Alignment') );
		$btnAlgn->setValue( array( "Left", "Center", "Right" ) );
		$btnAlgn->setValueCSS( array(
			"Center" 	=> "p.form-submit {text-align: center;}",
			"Right" 	=> "p.form-submit {text-align: right;}"
		));
		$btnAlgn->setDefaultValue("Left");
		$btnAlgn->whiteList();

		$oucf_submit_btn->addPreset(
			"padding",
			"oupcfbtn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$oucf_submit_btn->typographySection(__("Typography", "oxy-ultimate"), $btn_selector, $this );
		$oucf_submit_btn->borderSection( __( "Border", "oxy-ultimate" ), $btn_selector, $this );
		$oucf_submit_btn->boxShadowSection( __("Box Shadow"), $btn_selector, $this );

		$this->typographySection( __( "After Notes", "oxy-ultimate" ), '.comment-notes-after', $this );
	}

	function render( $options, $content, $default ) {
		$this->com_options = $options;

		add_filter( 'comment_form_defaults', array($this, 'oxyu_filter_comment_form_defaults') );

		comment_form();

		remove_filter( 'comment_form_defaults', array($this, 'oxyu_filter_comment_form_defaults') );

		if( isset( $options['oupcf_phlabel'] ) && $options['oupcf_phlabel'] == "yes" ) {
			if( defined('OXY_ELEMENTS_API_AJAX') || isset($_GET['oxygen_iframe']) ) {
				$this->El->builderInlineJS(
					"jQuery(document).ready(function(){
						if( jQuery('#" . $options['selector'] . " .oucf-show-placeholder').length > 0 ) {
							jQuery('.comment-form-comment textarea').attr(
								'placeholder', 
								jQuery('.comment-form-comment label').text() 
							);

							jQuery('.comment-form-email input').attr(
								'placeholder', 
								jQuery('.comment-form-email label').text() 
							);

							jQuery('.comment-form-author input').attr(
								'placeholder', 
								jQuery('.comment-form-author label').text() 
							);

							jQuery('.comment-form-url input').attr(
								'placeholder', 
								jQuery('.comment-form-url label').text() 
							);
						}
					});"
				);
			} else {
				$this->El->footerJS(
					"jQuery(document).ready(function(){
						if( jQuery('#" . $options['selector'] . " .oucf-show-placeholder').length > 0 ) {
							jQuery('.comment-form-comment textarea').attr(
								'placeholder', 
								jQuery('.comment-form-comment label').text() 
							);

							jQuery('.comment-form-email input').attr(
								'placeholder', 
								jQuery('.comment-form-email label').text() 
							);

							jQuery('.comment-form-author input').attr(
								'placeholder', 
								jQuery('.comment-form-author label').text() 
							);

							jQuery('.comment-form-url input').attr(
								'placeholder', 
								jQuery('.comment-form-url label').text() 
							);
						}
					});"
				);
			}
		}
	}

	function oxyu_filter_comment_form_defaults( $defaults ) {
		$options = $this->com_options;

		if( is_user_logged_in() ) {
			$defaults['class_form'] .= ' ou-usr-logged-in';
		}

		if( isset( $options['oupcf_label'] ) && $options['oupcf_label'] == "yes" )
			$defaults['class_form'] .= ' oucf-hide-label';

		if( isset( $options['oupcf_phlabel'] ) && $options['oupcf_phlabel'] == "yes" )
			$defaults['class_form'] .= ' oucf-show-placeholder';

		if( isset( $options['oupcf_rweb'] ) && $options['oupcf_rweb'] == "yes" ){
			unset( $defaults['fields']['url'] );
		}

		if( isset( $options['oupcf_afternote'] ) ) {
			$defaults['comment_notes_after'] = sprintf(
				'<p class="comment-notes-after">%s</p>',
				esc_html( $options['oupcf_afternote'] ) 
			);
		}

		if( isset( $options['oupcf_trt'] ) )
			$defaults['title_reply'] = esc_html( $options['oupcf_trt'] );

		if( isset( $options['oupcf_trtt'] ) )
			$defaults['title_reply_to'] = esc_html( $options['oupcf_trtt'] );

		if( isset( $options['oupcf_crt'] ) )
			$defaults['cancel_reply_link'] = esc_html( $options['oupcf_crt'] );

		if( isset( $options['oupcf_btnt'] ) )
			$defaults['label_submit'] = esc_html( $options['oupcf_btnt'] );

		if( isset( $options['oupcf_trtag'] ) && $options['oupcf_trtag'] !== "h3" ){
			$defaults['title_reply_before'] = str_replace('h3', $options['oupcf_trtag'], '<h3 id="reply-title" class="comment-reply-title">');
			$defaults['title_reply_after'] = str_replace('h3', $options['oupcf_trtag'], '</h3>');
		}

		if( isset( $options['cfbtn_hover_effect'] ) ) {
			$defaults['submit_field'] = '<p class="form-submit hover-effect-'. $options['cfbtn_hover_effect'] .'">%1$s %2$s</p>';
			$defaults['submit_button'] = '<button name="%1$s" type="submit" id="%2$s" class="%3$s ou-button-effect">%4$s</button>';
		}

		return $defaults;
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$this->css_added = true;
			$css .= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
		}

		$prefix = $this->El->get_tag();
		if( isset( $original[ $prefix . '_cfbtn_hover_effect'] ) ) {
			$css .= ou_button_hover_effect( $original[ $prefix . '_cfbtn_hover_effect'] );
		}

		return $css;
	}
}

new OUCommentForm();
//$oucfm->oucfm_load_scripts();