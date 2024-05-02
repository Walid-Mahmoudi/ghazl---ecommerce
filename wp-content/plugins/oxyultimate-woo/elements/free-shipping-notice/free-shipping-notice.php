<?php

class OUWooFreeShippingMsg extends UltimateWooEl {
	
	//* Avoiding the duplicate CSS
	public $css_added = false;

	//* This component have custom JS script
	public $has_js = true;
	
	//* JS will call one time
	public $js_added = false;
	
	//* Component options in array
	public $comp_options = array();

	function name() {
		return __( "Free Shipping Notice", "oxyultimate-woo" );
	}

	function ouwoo_button_place() {
		return "main";
	}

	function button() {
		$button = $this->addControlSection( 'button_section', __('Button'), "assets/icon.png", $this );

		$selector = '.call-to-action';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text', "oxyultimate-woo"),
			'slug' 		=> 'button_text'
		])->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

		$btn_url = $button->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-free-shipping-notice_btn_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-free-shipping-notice_btn_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-free-shipping-notice_btn_url\');iframeScope.checkResizeBoxOptions(\'oxy-free-shipping-notice_btn_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-free-shipping-notice_btn_url\')">set</div>
			</div>
			',
			"btn_url"
		);
		$btn_url->setParam( 'heading', __('URL') );

		$button->addStyleControl([
			'control_type'	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'transition-duration'
		])->setRange(0, 10, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.2);

		//* Padding & Margin
		$spacing = $button->addControlSection('btnsp_section', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"btn_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"btn_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Font & Color
		$button->typographySection(__('Typography'), $selector, $this);

		//* Colors and Width
		$colors = $button->addControlSection('btnclr_section', __('Colors & Width', "oxyultimate-woo"), "assets/icon.png", $this);

		$colors->addStyleControl([
			'control_type'	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'width'
		])->setRange(0, 1000, 10);

		$colors->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ":hover",
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Text Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ":hover",
				'property' 		=> 'color'
			]
		]);

		//* Borders
		$button->borderSection(__('Border'), $selector, $this);
		$button->borderSection(__('Hover Border'), $selector . ":hover", $this);

		//* Box Shadow
		$button->boxShadowSection(__('Box Shadow'), $selector, $this);
		$button->boxShadowSection(__('Hover Shadow'), $selector . ":hover", $this);
	}

	function animation() {
		$animation = $this->addControlSection( 'anim_section', __('Animation', "oxyultimate-woo"), "assets/icon.png", $this );

		$animation->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable Animation Effect', "oxyultimate-woo"),
			'slug' 		=> 'enable_animation',
			'value'		=> ['no' => __('No'), 'yes' => __("Yes")],
			'default' 	=> 'no'
		]);

		$animation->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Speed for Fade', "oxyultimate-woo"),
			'slug' 		=> 'fade_speed'
		])->setRange(0,5000,10)->setUnits('ms', 'ms')->setDefaultValue(950);

		$animation->addStyleControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Speed for Slide', "oxyultimate-woo"),
			'selector' 	=> ' ',
			'property' 	=> 'transition-duration'
		])->setRange(0,10,0.1)->setUnits('s', 'sec')->setDefaultValue(0.15);
	}

	
	function generalConfig() {
		//* Config Section
		$config = $this->addControlSection( 'config_section', __('Config', "oxyultimate-woo"), "assets/icon.png", $this );

		$config->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">'. __('Click on <span style="color:#ff7171;">Apply Params</span> button & see the message on builder editor.', "oxyultimate-woo") .'</div>', 
			'description'
		)->setParam('heading', 'Note:');

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Message Type', "oxyultimate-woo"),
			'slug' 	=> 'msg_type',
			'value' => [
				'amount' 	=> __('Minimum Amount', "oxyultimate-woo"),
				'quantity' 	=> __('Cart Quantity', "oxyultimate-woo")
			]
		]);

		/***************************
		 * Quantity Settings
		 ***************************/
		$config->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Required Quantity', "oxyultimate-woo"),
			'slug' 		=> 'required_qty',
			'condition' => 'msg_type=quantity'
		]);

		$config->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Threshold Quantity', "oxyultimate-woo"),
			'slug' 		=> 'threshold_qty',
			'condition' => 'msg_type=quantity'
		])->setParam('description', __('Set here the minimum product quantity to show the message.', "oxyultimate-woo"));

		$config->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Will quantity update automatically?', "oxyultimate-woo"),
			'slug' 		=> 'update_qty',
			'value'		=> ['no' => __('No'), 'yes' => __("Yes")],
			'default' 	=> 'no',
			'condition' => 'msg_type=quantity'
		]);



		/***************************
		 * Minimum Amount Settings
		 ***************************/
		$config->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Minimum Order Amount', "oxyultimate-woo"),
			'slug' 		=> 'min_amount',
			'condition' => 'msg_type=amount'
		])->setParam('description', __('Do not enter the currency. Minimum order amount to encourage users to purchase more.', "oxyultimate-woo"));

		$config->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Threshold Amount', "oxyultimate-woo"),
			'slug' 		=> 'threshold_amount',
			'condition' => 'msg_type=amount'
		])->setParam('description', __('Threshold amount after which notice should start appear.', "oxyultimate-woo"));

		$config->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Exclude Coupons Amount', "oxyultimate-woo"),
			'slug' 		=> 'exclude_coupons',
			'value'		=> ['no' => __('No'), 'yes' => __("Yes")],
			'default' 	=> 'no',
			'condition' => 'msg_type=amount'
		]);

		$config->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Will shipping price update automatically?', "oxyultimate-woo"),
			'slug' 		=> 'update_price',
			'value'		=> ['no' => __('No'), 'yes' => __("Yes")],
			'default' 	=> 'no',
			'condition' => 'msg_type=amount'
		]);


		
		$config->addCustomControl(
			'<div class="oxygen-option-default"><hr style="color: #f4f4f4;height: 1px" noshade/></div>', 
			'divider'
		);



		/****************** Minimum Amount *************************/
		$config->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('Enter Your Message', "oxyultimate-woo"),
			'slug' 			=> 'minamt_message',
			'default' 		=> 'Add {remaining_amount} to your cart in order to receive free shipping!',
			'condition' 	=> 'msg_type=amount'
		])->setParam('description', __('You can use <span style="color:#ff7171;">{remaining_amount}</span> as remaining amount required to meet the minimum order amount.', "oxyultimate-woo"));


		/****************** Cart Quantity *************************/
		$config->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('Enter Your Message', "oxyultimate-woo"),
			'slug' 			=> 'qty_message',
			'default' 		=> 'Add {remaining_quantity} into your cart in order to receive free shipping!',
			'condition' 	=> 'msg_type=quantity'
		]);

		/*'You can edit the text using the following placeholder: <br>{remaining_quantity} indicates the remaining quantity;<br>{products} specifies which of the listed product is in the cart;<br>{quantity} indicates quantity in cart,{required_quantity} states the exact number of product to purchase.'
		*/

		$config->addOptionControl([
			'type' => 'dropdown',
			'name' => __('Take Action when set amount/quantity is reached'),
			'slug' => 'after_action',
			'value' => [
				'hide' => __('Hide', 'oxyultimate-woo'),
				'custmsg' => __('Display custom message', 'oxyultimate-woo'),
			],
			'default' => 'hide'
		]);

		$config->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('Enter Your Custom Message', "oxyultimate-woo"),
			'slug' 			=> 'fs_notice',
			'condition' 	=> 'after_action=custmsg'
		]);

		$config->addCustomControl(
			'<div class="oxygen-option-default"><hr style="color: #f4f4f4;height: 1px" noshade/></div>', 
			'divider'
		);

		$config->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Outer Wrapper Selector', "oxyultimate-woo"),
			'slug' 		=> 'outer_wrap_sel'
		])->setParam('description', __('Setup when you will put this component into another wrapper.', "oxyultimate-woo"));
	}

	function progress_bar_controls() {
		$pb = $this->addControlSection('fsnpb_sec', __('Progress Bar', "oxyultimate-woo"), 'assets/icon.png', $this );

		$pb->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __('Enable Progress Bar', "oxyultimate-woo"),
			'slug' 	=> 'fsn_display_pb',
			'value' => [
				'no' 	=> __('No', "oxyultimate-woo"),
				'yes' 	=> __('Yes', "oxyultimate-woo")
			],
			'default' => 'no'
		])->rebuildElementOnChange();

		$pb->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __('Display Price / Quantity', "oxyultimate-woo"),
			'slug' 	=> 'pb_show_price',
			'value' => [
				'no' 	=> __('No', "oxyultimate-woo"),
				'yes' 	=> __('Yes', "oxyultimate-woo")
			],
			'default' => 'yes'
		])->rebuildElementOnChange();

		
		$pbcontainer = $pb->addControlSection('pbcnt_sec', __('Outer Container', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.fsn-progress-bar-wrap';

		$pbcontainer->addStyleControl([
			'name' 		=> __('Width'),
			'selector' 	=> '',
			'property' 	=> '--fsn-progress-bar-width',
			'control_type' => 'slider-measurebox'
		])->setRange(0, 600, 1)->setUnits('%','px,%,em,rem,auto,vw,vh,none')->setDefaultValue(100);

		$pbcontainer->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-top'
		])->setParam('hide_wrapper_end', true);

		$pbcontainer->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-right'
		])->setParam('hide_wrapper_start', true);

		$pbcontainer->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-bottom'
		])->setParam('hide_wrapper_end', true);

		$pbcontainer->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-left'
		])->setParam('hide_wrapper_start', true);

		$pb->typographySection( __('Price / Quantity'), '.fsn-progress-bar-min-price .woocommerce-Price-amount,
					.fsn-progress-amount .woocommerce-Price-amount, .fsn-progress-bar-min-qty, .fsn-progress-qty', $this );

		$pbstyle = $pb->addControlSection('pb_style_sec', __('Bar Config', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.fsn-progress-bar';

		$pbstyle->addStyleControl([
			'name' 		=> __('Initial Background Color', "oxyultimate-woo"),
			'selector' 	=> $selector,
			'property' 	=> 'background-color',
			'default' 	=> '#dacece'
		]);

		$pbstyle->addStyleControl([
			'name' 		=> __('Active Background Color', "oxyultimate-woo"),
			'selector' 	=> $selector . ' .fsn-progress-bar-res',
			'property' 	=> 'background-color',
			'default' 	=> '#f73a3a'
		]);

		$pbstyle->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'height',
			"control_type" 	=> 'slider-measurebox',
			'default' 	=> 5
		])->setRange(0, 30, 1)->setUnits("px", "px,em,rem");

		$pbstyle->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-top'
		])->setParam('hide_wrapper_end', true);

		$pbstyle->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-right',
			'default' 	=> 10
		])->setParam('hide_wrapper_start', true);

		$pbstyle->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-bottom'
		])->setParam('hide_wrapper_end', true);

		$pbstyle->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-left',
			'default' 	=> 10
		])->setParam('hide_wrapper_start', true);

		$pb->borderSection( __('Bar Borders', "oxyultimate-woo"), $selector, $this );
	}


	function controls() {

		$this->generalConfig();

		//* Price Section
		$this->typographySection( __('Price', "woocommerce"), '.woocommerce-Price-amount', $this);

		
		//* Quantity Section
		$this->typographySection( __('Quantity', "woocommerce"), '.remaining-qty', $this);


		//* Text Section
		$msg = $this->addControlSection( 'text_section', __('Message', "oxyultimate-woo"), "assets/icon.png", $this );

		$msg->typographySection( __('Typography'), '.free-shipping-content' , $this);


		//*	Spacing
		$spacing = $msg->addControlSection('fsnmsg_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"fst_padding",
			__("Padding"),
			'.free-shipping-content'
		)->whiteList();
		$spacing->addPreset(
			"margin",
			"fst_margin",
			__("Margin"),
			'.free-shipping-content'
		)->whiteList();

		//* Icon
		$icon = $msg->addControlSection('fsnmsg_icon', __('Icon'), 'assets/icon.png', $this );

		$selector = '.free-shipping-content svg.fsnmsg-icon';

		$icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxyultimate-woo"),
				"slug" 			=> 'fsnmsg_icon'
			)
		)->rebuildElementOnChange();

		$icon->addStyleControl(
			array(
				"name" 			=> __('Size', "oxyultimate-woo"),
				"slug" 			=> "fsnmsg_icon_size",
				"selector" 		=> $selector,
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)
		->setRange(10, 50, 2)
		->setUnits("px", "px");

		$icon->addStyleControl(
			array(
				"selector" 		=> $selector,
				"property" 		=> 'color'
			)
		);

		$icon->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-top'
		])->setParam('hide_wrapper_end', true);

		$icon->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-right',
			'default' 	=> 6
		])->setParam('hide_wrapper_start', true);

		$icon->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-bottom'
		])->setParam('hide_wrapper_end', true);

		$icon->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> 'margin-left'
		])->setParam('hide_wrapper_start', true);

		//* Child Elements Layout
		$childElements = $msg->addControlSection('fsnmsg_childlayout', __('Child Elements Layout'), 'assets/icon.png', $this);
		$childElements->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> 'p',
			'property' 			=> 'display',
			'value' 			=> ['flex' => 'flex', 'inline-flex' => 'inline-flex', 'block' => 'block', 'inline-block' => 'inline-block'],
			'default' 			=> 'flex'
		]);
		$childElements->flex('p', $this);

		//* Button
		$this->button();


		//* Animation
		$this->animation();


		//* Child Elements Layout
		$childElements = $this->addControlSection('ouwoofs_childlayout', __('Child Elements Layout'), 'assets/icon.png', $this);
		$childElements->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> ' ',
			'property' 			=> 'display',
			'value' 			=> ['flex' => 'flex', 'inline-flex' => 'inline-flex'],
			'default' 			=> 'flex'
		]);
		$childElements->flex(' ', $this);

		//* Progress Bar
		$this->progress_bar_controls();
	}

	function render( $options, $defaults, $content ) {
		$this->comp_options[] = $options;

		$message_type = isset($options['msg_type']) ? $options['msg_type'] : 'amount';
		$price_update = isset($options['update_price']) ? $options['update_price'] : 'no';
		$update_qty = isset($options['update_qty']) ? $options['update_qty'] : 'no';

		if( $message_type == 'amount' ) {
			
			if( ! isset( $options['min_amount'] ) )
				return;

			add_filter( 'woocommerce_price_trim_zeros', array( $this, 'ouwoo_fsn_price_trim_zeros' ) );

			$this->minimum_amount_notice( $options );

			remove_filter( 'woocommerce_price_trim_zeros', array( $this, 'ouwoo_fsn_price_trim_zeros' ) );

		} elseif( $message_type == 'quantity' ) {
			
			if( ! isset( $options['required_qty'] ) )
				return;

			$this->quantity_notice( $options );
		}

		add_filter('body_class', array($this, 'ouwoo_fs_body_class') );

		if( ! $this->js_added && ( $price_update == "yes" || $update_qty == 'yes' ) ) {
			$this->js_added = true;
			add_action( 'wp_footer', array( $this, 'ouwoo_js_output' ) );
		}
	}

	function ouwoo_fsn_price_trim_zeros( $bool ) {
		return true;
	}

	function minimum_amount_notice( $options ) {
		$order_amt = wp_kses_post( $options['min_amount'] );
		$exclude_coupons = isset( $options['exclude_coupons'] ) ? $options['exclude_coupons'] : "no";
		$threshold_amt = isset( $options['threshold_amount'] ) ? wp_kses_post( $options['threshold_amount'] ) : '';
		$price_update = isset($options['update_price']) ? $options['update_price'] : 'no';

		$cart_total = $this->cart_total( $exclude_coupons );
		$amount = ( $cart_total < $order_amt ) ? wc_price( $order_amt - $cart_total ) : wc_price( $cart_total );
		$enable_animation = isset($options['enable_animation']) ? $options['enable_animation'] : 'no';
		$fade_speed = isset($options['fade_speed']) ? $options['fade_speed'] : 1500;
		$outer_wrap_sel = isset($options['outer_wrap_sel']) ? ' data-wrapsel="'. $options['outer_wrap_sel'] . '"' : '';

		if( isset($options['minamt_message']) ) {
			$message = isset($options['minamt_message']) ? wp_kses_post($options['minamt_message']) : 'Add {remaining_amount} to your cart in order to receive free shipping!';
			$message = str_replace("{remaining_amount}", $amount, $message);
		} elseif( isset($options['before_text']) || isset($options['after_text']) ) {
			$message = wp_kses_post($options['before_text']) . ' ' . $amount . ' ' . wp_kses_post($options['after_text']);
		} else {
			//*
		}

		

		$notice = $toggleclass = $svgIcon = '';

		if( isset( $options['fsnmsg_icon'] ) ) {
			global $oxygen_svg_icons_to_load;

			$oxygen_svg_icons_to_load[] = $options['fsnmsg_icon'];
			$svgIcon = '<svg id="' . $options['selector'] . '-fsnmsg-icon" class="fsnmsg-icon"><use xlink:href="#' . $options['fsnmsg_icon'] . '"></use></svg>';
		}

		if( isset($options['after_action'] ) && $options['after_action'] != 'hide' ) {
			$after_action = ' data-after-action=showmsg';
			$notice = isset($options['fs_notice']) ? '<span class="fs-aftermsg">' . wp_kses_post($options['fs_notice']) . '</span>' : '';
			if( $cart_total >= $order_amt )
				$toggleclass = ' hide-defaultmsg';
		} else {
			$after_action = ' data-after-action=hidemsg';
		}

		$fstext = '<p class="free-shipping-content' . $toggleclass . '" data-fs-msgtype="amount" data-fsamount="' . $order_amt . '" data-ouwoofs-animation="'. $enable_animation .'" data-fade-speed="'. $fade_speed .'" data-exclude-coupons="'.$exclude_coupons.'" data-threshold-amt="'. $threshold_amt .'"' . $after_action . $outer_wrap_sel .'>' . $svgIcon . '<span class="fs-defaultmsg">' . apply_filters( 'ouwoo_free_shipping_notice_minimum_amount', $message, $options ) . '</span>' . $notice . '</p>';

		if ( $cart_total < $order_amt || $price_update == "yes" ) {

			echo $fstext;

			if( isset($options['button_text']) ) {
				$btn_url = isset($options['btn_url']) ? esc_url( $options['btn_url'] ) : '';
				echo '<a href="' . $btn_url .'" class="call-to-action ouwoo-fs-btn ' . $toggleclass . '" role="button">' . wp_kses_post($options['button_text']) . 
					'</a>';
			}
		}

		if( $cart_total >= $order_amt && $price_update !== "yes" && isset($options['after_action']) && $options['after_action'] != 'hide' ) {
			echo $fstext;
		}

		$showpb = isset($options['fsn_display_pb']) ? $options['fsn_display_pb'] : 'no';
		$pbshowprice = isset($options['pb_show_price']) ? $options['pb_show_price'] : 'yes';
		if( $showpb == 'yes' ) {
			$pbres = ($cart_total < $order_amt) ? ceil( ( $cart_total / $order_amt ) * 100 ) : 100;
	?>
		<div class="fsn-progress-bar-wrap">
			<?php if( $pbshowprice == 'yes' ): ?>
				<span class="fsn-progress-bar-min-price"><?php echo wc_price(0) ; ?></span>
			<?php endif; ?>
			<div class="fsn-progress-bar">
				<div class="fsn-progress-bar-res" style="width: <?php echo $pbres; ?>%;"></div>
			</div>
			<?php if( $pbshowprice == 'yes' ): ?>
				<span class="fsn-progress-amount"><?php echo wc_price( $order_amt ); ?></span>
			<?php endif; ?>
		</div>
	<?php
		}
	}

	function quantity_notice( $options ) {

		$required_qty = absint( wp_kses_post( $options['required_qty'] ) );
		$threshold_qty = isset( $options['threshold_qty'] ) ? wp_kses_post( $options['threshold_qty'] ) : '';
		$update_qty = isset($options['update_qty']) ? $options['update_qty'] : 'no';
		$outer_wrap_sel = isset($options['outer_wrap_sel']) ? ' data-wrapsel="'. $options['outer_wrap_sel'] . '"' : '';

		$cart_qty = ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ? 0 : WC()->cart->get_cart_contents_count();
		$remaining_qty = ( $cart_qty < $required_qty ) ? ( $required_qty - $cart_qty ) : $cart_qty ;		

		$notice = $toggleclass = '';
		if( isset($options['after_action']) && $options['after_action'] != 'hide' ) {
			$after_action = ' data-after-action="showmsg"';
			$notice = isset($options['fs_notice']) ? '<span class="fs-aftermsg">' . wp_kses_post($options['fs_notice']) . '</span>' : '';
			if( $cart_qty >= $required_qty )
				$toggleclass = ' hide-defaultmsg';
		} else {
			$after_action = ' data-after-action="hidemsg"';
		}		

		$enable_animation = isset($options['enable_animation']) ? $options['enable_animation'] : 'no';
		$fade_speed = isset($options['fade_speed']) ? $options['fade_speed'] : 1500;

		$message = isset($options['qty_message']) ? wp_kses_post($options['qty_message']) : 'Add {remaining_quantity} into your cart in order to receive free shipping!';
		$message = str_replace("{remaining_quantity}", '<span class="remaining-qty">' . $remaining_qty . '</span>', $message);

		$svgIcon = '';
		if( isset( $options['fsnmsg_icon'] ) ) {
			global $oxygen_svg_icons_to_load;

			$oxygen_svg_icons_to_load[] = $options['fsnmsg_icon'];
			$svgIcon = '<svg id="' . $options['selector'] . '-fsnmsg-icon" class="fsnmsg-icon"><use xlink:href="#' . $options['fsnmsg_icon'] . '"></use></svg>';
		}

		$fstext = '<p class="free-shipping-content' . $toggleclass . '" data-fs-msgtype="quantity" data-fsqty="' . $required_qty . '" data-ouwoofs-animation="'. $enable_animation .'" data-fade-speed="'. $fade_speed .'" data-threshold-qty="'. $threshold_qty .'"' . $after_action . $outer_wrap_sel .'>' . $svgIcon . '<span class="fs-defaultmsg">' . apply_filters( 'ouwoo_free_shipping_notice_cart_quantity', $message, $options ) . '</span>' . $notice . '</p>';

		if ( $cart_qty < $required_qty || $update_qty == "yes" ) {

			echo $fstext;

			if( isset($options['button_text']) ) {
				$btn_url = isset($options['btn_url']) ? esc_url( $options['btn_url'] ) : '';
				echo '<a href="' . $btn_url .'" class="call-to-action ouwoo-fs-btn' . $toggleclass . '" role="button">' . wp_kses_post($options['button_text']) . 
					'</a>';
			}
		}

		if( $cart_qty >= $required_qty && $update_qty !== "yes" && isset($options['after_action']) && $options['after_action'] != 'hide' ) {
			echo $fstext;
		}

		$showpb = isset($options['fsn_display_pb']) ? $options['fsn_display_pb'] : 'no';
		$pbshowprice = isset($options['pb_show_price']) ? $options['pb_show_price'] : 'yes';
		if( $showpb == 'yes' ) {
			$pbres = ($cart_qty < $required_qty) ? ceil( ( $cart_qty / $required_qty ) * 100 ) : 100;
	?>
		<div class="fsn-progress-bar-wrap">
			<?php if( $pbshowprice == 'yes' ): ?>
				<span class="fsn-progress-bar-min-qty">0</span>
			<?php endif; ?>
			<div class="fsn-progress-bar">
				<div class="fsn-progress-bar-res" style="width: <?php echo $pbres; ?>%;"></div>
			</div>
			<?php if( $pbshowprice == 'yes' ): ?>
				<span class="fsn-progress-qty"><?php echo $required_qty; ?></span>
			<?php endif; ?>
		</div>
	<?php
		}
	}

	public static function cart_total( $exclude_coupons = "no") {

		$cart_total = 0;

		if ( is_null( WC()->cart ) || WC()->cart->is_empty() ) {
			return $cart_total;
		}

		/*if( is_admin() ) {
			$cart_total = WC()->cart->get_subtotal();
		} else {
			if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
				$cart_total = ( ! WC()->cart->prices_include_tax ) ? WC()->cart->subtotal_ex_tax : WC()->cart->subtotal;
			} else {*/
				$cart_total = ( 'incl' === WC()->cart->get_tax_price_display_mode() ) ? WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() : WC()->cart->get_subtotal();
			/*}
		}*/

		if ( $exclude_coupons == "yes" ) {
			$coupons = WC()->cart->get_applied_coupons();

			if ( $coupons ) {
				$coupons_total_amount = 0;
				foreach ( $coupons as $coupon ) {
					$coupons_total_amount += WC()->cart->get_coupon_discount_amount( $coupon, WC()->cart->display_cart_ex_tax );
				}
				$cart_total -= ( $coupons_total_amount );
			}
		}

		return $cart_total;
	}

	function ouwoo_fs_body_class( $classes ) {
		if( $this->isBuilderEditorActive() )
			return $classes;
		
		foreach( $this->comp_options as $options ) {
			$message_type = isset($options['msg_type']) ? $options['msg_type'] : 'amount';
			$display_fstext = isset($options['after_action']) ? $options['after_action'] : 'hide';

			if( $message_type == 'amount' ) {
				$order_amt = wp_kses_post( $options['min_amount'] );
				$exclude_coupons = isset($options['exclude_coupons']) ? $options['exclude_coupons'] : "no";		
				$threshold_amt = isset( $options['threshold_amount'] ) ? wp_kses_post( $options['threshold_amount'] ) : '';

				$cart_total = $this->cart_total( $exclude_coupons );

				if( ( $threshold_amt != '' && $threshold_amt > $cart_total ) || ( $cart_total >= $order_amt ) ) {
					if( isset($options['outer_wrap_sel']) ) {
						$compsel = str_replace( array( '#', '.'), '' , $options['outer_wrap_sel'] );
					} else {
						$compsel = $options['selector'];
					}

					$classes[] = $compsel;
				}

				if( $cart_total >= $order_amt && $display_fstext != 'hide' ) {
					$classes[] = $compsel . '-hide-fstxt';
				}
			}

			if( $message_type == 'quantity' ) {
				$required_qty = wp_kses_post( $options['required_qty'] );
				$threshold_qty = isset( $options['threshold_qty'] ) ? wp_kses_post( $options['threshold_qty'] ) : '';

				$cart_qty = ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ? 0 : WC()->cart->get_cart_contents_count();

				if( ( $threshold_qty != '' && $threshold_qty > $cart_qty ) || ( $cart_qty >= $required_qty ) ) {
					if( isset($options['outer_wrap_sel']) ) {
						$compsel = str_replace( array( '#', '.'), '' , $options['outer_wrap_sel'] );
					} else {
						$compsel = $options['selector'];
					}

					$classes[] = $compsel;
				}

				if( $cart_qty >= $required_qty && $display_fstext != 'hide' ) {
					$classes[] = $compsel . '-hide-fstxt';
				}
			}
		}

		return $classes;
	}

	function ouwoo_js_output() {
		wp_enqueue_script(
			'ouwoo-free-shipping',
			OUWOO_URL . 'assets/js/ouwoo-fsn.min.js',
			array(),
			filemtime( OUWOO_DIR . 'assets/js/ouwoo-fsn.min.js' ),
			true
		);
	}

	function customCSS( $original, $selector ) {
		$css = '';
		$selector_class = str_replace( '#', '', $selector);
		
		if( ! $this->css_added ) {
			$this->css_added = true;

			$css .= '.oxy-free-shipping-notice {
						display: flex; 
						flex-direction: row; 
						align-items: center; 
						-webkit-transition: all 0.15s ease-in-out; 
						-moz-transition: all 0.15s ease-in-out; 
						transition: all 0.15s ease-in-out;
						position: relative;
						flex-wrap: wrap;
						--fsn-progress-bar-width: 100%;
					}
					.oxy-free-shipping-notice p { padding: 0; margin: 0; display: flex; align-items: center;}
					.oxy-free-shipping-notice .call-to-action {
						margin-left: 10px; 
						text-decoration: none; 
						-webkit-transition: all 0.2s ease; 
						-moz-transition: all 0.2s ease; 
						transition: all 0.2s ease; 
						opacity: 1;
					}
					.oxy-free-shipping-notice .remaining-qty { 
						font-weight: bold;
					}
					.oxy-free-shipping-notice .free-shipping-content:not(.hide-defaultmsg) .fs-aftermsg,
					.oxy-free-shipping-notice .free-shipping-content.hide-defaultmsg .fs-defaultmsg,
					.oxy-free-shipping-notice .call-to-action.hide-defaultmsg {
						display: none;
					}
					.fsn-progress-bar-wrap {
						display: -webkit-box;
						display: -ms-flexbox;
						display: flex;
						-webkit-box-align: center;
						-ms-flex-align: center;
						align-items: center;
						justify-content: center;
						flex: 0 0 var(--fsn-progress-bar-width);
					}
					.fsn-progress-bar {
						background: #dacece;
						display: -webkit-box;
						display: -ms-flexbox;
						display: flex;
						-webkit-box-flex: 1;
						-ms-flex: 1;
						flex: 1;
						height: 5px;
						margin: 0 10px;
						overflow: hidden;
					}
					.fsn-progress-bar-res {
						background: #f73a3a;
						height: 100%;
						-webkit-transition: width .4s ease-in-out;
						transition: width .4s ease-in-out;
						width: 0;
					}
					.fsn-progress-bar-min-price .woocommerce-Price-amount,
					.fsn-progress-amount .woocommerce-Price-amount {
						font-weight: 400;
					}
					.free-shipping-content svg.fsnmsg-icon {
						width: 20px;
						height: 20px;
						fill: currentColor;
						margin-right: 6px;
					}
					';
		}

		$css .= 'body.' . $selector_class . ':not(.' . $selector_class . '-hide-fstxt) ' . $selector .' {
					height: 0!important;
					padding:0!important;
					margin:0!important;
				}
				body.' . $selector_class . ':not(.' . $selector_class . '-hide-fstxt) ' . $selector .' .ouwoo-fs-btn, 
				body.' . $selector_class . ':not(.' . $selector_class . '-hide-fstxt) ' . $selector .' .free-shipping-content {
					opacity: 0;
				}';

		$prefix = $this->El->get_tag();

		if( isset( $original[ $prefix . '_outer_wrap_sel'] ) ) {
			$wrapper_selector = str_replace( array( '#', '.'), '', $original[ $prefix . '_outer_wrap_sel'] );

			$css .= $original[ $prefix . '_outer_wrap_sel'] .'{
						-webkit-transition: all 0.15s ease-in-out; 
						-moz-transition: all 0.15s ease-in-out; 
						transition: all 0.15s ease-in-out;
						position: relative;
					}
					body.' . $wrapper_selector . ':not(.' . $wrapper_selector . '-hide-fstxt) ' . $original[ $prefix . '_outer_wrap_sel'] .' {
						height: 0;
						padding:0!important;
						margin:0!important;
					}
					body.' . $wrapper_selector . ':not(.' . $wrapper_selector . '-hide-fstxt) ' . $original[ $prefix . '_outer_wrap_sel'] .' > * {
						opacity: 0;
					}';
		}

		return $css;
	}
}

