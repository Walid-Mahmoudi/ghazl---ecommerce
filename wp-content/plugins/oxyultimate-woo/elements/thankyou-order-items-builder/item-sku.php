<?php

class OUWooOrderItemSku extends UltimateWooEl {

	//public $css_added = false;
	public $single_template = 'item-template.php';
	
	function name() {
		return __( "SKU", 'oxyultimate-woo' );
	}

	function slug() {
		return "order-item-sku";
	}

	function options(){
		return array(
			"only_child" => "oxy-order-items-builder"
		);
	}
	
	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-order-item-sku-elements-label"
				ng-if="isActiveName('oxy-order-item-sku')&&!hasOpenTabs('oxy-order-item-sku')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-order-item-sku-elements hide-ty-elements"
				ng-if="isActiveName('oxy-order-item-sku')&&!hasOpenTabs('oxy-order-item-sku')">
				<?php do_action("oxygen_add_plus_ultimatewoo_thankyou"); ?>
			</div>
		<?php }, 30 );
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 9;
	}

	function controls() {
		
	}

	function render($options, $defaults, $content) {
		global $item_data;

		if( UltimateWooEl::isBuilderEditorActive() || ( isset($_GET['action']) && $_GET['action'] == "ct_save_components_tree" ) ) {
			$order = UltimateWooEl::ouwoo_get_builder_preview_order();

			$item_data = UltimateWooEl::ouwoo_get_order_item_data($order);
		}
		
		extract($item_data);

		if( is_object( $product ) ) {
			echo wp_kses_post( $product->get_sku() );
		}
	}
}

new OUWooOrderItemSku();