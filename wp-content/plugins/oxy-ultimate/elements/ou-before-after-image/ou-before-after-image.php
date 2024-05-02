<?php

namespace Oxygen\OxyUltimate;

Class OUBeforeAfterImage extends \OxyUltimateEl {
	public $has_js = true;
	private $baimg_js_code = '';
	public $js_added = false;

	function name() {
		return __( "Before After Image", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_baimg_heading";
	}

	function oxyu_button_place() {
		return "images";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below and see the changes on builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$before_image = $this->addCustomControl(
			"<div class=\"oxygen-control\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_baimg_heading_oubfi_before_image'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_baimg_heading_oubfi_before_image')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_baimg_heading_oubfi_before_image']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_baimg_heading_oubfi_before_image\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicBAIBImage\">data</div>
				</div>
			</div>",
			'before_image'
		);
		$before_image->setParam('heading', __('Before Image', "oxy-ultimate") );
		$before_image->rebuildElementOnChange();

		$bfText = $this->addOptionControl(
			array(
				"type" 	=> "textfield",
				"name" 	=> __("Before Label", "oxy-ultimate"),
				"slug" 	=> "oubfi_before_label",
				"base64" 	=> true
			)
		);
		$bfText->setValue('Before');
		$bfText->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicBAIBText">data</div>');

		$after_image = $this->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_baimg_heading_oubfi_after_image'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_baimg_heading_oubfi_after_image')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_baimg_heading_oubfi_after_image']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_baimg_heading_oubfi_after_image\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicBAIAImage\">data</div>
				</div>
			</div>",
			'after_image'
		);
		$after_image->setParam('heading', __('After Image', "oxy-ultimate") );
		$after_image->rebuildElementOnChange();

		$aftText = $this->addOptionControl(
			array(
				"type" 	=> "textfield",
				"name" 	=> __("After Label", "oxy-ultimate"),
				"slug" 	=> "oubfi_after_label",
				"base64" 	=> true
			)
		);
		$aftText->setValue('After');
		$aftText->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicBAIAText">data</div>');

		$this->addOptionControl(
			array(
				"type" 				=> "radio",
				"name" 				=> __("Always Show Label On Mobile", "oxy-ultimate"),
				"slug" 				=> "oubfi_label",
				"value" 			=> array(
					'no' 	=> __( 'No', 'oxy-ultimate' ),
					'yes' 	=> __( 'Yes', 'oxy-ultimate' ),
				),
				"default" 			=> 'no'
			)
		);

		$this->addOptionControl(
			array(
				"type" 				=> "radio",
				"name" 				=> __("Orientation", "oxy-ultimate"),
				"slug" 				=> "oubfi_orientation",
				"value" 			=> array(
					'horizontal' => __( 'Horizontal', 'oxy-ultimate' ),
					'vertical'   => __( 'Vertical', 'oxy-ultimate' ),
				),
				"default" 			=> 'horizontal'
			)
		);

		$this->addOptionControl(
			array(
				"type" 				=> "radio",
				"name" 				=> __("Move on Hover", "oxy-ultimate"),
				"slug" 				=> "oubfi_mhover",
				"value" 			=> array(
					'yes' 	=> __( 'Yes', 'oxy-ultimate' ),
					'no'   	=> __( 'No', 'oxy-ultimate' ),
				),
				"default" 			=> 'no'
			)
		);

		$comparisonHandle = $this->addControlSection( 'ba_comparison_handle', __('Comparison Handle', 'oxy-ultimate'), 'assets/icon.png', $this );

		$comparisonHandle->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below and see the changes on builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$comparisonHandle->addOptionControl(
			array(
				"type" 				=> "slider-measurebox",
				"name" 				=> __("Initial Offset", "oxy-ultimate"),
				"slug" 				=> "oubfi_initial_offset"
			)
		)->setUnits(''," ")->setRange('0','0.9','0.1')->setValue('0.5');

		$comparisonHandle->addStyleControl(
			array(
				"control_type" 		=> "slider-measurebox",
				"name" 				=> __("Triangle Width", "oxy-ultimate"),
				"slug" 				=> "oubfi_handle_twidth",
				"selector"			=> '.twentytwenty-left-arrow, .twentytwenty-right-arrow, .twentytwenty-up-arrow, .twentytwenty-down-arrow',
				"property" 			=> "border-width",
			)
		)->setRange('1','100','3')->setUnits("px","px")->setValue('7');

		$circle = $comparisonHandle->addControlSection( 'comparison_handle_circle', __('Circle', 'oxy-ultimate'), 'assets/icon.png', $this );
		$circle->addStyleControl(
			array(
				"control_type" 		=> "slider-measurebox",
				"name" 				=> __("Width", "oxy-ultimate"),
				"slug" 				=> "oubfi_handle_width",
				"selector"			=> '.twentytwenty-handle',
				"property" 			=> "width|height",
			)
		)->setRange('1','100','4')->setUnits("px","px")->setValue('38');

		$circle->addStyleControl(
			array(
				"control_type" 		=> "slider-measurebox",
				"name" 				=> __("Position", "oxy-ultimate"),
				"slug" 				=> "oubfi_handle_pos",
				"selector"			=> '.twentytwenty-handle',
				"property" 			=> "top",
			)
		)->setRange('0','100','5')->setUnits("%","%")->setValue('50');

		$circle->addStyleControl(
			array(
				"control_type" 		=> "slider-measurebox",
				"name" 				=> __("Thickness", "oxy-ultimate"),
				"slug" 				=> "oubfi_handle_size",
				"selector"			=> '.twentytwenty-handle',
				"property" 			=> "border-width",
			)
		)->setRange('1','50','2')->setUnits("px","px")->setValue('3');

		$circle->addStyleControl(
			array(
				"control_type" 		=> "slider-measurebox",
				"name" 				=> __("Radius", "oxy-ultimate"),
				"slug" 				=> "oubfi_handle_radius",
				"selector"			=> '.twentytwenty-handle',
				"property" 			=> "border-radius",
			)
		)->setRange('0','1000','10')->setUnits("px","px")->setValue('1000');

		$color = $comparisonHandle->addControlSection( 'comparison_handle_color', __('Color', 'oxy-ultimate'), 'assets/icon.png', $this );
		$color->addStyleControls(
			array(
				array(
					"control_type" 	=> "colorpicker",
					"property" 		=> "border-color",
					"name" 			=> __("Color", "oxy-ultimate"),
					"slug" 			=> "oubfi_handle_color",
					"selector"		=> '.twentytwenty-handle'
				),
				array(
					"control_type" 	=> "colorpicker",
					"property" 		=> "background-color",
					"name" 			=> __("Background Color", "oxy-ultimate"),
					"slug" 			=> "oubfi_handle_bcolor",
					"selector"		=> '.twentytwenty-handle'
				),
				array(
					"control_type" 	=> "colorpicker",
					"name" 			=> __("Triangle Color", "oxy-ultimate"),
					"slug" 			=> "oubfi_handle_tcolor",
					"selector"		=> '.twentytwenty-left-arrow',
					"property" 		=> "border-right-color",
					"default" 		=> '#ffffff'
				)
			)
		);

		$color->addStyleControl(
			array(
				"control_type" 	=> "colorpicker",
				"property" 		=> "background",
				"name" 			=> __("Bar Color", "oxy-ultimate"),
				"slug" 			=> "oubfi_handle_color",
				"selector"		=> '.twentytwenty-horizontal .twentytwenty-handle:before, .twentytwenty-horizontal .twentytwenty-handle:after, .twentytwenty-vertical .twentytwenty-handle:before, .twentytwenty-vertical .twentytwenty-handle:after'
			)
		)->setParam('ng_show', "!isActiveName('". $this->El->get_tag() ."')");

		$this->addApplyParamsButton();
	}

	function fetchDynamicData( $field ) {
		if( strstr( $field, 'oudata_') ) {
			$field = base64_decode( str_replace( 'oudata_', '', $field ) );
			$shortcode = ougssig( $this->El, $field );
			$field = do_shortcode( $shortcode );
		} elseif( strstr( $field, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($field));
            $field =  esc_attr(do_shortcode($shortcode));
		}

		return $field;
	}

	function render($options, $defaults, $content) {
		$bimg = OXYU_URL . 'assets/images/before-image.jpg';
		$afimg = OXYU_URL . 'assets/images/after-image.jpg';
		$blabel = $alabel = '';
		if( isset( $options['oubfi_before_image'] ) ) {
			$bimgurl = $this->fetchDynamicData( $options['oubfi_before_image'] );
		} else {
			$bimgurl = $bimg;
		}

		if( isset( $options['oubfi_after_image'] ) ) {
			$aimgurl = $this->fetchDynamicData( $options['oubfi_after_image'] );
		} else {
			$aimgurl =  $afimg;
		}

		if( isset( $options['oubfi_before_label'] ) ) {
			$blabel = $this->fetchDynamicData( $options['oubfi_before_label'] );
		}

		if( isset( $options['oubfi_after_label'] ) ) {
			$alabel = $this->fetchDynamicData( $options['oubfi_after_label'] );
		}

		$uid = str_replace( ".", "", uniqid( 'baimg', true ) );

		//$data = ' data-id="' . $options['selector'] . '"';
		$data = ' data-id="' . $uid . '"';
		$data .= ' data-orientation="' . $options['oubfi_orientation'] . '"';
		$data .= ' data-mhover="' . $options['oubfi_mhover'] . '"';
		$data .= ' data-initial-offset="' . apply_filters( 'before_after_image_initial_offset', $options['oubfi_initial_offset'] ) . '"';
	?>
		<div class="ou-beforeafterimage <?php echo $uid;?> label-mobile-<?php echo $options['oubfi_label'];?>"<?php echo $data;?>>
			<img src="<?php echo esc_url( $bimgurl ); ?>" alt="<?php echo $blabel; ?>" class="skip-lazy" />
			<img src="<?php echo esc_url( $aimgurl ); ?>" alt="<?php echo $alabel; ?>" class="skip-lazy" />
			<span class="baimg-bflbl"><?php echo $blabel; ?></span>
			<span class="baimg-aflbl"><?php echo $alabel; ?></span>
		</div>
	<?php

		if( isset( $_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') ) {
			$this->El->builderInlineJS(
				"
				var baimgTimeout;
				jQuery(window).ready(function($){
					new ouBeforeAfterImage({
						selector: '.' + $(this).attr('data-id'), 
						before_label: '" . $blabel . "', 
						after_label: '" . $alabel . "',
						orientation: '%%oubfi_orientation%%',
						mhover: '%%oubfi_mhover%%',
						initial_offset: '%%oubfi_initial_offset%%'
					});
					$(window).trigger('resize');

					if( typeof baimgTimeout != 'undefinied' )
						clearTimeout(baimgTimeout);
						
					baimgTimeout = setTimeout(function(){
						$('.ou-beforeafterimage').each(function(){
							new ouBeforeAfterImage({
								selector: '.' + $(this).attr('data-id'), 
								before_label: '" . $blabel . "', 
								after_label: '" . $alabel . "',
								orientation: '%%oubfi_orientation%%',
								mhover: '%%oubfi_mhover%%',
								initial_offset: '%%oubfi_initial_offset%%'
							});
						});
						$(window).trigger('resize');
					}, 300);
				});"
			);
		} else {
			if( ! $this->js_added ) {
				add_action( 'wp_footer', array( $this, 'baimg_enqueue_scripts' ) );

				$this->baimg_js_code ="
					var baimgTimeout;
					jQuery(document).ready(function($){
						$('.ou-beforeafterimage').each(function(){
							new ouBeforeAfterImage({
								selector: '.' + $(this).attr('data-id'), 
								before_label: $(this).find('.baimg-bflbl').text(), 
								after_label: $(this).find('.baimg-aflbl').text(),
								orientation: $(this).attr('data-orientation'),
								mhover: $(this).attr('data-mhover'),
								initial_offset: $(this).attr('data-initial-offset')
							});
						});
						jQuery(window).trigger('resize');

						if( typeof baimgTimeout != 'undefinied' )
							clearTimeout(baimgTimeout);
							
						baimgTimeout = setTimeout(function(){
							jQuery(window).trigger('resize');
						}, 900);
						
						if ( $('.oxy-tab').length > 0 ) {
							$('.oxy-tab').on('click', function(e) {
								if( typeof baimgTimeout != 'undefinied' )
									clearTimeout(baimgTimeout);
									
								setTimeout(function(){ $(window).trigger('resize');}, 5 );
							});
						}

						if ( $('.oxy-modal-backdrop').length > 0 ) {
							$( '.oxy-modal-backdrop' ).each(function( index ) {
								var modal = this,
									trigger = $( modal ).data('trigger');
									if( 'user_clicks_element' == trigger ) {
										$( $( modal ).data( 'trigger-selector' ) ).click( function( event ) {
											setTimeout(function(){ $(window).trigger('resize');}, 5 );
										});
									}
							});
						}
					});
				". "\n";

				$this->El->footerJS( $this->baimg_js_code );

				$this->js_added = true;
			}
		}
	}

	function customCSS( $original, $selector ) {
		$prefix = $this->El->get_tag();
		$handleWidth = (isset( $original[$prefix . '_oubfi_handle_width'] )) ? $original[$prefix . '_oubfi_handle_width'] : 38;
		$handleSize = (isset( $original[$prefix . '_oubfi_handle_size'] )) ? $original[$prefix . '_oubfi_handle_size'] : 3;
		$handleTWidth = (isset( $original[$prefix . '_oubfi_handle_twidth'] )) ? $original[$prefix . '_oubfi_handle_twidth'] : 7;

		$css = '.baimg-bflbl,.baimg-aflbl{display: none}';
		$css .= $selector . ' img{-webkit-transition: none!important;-moz-transition: none!important;transition: none!important;}';
		
		$css.= $selector . ' .twentytwenty-handle{ margin-left:calc(-' . $handleWidth . 'px / 2 - ' . $handleSize . 'px / 2); margin-top: calc(-' . $handleWidth . 'px / 2 - ' . $handleSize . 'px / 2); }';

		$css.= $selector . ' .twentytwenty-horizontal .twentytwenty-handle:before { margin-bottom: calc(' . $handleWidth . 'px / 2); margin-left: calc(-' . $handleSize . 'px / 2);}';
		$css.= $selector . ' .twentytwenty-horizontal .twentytwenty-handle:after { margin-top:calc(' . $handleWidth . 'px / 2); margin-left: calc(-' . $handleSize . 'px / 2);}';

		$css.= $selector . ' .twentytwenty-vertical .twentytwenty-handle:before { margin-left:calc(' . $handleWidth . 'px / 2); }';
		$css.= $selector . ' .twentytwenty-vertical .twentytwenty-handle:after { margin-right:calc(' . $handleWidth . 'px / 2); }';

		$tcolor = isset( $original[$prefix . '_oubfi_handle_tcolor'] ) ? $original[$prefix . '_oubfi_handle_tcolor'] : '#fff';
		$css.= $selector . ' .twentytwenty-left-arrow {border-right-color: ' . $tcolor . '; margin-top: -' . $handleTWidth . 'px;left: calc( 50% - ' . $handleTWidth . 'px * 2 + 13px );}';
		$css.= $selector . ' .twentytwenty-right-arrow {border-left-color: ' . $tcolor . '; margin-top: -' . $handleTWidth . 'px; right: calc( 50% - ' . $handleTWidth . 'px * 2 + 13px );}';
		$css.= $selector . ' .twentytwenty-down-arrow {border-top-color: ' . $tcolor . '; margin-top: -' . $handleTWidth . 'px; bottom: calc( 50% - ' . $handleTWidth . 'px * 2 + 12px ); left: calc( 50% - ' . $handleTWidth . 'px + 7px );}';
		$css.= $selector . ' .twentytwenty-up-arrow {border-bottom-color: ' . $tcolor . '; margin-top: -' . $handleTWidth . 'px; top: calc( 50% - ' . $handleTWidth . 'px * 2 + 4px ); left: calc( 50% - ' . $handleTWidth . 'px + 7px );}';
		$css.= $selector . ' .twentytwenty-horizontal .twentytwenty-handle:before,' . $selector  . ' .twentytwenty-horizontal .twentytwenty-handle:after{width: ' . $handleSize . 'px ;}';
		$css.= $selector . ' .twentytwenty-vertical .twentytwenty-handle:before,'. $selector . ' .twentytwenty-vertical .twentytwenty-handle:after {height: ' . $handleSize . 'px ;}';
		$css.='@media (max-width: 768px){ ' . 
				$selector . ' .label-mobile-yes:not(:hover) .twentytwenty-after-label,' . 
				$selector . ' .label-mobile-yes:not(:hover) .twentytwenty-before-label{opacity:1;} 
		}';

		return $css;
	}

	function baimg_load_scripts() {
		if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'baimg_enqueue_scripts' ) );
		}
	}

	function baimg_enqueue_scripts() {
		wp_enqueue_style('twentytwenty-style', OXYU_URL . 'assets/css/twentytwenty.css',array(),filemtime( OXYU_DIR . 'assets/css/twentytwenty.css' ),'all');
		wp_enqueue_script('ou-event-script', OXYU_URL . 'assets/js/jquery.event.js',array(),filemtime( OXYU_DIR . 'assets/js/jquery.event.js' ),true );
		wp_enqueue_script('ou-twentytwenty-script', OXYU_URL . 'assets/js/jquery.twentytwenty.js',array(),filemtime( OXYU_DIR . 'assets/js/jquery.twentytwenty.js' ),true);
		wp_enqueue_script('ou-baimg-script', OXYU_URL . 'assets/js/baimg.js',array(),filemtime( OXYU_DIR . 'assets/js/baimg.js' ),true);
	}
}

$oubaimg = new OUBeforeAfterImage();
$oubaimg->baimg_load_scripts();
