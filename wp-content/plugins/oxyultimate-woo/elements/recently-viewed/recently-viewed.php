<?php

class OUWooRecentlyViewedProducts extends UltimateWooEl {

	public $css_added = false;
	public $js_loaded = false;
	//public $nested_content = '';
	public $comp_options = array();

	function name() {
		return __( "Recent Viewed Products", 'oxyultimate-woo' );
	}

	function slug() {
		return "ou_recent_viewed_prds";
	}

	function ouwoo_button_place() {
		return "main";
	}

	/*function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}*/

	function controls() {
		$limit = $this->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Number', 'oxyultimate-woo'),
			'slug' 		=> 'rcv_number'
		]);

		$limit->setRange(1, 15, 1);
		$limit->setUnits(' ', ' ');
		$limit->setDefaultValue(4);
		$limit->setParam('description', 'How many products will show? Default value is 4');
		$limit->rebuildElementOnChange();

		$this->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Hide Out Of Stock Items?', "oxyultimate-woo"),
				"slug" 		=> 'rcv_hide_out_of_stock_items',
				"value" 	=> ['yes' => __("Yes"), "no" => __('No')],
				"default" 	=> 'yes'
			)
		)->rebuildElementOnChange();

		$this->grid_items();

		$this->order_items();

		$this->title();

		$this->price();

		$this->rating();

		$this->salesBadge();

		$this->atc_button();

		$this->carousel();

		$this->navigation_arrow();
	}

	function title() {
		$title = $this->addControlSection('item_title', __('Title', "oxyultimate-woo"), 'assets/icon.png', $this );
		$title->addStyleControl([
			'control_type' 	=> 'radio',
			'name' 			=> __('Show Title', 'woocommerce'),
			'selector' 		=> '.products .woocommerce-loop-product__title',
			'property' 		=> 'display',
			'value' 		=> ['block' => 'Yes', 'none' => 'No'],
			'default' 		=> 'block'
		]);

		$title->typographySection( __('Typography'), '.products .woocommerce-loop-product__title', $this);

		//* Padding & Margin
		$spacing = $title->addControlSection('title_sp', __('Spacing'), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"title_padding",
			__("Padding"),
			'.products .woocommerce-loop-product__title'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"title_margin",
			__("Margin"),
			'.products .woocommerce-loop-product__title'
		)->whiteList();
	}

	function order_items() {
		$orders = $this->addControlSection('order_sec', __('Items Order', "oxyultimate-woo"), 'assets/icon.png', $this );

		$orders->addStyleControls([
			[
				'control_type' => 'textfield',
				'name' => __('Product Image', "oxyultimate-woo"),
				'selector' => '.products .product img',
				'property' => 'order',
				'default'  => 1
			],
			[
				'control_type' => 'textfield',
				'name' => __('Product Title', "oxyultimate-woo"),
				'selector' => '.products .product .woocommerce-loop-product__title',
				'property' => 'order',
				'default'  => 2
			],
			[
				'control_type' => 'textfield',
				'name' => __('Product Rating', "oxyultimate-woo"),
				'selector' => '.products .product .star-rating',
				'property' => 'order',
				'default'  => 3
			],
			[
				'control_type' => 'textfield',
				'name' => __('Product Price', "oxyultimate-woo"),
				'selector' => '.products .product .price',
				'property' => 'order',
				'default'  => 4
			]
		]);
	}

	function grid_items() {

		$selector = '.products .product';
		
		$grid = $this->addControlSection('grid_sec', __('Columns', "oxyultimate-woo"), 'assets/icon.png', $this );

		$columns = $grid->addStyleControl([
			'control_type' 	=> 'radio',
			'name' 			=> __('Columns', 'woocommerce'),
			'selector' 		=> $selector,
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
			'selector' 		=> $selector,
			'property' 		=> 'margin-bottom'
		])->setUnits('px', 'px');

		$grid->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'height'
		]);

		$grid->addStyleControl([
			'selector' 		=> $selector . ' > div',
			'property' 		=> 'background-color'
		]);

		$grid->addStyleControl([
			'name' 			=> __('Background Hover Color', "oxyultimate-woo"),
			'selector' 		=> $selector . ':hover > div',
			'property' 		=> 'background-color'
		]);
		
		$align = $grid->addControlSection('items_align', __('Alignment', "oxyultimate-woo"), 'assets/icon.png', $this );
		$align->addStyleControls([
			[
				'selector' 	=> '.product, .woocommerce-loop-product__link, .product > div',
				'property' 	=> 'align-items',
				'default' 	=> 'stretch'
			],
			[
				'control_type' 	=> 'radio',
				'selector' 	=> '.product > div',
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
			$selector . ' > div'
		)->whiteList();

		$grid->borderSection(__('Border'), $selector . ' > div', $this );
		$grid->boxShadowSection(__('Box Shadow'), $selector . ' > div', $this );
	}

	function price() {
		$price = $this->addControlSection( 'price_section', __('Price', "woocommerce"), "assets/icon.png", $this );

		$hide_price = $price->addControl( 'buttons-list', 'hide_price', __('Hide Price') );
		$hide_price->setValue(['yes' => __('Yes'), 'no' => __('No')]);
		$hide_price->setDefaultValue('no');
		$hide_price->setValueCSS([
			'yes' => '.products .product .price{display: none;}'
		]);
		$hide_price->whiteList();

		$price->typographySection( 
			__('Strick Through Price', 'oxyultimate-woo'), 
			".price del span.woocommerce-Price-amount, .price del", 
			$this 
		);

		$price->typographySection( 
			__('Price'), 
			".price > span.woocommerce-Price-amount, .price ins span.woocommerce-Price-amount", 
			$this 
		);

		$align = $price->addControl( 'buttons-list', 'price_align', __('Alignment') );
		$align->setValue(['hr' => __('Horizontal'), 'vt' => __('Vertical')]);
		$align->setDefaultValue('vt');
		$align->setValueCSS([
			'hr' => '.products .product .price del{display: inline-block;}', 
			'vt' => '.products .product .price del{display: block;}'
		]);
		$align->whiteList();

		$price->addStyleControl([
			'control_type' 	=> 'radio',
			'name' 			=> __('Text  Align', 'woocommerce'),
			'selector' 		=> '.products .product .price',
			'property' 		=> 'text-align',
			'value' 		=> ['unset' => __('Default'), 'center' => __('Center')],
			'default' 		=> 'unset'
		]);

		$spacing = $price->addControlSection('price_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"margin",
			"price_margin",
			__("Margin"),
			'.products .product .price'
		)->whiteList();
	}

	function rating() {

		$selector = '.star-rating';

		$stars = $this->addControlSection( 'stars', __('Rating', "oxyultimate-woo"), 'assets/icon.png', $this );

		$hide_rating = $stars->addControl( 'buttons-list', 'hide_rating', __('Hide Price') );
		$hide_rating->setValue(['yes' => __('Yes'), 'no' => __('No')]);
		$hide_rating->setDefaultValue('no');
		$hide_rating->setValueCSS([
			'yes' => $selector . '{display: none;}'
		]);
		$hide_rating->whiteList();

		$stars->addStyleControls([
			[
				'name' 		=> __('Empty Stars Color', 'oxyultimate-woo'),
				'selector' 	=> $selector . '::before',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Filled Stars Color', 'oxyultimate-woo'),
				'selector' 	=> $selector . ', ' . $selector . ' span',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Size', 'oxyultimate-woo'),
				'selector' 	=> $selector,
				'property' 	=> 'font-size',
				'unit' 		=> 'em',
				'default'	=> '1'
			]
		]);

		$spacing = $stars->addControlSection('stars_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"margin",
			"stars_margin",
			__("Margin"),
			$selector
		)->whiteList();
	}

	function salesBadge() {
		$sale_badge = $this->addControlSection('sale_section', __('Sale Badge'), "assets/icon.png", $this );

		$sale_badge->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Badge Type', "oxyultimate-woo"),
			'slug' 		=> 'sale_type'
		])->setValue([
			'percent' 	=> __('Percentage', "oxyultimate-woo"),
			'text' 		=> __('Plain Text', "oxyultimate-woo"),
			'none' 		=> __('None', "oxyultimate-woo")
		])->setDefaultValue('text')->setParam('description', __('Click on Apply Params button to see the changes', "oxyultimate-woo"));

		$sale_badge->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Sale Text', "oxyultimate-woo"),
			'slug' 		=> 'sale_text',
			'default' 	=> 'Sale!',
			'condition' => 'sale_type=text'
		])->setParam('description', __('Click on Apply Params button to see the changes', "oxyultimate-woo"));

		$sale_badge->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Prefix', "oxyultimate-woo"),
			'slug' 		=> 'sale_prefix',
			'default' 	=> '-',
			'condition' => 'sale_type=percent'
		])->setParam('description', __('Click on Apply Params button to see the changes', "oxyultimate-woo"));

		$sale_badge->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Suffix', "oxyultimate-woo"),
			'slug' 		=> 'sale_suffix',
			'condition' => 'sale_type=percent'
		])->setParam('description', __('Click on Apply Params button to see the changes', "oxyultimate-woo"));

		$selector = 'span.onsale';

		$style = $sale_badge->addControlSection('badge_settings', __('Config'), "assets/icon.png", $this );
		$style->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$style->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'width'
		]);

		$style->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'height'
		]);

		$style->addStyleControl([
			'name' 			=> __('Position Top'),
			'selector' 		=> $selector,
			'property' 		=> 'top'
		])->setParam('hide_wrapper_end', true);

		$style->addStyleControl([
			'name' 			=> __('Position Left'),
			'selector' 		=> $selector,
			'property' 		=> 'left'
		])->setParam('hide_wrapper_start', true);

		$style->addStyleControl([
			'name' 			=> __('Position Right'),
			'selector' 		=> $selector,
			'property' 		=> 'right'
		]);

		//* Padding
		$spacing = $sale_badge->addControlSection('salesp_sec', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"badge_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$sale_badge->typographySection(__('Typography'), $selector, $this);
		$sale_badge->borderSection(__('Border'), $selector, $this);
		$sale_badge->boxShadowSection(__('Box Shadow'), $selector, $this);
	}

	function atc_button() {
		$selector = '.recent-viewed-product-content > a.ouwoo-rvprd-button';

		$btn = $this->addControlSection('btn_sec', __('Add To Cart', "oxyultimate-woo"), 'assets/icon.png', $this );

		$btn->addStyleControl(
			array(
				"control_type" 		=> "radio",
				"name" 				=> __('Disable Button?', "oxyultimate-woo"),
				'selector' 			=> $selector,
				'slug' 				=> 'remove_atc_btn',
				'property'  		=> 'display',
				"value" 			=> ['none' => __("Yes"), "flex" => __('No')],
				"default" 			=> 'flex'
			)
		);

		$btn->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'width',
				'condition' 		=> 'remove_atc_btn!=none'
			)
		);


		$btnText = $btn->addControlSection( "btn_text", __("Button Text", "oxy-ultimate"), "assets/icon.png", $this );

		$btnText->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Change Button Text', "oxyultimate-woo"),
				"slug" 		=> 'btn_change_text',
				"value" 	=> ['no' => __('No'), 'yes' => __('Yes')],
				"default" 	=> 'no',
				'condition' => 'remove_atc_btn!=none'
			)
		);

		$btnText->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Add To Cart Text', "oxyultimate-woo"),
				"slug" 		=> 'simple_btn_text',
				"default" 	=> 'Add to cart',
				'condition' => 'btn_change_text=yes&&remove_atc_btn!=none'
			)
		);

		$btnText->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Select Options Text', "oxyultimate-woo"),
				"slug" 		=> 'variable_btn_text',
				"default" 	=> 'Select Options',
				'condition' => 'btn_change_text=yes&&remove_atc_btn!=none'
			)
		);

		$btnText->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Read More Text', "oxyultimate-woo"),
				"slug" 		=> 'rm_btn_text',
				"default" 	=> 'Read More',
				'condition' => 'btn_change_text=yes&&remove_atc_btn!=none'
			)
		);

		$btnText->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('View Products Text', "oxyultimate-woo"),
				"slug" 		=> 'grouped_btn_text',
				"default" 	=> 'View Product',
				'condition' => 'btn_change_text=yes&&remove_atc_btn!=none'
			)
		);

		$spacing = $btn->addControlSection( "btn_sp", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
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

		$btn_clr = $btn->addControlSection( "btn_clr", __("Color & Font", "oxy-ultimate"), "assets/icon.png", $this );
		$btn_clr->addStyleControls(
			array(
				array(
					"selector" 			=> $selector,
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Background Hover Color',"oxyultimate-woo"),
					"selector" 			=> $selector . ':Hover',
					"property" 			=> 'background-color'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'color',
				),
				array(
					"name" 				=> __('Hover Color', "oxyultimate-woo"),
					"selector" 			=> $selector . ':hover',
					"property" 			=> 'color',
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'font-size'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'font-weight'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'font-family',
					'default' 			=> 'Inherit'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'line-height'
				),
				array(
					"selector" 			=> $selector,
					"property" 			=> 'text-transform'
				),
				[
					'name' 			=> __('Text Align'),
					'control_type' 	=> 'radio',
					'selector' 		=> $selector,
					'property' 		=> 'justify-content',
					'value'			=> ['flex-start' => __('Left'), 'center' => __('Center'), 'flex-end'  => __('Right')],
					'default' 		=> 'center'
				]
			)
		);

		$btn->borderSection( __("Border"), $selector, $this );
		$btn->borderSection( __("Hover Border"), $selector . ':hover', $this );
		$btn->boxShadowSection( __("Box Shadow"), $selector, $this );
		$btn->boxShadowSection( __("Hover Box Shadow"), $selector . ':hover', $this );



		/**************************
		 * View Cart Button
		 **************************/
		$viewcart_btn = $this->addControlSection( 'viewcart_btn', __('View Cart', "oxyultimate-woo"), 'assets/icon.png', $this);
		$selector = '.added_to_cart';

		$vcShow = $viewcart_btn->addControl( 'buttons-list', 'viewcart_btn_display', __('Show View Cart Button') );		
		$vcShow->setValue(['No', 'Yes']);
		$vcShow->setValueCSS(['No' => $selector . '{display: none;}']);
		$vcShow->setDefaultValue('Yes');
		$vcShow->whiteList();

		$viewcart_btn_sp = $viewcart_btn->addControlSection( 'viewcartc_btn_sp', __('Width / Spacing', "oxyultimate-woo"), 'assets/icon.png', $this);
		$viewcart_btn_sp->addStyleControl(
			array(
				'selector' 	=> $selector,
				'property' 	=> 'width',
			)
		);

		$viewcart_btn_sp->addPreset(
			"padding",
			"viewcart_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$viewcart_btn_sp->addPreset(
			"margin",
			"viewcart_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$viewcart_btn->typographySection( __('Typography'), $selector, $this );

		$viewcart_color = $viewcart_btn->addControlSection( 'viewcartc_btn_clr', __('Color', "oxyultimate-woo"), 'assets/icon.png', $this);
		$viewcart_color->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Background Hover Color', "oxyultimate-woo"),
					'selector' 	=> $selector . ':hover',
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Hover Text Color', "oxyultimate-woo"),
					'selector' 	=> $selector . ':hover',
					'property' 	=> 'color',
				)
			)
		);

		$viewcart_btn->borderSection( __('Border', "oxyultimate-woo"), $selector, $this );
		$viewcart_btn->borderSection( __('Hover Border', "oxyultimate-woo"), $selector . ":hover", $this );
		$viewcart_btn->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
		$viewcart_btn->boxShadowSection( __('Hover Box Shadow', "oxyultimate-woo"), $selector . ":hover", $this );
	}

	function carousel() {
		$carousel = $this->addControlSection('slider_sec', __('Carousel Settings', "oxyultimate-woo"), 'assets/icon.png', $this );

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
		$slideSettings = $controlObj->addControlSection('slide_settings', __('Transition & Others'), 'assets/icon.png', $this );

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
		$arrow = $this->addControlSection('arrow_style', __('Slider Arrow'), 'assets/icon.png', $this );

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

	function render( $options, $defaults, $content ) {

		$this->comp_options = $options;

		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
		$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		if ( empty( $viewed_products ) && $this->isBuilderEditorActive() ) {
			echo __('No Product Found. Please visit the single product page of some products first.', "oxyultimate-woo");
			return;
		}

		if ( empty( $viewed_products ) ) {
			return;
		}

		$limit = isset( $options['rcv_number'] ) ? intval( $options['rcv_number'] ) : 4;
		$hide_out_of_stock_items = isset( $options['rcv_hide_out_of_stock_items'] ) ? intval( $options['rcv_hide_out_of_stock_items'] ) : 'yes';

		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'post__in'       => $viewed_products,
			'orderby'        => 'post__in',
			'posts_per_page' => $limit,
			'no_found_rows'  => 1,
		);

		if ( 'yes' === $hide_out_of_stock_items ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'outofstock',
					'operator' => 'NOT IN',
				),
			); // WPCS: slow query ok.
		}

		$rv = new WP_Query( apply_filters( 'ouwoo_recently_viewed_products_query_args', $query_args ) );

		if ( $rv->have_posts() ) {

			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'ouwoo_rvprd_open_markup' ), 5 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'ouwoo_rvprd_close_markup' ), 95 );

			//add_action( 'woocommerce_shop_loop_item_title', array( $this, 'ouwoo_rvprd_cats' ), 5 );

			add_filter( 'woocommerce_sale_flash', array( $this, 'ouwoo_rvp_sales_text' ) );
			add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'ouwoo_rvp_add_to_cart_args' ) );

			/*if( $content ) {
				$this->nested_content = $content;
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'ouwo_add_nested_components' ) );
			}*/

			$remove_atc_btn = isset($options['remove_atc_btn']) ? $options['remove_atc_btn'] : 'flex';
			if( isset( $options['btn_change_text'] ) && $options['btn_change_text'] == "yes" && $remove_atc_btn == 'flex' ) {
				add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'ouwoo_rv_product_add_to_cart_text' ), 10, 2 );
			}

			//* Slider settings
			$class = '';
			$is_carousel_enabled = ( isset($options['is_carousel']) && $options['is_carousel'] == "yes" ) ? true : false;
			if( $is_carousel_enabled ) {
				$class .= ' swiper-wrapper';
				echo '<div class="swiper-container"' . $this->generateDataAttributes( $options ) . '>';
			}

			echo '<div class="products'. $class . '">';

			while ( $rv->have_posts() ) {
				$rv->the_post();

				wc_get_template( 'content-product.php', $options, '', OUWOO_DIR . 'templates/' );
			}

			echo '</div>';

			if( $is_carousel_enabled ) {
				echo '</div>';

				$this->loadArrows( $options );

				if ( $this->isBuilderEditorActive() || isset( $_GET['ct_template'] ) ) {
					$this->ouwoo_rvprd_slider_js();
					wp_print_styles('ou-swiper-style');
				} else {
					if( ! $this->js_loaded ) {
						$this->js_loaded = true;
						add_action( 'wp_footer', array( $this, 'ouwoo_rvprd_slider_js' ) );
					}
				}
			}

			remove_filter( 'woocommerce_sale_flash', array( $this, 'ouwoo_rvp_sales_text' ) );
			remove_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'ouwoo_rvp_add_to_cart_args' ) );

			/*if( $content ) {
				remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'ouwo_add_nested_components' ) );
			}*/

			if( isset( $options['btn_change_text'] ) && $options['btn_change_text'] == "yes" && $remove_atc_btn == 'flex' ) {
				remove_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'ouwoo_rv_product_add_to_cart_text' ), 10, 2 );
			}

			remove_action( 'woocommerce_before_shop_loop_item', array( $this, 'ouwoo_rvprd_open_markup'), 5 );
			remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'ouwoo_rvprd_close_markup'), 95 );
		}

		wp_reset_postdata();		
	}

	function ouwoo_rvprd_open_markup() {
		echo '<div class="recent-viewed-product-content">';
	}

	function ouwoo_rvprd_close_markup() {
		echo '</div>';
	}

	/*function ouwo_add_nested_components() {
		echo '<div class="ouwoo-rvprd-nested oxy-inner-content">';
		echo do_shortcode( $this->nested_content );
		echo '</div>';
	}*/

	function ouwoo_rvp_sales_text( $text ) {
		global $post, $product;

		$options = $this->comp_options;

		$sale_text    = ( isset( $options['sale_type'] ) && $options['sale_type'] == 'text' ) ? ( isset( $options['sale_text'] ) ? wp_kses_post($options['sale_text']) : false ) : false;
		$sale_percent = ( isset( $options['sale_type'] ) && $options['sale_type'] == 'percent' ) ? $options['sale_type'] : false;
		$final_price  = false;

		if ( $sale_percent ) {
			$prefix    = isset( $options['sale_prefix'] ) ? $options['sale_prefix'] : '';
			$suffix    = isset( $options['sale_suffix'] ) ? $options['sale_suffix'] : '';
			$final_price  = ou_get_sales_off_value( $sale_percent, $prefix, $product->get_id() );
			$final_price .= ' ' . $suffix;
		} elseif ( $sale_text ) {
			$final_price = $sale_text;
		}

		if ( ! $final_price ) {
			return $text;
		}

		return '<span class="onsale">' . esc_html( $final_price ) . '</span>';
	}

	function ouwoo_rv_product_add_to_cart_text( $text, $obj ) {

		$options = $this->comp_options;

		$simple_product_btn_txt = isset($options['simple_btn_text']) ? esc_html( $options['simple_btn_text'] ) : __('Add to cart');
		$variable_product_btn_text = isset($options['variable_btn_text']) ? esc_html( $options['variable_btn_text'] ) : $simple_product_btn_txt;
		$read_more_txt = isset($options['rm_btn_text']) ? esc_html( $options['rm_btn_text'] ) : $simple_product_btn_txt;
		$grouped_btn_text = isset($options['grouped_btn_text']) ? esc_html( $options['grouped_btn_text'] ) : $simple_product_btn_txt;

		if( $obj->get_type() == 'simple' ) {
			$text = $obj->is_purchasable() && $obj->is_in_stock() ? $simple_product_btn_txt : $read_more_txt;
		}

		if( $obj->get_type() == 'variable' ) {
			$text = $obj->is_purchasable() ? $variable_product_btn_text : $read_more_txt;
		}

		if( $obj->get_type() == 'grouped' ) {
			$text = $grouped_btn_text;
		}

		return $text;
	}

	function ouwoo_rvp_add_to_cart_args( $args ) {
		$args['class'] = $args['class'] . ' ouwoo-rvprd-button';

		return $args;
	}

	/*function ouwoo_rvprd_cats() {
		$terms = get_the_terms(get_the_ID(), 'product_cat');
		if ( !is_wp_error($terms)) {
			$cats = wp_list_pluck($terms, 'name'); 
			$cats = implode(", ", $cats);

			printf('<span class="product-cats">%s</span>', $cats);
		}
	}*/

	function ouwoo_rvprd_slider_js() {
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
		if( ! $this->css_added ) {
			$this->css_added = true;
			
			$css = ".oxy-ou-recent-viewed-prds { 
						display:block;
						position: relative;
						width: 100%;
					}
					.oxy-ou-recent-viewed-prds .products:not(.swiper-wrapper) {
						--grid-item-gap: 20px;
						display: flex;
						flex-wrap: wrap;
						margin: calc( 0px - var(--grid-item-gap) ) calc( 0px - var(--grid-item-gap) ) 0;
						position: relative;
					}
					.oxy-ou-recent-viewed-prds .product {
					    display: flex;
					    flex-direction: column;
						position: relative;
						justify-content: space-between;
					}
					.oxy-ou-recent-viewed-prds .product:not(.swiper-slide) {
					    width: 25%;
					    padding: var(--grid-item-gap);
					}
					.oxy-ou-recent-viewed-prds .product > div {
						display: flex;
						flex-direction: column;
						justify-content: space-between;
						height: 100%;
						width: 100%;
					}
					.oxy-ou-recent-viewed-prds .product img {
					    width: 100%;
					    height: auto;
					    display: block;
					    object-fit: cover;
					}
					.oxy-ou-recent-viewed-prds .woocommerce-loop-product__title {
						color: var(--standard-link);
						font-weight: 400;
						font-family: inherit;
						text-decoration: none;
						font-size: 16px;
						margin: 1em 0 0.5em;
					}
					.oxy-ou-recent-viewed-prds a > .price,
					.oxy-ou-recent-viewed-prds a > .star-rating {
						margin-bottom: 0.5em;
					}
					.oxy-ou-recent-viewed-prds a.woocommerce-loop-product__link {
						position: relative;
						width: 100%;
					}
					.oxy-ou-recent-viewed-prds div span.onsale {
						border-radius: 0px;
						font-size: 12px;
						display: flex;
						align-items: center;
					}
					.recent-viewed-product-content > a.ouwoo-rvprd-button,
					.oxy-ou-recent-viewed-prds .added_to_cart {
						width: 100%;
					}
					.oxy-ou-recent-viewed-prds .atc-product img {
						display: none;
					}

					.oxy-ou-recent-viewed-prds .products.swiper-wrapper {
						box-sizing: border-box;
					}
					.oxy-ou-recent-viewed-prds .product.swiper-slide {
					    padding: 0!important;
					    height: 100%;
					}					
					.oxy-ou-recent-viewed-prds .ou-swiper-button {
					    background: #fff;
					    border-radius: 50%;
					    color: #777;
					    width: 40px;
					    height: 40px;

					    -webkit-transition: all 0.5s;
					    -ms-transition: all 0.5s;
					    -moz-transition: all 0.5s;
					    transition: all 0.5s;
					}
					.oxy-ou-recent-viewed-prds .ou-swiper-button svg {
					    width: 20px;
					    height: 20px;
					}
					.oxy-ou-recent-viewed-prds .ou-swiper-button svg,
					.oxy-ou-recent-viewed-prds .ou-swiper-button:hover svg {
					    fill: currentColor;
					}
					.oxy-ou-recent-viewed-prds .swiper-button-next:focus,
					.oxy-ou-recent-viewed-prds .swiper-button-prev:focus,
					.oxy-ou-recent-viewed-prds .swiper-button-next:focus:after,
					.oxy-ou-recent-viewed-prds .swiper-button-prev:focus:after {
					    outline: 0;
					}
					.oxy-ou-recent-viewed-prds .swiper-button-next:after,
					.oxy-ou-recent-viewed-prds .swiper-button-prev:after {
					    display: none;
					}
					.oxy-ou-recent-viewed-prds .swiper-button-next.swiper-button-disabled,
					.oxy-ou-recent-viewed-prds .swiper-button-prev.swiper-button-disabled {
						visibility: hidden;
					}
					";
		}


		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooRecentlyViewedProducts();