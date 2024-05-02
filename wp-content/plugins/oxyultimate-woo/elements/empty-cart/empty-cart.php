<?php

class OUWooEmptyCart extends UltimateWooEl {

	public $css_added = false;
	public $js_added = false;

	function name() {
		return __( "Empty Cart Button", 'oxyultimate-woo' );
	}

	function slug() {
		return "ouwoo_emptycart";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function tag() {
		return 'a';
	}

	function init() {
		$this->El->useAJAXControls();

		add_action("oxygen_vsb_component_attr", [ $this, 'component_attrs' ], 99 );
	}

	function component_attrs( $options ) {
		if( strstr( $options['classes'], 'oxy-ouwoo-emptycart' ) ) {
			$slug = 'oxy_ouwoo_emptycart_';
			$attrs = array();
			$attrs[] = "role=button";

			if(! empty( $options[ $slug . 'rel'] ) ) { 
				$attrs[] = 'rel="' . $options[ $slug . 'rel'] . '"'; 
			}

			$aria_label = ! empty( $options[ $slug . 'aria_label'] ) ? $options[ $slug . 'aria_label'] : esc_html__('Empty Cart', "oxyultimate-woo");
			$attrs[] = 'aria-label="' . $aria_label . '"';

			$url_args['ou_empty_cart'] = 'yes';

			$link = !empty( $options[$slug . 'redirect_url'] ) ? $options[$slug . 'redirect_url'] : false;
			if( $link )
			{
				$url_args['ou_redirect'] = 'yes';
				$url = esc_url( add_query_arg( $url_args, $link ) );
			} else {
				$url = esc_url( add_query_arg( $url_args, get_permalink( get_the_ID() ) ) );
			}

			$attrs[] = 'href="'. $url . '"';

			echo implode( ' ', $attrs );
		}
	}

	function controls() {
		$this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'button_text',
			'name' 		=> esc_html__('Button Text', "oxyultimate-woo"),
			'default' 	=> esc_html__('Empty Cart', "oxyultimate-woo"),
		])->setParam('description', __('Click on Apply Params button to see the changes.', "oxyultimate-woo"));

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'aria_label',
			'name' 		=> esc_html__('Aria Label', "oxyultimate-woo"),
			'default' 	=> esc_html__('Empty Cart', "oxyultimate-woo"),
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'slug' 		=> 'rel',
			'name' 		=> esc_html__('Rel Attribute', "oxyultimate-woo"),
		])->setParam('placeholder', 'noopener noreferrer nofollow');

		$redirect_url = $this->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouwoo_emptycart_redirect_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouwoo_emptycart_redirect_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ouwoo_emptycart_redirect_url\');iframeScope.checkResizeBoxOptions(\'oxy-ouwoo_emptycart_redirect_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="https://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ouwoo_emptycart_redirect_url\')">set</div>
			</div>
			',
			"redirect_url"
		);
		$redirect_url->setParam( 'heading', __('Redirect to') );

		$selector = ' '; $selectorHover = ':hover';

		$icon = $this->addControlSection('btn_icon', __('Icon'), "assets/icon.png", $this );

		$icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxyultimate-woo"),
				"slug" 			=> 'btn_icon',
			)
		)->rebuildElementOnChange();

		$icon->addStyleControl(
			array(
				"name" 			=> __('Size', "oxyultimate-woo"),
				"slug" 			=> "icon_size",
				"selector" 		=> 'svg',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)
		->setRange(20, 50, 2)
		->setUnits("px", "px");

		$icon->addStyleControl(
			array(
				"control_type" 	=> 'radio',
				"name" 			=> esc_html__('Position', "oxyultimate-woo"),
				"slug" 			=> 'btn_icon_pos',
				'value' 		=> ['row-reverse' => __('Left'), 'row' => __('Right')],
				'default' 		=> 'row',
				"selector" 		=> ' ',
				"property" 		=> 'flex-direction'
			)
		);

		$icon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"selector" 		=> ' ',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'gap',
				'value' 		=> '8'
			)
		)
		->setRange(0, 100, 1)
		->setUnits("px", "px,%,em");

		$icon->addStyleControls([
			[
				'name' 		=> __('Color', "oxyultimate-woo"),
				'selector' 	=> 'svg.emptycart-btn-icon',
				'property' 	=> 'color',
			],
			[
				'name' 		=> __('Hover Color', "oxyultimate-woo"),
				'selector' 	=> ':hover svg.emptycart-btn-icon',
				'property' 	=> 'color',
			]
		]);

		$style = $this->addControlSection('btn_style', __('Button Style'), "assets/icon.png", $this );

		$style->addStyleControl(
			array(
				'selector' 		=> $selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
			)
		)->setRange(0, 1000, 10)->setUnits('px', 'px,em,%,vw');

		$style->addPreset(
			"padding",
			"bn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$style->addPreset(
			"margin",
			"bn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Color
		$style->addStyleControls(
			array(
				array(
					'selector' 	=> $selector,
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Background Hover Color', "oxyultimate-woo"),
					'selector' 	=> ':hover',
					'property' 	=> 'background-color',
				),
				array(
					'name' 		=> __('Hover Text Color', "oxyultimate-woo"),
					'selector' 	=> ':hover',
					'property' 	=> 'color',
				)
			)
		);

		//* Typography
		$style->typographySection( __('Fonts', "oxyultimate-woo"), $selector, $this );

		//* Border
		$style->borderSection( __('Borders', "oxyultimate-woo"), $selector, $this );
		$style->borderSection( __('Hover Borders', "oxyultimate-woo"), $selectorHover, $this );
		
		//* Box Shadow
		$style->boxShadowSection( __('Box Shadow', "oxyultimate-woo"), $selector, $this );
		$style->boxShadowSection( __('Hover Box Shadow', "oxyultimate-woo"), $selectorHover, $this );
	}

	function render( $options, $default, $content ) {

		$button_text 	= !empty( $options['button_text'] ) ? $options['button_text'] : __('Empty Cart', 'oxyultimate-woo');

		echo "<span class='button-text'>{$button_text}</span>";

		$icon = isset($options['btn_icon']) ? esc_html( $options['btn_icon'] ) : false;
		if( $icon ) {
			global $oxygen_svg_icons_to_load;
			$oxygen_svg_icons_to_load[] = $icon;

			echo '<svg class="emptycart-btn-icon"><use xlink:href="#' . $icon . '"></use></svg>';
		}

		if( ! $this->isBuilderEditorActive() && ! $this->js_added ) {
			$this->js_added = true;
			add_action('wp_footer', array( $this, 'emptycart_js_scripts'), 99 );
		}
	}

	function emptycart_js_scripts() { 
	?>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', () => {
			 	const $elements = document.querySelectorAll('.oxy-ouwoo-emptycart');
			 	if($elements.length > 0) {
					$elements.forEach(( el ) => {
						_visibility = function() {
							if( Cookies.get( 'woocommerce_items_in_cart' ) > 0 ) {
								el.classList.remove('hide-when-empty')
							} else {
								el.classList.add('hide-when-empty')
							}
						};

						_visibility();

						jQuery( document.body ).on( 'added_to_cart updated_wc_div update_checkout removed_from_cart', _visibility );
					});
				}
			});
		</script>
	<?php
	}
	
	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$this->css_added = true;

			return '.oxy-ouwoo-emptycart {
				border: 1px solid #999;
				display: flex; 
				padding: 8px 16px;
				align-items: center;
				justify-content: center;
				width: 100%;
			}
			.oxy-ouwoo-emptycart svg{
				width: 20px;
				height: 20px;
				fill: currentColor;
			}
			.oxy-ouwoo-emptycart.hide-when-empty {
				display: none!important;
			}';
		}
	}
}

new OUWooEmptyCart();