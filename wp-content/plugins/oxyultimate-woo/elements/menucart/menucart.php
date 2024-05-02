<?php

class OUWooMenuCart extends UltimateWooEl {
	
	public $has_js = true;
	public $css_added = false;
	public $js_added = false;
	private $minicart_js = array();
	public $hide_cart_counter = 'no';

	function name() {
		return __( "Menu Cart", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_minicart";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function custom_init() {
		add_filter("oxygen_vsb_element_presets_defaults", array( $this, "ouwoo_menucart_presets_defaults" ) );		
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'ouwoo_menu_cart_fragment' ) );
		add_action( 'woocommerce_before_mini_cart_contents', 'ouwoo_common_filter_mini_cart_contents' );
	}

	function ouwoo_menucart_presets_defaults( $all_elements_defaults ) {
		require("menucart-presets.php");

		$all_elements_defaults = array_merge($all_elements_defaults, $menucart_presets);

		return $all_elements_defaults;
	}

	function generalControlSection() {
		$general = $this->addControlSection( 'general', __('Menu Settings', "oxyultimate-woo"), "assets/icon.png", $this );

		$config = $general->addControlSection( 'menu_config', __('Config', "oxyultimate-woo"), "assets/icon.png", $this );

		$config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Use as Cart Counter Only?', "oxyultimate-woo"),
				'slug' 		=> 'oumc_is_cart_counter',
				'value' 	=> [ 'yes' => __('Yes'), 'no' => __('No') ],
				'default'	=> 'no'
			)
		)->rebuildElementOnChange();

		$config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Link With Cart Page?', "oxyultimate-woo"),
				'slug' 		=> 'oumc_link_cart_page',
				'value' 	=> [ 'yes' => __('Yes'), 'no' => __('No') ],
				'default'	=> 'yes',
				"condition" => 'oumc_is_cart_counter=yes'
			)
		);

		$config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Disable Cart Counter?', "oxyultimate-woo"),
				'slug' 		=> 'hide_cart_counter',
				'value' 	=> [ 'yes' => __('Yes'), 'no' => __('No') ],
				'default'	=> 'no',
				"condition" => 'oumc_is_cart_counter=yes'
			)
		)->rebuildElementOnChange();

		$visibility = $config->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Show Button', "oxyultimate-woo"),
			'slug' 		=> 'oumc_btnvisibility',
			'value' 	=> [
				'always' 		=> __('Always', "oxyultimate-woo"),
				'haveproducts' 	=> __('If cart is not empty', "oxyultimate-woo")
			],
			'default' 	=> 'always'
		]);

		$cart_action = $config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Show Cart Contents on", "oxyultimate-woo" ),
				'slug' 		=> 'oumc_trigger',
				"value" 	=> [	
					'hover' 	=> __('Hover', "oxyultimate-woo"), 
					'click' 	=> __('Click', "oxyultimate-woo")
				],
				"default" 	=> 'hover',
				"condition" => 'oumc_is_cart_counter=no'
			)
		);
		$cart_action->rebuildElementOnChange();

		$items_total = $config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Cart Counter Position", "oxyultimate-woo" ),
				'slug' 		=> 'oumc_tnumpos',
				"value" 	=> [
					'bubble' 	=> __('Bubble', "oxyultimate-woo"), 
					'before' 	=> __('Before', "oxyultimate-woo"), 
					"after" 	=> __("After", "oxyultimate-woo")
				],
				"default" 	=> 'bubble'
			)
		);
		$items_total->rebuildElementOnChange();

		$cartPrice= $config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Display Price", "oxyultimate-woo" ),
				'slug' 		=> 'oumc_cprice',
				"value" 	=> [
					'no' 		=> __('No', "oxyultimate-woo"), 
					'yes' 		=> __('Yes', "oxyultimate-woo")
				],
				"default" 	=> 'no'
			)
		);
		$cartPrice->rebuildElementOnChange();

		$priceAlignment = $config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Price Alignment", "oxyultimate-woo" ),
				'slug' 		=> 'oumc_ppos',
				"value" 	=> [
					'left' 		=> __('Left', "oxyultimate-woo"), 
					'right' 	=> __('Right', "oxyultimate-woo")
				],
				"default" 	=> 'left'
			)
		);
		$priceAlignment->rebuildElementOnChange();


		$cartbtn_type = $config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Cart Button Type", "oxyultimate-woo" ),
				'slug' 		=> 'oumc_btnt',
				"value" 	=> [
					'text' 		=> __('Text', "oxyultimate-woo"), 
					'icon' 		=> __('Icon', "oxyultimate-woo"), 
					"bothit" 	=> __("Icon + Text", "oxyultimate-woo")
				],
				"default" 	=> 'text'
			)
		);
		$cartbtn_type->rebuildElementOnChange();

		$config->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Text', "oxyultimate-woo"),
				'slug' 		=> 'oumc_text',
				'default'	=> __( 'Cart', "oxyultimate-woo" )
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='icon'");

		$config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Icon Type', "oxyultimate-woo"),
				'slug' 		=> 'oumc_ict',
				"value" 	=> [
					'icon' 		=> __('Icon', "oxyultimate-woo"), 
					'image' 	=> __('Image', "oxyultimate-woo")
				],
				'default'	=> ''
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='text'");

		$btn_image = $config->addControl("mediaurl", 'oumc_btnimg', __('Image', "oxyultimate-woo"));
		$btn_image->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='text'");

		$imgw = $config->addOptionControl(
			array(
				'type' 		=> 'measurebox',
				'slug' 		=> 'oumc_btnimgw',
				'name' 		=> __('Width', "oxyultimate-woo")
			)
		);
		$imgw->setUnits('px', 'px');
		$imgw->setParam('hide_wrapper_end', true);
		$imgw->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='text'");

		$imgh = $config->addOptionControl(
			array(
				'type' 		=> 'measurebox',
				'slug' 		=> 'oumc_btnimgh',
				'name' 		=> __('Height', "oxyultimate-woo")
			)
		);
		$imgh->setUnits('px', 'px');
		$imgh->setParam('hide_wrapper_start', true);
		$imgh->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='text'");

		$imgalt = $config->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'slug' 		=> 'oumc_btnimgalt',
				'name' 		=> __('Alt')
			)
		);
		$imgalt->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='text'");

		$cart_if = $config->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxyultimate-woo"),
				"slug" 			=> 'oumc_btnicon',
				"value" 		=> 'Lineariconsicon-cart'
			)
		);
		$cart_if->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_ict']=='icon'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='text'");
		$cart_if->rebuildElementOnChange();

		$config->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxyultimate-woo"),
				"slug" 			=> "oumc_icon_size",
				"selector" 		=> 'svg.oumcart-icon',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)
		->setRange(20, 50, 2)
		->setUnits("px", "px")
		->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_ict']=='icon'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_minicart_oumc_btnt']!='text'");

		$config->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Aria-Label/Title of Cart Button', "oxyultimate-woo"),
				'slug' 		=> 'oumc_aria_label',
				'default'	=> __( 'View your shopping cart', "oxyultimate-woo" )
			)
		);

		$csp = $general->addControlSection( 'color_spacing', __('Style', "oxyultimate-woo"), "assets/icon.png", $this );

		$csp->addStyleControl(
			array(
				"selector" 			=> '.oumc-cart-btn',
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox'
			)
		)
		->setUnits("px", "px")
		->setRange("0", "500", "10");

		$csp->addPreset(
			"padding",
			"oumcbtn_padding",
			__("Padding"),
			'.oumc-cart-btn'
		)->whiteList();


		$csp->addPreset(
			"margin",
			"oumcbtn_margin",
			__("Margin"),
			'.oumc-wrapper'
		)->whiteList();

		$csp->addStyleControl(
			array(
				"name" 			=> __('Space Between Icon & Text', "oxyultimate-woo"),
				"slug" 			=> "oumc_gapict",
				"selector" 		=> '.cart-btn-text',
				"control_type" 	=> 'measurebox',
				"property" 		=> 'margin-left',
				"unit" 			=> 'px',
				"condition" 	=> "oumc_btnt=bothit"
			)
		);

		$csp->addStyleControls([
			array(
				"slug" 			=> "oumc_iconbgclr",
				"selector" 		=> '.oumc-cart-btn',
				"property" 		=> 'background-color',
			)
		]);

		$csp->addStyleControls([
			array(
				'name' 			=> __('Background Hover Color', "oxyultimate-woo"),
				"slug" 			=> "oumc_iconbghclr",
				"selector" 		=> '.oumc-cart-btn:hover',
				"property" 		=> 'background-color',
				"control_type" 	=> 'colorpicker',
			)
		]);

		$csp->addStyleControl(
			array(
				"name" 			=> __('Icon Color', "oxyultimate-woo"),
				"slug" 			=> "oumc_iconclr",
				"selector" 		=> '.oumc-cart-btn svg',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color',
				"condition" 	=> "oumc_ict=icon"
			)
		);

		$csp->addStyleControl(
			array(
				"name" 			=> __('Icon Hover Color', "oxyultimate-woo"),
				"slug" 			=> "oumc_iconhclr",
				"selector" 		=> '.oumc-cart-btn:hover svg',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color',
				"condition" 	=> "oumc_ict=icon"
			)
		);

		$cpriceColor= $general->typographySection(__("Price"), ".top-price .woocommerce-Price-amount", $this );
		$cpriceColor->addStyleControl(
			array(
				"name" 			=> __('Hover Color', "oxyultimate-woo"),
				"slug" 			=> "oumc_cphclr",
				"selector" 		=> '.top-price:hover .woocommerce-Price-amount',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color'
			)
		);

		$cartBtnTxt = $general->typographySection(__("Text"), ".cart-btn-text", $this );
		$cartBtnTxt->addStyleControl(
			array(
				"name" 			=> __('Text Hover Color', "oxyultimate-woo"),
				"slug" 			=> "oumc_cthclr",
				"selector" 		=> '.oumc-cart-btn:hover .cart-btn-text',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color',
				"condition" 	=> "oumc_btnt!=icon"
			)
		);

		$general->borderSection(__("Border"), ".oumc-cart-btn", $this );
		$general->boxShadowSection(__("Box Shadow"), ".oumc-cart-btn", $this );
	}

	function cartItemsNumberControlSection(){
		$counter = $this->addControlSection( "cart_counter", __('Cart Counter', "oxyultimate-woo"), "assets/icon.png", $this );

		$counter->typographySection( __('Typography'), ".cart-counter", $this );

		$numcolor = $counter->addControlSection( "oumc_inum", __('Color'), "assets/icon.png", $this );
		$numcolor->addStyleControls([
			array(
				'selector'		=> '.cart-items-num',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Background Hover Color', "oxyultimate-woo"),
				'selector'		=> '.oumc-wrapper:hover .cart-items-num',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Number Hover Color', "oxyultimate-woo"),
				'selector'		=> '.oumc-wrapper:hover .cart-items-num',
				'property' 		=> 'color'
			),
			
		]);

		$numcbrd = $counter->addControlSection( "oumc_inumbrd", __('Border'), "assets/icon.png", $this );
		$numcbrd->addStyleControls([
			array(
				'selector'		=> '.cart-items-num',
				'property' 		=> 'border-color'
			),
			array(
				'name' 			=> __('Border Hover Color', "oxyultimate-woo"),
				'selector'		=> '.cart-items-num:hover',
				'property' 		=> 'border-color'
			),
			array(
				'name' 			=> __('Border Width', "oxyultimate-woo"),
				'selector'		=> '.cart-items-num',
				'property' 		=> 'border-width'
			),
		]);

		$numcbrd->addPreset(
			"border-radius",
			"mcin_border_radius",
			__("Border Radius"),
			'.cart-items-num'
		)->whiteList();

		$numc_bp = $counter->addControlSection( "oumc_inumbp", __('Bubble Config', "oxyultimate-woo"), "assets/icon.png", $this );
		$numc_bp->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">' . __('Use line height feature(from Typography section) to vertically center align the number.') . '</div>', 
			'note'
		)->setParam('heading', __('Note:', "oxyultimate-woo"));

		$numc_bp->addStyleControls([
			array(
				'selector'		=> '.cart-counter',
				'property' 		=> 'width',
				'slug' 			=> 'bubble_width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector'		=> '.cart-counter',
				'slug' 			=> 'bubble_height',
				'property' 		=> 'height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			)
		]);

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.oumc-type-bubble .cart-items-num',
				'property' 		=> 'left',
				'slug' 			=> 'bubble_pleft',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'oumc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_end', true);

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.oumc-type-bubble .cart-items-num',
				'property' 		=> 'top',
				'slug' 			=> 'bubble_ptop',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'oumc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_start', true);

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.oumc-type-bubble .cart-items-num',
				'property' 		=> 'right',
				'slug' 			=> 'bubble_pright',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'oumc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_end', true);

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.oumc-type-bubble .cart-items-num',
				'property' 		=> 'bottom',
				'slug' 			=> 'bubble_pbtm',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'oumc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_start', true);
	}

	function cartContainerControlSection() {
		$cartContainer = $this->addControlSection("cart_contents", __("Items Container", "oxyultimate-woo"), "assets/icon.png", $this );

		$cartContainer->addStyleControls([
			array(
				'selector' 		=> '.widget_shopping_cart_content',
				'property' 		=> 'background-color',
				'slug' 			=> 'oumc_wrapperbg'
			),
			array(
				'selector' 		=> '.widget_shopping_cart_content',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'slug' 			=> 'oumc_wrapper'
			)
		]);

		$cartContainer->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __( 'Height', "oxy-ultimate" ),
			'slug' 	=> 'oumc_wrapper_height',
			'value'	=> array(
				'auto' 		=> __( 'Auto', "oxy-ultimate" ),
				'fixed' 	=> __( 'Fixed', "oxy-ultimate" )
			),
			'default' => 'auto'
		])->rebuildElementOnChange();

		$cartContainer->addStyleControl(
			array(
				'selector' 		=> '.widget_shopping_cart_content',
				'property' 		=> 'max-height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'slug' 			=> 'oumc_wrapperh',
				'condition' 	=> 'oumc_wrapper_height=fixed'
			)
		);

		$pos = $cartContainer->addControlSection("container_pos", __("Position", "oxyultimate-woo"), "assets/icon.png", $this );
		$pos->addStyleControls([
			array(
				'name'  		=> __("Top"),
				'selector' 		=> '.oumc-cart-items',
				'property' 		=> 'top',
				'slug' 			=> 'oumc_wrappermt'
			),
			array(
				'name'  		=> __("Position Top (for hover animation effect)", "oxyultimate-woo"),
				'selector' 		=> '.oumc-cart-items.show-menucart',
				'property' 		=> 'top',
				'slug' 			=> 'oumc_wrappermat'
			),
			array(
				'name'  		=> __("Left"),
				'selector' 		=> '.oumc-cart-items',
				'property' 		=> 'left',
				'slug' 			=> 'oumc_wrappermlt'
			),
			array(
				'name'  		=> __("Right"),
				'selector' 		=> '.oumc-cart-items',
				'property' 		=> 'right',
				'slug' 			=> 'oumc_wrappermrt'
			)
		]);

		$sp = $cartContainer->addControlSection("container_space", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this );
		$sp->addPreset(
			"padding",
			"mccontainer_padding",
			__("Padding"),
			'.widget_shopping_cart_content'
		)->whiteList();

		$cartContainer->borderSection(__("Border"), ".widget_shopping_cart_content", $this );
		$cartContainer->boxShadowSection(__("Box Shadow"), ".widget_shopping_cart_content", $this );
	}

	function cartItemControlSection() {
		$cartItem = $this->addControlSection("cart_items", __("Cart Item", "oxyultimate-woo"), "assets/icon.png", $this );

		$wrapsp = $cartItem->addControlSection("wrapsp", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this );
		$wrapsp->addPreset(
			"padding",
			"mcitem_padding",
			__("Padding"),
			'ul.product_list_widget li'
		)->whiteList();

		$wrapbg = $cartItem->addControlSection("wrap_bg", __("Background Color", "oxyultimate-woo"), "assets/icon.png", $this );
		$wrapbg->addStyleControls([
			array(
				'name'  		=> __("Odd Rows", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li:nth-child(odd)',
				'property' 		=> 'background-color',
				'slug' 			=> 'oumc_itembgodd'
			),
			array(
				'name'  		=> __("Even Rows", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li:nth-child(even)',
				'property' 		=> 'background-color',
				'slug' 			=> 'oumc_itembgev'
			)
		]);

		$wrapdiv = $cartItem->addControlSection("wrap_div", __("Divider", "oxyultimate-woo"), "assets/icon.png", $this );
		$wrapdiv->addStyleControls([
			array(
				'name'  		=> __("Separator Width", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li.mini_cart_item',
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'default' 		=> 1,
				'min' 			=> 0,
				'max' 			=> 10,
				'step' 			=> 1,
				'slug' 			=> 'oumc_sepw'
			),
			array(
				'name'  		=> __("Separator Color", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li.mini_cart_item',
				'property' 		=> 'border-color',
				'slug' 			=> 'oumc_sepclr'
			)
		]);

		$itemImg = $cartItem->addControlSection( "oumc_itemimg" ,__('Product Image', "oxyultimate-woo"), "assets/icon.png", $this );

		$img_selector = "ul.cart_list li img, ul.product_list_widget li img";

		$display = $itemImg->addControl( 'buttons-list', 'oumc_imghide', __( 'Hide Image', "oxyultimate-woo" ));
		$display->setValue( array( "No","Yes" ) );
		$display->setValueCSS( array(
			"Yes" => $img_selector . "{display: none}"
		));
		$display->setDefaultValue("No");
		$display->whiteList();

		$imgAlignment = $itemImg->addControl( 'buttons-list', 'oumc_imgAlign', __( 'Alignment', "oxyultimate-woo" ));
		$imgAlignment->setValue( array( "Left", "Right" ) );
		$imgAlignment->setValueCSS( array(
			"Right" => $img_selector . "{float: right; margin-left: 10px; margin-right:0;}"
		));
		$imgAlignment->setDefaultValue("Left");
		$imgAlignment->whiteList();

		$itemImg->addStyleControls([
			array(
				'name'  		=> __("Size"),
				'selector' 		=> $img_selector,
				'slug' 			=> 'oumc_prdimgw',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'oumc_imghide=No'
			),
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'border-color',
				'condition' 	=> 'oumc_imghide=No'
			),
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'oumc_imghide=No'
			)
		]);

		$itemImg->addPreset(
			"padding",
			"itemimg_padding",
			__("Padding"),
			$img_selector
		)->whiteList();

		$itemImg->addPreset(
			"margin",
			"itemimg_margin",
			__("Margin"),
			$img_selector
		)->whiteList();

		$itemName = $cartItem->typographySection( __('Product Details', "oxyultimate-woo"), ".mini_cart_item a", $this );
		$itemName->addStyleControls([
			array(
				'name'  		=> __("Title Hover Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item a:hover',
				'property' 		=> 'color',
				'slug' 			=> 'oumc_titlehc'
			),
			array(
				'name'  		=> __("Margin Bottom", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .quantity',
				'property' 		=> 'margin-top',
				'slug' 			=> 'oumc_titlemb'
			),
			array(
				'name'  		=> __("Quantity Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .quantity',
				'property' 		=> 'color',
				'slug' 			=> 'oumc_prdqtyc'
			),
			array(
				'name'  		=> __("Quantity Font Size", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .quantity',
				'property' 		=> 'font-size',
				'slug' 			=> 'oumc_qtyfs',
			),
			array(
				'name'  		=> __("Price Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .quantity .woocommerce-Price-amount',
				'property' 		=> 'color',
				'slug' 			=> 'oumc_prdpc'
			),
			array(
				'name'  		=> __("Price Font Size", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .quantity .woocommerce-Price-amount',
				'property' 		=> 'font-size',
				'slug' 			=> 'oumc_prdpfs'
			),
			array(
				'name'  		=> __("Price Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .quantity .woocommerce-Price-amount',
				'property' 		=> 'font-weight',
				'slug' 			=> 'oumc_prdpfw'
			)
		]);

		$removeBtn = $cartItem->addControlSection( "oumc_rmvi", __('Remove Icon', "oxyultimate-woo"), "assets/icon.png", $this );
		$selector = '.mini_cart_item a.remove';
		$selectorSVG = '.mini_cart_item a.remove svg';

		$hidermv = $removeBtn->addControl('buttons-list', 'hide_remove_btn', __('Hide Remove Icon?', "oxyultimate-woo"));
		$hidermv->setValue([ 'no' => __('No'), 'yes' => __('Yes')]);
		$hidermv->setDefaultValue('no');
		$hidermv->setValueCSS(['yes' => $selector . '{display: none!important;}']);

		/*$removeBtn->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxy-ultimate"),
				"slug" 			=> 'remove_icon',
				'css' 			=> false
			)
		);*/
		
		$removeBtn->addStyleControls([
			array(
				'name'  		=> __("Size", "oxyultimate-woo"),
				'selector' 		=> $selectorSVG,
				'slug' 			=> 'remove_size',
				'property' 		=> 'width|height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'slug' 			=> 'remove_clr',
				'selector'		=> $selectorSVG,
				'property' 		=> 'color'
			),
			array(
				'slug' 			=> 'remove_hc',
				'name' 			=> __('Color on Hover', "oxyultimate-woo"),
				'selector'		=> $selector . ':hover svg',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Position Top", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'slug' 			=> 'remove_ptop',
				'property' 		=> 'top',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Position Left", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'slug' 			=> 'remove_pleft',
				'property' 		=> 'left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Position Right", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'slug' 			=> 'remove_pright',
				'property' 		=> 'right',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Gap Between Title & Icon", "oxyultimate-woo"),
				'selector' 		=> '.product-content .product-title',
				'property' 		=> 'padding-right',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'default' 		=> 20
			)
		]);

		$variation = $cartItem->addControlSection( "oumc_variations" ,__('Product Variation', "oxyultimate-woo"), "assets/icon.png", $this );
		$variation_selector = '.oumc-cart-items ul.product_list_widget li dl';
		$value_selector = '.oumc-cart-items .product_list_widget .mini_cart_item .variation dd, .oumc-cart-items .product_list_widget .mini_cart_item .variation dd p';
		$variation->addStyleControls([
			array(
				'name' 			=> __('Vertical Border Color', "oxyultimate-woo"),
				'selector' 		=> $variation_selector,
				'property' 		=> 'border-color'
			),
			array(
				'name' 			=> __('Vertical Border Width', "oxyultimate-woo"),
				'selector' 		=> $variation_selector,
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'margin-left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'padding-left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'font-size'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'line-height'
			),
			array(
				'name'  		=> __("Label Color", "oxyultimate-woo"),
				'selector' 		=> '.oumc-cart-items .product_list_widget .mini_cart_item .variation dt',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Label Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.oumc-cart-items .product_list_widget .mini_cart_item .variation dt',
				'property' 		=> 'font-weight'
			),
			array(
				'name'  		=> __("Value Color", "oxyultimate-woo"),
				'selector' 		=> $value_selector,
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Value Font Weight", "oxyultimate-woo"),
				'selector' 		=> $value_selector,
				'property' 		=> 'font-weight'
			)
		]);
	}

	function cartSubtotalControlSection() {
		$subTotal = $this->addControlSection( 'sub_total', __('Sub Total', "oxyultimate-woo"), "assets/icon.png", $this );
		$selector = '.oumc-cart-items .woocommerce-mini-cart__total';
		$subTotal->addStyleControl(
			array(
				'selector'		=> $selector,
				'property' 		=> 'background-color',
				'slug' 			=> 'subt_bglr'
			)
		);

		$subTotal->typographySection( __('Typography'), ".subtotal-wrap, .subtotal-wrap > strong, ". $selector ." strong", $this );
		$price = $subTotal->addControlSection( 'sub_total_price', __('Price', "oxyultimate-woo"), "assets/icon.png", $this );
		$price->addStyleControls([
			array(
				'name' 			=> __('Price Color', "oxyultimate-woo"),
				'selector'		=> '.total .woocommerce-Price-amount',
				'property' 		=> 'color',
				'slug' 			=> 'price_clr'
			),
			array(
				'name' 			=> __('Price Font Size', "oxyultimate-woo"),
				'selector'		=> '.total .woocommerce-Price-amount',
				'property' 		=> 'font-size',
				'slug' 			=> 'price_fs'
			),
		]);

		$priceAlignment = $price->addControl( 'buttons-list', 'oumc_priceAlign', __( 'Price Alignment', "oxyultimate-woo" ));
		$priceAlignment->setValue( array( "Default", "Right" ) );
		$priceAlignment->setValueCSS( array(
			"Right" => ".total .woocommerce-Price-amount {float: right;}"
		));
		$priceAlignment->setDefaultValue("Right");
		$priceAlignment->whiteList();

		$subTotal->borderSection( __('Border'), $selector, $this );

		$st_spacing = $subTotal->addControlSection( 'subtotal_spacing', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this );
		$st_spacing->addPreset(
			"padding",
			"mcsubt_padding",
			__("Padding", "oxyultimate-woo"),
			$selector
		)->whiteList();

		$st_spacing->addPreset(
			"margin",
			"mcsubt_margin",
			__("Margin", "oxyultimate-woo"),
			$selector
		)->whiteList();
	}

	function cartButtonsControlSection() {
		$btnStructure = $this->addControlSection( "oumc_btns", __('Buttons Structure', "oxyultimate-woo"), "assets/icon.png", $this );

		$btnWrapper = $btnStructure->addControlSection( "oumc_btncontainer", __('Container Spacing', "oxyultimate-woo"), "assets/icon.png", $this );

		$btnWrapper->addPreset(
			"padding",
			"btncontainer_padding",
			__("Padding", "oxyultimate-woo"),
			'.woocommerce-mini-cart__buttons'
		)->whiteList();

		$btnWrapper->addPreset(
			"margin",
			"btncontainer_margin",
			__("Margin", "oxyultimate-woo"),
			'.woocommerce-mini-cart__buttons'
		)->whiteList();

		$btnWrapper->addStyleControl(
			array(
				'selector'		=> '.woocommerce-mini-cart__buttons',
				'property' 		=> 'background-color',
				'slug' 			=> 'btnwrapper_bglr'
			)
		);

		$buttons = $btnStructure->addControlSection( "oumc_btnsspace", __('Buttons Spacing', "oxyultimate-woo"), "assets/icon.png", $this );

		$buttons->addPreset(
			"padding",
			"vc_padding",
			__("Padding for Buttons", "oxyultimate-woo"),
			'.woocommerce-mini-cart__buttons a.button'
		)->whiteList();

		$buttons->addStyleControl(
			array(
				"selector" 			=> '.woocommerce-mini-cart__buttons a.button',
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox',
				'slug' 				=> 'cartbgns_width'
			)
		)
		->setRange('0', '500', 20)
		->setUnits('px', 'px,em,%,auto');

		$gap = $buttons->addStyleControl(
			array(
				"name" 				=> __("Gap Between Two Buttons", "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons .button.checkout',
				"property" 			=> 'margin-left',
				"control_type" 		=> 'slider-measurebox',
				'slug' 				=> 'cartbgns_gap'
			)
		)
		->setRange('0', '50', 5)
		->setUnits('px', 'px');

		$gap->setParam('description', __('Default is 10px. You will set to 0 when buttons will stack one after another.', 'oxyultimate-woo' ) );

		$gapBottom = $buttons->addStyleControl(
			array(
				"name" 				=> __("Gap Bottom", "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons .button.checkout',
				"property" 			=> 'margin-top',
				"control_type" 		=> 'slider-measurebox',
				'slug' 				=> 'cartbgns_gaptop'
			)
		)
		->setRange('0', '50', 5)
		->setUnits('px', 'px');

		$gapBottom->setParam('description', __('This would work when buttons will stack vertically.', "oxyultimate-woo") );

		$btnAlign = $buttons->addControl("buttons-list", "btn_align", __("Alignment", "oxyultimate-woo") );
		$btnAlign->setValue([
			'Left',
			'Center',
			'Right'
		]);

		$btnAlign->setValueCSS([
			'Left' 		=> ".woocommerce-mini-cart__buttons{ text-align: left}",
			'Center'	=> ".woocommerce-mini-cart__buttons{ text-align: center}",
			'Right' 	=> ".woocommerce-mini-cart__buttons{ text-align: right}"
		]);
		$btnAlign->setDefaultValue('Right');
		$btnAlign->whiteList();

		$buttons->addStyleControl([
			'selector' 		=> '.woocommerce-mini-cart__buttons a.button',
			'property' 		=> 'transition-duration',
			'control_type'	=> 'slider-measurebox'
		])->setUnits('s','sec')->setRange(0, 5, 0.1)->setDefaultValue(0.2);

		$viewCart = $this->addControlSection( 'btn_viewcart', __('View Cart Button', "oxyultimate-woo"), "assets/icon.png", $this );

		$hideVC = $viewCart->addControl('buttons-list', 'viewcart_hide', __('Hide View Cart Button?', "oxyultimate-woo"));
		$hideVC->setValue(['No', 'Yes']);
		$hideVC->setValueCSS(['Yes' => '.woocommerce-mini-cart__buttons a:first-child{display:none}' ]);
		$hideVC->setDefaultValue('No');
		$hideVC->whiteList();

		$vcTg = $viewCart->typographySection( __('Font & Colors', "oxyultimate-woo"), ".woocommerce-mini-cart__buttons a:first-child", $this );

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child:hover',
				"property" 			=> 'color',
				'slug' 				=> 'cartbtns_hc'
			)
		)->setParam('hide_wrapper_end', true);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child:hover',
				"property" 			=> 'border-color',
				'slug' 				=> 'cartbtns_hbrdc'
			)
		)->setParam('hide_wrapper_start', true);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('BG Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartbtns_bgc'
			)
		)->setParam('hide_wrapper_end', true);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child:hover',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartbtns_bghc'
			)
		)->setParam('hide_wrapper_start', true);

		$viewCart->borderSection( __( "Border" ), '.woocommerce-mini-cart__buttons a:first-child', $this );
		$viewCart->boxShadowSection( __("Box Shadow"), '.woocommerce-mini-cart__buttons a:first-child', $this );

		$checkout = $this->addControlSection( 'btn_checkout', __('Checkout Button', "oxyultimate-woo"), "assets/icon.png", $this );
		
		$hideCB = $checkout->addControl('buttons-list', 'checkoutbtn_hide', __('Hide Checkout Button?', "oxyultimate-woo"));
		$hideCB->setValue(['No', 'Yes']);
		$hideCB->setValueCSS(['Yes' => '.woocommerce-mini-cart__buttons a.checkout{display:none}' ]);
		$hideCB->setDefaultValue('No');
		$hideCB->whiteList();

		$cTg = $checkout->typographySection( __('Font & Colors', "oxyultimate-woo"), ".woocommerce-mini-cart__buttons a.checkout", $this );
		$cTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout:hover',
				"property" 			=> 'color',
				'slug' 				=> 'cartcbtn_hc'
			)
		)->setParam('hide_wrapper_end', true);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout:hover',
				"property" 			=> 'border-color',
				'slug' 				=> 'cartcbtn_hbrdc'
			)
		)->setParam('hide_wrapper_start', true);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('BG Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartcbtn_bgc'
			)
		)->setParam('hide_wrapper_end', true);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color', "oxyultimate-woo"),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout:hover',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartcbtn_bghc'
			)
		)->setParam('hide_wrapper_start', true);

		$checkout->borderSection( __( "Border" ), '.woocommerce-mini-cart__buttons a.checkout', $this );
		$checkout->boxShadowSection( __("Box Shadow"), '.woocommerce-mini-cart__buttons a.checkout', $this );
	}

	function otherControlSection() {

		$others = $this->addControlSection( "oumc_others", __('Others Config', "oxyultimate-woo"), "assets/icon.png", $this );

		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Reveal Popup', 'oxyultimate-woo'),
			'slug' 		=> 'oumc_reveal_popup',
			'value' 	=> [ 'no' => __('No'), 'yes' => __('Yes') ],
			'default' 	=> 'no'
		])->setParam('description', __('When product will add to cart.'));

		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable AJAX add to cart function on single product page', "oxyultimate-woo"),
			'slug' 		=> 'oumc_ajax_single',
			'value' 	=> [ 'no' => __('No'), 'yes' => __('Yes') ],
			'default' 	=> 'no'
		]);

		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable fly to cart animation effect', "oxyultimate-woo"),
			'description' => __('Product image will fly to cart section & it will indicate that the product was added.', "oxyultimate-woo"),
			'slug' 		=> 'oumc_flytocart',
			'value' 	=> [ 'no' => __('No'), 'yes' => __('Yes') ],
			'default' 	=> 'no'
		]);

		$others->addOptionControl(
			array(
				"name"			=> __('Offset value of Top Position', "oxyultimate-woo"),
				"slug" 			=> "ftc_offset_top",
				"default"		=> "0",
				"type" 			=> 'slider-measurebox',
				'condition' => 'oumc_flytocart=yes'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setDefaultValue(5);

		$others->addOptionControl(
			array(
				"name"			=> __('Offset value of Left Position', "oxyultimate-woo"),
				"slug" 			=> "ftc_offset_left",
				"default"		=> "0",
				"type" 			=> 'slider-measurebox',
				'condition' => 'oumc_flytocart=yes'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setDefaultValue(5);
	}

	function controls() {

		$this->addCustomControl('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">'. __( "You will add some items in the cart before customizing the design. Therefore you can see the live preview on builder editor.", "oxyultimate-woo" ) . '</div>', 'note')->setParam('heading', __('Note'));

		$this->El->addControl(
			"buttons-list", 
			"cart_preview", 
			__( "Preview on Editor Mode", "oxyultimate-woo" ) 
		)
		->setValue([__( "Enable", "oxyultimate-woo" ), __( "Disable", "oxyultimate-woo" ) ])
		->setValueCSS([ 'Enable' => '.oumc-cart-items.oumc-builder-edit{opacity:1;visibility: visible}' ])
		->setDefaultValue('Disable');
		
		$this->generalControlSection();

		$this->cartItemsNumberControlSection();

		$this->cartContainerControlSection();

		$this->cartItemControlSection();

		$this->cartSubtotalControlSection();

		$this->cartButtonsControlSection();

		$this->otherControlSection();

	}

	function render( $options, $defaults, $content ) {
		global $oxygen_svg_icons_to_load;
		
		$total = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;

		if( isset($options['oumc_btnvisibility']) && $options['oumc_btnvisibility'] !== 'always' 
			&& ! $this->isBuilderEditorActive() ) {
			$dataAttr = ' data-oumc-appearance="' . $options['oumc_btnvisibility'] . '"';
		} else {
			$dataAttr = '';
		}

		if( isset( $options['oumc_ajax_single'] ) && $options['oumc_ajax_single'] == 'yes' && ! $this->isBuilderEditorActive() ) {
			$dataAttr .= ' data-ajaxonsinglebtn="yes"';
		}

		if( isset( $options['oumc_flytocart'] ) && $options['oumc_flytocart'] == 'yes' ) {
			$dataAttr .= ' data-flytocart="yes" data-offsettop="' . $options['ftc_offset_top'] . '" data-offsetleft="'. $options['ftc_offset_left'] .'"';
		}

		if( isset($options['oumc_reveal_popup']) && $options['oumc_reveal_popup'] == 'yes' ) {
			$dataAttr .= ' data-reveal-popup="yes"';
		}

		if( is_checkout() ) {
			$dataAttr .= ' data-checkoutpage="yes"';
		}

		$btnLink = "JavaScript: void(0);";
		$use_as_cart_counter = isset( $options['oumc_is_cart_counter'] ) ? $options['oumc_is_cart_counter'] : 'no';

		if( $use_as_cart_counter == 'yes' ) {
			
			$this->hide_cart_counter = isset( $options['hide_cart_counter'] ) ? $options['hide_cart_counter'] : "no";

			$dataAttr .= ' data-cartcounteron="' . $use_as_cart_counter . '"';

			$link_cart_page = isset( $options['oumc_link_cart_page'] ) ? $options['oumc_link_cart_page'] : "yes";
			if( $link_cart_page == 'yes' )
				$btnLink = esc_url( wc_get_cart_url() );
		}

		echo '<div class="oumc-wrapper oumc-type-' . $options['oumc_tnumpos'] .'"'. $dataAttr .'>';

		if ( $options['oumc_tnumpos'] == 'before' && $this->hide_cart_counter == 'no' ) {
			printf('<span class="cart-items-num cart-items-count-before"><span class="cart-counter">%d</span></span>', absint( $total ) );
		}

		$aria_label = isset( $options['oumc_aria_label'] ) ? $options['oumc_aria_label'] : __( 'View your shopping cart', "oxyultimate-woo" );
		echo '<a class="oumc-cart-btn oumc-type-' . $options['oumc_btnt'] .'" href="'. $btnLink . '" role="button" aria-label="'. esc_html( $aria_label ) .'">';
		
			if( isset( $options['oumc_cprice'] ) && $options['oumc_cprice'] == 'yes' && isset( $options['oumc_ppos'] ) && $options['oumc_ppos'] == 'left' )
			{

				printf('<span class="price-align-left top-price"><span class="cart-price">%d</span></span>', wc_price( $this->ou_menucart_total() ) );
			}

			if( $options['oumc_btnt'] == 'icon' ) {
				
				if( $options['oumc_ict'] == 'icon' ) {
					$oxygen_svg_icons_to_load[] = $options['oumc_btnicon'];

					echo '<svg id="' . $options['selector'] . '-cart-icon" class="oumcart-icon"><use xlink:href="#' . $options['oumc_btnicon'] . '"></use></svg>';
				}

				if( $options['oumc_ict'] == 'image' && isset($options['oumc_btnimg']) ) {
					$alt = isset($options['oumc_btnimgalt']) ? $options['oumc_btnimgalt'] : '';

					$width = (isset($options['oumc_btnimgw'])) ? ' width="' . $options['oumc_btnimgw'] . '"' : '';
					$height = (isset($options['oumc_btnimgh'])) ? ' height="' . $options['oumc_btnimgh'] .'"' : '';

					echo '<img src="' . $options['oumc_btnimg'] .'"'. $width . $height .' class="oumcart-btn-image" alt="'. wp_kses_post( $alt ) . '" />';
				}

			} elseif( $options['oumc_btnt'] == 'bothit') {
				if( $options['oumc_ict'] == 'icon' ) {
					$oxygen_svg_icons_to_load[] = $options['oumc_btnicon'];

					echo '<svg id="' . $options['selector'] . '-cart-icon" class="oumcart-icon"><use xlink:href="#' . $options['oumc_btnicon'] . '"></use></svg>';
				}

				if( $options['oumc_ict'] == 'image' && isset($options['oumc_btnimg']) ) {

					$alt = isset($options['oumc_btnimgalt']) ? $options['oumc_btnimgalt'] : '';

					$width = (isset($options['oumc_btnimgw'])) ? ' width="' . $options['oumc_btnimgw'] . '"' : '';
					$height = (isset($options['oumc_btnimgh'])) ? ' height="' . $options['oumc_btnimgh'] .'"' : '';

					echo '<img src="' . $options['oumc_btnimg'] .'"'. $width . $height .' class="oumcart-btn-image" alt="'. wp_kses_post( $alt ) . '" />';
				}

				if( isset( $options['oumc_text'] ) ) {
					echo '<span class="cart-btn-text">' . $options['oumc_text'] . '</span>';
				}

			} else {
				echo '<span class="cart-btn-text">' . $options['oumc_text'] . '</span>';
			}

			if( isset( $options['oumc_cprice'] ) && $options['oumc_cprice'] == 'yes' && isset( $options['oumc_ppos'] ) && $options['oumc_ppos'] == 'right' )
			{
				printf('<span class="price-align-right top-price"><span class="cart-price price-align-right">%d</span></span>', wc_price( $this->ou_menucart_total() ) );
			}

			if ( $options['oumc_tnumpos'] == 'bubble' && $this->hide_cart_counter == 'no' ) {
				printf('<span class="cart-items-num"><span class="cart-counter">%d</span></span>', absint( $total ) );
			}
		echo '</a>';

		if ( $options['oumc_tnumpos'] == 'after' && $this->hide_cart_counter == 'no' ) {
			printf('<span class="cart-items-num cart-items-count-after"><span class="cart-counter">%d</span></span>', absint( $total ) );
		}

		echo '</div>';

		$class = '';
		if( $this->isBuilderEditorActive() ) {
			$class = ' oumc-builder-edit';
		}

		if( $use_as_cart_counter == 'no' ) {
			echo '<div class="oumc-cart-items'.$class.'"><div class="widget_shopping_cart_content">';
			woocommerce_mini_cart();
			echo '</div></div>';
		}

		if( ! defined('OXY_ELEMENTS_API_AJAX') ) {
			if( ! $this->js_added ) {
				$this->js_added = true;
				add_action( 'wp_footer', array($this, 'ouwoo_menucart_js') );
			}

			$this->minicart_js[] = "jQuery(document).ready(function(){
										new OUWooMenuCart({
											selector: '{$options['selector']}', 
											trigger: '{$options['oumc_trigger']}'
										});
									});";
			$this->El->footerJS( join('', $this->minicart_js) );
		}
	}

	function ouwoo_menu_cart_fragment( $fragments ) {
		ob_start();

		if( $this->hide_cart_counter == 'no' ) :
		?>
		<span class="cart-counter"><?php echo is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : '0'; ?></span>
		<?php
		
		$fragments['span.cart-counter'] = ob_get_clean();

		endif;

		ob_start();
		?>
		<span class="cart-price"><?php echo is_object( WC()->cart ) ? wc_price( $this->ou_menucart_total() ) : wc_price( 0 ); ?></span>
		<?php
		
		$fragments['span.cart-price'] = ob_get_clean();

		remove_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'ouwoo_menu_cart_fragment' ) );
		
		return $fragments;
	}

	function ou_menucart_total() {
		if ( WC()->cart->display_prices_including_tax() ) {
			return ( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
		} else {
			return WC()->cart->get_cart_contents_total();
		}
	}

	function customCSS( $original, $selector ) {
		$defaultCSS = '';
		if( ! $this->css_added ) {
			$defaultCSS = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();
		if( isset( $original[ $prefix . '_oumc_wrapper_height' ] ) && $original[ $prefix . '_oumc_wrapper_height' ] == 'fixed' ) {
			$defaultCSS .= $selector . ' .widget_shopping_cart_content{overflow-y: scroll;}';
		}

		return $defaultCSS;
	}

	function enableFullPresets() {
		return true;
	}

	function ouwoo_menucart_js() {
		wp_enqueue_script(
			'ou-mc-script',
			OUWOO_URL . 'assets/js/oumenucart.min.js',
			array('wc-cart-fragments'),
			filemtime( OUWOO_DIR . 'assets/js/oumenucart.min.js' ),
			true
		);
	}
}

new OUWooMenuCart();