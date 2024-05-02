<?php

class OUWooAddToCart extends UltimateWooEl {
	public $btn_text, $loop_btn_text, $variable_btn_text, $read_more_txt, $grouped_btn_text, $btn_icon, $btn_icon_pos;
	public $css_added = false;
	public $js_added = false;
	public $ajax_js_added = false;
	public $qty_added = false;

	function name() {
		return __( "Add To Cart", 'oxyultimate-woo' );
	}

	function slug() {
		return "ou_addtocart";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function controls() {

		$this->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Will Use In Loop?', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_shoploop',
				"value" 	=> ['yes' => __("Yes"), "no" => __('No')],
				"default" 	=> 'no'
			)
		)->rebuildElementOnChange();

		$productIDFld = $this->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Product ID', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_product',
				'condition' => 'ouatc_shoploop=no'
			)
		);
		$productIDFld->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouwooATCProductID">data</div>');
		$productIDFld->rebuildElementOnChange();

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable AJAX add to cart function', "oxyultimate-woo"),
			'slug' 		=> 'ouatc_ajax_single',
			'value' 	=> [ 'no' => __('No'), 'yes' => __('Yes') ],
			'default' 	=> 'no',
			'condition' => 'ouatc_shoploop=no'
		]);

		$ouatc_redirect_url = $this->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_addtocart_ouatc_redirect_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_addtocart_ouatc_redirect_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_addtocart_ouatc_redirect_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_addtocart_ouatc_redirect_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_addtocart_ouatc_redirect_url\')">set</div>
			</div>
			',
			"ouatc_redirect_url"
		);
		$ouatc_redirect_url->setParam( 'heading', __('Redirect URL', "oxyultimate-woo") );
		$ouatc_redirect_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_addtocart_ouatc_ajax_single']=='yes'" );


		$this->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Turn On Quantity Field', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_qty',
				"value" 	=> ['yes' => __("Yes"), "hide" => __('No')],
				"default" 	=> 'yes'
			)
		)->rebuildElementOnChange();


		/*************************
		 * Price
		 ************************/
		$price = $this->addControlSection( 'price_section', __('Price', "woocommerce"), "assets/icon.png", $this );
		$price->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Show Price', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_price',
				"value" 	=> ['yes' => __("Yes"), "hide" => __('No')],
				"default" 	=> 'yes',
				'condition' => 'ouatc_shoploop=no'
			)
		)->rebuildElementOnChange();

		$price->typographySection( 
			__('Strick Through Price'), 
			".price del span.woocommerce-Price-amount, .price del", 
			$this 
		);

		$price->typographySection( 
			__('Sale Price', 'oxyultimate-woo'), 
			".price > span.woocommerce-Price-amount, .price ins span.woocommerce-Price-amount", 
			$this 
		);



		/**************************
		 * Quantity -/+ Buttons
		 **************************/
		$qtybtns = $this->addControlSection( 'qty_buttons', __('-/+ Buttons', "oxyultimate-woo"), 'assets/icon.png', $this);

		$btn_selector = '.ouatc-qty-chng';

		$qtybtns->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Add -/+ Buttons', "oxyultimate-woo"),
				"slug" 		=> 'enable_qtybtns',
				"value" 	=> ['no' => __('No'), 'yes' => __('Yes')],
				"default" 	=> 'no'
			)
		)->rebuildElementOnChange();

		$qtybtns->addOptionControl(
			array(
				"type" 		=> "dropdown",
				"name" 		=> __('Buttons Placement', "oxyultimate-woo"),
				"slug" 		=> 'qtybtns_pos',
				"value" 	=> ['lr' => __('Left and Right Side', 'oxyultimate-woo'), 'right' => __('Only Right Side', 'oxyultimate-woo')],
				"default" 	=> 'lr'
			)
		)->rebuildElementOnChange();

		$qtybtns->addStyleControl(
			array(
				"selector"  => ".have-qty-buttons.qtybtns-pos-right .quantity",
				"name" 		=> __('Outer Wrapper Width', "oxyultimate-woo"),
				"property" 	=> 'width',
				"default" 	=> 80,
				'condition' => 'qtybtns_pos=right'
			)
		);

		$size = $qtybtns->addControlSection( 'qtybtns_size', __('Width & Size', "oxyultimate-woo"), 'assets/icon.png', $this);
		$size->addStyleControls(
			array(
				array(
					'name' 		=> __('Width'),
					'selector' 	=> $btn_selector,
					'property' 	=> 'width|--qty-minus-offset',
					'control_type' => 'slider-measurebox',
					'unit' 		=> 'px',
					'description' => __('Adjust the height from Qty Text Field section', "oxyultimate-woo")
				),
				array(
					'name' 		=> __('Size', "oxyultimate-woo"),
					'selector' 	=> $btn_selector,
					'property' 	=> 'font-size',
				),
				array(
					'name' 		=> __('Weight', "oxyultimate-woo"),
					'selector' 	=> $btn_selector,
					'property' 	=> 'font-weight',
				)
			)
		);

		$clrs = $qtybtns->addControlSection( 'qtybtns_clrs', __('Colors', "oxyultimate-woo"), 'assets/icon.png', $this);
		$clrs->addStyleControls(
			array(
				array(
					'selector' 	=> $btn_selector,
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Background Color on Hover', "oxyultimate-woo"),
					'selector' 	=> $btn_selector . ":hover",
					'property' 	=> 'background-color',
				),
				array(
					'selector' 	=> $btn_selector,
					'property' 	=> 'color',
				),
				array(
					'name' 		=> __('Color on Hover', "oxyultimate-woo"),
					'selector' 	=> $btn_selector . ":hover",
					'property' 	=> 'color',
				)
			)
		);

		$qtybtn_sp = $qtybtns->addControlSection( 'qtybtn_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this);
		$qtybtn_sp->addPreset(
			"margin",
			"qtybtn_margin",
			__("Margin"),
			$btn_selector
		)->whiteList();

		$qtybtns->borderSection( __('Border'), $btn_selector, $this );
		$qtybtns->borderSection( __('Hover Border'), $btn_selector . ":hover", $this );

		$qtybtns->boxShadowSection( __('Shadow', "oxyultimate-woo"), $btn_selector, $this );
		$qtybtns->boxShadowSection( __('Hover Shadow', "oxyultimate-woo"), $btn_selector . ":hover", $this );


		
		/**************************
		 * Quantity Field
		 **************************/
		$qty = $this->addControlSection( 'qty_section', __('Qty Input Field', "oxyultimate-woo"), 'assets/icon.png', $this);

		$selector = '.quantity input';
		
		$qty->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Position', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_qtypos',
				"value" 	=> ['left' => __("Default", "oxyultimate-woo"), "above" => __('Above The Button', "oxyultimate-woo")],
				"default" 	=> 'left'
			)
		)->rebuildElementOnChange();

		$qty_sp = $qty->addControlSection( 'qty_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this);
		$qty_sp->addPreset(
			"padding",
			"qty_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$qty_sp->addPreset(
			"margin",
			"qty_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$qtyClr = $qty->addControlSection( 'qtyclr_section', __('Color & Width', "oxyultimate-woo"), 'assets/icon.png', $this);
		$qtyClr->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'width',
					'control_type' => 'slider-measurebox',
					'unit' 		=> 'px'
				),
				array(
					'selector' 	=> $selector . ', .ouatc-qty-chng',
					'property' 	=> 'height',
					'control_type' => 'slider-measurebox',
					'unit' 		=> 'px'
				),
				array(
					'selector' 	=> $selector,
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Focus Background Color'),
					'selector' 	=> $selector.':focus',
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Focus Text Color'),
					'selector' 	=> $selector.':focus',
					'property' 	=> 'color',
				)
			)
		);

		$qtybrd = $qty->addControlSection( 'qtybrd_section', __('Border'), 'assets/icon.png', $this);
		$qtybrd->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'border-color',
				),
				array(
					'selector' 	=> $selector,
					'property' 	=> 'border-width',
				),
				array(
					'name' 		=> __('Focus Border Color'),
					'selector' 	=> $selector .':focus',
					'property' 	=> 'border-color',
				),
				array(
					'selector' 	=> $selector,
					'property' 	=> 'border-radius',
				)
			)
		);

		$qty->typographySection( __('Typography'), $selector, $this );



		/**************************
		 * Add To Cart Button
		 **************************/
		$addtc_btn = $this->addControlSection( 'addtc_btn', __('Add To Cart Button', "oxyultimate-woo"), 'assets/icon.png', $this);

		$addtc_btn->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Add To Cart Text', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_btn_text',
				"default" 	=> 'Add to cart',
				'condition' => 'ouatc_shoploop=no'
			)
		)->setParam('description', __('Click on Apply Params button and apply the change. Use &nbsp; for empty text.', "oxyultimate-woo"));

		$addtc_btn->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Change Button Text', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_change_text',
				"value" 	=> ['no' => __('No'), 'yes' => __('Yes')],
				"default" 	=> 'no',
				'condition' => 'ouatc_shoploop=yes'
			)
		);

		$addtc_btn->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Add To Cart Text', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_simple_btn_text',
				"default" 	=> 'Add to cart',
				'condition' => 'ouatc_shoploop=yes&&ouatc_change_text=yes'
			)
		)->setParam('description', __('Click on Apply Params button and apply the change. Use &nbsp; for empty text.', "oxyultimate-woo"));

		$addtc_btn->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Select Options Text', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_variable_btn_text',
				"default" 	=> 'Select Options',
				'condition' => 'ouatc_shoploop=yes&&ouatc_change_text=yes'
			)
		);

		$addtc_btn->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Read More Text', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_rm_btn_text',
				"default" 	=> 'Read More',
				'condition' => 'ouatc_shoploop=yes&&ouatc_change_text=yes'
			)
		);

		$addtc_btn->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('View Products Text', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_grouped_btn_text',
				"default" 	=> 'View Product',
				'condition' => 'ouatc_shoploop=yes&&ouatc_change_text=yes'
			)
		);

		$selector = '.add_to_cart_button, .single_add_to_cart_button, .product-type-grouped a.button, .product-type-simple a.button, .button.product_type_external';
		$selectorHover = '.add_to_cart_button:hover, .single_add_to_cart_button:hover, .product-type-simple a.button:hover, .product-type-grouped a.button:hover, .button.product_type_external:hover';

		$icon = $addtc_btn->addControlSection( 'atc_btn_icon', __('Icon', "oxyultimate-woo"), 'assets/icon.png', $this);
		$icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon for Add To Cart Button', "oxy-ultimate"),
				"slug" 			=> 'btn_icon',
				'css' 			=> false,
				//'condition' 	=> 'ouatc_shoploop=yes'
			)
		)->setParam('description', __('Click on Apply Params button and apply the changes. It will work when loop option will be enabled.', "oxyultimate-woo"));

		$icon->addOptionControl(
			array(
				"type" 			=> 'radio',
				"name" 			=> __('Position', "oxy-ultimate"),
				"slug" 			=> 'btn_icon_pos',
				'value' 		=> ['left' => __('Left'), 'right' => __('Right')],
				'default' 		=> 'right',
				//'condition' 	=> 'ouatc_shoploop=yes'
			)
		)->rebuildElementOnChange();

		$icon->addStyleControl(
			array(
				"name" 			=> __('Size', "oxy-ultimate"),
				"selector" 		=> 'svg.atc-btn-icon',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> 16,
				"property" 		=> 'width|height',
				//'condition' 	=> 'ouatc_shoploop=yes'
			)
		)
		->setRange(10, 50, 1)
		->setUnits("px", "px");

		$icon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"selector" 		=> 'svg.atc-btn-icon',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'margin-left',
				'condition' 	=> 'btn_icon_pos=right', //ouatc_shoploop=yes&

			)
		)
		->setRange(0, 50, 1)
		->setUnits("px", "px,%,em");

		$icon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"selector" 		=> 'svg.atc-btn-icon',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'margin-right',
				'condition' 	=> 'btn_icon_pos=left' //ouatc_shoploop=yes&
			)
		)
		->setRange(0, 50, 1)
		->setUnits("px", "px,%,em");

		$icon->addStyleControls([
			[
				'name' 		=> __('Color', "oxyultimate-woo"),
				'selector' 	=> 'svg.atc-btn-icon',
				'property' 	=> 'color',
				'condition' => 'ouatc_shoploop=yes'
			],
			[
				'name' 		=> __('Hover Color', "oxyultimate-woo"),
				'selector' 	=> '.add_to_cart_button:hover svg.atc-btn-icon, .single_add_to_cart_button:hover svg.atc-btn-icon',
				'property' 	=> 'color',
				//'condition' => 'ouatc_shoploop=yes'
			]
		]);

		$addtc_btn_sp = $addtc_btn->addControlSection( 'addtc_btn_sp', __('Width & Spacing', "oxyultimate-woo"), 'assets/icon.png', $this);

		$addtc_btn_sp->addStyleControl(
			array(
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
			)
		)->setRange(0, 1000, 10)->setUnits('px', 'px,em,%,vw');

		$addtc_btn_sp->addPreset(
			"padding",
			"addtc_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$addtc_btn_sp->addPreset(
			"margin",
			"addtc_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$addtc_btn_clr = $addtc_btn->addControlSection( 'addtc_btn_clr', __('Color', "oxyultimate-woo"), 'assets/icon.png', $this);
		$addtc_btn_clr->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Background Hover Color', "oxyultimate-woo"),
					'selector' 	=> '.add_to_cart_button:hover,.single_add_to_cart_button:hover',
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Hover Text Color', "oxyultimate-woo"),
					'selector' 	=> '.add_to_cart_button:hover,.single_add_to_cart_button:hover',
					'property' 	=> 'color',
				)
			)
		);

		//* Typography
		$addtc_btn->typographySection( __('Fonts', "oxyultimate-woo"), $selector, $this );

		//* Border
		$addtc_btn->borderSection( __('Borders', "oxyultimate-woo"), $selector, $this );
		$addtc_btn->borderSection( __('Hover Borders', "oxyultimate-woo"), $selectorHover, $this );
		
		//* Box Shadow
		$addtc_btn->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
		$addtc_btn->boxShadowSection( __('Hover Box Shadow', "oxyultimate-woo"), $selectorHover, $this );


		
		/**************************
		 * View Cart Button
		 **************************/
		$viewcart_btn = $this->addControlSection( 'viewcart_btn', __('View Cart Button', "oxyultimate-woo"), 'assets/icon.png', $this);
		$selector = '.added_to_cart';

		$vcShow = $viewcart_btn->addControl( 'buttons-list', 'viewcart_btn_display', __('Show View Cart Button') );		
		$vcShow->setValue(['No', 'Yes']);
		$vcShow->setValueCSS(['No' => $selector . '{display: none;}']);
		$vcShow->setDefaultValue('No');
		$vcShow->whiteList();

		$viewcart_btn_sp = $viewcart_btn->addControlSection( 'viewcartc_btn_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this);
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

		$viewcart_btn_sp->addStyleControl(
			array(
				'selector' 	=> $selector,
				'property' 	=> 'width',
			)
		);

		$viewcart_btn->addStyleControls(
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
		$viewcart_btn->typographySection( __('Fonts', "oxyultimate-woo"), $selector, $this );
		$viewcart_btn->borderSection( __('Borders', "oxyultimate-woo"), $selector, $this );
		$viewcart_btn->borderSection( __('Hover Borders', "oxyultimate-woo"), $selector . ":hover", $this );
		$viewcart_btn->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
		$viewcart_btn->boxShadowSection( __('Hover Box Shadow', "oxyultimate-woo"), $selector . ":hover", $this );


		
		//* Variations
		$variations = $this->addControlSection('variation_section', __('Variations', "woocommerce"), "assets/icon.png", $this );
		$variations->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Show Variation Price (bottom)', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_vprice',
				"value" 	=> ['yes' => __("Yes"), "hide" => __('No')],
				"default" 	=> 'yes',
				'condition' => 'ouatc_shoploop=no'
			)
		)->rebuildElementOnChange();

		$variations->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Show Variation Description', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_vdesc',
				"value" 	=> ['yes' => __("Yes"), "hide" => __('No')],
				"default" 	=> 'yes',
				'condition' => 'ouatc_shoploop=no'
			)
		)->rebuildElementOnChange();

		$variations->typographySection( __('Label', 'oxyultimate-woo'), ".label, .label label", $this );
		$variations->typographySection( __('Price', 'oxyultimate-woo'), ".woocommerce-variation-price, .woocommerce-variation-price .price > span.woocommerce-Price-amount", $this );
		$variations->typographySection( __('Description', 'oxyultimate-woo'), ".woocommerce-variation-description p", $this );


		//* Clear Link
		$clearText = $this->addControlSection( 'clear_section', __('Clear Text', "oxyultimate-woo"), 'assets/icon.png', $this);
		$selector = ".reset_variations";

		$spacing = $clearText->addControlSection( 'clear_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this);
		$spacing->addPreset(
			"padding",
			"resetbtn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"resetbtn_padding",
			__("Margin"),
			$selector
		)->whiteList();

		$clearText->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'width',
				),
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

		$clearText->typographySection( __('Typography'), $selector, $this );
		$clearText->borderSection( __('Borders', "oxyultimate-woo"), $selector, $this );
		$clearText->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );

		$variationSel = $this->addControlSection( 'variationSel', __('Variation Dropdown', "oxyultimate-woo"), 'assets/icon.png', $this);
		$selector = ".variations select";

		$variationSel->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'background-color',
				),
				array(
					'selector' 	=> $selector,
					'property' 	=> 'border-color',
				),
				array(
					'selector' 	=> $selector,
					'property' 	=> 'border-width',
				)
			)
		);

		$spacing = $variationSel->addControlSection( 'variationSel_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this);
		$spacing->addPreset(
			"padding",
			"vsel_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"vsel_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$variationSel->typographySection( __('Typography'), $selector, $this );


		/**************************
		 * Availability
		 **************************/
		$availability = $this->addControlSection( 'availability', __('Availability', "oxyultimate-woo"), 'assets/icon.png', $this);
		$selector = '.stock.in-stock';

		$availability->addOptionControl(
			array(
				"type" 		=> "radio",
				"name" 		=> __('Hide Availability Info', "oxyultimate-woo"),
				"slug" 		=> 'ouatc_hide_availability',
				"value" 	=> ['yes' => __("Yes"), "no" => __('No')],
				"default" 	=> 'yes'
			)
		)->rebuildElementOnChange();

		$availability->typographySection( __('Typography'), $selector, $this );


		/*************************
		 * Group Products
		 ************************/
		$gp = $this->addControlSection( 'gp_section', __('Group Product', "woocommerce"), "assets/icon.png", $this );

		$gp->addStyleControls([
			[
				'name' 		=> __('Vertical Align', "oxyultimate-woo"),
				'selector' => 'form.cart .group_table td',
				'property' => 'vertical-align',
				'control_type' => 'radio',
				'value' => [
					'top' => __('Top'),
					'middle' => __('Center')
				]
			],
			[
				'name' 		=> __('Cell Spacing', "oxyultimate-woo"),
				'selector' => 'form.cart table.group_table',
				'property' => 'border-spacing',
				'control_type' => 'slider-measurebox',
				'unit' 		=> 'px',
				'min' 		=> 0,
				'max' 		=> 30,
				'step' 		=> 1
			]
		]);

		$pt = $gp->typographySection( __('Product Title'), '.woocommerce-grouped-product-list-item__label a', $this );
		$pt->addStyleControl([
			'name' 		=> __('Hover Color', "oxyultimate-woo"),
			'selector' => '.woocommerce-grouped-product-list-item__label a:hover',
			'property' => 'color'
		]);

		$gp->typographySection( 
			__('Price'), 
			'.woocommerce-grouped-product-list-item__price .woocommerce-Price-amount.amount, .woocommerce-grouped-product-list-item__price ins .woocommerce-Price-amount.amount', 
			$this 
		);

		$gp->typographySection( 
			__('Strick Through Price'), 
			".woocommerce-grouped-product-list-item__price del span.woocommerce-Price-amount, .woocommerce-grouped-product-list-item__price del", 
			$this 
		);
	}

	function fetchDynamicProductID( $id ) {
		if( strstr( $id, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($id));
			$id = do_shortcode($shortcode);
		}

		return intval( $id );
	}

	function render( $options, $default, $content ) {
		global $post, $product;

		if( isset( $options['ouatc_shoploop'] ) && $options['ouatc_shoploop'] == 'yes') {
			$product = WC()->product_factory->get_product( get_the_ID() );
		} else {
			if( empty( $options['ouatc_product'] ) || $options['ouatc_product'] == 'none' ) {
				
				if( is_singular( "product" ) ) {
					$product = WC()->product_factory->get_product( get_the_ID() );
				} else {
					echo __('Enter a product ID', "oxyultimate-woo" );
					return;
				}

			} else {
				$product = WC()->product_factory->get_product( $this->fetchDynamicProductID( $options['ouatc_product'] ) );
				/*if ( $product ) {
                    $post = get_post($product->get_id());
                    setup_postdata($post);
                }*/
			}
		}

		if( $product === false )
			return;

		$availability = isset( $options['ouatc_hide_availability'] ) ? $options['ouatc_hide_availability'] : "yes";
		$this->btn_icon = isset($options['btn_icon']) ? esc_html( $options['btn_icon'] ) : false;
		$this->btn_icon_pos = isset($options['btn_icon_pos']) ? esc_html( $options['btn_icon_pos'] ) : 'right';

		if( $this->btn_icon ) {
			global $oxygen_svg_icons_to_load;
			$oxygen_svg_icons_to_load[] = $this->btn_icon;
		}

		$classes = array();

		if( isset( $options['enable_qtybtns'] ) && $options['enable_qtybtns'] == "yes") {
			$classes[] = 'have-qty-buttons';
			$classes[] = 'qtybtns-pos-' . (isset( $options['qtybtns_pos'] ) ? $options['qtybtns_pos'] : 'lr');
			add_action( 'woocommerce_before_quantity_input_field', array( $this, 'ouatc_add_minus_button' ) );
			add_action( 'woocommerce_after_quantity_input_field', array( $this, 'ouatc_add_plus_button' ) );

			if( ! isset($_GET['oxygen_iframe']) && ! $this->qty_added ) {
				$this->qty_added = true;
				//add_action('wp_footer', array( $this, 'ouadtc_qty_change_script'), 99 );

				echo $this->ouadtc_qty_change_script();
			}
		}

		if( isset( $options['ouatc_shoploop'] ) && $options['ouatc_shoploop'] == 'no' && $product->get_type() != 'external' ) {
			$this->btn_text = isset( $options['ouatc_btn_text'] ) ? esc_html( $options['ouatc_btn_text'] ) : false;
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'ouwoo_custom_single_add_to_cart_text' ) );
		}

		if( isset( $options['ouatc_shoploop'] ) && $options['ouatc_shoploop'] == 'yes' && $product->get_type() != 'external' ) {

			$this->loop_btn_text = $this->variable_btn_text = $this->read_more_txt = $this->grouped_btn_text = false;

			if( isset( $options['ouatc_change_text'] ) && $options['ouatc_change_text'] == "yes" ) {
				$this->loop_btn_text = isset($options['ouatc_simple_btn_text']) ? $options['ouatc_simple_btn_text'] : __('Add to cart');
				$this->variable_btn_text = isset($options['ouatc_variable_btn_text']) ? $options['ouatc_variable_btn_text'] : __('Add to cart');
				$this->read_more_txt = isset($options['ouatc_rm_btn_text']) ? $options['ouatc_rm_btn_text'] : __('Add to cart');
				$this->grouped_btn_text = isset($options['ouatc_grouped_btn_text']) ? $options['ouatc_grouped_btn_text'] : __('Add to cart');
			}

			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'ouwoo_product_add_to_cart_text' ), 10, 2 );
		}

		if( isset( $options['ouatc_price'] ) && $options['ouatc_price'] == "hide" )
			$classes[] = 'wc-price-hide';

		if( isset( $options['ouatc_vprice'] ) && $options['ouatc_vprice'] == "hide" )
			$classes[] = 'wc-variation-price-hide';

		if( isset( $options['ouatc_qty'] ) && $options['ouatc_qty'] == "hide" )
			$classes[] = 'wc-qty-hide';

		if( isset( $options['ouatc_vdesc'] ) && $options['ouatc_vdesc'] == "hide" )
			$classes[] = 'wc-vdesc-hide';

		if( isset( $options['ouatc_qtypos'] ) && $options['ouatc_qtypos'] == "above" )
			$classes[] = 'wc-qty-above';

		$classes = implode(' ', $classes);

		$dataAttr = '';
		if( isset($options['ouatc_ajax_single']) && $options['ouatc_ajax_single'] == 'yes' && isset( $options['ouatc_shoploop'] ) && $options['ouatc_shoploop'] == 'no' ) {
			$dataAttr .= ' data-adtcajax="yes"';

			if( isset( $options['ouatc_redirect_url'] ) ) {
				$dataAttr .= ' data-redirect="' . esc_url( $options['ouatc_redirect_url'] ) . '"';
			}

			if( ! $this->isBuilderEditorActive() && ! $this->ajax_js_added ) {
				$this->ajax_js_added = true;

				add_action('wp_footer', array( $this, 'ouadtc_ajax_script'), 98 );
				wp_enqueue_script('wc-add-to-cart');
				wp_enqueue_script('wc-cart-fragments');
			}
		}

		echo '<div class="woocommerce ouatc-container clear clearfix product-type-'. $product->get_type() . ' ' . $classes . '"'. $dataAttr .'><div class="atc-product">';
		
		echo $product ? $product->get_image( 'woocommerce_thumbnail' ) : '';

			if( isset( $options['ouatc_shoploop'] ) && $options['ouatc_shoploop'] == 'no' ) {

				if( ( empty($options['ouatc_price']) || $options['ouatc_price'] != "no" ) && $product->get_type() != 'external' )
					woocommerce_template_single_price();

				
				if( $availability == "yes" ) {
					add_filter( 'woocommerce_get_stock_html', '__return_null' );
				}

				add_filter( 'esc_html', array( $this,'ouwoo_avoid_esc_html'), 10, 2 );

				woocommerce_template_single_add_to_cart();

				if( $availability == "yes" ) {
					remove_filter( 'woocommerce_get_stock_html', '__return_null' );
				}

				remove_filter( 'esc_html', array( $this,'ouwoo_avoid_esc_html'), 10, 2 );
			} else {

				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'ouwoo_atc_btn_icon' ) );
				
				if( isset( $options['ouatc_qty'] ) && $options['ouatc_qty'] == "yes" 
					&& $product->get_type() == 'simple' && $product->is_purchasable() 
				) {
					echo '<form class="cart"><div class="quantity">';

					do_action( 'woocommerce_before_quantity_input_field' );

					echo '<input 
						type="number" 
						id="' . uniqid( 'quantity_' ) . '" 
						class="'.  esc_attr( join( ' ', (array) apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ) ) ) . '" 
						step="'. esc_attr( apply_filters( 'woocommerce_quantity_input_step', 1, $product ) ) .'" 
						min="'. esc_attr( apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ) ) . '" 
						max="" 
						name="quantity" 
						value="1" 
						title="Qty" 
						size="4" 
						placeholder="'. esc_attr( apply_filters( 'woocommerce_quantity_input_placeholder', '', $product ) ) .'" 
						inputmode="numeric" 
						onkeyup="JavaScript: ATCUpdateQty(jQuery(this));" 
						onchange="JavaScript: ATCUpdateQty(jQuery(this));">';

					do_action( 'woocommerce_after_quantity_input_field' );

					echo '</div>';
				}

				woocommerce_template_loop_add_to_cart();
				
				if( isset( $options['ouatc_qty'] ) && $options['ouatc_qty'] == "yes" 
					&& $product->get_type() == 'simple' && $product->is_purchasable() 
				) {
					echo '</form>';

					if( UltimateWooEl::isBuilderEditorActive() ) {
						$this->El->builderInlineJS("
							function ATCUpdateQty(obj){
								var qty = obj.val(),
								parentDiv = obj.closest('.oxy-ou-addtocart');

								parentDiv.find('.add_to_cart_button').attr('data-quantity', qty);
							}"
						);
					}

					if( ! $this->isBuilderEditorActive() && ! $this->js_added ) {
						$this->js_added = true;
						add_action('wp_footer', array( $this, 'ouadtc_script'), 99 );
					}
				}
			}
		
		echo '</div></div>';
		
		if( isset( $options['enable_qtybtns'] ) && $options['enable_qtybtns'] == "yes") {
			remove_action( 'woocommerce_before_quantity_input_field', array( $this, 'ouatc_add_minus_button' ) );
			remove_action( 'woocommerce_after_quantity_input_field', array( $this, 'ouatc_add_plus_button' ) );
		}

		if( ! $this->isBuilderEditorActive() ) 
			wc_setup_product_data($post);

		remove_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'ouwoo_custom_single_add_to_cart_text' ) );
		remove_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'ouwoo_product_add_to_cart_text' ), 10, 2 );
		remove_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'ouwoo_atc_btn_icon' ) );
	}

	function ouatc_add_minus_button() {
		echo '<span class="ouatc-qty-minus ouatc-qty-chng"><span>-</span></span>';
	}
	
	function ouatc_add_plus_button() {
		echo '<span class="ouatc-qty-plus ouatc-qty-chng">+</span>';
	}

	function ouwoo_avoid_esc_html( $esc_html_text, $text ) {
		return $text;
	}

	function ouadtc_qty_change_script() {
	?>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', (event) => {
				
				document.querySelectorAll('.ouatc-container .qty').forEach(function(el) {
					var atcItemBaseQty = 1;

					el.addEventListener('focusin', function (e) {
						atcItemBaseQty = this.value;
					});

					el.addEventListener('change', function(e){
						var qtyinput 	= this,
							newQty 	= parseFloat( qtyinput.value ),
							step 	= qtyinput.getAttribute('step') > 0 ? parseFloat( qtyinput.getAttribute('step') ) : 1,
							min 	= parseFloat( qtyinput.getAttribute('min') ),
							max 	= parseFloat( qtyinput.getAttribute('max') ),
							invalid = false;

						if( isNaN( newQty )  || newQty < 0 || newQty < min ) {
							invalid = true;
						}
						else if( newQty > max ){
							invalid = true;
						}
						else if( ( newQty % step ) !== 0 ) {
							invalid = true;
						} 

						if( invalid ){
							qtyinput.value = atcItemBaseQty;
							return;
						}

						qtyinput.value = newQty;
					});
				});

				document.querySelectorAll('.ouatc-qty-chng').forEach(function(pm) {
					pm.addEventListener('click', function(e){
						e.preventDefault();
						e.stopImmediatePropagation();

						var toggler 	= e.currentTarget,
							qtyinput 	= toggler.closest('.quantity').querySelector('.qty') || false;

						if( ! qtyinput && ( ! toggler.closest('.grouped_form') || ! toggler.closest('.bundle_form') || ! toggler.closest('li.mnm_item') ) ) return;

						var baseQty = atcItemBaseQty = parseFloat( qtyinput.value ),
							step 	= qtyinput.getAttribute('step') > 0 ? parseFloat( qtyinput.getAttribute('step') ) : 1,
							min 	= parseFloat( qtyinput.getAttribute('min') ),
							max 	= parseFloat( qtyinput.getAttribute('max') ),
							action 	= toggler.classList.contains( 'ouatc-qty-plus' ) ? 'add' : 'less',
							newQty 	= action === 'add' ? baseQty + step : baseQty - step;

						if( isNaN( newQty ) && ( toggler.closest('.grouped_form') || toggler.closest('.bundle_form') || toggler.closest('li.mnm_item') ) )
							newQty = 1;
						
						qtyinput.value = newQty;

						var event = new Event('change');
						qtyinput.dispatchEvent(event);

						jQuery(':input.qty').trigger("change.wc-mnm-form");
						jQuery(qtyinput).trigger("change");
					});
				});
			});
		</script>
	<?php
	}
	
	function ouadtc_script() {
	?>
		<script type="text/javascript">
			function ATCUpdateQty(obj){
				var qty = obj.val(),
				parentDiv = obj.closest('.oxy-ou-addtocart');

				parentDiv.find('.add_to_cart_button').attr('data-quantity', qty);
			}
		</script>
	<?php
	}

	function ouadtc_ajax_script() {?>
		<script type="text/javascript">
			jQuery(document).ready(ouatcdoajax);

			window.WP_Grid_Builder && WP_Grid_Builder.on( 'init', function( wpgb ) {
				wpgb.facets.on( 'appended', function( facets ) { ouatcdoajax(jQuery); } );
			});

			function ouatcdoajax($){
				
				if ( typeof wc_add_to_cart_params === 'undefined' ) {
					return false;
				}

				$( '.ouatc-container' ).each(function(){
					var isAjaxEnable = $(this).attr('data-adtcajax'),
						redirect_url = $(this).attr('data-redirect') || '';

					if ( typeof isAjaxEnable != "undefined" && isAjaxEnable == 'yes' && ! $(this).hasClass('product-type-external') ) {
						$(this).find( '.single_add_to_cart_button' ).on( 'click tap', function(e) {
							e.preventDefault();
							var $thisbutton = $(e.currentTarget),
								$form = $thisbutton.closest('form.cart'),
								productData = $form.serializeArray(),
								hasProductId = false,
								variation_id = $form.find('input[name=variation_id]').val() || 0;

							$.each( productData, function( key, form_item ){
								if( form_item.name === 'productID' || form_item.name === 'add-to-cart' ){
									if( form_item.value ){
										hasProductId = true;
										return false;
									}
								}
							});

							if( !hasProductId ){
								var is_url = $form.attr('action').match(/add-to-cart=([0-9]+)/),
									productID = is_url ? is_url[1] : false; 
							}

							if( $thisbutton.attr('name') && $thisbutton.attr('name') == 'add-to-cart' && $thisbutton.attr('value') ){
								var productID = $thisbutton.attr('value');
							}

							if( productID ){
								productData.push({name: 'add-to-cart', value: productID});
							}

							productData.push({name: 'action', value: 'ouwoo_ajax_add_to_cart'});
							//productData.push({name: 'redirectURL', value: redirect_url});
							productData.push({name: 'variation_id', value: variation_id});

							$(document.body).trigger('adding_to_cart', [$thisbutton, productData]);
							
							$.ajax({
								type: 'post',
								url: wc_add_to_cart_params.ajax_url,
								data: $.param(productData),
								beforeSend: function () {
									$thisbutton.removeClass('added').addClass('loading');
								},
								complete: function () {
									$thisbutton.addClass('added').removeClass('loading');
								},
								success: function (response) {
									if (response.error && response.product_url) {
										window.location = response.product_url;
										return;
									} else {
										$('body').trigger('updated_wc_div');
										$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
										$('.added_to_cart').remove();
										$form[0].reset();
										if ( typeof redirect_url != "undefined" && redirect_url !== '' ) {
											window.location = redirect_url;
											return;
										}
									}
								},
							});
							return false;
						});
					}
				});
			}
		</script>
	<?php
	}

	function ouwoo_custom_single_add_to_cart_text( $text ) {

		if( $this->btn_text ) {
			$text = $this->btn_text;
		}

		if( $this->btn_icon_pos == 'left' && $this->btn_icon ) {
			$text = '<svg class="atc-btn-icon"><use xlink:href="#' . $this->btn_icon . '"></use></svg> ' . $text;
		}

		if( $this->btn_icon_pos == 'right' && $this->btn_icon ) {
			$text .= ' <svg class="atc-btn-icon"><use xlink:href="#' . $this->btn_icon . '"></use></svg>';
		}

		return $text; 
	}

	function ouwoo_product_add_to_cart_text( $text, $obj ) {

		if( $obj->get_type() == 'simple' && $this->loop_btn_text ) {
			$text = $obj->is_purchasable() && $obj->is_in_stock() ? $this->loop_btn_text : $this->read_more_txt;
		}

		if( $obj->get_type() == 'variable' && $this->variable_btn_text ) {
			$text = $obj->is_purchasable() ? $this->variable_btn_text : $this->read_more_txt;
		}

		if( $obj->get_type() == 'grouped' && $this->grouped_btn_text ) {
			$text = $this->grouped_btn_text;
		}

		if( $this->btn_icon_pos == 'left' && $this->btn_icon && $obj->get_type() == 'simple' ) {
			$text = '&times; ' . $text;
		}

		if( $this->btn_icon_pos == 'right' && $this->btn_icon && $obj->get_type() == 'simple' ) {
			$text .= ' &times;';
		}

		return $text;
	}

	function ouwoo_atc_btn_icon( $link ) {

		if( $this->btn_icon ) {
			return str_replace( '&times;', '<svg class="atc-btn-icon"><use xlink:href="#' . $this->btn_icon . '"></use></svg>', $link );
		}

		return $link;
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$this->css_added = true;
			return '.oxy-ou-addtocart {display: flex; flex-direction: row;}
					.wc-vdesc-hide .woocommerce-variation-description,
					.wc-qty-hide .quantity,
					.have-qty-buttons.wc-qty-hide .quantity,
					.wc-variation-price-hide .woocommerce-variation-price,
					.wc-price-hide p.price,
					.oxy-ou-addtocart .container-image-and-badge {
					  display: none;
					}
					.ouatc-container .atc-product > img {
						position: absolute;
						height: 100%;
						width: 100%;
						opacity: 0;
						visibility: hidden;
						padding: 0;
						margin: 0;
						left: 0;
					}
					.ouatc-container .quantity input{border-style: solid}
					.ouatc-container .quantity input:focus{outline:0;}
					.wc-qty-above form.cart div.quantity,
					.woocommerce.wc-qty-above div.atc-product form.cart div.quantity {
						float: none;
						clear: both;
						margin : 0 0 5px;
					}
					.oxy-ou-addtocart div.atc-product {position: relative;}
					.oxy-ou-addtocart div.atc-product form.cart {
						display: flex;
						flex-wrap: wrap;
						padding: 0!important;
						margin : 0!important;
						max-width: initial!important;
					}
					.oxy-ou-addtocart div.atc-product form.cart .variations {
						margin-bottom: 1em;
						border: 0;
						width: 100%;
					}
					.oxy-ou-addtocart div.atc-product form.cart div.quantity {
						float: left;
						margin: 0 4px 0 0;
					}
					.oxy-ou-addtocart .ouatc-container.wc-qty-above form.cart,
					.oxy-ou-addtocart div.product-type-grouped form.cart {
						flex-direction: column;
					}
					.ouatc-container {
						clear: both;
						display: inline-block;
						width: 100%;
					}
					.woocommerce.ouatc-container .quantity .qty{min-width: auto!important}				
					.single_add_to_cart_button.disabled {
						pointer-events: none;
					}
					.have-qty-buttons .quantity {
						text-align: center;
						font-family: inherit;
						align-items: center;
						margin-top: 5px;
					}
					.have-qty-buttons.qtybtns-pos-lr .quantity {
						display: flex;
					}
					.woocommerce.have-qty-buttons:not(.wc-qty-above) .button {
						margin-left: 10px;
					}
					.woocommerce .atc-product .button{ align-items: center; }
					.ouatc-container.product-type-external .button,
					.oxy-ou-addtocart .product-type-grouped .button,
					.oxy-ou-addtocart .product-type-variable a.button {
						margin-left: 0!important;
						width: 100%!important;
					}
					.woocommerce.have-qty-buttons.qtybtns-pos-lr input.qty,
					.have-qty-buttons.qtybtns-pos-lr .ouatc-qty-chng {
						background: transparent;
						height: 35px;
						--qty-minus-offset: 35px;
					}
					/*.have-qty-buttons.qtybtns-pos-lr .ouatc-qty-minus span { 
						margin-top: calc(100% - var(--qty-minus-offset) - 3px);
					}*/
					.ouatc-qty-chng {
						cursor: pointer;
						display: flex;
						font-size: 14px;
						line-height: 1;
						justify-content: center;
						align-items: center;
						width: 35px;
					}
					.have-qty-buttons.qtybtns-pos-lr .ouatc-qty-chng {
						background-color: #efefef;
						border-radius: 100%;
					}
					.woocommerce.have-qty-buttons input::-webkit-outer-spin-button,
					.woocommerce.have-qty-buttons input::-webkit-inner-spin-button {
						-webkit-appearance: none;
						appearance: none;
						margin: 0;
						display: none;
					}
					.woocommerce.have-qty-buttons input[type=number],
					.woocommerce.have-qty-buttons input[type=number]:focus {
						background-image: none;
						-moz-appearance: textfield;
						font-size: 14px;
						text-align: center;
						font-family: inherit;
						padding: 0;
						margin: 0;
						border-radius: 0;
						box-shadow: none;
					}
					.woocommerce.have-qty-buttons.qtybtns-pos-lr input[type=number]{
						border: none;
						width: 45px;
					}
					.have-qty-buttons.qtybtns-pos-right .quantity {
						display: grid;
						grid-template-columns: 1fr 1fr;
						width: 80px;
						align-items: stretch;
					}
					.have-qty-buttons.qtybtns-pos-right .ouatc-qty-chng {
						width: 100%;
						height: 100%;
						border: 1px solid #efefef;
						background: #fff;
					}
					.have-qty-buttons.qtybtns-pos-right .ouatc-qty-plus {
						order: 2;
					}
					.have-qty-buttons.qtybtns-pos-right .ouatc-qty-minus {
						order: 3;
						border-top: 0!important;
					}
					.have-qty-buttons.qtybtns-pos-right input[type=number] {
						width: 100%;
						order: 1;
						grid-row: span 2;
						border: 1px solid #efefef;
						border-right: 0;
						height: auto!important;
					}
					svg.atc-btn-icon {
						width: 16px;
						height: 16px;
						fill: currentColor;
					}
					';
		}
	}

}

new OUWooAddToCart();