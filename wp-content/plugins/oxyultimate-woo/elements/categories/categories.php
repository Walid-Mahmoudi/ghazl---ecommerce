<?php

class OUWooCategories extends UltimateWooEl {
	public $css_added = false;
	public $has_js = true;
	public $js_loaded = false;

    function name() {
		return __( "Product Categories", "oxyultimate-woo" );
    }
    
    function slug() {
		return "ou_categories";
	}

	function ouwoo_button_place() {
		return "main";
	}

    function cat_query() {
        $cat_query = $this->addControlSection('query_sec', __('Query', "oxyultimate-woo"), 'assets/icon.png', $this );

        $cat_query->addOptionControl([
            'type'      => 'textfield',
            'name'      => __('Specific Categories Only(IDs)', 'oxyultimate-woo'),
            'slug'      => 'include_ids'
		]);
		
		$cat_query->addOptionControl([
            'type'      => 'textfield',
            'name'      => __('Exclude Specific Categories(IDs)', 'oxyultimate-woo'),
            'slug'      => 'exclude_ids'
		]);

		$cat_query->addOptionControl(
			array(
                "type" => 'buttons-list',
                "name" => 'Hide Empty',
				"slug" => 'hide_empty',
				"default" => "yes"
            )
		)->setValue(array( 'yes' => __('Yes'), 'no' => __('No') ));

		$cat_query->addOptionControl(
			array(
                "type" => 'textfield',
				"name" => 'Limit',
				"slug" => 'limit'
            )
		);

		$cat_query->addOptionControl(
			array(
                "type" => 'textfield',
				"name" => 'Offset',
				"slug" => 'offset'
            )
		)->setParam('description',__( "Offset will work when you enter the limit.", "oxyultimate-woo"));

		$cat_query->addOptionControl(
			array(
                "type" => 'textfield',
                "name" => 'Parent',
                "slug" => 'parent'
            )
		);

		$cat_query->addOptionControl(
			array(
                "type" => 'buttons-list',
                "name" => 'Show Sub-Categories on Archive Page',
				"slug" => 'sub_cats',
				"default" => "no"
            )
		)->setValue(array( 'no' => __('No'), 'yes' => __('Yes') ));

		$cat_query->addOptionControl(
			array(
                "type" => 'buttons-list',
                "name" => 'Order',
				"slug" => 'order',
				"default" => "ASC"
            )
		)->setValue(array( 'ASC' => 'ASC', 'DESC' => 'DESC'));

		$cat_query->addOptionControl(
			array(
                "type" => 'dropdown',
                "name" => 'Order By',
				"slug" => 'orderby',
				'value' => array(
					'name' 			=> __('Name'), 
					'id' 			=> __('ID'), 
					'slug' 			=> __('Slug'), 
					'menu_order' 	=> __('Menu Order'), 
					'include' 		=> __('Include'),
					'count' 		=> __('Count')
				),
				"default" => "name"
            )
		);
	}
	
	function grid_items() {
		
		$grid = $this->addControlSection('grid_sec', __('Columns', "oxyultimate-woo"), 'assets/icon.png', $this );

		$columns = $grid->addStyleControl([
			'control_type' 	=> 'radio',
			'name' 			=> __('Columns', 'woocommerce'),
			'selector' 		=> '.products .product',
			'property' 		=> 'width',
			'value' 		=> ['100' => '1', '50' => '2', '33.33' => '3', '25' => '4', '20' => '5', '16.66' => '6'],
			'default' 		=> '25'
		]);
		$columns->setParam('description', __('You can change the columns in different breakpoints.', 'oxyultimate-woo'));
		$columns->setUnits('%', '%');

		$grid->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Space Between Grid Items', "oxyultimate-woo"),
			'selector' 		=> '.products',
			'property' 		=> '--grid-item-gap'
		])->setUnits('px', 'px')->setDefaultValue(20);

		$grid->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Margin Bottom', "oxyultimate-woo"),
			'selector' 		=> '.products .product',
			'property' 		=> 'margin-bottom'
		])->setUnits('px', 'px');

		$grid->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.products .product a',
			'property' 		=> 'height'
		]);
		
		$align = $grid->addControlSection('items_align', __('Alignment', "oxyultimate-woo"), 'assets/icon.png', $this );
		$align->addStyleControls([
			[
				'selector' 	=> '.product .cat-content-wrap',
				'property' 	=> 'align-items',
				'default' 	=> 'stretch'
			],
			[
				'control_type' 	=> 'radio',
				'selector' 	=> '.product .cat-content-wrap',
				'property' 	=> 'justify-content',
				'value'	=> ['flex-start', 'center', 'flex-end', 'space-between', 'space-arround','space-evenly']
			]
		]);

		//* Padding & Margin
		$spacing = $grid->addControlSection('column_sp', __('Padding'), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"col_padding",
			__("Padding"),
			'.products .product a'
		)->whiteList();

		$grid->borderSection(__('Border'), '.products .product a', $this );
		$grid->boxShadowSection(__('Box Shadow'), '.products .product a', $this );
	}

	function image() {
		
		$img = $this->addControlSection('img_sec', __('Image', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.cat-thumb-wrap';

		//* Padding & Margin
		$spacing = $img->addControlSection('img_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"img_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"img_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$img->borderSection(__('Image Border', "oxyultimate-woo"), $selector . ' img', $this );
		$img->borderSection(__('Wrapper Border', "oxyultimate-woo"), $selector, $this );
	}

	function title() {
		
		$title = $this->addControlSection('title_sec', __('Title', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.woocommerce-loop-category__title';

		$title->addOptionControl([
			"type" 	=> 'dropdown',
			"name" 	=> __('Tag'),
			"slug" 	=> 'title_tag',
			'value' => array(
				'h2' => __('H2'), 
				'h3' => __('H3'), 
				'h4' => __('H4'), 
				'h5' => __('H5'), 
				'h6' => __('H6'),
				'div' => __('DIV')
			),
			"default" => "h2"
		]);

		$title->typographySection( __('Typography'), $selector, $this );

		$color = $title->addControlSection('ttlclr_sec', __('Colors', "oxyultimate-woo"), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 	=>  ".product:hover " . $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Color on Hover', "oxyultimate-woo"),
				'selector' 	=> ".product:hover " . $selector,
				'property' 	=> 'color'
			]
		]);

		$spacing = $title->addControlSection('title_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"title_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"title_margin",
			__("Margin"),
			$selector
		)->whiteList();
	}

	function counts() {
		$counts = $this->addControlSection('counts_sec', __('Counts', "oxyultimate-woo"), 'assets/icon.png', $this );

		$counts->addOptionControl(
			array(
                "type" => 'buttons-list',
                "name" => 'Show Counts',
				"slug" => 'pad_counts',
				"default" => "no"
            )
		)->setValue(array( 'yes' => __('Yes'), 'no' => __('No') ));

		$selector = 'mark';

		$counts->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text'),
			'slug' 		=> 'label_text'
		])->setParam('description', __('Click on Apply Params button and see the text'));

		$counter = $counts->typographySection( __('Number', "oxyultimate-woo"), $selector, $this );
		$counter->addStyleControl([
			'name' 		=> __('Color on Hover', "oxyultimate-woo"),
			'selector' 	=> ".product:hover " . $selector,
			'property' 	=> 'color'
		]);

		$label = $counts->typographySection( __('Label', "oxyultimate-woo"), '.count-label', $this );

		$label->addStyleControl([
			'name' 		=> __('Color on Hover', "oxyultimate-woo"),
			'selector' 	=> ".product:hover .count-label",
			'property' 	=> 'color'
		]);

		$label->addStyleControl([
			'selector' 	=> '.count-label',
			'property' 	=> 'margin-left'
		])->setParam('hide_wrapper_end', true);

		$label->addStyleControl([
			'selector' 	=> '.count-label',
			'property' 	=> 'margin-right'
		])->setParam('hide_wrapper_start', true);

		$label->addStyleControl([
			'control_type' 	=> 'buttons-list',
			'name' 			=> __('Alignment'),
			'selector' 		=> '.items-number',
			'property' 		=> 'flex-direction'
		])->setValue(['row' => __('Right'), 'row-reverse' => __('Left')]);
	}

	function description() {
		$desc = $this->addControlSection('desc_sec', __('Description', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.product-category-description';

		$desc->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Display Description?', "oxyultimate-woo"),
			'slug' 		=> 'enable_description',
			'value' 	=> ['no' => __('No'), "yes" => __('Yes')],
			'default' 	=> 'no'
		])->rebuildElementOnChange();

		$limit = $desc->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Content Limit'),
			'slug' 		=> 'content_limit'
		]);
		$limit->setParam('description', __('Empty or 0 will show the full description.', "oxyultimate-woo"));

		$spacing = $desc->addControlSection('desc_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"desc_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"desc_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$desc->typographySection( __('Typography'), '.product-category-description, .product-category-description p', $this );

		$colors = $desc->addControlSection('desc_colors', __('Extra Colors', "oxyultimate-woo"), 'assets/icon.png', $this );
		$colors->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'background-color'
		]);

		$colors->addStyleControl([
			'name' 		=> __('Color on Hover', "oxyultimate-woo"),
			'selector' 	=> ".product:hover .product-category-description, .product:hover .product-category-description p",
			'property' 	=> 'color'
		]);
	}

	function button() {
		$button = $this->addControlSection('btn_sec', __('Button', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.cat-link';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text'),
			'slug' 		=> 'button_text'
		])->setParam('description', __('Click on Apply Params button and see the button'));

		$color = $button->addControlSection('btnclr_sec', __('Colors & Size', "oxyultimate-woo"), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> 'width',
			],
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 	=> $selector .":hover",
				'property' 	=> 'background-color'
			],
			[
				'selector' 	=> $selector,
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Color on Hover', "oxyultimate-woo"),
				'selector' 	=> $selector . ":hover",
				'property' 	=> 'color'
			],
		]);

		$color->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('s', 'sec')->setRange(0, 10, 0.1)->setDefaultValue(0.2);

		$spacing = $button->addControlSection('btn_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"btn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"btn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$button->typographySection( __('Typography'), $selector, $this );

		$icon = $button->addControlSection('btn_icon', __('Icon', "oxyultimate-woo"), "assets/icon.png", $this);

		$icon->addOptionControl([
			'type' 		=> 'icon_finder',
			'name' 		=> __('Select Icon', "oxyultimate-woo"),
			'slug' 		=> 'button_icon'
		]);

		$icon->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Size'),
			'selector' 		=> '.cat-link svg',
			'property' 		=> 'width|height'
		])->setRange(0, 100, 1)->setUnits('px', 'px')->setDefaultValue(20);

		$icon->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Gap'),
			'selector' 		=> '.cat-link svg',
			'property' 		=> 'margin-left'
		])->setRange(0, 100, 1)->setUnits('px', 'px');

		//* Border
		$button->borderSection( __('Border'), $selector, $this );
		$button->borderSection( __('Hover Border'), $selector . ":hover", $this );

		//* Box Shadow
		$button->boxShadowSection( __('Box Shadow'), $selector, $this );
		$button->boxShadowSection( __('Hover Shadow'), $selector . ":hover", $this );
	}

	function carousel() {
		$carousel = $this->addControlSection('slider_sec', __('Carousel', "oxyultimate-woo"), 'assets/icon.png', $this );

		$carousel->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable Carousel'),
			'slug' 		=> 'is_carousel',
			'value' 	=> [ 'no' => __('No'), 'yes' => __('Yes') ],
			'default' 	=> 'no'
		])->rebuildElementOnChange();

		//* Items per View
		$itemsPerView = $carousel->addControlSection('items_cols', __('Columns'), 'assets/icon.png', $this );
		$col_dsk = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxyultimate-woo"),
			'slug' 		=> 'columns'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('4');
		$col_dsk->rebuildElementOnChange();

		$bp_993 = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 993px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_993'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('3');
		$bp_993->rebuildElementOnChange();

		$bp_769 = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 769px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_769'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('3');
		$bp_769->rebuildElementOnChange();

		$bp_681 = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 681px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_681'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('2');
		$bp_681->rebuildElementOnChange();

		$bp_481 = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 481px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_481'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('1');
		$bp_481->rebuildElementOnChange();

		//* Spacing
		$sldSP = $carousel->addControlSection('sld_spacing', __('Columns Gap'), 'assets/icon.png', $this );
		$gap_dsk = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxyultimate-woo"),
			'slug' 		=> 'gap_dsk'
		])->setUnits('px','px')->setRange('5', '50', '5')->setValue('15');
		$gap_dsk->rebuildElementOnChange();

		$gap_993 = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 993px)', "oxyultimate-woo"),
			'slug' 		=> 'gap_993'
		])->setUnits('px','px')->setRange('5', '50', '5')->setValue('15');
		$gap_993->rebuildElementOnChange();

		$gap_769 = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 769px)', "oxyultimate-woo"),
			'slug' 		=> 'gap_769'
		])->setUnits('px','px')->setRange('5', '50', '5')->setValue('15');
		$gap_769->rebuildElementOnChange();

		$gap_681 = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 681px)', "oxyultimate-woo"),
			'slug' 		=> 'gap_681'
		])->setUnits('px','px')->setRange('5', '50', '5')->setValue('15');
		$gap_681->rebuildElementOnChange();

		$gap_481 = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 481px)', "oxyultimate-woo"),
			'slug' 		=> 'gap_481'
		])->setUnits('px','px')->setRange('5', '50', '5')->setValue('15');
		$gap_481->rebuildElementOnChange();

		//* Slides to Scroll
		$sldSTS = $carousel->addControlSection('sld_scroll', __('Slides to Scroll'), 'assets/icon.png', $this );
		$sts_dsk = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxyultimate-woo"),
			'slug' 		=> 'sts_dsk'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('1');
		$sts_dsk->setParam('description',__('Set numbers of slides to move at a time.', "oxyultimate-woo"));

		$sts_993 = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 993px)', "oxyultimate-woo"),
			'slug' 		=> 'sts_993'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('1');

		$sts_769 = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 769px)', "oxyultimate-woo"),
			'slug' 		=> 'sts_769'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('1');

		$sts_681 = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 681px)', "oxyultimate-woo"),
			'slug' 		=> 'sts_681'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('1');

		$sts_481 = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 481px)', "oxyultimate-woo"),
			'slug' 		=> 'sts_481'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('1');

		$this->transitionSettings( $carousel );
	}

	function transitionSettings( $controlObj ) {
		$slideSettings = $controlObj->addControlSection('slide_settings', __('Transition'), 'assets/icon.png', $this );

		$slideSettings->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Speed', "oxyultimate-woo"),
			'slug' 		=> 'transition_speed'
		])->setUnits('ms','ms')->setRange('1000', '20000', '500')->setValue('1000');

		$autoPlay = $slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Auto Play', 'oxyultimate-woo'),
			'slug' 		=> 'autoplay',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxyultimate-woo"),
				'no' 		=> __('No', "oxyultimate-woo")
			]
		]);
		$autoPlay->rebuildElementOnChange();

		$slideSettings->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Auto Play Speed', "oxyultimate-woo"),
			'slug' 		=> 'autoplay_speed',
			"condition" => 'autoplay=yes'
		])->setUnits('ms','ms')->setRange('1000', '20000', '500')->setValue('5000');

		$slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Pause on Hover', 'oxyultimate-woo'),
			'slug' 		=> 'pause_on_hover',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxyultimate-woo"),
				'no' 		=> __('No', "oxyultimate-woo")
			]
		]);

		$slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Pause on Interaction', 'oxyultimate-woo'),
			'slug' 		=> 'pause_on_interaction',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxyultimate-woo"),
				'no' 		=> __('No', "oxyultimate-woo")
			]
		]);

		$centeredSld = $slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Centered Slide', 'oxy-ultimate'),
			'slug' 		=> 'carousel_centered',
			'default' 	=> 'no',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
		$centeredSld->rebuildElementOnChange();

		$sldLoop = $slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Infinite Loop', 'oxy-ultimate'),
			'slug' 		=> 'carousel_loop',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
		$sldLoop->rebuildElementOnChange();
	}

	function navigation_arrow() {
		$arrow = $this->addControlSection('arrow_style', __('Arrow'), 'assets/icon.png', $this );

		$navArrow = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show Arrows', 'oxy-ultimate'),
			'slug' 		=> 'slider_navigation',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
		$navArrow->rebuildElementOnChange();

		$arrowOnHover = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show on Hover', 'oxy-ultimate'),
			'slug' 		=> 'slider_navapr',
			'default' 	=> 'no',
			'value' 	=> [
				'no'		=> __('No', "oxy-ultimate"),
				'onhover' 	=> __('Yes', "oxy-ultimate")
			]
		]);
		$arrowOnHover->setParam('description', "Preview is disable for builder editor.");
		$arrowOnHover->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_slider_navigation']!='no'");
		$arrowOnHover->rebuildElementOnChange();

		$mbVisibility = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Hide on Devices', 'oxy-ultimate'),
			'slug' 		=> 'slider_hidemb',
			'default' 	=> 'no',
			'value' 	=> [
				'no'		=> __('No', "oxy-ultimate"),
				'yes' 		=> __('Yes', "oxy-ultimate")
			],
			'condition' => 'slider_navigation=yes'
		]);

		$arrowBreakpoint = $arrow->addOptionControl([
			'type' 		=> 'measurebox',
			'name' 		=> __('Breakpoint'),
			'slug' 		=> 'arrow_rsp_breakpoint',
			'condition' => 'slider_hidemb=yes'
		]);
		$arrowBreakpoint->setUnits('px', 'px');
		$arrowBreakpoint->setDefaultValue(680);
		$arrowBreakpoint->setParam('description', 'Default breakpoint value is 680px.');
		$arrowBreakpoint->rebuildElementOnChange();

		$icon = $arrow->addControlSection('arrow_icon', __('Icon'), 'assets/icon.png', $this );
		$leftArrow = $icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Left Arrow', "oxy-ultimate"),
				"slug" 			=> 'arrow_left'
			)
		);
		$leftArrow->rebuildElementOnChange();

		$rightArrow= $icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Right Arrow', "oxy-ultimate"),
				"slug" 			=> 'arrow_right'
			)
		);
		$rightArrow->rebuildElementOnChange();

		$pclr = $arrow->addControlSection('arrow_pclr', __('Color & Size'), 'assets/icon.png', $this );
		
		$pclr->addStyleControl([
			'name' 			=> __('Wrapper Size', "oxyultimate-woo"),
			'selector' 		=> '.ou-swiper-button',
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 40
		]);

		$pclr->addStyleControls([
			[
				"name" 			=> __('Icon Size', "oxy-ultimate"),
				"slug" 			=> "arrow_fs",
				"selector" 		=> '.ou-swiper-button svg',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height',
				"unit" 			=> 'px'
			],
			[
				'selector' 		=> '.ou-swiper-button',
				'property' 		=> 'background-color',
				'slug' 			=> 'arrow_bgc'
			],
			[
				'name' 			=> _('Hover Background Color'),
				'selector' 		=> '.ou-swiper-button:hover',
				'property' 		=> 'background-color',
				'slug' 			=> 'arrow_bghc'
			],
			[
				'selector' 		=> '.ou-swiper-button svg',
				'property' 		=> 'color',
				'slug' 			=> 'arrow_clr'
			],
			[
				'name' 			=> _('Hover Color'),
				'selector' 		=> '.ou-swiper-button:hover svg',
				'property' 		=> 'color',
				'slug' 			=> 'arrow_hclr'
			]
		]);

		$spacing = $arrow->addControlSection('arrow_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"arrow_padding",
			__("Padding"),
			'.ou-swiper-button'
		)->whiteList();

		$arrowPos = $arrow->addControlSection('arrow_pos', __('Position'), 'assets/icon.png', $this );

		$arrowPos->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on the Apply Params button, if position value is not working properly.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		// Previous Arrow button
		$arrowPos->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7;line-height: 1.3;font-size:12px">Bottom settings are for previous arrow button.</div>'), 
			'arrow_description'
		)->setParam('heading',__('Previous Arrow'));

		$prevPosTop = $arrowPos->addStyleControl([
			'name' 		=> __('Top'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_top',
			'property' 	=> 'top'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$prevPosBottom = $arrowPos->addStyleControl([
			'name' 		=> __('Bottom'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_btm',
			'property' 	=> 'bottom'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);

		$prevPosLeft = $arrowPos->addStyleControl([
			'name' 		=> __('Left'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_left',
			'property' 	=> 'left'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$prevPosRight = $arrowPos->addStyleControl([
			'name' 		=> __('Right'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_right',
			'property' 	=> 'right'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);

		$arrowPos->addCustomControl(
			'<div class="oxygen-option-default"><hr style="color: #f4f4f4;height: 1px" noshade/></div>', 
			'divider'
		);

		// Next Arrow button
		$arrowPos->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7;line-height: 1.3;font-size:12px">Bottom settings are for next arrow button.</div>'), 
			'arrow_description'
		)->setParam('heading',__('Next Arrow'));
		
		$nextPosTop = $arrowPos->addStyleControl([
			'name' 		=> __('Top'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_top',
			'property' 	=> 'top'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$nextPosBottom = $arrowPos->addStyleControl([
			'name' 		=> __('Bottom'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_btm',
			'property' 	=> 'bottom'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);

		$nextPosLeft = $arrowPos->addStyleControl([
			'name' 		=> __('Left'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_left',
			'property' 	=> 'left'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$nextPosRight = $arrowPos->addStyleControl([
			'name' 		=> __('Right'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_right',
			'property' 	=> 'right'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);

		$arrow->borderSection(__('Border'), '.ou-swiper-button', $this );
		$arrow->borderSection(__('Hover Border'), '.ou-swiper-button:hover', $this );

		$arrow->boxShadowSection(__('Shadow'), '.ou-swiper-button', $this );
		$arrow->boxShadowSection(__('Hover Shadow'), '.ou-swiper-button:hover', $this );
	}

	function skins() {
		$skins = $this->addControlSection('skins_sec', __('Skins'), 'assets/icon.png', $this );

		$skins->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Select Skin', "oxyultimate-woo"),
			'slug' 		=> 'cat_skins',
			'value' 	=> [
				'none' 		=> __('None', 'oxyultimate-woo'),
				'skin-1' 	=> __('Skin 1', 'oxyultimate-woo'),
				'skin-2' 	=> __('Skin 2', 'oxyultimate-woo'),
				'skin-3' 	=> __('Skin 3', 'oxyultimate-woo'),
				'skin-4' 	=> __('Skin 4', 'oxyultimate-woo')
			]
		])->rebuildElementOnChange();

		/***************************
		 * Settings for Skin 1
		 ***************************/
		$skins->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-1 .cat-content-wrap',
			'property' 		=> 'width',
			'condition' 	=> 'cat_skins=skin-1'
		])->setUnits('%', '%,px');

		$skins->addStyleControl([
			'selector' 		=> '.skin-1 .cat-content-wrap, .skin-2 .cat-content-wrap',
			'property' 		=> 'background-color',
			'condition' 	=> 'cat_skins=skin-1||cat_skins=skin-2'
		]);

		$skins->addStyleControl([
			'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
			'selector' 		=> '.skin-1 .product:hover .cat-content-wrap',
			'property' 		=> 'background-color',
			'condition' 	=> 'cat_skins=skin-1'
		]);

		$skins->addStyleControl([
			'name' 			=> __('Position Left', "oxyultimate-woo"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-1 .cat-content-wrap',
			'property' 		=> 'left',
			'condition' 	=> 'cat_skins=skin-1'
		])->setUnits('px', '%,px')->setDefaultValue(40);

		$skins->addStyleControl([
			'name' 			=> __('Position Bottom', "oxyultimate-woo"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-1 .cat-content-wrap',
			'property' 		=> 'bottom',
			'condition' 	=> 'cat_skins=skin-1'
		])->setUnits('px', '%,px')->setDefaultValue(40);
		

		/***************************
		 * Settings for Skin 2
		 ***************************/
		$skins->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-2 .cat-content-wrap,.skin-2.cat-thumb-wrap > img,.skin-2 .cat-content-wrap > *',
			'property' 		=> 'transition-duration',
			'condition' 	=> 'cat_skins=skin-2'
		])->setRange(0, 3, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.6);

		
		/***************************
		 * Settings for Skin 3
		 ***************************/
		$skins->addStyleControl([
			'name' 			=> __('Overlay Color', "oxyultimate-woo"),
			'selector' 		=> '.skin-3 .product a:before',
			'property' 		=> 'background-color',
			'condition' 	=> 'cat_skins=skin-3'
		]);

		$skins->addStyleControl([
			'name' 			=> __('Transition Duration of Title', "oxyultimate-woo"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-3 .cat-content-wrap .woocommerce-loop-category__title',
			'property' 		=> 'transition-duration',
			'condition' 	=> 'cat_skins=skin-3'
		])->setRange(0, 3, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.6);

		$skins->addStyleControl([
			'name' 			=> __('Transition Duration of Number', "oxyultimate-woo"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-3 .cat-content-wrap .items-number',
			'property' 		=> 'transition-duration',
			'condition' 	=> 'cat_skins=skin-3'
		])->setRange(0, 3, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.6);

		$skins->addStyleControl([
			'name' 			=> __('Transition Duration of Description', "oxyultimate-woo"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-3 .cat-content-wrap .product-category-description',
			'property' 		=> 'transition-duration',
			'condition' 	=> 'cat_skins=skin-3'
		])->setRange(0, 3, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.6);

		$skins->addStyleControl([
			'name' 			=> __('Transition Duration of Button', "oxyultimate-woo"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.skin-3 .cat-content-wrap .cat-link',
			'property' 		=> 'transition-duration',
			'condition' 	=> 'cat_skins=skin-3'
		])->setRange(0, 3, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.6);


		/***************************
		 * Settings for Skin 4
		 ***************************/
		$skins->addStyleControl([
			'name' 			=> __('Overlay Color', "oxyultimate-woo"),
			'selector' 		=> '.skin-4 .product > a:before',
			'property' 		=> 'background-color',
			'condition' 	=> 'cat_skins=skin-4'
		]);

		$skins->addStyleControl([
			'selector' 		=> '.skin-4 .cat-content-wrap:before, .skin-4 .cat-content-wrap:after',
			'property' 		=> 'border-color',
			'condition' 	=> 'cat_skins=skin-4'
		]);

		$skins->addStyleControl([
			'selector' 		=> '.skin-4 .product, .skin-4 .product',
			'property' 		=> '--skin4-border-width',
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px',
			'default' 		=> 2,
			'condition' 	=> 'cat_skins=skin-4'
		]);
	}

    function controls() {

		$this->cat_query();
		
		$this->grid_items();

		$this->image();

		$this->title();

		$this->counts();

		$this->description();

		$this->button();

		$this->carousel();

		$this->navigation_arrow();

		$this->skins();
	}
	
	function ouwoo_cat_thumb_wrap_open( $category, $options ) {
		echo '<div class="cat-thumb-wrap">';
	}
	
	function ouwoo_cat_thumb_wrap_close( $category, $options ) {
		echo '</div>';
	}

	function ouwoo_loop_category_content_wrap_open( $category, $options ) {
		echo '<div class="cat-content-wrap">';
	}

	function ouwoo_loop_category_content_wrap_close( $category, $options ) {
		echo '</div>';
	}

	function ouwoo_loop_category_title( $category, $options ) {
		$tag = isset($options['title_tag']) ? $options['title_tag'] : 'h2';
	?>
		<<?php echo $tag; ?> class="woocommerce-loop-category__title" itemprop="heading">
			<?php echo esc_html( $category->name ); ?>
		</<?php echo $tag; ?>>
	<?php
		$show_counts = isset( $options['pad_counts'] ) ? $options['pad_counts'] : "no";
		if ( $category->count > 0 && $show_counts == "yes" ) {
			$text = '';

			if( isset( $options['label_text'] ) )
				$text = ' <span class="count-label">' . wp_kses_post( $options['label_text'] ) . '</span>';

			echo apply_filters( 'woocommerce_subcategory_count_html', '<span class="items-number"><mark class="count">' . esc_html( $category->count ) . '</mark>' . $text . '</span>', $category );
		}
	}

	function ouwoo_loop_category_description( $category, $options ) {
		if( ! $category->description )
			return;
		
		if( ! isset( $options['content_limit'] ) || $options['content_limit'] == 0 || $options['content_limit'] == '' ) {
			$desc = wc_format_content( $category->description );
		} else {
			$this->content_limit = absint( $options['content_limit'] );
			$desc = wpautop( wc_trim_string( $category->description, absint( $options['content_limit'] ) ) );
		}

		echo '<div class="product-category-description" itemprop="text">' . $desc . '</div>';
	}
	
	function ouwoo_loop_category_link_button( $category, $options ) {
		echo '<div class="cat-link" role="button">';
		echo wp_kses_post( $options['button_text'] );

		
		if( isset($options['button_icon']) ) {
			global $oxygen_svg_icons_to_load;

			$oxygen_svg_icons_to_load[] = $options['button_icon'];
			echo '<svg id="' . $options['selector'] . '-btn-icon link-btn-icon"><use xlink:href="#' . $options['button_icon'] . '"></use></svg>';
		}

		echo '</div>';
	}

	private function getAtts( $options ) {
		$atts = array();

		if( isset( $options['include_ids'] ) ) {
			$atts['include'] = array_filter( array_map( 'trim', explode( ',', $options['include_ids'] ) ) );
		}

		if( isset( $options['exclude_ids'] ) ) {
			$atts['exclude'] = array_filter( array_map( 'trim', explode( ',', $options['exclude_ids'] ) ) );
		}

		if( isset( $options['parent'] ) ) {
			$atts['parent'] = absint( $options['parent'] );
		}

		if ( is_tax() && isset($options['sub_cats']) && $options['sub_cats'] == "yes" ) {
			$term 		= get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$atts['parent'] = absint( $term->term_id );
		}

		if( isset( $options['limit'] ) ) {
			$atts['number'] = absint( $options['limit'] );
		}

		if( isset( $options['offset'] ) ) {
			$atts['offset'] = absint( $options['offset'] );
		}

		$atts['hide_empty'] = ( isset( $options['hide_empty'] ) && $options['hide_empty'] == "yes" ) ? true : false;
		$atts['orderby'] = isset( $options['orderby'] ) ? $options['orderby'] : "name";
		$atts['order'] = isset( $options['order'] ) ? $options['order'] : "ASC";

		return $atts;
	}

    function render( $options, $defaults, $content ) {
		
		add_action( 'woocommerce_before_subcategory_title', array( $this, 'ouwoo_cat_thumb_wrap_open' ), 9, 2 );
		add_action( 'woocommerce_before_subcategory_title', array( $this, 'ouwoo_cat_thumb_wrap_close' ), 12, 2 );

		add_action( 'woocommerce_before_subcategory_title', array( $this, 'ouwoo_loop_category_content_wrap_open' ), 20, 2 );
		add_action( 'woocommerce_after_subcategory_title', array( $this, 'ouwoo_loop_category_content_wrap_close' ), 10, 2 );

		remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title' );
		add_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'ouwoo_loop_category_title' ), 10, 2 );

		if( isset( $options['enable_description'] ) && $options['enable_description'] == "yes") {
			add_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'ouwoo_loop_category_description' ), 15, 2 );
		}

		if( isset( $options['button_text'] ) ) {
			add_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'ouwoo_loop_category_link_button' ), 20, 2 );
		}		

		$product_categories = apply_filters(
			'woocommerce_product_categories',
			get_terms( 'product_cat', apply_filters( 'ouwoo_categories_args', $this->getAtts( $options ), $options ) ),
			$options
		);

		if ( $product_categories ) {

			$class = '';
			$is_carousel_enabled = ( isset($options['is_carousel']) && $options['is_carousel'] == "yes" ) ? true : false;
			if( $is_carousel_enabled ) {
				$class .= ' swiper-wrapper';
				echo '<div class="swiper-container"' . $this->generateDataAttributes( $options ) . '>';
			}

			if( isset( $options['cat_skins'] ) ) {
				$class .= ' skins ' . $options['cat_skins'];
			}

			echo '<div class="products'. $class . '">';

			foreach ( $product_categories as $category ) {
				$file = OUWOO_DIR . 'templates/content-product_cat.php';
				include apply_filters( 'ouwoo_template', $file, 'ouwoo-product-cat-tpl' );
			}

			echo '</div>';

			if( $is_carousel_enabled ) {
				echo '</div>';

				$this->loadArrows( $options );

				if ( $this->isBuilderEditorActive() || isset( $_GET['ct_template'] ) ) {
					$this->ouwoo_category_slider_js();
					wp_print_styles('ou-swiper-style');
				} else {
					if( ! $this->js_loaded ) {
						$this->js_loaded = true;
						add_action( 'wp_footer', array( $this, 'ouwoo_category_slider_js' ) );
					}
				}
			}
		}

		remove_action( 'woocommerce_before_subcategory_title', array( $this, 'ouwoo_cat_thumb_wrap_open' ), 9, 2 );
		remove_action( 'woocommerce_before_subcategory_title', array( $this, 'ouwoo_cat_thumb_wrap_close' ), 12, 2 );
		remove_action( 'woocommerce_before_subcategory_title', array( $this, 'ouwoo_loop_category_content_wrap_open' ), 20, 2 );
		remove_action( 'woocommerce_after_subcategory_title', array( $this, 'ouwoo_loop_category_content_wrap_close' ), 10, 2 );
		remove_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'ouwoo_loop_category_title' ), 10, 2 );

		if( isset( $options['enable_description'] ) && $options['enable_description'] == "yes") {
			remove_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'ouwoo_loop_category_description' ), 15, 2 );
		}

		if( isset( $options['button_text'] ) ) {
			remove_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'ouwoo_loop_category_link_button' ), 20, 2 );
		}
	}

	function ouwoo_category_slider_js() {
		global $ouwoo_constant;
		
		if( ! $ouwoo_constant['swiper_css'] ) {
			wp_enqueue_style(
				'ou-swiper-style', 
				OUWOO_URL . 'assets/css/swiper.min.css', 
				array(), 
				filemtime( OUWOO_DIR . 'assets/css/swiper.min.css' ), 
				'all' 
			);
			$ouwoo_constant['swiper_css'] = true;
		}

		ouwoo_enqueue_common_scripts();
	}
	
	function customCSS( $original, $selector ) {
		$css = '';
		$prefix = $this->El->get_tag();

		if( ! $this->css_added ) {
			$css = file_get_contents( __DIR__.'/'.basename(__FILE__, '.php').'.css' );
			$this->css_added = true;
		}

		if( isset($original[$prefix . '_slider_navigation']) && $original[$prefix . '_slider_navigation'] == 'yes' ) {
			$prevPos = $nextPos = '';
			if( isset($original[$prefix . '_prevbtn_top-unit']) && $original[$prefix . '_prevbtn_top-unit'] == 'auto' )
			{
				$prevPos .= 'top: auto;';
			}
			if( isset($original[$prefix . '_prevbtn_btm-unit']) && $original[$prefix . '_prevbtn_btm-unit'] == 'auto' )
			{
				$prevPos .= 'bottom: auto;';
			}
			if( isset($original[$prefix . '_prevbtn_left-unit']) && $original[$prefix . '_prevbtn_left-unit'] == 'auto' )
			{
				$prevPos .= 'left: auto;';
			}
			if( isset($original[$prefix . '_prevbtn_right-unit']) && $original[$prefix . '_prevbtn_right-unit'] == 'auto' )
			{
				$prevPos .= 'right: auto;';
			}

			if( isset($original[$prefix . '_nextbtn_top-unit']) && $original[$prefix . '_nextbtn_top-unit'] == 'auto' )
			{
				$nextPos .= 'top: auto;';
			}
			if( isset($original[$prefix . '_nextbtn_btm-unit']) && $original[$prefix . '_nextbtn_btm-unit'] == 'auto' )
			{
				$nextPos .= 'bottom: auto;';
			}
			if( isset($original[$prefix . '_nextbtn_left-unit']) && $original[$prefix . '_nextbtn_left-unit'] == 'auto' )
			{
				$nextPos .= 'left: auto;';
			}
			if( isset($original[$prefix . '_nextbtn_right-unit']) && $original[$prefix . '_nextbtn_right-unit'] == 'auto' )
			{
				$nextPos .= 'right: auto;';
			}
			$css .= $selector . ' .ou-swiper-button-prev{'. $prevPos .'}';
			$css .= $selector . ' .ou-swiper-button-next{'. $nextPos .'}';
		}

		if( isset($original[$prefix . '_slider_hidemb']) && $original[$prefix . '_slider_hidemb'] == 'yes' ) {		
			$arrowBP = isset($original[$prefix . '_arrow_rsp_breakpoint']) ? $original[$prefix . '_arrow_rsp_breakpoint'] : 680;
			$css .= '@media only screen and (max-width: '. absint($arrowBP) .'px){' . $selector . ' .ou-swiper-button{display:none;}}';
		}

		if( isset($original[$prefix . '_slider_navapr']) && $original[$prefix . '_slider_navapr'] == 'onhover' )
		{
			$css .= 'body:not(.oxygen-builder-body) ' . $selector . ':not(:hover) .ou-swiper-button{opacity: 0;}
					body:not(.oxygen-builder-body) ' . $selector . ':not(:hover) .ou-swiper-button-prev{left: -1em;}
					body:not(.oxygen-builder-body) ' . $selector . ':not(:hover) .ou-swiper-button-next{right: -1em;}';
			$css .= 'body:not(.oxygen-builder-body) ' . $selector . ':hover .ou-swiper-button {opacity: 1;}';
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooCategories();