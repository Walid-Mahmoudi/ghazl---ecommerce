<?php

class OUWooLoginForm extends UltimateWooEl {
	public $css_added = false;
	public $has_js = true;
	public $js_added = false;
	public $login_success_msg = '';

	function name() {
		return __( "Login", "oxyultimate-woo" );
	}

	function slug() {
		return "ouwoo_login";
	}

	function ouwoo_button_place() {
		return "myaccount";
	}

	function button_priority() {
		return '1';
	}

	function buttonSettings( ) {
		$button = $this->addControlSection( 'button_section', __('Button'), "assets/icon.png", $this );

		$selector = '.woocommerce-form-login__submit';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Button Text', "oxyultimate-woo"),
			'slug' 		=> 'button_text',
			'default' 	=> __('Log In', 'woocommerce')
		])->setParam('description', 'Click on Apply Params button and apply the changes.', "oxyultimate-woo");

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


	/************************
	 * Container
	 ***********************/
	function formContainer() {
		$selector = '.woocommerce-form-login.login';

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

	/************************
	 * Fields Label
	 ***********************/
	function formFieldsLabel() {
		$selector = '.form-row label:not(.woocommerce-form-login__rememberme)';

		$label = $this->addControlSection('labels_section', __('Fields Label', "oxyultimate-woo"), "assets/icon.png", $this);

		$hide_label = $label->addControl('buttons-list', 'hide_label', __('Hide Labels?', "oxyultimate-woo"));
		$hide_label->setValue(['No', 'Yes']);
		$hide_label->setValueCSS([
			'Yes' => $selector . '{display: none;}',
			'No' => $selector . '{display: block;}'
		]);
		$hide_label->setDefaultValue('No');

		$label->addStyleControl([
			'name' 			=> __('Asterisk Color', "oxyultimate-woo"),
			'selector' 		=> $selector . ' .required',
			'property' 		=> 'color'
		]);

		$label->addStyleControl([
			'name' 			=> __('Gap Between Label & Fields', "oxyultimate-woo"),
			'selector' 		=> $selector,
			'property' 		=> 'margin-bottom',
			'control_type' 	=> 'slider-measurebox'
		])->setRange(0, 20, 1);

		$label->typographySection(__('Typography'), $selector, $this);
	}


	/************************
	 * Input Fields
	 ***********************/
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

		$spacing->addStyleControl([
			'name' 			=> __('Gap Between Fields', "oxyultimate-woo"),
			'selector' 		=> '.form-row-wide',
			'property' 		=> 'margin-bottom',
			'control_type' 	=> 'slider-measurebox'
		])->setRange(0, 20, 1);

		//* Placeholder Text
		$placeholder = $input->addControlSection('placeholdder_section', __('Placeholder', "oxyultimate-woo"), "assets/icon.png", $this);
		$placeholder->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Text for Username Field'),
			'slug' 	=> 'placeholder_username'
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


	/************************
	 * Input Icon
	 ***********************/
	function formInputIcon() {
		$icon = $this->addControlSection('inpicon_sec', __('Input Icon', "oxyultimate-woo"), "assets/icon.png", $this);

		$icon->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Display Icon', 'oxyultimate-woo'),
			'slug' 		=> 'add_inp_icon',
			'value' 	=> ['no' => __('No', "oxyultimate-woo"), 'yes' => __('Yes')],
			'default' 	=> 'no'
		])->rebuildElementOnChange();

		$icon_pos = $icon->addControl('buttons-list', 'hide_pos', __('Position', "oxyultimate-woo"));
		$icon_pos->setValue(['Left', 'Right']);
		$icon_pos->setValueCSS([
			'Left' => '.icon-wrap{flex-direction: row;}',
			'Right' => '.icon-wrap{flex-direction: row-reverse;}'
		]);
		$icon_pos->setDefaultValue('Left');

		$user = $icon->addControlSection('usericon_section', __('Username Field', "oxyultimate-woo"), "assets/icon.png", $this);
		$user->addOptionControl([
			'type' 		=> 'icon_finder',
			'name' 		=> __('Icon For Username Field', "oxyultimate-woo"),
			'slug' 		=> 'icon_user'
		]);

		$pass = $icon->addControlSection('passicon_section', __('Password Field', "oxyultimate-woo"), "assets/icon.png", $this);
		$pass->addOptionControl([
			'type' 		=> 'icon_finder',
			'name' 		=> __('Icon For Password Field', "oxyultimate-woo"),
			'slug' 		=> 'icon_pass'
		]);

		$colors = $icon->addControlSection('icons_color', __('Color & Size', "oxyultimate-woo"), "assets/icon.png", $this);
		$colors->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Wrapper Width', "oxyultimate-woo"),
			'property' 		=> 'width',
			'selector' 		=> '.icon-wrap .user-icon, .icon-wrap .pass-icon'
		])->setUnits('px', 'px');

		$colors->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Icon Size', "oxyultimate-woo"),
			'property' 		=> 'width|height',
			'selector' 		=> '.icon-wrap svg'
		])->setUnits('px', 'px');

		$colors->addStyleControls([
			[
				'selector' 	=> '.icon-wrap svg',
				'property' 	=> 'color'
			],
			[
				'selector' 	=> '.icon-wrap .user-icon, .icon-wrap .pass-icon',
				'property' 	=> 'background-color'
			]
		]);

		$icon->borderSection( __('Border'), '.icon-wrap .user-icon, .icon-wrap .pass-icon', $this);
	}

	/************************
	 * Remember Me
	 ***********************/
	function remembermeCheckbox() {
		$rememberme = $this->addControlSection('remember_me', __('Remember Me', "oxyultimate-woo"), 'assets/icon.png', $this );

		$hide_cb = $rememberme->addControl('buttons-list', 'hide_cb', __('Hide This Checkbox?', "oxyultimate-woo"));
		$hide_cb->setValue(['No', 'Yes']);
		$hide_cb->setValueCSS(['Yes' => '.woocommerce-form-login__rememberme{display: none;}']);
		$hide_cb->setDefaultValue('No');
		$hide_cb->whiteList();

		$rememberme->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text'),
			'slug' 		=> 'remember_me_text'
		])->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

		$rememberme->addStyleControl([
			'name' 			=> __('Gap Between Checkbox & Text', "oxyultimate-woo"),
			'selector' 		=> '.woocommerce-form-login__rememberme span',
			'property' 		=> 'margin-left',
			'control_type' 	=> 'slider-measurebox',
		])->setUnits('px', 'px,%,em,vw')->setRange(0,100,1);

		$rememberme->typographySection(__('Text', "oxyultimate-woo"), '.woocommerce-form-login__rememberme span', $this );

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
	}


	/************************
	 * Lost Password
	 ***********************/
	function lostPassword() {

		$lost_password = $this->addControlSection('lostpwd_section', __('Lost Password', "oxyultimate-woo"), 'assets/icon.png', $this );

		$hide_lost_pass = $lost_password->addControl('buttons-list', 'hide_lost_pass', __('Disable It?', "oxyultimate-woo"));
		$hide_lost_pass->setValue(['No', 'Yes']);
		$hide_lost_pass->setValueCSS(['Yes' => '.lost_password a{display: none;}']);
		$hide_lost_pass->setDefaultValue('No');

		$lost_password->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text'),
			'slug' 		=> 'lostpwd_text'
		])->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

		$lost_password->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __('Lost Your Password Form Page', "oxyultimate-woo"),
			'slug' 	=> 'lost_pass',
			'value' => ['default' => __('Default'), 'custom' => __('Custom Page')],
			'default' => 'default'
		]);

		$lost_pass_url = $lost_password->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouwoo_login_lost_pass_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouwoo_login_lost_pass_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ouwoo_login_lost_pass_url\');iframeScope.checkResizeBoxOptions(\'oxy-ouwoo_login_lost_pass_url\');" placeholder="https://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ouwoo_login_lost_pass_url\')">set</div>
			</div>
			',
			"ouwoo_loss_pass_url"
		);
		$lost_pass_url->setParam( 'heading', __('Page URL', "oxyultimate-woo") );
		$lost_pass_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouwoo_login_lost_pass']=='custom'" );

		$tg = $lost_password->typographySection(__('Typography'), '.lost_password, .lost_password a', $this );
		$tg->addStyleControl([
			'name' 			=> __('Color on Hover', "oxyultimate-woo"),
			'selector' 		=> '.lost_password a:hover',
			'property' 		=> 'color'
		]);
	}


	/************************
	 * General Config
	 ***********************/
	function generalConfig() {
		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Redirect URL(after login)', "oxyultimate-woo"),
			'slug' 		=> 'redirect_url',
			'value' 	=> [
				'current' 	=> __('Current Page', "oxyultimate-woo"),
				'referrer' 	=> __('HTTP Referer', "oxyultimate-woo"),
				'myaccount' => __('My Account Page', "oxyultimate-woo"),
				'custom' 	=> __('Custom URL', "oxyultimate-woo")
			],
			'default' 	=> 'myaccount'
		])->setParam('description', __("Site will redirect to selected URL after login.", "oxyultimate-woo"));

		$redirect_url = $this->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouwoo_login_custom_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouwoo_login_custom_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ouwoo_login_custom_url\');iframeScope.checkResizeBoxOptions(\'oxy-ouwoo_login_custom_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ouwoo_login_custom_url\')">set</div>
			</div>
			',
			"custom_url"
		);
		$redirect_url->setParam( 'heading', __('URL') );
		$redirect_url->setParam( 'ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouwoo_login_redirect_url']=='custom'" );

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Process Login', "oxyultimate-woo"),
			'slug' 		=> 'login_action',
			'value' 	=> [ 'submit' => __('On Submit', "oxyultimate-woo"), "ajax" => __('By AJAX', "oxyultimate-woo") ],
			'default' 	=> 'submit'
		]);

		/*$logout_url = $this->addCustomControl(
			'<div class="oxygen-file-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouwoo_login_logout_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouwoo_login_logout_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ouwoo_login_logout_url\');iframeScope.checkResizeBoxOptions(\'oxy-ouwoo_login_logout_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ouwoo_login_logout_url\')">set</div>
			</div>
			',
			"logout_url"
		);
		$logout_url->setParam( 'heading', __('Redirect URL(after logout)') );
		$logout_url->setParam( 'description', "Site will redirect to this URL when use will logged out. Default is My Account page." );*/
	}


	/************************
	 * Error Notcies
	 ***********************/
	function errorNotice() {
		$error = $this->addControlSection('error_section', __('Notices', "oxyultimate-woo"), 'assets/icon.png', $this );

		$selector = '.woocommerce-error';

		$error->addStyleControl([
			'selector' 		=> $selector,
			'property' 		=> 'background-color'
		]);

		$error->addOptionControl([
			'type' 		=> 'textarea',
			'name' 		=> __('Login Success Message', "oxyultimate-woo"),
			'slug' 		=> 'login_success',
			'default' 	=> __('You logged in successfully. Do not close the page. We are redirecting now.'),
			'base64' 	=> true,
			'condition' => 'login_action=ajax'
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

		$this->generalConfig();

		$this->formContainer();

		$this->formFieldsLabel();

		$this->formInputFields();

		$this->formInputIcon();

		$this->remembermeCheckbox();

		$this->lostPassword();

		$this->buttonSettings();

		$this->errorNotice();
	}

	function render($options, $defaults, $content) {
		if( is_user_logged_in() && ! $this->isBuilderEditorActive() )
			return;
		
		global $oxygen_svg_icons_to_load;

		$remember_me_text = ( isset( $options['remember_me_text'] ) ) ? wp_kses_post( $options['remember_me_text'] ) : esc_html( 'Remember me', 'woocommerce' );

		$button_text = ( isset( $options['button_text'] ) ) ? wp_kses_post( $options['button_text'] ) : esc_attr( 'Log in', 'woocommerce' );

		$lostpwd_text = ( isset( $options['lostpwd_text'] ) ) ? wp_kses_post( $options['lostpwd_text'] ) : esc_html( 'Lost your password?', 'woocommerce' );

		//* Placeholder Text
		$username_ptext = ( isset( $options['placeholder_username'] ) ) ? ' placeholder="' . wp_kses_post( $options['placeholder_username'] ) .'"' : '';

		$password_ptext = ( isset( $options['placeholder_password'] ) ) ? ' placeholder="' . wp_kses_post( $options['placeholder_password'] ) .'"' : '';

		$this->login_success_msg = ( isset( $options['login_success'] ) ) ? wp_kses_post( $options['login_success'] ) : '';

		//* WC Notices
		if( isset( $_POST['login_action'] ) && $_POST['login_action'] == 'do_login' ) {

			$message = apply_filters( 'woocommerce_my_account_message', '' );

			if ( ! empty( $message ) ) {
				wc_add_notice( $message );
			}

			woocommerce_output_all_notices();
		}

		$class = (isset( $options['login_action'] ) && $options['login_action'] == 'ajax') ? ' woocommerce-form-login__ajax' : '';

		//do_action( 'woocommerce_before_customer_login_form' );
	?>
		<form class="woocommerce-form woocommerce-form-login login" method="post">

			<?php if(isset( $options['login_action'] ) && $options['login_action'] == 'ajax'): ?>
				<div class="woocommerce-notices-wrapper"></div>
			<?php endif; ?>

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username" class="label-username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>

				<?php 
					if( isset($options['icon_user']) && isset($options['add_inp_icon']) && $options['add_inp_icon'] == 'yes') : 
						$oxygen_svg_icons_to_load[] = $options['icon_user'];
						$iconUser = $options['icon_user'];
				?>
				<span class="icon-wrap">
					<span class="user-icon">
						<svg id="<?php echo $options['selector']; ?>-user-icon">
							<use xlink:href="#<?php echo $iconUser; ?>"></use>
						</svg>
					</span>
				<?php endif; ?>

					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"<?php echo $username_ptext; ?> autocomplete="off" /><?php // @codingStandardsIgnoreLine ?>
				
				<?php  if( isset($options['icon_user']) && isset($options['add_inp_icon']) && $options['add_inp_icon'] == 'yes') : ?>
					</span>
				<?php endif; ?>

			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password" class="label-pass"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>

				<?php 
					if( isset($options['icon_pass']) && isset($options['add_inp_icon']) && $options['add_inp_icon'] == 'yes' ) : 
						$oxygen_svg_icons_to_load[] = $options['icon_pass'];
						$iconPass = $options['icon_pass'];
				?>
				<span class="icon-wrap">
					<span class="pass-icon">
						<svg id="<?php echo $options['selector']; ?>-pass-icon">
							<use xlink:href="#<?php echo $iconPass; ?>"></use>
						</svg>
					</span>
				<?php endif; ?>

				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password"<?php echo $password_ptext; ?> autocomplete="off" />

				<?php  if( isset($options['icon_pass']) && isset($options['add_inp_icon']) && $options['add_inp_icon'] == 'yes') : ?>
					</span>
				<?php endif; ?>

			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row form-row-wide rm-lp-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php echo $remember_me_text; ?></span>
				</label>

				<?php
					$lost_pass = isset($options['lost_pass']) ? $options['lost_pass'] : 'default';
					$lost_pass_url = ($lost_pass == 'default') ? wp_lostpassword_url() : ( isset($options['lost_pass_url']) ? $options['lost_pass_url'] : wp_lostpassword_url() );
				?>
				<span class="woocommerce-LostPassword lost_password">
					<a href="<?php echo esc_url( $lost_pass_url ); ?>"><?php echo $lostpwd_text; ?></a>
				</span>
			</p>

			<p class="form-row">
				<input type="hidden" name="login_action" value="do_login">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<?php 
					if( isset( $options['redirect_url'] ) && $options['redirect_url'] !== 'referrer' ) {
						$rediect_url = '';

						if( $options['redirect_url'] == 'custom' ) {
							$rediect_url = ! empty($options['custom_url']) ? wp_kses_post( $options['custom_url'] ) : wc_get_page_permalink( 'myaccount' );	
						} elseif( $options['redirect_url'] == 'current' ) {
							$rediect_url = get_permalink();
						} else {
							$rediect_url = wc_get_page_permalink( 'myaccount' );
						}
						if( $rediect_url !== '' ): 
				?>
						<input type="hidden" name="redirect" value="<?php echo esc_url( $rediect_url ); ?>">
				<?php
						endif;
					}
				?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo $class; ?>" name="login" value="<?php echo $button_text; ?>"><?php echo $button_text; ?></button>				
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>
	<?php

		if( ! $this->js_added && isset( $options['login_action'] ) && $options['login_action'] == 'ajax' ) {
			$this->js_added = true;
			add_action( 'wp_footer', array( $this, 'ouwoo_ajax_login_script' ) );
			remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'process_login' ), 20 );
		}
		do_action( 'woocommerce_after_customer_login_form' );
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$css = '.oxy-ouwoo-login {width: 100%; min-height: 40px;}
				.oxy-ouwoo-login .woocommerce-form-login.login {
					border: none;
					box-shadow: none;
					padding: 0;
					margin: 0;
					width: 100%;
				}
				.oxy-ouwoo-login .form-row label {
					margin-top: 0;
				}
				.oxy-ouwoo-login .form-row .input-text:focus {
					outline: 0;
					box-shadow: none;
				}
				.oxy-ouwoo-login .woocommerce-form-login label.woocommerce-form__label.woocommerce-form__label-for-checkbox.woocommerce-form-login__rememberme {
					display: block;
					margin: 0;
				}
				.rm-lp-row {
					display: flex;
					flex-direction: row;
					align-items: center;
				}
				.rm-lp-row * {
					font-size: 14px;
					flex-grow: 1;
				}
				.rm-lp-row .woocommerce-LostPassword {
					text-align: right;
				}
				.oxy-ouwoo-login .icon-wrap {
					display: flex;
					flex-direction: row;
					align-items: stretch;
				}
				.oxy-ouwoo-login .icon-wrap input,
				.oxy-ouwoo-login .icon-wrap .password-input {
					-webkit-flex-grow: 1;
					-moz-flex-grow: 1;
					flex-grow: 1;
					width: 100%;
				}
				.oxy-ouwoo-login .icon-wrap .user-icon,
				.oxy-ouwoo-login .icon-wrap .pass-icon {
					width: 50px;
					justify-content: center;
					align-items: center;
				}
				.oxy-ouwoo-login .icon-wrap svg {
					width: 20px;
					height: 20px;
					fill: currentColor;
				}
				.oxy-ouwoo-login .icon-wrap .user-icon,
				.oxy-ouwoo-login .icon-wrap .pass-icon,
				.oxy-ouwoo-login .icon-wrap > input,
				.oxy-ouwoo-login .icon-wrap .password-input {
					display: flex;
					flex-direction: row;
				}
				';

			$this->css_added = true;

			return $css;
		}
	}

	function ouwoo_ajax_login_script() {
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('.woocommerce-form-login__ajax').on('click submit', function(e){
					e.preventDefault();

					if ( typeof wc_add_to_cart_params === 'undefined' ) {
						return false;
					}

					var $thisbutton = $(e.currentTarget),
						$form = $thisbutton.closest('form.login'),
						loginData = $form.serializeArray();

					loginData.push({name: 'action', value: 'ouwoo_process_login'});

					$.ajax({
						type: 'post',
						url: wc_add_to_cart_params.ajax_url,
						data: $.param(loginData),
						dataType: "json",
						beforeSend: function (response) {
							$thisbutton.addClass('loading');
						},
						success: function (response) {
							$thisbutton.removeClass('loading');
							if (response.redirect && response.loggedin) {
								text = '<?php echo $this->login_success_msg; ?>';
								var $msg = '<ul class="woocommerce-error" role="alert"><li>'+ text +'</li></ul>';
								$form.find('.woocommerce-notices-wrapper').html($msg);

								window.location = response.redirect;
								return;
							} else {
								var $msg = '<ul class="woocommerce-error" role="alert"><li>'+ response.message +'</li></ul>';
								$form.find('.woocommerce-notices-wrapper').html($msg);
							}
						},
					});
				});
			});
		</script>
	<?php
	}
}

