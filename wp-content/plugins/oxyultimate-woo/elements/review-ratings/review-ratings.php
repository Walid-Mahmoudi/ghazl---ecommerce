<?php

class OUWooRating extends UltimateWooEl {

	public $rating_css = false;

	function name() {
		return __( "Ratings", "oxyultimate-woo" );
    }
    
    function slug() {
		return "ou_review_rating";
	}

	function ouwoo_button_place() {
		return "reviews";
	}


	/*******************************
	 * Enable nestable components
	 *******************************/
	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}


	/*******************************
	 * Average Rating Controls
	 *******************************/
	function average_rating() {
		$points = $this->addControlSection( 'rating_points', __('Average Rating', "oxyultimate-woo"), 'assets/icon.png', $this );

		$condition = $points->addControl(
			'buttons-list',
			'show_rating_points',
			__('Display', "oxyultimate-woo")
		);
		$condition->setValue(['yes' => __('Yes'), 'no' => __('No')]);
		$condition->setValueCSS([
			'yes' 	=> '.rating-points{display: block}',
			'no' 	=> '.rating-points{display: none}'
		]);
		$condition->setDefaultValue('yes');

		$points->addOptionControl([
			'type' 		=> 'textarea',
			'name' 		=> __('Text'),
			'slug' 		=> 'average_rating_text',
			'default' 	=> "{average_rating} out of 5"
		]);

		$points->addStyleControl([
			'selector' 	=> '.rating-points',
			'property' 	=> 'background-color'
		]);

		$points->typographySection(__('Text'), '.rating-points', $this );
		
		$points->typographySection(__('Rating'), '.average-rating', $this );

		$spacing = $points->addControlSection('points_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"points_padding",
			__("Padding"),
			'.rating-points'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"points_margin",
			__("Margin"),
			'.rating-points'
		)->whiteList();

		$points->borderSection(__('Border'), '.rating-points', $this );
	}

	
	/*******************************
	 * Stars Controls
	 *******************************/
	function stars() {
		$stars = $this->addControlSection( 'stars', __('Stars', "oxyultimate-woo"), 'assets/icon.png', $this );

		$condition = $stars->addControl(
			'buttons-list',
			'show_stars',
			__('Display', "oxyultimate-woo")
		);
		$condition->setValue(['yes' => __('Yes'), 'no' => __('No')]);
		$condition->setValueCSS([
			'yes' 	=> '.star-rating{display: block}',
			'no' 	=> '.star-rating{display: none}'
		]);
		$condition->setDefaultValue('yes');

		$stars->addStyleControls([
			[
				'name' 		=> __('Empty Stars Color', 'oxyultimate-woo'),
				'selector' 	=> '.star-rating::before',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Filled Stars Color', 'oxyultimate-woo'),
				'selector' 	=> '.star-rating',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Size', 'oxyultimate-woo'),
				'selector' 	=> '.star-rating',
				'property' 	=> 'font-size',
				'unit' 		=> 'em',
				'default'	=> '1'
			]
		]);

		$spacing = $stars->addControlSection('stars_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"margin",
			"stars_margin",
			__("Margin"),
			'.star-rating'
		)->whiteList();
	}

	
	/*******************************
	 * Total Reviews Controls
	 *******************************/
	function total_reviews() {
		$total = $this->addControlSection( 'total_reviews', __('Total Reviews', "oxyultimate-woo"), 'assets/icon.png', $this );

		$condition = $total->addControl(
			'buttons-list',
			'show_total_reviews',
			__('Display', "oxyultimate-woo")
		);
		$condition->setValue(['yes' => __('Yes'), 'no' => __('No')]);
		$condition->setValueCSS([
			'yes' 	=> '.total-reviews{display: block}',
			'no' 	=> '.total-reviews{display: none}'
		]);
		$condition->setDefaultValue('yes');

		$total->addOptionControl([
			'type' 		=> 'textarea',
			'name' 		=> __('Text'),
			'slug' 		=> 'total_reviews_text',
			'default' 	=> "{total_reviews} reviews"
		]);
		
		$total->typographySection(__('Text'), '.total-reviews', $this );
		
		$total->typographySection(__('Counts', "oxyultimate-woo"), '.reviews-counts', $this );

		$spacing = $total->addControlSection('totals_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"margin",
			"total_margin",
			__("Margin"),
			'.total-reviews'
		)->whiteList();
	}

	
	/*******************************
	 * Flex Layout Controls
	 *******************************/
	function flexLayout() {
		$layout_child = $this->addControlSection('child_layout', __('Layout'), 'assets/icon.png', $this);
		$layout_child->flex(' ', $this);
		$layout_child->addControl(
			'buttons-list',
			'flex_direction',
			__('Reverse Flex Direction', 'oxyultimate-woo')
		)->setValue([ 'default', 'row-reverse', 'column-reverse'])->setValueCSS([
			'row-reverse' => '{flex-direction: row-reverse;}',
			'column-reverse' => '{flex-direction: column-reverse;}'
		]);
	}

	
	/*******************************
	 * Hover Popup Controls
	 *******************************/
	function popupbox() {
		$popup = $this->addControlSection('popup_section', __('Popup'), 'assets/icon.png', $this);

		$selector = '.popup-rating-graph';

		$builderPeview = $popup->addControl(
			'buttons-list',
			'popup_builder_preview',
			__('In Builder Mode', "oxyultimate-woo")
		);
		$builderPeview->setValue(['Editing', 'Live Preview']);
		$builderPeview->setValueCSS([
			'Editing' => $selector . '.popup-builder-edit{opacity: 1; visibility: visible; top: 30px;pointer-events: auto; min-height: 100px;}'
		]);
		$builderPeview->setDefaultValue('Live Preview');

		$style = $popup->addControlSection('popup_clr', __('Color & Size', "oxyultimate-woo"), "assets/icon.png", $this);
		$style->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector'		=> $selector,
			'property' 		=> 'width'
		])->setRange(0,800,1)->setUnits('px', 'px,%,em')->setDefaultValue(320);

		$style->addStyleControl([
			'selector'		=> $selector,
			'property' 		=> 'background-color'
		]);

		$arrow = $popup->addControlSection('popup_arrow', __('Arrow', "oxyultimate-woo"), "assets/icon.png", $this);
		$arrow->addControl(
			'buttons-list',
			'hide_arrow',
			__('Hide Arrow', 'oxyultimate-woo')
		)->setValue([ 'No', 'Yes'])->setValueCSS([
			'Yes' => '.popup-rating-graph:before{display: none;}'
		])->setDefaultValue('No');

		$arrow->addStyleControl([
			'selector'		=> $selector,
			'property' 		=> '--arrow-color',
			'control_type'	=> 'colorpicker'
		]);

		$arrow->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Position'),
			'selector'		=> $selector . ":before",
			'property' 		=> 'left'
		])->setRange(0,100,1)->setUnits('px', 'px,%')->setDefaultValue(33);

		$spacing = $popup->addControlSection('popup_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"popupbox_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$pos = $popup->addControlSection('popup_pos', __('Position'), "assets/icon.png", $this);
		$pos->addStyleControl([
			'control_type' 	=> 'measurebox',
			'selector'		=> $selector,
			'property' 		=> 'left',
			'unit' 			=> 'px',
			'default' 		=> 0
		])->setParam('hide_wrapper_end', true);

		$pos->addStyleControl([
			'control_type' 	=> 'measurebox',
			'selector'		=> $selector,
			'property' 		=> 'right'
		])->setParam('hide_wrapper_start', true);

		$pos->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Top - Initial State'),
			'selector'		=> $selector,
			'property' 		=> 'top'
		])->setRange(0,100,1)->setUnits('px', 'px')->setDefaultValue(10);

		$pos->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Top - Hover State'),
			'selector'		=> ' ',
			'property' 		=> '--popup-hover-pos'
		])->setRange(0,100,1)->setUnits('px', 'px')->setDefaultValue(30);

		$popup->borderSection( __('Border'), $selector, $this );
		$popup->boxShadowSection( __('Box Shadow'), $selector, $this );
	}

	
	/*******************************
	 * Custom Init
	 *******************************/
	function custom_init() {
		add_filter("oxy_allowed_empty_options_list", array( $this, "ouwoo_allowed_empty_options_list" ) );
	}

	function ouwoo_allowed_empty_options_list( $empty_options ) {
		$new_empty_option = array(
			"oxy-ou_review_rating_total_reviews_text",
			'oxy-ou_review_rating_average_rating_text',
			'oxy-ou_review_rating_hide_ou_reviews'
		);

		$empty_options = array_merge($empty_options, $new_empty_option);

		return $empty_options;
	}

	
	/*******************************
	 * Register Component Controls
	 *******************************/
	function controls() {

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Product ID'),
			'slug' 		=> 'product_id'
		])->setParam('description', __('Left blank if you are using in single product or loop.', "oxyultimate-woo"));

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Hide if Number of Reviews is less than'),
			'slug' 		=> 'hide_ou_reviews',
			'default' 	=> 0
		])->setParam('description', __('It is accepting the negative value also.', "oxyultimate-woo"));

		$this->average_rating();

		$this->stars();

		$this->total_reviews();

		$this->flexLayout();

		$this->popupbox();
	}

	
	/*******************************
	 * Render components
	 *******************************/
	function render( $options, $defaults, $content ) {
		global $post, $product;
		if( isset( $options['product_id'] ) ) {
			$product = wc_get_product( $options['product_id'] );
		} else {
			$product = wc_get_product();
		}

		if( ! is_a( $product, 'WC_Product') )
			return;

		if ( ! wc_review_ratings_enabled() ) {
			return;
		}

		$average_rating_text = wp_kses_post( $options['average_rating_text'] );
		$total_reviews_text = wp_kses_post( $options['total_reviews_text'] );
		$total_reviews = $product->get_review_count();
		$average_rating = wc_format_decimal( $product->get_average_rating(), 2 );
		$show_rating_points = isset($options['show_rating_points']) ? $options['show_rating_points'] : 'yes';
		$show_stars = isset($options['show_stars']) ? $options['show_stars'] : 'yes';
		$show_total_reviews = isset($options['show_total_reviews']) ? $options['show_total_reviews'] : 'yes';

		if( ! empty( $options['hide_ou_reviews'] ) || $options['hide_ou_reviews'] == '0' ) {
			$hide_ou_reviews = $options['hide_ou_reviews'];
		} else {
			$hide_ou_reviews = -1;
		}

		if( $this->isBuilderEditorActive() ) :?>

			<?php if( isset( $average_rating_text ) && $show_rating_points == 'yes' ) : ?>
				<div class="rating-points">
					<?php
						echo str_replace( "{average_rating}", '<span class="average-rating">'. $average_rating .'</span>', $average_rating_text );
					?>
				</div>
			<?php endif; ?>

			<?php if( $show_stars == "yes") : ?>
				<?php echo wc_get_rating_html( $average_rating + 0.01 ); ?>
			<?php endif; ?>

			<?php if( isset( $total_reviews_text ) && $show_total_reviews == "yes" ) : ?>
				<div class="total-reviews">
					<?php
						echo str_replace( "{total_reviews}", '<span class="reviews-counts">'. $total_reviews .'</span>', $total_reviews_text );
					?>
				</div>
			<?php endif; ?>

		<?php else:	?>

			<?php if( isset( $average_rating_text ) && $total_reviews > $hide_ou_reviews && $show_rating_points == 'yes' ) : ?>
				<div class="rating-points">
					<?php
						echo str_replace( "{average_rating}", '<span class="average-rating">'. $average_rating .'</span>', $average_rating_text );
					?>
				</div>
			<?php endif; ?>
			<?php if( $total_reviews > $hide_ou_reviews && $show_stars == "yes") : ?>
				<?php echo wc_get_rating_html( $average_rating + 0.01 ); ?>
			<?php endif; ?>

			<?php if( isset( $total_reviews_text ) && $total_reviews > $hide_ou_reviews && $show_total_reviews == "yes" ) : ?>
				<div class="total-reviews">
					<?php
						echo str_replace( "{total_reviews}", '<span class="reviews-counts">'. $total_reviews .'</span>', $total_reviews_text );
					?>
				</div>
			<?php endif; ?>
			
		<?php endif;

		$class = ( $this->isBuilderEditorActive() ) ? ' popup-builder-edit' : '';

		if( $content ) {
			echo '<div class="popup-rating-graph oxy-inner-content'.$class.'">';
			
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );

			echo '</div>';
		}
	}

	
	/*******************************
	 * Component CSS
	 *******************************/
	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->rating_css) {
			$css = '.oxy-ou-review-rating{
				display: flex;
				flex-direction: row;
				line-height: 1;
				align-items: center;
				justify-content: center;
				position: relative;
				--popup-hover-pos: 30px;
				--arrow-color: #eee;
			}
			.rating-points, 
			.star-rating { margin-right: 8px; }
			.popup-rating-graph {
				background: #fff;
				border: 2px solid #ebebeb;
				border-radius: 5px;
				-webkit-box-shadow: 2px 5px 6px rgba(0,0,0,.15);
				box-shadow: 2px 5px 6px rgba(0,0,0,.15);
				padding: 20px 15px;
				position: absolute;
				opacity: 0;
				visibility: hidden;
				pointer-events: none;
				top: 10px;
				left: 0;
				z-index: 1;
				width: 320px;
				-webkit-transition: all ease .3s;
				transition: all ease .3s;
			}
			.oxy-ou-review-rating:hover .popup-rating-graph {
				visibility: visible;
				opacity: 1;
				top: var(--popup-hover-pos)!important;
				pointer-events: auto;
			}
			.oxy-ou-review-rating .popup-rating-graph:before {
				content: "";
				border-style: solid;
				border-width: 0 5.5px 6px 5.5px;
				border-color: transparent transparent var(--arrow-color) transparent;
				position: absolute;
				margin-top: -8px;
				left: 33px;
				top: 0;
			}
			';

			$this->rating_css = true;
		}
        return $css;
    }
}

new OUWooRating();