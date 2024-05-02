<?php

/**
 * Enqueue JS files
 */
add_action( 'wp_enqueue_scripts', 'ouwoo_load_scripts', 999 );
function ouwoo_load_scripts() {
	$active_components = ouwoo_get_active_components();

	if( sizeof( (array) $active_components ) - 1 <= 0) {
		$active_components = array();
	}

	if( in_array( 'offcanvascart', $active_components ) || in_array( 'minicart', $active_components ) ) {
		wp_register_script(
			'ou-occ-script', 
			OUWOO_URL . 'assets/js/ou-off-canvas-cart.min.js',
			array('wc-add-to-cart', 'wc-cart-fragments'),
			filemtime( OUWOO_DIR . 'assets/js/ou-off-canvas-cart.min.js' ),
			true
		);
	}

	if( isset($_GET['ct_builder']) ) {
		wp_enqueue_script(
			'ouwoo-ctbuilder',
			OUWOO_URL . 'assets/js/ouwoo-ct-builder.min.js',
			array(),
			filemtime( OUWOO_DIR . 'assets/js/ouwoo-ct-builder.min.js' ),
			true
		);
	}
}

function ouwoo_enqueue_common_scripts() {
	wp_enqueue_script( 
		'swiper-script', 
		OUWOO_URL . 'assets/js/swiper.min.js', 
		array(), 
		filemtime( OUWOO_DIR . 'assets/js/swiper.min.js' ), 
		true
	);

	wp_enqueue_script( 
		'ouwoo-swiper-carousel', 
		OUWOO_URL . 'assets/js/ouwoo-swiper-carousel.min.js', 
		array(), 
		filemtime( OUWOO_DIR . 'assets/js/ouwoo-swiper-carousel.min.js' ), 
		true
	);
}

add_action('wp_ajax_ouwoo_ajax_add_to_cart', 'ouwoo_ajax_add_to_cart');
add_action('wp_ajax_nopriv_ouwoo_ajax_add_to_cart', 'ouwoo_ajax_add_to_cart');
function ouwoo_ajax_add_to_cart() {
	
	if ( ! isset( $_POST['add-to-cart'] ) ) {
		return;
	}

	$product_id 		= apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['add-to-cart']));
	//$product           	= wc_get_product( $product_id );
	$quantity 			= empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
	$variation_id 		= absint($_POST['variation_id']);
	$passed_validation 	= apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id);
	$product_status 	= get_post_status($product_id);

	if ($passed_validation && 'publish' === $product_status) {

		do_action('woocommerce_ajax_added_to_cart', $product_id);

		wc_clear_notices();

		WC_AJAX::get_refreshed_fragments();
	} else {
		$data = array(
			'error' => true,
			'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id )
		);

		wp_send_json($data);
	}
}

add_action('wp_ajax_ouwoo_update_cart_item_quantity', 'ouwoo_update_cart_item_quantity');
add_action('wp_ajax_nopriv_ouwoo_update_cart_item_quantity', 'ouwoo_update_cart_item_quantity');
function ouwoo_update_cart_item_quantity() {
	$cart_key 	= sanitize_text_field( $_POST['cart_key'] );
	$new_qty 	= wc_stock_amount(absint( $_POST['qty'] ));
	$cart_item_data = WC()->cart->get_cart_item( $cart_key );
	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
	$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $new_qty);	

	if( $passed_validation && $cart_key && ! empty( $cart_item_data ) ) {
		$updated = $new_qty == 0 ? WC()->cart->remove_cart_item( $cart_key ) : WC()->cart->set_quantity( $cart_key, $new_qty );
		if( $updated ){
			
			wc_clear_notices();

			WC_AJAX::get_refreshed_fragments();
		}
	} else {
		$data = array(
			'error' => __( 'Failed. Something went wrong', 'oxyultimate-woo' ),
		);
		
		wp_send_json($data);
	}
}

function ouwoo_common_filter_mini_cart_contents() {
	add_filter( 'woocommerce_cart_item_name', 'ouwoo_product_title', 10, 3 );
	add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );
	add_filter( 'woocommerce_cart_item_remove_link', 'ouwoo_woo_cart_remove_button', 10, 2 );

	add_action('woocommerce_widget_shopping_cart_total', 'ouwoo_sub_total_wrap_open', 2 );
	add_action('woocommerce_widget_shopping_cart_total', 'ouwoo_sub_total_wrap_close', 19 );
}

function ouwoo_filter_mini_cart_contents() {
	add_filter( 'woocommerce_widget_cart_item_quantity', 'ouwoo_cart_item_quantity', 10, 3 );

	add_action('woocommerce_widget_shopping_cart_total', 'ouwoo_coupon_code', 1 );
	add_action('woocommerce_widget_shopping_cart_total', 'ouwoo_cart_coupon_with_total', 20 );
}

add_action( 'woocommerce_after_mini_cart', function() {
	remove_filter( 'woocommerce_cart_item_name', 'ouwoo_product_title', 10, 3 );
	remove_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );
	remove_filter( 'woocommerce_cart_item_remove_link', 'ouwoo_woo_cart_remove_button', 10, 2 );

	remove_filter( 'woocommerce_widget_cart_item_quantity', 'ouwoo_cart_item_quantity', 10, 3 );

	remove_action('woocommerce_widget_shopping_cart_total', 'ouwoo_coupon_code', 1 );
	remove_action('woocommerce_widget_shopping_cart_total', 'ouwoo_cart_coupon_with_total', 20 );

	remove_action('woocommerce_widget_shopping_cart_total', 'ouwoo_sub_total_wrap_open', 2 );
	remove_action('woocommerce_widget_shopping_cart_total', 'ouwoo_sub_total_wrap_close', 19 );
});

function ouwoo_coupon_code() {
	echo '<span class="coupon-code-wrap">
			<input type="text" name="ouocc_coupon_code" class="ouocc-coupon-field" value="" placeholder="'. __("Enter coupon code", "woocommerce") . '"/><button type="button" name="apply_btn" class="coupon-btn">'. __('Apply', 'woocommerce') .'</button>
		</span>';
}

function ouwoo_sub_total_wrap_open() {
	echo '<span class="subtotal-wrap">';
}

function ouwoo_sub_total_wrap_close() {
	echo '</span>';
}

function ouwoo_cart_coupon_with_total() {
	$haveCoupon = false;
	$discount = 0;

	foreach ( WC()->cart->get_coupons() as $code => $coupon ) :
		echo '<span class="oucc-coupon-row" data-title="'. esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ) .'"><span class="oucc-coupon-label">';
				wc_cart_totals_coupon_label( $coupon );

		echo '<a role="button" class="ouocc-remove-coupon" data-coupon="' . esc_attr( $coupon->get_code() ) . '" href="?remove_coupon='. $coupon->get_code() .'"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M408.299,98.512l-32.643,371.975H136.344L103.708,98.512l-41.354,3.625l33.232,378.721
		C97.335,498.314,112.481,512,130.076,512h251.849c17.588,0,32.74-13.679,34.518-31.391l33.211-378.472L408.299,98.512z"/></g></g><g><g><path d="M332.108,0H179.892c-19.076,0-34.595,15.519-34.595,34.595v65.73h41.513V41.513h138.378v58.811h41.513v-65.73
		C366.703,15.519,351.184,0,332.108,0z"/></g></g><g><g><path d="M477.405,79.568H34.595c-11.465,0-20.757,9.292-20.757,20.757s9.292,20.757,20.757,20.757h442.811
		c11.465,0,20.757-9.292,20.757-20.757S488.87,79.568,477.405,79.568z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg></a>';

		echo '</span><span class="oucc-discount-price">';
				wc_cart_totals_coupon_html( $coupon );
		echo '</span></span>';

		$discount += WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );

		$haveCoupon = true;

	endforeach;

	if( $haveCoupon ) {
		echo '<span class="order-total-row">';
		echo '<span class="order-total-label">' . esc_html__( 'New Total:', 'woocommerce' ) . '</span>';
		echo '<span class="order-total-price order-total">';
		echo wc_price( WC()->cart->get_displayed_subtotal() - $discount );
		echo '</span>';
		echo '</span>';
	}
}

function ouwoo_cart_item_quantity( $price, $cart_item, $cart_item_key ) {
	$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $cart_item['data'] ), $cart_item, $cart_item_key );

	$price .='<div class="price-label">' . sprintf( '%s: %s', __('Price', 'woocommerce'), $product_price ) . '</div>';

	if ( WC()->cart->display_prices_including_tax() ) {
		$product_price = wc_get_price_including_tax( $cart_item['data'] );
	} else {
		$product_price = wc_get_price_excluding_tax( $cart_item['data'] );
	}

	/* WPC Product Bundles */
	if ( ( isset( $cart_item['woosb_ids'], $cart_item['woosb_price'], $cart_item['woosb_fixed_price'] ) && ! $cart_item['woosb_fixed_price'] ) 
		|| ( isset( $cart_item['woosb_parent_id'], $cart_item['woosb_price'], $cart_item['woosb_fixed_price'] ) && $cart_item['woosb_fixed_price'] ) ) {
	    $product_price = $cart_item['woosb_price'];
	}

	$min_value = max( $cart_item['data']->get_min_purchase_quantity(), 0 );
	$max_value = $cart_item['data']->get_max_purchase_quantity();
	$max_value = 0 < $max_value ? $max_value : '';
	if ( '' !== $max_value && $max_value < $min_value ) {
		$max_value = $min_value;
	}

	if( class_exists( 'Morningtrain\WooAdvancedQTY\Plugin\Controllers\InputArgsController' ) ) {
		$args = Morningtrain\WooAdvancedQTY\Plugin\Controllers\InputArgsController::applyArgs(array(), $cart_item['product_id'], true);
		if( isset( $args['min_value'] ) && $args['min_value'] > $min_value )
			$min_value = $args['min_value'];
		
		if( isset( $args['max_value'] ) && $args['max_value'] > $max_value )
			$max_value = $args['max_value'];
	}

	$qtyField = '<input 
					type="number" 
					class="ouocc-qty" 
					step="1" 
					min="'. apply_filters( 'woocommerce_quantity_input_min', $min_value, $cart_item['data'] ) .'" 
					max="'. apply_filters( 'woocommerce_quantity_input_max', $max_value, $cart_item['data'] ) .'" 
					value="' . $cart_item['quantity'] . '" 
					placeholder="" 
					inputmode="numeric"
				>';

	$price .= sprintf( 
		'<div class="qty-price-wrap">
			<div class="ouocc-qty-box" data-product_id="%d" data-cart_item_key="%s">
				<span class="ouocc-qty-minus ouocc-qty-chng">-</span>
				%s
				<span class="ouocc-qty-plus ouocc-qty-chng">+</span>
			</div>
			<div class="item-total-price">%s</div>
		</div>',
		$cart_item['product_id'],
		$cart_item_key,
		$qtyField,
		wc_price( $cart_item['quantity'] * $product_price )
	);

	return $price;
}

function ouwoo_product_title( $name, $cart_item, $cart_item_key ) {
	return $cart_item['data']->get_title();
}

function ouwoo_woo_cart_remove_button( $remove_link, $cart_item_key ) {

	$svgFile = '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M408.299,98.512l-32.643,371.975H136.344L103.708,98.512l-41.354,3.625l33.232,378.721
		C97.335,498.314,112.481,512,130.076,512h251.849c17.588,0,32.74-13.679,34.518-31.391l33.211-378.472L408.299,98.512z"/></g></g><g><g><path d="M332.108,0H179.892c-19.076,0-34.595,15.519-34.595,34.595v65.73h41.513V41.513h138.378v58.811h41.513v-65.73
		C366.703,15.519,351.184,0,332.108,0z"/></g></g><g><g><path d="M477.405,79.568H34.595c-11.465,0-20.757,9.292-20.757,20.757s9.292,20.757,20.757,20.757h442.811
		c11.465,0,20.757-9.292,20.757-20.757S488.87,79.568,477.405,79.568z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';

	$remove_link = str_replace( '&times;', $svgFile, $remove_link );

	return $remove_link;
}

add_action( 'wp_footer', function(){
?>
	<style type="text/css">
		body:not(.ouwoo-payment-comp) a.return-to-shop-button, 
		div:not(.oxy-ou-offcanvascart) a.button.ouocc-shop-button,
		.ct-widget .product_list_widget .price-label,
		.ct-widget .product_list_widget .qty-price-wrap, 
		.oxy-ou-cart .product-content > span.quantity,
		.oxy-ou-offcanvascart .product-content > span.quantity {display: none;}
		.product-content .product-title {padding-right: 20px;}
		.ct-widget .remove svg{ width: 12px; height: 12px; }
	</style>
<?php
});

if ( get_option( 'ouwoo_hide_shipping_methods' ) == 'hide_all' ) {
	add_filter( 'woocommerce_package_rates', 'ouwoo_hide_shipping_when_free_is_available' );
	function ouwoo_hide_shipping_when_free_is_available( $rates ) {
		$free = array();
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'free_shipping' === $rate->method_id ) {
				$free[$rate_id] = $rate;
				break;
			}
		}
		
		return !empty( $free ) ? $free : $rates;
	}
}

if ( get_option( 'ouwoo_hide_shipping_methods' ) == 'hide_except_local' ) {
	add_filter( 'woocommerce_package_rates', 'ouwoo_hide_shipping_when_free_is_available_keep_local', 10, 2 ); 
	function ouwoo_hide_shipping_when_free_is_available_keep_local( $rates, $package ) {
		$new_rates = array();
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'free_shipping' === $rate->method_id ) {
				$new_rates[ $rate_id ] = $rate;
				break;
			}
		}

		if ( ! empty( $new_rates ) ) {
			foreach ( $rates as $rate_id => $rate ) {
				if ('local_pickup' === $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
					break;
				}
			}
			return $new_rates;
		}

		return $rates;
	}
}

if ( get_option( 'ouwoo_hide_shipping_methods' ) == 'hide_except_states' ) {
	add_filter( 'woocommerce_package_rates', 'ouwoo_states_hide_all_shipping_when_free_is_available', 10, 2 ); 
	function ouwoo_states_hide_all_shipping_when_free_is_available( $rates, $package ) {
		$states = get_option( 'ouwoo_hide_shipping_methods_states' );
		if( $states ) {
			$excluded_states = explode( ',', $states );
		} else {
			$excluded_states = array();
		}
		
		if( isset( $rates['free_shipping'] ) AND !in_array( WC()->customer->shipping_state, $excluded_states ) ) :
			$freeshipping = array();
			$freeshipping = $rates['free_shipping'];
	
			unset( $rates );

			$rates = array();
			$rates[] = $freeshipping;
		endif;
	
		if( isset( $rates['free_shipping'] ) AND in_array( WC()->customer->shipping_state, $excluded_states ) ) {
			unset( $rates['free_shipping'] );
		}

		return $rates;
	}
}

function ouwoo_is_product_out_of_stock( $product ) {
	if ( ! $product || ! is_object( $product ) ) {
		return false;
	}

	$in_stock     = $product->is_in_stock();
	$manage_stock = $product->managing_stock();
	$quantity     = $product->get_stock_quantity();

	if (
		( $product->is_type( 'simple' ) && ( ! $in_stock || ( $manage_stock && 0 === $quantity ) ) ) ||
		( $product->is_type( 'variable' ) && $manage_stock && 0 === $quantity )
	) {
		return true;
	}

	return false;
}

add_filter( 'oxy_base64_encode_options', 'ouwoo_dynamic_data_fields');
function ouwoo_dynamic_data_fields( $list ) {
	$list = array_merge($list, array('oxy-ou_addtocart_ouatc_product', 'oxy-ou_review_form_product_id', 'oxy-ou_sales_offer_product_id', 'oxy-ouwoo_newbadge_product_id', 'oxy-ouwoo_newbadge_days')); 
        
    return $list;
}

if( ! is_admin() && ! isset($_GET['oxygen_iframe']) && ! defined('OXY_ELEMENTS_API_AJAX') ) {
	function ouwoo_track_product_view() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) );
		}

		$keys = array_flip( $viewed_products );

		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}

		$viewed_products[] = $post->ID;

		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only.
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
	}
	add_action( 'template_redirect', 'ouwoo_track_product_view', 21 );

	add_filter( 'do_shortcode_tag', function( $output, $tag ) {

		global $wp;

		if( in_array( $tag, [ 'oxy-ou_checkout', 'oxy-woo_checkout', 'oxy-woo-checkout' ] ) && isThankYouPage() && ouwoo_oxygen_template_exist( 'ouwoo_template_thankyou' )
		) {

			ob_start();

			WC_Shortcode_Checkout::output( array() );

			return ob_get_clean();
		}

		return $output;

	}, 90, 2);
}

add_filter( 'woocommerce_locate_template', 'UltimateWooEl::ouwoo_woocommerce_locate_template', 999, 3 );

/**
 * Checking the oxygen template
 */
function ouwoo_oxygen_template_exist( $key, $limit = 1 ) {
	global $wpdb;

	$sql = "SELECT ID, post_title 
			FROM $wpdb->posts as P 
			LEFT JOIN $wpdb->postmeta as PM 
			ON PM.post_id = P.ID 
			WHERE P.post_status='publish' 
			AND P.post_type='ct_template' 
			AND PM.meta_key='{$key}' 
			AND PM.meta_value='true' 
			ORDER BY ID LIMIT 0, $limit";

	$templates = $wpdb->get_results($sql);

	if( $templates ){
		return $templates;
	}

	return false;
}

/**
 * Checking the Thank You Page
 */
function isThankYouPage() {
	global $wp;
	
	if( is_checkout() && ( isset($wp->query_vars['order-received']) || is_wc_endpoint_url( 'order-received' ) || isset( $wp->query_vars['order'] ) ) ) {
		return true;
	} else {
		return false;
	}
}