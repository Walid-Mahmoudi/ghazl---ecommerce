<?php

class OUWooOrderItemImage extends UltimateWooEl {

	public $css_added = false;
	public $single_template = 'item-template.php';
	
	function name() {
		return __( "Product Image", 'oxyultimate-woo' );
	}

	function options(){
        return array(
            "only_child" => "oxy-order-items-builder"
        );
    }

	function slug() {
		return "order-item-image";
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-order-item-image-elements-label"
				ng-if="isActiveName('oxy-order-item-image')&&!hasOpenTabs('oxy-order-item-image')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-order-item-image-elements hide-ty-elements"
				ng-if="isActiveName('oxy-order-item-image')&&!hasOpenTabs('oxy-order-item-image')">
				<?php do_action("oxygen_add_plus_ultimatewoo_thankyou"); ?>
			</div>
		<?php }, 30 );
	}

	function ouwoo_button_place() {
		return "thankyou";
	}

	function button_priority() {
		return 4;
	}

	function controls() {

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Thumbnail Size', "oxyultimate-woo"),
			'slug' 		=> 'thumbnail_size',
			'value' 	=> $this->product_image_sizes(),
			'default' 	=> 'woocommerce_thumbnail'
		])->rebuildElementOnChange();
	}

	/**
	 * Making thumbnail size list 
	 */ 
	function product_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = $img_sizes =array();

		foreach( get_intermediate_image_sizes() as $s ) {
			$sizes[ $s ] = array( 0, 0 );
			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $s ][0] = get_option( $s . '_size_w' );
				$sizes[ $s ][1] = get_option( $s . '_size_h' );
			} else {
				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
			}
		}

		foreach( $sizes as $size => $atts ) {
			$size_title = ucwords(str_replace("-"," ", $size));
			$img_sizes[$size] =  $size_title . ' (' . implode( 'x', $atts ) . ')';
		}

		$img_sizes['full'] = __('Full');

		return $img_sizes;
	}

	function render($options, $defaults, $content) {
		global $item_data;

		if( UltimateWooEl::isBuilderEditorActive() || ( isset($_GET['action']) && $_GET['action'] == "ct_save_components_tree" ) ) {
			$order = UltimateWooEl::ouwoo_get_builder_preview_order();
			$item_data = UltimateWooEl::ouwoo_get_order_item_data($order);
		}
		
		extract($item_data);

		if( ! is_a( $product, 'WC_Product' ) )
			return;

		$thumbnail_size = isset($options['thumbnail_size']) ? $options['thumbnail_size'] : 'woocommerce_thumbnail';

		$thumbnail = $product->get_image($thumbnail_size);

		if( empty($thumbnail) )
			return;

		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

		if ( ! $product_permalink ) {
			echo $thumbnail;
		} else {
			printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
		}
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$css = ".oxy-order-item-image:empty {
						display: none;
					}
					.oxy-order-item-image {
						line-height: 0;
					}
					.oxy-order-item-image a {
						display: inline-block;
					}
					.oxy-order-item-image img {
						width: 100%;
						height: auto;
						max-width: 100%;
					}";

			$this->css_added = true;

			return $css;
		}
	}
}

new OUWooOrderItemImage();