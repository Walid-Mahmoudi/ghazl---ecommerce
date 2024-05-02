<?php

class OUWooBuyNow extends UltimateWooEl {

	public $css_added = false;
	public $js_added = false;

	function name() {
		return __( "Buy Now Button", 'oxyultimate-woo' );
	}

	function slug() {
		return "ouwoo_buynow";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function tag() {
		return 'a';
	}

	function init() {
		$this->El->useAJAXControls();

		add_action("oxygen_vsb_component_attr", [ $this, 'component_attrs' ], 99 );
		add_filter( 'do_shortcode_tag', [$this, 'check_permission'], 99, 3 );
	}

	function check_permission( $output, $tag, $options ) {
		if( $tag == 'oxy-ouwoo_buynow' ) {
			$ct_options = json_decode( $options['ct_options'], true );

			if( ! $this->hasPermission( $ct_options['original'], 'oxy-ouwoo_buynow_' ) )
				return;
		}

		return $output;
	}

	function component_attrs( $options ) {
		if( strstr( $options['classes'], 'oxy-ouwoo-buynow' ) ) {
			$slug = 'oxy_ouwoo_buynow_';
			$attrs = array();

			if( ! $this->hasPermission( $options, $slug ) )
				return;

			$attrs[] = "role=button";

			if(! empty( $options[ $slug . 'rel'] ) ) { 
				$attrs[] = 'rel="' . $options[ $slug . 'rel'] . '"'; 
			}

			$aria_label = ! empty( $options[ $slug . 'aria_label'] ) ? $options[ $slug . 'aria_label'] : esc_html__('Buy Now', "oxyultimate-woo");
			$attrs[] = 'aria-label="' . $aria_label . '"';

			$using_in_loop 	= !empty( $options[$slug . 'using_in_loop'] ) ? $options[$slug . 'using_in_loop'] : 'no';
			$keep_cart 		= !empty( $options[$slug . 'keep_cart_items'] ) ? $options[$slug . 'keep_cart_items'] : 'no';
			$redirect_link 	= !empty( $options[$slug . 'redirect_url'] ) ? $options[$slug . 'redirect_url'] : 'checkout';
			$link 			= !empty( $options[$slug . 'custom_url'] ) ? $options[$slug . 'custom_url'] : false;

			$url = ( $redirect_link == 'custom' && ! $link ) ? esc_url( $link ) : wc_get_checkout_url();

			if( $using_in_loop == 'yes' ) {
				$url = add_query_arg([ 
						'add_to_cart' 		=> get_the_ID(), 
						'keep_cart_items' 	=> $keep_cart, 
						'ou_buy_now' 		=> 'yes' 
					], 
					$url 
				);
			}

			$attrs[] = 'href="'. $url . '"';

			$attrs[] = 'data-in-loop="'. $using_in_loop . '"';
			$attrs[] = 'data-keep-cart="'. $keep_cart . '"';
			$attrs[] = 'data-product_id="'. get_the_ID() . '"';

			echo implode( ' ', $attrs );
		}
	}

	function controls() {
		$this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'button_text',
			'name' 		=> esc_html__('Button Text', "oxyultimate-woo"),
			'default' 	=> esc_html__('Buy Now', "oxyultimate-woo"),
		])->setParam('description', __('Click on Apply Params button to see the changes.', "oxyultimate-woo"));

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'aria_label',
			'name' 		=> esc_html__('Aria Label', "oxyultimate-woo"),
			'default' 	=> esc_html__('Buy Now', "oxyultimate-woo"),
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'rel',
			'name' 		=> esc_html__('Rel Attribute', "oxyultimate-woo"),
		])->setParam('placeholder', 'noopener noreferrer nofollow');

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> esc_html__('Redirect to', "oxyultimate-woo"),
			'slug' 		=> 'redirect_url'
		])->setValue([
			'checkout' 	=> esc_html__('Checkout Page', "oxyultimate-woo"),
			'custom' 	=> esc_html__('Custom Url', "oxyultimate-woo"),
		])->setDefaultValue('checkout')->setParam('description', __('Redirecting to the selected URL after adding the product to cart.', "oxyultimate-woo"));

		$redirect_url = $this->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouwoo_buynow_custom_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouwoo_buynow_custom_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ouwoo_buynow_custom_url\');iframeScope.checkResizeBoxOptions(\'oxy-ouwoo_buynow_custom_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="https://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ouwoo_buynow_custom_url\')">set</div>
			</div>
			',
			"custom_url"
		);
		$redirect_url->setParam( 'heading', __('Custom URL') );
		$redirect_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouwoo_buynow_redirect_url']=='custom'" );

		$options = $this->addControlSection('extra_options', __('Extra options'), "assets/icon.png", $this );

		$options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Keep existing cart items?', "oxyultimate-woo"),
			'slug' 		=> 'keep_cart_items',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no',
		]);

		$options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Using inside WP Query/Repeater?', "oxyultimate-woo"),
			'slug' 		=> 'using_in_loop',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no',
		]);

		$options->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Disable button for these products', "oxyultimate-woo"),
			'slug' 		=> 'exclude_products',
		])->setParam('placeholder', esc_html__('Enter product ids with comma', 'oxyultimate-woo'));

		$options->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Disable button for these product types', "oxyultimate-woo"),
			'slug' 		=> 'exclude_product_types',
		])->setParam('placeholder', 'simple,grouped,variable,external');

		$selector = ' '; $selectorHover = ':hover';

		$icon = $this->addControlSection('btn_icon', __('Icon'), "assets/icon.png", $this );

		$icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxyultimate-woo"),
				"slug" 			=> 'btn_icon',
			)
		)->rebuildElementOnChange();

		$icon->addStyleControl(
			array(
				"name" 			=> __('Size', "oxyultimate-woo"),
				"slug" 			=> "icon_size",
				"selector" 		=> 'svg',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)
		->setRange(20, 50, 2)
		->setUnits("px", "px");

		$icon->addStyleControl(
			array(
				"control_type" 	=> 'radio',
				"name" 			=> esc_html__('Position', "oxyultimate-woo"),
				"slug" 			=> 'btn_icon_pos',
				'value' 		=> ['row-reverse' => __('Left'), 'row' => __('Right')],
				'default' 		=> 'row',
				"selector" 		=> ' ',
				"property" 		=> 'flex-direction'
			)
		);

		$icon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"selector" 		=> ' ',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'gap',
				'value' 		=> '8'
			)
		)
		->setRange(0, 100, 1)
		->setUnits("px", "px,%,em");

		$icon->addStyleControls([
			[
				'name' 		=> __('Color', "oxyultimate-woo"),
				'selector' 	=> 'svg.bn-btn-icon',
				'property' 	=> 'color',
			],
			[
				'name' 		=> __('Hover Color', "oxyultimate-woo"),
				'selector' 	=> ':hover svg.bn-btn-icon',
				'property' 	=> 'color',
			]
		]);

		$style = $this->addControlSection('btn_style', __('Button Style'), "assets/icon.png", $this );

		$style->addStyleControl(
			array(
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
			)
		)->setRange(0, 1000, 10)->setUnits('px', 'px,em,%,vw');

		$style->addPreset(
			"padding",
			"bn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$style->addPreset(
			"margin",
			"bn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Color
		$style->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Background Hover Color', "oxyultimate-woo"),
					'selector' 	=> ':hover',
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Hover Text Color', "oxyultimate-woo"),
					'selector' 	=> ':hover',
					'property' 	=> 'color',
				)
			)
		);

		//* Typography
		$style->typographySection( __('Fonts', "oxyultimate-woo"), $selector, $this );

		//* Border
		$style->borderSection( __('Borders', "oxyultimate-woo"), $selector, $this );
		$style->borderSection( __('Hover Borders', "oxyultimate-woo"), $selectorHover, $this );
		
		//* Box Shadow
		$style->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
		$style->boxShadowSection( __('Hover Box Shadow', "oxyultimate-woo"), $selectorHover, $this );
	}

	function render( $options, $default, $content ) {

		if( ! $this->hasPermission( $options ) )
			return;

		$button_text 	= !empty( $options['button_text'] ) ? $options['button_text'] : __('Buy Now', 'oxyultimate-woo');

		echo "<span class='button-text'>{$button_text}</span>";

		$icon = isset($options['btn_icon']) ? esc_html( $options['btn_icon'] ) : false;
		if( $icon ) {
			global $oxygen_svg_icons_to_load;
			$oxygen_svg_icons_to_load[] = $icon;

			echo '<svg class="bn-btn-icon"><use xlink:href="#' . $icon . '"></use></svg>';
		}

		if( ! $this->isBuilderEditorActive() && ! $this->js_added ) {
			$this->js_added = true;
			add_action('wp_footer', array( $this, 'buynow_js_scripts'), 99 );
		}
	}

	public function hasPermission( $settings, $slug = '' ) {
		global $product;

		$product = WC()->product_factory->get_product( get_the_ID() );

		if( $product === false )
			return false;

		if ( ! $product->is_in_stock() )
			return false;

		$used_in_loop = ! empty( $settings[ $slug. 'using_in_loop'] ) ? $settings[$slug. 'using_in_loop'] : 'no';
		$exclude_products = !empty( $settings[$slug. 'exclude_products'] ) ? $settings[$slug. 'exclude_products' ] : false;
		$exclude_product_types = !empty( $settings[$slug. 'exclude_product_types'] ) ? $settings[$slug. 'exclude_product_types'] : false;

		if( ! empty( $exclude_products ) ) {
			$exclude_products = explode( ",", $exclude_products );
			if( in_array( $product->get_id(), $exclude_products) )
				return false;
		}

		if( ! empty( $exclude_product_types ) ) {
			$exclude_prdtypes = explode( ",", $exclude_product_types );
			if( in_array( $product->get_type(), $exclude_prdtypes) )
				return false;
		}

		if( is_singular( 'product' ) && $used_in_loop == 'no' ) {
			return true;
		}

		return ( 'yes' == $used_in_loop && 'simple' !== $product->get_type() ) ? false : true;
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$this->css_added = true;

			return '.oxy-ouwoo-buynow {
						border: 1px solid #999;
						display: flex; 
						padding: 8px 16px;
						align-items: center;
						justify-content: center;
						width: 100%;
					}
					.oxy-ouwoo-buynow svg{
						width: 20px;
						height: 20px;
						fill: currentColor;
					}';
		}
	}

	function buynow_js_scripts() {
	?>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', () => {
			const $elements = document.querySelectorAll('.oxy-ouwoo-buynow');

			if( $elements.length > 0 ) 
			{
				$elements.forEach(( el ) => {
					doBuyNow( el );
				});
			}
		});

		let doBuyNow = ( btn ) => {
			
			let keepcart = btn.getAttribute('data-keep-cart'),
				usedinloop = btn.getAttribute('data-in-loop');

			if( usedinloop == 'no' ) 
			{
				const vforms = document.querySelectorAll('.variations_form')
				if( vforms.length > 0 ) 
				{
					let eventListener = 'mouseover';

					if('ontouchstart' in window) // iOS & android
						eventListener = 'touchstart';
					else if(window.navigator.msPointerEnabled) // Win8
						eventListener = 'touchstart';
					else if('ontouchstart' in document.documentElement)
						eventListener = 'touchstart';

					vforms.forEach(($form) => {
						$form.addEventListener(eventListener, function(event) {
							let hasFields = $form.getAttribute('data-append-fields');

							if( typeof hasFields !== 'undefined' && hasFields == 'yes' ) {
								$form.querySelector('.ou-buy-now-inp').remove();
								$form.querySelector('.ou-kp-cart-inp').remove();
								$form.querySelector('.ou-rd-url-inp').remove();

								$form.removeAttribute('data-append-fields');
							}
						})
					})
				}

				btn.addEventListener('click', function(event) {
					event.preventDefault();
					event.stopPropagation();

					let product_id = this.getAttribute('data-product_id'),
						repeater = event.target.closest('.oxy-dynamic-list');

					const forms = (repeater) ? repeater.querySelectorAll('.cart') : document.querySelectorAll('.cart');
					if( forms.length > 0 ) 
					{
						forms.forEach(($form) => {
							let atcbtn = $form.querySelector('.single_add_to_cart_button'),
								isappendfields = $form.getAttribute('data-append-fields'),
								form_product_id = ( $form.hasAttribute('data-product_id') ) ? $form.getAttribute('data-product_id') : atcbtn.getAttribute('value');

								if(atcbtn && atcbtn.classList.contains('disabled') ) {
									if ( atcbtn.classList.contains('wc-variation-is-unavailable') ) {
										window.alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
									} else if ( atcbtn.classList.contains('wc-variation-selection-needed') ) {
										window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
									}
									
									return;
								}

							if( parseInt( product_id ) == parseInt( form_product_id ) ) {
								if( ! isappendfields || isappendfields != 'yes' ) {
									let bn = document.createElement("input");
									bn.setAttribute("type", "hidden");
									bn.setAttribute("name", "ou_buy_now");
									bn.setAttribute("value", "yes");
									bn.setAttribute("class", "ou-buy-now-inp");

									$form.appendChild(bn);

									let kci = document.createElement("input");
									kci.setAttribute("type", "hidden");
									kci.setAttribute("name", "keep_cart_items");
									kci.setAttribute("value", keepcart);
									kci.setAttribute("class", "ou-kp-cart-inp");

									$form.appendChild(kci);

									let rurl = document.createElement("input");
									rurl.setAttribute("type", "hidden");
									rurl.setAttribute("name", "ou_redirect_url");
									rurl.setAttribute("value", this.getAttribute('href'));
									rurl.setAttribute("class", 'ou-rd-url-inp');

									$form.appendChild(rurl);

									if($form.classList.contains('variations_form') === false ) {
										let prd = document.createElement("input");
										prd.setAttribute("type", "hidden");
										prd.setAttribute("name", "product_id");
										prd.setAttribute("value", product_id);
										prd.setAttribute("class", 'ou-prd-inp');

										$form.appendChild(prd);
									}

									$form.setAttribute('data-append-fields', 'yes');
								}

								//atcbtn.click()

								$form.submit();
							}
						})
					}
				})
			}
		}
	</script>
	<?php
	}

}

new OUWooBuyNow();