<?php

/**
 * UltimateWooEl
 */
class UltimateWooEl extends OxyEl
{
	public $single_template = '';
	
	function init()
	{
		$this->El->useAJAXControls();
		if( $this->single_template !== '' ) {
			add_filter("template_include", array( $this, "ouwoo_single_template" ), 1001 );
		}
	}

	function class_names() {
		return array('oxy-ultimate-element woocommerce');
	}

	/**
     * Setting button position
     */
	function button_place() {
		$button_place = $this->ouwoo_button_place();
		if( $button_place )
			return "ultimatewoo::" . $button_place;

		return "";
	}

	/**
     * Button order
     */
	function button_priority() {
        return '';
    }

    /**
     * Checking the builder editor
     */
    public static function isBuilderEditorActive() {
		if( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) {
			return true;
		}

		return false;
    }

    /**
     * Adding the data attributes to HTML markup
     */
    function generateDataAttributes( $options ) {
		$jsdata = '';

		$jsdata .= ' data-cols-desktop="' . (isset($options['columns']) ? $options['columns'] : 4 ) . '"';
		$jsdata .= ' data-cols-bp1="' . (isset($options['bp_993']) ? $options['bp_993'] : 3 ) . '"';
		$jsdata .= ' data-cols-bp2="' . (isset($options['bp_769']) ? $options['bp_769'] : 3 ) . '"';
		$jsdata .= ' data-cols-bp3="' . (isset($options['bp_681']) ? $options['bp_681'] : 2 ) . '"';
		$jsdata .= ' data-cols-bp4="' . (isset($options['bp_481']) ? $options['bp_481'] : 1 ) . '"';

		$jsdata .= ' data-gap-desktop="' . (isset($options['gap_dsk']) ? $options['gap_dsk'] : 15 ) . '"';
		$jsdata .= ' data-gap-bp1="' . (isset($options['gap_993']) ? $options['gap_993'] : 15 ) . '"';
		$jsdata .= ' data-gap-bp2="' . (isset($options['gap_769']) ? $options['gap_769'] : 15 ) . '"';
		$jsdata .= ' data-gap-bp3="' . (isset($options['gap_681']) ? $options['gap_681'] : 15 ) . '"';
		$jsdata .= ' data-gap-bp4="' . (isset($options['gap_481']) ? $options['gap_481'] : 15 ) . '"';

		$jsdata .= ' data-sts-desktop="' . (isset($options['sts_dsk']) ? $options['sts_dsk'] : 1 ) . '"';
		$jsdata .= ' data-sts-bp1="' . (isset($options['sts_993']) ? $options['sts_993'] : 1 ) . '"';
		$jsdata .= ' data-sts-bp2="' . (isset($options['sts_769']) ? $options['sts_769'] : 1 ) . '"';
		$jsdata .= ' data-sts-bp3="' . (isset($options['sts_681']) ? $options['sts_681'] : 1 ) . '"';
		$jsdata .= ' data-sts-bp4="' . (isset($options['sts_481']) ? $options['sts_481'] : 1 ) . '"';

		$jsdata .= ' data-tsp="' . (isset($options['transition_speed']) ? $options['transition_speed'] : 1000 ) . '"';
		$jsdata .= ' data-autoplay="' . (isset($options['autoplay']) ? $options['autoplay'] : 'yes' ) . '"';
		$jsdata .= ' data-autoplay-speed="' . ( ( isset($options['autoplay']) && $options['autoplay'] == "yes" ) ? $options['autoplay_speed'] : "false" ) . '"';
		$jsdata .= ' data-pause="' . (isset($options['pause_on_hover']) ? $options['pause_on_hover'] : 'yes' ) . '"';
		$jsdata .= ' data-pauseintr="' . (isset($options['pause_on_interaction']) ? $options['pause_on_interaction'] : 'yes' ) . '"';
		$jsdata .= ' data-centered="' . (isset($options['carousel_centered']) ? $options['carousel_centered'] : 'no' ) . '"';
		$jsdata .= ' data-loop="' . (isset($options['carousel_loop']) ? $options['carousel_loop'] : 'yes' ) . '"';
		$jsdata .= ' data-isbuilder="' . (defined('OXY_ELEMENTS_API_AJAX') ? 'yes' : 'no' ) . '"';

		return $jsdata;
	}