new OUWooFreeShippingMsg();

add_action( 'wp_ajax_ouwoo_update_free_shipping_qty', 'ouwoo_update_free_shipping_qty' );
add_action( 'wp_ajax_nopriv_ouwoo_update_free_shipping_qty', 'ouwoo_update_free_shipping_qty' );
function ouwoo_update_free_shipping_qty() {
	//check_ajax_referer( 'ouwoo-free-shipping', 'security' );

	$reqqty = $_POST['reqqty'];
	$threshold_qty = $_POST['thqty'];
	$cart_qty = ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ? 0 : WC()->cart->get_cart_contents_count();
	$pbres = ($cart_qty < $reqqty) ? ceil( ( $cart_qty / $reqqty ) * 100 ) : 100;

	if( ! empty($threshold_qty) && $threshold_qty > $cart_qty) {
		wp_send_json( array(
			'remaining_qty' 	=> 'false',
			'threshold' 		=> 'true',
			'pbres' 	=> $pbres
		));
	}

	if ( $cart_qty < $reqqty ) {
		$data = array(
			'remaining_qty' => '<span class="remaining-qty">' . ( $reqqty - $cart_qty ) . '</span>',
			'threshold' 	=> 'false',
			'pbres' 	=> $pbres
		);
	} else {
		$data = array(
			'remaining_qty' 	=> 'false',
			'threshold' 		=> 'false',
			'pbres' 	=> $pbres
		);
	}

	wp_send_json($data);
}

add_action( 'wp_ajax_ouwoo_update_free_shipping_amount', 'ouwoo_update_free_shipping_amount' );
add_action( 'wp_ajax_nopriv_ouwoo_update_free_shipping_amount', 'ouwoo_update_free_shipping_amount' );
function ouwoo_update_free_shipping_amount() {
	//check_ajax_referer( 'ouwoo-free-shipping', 'security' );

	$price = $_POST['price'];
	$exclude_coupons = $_POST['excl_cp'];
	$threshold_amount = $_POST['thamt'];
	$order_amount = OUWooFreeShippingMsg::cart_total( $exclude_coupons );
	$pbres = ($order_amount < $price) ? ceil( ( $order_amount / $price ) * 100 ) : 100;

	if( ! empty($threshold_amount) && $threshold_amount > $order_amount) {
		wp_send_json( array(
			'amount' 	=> 'false',
			'threshold' => 'true',
			'pbres' 	=> $pbres
		));
	}

	if ( $order_amount < $price ) {
		$data = array(
			'amount' 	=> wc_price( $price - $order_amount ),
			'threshold' => 'false',
			'pbres' 	=> $pbres
		);
	} else {
		$data = array(
			'amount' 	=> 'false',
			'threshold' => 'false',
			'pbres' 	=> $pbres
		);
	}

	wp_send_json($data);
}