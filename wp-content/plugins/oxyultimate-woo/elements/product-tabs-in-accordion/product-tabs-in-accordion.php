<?php
class OUWooProductTabsInAccrodion extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return "Tabs To Accordion";
	}

	function slug() {
		return "product-tabs-in-accordion";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function custom_init() {
		add_filter("oxygen_vsb_element_presets_defaults", array( $this, "ouwoo_tabs_acrd_presets_defaults" ) );
	}

	function ouwoo_tabs_acrd_presets_defaults( $all_elements_defaults ) {
		require("preset.php");

		$all_elements_defaults = array_merge($all_elements_defaults, $accordion);

		return $all_elements_defaults;
	}

    function controls() {

		$this->addOptionControl(
			array(
				'type' 		=> "radio",
				"name" 		=> __('Expand First Item', "oxyultimate-woo"),
				"slug" 		=> "prdacd_expand",
				"value" 	=> array(
					"yes" 		=> __('Yes', "oxyultimate-woo"),
					"no" 		=> __('No', "oxyultimate-woo"),
				),
				"default" 		=> "yes"
			)
		);

		$this->addOptionControl(
			array(
				'type' 		=> "radio",
				"name" 		=> __('Remove Description Tab?', "oxyultimate-woo"),
				"slug" 		=> "prdacd_rem_desc",
				"value" 	=> array(
					"yes" 		=> __('Yes', "oxyultimate-woo"),
					"no" 		=> __('No', "oxyultimate-woo"),
				),
				"default" 		=> "no"
			)
		)->rebuildElementOnChange();

		$this->addOptionControl(
			array(
				'type' 		=> "radio",
				"name" 		=> __('Remove Additional Information Tab?', "oxyultimate-woo"),
				"slug" 		=> "prdacd_rem_addinfo",
				"value" 	=> array(
					"yes" 		=> __('Yes', "oxyultimate-woo"),
					"no" 		=> __('No', "oxyultimate-woo"),
				),
				"default" 		=> "no"
			)
		)->rebuildElementOnChange();

		$this->addOptionControl(
			array(
				'type' 		=> "radio",
				"name" 		=> __('Remove Reviews Tab?', "oxyultimate-woo"),
				"slug" 		=> "prdacd_rem_reviews",
				"value" 	=> array(
					"yes" 		=> __('Yes', "oxyultimate-woo"),
					"no" 		=> __('No', "oxyultimate-woo"),
				),
				"default" 		=> "no"
			)
		)->rebuildElementOnChange();


		$this->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Duration for Slide Up/Down'),
			'slug' 		=> 'toggle_speed'
		])->setRange(0,2000,10)->setUnits('ms', 'ms')->setDefaultValue(650);

    	/**
    	 * Tabs Title section
    	 */
    	$acrdtitle_section = $this->addControlSection( "acrdtitle_section", __("Tab Title"), "assets/icon.png", $this );

    	$selector = ".wc-prd-accordion-button";

    	$acrdtitle_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color - Initial State'),
					"selector" 			=> $selector,
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker',
				),
				array(
					"name" 				=> __('Gap Bewtween Two Tabs'),
					"selector" 			=> $selector,
					"property" 			=> 'margin-bottom',
					"control_type" 		=> 'slider-measurebox',
					"unit" 				=> 'px'
				)
			)
		);

    	$acrdtitle_section->addStyleControl([
			'control_type' 		=> 'slider-measurebox',
			'name' 				=> __('Transition Duration of Hover/Active State'),
			'selector' 			=> $selector,
			'property' 			=> 'transition-duration'

		])->setRange(0,15,0.1)->setUnits('s', 'sec')->setDefaultValue(0.3);

		//* sub-section
		$default_state_section = $acrdtitle_section->addControlSection(
			"default_state",
			__("Typography"), 
			"assets/icon.png", 
			$this
		);

        $slug = $this->selector2slug($selector);
        $slug.= "_typography";

		$typographyPreset = $default_state_section->addPreset(
			"typography",
			$slug,
			__("Typography"),
			$selector
		);

		$typographyPreset->removeTypographyAlign();

		/**
		 * Hovered state
		 */
		$hover_state_section = $acrdtitle_section->addControlSection("hover_state", __("Hover State"), "assets/icon.png", $this);

		$hover_state_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Text Color'),
					"selector" 			=> '.wc-prd-accordion-button:hover',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> '.wc-prd-accordion-button:hover',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker',
				),
				array(
					"name" 				=> __('Border Color'),
					"selector" 			=> '.wc-prd-accordion-button:hover',
					"property" 			=> 'border-color',
					"control_type" 		=> 'colorpicker',
				)
			)
		);

		/**
		 * Active state
		 */
		$active_state_section = $acrdtitle_section->addControlSection("active_state", __("Active State"), "assets/icon.png", $this);
		$active_state_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Active Tab Text Color'),
					"selector" 			=> '.wc-prd-accordion-item-active .wc-prd-accordion-button',
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Active Tab Background Color'),
					"selector" 			=> '.wc-prd-accordion-item-active .wc-prd-accordion-button',
					"property" 			=> 'background-color|border-bottom-color',
					"control_type" 		=> 'colorpicker',
				),
				array(
					"name" 				=> __('Active Tab Border Color'),
					"selector" 			=> '.wc-prd-accordion-item-active .wc-prd-accordion-button',
					"property" 			=> 'border-color',
					"control_type" 		=> 'colorpicker',
				)
			)
		);


		/********************
		 * Spacing
		 ********************/
		$spacing = $acrdtitle_section->addControlSection("spacing_tab", __("Spacing"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"tabttle_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"tabttle_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Border
		$acrdtitle_section->borderSection( __("Border"), $selector, $this );

		//* Box Shadow
		$acrdtitle_section->boxShadowSection( __("Box Shadow"), $selector, $this );



		/***********************
		 * Plus/Minus Button
		 **********************/

		$pmbutton = $this->addControlSection('pm_section', __("Toggle Button"), "assets/icon.png", $this);
		
		$pmselector = '.wc-prd-accordion-pm';

		$pmbutton->addStyleControls([
			[
				'name' 			=> __('Wrapper Width'),
				'selector' 		=> $pmselector,
				'property' 		=> 'width|height|line-height',
				"control_type" 	=> "slider-measurebox",
				"unit" 			=> "px"
			],
			[
				'name' 		=> __('Size'),
				'selector' 	=> $pmselector,
				'property' 	=> 'font-size'
			],
		]);

		$pmclr = $pmbutton->addControlSection('pmclr_section', __("Default Color"), "assets/icon.png", $this);
		$pmclr->addStyleControls([
			[
				'selector' 	=> $pmselector,
				'property' 	=> 'color'
			],
			[
				'selector' 	=> $pmselector,
				'property' 	=> 'background-color'
			]
		]);

		$pmhclr = $pmbutton->addControlSection('pmhclr_section', __("Hover Color"), "assets/icon.png", $this);
		$pmhclr->addStyleControls([
			[
				'selector' 	=> '.wc-prd-accordion-button:hover ' . $pmselector,
				'property' 	=> 'color'
			],
			[
				'selector' 	=> '.wc-prd-accordion-button:hover ' . $pmselector,
				'property' 	=> 'background-color'
			],
			[
				'selector' 	=> '.wc-prd-accordion-button:hover ' . $pmselector,
				'property' 	=> 'border-color'
			]
		]);

		$pmaclr = $pmbutton->addControlSection('pmaclr_section', __("Active Color"), "assets/icon.png", $this);
		$pmaclr->addStyleControls([
			[
				'selector' 	=> '.wc-prd-accordion-item-active ' . $pmselector,
				'property' 	=> 'color'
			],
			[
				'selector' 	=> '.wc-prd-accordion-item-active ' . $pmselector,
				'property' 	=> 'background-color'
			],
			[
				'selector' 	=> '.wc-prd-accordion-item-active ' . $pmselector,
				'property' 	=> 'border-color'
			]
		]);

		//* Border
		$pmbutton->borderSection( __("Border"), $pmselector, $this );

		//* Box Shadow
		$pmbutton->boxShadowSection( __("Box Shadow"), $pmselector, $this );

		
		/**
		 * Headings Section 
		 */
		$headings_section = $this->typographySection(
			__("Headings"),
			'.wc-prd-accordion-content h2, #reviews #comments h2',
			$this
		);

		/**
		* Accordion Content Section
		*/

		$content_section = $this->addControlSection("content_section", __("Content"), "assets/icon.png", $this);
		$selector = ".wc-prd-accordion-content";

		$content_section->addStyleControls(
			array(
				array(
				"selector" => $selector,
				"property" => 'background-color',
				),
			)
		);

		$content_sp = $content_section->addControlSection("contentsp_section", __("Spacing"), "assets/icon.png", $this);
		$content_sp->addPreset(
			"padding",
			"content_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$content_sp->addPreset(
			"margin",
			"content_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$content_section->typographySection(
			__("Typography"),
			$selector,
			$this
		);

		// Border sub-section
		$content_section->borderSection(
			__("Border"),
			$selector,
			$this
		);

		//* Box Shadow
		$content_section->boxShadowSection( 
			__("Box Shadow"), 
			$selector, 
			$this 
		);


        /**
         * additional info
         */

		$addtlinfo_section = $this->addControlSection("addtlinfo_section", __("Additional Info"), "assets/icon.png", $this);

		$addtlinfo_section->typographySection(
			__("Typography"),
			'.woocommerce-Tabs-panel--additional_information table.shop_attributes th, table.shop_attributes td',
			$this
		);


		$addtlinfo_section->typographySection(
			__("Value Typography"),
			'.woocommerce-Tabs-panel--additional_information table.shop_attributes th + td', 
			$this
		);

		$cellSection = $addtlinfo_section->addControlSection("cells", __("Cell Content"), "assets/icon.png", $this);

		$cellSection->addPreset(
			"padding",
			"table_cell_padding",
			__("Padding"),
			".woocommerce-Tabs-panel--additional_information table.shop_attributes th, .woocommerce-Tabs-panel--additional_information table.shop_attributes td"
		);

		$cellSection->addStyleControls(
			array(
				array(
					"name" 				=> __('Background'),
					"property" 			=> 'background-color',
					"selector" 			=> ".woocommerce-Tabs-panel--additional_information table.shop_attributes tr"
				),
				array(
					"name" 				=> __('Alternating Background'),
					"property" 			=> 'background-color',
					"selector" 			=> ".woocommerce-Tabs-panel--additional_information table.shop_attributes tr:nth-child(even)"
				),
				array(
					"name"				=> __('Border Color'),
					"property" 			=> 'border-color',
					"selector" 			=> ".woocommerce-Tabs-panel--additional_information table.shop_attributes th, .woocommerce-Tabs-panel--additional_information table.shop_attributes td"
				),
			)
		);


		/**
		 * Product Reviews Controls
		 */

		$reviews_section = $this->addControlSection("reviews_section", __("Reviews Form"), "assets/icon.png", $this);

		$reviews_section->addStyleControls(
			array(
				array(
					"name" 				=> __('Background Color'),
					"selector" 			=> '#review_form #respond',
					"property" 			=> 'background-color',
					"control_type" 		=> 'colorpicker',
				),
			)
		);


		//* Spacing
		$spacing = $reviews_section->addControlSection("spacing_formwrap", __("Wrapper Spacing"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"rvsp_padding",
			__("Padding"),
			"#review_form #respond"
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"rvsp_margin",
			__("Margin"),
			"#review_form #respond"
		)->whiteList();

		//* Borders
		$reviews_section->borderSection(
			__("Wrapper Borders"),
			"#review_form #respond",
			$this
		);

		$review_box_shadow = $reviews_section->addControlSection("reviews_box_shadow", __("Wrapper Shadow"), "assets/icon.png", $this);

		$review_box_shadow->addPreset(
			"box-shadow",
			"review_shadow",
			__("Original Thumbs Shadow"),
			"#review_form #respond"
		);


		//* Form Heading
		$heading = $reviews_section->typographySection(
			__("Form Heading"),
			"#reply-title",
			$this
		);

		$heading->addStyleControl(
			array(
				"selector" 		=> "#reply-title",
				"property" 		=> 'margin-bottom',
				"control_type" 	=> "slider-measurebox",
				"unit" 			=> 'px'
			)
		);

		//* Form Description
		$frm_desc = $reviews_section->typographySection(
			__("Form Description"),
			".comment-notes",
			$this
		);

		$frm_desc->addStyleControl(
			array(
				"selector" 		=> "#review_form #respond .comment-notes",
				"property" 		=> 'margin-bottom',
				"control_type" 	=> "slider-measurebox",
				"unit" 			=> 'px'
			)
		);



		//* Form Labels
		$label_tg = $reviews_section->typographySection(
			__("Form Labels"),
			"form.comment-form label",
			$this
		);

		$label_tg->addStyleControl(
			array(
				"name" 		=> __("Asterisk Color"),
				"selector" 	=> 'form.comment-form label .required, .comment-notes .required',
				"property" 	=> 'color',
			)
		);


		$stars_section = $reviews_section->addControlSection("stars_section", __("Stars Field"), "assets/icon.png", $this);

		$stars_section->addStyleControl(
			array(
				"name" 		=> __('Color'),
				"selector" 	=> '.comment-form-rating a',
				"property" 	=> 'color'
			)
		);

		$inputs_section = $reviews_section->addControlSection("inputs_section", __("Inputs Config"), "assets/icon.png", $this);

		$fld_selector = '#respond input[type=text], #respond input[type=email], #respond textarea';
		$fld_selector_focus = '#respond input[type=text]:focus, #respond input[type=email]:focus, #respond textarea:focus';

		$inputs_section->addStyleControls(
			array(
				array(
					'name' 		=> __('Textarea Height'),
					'selector' 	=> "#reviews #comment",
					"property"	=> "height",
					"control_type" => "slider-measurebox",
					"unit" 		=> "px"
				),
				array(
					'selector' 	=> $fld_selector,
					"property"	=> "font-size",
				),
				array(
					'selector' 	=> $fld_selector,
					"property"	=> "line-height",
				),
				array(
					'selector' 	=> $fld_selector,
					"property"	=> "color",
				),
				array(
					"name" 				=> __('Focused Text Color'),
					"selector" 			=> $fld_selector_focus,
					"property" 			=> 'color'
				),
				array(
					"selector" 			=> $fld_selector,
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Focused Background Color'),
					"selector" 			=> $fld_selector_focus,
					"property" 			=> 'background-color'
				),
				array(
					"selector" 			=> $fld_selector,
					"property" 			=> 'border-width'
				),
				array(
					"selector" 			=> $fld_selector,
					"property" 			=> 'border-color'
				),
				array(
					"name" 				=> __('Focused Border Color'),
					"selector" 			=> $fld_selector_focus,
					"property" 			=> 'border-color'
				),
				array(
					"selector" 			=> $fld_selector,
					"property" 			=> 'border-radius'
				)
			)
		);

		$inputs_section->addStyleControl([
			"selector" 	=> $fld_selector,
			"property" 	=> "padding-top",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_end', true);

		$inputs_section->addStyleControl([
			"selector" 	=> $fld_selector,
			"property" 	=> "padding-bottom",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_start', true);

		$inputs_section->addStyleControl([
			"selector" 	=> $fld_selector,
			"property" 	=> "padding-left",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_end', true);

		$inputs_section->addStyleControl([
			"selector" 	=> $fld_selector,
			"property" 	=> "padding-right",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_start', true);

		/*****************************
         * Button
         ***************************/
        $btn_selector = '#review_form #respond .form-submit input';

        $button_config = $reviews_section->addControlSection(
			'button_config',
			__("Button Config"),
			'assets/icon.png',
			$this
		);

		$button_config->addStyleControl(
			array(
				"selector" => $btn_selector,
				"property" => 'width',
				"control_type" => 'slider-measurebox',
				"unit" 			=> "px"
			)
		);

		$button_config->addControl(
			"buttons-list",
			"button_align",
			__('Alignment')
		)->setValue([
			'left' => __('Left'),
			'center' => __('Center'),
			'right' => __('Right'),
		])->setValueCSS([
			'left' => "#review_form #respond .form-submit{justify-content: start;}",
			'center' => "#review_form #respond .form-submit{justify-content: center;}",
			'right' => "#review_form #respond .form-submit{justify-content: flex-end;}"
		])->setDefaultValue("left");

		$button_config->addPreset(
            "padding",
            "sbtn_padding",
            __("Padding"),
            $btn_selector
        );

		$button_config->addPreset(
			"margin",
			"sbtn_margin",
			__("Margin"),
			$btn_selector
		)->whiteList();

		$btntg = $submit_section = $reviews_section->typographySection(
			__("Button Font & Color"),
			$btn_selector,
			$this
		);

		$btntg->addStyleControls(
			array(
				array(
					"selector" => $btn_selector,
					"property" => 'background-color',
					"control_type" => 'colorpicker',
				),
				array(
					"name" => __('Hover Background Color'),
					"selector" => $btn_selector . ':hover',
					"property" => 'background-color'
				),
				array(
					"name" => __('Hover Text Color'),
					"selector" => $btn_selector . ':hover',
					"property" => 'color'
				),
				array(
					"name" => __('Hover Border Color'),
					"selector" => $btn_selector . ':hover',
					"property" => 'border-color'
				)
			)
		);

		$reviews_section->borderSection(__('Button Border'), $btn_selector, $this);
		$reviews_section->boxShadowSection(__('Button Shadow'), $btn_selector, $this);


		//* Reviews
		$reviews_list = $this->addControlSection(
			'reviews_list',
			__("Reviews"),
			"assets/icon.png",
			$this
		);		

		$container = $reviews_list->addControlSection(
			'comment_box',
			__("Container"),
			"assets/icon.png",
			$this
		);

		$box_selector = '#reviews #comments ol.commentlist li .comment-text';
		$container->addStyleControls([
			array(
				'selector' => $box_selector,
				'property' => 'background-color'
			),
			array(
				'name' 		=> __('Gap'),
				'selector' 	=> '#reviews #comments ol.commentlist li',
				'property' 	=> 'margin-bottom',
				"control_type" => "slider-measurebox",
				"unit" 		=> "px"
			)
		]);

		$container->addStyleControl([
			"selector" 	=> $box_selector,
			"property" 	=> "padding-top",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_end', true);

		$container->addStyleControl([
			"selector" 	=> $box_selector,
			"property" 	=> "padding-bottom",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_start', true);

		$container->addStyleControl([
			"selector" 	=> $box_selector,
			"property" 	=> "padding-left",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_end', true);

		$container->addStyleControl([
			"selector" 	=> $box_selector,
			"property" 	=> "padding-right",
			"control_type" => "measurebox",
			"unit" 		=> "px"
		])->setParam('hide_wrapper_start', true);

		$reviews_list->borderSection(__('Container Border'), $box_selector, $this);
		$reviews_list->boxShadowSection(__('Container Shadow'), $box_selector, $this);

		$avatar = $reviews_list->addControlSection(
			'author_image',
			__("Author Image"),
			"assets/icon.png",
			$this
		);

		$avatar->addStyleControl(
			array(
				'name' 			=> __('Image Size(max-width: 60px)'),
				"selector" 		=> ' ',
				"property" 		=> '--author-avatar-size',
				"control_type" 	=> 'slider-measurebox'
			)
		)->setUnits('px', 'px');

		$avatar->addStyleControl(
			array(
				"selector" 		=> '#reviews #comments ol.commentlist li img.avatar',
				"property" 		=> 'padding',
				"control_type" 	=> 'slider-measurebox'
			)
		)->setUnits('px', 'px');

		$reviews_list->borderSection(__('Image Border'), '#reviews #comments ol.commentlist li img.avatar', $this);

		$reviews_list->typographySection(__('Author'), 'strong.woocommerce-review__author', $this);
		$reviews_list->typographySection(__('Date'), 'time.woocommerce-review__published-date', $this);
		$reviews_list->typographySection(__('Review Text'), '#reviews #comments ol.commentlist li .comment-text p', $this);

		$stars = $reviews_list->addControlSection(
			'reviews_stars',
			__("Stars"),
			"assets/icon.png",
			$this
		);

		$stars->addStyleControls([
			array(
				'name' 		=> __('Color of Empty Star'),
				'selector' 	=> '.star-rating::before',
				'property' 	=> 'color'
			),
			array(
				'name' 		=> __('Color of Full Star'),
				'selector' 	=> '.star-rating, .star-rating span',
				'property' 	=> 'color'
			),
			array(
				'name'		=> __('Adjust Vertical Alignment'),
				'selector' 	=> '.star-rating',
				'property' 	=> 'position-top',
				"control_type" 	=> 'slider-measurebox',
				"unit" 		=> "px"
			)
		]);

		//* Custom Tabs
		$custom_tabs = $this->addControlSection(
			'custom_tabs',
			__("Custom Tabs"),
			"assets/icon.png",
			$this
		);

		//* Repeater fields
		$custom_tabs->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("ACF Repeater Name", "oxy-ultimate"),
				"slug" 		=> "tab_rep"
			)
		);

		$custom_tabs->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Title Field Name", "oxy-ultimate"),
				"slug" 		=> "tab_title"
			)
		);

		$custom_tabs->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Content Field Name", "oxy-ultimate"),
				"slug" 		=> "tab_cnt"
			)
		);
    }

    function render($options, $defaults, $content) {
		global $product, $post;
		$product = wc_get_product();

		if ($product != false) {
			setup_postdata( $post );
        	$this->ouwoo_product_data_tabs_accordion_template( $options );
        }

		if ( $this->isBuilderEditorActive() ) :
			$this->ouwoo_accordion_js();
		else:
			add_action( 'wp_footer', array( $this, 'ouwoo_accordion_js' ) );
		endif;
    }

    /** 
	 * Converting the product tabs layout to accordion style
	 */
	function ouwoo_product_data_tabs_accordion_template( $options ) {
		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );
		$post_id = get_the_ID();

		$td = isset($options['toggle_speed']) ? $options['toggle_speed'] : '650';

		if ( ! empty( $product_tabs ) ) : $isExpandFirst = ! empty( $options['prdacd_expand'] ) ? $options['prdacd_expand'] : 'yes'; 

			$remove_desc_tab = isset( $options['prdacd_rem_desc'] ) ? $options['prdacd_rem_desc'] : 'no';
			$remove_info_tab = isset( $options['prdacd_rem_addinfo'] ) ? $options['prdacd_rem_addinfo'] : 'no';
			$remove_reviews_tab = isset( $options['prdacd_rem_reviews'] ) ? $options['prdacd_rem_reviews'] : 'no';

			if( $remove_desc_tab == 'yes' ) {
				unset( $product_tabs['description'] );
			}

			if( $remove_info_tab == 'yes' ) {
				unset( $product_tabs['additional_information'] );
			}

			if( $remove_reviews_tab == 'yes' ) {
				unset( $product_tabs['reviews'] );
			}

			add_filter( 'woocommerce_product_description_heading', '__return_null' );
			add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );
		?>

			<div class="woocommerce-tabs wc-tabs-wrapper" data-expand-first="<?php echo $isExpandFirst; ?>" data-acrd-speed="<?php echo $td; ?>">
				<div class="wc-prd-accordion" role="tablist">
					<?php
						foreach ( $product_tabs as $key => $tab ) :
							echo '<div class="wc-prd-accordion-item">';
								echo '<div id="acrd-'.$key.'" class="wc-prd-accordion-button" aria-selected="false" aria-expanded="false" role="tab">';
									echo '<span class="wc-prd-accordion-label">' . apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) . '</span>';
									echo '<span class="wc-prd-accordion-pm">' . apply_filters( 'ouwoo_wc_prd_ard_icon', '+' ) .'</span>';
								echo '</div>';

								if ( isset( $tab['callback'] ) ) {
									echo '<div class="wc-prd-accordion-content woocommerce-Tabs-panel--' . esc_attr( $key ) . ' clearfix" aria-selected="false" aria-hidden="true" role="tabpanel" aria-labelledby="tab-title-' . esc_attr( $key ) . '">';
									call_user_func( $tab['callback'], $key, $tab );
									echo '</div>';
								}
							echo '</div>';
						endforeach;

						if( function_exists( 'have_rows' ) && isset( $options['tab_rep'] ) && have_rows( $options['tab_rep'], $post_id ) ) :
							global $wp_embed;

							$i = 0;
							while( have_rows( $options['tab_rep'], $post_id ) ) : the_row();
								$tab_title = $tab_content = '';

								if( isset( $options['tab_title'] ) ) {
									$slug = $options['tab_rep'] . '_' . $i . '_' . $options['tab_title'];
									$tab_title = get_post_meta( $post_id, $slug, true );
								}

								if( isset( $options['tab_cnt'] ) ) {
									$slug = $options['tab_rep'] . '_' . $i . '_' . $options['tab_cnt'];
									$tab_content = get_post_meta( $post_id, $slug, true );
								}

								$i++;

								if( $tab_content ) {
									echo '<div class="wc-prd-accordion-item">';
										echo '<div id="acrd-item-' . $i .'" class="wc-prd-accordion-button" aria-selected="false" aria-expanded="false" role="tab">';
											echo '<span class="wc-prd-accordion-label">' . wp_kses_post( $tab_title ) . '</span>';
											echo '<span class="wc-prd-accordion-pm">' . apply_filters( 'ouwoo_wc_prd_ard_icon', '+' ) .'</span>';
										echo '</div>';

										echo '<div class="wc-prd-accordion-content woocommerce-Tabs-panel--' . esc_attr( $slug ) . ' clearfix" aria-selected="false" aria-hidden="true" role="tabpanel" aria-labelledby="tab-title-' . esc_attr( $slug ) . '">';
											
											echo do_shortcode( wpautop( $wp_embed->autoembed( $tab_content ) ) );

										echo '</div>';

									echo '</div>';
								}

								
							endwhile;
						endif;
					?>
				</div>

				<?php do_action( 'woocommerce_product_after_tabs' ); ?>
			</div>

		<?php

			remove_filter( 'woocommerce_product_description_heading', '__return_null' );
			remove_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

			endif;
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			return file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		} else {
			return;
		}
	}

	function enableFullPresets() {
		return true;
	}

	function ouwoo_accordion_js() {
		wp_enqueue_script(
			'ouwo-tacrd-script',
			OUWOO_URL . 'assets/js/tabtoacrd.min.js',
			array(),
			filemtime( OUWOO_DIR . 'assets/js/tabtoacrd.min.js' ),
			true
		);
	}
}

new OUWooProductTabsInAccrodion();