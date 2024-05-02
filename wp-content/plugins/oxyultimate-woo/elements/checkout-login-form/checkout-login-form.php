<?php

class OUCheckoutLoginForm extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Login Box", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_checkout_login_form";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 3;
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_checkout_login_form-elements-label"
				ng-if="isActiveName('oxy-ou_checkout_login_form')&&!hasOpenTabs('oxy-ou_checkout_login_form')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_checkout_login_form-elements"
				ng-if="isActiveName('oxy-ou_checkout_login_form')&&!hasOpenTabs('oxy-ou_checkout_login_form')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 63 );
	}


	/*******************************
	 * Toggle Button
	 ********************************/
	function toggleButton() {
		$toggle_btn = $this->addControlSection('login_toggle_button', __('Link Box', "oxyultimate-woo"), 'assets/icon.png', $this);

		$hide_toggle_section = $toggle_btn->addControl('buttons-list', 'hide_login_toggle', __('Hide'));
		$hide_toggle_section->setValue(['No', 'Yes']);
		$hide_toggle_section->setValueCSS(['Yes' => '.woocommerce-form-login-toggle{display: none;}']);
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
			"lt_padding",
			__("Padding"),
			'.woocommerce-info'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"lt_margin",
			__("Margin"),
			'.woocommerce-info'
		)->whiteList();

		$font = $toggle_btn->typographySection( __('Typography'), '.woocommerce-form-login-toggle .woocommerce-info', $this );
		$font->addStyleControls([
			[
				'name' 			=> __('Link Color', "oxyultimate-woo"),
				'selector' 		=> 'a.showlogin',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Link Color on Hover', "oxyultimate-woo"),
				'selector' 		=> 'a.showlogin:hover',
				'property' 		=> 'color'
			]
		]);

		$toggle_btn->borderSection( __('Border'), '.woocommerce-info', $this );
		$toggle_btn->boxShadowSection( __('Box Shadow'), '.woocommerce-info', $this );
	}


	/*******************************
	 * Login Form
	 ********************************/
	function loginForm() {
		$form = $this->addControlSection('login_form_box', __('Form Box', "oxyultimate-woo"), 'assets/icon.png', $this);

		$selector = '.woocommerce-form-login.login';

		$preview = $form->addControl('buttons-list', 'preview_form', __('Preview on Builder Editor', "oxyultimate-woo"));
		$preview->setValue(['Enable', 'Disable']);
		$preview->setValueCSS([
			'Enable' => $selector.'.builder-editor-on{display: block!important; height: auto!important;}',
			'Disable' => $selector.'.builder-editor-on{display: none!important; height: 0!important;}'
		]);
		$preview->setDefaultValue('Enable');
		$preview->whiteList();

		$show_form = $form->addControl('buttons-list', 'show_login_form', __('Always Show on Page Load', "oxyultimate-woo"));
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
		 * Box Spacing
		 ********************************/
		$spacing = $form->addControlSection('formbox_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"fb_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"fb_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$form->borderSection( __('Box Border'), $selector, $this );
		$form->boxShadowSection( __('Box Shadow'), $selector, $this );


		/************************
		 * Notice
		 ***********************/
		$notice = $this->addControlSection('lf_notice', __('Text', "oxyultimate-woo"), 'assets/icon.png', $this);
		$notice->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Message', "oxyultimate-woo"),
			'slug' 		=> 'form_notice',
			'value' 	=> ['default' => __('Default', "oxyultimate-woo"), 'custom' => __('Custom', "oxyultimate-woo")],
			'default' 	=> 'default'
		]);

		$notice->addOptionControl([
			'type' 		=> 'textarea',
			'name' 		=> __('Custom Message', "oxyultimate-woo"),
			'slug' 		=> 'notice_custom',
			'condition' => 'form_notice=custom',
			'description' => __('Click on Apply Params button and apply the changes.', "oxyultimate-woo")
		]);

		$notice->typographySection(__('Typography'), $selector . ' p:not(.form-row)', $this);


		/************************
		 * Fields Label
		 ***********************/
		$label = $this->addControlSection('lf_fields_label', __('Fields Label', "oxyultimate-woo"), 'assets/icon.png', $this);

		$selector = 'form.woocommerce-form-login .form-row label';

		$label->typographySection(__('Typography'), $selector, $this);

		$spacing = $label->addControlSection('lf_label_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"lflabel_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"lflabel_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$asterix = $label->addControlSection('lf_label_asterix', __('Asterisk', "oxyultimate-woo"), 'assets/icon.png', $this );
		$asterix->addStyleControls([
			[
				'selector' 		=> $selector . ' .required',
				'property' 		=> 'color'
			],
			[
				'selector' 		=> $selector . ' .required',
				'property' 		=> 'font-size'
			]
		]);


		/************************
		 * Input Fields
		 ***********************/
		$input = $this->addControlSection('login_form_input', __('Input Fields', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = 'form.woocommerce-form-login .form-row .input-text';

		$layout = $input->addControlSection('fields_layout_section', __('Layout'), 'assets/icon.png', $this );
		$alignment = $layout->addControl('buttons-list', 'fields_layout', __('Layout'));
		$alignment->setValue(['Inline', 'Stack']);
		$alignment->setValueCSS([
			'Stack' => '
				form.woocommerce-form-login .form-row-first, 
				form.woocommerce-form-login .form-row-last{float:none;width:100%;}
			' 
		]);

		$layout->addStyleControl([
			'name' 			=> __('Gap Between Input Fields', "oxyultimate-woo"),
			'selector' 		=> ' ',
			'property' 		=> '--login-fields-gap',
			'control_type' 	=> 'slider-measurebox',
			'condition' 	=> 'fields_layout=Inline'
		])->setUnits('px', 'px,%,em,vw')->setRange(0,30,1)->setDefaultValue(12);

		$layout->addStyleControl([
			'name' 			=> __('Margin Bottom', "oxyultimate-woo"),
			'selector' 		=> 'form.woocommerce-form-login .form-row-first, form.woocommerce-form-login .form-row-last',
			'property' 		=> 'margin-bottom',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('px', 'px,%,em,vw')->setRange(0,30,1)->setDefaultValue(6);


		$input->typographySection(__('Typography'), $selector, $this );

		$color = $input->addControlSection('lf_input_color', __('Color'), 'assets/icon.png', $this );
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
				'selector' 		=> 'form.woocommerce-form-login .form-row .input-text::placeholder',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Focus Border Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ':focus',
				'property' 		=> 'border-color'
			],
		]);

		$spacing = $input->addControlSection('lf_inp_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"lfinp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$input->borderSection(__('Border'), $selector, $this );
		$input->boxShadowSection(__('Box Shadow'), $selector, $this );


		/************************
		 * Button
		 ***********************/
		$button = $this->addControlSection('lform_button', __('Button'), 'assets/icon.png', $this );
		$selector = '.woocommerce-form-login .form-row .button';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text', "oxyultimate-woo"),
			'slug' 		=> 'button_text',
			'description' => __('Click on Apply Params button and apply the changes.')
		]);

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

		$spacing = $button->addControlSection('lbutton_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"lbutton_padding",
			__("Padding"),
			$selector
		)->whiteList();		

		$button->borderSection(__('Border'), $selector, $this );
		$button->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ':hover', $this );

		$button->boxShadowSection(__('Box Shadow'), $selector, $this );
		$button->boxShadowSection(__('Hover Box Shadow', "oxyultimate-woo"), $selector . ':hover', $this );


		/************************
		 * Remember Me
		 ***********************/
		$rememberme = $this->addControlSection('remember_me', __('Remember Me',"woocommerce"), 'assets/icon.png', $this );

		$hide_cb = $rememberme->addControl('buttons-list', 'hide_cb', __('Hide This Checkbox?', "oxyultimate-woo"));
		$hide_cb->setValue(['No', 'Yes']);
		$hide_cb->setValueCSS(['Yes' => '.woocommerce-form-login__rememberme{display: none;}']);
		$hide_cb->setDefaultValue('No');
		$hide_cb->whiteList();

		$rememberme->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text'),
			'slug' 		=> 'remember_me_text',
			'description' => __('Click on Apply Params button and apply the changes.', "oxyultimate-woo")
		]);

		$rememberme->addStyleControl([
			'name' 			=> __('Gap Between Checkbox & Text', "oxyultimate-woo"),
			'selector' 		=> '.woocommerce-form-login__rememberme span',
			'property' 		=> 'margin-left',
			'control_type' 	=> 'slider-measurebox',
		])->setUnits('px', 'px,%,em,vw')->setRange(0,100,1);

		$cb = $rememberme->addControlSection('remember_checkbox', __('Checkbox'), 'assets/icon.png', $this );
		$cb->addStyleControl([
			'name' 			=> __('Size'),
			'selector' 		=> '.woocommerce-form-login__rememberme span:before',
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox',
		])->setUnits('px', 'px')->setRange(0,100,1);

		$cb->addStyleControl([
			'name' 			=> __('Position Left', "oxyultimate-woo"),
			'selector' 		=> '.woocommerce-form-login__rememberme span:before',
			'property' 		=> 'left',
			'control_type' 	=> 'slider-measurebox',
		])->setUnits('px', 'px')->setRange(0,100,1);

		$cb->addStyleControl([
			'name' 			=> __('Position Top', "oxyultimate-woo"),
			'selector' 		=> '.woocommerce-form-login__rememberme span:before',
			'property' 		=> 'top',
			'control_type' 	=> 'slider-measurebox',
		])->setUnits('px', 'px')->setRange(0,100,1);

		$rememberme->borderSection(__('Checkbox Border', "oxyultimate-woo"), '.woocommerce-form-login__rememberme span:before', $this );

		$spacing = $rememberme->addControlSection('remember_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"margin",
			"rm_margin",
			__("Margin"),
			'.woocommerce-form-login__rememberme'
		)->whiteList();

		$rememberme->typographySection(__('Typography'), '.woocommerce-form-login__rememberme span', $this );


		/************************
		 * Lost Password
		 ***********************/
		$lost_password = $this->typographySection(__('Lost Password',"woocommerce"), '.lost_password, .lost_password a', $this );
		$lost_password->addStyleControl([
			'name' 			=> __('Color on Hover'),
			'selector' 		=> '.lost_password a:hover',
			'property' 		=> 'color'
		]);

		$hide_lost_pass = $lost_password->addControl('buttons-list', 'hide_lost_pass', __('Disable It?', "oxyultimate-woo"));
		$hide_lost_pass->setValue(['No', 'Yes']);
		$hide_lost_pass->setValueCSS(['Yes' => '.lost_password a{display: none;}']);
		$hide_lost_pass->setDefaultValue('No');
		$hide_lost_pass->whiteList();
	}

	function controls() {
		$this->toggleButton();

		$this->loginForm();
	}

	function render( $options, $defaults, $content ) {
		if ( is_user_logged_in() && ! $this->isBuilderEditorActive() ) {
			return;
		}

		if( $options['form_notice'] == 'default' ) {
			$message = esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce' );
		} elseif( $options['form_notice'] == 'custom' ) {
			if( isset( $options['notice_custom'] ) )
				$message = wp_kses_post( $options['notice_custom'] );
		} else {
			$message = '';
		}

		$remember_me_text = ( isset( $options['remember_me_text'] ) ) ? wp_kses_post( $options['remember_me_text'] ) : esc_html( 'Remember me', 'woocommerce' );
		$button_text = ( isset( $options['button_text'] ) ) ? wp_kses_post( $options['button_text'] ) : esc_attr( 'Login', 'woocommerce' );

		if( ! is_checkout() ) {
			$redirect_url = get_permalink();
		} else {
			$redirect_url = wc_get_checkout_url();
		}


		$builderClass = ( $this->isBuilderEditorActive() ) ? ' builder-editor-on' : '';
		$hidden = ( $this->isBuilderEditorActive() ) ? false : true;

	?>
		<div class="woocommerce-form-login-toggle">
			<?php wc_print_notice( apply_filters( 'woocommerce_checkout_login_message', esc_html__( 'Returning customer?', 'woocommerce' ) ) . ' <a href="#" class="showlogin">' . esc_html__( 'Click here to login', 'woocommerce' ) . '</a>', 'notice' ); ?>
		</div>

		<form class="woocommerce-form woocommerce-form-login login<?php echo $builderClass; ?>" method="post" <?php echo ( $hidden ) ? 'style="display:none;"' : ''; ?>>

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<?php echo ( $message ) ? wpautop( wptexturize( $message ) ) : ''; // @codingStandardsIgnoreLine ?>

			<p class="form-row form-row-first">
				<label for="username"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" autocomplete="username" />
			</p>
			<p class="form-row form-row-last">
				<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" autocomplete="current-password" />
			</p>
			<div class="clear"></div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php echo $remember_me_text; ?></span>
				</label>
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect_url ); ?>" />
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php echo $button_text; ?>"><?php echo $button_text; ?></button>
			</p>
			<p class="lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

	<?php
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-checkout-login-form {
				width: 100%;
				min-height: 40px;
				--login-fields-gap: 12px;
			}
			.oxy-ou-checkout-login-form form.woocommerce-form-login .form-row-first,
			.oxy-ou-checkout-login-form form.woocommerce-form-login .form-row-last {
				width: calc(50% - ( var(--login-fields-gap) / 2 ) );
			}
			.oxy-ou-checkout-login-form form.woocommerce-form-login .form-row-first {
				margin-right: calc(var(--login-fields-gap) / 2);
			}
			.oxy-ou-checkout-login-form form.woocommerce-form-login .form-row-last {
				margin-left: calc(var(--login-fields-gap) / 2);
			}
			.oxy-ou-checkout-login-form .woocommerce-form-login .woocommerce-form-login__rememberme {
				display: block;
				margin-bottom: 12px;
			}
			';

			$this->css_added = true;
		}

		return $css;
	}
}

new OUCheckoutLoginForm();