<?php
class OUWCCheckoutPage extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Checkout Builder", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_checkout";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 1;
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_checkout-elements-label"
				ng-if="isActiveName('oxy-ou_checkout')&&!hasOpenTabs('oxy-ou_checkout')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_checkout-elements"
				ng-if="isActiveName('oxy-ou_checkout')&&!hasOpenTabs('oxy-ou_checkout')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 61 );
	}

	function controls() {
		
	}

	function render($options, $defaults, $content) {
		global $wp;

		// Check cart class is loaded or abort.
		if ( is_null( WC()->cart ) ) {
			return;
		}

		// Backwards compatibility with old pay and thanks link arguments.
		if ( isset( $_GET['order'] ) && isset( $_GET['key'] ) ) { // WPCS: input var ok, CSRF ok.
			wc_deprecated_argument( __CLASS__ . '->' . __FUNCTION__, '2.1', '"order" is no longer used to pass an order ID. Use the order-pay or order-received endpoint instead.' );

			// Get the order to work out what we are showing.
			$order_id = absint( $_GET['order'] ); // WPCS: input var ok.
			$order    = wc_get_order( $order_id );

			if ( $order && $order->has_status( 'pending' ) ) {
				$wp->query_vars['order-pay'] = absint( $_GET['order'] ); // WPCS: input var ok.
			} else {
				$wp->query_vars['order-received'] = absint( $_GET['order'] ); // WPCS: input var ok.
			}
		}

		// Handle checkout actions.
		if ( ! empty( $wp->query_vars['order-pay'] ) ) {

			WC_Shortcode_Checkout::output( array() );

		} elseif ( isset( $wp->query_vars['order-received'] ) && ! ouwoo_oxygen_template_exist( 'ouwoo_template_thankyou' ) ) {

			WC_Shortcode_Checkout::output( array() );

		} else {

			// Check cart has contents.
			if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_redirect_empty_cart', true ) ) {
				return;
			}

			// Check cart contents for errors.
			do_action( 'woocommerce_check_cart_items' );

			// Calc totals.
			WC()->cart->calculate_totals();

			// Get checkout object.
			$checkout = WC()->checkout();

			if ( empty( $_POST ) && wc_notice_count( 'error' ) > 0 ) { // WPCS: input var ok, CSRF ok.

				wc_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );
				wc_clear_notices();

			} else {

				$non_js_checkout = ! empty( $_POST['woocommerce_checkout_update_totals'] ); // WPCS: input var ok, CSRF ok.

				if ( wc_notice_count( 'error' ) === 0 && $non_js_checkout ) {
					wc_add_notice( __( 'The order totals have been updated. Please confirm your order by pressing the "Place order" button at the bottom of the page.', 'woocommerce' ) );
				}

				remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
				remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

				do_action( 'woocommerce_before_checkout_form', $checkout );

				echo '<div class="checkout-inner-wrap oxy-inner-content">';

				if( $content ) {
					if( function_exists('do_oxygen_elements') )
						echo do_oxygen_elements( $content );
					else
						echo do_shortcode( $content );
				}

				echo '</div>';

				do_action( 'woocommerce_after_checkout_form', $checkout );

				if( ! is_checkout() ) {
					wp_enqueue_script( 'wc-checkout' );
				}
			}
		}
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-checkout {
				width: 100%;
				min-height: 40px;
			}';

			$this->css_added = true;
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWCCheckoutPage();