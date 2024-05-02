<?php
class OUWooCart extends UltimateWooEl {
	
	public $has_js = true;
	public $css_added = false;
	public $msg = '';
	public $disable_permalink = 'no';

	function name() {
		return __( "Ultimate Cart", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_cart";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();

		add_action( 'woocommerce_before_mini_cart_contents', 'ouwoo_common_filter_mini_cart_contents' );
		add_action( 'woocommerce_before_mini_cart_contents', 'ouwoo_filter_mini_cart_contents' );
		add_filter(	"oxygen_vsb_element_presets_defaults", array( $this, "ouwoo_minicart_presets_defaults" ) );
	}

	function ouwoo_minicart_presets_defaults( $all_elements_defaults ) {
		require("minicart-presets.php");

		$all_elements_defaults = array_merge($all_elements_defaults, $minicart_presets);

		return $all_elements_defaults;
	}

	function cartItems() {
		$this->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">' . __('Click on <span style="color:#ff7171;">Apply Params</span> button, if content is not showing properly on Builder editor.') . '</div>', 
			'description'
		)->setParam('heading', 'Note:');

		$emptycart = $this->El->addControl('buttons-list', 'hide_if_empty_msg', __('Display Empty Cart Message'));
		$emptycart->setValue([ 'no' => __('No'), 'yes' => __('Yes')]);
		$emptycart->setDefaultValue('no');
		$emptycart->setValueCSS(['yes' => '.woocommerce-mini-cart__empty-message{display: none;}']);
		$emptycart->rebuildElementOnChange();

		$preview = $this->El->addControl('buttons-list', 'preview_empty_msg', __('Enable Preview of Custom Empty Cart Message'));
		$preview->setValue(['No', 'Yes']);
		$preview->setValueCSS([
			'Yes' => '.preview-empty-msg{display:block!important;}',
			'No' => '.preview-empty-msg{display:none!important;}'
		]);
		$preview->setDefaultValue('No');

		$container = $this->addControlSection("container", __("Container", "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.widget_shopping_cart_content';

		$container->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$sp = $container->addControlSection("container_sp", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this );
		$sp->addPreset(
			"padding",
			"cntsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$container->borderSection(__('Border'), $selector, $this);
		$container->boxShadowSection(__('Box Shadow'), $selector, $this);

		//* Cart Items
		$cartItem = $this->addControlSection("cart_items", __("Cart Items", "oxyultimate-woo"), "assets/icon.png", $this );

		$disable_permalink = $cartItem->addControl('buttons-list', 'disable_permalink', __('Disable Permalink', "oxyultimate-woo"));
		$disable_permalink->setValue(['No', 'Yes']);
		$disable_permalink->setValueCSS([
			'Yes' => '.product-image a, .product-title{pointer-events: none;}',
		]);
		$disable_permalink->setDefaultValue('No');
		$disable_permalink->setParam('description', __('Link will remove from product image and title.', "oxyultimate-woo"));

		$cartwsp = $cartItem->addControlSection("mcitemwrap_sp", __("Wrapper Spacing", "oxyultimate-woo"), "assets/icon.png", $this );
		$cartwsp->addPreset(
			"padding",
			"mcwrap_padding",
			__("Padding"),
			'.ou-cart-items ul.product_list_widget'
		)->whiteList();

		$cartwsp->addPreset(
			"margin",
			"mcwrap_margin",
			__("Margin"),
			'.ou-cart-items ul.product_list_widget'
		)->whiteList();

		$cartItemsp = $cartItem->addControlSection("items_sp", __("Items Spacing", "oxyultimate-woo"), "assets/icon.png", $this );
		$cartItemsp->addPreset(
			"padding",
			"mcitem_padding",
			__("Padding"),
			'ul.product_list_widget li'
		)->whiteList();

		$cartItemsp->addStyleControl([
			'selector' 		=> 'ul.product_list_widget li',
			'property' 		=> 'margin-bottom',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 4
		]);

		$bg = $cartItem->addControlSection("items_bg", __("Background Color", "oxyultimate-woo"), "assets/icon.png", $this );
		$bg->addStyleControls([
			array(
				'name'  		=> __("Odd Row(s)", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li:nth-child(odd)',
				'property' 		=> 'background-color'
			),
			array(
				'name'  		=> __("Even Row(s)", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li:nth-child(even)',
				'property' 		=> 'background-color'
			)
		]);

		$divider = $cartItem->addControlSection("items_divider", __("Divider", "oxyultimate-woo"), "assets/icon.png", $this );
		$divider->addStyleControls([
			array(
				'name'  		=> __("Width", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li.mini_cart_item',
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'default' 		=> 1,
				'min' 			=> 0,
				'max' 			=> 10,
				'step' 			=> 1
			),
			array(
				'name'  		=> __("Color", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li.mini_cart_item',
				'property' 		=> 'border-color'
			)
		]);

		$itemImg = $cartItem->addControlSection( "item_img" ,__('Image'), "assets/icon.png", $this );

		$img_selector = 'ul.cart_list li img, ul.product_list_widget li img';

		$display = $itemImg->addControl( 'buttons-list', 'ouocc_imghide', __( 'Hide Image', "oxyultimate-woo" ));
		$display->setValue( array( "No","Yes" ) );
		$display->setValueCSS( array(
			"Yes" => "ul.cart_list li img, ul.product_list_widget li img {display: none}"
		));
		$display->setDefaultValue("No");
		$display->whiteList();

		$itemImg->addStyleControls([
			array(
				'name'  		=> __("Size", "oxyultimate-woo"),
				'selector' 		=> $img_selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_imghide=No'
			),
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'background-color',
				'condition' 	=> 'ouocc_imghide=No'
			),
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'border-color'
			),
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_imghide=No'
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

		$itemName = $cartItem->typographySection( __('Title'), ".mini_cart_item a", $this );
		$itemName->addStyleControl([
			'name'  		=> __("Hover Color", "oxyultimate-woo"),
			'selector' 		=> '.mini_cart_item a:hover',
			'property' 		=> 'color'
		]);

		$variation = $cartItem->addControlSection( "ouocc_variations" ,__('Variations'), "assets/icon.png", $this );
		$variation_selector = '.ou-cart-items ul.product_list_widget li dl';
		$value_selector = '.ou-cart-items .product_list_widget .mini_cart_item .variation dd, .ou-cart-items .product_list_widget .mini_cart_item .variation dd p';
		$variation->addStyleControls([
			array(
				'name' 			=> __('Vertical Border Color'),
				'selector' 		=> $variation_selector,
				'property' 		=> 'border-color'
			),
			array(
				'name' 			=> __('Vertical Border Width'),
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
				'selector' 		=> '.ou-cart-items .product_list_widget .mini_cart_item .variation dt',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Label Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.ou-cart-items .product_list_widget .mini_cart_item .variation dt',
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


		$removeBtn = $cartItem->addControlSection( "remove_btn", __('Remove Icon', "oxyultimate-woo"), "assets/icon.png", $this );
		$selector = '.mini_cart_item a.remove';
		$selectorSVG = '.mini_cart_item a.remove svg';

		$hidermv = $removeBtn->addControl('buttons-list', 'hide_remove_btn', __('Hide Remove Icon?'));
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
				'selector'		=> $selectorSVG,
				'property' 		=> 'color'
			),
			array(
				'name' 			=> __('Color on Hover', "oxyultimate-woo"),
				'selector'		=> $selector . ':hover svg',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Size", "oxyultimate-woo"),
				'selector' 		=> $selectorSVG,
				'property' 		=> 'width|height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Position Top", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'top',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			/*array(
				'name'  		=> __("Position Left", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'slug' 			=> 'remove_pleft',
				'property' 		=> 'left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),*/
			array(
				'name'  		=> __("Position Right", "oxyultimate-woo"),
				'selector' 		=> $selector,
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

		$itemPrice = $cartItem->addControlSection( "prd_price" ,__('Price'), "assets/icon.png", $this );
		$itemPrice->addStyleControls([
			array(
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'margin-top',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name' 			=> __('Gap Between Label & Price'),
				'selector' 		=> '.price-label .woocommerce-Price-amount',
				'property' 		=> 'margin-left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Label Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'color',
				'slug' 			=> 'ouocc_prdplblc'
			),
			array(
				'name'  		=> __("Label Font Size", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'font-size',
				'slug' 			=> 'ouocc_prdplblfs'
			),
			array(
				'name'  		=> __("Label Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'font-weight',
				'slug' 			=> 'ouocc_prdplblfw'
			),
			array(
				'name'  		=> __("Price Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label .woocommerce-Price-amount',
				'property' 		=> 'color',
				'slug' 			=> 'ouocc_prdpc'
			),
			array(
				'name'  		=> __("Price Font Size", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label .woocommerce-Price-amount',
				'property' 		=> 'font-size',
				'slug' 			=> 'ouocc_prdpfs'
			),
			array(
				'name'  		=> __("Price Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label .woocommerce-Price-amount',
				'property' 		=> 'font-weight',
				'slug' 			=> 'ouocc_prdpfw'
			),
			array(
				'name'  		=> __("Text Transform", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'text-transform'
			)
		]);

		$cartItem->typographySection( __('Total Price', "oxyultimate-woo"), ".item-total-price, .item-total-price .woocommerce-Price-amount", $this );

		//* Quantity Box
		$qty = $this->addControlSection( "prd_quantity", __('Quantity', "oxyultimate-woo"), "assets/icon.png", $this );
		$qty->addStyleControls([
			array(
				'name' 			=> __('Wrapper Width'),
				'selector' 		=> ' ',
				'property' 		=> '--qty-box-width',
				'control_type' 	=> 'slider-measurebox',
				'default' 		=> 85,
				'min' 			=> 0,
				'max' 			=> 400,
				'unit' 			=> 'px'
			),
			array(
				'name'  		=> __("Height", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty, .ouocc-qty-plus',
				'property' 		=> 'height',
				'control_type' 	=> 'slider-measurebox',
				'default' 		=> 25,
				'unit' 			=> 'px'
			)
		]);

		$qtyinp = $qty->addControlSection( "qty_input", __('Input Field', "oxyultimate-woo"), "assets/icon.png", $this );
		$qtyinp->addStyleControls([
			array(
				'name' 			=> __('Background Color'),
				'selector' 		=> '.ouocc-qty',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Color'),
				'selector' 		=> '.ouocc-qty',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Font Size", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty',
				'property' 		=> 'font-size'
			)
		]);

		$qtypm = $qty->addControlSection( "qty_pm", __('+/- Button', "oxyultimate-woo"), "assets/icon.png", $this );
		$qtypm->addStyleControls([
			array(
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'default' 		=> 50,
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'background-color'
			),
			array(
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'color'
			),
			array(
				'name' 			=> __('Background Color on Hover'),
				'selector' 		=> '.ouocc-qty-minus:hover, .ouocc-qty-plus:hover',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Color on Hover'),
				'selector' 		=> '.ouocc-qty-minus:hover, .ouocc-qty-plus:hover',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Font Size", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'font-size'
			),
			array(
				'name'  		=> __("Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'font-weight'
			)
		]);
	}


	/******************************* Promo Code ********************************/
	function cartCouponControlSection() {
		$coupon = $this->addControlSection( 'cart_coupon', __('Coupon Form', "oxyultimate-woo"), "assets/icon.png", $this );

		//Enable coupon
		$formLogic = $coupon->addControl('buttons-list', 'coupon_form_enable', __('Display Coupon Submission Form'));
		$formLogic->setValue(['No', 'Yes']);
		$formLogic->setDefaultValue('No');
		$formLogic->setValueCSS(['Yes'  => '.coupon-code-wrap{display:flex}.oucc-coupon-row,.order-total-row{display: grid;}']);
		$formLogic->whiteList();

		$general = $coupon->addControlSection( 'general_cf', __('General', "oxyultimate-woo"), "assets/icon.png", $this );
		$formDisplay = $general->addControl("buttons-list", "cform_display", __("Stack") );
		$formDisplay->setValue([ 'Horizontally', 'Vertically']);
		$formDisplay->setValueCSS([
			'Vertically' 	=> ".ou-cart-items .coupon-code-wrap{flex-direction: column;}
							.coupon-code-wrap .ouocc-coupon-field {margin-bottom: var(--gap-cf-fields);}",
			'Horizontally'	=> ".ou-cart-items .coupon-code-wrap{flex-direction: row}
							.coupon-code-wrap .ouocc-coupon-field {margin-right: var(--gap-cf-fields);}"
		]);
		$formDisplay->setDefaultValue('Horizontally');
		$formDisplay->whiteList();

		$general->addStyleControls([
			array(
				'name' 			=> __('Gap Between Field & Button'),
				'selector' 		=> ' ',
				'property' 		=> '--gap-cf-fields',
				'control_type' 	=> 'slider-measurebox',
				'min' 			=> '0',
				'max' 			=> '20',
				'step' 			=> '1',
				'unit' 			=> 'px'
			),
			array(
				'name' 			=> __('Wrapper Background Color'),
				'selector' 		=> '.coupon-code-wrap',
				'property' 		=> 'background-color'
			)
		]);

		$spacing = $coupon->addControlSection( 'ccwrap_spacing', __('Wrapper Spacing', "oxyultimate-woo"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"cc_padding",
			__("Padding"),
			'.coupon-code-wrap'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cc_margin",
			__("Margin"),
			'.coupon-code-wrap'
		)->whiteList();

		$coupon->borderSection( __('Wrapper Border', "oxyultimate-woo"), '.coupon-code-wrap', $this );


		$padding = $coupon->addControlSection( 'field_padding', __('Fields Padding', "oxyultimate-woo"), "assets/icon.png", $this );
		$padding->addPreset(
			"padding",
			"field_padding",
			__("Padding for Field & Button"),
			'.ouocc-coupon-field,.coupon-btn'
		)->whiteList();


		$coupon->typographySection( __('Input Font', "oxyultimate-woo"), '.ouocc-coupon-field', $this );
		$input = $coupon->addControlSection( 'input_style', __('Input Style', "oxyultimate-woo"), "assets/icon.png", $this );
		$input->addStyleControls([
			array(
				'selector' 		=> '.ouocc-coupon-field',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'min' 			=> '0',
				'max' 			=> '150',
				'step' 			=> '5',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> '.ouocc-coupon-field',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Background Color on Focus'),
				'selector' 		=> '.ouocc-coupon-field:focus',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Color on Focus'),
				'selector' 		=> '.ouocc-coupon-field:hover',
				'property' 		=> 'color'
			),
			array(
				'name' 			=>  __('Placeholder Color'),
				'selector' 		=> '.coupon-code-wrap input::placeholder',
				'property' 		=> 'color'
			)
		]);

		$coupon->borderSection( __('Input Border', "oxyultimate-woo"), '.ouocc-coupon-field', $this );
		$coupon->borderSection( __('Input Focus Border', "oxyultimate-woo"), '.ouocc-coupon-field:focus', $this );

		$coupon->typographySection( __('Button Font', "oxyultimate-woo"), '.coupon-btn', $this );

		$button = $coupon->addControlSection( 'button_style', __('Button Style', "oxyultimate-woo"), "assets/icon.png", $this );
		$button->addStyleControls([
			array(
				'selector' 		=> '.coupon-btn',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'min' 			=> '0',
				'max' 			=> '150',
				'step' 			=> '5',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> '.coupon-btn',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Background Color on Hover'),
				'selector' 		=> '.coupon-btn:hover',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Color on Hover'),
				'selector' 		=> '.coupon-btn:hover',
				'property' 		=> 'color'
			),
		]);

		$coupon->borderSection( __('Button Border', "oxyultimate-woo"), '.coupon-btn', $this );
		$coupon->borderSection( __('Button Hover Border', "oxyultimate-woo"), '.coupon-btn:hover', $this );

		$coupon->boxShadowSection( __('Button Shadow', "oxyultimate-woo"), '.coupon-btn', $this );
		$coupon->boxShadowSection( __('Button Hover Shadow', "oxyultimate-woo"), '.coupon-btn:hover', $this );
	}

	/******************************* Sub Total ********************************/
	function cartSubtotalControlSection() {
		$subTotal = $this->addControlSection( 'sub_total', __('Sub Total', "oxyultimate-woo"), "assets/icon.png", $this );

		//Disable it
		$sbrow = $subTotal->addControl('buttons-list', 'disable_sbrow', __('Disable Subtotal Row'));
		$sbrow->setValue(['No', 'Yes']);
		$sbrow->setDefaultValue('No');
		$sbrow->setValueCSS(['Yes'  => '.subtotal-wrap{display:none}']);		

		$subTotal->addStyleControl(
			array(
				'selector'		=> '.subtotal-wrap',
				'property' 		=> 'background-color'
			)
		);

		$subTotal->typographySection( __('Typography'), ".subtotal-wrap, .subtotal-wrap > strong, .subtotal-wrap .woocommerce-mini-cart__total strong", $this );
		
		$price = $subTotal->addControlSection( 'sub_total_price', __('Price', "woocommerce"), "assets/icon.png", $this );
		$price->addStyleControls([
			array(
				'name' 			=> __('Color'),
				'selector'		=> '.total .woocommerce-Price-amount',
				'property' 		=> 'color',
				'slug' 			=> 'price_clr'
			),
			array(
				'name' 			=> __('Font Size'),
				'selector'		=> '.total .woocommerce-Price-amount',
				'property' 		=> 'font-size',
				'slug' 			=> 'price_fs'
			),
		]);
		$priceAlignment = $price->addControl( 'buttons-list', 'ouocc_priceAlign', __( 'Price Alignment', "oxyultimate-woo" ));
		$priceAlignment->setValue( array( "Default", "Right" ) );
		$priceAlignment->setValueCSS( array(
			"Default" => ".total .woocommerce-Price-amount {float: none;}",
			"Right" => ".total .woocommerce-Price-amount {float: right;}"
		));
		$priceAlignment->setDefaultValue("Right");
		$priceAlignment->whiteList();

		$spacing = $subTotal->addControlSection( 'subtotal_sp', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"stsp_padding",
			__("Padding"),
			'.subtotal-wrap'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"stsp_margin",
			__("Margin"),
			'.subtotal-wrap'
		)->whiteList();

		$subTotal->borderSection( __('Border', "oxyultimate-woo"), '.subtotal-wrap', $this );
	}

	/******************************* Coupon Details ********************************/
	function cartCouponDetailsControlSection() {
		$coupons = $this->addControlSection( 'cart_coupons', __('Coupon Details', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.oucc-coupon-row';

		//Disable it
		$cprow = $coupons->addControl('buttons-list', 'disable_cprow', __('Disable Coupon Details Row'));
		$cprow->setValue(['No', 'Yes']);
		$cprow->setDefaultValue('No');
		$cprow->setValueCSS(['Yes'  => $selector . '{display:none}']);

		$spacing = $coupons->addControlSection( 'ccs_spacing', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"ccsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"ccsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$coupons->addStyleControl(
			array(
				'selector'		=> $selector,
				'property' 		=> 'background-color'
			)
		);

		$coupons->typographySection( __('Coupon Text', "oxyultimate-woo"), '.oucc-coupon-label', $this );
		$coupons->typographySection( __('Coupon Price', "oxyultimate-woo"), '.oucc-discount-price, .oucc-discount-price .woocommerce-Price-amount', $this );

		$delete = $coupons->addControlSection( 'ccs_delete', __('Delete Icon', "oxyultimate-woo"), "assets/icon.png", $this );
		$selector = 'a.ouocc-remove-coupon svg';
		$delete->addStyleControls([
			array(
				'name' 			=> __('Size'),
				'selector' 		=> $selector,
				'property' 		=> 'width|height',
				'control_type' 	=> 'slider-measurebox',
				'max' 			=> 100,
				'unit' 			=> 'px'
			),
			array(
				'selector'		=> $selector,
				'property' 		=> 'color'
			),
			array(
				'name' 			=> __('Color on Hover'),
				'selector'		=> 'a.ouocc-remove-coupon:hover svg',
				'property' 		=> 'color'
			)
		]);
	}

	/******************************* Total ********************************/
	function cartTotalControlSection() {
		$total = $this->addControlSection( 'cart_total', __('New Total Row', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.order-total-row';

		//Disable it
		$ntrow = $total->addControl('buttons-list', 'disable_ntrow', __('Disable New Total Row'));
		$ntrow->setValue(['No', 'Yes']);
		$ntrow->setDefaultValue('No');
		$ntrow->setValueCSS(['Yes'  => $selector . '{display:none}']);

		$total->addStyleControl(
			array(
				'selector'		=> $selector,
				'property' 		=> 'background-color'
			)
		);

		$spacing = $total->addControlSection( 'ottl_spacing', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"ottl_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"ottl_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$total->typographySection(__('Total Label'), $selector . ' .order-total-label', $this );
		$total->typographySection(__('Total Price'), $selector . ' .order-total-price, .order-total-price .woocommerce-Price-amount', $this );

		$total->borderSection(__('Border'), $selector, $this );
	}


	function cartButtonsControlSection() {
		$btnStructure = $this->addControlSection( "mincart_btns", __('Buttons', "oxyultimate-woo"), "assets/icon.png", $this );

		//Disable it
		$hidebtns = $btnStructure->addControl('buttons-list', 'disable_ntrow', __('Remove Both Buttons'));
		$hidebtns->setValue(['No', 'Yes']);
		$hidebtns->setDefaultValue('No');
		$hidebtns->setValueCSS(['Yes'  => '.woocommerce-mini-cart__buttons{display:none}']);

		/*****************************
		 * Wrapper Config
		 ****************************/
		$btnWrapper = $btnStructure->addControlSection( "btn_wrapper", __('Wrapper Config', "oxyultimate-woo"), "assets/icon.png", $this );

		$btnWrapper->addStyleControl(
			array(
				'selector'		=> '.woocommerce-mini-cart__buttons',
				'property' 		=> 'background-color'
			)
		);

		$btnWrapper->addPreset(
			"padding",
			"btncontainer_padding",
			__("Padding"),
			'.woocommerce-mini-cart__buttons'
		)->whiteList();

		$btnWrapper->addPreset(
			"margin",
			"btncontainer_margin",
			__("Margin"),
			'.woocommerce-mini-cart__buttons'
		)->whiteList();


		/*****************************
		 * Wrapper Border
		 ****************************/
		$btnStructure->borderSection( __( "Wrapper Border", "oxyultimate-woo" ), '.woocommerce-mini-cart__buttons', $this );


		/*****************************
		 * Buttons Config
		 ****************************/
		$btnconfig = $btnStructure->addControlSection( "btns_config", __('Buttons Config', "oxyultimate-woo"), "assets/icon.png", $this );

		$btnDisplay = $btnconfig->addControl("buttons-list", "btn_display", __("Stack") );
		$btnDisplay->setValue([ 'Horizontally', 'Vertically']);
		$btnDisplay->setValueCSS([
			'Vertically' 	=> ".woocommerce-mini-cart__buttons{flex-direction: column;}
							.woocommerce-mini-cart__buttons a.checkout {margin-top: 10px;margin-left: 0;}",
			'Horizontally'	=> ".woocommerce-mini-cart__buttons{flex-direction: row}
							.woocommerce-mini-cart__buttons a.checkout {margin-top: 0px;margin-left: 10px;}"
		]);
		$btnDisplay->setDefaultValue('Vertically');
		$btnDisplay->whiteList();

		$btnAlign = $btnconfig->addControl("buttons-list", "btn_align", __("Alignment") );
		$btnAlign->setValue(['Left', 'Center', 'Right']);

		$btnAlign->setValueCSS([
			'Left' 		=> ".woocommerce-mini-cart__buttons{align-items: start}",
			'Center'	=> ".woocommerce-mini-cart__buttons{align-items: center}",
			'Right' 	=> ".woocommerce-mini-cart__buttons{align-items: flex-end}"
		]);
		$btnAlign->setDefaultValue('Center');
		$btnAlign->whiteList();

		$btnconfig->addStyleControl([
			'selector' 		=> '.woocommerce-mini-cart__buttons a.button',
			'property' 		=> 'transition-duration',
			'control_type'	=> 'slider-measurebox'
		])->setUnits('s','sec')->setRange(0, 5, 0.1)->setDefaultValue(0.2);

		$btnconfig->addPreset(
			"padding",
			"mctns_padding",
			__("Padding of Both Buttons"),
			'.woocommerce-mini-cart__buttons a.button'
		)->whiteList();
	}

	function viewCartButtonControlSection() {
		$viewCart = $this->addControlSection( 'btn_viewcart', __('View Cart', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.woocommerce-mini-cart__buttons a:first-child';

		$hideVC = $viewCart->addControl('buttons-list', 'viewcart_hide', __('Hide View Cart Button?'));
		$hideVC->setValue(['No', 'Yes']);
		$hideVC->setValueCSS(['Yes' => $selector . '{display:none}' ]);
		$hideVC->setDefaultValue('No');
		$hideVC->whiteList();

		$viewCart->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox'
			)
		)
		->setRange('0', '500', 20)
		->setUnits('%', 'px,em,%,auto')
		->setDefaultValue(100);

		$spacing = $viewCart->addControlSection('viewcart_btn_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"vcbtn_margin",
			__("Margin"),
			$selector
		)->whiteList();
		
		$vcTg = $viewCart->typographySection( __('Font & Colors', "oxyultimate-woo"), $selector, $this );

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $selector . ':hover',
				"property" 			=> 'color'
			)
		);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_end', true);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $selector . ':hover',
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_start', true);

		$viewCart->borderSection( __( "Border", "oxyultimate-woo" ), $selector, $this );
		$viewCart->borderSection( __( "Hover Border", "oxyultimate-woo" ), $selector . ':hover', $this );

		$viewCart->boxShadowSection( __("Box Shadow"), $selector, $this );
		$viewCart->boxShadowSection( __("Hover Shadow"), $selector . ':hover', $this );
	}
	
	function checkoutCartButtonControlSection() {
		$checkout = $this->addControlSection( 'btn_checkout', __('Checkout', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.woocommerce-mini-cart__buttons a.checkout';

		$hideCB = $checkout->addControl('buttons-list', 'checkoutbtn_hide', __('Hide Checkout Button?'));
		$hideCB->setValue(['No', 'Yes']);
		$hideCB->setValueCSS(['Yes' => $selector . '{display:none}' ]);
		$hideCB->setDefaultValue('No');
		$hideCB->whiteList();

		$checkout->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox'
			)
		)
		->setRange('0', '500', 20)
		->setUnits('%', 'px,em,%,auto')
		->setDefaultValue(100);

		$spacing = $checkout->addControlSection('checkout_btn_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"checkoutbtn_margin",
			__("Margin"),
			'.ou-cart-items .woocommerce-mini-cart__buttons > a.checkout'
		)->whiteList();

		$cTg = $checkout->typographySection( __('Font & Colors', "oxyultimate-woo"), $selector, $this );
		$cTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $selector . ':hover',
				"property" 			=> 'color'
			)
		);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_end', true);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $selector . ':hover',
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_start', true);

		$checkout->borderSection( __( "Border", "oxyultimate-woo" ), $selector, $this );
		$checkout->borderSection( __( "Hover Border", "oxyultimate-woo" ), $selector . ':hover', $this );

		$checkout->boxShadowSection( __("Box Shadow"), $selector, $this );
		$checkout->boxShadowSection( __("Hover Shadow"), $selector . ':hover', $this );
	}

	/******************************* Notices ********************************/
	function cartMessageControl() {
		$message = $this->addControlSection( 'cart_msg', __('Notices', "oxyultimate-woo"), "assets/icon.png", $this );

		$alertDisable = $message->addControl('buttons-list', 'hide_notices', __('Disable Alert Message'));
		$alertDisable->setValue(['No', 'Yes']);
		$alertDisable->setValueCSS(['Yes' => '.oucc-wc-notice{display:none!important}']);
		$alertDisable->setDefaultValue('No');

		$message->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Live preview is not available on Builder Editor. Enter &amp;apos; for single quote.</div>'), 
			'msg_desc'
		)->setParam('heading', 'Note:');

		$message->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Add Notice'),
			'slug' 		=> 'notice_add',
			'default' 	=> __('Item added')
		]);

		$message->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Update Notice'),
			'slug' 		=> 'notice_update',
			'default' 	=> __('Item updated')
		]);

		$message->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Remove Notice'),
			'slug' 		=> 'notice_remove',
			'default' 	=> __('Item removed')
		]);

		$message->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Out of Stock Notice'),
			'slug' 		=> 'max_qty_msg',
			'default' 	=> __('No more products on stock'),
			'base64' 	=> true
		]);

		$message->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Wrong Input Notice'),
			'slug' 		=> 'min_qty_msg',
			'default' 	=> __('You entered wrong value.'),
			'base64' 	=> true
		]);

		$message->addStyleControl(
			array(
				'selector'		=> '.oucc-wc-notice',
				'property' 		=> 'background-color'
			)
		);

		$spacing = $message->addControlSection( 'cartmsg_spacing', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"cartmsg_padding",
			__("Padding"),
			'.oucc-wc-notice'
		)->whiteList();

		$message->typographySection( __('Typography'), ".wc-notice-text", $this );
	}

	function controls() {

		$this->cartItems();

		$this->cartSubtotalControlSection();

		$this->cartCouponControlSection();

		$this->cartCouponDetailsControlSection();

		$this->cartTotalControlSection();

		$this->cartButtonsControlSection();

		$this->viewCartButtonControlSection();

		$this->checkoutCartButtonControlSection();

		$this->cartMessageControl();
	}

	function render( $options, $defaults, $content ) {
		$class= '';

		if( isset($options['hide_if_empty_msg']) && $options['hide_if_empty_msg'] == 'yes' )
			$class = ' ou-hide-cart-if-empty';

		$previewClass = ( $this->isBuilderEditorActive() == true ) ? ' preview-empty-msg' : '';

		$dataAttr = '';

		if( ! is_cart() ) {
			$dataAttr .= ' data-coupon-nonce="' . wp_create_nonce('apply-coupon') . '" data-remove-coupon-nonce="' . wp_create_nonce( 'remove-coupon' ) .'"'; 
		}

		//$dataAttr .= ' data-maxqtymsg="' . ( isset($options['max_qty_msg']) ? esc_html( $options['max_qty_msg'] ) : __('No more products on stock')) . '"';
		//$dataAttr .= ' data-minqtymsg="' . ( isset($options['min_qty_msg']) ? esc_html( $options['min_qty_msg'] ) : __('You entered wrong value.')) . '"';

		if( $content ) {
			echo '<div class="ou-empty-cart-message oxy-inner-content'.$previewClass.'">';

			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );

			echo '</div>';
		}

		echo '	<div class="ou-cart-items oucart-coupon'. $class .'"' . $dataAttr .'>
					<div class="oucc-wc-notice">
						<span class="wc-notice-text message-add ouocc-hide-msg">' . $options['notice_add'] . '</span>
						<span class="wc-notice-text message-update ouocc-hide-msg">' . $options['notice_update'] . '</span>
						<span class="wc-notice-text message-remove ouocc-hide-msg">' . $options['notice_remove'] . '</span>
						<span class="wc-notice-text message-maxqty ouocc-hide-msg">' . $options['max_qty_msg'] . '</span>
						<span class="wc-notice-text message-minqty ouocc-hide-msg">' . $options['min_qty_msg'] . '</span>
						<span class="wc-notice-text message-error ouocc-hide-msg"></span>
					</div>
					<div class="widget_shopping_cart_content">'; 
						woocommerce_mini_cart();
		echo '		</div>
				</div>';

		$js = "jQuery(document).ready(function($){
				if ( Cookies.get( 'woocommerce_items_in_cart' ) > 0 ) {
					$('.oxy-ou-cart').find( '.ou-hide-cart-if-empty' ).show();
					$('.oxy-ou-cart').find('.ou-empty-cart-message').hide();
				} else {
					$('.oxy-ou-cart').find('.ou-empty-cart-message').show();
					$('.oxy-ou-cart').find( '.ou-hide-cart-if-empty' ).hide();
					$('.oxy-ou-cart').find( '.ou-hide-cart-if-empty .woocommerce-mini-cart__empty-message' ).remove();
				}

				$( document.body ).on( 'adding_to_cart', function() {
					$('.oxy-ou-cart').find( '.ou-hide-cart-if-empty' ).show();
					$('.oxy-ou-cart').find('.ou-empty-cart-message').hide();
				});

				$( document ).on( 'removed_from_cart', function(){
					if ( Cookies.get( 'woocommerce_items_in_cart' ) > 0 ) {
						$('.oxy-ou-cart').find( '.ou-hide-cart-if-empty' ).show();
						$('.oxy-ou-cart').find('.ou-empty-cart-message').hide();
					} else {
						$('.oxy-ou-cart').find('.ou-empty-cart-message').show();
						$('.oxy-ou-cart').find( '.ou-hide-cart-if-empty' ).hide();
						$('.oxy-ou-cart').find( '.ou-hide-cart-if-empty .woocommerce-mini-cart__empty-message' ).remove();

						if( $('.oxy-ou-billing-form-wrap').length > 0 )
							window.location.reload();
					}
				});
			});";

		if ( $this->isBuilderEditorActive() )  {
			$this->El->builderInlineJS($js);
		} else {
			$this->El->footerJS($js);
		}

		wp_enqueue_script('ou-occ-script');
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}
		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooCart();