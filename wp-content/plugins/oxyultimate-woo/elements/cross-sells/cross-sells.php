<?php

class OUWooCrossSells extends UltimateWooEl {
	public $css_added = false;
	public $heading = '';

	function name() {
		return __( "Cross-sells", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_cross_sells";
	}

	function ouwoo_button_place() {
		return "cart";
	}

	function button_priority() {
		return 5;
	}

	function custom_init() {
		add_filter("oxygen_vsb_element_presets_defaults", array( $this, "ouwoo_cross_sells_presets_defaults" ) );
	}

	function ouwoo_cross_sells_presets_defaults( $all_elements_defaults ) {
		require("cross-sells-presets.php");

		$all_elements_defaults = array_merge($all_elements_defaults, $cross_sells);

		return $all_elements_defaults;
	}


	/***************************
	 * Outer Wrapper
	 ***************************/
	function csOuterWrapper() {
		$wrapper = $this->addControlSection( 'wrapper_section', __('Outer Wrapper', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.cross-sells';

		$wrapper->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$wrapper->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'width',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px'
		]);

		$spacing = $wrapper->addControlSection( 'wrappersp_section', __('Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"csw_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"csw_margin",
			__("Margin"),
			$selector
		)->whiteList();
	}


	/***************************
	 * Section Heading
	 ***************************/
	function sectionHeading() {
		$heading = $this->addControlSection( 'heading_section', __('Heading'), 'assets/icon.png', $this );

		$selector = '.cross-sells > h2';

		$heading->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">' . __('Click on <span style="color:#ff7171;">Apply Params</span> button at below and apply changes.') . '</div>', 
			'description'
		)->setParam('heading', __('Note:', "oxyultimate-woo"));

		$heading->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Title'),
			'placeholder' 	=> __( 'You may be interested in&hellip;', 'woocommerce' ),
			'slug' 		=> 'cs_heading'
		]);

		$heading->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$spacing = $heading->addControlSection( 'heading_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"hsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"hsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$heading->typographySection( __('Typography'), $selector, $this );
	}

