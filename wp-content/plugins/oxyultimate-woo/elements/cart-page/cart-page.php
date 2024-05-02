<?php

class OUWooCartPage extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Cart Builder", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_cart_page";
	}

	function ouwoo_button_place() {
		return "cart";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_cart_page-elements-label"
				ng-if="isActiveName('oxy-ou_cart_page')&&!hasOpenTabs('oxy-ou_cart_page')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_cart_page-elements"
				ng-if="isActiveName('oxy-ou_cart_page')&&!hasOpenTabs('oxy-ou_cart_page')">
				<?php do_action("oxygen_add_plus_ultimatewoo_cart"); ?>
			</div>
		<?php }, 60 );
	}

	function button_priority() {
		return 2;
	}


	function message() {
		$message = $this->addControlSection('message_section', __('Empty Cart Text', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.cart-empty';

		$message->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setRange(0, 1000, 10);

		$message->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $message->addControlSection('empty_msg_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"emsg_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"emsg_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Typography
		$message->typographySection(__('Typography'), $selector, $this );

		$icon = $message->addControlSection('icon_section', __('Icon'), 'assets/icon.png', $this );

		$icon->addControl(
			'buttons-list',
			'hide_icon',
			__('Hide Icon?')
		)->setValue([
			'No',
			'Yes'
		])->setValueCSS([
			'No' 	=> '.cart-empty.woocommerce-info::before{display: block}',
			'Yes' 	=> '.cart-empty.woocommerce-info::before{display: none}'
		])->setDefaultValue('No');

		$icon->addStyleControl(
			[
				'selector' 		=> '.cart-empty.woocommerce-info::before',
				'property' 		=> 'color'
			]
		);

		//* Border
		$message->borderSection(__('Border'), $selector, $this );
		$message->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ':hover', $this );
	}

	function button() {
		$shopButton = $this->addControlSection('empty_shop_btn', __('Empty Cart Button', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.return-to-shop a.button';

		$shopButton->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setRange(0, 1000, 10);

		$spacing = $shopButton->addControlSection('empty_shop_btn_sp', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"esbtn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"esbtn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$color = $shopButton->addControlSection('empty_btn_color', __('Color', "oxyultimate-woo"), 'assets/icon.png', $this );
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

		$rd = $this->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_cart_page_redirect_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_cart_page_redirect_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_cart_page_redirect_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_cart_page_redirect_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_cart_page_redirect_url\')">set</div>
			</div>
			',
			"redirect_url"
		);
		$rd->setParam( 'heading', __('Redirect URL') );
		$rd->setParam( 'description', __('Empty cart page will redirect to specified URL.') );

		$this->message();

		$this->button();
	}

	function render($options, $defaults, $content) {
		
		// Constants.
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );//

		$nonce_value = wc_get_var( $_REQUEST['woocommerce-shipping-calculator-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		// Update Shipping. Nonce check uses new value and old value (woocommerce-cart). @todo remove in 4.0.
		if ( ! empty( $_POST['calc_shipping'] ) && ( wp_verify_nonce( $nonce_value, 'woocommerce-shipping-calculator' ) || wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) ) { // WPCS: input var ok.
			WC_Shortcode_Cart::calculate_shipping();

			// Also calc totals before we check items so subtotals etc are up to date.
			WC()->cart->calculate_totals();
		}

		// Check cart items are valid.
		do_action( 'woocommerce_check_cart_items' );

		$data = '';

		if( isset(	$options['redirect_url']	)	)
			$data = ' data-cb-redirect="' . $options['redirect_url'] . '"';

		echo '<div class="woocommerce oxy-inner-content"'.$data.'>';
		
		if ( is_null( WC()->cart ) || WC()->cart->is_empty() ) {
			
			wc_get_template( 'cart/cart-empty.php' );

		} else {

			do_action( 'woocommerce_before_cart' );

			if( $content ) {
				if( function_exists('do_oxygen_elements') )
					echo do_oxygen_elements( $content );
				else
					echo do_shortcode( $content );
			}

			do_action( 'woocommerce_after_cart' );
		}
		
		echo '</div>';

		if( ! $this->isBuilderEditorActive() ) {
			$js = "jQuery(document).ready(function($){
						if ( typeof Cookies.get( 'woocommerce_items_in_cart' ) == 'undefined' && 
							typeof $('.oxy-ou-cart-page .oxy-inner-content').attr('data-cb-redirect') != 'undefined'
						) {
							window.location = $('.oxy-ou-cart-page .oxy-inner-content').attr('data-cb-redirect');
						}

						$( document.body ).on( 'wc_cart_emptied', function(){
							if( typeof $('.oxy-ou-cart-page .oxy-inner-content').attr('data-cb-redirect') != 'undefined')
								window.location = $('.oxy-ou-cart-page .oxy-inner-content').attr('data-cb-redirect');
							else
								window.location.reload();
						});
					});";

			$this->El->footerJS( $js );
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-cart-page{ display: flex; flex-direction: column; min-height: 40px; width: 100%}
					body.oxygen-builder-body .empty-cart{display: none}';

			$this->css_added = true;
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooCartPage();