<?php

class OUWooReviewForm extends UltimateWooEl {

	public $form_css = false;

	function name() {
		return __( "Form", "oxyultimate-woo" );
	}

	function slug() {
		return "ou_review_form";
	}

	function ouwoo_button_place() {
		return "reviews";
	}

	function custom_init() {
		add_filter( 'woocommerce_product_tabs', array( $this, 'ouwoo_product_tabs' ), 1000 );
	}

	function outerWrapper() {
		$wrapper = $this->addControlSection("form_wrapper", __("Form Wrapper", "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = '#review_form #respond';

		$wrapper->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		$wrapper->addStyleControl(
			array(
				"selector" 		=> $selector,
				"property" 		=> 'width',
				"control_type" 	=> 'slider-measurebox',
				"unit" 			=> "%"
			)
		);

		//* Spacing
		$spacing = $wrapper->addControlSection("spacing_formwrap", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"rvsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"rvsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		//* Borders
		$wrapper->borderSection(__("Border"), $selector, $this);

		//* Box Shadow
		$wrapper->boxShadowSection(__("Box Shadow"), $selector, $this);
	}

	function formTitle() {
		$title = $this->addControlSection("form_title", __("Heading", "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = "#reply-title";

		$title->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		//* Spacing
		$spacing = $title->addControlSection("spacing_title", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"title_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"title_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$title->typographySection(
			__("Typography"),
			$selector,
			$this
		);

		//* Borders
		$title->borderSection(__("Border"), $selector, $this);
	}

	function formDescription() {
		$desc = $this->addControlSection("form_desc", __("Description", "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = ".comment-notes";

		$desc->addStyleControl(
			array(
				"selector" 	=> $selector,
				"property" 	=> 'background-color'
			)
		);

		//* Spacing
		$spacing = $desc->addControlSection("spacing_desc", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"desc_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"desc_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$desc->typographySection(__("Typography"), $selector, $this);

		//* Borders
		$desc->borderSection(__("Border"), $selector, $this);
	}

	function formLabels() {
		$labels = $this->addControlSection("form_label", __("Labels", "oxyultimate-woo"), "assets/icon.png", $this);

		$labels->addStyleControl(
			array(
				"name" 	=> __("Asterisk Color"),
				"selector" => 'form.comment-form label .required, .comment-notes .required',
				"property" => 'color',
			)
		);

		$labels->addStyleControl(
			array(
				"name" 	=> __("Asterisk Size"),
				"selector" => 'form.comment-form label .required, .comment-notes .required',
				"property" => 'font-size',
			)
		);

		//* Spacing
		$spacing = $labels->addControlSection("spacing_labels", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"margin",
			"label_margin",
			__("Margin"),
			"form.comment-form p:not(.comment-form-cookies-consent) label, .comment-form-rating label"
		)->whiteList();

		$labels->typographySection(
			__("Typography"),
			"form.comment-form p:not(.comment-form-cookies-consent) label, .comment-form-rating label",
			$this
		);
	}

	function formStars() {
		$stars_section = $this->addControlSection("stars_section", __("Stars", "oxyultimate-woo"), "assets/icon.png", $this);

		$stars_section->addStyleControls(
			array(
				array(
					"name" 		=> __('Color'),
					"selector" 	=> 'p.stars a',
					"property" 	=> 'color'
				),
				array(
					'name' 		=> __('Size'),
					'selector' 	=> 'p.stars',
					'property' 	=> 'font-size',
					'unit' 		=> 'em',
					'default'	=> '1'
				)
			)
		);
	}

	function formInputFields() {
		$inpf = $this->addControlSection("inputs_section", __("Input Fields", 'oxyultimate-woo'), "assets/icon.png", $this);

		$fld_selector = '#respond input[type=text], #respond input[type=email], #respond textarea';
		$fld_selector_focus = '#respond input[type=text]:focus, #respond input[type=email]:focus, #respond textarea:focus';

		$inpf->addStyleControl(
			array(
				'name' 		=> __('Textarea Height'),
				'selector' 	=> "#reviews #comment",
				"property"	=> "height",
				"control_type" => "slider-measurebox",
				"unit" 		=> "px"
			)
		);

		$inpf->typographySection(__("Typography"), $fld_selector, $this);

		$focus = $inpf->addControlSection("inp_colors", __("Colors", "oxyultimate-woo"), "assets/icon.png", $this);
		$focus->addStyleControls(
			array(
				array(
					"name" 				=> __('Focused Text Color', "oxyultimate-woo"),
					"selector" 			=> $fld_selector_focus,
					"property" 			=> 'color'
				),
				array(
					"selector" 			=> $fld_selector,
					"property" 			=> 'background-color'
				),
				array(
					"name" 				=> __('Focused Background Color', "oxyultimate-woo"),
					"selector" 			=> $fld_selector_focus,
					"property" 			=> 'background-color'
				)
			)
		);

		//* Spacing
		$spacing = $inpf->addControlSection("spacing_inp", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"inpf_padding",
			__("Padding"),
			$fld_selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"inpf_margin",
			__("Margin"),
			$fld_selector
		)->whiteList();

		//* Borders
		$inpf->borderSection(__("Border"), $fld_selector, $this);
		$inpf->borderSection(__("Focus Border", "oxyultimate-woo"), $fld_selector_focus, $this);

		//* Box Shadow
		$inpf->boxShadowSection(__("Shadow", "oxyultimate-woo"), $fld_selector, $this);
		$inpf->boxShadowSection(__("Focus Shadow", "oxyultimate-woo"), $fld_selector_focus, $this);
	}

	function formCheckbox() {
		$cb = $this->addControlSection("cb_section", __("Checkbox", 'oxyultimate-woo'), "assets/icon.png", $this);

		$cb->addStyleControl(
			array(
				'name' 		=> __('Checkbox Size', "oxyultimate-woo"),
				'selector' 	=> "#wp-comment-cookies-consent",
				"property"	=> "width|height",
				"control_type" => "slider-measurebox",
				"unit" 		=> "px"
			)
		);

		$cb->addStyleControl(
			array(
				'name' 		=> __('Gap Between Checkbox & Text', "oxyultimate-woo"),
				'selector' 	=> ".comment-form-cookies-consent label",
				"property"	=> "margin-left",
				"control_type" => "slider-measurebox",
				"unit" 		=> "px",
				"default" 	=> 18
			)
		);

		$cb->addStyleControl(
			array(
				'selector' 	=> "#review_form #respond p.comment-form-cookies-consent",
				"property"	=> "margin-top",
				"control_type" => "slider-measurebox",
				"unit" 		=> "px"
			)
		);

		$cb->addStyleControl(
			array(
				'selector' 	=> "#review_form #respond p.comment-form-cookies-consent",
				"property"	=> "margin-bottom",
				"control_type" => "slider-measurebox",
				"unit" 		=> "px"
			)
		);

		$cb->typographySection(__('Label'), '.comment-form-cookies-consent label', $this);
	}

	function formButton() {
		$btn = $this->addControlSection("btn_section", __("Button"), "assets/icon.png", $this);

		$btn_selector = '#review_form #respond .form-submit input';

		$btn->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Text'),
			'slug' 		=> 'button_text'
		]);

		$button_config = $btn->addControlSection(
			'button_size',
			__("Size & Position", "oxyultimate-woo"),
			'assets/icon.png',
			$this
		);

		$button_config->addStyleControl(
			array(
				"selector" 		=> $btn_selector,
				"property" 		=> 'width',
				"control_type" 	=> 'slider-measurebox',
				"unit" 			=> "px"
			)
		);

		$button_config->addControl(
			"buttons-list",
			"button_align",
			__('Alignment')
		)->setValue([
			'left' => __('Left'),
			'center' => __('Center'),
			'right' => __('Right'),
		])->setValueCSS([
			'left' => "#review_form #respond .form-submit{justify-content: start;}",
			'center' => "#review_form #respond .form-submit{justify-content: center;}",
			'right' => "#review_form #respond .form-submit{justify-content: flex-end;}"
		])->setDefaultValue("left");

		//* Spacing
		$spacing = $btn->addControlSection("sp_btn", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"btn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"btn_margin",
			__("Margin"),
			$btn_selector
		)->whiteList();
		
		$btn->typographySection(
			__("Typography"),
			$btn_selector,
			$this
		);

		$btnclr = $btn->addControlSection("btn_clr", __("Color"), "assets/icon.png", $this);
		$btnclr->addStyleControls(
			array(
				array(
					"selector" => $btn_selector,
					"property" => 'background-color',
					"control_type" => 'colorpicker',
				),
				array(
					"name" => __('Hover Background Color', "oxyultimate-woo"),
					"selector" => $btn_selector . ':hover',
					"property" => 'background-color'
				),
				array(
					"name" => __('Hover Text Color', "oxyultimate-woo"),
					"selector" => $btn_selector . ':hover',
					"property" => 'color'
				)
			)
		);

		$btn->borderSection(__('Border'), $btn_selector, $this);
		$btn->borderSection(__('Hover Border', "oxyultimate-woo"), $btn_selector . ':hover', $this);

		$btn->boxShadowSection(__('Shadow', "oxyultimate-woo"), $btn_selector, $this);
		$btn->boxShadowSection(__('Hover Shadow', "oxyultimate-woo"), $btn_selector . ':hover', $this);
	}

	function formVerification() {
		$msg = $this->addControlSection("msg_sec", __("Message", "oxyultimate-woo"), "assets/icon.png", $this);

		$selector = ".woocommerce-verification-required";

		$msg->addOptionControl([
			'type' 		=> 'textarea',
			'name' 		=> __('Text'),
			'slug' 		=> 'verify_text'
		])->setParam('description', __('Default:', "oxyultimate-woo") . ' ' .  esc_html( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ) );

		$msg->addStyleControl(
			array(
				"selector" 			=> $selector,
				"property" 			=> 'background-color'
			)
		);

		//* Spacing
		$spacing = $msg->addControlSection("spacing_msg", __("Spacing", "oxyultimate-woo"), "assets/icon.png", $this);
		$spacing->addPreset(
			"padding",
			"msg_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"msg_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$msg->typographySection(
			__("Typography"),
			$selector,
			$this
		);

		//* Borders
		$msg->borderSection(__("Border"), $selector, $this);

		//* Box Shadow
		$msg->boxShadowSection(__("Box Shadow"), $selector, $this);
	}
	
	function controls() {

		$productId = $this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Product ID', "oxyultimate-woo"),
			'slug' 		=> 'product_id'
		]);
		$productId->setParam('description', __('Leave blank if you are using on single product page or loop.', "oxyultimate-woo"));
		$productId->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouwooRFrmPrdID">data</div>');

		$this->outerWrapper();

		$this->formTitle();

		$this->formDescription();

		$this->formLabels();

		$this->formStars();

		$this->formInputFields();

		$this->formCheckbox();

		$this->formButton();

		$this->formVerification();
	}

	function fetchDynamicProductID( $id ) {
		if( strstr( $id, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($id));
			$id = do_shortcode($shortcode);
		}

		return intval( $id );
	}

	function render( $options, $defaults, $content ) {

		global $post, $product;

		if( is_singular('product') ) { wp_reset_postdata(); }

		if( isset( $options['product_id'] ) ) {
			$product = wc_get_product( $this->fetchDynamicProductID( $options['product_id'] ) );
		} else {
			$product = wc_get_product();
		}

		if( ! is_a( $product, 'WC_Product') )
			return;

		if ( ! wc_review_ratings_enabled() ) {
			return;
		}
		
		if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) || defined('OXY_ELEMENTS_API_AJAX') ) : 
			$button_text = isset( $options['button_text'] ) ? wp_kses_post( $options['button_text'] ) : esc_html__( 'Submit', 'woocommerce' );
	?>
			<div id="review_form_wrapper">
				<div id="review_form">
					<?php
					$commenter    = wp_get_current_commenter();
					$comment_form = array(
						/* translators: %s is product title */
						'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'woocommerce' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
						/* translators: %s is product title */
						'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
						'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
						'title_reply_after'   => '</span>',
						'comment_notes_after' => '',
						'label_submit'        => $button_text,
						'logged_in_as'        => '',
						'comment_field'       => '',
					);
	
					$name_email_required = (bool) get_option( 'require_name_email', 1 );
					$fields              = array(
						'author' => array(
							'label'    => __( 'Name', 'woocommerce' ),
							'type'     => 'text',
							'value'    => $commenter['comment_author'],
							'required' => $name_email_required,
						),
						'email'  => array(
							'label'    => __( 'Email', 'woocommerce' ),
							'type'     => 'email',
							'value'    => $commenter['comment_author_email'],
							'required' => $name_email_required,
						),
					);
	
					$comment_form['fields'] = array();
	
					foreach ( $fields as $key => $field ) {
						$field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
						$field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );
	
						if ( $field['required'] ) {
							$field_html .= '&nbsp;<span class="required">*</span>';
						}
	
						$field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';
	
						$comment_form['fields'][ $key ] = $field_html;
					}
	
					$account_page_url = wc_get_page_permalink( 'myaccount' );
					if ( $account_page_url ) {
						/* translators: %s opening and closing link tags respectively */
						$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
					}
	
					if ( wc_review_ratings_enabled() ) {
						$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
						</select></div>';
					}
	
					$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';
	
					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ), $product->get_id() );
					?>
				</div>
			</div>
		<?php else : 
			$verify_text = isset( $options['verify_text'] ) ? wp_kses_post( $options['verify_text'] ) : esc_html__( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); 
		?>
			<p class="woocommerce-verification-required"><?php echo $verify_text; ?></p>
		<?php endif;
	}

	function ouwoo_product_tabs( $tabs ) {
		if ( comments_open() ) {
			unset( $tabs['reviews'] );
		}

		return $tabs;
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->form_css ) {
			$css = '.oxy-ou-review-form,
				.oxy-ou-review-form #reply-title {
					display: block;
					width: 100%;
				}
				.oxy-ou-review-form form.comment-form label {
					color: #666;
				}
				.oxy-ou-review-form form.comment-form p:not(.comment-form-cookies-consent) label, 
				.oxy-ou-review-form .comment-form-rating label {
					display: block;
				}
				.oxy-ou-review-form .comment-form-rating a {
					color: #6799b2;
				}
				.oxy-ou-review-form input#author,
				.oxy-ou-review-form input#email {
					width: 100%;
				}
				.oxy-ou-review-form #review_form #respond .form-submit {
					display: flex;
					margin-bottom: 0;
				}

				.oxy-ou-review-form #review_form #respond input:focus,
				.oxy-ou-review-form #review_form #respond textarea:focus {
					box-shadow: none;
					outline: 0;
				}
				.oxy-ou-review-form #review_form #respond p.comment-form-cookies-consent {
					display: inline-flex;
					align-items: flex-start;
					margin-top: 12px;
					margin-bottom: 20px;
				}
				.oxy-ou-review-form .comment-form-cookies-consent input[type="checkbox"] {
					width: 18px;
					height: 18px;
				}
				.oxy-ou-review-form .comment-form-cookies-consent label {
					margin-top: 0;
					margin-left: 8px;
				}
				.oxy-ou-review-form .comment-form-rating .stars {
					display: none;
				}
				.oxy-ou-review-form .comment-form-rating label+p.stars {
					display: block!important;
				}
				';

			$this->form_css = true;
		}

		return $css;
	}
}

new OUWooReviewForm();