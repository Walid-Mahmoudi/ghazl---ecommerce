<?php
namespace Oxygen\OxyUltimate;

class OURating extends \OxyUltimateEl {
	public $css_added = false;
	public $js_added = false;

	function name() {
		return __( "Star Rating", "oxy-ultimate" );
	}

	function slug() {
		return "ou_rating";
	}

	function oxyu_button_place() {
		return "content";
	}

	function controls() {
		$rating = $this->addOptionControl( 
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Rating point' , "oxy-ultimate"),
				'slug' 		=> 'rating_points',
				'placeholder' => '3.7',
				'value' => 3.7
			)
		);

		$rating->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouRating">data</div>');
		$rating->rebuildElementOnChange();

		$this->addOptionControl( 
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Aria label' , "oxy-ultimate"),
				'slug' 		=> 'aria_label',
				'placeholder' => 'enter a text',
			)
		);

		$this->addOptionControl( 
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('Display stars' , "oxy-ultimate"),
				'slug' 		=> 'displayStars',
				'value' 	=> [
					"full" => esc_html__('With empty stars', 'oxy-ultimate' ), 
					"noempty" => esc_html__('Without empty stars', 'oxy-ultimate' )
				],
				'default' 	=> "full",
			)
		)->rebuildElementOnChange();

		$this->addStyleControls(
			array(
				array(
					"name" 		=> esc_html__('Fill stars color', 'oxy-ultimate'),
					"selector" 	=> 'span',
					"property" 	=> '--star-background',
					"control_type" 	=> 'colorpicker',
					"slug" 	=> 'fillStarsColor'
				),
				array(
					"name" 		=> esc_html__('Empty stars color', 'oxy-ultimate'),
					"selector" 	=> 'span',
					"property" 	=> '--star-color',
					"control_type" 	=> 'colorpicker',
					"slug" 	=> 'emptyStarsColor'
				),
				array(
					"name" 		=> esc_html__('Stars Size', 'oxy-ultimate'),
					"selector" 	=> 'span',
					"property" 	=> 'font-size',
					"slug" 	=> 'starsSize'
				),
				array(
					"name" 		=> esc_html__('Space between stars', 'oxy-ultimate'),
					"selector" 	=> 'span,span:before',
					"property" 	=> 'letter-spacing|--gap-stars',
					"control_type" 		=> 'slider-measurebox',
					"unit" 				=> 'px',
					"value" 			=> 3,
					"slug" 	=> 'gapStars'
				),
			)
		);
	}

	function render( $options, $defaults, $content ) {
		$data = '';
		$ratings = isset( $options['rating_points'] ) ? $this->fetchDynamicData( $options['rating_points'] ) : 3.7;

		$data .= 'style="--rating:' . $ratings . '" ';

		$displayStars = isset( $options['displayStars'] ) ? $options['displayStars'] : 'full';
		$data .= 'data-star-type="' . $displayStars . '" ';

		$aria = isset( $options['aria_label'] ) ? $options['aria_label'] : false;
		if( $aria ) {
			$data .= 'aria-label="' . esc_attr__($aria) . '"';
		}

		echo "<span {$data}></span>";
	}

	function fetchDynamicData( $field ) {
		if( strstr( $field, 'oudata_') ) {
			$field = base64_decode( str_replace( 'oudata_', '', $field ) );
			$shortcode = ougssig( $this->El, $field );
			$field = do_shortcode( $shortcode );
		} elseif( strstr( $field, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($field));
            $field =  esc_attr(do_shortcode($shortcode));
		}

		return $field;
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-rating span{
					--star-color: #777;
					--star-background: #fc0;
					--percent: calc(var(--rating) / 5 * 100%);
				  display: inline-block;
				  font-size: 20px;
				  font-family: Times;
				  line-height: 1;
				}

				.oxy-ou-rating span::before {
				    content: \'★★★★★\';
				    letter-spacing: 3px;
				    background: linear-gradient(90deg, var(--star-background) var(--percent), var(--star-color) var(--percent));
				    -webkit-background-clip: text;
				    -webkit-text-fill-color: transparent;
				    word-wrap: normal;
				}

				.oxy-ou-rating span[data-star-type="noempty"] {
				  width: calc( var(--rating) / 5 * 100% );
				  overflow: hidden;
				}';

			$this->css_added = true;
		}

		return $css;
	}
}

new OURating();