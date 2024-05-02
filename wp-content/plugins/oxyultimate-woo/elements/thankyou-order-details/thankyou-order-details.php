<?php

class OUWooThankYouOrderDetails extends UltimateWooEl {

	public $css_added = false;

	function name() {
		return __( "Order Details", 'oxyultimate-woo' );
	}

	function slug() {
		return "thankyou-order-details";
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 1;
	}

	function tag() {
		return [ 'default' => 'span', 'choices' => 'div,p,span'];
	}

	function controls() {
		$this->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Display', "oxyultimate-woo"),
			'slug' 	=> 'details_type',
			'value' => [
				'custnote' 	=> __('Customer Note', "oxyultimate-woo"),
				'date' 		=> __('Date'),
				'discount' 	=> __('Discount Price', "oxyultimate-woo"),
				'fees' 		=> __('Fees', "oxyultimate-woo"),
				'method' 	=> __('Payment Method', "oxyultimate-woo"),
				'number' 	=> __('Order Number', "oxyultimate-woo"),
				'ordstatus' => __('Order Status'),
				'subtotal' 	=> __('Sub Total', "oxyultimate-woo"),
				'shipping' 	=> __('Shipping Price', "oxyultimate-woo"),
				'tax' 		=> __('Tax', "oxyultimate-woo"),
				'total' 	=> __('Total Price', "oxyultimate-woo")
			],
			'default' 		=> 'number'
		])->rebuildElementOnChange();

		$this->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Display Price with', "oxyultimate-woo"),
			'slug' 	=> 'price_tax',
			'value' => [
				'none'  => __('Default WooCommerce Settings', "oxyultimate-woo"),
				'excl' 	=> __('Excluding Tax', "oxyultimate-woo"),
				'incl' 	=> __('Including Tax', "oxyultimate-woo")
			],
			'default' => 'none',
			'condition' 	=> 'details_type=subtotal||details_type=discount||details_type=shipping||details_type=total'
		])->rebuildElementOnChange();

		$this->addTagControl();

		$selector = 'span.woocommerce-Price-amount.amount, ins > span.woocommerce-Price-amount';

		$price = $this->addControlSection( 'typrice_section', __('Price', "woocommerce"), "assets/icon.png", $this );
		$price->typographySection( __('Typography'), $selector, $this );
		
		$selector = '.includes_tax, .shipped_via, .tax_label, .includes_tax .woocommerce-Price-amount.amount, .shipped_via .woocommerce-Price-amount.amount,.tax_label .woocommerce-Price-amount.amount';
		$suffix = $price->typographySection( __('Suffix Text', "oxyultimate-woo"), $selector, $this );
		
		$pos = $suffix->addControl("buttons-list", "position", __("Place under the price", "oxyultimate-woo") );
		$pos->setValue(['No', 'Yes'])
			->setDefaultValue('No')
			->setValueCSS(['Yes' => '> span.woocommerce-Price-amount.amount{display: block;}'])
			->whiteList();

		$price->typographySection( __('Strikethrough Price', "oxyultimate-woo"), "del span.woocommerce-Price-amount, del", $this );
	}

	function render($options, $defaults, $content) {
		$display = isset($options['details_type']) ? $options['details_type'] : 'number';
		$price_tax = ( isset($options['price_tax']) && $options['price_tax'] != 'none' ) ? $options['price_tax'] : '';

		if( $display == 'number' ) {
			echo ouwoo_order_number();
		}

		if( $display == 'date' ) {
			echo ouwoo_order_date();
		}

		if( $display == 'ordstatus' ) {
			echo ouwoo_order_status();
		}

		if( $display == 'subtotal' ) {
			echo ouwoo_order_subtotal( $price_tax );
		}

		if( $display == 'discount' ) {
			echo ouwoo_order_discount( $price_tax );
		}

		if( $display == 'shipping' ) {
			echo ouwoo_order_shipping( $price_tax );
		}

		if( $display == 'fees' ) {
			ouwoo_order_fees( $price_tax );
		}

		if( $display == 'tax' ) {
			ouwoo_order_tax( $price_tax );
		}

		if( $display == 'total' ) {
			$tax_display = $price_tax ? $price_tax : get_option( 'woocommerce_tax_display_cart' );
			echo ouwoo_order_total($tax_display);
		}

		if( $display == 'method' ) {
			echo ouwoo_order_payment_method();
		}

		if( $display == 'custnote' ) {
			echo ouwoo_customer_note();
		}
	}

	function customCSS($original, $selector) {
		if( ! $this->css_added) {
			$css = "body:not(.oxygen-builder-body) .oxy-thankyou-order-details:empty{display: none;},
					.oxygen-builder-body .oxy-thankyou-order-details:empty{min-height: 40px; width: 100%;}
					.ouwoo-col-1, .ouwoo-col-2{display:inline-block; float: left; width: 70%; padding: 20px;}
					.ouwoo-col-2{width: 30%; text-align: right;}";

			$this->css_added = true;

			return $css;
		}
	}
}

new OUWooThankYouOrderDetails();