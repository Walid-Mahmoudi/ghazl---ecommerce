<?php

class OUWooCheckoutBillingForm extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Billing Form", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_billing_form";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 5;
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_billing_form-elements-label"
				ng-if="isActiveName('oxy-ou_billing_form')&&!hasOpenTabs('oxy-ou_billing_form')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_billing_form-elements"
				ng-if="isActiveName('oxy-ou_billing_form')&&!hasOpenTabs('oxy-ou_billing_form')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 65 );
	}

	function controls() {
		$this->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">'. __("Fields style options are available in Checkout Form Builder component." , "oxyultimate-woo") .'</div>', 
			'description'
		)->setParam('heading', __('Note:', "oxyultimate-woo"));
	}

	function render($options, $defaults, $content) {
		
		if ( WC()->checkout()->get_checkout_fields() ) :

			do_action( 'woocommerce_checkout_before_customer_details' );

			do_action( 'woocommerce_checkout_billing' );

		endif;
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-billing-form {
				width: 100%;
				min-height: 40px;
			}
			';

			$this->css_added = true;
		}

		return $css;
	}
}

new OUWooCheckoutBillingForm();