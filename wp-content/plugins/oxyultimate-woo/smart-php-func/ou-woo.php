<?php

function ouwoo_get_product_ids_on_sale() {
	return implode( ",", (array) wc_get_product_ids_on_sale() );
}

function ou_get_sales_off_value( $type = 'percentage', $prefix = '', $id = '' ) {
	global $product;

	if( $id == '' )
		$id = get_the_ID();

	if( ! is_object( $product ) ) {
		$product = WC()->product_factory->get_product( $id );
	}

	if( $product->get_type() === 'simple' || $product->get_type() === 'external' ) {
		$regular_price = $product->get_regular_price();
		$sale_price = $product->get_sale_price();
	} 

	if( $product->get_type() === 'variable' || $product->get_type() === 'variation' ) {
		$regular_price = $product->get_variation_regular_price( 'min', true );
		$sale_price = $product->get_variation_sale_price( 'min', true );
	}

	if( empty ( $sale_price ) )
		return;

	if( $type == 'fixed' || $type == 'fixed_rate' ) {
		$sales_off_price = wc_price( $regular_price - $sale_price );
	} else {
		$sales_off_price = round( ( $regular_price - $sale_price ) / $regular_price * 100 ) . '%';
	}

	return $prefix . ' ' . $sales_off_price;
}

function ouwoo_get_order( $order_id = 'latest' ) {
	global $wp;
	
	if( UltimateWooEl::isBuilderEditorActive() || ( isset($_GET['action']) && $_GET['action'] == "ct_save_components_tree" ) ) {
		$order = UltimateWooEl::ouwoo_get_builder_preview_order( $order_id );
	} elseif( isThankYouPage() ) {
		$order = wc_get_order( $wp->query_vars['order-received'] );		
	} elseif ( isset( $_GET['order'] ) ) {
		$order = wc_get_order( absint( $_GET['order'] ) );
	} elseif( ! empty( $wp->query_vars['order-pay'] ) ) {
		$order = wc_get_order( absint( $wp->query_vars['order-pay'] ) );
	} else {
		$order = false;
	}

	return $order;
}

/**
 * Order number
 */
