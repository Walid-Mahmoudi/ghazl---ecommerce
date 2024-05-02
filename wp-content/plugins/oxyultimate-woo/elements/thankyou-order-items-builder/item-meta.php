<?php

class OUWooOrderItemMeta extends UltimateWooEl {

	public $css_added = false;
	public $single_template = 'item-template.php';
	
	function name() {
		return __( "Variations Info", 'oxyultimate-woo' );
	}

	function options(){
		return array(
			"only_child" => "oxy-order-items-builder"
		);
	}

	function slug() {
		return "order-item-meta";
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-order-item-meta-elements-label"
				ng-if="isActiveName('oxy-order-item-meta')&&!hasOpenTabs('oxy-order-item-meta')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-order-item-meta-elements hide-ty-elements"
				ng-if="isActiveName('oxy-order-item-meta')&&!hasOpenTabs('oxy-order-item-meta')">
				<?php do_action("oxygen_add_plus_ultimatewoo_thankyou"); ?>
			</div>
		<?php }, 30 );
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 6;
	}

	function controls() {
		$this->addCustomControl(
			sprintf( '<div class="oxygen-option-default" style="color: #c3c5c7;font-size: 13px; line-height: 1.325">%s</div>', __('It will show the attributes of variable product type.', "oxyultimate-woo") ), 
			'description'
		)->setParam('heading', 'Note:');

		//* Padding & Margin
		$spacing = $this->addControlSection('metasp_sec', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"metasp_padding",
			__("Padding"),
			'.wc-item-meta'
		)->whiteList();

		$this->typographySection( __('Label'), '.wc-item-meta-label', $this );
		$this->typographySection( __('Value'), '.wc-item-meta li p', $this );
	}

	function render($options, $defaults, $content) {
		global $item_data;

		if( UltimateWooEl::isBuilderEditorActive() || ( isset($_GET['action']) && $_GET['action'] == "ct_save_components_tree" ) ) {
			$order = UltimateWooEl::ouwoo_get_builder_preview_order();

			$item_data = UltimateWooEl::ouwoo_get_order_item_data($order);
		}
		
		extract($item_data);

		if( is_object( $item ) ) {

			add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

			do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

			$html = wc_display_item_meta( $item, array('echo' => false) );

			if( empty($html) && UltimateWooEl::isBuilderEditorActive() ) {
				echo 'no data found';
			} else {
				echo $html;
			}

			do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
		}
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$css = ".oxy-order-item-meta {
						display: inline-block;
						border-left: 1px solid #dedede;
						width: 100%;
					}
					body:not(.oxygen-builder-body) .oxy-order-item-meta:empty {
						display:none;
						visibility: hidden;
					}
					.oxy-order-item-meta .wc-item-meta {
						padding: 0 0 0 10px;
						margin: 0;
						list-style: none;
					}
					.oxy-order-item-meta .wc-item-meta li {
						    display: flex;
    						align-items: center;
					}
					.oxy-order-item-meta .wc-item-meta li p {
						color: #999;
    					font-size: 11px;
						margin: 0;
						line-height: 1;
					}
					.oxy-order-item-meta .wc-item-meta-label {
						color: #888;
						font-size: 12px;
						margin-right: .25em;
						text-transform: capitalize;
						line-height: 1;
					}";

			$this->css_added = true;

			return $css;
		}
	}
}

new OUWooOrderItemMeta();