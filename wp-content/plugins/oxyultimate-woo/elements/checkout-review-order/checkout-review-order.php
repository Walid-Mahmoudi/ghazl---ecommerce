<?php

class OUCheckoutReviewOrder extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Review Order", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_review_order";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 7;
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_review_order-elements-label"
				ng-if="isActiveName('oxy-ou_review_order')&&!hasOpenTabs('oxy-ou_review_order')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_review_order-elements"
				ng-if="isActiveName('oxy-ou_review_order')&&!hasOpenTabs('oxy-ou_review_order')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 67 );
	}

	function orderTable() {
		$table = $this->addControlSection('order_table', __('Table Contents', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = 'table.woocommerce-checkout-review-order-table';

		$spacing = $table->addControlSection('order_table_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"ordtble_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"padding",
			"ordtble_padding",
			__("Cell Padding"),
			$selector . ' tr td,' . $selector . ' tbody th,' . $selector . ' tfoot th'
		)->whiteList();

		$bg = $table->addControlSection('table_bgcolor', __('Background Color'), 'assets/icon.png', $this );
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
				'name' 			=> __('Background Color for Labels', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tfoot th',
				'property' 		=> 'background-color'
			]
		]);

		$cell_border = $table->addControlSection('cell_border', __('Cell Border', "oxyultimate-woo"), 'assets/icon.png', $this );
		$cell_border->addStyleControls([
			[
				'selector' 		=> $selector . ' tr',
				'property' 		=> 'border-top-color',
			],
			[
				'selector' 		=> $selector . ' tr',
				'property' 		=> 'border-top-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'default' 		=> '1'
			],
			[
				'selector' 		=> $selector . ' tr',
				'property' 		=> 'border-top-style',
				'control_type' 	=> 'radio',
				'value' 		=> ['none', 'solid', 'dashed', 'dotted']
			]
		]);

		//* Label Font
		$labelfont = $table->typographySection(__('Label Typography', "oxyultimate-woo"), $selector . ' tfoot th', $this );

		//* Price Font
		$price = $table->typographySection(__('Price Typography', "oxyultimate-woo"), $selector . ' tfoot td, ' . $selector . ' tfoot td .woocommerce-Price-amount', $this );
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

		//* Border
		$table->borderSection(__('Outer Border', "oxyultimate-woo"), $selector, $this );

		//* Box Shadow
		$table->boxShadowSection(__('Box Shadow'), $selector, $this );
	}
	

	/******************************
	 * Products
	 ******************************/
	function productControl() {
		$products = $this->addControlSection('prd_section', __('Products',"woocommerce"), 'assets/icon.png', $this );

		$selector = '.woocommerce-checkout-review-order-table';

		$hide_prdlist = $products->addControl('buttons-list', 'hide_prodlist', __('Disable Products details', "oxyultimate-woo"));
		$hide_prdlist->setValue(['No', 'Yes']);
		$hide_prdlist->setValueCSS([
			'Yes' 	=> $selector . ' > thead,'.$selector . ' > tbody{display: none;}'
		]);
		$hide_prdlist->setDefaultValue('No');
		$hide_prdlist->whiteList();

		$products->addStyleControls([
			[
				'name' 			=> __('Background Color of Table Heading Row', "oxyultimate-woo"),
				'selector' 		=> $selector . ' thead tr',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Odd Cell', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tbody tr:nth-child(odd)',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color of Even Cell', "oxyultimate-woo"),
				'selector' 		=> $selector . ' tbody tr:nth-child(even)',
				'property' 		=> 'background-color'
			]
		]);

		$heading = $products->typographySection( __('Heading Row', "oxyultimate-woo"), $selector . ' thead tr th', $this );
		$heading->addStyleControl([
			'name' 			=> __('Subtotal Text Align', "oxyultimate-woo"),
			'selector' 		=> $selector . ' thead .product-total',
			'property' 		=> 'text-align',
			'control_type' 	=> 'radio',
			'value' 		=> ['left', 'center', 'right', 'justify']
		]);

		$heading->addPreset(
			"padding",
			"prdtblh_padding",
			__("Padding"),
			$selector . ' thead tr th'
		)->whiteList();

		$products->typographySection(__('Title'), $selector . ' tbody .product-name', $this );
		$products->typographySection(__('Quantity', "woocommerce"), $selector . ' .product-name .product-quantity', $this );
		$products->typographySection(__('Price', "woocommerce"), $selector . ' tbody .product-total,' . $selector . ' tbody .product-total .woocommerce-Price-amount', $this );
	}


	/******************************
	 * Shipping Methods
	 ******************************/
	function radioButtonControl() {
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
				'selector' 		=> $radio_selector,
				'property' 		=> 'border-color'
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
	 * Total Row
	 ******************************/
	function totalRowControl() {
		//* Order Total Row
		$total = $this->addControlSection('order_total_row', __('Total Row', "oxyultimate-woo"), 'assets/icon.png', $this );
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
	}

	function controls() {
		$this->orderTable();

		$this->productControl();

		$this->radioButtonControl();

		$this->totalRowControl();
	}

	function render($options, $defaults, $content) {
		/*if( ! function_exists('get_cart') )
        	return;*/
        
        do_action( 'woocommerce_checkout_before_order_review' );

		echo '<div class="woocommerce-checkout-review-order">';
		woocommerce_order_review();
		echo '</div>';
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-review-order {
				width: 100%;
				min-height: 40px;
				--checked-bullet-size: 4px;
				--checked-radio-alt-color: #ffffff;
			}
			.product-name-wrap {
				display: inline-flex;
				flex-direction: column
			}
			.product-name-wrap a:last-child {
				margin-left: 5px;
			}
			.oxy-ou-review-order #shipping_method input[type=radio]:checked,
			.oxy-ou-review-order #shipping_method input[type=radio]:checked:hover {
				box-shadow: inset 0 0 0 var(--checked-bullet-size) var(--checked-radio-alt-color);
			}
			.oxy-ou-review-order thead tr:first-child{border-top: none}
			';

			$this->css_added = true;
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUCheckoutReviewOrder();