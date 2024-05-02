<?php

namespace Oxygen\OxyUltimate;

if ( ! function_exists( 'wpFluentForm' ) )
	return;

class OUFFStyler extends \OxyUltimateEl {

	public $css_added = false;

	function name() {
		return __( "Fluent Form Styler", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_ff_styler";
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
		$ff_forms = $this->addOptionControl( 
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('Fluent Form' , "oxy-ultimate"),
				'slug' 		=> 'fluentform',
				'value' 	=> $this->oxyu_get_fluent_forms(),
				'default' 	=> "no",
				"css" 		=> false
			)
		);
		$ff_forms->rebuildElementOnChange();

		/*****************************
		 * Form Wrapper
		 *****************************/
		$ffwrapper_section = $this->addControlSection( "ffwrapper_section", __("Form Wrapper", "oxy-ultimate"), "assets/icon.png", $this );
		$selector = '.fluentform';
		$ffwrapper_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> $selector,
					"property" 			=> 'background-color'
				)
			)
		);

		$fwsp_brd = $ffwrapper_section->addControlSection( "fwsp_section", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$fwsp_brd->addPreset(
			"padding",
			"fw_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$fwsp_brd->addPreset(
			"margin",
			"fw_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$fw_brd = $ffwrapper_section->addControlSection( "fwbrd_section", __("Border", "oxy-ultimate"), "assets/icon.png", $this );
		$fw_brd->addPreset(
			"border",
			"fw_border",
			__("Border"),
			$selector
		)->whiteList();

		$fw_brd->addPreset(
			"border-radius",
			"fw_border_radius",
			__("Border Radius"),
			$selector
		)->whiteList();

		/*****************************
		 * Form Label
		 *****************************/
		$fflabel_section = $this->addControlSection( "fflabel_section", __("Form Label", "oxy-ultimate"), "assets/icon.png", $this );

		$fflabel_section->addStyleControl(
			array(
				"name" 				=> __('Asterisk Color'),
				"selector" 			=> '.ff-el-input--label.ff-el-is-required.asterisk-left label:before, .ff-el-input--label.ff-el-is-required.asterisk-right label:after',
				"property" 			=> 'color',
			)
		);

		$fflabel_section->typographySection( __('Typography'), '.ff-el-input--label label', $this );

		/*****************************
		 * Form Label Tooltip
		 *****************************/
		//$tooltip = $fflabel_section->addControlSection( "fflabel_tooltip", __("Info Circle", "oxy-ultimate"), "assets/icon.png", $this );
		//$infocircle = $tooltip->addControlSection( "infocircle", __("Info Circle", "oxy-ultimate"), "assets/icon.png", $this );
		$infocircle = $fflabel_section->addControlSection( "fflabel_tooltip", __("Info Circle", "oxy-ultimate"), "assets/icon.png", $this );
		$infocircle->addStyleControls([
			array(
				"selector" 		=> '.fluentform .ff-el-tooltip',
				"property" 		=> 'color'
			),
			array(
				"name"			=> __('Hover Color'),
				"selector" 		=> '.fluentform .ff-el-tooltip:hover',
				"property" 		=> 'color'
			),
			array(
				'name' 			=> __("Size", "oxy-ultimate"),
				"selector" 		=> '.fluentform .ff-el-tooltip',
				"property" 		=> 'font-size'
			)
		]);

		$infc_pt = $infocircle->addStyleControl([
			'control_type' 		=> 'measurebox',
			'property' 			=> 'top',
			'name' 				=> __("Position Top", "oxy-ultimate"),
			"selector" 			=> '.fluentform .ff-el-tooltip',
		]);
		$infc_pt->setRange('0', '30', '1');
		$infc_pt->setUnits('px', 'px,%,em');
		$infc_pt->setParam('hide_wrapper_end', true);

		$infc_pl = $infocircle->addStyleControl([
			'control_type' 		=> 'measurebox',
			'property' 			=> 'left',
			'name' 				=> __("Position Left", "oxy-ultimate"),
			"selector" 			=> '.fluentform .ff-el-tooltip',
		]);
		$infc_pl->setRange('0', '30', '1');
		$infc_pl->setUnits('px', 'px,%,em');
		$infc_pl->setParam('hide_wrapper_start', true);

		/*$pbfc = $tooltip->typographySection( __('Popup Box'), '.ff-el-pop-content', $this );
		$pbfc->addStyleControl([
			'name' 			=> __("Box Background Color", "oxy-ultimate"),
			"selector" 		=> '.ff-el-pop-content',
			"property" 		=> 'background-color'
		]);

		$ppbpad = $tooltip->addControlSection( 'ppbox_pad', __('Popup Box Padding'), "assets/icon.png", $this );
		$ppbpad->addPreset(
			"padding",
			"popbox_padding",
			__("Padding"),
			'.ff-el-pop-content'
		)->whiteList();

		$tooltip->borderSection( __('Popup Box Border'), '.ff-el-pop-content', $this );
		$tooltip->boxShadowSection( __('Popup Box Shadow'), '.ff-el-pop-content', $this );*/

		/*****************************
		 * Input Fields
		 *****************************/

		$ffinput_section = $this->addControlSection( "ffinput_section", __("Input Fields", "oxy-ultimate"), "assets/icon.png", $this );

		$ffinput_section->addPreset(
			"padding",
			"ffinput_section_padding",
			__("Padding"),
			'.ff-el-form-control'
		)->whiteList();

		$ffinput_section->addStyleControl(
			array(
				"name" 				=> __('Margin Bottom'),
				"selector" 			=> '.ff-el-group',
				"property" 			=> 'margin-bottom'
			)
		)->whiteList();

		$ffinput_section->addStyleControl(
			array(
				"name" 				=> __('Textarea Height'),
				"selector" 			=> 'textarea.ff-el-form-control',
				"property" 			=> 'height'
			)
		);

		$ffip_clr = $ffinput_section->addControlSection( "ffip_clr", __("Color", "oxy-ultimate"), "assets/icon.png", $this );
		$ffip_clr->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> '.ff-el-form-control',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Focus Background Color'),
					"selector" 			=> '.ff-el-form-control:focus',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Focus Text Color'),
					"selector" 			=> '.ff-el-form-control:focus',
					"property" 			=> 'color'
				),
				array(
					"name" 				=> __('Placeholder Color'),
					"slug" 				=> "ff_inp_placeholder",
					"selector" 			=> '::placeholder',
					"property" 			=> 'color'
				)
			)
		);

		$ffip_brd = $ffinput_section->addControlSection( "ffip_brd", __("Border", "oxy-ultimate"), "assets/icon.png", $this );
		$ffip_brd->addPreset(
			"border",
			'ffinput_section_border',
			__("Border"),
			'.ff-el-form-control'
		)->whiteList();

		$ffip_brd->addStyleControl(
			array(
				"name" 				=> __('Focus Border Color'),
				"selector" 			=> '.ff-el-form-control:focus',
				"property" 			=> 'border-color',
				"control_type" 		=> 'colorpicker'
			)
		)->whiteList();

		$ffip_brd->addPreset(
			"border-radius",
			"ffinput_section_border_radius",
			__("Border Radius"),
			'.ff-el-form-control'
		)->whiteList();

		$ffinput_section->typographySection( __('Typography'), '.ff-el-form-control', $this );

		$ffrc_section = $this->addControlSection( "ffrc_section", __("Checkbox & Radio", "oxy-ultimate"), "assets/icon.png", $this );
		$cr_selector = '.ff-el-group input[type=checkbox]:after,.ff-el-group input[type=radio]:after';
		$ffrc_section->addOptionControl(
			array(
				"type"			=> "radio",
				"name" 			=> __('Enable Smart UI'),
				"slug" 			=> "rc_smart_ui",
				"value" 		=> [ "yes" => __("Yes"), "no" => __("No") ],
				"default" 		=> "no",
				"css" 		=> false
			)
		)->rebuildElementOnChange();

		$ffrc_text = $ffrc_section->addControlSection( "ffrc_text", __("Text Style", "oxy-ultimate"), "assets/icon.png", $this );

		$ffrc_text->addStyleControls(
			array(
				array(
					'selector' 			=> '.ff-el-form-check-label, .ff_t_c',
					'property' 			=> 'color',
					'name' 				=> __('Text Color')
				),
				array(
					'selector' 			=> '.ff_t_c a',
					'property' 			=> 'color',
					'name' 				=> __('Link Color')
				),
				array(
					'selector' 			=> '.ff_t_c a:hover',
					'property' 			=> 'color',
					'name' 				=> __('Link Hover Color')
				),
				array(
					'selector' 			=> '.ff-el-form-check-label, .ff_t_c',
					'property' 			=> 'font-size',
					"control_type" 		=> 'slider-measurebox',
					'name' 				=> __('Text Font Size'),
					'unit' 				=> 'px'
				)
			)
		);

		$ffrc = $ffrc_section->addControlSection( "ffrc_style", __("Checkbox/Radio", "oxy-ultimate"), "assets/icon.png", $this );
		
		$ffrc->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Options would be available when you will enable the Smart UI feature.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$ffrc->addStyleControl(
			array(
				'selector' 			=> $cr_selector,
				'name'				=> __("Width"),
				"property" 			=> "width|height",
				"control_type" 		=> 'slider-measurebox',
				"unit" 				=> 'px',
				"value" 			=> 15,
				"condition" 		=> "rc_smart_ui=yes"
			)
		);

		$ffrc->addCustomControl(
			'<div class="oxygen-option-default"><hr style="color: #f4f4f4;height: 1px" noshade/></div>', 
			'divider'
		);

		$ffrc->addStyleControls(
			array(
				array(
					"selector" 			=> $cr_selector,
					"property" 			=> 'background-color',
					"slug" 				=> "cr_bg_color",
					"condition" 		=> "rc_smart_ui=yes"
				),
				array(
					"selector" 			=> $cr_selector,
					"property" 			=> 'border-color',
					"slug" 				=> "cr_brd_color",
					"condition" 		=> "rc_smart_ui=yes"
				),
				array(
					"selector" 			=> $cr_selector . ", .ff-el-group input[type=checkbox]:checked:after, .ff-el-group input[type=radio]:checked:after",
					"property" 			=> 'border-width',
					"condition" 		=> "rc_smart_ui=yes"
				),
				array(
					'name' 				=> __('Border Radius for Checkbox', "oxy-ultimate"),
					"selector" 			=> ".ff-el-group input[type=checkbox]:after, .ff-el-group input[type=checkbox]:checked:after",
					"property" 			=> 'border-radius',
					"condition" 		=> "rc_smart_ui=yes"
				),
			)
		);

		$ffrc->addCustomControl(
			'<div class="oxygen-option-default"><hr style="color: #f4f4f4;height: 1px" noshade/></div>', 
			'divider_2'
		);

		$ffrc->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Size - Selected State'),
					"selector" 			=> $cr_selector,
					"property" 			=> 'background-size',
					"control_type" 		=> 'measurebox',
					"unit" 				=> 'px',
					"value" 			=> 10,
					"condition" 		=> "rc_smart_ui=yes"
				),
				array(
					"name" 				=> __('Background Color - Selected State'),
					"selector" 			=> '.ff-el-group input[type=checkbox]:checked:after, .ff-el-group input[type=radio]:checked:after',
					"property" 			=> 'background-color|border-color',
					"slug" 				=> "crc_bg_color",
					"control_type" 		=> 'colorpicker',
					"condition" 		=> "rc_smart_ui=yes"
				)
			)
		);

		$ffrc_sp = $ffrc_section->addControlSection( "ffrc_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );

		$ffrc_sp->addPreset(
			"margin",
			'ffrc_margin',
			__("Checkbox/Radio Margin"),
			'.fluentform input[type=checkbox], .ff-el-group input[type=radio]'
		)->whiteList();

		$ffrc_sp->addPreset(
			"padding",
			'ffrcl_padding',
			__("Label Spacing"),
			'.ff-el-form-check-label, .ff_t_c'
		)->whiteList();

		/*****************************
		 * Checkable Grid Field
		 *****************************/
		$cbgf = $this->addControlSection( "ff_checkable_gird", __("Checkable Grid Field", "oxy-ultimate"), "assets/icon.png", $this );

		$cbgspacing = $cbgf->addControlSection( "ff_checkable_gird_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$cbgwidth = $cbgspacing->addStyleControl(
			array(
				"selector" 			=> '.fluentform .ff-checkable-grids',
				"property" 			=> 'width',
				'control_type' 		=> 'slider-measurebox'
			)
		);
		$cbgwidth->setRange('0', '100', '1');
		$cbgwidth->setUnits('%', 'px,%,em');
		$cbgwidth->setDefaultValue('100');

		$cbgspacing->addPreset(
			"padding",
			"cbgsp_padding",
			__("Table Head Cell Padding"),
			".fluentform .ff-checkable-grids thead>tr>th"
		)->whiteList();

		$cbgspacing->addPreset(
			"padding",
			"cbgsptb_padding",
			__("Table Body Cell Padding"),
			".fluentform .ff-checkable-grids tbody>tr>td"
		)->whiteList();

		$cg_clr = $cbgf->addControlSection( "ff_cg_clr", __("Color", "oxy-ultimate"), "assets/icon.png", $this );
		$cg_clr->addStyleControls([
			array(
				"name" 				=> __('Table Outer Border Width', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-checkable-grids',
				"property" 			=> 'border-width'
			),
			array(
				"name" 				=> __('Table Outer Border Color', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-checkable-grids',
				"property" 			=> 'border-color'
			),
			array(
				"name" 				=> __('Table Head Background', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-checkable-grids thead>tr>th',
				"property" 			=> 'background-color'
			),
			array(
				"name" 				=> __('Table Body Background', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-checkable-grids tbody>tr>td',
				"property" 			=> 'background-color'
			),
			array(
				"name" 				=> __('Table Body Alt Background', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-checkable-grids tbody>tr:nth-child(2n)>td',
				"property" 			=> 'background-color'
			),
			array(
				"name" 				=> __('Table Body Alt Text Color', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-checkable-grids tbody>tr:nth-child(2n)>td',
				"property" 			=> 'color'
			)
		]);

		$cbgfth = $cbgf->typographySection( __("Heading Typography", "oxy-ultimate"), ".fluentform .ff-checkable-grids thead>tr>th", $this );
		$cbgftb = $cbgf->typographySection( __("Cell Typography", "oxy-ultimate"), ".fluentform .ff-checkable-grids tbody>tr>td", $this );

		/*****************************
		 * Ratings Field
		 *****************************/
		$ratings = $this->addControlSection( "ff_ratings", __("Ratings Field", "oxy-ultimate"), "assets/icon.png", $this );
		$ratings->addStyleControls(
			array(
				array(
					"name" 				=> __('Inactive Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-ratings',
					"property" 			=> '--fill-inactive',
					'control_type' 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Active Color', "oxy-ultimate"),
					"description" 		=> __('On Mouseover you can see the preview', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-ratings',
					"property" 			=> '--fill-active',
					'control_type' 		=> 'colorpicker'
				)
			)
		);

		$starSize = $ratings->addStyleControl(
			array(
				"name" 				=> __('Star Size', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-el-ratings svg',
				"property" 			=> 'width|height',
				'control_type' 		=> 'slider-measurebox'
			)
		);
		$starSize->setRange('0', '100', '1');
		$starSize->setUnits('px', 'px,%,em');
		$starSize->setDefaultValue('22');

		$starGap = $ratings->addStyleControl(
			array(
				"name" 				=> __('Gap between stars', "oxy-ultimate"),
				"selector" 			=> '.fluentform .ff-el-ratings label',
				"property" 			=> 'margin-right',
				'control_type' 		=> 'slider-measurebox'
			)
		);
		$starGap->setRange('0', '100', '1');
		$starGap->setUnits('px', 'px,%,em');
		$starGap->setDefaultValue('3');

		$ratingstxt = $ratings->typographySection( __("Ratings Text", "oxy-ultimate"), ".ff-el-rating-text", $this );
		$gaprt = $ratingstxt->addStyleControl(
			array(
				"name" 				=> __('Gap between star & text', "oxy-ultimate"),
				"selector" 			=> '.ff-el-rating-text',
				"property" 			=> 'padding-left',
				'control_type' 		=> 'slider-measurebox'
			)
		);
		$gaprt->setRange('0', '100', '1');
		$gaprt->setUnits('px', 'px,%,em');
		$gaprt->setDefaultValue('5');

		$rtxtalign = $ratingstxt->addControl('buttons-list', 'ratingtxt_align', __('Vertical Align') );
		$rtxtalign->setValue(['Top', 'Center', 'Bottom']);
		$rtxtalign->setValueCSS([
			'Top' 		=> 'span.ff-el-rating-text{vertical-align:top}', 
			'Center' 	=> 'span.ff-el-rating-text{vertical-align:middle}'
		]);
		$rtxtalign->setDefaultValue('Bottom');
		$rtxtalign->whiteList();


		/*****************************
		 * Range Slider Field
		 *****************************/
		/*$range_sld = $this->addControlSection( "ff_range_slider", __("Range Slider Field", "oxy-ultimate"), "assets/icon.png", $this );
		$range_sld->addStyleControls(
			array(
				array(
					"name" 				=> __('Horizontal Bar Height', "oxy-ultimate"),
					"selector" 			=> '.rangeslider--horizontal',
					"property" 			=> 'height'
				),
				array(
					"name" 				=> __('Horizontal Bar Background', "oxy-ultimate"),
					"selector" 			=> '.rangeslider',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Horizontal Bar Active Background', "oxy-ultimate"),
					"selector" 			=> '.rangeslider__fill',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Horizontal Bar Border Radius', "oxy-ultimate"),
					"selector" 			=> '.rangeslider, .rangeslider__fill',
					"property" 			=> 'border-radius'
				),
				array(
					"name" 				=> __('Slider Handle Size', "oxy-ultimate"),
					"selector" 			=> '.rangeslider__handle',
					"property" 			=> 'width|height',
					"control_type" 		=> 'measurebox'
				),
				array(
					"name" 				=> __('Slider Handle Border Radius', "oxy-ultimate"),
					"selector" 			=> '.rangeslider__handle',
					"property" 			=> 'border-radius'
				),
			)
		);

		$range_sld_num = $range_sld->addControlSection( "ff_range_number", __("Range Number", "oxy-ultimate"), "assets/icon.png", $this );
		$hide_rsld_num = $range_sld_num->addControl('buttons-list', 'hide_number', __('Hide Number Value'));
		$hide_rsld_num->setValue(['No', 'Yes']);
		$hide_rsld_num->setValueCSS(['Yes' => '.ff_range_value{display: none}']);
		$hide_rsld_num->setDefaultValue('No');
		$hide_rsld_num->whiteList();

		$range_selector = '.ff_range_value';
		$range_sld_num->addStyleControls(
			array(
				array(
					"selector" 			=> $range_selector,
					"property" 			=> 'font-size'
				),
				array(
					"selector" 			=> $range_selector,
					"property" 			=> 'color'
				),
				array(
					"selector" 			=> $range_selector,
					"property" 			=> 'font-weight'
				)
			)
		);*/

		/*****************************
		 * Repeater Field
		 *****************************/
		$ffrepeater = $this->addControlSection( "ff_repeater", __("Repeater Field", "oxy-ultimate"), "assets/icon.png", $this );
		$rep_selector = '.ff-el-repeater';

		$ffrepsp = $ffrepeater->addControlSection( "ff_repeatersp", __("Spacing & Background", "oxy-ultimate"), "assets/icon.png", $this );
		$ffrepsp->addPreset(
			"padding",
			"ffrepsp_padding",
			__("Padding"),
			$rep_selector
		)->whiteList();

		$ffrepsp->addStyleControls(
			array(
				array(
					"selector" 			=> $rep_selector ,
					"property" 			=> 'width'
				),
				array(
					"selector" 			=> $rep_selector ,
					"property" 			=> 'background-color'
				)
			)
		);

		$repvgap = $ffrepsp->addStyleControl(
			array(
				"name" 				=> __('Horizontal Gap between Fields', "oxy-ultimate"),
				"selector" 			=> 'table.ff_repeater_table td',
				"property" 			=> 'padding-right',
				'control_type' 		=> 'slider-measurebox'
			)
		);
		$repvgap->setRange('0', '100', '1');
		$repvgap->setUnits('px', 'px,%,em');
		$repvgap->setDefaultValue('15');

		$rephgap = $ffrepsp->addStyleControl(
			array(
				"name" 				=> __('Vertical Gap between Fields', "oxy-ultimate"),
				"selector" 			=> 'table.ff_repeater_table td',
				"property" 			=> 'padding-bottom',
				'control_type' 		=> 'slider-measurebox'
			)
		);
		$rephgap->setRange('0', '100', '1');
		$rephgap->setUnits('px', 'px,%,em');
		$rephgap->setDefaultValue('15');

		$ffrepeater->borderSection(__('Wrapper Border'), $rep_selector, $this);
		$ffrepeater->boxShadowSection(__('Wrapper Box Shadow'), $rep_selector, $this);

		$ffrepip = $ffrepeater->addControlSection( "ff_repip", __("Input Field Color", "oxy-ultimate"), "assets/icon.png", $this );
		$ffrepip->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> '.ff_repeater_table input, .ff_repeater_table select',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Focus Background Color'),
					"selector" 			=> '.ff_repeater_table input:focus, .ff_repeater_table select:focus',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Focus Text Color'),
					"selector" 			=> '.ff_repeater_table input:focus, .ff_repeater_table select:focus',
					"property" 			=> 'color'
				)
			)
		);

		$ffrepip_brd = $ffrepeater->addControlSection( "ffrepip_brd", __("Input Field Border", "oxy-ultimate"), "assets/icon.png", $this );
		$ffrepip_brd->addPreset(
			"border",
			'repip_border',
			__("Border"),
			'.ff_repeater_table input, .ff_repeater_table select'
		)->whiteList();

		$ffrepip_brd->addStyleControl(
			array(
				"name" 				=> __('Focus Border Color'),
				"selector" 			=> '.ff_repeater_table input:focus, .ff_repeater_table select:focus',
				"property" 			=> 'border-color'
			)
		)->whiteList();

		$ffrepip_brd->addPreset(
			"border-radius",
			"repip_border_radius",
			__("Border Radius"),
			'.ff_repeater_table input, .ff_repeater_table select'
		)->whiteList();

		$ffrepeater->typographySection( __('Input Text Typography'), '.ff_repeater_table input, .ff_repeater_table select', $this );

		$ffrepip_btn = $ffrepeater->addControlSection( "ffrepip_btn", __("Plus/Minus Circle", "oxy-ultimate"), "assets/icon.png", $this );
		$ffrepip_btn->addStyleControls(
			array(
				array(
					"name" 				=> __('Color'),
					"selector" 			=> '.repeat-plus svg, .repeat-minus svg',
					"property" 			=> 'color'
				),
				array(
					"name" 				=> __('Hover Color'),
					"selector" 			=> '.repeat-plus:hover svg, .repeat-minus:hover svg',
					"property" 			=> 'color'
				),
				array(
					"name" 				=> __('Size'),
					"selector" 			=> '.repeat-plus svg, .repeat-minus svg',
					'control_type' 		=> 'slider-measurebox',
					"property" 			=> 'width|height',
					'unit' 			=> 'px',
					'min' 				=> 20,
					'max' 				=> 50
				)
			)
		);

		$ffrepeater->typographySection(__('Field Label Typography'), $rep_selector . ' .ff-el-input--label label', $this);
		$ffrepeater->typographySection(__('Column Label Typography'), '.ff_repeater_table th .ff-el-input--label label', $this);

		/*****************************
		 * File Upload Field
		 *****************************/
		$ff_fup_section = $this->addControlSection( "ff_fup", __("File Upload Field", "oxy-ultimate"), "assets/icon.png", $this );
		$ffup_selector = '.ff_upload_btn';

		$ff_fup_section->typographySection( __("Button Typography"), $ffup_selector, $this );
		$ff_fup_section->borderSection( __("Button Border"), $ffup_selector, $this );
		$ff_fup_section->borderSection( __("Button Hover Border"), $ffup_selector . ":hover", $this );
		$ff_fup_section->boxShadowSection( __("Button Box Shadow"), $ffup_selector, $this );
		$ff_fup_section->boxShadowSection( __("Button Hover Box Shadow"), $ffup_selector . ":hover", $this );

		$fupBtn = $ff_fup_section->addControlSection( "ff_fupbtn", __("Button Style", "oxy-ultimate"), "assets/icon.png", $this );
		$fupBtn->addPreset(
			"padding",
			"fupBtn_padding",
			__("Padding"),
			$ffup_selector
		)->whiteList();

		$fupBtn->addStyleControls(
			array(
				array(
					"name" 				=> __('Text Hover Color', "oxy-ultimate"),
					"selector" 			=> $ffup_selector . ':hover',
					"property" 			=> 'color',
				),
				array(
					"selector" 			=> $ffup_selector,
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Background Hover Color', "oxy-ultimate"),
					"selector" 			=> $ffup_selector . ':Hover',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Width'),
					"selector" 			=> $ffup_selector,
					"property" 			=> 'width'
				)
			)
		);


		$ffsb_section = $this->addControlSection( "ffsb_section", __("Section Break", "oxy-ultimate"), "assets/icon.png", $this );

		$sec_selector = '.ff-el-section-break';
		$ffsb_section->addStyleControl(
			array(
				"selector" 			=> $sec_selector,
				"slug" 				=> 'ffsb_bgc',
				"property" 			=> 'background-color'
			)
		);

		$ffsb_section->addStyleControl(
			array(
				"selector" 			=> $sec_selector . ' hr',
				"slug" 				=> 'ffsb_line',
				"property" 			=> 'border-color'
			)
		)->setParam('hide_wrapper_end', true);

		$ffsb_section->addStyleControl(
			array(
				"selector" 			=> $sec_selector . ' hr',
				"slug" 				=> 'ffsb_linew',
				"property" 			=> 'border-width'
			)
		)->setParam('hide_wrapper_start', true);

		$ffsb_sp = $ffsb_section->addControlSection( "ffsb_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$ffsb_sp->addPreset(
			"padding",
			"ffsb_padding",
			__("Padding"),
			$sec_selector
		)->whiteList();
		
		$ffsb_sp->addPreset(
			"margin",
			"ffsb_margin",
			__("Margin"),
			$sec_selector
		)->whiteList();

		$ffsb_section->typographySection(__("Section Title", "oxy-ultimate"),".ff-el-section-title", $this);
		$ffsb_section->typographySection(__("Section Description", "oxy-ultimate"),".ff-section_break_desk", $this);

		/*****************************
		 * Net Promoter Score
		 *****************************/
		$ffnps_section = $this->addControlSection( "nps_section", __("Net Promoter Score", "oxy-ultimate"), "assets/icon.png", $this );
		$nps_selector = '.ff_net_table';

		$ffnps_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Sub Label Color', 'oxy-ultimate'),
					"selector" 			=> $nps_selector . ' thead th span',
					"property" 			=> 'color',
					"slug" 				=> 'ouffnps_tlc'
				),
				array(
					"selector" 			=> $nps_selector . ' tbody tr td',
					"property" 			=> 'background-color',
					"slug" 				=> 'ouffnps_tbgc'
				),
				array(
					"selector" 			=> $nps_selector . ' tbody tr td, .ff_net_table tbody tr td:first-of-type',
					"property" 			=> 'border-color',
					"slug" 				=> 'ouffnps_tbrdc'
				),
				array(
					"selector" 			=> $nps_selector . ' tbody tr td, .ff_net_table tbody tr td:first-of-type',
					"property" 			=> 'border-width'
				),
				array(
					"name" 				=> __('Hover Background Color', 'oxy-ultimate'),
					"selector" 			=> '.ff_net_table tbody tr td label:hover:after',
					"property" 			=> 'background-color',
					"slug" 				=> 'ouffnps_thbgc'
				),
				array(
					"name" 				=> __('Hover Border Color', 'oxy-ultimate'),
					"selector" 			=> '.ff_net_table tbody tr td label:hover:after',
					"property" 			=> 'border-color',
					"slug" 				=> 'ouffnps_thbrdc'
				),
				array(
					"name" 				=> __('Hover Border Width', 'oxy-ultimate'),
					"selector" 			=> '.ff_net_table tbody tr td label:hover:after',
					"property" 			=> 'border-width',
					"slug" 				=> 'ouffnps_thbw'
				),
				array(
					"name" 				=> __('Number Color', 'oxy-ultimate'),
					"selector" 			=> '.ff-el-net-label span',
					"property" 			=> 'color',
					"slug" 				=> 'ouffnps_txtc'
				),
				array(
					"name" 				=> __('Hover Number Color', 'oxy-ultimate'),
					"selector" 			=> '.ff-el-net-label:hover span',
					"property" 			=> 'color',
					"slug" 				=> 'ouffnps_txthc'
				),
				array(
					"name" 				=> __('Checked Background Color', 'oxy-ultimate'),
					"selector" 			=> $nps_selector . ' tbody tr td input[type=radio]:checked+label',
					"property" 			=> 'background-color',
					"slug" 				=> 'ouffnps_txtbgc'
				),
				array(
					"name" 				=> __('Checked Number Color', 'oxy-ultimate'),
					"selector" 			=> $nps_selector . ' tbody tr td input[type=radio]:checked+label *',
					"property" 			=> 'color',
					"slug" 				=> 'ouffnps_txtcbgc'
				),
			)
		);




		/*****************************
		 * Payment Summary
		 *****************************/
		$ffps_section = $this->addControlSection( "ffps_section", __("Payment Summary", "oxy-ultimate"), "assets/icon.png", $this );
		$ps_selector = '.ff_dynamic_payment_summary';
		$ffps_section->addStyleControls(
			array(
				array(
					"selector" 			=> $ps_selector . ' .ffp_table',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Outer Border Color'),
					"selector" 			=> $ps_selector . ' .ffp_table',
					"property" 			=> 'border-color',
					"slug" 				=> 'ouffps_tbrdc'
				),
				array(
					"name" 				=> __('Outer Border Width'),
					"selector" 			=> $ps_selector . ' .ffp_table',
					"property" 			=> 'border-width'
				)
			)
		);

		$thead_section = $ffps_section->addControlSection( "ffpsth_section", __("Table Head", "oxy-ultimate"), "assets/icon.png", $this );
		$thead_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Table Head Background Color'),
					"selector" 			=> $ps_selector . ' .ffp_table thead',
					"property" 			=> 'background-color'
				),
				array(
					"selector" 			=> 'table.input_items_table thead tr th',
					"property" 			=> 'border-color',
					"slug" 				=> 'ouffps_thcbrdc'
				),
				array(
					"selector" 			=> 'table.input_items_table thead tr th',
					"property" 			=> 'border-width',
					"slug" 				=> 'border-thcbrdw'
				),
				array(
					"name" 				=> __('Text Color'),
					"selector" 			=> $ps_selector . ' thead th',
					"property" 			=> 'color'
				),
				array(
					"name" 				=> __('Text Font Weight'),
					"selector" 			=> $ps_selector . ' thead th',
					"property" 			=> 'font-weight'
				),
				array(
					"name" 				=> __('Text Font Size'),
					"selector" 			=> $ps_selector . ' thead th',
					"property" 			=> 'font-size'
				),
			)
		);

		$ffpstbody_section = $ffps_section->addControlSection( "ffpstbody_section", __("Table Body", "oxy-ultimate"), "assets/icon.png", $this );

		$ffpstbody_section->addStyleControls(
			array(
				array(
					"selector" 			=> $ps_selector . ' .ffp_table tbody tr',
					"property" 			=> 'background-color',
					"slug" 				=> 'ouffps_tcbgc'
				),
				array(
					"name" 				=> __('Alternate Background Color'),
					"selector" 			=> $ps_selector . ' .ffp_table tbody tr:even',
					"property" 			=> 'background-color',
					"slug" 				=> 'ouff_tcabgc'
				),
				array(
					"selector" 			=> 'table.input_items_table tbody tr td',
					"property" 			=> 'border-color',
					"slug" 				=> 'ouffps_tcbrdc'
				),
				array(
					"selector" 			=> 'table.input_items_table tbody tr td',
					"property" 			=> 'border-width',
					"slug" 				=> 'border-tcbrdw'
				),
				array(
					"name" 				=> __('Text Color'),
					"selector" 			=> $ps_selector . ' tbody td',
					"property" 			=> 'color'
				),
				array(
					"name" 				=> __('Text Font Weight'),
					"selector" 			=> $ps_selector . ' tbody td',
					"property" 			=> 'font-weight'
				),
				array(
					"name" 				=> __('Text Font Size'),
					"selector" 			=> $ps_selector . ' tbody td',
					"property" 			=> 'font-size'
				),
			)
		);

		$ffpstft_section = $ffps_section->addControlSection( "ffpstft_section", __("Table Footer", "oxy-ultimate"), "assets/icon.png", $this );
		$ffpstft_section->addStyleControls(
			array(
				array(
					"selector" 			=> $ps_selector . ' .ffp_table tfoot tr',
					"property" 			=> 'background-color',
					"slug" 				=> 'ouffps_tfbgc'
				),
				array(
					"selector" 			=> 'table.input_items_table tfoot tr th',
					"property" 			=> 'border-color',
					"slug" 				=> 'ouffps_tftbrdc'
				),
				array(
					"selector" 			=> 'table.input_items_table tfoot tr th',
					"property" 			=> 'border-width',
					"slug" 				=> 'border-tftbrdw'
				),
				array(
					"name" 				=> __('Total Text Color'),
					"selector" 			=> $ps_selector . ' tfoot th.item_right',
					"property" 			=> 'color',
					"slug" 				=> 'ouffps_ttc'
				),
				array(
					"name" 				=> __('Total Text Font Weight'),
					"selector" 			=> $ps_selector . ' tfoot th.item_right',
					"property" 			=> 'font-weight',
					"slug" 				=> 'ouffps_tffw'
				),
				array(
					"name" 				=> __('Total Text Font Size'),
					"selector" 			=> $ps_selector . ' tfoot th.item_right',
					"property" 			=> 'font-size',
					"slug" 				=> 'ouffps_twfs'
				),
				array(
					"name" 				=> __('Total Price Color'),
					"selector" 			=> $ps_selector . ' tfoot th',
					"property" 			=> 'color',
					"slug" 				=> 'ouffps_tfpc'
				),
				array(
					"name" 				=> __('Total Price Font Weight'),
					"selector" 			=> $ps_selector . ' tfoot th',
					"property" 			=> 'font-weight',
					"slug" 				=> 'ouffps_tfpfw'
				),
				array(
					"name" 				=> __('Total Price Font Size'),
					"selector" 			=> $ps_selector . ' tfoot th',
					"property" 			=> 'font-size',
					"slug" 				=> 'ouffps_tfpfs'
				),
			)
		);

		/*****************************
		 * Progress Bar
		 *****************************/
		$ffmpb_section = $this->addControlSection( "ffmpb_section", __("Progress Bar", "oxy-ultimate"), "assets/icon.png", $this );

		$ffmpb_section->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __("Progress Indicator", "oxy-ultimate"),
				"value" 	=> [ 'pb' => __("Progress Bar", "oxy-ultimate"), "step" => __("Steps", "oxy-ultimate"), "none" => __("None", "oxy-ultimate") ],
				"slug" 		=> "ouff_pbind",
				"default" 	=> 'pb',
				"css" 		=> false
			)
		);

		$ffmpb_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Label Color', "oxy-ultimate"),
					"selector" 			=> '.ff-el-progress-status',
					"property" 			=> 'color',
					"slug" 				=> "ouff_pblbc",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Label Font Size', "oxy-ultimate"),
					"selector" 			=> '.ff-el-progress-status',
					"property" 			=> 'font-size',
					"slug" 				=> "ouff_pblbfs",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Label Font Weight', "oxy-ultimate"),
					"selector" 			=> '.ff-el-progress-status',
					"property" 			=> 'font-weight',
					"slug" 				=> "ouff_pblbfw",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress',
					"property" 			=> 'background-color',
					"slug" 				=> "ouff_pbabg",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Active Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress-bar',
					"property" 			=> 'background-color',
					"slug" 				=> "ouff_pbabgc",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Height', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress',
					"property" 			=> 'height',
					"slug" 				=> "ouff_pbah",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Overflow', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress',
					"property" 			=> 'overflow',
					"control_type" 		=> 'radio',
					"value" 			=> [ "visible" => __( "Visible", "oxy-ultimate" ), "hidden" => __( "Hidden", "oxy-ultimate" ) ],
					"default" 			=> 'hidden',
					"slug" 				=> "ouff_pbvb",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Score Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress-bar span',
					"property" 			=> 'color',
					"slug" 				=> "ouff_pbtxtc",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Score Position Top', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress span',
					"property" 			=> 'top',
					"slug" 				=> "ouff_pbptop",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Score Position Left', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress span',
					"property" 			=> 'left',
					"slug" 				=> "ouff_pbpleft",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Score Font Size', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress span',
					"property" 			=> 'font-size',
					"slug" 				=> "ouff_pbscfs",
					"condition" 		=> 'ouff_pbind=pb'
				),
				array(
					"name" 				=> __('Progress Bar Score Font Weight', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-el-progress span',
					"property" 			=> 'font-weight',
					"slug" 				=> "ouff_pbscfw",
					"condition" 		=> 'ouff_pbind=pb'
				)
			)
		);

		$sqbpb = $ffmpb_section->addControlSection( 'ffspsb_section', __('Square Box', "oxy-ultimate"), 'assets/icon.png', $this );

		$sqbpb->addStyleControls(
			array(
				array(
					"name" 				=> __('Width', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:before',
					"property" 			=> 'width|height',
					'unit' 				=> 'px',
					"control_type" 		=> 'slider-measurebox',
					"slug" 				=> "ouff_pbstepw",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Border Radius', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:before',
					"property" 			=> 'border-radius',
					"slug" 				=> "ouff_pbstepbr",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Background Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:not(.ff_active):before',
					"property" 			=> 'background',
					'control_type' 		=> 'colorpicker',
					"slug" 				=> "ouff_pbstepbgc",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Border Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:not(.ff_active):before',
					"property" 			=> 'border-color',
					"slug" 				=> "ouff_pbstepbrdc",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Border Width', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:before',
					"property" 			=> 'border-width',
					"slug" 				=> "ouff_pbstepbrdw",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Active Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li.ff_active:before, .fluentform .ff-step-titles li.ff_completed:before',
					"property" 			=> 'border-color|background',
					"control_type" 		=> 'colorpicker',
					"slug" 				=> "ouff_pbstepactive",
					"condition" 		=> 'ouff_pbind=step'
				)
			)
		);

		$hrpb = $ffmpb_section->addControlSection( 'ffsphr_section', __('Horizontal Line', "oxy-ultimate"), 'assets/icon.png', $this );
		$hrpb->addStyleControls(
			array(
				array(
					"name" 				=> __('Position', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:after',
					"property" 			=> 'top',
					"slug" 				=> "ouff_pbsteplpos",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Width', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:after',
					"property" 			=> 'height',
					"slug" 				=> "ouff_pbsteplh",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:after',
					"property" 			=> 'background',
					"control_type" 		=> 'colorpicker',
					"slug" 				=> "ouff_pbsteplactive",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Active Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li.ff_active:after, .fluentform .ff-step-titles li.ff_completed:after',
					"property" 			=> 'background',
					'control_type' 		=> 'colorpicker',
					"slug" 				=> "ouff_pbsteplac",
					"condition" 		=> 'ouff_pbind=step'
				)
			)
		);

		$numbpb = $ffmpb_section->addControlSection( 'ffspnumb_section', __('Step Number', "oxy-ultimate"), 'assets/icon.png', $this );
		$numbpb->addStyleControls(
			array(
				array(
					"name" 				=> __('Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:not(.ff_active):before',
					"property" 			=> 'color',
					"slug" 				=> "ouff_pbstepnumc",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Active Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li.ff_active:before, .fluentform .ff-step-titles li.ff_completed:before',
					"property" 			=> 'color',
					"slug" 				=> "ouff_pbstepnumac",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Font Size', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:before',
					"property" 			=> 'font-size',
					"slug" 				=> "ouff_pbstepnumfs",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Font Weight', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:before',
					"property" 			=> 'font-weight',
					"slug" 				=> "ouff_pbstepnumfw",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Line Height', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li:before',
					"property" 			=> 'line-height',
					"slug" 				=> "ouff_pbstepnumlh",
					"condition" 		=> 'ouff_pbind=step'
				)
			)
		);

		$txtpb = $ffmpb_section->addControlSection( 'ffsptxt_section', __('Text or Label', "oxy-ultimate"), 'assets/icon.png', $this );
		$txtpb->addStyleControls(
			array(
				array(
					"name" 				=> __('Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li',
					"property" 			=> 'color',
					"slug" 				=> "ouff_pbsteptxtc",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Active Color', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li.ff_active, .fluentform .ff-step-titles li.ff_completed',
					"property" 			=> 'color',
					"slug" 				=> "ouff_pbsteptxtac",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Font Size', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li',
					"property" 			=> 'font-size',
					"slug" 				=> "ouff_pbsteptxtfs",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Font Weight', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li',
					"property" 			=> 'font-weight',
					"slug" 				=> "ouff_pbsteptxtfw",
					"condition" 		=> 'ouff_pbind=step'
				),
				array(
					"name" 				=> __('Line Height', "oxy-ultimate"),
					"selector" 			=> '.fluentform .ff-step-titles li',
					"property" 			=> 'line-height',
					"slug" 				=> "ouff_pbsteptxtlh",
					"condition" 		=> 'ouff_pbind=step'
				),
			)
		);

		$ffmpb_section->addPreset(
			"margin",
			"ffpbar_margin",
			__("Margin"),
			'.ff-step-header'
		)->whiteList();


		/*****************************
		 * Submit Button
		 *****************************/
		$ffbtn_section = $this->addControlSection( "ffbtn_section", __("Submit Button", "oxy-ultimate"), "assets/icon.png", $this );
		
		$ffbtn_section->addOptionControl(
			array(
				'type' 					=> 'radio',
				'name' 					=> __( 'Hide Button', 'oxy-ultimate' ),
				'slug' 					=> 'ffbtn_hide',
				'value' 				=> array( "no" => __("No"), "yes" => __("Yes") ),
				'default' 				=> 'no',
				"css" 		=> false
			)
		);

		$ffbtn_section->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> '.ff-btn.ff-btn-submit',
				"property" 			=> 'width'
			)
		);

		$spacing = $ffbtn_section->addControlSection( "ffbtn_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"ffbtn_section_padding",
			__("Padding"),
			'.ff-btn-submit'
		)->whiteList();
		
		$ffbtn_clr = $ffbtn_section->addControlSection( "ffbtn_clr", __("Color", "oxy-ultimate"), "assets/icon.png", $this );
		$ffbtn_clr->addStyleControls(
			array(
				array(
					"name" 				=> __('Text Color', "oxy-ultimate"),
					"selector" 			=> '.ff-btn.ff-btn-submit',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Text Hover Color', "oxy-ultimate"),
					"selector" 			=> '.ff-btn.ff-btn-submit:hover',
					"property" 			=> 'color',
				),
				array(
					"selector" 			=> '.ff-btn.ff-btn-submit',
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Background Hover Color', "oxy-ultimate"),
					"selector" 			=> '.ff-btn.ff-btn-submit:Hover',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				)
			)
		);

		$ffbtn_section->typographySection( __("Typography"), '.ff-btn.ff-btn-submit', $this );
		$ffbtn_section->borderSection( __("Border"), '.ff-btn.ff-btn-submit', $this );
		$ffbtn_section->borderSection( __("Hover Border"), '.ff-btn.ff-btn-submit:hover', $this );
		$ffbtn_section->boxShadowSection( __("Box Shadow"), '.ff-btn.ff-btn-submit', $this );
		$ffbtn_section->boxShadowSection( __("Hover Box Shadow"), '.ff-btn.ff-btn-submit:hover', $this );


		/*****************************
		 * Step Form Next Button
		 *****************************/
		$ffsfnb_section = $this->addControlSection( "ffsfnb_section", __("Next Button", "oxy-ultimate"), "assets/icon.png", $this );

		$ffsfnb_section->addPreset(
			"padding",
			"sfnb_padding",
			__("Padding"),
			'.ff-btn.ff-btn-next'
		)->whiteList();

		$ffsfnb_section->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> '.ff-btn.ff-btn-next',
				"property" 			=> 'width'
			)
		);

		$ffsfnb_clr = $ffsfnb_section->addControlSection( "ffsfnb_clr", __("Color", "oxy-ultimate"), "assets/icon.png", $this );
		$ffsfnb_clr->addStyleControls(
			array(
				array(
					"name" 				=> __('Text Color'),
					"selector" 			=> '.ff-btn.ff-btn-next',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Text Hover Color'),
					"selector" 			=> '.ff-btn.ff-btn-next:hover',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> '.ff-btn.ff-btn-next',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Background Hover Color'),
					"selector" 			=> '.ff-btn.ff-btn-next:Hover',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				)
			)
		);

		$ffsfnb_section->typographySection( __("Typography"), '.ff-btn.ff-btn-next', $this );
		$ffsfnb_section->borderSection( __("Border"), '.ff-btn.ff-btn-next', $this );
		$ffsfnb_section->borderSection( __("Hover Border"), '.ff-btn.ff-btn-next:hover', $this );
		$ffsfnb_section->boxShadowSection( __("Box Shadow"), '.ff-btn.ff-btn-next', $this );
		$ffsfnb_section->boxShadowSection( __("Hover Box Shadow"), '.ff-btn.ff-btn-next:hover', $this );

		/*****************************
		 * Step Form Previous Button
		 *****************************/
		$ffsfpb_section = $this->addControlSection( "ffsfpb_section", __("Previous Button", "oxy-ultimate"), "assets/icon.png", $this );
		
		$ffsfpb_section->addPreset(
			"padding",
			"sfpb_padding",
			__("Padding"),
			'.ff-btn.ff-btn-prev'
		)->whiteList();

		$ffsfpb_section->addStyleControl(
			array(
				"name" 				=> __('Width'),
				"selector" 			=> '.ff-btn.ff-btn-prev',
				"property" 			=> 'width'
			)
		);

		$ffsfpb_clr = $ffsfpb_section->addControlSection( "ffsfpb_clr", __("Color", "oxy-ultimate"), "assets/icon.png", $this );
		$ffsfpb_clr->addStyleControls(
			array(
				array(
					"name" 				=> __('Text Color'),
					"selector" 			=> '.ff-btn.ff-btn-prev',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Text Hover Color'),
					"selector" 			=> '.ff-btn.ff-btn-prev:hover',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> '.ff-btn.ff-btn-prev',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Background Hover Color'),
					"selector" 			=> '.ff-btn.ff-btn-prev:Hover',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker'
				),
				array(
					"name" 				=> __('Width'),
					"selector" 			=> '.ff-btn.ff-btn-prev',
					"property" 			=> 'width'
				)
			)
		);

		$ffsfpb_section->typographySection( __("Typography"), '.ff-btn.ff-btn-prev', $this );
		$ffsfpb_section->borderSection( __("Border"), '.ff-btn.ff-btn-prev', $this );
		$ffsfpb_section->borderSection( __("Hover Border"), '.ff-btn.ff-btn-prev:hover', $this );
		$ffsfpb_section->boxShadowSection( __("Box Shadow"), '.ff-btn.ff-btn-prev', $this );
		$ffsfpb_section->boxShadowSection( __("Hover Box Shadow"), '.ff-btn.ff-btn-prev:hover', $this );

		/*****************************
		 * Validation Error
		 *****************************/

		$ffer_section = $this->addControlSection( "ffer_section", __("Validation Error", "oxy-ultimate"), "assets/icon.png", $this );
		$ffer_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Text Color'),
					"selector" 			=> '.ff-el-is-error .text-danger',
					"property" 			=> 'color',
					"value" 			=> "#f56c6c"
				),
				array(
					"selector" 			=> '.ff-el-is-error .text-danger',
					"property" 			=> 'font-size',
					"value" 			=> 14
				),
				array(
					"selector" 			=> '.ff-el-is-error .text-danger',
					"property" 			=> 'font-weight'
				),
				array(
					"selector" 			=> '.ff-el-is-error .text-danger',
					"property" 			=> 'margin-top',
					"value" 			=> 4
				),
			)
		);

		
		/*****************************
		 * Success Message
		 *****************************/
		$ffsuc_section = $this->addControlSection( "ffsuc_section", __("Success Message", "oxy-ultimate"), "assets/icon.png", $this );

		$ffsuc_section->addStyleControl(
			array(
				'selector' 		=> '.ff-message-success',
				'value' 		=> 100,
				'property' 		=> 'width'
			)
		)->setUnits("%", "px,%");

		$ffsuc_section->addStyleControl(
			array(
				'selector' 		=> '.ff-message-success',
				'property' 		=> 'background-color'
			)
		);

		$ffsuc_section->typographySection( __("Typography"), '.ff-message-success, .ff-message-success p', $this );
		$ffsuc_section->borderSection( __("Border"), '.ff-message-success', $this );
		$ffsuc_section->boxShadowSection( __("Box Shadow"), '.ff-message-success', $this );

		$ffsuc_section->addPreset(
			"padding",
			"ffsuc_padding",
			__("Padding"),
			'.ff-message-success'
		)->whiteList();
	}

	function render( $options, $defaults, $content ) {
		if( $options['fluentform'] == "no" ) {
			echo '<h5 class="form-missing">' . __("Select a form", 'oxy-ultimate') . '</h5>';
			return;
		}

		echo do_shortcode('[fluentform id='. $options['fluentform'] .']' );
	}

	function init() {
		$this->El->useAJAXControls();
		if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'oxyu_ff_enqueue_scripts' ) );
		}
	}

	function oxyu_ff_enqueue_scripts() {
		$fluentFormPublicCss = get_home_url() . '/wp-content/plugins/fluentform/public/css/fluent-forms-public.css';
		$fluentFormPublicDefaultCss = get_home_url() . '/wp-content/plugins/fluentform/public/css/fluentform-public-default.css';

		if (is_rtl()) {
			$fluentFormPublicCss = get_home_url() . '/wp-content/plugins/fluentform/public/css/fluent-forms-public-rtl.css';
			$fluentFormPublicDefaultCss = get_home_url() . '/wp-content/plugins/fluentform/public/css/fluentform-public-default-rtl.css';
		}

		wp_enqueue_style('fluent-form-styles',$fluentFormPublicCss,array(),FLUENTFORM_VERSION);
		wp_enqueue_style('fluentform-public-default',$fluentFormPublicDefaultCss,array(),FLUENTFORM_VERSION);
		wp_enqueue_script('fluent-form-submission');
	}

	// Get all forms of WP Fluent Forms plugin
	function oxyu_get_fluent_forms() {
		$options = array();
		$options['no'] = esc_html__( 'Select a form', 'oxy-ultimate' );

		if ( function_exists( 'wpFluentForm' ) ) {
			global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fluentform_forms" );
			if ( $result ) {
				foreach ( $result as $form ) {
					$options[$form->id] = str_replace(' ', '&#8205; ', preg_replace("/[^a-zA-Z0-9\s]+/", "", $form->title ) );
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

	function customCSS( $options, $selector ) {
		$css = $defaultCSS = '';
		
		if( ! $this->css_added ) {
			$defaultCSS = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();
		if( isset( $options[ $prefix . '_ff_inp_placeholder'] ) ){
			$css .= '.fluent_form_'. $options[$prefix . '_fluentform'] .' .ff-el-form-control::-webkit-input-placeholder,.fluent_form_'. $options[$prefix . '_fluentform'] .' .ff-el-form-control::-moz-input-placeholder,.fluent_form_'. $options[$prefix . '_fluentform'] .' .ff-el-form-control:-ms-input-placeholder,.fluent_form_'. $options[$prefix . '_fluentform'] .' .ff-el-form-control::placeholder{color:'. $options[$prefix . '_ff_inp_placeholder'] .';}';
		}

		if( isset( $options[$prefix . '_rc_smart_ui'] ) && $options[$prefix . '_rc_smart_ui'] == "yes" ) {
			$css .= $selector ." .ff-el-group input[type=checkbox]:after, ". $selector  ." .ff-el-group input[type=radio]:after {content: \" \"!important;display: inline-block!important;}";
			$css .= $selector ." .ff-el-group input[type=checkbox], " . $selector ." .ff-el-group input[type=radio]{width: 0px;}";
			$css .= $selector ." .ff-el-group .ff-el-form-check-label span{ padding-left: 23px; }";
		} else {
			$css .= $selector ." .ff-el-group input[type=checkbox]:after, ". $selector  ." .ff-el-group input[type=radio]:after {content: none;}";
		}

		if( isset( $options[ $prefix . '_ffbtn_hide'] ) && $options[ $prefix . '_ffbtn_hide'] == "yes" ){
			$css .= $selector ." .ff_submit_btn_wrapper{ display: none }";
		}

		return $defaultCSS . $css;
	}
}
 
new OUFFStyler();