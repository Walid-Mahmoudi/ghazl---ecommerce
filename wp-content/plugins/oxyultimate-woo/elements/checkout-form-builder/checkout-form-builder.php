<?php

class OUBillingFormBuilder extends UltimateWooEl {
	public $css_added = false;
	public $js_added = false;

	function name() {
		return __( "Checkout Form Builder", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_billing_form_wrap";
	}

	function ouwoo_button_place() {
		return "checkout";
	}

	function button_priority() {
		return 4;
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_billing_form_wrap-elements-label"
				ng-if="isActiveName('oxy-ou_billing_form_wrap')&&!hasOpenTabs('oxy-ou_billing_form_wrap')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxygen"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_billing_form_wrap-elements"
				ng-if="isActiveName('oxy-ou_billing_form_wrap')&&!hasOpenTabs('oxy-ou_billing_form_wrap')">
				<?php do_action("oxygen_add_plus_ultimatewoo_checkout"); ?>
			</div>
		<?php }, 64 );
		if( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'ou_billing_form_wrap_script' ) );
		}
	}


	/************************
	 * Heading
	 ***********************/
	function formHeading() {
		$heading = $this->typographySection(__('Heading', "oxyultimate-woo"), '.woocommerce-billing-fields h3, .woocommerce-additional-fields h3' , $this);

		$hide_heading = $heading->addControl('buttons-list', 'hide_heading', __('Hide Billing Details Text', "oxyultimate-woo"));
		$hide_heading->setValue(['No', 'Yes']);
		$hide_heading->setValueCSS([
			'Yes' => '.woocommerce-billing-fields h3{display: none;}'
		]);
		$hide_heading->setDefaultValue('No');
		$hide_heading->whiteList();

		$hide_addinfo = $heading->addControl('buttons-list', 'hide_addinfo', __('Hide Additional information Text', "oxyultimate-woo"));
		$hide_addinfo->setValue(['No', 'Yes']);
		$hide_addinfo->setValueCSS([
			'Yes' => '.woocommerce-additional-fields h3{display: none;}'
		]);
		$hide_addinfo->setDefaultValue('No');
		$hide_addinfo->whiteList();
	}


	/************************
	 * Input Label
	 ***********************/
	function checkoutFormFieldsLabel() {
		$label = $this->addControlSection('fields_label', __('Fields Label', "oxyultimate-woo"), 'assets/icon.png', $this);

		$selector = 'form.woocommerce-checkout .form-row label';

		$label->typographySection(__('Typography'), $selector, $this);

		$hide = $label->addControl('buttons-list', 'hide_label', __('Hide Label', "oxyultimate-woo"));
		$hide->setValue(['No', 'Yes']);
		$hide->setValueCSS(['Yes' => $selector . '{display: none;}']);
		$hide->setDefaultValue('No');
		$hide->whiteList();	

		$spacing = $label->addControlSection('label_spacing', __('Spacing'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"label_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"label_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$asterix = $label->addControlSection('label_asterix', __('Asterisk', "oxyultimate-woo"), 'assets/icon.png', $this );
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

		$optional = $label->typographySection(__('Optional Text', "oxyultimate-woo"), $selector . ' .optional', $this);
		
		$hide = $optional->addControl('buttons-list', 'hide_optional_text', __('Hide Optional Text', "oxyultimate-woo"));
		$hide->setValue(['No', 'Yes']);
		$hide->setValueCSS(['Yes' => $selector . ' .optional{display: none;}']);
		$hide->setDefaultValue('No');
		$hide->whiteList();		
	}


	/************************
	 * Input Field
	 ***********************/
	function checkoutFormInputFields() {
		
		$input = $this->addControlSection('bform_input', __('Input Fields', "oxyultimate-woo"), 'assets/icon.png', $this );
		$selector = 'form.woocommerce-checkout .form-row .input-text, form.woocommerce-checkout .form-row .select2-container .select2-selection--single';
		$selector_focus = 'form.woocommerce-checkout .form-row .input-text:focus, form.woocommerce-checkout .form-row .select2-container .select2-selection--single:focus';

		$address2 = $input->addControl('buttons-list', 'hide_badd2', __('Remove Billing Address Line 2', "oxyultimate-woo"));
		$address2->setValue(['No', 'Yes']);
		$address2->setValueCSS(['Yes' => '#billing_address_2{display: none;}']);
		$address2->setDefaultValue('No');
		$address2->whiteList();

		$ship_address2 = $input->addControl('buttons-list', 'hide_shipadd2', __('Remove Shipping Address Line 2', "oxyultimate-woo"));
		$ship_address2->setValue(['No', 'Yes']);
		$ship_address2->setValueCSS(['Yes' => '#shipping_address_2{display: none;}']);
		$ship_address2->setDefaultValue('No');
		$ship_address2->whiteList();	

		$input->addStyleControl([
			'name' 			=> __('Gap Between Name Fields', "oxyultimate-woo"),
			'selector' 		=> ' ',
			'property' 		=> '--name-fields-gap',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('px', 'px,%,em,vw')->setRange(0,30,1)->setDefaultValue(12);

		$input->addStyleControl([
			'name' 			=> __('Textarea Height', "oxyultimate-woo"),
			'selector' 		=> 'form.woocommerce-checkout .form-row textarea',
			'property' 		=> 'height',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('em', 'px,%,em,vw')->setRange(0,30,1)->setDefaultValue(4);

		$input->typographySection(__('Typography'), $selector, $this );

		$color = $input->addControlSection('input_color', __('Color'), 'assets/icon.png', $this );
		$color->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Background Color', "oxyultimate-woo"),
				'selector' 		=> $selector_focus,
				'property' 		=> 'background-color'
			],
			[
				'name' 			=> __('Focus Text Color', "oxyultimate-woo"),
				'selector' 		=> $selector_focus,
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Placeholder Color', "oxyultimate-woo"),
				'selector' 		=> 'form.woocommerce-checkout .form-row .input-text::placeholder',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Focus Border Color', "oxyultimate-woo"),
				'selector' 		=> $selector_focus,
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
		$input->boxShadowSection(__('Focus Shadow', "oxyultimate-woo"), $selector_focus, $this );
	}


	/************************
	 * Dropdown Field
	 ***********************/
	function dropdownField() {
		$dropdown = $this->addControlSection( 'form_dorpdown', __('Dropdown', "oxyultimate-woo"), 'assets/icon.png', $this );

		$dropdown->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">' . __('Click on <span style="color:#ff7171;">Apply Params</span> button at below and see the changes on Builder editor.') . '</div>', 
			'description'
		)->setParam('heading', 'Note:');

		$dropdown->addOptionControl([
			'name' 			=> __('Background Color of Selected Option', "oxyultimate-woo"),
			'slug' 			=> 'selected_bgcolor',
			'type' 			=> 'colorpicker'
		]);

		$dropdown->addOptionControl([
			'name' 			=> __('Option Background Color on Hover', "oxyultimate-woo"),
			'slug' 			=> 'selected_hbgcolor',
			'type' 			=> 'colorpicker'
		]);
		
		$dropdown->addOptionControl([
			'name' 			=> __('Selected Option Color', "oxyultimate-woo"),
			'slug' 			=> 'selected_color',
			'type' 			=> 'colorpicker'
		]);
		
		$dropdown->addOptionControl([
			'name' 			=> __('Option Color on Hover', "oxyultimate-woo"),
			'slug' 			=> 'highlighted_color',
			'type' 			=> 'colorpicker'
		]);

		$textfield = $dropdown->addControlSection( 'dorpdown_textfield', __('Text Field', "oxyultimate-woo"), 'assets/icon.png', $this );

		$textfield->addOptionControl([
			'name' 			=> __('Background Color'),
			'slug' 			=> 'sfield_bgcolor',
			'type' 			=> 'colorpicker'
		]);

		$textfield->addOptionControl([
			'name' 			=> __('Focus Background Color', "oxyultimate-woo"),
			'slug' 			=> 'sfield_fbgcolor',
			'type' 			=> 'colorpicker'
		]);

		$textfield->addOptionControl([
			'name' 			=> __('Text Color', "oxyultimate-woo"),
			'slug' 			=> 'sfield_color',
			'type' 			=> 'colorpicker'
		]);

		$textfield->addOptionControl([
			'name' 			=> __('Focus Text Color', "oxyultimate-woo"),
			'slug' 			=> 'sfield_fcolor',
			'type' 			=> 'colorpicker'
		]);

		$textfield->addOptionControl([
			'name' 			=> __('Border Color'),
			'slug' 			=> 'sfield_bordercolor',
			'type' 			=> 'colorpicker'
		]);

		$textfield->addOptionControl([
			'name' 			=> __('Focus Border Color', "oxyultimate-woo"),
			'slug' 			=> 'sfield_focusbrdcolor',
			'type' 			=> 'colorpicker'
		]);

		$textfield->addOptionControl([
			'name' 			=> __('Border Width', "oxyultimate-woo"),
			'slug' 			=> 'sfield_brdwidth',
			'type' 			=> 'slider-measurebox',
			'default' 		=> 1
		])->setUnits('px', 'px,%,em');

		$textfield->addOptionControl([
			'name' 			=> __('Border Radius', "oxyultimate-woo"),
			'slug' 			=> 'sfield_brdradius',
			'type' 			=> 'slider-measurebox',
			'default' 		=> 4,
		])->setUnits('px', 'px,%,em');


		$arrow = $dropdown->addControlSection( 'dorpdown_arrow', __('Arrow', "oxyultimate-woo"), 'assets/icon.png', $this );

		$arrow_color = $arrow->addControl('buttons-list', 'arrow_color', __('Color'));
		$arrow_color->setValue(['Black', 'White']);
		$arrow_color->setValueCSS([
			'White' => '.select2-container--default .select2-selection--single .select2-selection__arrow{background-image:url('.OUWOO_URL.'elements/' . basename(__FILE__, '.php').'/chevron-down-white.svg)!important;}'
		]);
		$arrow_color->whiteList();

		$arrow->addStyleControl([
			'name' 			=> __('Size'),
			'selector' 		=> ' ',
			'property' 		=> '--select2-arrow-size',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('px', 'px')->setDefaultValue(10);
	}


	/************************
	 * Checkbox Field
	 ***********************/
	function checkboxField() {
		$checkbox = $this->addControlSection('input_checkbox', __('Checkbox'), 'assets/icon.png', $this );

		$checkbox->addStyleControl([
			'name' 			=> __('Size'),
			'selector' 		=> 'input[type="checkbox"]',
			'property' 		=> 'width|height',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('px', 'px,%,em,vw,vh')->setRange(0,100,1)->setDefaultValue(16);

		$checkbox->addStyleControl([
			'name' 			=> __('Gap Between Field and Label', "oxyultimate-woo"),
			'selector' 		=> '#ship-to-different-address .woocommerce-form__label-for-checkbox.checkbox span',
			'property' 		=> 'padding-left',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('px', 'px,%,em,vw,vh')->setRange(0,50,1)->setDefaultValue(8);

		$checkbox->typographySection(__('Typography'), '#ship-to-different-address .woocommerce-form__label-for-checkbox.checkbox span', $this );
	}

	function controls() {
		$this->formHeading();

		$this->checkoutFormFieldsLabel();

		$this->checkoutFormInputFields();

		$this->dropdownField();

		$this->checkboxField();
	}

	function render($options, $defaults, $content) {
		// Get checkout object.
		$checkout = WC()->checkout();

		// If checkout registration is disabled and not logged in, the user cannot checkout.
		if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
			echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
			return;
		}

	?>

		<form name="checkout" method="post" class="checkout woocommerce-checkout oxy-inner-content" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

			<?php if( $content ) {
				if( function_exists('do_oxygen_elements') )
					echo do_oxygen_elements( $content );
				else
					echo do_shortcode( $content );
			}?>

		</form>

	<?php
		do_action( 'woocommerce_after_checkout_form', $checkout );

		if( ! $this->js_added && ! $this->isBuilderEditorActive() ) {
			add_action( 'wp_footer', array( $this, 'ou_billing_form_wrap_script' ) );
			$this->js_added = true;
		}
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-ou-billing-form-wrap {
				width: 100%;
				min-height: 40px;
				--name-fields-gap: 12px;
				--select2-arrow-size: 10px;
			}
			.oxy-ou-billing-form-wrap form.woocommerce-checkout .form-row-first,
			.oxy-ou-billing-form-wrap form.woocommerce-checkout .form-row-last {
				width: calc(50% - ( var(--name-fields-gap) / 2 ) );
			}
			.oxy-ou-billing-form-wrap form.woocommerce-checkout .form-row-first {
				margin-right: calc(var(--name-fields-gap) / 2);
			}
			.oxy-ou-billing-form-wrap form.woocommerce-checkout .form-row-last {
				margin-left: calc(var(--name-fields-gap) / 2);
			}
			#ship-to-different-address .woocommerce-form__label-for-checkbox.checkbox {
				display: flex;
				flex-direction: row;
				align-items: center;
			}
			#ship-to-different-address .woocommerce-form__label-for-checkbox.checkbox span {
				padding-left: 8px;
			}
			.oxy-ou-billing-form-wrap input[type="checkbox"] {
				width: 16px;
				height: 16px;
			}
			.oxy-ou-billing-form-wrap .select2-container .select2-selection--single {
				border: 1px solid #d3ced2;
				height: auto;
			}
			.oxy-ou-billing-form-wrap select:focus,
			.oxy-ou-billing-form-wrap input.input-text:focus,
			.select2-container--default .select2-search--dropdown .select2-search__field:focus {
				box-shadow: none;
				outline: 0;
			}
			.select2-container--default .select2-selection--single,
			.select2-container--default .select2-search--dropdown .select2-search__field {
				outline: none;
				height: auto;
				-webkit-appearance: none;
				outline: none;
				-moz-appearance: none;
				text-align: left;
			}
			.select2-container--default ul.select2-results__options .select2-results__option[data-selected=true] {
				background: #333;
				color: #fff;
			}
			.select2-container--default ul.select2-results__options .select2-results__option--highlighted[aria-selected], .select2-container--default ul.select2-results__options .select2-results__option--highlighted[data-selected] {
				background: #999;
				color: #fff;
			}
			.oxy-ou-billing-form-wrap .form-row .select2-container--default .select2-selection--single .select2-selection__arrow {
				background-size: var(--select2-arrow-size);
				width: calc(15px + var(--select2-arrow-size) );
			}
			.address-field .select2-container {
				display: none;
			}
			.address-field select+.select2-container {
				display: block;
			}
			@media (max-width: 768px) {
				.oxy-ou-billing-form-wrap form.woocommerce-checkout .form-row-first,
				.oxy-ou-billing-form-wrap form.woocommerce-checkout .form-row-last {
					margin-left: 0;
					margin-right: 0;
					width: 100%;
				}
			}
			';

			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();

		if( isset($original[$prefix . '_selected_bgcolor']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option[data-selected=true] {
						background: '.$original[$prefix . '_selected_bgcolor'].';
					}';
		}

		if( isset($original[$prefix . '_selected_hbgcolor']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option--highlighted[aria-selected], .select2-container--default ul.select2-results__options .select2-results__option--highlighted[data-selected] {
						background: '. $original[$prefix . '_selected_hbgcolor'] .';
					}';
		}
		
		if( isset($original[$prefix . '_selected_color']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option[data-selected=true] {
						color: '.$original[$prefix . '_selected_color'].';
					}';
		}
		
		if( isset($original[$prefix . '_highlighted_color']) ) {
			$css .= '.select2-container--default ul.select2-results__options .select2-results__option--highlighted[aria-selected], .select2-container--default ul.select2-results__options .select2-results__option--highlighted[data-selected] {
						color: '.$original[$prefix . '_highlighted_color'].';
					}';
		}

		$css .= '.select2-container--default .select2-dropdown--below .select2-search--dropdown .select2-search__field {
					background: '. ( isset($original[$prefix . '_sfield_bgcolor']) ? $original[$prefix . '_sfield_bgcolor'] : '#fff' ) .';
					border-color: '. ( isset($original[$prefix . '_sfield_bordercolor']) ? $original[$prefix . '_sfield_bordercolor'] : '#d3ced2' ) .';
					border-width: '. ( isset($original[$prefix . '_sfield_brdwidth']) ? $original[$prefix . '_sfield_brdwidth'] : '1' ) .'px;
					border-radius: '. ( isset($original[$prefix . '_sfield_brdradius']) ? $original[$prefix . '_sfield_brdradius'] : '4' ) .'px;
					color: '. ( isset($original[$prefix . '_sfield_color']) ? $original[$prefix . '_sfield_color'] : '#333' ) .';
				}';

		$css .= '.select2-container--default .select2-dropdown--below .select2-search--dropdown .select2-search__field:focus {
					background: '. ( isset($original[$prefix . '_sfield_fbgcolor']) ? $original[$prefix . '_sfield_fbgcolor'] : '#fff' ) .';
					border-color: '. ( isset($original[$prefix . '_sfield_focusbrdcolor']) ? $original[$prefix . '_sfield_focusbrdcolor'] : '#bbb' ) .';
					color: '. ( isset($original[$prefix . '_sfield_fcolor']) ? $original[$prefix . '_sfield_fcolor'] : '#666' ) .';
				}';

		return $css;
	}

	function ou_billing_form_wrap_script() {
		if( is_checkout() )
			return;

		wp_enqueue_style( 'select2' );

		wp_enqueue_script( 'selectWoo' );
		wp_enqueue_script( 'wc-country-select' );
		wp_enqueue_script( 'wc-checkout' );
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				if( $('.oxy-ou-place-payment').length && $( '.oxy-ou-place-payment .no-items-in-cart').length ) {
					$('.no-items-in-cart #place_order').attr('disabled', 'disabled');
				}

				$( document ).on( 'added_to_cart removed_from_cart', function() {
					if ( Cookies.get( 'woocommerce_items_in_cart' ) < 0 ) {
						window.location.reload();
					}

					if( $('.ou-cart-button').attr('data-checkoutpage') == 'yes' || $('.woocommerce-checkout-review-order-table').length > 0 )
						$( document.body ).trigger( 'update_checkout' );
				});


			});
		</script>
	<?php
	}

	function enableFullPresets() {
		return true;
	}
}

new OUBillingFormBuilder();