	function Products() {
		$products = $this->addControlSection( 'products_section', __('Products', "woocommerce"), 'assets/icon.png', $this );

		/* Query */
		$query = $products->addControlSection("prd_query", __("Query", "oxyultimate-woo"), "assets/icon.png", $this);

		$limit = $query->addOptionControl([
			'type' 		=> 'measurebox',
			'name' 		=> __('Limit'),
			'slug' 		=> 'prd_limit'
		]);

		$limit->setUnits(' ', ' ');
		$limit->setDefaultValue(2);
		$limit->setParam('description', 'How many products will show? Default value is 2');
		$limit->rebuildElementOnChange();

		$query->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Columns'),
			'slug' 		=> 'prd_columns',
			'value' 	=> [ '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'],
			'default' 	=> '2'
		])->rebuildElementOnChange();

		$query->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Orderby'),
			'slug' 		=> 'prd_orderby',
			'value' 	=> [ 
				'date' 		=> 'date', 
				'id' 		=> 'id', 
				'menu_order' => 'menu_order', 
				'popularity' => 'popularity', 
				'rand' 		=> 'rand',
				'rating' 	=> 'rating',
				'title' 	=> 'title',
			],
			'default' 	=> 'rand'
		])->rebuildElementOnChange();

		$query->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Order'),
			'slug' 		=> 'prd_order',
			'value' 	=> [ 'ASC' => 'ASC', 'DESC' => 'DESC'],
			'default' 	=> 'DESC'
		])->rebuildElementOnChange();

		/* Layout */
		$layout_section = $products->addControlSection("layout", __("Layout"), "assets/icon.png", $this);

		$layout_section->addPreset(
			"padding",
			"columns_inner_padding",
			__("Columns Inner Padding"),
			'li.product'
		);

		$items_align = $layout_section->addControl("buttons-list", "items_align", __("Inner Contents Alignment", "oxyultimate-woo") );

		$items_align->setValue( array(
			"left"		=> "Left",
			"center" 	=> "Center", 
			"right" 	=> "Right" 
		));

		$items_align->setValueCSS( array(

			"left" => "
				ul.products li.product {
				align-items: flex-start;
				text-align: left;
			}
			",

			"center" => "
				ul.products li.product {
				align-items: center;
				text-align: center;
			}
			.products .star-rating {
				margin-left: auto;
				margin-right: auto;
			}
			",

			"right" => "
				ul.products li.product {
				align-items: flex-end;
				text-align: right;
			}
			.products .star-rating {
				margin-left: auto;
			}
			"
		));


		/* Title */
		$title = $products->typographySection( __("Title"), "ul.products li.product .woocommerce-loop-product__title", $this );

		$title->addStyleControl(
			array(
				"name" 		=> __('Hover Color', "oxyultimate-woo"),
				"selector" 	=> 'ul.products li.product .woocommerce-loop-product__title:hover',
				"property" 	=> 'color',
			)
		);

		$title->addPreset(
			"padding",
			"prdtitle_padding",
			__("Padding"),
			'ul.products li.product .woocommerce-loop-product__title'
		);

		/* Price */
		$price = $products->typographySection( __("Price", "woocommerce"), ".price, .price span", $this );

		$display = $price->addControl("buttons-list", "price_display", __("Display", "oxyultimate-woo") );

		$display->setValue( array(
			"block"		=> __("Stack", "oxyultimate-woo"),
			"inline" 	=> __("Inline", "oxyultimate-woo")
		));

		$display->setValueCSS( array(
			'inline' => 'ul.products li.product .price del{display: inline-block}'
		) );

		$display->setDefaultValue('block');

		$display->whiteList();

		$price->addStyleControls([
			array(
				"name" 			=> __('Gap Between Prices', "oxyultimate-woo"),
				"selector" 		=> 'ul.products li.product .price del',
				"property" 		=> 'margin-right',
				"control_type" 	=> 'slider-measurebox',
				"unit" 			=> 'px'
			),
			array(
				"name" 		=> __('Color of Strikethrough Price', "oxyultimate-woo"),
				"selector" 	=> '.price del span, ul.products li.product .price del',
				"property" 	=> 'color'
			),
			array(
				"name" 		=> __('Font Size of Strikethrough Price', "oxyultimate-woo"),
				"selector" 	=> '.price del span, ul.products li.product .price del',
				"property" 	=> 'font-size'
			),
			array(
				"name" 		=> __('Font Weight of Strikethrough Price', "oxyultimate-woo"),
				"selector" 	=> '.price del span, ul.products li.product .price del',
				"property" 	=> 'font-weight'
			)
		]);

		/* Stars */
		$stars_section = $products->addControlSection("stars", __("Stars", "oxyultimate-woo"), "assets/icon.png", $this);
		$stars_section->addStyleControls(array(
			array(
				"name" => __('Stars Size', "oxyultimate-woo"),
				"selector" => ".star-rating",
				"property" => 'font-size',
			),
			array(
				"name" => __('Filled Stars Color', "oxyultimate-woo"),
				"selector" => ".star-rating span",
				"property" => 'color',
			),
			array(
				"name" => __('Empty Stars Color', "oxyultimate-woo"),
				"selector" => ".star-rating::before",
				"property" => 'color',
			),
		));
	}

	function productImages() {
		$images = $this->addControlSection('images', __('Product Images', "oxyultimate-woo"), 'assets/icon.png', $this );
		
		$selector = '.cross-sells ul.products li.product a img';

		$images->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $images->addControlSection('prdimg_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"prdimg_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"prdimg_margin",
			__("Margin"),
			$selector
		)->whiteList();		

		$images->borderSection(__('Border'), $selector, $this);
		$images->boxShadowSection(__('Box Shadow'), $selector, $this);
	}

	function salesBadge() {
		$badge = $this->addControlSection('badge', __('Sales Badge', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = 'ul.products li.product .onsale, span.onsale';

		$spacing = $badge->addControlSection('badge_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"badge_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"badge_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$tg = $badge->typographySection(__('Typography'), $selector, $this);
		$tg->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$position = $badge->addControlSection('badge_position', __('Position'), 'assets/icon.png', $this );
		$position->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'top',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setDefaultValue(20);

		$position->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'left',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,vw')->setDefaultValue(20);

		$badge->borderSection(__('Border'), $selector, $this);
		$badge->boxShadowSection(__('Box Shadow'), $selector, $this);
	}

	function ProductButton() {
		$button = $this->addControlSection('addtocart_btn', __('Button'), 'assets/icon.png', $this );

		$selector = 'ul.products li.product a.button';

		$button->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox'
			]
		)->setUnits('px', 'px,%,em,auto,rem')->setRange(0, 1000, 10);

		$spacing = $button->addControlSection('addtocart_btn_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"atcbtn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"atcbtn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$color = $button->addControlSection('addtocart_btn_color', __('Color'), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Text Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ':hover',
				'property' 		=> 'color'
			]
		]);

		//* Typography
		$button->typographySection(__('Typography'), $selector, $this );

		//* Border
		$button->borderSection(__('Border'), $selector, $this );
		$button->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ':hover', $this );

		//* Box Shadow
		$button->boxShadowSection( __('Box Shadow'), $selector, $this );
		$button->boxShadowSection( __('Hover Box Shadow', "oxyultimate-woo"), $selector . ':hover', $this );
	}

	function controls() {

		$this->csOuterWrapper();

		$this->sectionHeading();

		$this->Products();

		$this->productImages();

		$this->salesBadge();

		$this->ProductButton();
	}

	function render($options, $defaults, $content) {
		if( isset( $options['cs_heading'] ) ) {
			$this->heading = wp_kses_post( $options['cs_heading'] );

			add_filter( 'woocommerce_product_cross_sells_products_heading', array( $this, 'cross_sells_products_heading' ) );
		}

		$limit = isset( $options['prd_limit'] ) ? intval( $options['prd_limit'] ) : 2;
		$columns = isset( $options['prd_columns'] ) ? intval( $options['prd_columns'] ) : 2;
		$orderby = isset( $options['prd_orderby'] ) ? $options['prd_orderby'] : 'rand';
		$order = isset( $options['prd_order'] ) ? $options['prd_order'] : 'DESC';

		woocommerce_cross_sell_display($limit, $columns, $orderby, $order);

		if( isset( $options['cs_heading'] ) ) {
			remove_filter( 'woocommerce_product_cross_sells_products_heading', array( $this, 'cross_sells_products_heading' ) );
		}
	}

	function cross_sells_products_heading( $heading ) {
		return ( ! empty( $this->heading ) ? $this->heading : $heading );
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-cross-sells {
						display: flex;
						min-height: 40px;
						width: 100%;
					}
					
					.cross-sells ul.products li.product a img {
						border: none;
					}

					.cross-sells ul.products li.product a .onsale,
					.cross-sells a span.onsale {
						border-radius: 0;
						padding: 10px;
						top: 20px;
						left: 20px;
					}
					';
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooCrossSells();