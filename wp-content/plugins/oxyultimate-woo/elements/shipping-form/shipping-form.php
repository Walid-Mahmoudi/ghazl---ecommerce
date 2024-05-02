<?php
class OUCheckoutShippingForm extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Shipping Form", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_shipping_form";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 6;
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_shipping_form-elements-label"
				ng-if="isActiveName('oxy-ou_shipping_form')&&!hasOpenTabs('oxy-ou_shipping_form')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_shipping_form-elements"
				ng-if="isActiveName('oxy-ou_shipping_form')&&!hasOpenTabs('oxy-ou_shipping_form')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 66 );
	}

	function controls() {
		$this->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">' . __('Fields style options are available in Checkout Form Builder component.') . '</div>', 
			'description'
		)->setParam('heading', 'Note:');
	}

	function render($options, $defaults, $content) {
		if ( WC()->checkout()->get_checkout_fields() && method_exists( WC()->cart, 'needs_shipping_address') ) :

			do_action( 'woocommerce_checkout_shipping' );

			do_action( 'woocommerce_checkout_after_customer_details' );

		endif;
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-shipping-form {
				width: 100%;
				min-height: 40px;
			}';

			$this->css_added = true;
		}

		return $css;
	}
}

new OUCheckoutShippingForm();