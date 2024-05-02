<?php

class OUWooOrderItemsBuilder extends UltimateWooEl {

	public $css_added = false;

	function name() {
		return __( "Items List Builder", 'oxyultimate-woo' );
	}

	function slug() {
		return "order-items-builder";
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 3;
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-order-items-builder-elements-label"
				ng-if="isActiveName('oxy-order-items-builder')&&!hasOpenTabs('oxy-order-items-builder')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-order-items-builder-elements hide-ty-elements"
				ng-if="isActiveName('oxy-order-items-builder')&&!hasOpenTabs('oxy-order-items-builder')">
				<?php do_action("oxygen_add_plus_ultimatewoo_thankyou"); ?>
			</div>
		<?php }, 30 );
	}

	function controls() {
		$this->addCustomControl(
			sprintf( '<div class="oxygen-option-default" style="color: #c3c5c7;font-size: 13px; line-height: 1.325">%s</div>', __('You can overwrite the default layout with Available Elements which are listed below.', "oxyultimate-woo") ), 
			'description'
		)->setParam('heading', 'Note:');

		$this->addOptionControl([
			'type' 	=> 'dropdown',
			'name'  => __('Order ID for Builder Preview', "oxyultimate-woo"),
			'slug' 	=> 'order_preview',
			'value' => [
				'latest' => __('Latest Order', 'oxyultimate-woo'),
				'custom' => __('Specific Order', 'oxyultimate-woo')
			],
			'default' => 'latest'
		]);

		$this->addOptionControl([
			'type' 	=> 'textfield',
			'name'  => __('Enter Order ID', "oxyultimate-woo"),
			'slug' 	=> 'order_id',
			'condition' => 'order_preview=custom'
		])->setParam('description', __('Click on Apply Params button and apply the changes', "oxyultimate-woo" ) );
	}

	function render($options, $defaults, $content) {

		$order_id = 'latest';

		if( isset($options['order_preview']) && $options['order_preview'] != 'latest' ) {
			$order_id = isset($options['order_id']) ? $options['order_id'] : $order_id;
		}

		$order = ouwoo_get_order( $order_id );

		if( ! $content && $order ) {
			 woocommerce_order_details_table( $order->get_id() );
		} else {
			if( UltimateWooEl::isBuilderEditorActive() ) {
				
				echo '<div class="oxy-product-wrapper-inner oxy-inner-content">';
				echo do_shortcode( $content );
				echo '</div>';
			
			} else {

				if ( ! $order )
					return;

				global $item_data;

				$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
				$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );

				do_action( 'woocommerce_order_details_before_order_table_items', $order );

				add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_false' );

				foreach ( $order_items as $item_id => $item ) {

					$product = $item->get_product();

					$item_data = array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					);

					if( $content && apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
						echo do_shortcode( $content );
					}
				}

				do_action( 'woocommerce_order_details_after_order_table_items', $order );
			}
		}
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$css = ".oxy-order-items-builder {
						min-height: 40px;
						width: 100%;
					}";
			$this->css_added = true;

			return $css;
		}
	}
}

new OUWooOrderItemsBuilder();

include_once __DIR__ . '/item-image.php';
include_once __DIR__ . '/item-title.php';
include_once __DIR__ . '/quantity.php';
include_once __DIR__ . '/item-meta.php';
include_once __DIR__ . '/item-price.php';
include_once __DIR__ . '/item-sku.php';