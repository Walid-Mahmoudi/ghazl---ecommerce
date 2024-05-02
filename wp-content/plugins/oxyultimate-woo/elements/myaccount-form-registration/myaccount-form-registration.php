<?php

class OUWooRegistrationForm extends UltimateWooEl {
	public $css_added = false;

	function name() {
		return __( "Registration", "oxyultimate-woo" );
	}

	function slug() {
		return "ouwoo_registration";
	}

	function ouwoo_button_place() {
		return "myaccount";
	}

	function button_priority() {
		return '2';
	}

	function formContainer() {
		$selector = '.woocommerce-form-register.register';

		$container = $this->addControlSection('container_section', __('Container', "oxyultimate-woo"), "assets/icon.png", $this);

		$container->addStyleControl(
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		);

		$spacing = $container->addControlSection('formbox_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"cnt_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"cnt_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Borders
		$container->borderSection(__('Border'), $selector, $this);

		//* Box Shadow
		$container->boxShadowSection(__('Box Shadow'), $selector, $this);
	}

	function formFieldsLabel() {
		$selector = '.form-row label';

		$label = $this->addControlSection('labels_section', __('Fields Label', "oxyultimate-woo"), "assets/icon.png", $this);

		$label->typographySection(__('Typography'), $selector, $this);

		$label->addStyleControl([
			'name' 			=> __('Asterisk Color', "oxyultimate-woo"),
			'selector' 		=> $selector . ' .required',
			'property' 		=> 'color'
		]);

		$label->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'margin-bottom',
			'control_type' 	=> 'slider-measurebox'
		])->setRange(0, 20, 1);		
	}

	function formInputFields() {
		$selector = '.form-row .input-text';

		$input = $this->addControlSection('inp_section', __('Input Fields', "oxyultimate-woo"), "assets/icon.png", $this);

		//* Padding & Margin
		$spacing = $input->addControlSection('inpsp_section', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"inpsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"inpsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Placeholder Text
		$placeholder = $input->addControlSection('placeholdder_section', __('Placeholder', "oxyultimate-woo"), "assets/icon.png", $this);
		$placeholder->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Text for Username Field'),
			'slug' 	=> 'placeholder_username'
		]);

		$placeholder->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Text for Email Field'),
			'slug' 	=> 'placeholder_email'
		]);

		$placeholder->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Text for Password Field'),
			'slug' 	=> 'placeholder_password'
		]);

		$placeholder->addStyleControls([
			[
				'selector' 		=> $selector . "::placeholder",
				'property' 		=> 'color'
			]
		]);

		$input->typographySection(__('Typography'), $selector, $this);

		//* Colors
		$colors = $input->addControlSection('inpclr_section', __('Colors', "oxyultimate-woo"), "assets/icon.png", $this);
		$colors->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Background Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ":focus",
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Text Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ":focus",
				'property' 		=> 'color'
			]
		]);

		//* Borders
		$input->borderSection(__('Border'), $selector, $this);
		$input->borderSection(__('Focus Border', "oxyultimate-woo"), $selector . ":focus", $this);

		//* Box Shadow
		$input->boxShadowSection(__('Box Shadow'), $selector, $this);
		$input->boxShadowSection(__('Focus Shadow', "oxyultimate-woo"), $selector . ":focus", $this);
	}

	function formPasswordNotice() {
		$passwordNotice = $this->addControlSection('password_section', __('Password Info', "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = '.password-notice';

		$passwordNotice->addOptionControl(
			array(
				"type" 			=> "textfield",
				"name" 			=> __('Alert Message', "oxyultimate-woo"),
				"slug" 			=> 'rgfrm_pass_msg',
				'default' 		=> __( 'A password will be sent to your email address.', 'woocommerce' )
			)
		)->setParam('description', __('This message will show when password field would be disabled. Click on Apply Params button to see the changes.', 'oxyultimate-woo'));

		$passwordNotice->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$passwordNotice->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'width'
		]);

		//* Padding & Margin
		$spacing = $passwordNotice->addControlSection('pnsp_section', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"pnsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"pnsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$passwordNotice->typographySection(__('Typography'), $selector, $this);
		$passwordNotice->borderSection(__('Border'), $selector, $this);
	}

	function formPrivacyPolicyText() {
		$pp = $this->addControlSection('pp_section', __('Privacy Policy', "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = '.woocommerce-privacy-policy-text p';

		$hide_privacy = $pp->addControl('buttons-list', 'hide_privacy', __('Hide Privacy Text?', "oxyultimate-woo"));
		$hide_privacy->setValue(['No', 'Yes']);
		$hide_privacy->setValueCSS([
			'Yes' => '.woocommerce-privacy-policy-text{display: none;}',
			'No' => '.woocommerce-privacy-policy-text{display: block;}'
		]);
		$hide_privacy->setDefaultValue('No');

		$pp->addStyleControl([
			'selector' 		=> '.woocommerce-privacy-policy-text',
			'property' 		=> 'background-color'
		]);

		//* Padding & Margin
		$spacing = $pp->addControlSection('ppsp_section', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"ppsp_padding",
			__("Padding"),
			'.woocommerce-privacy-policy-text'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"ppsp_margin",
			__("Margin"),
			'.woocommerce-privacy-policy-text'
		)->whiteList();

		$pp->typographySection(__('Typography'), $selector, $this);

		$link = $pp->addControlSection('pplnk_section', __('Link Color', "oxyultimate-woo"), "assets/icon.png", $this);
		$selector = '.woocommerce-privacy-policy-text p a';
		$link->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Hover Color', "oxyultimate-woo"),
				'selector' 		=> $selector . ":hover",
				'property' 		=> 'color'
			]
		]);

		$pp->borderSection(__('Border'), '.woocommerce-privacy-policy-text', $this);
	}

	function buttonSettings( ) {
		$button = $this->addControlSection( 'button_section', __('Button'), "assets/icon.png", $this );

		$selector = '.woocommerce-form-register__submit';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text', "oxyultimate-woo"),
			'slug' 		=> 'button_text',
			'default' 	=> __('Register', 'woocommerce')
		])->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

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
		$button->borderSection(__('Hover Border', "oxyultimate-woo"), $selector . ":hover", $this);

		//* Box Shadow
		$button->boxShadowSection(__('Box Shadow'), $selector, $this);
		$button->boxShadowSection(__('Hover Shadow', "oxyultimate-woo"), $selector . ":hover", $this);
	}

	function errorNotice() {
		$error = $this->addControlSection('error_section', __('Error Notice', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.woocommerce-error';

		$error->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$spacing = $error->addControlSection('error_sp', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"errsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"errsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$error->typographySection( __('Typography'), $selector, $this );

		$error->typographySection( __('Bold Text', "oxyultimate-woo"), $selector . ' strong', $this );

		$error->borderSection( __('Borders'), $selector, $this );
		$error->boxShadowSection( __('Box Shadow'), $selector, $this );

		$icon = $error->addControlSection('error_icon', __('Icon'), 'assets/icon.png', $this );
		$hide_icon = $icon->addControl('buttons-list', 'hide_icon', __('Hide Icon?', "oxyultimate-woo"));
		$hide_icon->setValue(['No', 'Yes']);
		$hide_icon->setValueCSS([
			'No' => $selector . '::before{display: inline-block;}', 
			'Yes' => $selector . '::before{display: none;}']);
		$hide_icon->setDefaultValue('No');

		$icon->addStyleControl([
			'selector' 		=> $selector . '::before',
			'property' 		=> 'color'
		]);
	}

	function controls() {

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Redirect URL', "oxyultimate-woo"),
			'slug' 		=> 'redirect_url',
			'value' 	=> [
				'current' 	=> __('Current Page', "oxyultimate-woo"),
				'myaccount' => __('My Account Page', "oxyultimate-woo"),
				'custom' 	=> __('Custom URL', "oxyultimate-woo")
			],
			'default' 	=> 'myaccount'
		])->setParam('description', "Site will redirect to selected URL after registration.");

		$redirect_url = $this->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouwoo_registration_custom_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouwoo_registration_custom_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ouwoo_registration_custom_url\');iframeScope.checkResizeBoxOptions(\'oxy-ouwoo_registration_custom_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ouwoo_registration_custom_url\')">set</div>
			</div>
			',
			"custom_url"
		);
		$redirect_url->setParam( 'heading', __('URL') );
		$redirect_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouwoo_registration_redirect_url']=='custom'" );

		$this->formContainer();

		$this->formFieldsLabel();

		$this->formInputFields();

		$this->formPasswordNotice();

		$this->formPrivacyPolicyText();

		$this->buttonSettings();

		$this->errorNotice();
	}

	function render( $options, $defaults, $content ) {

		if( is_user_logged_in() && ! $this->isBuilderEditorActive() )
			return;

		if( isset( $_POST['register_action'] ) && $_POST['register_action'] == 'do_register') {

			$message = apply_filters( 'woocommerce_my_account_message', '' );

			if ( ! empty( $message ) ) {
				wc_add_notice( $message );
			}

			woocommerce_output_all_notices();
		}

		$button_text = ( isset( $options['button_text'] ) ) ? wp_kses_post( $options['button_text'] ) : esc_attr( 'Register', 'woocommerce' );
		//* Placeholder Text
		$username_ptext = ( isset( $options['placeholder_username'] ) ) ? 'placeholder="' . wp_kses_post( $options['placeholder_username'] ) .'"' : '';
		$email_ptext = ( isset( $options['placeholder_email'] ) ) ? 'placeholder="' . wp_kses_post( $options['placeholder_email'] ) .'"' : '';
		$password_ptext = ( isset( $options['placeholder_password'] ) ) ? 'placeholder="' . wp_kses_post( $options['placeholder_password'] ) .'"' : '';
	?>
		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" <?php echo $username_ptext; ?> /><?php // @codingStandardsIgnoreLine ?>
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" <?php echo $email_ptext; ?> /><?php // @codingStandardsIgnoreLine ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" <?php echo $password_ptext; ?> />
				</p>

			<?php else : ?>

				<?php $msg = isset( $options['rgfrm_pass_msg'] ) ? wp_kses_post( $options['rgfrm_pass_msg'] ) : __( 'A password will be sent to your email address.', 'woocommerce' ); ?>
				<p class="password-notice"><?php echo $msg; ?></p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-form-row form-row register-button">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<?php 
					if( isset( $options['redirect_url'] ) && $options['redirect_url'] == 'custom' ) {
						$custom_url = ! empty($options['custom_url']) ? wp_kses_post( $options['custom_url'] ) : get_permalink();
					} elseif( isset( $options['redirect_url'] ) && $options['redirect_url'] == 'current' ) {
						$custom_url = get_permalink();
					}  else {
						$custom_url = wc_get_page_permalink( 'myaccount' );
					}
				?>
				<input type="hidden" name="redirect" value="<?php echo esc_url( $custom_url ); ?>">
				<input type="hidden" name="register_action" value="do_register">
				<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php echo $button_text; ?>"><?php echo $button_text; ?></button>				
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>
	<?php
		if( ! is_account_page() ){
			wp_enqueue_script( 'wc-password-strength-meter' );
		}
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$css ='.oxy-ouwoo-registration {width: 100%; min-height: 40px;}
					.oxy-ouwoo-registration form.register {
						border: none;
						padding: 0;
						margin: 0;
						border-radius: 0;
						width: 100%;
					}';

			$this->css_added = true;
			
			return $css;
		}
	}
}

new OUWooRegistrationForm();