new OUWooLoginForm();

add_action('wp_ajax_nopriv_ouwoo_process_login', 'ouwoo_process_login');
function ouwoo_process_login() {
	$nonce_value = wc_get_var( $_REQUEST['woocommerce-login-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) );

	if ( empty( $_POST['username'] ) ) {
		wp_send_json( array( 'message' => '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . __( 'Username is required.', 'woocommerce' ) ) );
		//die();
	}

	if ( isset( $_POST['username'], $_POST['password'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
		try {
			$creds = array(
				'user_login'    => trim( wp_unslash( $_POST['username'] ) ),
				'user_password' => $_POST['password'],
				'remember'      => isset( $_POST['rememberme'] ),
			);

			if ( empty( $creds['user_login'] ) ) {
				wp_send_json( array( 'message' => '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . __( 'Username is required.', 'woocommerce' ) ) );
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// Perform the login.
			$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				wp_send_json( array( 'message' => $user->get_error_message() ) );
			} else {
				if ( ! empty( $_POST['redirect'] ) ) {
					$redirect = wp_unslash( $_POST['redirect'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				} elseif ( wc_get_raw_referer() ) {
					$redirect = wc_get_raw_referer();
				} else {
					$redirect = wc_get_page_permalink( 'myaccount' );
				}

				$data = array(
					'redirect' 	=> $redirect,
					'loggedin' 	=> "done"
				);

				wp_send_json( $data );			
			}
		} catch ( Exception $e ) {
			wp_send_json( array( 'message' => $e->getMessage() ) );
		}
	}

	//die();
}