	/**
	 * Navigation button icons for slider
	 */
	function loadArrows( $options ) {
		if( $options['slider_navigation'] == 'yes' ) { 
			global $oxygen_svg_icons_to_load; 
	?>
			<?php if( isset($options['arrow_left']) ) { $oxygen_svg_icons_to_load[] = $options['arrow_left']; ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-prev swiper-button-prev">
					<svg><use xlink:href="#<?php echo $options['arrow_left'];?>"></use></svg>
				</div>
			<?php } else { ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-prev swiper-button-prev">
					<svg><use xlink:href="#Lineariconsicon-chevron-left"></use></svg>
				</div>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"><defs><symbol id="Lineariconsicon-chevron-left" viewBox="0 0 20 20"><title>chevron-left</title><path class="path1" d="M14 20c0.128 0 0.256-0.049 0.354-0.146 0.195-0.195 0.195-0.512 0-0.707l-8.646-8.646 8.646-8.646c0.195-0.195 0.195-0.512 0-0.707s-0.512-0.195-0.707 0l-9 9c-0.195 0.195-0.195 0.512 0 0.707l9 9c0.098 0.098 0.226 0.146 0.354 0.146z"/></symbol></defs></svg>
			<?php } if( isset($options['arrow_right']) ) { $oxygen_svg_icons_to_load[] = $options['arrow_right']; ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-next swiper-button-next"><svg><use xlink:href="#<?php echo $options['arrow_right'];?>"></use></svg></div>
			<?php } else { ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-next swiper-button-next"><svg><use xlink:href="#Lineariconsicon-chevron-right"></use></svg></div>

				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"><defs><symbol id="Lineariconsicon-chevron-right" viewBox="0 0 20 20"><title>chevron-right</title><path class="path1" d="M5 20c-0.128 0-0.256-0.049-0.354-0.146-0.195-0.195-0.195-0.512 0-0.707l8.646-8.646-8.646-8.646c-0.195-0.195-0.195-0.512 0-0.707s0.512-0.195 0.707 0l9 9c0.195 0.195 0.195 0.512 0 0.707l-9 9c-0.098 0.098-0.226 0.146-0.354 0.146z"/></symbol></defs></svg>
			<?php }
		}
	}

	/**
	 * Loading the woocommerce template file from OxyUltimateWoo plugin
	 */
	public static function ouwoo_woocommerce_locate_template( $template, $template_name, $template_path ) {
		$active_components = ouwoo_get_active_components();

		if( sizeof( (array) $active_components ) - 1 <= 0) {
			$active_components = array();
		}

		if( $template_name === 'cart/mini-cart.php' && array_intersect( ['offcanvascart', 'minicart', 'menucart'], $active_components ) ) {

			$new_template = OUWOO_DIR . 'templates/' . $template_name;

			if( file_exists( $new_template ) )
				return $new_template;
		}

		if ( is_checkout() && $template_name == 'checkout/thankyou.php' ) {
			global $wp;
			if (
				( isset( $wp->query_vars['order-received'] ) || isset( $wp->query_vars['order'] ) || is_wc_endpoint_url( 'order-received' ) ) &&
				ouwoo_oxygen_template_exist( 'ouwoo_template_thankyou' )
			) {
				
				$template = OUWOO_DIR . 'templates/thank-you.php';

				if( file_exists( $template ) )
					return $template;
			}
		}

		return $template;
	}

	/**
	 * Getting the order for Builder Editor
	 */
	public static function ouwoo_get_builder_preview_order( $order_id = 'latest' ) {
		if( $order_id == 'latest' ) {
			global $wpdb;

			$order_id = $wpdb->get_var( 
				"SELECT ID from $wpdb->posts 
				WHERE post_type='shop_order' 
				AND post_status IN ('wc-completed','wc-processing','wc-pending','wc-on-hold','wc-cancelled','wc-failed') 
				ORDER BY post_date DESC LIMIT 1" 
			);
		}
		
		if ( $order_id ) {
			return wc_get_order( $order_id );
		}

		return false;
	}


	/**
	 * Getting latest product
	 */
	public static function ouwoo_get_latest_product( $product_id = 'latest' ) {
		if( $product_id == 'latest' ) {
			global $wpdb;

			$product_id = $wpdb->get_var( 
				"SELECT ID from $wpdb->posts 
				WHERE post_type='product' 
				AND post_status IN ('publish') 
				ORDER BY post_date DESC LIMIT 1" 
			);
		}
		
		if ( $product_id ) {
			return WC()->product_factory->get_product( $product_id );
		}

		return false;
	}

	public static function ouwoo_get_order_item_data( $order = false ) {
		$item_data = array();

		if( $order ) {
			$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
			$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );

			foreach ( $order_items as $item_id => $item ) {

				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}

				$product = $item->get_product();

				$item_data = array(
					'order'              => $order,
					'item_id'            => $item_id,
					'item'               => $item,
					'show_purchase_note' => $show_purchase_note,
					'purchase_note'      => $product ? $product->get_purchase_note() : '',
					'product'            => $product,
				);

				break;
			}
		}
		
		return $item_data;
	}

	/**
	 * Custom Layout
	 */
	function ouwoo_single_template( $template ) {

        $new_template = '';

        if( ! empty($this->single_template) && isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'oxy_render_' . $this->El->get_tag() ) {
            
            if ( file_exists( OUWOO_DIR . '/templates/' . $this->single_template ) ) {
            	
            	global $oxy_api_element, $item_data;
				$oxy_api_element = $this->El;
                
                $new_template = OUWOO_DIR . '/templates/' . $this->single_template;
            }
        }

        if ( '' != $new_template ) {
            return $new_template ;
        }

        return $template;
    }
}