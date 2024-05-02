<?php

class OUWooOrderItemTitle extends UltimateWooEl {

	//public $css_added = false;
	public $single_template = 'item-template.php';
	
	function name() {
		return __( "Product Name", 'oxyultimate-woo' );
	}

	function options(){
		return array(
			"only_child" => "oxy-order-items-builder"
		);
	}

	function slug() {
		return "order-item-title";
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-order-item-title-elements-label"
				ng-if="isActiveName('oxy-order-item-title')&&!hasOpenTabs('oxy-order-item-title')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-order-item-title-elements hide-ty-elements"
				ng-if="isActiveName('oxy-order-item-title')&&!hasOpenTabs('oxy-order-item-title')">
				<?php do_action("oxygen_add_plus_ultimatewoo_thankyou"); ?>
			</div>
		<?php }, 30 );
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 5;
	}

	function controls() {
		$selector = 'a';
		$this->typographySection(__('Title'), $selector, $this);
	}

	function render($options, $defaults, $content) {
		global $item_data;

		if( UltimateWooEl::isBuilderEditorActive() || ( isset($_GET['action']) && $_GET['action'] == "ct_save_components_tree" ) ) {
			$order = UltimateWooEl::ouwoo_get_builder_preview_order();

			$item_data = UltimateWooEl::ouwoo_get_order_item_data($order);
		}
		
		extract($item_data);

		if( is_object( $item ) ) {
			
			$is_visible        	= $product && $product->is_visible();
			$product_permalink 	= apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
			$title 				= $item->get_name();

			echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $title ) : $title, $item, $is_visible ) );
		}
	}
}

new OUWooOrderItemTitle();