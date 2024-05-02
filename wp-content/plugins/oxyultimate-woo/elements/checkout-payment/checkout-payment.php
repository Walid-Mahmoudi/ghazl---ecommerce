<?php

class OUCheckoutPyament extends UltimateWooEl {
	public $css_added = false;
	public $place_order_text = '';

	function name() {
		return __( "Payment", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_place_payment";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 8;
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_place_payment-elements-label"
				ng-if="isActiveName('oxy-ou_place_payment')&&!hasOpenTabs('oxy-ou_place_payment')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_place_payment-elements"
				ng-if="isActiveName('oxy-ou_place_payment')&&!hasOpenTabs('oxy-ou_place_payment')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 68 );
	}


	/******************************
	 * Container
	 ******************************/
	function containerBox() {
		$container = $this->addControlSection('container_section', __('Main Wrapper', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '#payment';

		$container->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $container->addControlSection('container_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"container_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"container_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Border
		$container->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$container->boxShadowSection( __('Box Shadow'), $selector, $this );
	}


	/******************************
	 * Payment Methods
	 ******************************/
	function paymentMethodsBox() {
		$methods = $this->addControlSection( 'methods_section', __('Methods Wrapper', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '#payment .wc_payment_methods';

		$methods->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $methods->addControlSection('methodsbox_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"listbox_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"listbox_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Border
		$methods->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$methods->boxShadowSection( __('Box Shadow'), $selector, $this );
	}


	/******************************
	 * Methods List
	 ******************************/
	function paymentMethodsList() {
		$list = $this->addControlSection( 'list_section', __('Methods List', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '#payment .wc_payment_methods li';

		$list->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $list->addControlSection('list_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"list_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"list_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Border
		$list->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$list->boxShadowSection( __('Box Shadow'), $selector, $this );
	}

	function selectedMethod() {

		$selected = $this->addControlSection( 'selected_list', __('Selected Method', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '#payment .wc_payment_methods li';

		$selected->addStyleControls([
			[
				'selector' 		=> $selector . '.selected-method',
				'property' 		=> 'background-color'
			],
			[
				'name' 		=> __('Background Color of Description', "oxyultimate-woo"),
				'selectors' => array(
					array(
						'selector' 	=> $selector . '.selected-method .payment_box',
						'property' 	=> 'background-color'
					),
					array(
						'selector' 	=> $selector . '.selected-method .payment_box::before',
						'property' 	=> 'border-bottom-color'
					)
				),
				'control_type' 	=> 'colorpicker'
			],
			[
				'name' 		=> __('Label Color', "oxyultimate-woo"),
				'selector' 	=> $selector . '.selected-method label',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Description Color', "oxyultimate-woo"),
				'selector' 	=> $selector . '.selected-method .payment_box',
				'property' 	=> 'color'
			]
		]);

		//* Border
		$selected->borderSection(__('Border'), $selector . '.selected-method', $this );

		//* Box Shadow
		$selected->boxShadowSection( __('Box Shadow'), $selector . '.selected-method', $this );
	}


	/******************************
	 * Radio Buttons
	 ******************************/
	function radioButtonControl() {
		$radios = $this->addControlSection( 'radio_buttons', __('Radio Buttons', "oxyultimate-woo"), 'assets/icon.png', $this );

		$radio = $radios->addControlSection( 'rb_size_color', __('Size & Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$radio_selector = '#payment input[type=radio]';
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
				'name' 			=> __('Selected Color', "oxyultimate-woo"),
				'selector' 		=> $radio_selector . ':checked',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Selected Alt Color', "oxyultimate-woo"),
				'selector' 		=> '#payment',
				'property' 		=> '--checked-radio-alt-color',
				'control_type' 	=> 'colorpicker',
			],
			[
				'name' 			=> __('Selected Border Color', "oxyultimate-woo"),
				'selector' 		=> $radio_selector . ':checked',
				'property' 		=> 'border-color'
			]
		]);

		$radio->addStyleControl(
			[
				'name' 			=> __('Selected Radio Button Bullet Size', "oxyultimate-woo"),
				'control_type' 	=> 'slider-measurebox',
				'selector' 		=> '#payment',
				'property' 		=> '--checked-bullet-size'
			]
		)->setUnits('px', 'px')->setRange(0,30,1)->setDefaultValue(4);		

		$spacing = $radios->addControlSection('rb_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"rb_margin",
			__("Gap Between Button & Text"),
			'#payment .payment_methods li input'
		)->whiteList();

		$radios->borderSection( __('Border'), $radio_selector, $this );

		$radios->typographySection(__('Label'), '#payment label', $this);

		$link = $radios->typographySection(__('Link'), '#payment label a', $this);
		$link->addStyleControl([
			'selector' 	=> '#payment label a:hover',
			'property' 	=> 'color',
			'name' 		=> __('Color on Hover', "oxyultimate-woo")
		]);
	}

	/******************************
	 * Payment Methods Description
	 ******************************/
	function methodsDescription() {
		$desc = $this->addControlSection( 'desc_section', __('Description', "oxyultimate-woo"), 'assets/icon.png', $this );

		$disable_desc = $desc->addControl( 'buttons-list', 'disable_desc', __('Hide Description', "oxyultimate-woo") );
		$disable_desc->setValue(['No', 'Yes']);
		$disable_desc->setValueCSS([
			'Yes' 	=> '#payment .payment_box{display: none;}'
		]);
		$disable_desc->setDefaultValue('No');

		$selector = '#payment .payment_box';

		$desc->addStyleControl([
			'name' 		=> __('Background Color'),
			'selectors' => array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'background-color'
				),
				array(
					'selector' 	=> $selector . '::before',
					'property' 	=> 'border-bottom-color'
				)
			),
			'control_type' 	=> 'colorpicker'
		]);

		$hide_arrow = $desc->addControl( 'buttons-list', 'disable_arrow', __('Hide Arrow', "oxyultimate-woo") );
		$hide_arrow->setValue(['No', 'Yes']);
		$hide_arrow->setValueCSS([
			'No' 	=> $selector . '::before{display: block}',
			'Yes' 	=> $selector . '::before{display: none;}'
		]);
		$hide_arrow->setDefaultValue('No');

		$spacing = $desc->addControlSection('desc_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"descbox_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"descbox_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Border
		$desc->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$desc->boxShadowSection( __('Box Shadow'), $selector, $this );

		$desc->typographySection(__('Typography'), $selector .','. $selector . ' p', $this);

		$link = $desc->addControlSection('desc_link', __('Link', "oxyultimate-woo"), 'assets/icon.png', $this );
		$link->addStyleControls([
			[
				'selector' 	=> $selector . ' p a',
				'property' 	=> 'color'
			],
			[
				'selector' 	=> $selector . ' p a:hover',
				'property' 	=> 'color',
				'name' 		=> __('Color on Hover', "oxyultimate-woo")
			]
		]);
	}

	/******************************
	 * Place Order Row
	 ******************************/
	function placeOrderRow() {
		$selector = '#payment .form-row.place-order';

		//* Main Wrapper
		$place = $this->addControlSection('order_section', __('Place Order Box', "oxyultimate-woo"), 'assets/icon.png', $this );

		$place->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $place->addControlSection('order_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"powrap_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"powrap_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Border
		$place->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$place->boxShadowSection( __('Box Shadow'), $selector, $this );
	}


	/******************************
	 * Terms & Condition
	 ******************************/
	function termsDetails() {
		$toc = $this->addControlSection('toc_section', __('Privacy Policy', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.woocommerce-terms-and-conditions-wrapper';

		//* Wrapper
		$tocwrap_color = $toc->addControlSection('tocwrap_color_section', __('Wrapper Color', "oxyultimate-woo"), 'assets/icon.png', $this );
		$tocwrap_color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		]);

		$tocwrap_sp = $toc->addControlSection('tocwrap_sp_section', __('Wrapper Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$tocwrap_sp->addPreset(
			"padding",
			"tocwrapsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$tocwrap_sp->addPreset(
			"margin",
			"tocwrapsp_margin",
			__("Margin"),
			$selector
		)->whiteList();


		//* Text Typography
		$textSelector = '.woocommerce-privacy-policy-text, .woocommerce-privacy-policy-text p';
		//* Typography
		$tg = $toc->typographySection(__('Typography'), $textSelector, $this );
		$tg->addStyleControls([
			[
				'name' 			=> __('Link Color', "oxyultimate-woo"),
				'selector' 		=> $textSelector . ' a',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Link Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $textSelector . ' a:hover',
				'property' 		=> 'color'
			]
		]);


		//* Border
		$toc->borderSection(__('Border'), $selector, $this );

		//* Box Shadow
		$toc->boxShadowSection( __('Box Shadow'), $selector, $this );
	}


	/******************************
	 * Buttons Config
	 ******************************/
	function buttonsConfig() {
		$button = $this->addControlSection('buttons_config', __('Buttons Config', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.ouwoo-checkout-buttons';

		$alignment = $button->addControl('buttons-list', 'buttons_alignment', __('Stack', "oxyultimate-woo"));
		$alignment->setValue(['Vertical', 'Horizontal']);
		$alignment->setValueCSS([
			'Horizontal' => $selector . '{flex-direction: row;}.ouwoo-checkout-buttons .return-to-shop-button{margin-right: 10px; margin-bottom: 0;}',
			'Vertical' => $selector . '{flex-direction: column;}.ouwoo-checkout-buttons .return-to-shop-button{margin-right: 0; margin-bottom: 10px;}',
		]);
		$alignment->setDefaultValue('Horizontal');

		$position = $button->addControl('buttons-list', 'buttons_position', __('Alignment', "oxyultimate-woo"));
		$position->setValue(['Left', 'Center', 'Right']);
		$position->setValueCSS([
			'Center' 	=> $selector . '{justify-content: center; align-items: center;}',
			'Left' 		=> $selector . '{justify-content: start; align-items: center;}',
			'Right' 	=> $selector . '{justify-content: flex-end; align-items: center;}',
		]);
		$position->setDefaultValue('Right');
		$position->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_place_payment_buttons_alignment']=='Horizontal'");

		$spacing = $button->addControlSection('btns_sp', __('Padding of Buttons', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector . ' .button',
				"property" 			=> 'padding-top',
				"control_type" 		=> 'measurebox',
				"unit" 				=> "px"
			)
		)->setParam('hide_wrapper_end', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector . ' .button',
				"property" 			=> 'padding-bottom',
				"control_type" 		=> 'measurebox',
				"unit" 				=> "px"
			)
		)->setParam('hide_wrapper_start', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector . ' .button',
				"property" 			=> 'padding-left',
				"control_type" 		=> 'measurebox',
				"unit" 				=> "px"
			)
		)->setParam('hide_wrapper_end', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector . ' .button',
				"property" 			=> 'padding-right',
				"control_type" 		=> 'measurebox',
				"unit" 				=> "px"
			)
		)->setParam('hide_wrapper_start', true);
	}



	/******************************
	 * Place Button
	 ******************************/
	function placeButton() {
		$button = $this->addControlSection('place_btn', __('Place Order', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '#payment button.button';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text', "oxyultimate-woo"),
			'slug' 		=> 'place_order_text',
			'default' 	=> __( 'Place order', 'woocommerce' ),
		])->setParam('description', __('Click on Apply Params button and apply the change.', "oxyultimate-woo"));

		$button->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,rem')->setRange(0, 1000, 10);

		$spacing = $button->addControlSection('place_btn_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-top'
			)
		)->setParam('hide_wrapper_end', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-bottom'
			)
		)->setParam('hide_wrapper_start', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-left'
			)
		)->setParam('hide_wrapper_end', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-right'
			)
		)->setParam('hide_wrapper_start', true);

		$color = $button->addControlSection('place_btn_color', __('Color'), 'assets/icon.png', $this );
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
	 * Return to Shop Button
	 ******************************/
	function returnShopButton() {
		$shopButton = $this->addControlSection('shop_btn', __('Shop Button', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '#payment .return-to-shop-button';

		$hideRB = $shopButton->addControl('buttons-list', 'shopbtn_hide', __('Hide It?', "oxyultimate-woo"));
		$hideRB->setValue(['No', 'Yes']);
		$hideRB->setValueCSS(['No' => $selector . '{display:block}', 'Yes' => $selector . '{display:none}' ]);
		$hideRB->setDefaultValue('No');
		$hideRB->whiteList();

		$shopButton->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text'),
			'slug' 		=> 'shop_text',
			'condition' => 'shopbtn_hide=No'
		])->setParam('description', __('Click on Apply Params button and apply the change.', "oxyultimate-woo"));

		$shopButton->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Button URL Source', "oxyultimate-woo"),
			'slug' 		=> 'shop_button_url_source',
			'value' 	=> ['shop' => __('Shop Page', "oxyultimate-woo"), 'custom' => __('Custom', "oxyultimate-woo")],
			'default' 	=> 'shop',
			'condition' => 'shopbtn_hide=No'
		]);

		$custom_url = $shopButton->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_place_payment_custom_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_place_payment_custom_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_place_payment_custom_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_place_payment_custom_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_place_payment_custom_url\')">set</div>
			</div>
			',
			"custom_url",
			$shopButton
		);
		$custom_url->setParam( 'heading', __('Custom URL', "oxyultimate-woo") );
		$custom_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_place_payment_shop_button_url_source']=='custom'" );

		$shopButton->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'condition' 	=> 'shopbtn_hide=No'
			]
		)->setUnits('px', 'px,%,em,auto,rem')->setRange(0, 1000, 10);

		$spacing = $shopButton->addControlSection('shop_btn_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-top'
			)
		)->setParam('hide_wrapper_end', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-bottom'
			)
		)->setParam('hide_wrapper_start', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-left'
			)
		)->setParam('hide_wrapper_end', true);

		$spacing->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'margin-right'
			)
		)->setParam('hide_wrapper_start', true);


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
		$shopButton->borderSection(__('Hover Border'), $selector . ':hover', $this );

		//* Box Shadow
		$shopButton->boxShadowSection( __('Box Shadow'), $selector, $this );
		$shopButton->boxShadowSection( __('Hover Shadow'), $selector . ':hover', $this );
	}

	function controls() {

		$this->containerBox();

		$this->paymentMethodsBox();

		$this->paymentMethodsList();

		$this->radioButtonControl();

		$this->methodsDescription();

		$this->selectedMethod();

		$this->placeOrderRow();

		$this->termsDetails();

		$this->buttonsConfig();

		$this->placeButton();

		$this->returnShopButton();
	}

	function render($options, $defaults, $content) {
		if( ! method_exists( WC()->cart, 'needs_payment') )
        	return;

		$class = ' class="no-items-in-cart"';

		if ( ! is_null( WC()->cart ) && ! WC()->cart->is_empty() ) {
			$class = ' class="has-items-in-cart"';
		}

		$this->place_order_text = isset( $options['place_order_text'] ) ? wp_kses_post( $options['place_order_text'] ) : __( 'Place order', 'woocommerce' );
		add_filter( 'woocommerce_order_button_text', function($btn_text) { 
			if( ! empty( $this->place_order_text ) ) { 
				return $this->place_order_text;
			} else { 
				return $btn_text;
			} 
		});

		if( 
			isset($options['shop_button_url_source']) 
			&& $options['shop_button_url_source'] == 'custom' 
			&& isset($options['custom_url']) 
		) {
			$shop_button_url = $options['custom_url'];
		} else {
			$shop_button_url = ( wc_get_page_id( 'shop' ) > 0 ) ? wc_get_page_permalink( 'shop' ) : '#';
		}

		$dataAttr = ' data-shopbtntxt="' . ( isset($options['shop_text']) ? wp_kses_post( $options['shop_text'] ) : esc_html__('Continue Shopping', 'woocommerce') ) .'"'; 
		$dataAttr .= ' data-shopbtnurl="' . esc_url( $shop_button_url ) .'"'; 

		echo '<div id="order_review"' . $class . $dataAttr . '>';
		woocommerce_checkout_payment();
		echo '</div>';

		do_action( 'woocommerce_checkout_after_order_review' );

		$js = '';

		if( $this->isBuilderEditorActive() ) {
			$js = "$('.wc_payment_method').each(function() {
					if( $(this).find('input[type=radio]').is(':checked') ) {
						$(this).addClass('selected-method');
					}
				});";
		}

		$js ="jQuery(document).ready(function($){" . "\n". $js . "\n" . "
				if( $('#payment .return-to-shop-button').length ) {
					$('#payment .return-to-shop-button').attr('href', $('#order_review').attr('data-shopbtnurl') );
					$('#payment .return-to-shop-button').text( $('#order_review').attr('data-shopbtntxt') );
				}

				$(document.body).on('payment_method_selected updated_checkout', function() {
					$('.wc_payment_method').removeClass('selected-method');

					$('.wc_payment_method').each(function() {
						if( $(this).find('input[type=radio]').is(':checked') ) {
							$(this).addClass('selected-method');
						}
					});

					if( $('#payment .return-to-shop-button').length ) {
						$('#payment .return-to-shop-button').attr('href', $('#order_review').attr('data-shopbtnurl') );
						$('#payment .return-to-shop-button').text( $('#order_review').attr('data-shopbtntxt') );
					}
				});
			});";

		if( $this->isBuilderEditorActive() ) {
			$this->El->builderInlineJS($js);
		} else {
			$this->El->footerJS($js);
		}

		add_filter('body_class', function($classes){ $classes[] = 'ouwoo-payment-comp'; return $classes; });
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-place-payment {
				width: 100%;
				min-height: 40px;
			}
			#payment {
				--checked-bullet-size: 7px;
				--checked-radio-alt-color: #ffffff;
			}
			.oxy-ou-place-payment #payment input[type=radio]:checked,
			.oxy-ou-place-payment #payment input[type=radio]:checked:hover {
				box-shadow: inset 0 0 0 var(--checked-bullet-size) var(--checked-radio-alt-color);
			}
			.oxy-ou-place-payment #payment ul.payment_methods li {
				display: -webkit-flex;
				display: -moz-flex;
				display: flex;
				flex-wrap: wrap;
			}
			.oxy-ou-place-payment #payment ul.payment_methods li input {
				margin-top: 8px;
				margin-right: 6px;
			}
			.ouwoo-checkout-buttons {
				display: -webkit-flex;
				display: -moz-flex;
				display: flex;
				flex-direction: row;
				justify-content: flex-end;
				flex-wrap: wrap;
			}
			.ouwoo-checkout-buttons .woocommerce-terms-and-conditions {
				width: 100%;
				word-wrap: break-word;
			}
			.ouwoo-checkout-buttons .form-row {
				flex: 1 0 100%;
			}
			.oxy-ou-place-payment #payment label {
				display: flex;
				flex-direction: row;
				align-items: center;
				flex: 1;
			}
			.oxy-ou-place-payment #payment label a {
				margin-left: 8px;
				line-height: 1.3!important;
			}
			#payment .return-to-shop-button{margin-right: 10px;}
			';

			$this->css_added = true;
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUCheckoutPyament();

add_action( 'woocommerce_review_order_before_submit', 'ouwoo_review_order_button_continue_shopping', 11 );
function ouwoo_review_order_button_continue_shopping() {
	$url = ( wc_get_page_id( 'shop' ) > 0 ) ? wc_get_page_permalink( 'shop' ) : '#';
?>
	<a class="button return-to-shop-button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', $url ) ); ?>">
		<?php echo esc_html__('Continue Shopping', 'woocommerce'); ?>
	</a>
<?php
}
add_action( 'woocommerce_review_order_before_submit', function(){ echo '<div class="ouwoo-checkout-buttons">'; }, 1);
add_action( 'woocommerce_review_order_after_submit', function(){ echo '</div>'; }, 1);

remove_action( 'woocommerce_review_order_before_submit', 'woocommerce_gzd_template_set_order_button_remove_filter', 1500 );
if ( get_option( 'gm_deactivate_checkout_hooks', 'off' ) == 'off' && class_exists('WGM_Template') ) {
	remove_filter( 'woocommerce_order_button_html', array( 'WGM_Template', 'remove_order_button_html' ), 9999 );
}