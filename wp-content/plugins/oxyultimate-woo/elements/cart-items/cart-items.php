<?php

class OUWooCartItems extends UltimateWooEl {
	public $css_added = false;
	public $has_js = true;

	function name() {
		return __( "Cart Items", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_cart_items";
	}

	function ouwoo_button_place() {
		return "cart";
	}

	function button_priority() {
		return 3;
	}

	function custom_init() {
		add_filter("oxygen_vsb_element_presets_defaults", array( $this, "ouwoo_cart_items_presets_defaults" ) );
	}

	function ouwoo_cart_items_presets_defaults( $all_elements_defaults ) {
		require("cart-items-presets.php");

		$all_elements_defaults = array_merge($all_elements_defaults, $cart_items);

		return $all_elements_defaults;
	}

	/*******************************
	 * Main Table
	 ********************************/
	function itemsTable() {
		$table = $this->addControlSection('items_table', __('Table'), 'assets/icon.png', $this );

		$selector = 'table.shop_table';

		/*******************************
		 * Table Color & Width
		 ********************************/
		$table->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setRange(0, 1000, 10);

		//* Border
		$table->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$table->boxShadowSection(__('Box Shadow'), $selector, $this );
	}


	/*******************************
	 * Table Head
	 ********************************/
	function itemsTableHead() {

		$header = $this->addControlSection('items_table_head', __('Table Header', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = 'table.shop_table thead th';

		$header->addStyleControl(
			[
				'selector' 		=> 'table.shop_table thead tr',
				'property' 		=> 'background-color'
			]
		);

		/*******************************
		 * Border Top
		 ********************************/
		$brd = $header->addControlSection('tblhead_brdtop', __('Border'), 'assets/icon.png', $this );
		$brd->addStyleControls([
			[
				'name' 			=> __('Border Top Color', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table thead tr',
				'property' 		=> 'border-top-color'
			],
			[
				'name' 			=> __('Border Top Width', "oxyultimate-woo"),
				'selector' 		=> 'table.shop_table thead tr',
				'property' 		=> 'border-top-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			],
			[
				'name' 			=> __('Vertical Border Color', "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'border-left-color'
			],
			[
				'name' 			=> __('Vertical Border Width', "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'border-left-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			],
			[
				'name' 			=> __('Vertical Border Style', "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'border-left-style',
				'control_type' 	=> 'radio',
				'value' 		=> [
					'none' 		=> __('None'),
					'solid' 	=> __('Solid'),
					'dashed' 	=> __('Dashed'),
					'dotted' 	=> __('Dotted'),
				]
			]
		]);


		/*******************************
		 * Padding
		 ********************************/
		$spacing = $header->addControlSection('tblhead_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"tablehead_padding",
			__("Padding"),
			$selector
		)->whiteList();


		/*******************************
		 * Font
		 ********************************/
		$header->typographySection(__('Typography'), $selector, $this );


		/*******************************
		 * Responsiveness
		 ********************************/
		$rsp = $header->addControlSection('tblhead_font', __('Responsiveness', "oxyultimate-woo"), 'assets/icon.png', $this );

		$rsp->addStyleControls([
			[
				'name' 		=> __('Product Label Font Size', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-name::before',
				'property' 	=> 'font-size'
			],
			[
				'name' 		=> __('Price Label Font Size', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-price::before',
				'property' 	=> 'font-size'
			],
			[
				'name' 		=> __('Quantity Label Font Size', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-quantity::before',
				'property' 	=> 'font-size'
			],
			[
				'name' 		=> __('Subtotal Label Font Size', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-subtotal::before',
				'property' 	=> 'font-size'
			]
		]);

		$rsp->addCustomControl(
			'<div class="oxygen-option-default"><hr style="color: #f4f4f4;height: 1px" noshade/></div>', 
			'divider'
		);

		$rsp->addStyleControls([
			[
				'name' 		=> __('Product Label Color', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-name::before',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Price Label Color', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-price::before',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Quantity Label Color', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-quantity::before',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Subtotal Label Color', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td.product-subtotal::before',
				'property' 	=> 'color'
			]
		]);

		$rsp->addCustomControl(
			'<div class="oxygen-option-default"><hr style="color: #f4f4f4;height: 1px" noshade/></div>', 
			'divider_2'
		);

		$rsp->addStyleControls([
			[
				'name' 		=> __('Overall Text Transform', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td::before',
				'property' 	=> 'text-transform'
			],
			[
				'name' 		=> __('Overall Line Height', "oxyultimate-woo"),
				'selector' 	=> 'table.shop_table_responsive tr td::before',
				'property' 	=> 'line-height'
			]
		]);
	}


	/*******************************
	 * Table Cell
	 ********************************/
	function tabelCell() {
		$cell = $this->addControlSection('table_cell', __('Table Body', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = 'table.shop_table tbody td';
		$cell->addStyleControl(
			[
				'selector' 			=> $selector . ', table.shop_table_responsive tr:nth-child(2n) td',
				'property' 			=> 'background-color'
			]
		);

		//* Cell Padding
		$spacing = $cell->addControlSection('cell_padding', __('Padding'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"tablecell_padding",
			__("Padding"),
			$selector
		)->whiteList();

		/*******************************
		 * Border Top
		 ********************************/
		$brd = $cell->addControlSection('tblbdy_brdtop', __('Border'), 'assets/icon.png', $this );
		$brd->addStyleControls([
			[
				'selector' 		=> 'table.shop_table tbody tr',
				'property' 		=> 'border-top-color'
			],
			[
				'selector' 		=> 'table.shop_table tbody tr',
				'property' 		=> 'border-top-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			],
			[
				'name' 			=> __('Vertical Border Color', "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'border-left-color'
			],
			[
				'name' 			=> __('Vertical Border Width', "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'border-left-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			],
			[
				'name' 			=> __('Vertical Border Style', "oxyultimate-woo"),
				'selector' 		=> $selector,
				'property' 		=> 'border-left-style',
				'control_type' 	=> 'radio',
				'value' 		=> [
					'none' 		=> __('None'),
					'solid' 	=> __('Solid'),
					'dashed' 	=> __('Dashed'),
					'dotted' 	=> __('Dotted'),
				]
			]
		]);

		//* Remove Button
		$removeIcon = $cell->addControlSection('remove_icon', __('Remove Icon', "oxyultimate-woo"), 'assets/icon.png', $this );
		$remove_selector = 'td.product-remove a.remove';
		$removeIcon->addStyleControls([
			[
				'selector' 			=> $remove_selector,
				'property' 			=> 'background-color'
			],
			[
				'name' 				=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 			=> $remove_selector . ":hover",
				'property' 			=> 'background-color'
			],
			[
				'name' 				=> __('Color'),
				'selector' 			=> $remove_selector,
				'property' 			=> '--remove-link-color',
				'control_type' 		=> 'colorpicker'
			],
			[
				'name' 				=> __('Color on Hover', "oxyultimate-woo"),
				'selector' 			=> $remove_selector . ":hover",
				'property' 			=> '--remove-link-hover-color',
				'control_type' 		=> 'colorpicker'
			],
			[
				'selector' 			=> $remove_selector,
				'property' 			=> 'font-size'
			]
		]);

		//* Product Image
		$images = $cell->addControlSection('product_image', __('Product Image', "oxyultimate-woo"), 'assets/icon.png', $this );
		$images->addStyleControl(
			[
				'name' 				=> __('Size'),
				'control_type' 		=> 'slider-measurebox',
				'selector' 			=> 'td.product-thumbnail img',
				'property' 			=> 'min-width|min-height|width|height'
			]
		)->setRange(0,200,10)->setUnits('px', 'px,%,em')->setDefaultValue(64);

		$images->addStyleControl(
			[
				'control_type' 		=> 'slider-measurebox',
				'selector' 			=> 'td.product-thumbnail img',
				'property' 			=> 'border-radius'
			]
		)->setRange(0,100,1)->setUnits('px', 'px,%')->setDefaultValue(0);

		$images->addStyleControl(
			[
				'control_type' 		=> 'slider-measurebox',
				'selector' 			=> 'td.product-thumbnail img',
				'property' 			=> 'border-width'
			]
		)->setRange(0,10,1)->setUnits('px', 'px')->setDefaultValue(1);

		$images->addStyleControl(
			[
				'selector' 			=> 'td.product-thumbnail img',
				'property' 			=> 'border-color'
			]
		);

		//* Product Title
		$title = $cell->typographySection(__('Product Title', "oxyultimate-woo"), 'td.product-name a', $this );
		$title->addStyleControl(
			[
				'name' 				=> __('Color on Hover', "oxyultimate-woo"),
				'selector' 			=> 'td.product-name a:hover',
				'property' 			=> 'color'
			]
		);

		//* Product Prcie
		$cell->typographySection(__('Price', "woocommerce"), '.product-price, .product-price .woocommerce-Price-amount', $this );

		//* Product Sub Total
		$cell->typographySection(__('Sub Total', "oxyultimate-woo"), 'td.product-subtotal, td.product-subtotal .woocommerce-Price-amount', $this );

		$variation = $cell->addControlSection( "cart_variations" ,__('Product Variation'), "assets/icon.png", $this );
		$variation_selector = '.product-name';
		$value_selector = $variation_selector . ' .variation dd,' . $variation_selector . ' .variation dd p';
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
				'selector' 		=> '.product-name dt',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Label Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.product-name dt',
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


	/*******************************
	 * Table Footer
	 ********************************/
	function itemsTableFooter() {

		$footer = $this->addControlSection('items_table_footer', __('Table Footer', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = 'table.cart td.actions';

		$hide_footer = $footer->addControl('buttons-list', 'hide_footer_row', __('Hide Footer Row', "oxyultimate-woo"));
		$hide_footer->setValue(['No', 'Yes']);
		$hide_footer->setValueCSS([
			'Yes' => $selector . ' * {display: none;}
				table.cart tr:last-child{border-top: none;}
				'. $selector . ' {padding: 0;}'
		]);
		$hide_footer->setDefaultValue('No');
		$hide_footer->whiteList();

		$footer->addStyleControl(
			[
				'selector' 		=> $selector . ', table.shop_table_responsive tr:nth-child(2n) td.actions',
				'property' 		=> 'background-color'
			]
		);

		$spacing = $footer->addControlSection('td_actions_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"tdaction_padding",
			__("Padding"),
			$selector
		)->whiteList();
	}


	/*******************************
	 * Quantity Field
	 ********************************/
	function itemsQuantityField() {
		//* Product Quantity
		$qty = $this->addControlSection('product_qty', __('Quantity', "woocommerce"), 'assets/icon.png', $this );
		$qty_selector = 'td.product-quantity input.qty';
		$qty->addStyleControl(
			[
				'name' 				=> __('Alignment', "oxyultimate-woo"),
				'selector' 			=> 'td.product-quantity .quantity',
				'property' 			=> 'justify-content',
				'control_type' 		=> 'radio',
				'value' 			=> ['flex-start' => __('Left'), 'center' => __('Center'), 'flex-end' => __('Right')]
			]
		);

		$qty->addStyleControl(
			[
				'control_type' 		=> 'slider-measurebox',
				'selector' 			=> $qty_selector,
				'property' 			=> 'width'
			]
		)->setRange(0,200,10)->setUnits('px', 'px,%,em,vw');

		$qty->addStyleControl(
			[
				'selector' 			=> $qty_selector,
				'property' 			=> 'font-size'
			]
		);

		$color = $qty->addControlSection('qty_color', __('Initial Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$color->addStyleControl(
			[
				'selector' 			=> $qty_selector,
				'property' 			=> 'background-color'
			]
		);

		$color->addStyleControl(
			[
				'selector' 			=> $qty_selector,
				'property' 			=> 'color'
			]
		);

		$fcolor = $qty->addControlSection('qty_focus_color', __('Focus Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$fcolor->addStyleControl(
			[
				'selector' 			=> $qty_selector . ':focus',
				'property' 			=> 'background-color'
			]
		);

		$fcolor->addStyleControl(
			[
				'selector' 			=> $qty_selector . ':focus',
				'property' 			=> 'color'
			]
		);

		$qty->borderSection(_('Border'), $qty_selector, $this);
		$qty->borderSection(_('Focus Border'), $qty_selector . ':focus', $this);
	}

	/*******************************
	 * Quantity +/-
	 ********************************/
	function qtyPlusMinus() {
		$pm = $this->addControlSection('qty_pm', __('+/- Button', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.oucart-qty-chng';

		$pm->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable'),
			'slug' 		=> 'pm_disable',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no'
		])->rebuildElementOnChange();

		$fssize = $pm->addControlSection('qty_pm_width', __('Size', "oxyultimate-woo"), 'assets/icon.png', $this );
		$fssize->addStyleControl(
			[
				'control_type' 		=> 'slider-measurebox',
				'selector' 			=> $selector,
				'property' 			=> 'width'
			]
		)->setRange(0,200,10)->setUnits('px', 'px,%,em,vw');

		$fssize->addStyleControl(
			[
				'control_type' 		=> 'slider-measurebox',
				'selector' 			=> $selector,
				'property' 			=> 'height'
			]
		)->setRange(0,200,10)->setUnits('px', 'px,%,em,vw');

		$fs = $pm->addControlSection('qty_pm_fs', __('Font Size'), 'assets/icon.png', $this );
		$fs->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'font-size'
			],
			[
				'selector' 		=> $selector,
				'property' 		=> 'font-weight'
			],
			[
				'selector' 		=> $selector,
				'property' 		=> 'line-height'
			]
		]);

		$color = $pm->addControlSection('qty_pm_color', __('Initial Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'selector' 		=> $selector,
				'property' 		=> 'color'
			]
		]);


		$hcolor = $pm->addControlSection('qty_pm_hcolor', __('Hover Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$hcolor->addStyleControls([
			[
				'selector' 		=> $selector . ":hover",
				'property' 		=> 'background-color'
			],
			[
				'selector' 		=> $selector . ":hover",
				'property' 		=> 'color'
			]
		]);

		$pm->borderSection(__('Border'), $selector, $this);
		$pm->borderSection(__('Hove Border', "oxyultimate-woo"), $selector . ':hover', $this);

		$pm->boxShadowSection(__('Shadow', "oxyultimate-woo"), $selector, $this);
		$pm->boxShadowSection(__('Hover Shadow', "oxyultimate-woo"), $selector . ':hover', $this);
	}


	/*******************************
	 * Coupon Form
	 ********************************/
	function couponForm() {
		$form = $this->addControlSection('coupon_form', __('Coupon Form', "oxyultimate-woo"), 'assets/icon.png', $this );

		$input_selector = 'table.cart td.actions .input-text, table.cart td.actions .coupon .input-text';
		$input_focus_selector = 'table.cart td.actions .input-text:focus, table.cart td.actions .coupon .input-text:focus';

		$hide_form = $form->addControl('buttons-list', 'hide_cf', __('Hide Form', "oxyultimate-woo"));
		$hide_form->setValue(['No', 'Yes']);
		$hide_form->setValueCSS([
			'Yes' => 'table.cart td.actions .coupon{display: none;}',
			'No' => 'table.cart td.actions .coupon{display: flex;}'
		]);
		$hide_form->setDefaultValue('No');
		$hide_form->whiteList();

		/*******************************
		 * Width
		 ********************************/
		$width = $form->addControlSection('fields_width', __('Width'), 'assets/icon.png', $this );

		$width->addStyleControl(
			array(
				"name" 			=> __('Wrapper', "oxyultimate-woo"),
				"selector" 		=> 'table.cart td.actions .coupon',
				"property" 		=> 'width',
				"control_type" 	=> 'slider-measurebox'
			)
		)
		->setRange('0', '750', '1')
		->setUnits('%', 'px,%,em');

		$width->addStyleControl(
			array(
				"name" 			=> __('Input Field', "oxyultimate-woo"),
				"selector" 		=> 'table.cart td.actions .coupon .input-text',
				"property" 		=> 'width',
				"control_type" 	=> 'slider-measurebox'
			)
		)
		->setRange('0', '750', '5')
		->setUnits('px', 'px,%,em');

		$width->addStyleControl(
			array(
				"name" 			=> __('Button', "oxyultimate-woo"),
				"selector" 		=> 'table.cart td.actions .coupon .button',
				"property" 		=> 'width',
				"control_type" 	=> 'slider-measurebox'
			)
		)
		->setRange('0', '750', '5')
		->setUnits('px', 'px,%,em');

		/*******************************
		 * Alignment
		 ********************************/
		$alignment = $form->addControlSection('fields_alignment', __('Alignment'), 'assets/icon.png', $this );
		
		$alignment->addStyleControl([
			"control_type" 	=> "radio",
			"selector" 		=> 'table.cart',
			"property"	 	=> '--cf-fields-align',
			"name" 			=> __('Align', "oxyultimate-woo"),
			"value" 		=> ['row' => __('Horizonal', "oxyultimate-woo"), 'column' => __('Vertical', "oxyultimate-woo")],
			"default" 		=> "row"
		]);

		$alignment->addStyleControl(
			array(
				"selector" 		=> 'table.cart td.actions .input-text, table.cart td.actions .coupon .input-text',
				"property" 		=> 'margin-right',
				"control_type" 	=> 'slider-measurebox'
			)
		)
		->setRange('0', '50', '5')
		->setUnits('px', 'px');

		$alignment->addStyleControl(
			array(
				"selector" 		=> 'table.cart td.actions .input-text, table.cart td.actions .coupon .input-text',
				"property" 		=> 'margin-bottom',
				"control_type" 	=> 'slider-measurebox'
			)
		)
		->setRange('0', '50', '5')
		->setUnits('px', 'px');


		/*******************************
		 * Padding
		 ********************************/
		$spacing = $form->addControlSection('fields_sp', __('Fields Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"fields_padding",
			__("Padding"),
			'table.cart td.actions .coupon .input-text + .button'
		)->whiteList();


		/*******************************
		 * Input Field
		 ********************************/
		$input = $form->addControlSection('input_field', __('Input Field', "oxyultimate-woo"), 'assets/icon.png', $this );

		$form->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Placeholder Text', "oxyultimate-woo"),
			'slug' 		=> 'placeholder_text'
		]);

		$input->addStyleControls([
			[
				'selector' 		=> $input_selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Background Color', "oxyultimate-woo"),
				'selector' 		=> $input_focus_selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Text Color', "oxyultimate-woo"),
				'selector' 		=> $input_focus_selector,
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Placeholder Color', "oxyultimate-woo"),
				'selector' 		=> 'table.cart td.actions .input-text::placeholder, table.cart td.actions .coupon .input-text::placeholder',
				'property' 		=> 'color'
			],
			[
				'selector' 		=> $input_selector,
				'property' 		=> 'border-color'
			],
			[
				'selector' 		=> $input_selector,
				'property' 		=> 'border-width'
			],
			[
				'name' 			=> __('Focus Border Color', "oxyultimate-woo"),
				'selector' 		=> $input_focus_selector,
				'property' 		=> 'border-color'
			],
			[
				'selector' 		=> $input_selector,
				'property' 		=> 'border-radius'
			]
		]);

		$form->typographySection(__('Input Text', "oxyultimate-woo"), $input_selector, $this );

		$form->boxShadowSection(__('Input Field Shadow', "oxyultimate-woo"), $input_selector, $this );


		/*******************************
		 * Button
		 ********************************/
		$button_selector = 'table.cart td.actions .coupon .button';

		$form->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text', "oxyultimate-woo"),
			'slug' 		=> 'apply_btn_text'
		]);

		$button = $form->addControlSection('button_color', __('Button Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$button->addStyleControls([
			[
				'selector' 		=> $button_selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $button_selector . ':hover',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Text Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $button_selector . ':hover',
				'property' 		=> 'color'
			]
		]);

		$form->typographySection(__('Button Text', "oxyultimate-woo"), $button_selector, $this );

		//* Border
		$form->borderSection(__('Button Border', "oxyultimate-woo"), $button_selector, $this );
		$form->borderSection(__('Hover Border', "oxyultimate-woo"), $button_selector . ':hover', $this );

		//* Box Shadow
		$form->boxShadowSection(__('Button Box Shadow', "oxyultimate-woo"), $button_selector, $this );
		$form->boxShadowSection(__('Hover Box Shadow', "oxyultimate-woo"), $button_selector . ':hover', $this );
	}


	function updateCartButton() {
		$button = $this->addControlSection('update_cart_btn', __('Update Button', "oxyultimate-woo"), 'assets/icon.png', $this );

		$button_selector = 'table.cart td.actions button[name=update_cart]';

		//$disabled_selector = 'table.cart td.actions button[type=submit]:disabled[disabled]';

		$hide_update_button = $button->addControl('buttons-list', 'hide_update_button', __('Hide Button', "oxyultimate-woo"));
		$hide_update_button->setValue(['No', 'Yes']);
		$hide_update_button->setValueCSS(['Yes' => $button_selector . '{display: none;}']);
		$hide_update_button->setDefaultValue('No');
		$hide_update_button->whiteList();

		$button->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Update Automatically?', "oxyultimate-woo"),
			'slug' 		=> 'update_cart_auto',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no',
			'condition' => 'hide_update_button=No'
		])->setParam('description', __('Cart will update automatically without clicking on the Update Cart button.', "oxyultimate-woo"));

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text', "oxyultimate-woo"),
			'slug' 		=> 'update_btn_text',
			'condition' => 'hide_update_button=No'
		]);

		/*******************************
		 * Padding
		 ********************************/
		$spacing = $button->addControlSection('submit_btn_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"submitbtn_padding",
			__("Padding"),
			$button_selector
		)->whiteList();


		/*******************************
		 * Button Color
		 ********************************/
		$button_color = $button->addControlSection('submit_button_color', __('Color'), 'assets/icon.png', $this );
		$button_color->addStyleControls([
			[
				'selector' 		=> $button_selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $button_selector . ':hover',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Text Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $button_selector . ':hover',
				'property' 		=> 'color'
			]
		]);

		$button->typographySection(__('Typography'), $button_selector, $this );

		//* Border
		$button->borderSection(__('Border'), $button_selector, $this );
		$button->borderSection(__('Hover Border', "oxyultimate-woo"), $button_selector . ':hover', $this );

		//* Box Shadow
		$button->boxShadowSection(__('Box Shadow'), $button_selector, $this );
		$button->boxShadowSection(__('Hover Box Shadow', "oxyultimate-woo"), $button_selector . ':hover', $this );
	}

	function controls() {
		$this->itemsTable();

		$this->itemsTableHead();

		$this->tabelCell();

		$this->itemsTableFooter();

		$this->itemsQuantityField();

		$this->qtyPlusMinus();

		$this->couponForm();

		$this->updateCartButton();
	}

	function ouwoo_quantity_change_minus_button() {
		echo '<span class="oucart-qty-minus oucart-qty-chng">-</span>';
	}

	function ouwoo_quantity_change_plus_button() {
		echo '<span class="oucart-qty-plus oucart-qty-chng">+</span>';
	}

	function render($options, $defaults, $content) {
		
		if ( is_null( WC()->cart ) || WC()->cart->is_empty() ) {
			return;
		}

		add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

		if( isset($options['pm_disable']) && $options['pm_disable'] != 'yes' ) {
			add_action( 'woocommerce_before_quantity_input_field', array( $this, 'ouwoo_quantity_change_minus_button' ) );
			add_action( 'woocommerce_after_quantity_input_field', array( $this, 'ouwoo_quantity_change_plus_button' ) );
		}

		$apply_btn = isset($options['apply_btn_text']) ? wp_kses_post( $options['apply_btn_text'] ) : esc_attr__( 'Apply coupon', 'woocommerce' );
		$update_cart = isset($options['update_btn_text']) ? wp_kses_post( $options['update_btn_text'] ) : esc_attr__( 'Update cart', 'woocommerce' );
		$placeholder_text = isset($options['placeholder_text']) ? wp_kses_post( $options['placeholder_text'] ) : esc_attr__( 'Coupon code', 'woocommerce' );
		
		$hide_update_button = isset($options['hide_update_button']) ? $options['hide_update_button'] : 'no';

		if( strtolower( $hide_update_button ) == "yes" ) {
			$auto_submit = "yes";
		} elseif( isset($options['update_cart_auto']) && $options['update_cart_auto'] == "yes" ) {
			$auto_submit = "yes";
		} else {
			$auto_submit = "no";
		}

		// Calc totals.
		WC()->cart->calculate_totals();

		wc_get_template( 'cart-items-view.php', array( 'placeholder_text' => $placeholder_text, 'apply_coupon' => $apply_btn, 'update_cart' => $update_cart, 'auto_submit' => $auto_submit), '', OUWOO_DIR . 'elements/cart-items/' );

		if( ! $this->isBuilderEditorActive() ) {
			$js = "jQuery(document).ready(function($){
						$(document.body).on('change input', '.input-text.qty', function (e) {
							if( $('.woocommerce-cart-form').attr('data-cart-autoupdate') == 'yes' ) { 
								$('.woocommerce-cart-form :input[name=\"update_cart\"]').trigger(\"click\");
							}
						});

						$(document.body).on('click', '.oucart-qty-chng', function (e) {
							var toggler = $(e.currentTarget),
								input 	 = toggler.siblings('.input-text.qty');

							if( !input.length ) return;

							var baseQty = itemBaseQty = parseFloat( input.val() ),
								step 	= parseFloat( input.attr('step') ),
								min 	= parseFloat( input.attr('min') ),
								max 	= parseFloat( input.attr('max') ),
								action 	= toggler.hasClass( 'oucart-qty-plus' ) ? 'add' : 'less',
								newQty 	= action === 'add' ? baseQty + step : baseQty - step,
								invalid = false;

							if( isNaN( newQty )  || newQty < 0 || newQty < min  ){
								invalid = true;
							} else if( newQty > max ){
								invalid = true;
							} else if( ( newQty % step ) !== 0 ){
								invalid = true;
							}

							if( invalid ){
								input.val( itemBaseQty );
								return;
							}
							
							input.val(newQty).trigger('change');
							if( $('.woocommerce-cart-form').attr('data-cart-autoupdate') == 'yes' ) {
								$('.woocommerce-cart-form :input[name=\"update_cart\"]').trigger(\"click\");
							}
						});
					});";

			if( ! is_cart() ) { wp_enqueue_script('wc-cart'); }

			$this->El->footerJS( $js );
		}

		if( isset($options['pm_disable']) && $options['pm_disable'] != 'yes' ) {
			remove_action( 'woocommerce_before_quantity_input_field', array( $this, 'ouwoo_quantity_change_minus_button' ) );
			remove_action( 'woocommerce_after_quantity_input_field', array( $this, 'ouwoo_quantity_change_plus_button' ) );
		}
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '
				.oxy-ou-cart-items {
					width: 100%;
					--remove-link-color: #900;
					--remove-link-hover-color: #fff;
					min-height: 40px;
				}
				.oxy-ou-cart-items .product-remove > a.remove:hover {
					text-decoration: none!important;
				}
				.oxy-ou-cart-items table.cart td.actions {
					background-color: #fff;
					padding: 15px;
				}
				.oxy-ou-cart-items table.shop_table thead th:first-child,
				.oxy-ou-cart-items table.shop_table tbody td:first-child {
					border: none;
				}
				.oxy-ou-cart-items table.cart{--cf-fields-align: row;}
				.oxy-ou-cart-items table.cart td.actions .coupon {
					display: flex;
					flex-direction: var(--cf-fields-align);
				}
				.oxy-ou-cart-items table.cart td.actions .input-text,
				.oxy-ou-cart-items table.cart td.actions .coupon .input-text {
					margin-right: 12px;
					padding: 8px 12px;
					width: 100%;
				}
				.oxy-ou-cart-items table.cart td.actions .input-text:focus,
				.oxy-ou-cart-items table.cart td.actions .coupon .input-text:focus {
					box-shadow: none;
					outline: 0;
				}
				.oxy-ou-cart-items table.cart td.actions button[type=submit] {
					float: right;
					padding: 12px 15px;
				}
				.oxy-ou-cart-items table.cart td.actions .coupon .input-text + .button {
					width: 100%;
					padding: 12px 5px;
				}
				.oxy-ou-cart-items td.product-remove a.remove {
					color: var(--remove-link-color)!important;
				}
				.oxy-ou-cart-items td.product-remove a.remove:hover {
					color: var(--remove-link-hover-color)!important;
				}
				td.product-name a:hover{
					text-decoration: none!important;
				}
				.oxy-ou-cart-items .product-quantity .quantity {
					display: flex;
					align-items: center;
					justify-content: center;
					font-family: inherit;
					width: 100%;
				}
				.oxy-ou-cart-items td.product-thumbnail img {
					width: 64px;
					height: 64px;
				}
				.oucart-qty-minus,
				.oucart-qty-plus {
					background: #efefef;
					cursor: pointer;
					display: flex;
					width: 20px;
					height: 20px;
					justify-content: center;
					align-items: center;
				}
				.oxy-ou-cart-items table.shop_table,
				.oxy-ou-cart-items td.product-quantity input.qty:focus {
					box-shadow: none;
				}
				.oxy-ou-cart-items .product-name dl {
					border-left: 2px solid rgba(0,0,0,.1);
					display: inline-block;
					padding-left: 8px;
					font-size: 13px;
					line-height: 1.425;
					margin: 0;
					width: 100%;				
				}
				.oxy-ou-cart-items .product-name .variation dt {
					margin: 0;
					padding: 0;
					display: inline-block;
					float: left;
				}
				.oxy-ou-cart-items .variation dd,
				.oxy-ou-cart-items .variation dd p {
					margin: 0;
					padding: 0 0 0 4px;
					display: inline-block;
					float: left;
					color: #777;
				}
				@media (max-width: 768px) {
					table.shop_table thead th,
					table.shop_table tbody td {
						border: none;
					}
					.oxy-ou-cart-items table.cart td.actions .coupon {
						padding-bottom: 12px;
					}
					.oxy-ou-cart-items table.cart td.actions button[type=submit] {
						float: none;
					}

					.oxy-ou-cart-items .product-quantity .quantity {
						justify-content: flex-end;
						width: auto;
					}
				}
				';

			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();
		$disable = isset($original[$prefix . '_pm_disable']) ? $original[$prefix . '_pm_disable'] : 'no';

		if( $disable == 'no' ) {
			$css .= $selector . ' td.product-quantity input[type=number]::-webkit-outer-spin-button,
					' . $selector . ' td.product-quantity input[type=number]::-webkit-inner-spin-button {
						-webkit-appearance: none;
						margin: 0;
						display: none;
					}
					' . $selector . ' td.product-quantity input.qty {
						-moz-appearance:textfield;
						margin: 0;
						background: none;
						border: none;
						border-radius: 0;
						min-width: auto;
						padding: 0;
						width: 35px;
					}';
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooCartItems();