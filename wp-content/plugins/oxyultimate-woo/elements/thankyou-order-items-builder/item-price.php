<?php

class OUWooOrderItemPrice extends UltimateWooEl {

	public $single_template = 'item-template.php';
	
	function name() {
		return __( "Price", 'oxyultimate-woo' );
	}

	function options(){
		return array(
			"only_child" => "oxy-order-items-builder"
		);
	}

	function slug() {
		return "order-item-price";
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-order-item-price-elements-label"
				ng-if="isActiveName('oxy-order-item-price')&&!hasOpenTabs('oxy-order-item-price')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-order-item-price-elements hide-ty-elements"
				ng-if="isActiveName('oxy-order-item-price')&&!hasOpenTabs('oxy-order-item-price')">
				<?php do_action("oxygen_add_plus_ultimatewoo_thankyou"); ?>
			</div>
		<?php }, 30 );
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 8;
	}

	function controls() {
		$this->typographySection( __('Typography'), '.woocommerce-Price-amount.amount', $this );
		$suffix = $this->typographySection( __('Suffix Text', "oxyultimate-woo"), '.shipped_via, .tax_label', $this );
		$pos = $suffix->addControl("buttons-list", "position", __("Place under the price", "oxyultimate-woo") );
		$pos->setValue(['No', 'Yes'])
			->setDefaultValue('No')
			->setValueCSS(['Yes' => '> span.woocommerce-Price-amount.amount{display: block;}'])
			->whiteList();
	}

	function render($options, $defaults, $content) {
		global $item_data;

		if( UltimateWooEl::isBuilderEditorActive() || ( isset($_GET['action']) && $_GET['action'] == "ct_save_components_tree" ) ) {
			$order = UltimateWooEl::ouwoo_get_builder_preview_order();

			$item_data = UltimateWooEl::ouwoo_get_order_item_data($order);
		}

		extract($item_data);

		if( ! is_object( $order ) )
			return;

		echo $order->get_formatted_line_subtotal( $item );
	}
}

new OUWooOrderItemPrice();