<?php

class OUCheckoutCouponForm extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Coupon Box", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_checkout_coupon_form";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 2;
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_checkout_coupon_form-elements-label"
				ng-if="isActiveName('oxy-ou_checkout_coupon_form')&&!hasOpenTabs('oxy-ou_checkout_coupon_form')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_checkout_coupon_form-elements"
				ng-if="isActiveName('oxy-ou_checkout_coupon_form')&&!hasOpenTabs('oxy-ou_checkout_coupon_form')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 62 );
	}


	/***************************
	 * Coupon Toggle
	 **************************/
	function toggleButton() {
		$toggle_btn = $this->addControlSection('toggle_button', __('Link Box', "oxyultimate-woo"), 'assets/icon.png', $this);

		$hide_toggle_section = $toggle_btn->addControl('buttons-list', 'hide_coupon_toggle', __('Hide', "oxyultimate-woo"));
		$hide_toggle_section->setValue(['No', 'Yes']);
		$hide_toggle_section->setValueCSS(['Yes' => '.woocommerce-form-coupon-toggle{display: none;}']);
		$hide_toggle_section->setDefaultValue('No');
		$hide_toggle_section->whiteList();

		$toggle_btn->addStyleControl(
			[
				'selector' 		=> '.woocommerce-info',
				'property' 		=> 'background-color'
			]
		);

		$toggle_btn->addStyleControl(
			[
				'name' 			=> __('Icon Color', "oxyultimate-woo"),
				'selector' 		=> '.woocommerce-info::before',
				'property' 		=> 'color'
			]
		);

		/*******************************
		 * Spacing
		 ********************************/
		$spacing = $toggle_btn->addControlSection('toggle_box', __('Box Spacing', "oxyultimate-woo"), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"ctoggle_padding",
			__("Padding"),
			'.woocommerce-info'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"ctoggle_margin",
			__("Margin"),
			'.woocommerce-info'
		)->whiteList();

		$font = $toggle_btn->typographySection( __('Typography'), '.woocommerce-form-coupon-toggle .woocommerce-info', $this );
		$font->addStyleControls([
			[
				'name' 			=> __('Link Color', "oxyultimate-woo"),
				'selector' 		=> 'a.showcoupon',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Link Color on Hover', "oxyultimate-woo"),
				'selector' 		=> 'a.showcoupon:hover',
				'property' 		=> 'color'
			]
		]);

		$toggle_btn->borderSection( __('Border'), '.woocommerce-info', $this );
		$toggle_btn->boxShadowSection( __('Box Shadow'), '.woocommerce-info', $this );
	}

	function couponForm() {
		$form = $this->addControlSection('coupon_form', __('Form Box', "oxyultimate-woo"), 'assets/icon.png', $this);

		$selector = '.checkout_coupon.woocommerce-form-coupon';

		$preview = $form->addControl('buttons-list', 'preview_form', __('Preview on Builder Editor', "oxyultimate-woo"));
		$preview->setValue(['Enable', 'Disable']);
		$preview->setValueCSS([
			'Enable' => $selector.'.builder-editor-on{display: block!important; height: auto!important;}',
			'Disable' => $selector.'.builder-editor-on{display: none!important; height: 0!important;}'
		]);
		$preview->setDefaultValue('Enable');
		$preview->whiteList();

		$show_form = $form->addControl('buttons-list', 'show_form', __('Always Show on Page Load', "oxyultimate-woo"));
		$show_form->setValue(['No', 'Yes']);
		$show_form->setValueCSS(['Yes' => $selector. '{display: block!important;}']);
		$show_form->setDefaultValue('No');
		$show_form->whiteList();

		$form->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		/*******************************
		 * Spacing
		 ********************************/
		$spacing = $form->addControlSection('form_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"cform_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cform_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$form->typographySection(__('Text Font', "oxyultimate-woo"), $selector . ' p:not(.form-row)', $this );

		$form->borderSection( __('Box Border'), $selector, $this );
		$form->boxShadowSection( __('Box Shadow'), $selector, $this );


		/************************
		 * Input Field
		 ***********************/
		$input = $this->addControlSection('form_input', __('Input Field', "oxyultimate-woo"), 'assets/icon.png', $this );
		$selector = '.checkout_coupon .form-row .input-text';

		$input->typographySection(__('Typography'), $selector, $this );
		
		$color = $input->addControlSection('input_color', __('Color'), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Background Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ':focus',
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Text Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ':focus',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Placeholder Color', "oxyultimate-woo"),
				'selector' 		=> $selector . '::placeholder',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Focus Border Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ':focus',
				'property' 		=> 'border-color'
			],
		]);

		$spacing = $input->addControlSection('inp_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"inp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$input->borderSection(__('Border'), $selector, $this );
		$input->boxShadowSection(__('Box Shadow'), $selector, $this );


		/************************
		 * Button
		 ***********************/
		$button = $this->addControlSection('form_button', __('Button'), 'assets/icon.png', $this );
		$selector = '.checkout_coupon .form-row .button';

		$color = $button->typographySection(__('Font & Color', "oxyultimate-woo"), $selector, $this );
		$color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Background Color on hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ":hover",
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Text Color on Hover', "oxyultimate-woo"),
				'selector' 		=> $selector . ":hover",
				'property' 		=> 'color'
			]
		]);

		$spacing = $button->addControlSection('button_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"button_padding",
			__("Padding"),
			$selector
		)->whiteList();		

		$button->borderSection(__('Border'), $selector, $this );
		$button->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ':hover', $this );

		$button->boxShadowSection(__('Box Shadow'), $selector, $this );
		$button->boxShadowSection(__('Hover Box Shadow', "oxyultimate-woo"), $selector . ':hover', $this );
	}

	function controls() {
		$this->toggleButton();

		$this->couponForm();
	}

	function render($options, $defaults, $content ) {
		if( function_exists('wc_print_notice') ) {
			woocommerce_checkout_coupon_form();

			if( $this->isBuilderEditorActive() ) {
				$this->El->builderInlineJS("
					jQuery(document).ready(function($){
						setTimeout(function(){
							$('form.checkout_coupon').addClass('builder-editor-on');
						}, 10);
					});
				");
			}
		}
	}


	function customCSS($original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-checkout-coupon-form{width: 100%; min-height: 40px;}
			.oxy-ou-checkout-coupon-form form.checkout_coupon .button {
				line-height: 1.2;
				text-align: center;
				background: #ffffff;
				border: 1px solid #65bec2;
				text-transform: uppercase;
				letter-spacing: 1px;
				font-weight: 700;
				font-size: 12px;
				color: #65bec2;
				padding: 16px 32px;
				outline: none;
			}

			body.oxygen-builder-body a.showcoupon{pointer-events:none;}

			body.oxygen-builder-body .checkout_coupon.woocommerce-form-coupon,
			.builder-editor-on {
				display: block!important;
				height: auto!important;
			}

			.oxy-ou-checkout-coupon-form form.checkout_coupon #coupon_code {
				padding: 14px 12px;
			}

			.oxy-ou-checkout-coupon-form .woocommerce-info a.showcoupon:hover{
				text-decoration: none;
			}

			.oxy-ou-checkout-coupon-form form.checkout_coupon p {
				margin-top: 0;
				padding: 0;
			}

			.oxy-ou-checkout-coupon-form .form-row-first {
				width: calc(50% - 12px);
			}

			.oxy-ou-checkout-coupon-form form .form-row-last {
				float: left;
				margin-left: 12px;
				width: 50%;
			}';

			$this->css_added = true;
		}
		
		return $css;
	}
}

new OUCheckoutCouponForm();