<?php

class OUWooProductImages extends UltimateWooEl {
	public $slider_css = false;
	public $js_loaded = false;
	public $image_size = '';
	public $pswp = '';

	function name() {
		return __( "Product Gallery Slider", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_product_images";
	}

	function ouwoo_button_place() {
		return "main";
	}
	
	/*******************************
	 * Custom Init
	 *******************************/
	function custom_init() {
		add_filter("oxy_allowed_empty_options_list", array( $this, "ouwoo_allowed_empty_options_list" ) );
	}

	function ouwoo_allowed_empty_options_list( $empty_options ) {
		$new_empty_option = array(
			"oxy-ou_product_images_sale_text",
			"oxy-ou_product_images_sale_prefix",
			"oxy-ou_product_images_outofstock_label"
		);

		$empty_options = array_merge($empty_options, $new_empty_option);

		return $empty_options;
	}

    function general() {
        $this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Product ID', "oxyultimate-woo"),
			'slug' 		=> 'product_id'
		])->setParam('description', __('Left blank if you are using in single product page.', "oxyultimate-woo"));

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Zoom Effect', "oxyultimate-woo"),
			'slug' 		=> 'disable_zoom',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no'
		]);

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Photoswipe Effect', "oxyultimate-woo"),
			'slug' 		=> 'disable_pswp',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no'
		]);
	}
	
	function salesBadge() {
		$sale_badge = $this->addControlSection('sale_section', __('Sale Badge'), "assets/icon.png", $this );

		$sale_badge->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Badge Type', "oxyultimate-woo"),
			'slug' 		=> 'sale_type'
		])->setValue([
			'percent' 	=> __('Percentage', "oxyultimate-woo"),
			'text' 		=> __('Plain Text', "oxyultimate-woo"),
			'none' 		=> __('None', "oxyultimate-woo")
		])->setDefaultValue('text');

		$sale_badge->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Sale Text', "oxyultimate-woo"),
			'slug' 		=> 'sale_text',
			'default' 	=> 'Sale!',
			'condition' => 'sale_type=text'
		]);

		$sale_badge->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Prefix', "oxyultimate-woo"),
			'slug' 		=> 'sale_prefix',
			'default' 	=> '-',
			'condition' => 'sale_type=percent'
		]);

		$sale_badge->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Suffix', "oxyultimate-woo"),
			'slug' 		=> 'sale_suffix',
			'condition' => 'sale_type=percent'
		]);

		$selector = 'span.ouwoo-onsale.onsale';

		$style = $sale_badge->addControlSection('badge_settings', __('Settings'), "assets/icon.png", $this );
		$style->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$style->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'width'
		]);

		$style->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'height'
		]);

		$style->addStyleControl([
			'name' 			=> __('Position Top'),
			'selector' 		=> $selector,
			'property' 		=> 'top'
		])->setParam('hide_wrapper_end', true);

		$style->addStyleControl([
			'name' 			=> __('Position Left'),
			'selector' 		=> $selector,
			'property' 		=> 'left'
		])->setParam('hide_wrapper_start', true);

		$style->addStyleControl([
			'name' 			=> __('Position Right'),
			'selector' 		=> $selector,
			'property' 		=> 'right'
		]);

		//* Padding
		$spacing = $sale_badge->addControlSection('sp_sec', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"badge_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$sale_badge->typographySection(__('Typography'), $selector, $this);
		$sale_badge->borderSection(__('Border'), $selector, $this);
		$sale_badge->boxShadowSection(__('Box Shadow'), $selector, $this);
	}

	function outofstock() {
		$outofstock = $this->addControlSection('oos_section', __('Out Of Stock'), "assets/icon.png", $this );

		$outofstock->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Out of Stock Text', "oxyultimate-woo"),
			'slug' 		=> 'outofstock_label',
			'default' 	=> 'Sold Out'
		])->setParam('description', __('Do not show if you leave it empty.', "oxyultimate-woo"));

		$selector = '.ouwoo-out-of-stock-label';

		$style = $outofstock->addControlSection('oosbadge_settings', __('Settings'), "assets/icon.png", $this );
		$style->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$style->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'width'
		]);

		$style->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'height'
		]);

		$style->addStyleControl([
			'name' 			=> __('Position Top'),
			'selector' 		=> $selector,
			'property' 		=> 'top'
		])->setParam('hide_wrapper_end', true);

		$style->addStyleControl([
			'name' 			=> __('Position Left'),
			'selector' 		=> $selector,
			'property' 		=> 'left'
		])->setParam('hide_wrapper_start', true);

		$style->addStyleControl([
			'name' 			=> __('Position right'),
			'selector' 		=> $selector,
			'property' 		=> 'right'
		]);

		//* Padding
		$sp = $outofstock->addControlSection('oos_sec', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$sp->addPreset(
			"padding",
			"oosbdg_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$outofstock->typographySection(__('Typography'), $selector, $this);
		$outofstock->borderSection(__('Border'), $selector, $this);
		$outofstock->boxShadowSection(__('Box Shadow'), $selector, $this);
	}

	function slider() {
		$config = $this->addControlSection('config_sec', __('Slider', "oxyultimate-woo"), "assets/icon.png", $this );

		$sld = $config->addControlSection('sld_sec', __('Settings', "oxyultimate-woo"), "assets/icon.png", $this );

		$sld->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Effect (Single Image)', 'oxyultimate-woo'),
			'slug' 		=> 'slider_effect',
			'default' 	=> 'slide',
			'value' 	=> [
				'slide'		=> __('Slide', "oxy-ultimate"),
				'fade' 		=> __('Fade', "oxy-ultimate"),
				'flip' 		=> __('Flip', "oxy-ultimate")
			]
		]);

		$sld->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Speed', "oxyultimate-woo"),
			'slug' 		=> 'transition_speed'
		])->setRange('100', '4000', '10')->setUnits('ms','ms')->setValue('800');

		$sld->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Infinite Loop', "oxyultimate-woo"),
			'slug' 		=> 'infinite_loop',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no'
		]);

		$sld->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Auto Height', "oxyultimate-woo"),
			'slug' 		=> 'auto_height',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'yes'
		]);

		/*$sld->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Zoom Effect', "oxyultimate-woo"),
			'slug' 		=> 'disable_zoom',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no'
		]);*/

		$imgSize = $config->addControlSection('img_sec', __('Image Size', "oxyultimate-woo"), "assets/icon.png", $this );
		
		$thumbnail         = wc_get_image_size( 'thumbnail' );
		$single            = wc_get_image_size( 'single' );
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );

		$imgSize->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Single Images Size', "oxyultimate-woo"),
			'slug' 		=> 'single_image_size',
			'value' 	=> [
				'thumbnail' 				=> __('Thumbnails(150x150)', "oxyultimate-woo"),
				'woocommerce_thumbnail' 	=> __('WooCommerce Thumbnails('. $thumbnail['width']. 'x'. $thumbnail['height'] .')', "oxyultimate-woo"),
				'woocommerce_single' 		=> __('WooCommerce Single('. $single['width']. 'x'. $single['height'] .')', "oxyultimate-woo"),
				'woocommerce_gallery_thumbnail' => __('WooCommerce Gallery Thumbnails('. $gallery_thumbnail['width']. 'x'. $gallery_thumbnail['height'] .')', "oxyultimate-woo"),
				'full' 						=> __('Full', "oxyultimate-woo"),
			],
			'default' 	=> 'woocommerce_single'
		]);

		$imgSize->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Thumb Images Size', "oxyultimate-woo"),
			'slug' 		=> 'thumb_images_size',
			'value' 	=> [
				'thumbnails' 				=> __('Thumbnails(150x150)', "oxyultimate-woo"),
				'woocommerce_thumbnail' 	=> __('WooCommerce Thumbnails('. $thumbnail['width']. 'x'. $thumbnail['height'] .')', "oxyultimate-woo"),
				'woocommerce_single' 		=> __('WooCommerce Single('. $single['width']. 'x'. $single['height'] .')', "oxyultimate-woo"),
				'woocommerce_gallery_thumbnail' => __('WooCommerce Gallery Thumbnails('. $gallery_thumbnail['width']. 'x'. $gallery_thumbnail['height'] .')', "oxyultimate-woo"),
				'full' 						=> __('Full', "oxyultimate-woo"),
			],
			'default' 	=> 'thumbnails'
		]);

		$opacity = $config->addControlSection('thumbop_sec', __('Thumbs Opacity', "oxyultimate-woo"), "assets/icon.png", $this );
		$opacity->addStyleControl([
			'name' 		=> __('Initial', "oxyultimate-woo"),
			'selector' 	=> '.thumbnail-item',
			'property' 	=> 'opacity',
			'default' 	=> 1
		]);

		$opacity->addStyleControl([
			'name' 		=> __('On Hover', "oxyultimate-woo"),
			'selector' 	=> '.thumbnail-item:hover',
			'property' 	=> 'opacity',
			'default' 	=> 1
		]);

		$opacity->addStyleControl([
			'name' 		=> __('Active Thumbnail', "oxyultimate-woo"),
			'selector' 	=> '.thumbnail-item.swiper-slide-thumb-active',
			'property' 	=> 'opacity',
			'default' 	=> 1
		]);

		$opacity->addStyleControl([
			'name' 		=> __('Transition Duration', "oxyultimate-woo"),
			'selector' 	=> '.thumbnail-item',
			'property' 	=> 'transition-duration',
			'control_type'	=> 'slider-measurebox'
		])->setUnits('s', 'sec')->setRange(0,2,0.1)->setDefaultValue(0.7);

		$cols = $config->addControlSection('items_cols', __('Thumbs Columns'), 'assets/icon.png', $this );
		
		$cols->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Thumbnails', "oxyultimate-woo"),
			'slug' 		=> 'disable_thumbs_slider',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no'
		])->rebuildElementOnChange();

		$col_dsk = $cols->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxyultimate-woo"),
			'slug' 		=> 'columns'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('4');
		$col_dsk->rebuildElementOnChange();

		$bp_993 = $cols->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 993px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_993'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('4');
		$bp_993->rebuildElementOnChange();

		$bp_769 = $cols->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 769px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_769'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('4');
		$bp_769->rebuildElementOnChange();

		$bp_681 = $cols->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 681px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_681'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('3');
		$bp_681->rebuildElementOnChange();

		$bp_481 = $cols->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Less than 481px)', "oxyultimate-woo"),
			'slug' 		=> 'bp_481'
		])->setUnits(' ',' ')->setRange('1', '10', '1')->setValue('3');
		$bp_481->rebuildElementOnChange();
	}

	function arrows() {
		$arrows = $this->addControlSection('arrow_sec', __('Prev/Next', "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = '.ou-swiper-button';

		$leftA = $arrows->addControlSection('licon_sec', __('Left Arrow Icon', "oxyultimate-woo"), "assets/icon.png", $this );
		$leftArrow = $leftA->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Select Icon', "oxyultimate-woo"),
				"slug" 			=> 'arrow_left'
			)
		);
		$leftArrow->rebuildElementOnChange();

		$leftR = $arrows->addControlSection('ricon_sec', __('Right Arrow Icon', "oxyultimate-woo"), "assets/icon.png", $this );
		$rightArrow= $leftR->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Right Arrow', "oxyultimate-woo"),
				"slug" 			=> 'arrow_right'
			)
		);
		$rightArrow->rebuildElementOnChange();

		$config = $arrows->addControlSection('aconfig_sec', __('Visibility'), "assets/icon.png", $this );

		$config->addControl(
			'buttons-list',
			'arrows_on_hover',
			__('Show On Hover', "oxyultimate-woo")
		)->setValue([
			'yes' 	=> __('Yes'), 
			'no' 	=> __('No')
		])->setValueCSS([
			'yes' 	=> '
				.product-images:not(:hover) .swiper-button-prev,
				.product-thumbnail-images:not(:hover) .swiper-button-prev{opacity: 0; left: -1em;}
				.product-images:not(:hover) .swiper-button-next,
				.product-thumbnail-images:not(:hover) .swiper-button-next{opacity: 0; right: -1em;}
				.product-images:hover .ou-swiper-button,
				.product-thumbnail-images:hover .ou-swiper-button{opacity: 1;}
			'
		])->setDefaultValue('no');

		$config->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox',
		])->setUnits('s', 'sec')->setRange(0, 5, 0.1)->setDefaultValue(0.5);

		$config->addControl(
			'buttons-list',
			'hide_single_arrows',
			__('Hide Arrows of Single Image', "oxyultimate-woo")
		)->setValue([
			'yes' 	=> __('Yes'), 
			'no' 	=> __('No')
		])->setValueCSS([
			'yes' 	=> '.product-images .ou-swiper-button{display: none;}',
			'no' 	=> '.product-images .ou-swiper-button{display: flex;}'
		])->setDefaultValue('no');

		$config->addControl(
			'buttons-list',
			'hide_thumbs_arrows',
			__('Hide Arrows of Thumb Images', "oxyultimate-woo")
		)->setValue(
			['yes' => __('Yes'), 'no' => __('No')]
		)->setValueCSS([
			'yes' 	=> '.product-thumbnail-images .ou-swiper-button{display: none;}',
			'no' 	=> '.product-thumbnail-images .ou-swiper-button{display: flex;}'
		])->setDefaultValue('no');

		$position = $arrows->addControlSection('pos_sec', __('Position'), "assets/icon.png", $this);
		$position->addStyleControl([
			'name' 		=> __('Arrows of Single Image', "oxyultimate-woo"),
			'selectors' => array(
				array(
					'selector' 	=> '.product-images .swiper-button-prev',
					'property' 	=> 'left'
				),
				array(
					'selector' 	=> '.product-images .swiper-button-next',
					'property' 	=> 'right'
				)
			),
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 10
		]);

		$position->addStyleControl([
			'name' 		=> __('Arrows of Thumb Images', "oxyultimate-woo"),
			'selectors' => array(
				array(
					'selector' 	=> '.product-thumbnail-images .swiper-button-prev',
					'property' 	=> 'left'
				),
				array(
					'selector' 	=> '.product-thumbnail-images .swiper-button-next',
					'property' 	=> 'right'
				)
			),
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 10
		]);

		$colors = $arrows->addControlSection('color_sec', __('Colors'), "assets/icon.png", $this);

		$colors->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 	=> $selector . ":hover",
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Arrow Color', "oxyultimate-woo"),
				'selector' 	=> $selector,
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Arrow Color on Hover', "oxyultimate-woo"),
				'selector' 	=> $selector . ":hover",
				'property' 	=> 'color'
			]
		]);

		$size = $arrows->addControlSection('size_sec', __('Size', "oxyultimate-woo"), "assets/icon.png", $this);

		$size->addStyleControl([
			'name' 			=> __('Icon Size of Single Image', "oxyultimate-woo"),
			'selector' 		=> '.product-images ' . $selector . ' svg',
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 12
		]);

		$size->addStyleControl([
			'name' 			=> __('Button Size of Single Image', "oxyultimate-woo"),
			'selector' 		=> '.product-images ' . $selector,
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 40
		]);

		$size->addStyleControl([
			'name' 			=> __('Icon Size of Thumb Images', "oxyultimate-woo"),
			'selector' 		=> '.product-thumbnail-images ' . $selector . ' svg',
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 9
		]);

		$size->addStyleControl([
			'name' 			=> __('Button Size of Thumb Images', "oxyultimate-woo"),
			'selector' 		=> '.product-thumbnail-images ' . $selector,
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 25
		]);

		$arrows->borderSection(__('Border'), $selector, $this);
		$arrows->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ":hover", $this);
		$arrows->boxShadowSection(__('Shadow', "oxyultimate-woo"), $selector, $this);
		$arrows->boxShadowSection(__('Hover Shadow', "oxyultimate-woo"), $selector . ":hover", $this);
	}
    
    function controls() {
		$this->general();

		$this->slider();

		$this->arrows();

		$this->salesBadge();

		$this->outofstock();
    }

    function render( $options, $defaults, $content ) {
    	
		$this->image_size = isset( $options['single_image_size'] ) ? $options['single_image_size'] : 'woocommerce_single';

		add_filter( 'woocommerce_gallery_image_size', array( $this, 'ouwoo_set_variations_image_size' ) );
		add_action( 'woocommerce_after_variations_form', function() {
			remove_filter( 'woocommerce_gallery_image_size', array( $this, 'ouwoo_set_variations_image_size' ) );
		}, 99 );

		global $product, $post;
		if( isset( $options['product_id'] ) ) {
			$product = wc_get_product( $options['product_id'] );
		} else {
			$product = wc_get_product();
		}

		if( ! is_a( $product, 'WC_Product') )
			return;
		
		$image_ids = ! empty( $product ) ? $product->get_gallery_image_ids() : array();
		$disable_thumbs = (isset( $options['disable_thumbs_slider'] ) && $options['disable_thumbs_slider'] == 'yes' ) ? true : false;
		
		if( $disable_thumbs ) {
			$classes[]  = 'mb-zero';
		}

		$this->pswp = ( isset( $options['disable_pswp'] ) && $options['disable_pswp'] == "yes" ) ? 'yes' : 'no';

		$classes[]  = ! empty( $image_ids ) ? 'has-product-thumbnails' : '';
		$classes[]  = 'gallery-wrapper-' . $product->get_id();

		$dataAttr = ' data-comp-selector="' . $options['selector'] . '-' . $product->get_id() .'"';
		$dataAttr .= ' data-slider-effect="' . (isset($options['slider_effect']) ? $options['slider_effect'] : 'slide' ) . '"';
		$dataAttr .= ' data-sldts-speed="' . (isset($options['transition_speed']) ? $options['transition_speed'] : 800 ) . '"';
		$dataAttr .= ' data-cols-desktop="' . (isset($options['columns']) ? $options['columns'] : 4 ) . '"';
		$dataAttr .= ' data-cols-bp1="' . (isset($options['bp_993']) ? $options['bp_993'] : 4 ) . '"';
		$dataAttr .= ' data-cols-bp2="' . (isset($options['bp_769']) ? $options['bp_769'] : 4 ) . '"';
		$dataAttr .= ' data-cols-bp3="' . (isset($options['bp_681']) ? $options['bp_681'] : 3 ) . '"';
		$dataAttr .= ' data-cols-bp4="' . (isset($options['bp_481']) ? $options['bp_481'] : 3 ) . '"';
		$dataAttr .= ' data-sld-loop="' . ( isset($options['infinite_loop']) ? $options['infinite_loop'] : 'no' ) . '"';
		$dataAttr .= ' data-sld-autoheight="' . ( isset($options['auto_height']) ? $options['auto_height'] : 'yes' ) . '"';
		$dataAttr .= ' data-disable-pswp="' . $this->pswp . '"';
		
		$sale_type = isset( $options['sale_type']) ? $options['sale_type'] : false;
		if( $sale_type && $sale_type != "none" ) {
			$dataAttr .= ' data-sale-type="'. $options['sale_type'] .'"';
			if( $sale_type == "text" && isset( $options['sale_text'] ) ) {
				$dataAttr .= ' data-sale-text="'. wp_kses_post( $options['sale_text'] ) .'"';
			}

			if( $sale_type == "percent" ) {
				$dataAttr .= ' data-sale-prefix="'. ( isset( $options['sale_prefix'] ) ? wp_kses_post( $options['sale_prefix'] ) : '').'"';
				$dataAttr .= ' data-sale-suffix="'. ( isset( $options['sale_suffix'] ) ? wp_kses_post( $options['sale_suffix'] ) : '').'"';
			}
		}

		if( ! empty( $options['outofstock_label'] ) ) {
			$dataAttr .= ' data-outofstock-label="' . wp_kses_post( $options['outofstock_label'] ) . '"';
		}
		
		?>
			<div class="ouwoo-product-gallery product-images-slider <?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $dataAttr; ?>>
				<?php 
					$this->ouwoo_product_big_images( $product, $options );
					if( ! $disable_thumbs ) {
						$this->ouwoo_product_images_thumb( $product, $options );
					}
				?>
			</div>
		<?php
		
		if( $this->pswp == 'no' )
			include_once __DIR__ . '/photoswipe-markup.php';

		if( $this->isBuilderEditorActive() || isset( $_GET['ct_template'] ) ) {
			$this->ouwoo_product_images_script();
		} else {
			if( ! $this->js_loaded ) {
				$this->js_loaded = true;
				add_action('wp_footer', array( $this, 'ouwoo_product_images_script' ) );
			}
		}
	}

	/**
	 * Sales badge
	 */
	function ouwoo_product_sales_badge( $product, $options ) {

		if ( empty( $product ) ) {
			return;
		}
	
		$sale         = $product->is_on_sale();
		$sale_text    = ( isset( $options['sale_type'] ) && $options['sale_type'] == 'text' ) ? isset( $options['sale_text'] ) ? wp_kses_post($options['sale_text']) : false : false;
		$sale_percent = ( isset( $options['sale_type'] ) && $options['sale_type'] == 'percent' ) ? $options['sale_type'] : false;
		$final_price  = '';
		$out_of_stock = ouwoo_is_product_out_of_stock( $product );
	
		// Out of stock.
		if ( $out_of_stock ) {
			return;
		}
	
		if ( $sale ) {
			if ( $sale_percent ) {
				$prefix    = isset( $options['sale_prefix'] ) ? $options['sale_prefix'] : '';
				$suffix    = isset( $options['sale_suffix'] ) ? $options['sale_suffix'] : '';
				$final_price  = ou_get_sales_off_value( $sale_percent, $prefix, $product->get_id() );
				$final_price .= ' ' . $suffix;
			} elseif ( $sale_text ) {
				$final_price = $sale_text;
			}
	
			if ( ! $final_price ) {
				return;
			}
	
			?>
			<span class="ouwoo-onsale onsale">
				<?php echo esc_html( $final_price ); ?>
			</span>
			<?php
		}
	}

	/**
	 * Add out of stock label
	 */
	function ouwoo_product_out_of_stock_label( $product, $options ) {

		$outofstock_label = isset( $options['outofstock_label'] ) ? wp_kses_post($options['outofstock_label']) : false;

		if ( ! $outofstock_label ) {
			return;
		}

		if ( empty( $product ) ) {
			return;
		}

		if ( ! ouwoo_is_product_out_of_stock( $product ) ) {
			return;
		}

		if ( $product->backorders_allowed() ) {
			return;
		}
		?>
		<span class="ouwoo-out-of-stock-label"><?php echo $outofstock_label; ?></span>
		<?php
	}

	function ouwoo_get_image_alt( $id = null, $alt = '' ) {
		if ( ! $id ) {
			return esc_attr__( 'No image', 'oxyultimate-woo' );
		}
	
		$data    = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$img_alt = ! empty( $data ) ? $data : $alt;
	
		return apply_filters( 'ouwoo_product_image_alt', $img_alt );
	}

	function getArrows($options) {
		global $oxygen_svg_icons_to_load;
		
		if( isset($options['arrow_left']) ) { $oxygen_svg_icons_to_load[] = $options['arrow_left']; ?>
			<div class="ou-swiper-button ou-swiper-button-prev swiper-button-prev">
				<svg><use xlink:href="#<?php echo $options['arrow_left'];?>"></use></svg>
			</div>
		<?php } else { ?>
				<div class="ou-swiper-button ou-swiper-button-prev swiper-button-prev"><svg><use xlink:href="#Lineariconsicon-chevron-left"></use></svg></div>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"><defs><symbol id="Lineariconsicon-chevron-left" viewBox="0 0 20 20"><title>chevron-left</title><path class="path1" d="M14 20c0.128 0 0.256-0.049 0.354-0.146 0.195-0.195 0.195-0.512 0-0.707l-8.646-8.646 8.646-8.646c0.195-0.195 0.195-0.512 0-0.707s-0.512-0.195-0.707 0l-9 9c-0.195 0.195-0.195 0.512 0 0.707l9 9c0.098 0.098 0.226 0.146 0.354 0.146z"/></symbol></defs></svg>
		<?php } if( isset($options['arrow_right']) ) { $oxygen_svg_icons_to_load[] = $options['arrow_right']; ?>
			<div class="ou-swiper-button ou-swiper-button-next swiper-button-next"><svg><use xlink:href="#<?php echo $options['arrow_right'];?>"></use></svg></div>
		<?php } else { ?>
			<div class="ou-swiper-button ou-swiper-button-next swiper-button-next"><svg><use xlink:href="#Lineariconsicon-chevron-right"></use></svg></div>
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"><defs><symbol id="Lineariconsicon-chevron-right" viewBox="0 0 20 20"><title>chevron-right</title><path class="path1" d="M5 20c-0.128 0-0.256-0.049-0.354-0.146-0.195-0.195-0.195-0.512 0-0.707l8.646-8.646-8.646-8.646c-0.195-0.195-0.195-0.512 0-0.707s0.512-0.195 0.707 0l9 9c0.195 0.195 0.195 0.512 0 0.707l-9 9c-0.098 0.098-0.226 0.146-0.354 0.146z"/></symbol></defs></svg>
		<?php
			}
	}

	function ouwoo_product_big_images( $product, $options ) {
		$single_image_size 	 = isset( $options['single_image_size'] ) ? $options['single_image_size'] : 'woocommerce_single';
		$image_id            = $product->get_image_id();
		$image_alt           = $this->ouwoo_get_image_alt( $image_id, esc_attr__( 'Product image', 'oxyultimate-woo' ) );
		$get_size            = wc_get_image_size( $single_image_size );
		$image_size          = $get_size['width'] . 'x' . ( ! empty( $get_size['height'] ) ? $get_size['height'] : $get_size['width'] );
		$image_medium_src[0] = wc_placeholder_img_src();
		$image_full_src[0]   = wc_placeholder_img_src();
		$image_srcset        = '';
	
		if ( $image_id ) {
			$image_medium_src = wp_get_attachment_image_src( $image_id, $single_image_size );
			$image_full_src   = wp_get_attachment_image_src( $image_id, 'full' );
			$image_size       = $image_full_src[1] . 'x' . $image_full_src[2];
			$image_srcset     = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $image_id, $single_image_size ) : '';
		}

		$ez_zoom = ( isset( $options['disable_zoom'] ) && $options['disable_zoom'] == "yes" ) ? '' : ' ez-zoom';
	
		// Gallery.
		$gallery_id = $product->get_gallery_image_ids();
		?>
	
		<div class="product-images swiper-container big-images-<?php echo $options['selector'];?>-<?php echo $product->get_id(); ?>">
			<div id="product-images" class="swiper-wrapper" itemscope itemtype="http://schema.org/ImageGallery">
				<figure class="image-item swiper-slide<?php echo $ez_zoom; ?>" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" data-image-id="<?php echo $image_id; ?>">
					<a href="<?php echo esc_url( $image_full_src[0] ); ?>" itemprop="contentUrl" data-size="<?php echo esc_attr( $image_size ); ?>">
						<img width="<?php echo esc_attr( $image_full_src[1] ); ?>" height="<?php echo esc_attr( $image_full_src[2] ); ?>" srcset="<?php echo wp_kses_post( $image_srcset ); ?>" src="<?php echo esc_url( $image_medium_src[0] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
					</a>
				</figure>
				<?php
	
				if ( ! empty( $gallery_id ) ) {
					foreach ( $gallery_id as $key ) {
						$g_full_img_src   = wp_get_attachment_image_src( $key, 'full' );
						$g_medium_img_src = wp_get_attachment_image_src( $key, $single_image_size );
						$g_image_size     = $g_full_img_src[1] . 'x' . $g_full_img_src[2];
						$g_img_alt        = $this->ouwoo_get_image_alt( $key, esc_attr__( 'Product image', 'oxyultimate-woo' ) );
						$g_img_srcset     = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $key, $single_image_size ) : '';
						?>
						<figure class="image-item swiper-slide<?php echo $ez_zoom; ?>" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" data-image-id="<?php echo $key; ?>">
							<a href="<?php echo esc_url( $g_full_img_src[0] ); ?>" itemprop="contentUrl" data-size="<?php echo esc_attr( $g_image_size ); ?>">
								<img width="<?php echo esc_attr( $g_full_img_src[1] ); ?>" height="<?php echo esc_attr( $g_full_img_src[2] ); ?>"  src="<?php echo esc_url( $g_medium_img_src[0] ); ?>" alt="<?php echo esc_attr( $g_img_alt ); ?>" srcset="<?php echo wp_kses_post( $g_img_srcset ); ?>">
							</a>
						</figure>
						<?php
					}

					$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
					if( $get_variations && $product->get_type() == 'variable') {
						$available_variations = $product->get_available_variations();

						foreach ($available_variations as $key => $variation) { 
							if( ! in_array( $variation['image_id'], $gallery_id ) && $image_id != $variation['image_id'] ) {

								$v_full_img_src   = wp_get_attachment_image_src( $variation['image_id'], 'full' );
								$v_medium_img_src = wp_get_attachment_image_src( $variation['image_id'], $single_image_size );
								$v_image_size     = $v_full_img_src[1] . 'x' . $v_full_img_src[2];
								$v_img_alt        = $this->ouwoo_get_image_alt( $variation['image_id'], esc_attr__( 'Product image', 'oxyultimate-woo' ) );
								$v_img_srcset     = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $variation['image_id'], $single_image_size ) : '';
								?>
								<figure class="image-item swiper-slide<?php echo $ez_zoom; ?>" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" data-image-id="<?php echo $variation['image_id']; ?>">
									<a href="<?php echo esc_url( $v_full_img_src[0] ); ?>" itemprop="contentUrl" data-size="<?php echo esc_attr( $v_image_size ); ?>">
										<img width="<?php echo esc_attr( $v_full_img_src[1] ); ?>" height="<?php echo esc_attr( $v_full_img_src[2] ); ?>" src="<?php echo esc_url( $v_medium_img_src[0] ); ?>" alt="<?php echo esc_attr( $v_img_alt ); ?>" srcset="<?php echo wp_kses_post( $v_img_srcset ); ?>">
									</a>
								</figure>
								<?php
							}
						}
					}
				}
				?>
			</div>

			<?php
				do_action( 'ouwoo_product_images_slider_end', $product, $options );

				$this->ouwoo_product_sales_badge( $product, $options );
				$this->ouwoo_product_out_of_stock_label( $product, $options );
				
				if( ! empty( $gallery_id ) ) { $this->getArrows($options); }
			?>
		</div>
		<?php
	}

	function ouwoo_product_images_thumb( $product, $options ) {
		$thumb_images_size 	 = isset( $options['thumb_images_size'] ) ? $options['thumb_images_size'] : 'thumbnails';
		$image_id        = $product->get_image_id();
		$image_alt       = $this->ouwoo_get_image_alt( $image_id, esc_attr__( 'Product image', 'oxyultimate-woo' ) );
		$image_small_src = $image_id ? wp_get_attachment_image_src( $image_id, $thumb_images_size ) : wc_placeholder_img_src();
		$gallery_id      = $product->get_gallery_image_ids();
		if ( ! empty( $gallery_id ) ) {
		?>
			<div class="product-thumbnail-images swiper-container thumbs-<?php echo $options['selector'];?>-<?php echo $product->get_id(); ?>">
				<div id="product-thumbnail-images" class="swiper-wrapper" itemscope itemtype="http://schema.org/ImageGallery">
					<div class="thumbnail-item swiper-slide" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
						<img src="<?php echo esc_url( $image_small_src[0] ); ?>" width="<?php echo esc_attr( $image_small_src[1] ); ?>" height="<?php echo esc_attr( $image_small_src[2] ); ?>" itemprop="thumbnail" alt="<?php echo esc_attr( $image_alt ); ?>">
					</div>
		
					<?php
					foreach ( $gallery_id as $key ) :
						$g_thumb_src = wp_get_attachment_image_src( $key, $thumb_images_size );
						$g_thumb_alt = $this->ouwoo_get_image_alt( $key, esc_attr__( 'Product image', 'oxyultimate-woo' ) );
						?>
						<div class="thumbnail-item swiper-slide" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
							<img src="<?php echo esc_url( $g_thumb_src[0] ); ?>" width="<?php echo esc_attr( $g_thumb_src[1] ); ?>" height="<?php echo esc_attr( $g_thumb_src[2] ); ?>" itemprop="thumbnail" alt="<?php echo esc_attr( $g_thumb_alt ); ?>">
						</div>
					<?php endforeach; ?>
					<?php
						$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
						if( $get_variations && $product->get_type() == 'variable' ) {
							$available_variations = $product->get_available_variations();

							foreach ($available_variations as $key => $variation) {
								if( ! in_array( $variation['image_id'], $gallery_id ) && $image_id != $variation['image_id'] ) {
									$g_thumb_src = wp_get_attachment_image_src( $variation['image_id'], $thumb_images_size );
									$g_thumb_alt = $this->ouwoo_get_image_alt( $variation['image_id'], esc_attr__( 'Product image', 'oxyultimate-woo' ) );
					?>
								<div class="thumbnail-item swiper-slide" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
									<img src="<?php echo esc_url( $g_thumb_src[0] ); ?>" width="<?php echo esc_attr( $g_thumb_src[1] ); ?>" height="<?php echo esc_attr( $g_thumb_src[2] ); ?>" itemprop="thumbnail" alt="<?php echo esc_attr( $g_thumb_alt ); ?>">
								</div>
					<?php
								}
							}
						}
					?>
				</div>

				<?php $this->getArrows($options); ?>
				
			</div>
		<?php
		}
	}

	function ouwoo_product_images_script() {

		if( $this->pswp == 'no' ) {
			// Photoswipe init js.
			wp_enqueue_script(
				'ouwoo-photoswipe-init',
				OUWOO_URL . 'assets/js/photoswipe-init.min.js',
				array(),
				'1.0',
				true
			);
		}

		if( $this->pswp == 'yes' ) {
			wp_dequeue_script('photoswipe');
			wp_dequeue_script('photoswipe-ui-default');
		}

		// Product gallery zoom.
		wp_enqueue_script(
			'ouwoo-easyzoom',
			OUWOO_URL . 'assets/js/easyzoom.min.js',
			array(),
			'1.0',
			true
		);

		// Swiper Slider js.
		wp_enqueue_script( 
			'swiper-script', 
			OUWOO_URL . 'assets/js/swiper.min.js', 
			array(), 
			filemtime( OUWOO_DIR . 'assets/js/swiper.min.js' ), 
			true
		);

		// Product Images Slider js.
		wp_enqueue_script(
			'ouwoo-product-images',
			OUWOO_URL . 'assets/js/product-images-slider.min.js',
			array(),
			time(),
			true
		);
	}

	function customCSS( $original, $selector ) {
		global $ouwoo_constant;

		$css = '';

		if( ! $this->slider_css ) {
			if( ! $ouwoo_constant['swiper_css'] ) {
				$css .= file_get_contents( OUWOO_DIR . 'assets/css/swiper.min.css' );
				$ouwoo_constant['swiper_css'] = true;
			}		

			$css .= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->slider_css = true;
		}

		return $css;
	}

	function ouwoo_set_variations_image_size( $size ) {
		if( isset( $this->image_size ) ) {
			return $this->image_size;
		}

		return $size;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooProductImages();