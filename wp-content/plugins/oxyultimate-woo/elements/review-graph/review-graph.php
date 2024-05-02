<?php

class OUWooReviewGraph extends UltimateWooEl {

	public $graph_css = false;

	function name() {
		return __( "Graph", "oxyultimate-woo" );
    }
    
    function slug() {
		return "ou_review_graph";
	}

	function ouwoo_button_place() {
		return "reviews";
    }

	function values() {
		$values = $this->addControlSection('values_section', __('Values', "oxyultimate-woo"), "assets/icon.png", $this );

		$type = $values->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Type', "oxyultimate-woo"),
			'slug' 		=> 'values_type'
		]);
		$type->setValue(
			[
				'star_txt' 	=> __('Star Text', "oxyultimate-woo"),
				'num_icon' 	=> __('Num + Star Icon', "oxyultimate-woo"),
				'icon' 		=> __('Star Icons', "oxyultimate-woo"),
				'text' 		=> __('Custom Text', "oxyultimate-woo")
			]
		);
		$type->setDefaultValue('star_txt');
		$type->rebuildElementOnChange();

		$values->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> '',
			'slug' 			=> 'text_5',
			'default' 		=> __('Excellent', "oxyultimate-woo"),
			'condition' 	=> 'values_type=text'
		]);

		$values->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> '',
			'slug' 			=> 'text_4',
			'default' 		=> __('Good', "oxyultimate-woo"),
			'condition' 	=> 'values_type=text'
		]);

		$values->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> '',
			'slug' 			=> 'text_3',
			'default' 		=> __('Average', "oxyultimate-woo"),
			'condition' 	=> 'values_type=text'
		]);

		$values->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> '',
			'slug' 			=> 'text_2',
			'default' 		=> __('Not Bad', "oxyultimate-woo"),
			'condition' 	=> 'values_type=text'
		]);

		$values->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> '',
			'slug' 			=> 'text_1',
			'default' 		=> __('Very Poor', "woocommerce"),
			'condition' 	=> 'values_type=text'
		]);

		$selector = '.ouwoo-stars-value';

		$values->addStyleControl([
			'name' 			=> __('Gap Between Text & Icon', "oxyultimate-woo"),
			'selector' 		=> $selector . ' svg',
			'property' 		=> 'margin-left',
			'control_type' 	=> 'slider-measurebox',
			'condition' 	=> 'values_type=icon'
		])->setDefaultValue(4);

		$values->addStyleControl([
			'name' 			=> __('Icon Size', "oxyultimate-woo"),
			'selector' 		=> $selector . ' svg',
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox',
			'condition' 	=> 'values_type=icon'
		])->setRange(0, 100, 1)->setUnits('px', 'px');

		$values->addStyleControl([
			'name' 			=> __('Icon Color', "oxyultimate-woo"),
			'selector' 		=> $selector . ' svg',
			'property' 		=> 'color'
		]);

		$values->addStyleControl([
			'name' 			=> __('Width'),
			'selector' 		=> $selector,
			'property' 		=> 'min-width',
			'control_type' 	=> 'slider-measurebox'
		])->setRange(0, 400, 1)->setUnits('px', 'px')->setDefaultValue(80);

		$values->typographySection(__('Typography'), $selector, $this );

	}
	function progressbar() {
		$bar = $this->addControlSection('bar_section', __('Progress Bar', "oxyultimate-woo"), "assets/icon.png", $this );

		$type = $bar->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Type', "oxyultimate-woo"),
			'slug' 		=> 'bar_type'
		]);
		$type->setValue(
			[
				'bar' 	=> __('Bar', "oxyultimate-woo"),
				'star' 	=> __('Star', "oxyultimate-woo")
			]
		);
		$type->setDefaultValue('bar');
		$type->rebuildElementOnChange();

		$bar->addStyleControls([
			[
				'name' 		=> __('Primary Color', 'oxyultimate-woo'),
				'selector' 	=> '.rating-bar-wrap',
				'property' 	=> 'background-color',
				'condition' => 'bar_type=bar'
			],
			[
				'name' 		=> __('Secondary Color', 'oxyultimate-woo'),
				'selector' 	=> '.ouwoo-perc-rating',
				'property' 	=> 'background-color',
				'condition' => 'bar_type=bar'
			],
			[
				'control_type' => 'slider-measurebox',
				'name' 		=> __('Row Height', 'oxyultimate-woo'),
				'selector' 	=> '.ouwoo-review-row > span, .ouwoo-review-row .rating-bar-wrap, .ouwoo-review-row .ouwoo-perc-rating',
				'property' 	=> 'height|line-height',
				'slug' 		=> 'bar_height',
				'default' 	=> '21',
				'unit' 		=> 'px',
				'condition' => 'bar_type=bar'
			]
		]);

		$bar->addStyleControls([
			[
				'name' 		=> __('Empty Stars Color', 'oxyultimate-woo'),
				'selector' 	=> '.star-rating::before',
				'property' 	=> 'color',
				'condition' => 'bar_type=star'
			],
			[
				'name' 		=> __('Filled Stars Color', 'oxyultimate-woo'),
				'selector' 	=> '.star-rating span',
				'property' 	=> 'color',
				'condition' => 'bar_type=star'
			],
			[
				'name' 		=> __('Size', 'oxyultimate-woo'),
				'selector' 	=> '.star-rating',
				'property' 	=> 'font-size',
				'unit' 		=> 'em',
				'default'	=> '1',
				'condition' => 'bar_type=star'
			]
		]);
	}

	function reviews_num() {
		$num = $this->addControlSection('number_section', __('Number', "oxyultimate-woo"), "assets/icon.png", $this );

		$disable = $num->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable', "oxyultimate-woo"),
			'slug' 		=> 'disable_num'
		]);
		$disable->setValue(['no' => __('No'), 'yes' => __('Yes')]);
		$disable->setDefaultValue('no');
		$disable->rebuildElementOnChange();

		$type = $num->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Type', "oxyultimate-woo"),
			'slug' 		=> 'num_type'
		]);
		$type->setValue(
			[
				'num' 	=> __('Number', "oxyultimate-woo"),
				'perc' 	=> __('Percentage', "oxyultimate-woo")
			]
		);
		$type->setDefaultValue('num');
		$type->rebuildElementOnChange();

		$selector = '.ouwoo-num-reviews';

		$num->addStyleControl([
			'name' 			=> __('Width'),
			'selector' 		=> $selector,
			'property' 		=> 'min-width',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> 'px',
			'default' 		=> 50
		]);

		$num->typographySection(__('Typography'), $selector, $this );

		$spacing = $num->addControlSection('num_sp', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"num_padding",
			__("Padding"),
			$selector
		)->whiteList();
	}

    function controls() {

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Product ID'),
			'slug' 		=> 'product_id'
		])->setParam('description', __('Left blank if you are using in single product or loop.', "oxyultimate-woo"));

		$this->values();

		$this->progressbar();

		$this->reviews_num();
    }

    function render( $options, $defaults, $content ) {
		global $post, $product, $wpdb;

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

		$total_reviews = $product->get_review_count();

		for($i = 5; $i >= 1; $i--) {

			$sql = "SELECT comment_post_ID, COUNT( {$wpdb->prefix}comments.comment_ID ) as num 
					FROM {$wpdb->prefix}comments 
					INNER JOIN {$wpdb->prefix}commentmeta 
						ON ( {$wpdb->prefix}comments.comment_ID = {$wpdb->prefix}commentmeta.comment_id ) 
					WHERE ( comment_approved = '1' ) 
						AND comment_post_ID = {$product->get_id()} 
						AND comment_type IN ('review') 
						AND ( ( 
							{$wpdb->prefix}commentmeta.meta_key = 'rating' 
							AND CAST({$wpdb->prefix}commentmeta.meta_value AS SIGNED) = '". absint( $i ). "' 
						) ) 
						AND comment_type != 'order_note' 
						AND  comment_type != 'webhook_delivery' 
					GROUP BY comment_post_ID 
					ORDER BY num DESC";

			$ratings = $wpdb->get_row( $sql );
			$rating_num = empty($ratings) ? 0 : $ratings->num;
			$perc = ($total_reviews == '0') ? 0 : floor( $rating_num / $total_reviews * 100 );

			$values_type = isset($options['values_type']) ? $options['values_type'] : 'star_txt';
			$disable_num = isset($options['disable_num']) ? $options['disable_num'] : 'no';
			$num_type = isset($options['num_type']) ? $options['num_type'] : 'num';
			$bar_type = isset($options['bar_type']) ? $options['bar_type'] : 'bar';	
        ?>
            <div class="ouwoo-review-row display-<?php echo $bar_type; ?>">
                
				<span class="ouwoo-stars-value vtype-<?php echo $values_type; ?>">
					<?php if( $values_type == 'star_txt') { printf(_n('%s star', '%s stars', $i, 'oxyultimate-woo'), $i); } ?>
					<?php 
						if( $values_type == 'num_icon') { 
							global $oxygen_svg_icons_to_load; 
							$oxygen_svg_icons_to_load[] = 'FontAwesomeicon-star';

							echo $i . ' <svg id="' . $options['selector'] . '-star-icon" class="star-icon"><use xlink:href="#FontAwesomeicon-star"></use></svg>';
						}
					?>
					<?php if( $values_type == 'icon') { echo wc_get_rating_html( $i ); } ?>
					<?php if( $values_type == 'text') { echo wp_kses_post( $options['text_' . $i] ); } ?>
				</span>

				<?php if( $disable_num == "no"): ?>
                	<span class="ouwoo-num-reviews"><?php if( $num_type == "num" ) { echo $rating_num; } ?><?php if( $num_type == "perc" ) { printf('%s %%', $perc); } ?></span>
				<?php endif; ?>

				<span class="ouwoo-rating-bar">
					<?php if( $bar_type == 'bar' ): ?>
						<span class="rating-bar-wrap">
							<span class="ouwoo-perc-rating" style="width: <?php echo $perc; ?>%;"></span>
						</span>
					<?php else: ?>
						<?php echo wc_get_rating_html( $i ); ?>
					<?php endif; ?>
				</span>

            </div>
        <?php
		}
    }

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->graph_css ) {
			$css = '.oxy-ou-review-graph {
				width: 100%;
				min-height: 40px;
			}
			.ouwoo-review-row {
				padding-bottom: 10px;
				position: relative;
			}
			.ouwoo-review-row span {
				color: #24890d;
				display: block;
			}
			.display-bar > span,
			.ouwoo-review-row .rating-bar-wrap,
			.ouwoo-review-row .ouwoo-perc-rating {
				height: 21px;
				line-height: 21px;
			}
			.oxy-ou-review-graph .ouwoo-review-row:not(.display-bar) span.ouwoo-rating-bar {
				height: auto!important;
				line-height: 1;
			}
			.ouwoo-stars-value {
				float: left;
				min-width: 80px;
			}
			.ouwoo-review-row .ouwoo-num-reviews {
				float: right;
				min-width: 50px;
				padding-left: 10px;
			}
			.ouwoo-review-row .ouwoo-rating-bar {
				float: none;
				overflow: hidden;
			}
			.ouwoo-review-row .rating-bar-wrap {
				background-color:#f4f4f4;
				clear: both;
				position: relative;
			}
			.ouwoo-review-row .ouwoo-perc-rating {
				background-color: #8fb3fb;
				color: #000000;
				float: left;
			}
			.ouwoo-review-row .ouwoo-stars-value.vtype-num_icon {
				display: flex;
				align-items: center;
			}
			.ouwoo-review-row svg {
				margin-left: 4px;
				width: 20px;
				height: 20px;
				fill: currentColor;
			}
			';

			$this->graph_css = true;
		}

		return $css;
	}
}

new OUWooReviewGraph();