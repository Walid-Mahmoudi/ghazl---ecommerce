<?php
class OUSalesOffer extends UltimateWooEl {
	function name() {
		return __( "Sales Offer", 'oxyultimate-woo' );
	}

	function slug() {
		return "ou_sales_offer";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function controls() {
		$this->addOptionControl(
			[
				'type' 		=> 'radio',
				'name' 		=> esc_html__('Calculate', 'oxyultimate-woo'),
				'slug' 		=> 'type',
				'default' 	=> 'percentage',
				'value' 	=> ['percentage' => esc_html__( 'Percentage', 'oxyultimate-woo' ), 'fixed' => esc_html__( 'Fixed Rate', 'oxyultimate-woo' )]
			]
		)->rebuildElementOnChange();

		$this->addOptionControl(
			[
				'type' 		=> 'radio',
				'name' 		=> esc_html__('Math Logic', 'oxyultimate-woo'),
				'slug' 		=> 'math',
				'default' 	=> 'round',
				'value' 	=> [
					'ceil' 	=> esc_html__( 'Ceil', 'oxyultimate-woo' ),
					'floor' => esc_html__( 'Floor', 'oxyultimate-woo' ),
					'round' => esc_html__( 'Round', 'oxyultimate-woo' )
				],
				'condition' => 'type=percentage'
			]
		)->rebuildElementOnChange();

		$this->addOptionControl(
			[
				'type' 		=> 'textfield',
				'name' 		=> esc_html__('Before Text', 'oxyultimate-woo'),
				'placeholder' => esc_html__('Enter before text', 'oxyultimate-woo'),
				'slug' 		=> 'before',
				'description' => esc_html__('Click on Apply Params button to see the changes', 'oxyultimate-woo')
			]
		);

		$this->addOptionControl(
			[
				'type' 		=> 'textfield',
				'name' 		=> esc_html__('After Text', 'oxyultimate-woo'),
				'placeholder' => esc_html__('Enter after text', 'oxyultimate-woo'),
				'slug' 		=> 'after',
				'description' => esc_html__('Click on Apply Params button to see the changes', 'oxyultimate-woo')
			]
		);

		$productIDFld = $this->addOptionControl(
			[
				'type' 		=> 'textfield',
				'name' 		=> esc_html__('Product ID', 'oxyultimate-woo'),
				'description' => esc_html__('Leave it blank if you are using on single product page or repeater.', 'zilultimate'),
				'slug' 		=> 'product_id'
			]
		);
		$productIDFld->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouwooSOProductID">data</div>');
		$productIDFld->rebuildElementOnChange();
	}

	function fetchDynamicProductID( $id ) {
		if( ! empty( $id ) && strstr( $id, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($id));
			$id = do_shortcode($shortcode);
		} elseif( ! empty( $id ) ) {
			return intval( $id );
		} else {
			$id = get_the_ID();
		}

		return intval( $id );
	}

	function render( $options, $default, $content ) {
		global $product;

		if( ! is_object( $product ) ) {
			$product = WC()->product_factory->get_product( $this->fetchDynamicProductID( $options['product_id'] ) );
		}

		if( $product === false )
			return;

		if( $product->get_type() === 'simple' || $product->get_type() === 'external' ) {
			$regular_price = (float) $product->get_regular_price();
			$sale_price = (float) $product->get_sale_price();
		} 

		if( $product->get_type() === 'variable' || $product->get_type() === 'variation' ) {
			$regular_price = (float) $product->get_variation_regular_price( 'min', true );
			$sale_price = (float) $product->get_variation_sale_price( 'min', true );
		}

		if( empty ( $sale_price ) )
			return;

		$calculate = isset( $options['type'] ) ? $options['type'] : 'percentage';

		if( $calculate == 'fixed' ) {
			$sales_off_price = wc_price( $regular_price - $sale_price );
		} else {
			$math_fn = isset( $options['math'] ) ? $options['math'] : 'ceil';

			if( $math_fn == 'ceil' )
				$sales_off_price = ceil( 100 - ( ( $sale_price / $regular_price ) * 100 ) ) . '%';
			elseif( $math_fn == 'round' )
				$sales_off_price = round( 100 - ( ( $sale_price / $regular_price ) * 100 ) ) . '%';
			else
				$sales_off_price = floor( 100 - ( ( $sale_price / $regular_price ) * 100 ) ) . '%';
		}

		$before_text = isset( $options['before'] ) ? wp_kses_post( $options['before'] ) . ' ' : '';
		$after_text = isset( $options['after'] ) ? ' ' . wp_kses_post( $options['after'] ) : '';

		echo $before_text . $sales_off_price . $after_text;
	}
}

new OUSalesOffer();