function ouwoo_order_number() {

	if( $order = ouwoo_get_order() ) {
		return $order->get_order_number();
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Order date
 */
function ouwoo_order_date() {

	if( $order = ouwoo_get_order() ) {
		return wc_format_datetime( $order->get_date_created() );
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Getting order status
 */
function ouwoo_order_status() {
	if( $order = ouwoo_get_order() ) {
		return $order->get_status();
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Getting sub-total
 */
function ouwoo_order_subtotal($tax_display = '') {

	if( $order = ouwoo_get_order() ) {
		return $order->get_subtotal_to_display($tax_display);
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Order discount
 */ 
function ouwoo_order_discount($tax_display = '') {
	$order = ouwoo_get_order();

	if ( $order && $order->get_total_discount() > 0 ) {
		return '-' . $order->get_discount_to_display( $tax_display );
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * order shipping
 */
function ouwoo_order_shipping($tax_display = '') {

	$order = ouwoo_get_order();

	if( $order && $order->get_shipping_method() ) {
		return $order->get_shipping_to_display( $tax_display );
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Order Fees
 */
function ouwoo_order_fees($tax_display = '') {

	$order = ouwoo_get_order();

	if( $order && $fees = $order->get_fees() ) {

		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );

		foreach ( $fees as $id => $fee ) {
			if ( apply_filters( 'woocommerce_get_order_item_totals_excl_free_fees', empty( $fee['line_total'] ) && empty( $fee['line_tax'] ), $id ) ) {
				continue;
			}

			printf(
				'<span class="ouwoo-order-fees ouwoo-col-1">%s</span><span class="ouwoo-order-fees ouwoo-col-2">%s</span>',
				$fee->get_name(),
				wc_price( 'excl' === $tax_display ? $fee->get_total() : $fee->get_total() + $fee->get_total_tax(), array( 'currency' => $order->get_currency() ) )
			);
		}
		
	}

	echo UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Order tax
 */
function ouwoo_order_tax($tax_display = '') {
	if( $order = ouwoo_get_order() ) {
		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );
		// Tax for tax exclusive prices.
		if ( 'excl' === $tax_display && wc_tax_enabled() ) {
			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( $order->get_tax_totals() as $code => $tax ) {
					printf(
						'<span class="ouwoo-order-tax ouwoo-col-1">%s</span><span class="ouwoo-order-tax ouwoo-col-2">%s</span>',
						$tax->label,
						$tax->formatted_amount
					);
				}
			} else {
				printf(
					'<span class="ouwoo-order-tax ouwoo-col-1">%s</span><span class="ouwoo-order-tax ouwoo-col-2">%s</span>',
					WC()->countries->tax_or_vat(),
					wc_price( $order->get_total_tax(), array( 'currency' => $order->get_currency() ) )
				);
			}
		}
	}

	echo UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Order total
 */
function ouwoo_order_total($tax_display = '') {

	if( $order = ouwoo_get_order() ) {
		return $order->get_formatted_order_total($tax_display);
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Order payment method
 */ 
function ouwoo_order_payment_method() {

	if( $order = ouwoo_get_order() ) {
		return wp_kses_post( $order->get_payment_method_title() );
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Customer Note
 */
function ouwoo_customer_note() {
	$order = ouwoo_get_order();
	
	if( $order && $order->get_customer_note() ) {
		return wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) );
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Customer billing name
 */
function ouwoo_billing_name($type = 'fname') {
	$order = ouwoo_get_order();
	if( $order ) {
		if( $type == 'flname' ) {
			return $order->get_formatted_billing_full_name();
		} elseif( $type == 'fname' ) {
			return $order->get_billing_first_name();
		} elseif( $type == 'lname' ) {
			return $order->get_billing_last_name();
		} else {
			return $order->get_billing_first_name();
		}
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Customer shipping name
 */
function ouwoo_shipping_name($type = 'fname') {

	if( $order = ouwoo_get_order() ) {
		if( $type == 'flname' ) {
			return ( ! wc_ship_to_billing_address_only() ) ? $order->get_formatted_shipping_full_name() : $order->get_formatted_shipping_full_name();
		} elseif( $type == 'fname' ) {
			return ( ! wc_ship_to_billing_address_only() ) ? $order->get_shipping_first_name() : $order->get_billing_first_name();
		} elseif( $type == 'lname' ) {
			return ( ! wc_ship_to_billing_address_only() ) ? $order->get_shipping_last_name() : $order->get_billing_last_name();
		} else {
			return ( ! wc_ship_to_billing_address_only() ) ? $order->get_shipping_first_name() : $order->get_billing_first_name();
		}
	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Getting Billing details
 */
function ouwoo_billing_info( $field ) {
	if( $order = ouwoo_get_order() ) {

		if( $field == 'email')
			return $order->get_billing_email();

		if( $field == 'phone')
			return $order->get_billing_phone();

		if( $field == 'company')
			return $order->get_shipping_company();

		if( $field == 'address_1')
			return $order->get_billing_address_1();

		if( $field == 'address_2')
			return $order->get_billing_address_2();

		if( $field == 'state')
			return $order->get_billing_state();

		if( $field == 'city')
			return $order->get_billing_city();

		if( $field == 'postcode')
			return $order->get_billing_postcode();

		if( $field == 'country')
			return $order->get_billing_country();

		if( $field == 'address')
			return wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) );

	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}

/**
 * Getting Shipping Info
 */
function ouwoo_shipping_info( $field ) {
	if( $order = ouwoo_get_order() ) {
		
		if( $field == 'company')
			return $order->get_shipping_company();

		if( $field == 'address_1')
			return $order->get_shipping_address_1();

		if( $field == 'address_2')
			return $order->get_shipping_address_2();

		if( $field == 'state')
			return $order->get_shipping_state();

		if( $field == 'city')
			return $order->get_shipping_city();

		if( $field == 'postcode')
			return $order->get_shipping_postcode();

		if( $field == 'country')
			return $order->get_shipping_country();

		if( $field == 'address') {
			$address = ( ! wc_ship_to_billing_address_only() ) ? $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) : $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) );

			return wp_kses_post( $address );
		}

	}

	return UltimateWooEl::isBuilderEditorActive() ? 'no data found' : false;
}