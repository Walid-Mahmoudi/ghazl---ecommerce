<?php

class OUWooProductImage extends UltimateWooEl {

	public $css_added = false;

	function name() {
		return __( "Product Featured Image", 'oxyultimate-woo' );
	}

	function slug() {
		return "prdfeatimg";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function tag() {
		return 'figure';
	}

	function controls() {
		$thumbnail         = wc_get_image_size( 'thumbnail' );
		$single            = wc_get_image_size( 'single' );
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Thumbnail resolution', "oxyultimate-woo"),
			'slug' 		=> 'image_size',
			'value' 	=> [
				'thumbnail' 				=> __('Thumbnails(150x150)', "oxyultimate-woo"),
				'woocommerce_thumbnail' 	=> __('WooCommerce Thumbnails('. $thumbnail['width']. 'x'. $thumbnail['height'] .')', "oxyultimate-woo"),
				'woocommerce_single' 		=> __('WooCommerce Single('. $single['width']. 'x'. $single['height'] .')', "oxyultimate-woo"),
				'woocommerce_gallery_thumbnail' => __('WooCommerce Gallery Thumbnails('. $gallery_thumbnail['width']. 'x'. $gallery_thumbnail['height'] .')', "oxyultimate-woo"),
				'full' 						=> __('Full', "oxyultimate-woo"),
			],
			'default' 	=> 'woocommerce_thumbnail'
		]);

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show 2nd image on hover', "oxyultimate-woo"),
			'slug' 		=> 'hover_img',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'no',
		]);

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Link to post', "oxyultimate-woo"),
			'slug' 		=> 'link_to_post',
			'value' 	=> ['no' => __('No'), 'yes' => __('Yes')],
			'default' 	=> 'yes',
		]);
	}

	function render( $options, $default, $content ) {
		global $product;

		$product = wc_get_product();

		if( $product === false )
			return;

		$image_size 	= isset( $options['image_size'] ) ? $options['image_size'] : 'woocommerce_thumbnail';
		$hover_img 		= isset( $options['hover_img'] ) ? $options['hover_img'] : "no";
		$link_to_post 	= isset( $options['link_to_post'] ) ? $options['link_to_post'] : "yes";
		$permalink 		= ( $link_to_post == "yes" ) ? true : false;

		if ( $permalink ) {
			echo '<a href="' . get_the_permalink( $product->get_id() ) . '" aria-label="link to permalink" alt="link to permalink">';
		}

		echo $product->get_image( $image_size, [], true );

		if( $hover_img == "yes" ) {
			$attachment_ids = $product->get_gallery_image_ids();
			if ( $attachment_ids && $product->get_image_id() ) {
				echo wp_get_attachment_image( $attachment_ids[0], $image_size, false, [ 'class' => 'show-on-hover back-image' ] );
			}
		}

		if ( $permalink ) {
			echo '</a>';
		}
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$this->css_added = true;

			return '.oxy-prdfeatimg.woocommerce {
				position: relative;
				height: auto;
				margin: 0 auto;
				overflow: hidden;

				-webkit-transition: opacity .3s,background-color .3s,-webkit-transform .3s;
				transition: opacity .3s,background-color .3s,-webkit-transform .3s;
				-o-transition: opacity .3s,transform .3s,background-color .3s;
				transition: opacity .3s,transform .3s,background-color .3s;
				transition: opacity .3s,transform .3s,background-color .3s,-webkit-transform .3s;
			}

			.oxy-prdfeatimg.woocommerce > a {
				display: block;
				line-height: 0;
			}

			.oxy-prdfeatimg img:not(.zoomImg):not(.pswp__img) {
				-webkit-transition: opacity .6s,-webkit-filter .6s,-webkit-transform .6s,-webkit-box-shadow .3s;
				transition: opacity .6s,-webkit-filter .6s,-webkit-transform .6s,-webkit-box-shadow .3s;
				-o-transition: filter .6s,opacity .6s,transform .6s,box-shadow .3s;
				transition: filter .6s,opacity .6s,transform .6s,box-shadow .3s;
				transition: filter .6s,opacity .6s,transform .6s,box-shadow .3s,-webkit-filter .6s,-webkit-transform .6s,-webkit-box-shadow .3s;
			}

			.oxy-prdfeatimg.woocommerce .show-on-hover:not(.zoomImg):not(.pswp__img) {
				right: 0;
				width: 100%;
				height: 100%;
				bottom: 0;
				left: 0;
				top: 0;
				position: absolute;
				-o-object-position: 50% 50%;
				object-position: 50% 50%;
				-o-object-fit: cover;
				object-fit: cover;

				opacity: 0;
				-webkit-transition: opacity .5s,max-height .6s,-webkit-transform .3s,-webkit-filter .6s;
				transition: opacity .5s,max-height .6s,-webkit-transform .3s,-webkit-filter .6s;
				-o-transition: opacity .5s,transform .3s,max-height .6s,filter .6s;
				transition: opacity .5s,transform .3s,max-height .6s,filter .6s;
				transition: opacity .5s,transform .3s,max-height .6s,filter .6s,-webkit-transform .3s,-webkit-filter .6s;
				-webkit-filter: blur(0);
				filter: blur(0);
				pointer-events: none;
			}

			.oxy-prdfeatimg.woocommerce:hover img.show-on-hover:not(.zoomImg):not(.pswp__img) {
				opacity: 1;
				-webkit-transform: scale(1) translateZ(0) translateY(0)!important;
				transform: scale(1) translateZ(0) translateY(0)!important;
				pointer-events: inherit;
			}';
		}
	}
}

new OUWooProductImage();