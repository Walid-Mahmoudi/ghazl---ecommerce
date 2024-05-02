<?php

/**
 * Condition Settings
 */
function register_ouwoo_conditions() {

	if( ! is_admin() ) {
		global $OxygenConditions;

		$OxygenConditions->register_condition(
			'Is Cart Empty',
			array('options' => array(true, false), 'custom' => false),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_cart_is_empty',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Shop Page',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_shop_page',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Cart Page',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_cart_page',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Checkout Page',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_checkout_page',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Single Product Page',
			array('options' => array(true, false), 'custom' => false),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_product_page',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Account Page',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_account_page',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Featured Product',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_featured_product',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Virtual Product',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_virtual_product',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Product Downloadable',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_is_downloadable_product',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Is Parent Category',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_parent_category',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Product In Stock',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_in_stock',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Stock Status',
			array(
				'options' => array(
					'instock'     => __( 'In stock', 'woocommerce' ),
					'outofstock'  => __( 'Out of stock', 'woocommerce' ),
					'onbackorder' => __( 'On backorder', 'woocommerce' )
				), 
				'custom' => true 
			),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_stock_status',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Product Type',
			array(
				'options' => array(
					'simple' => __('Simple'), 
					'grouped' => __('Grouped'), 
					'variable' => __('Variable'),  
					'external' => __('External'), 
				), 
				'custom' => true 
			),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_is_type',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Product Is On Sale',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_is_on_sale',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Product Is Visible',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_is_visible',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Product Is Purchasable',
			array('options' => array(true, false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_is_purchasable',
			'OxyUltimateWoo'
		);
		
		$terms = get_terms( array( 
			'taxonomy' 		=> 'product_cat',
			'hide_empty'   	=> 0,
			'number' 		=> 999,
			'suppress_filter' => true,
		) );

		$new_terms = array();
		foreach( $terms as $term ) {
			$new_terms[$term->term_id] = $term->name;
		}

		$OxygenConditions->register_condition(
			'Product In Category',
			array('options' => $new_terms, 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_in_category',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Product Has Reviews',
			array('options' => array(true,false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_has_reviews',
			'OxyUltimateWoo'
		);
		
		$OxygenConditions->register_condition(
			'Number of Reviews',
			array('options' => array(), 'custom' => true),
			$OxygenConditions->condition_operators['int'],
			'ouwoo_condition_callback_product_total_reviews',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'User Country (alpha-2 code)',
			array('options' => array(), 'custom' => true),
			$OxygenConditions->condition_operators['string'],
			'ouwoo_condition_callback_user_country',
			'OxyUltimateWoo'
		);
		
		$OxygenConditions->register_condition(
			'Endpoint',
			array('options' => array(
				'Any',
				'Order Pay',
				'Order Received',
				'View Order',
				'Edit Account',
				'Edit Address',
				'Add Payment Method',
				'Customer Logout',
				'Lost Password'
			), 'custom' => true ),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_wc_endpoint',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Has Recent Viewed Products?',
			array('options' => array(true,false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_product_recent_viewed',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Bought atleast one product?',
			array('options' => array(true,false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_customer_bought_product',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Discount Applied?',
			array('options' => array(true,false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_order_has_discount',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Shipping Applied?',
			array('options' => array(true,false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_order_has_shipping_method',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Fees Applied?',
			array('options' => array(true,false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_order_has_fees',
			'OxyUltimateWoo'
		);

		$OxygenConditions->register_condition(
			'Tax Applied?',
			array('options' => array(true,false), 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_order_has_tax',
			'OxyUltimateWoo'
		);

		/*$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
		$methods = [];
		if( $available_payment_methods ) {
			foreach( $available_payment_methods as $slug => $method ) {
				if( ! empty( $method ) && is_object( $method ) ) {
					$methods[ $slug ] = $method->get_title();
				}
			}
		}

		$OxygenConditions->register_condition(
			'Payment Method',
			array('options' => $methods, 'custom' => true),
			$OxygenConditions->condition_operators['simple'],
			'ouwoo_condition_callback_order_payment_method',
			'OxyUltimateWoo'
		);*/
		
		/**
		 * Is Cart Empty
		 */
		function ouwoo_condition_callback_cart_is_empty( $value, $operator ) {
			global $OxygenConditions;

			$is_cart_empty = WC()->cart->is_empty();

			return $OxygenConditions->eval_string($is_cart_empty, (bool) $value, $operator);
		}

		/**
		 * Is Shop Page?
		 */
		function ouwoo_condition_callback_is_shop_page($value, $operator) {
			global $OxygenConditions;

			$is_shop = is_shop();

			return $OxygenConditions->eval_string($is_shop, (bool) $value, $operator);
		}

		/**
		 * Is Cart Page?
		 */
		function ouwoo_condition_callback_is_cart_page($value, $operator) {
			global $OxygenConditions;

			$is_cart = is_cart();

			return $OxygenConditions->eval_string($is_cart, (bool) $value, $operator);
		}

		/**
		 * Is Checkout Page?
		 */
		function ouwoo_condition_callback_is_checkout_page($value, $operator) {
			global $OxygenConditions;

			$is_checkout = is_checkout();

			return $OxygenConditions->eval_string($is_checkout, (bool) $value, $operator);
		}

		/**
		 * Is Single Product Page?
		 */
		function ouwoo_condition_callback_is_product_page($value, $operator) {
			global $OxygenConditions;

			$is_product = is_product();

			return $OxygenConditions->eval_string($is_product, (bool) $value, $operator);
		}

		/**
		 * Is Account Page?
		 */
		function ouwoo_condition_callback_is_account_page($value, $operator) {
			global $OxygenConditions;

			$is_account_page = is_account_page();

			return $OxygenConditions->eval_string($is_account_page, (bool) $value, $operator);
		}

		/**
		 * Is Featured Product
		 */
		function ouwoo_condition_callback_is_featured_product($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$is_featured = $product->is_featured();

				return $OxygenConditions->eval_string($is_featured, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Is Virtual Product
		 */
		function ouwoo_condition_callback_is_virtual_product($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$is_virtual = $product->is_virtual();

				return $OxygenConditions->eval_string($is_virtual, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Checks if a product is downloadable.
		 */
		function ouwoo_condition_callback_is_downloadable_product($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$is_downloadable = $product->is_downloadable();

				return $OxygenConditions->eval_string($is_downloadable, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Is Parent Category
		 */
		function ouwoo_condition_callback_parent_category($value, $operator) {

			if( ! is_tax() )
				return false;

			global $OxygenConditions;

			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$is_parent = ( count( get_term_children( $term->term_id, get_query_var( 'taxonomy' ) ) ) > 0 ) ? true : false;

			return $OxygenConditions->eval_string( $is_parent, ( bool ) $value, $operator );
		}

		/**
		 * Product Type
		 */
		function ouwoo_condition_callback_product_is_type( $value, $operator ) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$type = $product->get_type();

				return $OxygenConditions->eval_string( strtolower( $type ), strtolower( $value ), $operator);
			} else {
				return false;
			}
		}

		/**
		 * Product In Stock
		 */
		function ouwoo_condition_callback_product_in_stock($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$is_in_stock = $product->is_in_stock();

				return $OxygenConditions->eval_string($is_in_stock, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Stock Status
		 */
		function ouwoo_condition_callback_product_stock_status($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$status = $product->get_stock_status();

				return $OxygenConditions->eval_string( strtolower( $status ), strtolower( $value ), $operator );
			} else {
				return false;
			}
		}

		/**
		 * Product Is On Sale
		 */
		function ouwoo_condition_callback_product_is_on_sale($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$is_on_sale = $product->is_on_sale();

				return $OxygenConditions->eval_string($is_on_sale, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Product Is Visible
		 */
		function ouwoo_condition_callback_product_is_visible($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$is_visible = $product->is_visible();

				return $OxygenConditions->eval_string($is_visible, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Product Is Purchasable
		 */
		function ouwoo_condition_callback_product_is_purchasable($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$is_purchasable = $product->is_purchasable();

				return $OxygenConditions->eval_string($is_purchasable, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Product In Category
		 */
		function ouwoo_condition_callback_product_in_category($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();

			if ($product != false) {
				$term_list = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
				if( $operator === "==" ) {
					return in_array( $value, (array) $term_list );
				}

				if( $operator == "!=" ) {
					return ! in_array( $value, (array) $term_list );
				}
			} else {
				return false;
			}
		}

		/**
		 * Payment method
		 */
		function ouwoo_condition_callback_order_payment_method( $value, $operator ) {
			global $OxygenConditions;

			$order = ouwoo_get_order();
			
			if( ! $order )
				return false;

			$payment_method = $order->get_payment_method();

			return $OxygenConditions->eval_string( strtolower( $payment_method ), strtolower( $value ), $operator);
		}

		/**
		 * Has Reviews
		 */
		function ouwoo_condition_callback_product_has_reviews($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$total_reviews = ( $product->get_review_count() > 0 ) ? true : false;

				return $OxygenConditions->eval_string($total_reviews, (bool) $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * Number of Reviews
		 */
		function ouwoo_condition_callback_product_total_reviews($value, $operator) {
			global $product;
			global $OxygenConditions;

			$product = wc_get_product();
			if ($product != false) {
				$reviews = ( $product->get_review_count() > 0 ) ? $product->get_review_count() : 0;

				return $OxygenConditions->eval_int($reviews, $value, $operator);
			} else {
				return false;
			}
		}

		/**
		 * User Country
		 */
		function ouwoo_condition_callback_user_country($value, $operator) {
			global $OxygenConditions;

			$location        = WC_Geolocation::geolocate_ip( WC_Geolocation::get_external_ip_address() );
			$current_country = $location['country'];

			if( $current_country ) {
				return $OxygenConditions->eval_string( strtolower( $current_country ), strtolower( $value ), $operator );
			} else {
				return false;
			}
		}

		/**
		 * Endpoint
		 */
		function ouwoo_condition_callback_wc_endpoint($value, $operator) {
			if( $value == 'Any') {
				$endpoint_url = is_wc_endpoint_url();
			} else if( $value == "Order Pay" ) {
				$endpoint_url = is_wc_endpoint_url( 'order-pay' );
			} else if( $value == "Order Received" ) {
				$endpoint_url = is_wc_endpoint_url( 'order-received' );
			} else if( $value == "View Order" ) {
				$endpoint_url = is_wc_endpoint_url( 'view-order' );
			} else if( $value == "Edit Account" ) {
				$endpoint_url = is_wc_endpoint_url( 'edit-account' );
			} else if( $value == "Edit Address" ) {
				$endpoint_url = is_wc_endpoint_url( 'edit-address' );
			} else if( $value == "Add Payment Method" ) {
				$endpoint_url = is_wc_endpoint_url( 'add-payment-method' );
			} else if( $value == "Customer Logout" ) {
				$endpoint_url = is_wc_endpoint_url( 'customer-logout' );
			} else if( $value == "Lost Password" ) {
				$endpoint_url = is_wc_endpoint_url( 'lost-password' );
			} else {
				//*
			}

			if ( $operator == "==" ) {
				return ( ! empty( $endpoint_url ) ) ? true : false;
			} else if ( $operator == "!=") {
				return ( empty( $endpoint_url ) ) ? true : false;
			}
		}

		/**
		 * Has Recent Viewed Products
		 */
		function ouwoo_condition_callback_product_recent_viewed($value, $operator) {
			global $OxygenConditions;

			$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
			$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

			if ( empty( $viewed_products ) ) {
				$rc_viewed_products = false;
			} else {
				$rc_viewed_products = true;
			}

			return $OxygenConditions->eval_string($rc_viewed_products, (bool) $value, $operator);
		}

		/**
		 * Is discount applied
		 */
		function ouwoo_condition_callback_order_has_discount( $value, $operator ) {
			global $OxygenConditions;

			$order = ouwoo_get_order();
			
			if( ! $order )
				return false;

			if ( $order && $order->get_total_discount() > 0 ) {
				$discount_applied = true;
			} else {
				$discount_applied = false;
			}

			return $OxygenConditions->eval_string($discount_applied, (bool) $value, $operator);

		}

		function ouwoo_condition_callback_order_has_shipping_method( $value, $operator ) {
			global $OxygenConditions;

			$order = ouwoo_get_order();
			
			if( ! $order )
				return false;

			if ( $order && $order->get_shipping_method() ) {
				$shipping_applied = true;
			} else {
				$shipping_applied = false;
			}

			return $OxygenConditions->eval_string($shipping_applied, (bool) $value, $operator);

		}

		function ouwoo_condition_callback_order_has_fees($value, $operator) {
			global $OxygenConditions;

			$order = ouwoo_get_order();
			
			if( ! $order )
				return false;

			if ( $order && $order->get_fees() ) {
				$fees_applied = true;
			} else {
				$fees_applied = false;
			}

			return $OxygenConditions->eval_string($fees_applied, (bool) $value, $operator);
		}

		function ouwoo_condition_callback_order_has_tax($value, $operator) {
			global $OxygenConditions;

			$tax_display = get_option( 'woocommerce_tax_display_cart' );

			if ( 'excl' === $tax_display && wc_tax_enabled() ) {
				$order = ouwoo_get_order();
				$tax_applied = ( $order->get_tax_totals() || $order->get_total_tax() ) ? true : false;
			} else {
				$tax_applied = false;
			}

			return $OxygenConditions->eval_string($tax_applied, (bool) $value, $operator);
		}

		/**
		 * Customer bought atleast one product
		 */
		function ouwoo_condition_callback_customer_bought_product( $value, $operator ) {
			global $OxygenConditions;

			if ( ! is_user_logged_in() ) {
				return false;
			}

			global $wpdb;

			$meta_key   = '_customer_user';
        	$meta_value = (int) get_current_user_id();

        	$paid_order_statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );

			$count = $wpdb->get_var( $wpdb->prepare("
				SELECT COUNT(p.ID) FROM {$wpdb->prefix}posts AS p
				INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
				WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $paid_order_statuses ) . "' )
				AND p.post_type LIKE 'shop_order'
				AND pm.meta_key = '%s'
				AND pm.meta_value = %s
				LIMIT 1
			", $meta_key, $meta_value ) );

			// Return a boolean value based on orders count
			$isBoughtProduct = $count > 0 ? true : false;

			return $OxygenConditions->eval_string($isBoughtProduct, (bool) $value, $operator);
		}
	}
}

register_ouwoo_conditions();