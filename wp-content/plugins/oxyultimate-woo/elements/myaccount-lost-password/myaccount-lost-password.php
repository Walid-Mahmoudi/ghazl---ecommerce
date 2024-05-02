<?php

class OUWooLostPassword extends UltimateWooEl {
	public $css_added = false;
	public $error_messages = array( 'msg_1' => '', 'msg_2' => '', 'msg_3' => '');

	function name() {
		return __( "Lost Password", "oxyultimate-woo" );
	}

	function slug() {
		return "ouwoo_lost_password";
	}

	function ouwoo_button_place() {
		return "myaccount";
	}

	function button_priority() {
		return '3';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}

	function message_control() {
		$msg = $this->addControlSection( 'msg_text_section', __('Message'), "assets/icon.png", $this );

		$msg->addOptionControl([
			'type' 		=> 'textarea',
			'name' 		=> __('Text'),
			'slug' 		=> 'msg_text',
			'default' 	=> esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' )
		])->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

		$selector = 'p.form-text, .lost_reset_password p:first-child';

		//* Padding & Margin
		$spacing = $msg->addControlSection('msgsp_sec', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"msgsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		//* Font & Color
		$msg->typographySection(__('Typography'), $selector, $this);
	}

	/************************
	 * Container
	 ***********************/
	function formContainer() {
		$selector = 'form.woocommerce-ResetPassword.lost_reset_password';

		$container = $this->addControlSection('container_section', __('Form Wrapper', "oxyultimate-woo"), "assets/icon.png", $this);

		$container->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'width'
			],
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			]
		]);

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

	function label_control() {

		$label = $this->addControlSection('lbl_section', __('Field Label', "oxyultimate-woo"), "assets/icon.png", $this);

		$label->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text'),
			'slug' 		=> 'label_text',
			'default' 	=> esc_html__( 'Username or email', 'woocommerce' )
		])->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo"));

		$selector = '.lost_reset_password .form-row label';

		//* Padding & Margin
		$spacing = $label->addControlSection('lblsp_sec', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"margin",
			"lblsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Font & Color
		$labeltg = $label->typographySection(__('Typography'), $selector, $this);
		$labeltg->addStyleControl([
			'name' => __('Asterisk', "oxyultimate-woo"),
			'selector' => $selector . ' > .required',
			'property' => 'color'
		]);

		$labeltg->addStyleControl([
			'name' => __('Asterisk Size', "oxyultimate-woo"),
			'selector' => $selector . ' > .required',
			'property' => 'font-size'
		]);
	}

	function input_control() {
		$input = $this->addControlSection('inp_section', __('Input Field', "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = '.lost_reset_password .form-row .input-text';

		//* Padding & Margin
		$spacing = $input->addControlSection('inpsp_section', __('Spacing', "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"inpsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$input->typographySection(__('Typography'), $selector, $this);

		//* Colors
		$colors = $input->addControlSection('inpclr_section', __('Colors & Width', "oxyultimate-woo"), "assets/icon.png", $this);
		
		$colors->addStyleControl([
			'control_type'	=> 'slider-measurebox',
			'selector' 		=> $selector,
			'property' 		=> 'width'
		])->setRange(0, 500, 10);

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

	function button_control( ) {
		$button = $this->addControlSection( 'button_section', __('Button'), "assets/icon.png", $this );

		$selector = '.lost_reset_password .form-row .button';

		$button->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text', "oxyultimate-woo"),
			'slug' 		=> 'button_text',
			'default' 	=> __('Reset password', 'woocommerce')
		])->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo") );

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
		])->setRange(0, 500, 10);

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

	function error_control() {
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

	function confirmation_control() {
		$cnfm = $this->addControlSection('cnf_section', __('Confirmation', "oxyultimate-woo"), 'assets/icon.png', $this );

		$preview = $cnfm->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __('Builder Preview', "oxyultimate-woo"),
			'slug' 	=> 'builder_preview',
			'value' => ['no' => __('Disable'), 'yes' => __('Enable')],
			'default' => 'no'
		]);
		$preview->setParam('description', __('Click on Apply Params button and apply the changes.', "oxyultimate-woo") );
		$preview->rebuildElementOnChange();


		$cnfm->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">You can overwrite the default confirmation layout by nested elements.</div>'), 
			'lost_password_note'
		)->setParam('heading', 'Note:');
		
	}

	function controls() {
		$this->message_control();

		$this->formContainer();

		$this->label_control();

		$this->input_control();

		$this->button_control();

		$this->error_control();

		$this->confirmation_control();
	}

	function render($options, $default, $content ) {
		if ( ! empty( $_GET['reset-link-sent'] ) || ( $this->isBuilderEditorActive() && $options['builder_preview'] == "yes" ) ) {
			if( $content ) {
		?>
				<div class="oxy-inner-content">
					<?php
						if( function_exists('do_oxygen_elements') )
							echo do_oxygen_elements( $content );
						else
							echo do_shortcode( $content );
					?>
				</div>
		<?php
				return;
			} else {
				return wc_get_template( 'myaccount/lost-password-confirmation.php' );
			}

		} elseif ( ! empty( $_GET['show-reset-form'] ) ) {
			if ( isset( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ) && 0 < strpos( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ], ':' ) ) {
				list( $rp_id, $rp_key ) = array_map( 'wc_clean', explode( ':', wp_unslash( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ), 2 ) );
				$userdata               = get_userdata( absint( $rp_id ) );
				$rp_login               = $userdata ? $userdata->user_login : '';
				$user                   = WC_Shortcode_My_Account::check_password_reset_key( $rp_key, $rp_login );

				if ( is_object( $user ) ) {
					return wc_get_template(
						'myaccount/form-reset-password.php',
						array(
							'key'   => $rp_key,
							'login' => $rp_login,
						)
					);
				}
			}
		}

		$msg_text = isset($options['msg_text']) ? wp_kses_post( $options['msg_text'] ) : esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' );

		$label_text = isset($options['label_text']) ? wp_kses_post( $options['label_text'] ) : esc_html__( 'Username or email', 'woocommerce' );
		$button_text = isset($options['button_text']) ? wp_kses_post( $options['button_text'] ) : esc_html__( 'Reset password', 'woocommerce' );

		do_action( 'woocommerce_before_lost_password_form' );
		?>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password">

			<p class="form-text"><?php echo apply_filters( 'woocommerce_lost_password_message', $msg_text ); ?></p>

			<p class="woocommerce-form-row form-row">
				<label for="user_login"><?php echo $label_text; ?></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" />
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="woocommerce-Button button" value="<?php echo $button_text; ?>"><?php echo $button_text; ?></button>
			</p>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

		</form>

		<?php
		do_action( 'woocommerce_after_lost_password_form' );
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$css = ".oxy-ouwoo-lost-password {
						width: 480px;
					}
					.oxy-ouwoo-lost-password.woocommerce form.woocommerce-ResetPassword.lost_reset_password {
						background: transparent;
						border: none;
						border-radius: 0;
						max-width: inherit;
						padding: 0;
						box-shadow: none;
					}";

			$this->css_added = true;

			return $css;
		}
	}
}

new OUWooLostPassword();