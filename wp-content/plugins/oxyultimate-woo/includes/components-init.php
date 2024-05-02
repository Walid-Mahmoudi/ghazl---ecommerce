<?php

/**
 * Loading components
 */
function ouwoo_elements() {

	$active_components = ouwoo_get_active_components();

	if( sizeof( (array) $active_components ) - 1 <= 0) 
		return;

	foreach ( $active_components as $comp ) {
		$element_path = OUWOO_DIR . 'elements/' . $comp . '/' . $comp . '.php';
		if ( file_exists( $element_path ) ) {
			include_once $element_path;
		}
	}
}

/**
 * Adding OxyUltimate Woo button on Builder Editor
 */
add_action('oxygen_add_plus_sections', 'ouwoo_register_add_plus_section' );
function ouwoo_register_add_plus_section() {
	$plugin_name = __("OxyUltimate Woo", "oxyultimate-woo");

	$ouwoowl = get_option('ouwoowl');
	if( $ouwoowl ) {
		$plugin_name = ! empty( $ouwoowl['plugin_name'] ) ? esc_html( $ouwoowl['plugin_name'] ) : $plugin_name;
	}

	CT_Toolbar::oxygen_add_plus_accordion_section( "ultimatewoo", $plugin_name );
}

/** 
 * Creating sections
 */
add_action('oxygen_add_plus_ultimatewoo_section_content', 'ouwoo_register_add_plus_subsections');
function ouwoo_register_add_plus_subsections() {
	$data = getAllOuWooComps();
	if( ! empty( $data ) ) {
		if( ouwoo_check_active_components($data['Classic']) ) {
			printf('<h2>%s</h2>', __('Classic', 'oxyultimate-woo') );
			do_action("oxygen_add_plus_ultimatewoo_main");
		}

		if( ouwoo_check_active_components($data['Cart Page']) ) {
			printf('<h2>%s</h2>', __('Cart Page', 'oxyultimate-woo') );
			do_action("oxygen_add_plus_ultimatewoo_cart");
		}

		if( ouwoo_check_active_components($data['Checkout Page']) ) {
			printf('<h2>%s</h2>', __('Checkout Page', 'oxyultimate-woo') );
			do_action("oxygen_add_plus_ultimatewoo_checkout");
		}

		if( ouwoo_check_active_components($data['Account']) ) {
			printf('<h2>%s</h2>', __('My Account Page', 'oxyultimate-woo') );
			do_action("oxygen_add_plus_ultimatewoo_myaccount");
		}

		if( ouwoo_check_active_components($data['Thank You Page']) ) {
			printf('<h2>%s</h2>', __('Thank You Page', 'oxyultimate-woo') );
			do_action("oxygen_add_plus_ultimatewoo_thankyou");
		}

		if( ouwoo_check_active_components($data['Reviews']) ) {
			printf('<h2>%s</h2>', __('Reviews', 'oxyultimate-woo') );
			do_action("oxygen_add_plus_ultimatewoo_reviews");
		}
	}
}

function ouwoo_check_active_components( $components ) {
	$active_components = ouwoo_get_active_components();

	if( sizeof( (array) $active_components ) - 1 <= 0 ) 
		return false;

	foreach ($components as $key => $value) {
		if( in_array( $key, $active_components ) ) {
			return true;
		}
	}

	return false;
}

function ouwoo_get_active_components() {
	$active_components = '';
	if ( is_network_admin() ) {
		$active_components = get_site_option( '_ouwoo_active_components' );
	} else {
		$active_components = get_option( '_ouwoo_active_components' );
	}

	return $active_components;
}

/**
 * All components list
 */
function getAllOuWooComps() {
	$compsList = [
		'Classic' => [
			'add-to-cart' 				=> __( "Add To Cart", 'oxyultimate-woo' ),
			'buy-now' 					=> __( "Buy Now Button", 'oxyultimate-woo' ),
			'empty-cart' 				=> __( "Empty Cart Button", 'oxyultimate-woo' ),
			'free-shipping-notice' 		=> __( "Free Shipping Notice", 'oxyultimate-woo' ),
			'menucart' 					=> __( "Menu Cart", 'oxyultimate-woo' ),
			'offcanvascart' 			=> __( "Off Canvas Cart", 'oxyultimate-woo' ),
			'categories' 				=> __( "Product Categories", 'oxyultimate-woo' ),
			'product-images' 			=> __( "Product Gallery Slider", "oxyultimate-woo" ),
			'product-image' 			=> __( "Product Featured Image", "oxyultimate-woo" ),
			'recently-viewed' 			=> __( "Recent Viewed Products", 'oxyultimate-woo' ),
			'sales-offer' 				=> __( "Sales Offer", 'oxyultimate-woo' ),
			'new-badge' 				=> __( "New Badge", 'oxyultimate-woo' ),
			'product-tabs-in-accordion' => __( "Tabs To Accordion", 'oxyultimate-woo' ),
			'minicart' 					=> __( "Ultimate Cart", 'oxyultimate-woo' ),
			'quick-view' 				=> __( "Quick View", 'oxyultimate-woo' )
		],
		'Cart Page' => [
			'cart-page' 				=> __( "Cart Page Builder", 'oxyultimate-woo' ),
			'cart-items' 				=> __( "Cart Items", 'oxyultimate-woo' ),
			'cart-totals' 				=> __( "Cart Totals Table", 'oxyultimate-woo' ),
			'cross-sells' 				=> __( "Cross-sells", 'oxyultimate-woo' )
		],
		'Checkout Page' => [
			'checkout-page' 			=> __( "Checkout Page Builder", 'oxyultimate-woo' ),
			'checkout-login-form' 		=> __( "Login Box", 'oxyultimate-woo' ),
			'checkout-coupon-form' 		=> __( "Coupon Box", 'oxyultimate-woo' ),
			'checkout-form-builder' 	=> __( "Checkout Form Builder", 'oxyultimate-woo' ),
			'billing-form' 				=> __( "Billing Form", 'oxyultimate-woo' ),
			'checkout-payment' 			=> __( "Payment", 'oxyultimate-woo' ),
			'checkout-review-order' 	=> __( "Review Order", 'oxyultimate-woo' ),
			'shipping-form' 			=> __( "Shipping Form", 'oxyultimate-woo' )
		],
		'Account' => [
			'myaccount-form-registration' 	=> __( "Registration Form", 'oxyultimate-woo' ),
			'myaccount-form-login' 			=> __( "Login Form", 'oxyultimate-woo' ),
			'myaccount-lost-password' 		=> __( "Lost Password Form", 'oxyultimate-woo' ),
			//'myaccount-edit-account' 		=> __( "Edit Account Details", 'oxyultimate-woo' ),
			//'myaccount-orders' 			=> __( "Orders", 'oxyultimate-woo' )
		],
		'Reviews' => [
			'review-ratings' 			=> __( "Ratings", 'oxyultimate-woo' ),
			'review-graph' 				=> __( "Graph", 'oxyultimate-woo' ),
			'review-form' 				=> __( "Review Form", 'oxyultimate-woo' ),
			//'review-list' 			=> __( "Reviews List", 'oxyultimate-woo' )
		],
		'Thank You Page' => [
			'customer-details' 				=> __( "Customer Details", 'oxyultimate-woo' ),
			'thankyou-order-details' 		=> __( "Order Details", 'oxyultimate-woo' ),
			'thankyou-order-items-builder' 	=> __( "Items List Builder", 'oxyultimate-woo' ),
		]
	];

	return $compsList;
}

ouwoo_elements();