<?php

class OUWooNewBadge extends UltimateWooEl {

	public $css_added = false;

	function name() {
		return __( "New Badge", 'oxyultimate-woo' );
	}

	function slug() {
		return "ouwoo_newbadge";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function tag() {
		return 'span';
	}

	function init() {
		$this->El->useAJAXControls();

		add_filter( 'do_shortcode_tag', [$this, 'check_permission'], 99, 3 );
	}

	function check_permission( $output, $tag, $options ) {
		if( $tag == 'oxy-ouwoo_newbadge' ) {
			$ct_options = json_decode( $options['ct_options'], true );

			if( ! $this->product_is_new( $ct_options['original'], 'oxy-ouwoo_newbadge_' ) )
				return;
		}

		return $output;
	}

	function controls() {
		$this->addCustomControl( 
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">' . 
			__('Builder editor will show the demo value for editing. You will get correct data at frontend.', 'oxyultimate-woo') . 
			'</div>', 
			'info'
		)->setParam('heading', 'Note:');

		$productID = $this->addOptionControl(
			array(
				"type" 		=> "textfield",
				"name" 		=> __('Product ID', "oxyultimate-woo"),
				"slug" 		=> 'product_id'
			)
		);
		$productID->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouwooNewBadgePrdID">data</div>');

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'badge',
			'name' 		=> esc_html__('Badge', "oxyultimate-woo"),
			'default' 	=> esc_html__('New', "oxyultimate-woo"),
		])->setParam('description', __('Click on Apply Params button to see the changes.', "oxyultimate-woo"));

		$daysfield = $this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'days',
			'name' 		=> esc_html__('Days', "oxyultimate-woo"),
			'default' 	=> 14
		]);
		$daysfield->setParam('description', __('Show badge if product is less than ... days old.', "oxyultimate-woo") );
		$daysfield->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouwooNewBadgeDays">data</div>');
	}

	function render( $options, $default, $content ) {
		$badge = isset( $options['badge'] ) ? esc_html( $options['badge'] ) : __('New', 'oxyultimate-woo');
		if( $this->product_is_new($options) && ! $this->isBuilderEditorActive() ) {
			echo $badge;
		} else {
			echo $badge;
		}
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$this->css_added = true;

			return '.oxy-ouwoo-newbadge {
						background-color: #03a9f4;
						color: #fff;
						display: flex; 
						padding: 4px 8px;
						align-items: center;
						justify-content: center;
					}';
		}
	}

	function product_is_new( $options, $slug = '' ) {
		global $product;

		$product_id = isset( $options[$slug . 'product_id'] ) ? $options[$slug . 'product_id'] : get_the_ID();

		if( ! is_object( $product ) ) {
			$product = WC()->product_factory->get_product( $product_id );
		}

		if( $product === false )
			return;

		$newness_in_days 	= isset( $options[$slug . 'days'] ) ? intval( $options[$slug . 'days'] ) : 14;
		$newness_timestamp 	= time() - ( 60 * 60 * 24 * $newness_in_days );
		$created 			= strtotime( $product->get_date_created() );

		return $newness_timestamp < $created;
	}
}

new OUWooNewBadge();