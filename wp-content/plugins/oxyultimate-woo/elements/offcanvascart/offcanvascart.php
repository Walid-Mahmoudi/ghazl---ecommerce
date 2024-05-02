<?php

class OUWooOffCanvasCart extends UltimateWooEl {
	
	public $has_js = true;
	public $css_added = false;
	public $msg = '';

	function name() {
		return __( "Off Canvas Cart", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_offcanvascart";
	}

	function ouwoo_button_place() {
		return "main";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'ouwoo_woo_cart_fragment' ) );
		
		add_action( 'woocommerce_before_mini_cart_contents', 'ouwoo_common_filter_mini_cart_contents' );
		add_action( 'woocommerce_before_mini_cart_contents', 'ouwoo_filter_mini_cart_contents' );

		add_action('woocommerce_widget_shopping_cart_buttons', array($this, 'return_to_shop_button'), 99 );

		add_filter("oxygen_vsb_element_presets_defaults", array( $this, "ouwoo_offcanvascart_presets_defaults" ) );
	}

	function ouwoo_offcanvascart_presets_defaults( $all_elements_defaults ) {
		require("offcanvascart-presets.php");

		$all_elements_defaults = array_merge($all_elements_defaults, $offcanvascart_presets);

		return $all_elements_defaults;
	}

	function render( $options, $defaults, $content ) {
		global $oxygen_svg_icons_to_load;

		$total = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;

		if( isset($options['ouocc_btnvisibility']) && $options['ouocc_btnvisibility'] !== 'always' 
			&& ! $this->isBuilderEditorActive() ) {
			$dataAttr = ' data-ouocc-appearance="' . $options['ouocc_btnvisibility'] . '"';
		} else {
			$dataAttr = '';
		}

		$trigger = isset( $options['ouocc_trigger'] ) ? $options['ouocc_trigger'] : 'hover';
		$dataAttr .= ' data-ouocc-trigger="' . $trigger . '"';

		if( isset( $options['ouocc_ajax_single'] ) && $options['ouocc_ajax_single'] == 'yes' && ! $this->isBuilderEditorActive() ) {
			$dataAttr .= ' data-ajaxonsinglebtn="yes"';
		}

		if( ! is_cart() ) {
			$dataAttr .= ' data-coupon-nonce="' . wp_create_nonce('apply-coupon') . '" data-remove-coupon-nonce="' . wp_create_nonce( 'remove-coupon' ) .'"'; 
		}

		$dataAttr .= ' data-checkoutpage="' . ( is_checkout() ? 'yes' : 'no' ) . '"'; 

		if( isset( $options['ouocc_flytocart'] ) && $options['ouocc_flytocart'] == 'yes' ) {
			$dataAttr .= ' data-flytocart="yes" data-offsettop="' . $options['ftc_offset_top'] . '" data-offsetleft="'. $options['ftc_offset_left'] .'"';
		}

		//$dataAttr .= ' data-maxqtymsg="' . ( isset($options['max_qty_msg']) ? esc_html( $options['max_qty_msg'] ) : __('No more products on stock')) . '"';
		//$dataAttr .= ' data-minqtymsg="' . ( isset($options['min_qty_msg']) ? esc_html( $options['min_qty_msg'] ) : __('You entered wrong value.')) . '"';

		$dataAttr .= ' data-shopbtntxt="' . ( isset($options['shop_button_text']) ? wp_kses_post( $options['shop_button_text'] ) : esc_html__('Continue Shopping', 'woocommerce') ) .'"';
		$returnshop = isset($options['shop_button_url_source']) ? esc_html( $options['shop_button_url_source'] ) : 'shop';
		if( $returnshop == 'custom' && isset( $options['custom_url'] ) ) {
			$shop_button_url = $options['custom_url'];
			$dataAttr .= ' data-shopbtnurl="' . esc_url( $shop_button_url ) .'"';
		} elseif( $returnshop != 'close' ) {
			$shop_button_url = ( wc_get_page_id( 'shop' ) > 0 ) ? wc_get_page_permalink( 'shop' ) : '#';
			$dataAttr .= ' data-shopbtnurl="' . esc_url( $shop_button_url ) .'"';
		} else {
			$dataAttr .= ' data-shopbtnurl="' . $returnshop .'"';
		}

		echo '<div class="ou-cart-button ouocc-type-' . $options['ouocc_tnumpos'] .'"'. $dataAttr .'>';

		if ( $options['ouocc_tnumpos'] == 'before' ) {
			printf('<span class="cart-items-num cart-items-count-before"><span class="cart-counter">%d</span></span>', absint( $total ) );
		}

		$aria_label = isset( $options['ouocc_aria_label'] ) ? $options['ouocc_aria_label'] : __( 'View your shopping cart', "oxyultimate-woo" );
		echo '<a class="ouocc-cart-btn oumc-cart-btn ouocc-type-' . $options['ouocc_btnt'] .'" href="JavaScript: void(0);" title="'. esc_html( $aria_label ) .'" aria-label="'. esc_html( $aria_label ) .'">';

			if( isset( $options['ouocc_cprice'] ) && $options['ouocc_cprice'] == 'yes' && isset( $options['ouocc_ppos'] ) && $options['ouocc_ppos'] == 'left' )
			{

				printf('<span class="price-align-left top-price"><span class="cart-price">%d</span></span>', wc_price( $this->ou_cart_total() ) );
			}

			if( $options['ouocc_btnt'] == 'icon' ) {
				
				if( $options['ouocc_ict'] == 'icon' ) {
					$oxygen_svg_icons_to_load[] = $options['ouocc_btnicon'];

					echo '<svg id="' . $options['selector'] . '-cart-icon" class="oumcart-icon"><use xlink:href="#' . $options['ouocc_btnicon'] . '"></use></svg>';
				}

				if( $options['ouocc_ict'] == 'image' && isset($options['ouocc_btnimg']) ) {

					$alt = isset($options['ouocc_btnimgalt']) ? $options['ouocc_btnimgalt'] : '';

					$width = (isset($options['ouocc_btnimgw'])) ? ' width="' . $options['ouocc_btnimgw'] . '"' : '';
					$height = (isset($options['ouocc_btnimgh'])) ? ' height="' . $options['ouocc_btnimgh'] .'"' : '';

					echo '<img src="' . $options['ouocc_btnimg'] .'"'. $width . $height .' class="oumcart-btn-image" alt="'. wp_kses_post( $alt ) . '" />';
				}

			} elseif( $options['ouocc_btnt'] == 'bothit') {
				if( $options['ouocc_ict'] == 'icon' ) {
					$oxygen_svg_icons_to_load[] = $options['ouocc_btnicon'];

					echo '<svg id="' . $options['selector'] . '-cart-icon" class="oumcart-icon"><use xlink:href="#' . $options['ouocc_btnicon'] . '"></use></svg>';
				}

				if( $options['ouocc_ict'] == 'image' && isset($options['ouocc_btnimg']) ) {
					$alt = isset($options['ouocc_btnimgalt']) ? $options['ouocc_btnimgalt'] : '';

					$width = (isset($options['ouocc_btnimgw'])) ? ' width="' . $options['ouocc_btnimgw'] . '"' : '';
					$height = (isset($options['ouocc_btnimgh'])) ? ' height="' . $options['ouocc_btnimgh'] .'"' : '';

					echo '<img src="' . $options['ouocc_btnimg'] .'"'. $width . $height .' class="oumcart-btn-image" alt="'. wp_kses_post( $alt ) . '" />';
				}

				if( isset( $options['ouocc_text'] ) ) {
					echo '<span class="cart-btn-text">' . $options['ouocc_text'] . '</span>';
				}

			} else {
				echo '<span class="cart-btn-text">' . $options['ouocc_text'] . '</span>';
			}

			if( isset( $options['ouocc_cprice'] ) && $options['ouocc_cprice'] == 'yes' && isset( $options['ouocc_ppos'] ) && $options['ouocc_ppos'] == 'right' )
			{
				printf('<span class="price-align-right top-price"><span class="cart-price price-align-right">%d</span></span>', wc_price( $this->ou_cart_total() ) );
			}

			if ( $options['ouocc_tnumpos'] == 'bubble' ) {
				printf('<span class="cart-items-num"><span class="cart-counter">%d</span></span>', absint( $total ) );
			}
		echo '</a>';

		if ( $options['ouocc_tnumpos'] == 'after' ) {
			printf('<span class="cart-items-num cart-items-count-after"><span class="cart-counter">%d</span></span>', absint( $total ) );
		}

		echo '</div>';


		$class = '';
		
		if( $this->isBuilderEditorActive() ) { $class = ' ouocc-builder-edit'; }

		echo '<div class="ouocc-overlay' . $class . '"></div>';
		echo '<div class="ouocc-panel-container align-' . strtolower($options['ocpanel_align']) . $class . '" data-reveal="'. $options['reveal_panel'] . '"';

		if( isset( $options['reveal_panel'] ) && $options['reveal_panel'] == "yes" ) {
			echo ' data-reveal-delayin="'. ( isset( $options['reveal_delayin'] ) ? absint( $options['reveal_delayin'] ) : 1500 ) . '"';
			echo ' data-reveal-delayout="' . (isset( $options['reveal_delayout'] ) ? absint( $options['reveal_delayout'] ) : 4500 ) . '"';
			echo ' data-reveal-autoclose="' . (isset( $options['auto_close'] ) ? $options['auto_close'] : 'yes' ) . '"';
		}
		
		echo '>';

		if( isset( $options['panel_title']) || isset($options['close_icon']) ) {
			echo '<div class="ouocc-panel-header">';

			printf('<div class="ouocc-panel-title">%s</div>', ( isset( $options['panel_title'] ) ? $options['panel_title'] : '') );

			if( isset($options['close_icon'])  ) {
				$oxygen_svg_icons_to_load[] = $options['close_icon'];

				echo '<div class="ouocc-close-panel"><svg id="' . $options['selector'] . '-cart-icon" class="close-icon"><use xlink:href="#' . $options['close_icon'] . '"></use></svg></div>';	
			}

			echo '</div>';
		}

		if( $content ) {
			
			if( function_exists('do_oxygen_elements') )
				$innercontent = do_oxygen_elements( $content );
			else
				$innercontent = do_shortcode( $content );

			printf('<div class="panel-header-after-content oxy-inner-content">%s</div>', $innercontent );
		}

		echo '	<div class="ouocc-cart-items">
					<div class="oucc-wc-notice">
						<span class="wc-notice-text message-add ouocc-hide-msg">' . $options['notice_add'] . '</span>
						<span class="wc-notice-text message-update ouocc-hide-msg">' . $options['notice_update'] . '</span>
						<span class="wc-notice-text message-remove ouocc-hide-msg">' . $options['notice_remove'] . '</span>
						<span class="wc-notice-text message-maxqty ouocc-hide-msg">' . $options['max_qty_msg'] . '</span>
						<span class="wc-notice-text message-minqty ouocc-hide-msg">' . $options['min_qty_msg'] . '</span>
						<span class="wc-notice-text message-error ouocc-hide-msg"></span>
					</div>
					<div class="widget_shopping_cart_content">'; 
						woocommerce_mini_cart();
		echo '		</div>
				</div>
			</div>';

		if ( $this->isBuilderEditorActive() ) :
	?>
		<script type="text/javascript">
			(function($) { 
				$(window).ready(function(){
					setTimeout(function(){
						$('.oxy-ou-offcanvascart').each( function(){
							headerHeight = headerAfterContentHeight = 0;

							if( $(this).find('.ouocc-panel-header').length > 0 )
								headerHeight = $(this).find('.ouocc-panel-header').outerHeight(true);

							if( $(this).find('.panel-header-after-content').length > 0 )
								headerAfterContentHeight = $(this).find('.panel-header-after-content').outerHeight(true);

							$selector = $(this).attr('id');

							var style = '<style id="ou-offcanvascart-style' + $selector + '">';
								style += '#' + $selector + ' .widget_shopping_cart_content {'
								style += 'top: ' + (headerAfterContentHeight + headerHeight) + 'px';
								style += '}';
								style += '</style>';

								if ( $('#ou-offcanvascart-style' + $selector).length > 0 ) {
									$('#ou-offcanvascart-style' + $selector).remove();	
								}

							$('body').append(style);

							if( $('#' + $selector + ' .ouocc-shop-button').length ) {
								$('#' + $selector + ' .ouocc-shop-button').attr('href', $('#' + $selector + ' .ou-cart-button').attr('data-shopbtnurl') );
								$('#' + $selector + ' .ouocc-shop-button').text( $('#' + $selector + ' .ou-cart-button').attr('data-shopbtntxt') );
							}
						});
					}, 100 );
				});
			})(jQuery);
		</script>
	<?php
		else:
			wp_enqueue_script('ou-occ-script');
		endif;

		if( isset($options['remove_icon']) ) {
			$this->remove_icon = $options['remove_icon'];

			global $oxygen_svg_icons_to_load;
			$oxygen_svg_icons_to_load[] = $this->remove_icon;
		}
	}

	function return_to_shop_button() {
	?>
		<a class="button ouocc-shop-button close-ouocc-panel wc-backward" role="button" aria-label="<?php _e('Continue Shopping', 'woocommerce');?>">
			<?php echo apply_filters( 'woocommerce_return_to_shop_text', esc_html__('Continue Shopping', 'woocommerce') ); ?>
		</a>
	<?php
	}

	function offCanvasPanelControl() {
		$panel = $this->addControlSection('ouocc_panel', __('Off Canvas Panel'), 'assets/icon.png', $this );

		$ovSection = $panel->addControlSection('ouocc_obgsection', __('Backdrop', "oxy-ultimate"), 'assets/icon.png', $this );
		$ovBGCondition = $ovSection->addControl( 'buttons-list', 'ocpanel_ovbg', __( 'Disable Backdrop', "oxyultimate-woo" ));
		$ovBGCondition->setValue( array( "No","Yes" ) );
		$ovBGCondition->setValueCSS( array( "Yes" 	=> ".ouocc-overlay{display: none;}" ));
		$ovBGCondition->setDefaultValue("No");
		$ovBGCondition->whiteList();

		$ovSection->addStyleControls([
			array(
				'property' 	=> 'background-color',
				'selector' 	=> '.ouocc-overlay'
			),
			array(
				'property' 	=> 'z-index',
				'selector' 	=> '.ouocc-overlay'
			)
		]);

		$ovSection->addStyleControl(
			array(
				"selector" 		=> ".ouocc-overlay",
				'property' 		=> 'transition-duration',
				"control_type" 	=> 'slider-measurebox'
			)
		)->setRange("0", "20", "0.1")->setUnits("s", "sec")->setDefaultValue(0.45);

		$slidePanel = $panel->addControlSection('ouocc_spanel', __('Sliding Panel'), 'assets/icon.png', $this );
		$palign = $slidePanel->addControl( 'buttons-list', 'ocpanel_align', __( 'Position', "oxyultimate-woo" ));
		$palign->setValue( array( "Left","Right" ) );
		$palign->setValueCSS( array(
			"Left" 		=> ".ouocc-panel-container{left: 0; right: auto;}",
			"Right" 	=> ".ouocc-panel-container{right: 0; left: auto;}"
		));
		$palign->setDefaultValue("Right");
		$palign->whiteList();

		$slidePanel->addStyleControls([
			array(
				'property' 	=> 'width',
				'selector' 	=> '.ouocc-panel-container',
				'slug' 		=> 'panel_width'
			),
			array(
				'property' 	=> 'background-color',
				'default' 	=> '#ffffff',
				'selector' 	=> '.ouocc-panel-container',
				'slug' 		=> 'panel_bgc'
			),
			array(
				'property' 	=> 'z-index',
				'selector' 	=> '.ouocc-panel-container'
			)
		]);

		$slidePanel->addOptionControl(
			array(
				"name" 			=> __('Transition Duration', "oxyultimate-woo"),
				"slug" 			=> "panel_td",
				"type" 			=> 'measurebox',
				"value" 		=> 0.4,
				"css"			=> false
			)
		)->setUnits("sec", "sec");

		$revealPanel = $slidePanel->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Reveal Panel', "oxyultimate-woo" ),
				'slug' 		=> 'reveal_panel',
				'value'		=> ['no' => __('No'), 'yes' => __("Yes")],
				'default' 	=> 'no'
			)
		);
		$revealPanel->setParam('description', __('Panel will flyout when a product will add to cart.', 'oxyultimate-woo' ) );

		$slidePanel->addOptionControl(
			array(
				"name" 			=> __('Delay In', "oxyultimate-woo"),
				"slug" 			=> "reveal_delayin",
				"type" 			=> 'measurebox',
				"value" 		=> 1500,
				"condition"		=> 'reveal_panel=yes'
			)
		)->setUnits("ms", "ms");

		$slidePanel->addOptionControl(
			array(
				"name" 			=> __('Delay Out', "oxyultimate-woo"),
				"slug" 			=> "reveal_delayout",
				"type" 			=> 'measurebox',
				"value" 		=> 4500,
				"condition"		=> 'reveal_panel=yes'
			)
		)->setUnits("ms", "ms");

		$slidePanel->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Will Panel close automatically?', "oxyultimate-woo" ),
				'slug' 		=> 'auto_close',
				'value'		=> ['no' => __('No'), 'yes' => __("Yes")],
				'default' 	=> 'yes',
				"condition"	=> 'reveal_panel=yes'
			)
		);
	}

	function offCanvasPanelHeaderControl() {
		$panelHeader = $this->addControlSection('ouocc_panelh', __('Panel Header'), 'assets/icon.png', $this );
		$panelHeader->addStyleControl(
			array(
				'selector' 		=> '.ouocc-panel-header',
				'property' 		=> 'background-color'
			)
		);

		$ptitle = $panelHeader->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Title'),
				'slug' 		=> 'panel_title',
				'placeholder' 	=> __('My Cart'),
			)
		);

		$panelHeader->typographySection( __('Title Typography'), '.ouocc-panel-title', $this );
		
		$panelspace = $panelHeader->addControlSection('ouocc_panelspace', __('Panel Title Spacing'), 'assets/icon.png', $this );

		$panelspace->addPreset(
			"padding",
			"pspc_padding",
			__("Padding"),
			'.ouocc-panel-title'
		)->whiteList();

		$panelspace->addPreset(
			"margin",
			"pspc_margin",
			__("Margin"),
			'.ouocc-panel-header'
		)->whiteList();

		$panelHeader->borderSection( __('Panel Header Borders'), '.ouocc-panel-header', $this );

		$panelCloseBtn = $panelHeader->addControlSection('ouocc_panelcbtn', __('Close Button'), 'assets/icon.png', $this );

		$closeIcon = $panelCloseBtn->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxyultimate-woo"),
				"slug" 			=> 'close_icon',
				"value" 		=> 'Lineariconsicon-cross'
			)
		);
		$closeIcon->rebuildElementOnChange();

		$panelCloseBtn->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxyultimate-woo"),
				"slug" 			=> "closeicon_size",
				"selector" 		=> 'svg.close-icon',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)
		->setRange(20, 50, 2)
		->setUnits("px", "px");

		$panelCloseBtn->addStyleControls([
			array(
				'selector' 		=> '.ouocc-close-panel',
				'property' 		=> 'width'
			),
			array(
				'selector' 		=> '.ouocc-close-panel',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Hover Background Color'),
				'selector' 		=> '.ouocc-close-panel:hover',
				'property' 		=> 'background-color'
			),
			array(
				'selector' 		=> 'svg.close-icon',
				'property' 		=> 'color'
			),
			array(
				'name' 			=> __('Hover Color'),
				'selector' 		=> '.ouocc-close-panel:hover svg',
				'property' 		=> 'color'
			)
		]);

		$panelHeader->borderSection( __('Close Button Borders'), '.ouocc-close-panel', $this );
		$closeBtnSpc = $panelHeader->addControlSection('panelcbtnspc', __('Close Button Spacing'), 'assets/icon.png', $this );
		$closeBtnSpc->addPreset(
			"padding",
			"cbtnspc_padding",
			__("Padding"),
			'.ouocc-close-panel'
		)->whiteList();

		$closeBtnSpc->addPreset(
			"margin",
			"cbtnspc_margin",
			__("Margin"),
			'.ouocc-close-panel'
		)->whiteList();
	}

	function generalControlSection() {
		$general = $this->addControlSection( 'general', __('Cart Button', "oxyultimate-woo"), "assets/icon.png", $this );

		$visibility = $general->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Visibility'),
			'slug' 		=> 'ouocc_btnvisibility',
			'value' 	=> [
				'always' 		=> __('Always'),
				'haveproducts' 	=> __('When a product in cart')
			],
			'default' 	=> 'always'
		]);

		$general->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Aria-Label/Title of Cart Button', "oxyultimate-woo"),
				'slug' 		=> 'ouocc_aria_label',
				'default'	=> __( 'View your shopping cart', "oxyultimate-woo" )
			)
		);

		$general->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __( "Show Panel On", "oxyultimate-woo" ),
				'slug' 		=> 'ouocc_trigger',
				"value" 	=> [
					'click' 		=> __('Click', "oxyultimate-woo"),
					'mouseenter' 	=> __('Hover', "oxyultimate-woo") 
				],
				"default" 	=> 'click'
			)
		);

		$items_total = $general->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Cart Number Position", "oxyultimate-woo" ),
				'slug' 		=> 'ouocc_tnumpos',
				"value" 	=> [
					'bubble' 	=> __('Bubble', "oxyultimate-woo"), 
					'before' 	=> __('Before', "oxyultimate-woo"), 
					"after" 	=> __("After", "oxyultimate-woo")
				],
				"default" 	=> 'bubble'
			)
		);
		$items_total->rebuildElementOnChange();

		$cartPrice= $general->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Display Price", "oxyultimate-woo" ),
				'slug' 		=> 'ouocc_cprice',
				"value" 	=> [
					'no' 		=> __('No', "oxyultimate-woo"), 
					'yes' 		=> __('Yes', "oxyultimate-woo")
				],
				"default" 	=> 'no'
			)
		);
		$cartPrice->rebuildElementOnChange();

		$priceAlignment = $general->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Price Alignment", "oxyultimate-woo" ),
				'slug' 		=> 'ouocc_ppos',
				"value" 	=> [
					'left' 		=> __('Left', "oxyultimate-woo"), 
					'right' 	=> __('Right', "oxyultimate-woo")
				],
				"default" 	=> 'left'
			)
		);
		$priceAlignment->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_cprice']=='yes'");
		$priceAlignment->rebuildElementOnChange();


		$cartbtn_type = $general->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __( "Cart Button Type", "oxyultimate-woo" ),
				'slug' 		=> 'ouocc_btnt',
				"value" 	=> [
					'text' 		=> __('Text', "oxyultimate-woo"), 
					'icon' 		=> __('Icon', "oxyultimate-woo"), 
					"bothit" 	=> __("Icon + Text", "oxyultimate-woo")
				],
				"default" 	=> 'text'
			)
		);
		$cartbtn_type->rebuildElementOnChange();

		$general->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __('Text', "oxyultimate-woo"),
				'slug' 		=> 'ouocc_text',
				'default'	=> __( 'Cart', "oxyultimate-woo" )
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='icon'");

		$general->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Icon Type', "oxyultimate-woo"),
				'slug' 		=> 'ouocc_ict',
				"value" 	=> [
					'icon' 		=> __('Icon', "oxyultimate-woo"), 
					'image' 	=> __('Image', "oxyultimate-woo")
				],
				'default'	=> ''
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='text'");

		$btn_image = $general->addControl("mediaurl", 'ouocc_btnimg', __('Image', "oxyultimate-woo"));
		$btn_image->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='text'");

		$imgw = $general->addOptionControl(
			array(
				'type' 		=> 'measurebox',
				'slug' 		=> 'ouocc_btnimgw',
				'name' 		=> __('Width')
			)
		);
		$imgw->setUnits('px', 'px');
		$imgw->setParam('hide_wrapper_end', true);
		$imgw->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='text'");

		$imgh = $general->addOptionControl(
			array(
				'type' 		=> 'measurebox',
				'slug' 		=> 'ouocc_btnimgh',
				'name' 		=> __('Height')
			)
		);
		$imgh->setUnits('px', 'px');
		$imgh->setParam('hide_wrapper_start', true);
		$imgh->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='text'");

		$imgalt = $general->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'slug' 		=> 'ouocc_btnimgalt',
				'name' 		=> __('Alt')
			)
		);
		$imgalt->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_ict']=='image'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='text'");

		$cart_if = $general->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxyultimate-woo"),
				"slug" 			=> 'ouocc_btnicon',
				"value" 		=> 'Lineariconsicon-cart'
			)
		);
		$cart_if->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_ict']=='icon'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='text'");
		$cart_if->rebuildElementOnChange();

		$general->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxyultimate-woo"),
				"slug" 			=> "ouocc_icon_size",
				"selector" 		=> 'svg.oumcart-icon',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height',
				"condition" 	=> "ouocc_ict=icon"
			)
		)
		->setRange(20, 50, 2)
		->setUnits("px", "px")
		->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_ict']=='icon'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_ouocc_btnt']!='text'");

		$csp = $general->addControlSection( 'color_spacing', __('General Config', "oxyultimate-woo"), "assets/icon.png", $this );

		$csp->addStyleControl(
			array(
				"selector" 			=> '.ouocc-cart-btn',
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox',
				"unit" 				=> 'px'
			)
		)
		->setUnits("px", "px")
		->setRange("0", "500", "10");

		$csp->addPreset(
			"padding",
			"oumcbtn_padding",
			__("Padding"),
			'.ouocc-cart-btn'
		)->whiteList();


		$csp->addPreset(
			"margin",
			"oumcbtn_margin",
			__("Margin"),
			'.ou-cart-button'
		)->whiteList();

		$csp->addStyleControl(
			array(
				"name" 			=> __('Space Between Icon & Text', "oxyultimate-woo"),
				"slug" 			=> "ouocc_gapict",
				"selector" 		=> '.cart-btn-text',
				"control_type" 	=> 'measurebox',
				"property" 		=> 'margin-left',
				"unit" 			=> 'px',
				"condition" 	=> "ouocc_btnt=bothit"
			)
		);

		$csp->addStyleControls([
			array(
				"slug" 			=> "ouocc_iconbgclr",
				"selector" 		=> '.ouocc-cart-btn',
				"property" 		=> 'background-color',
			)
		]);

		$csp->addStyleControls([
			array(
				'name' 			=> __('Background Hover Color', "oxyultimate-woo"),
				"slug" 			=> "ouocc_iconbghclr",
				"selector" 		=> '.ouocc-cart-btn:hover',
				"property" 		=> 'background-color',
				"control_type" 	=> 'colorpicker',
			)
		]);

		$csp->addStyleControl(
			array(
				"name" 			=> __('Icon Color', "oxyultimate-woo"),
				"slug" 			=> "ouocc_iconclr",
				"selector" 		=> '.ouocc-cart-btn svg',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color',
				"condition" 	=> "ouocc_ict=icon"
			)
		);

		$csp->addStyleControl(
			array(
				"name" 			=> __('Icon Hover Color', "oxyultimate-woo"),
				"slug" 			=> "ouocc_iconhclr",
				"selector" 		=> '.ouocc-cart-btn:hover svg',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color',
				"condition" 	=> "ouocc_ict=icon"
			)
		);

		$cpriceColor= $general->typographySection(__("Price Font & Color"), ".top-price .cart-price .woocommerce-Price-amount", $this );
		$cpriceColor->addStyleControl(
			array(
				"name" 			=> __('Hover Color', "oxyultimate-woo"),
				"slug" 			=> "ouocc_cphclr",
				"selector" 		=> '.top-price .cart-price:hover .woocommerce-Price-amount',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color'
			)
		);

		$cartBtnTxt = $general->typographySection(__("Text Font & Color"), ".cart-btn-text", $this );
		$general->borderSection(__("Border"), ".ouocc-cart-btn", $this );
		$general->boxShadowSection(__("Box Shadow"), ".ouocc-cart-btn", $this );

		$cartBtnTxt->addStyleControl(
			array(
				"name" 			=> __('Text Hover Color', "oxyultimate-woo"),
				"slug" 			=> "ouocc_cthclr",
				"selector" 		=> '.ouocc-cart-btn:hover .cart-btn-text',
				"control_type" 	=> 'colorpicker',
				"property" 		=> 'color',
				"condition" 	=> "ouocc_btnt!=icon"
			)
		);

		$floating = $general->addControlSection('oumc_floating', __('Floating Settings'), 'assets/icon.png', $this );

		$floating->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if preview is not working on Builder editor.</div>'), 
			'cartbtn_description'
		)->setParam('heading', 'Note:');

		$floating->addOptionControl([
			'type' 			=> 'radio',
			'name' 			=> __('Enable Floating Effect'),
			'slug' 			=> 'oumc_floatingbtn',
			'value' 		=> ['no' => __('No'), 'yes' => __('Yes') ],
			'default' 		=> 'no'
		])->rebuildElementOnChange();

		$floating->addStyleControl([
			'control_type' 		=> 'measurebox',
			'name' 				=> 'Margin Top',
			'selector' 			=> ' ',
			'property' 			=> '--floatbtn-margin-top',
			'condition' 		=> 'oumc_floatingbtn=yes'
		])
		->setUnits('px', 'px,%,em,vw,vh')
		->setParam('hide_wrapper_end', true);

		$floating->addStyleControl([
			'control_type' 		=> 'measurebox',
			'name' 				=> 'Margin Left',
			'selector' 			=> ' ',
			'property' 			=> '--floatbtn-margin-left',
			'condition' 		=> 'oumc_floatingbtn=yes'
		])
		->setUnits('px', 'px,%,em,vw,vh')
		->setParam('hide_wrapper_start', true);

		$floating->addStyleControl([
			'control_type' 		=> 'measurebox',
			'name' 				=> 'Margin Bottom',
			'selector' 			=> ' ',
			'property' 			=> '--floatbtn-margin-bottom',
			'condition' 		=> 'oumc_floatingbtn=yes'
		])
		->setUnits('px', 'px,%,em,vw,vh')
		->setParam('hide_wrapper_end', true);

		$floating->addStyleControl([
			'control_type' 		=> 'measurebox',
			'name' 				=> 'Margin Right',
			'selector' 			=> ' ',
			'property' 			=> '--floatbtn-margin-right',
			'condition' 		=> 'oumc_floatingbtn=yes'
		])
		->setUnits('px', 'px,%,em,vw,vh')
		->setParam('hide_wrapper_start', true);

		$floating->addStyleControl([
			'selector' 			=> '.ou-cart-button',
			'property' 			=> 'z-index',
			'condition' 		=> 'oumc_floatingbtn=yes'
		]);

		/*$floating->addPreset(
			"margin",
			"oumcfltbtn_margin",
			__("White Spaces"),
			'.oxy-ou-offcanvascart'
		)->whiteList();*/
	}

	function cartItemsNumberControlSection(){
		
		$itemsNum = $this->addControlSection( "ouocc_itemsnum", __('Cart Counter'), "assets/icon.png", $this );


		/******************* Color & Spacing *****************************/
		$numcsp = $itemsNum->addControlSection( "ouocc_inum", __('Color & Spacing'), "assets/icon.png", $this );

		$numcsp->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">' . __('Use line height feature(from Typography section) to vertically center align the number.') . '</div>', 
			'note'
		)->setParam('heading', 'Note:');

		$numcsp->addStyleControls([
			array(
				'selector'		=> '.cart-counter',
				'property' 		=> 'width',
				'slug' 			=> 'bubble_width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector'		=> '.cart-counter',
				'slug' 			=> 'bubble_height',
				'property' 		=> 'height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector'		=> '.cart-items-num',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Background Hover Color', "oxyultimate-woo"),
				'selector'		=> '.ou-cart-button:hover .cart-items-num',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Number Hover Color', "oxyultimate-woo"),
				'selector'		=> '.ou-cart-button:hover .cart-items-num',
				'property' 		=> 'color'
			),
			array(
				'selector'		=> '.cart-items-num',
				'property' 		=> 'border-color'
			),
			array(
				'name' 			=> __('Border Hover Color', "oxyultimate-woo"),
				'selector'		=> '.cart-items-num:hover',
				'property' 		=> 'border-color'
			),
			array(
				'name' 			=> __('Border Width', "oxyultimate-woo"),
				'selector'		=> '.cart-items-num',
				'property' 		=> 'border-width'
			),
		]);

		$numcsp->addPreset(
			"border-radius",
			"mcin_border_radius",
			__("Border Radius"),
			'.cart-items-num'
		)->whiteList();


		/******************* Typography *****************************/
		$itemsNum->typographySection( __('Typography'), ".cart-counter", $this );


		/******************* Bubble Postion *****************************/
		$numc_bp = $itemsNum->addControlSection( "ouocc_inumbp", __('Bubble Position'), "assets/icon.png", $this );

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.ouocc-type-bubble .cart-items-num',
				'property' 		=> 'left',
				'slug' 			=> 'bubble_pleft',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_end', true);

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.ouocc-type-bubble .cart-items-num',
				'property' 		=> 'top',
				'slug' 			=> 'bubble_ptop',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_start', true);

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.ouocc-type-bubble .cart-items-num',
				'property' 		=> 'right',
				'slug' 			=> 'bubble_pright',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_end', true);

		$numc_bp->addStyleControl(
			array(
				'selector'		=> '.ouocc-type-bubble .cart-items-num',
				'property' 		=> 'bottom',
				'slug' 			=> 'bubble_pbtm',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_tnumpos=bubble'
			)
		)->setParam('hide_wrapper_start', true);
	}

	function cartItemControlSection() {
		$cartItem = $this->addControlSection("cart_items", __("Cart Items", "oxyultimate-woo"), "assets/icon.png", $this );

		$cartContainer = $cartItem->addControlSection("cart_contents", __("Container", "oxyultimate-woo"), "assets/icon.png", $this );
		$cartContainer->addStyleControl(
		array(
			'selector' 		=> '.woocommerce-mini-cart',
			'property' 		=> 'background-color'
		));

		$cartContainer->addPreset(
			"padding",
			"mccontainer_padding",
			__("Spacing"),
			'.woocommerce-mini-cart'
		)->whiteList();

		$cartItemsp = $cartItem->addControlSection("cart_items_sp", __("Items Spacing", "oxyultimate-woo"), "assets/icon.png", $this );
		$cartItemsp->addPreset(
			"padding",
			"mcitem_padding",
			__("Padding"),
			'ul.product_list_widget li'
		)->whiteList();

		$bg = $cartItem->addControlSection("items_bg", __("Background Color", "oxyultimate-woo"), "assets/icon.png", $this );
		$bg->addStyleControls([
			array(
				'name'  		=> __("For Odd Items", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li:nth-child(odd)',
				'property' 		=> 'background-color',
				'slug' 			=> 'ouocc_itembgodd'
			),
			array(
				'name'  		=> __("For Even Items", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li:nth-child(even)',
				'property' 		=> 'background-color',
				'slug' 			=> 'ouocc_itembgev'
			)
		]);

		$divider = $cartItem->addControlSection("items_divider", __("Divider", "oxyultimate-woo"), "assets/icon.png", $this );
		$divider->addStyleControls([
			array(
				'name'  		=> __("Width", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li.mini_cart_item',
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'default' 		=> 1,
				'min' 			=> 0,
				'max' 			=> 10,
				'step' 			=> 1,
				'slug' 			=> 'ouocc_sepw'
			),
			array(
				'name'  		=> __("Color", "oxyultimate-woo"),
				'selector' 		=> 'ul.product_list_widget li.mini_cart_item',
				'property' 		=> 'border-color',
				'slug' 			=> 'ouocc_sepclr'
			)
		]);

		$itemImg = $cartItem->addControlSection( "ouocc_itemimg" ,__('Product Image'), "assets/icon.png", $this );

		$img_selector = 'ul.cart_list li img, ul.product_list_widget li img';

		$display = $itemImg->addControl( 'buttons-list', 'ouocc_imghide', __( 'Hide Image', "oxyultimate-woo" ));
		$display->setValue( array( "No","Yes" ) );
		$display->setValueCSS( array(
			"Yes" => "ul.cart_list li img, ul.product_list_widget li img {display: none}"
		));
		$display->setDefaultValue("No");
		$display->whiteList();

		$imgAlignment = $itemImg->addControl( 'buttons-list', 'ouocc_imgAlign', __( 'Alignment', "oxyultimate-woo" ));
		$imgAlignment->setValue( array( "Left", "Right" ) );
		$imgAlignment->setValueCSS( array(
			"Right" => "ul.cart_list li img, ul.product_list_widget li img {float: right; margin-left: 10px; margin-right:0;}"
		));
		$imgAlignment->setDefaultValue("Left");
		$imgAlignment->whiteList();

		$itemImg->addStyleControls([
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'background-color',
				'condition' 	=> 'ouocc_imghide=No'
			),
			array(
				'name'  		=> __("Size", "oxyultimate-woo"),
				'selector' 		=> $img_selector,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_imghide=No'
			),
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'border-color'
			),
			array(
				'selector' 		=> $img_selector,
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'ouocc_imghide=No'
			)
		]);

		$itemImg->addPreset(
			"padding",
			"itemimg_padding",
			__("Padding"),
			$img_selector
		)->whiteList();

		$itemImg->addPreset(
			"margin",
			"itemimg_margin",
			__("Margin"),
			$img_selector
		)->whiteList();

		$itemName = $cartItem->typographySection( __('Product Title'), ".mini_cart_item a", $this );
		$itemName->addStyleControls([
			array(
				'name'  		=> __("Title Hover Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item a:hover',
				'property' 		=> 'color',
				'slug' 			=> 'ouocc_titlehc'
			),
		]);

		$removeBtn = $cartItem->addControlSection( "ouocc_rmvi", __('Remove Icon', "oxyultimate-woo"), "assets/icon.png", $this );
		$selector = '.mini_cart_item a.remove';
		$selectorSVG = '.mini_cart_item a.remove svg';

		/*$removeBtn->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxy-ultimate"),
				"slug" 			=> 'remove_icon',
				'css' 			=> false
			)
		);*/
		
		$removeBtn->addStyleControls([
			array(
				'name'  		=> __("Size", "oxyultimate-woo"),
				'selector' 		=> $selectorSVG,
				'slug' 			=> 'remove_size',
				'property' 		=> 'width|height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'slug' 			=> 'remove_clr',
				'selector'		=> $selectorSVG,
				'property' 		=> 'color'
			),
			array(
				'slug' 			=> 'remove_hc',
				'name' 			=> __('Color on Hover', "oxyultimate-woo"),
				'selector'		=> $selector . ':hover svg',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Position Top", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'slug' 			=> 'remove_ptop',
				'property' 		=> 'top',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Position Left", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'slug' 			=> 'remove_pleft',
				'property' 		=> 'left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Position Right", "oxyultimate-woo"),
				'selector' 		=> $selector,
				'slug' 			=> 'remove_pright',
				'property' 		=> 'right',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Gap Between Title & Icon", "oxyultimate-woo"),
				'selector' 		=> '.product-content .product-title',
				'property' 		=> 'padding-right',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
				'default' 		=> 20
			)
		]);

		$variation = $cartItem->addControlSection( "ouocc_variations" ,__('Product Variation'), "assets/icon.png", $this );
		$variation_selector = '.ouocc-cart-items ul.product_list_widget li dl';
		$value_selector = '.ouocc-cart-items .product_list_widget .mini_cart_item .variation dd, .ouocc-cart-items .product_list_widget .mini_cart_item .variation dd p';
		$variation->addStyleControls([
			array(
				'name' 			=> __('Vertical Border Color'),
				'selector' 		=> $variation_selector,
				'property' 		=> 'border-color'
			),
			array(
				'name' 			=> __('Vertical Border Width'),
				'selector' 		=> $variation_selector,
				'property' 		=> 'border-width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'margin-left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'padding-left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'font-size'
			),
			array(
				'selector' 		=> $variation_selector,
				'property' 		=> 'line-height'
			),
			array(
				'name'  		=> __("Label Color", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-cart-items .product_list_widget .mini_cart_item .variation dt',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Label Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-cart-items .product_list_widget .mini_cart_item .variation dt',
				'property' 		=> 'font-weight'
			),
			array(
				'name'  		=> __("Value Color", "oxyultimate-woo"),
				'selector' 		=> $value_selector,
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Value Font Weight", "oxyultimate-woo"),
				'selector' 		=> $value_selector,
				'property' 		=> 'font-weight'
			)
		]);

		$itemPrice = $cartItem->addControlSection( "ouocc_itemprice" ,__('Product Price'), "assets/icon.png", $this );
		$itemPrice->addStyleControls([
			array(
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'margin-top',
				'slug' 			=> 'ouocc_titlemb',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name' 			=> __('Gap Between Label & Price'),
				'selector' 		=> '.price-label .woocommerce-Price-amount',
				'property' 		=> 'margin-left',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px',
			),
			array(
				'name'  		=> __("Label Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'color',
				'slug' 			=> 'ouocc_prdplblc'
			),
			array(
				'name'  		=> __("Label Font Size", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'font-size',
				'slug' 			=> 'ouocc_prdplblfs'
			),
			array(
				'name'  		=> __("Label Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'font-weight',
				'slug' 			=> 'ouocc_prdplblfw'
			),
			array(
				'name'  		=> __("Price Color", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label .woocommerce-Price-amount',
				'property' 		=> 'color',
				'slug' 			=> 'ouocc_prdpc'
			),
			array(
				'name'  		=> __("Price Font Size", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label .woocommerce-Price-amount',
				'property' 		=> 'font-size',
				'slug' 			=> 'ouocc_prdpfs'
			),
			array(
				'name'  		=> __("Price Font Weight", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label .woocommerce-Price-amount',
				'property' 		=> 'font-weight',
				'slug' 			=> 'ouocc_prdpfw'
			),
			array(
				'name'  		=> __("Text Transform", "oxyultimate-woo"),
				'selector' 		=> '.mini_cart_item .price-label',
				'property' 		=> 'text-transform'
			)
		]);

		$qty = $cartItem->addControlSection( "ouocc_quantity", __('Quantity Box', "oxyultimate-woo"), "assets/icon.png", $this );
		$qty->addStyleControls([
			array(
				'name' 			=> __('Box Width'),
				'selector' 		=> ' ',
				'property' 		=> '--qty-box-width',
				'control_type' 	=> 'slider-measurebox',
				'default' 		=> 85,
				'min' 			=> 0,
				'max' 			=> 400,
				'unit' 			=> 'px'
			),
			array(
				'name'  		=> __("Height of +/- Button & Quantity Field", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty, .ouocc-qty-plus',
				'property' 		=> 'height',
				'control_type' 	=> 'slider-measurebox',
				'default' 		=> 25,
				'unit' 			=> 'px'
			),
			array(
				'name' 			=> __('Background Color for Quantity Field'),
				'selector' 		=> '.ouocc-qty',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Color for Quantity'),
				'selector' 		=> '.ouocc-qty',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Font Size for Quantity", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty',
				'property' 		=> 'font-size'
			),
			array(
				'name'  		=> __("Font Weight for Quantity", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty',
				'property' 		=> 'font-weight'
			),
			array(
				'name' 			=> __('Width of +/- Button'),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'default' 		=> 50,
				'unit' 			=> 'px'
			),
			array(
				'name' 			=> __('Background Color for +/- Button'),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=> __('Color for +/- Button'),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'color'
			),
			array(
				'name'  		=> __("Font Size for +/- Button", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'font-size'
			),
			array(
				'name'  		=> __("Font Weight for +/- Button", "oxyultimate-woo"),
				'selector' 		=> '.ouocc-qty-minus, .ouocc-qty-plus',
				'property' 		=> 'font-weight'
			)
		]);

		$totalPrice = $cartItem->typographySection( __('Total Price', "oxyultimate-woo"), ".item-total-price, .item-total-price .woocommerce-Price-amount", $this );
	}

	function cartTotalWrapperControlSection() {
		$wrapper = $this->addControlSection( 'cart_total_wrapper', __('Cart Total Wrapper', "oxyultimate-woo"), "assets/icon.png", $this );
		$selector = '.ouocc-cart-items .woocommerce-mini-cart__total';
		$wrapper->addStyleControl(
			array(
				'selector'		=> $selector,
				'property' 		=> 'background-color',
				'slug' 			=> 'subt_bglr'
			)
		);

		$st_spacing = $wrapper->addControlSection( 'twrapper_spacing', __('Spacing'), "assets/icon.png", $this );
		$st_spacing->addPreset(
			"padding",
			"mcsubt_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$st_spacing->addPreset(
			"margin",
			"mcsubt_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$wrapper->borderSection(__('Border'), $selector, $this );
	}

	/******************************* Promo Code ********************************/
	function cartCouponControlSection() {
		$coupon = $this->addControlSection( 'cart_coupon', __('Coupon Form', "oxyultimate-woo"), "assets/icon.png", $this );

		//Enable coupon
		$formLogic = $coupon->addControl('buttons-list', 'coupon_form_enable', __('Display Coupon Submission Form'));
		$formLogic->setValue(['No', 'Yes']);
		$formLogic->setDefaultValue('No');
		$formLogic->setValueCSS(['Yes'  => '.coupon-code-wrap{display:flex}.oucc-coupon-row,.order-total-row{display: grid;}']);
		$formLogic->whiteList();
		
		$padding = $coupon->addControlSection( 'field_padding', __('Padding', "oxyultimate-woo"), "assets/icon.png", $this );
		$padding->addPreset(
			"padding",
			"field_padding",
			__("Padding for Field & Button"),
			'.ouocc-coupon-field,.coupon-btn'
		)->whiteList();


		$coupon->typographySection( __('Input Font', "oxyultimate-woo"), '.ouocc-coupon-field', $this );
		$input = $coupon->addControlSection( 'input_style', __('Input Style', "oxyultimate-woo"), "assets/icon.png", $this );
		$input->addStyleControls([
			array(
				'selector' 		=> '.ouocc-coupon-field',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'min' 			=> '200',
				'max' 			=> '600',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> '.ouocc-coupon-field',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Background Color on Focus'),
				'selector' 		=> '.ouocc-coupon-field:focus',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Color on Focus'),
				'selector' 		=> '.ouocc-coupon-field:hover',
				'property' 		=> 'color'
			),
			array(
				'name' 			=>  __('Placeholder Color'),
				'selector' 		=> '.coupon-code-wrap input::placeholder',
				'property' 		=> 'color'
			)
		]);

		$coupon->borderSection( __('Input Border', "oxyultimate-woo"), '.ouocc-coupon-field', $this );
		$coupon->borderSection( __('Input Focus Border', "oxyultimate-woo"), '.ouocc-coupon-field:focus', $this );

		$coupon->typographySection( __('Button Font', "oxyultimate-woo"), '.coupon-btn', $this );

		$button = $coupon->addControlSection( 'button_style', __('Button Style', "oxyultimate-woo"), "assets/icon.png", $this );
		$button->addStyleControls([
			array(
				'selector' 		=> '.coupon-btn',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'max' 			=> 600,
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> '.coupon-btn',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Background Color on Hover'),
				'selector' 		=> '.coupon-btn:hover',
				'property' 		=> 'background-color'
			),
			array(
				'name' 			=>  __('Color on Hover'),
				'selector' 		=> '.coupon-btn:hover',
				'property' 		=> 'color'
			),
		]);

		$coupon->borderSection( __('Button Border', "oxyultimate-woo"), '.coupon-btn', $this );
		$coupon->borderSection( __('Button Hover Border', "oxyultimate-woo"), '.coupon-btn:hover', $this );
		$coupon->boxShadowSection( __('Button Shadow', "oxyultimate-woo"), '.coupon-btn', $this );
		$coupon->boxShadowSection( __('Button Hover Shadow', "oxyultimate-woo"), '.coupon-btn:hover', $this );


		$coupon->borderSection( __('Wrapper Border', "oxyultimate-woo"), '.coupon-code-wrap', $this );

		$spacing = $coupon->addControlSection( 'ccwrap_spacing', __('Wrapper Spacing', "oxyultimate-woo"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"cc_padding",
			__("Padding"),
			'.coupon-code-wrap'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cc_margin",
			__("Margin"),
			'.coupon-code-wrap'
		)->whiteList();


		$others = $coupon->addControlSection( 'ccf_others', __('Others', "oxyultimate-woo"), "assets/icon.png", $this );
		$others->addStyleControls([
			array(
				'name' 			=> __('Gap Between Field & Button'),
				'selector' 		=> '.ouocc-coupon-field',
				'property' 		=> 'margin-right',
				'control_type' 	=> 'slider-measurebox',
				'min' 			=> '75',
				'max' 			=> '600',
				'unit' 			=> 'px'
			),
			array(
				'name' 			=> __('Wrapper Background Color'),
				'selector' 		=> '.coupon-code-wrap',
				'property' 		=> 'background-color'
			)
		]);
	}

	/******************************* Sub Total ********************************/
	function cartSubtotalControlSection() {
		$subTotal = $this->addControlSection( 'sub_total', __('Sub Total', "oxyultimate-woo"), "assets/icon.png", $this );

		$subTotal->addStyleControl(
			array(
				'selector'		=> '.subtotal-wrap',
				'property' 		=> 'background-color'
			)
		);

		$subTotal->typographySection( __('Typography'), ".subtotal-wrap, .subtotal-wrap > strong, .subtotal-wrap .woocommerce-mini-cart__total strong", $this );
		
		$price = $subTotal->addControlSection( 'sub_total_price', __('Price', "woocommerce"), "assets/icon.png", $this );
		$price->addStyleControls([
			array(
				'name' 			=> __('Color'),
				'selector'		=> '.total .woocommerce-Price-amount',
				'property' 		=> 'color',
				'slug' 			=> 'price_clr'
			),
			array(
				'name' 			=> __('Font Size'),
				'selector'		=> '.total .woocommerce-Price-amount',
				'property' 		=> 'font-size',
				'slug' 			=> 'price_fs'
			),
		]);
		$priceAlignment = $price->addControl( 'buttons-list', 'ouocc_priceAlign', __( 'Price Alignment', "oxyultimate-woo" ));
		$priceAlignment->setValue( array( "Default", "Right" ) );
		$priceAlignment->setValueCSS( array(
			"Default" => ".total .woocommerce-Price-amount {float: none;}",
			"Right" => ".total .woocommerce-Price-amount {float: right;}"
		));
		$priceAlignment->setDefaultValue("Right");
		$priceAlignment->whiteList();

		$spacing = $subTotal->addControlSection( 'subtotal_sp', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"stsp_padding",
			__("Padding"),
			'.subtotal-wrap'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"stsp_margin",
			__("Margin"),
			'.subtotal-wrap'
		)->whiteList();
	}

	/******************************* Coupon Details ********************************/
	function cartCouponDetailsControlSection() {
		$coupons = $this->addControlSection( 'cart_coupons', __('Coupons Details', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.oucc-coupon-row';

		$spacing = $coupons->addControlSection( 'ccs_spacing', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"ccsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"ccsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$coupons->addStyleControl(
			array(
				'selector'		=> $selector,
				'property' 		=> 'background-color'
			)
		);

		$coupons->typographySection( __('Coupon Text', "oxyultimate-woo"), '.oucc-coupon-label', $this );
		$coupons->typographySection( __('Coupon Price', "oxyultimate-woo"), '.oucc-discount-price, .oucc-discount-price .woocommerce-Price-amount', $this );

		$delete = $coupons->addControlSection( 'ccs_delete', __('Delete Icon', "oxyultimate-woo"), "assets/icon.png", $this );
		$selector = 'a.ouocc-remove-coupon svg';
		$delete->addStyleControls([
			array(
				'name' 			=> __('Size'),
				'selector' 		=> $selector,
				'property' 		=> 'width|height',
				'control_type' 	=> 'slider-measurebox',
				'max' 			=> 100,
				'unit' 			=> 'px'
			),
			array(
				'selector'		=> $selector,
				'property' 		=> 'color'
			),
			array(
				'name' 			=> __('Color on Hover'),
				'selector'		=> 'a.ouocc-remove-coupon:hover svg',
				'property' 		=> 'color'
			)
		]);
	}

	/******************************* Total ********************************/
	function cartTotalControlSection() {
		$total = $this->addControlSection( 'cart_total', __('New Total Price', "oxyultimate-woo"), "assets/icon.png", $this );

		$selector = '.order-total-row';

		$total->addStyleControl(
			array(
				'selector'		=> $selector,
				'property' 		=> 'background-color'
			)
		);

		/*$total->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> __('Label'),
			'default' 		=> __('New Total'),
			'slug' 			=> 'ouocc_total_text'
		])->setParam('description', __('Click on Apply Params button and see the changes', "oxyultimate-woo"));*/

		$spacing = $total->addControlSection( 'ottl_spacing', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"ottl_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"ottl_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$total->typographySection(__('Total Label'), $selector . ' .order-total-label', $this );
		$total->typographySection(__('Total Price'), $selector . ' .order-total-price, .order-total-price .woocommerce-Price-amount', $this );

		$total->borderSection(__('Border'), $selector, $this );
	}

	/******************************* Buttons ********************************/
	function cartButtonsControlSection() {
		$btnStructure = $this->addControlSection( "ouocc_btns", __('Buttons Structure', "oxyultimate-woo"), "assets/icon.png", $this );

		$btnWrapper = $btnStructure->addControlSection( "ouocc_btncontainer", __('Container', "oxyultimate-woo"), "assets/icon.png", $this );

		$btnWrapper->addStyleControl(
			array(
				'selector'		=> '.woocommerce-mini-cart__buttons',
				'property' 		=> 'background-color',
				'slug' 			=> 'btnwrapper_bglr'
			)
		);

		$btnWrapper->addPreset(
			"padding",
			"btncontainer_padding",
			__("Padding"),
			'.woocommerce-mini-cart__buttons'
		)->whiteList();

		$btnWrapper->addPreset(
			"margin",
			"btncontainer_margin",
			__("Margin"),
			'.woocommerce-mini-cart__buttons'
		)->whiteList();

		$buttons = $btnStructure->addControlSection( "ouocc_btnsspace", __('Buttons', "oxyultimate-woo"), "assets/icon.png", $this );

		$buttons->addStyleControl(
			array(
				"selector" 			=> '.woocommerce-mini-cart__buttons a.button',
				"property" 			=> 'width',
				"control_type" 		=> 'slider-measurebox',
				'slug' 				=> 'cartbgns_width'
			)
		)
		->setRange('0', '500', 20)
		->setUnits('px', 'px,em,%,auto');

		$gap = $buttons->addStyleControl(
			array(
				"name" 				=> __("Horizontal Gap Between Two Buttons"),
				"selector" 			=> '.woocommerce-mini-cart__buttons .button.checkout',
				"property" 			=> 'margin-left',
				"control_type" 		=> 'slider-measurebox',
				'slug' 				=> 'cartbgns_gap'
			)
		)
		->setRange('0', '50', 5)
		->setUnits('px', 'px');
		$gap->setParam('description', __('This would work when buttons will stay horizontally.', "oxyultimate-woo") );

		$gapBottom = $buttons->addStyleControl(
			array(
				"name" 				=> __("Vertical Gap Between Two Buttons"),
				"selector" 			=> '.woocommerce-mini-cart__buttons .button.checkout',
				"property" 			=> 'margin-top',
				"control_type" 		=> 'slider-measurebox',
				'slug' 				=> 'cartbgns_gaptop'
			)
		)
		->setRange('0', '50', 5)
		->setUnits('px', 'px');

		$gapBottom->setParam('description', __('This would work when buttons will stay vertically.', "oxyultimate-woo") );

		$btnAlign = $buttons->addControl("buttons-list", "btn_align", __("Alignment") );
		$btnAlign->setValue([
			'Left',
			'Center',
			'Right'
		]);

		$btnAlign->setValueCSS([
			'Left' 		=> ".woocommerce-mini-cart__buttons{ text-align: left}",
			'Center'	=> ".woocommerce-mini-cart__buttons{ text-align: center}",
			'Right' 	=> ".woocommerce-mini-cart__buttons{ text-align: right}"
		]);
		$btnAlign->setDefaultValue('Right');
		$btnAlign->whiteList();

		$buttons->addPreset(
			"padding",
			"vc_padding",
			__("Padding"),
			'.woocommerce-mini-cart__buttons a.button'
		)->whiteList();


		/********************
		 * View Cart Button
		 *******************/
		$viewCart = $this->addControlSection( 'btn_viewcart', __('View Cart Button', "oxyultimate-woo"), "assets/icon.png", $this );

		$hideVC = $viewCart->addControl('buttons-list', 'viewcart_hide', __('Hide View Cart Button?'));
		$hideVC->setValue(['No', 'Yes']);
		$hideVC->setValueCSS(['Yes' => '.woocommerce-mini-cart__buttons a:first-child{display:none}' ]);
		$hideVC->setDefaultValue('No');
		$hideVC->whiteList();
		
		$vcTg = $viewCart->typographySection( __('Font & Colors', "oxyultimate-woo"), ".woocommerce-mini-cart__buttons a:first-child", $this );

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child:hover',
				"property" 			=> 'color',
				'slug' 				=> 'cartbtns_hc'
			)
		)->setParam('hide_wrapper_end', true);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child:hover',
				"property" 			=> 'border-color',
				'slug' 				=> 'cartbtns_hbrdc'
			)
		)->setParam('hide_wrapper_start', true);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartbtns_bgc'
			)
		)->setParam('hide_wrapper_end', true);

		$vcTg->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a:first-child:hover',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartbtns_bghc'
			)
		)->setParam('hide_wrapper_start', true);

		$viewCart->borderSection( __( "Border", "oxyultimate-woo" ), '.woocommerce-mini-cart__buttons a:first-child', $this );
		$viewCart->boxShadowSection( __("Box Shadow"), '.woocommerce-mini-cart__buttons a:first-child', $this );


		/********************
		 * Checkout Button
		 *******************/
		$checkout = $this->addControlSection( 'btn_checkout', __('Checkout Button', "oxyultimate-woo"), "assets/icon.png", $this );

		$hideCB = $checkout->addControl('buttons-list', 'checkoutbtn_hide', __('Hide Checkout Button?'));
		$hideCB->setValue(['No', 'Yes']);
		$hideCB->setValueCSS(['Yes' => '.woocommerce-mini-cart__buttons a.checkout{display:none}' ]);
		$hideCB->setDefaultValue('No');
		$hideCB->whiteList();

		$cTg = $checkout->typographySection( __('Font & Colors', "oxyultimate-woo"), ".woocommerce-mini-cart__buttons a.checkout", $this );
		$cTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout:hover',
				"property" 			=> 'color',
				'slug' 				=> 'cartcbtn_hc'
			)
		)->setParam('hide_wrapper_end', true);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout:hover',
				"property" 			=> 'border-color',
				'slug' 				=> 'cartcbtn_hbrdc'
			)
		)->setParam('hide_wrapper_start', true);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartcbtn_bgc'
			)
		)->setParam('hide_wrapper_end', true);

		$cTg->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> '.woocommerce-mini-cart__buttons a.checkout:hover',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker',
				'slug' 				=> 'cartcbtn_bghc'
			)
		)->setParam('hide_wrapper_start', true);

		$checkout->borderSection( __( "Border", "oxyultimate-woo" ), '.woocommerce-mini-cart__buttons a.checkout', $this );
		$checkout->boxShadowSection( __("Box Shadow"), '.woocommerce-mini-cart__buttons a.checkout', $this );


		/*************************
		 * Return To Shop Button
		 *************************/
		$shop = $this->addControlSection( 'btn_shop', __('Return To Shop', "oxyultimate-woo"), "assets/icon.png", $this );

		$sel = '.woocommerce-mini-cart__buttons a.ouocc-shop-button';

		$hideRB = $shop->addControl('buttons-list', 'shopbtn_show', __('Display Return To Shop Button'));
		$hideRB->setValue(['No', 'Yes']);
		$hideRB->setValueCSS(['No' => '.ouocc-cart-items a.ouocc-shop-button{display:none}', 'Yes' => '.ouocc-cart-items a.ouocc-shop-button{display:block}' ]);
		$hideRB->setDefaultValue('No');
		$hideRB->whiteList();

		$shop->addOptionControl(
			[
				'type' 		=> 'textfield',
				'name' 		=> __('Button Text'),
				'default' 	=> __('Continue Shopping'),
				'slug' 		=> 'shop_button_text',
				'condition' => 'shopbtn_show=Yes'
			]
		)->setParam('description', __('Click on Apply Params button to see the text on editor.'));

		$shop->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> _('Button Click Action'),
			'slug' 		=> 'shop_button_url_source',
			'value' 	=> ['shop' => __('Redirect Shop Page'), 'custom' => __('Redirect Custom URL'), 'close' => __('Close Panel', 'oxyultimate-woo')],
			'default' 	=> 'shop',
			'condition' => 'shopbtn_show=Yes'
		]);

		$custom_url = $shop->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_offcanvascart_custom_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_offcanvascart_custom_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_offcanvascart_custom_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_offcanvascart_custom_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_offcanvascart_custom_url\')">set</div>
			</div>
			',
			"custom_url",
			$shop
		);
		$custom_url->setParam( 'heading', __('Custom URL') );
		$custom_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_offcanvascart_shop_button_url_source']=='custom'" );

		$shop->addStyleControl(
			[
				'selector' 		=> $sel,
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'condition' 	=> 'shopbtn_show=Yes'
			]
		)->setUnits('px', 'px,%,em,auto,rem')->setRange(0, 1000, 10);

		$rbTg = $shop->typographySection( __('Font & Colors', "oxyultimate-woo"), $sel, $this );
		$rbTg->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $sel . ':hover',
				"property" 			=> 'color'
			)
		)->setParam('hide_wrapper_end', true);

		$rbTg->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> $sel . ':hover',
				"property" 			=> 'border-color'
			)
		)->setParam('hide_wrapper_start', true);

		$rbTg->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $sel,
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker'
			)
		)->setParam('hide_wrapper_end', true);

		$rbTg->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $sel . ':hover',
				"property" 			=> 'background-color',
				"control_type" 		=> 'colorpicker'
			)
		)->setParam('hide_wrapper_start', true);

		$gap = $shop->addControlSection( 'gap_section', __('Gap', "oxyultimate-woo"), 'assets/icon.png', $this );
		$gap->addStyleControl(
			array(
				"selector" 			=> $sel,
				"property" 			=> 'margin-top'
			)
		)->setParam('hide_wrapper_end', true);

		$gap->addStyleControl(
			array(
				"selector" 			=> $sel,
				"property" 			=> 'margin-bottom'
			)
		)->setParam('hide_wrapper_start', true);

		$gap->addStyleControl(
			array(
				"selector" 			=> $sel,
				"property" 			=> 'margin-left'
			)
		)->setParam('hide_wrapper_end', true);

		$gap->addStyleControl(
			array(
				"selector" 			=> $sel,
				"property" 			=> 'margin-right'
			)
		)->setParam('hide_wrapper_start', true);

		$shop->borderSection( __( "Border" ), $sel, $this );
		$shop->boxShadowSection( __("Box Shadow"), $sel, $this );
	}

	/******************************* Notices ********************************/
	function messageControl() {
		$message = $this->addControlSection( 'cart_msg', __('Notices', "oxyultimate-woo"), "assets/icon.png", $this );

		$message->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">' . __('Live preview is not available on Builder Editor. Enter &amp;apos; for single quote.') . '</div>', 
			'msg_desc'
		)->setParam('heading', 'Note:');

		$message->addStyleControl(
			array(
				'selector'		=> '.oucc-wc-notice',
				'property' 		=> 'background-color'
			)
		);

		$messages = $message->addControlSection( 'notices', __('Messages'), "assets/icon.png", $this );

		$messages->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Add Notice'),
			'slug' 		=> 'notice_add',
			'default' 	=> __('Item added'),
			'base64' 	=> true
		]);

		$messages->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Update Notice'),
			'slug' 		=> 'notice_update',
			'default' 	=> __('Item updated'),
			'base64' 	=> true
		]);

		$messages->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Remove Notice'),
			'slug' 		=> 'notice_remove',
			'default' 	=> __('Item removed'),
			'base64' 	=> true
		]);

		$messages->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Out of Stock Notice'),
			'slug' 		=> 'max_qty_msg',
			'default' 	=> __('No more products on stock'),
			'base64' 	=> true
		]);

		$messages->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Wrong Input Notice'),
			'slug' 		=> 'min_qty_msg',
			'default' 	=> __('You entered wrong value.'),
			'base64' 	=> true
		]);

		$spacing = $message->addControlSection( 'cartmsg_spacing', __('Spacing'), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"cartmsg_padding",
			__("Padding"),
			'.oucc-wc-notice'
		)->whiteList();

		$message->typographySection( __('Typography'), ".wc-notice-text", $this );
	}

	function othersSection() {

		$others = $this->addControlSection( "ouocc_others", __('Others Config', "oxyultimate-woo"), "assets/icon.png", $this );

		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable AJAX add to cart function on single product page'),
			'description' => __('Single product page will not reload or refresh. Product will add to cart via AJAX.'),
			'slug' 		=> 'ouocc_ajax_single',
			'value' 	=> [ 'no' => __('No'), 'yes' => __('Yes') ],
			'default' 	=> 'no'
		]);

		$others->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable fly to cart animation effect'),
			'description' => __('Product image will fly to cart section & it will indicate that the product was added.'),
			'slug' 		=> 'ouocc_flytocart',
			'value' 	=> [ 'no' => __('No'), 'yes' => __('Yes') ],
			'default' 	=> 'no'
		]);

		$others->addOptionControl(
			array(
				"name"			=> __('Offset value of Top Position'),
				"slug" 			=> "ftc_offset_top",
				"default"		=> "0",
				"type" 			=> 'slider-measurebox',
				'condition' => 'ouocc_flytocart=yes'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setDefaultValue(5);

		$others->addOptionControl(
			array(
				"name"			=> __('Offset value of Left Position'),
				"slug" 			=> "ftc_offset_left",
				"default"		=> "0",
				"type" 			=> 'slider-measurebox',
				'condition' => 'ouocc_flytocart=yes'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setDefaultValue(5);
	}

	function controls() {
		$this->addCustomControl('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">You will click on <span style="color:#ff7171;">Apply Params</span> button, if preview is not working properly.</div>', 'desc');

		$this->El->addControl("buttons-list", "occart_preview", __( "Enable Preview of Off Canvas Panel", "oxyultimate-woo" ) )->setValue([__( "Yes", "oxyultimate-woo" ), __( "No", "oxyultimate-woo" ) ])->setValueCSS([ 'Yes' => '.ouocc-builder-edit.ouocc-overlay,.ouocc-builder-edit.ouocc-panel-container{display: block;visibility: visible;}' ])->setDefaultValue('No');

		$this->offCanvasPanelControl();

		$this->offCanvasPanelHeaderControl();

		$this->generalControlSection();

		$this->cartItemsNumberControlSection();

		$this->cartItemControlSection();

		$this->cartTotalWrapperControlSection();
		
		$this->cartCouponControlSection();

		$this->cartSubtotalControlSection();

		$this->cartCouponDetailsControlSection();

		$this->cartTotalControlSection();

		$this->cartButtonsControlSection();

		$this->messageControl();

		$this->othersSection();
	}

	function ouwoo_woo_cart_fragment( $fragments ) {
		ob_start();
		?>
		<span class="cart-counter"><?php echo is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : '0'; ?></span>
		<?php
		
		$fragments['span.cart-counter'] = ob_get_clean();

		ob_start();
		?>
		<span class="cart-price"><?php echo is_object( WC()->cart ) ? wc_price( $this->ou_cart_total() ) : wc_price( 0 ); ?></span>
		<?php
		
		$fragments['span.cart-price'] = ob_get_clean();

		remove_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'ouwoo_woo_cart_fragment' ) );

		return $fragments;
	}

	function ou_cart_total() {
		if( ! is_object( WC()->cart ) )
			return;
		
		if( WC()->cart->display_prices_including_tax() ) {
			return ( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
		} else {
			return WC()->cart->get_cart_contents_total();
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();

		if( isset( $original[$prefix . '_panel_td'] ) ) {
			$css .= $selector .' .ouocc-panel-container{transition-duration: ' . $original[$prefix . '_panel_td'] . 's}';
		}

		if( isset($original[ $prefix . '_oumc_floatingbtn' ]) && $original[ $prefix . '_oumc_floatingbtn' ] == 'yes' ) {
			$panel_td = isset($original[$prefix . '_panel_td'] ) ? $original[$prefix . '_panel_td'] : '0.3';
			$panelWidth = isset($original[$prefix . '_panel_width'] ) ? $original[$prefix . '_panel_width'] : 350;
			$panelAlign = isset($original[$prefix . '_ocpanel_align'] ) ? $original[$prefix . '_ocpanel_align'] : 'right';

			$css .= $selector . '{position: fixed; bottom: 0; margin: var(--floatbtn-margin-top) var(--floatbtn-margin-right) var(--floatbtn-margin-bottom) var(--floatbtn-margin-left); z-index: 7658468567; transition: all '. $panel_td . 's ease;}';
			$css .= $selector . ' .ou-cart-button{z-index: 7658468567;}';
			
			$css .= $selector . '{' . $panelAlign . ': 0;}';
			$css .= $selector . '.ou-panel-active{' . $panelAlign . ': ' . $panelWidth . 'px;}';

			if( isset($original[ $prefix . '_occart_preview' ]) && $original[ $prefix . '_occart_preview' ] == 'Yes' ) {
				$css .= 'body.oxygen-builder-body ' . $selector . '{' . $panelAlign . ': ' . $panelWidth . 'px;}';
			}
		}

		return $css;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUWooOffCanvasCart();