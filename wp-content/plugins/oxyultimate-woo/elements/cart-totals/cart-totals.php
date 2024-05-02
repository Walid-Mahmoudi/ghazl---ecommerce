<?php

class OUWooCartTotals extends UltimateWooEl {
	public $css_added = false;
	public $checkout_text = '';
	public $shop_button_text = '';
	public $shop_button_url = '';

	function name() {
		return __( "Cart Totals", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_cart_totals";
	}

	function ouwoo_button_place() {
		return "cart";
	}

	function button_priority() {
		return 4;
	}

	function custom_init() {
		add_filter("oxygen_vsb_element_presets_defaults", array( $this, "ouwoo_cart_totals_presets_defaults" ) );
	}

	function ouwoo_cart_totals_presets_defaults( $all_elements_defaults ) {
		require("cart-totals-presets.php");

		$all_elements_defaults = array_merge($all_elements_defaults, $cartTotals);

		return $all_elements_defaults;
	}

	/******************************
	 * Cart Total Config
	 ******************************/
	function cartTotalConfig() {

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Conditionally Hide Shipping Methods', "oxyultimate-woo"),
			'slug' 		=> 'hide_shipping_methods',
			'value' 	=> [
				'none' 				=> __('Show All Methods',"oxyultimate-woo"),
				'hide_all' 			=> __('Only Show Free Shipping', "oxyultimate-woo"),
				'hide_except_local' => __('Show Free Shipping & Local Pickup', "oxyultimate-woo"),
				'hide_except_states' => __('Show Free Shipping except following states', "oxyultimate-woo")
			],
			'default' 	=> 'none'
		])->setParam('description', __('It automatically hides all other shipping methods when "Free shipping" is available during checkout.', "oxyultimate-woo"));

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Enter States Alpha-2 Code', "oxyultimate-woo"),
			'slug' 		=> 'states_list',
			'condition' => 'hide_shipping_methods=hide_except_states'
		])->setParam('description', __('e,g: AK,HI,GU,PR', "oxyultimate-woo"));

		$this->addCustomControl(
			'<div class="oxygen-control-description">'.
			__('You would clear your WooCommerce cache. Go to <strong>WooCommerce > System Status > Tools > WooCommerce Transients > Clear transients</strong>', "oxyultimate-woo")
			. '</div>', 
			'info'
		)->setParam('heading', __("Note for Above Option:", "oxyultimate-woo"));

		$this->addCustomControl(
			'<hr style="color: #f4f4f4;height: 1px" noshade/>', 
			'divider'
		);

		$selector = '.cart_totals';

		$config = $this->addControlSection('cart_totals_config', __('Outer Wrapper', "oxyultimate-woo"), 'assets/icon.png', $this );

		$config->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setRange(0, 1000, 10);

		$config->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $config->addControlSection('carttotalbox_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"cttlbox_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cttlbox_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Border
		$config->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$config->boxShadowSection(__('Box Shadow'), $selector, $this );
	}


	/******************************
	 * Cart Totals Heading
	 ******************************/
	function cartTotalHeading() {

		$selector = '.cart_totals > h2';

		$heading = $this->addControlSection('cart_totals_heading', __('Cart Totals Text', "oxyultimate-woo"), 'assets/icon.png', $this );

		$hide_heading = $heading->addControl('buttons-list', 'hide_heading', __('Hide Heading?', "oxyultimate-woo"));
		$hide_heading->setValue(['No', 'Yes']);
		$hide_heading->setValueCSS(['Yes' => $selector .'{display:none}' ]);
		$hide_heading->setDefaultValue('No');
		$hide_heading->setParam('description', __('If you want to add the custom text, you will hide this heading and add the custom one via Heading or any other component.', "oxyultimate-woo"));
		$hide_heading->whiteList();

		$heading->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setRange(0, 1000, 10);

		$heading->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $heading->addControlSection('heading_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"heading_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"heading_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$heading->typographySection(__('Typography'), $selector, $this );

		//* Border
		$heading->borderSection(__('Border'), $selector, $this );
	}


	/******************************
	 * Cart Total Table
	 ******************************/
	function cartTotalTable() {
		$selector = 'table.shop_table';

		$table = $this->addControlSection('totals_table', __('Table Content', "oxyultimate-woo"), 'assets/icon.png', $this );

		$table->addStyleControl(
			array(
				'name' 				=> __('1st Column Width', "oxyultimate-woo"),
				"selector" 			=> $selector . ' tr th',
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox'
			)
		)
		->setRange('0', '500', 20)
		->setUnits('px', 'px,em,%,auto');

		$bg = $table->addControlSection('table_bgcolor', __('Background Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$bg->addStyleControls([
			[
				'name' 			=> __('Background Color of Whole Table', "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Subtotal Row', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tr.cart-subtotal',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Coupon Row', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tr.cart-discount',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Shipping Row', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tr.woocommerce-shipping-totals',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Fees Row', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tr.fee',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Tax Row', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tr.tax-total, ' . $selector . ' tr.tax-rate',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Total Row', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tr.order-total, '. $selector . ' tr.order-total th,' . $selector . ' tr.order-total td',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Labels', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table th',
				'property' 		=> 'background-color'
			]
		]);

		$spacing = $table->addControlSection('table_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"cell_padding",
			__("Cell Padding"),
			$selector . ' td, ' . $selector . ' tfoot td,' . $selector . ' tfoot th,' . $selector . ' th'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"table_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Label Font
		$labelfont = $table->typographySection(__('Label Typography', "oxyultimate-woo"), 'table.shop_table th,table.shop_table_responsive tr td::before, table.shop_table_responsive tr td::before', $this );

		//* Price Font
		$price = $table->typographySection(__('Price Typography', "oxyultimate-woo"), 'table.shop_table td, table.shop_table td .woocommerce-Price-amount', $this );
		$price->addStyleControls([
			[
				'name' 			=> __('Coupon Remove Link Color', "oxyultimate-woo"),
				'selector' 		=> '.cart-discount .woocommerce-remove-coupon',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Coupon Remove Link Hover Color', "oxyultimate-woo"),
				'selector' 		=> '.cart-discount .woocommerce-remove-coupon:hover',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Coupon Remove Link Font Size', "oxyultimate-woo"),
				'selector' 		=> '.cart-discount .woocommerce-remove-coupon',
				'property' 		=> 'font-size'
			],
			[
				'name' 			=> __('Coupon Remove Link Font Weight', "oxyultimate-woo"),
				'selector' 		=> '.cart-discount .woocommerce-remove-coupon',
				'property' 		=> 'font-weight'
			],
		]);

		//* Order Total Row
		$total = $table->addControlSection('order_total', __('Order Total Row', "oxyultimate-woo"), 'assets/icon.png', $this );
		$total->addStyleControls([
			[
				'name' 			=> __('Label Color', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table tr.order-total th',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Label Font Size', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table tr.order-total th',
				'property' 		=> 'font-size'
			],
			[
				'name' 			=> __('Label Font Weight', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table tr.order-total th',
				'property' 		=> 'font-weight'
			],
			[
				'name' 			=> __('Price Color', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table tr.order-total .woocommerce-Price-amount',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Price Font Size', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table tr.order-total .woocommerce-Price-amount',
				'property' 		=> 'font-size'
			],
			[
				'name' 			=> __('Price Font Weight', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table tr.order-total td strong, table.shop_table tr.order-total .woocommerce-Price-amount',
				'property' 		=> 'font-weight'
			]
		]);
		
		//* Borders
		$borderTop = $table->addControlSection('cellborder_top', __('Cell Top Border', "oxyultimate-woo"), 'assets/icon.png', $this );

		$borderTop->addStyleControls([
			[
				'selector' 		=> 'table.shop_table tr, table.shop_table tbody th, table.shop_table tfoot td, table.shop_table tfoot th',
				'property' 		=> 'border-top-color'
			],
			[
				'selector' 		=> 'table.shop_table tr, table.shop_table tbody th, table.shop_table tfoot td, table.shop_table tfoot th',
				'property' 		=> 'border-top-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'default' 		=> '1'
			]
		]);

		$borderRight = $table->addControlSection('cellborder_right', __('Cell Right Border', "oxyultimate-woo"), 'assets/icon.png', $this );

		$borderRight->addStyleControls([
			[
				'selector' 		=> 'table.shop_table tbody th, table.shop_table tfoot th',
				'property' 		=> 'border-right-color'
			],
			[
				'selector' 		=> 'table.shop_table tbody th, table.shop_table tfoot th',
				'property' 		=> 'border-right-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			]
		]);

		//* Border
		$table->borderSection(__('Outer Border', "oxyultimate-woo"), $selector, $this );

		//* Box Shadow
		$table->boxShadowSection(__('Box Shadow'), $selector, $this );
	}



	/******************************
	 * Cart Shipping Method
	 ******************************/
	function cartShippingMethod() {
		$method = $this->addControlSection( 'radio_buttons', __('Radio Buttons', "oxyultimate-woo"), 'assets/icon.png', $this );

		$radio = $method->addControlSection( 'rb_size_color', __('Size & Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$radio_selector = '#shipping_method input[type=radio]';
		$radio->addStyleControl(
			[
				'name' 			=> __('Size'),
				'control_type' 	=> 'slider-measurebox',
				'selector' 		=> $radio_selector,
				'property' 		=> 'width|height'
			]
		)->setUnits('px', 'px')->setRange(0,100,1)->setDefaultValue(24);

		$radio->addStyleControls([
			[
				'selector' 		=> $radio_selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Checked Radio Button Color', "oxyultimate-woo"),
				'selector' 		=> '#shipping_method input[type=radio]:checked',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Checked Radio Button Alt Color', "oxyultimate-woo"),
				'selector' 		=> '#shipping_method',
				'property' 		=> '--checked-radio-alt-color',
				'control_type' 	=> 'colorpicker',
			],
			[
				'name' 			=> __('Checked Radio Button Border Color', "oxyultimate-woo"),
				'selector' 		=> '#shipping_method input[type=radio]:checked',
				'property' 		=> 'border-color'
			]
		]);

		$radio->addStyleControl(
			[
				'name' 			=> __('Checked Radio Button Bullet Size', "oxyultimate-woo"),
				'control_type' 	=> 'slider-measurebox',
				'selector' 		=> '#shipping_method',
				'property' 		=> '--checked-bullet-size'
			]
		)->setUnits('px', 'px')->setRange(0,30,1)->setDefaultValue(4);

		$spacing = $method->addControlSection('rb_sp', __('Align / Gap', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"rb_margin",
			__("Margin"),
			'ul#shipping_method li input'
		)->whiteList();

		$spacing->addStyleControl(
			[
				'name' 			=> __('Gap Between Buttons', "oxyultimate-woo"),
				'control_type' 	=> 'slider-measurebox',
				'selector' 		=> 'ul#shipping_method li',
				'property' 		=> 'margin-bottom'
			]
		)->setUnits('px', 'px,em,%')->setRange(0,100,1);

		$method->borderSection( __('Border'), $radio_selector, $this );

		$method->typographySection(__('Label Typography', "oxyultimate-woo"), '#shipping_method label', $this);
		$method->typographySection(__('Price Typography', "oxyultimate-woo"), '#shipping_method label .woocommerce-Price-amount', $this);
	}



	/******************************
	 * Cart Shipping Method
	 ******************************/
	function cartShippingForm() {
		$form = $this->addControlSection( 'shipping_form', __('Form', "oxyultimate-woo"), 'assets/icon.png', $this );

		$form->addOptionControl(
			[
				'type' 		=> 'radio',
				'name' 		=> __('Show Preview of Shipping Form on Builder Editor', "oxyultimate-woo"),
				'slug' 		=> 'preview_form',
				'value' 	=> [ "no" => __('No'), "yes" => __('Yes')],
				'default' 	=> 'no'
			]
		)->rebuildElementOnChange();

		$hide_address = $form->addControl('buttons-list', 'hide_ship_address', __('Hide Shipping Options Message?', "oxyultimate-woo"));
		$hide_address->setValue(['No', 'Yes']);
		$hide_address->setValueCSS(['Yes' => '.woocommerce-shipping-destination{display:none}']);
		$hide_address->setDefaultValue('No');
		$hide_address->whiteList();

		$form->typographySection(__('Shipping Info Font', "oxyultimate-woo"), '.woocommerce-shipping-destination', $this);


		$togglebtn = $form->typographySection(__('Form Toggle Link', "oxyultimate-woo"), '.shipping-calculator-button', $this );
		$togglebtn->addStyleControls([
			[
				'name' 			=> __('Space at Top'),
				'selector' 		=> '.shipping-calculator-button',
				'property' 		=> 'margin-top',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			],
			[
				'name' 			=> __('Space at Bottom', "oxyultimate-woo"),
				'selector' 		=> '.shipping-calculator-button',
				'property' 		=> 'margin-bottom',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			],
		]);


		//* Input Field
		$selector = '.shipping-calculator-form .input-text';
		$input = $form->addControlSection( 'form_text_field', __('Input Field', "oxyultimate-woo"), 'assets/icon.png', $this );
		$input->addStyleControls([
			[
				'selector' 		=> $selector . ', .shipping-calculator-form .select2-selection',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Background Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ':focus',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Text Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ':focus',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Placeholder Color', "oxyultimate-woo"),
				'selector' 		=> $selector . '::placeholder',
				'property' 		=> 'color'
			],
			[
				'selector' 		=> $selector . ', .shipping-calculator-form .select2-selection',
				'property' 		=> 'border-color'
			],
			[
				'name' 			=> __('Focus Border Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ':focus',
				'property' 		=> 'border-color'
			],
			[
				'selector' 		=> $selector . ', .shipping-calculator-form .select2-selection',
				'property' 		=> 'border-width'
			],
			[
				'selector' 		=> $selector . ', .shipping-calculator-form .select2-selection',
				'property' 		=> 'border-radius'
			]
		]);

		$form->typographySection(__('Input Text', "oxyultimate-woo"), $selector . ', .shipping-calculator-form .select2-selection', $this );

		$dropdown = $form->addControlSection( 'form_dorpdown', __('Dropdown', "oxyultimate-woo"), 'assets/icon.png', $this );
		$dropdown->addOptionControl([
			'name' 			=> __('Background Color of Selected Option', "oxyultimate-woo"),
			'slug' 			=> 'selected_bgcolor',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Option Background Color on Hover', "oxyultimate-woo"),
			'slug' 			=> 'selected_hbgcolor',
			'type' 			=> 'colorpicker'
		]);
		
		$dropdown->addOptionControl([
			'name' 			=> __('Selected Option Color', "oxyultimate-woo"),
			'slug' 			=> 'selected_color',
			'type' 			=> 'colorpicker'
		]);
		
		$dropdown->addOptionControl([
			'name' 			=> __('Option Color on Hover', "oxyultimate-woo"),
			'slug' 			=> 'highlighted_color',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Background Color of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_bgcolor',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Focus Background Color of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_fbgcolor',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Text Color of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_color',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Focus Text Color of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_fcolor',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Border Color of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_bordercolor',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Focus Border Color of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_focusbrdcolor',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Border Width of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_brdwidth',
			'type' 			=> 'slider-measurebox',
			'default' 		=> 1
		])->setUnits('px', 'px,%,em');

		$dropdown->addOptionControl([
			'name' 			=> __('Border Radius of Search Field', "oxyultimate-woo"),
			'slug' 			=> 'sfield_brdradius',
			'type' 			=> 'slider-measurebox',
			'default' 		=> 4,
		])->setUnits('px', 'px,%,em');

		//* Update Button
		$btnsize = $form->addControlSection( 'shipform_button', __('Button Size', "oxyultimate-woo"), 'assets/icon.png', $this );
		$selector = '.shipping-calculator-form button.button';
		$btnsize->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,auto,em')->setRange(0, 1000, 10);

		$btnsize->addPreset(
			"padding",
			"shipbtn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$btnsize->addPreset(
			"margin",
			"shipbtn_margin",
			__("Margin"),
			$selector
		)->whiteList();


		//* Typography
		$btncolor = $form->typographySection(__('Button Text', "oxyultimate-woo"), $selector, $this );
		$btncolor->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'color'
			]
		]);


		//* Border
		$form->borderSection(__('Button Border', "oxyultimate-woo"), $selector, $this );
		$form->borderSection(__('Button Hover Border', "oxyultimate-woo"), $selector . ':hover', $this );


		//* Box Shadow
		$form->boxShadowSection(__('Button Shadow', "oxyultimate-woo"), $selector, $this );
		$form->boxShadowSection(__('Button Hover Shadow', "oxyultimate-woo"), $selector . ':hover', $this );
	}



	/******************************
	 * Button Wrapper
	 ******************************/
	function cartTotalButtonWrap() {
		$config = $this->addControlSection('buttons_config', __('Buttons Config', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.wc-proceed-to-checkout';


		/*****************************
		 * Wrapper Config
		 ****************************/
		$container = $config->addControlSection('button_wrap', __('Wrapper Config', "oxyultimate-woo"), 'assets/icon.png', $this );
		$container->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);
		$container->addPreset(
			"padding",
			"btnwrap_padding",
			__("Padding"),
			$selector
		)->whiteList();
		$container->addPreset(
			"margin",
			"btnwrap_margin",
			__("Margin"),
			$selector
		)->whiteList();


		/*****************************
		 * Wrapper Border
		 ****************************/
		$config->borderSection( __( "Wrapper Border", "oxyultimate-woo" ), $selector, $this );


		/*****************************
		 * Buttons Config
		 ****************************/
		$buttons = $config->addControlSection('buttons_sp', __('Buttons Config', "oxyultimate-woo"), 'assets/icon.png', $this );

		$alignment = $buttons->addControl('buttons-list', 'buttons_alignment', __('Stack', "oxyultimate-woo"));
		$alignment->setValue(['Vertical', 'Horizontal']);
		$alignment->setValueCSS([
			'Horizontal' => $selector . '{flex-direction: row;}' .  $selector . ' a.shop-button{margin-right: 10px; margin-bottom: 0;}',
			'Vertical' => $selector . '{flex-direction: column;}' .  $selector . ' a.shop-button{margin-bottom: 10px; margin-right: 0;}',
		]);
		$alignment->setDefaultValue('Horizontal');
		$alignment->whiteList();

		$position = $buttons->addControl('buttons-list', 'button_align', __('Alignment', "oxyultimate-woo"));
		$position->setValue(['Left', 'Center', 'Right']);
		$position->setValueCSS([
			'Center' 	=> $selector . '{justify-content: center;}',
			'Left' 		=> $selector . '{justify-content: start;}',
			'Right' 	=> $selector . '{justify-content: flex-end;}',
		]);
		$position->setDefaultValue('Right');
		$position->whiteList();

		$buttons->addStyleControl([
			'selector' 		=> $selector . ' a.button',
			'property' 		=> 'transition-duration',
			'control_type'	=> 'slider-measurebox'
		])->setUnits('s','sec')->setRange(0, 5, 0.1)->setDefaultValue(0.2);

		$buttons->addPreset(
			"padding",
			"checkoutbtn_padding",
			__("Padding"),
			$selector . ' a.button'
		)->whiteList();
	}



	/******************************
	 * Checkout Button
	 ******************************/
	function cartTotalCheckoutButton() {
		$button = $this->addControlSection('checkout_btn', __('Checkout', "woocommerce"), 'assets/icon.png', $this );

		$selector = '.wc-proceed-to-checkout a.checkout-button';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text'),
			'slug' 		=> 'checkout_text'
			
		])->setParam('description', __('Click on Apply Params button and apply the change.', "oxyultimate-woo"));

		$button->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,rem')->setRange(0, 1000, 10);

		$spacing = $button->addControlSection('checkout_btn_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"checkoutbtn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$color = $button->addControlSection('checkout_btn_color', __('Color'), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'color'
			]
		]);

		//* Typography
		$button->typographySection(__('Typography'), $selector, $this );

		//* Border
		$button->borderSection(__('Border'), $selector, $this );
		$button->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ':hover', $this );

		//* Box Shadow
		$button->boxShadowSection( __('Box Shadow'), $selector, $this );
		$button->boxShadowSection( __('Hover Shadow', "oxyultimate-woo"), $selector . ':hover', $this );
	}


	/******************************
	 * Shop Button
	 ******************************/
	function cartTotalShopButton() {
		$shopButton = $this->addControlSection('shop_btn', __('Shop Button', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.cart_totals .wc-proceed-to-checkout > a.shop-button';

		$shopButton->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Add Continue Shopping Button?', "oxyultimate-woo"),
			'slug' 		=> 'shop_button',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no'
		])->rebuildElementOnChange();

		$shopButton->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text'),
			'slug' 		=> 'shop_text',
			'condition' => 'shop_button=yes',
			'placeholder' => "Continue Shopping"
		])->setParam('description', __('Click on Apply Params button and apply the change.', "oxyultimate-woo"));

		$shopButton->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Button URL Source', "oxyultimate-woo"),
			'slug' 		=> 'shop_button_url_source',
			'value' 	=> ['shop' => __('Shop Page', "oxyultimate-woo"), 'custom' => __('Custom', "oxyultimate-woo")],
			'default' 	=> 'shop',
			'condition' => 'shop_button=yes'
		]);

		$custom_url = $shopButton->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_cart_totals_custom_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_cart_totals_custom_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_cart_totals_custom_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_cart_totals_custom_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_cart_totals_custom_url\')">set</div>
			</div>
			',
			"custom_url",
			$shopButton
		);
		$custom_url->setParam( 'heading', __('Custom URL', "oxyultimate-woo") );
		$custom_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_cart_totals_shop_button_url_source']=='custom'" );

		/*$shopButton->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Custom URL'),
			'slug' 		=> 'custom_url',
			'condition' => 'shop_button=yes&&shop_button_url_source=custom'
		]);*/

		$shopButton->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'condition' 	=> 'shop_button=yes'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setRange(0, 1000, 10);

		$spacing = $shopButton->addControlSection('shop_btn_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"shopbtn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$color = $shopButton->addControlSection('shop_btn_color', __('Color'), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'color'
			]
		]);

		//* Typography
		$shopButton->typographySection(__('Typography'), $selector, $this );

		//* Border
		$shopButton->borderSection(__('Border'), $selector, $this );
		$shopButton->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ':hover', $this );

		//* Box Shadow
		$shopButton->boxShadowSection( __('Box Shadow'), $selector, $this );
		$shopButton->boxShadowSection( __('Hover Shadow', "oxyultimate-woo"), $selector . ':hover', $this );
	}

	function controls() {

		$this->cartTotalConfig();

		$this->cartTotalHeading();

		$this->cartTotalTable();

		$this->cartShippingMethod();

		$this->cartShippingForm();

		$this->cartTotalButtonWrap();

		$this->cartTotalCheckoutButton();

		$this->cartTotalShopButton();
	}

	function render($options, $defaults, $content ) {
		
		if ( is_null( WC()->cart ) || WC()->cart->is_empty() ) {
			return;
		}

		if( isset( $options['hide_shipping_methods'] ) && $options['hide_shipping_methods'] !== 'none' ) {
			update_option('ouwoo_hide_shipping_methods', $options['hide_shipping_methods'], false );
			if( $options['hide_shipping_methods'] == 'hide_except_states' ) {
				update_option('ouwoo_hide_shipping_methods_states', esc_attr( $options['states_list'] ), false );
			} else {
				delete_option('ouwoo_hide_shipping_methods_states');
			}
		} else {
			delete_option('ouwoo_hide_shipping_methods');
			delete_option('ouwoo_hide_shipping_methods_states');
		}

		$nonce_value = wc_get_var( $_REQUEST['woocommerce-shipping-calculator-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		// Update Shipping. Nonce check uses new value and old value (woocommerce-cart). @todo remove in 4.0.
		if ( ! empty( $_POST['calc_shipping'] ) && ( wp_verify_nonce( $nonce_value, 'woocommerce-shipping-calculator' ) || wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) ) { // WPCS: input var ok.
			WC_Shortcode_Cart::calculate_shipping();

			// Also calc totals before we check items so subtotals etc are up to date.
			WC()->cart->calculate_totals();
		}

		// Calc totals.
		WC()->cart->calculate_totals();
			
		if( isset($options['checkout_text']) ) {
			$this->checkout_text = wp_kses_post( $options['checkout_text'] );
			add_filter('gettext_woocommerce', array($this, 'ouwoo_btn_text'), 10, 3);
		}

		if( isset($options['shop_button']) && $options['shop_button'] == 'yes') {
			$this->shop_button_text = isset($options['shop_text']) ? wp_kses_post( $options['shop_text'] ) : esc_html('Continue Shopping', 'woocommerce');
			if( 
				isset($options['shop_button_url_source']) 
				&& $options['shop_button_url_source'] == 'custom' 
				&& isset($options['custom_url']) 
			) {
				$this->shop_button_url = $options['custom_url'];
			} else {
				$this->shop_button_url = ( wc_get_page_id( 'shop' ) > 0 ) ? wc_get_page_permalink( 'shop' ) : '#';
			}
			add_action( 'woocommerce_proceed_to_checkout', array( $this, 'ouwoo_button_continue_shopping' ), 19 );
		}

		woocommerce_cart_totals();

		if( isset($options['checkout_text']) ) {
			remove_filter('gettext_woocommerce', array($this, 'ouwoo_btn_text'), 10, 3);
		}
	}

	function ouwoo_btn_text( $translation, $text, $domain ) {
		if( $this->checkout_text && $text == 'Proceed to checkout') {
			$translation = $this->checkout_text;
		}

		return $translation;
	}

	function ouwoo_button_continue_shopping() {
		if( $this->shop_button_text ) {
	?>
		<a class="button shop-button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', $this->shop_button_url ) ); ?>">
			<?php echo $this->shop_button_text; ?>
		</a>
	<?php
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-cart-totals {
						width: 100%;
						--checked-bullet-size: 4px;
						--checked-radio-alt-color: #ffffff;
					}
					.shop_table tr.woocommerce-shipping-totals {
						vertical-align: top;
					}
					.oxy-ou-cart-totals .wc-proceed-to-checkout {
						display: flex;
						justify-content: flex-end;
						padding: 0;
						width: 100%;
					}
					.oxy-ou-cart-totals .wc-proceed-to-checkout a.button {
						margin-bottom: 0;
						-webkit-transition: all 0.2s ease;
						-moz-transition: all 0.2s ease;
						transition: all 0.2s ease;
					}
					.oxy-ou-cart-totals .wc-proceed-to-checkout a.shop-button {
						margin-right: 10px;
					}
					.oxy-ou-cart-totals .woocommerce table.shop_table tbody th {
						border: none;
					}
					.oxy-ou-cart-totals table.shop_table tr th {
						border-right-color: #d3ced2;
						border-right-style: solid!important;
						border-right-width: 0;
					}
					.oxy-ou-cart-totals #shipping_method input[type=radio]:checked,
					.oxy-ou-cart-totals #shipping_method input[type=radio]:checked:hover {
						box-shadow: inset 0 0 0 var(--checked-bullet-size) var(--checked-radio-alt-color); border: none;
					}
					.woocommerce .shipping-calculator-form select:focus,
					.woocommerce .shipping-calculator-form input.input-text:focus,
					.select2-container--default .select2-search--dropdown .select2-search__field:focus {
						box-shadow: none;
						outline: 0;
					}
					form.woocommerce-shipping-calculator .form-row {
						clear: both;
						float: none;
						width: 100%;
						padding-left: 0;
					}
					.shipping-calculator-button {display:inline-block; width: 100%;}
					.select2-container--default .select2-selection--single,
					.select2-container--default .select2-search--dropdown .select2-search__field {
						outline: none;
						height: auto;
						-webkit-appearance: none;
						outline: none;
						-moz-appearance: none;
						text-align: left;
					}
					.select2-container--default ul.select2-results__options .select2-results__option[data-selected=true] {
						background: #333;
						color: #fff;
					}
					.select2-container--default ul.select2-results__options .select2-results__option--highlighted[aria-selected], .select2-container--default ul.select2-results__options .select2-results__option--highlighted[data-selected] {
						background: #999;
						color: #fff;
					}
					.oxy-ou-cart-totals table.shop_table_responsive tr:nth-child(2n) td,
					.oxy-ou-cart-totals table.shop_table_responsive tr:nth-child(2n) td {
						background-color: transparent!important;
					}';

			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();
		if( isset($original[$prefix . '_preview_form' ]) && $original[$prefix . '_preview_form' ] == 'yes' ) {
			$css .= 'body.oxygen-builder-body .shipping-calculator-form{display: block!important;}';
		}

		if( isset($original[$prefix . '_selected_bgcolor']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option[data-selected=true] {
						background: '.$original[$prefix . '_selected_bgcolor'].';
					}';
		}

		if( isset($original[$prefix . '_selected_hbgcolor']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option--highlighted[aria-selected], .select2-container--default ul.select2-results__options .select2-results__option--highlighted[data-selected] {
						background: '. $original[$prefix . '_selected_hbgcolor'] .';
					}';
		}
		
		if( isset($original[$prefix . '_selected_color']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option[data-selected=true] {
						color: '.$original[$prefix . '_selected_color'].';
					}';
		}
		
		if( isset($original[$prefix . '_highlighted_color']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option--highlighted[aria-selected], .select2-container--default ul.select2-results__options .select2-results__option--highlighted[data-selected] {
						color: '.$original[$prefix . '_highlighted_color'].';
					}';
		}

		$css .= '.select2-container--default .select2-dropdown--below .select2-search--dropdown .select2-search__field {
					background: '. ( isset($original[$prefix . '_sfield_bgcolor']) ? $original[$prefix . '_sfield_bgcolor'] : '#fff' ) .';
					border-color: '. ( isset($original[$prefix . '_sfield_bordercolor']) ? $original[$prefix . '_sfield_bordercolor'] : '#d3ced2' ) .';
					border-width: '. ( isset($original[$prefix . '_sfield_brdwidth']) ? $original[$prefix . '_sfield_brdwidth'] : '1' ) .'px;
					border-radius: '. ( isset($original[$prefix . '_sfield_brdradius']) ? $original[$prefix . '_sfield_brdradius'] : '4' ) .'px;
					color: '. ( isset($original[$prefix . '_sfield_color']) ? $original[$prefix . '_sfield_color'] : '#333' ) .';
				}';

		$css .= '.select2-container--default .select2-dropdown--below .select2-search--dropdown .select2-search__field:focus {
					background: '. ( isset($original[$prefix . '_sfield_fbgcolor']) ? $original[$prefix . '_sfield_fbgcolor'] : '#fff' ) .';
					border-color: '. ( isset($original[$prefix . '_sfield_focusbrdcolor']) ? $original[$prefix . '_sfield_focusbrdcolor'] : '#bbb' ) .';
					color: '. ( isset($original[$prefix . '_sfield_fcolor']) ? $original[$prefix . '_sfield_fcolor'] : '#666' ) .';
				}';
		
		return $css;
	}

	function enableFullPresets() {
		return true;
	}

}

new OUWooCartTotals();