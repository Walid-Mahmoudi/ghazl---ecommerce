<?php

class OUWooOrderItemQty extends UltimateWooEl {

	public $css_added = false;
	public $single_template = 'item-template.php';
	
	function name() {
		return __( "Quantity", 'oxyultimate-woo' );
	}

	function options(){
		return array(
			"only_child" => "oxy-order-items-builder"
		);
	}

	function slug() {
		return "order-item-qty";
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-order-item-qty-elements-label"
				ng-if="isActiveName('oxy-order-item-qty')&&!hasOpenTabs('oxy-order-item-qty')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-order-item-qty-elements hide-ty-elements"
				ng-if="isActiveName('oxy-order-item-qty')&&!hasOpenTabs('oxy-order-item-qty')">
				<?php do_action("oxygen_add_plus_ultimatewoo_thankyou"); ?>
			</div>
		<?php }, 30 );
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 7;
	}

	function controls() {
		$this->typographySection( __('Quantity', "woocommerce"), '.product-quantity, .product-quantity > ins', $this );
		$this->typographySection( __('Strikethrough Qty', "oxyultimate-woo"), '.product-quantity > del', $this );
	}

	function render($options, $defaults, $content) {
		global $item_data;

		if( UltimateWooEl::isBuilderEditorActive() || ( isset($_GET['action']) && $_GET['action'] == "ct_save_components_tree" ) ) {
			$order = UltimateWooEl::ouwoo_get_builder_preview_order();

			$item_data = UltimateWooEl::ouwoo_get_order_item_data($order);
		}
		
		extract($item_data);

		$qty          = $item->get_quantity();
		$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

		if ( $refunded_qty ) {
			$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
		} else {
			$qty_display = esc_html( $qty );
		}

		echo apply_filters( 'woocommerce_order_item_quantity_html', '<span class="product-quantity">' . $qty_display . '</span>', $item );
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$css = ".product-quantity {
						font-weight: bold;
					}";

			$this->css_added = true;

			return $css;
		}
	}

}

new OUWooOrderItemQty();