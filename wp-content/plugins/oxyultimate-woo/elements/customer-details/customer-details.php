<?php

class OUWooCustomerDetails extends UltimateWooEl {

	public $css_added = false;

	function name() {
		return __( "Customer Details", 'oxyultimate-woo' );
	}

	function slug() {
		return "customer-details";
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 2;
	}

	function tag() {
		return [ 'default' => 'span', 'choices' => 'div,p,span'];
	}

	function controls() {
		$this->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Info', "oxyultimate-woo"),
			'slug' 	=> 'details_type',
			'value' => [
				'name' 		=> __( 'Name ', 'woocommerce' ),
				'email' 	=> __( 'Email', 'woocommerce' ),
				'phone' 	=> __( 'Phone', 'woocommerce' ),
				'company'    => __( 'Company', 'woocommerce' ),
				'address_1'  => __( 'Address line 1', 'woocommerce' ),
				'address_2'  => __( 'Address 2', "woocommerce"),
				'city'       => __( 'City', "woocommerce"),
				'state'      => __( 'State / County', "woocommerce"),
				'postcode'   => __( 'Postcode / ZIP', "woocommerce"),
				'country'    => __( 'Country / Region', "woocommerce"),
				'address'    => __( 'Complete Address', "woocommerce"),
			],
			'default' 		=> 'name'
		])->rebuildElementOnChange();

		$this->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Pick Form', "oxyultimate-woo"),
			'slug' 	=> 'form_type',
			'value' => [
				'billing' 	=> __('Billing Form', "oxyultimate-woo"),
				'shipping' 	=> __('Shipping', "oxyultimate-woo"),
			],
			'default' 		=> 'billing',
			'condition' 	=> 'details_type!=email&&details_type!=phone'
		])->rebuildElementOnChange();

		$this->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Name Type', "oxyultimate-woo"),
			'slug' 	=> 'name_type',
			'value' => [
				'flname' 	=> __('Full Name', "oxyultimate-woo"),
				'fname' 	=> __('First Name', "oxyultimate-woo"),
				'lname' 	=> __('Last Name', "oxyultimate-woo")
			],
			'default' 		=> 'fname',
			'condition' 	=> 'details_type=name'
		])->rebuildElementOnChange();

		$this->addTagControl(); 
	}

	function render($options, $defaults, $content) {
		$display = isset($options['details_type']) ? $options['details_type'] : 'name';
		$form = isset($options['form_type']) ? $options['form_type'] : 'billing';
		$nameType = isset($options['name_type']) ? $options['name_type'] : 'fname';

		if( $display == 'name' ) {
			echo call_user_func("ouwoo_{$form}_name", $nameType);
		}

		if( $display == 'email' ) {
			echo call_user_func("ouwoo_billing_info", $display);
		}

		if( $display == 'phone' ) {
			echo call_user_func("ouwoo_billing_info", $display);
		}

		if( ! in_array( $display, ['name', 'email', 'phone'] ) ) {
			echo call_user_func("ouwoo_{$form}_info", $display);
		}
	}
}

new OUWooCustomerDetails();