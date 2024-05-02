<?php

namespace Oxygen\OxyUltimate;

if ( ! class_exists('ACF') )
	return;

Class OUACFGalleryAccordion extends \OxyUltimateEl {
	public $css_added = false;
	public $lghbox_added = false;
	public $script_added = false;
	public $has_js = true;

	function name() {
		return __( "ACF Gallery Accordion", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_acf_acrd";
	}

	function oxyu_button_place() {
		return "images";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/ou-image-panels.svg';
	}

	function controls() {

		$layout = $this->El->addControl( 'buttons-list', 'ouacfa_layout', __('Layout') );
		$layout->setValue( array( "Vertical", "Horizontal" ) );
		$layout->setValueCSS( array(
			"Horizontal" 	=> ".ouacf-acrd-wrap{flex-direction: column;} .oxy-ou-acrd-item{width: 100%;}"
		));
		$layout->setDefaultValue("Vertical");
		$layout->whiteList();

		$gallery = $this->addControlSection(  'ouacfacrd_gallery', __('Setting Gallery', "oxy-ultimate"),'assets/icon.png', $this );
		$source = $gallery->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Source"),
				"slug" 		=> "acfg_source",
				"value" 	=> array(
					"same" 		=> __("Same Post/Page", "oxy-ultimate"), 
					"import" 	=> __("Other", "oxy-ultimate")
				),
				"default" 	=> "same",
				"css" 		=> false
			)
		);

		$import = $gallery->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Enter Post/Page ID", "oxy-ultimate"),
				"slug" 		=> "page_id",
				"css" 		=> false
			)
		);

		$import->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_acrd_acfg_source']=='import'");
		$import->rebuildElementOnChange();

		$gf_name = $gallery->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Gallery Field Name", "oxy-ultimate"),
				"slug" 		=> "field_name",
				"css" 		=> false
			)
		);
		$gf_name->rebuildElementOnChange();

		$height = $gallery->addStyleControl([
			'selector' 		=> '.ouacf-acrd-wrap',
			'property' 		=> 'height',
			'control_type' 	=> 'slider-measurebox'
		]);

		$height->setRange( '0', '1000', '10');
		$height->setValue( '450' );
		$height->setUnits( 'px', 'px' );

		$imgpos = $gallery->addControl( 'buttons-list', 'imgpos', __('Image Position') );
		$imgpos->setValue( array( "Center", "Custom" ) );
		$imgpos->setValueCSS( array(
			"Center" 	=> ".oxy-ou-acrd-item{background-position: center;}"
		));
		$imgpos->setDefaultValue("Center");
		$imgpos->whiteList();

		$custPos = $gallery->addStyleControl([
			'selector' 		=> '.oxy-ou-acrd-item',
			'name' 			=> __('Position Left'),
			'slug' 			=> 'acfimg_cust_posleft',
			'property' 		=> 'background-position-x',
			'control_type'	=> 'slider-measurebox',
			'condition' 	=> 'imgpos=Custom'
		]);
		$custPos->setRange('0', '100', '5');
		$custPos->setUnits( '%', '%' );

		$custPosRght = $gallery->addStyleControl([
			'selector' 		=> '.oxy-ou-acrd-item',
			'name' 			=> __('Position Right'),
			'slug' 			=> 'acfimg_cust_posright',
			'property' 		=> 'background-position-y',
			'control_type'	=> 'slider-measurebox',
			'condition' 	=> 'imgpos=Custom'
		]);
		$custPosRght->setRange('0', '100', '5');
		$custPosRght->setUnits( '%', '%' );

		$gallery->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> 'Display Horizontally on Mobile',
			'slug' 		=> 'ouacfacrd_rp',
			'value' 	=> [ 'yes' => __( "Yes" ), "no" => __( "No" ) ],
			'default' 	=> 'no'
		]);

		/***************************
		 * Lightbox
		 **************************/
		$lightboxSec = $this->addControlSection( 'ouacf_lightboxsec', __('Lightbox', "oxy-ultimate"), 'assets/icon.png', $this );

		$lightboxSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Enable Lightbox"),
				"slug" 		=> "ouacfa_lightbox",
				"value" 	=> array(
					"yes" 		=> __("Yes", "oxy-ultimate"), 
					"no" 		=> __("No", "oxy-ultimate")
				),
				"default" 	=> "no",
				"css" 		=> false
			)
		);

		$lightboxSec->addOptionControl(
			array(
				"name" 			=> __('Animation Effect', "oxy-ultimate"),
				"slug" 			=> "image_aimeffect",
				"type" 			=> 'radio',
				"value" 		=> [
					"none" 					=> __( 'None', "oxy-ultimate" ),
					"mfp-zoom-in" 			=> __( 'Zoom', "oxy-ultimate" ), 
					"mfp-newspaper" 		=> __( 'Newspaper', "oxy-ultimate" ), 
					'mfp-move-horizontal' 	=> __( 'Move Horizontal', "oxy-ultimate" ), 
					'mfp-move-from-top' 	=> __( 'Move from Top', "oxy-ultimate" ), 
					'mfp-3d-unfold' 		=> __( '3d Unfold', "oxy-ultimate" ),
					'mfp-zoom-out' 			=> __( 'Zoom Out', "oxy-ultimate" )
				],
				"default"		=> "mfp-zoom-in"
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_acrd_ouacfa_lightbox']=='yes'");

		/***************************
		 * Image Hover Animation
		 **************************/
		$hvAnim = $this->addControlSection( 'hover_animation', __('Hover Animation', "oxy-ultimate"), 'assets/icon.png', $this );

		$flexGrow = $hvAnim->addStyleControl([
			'selector' 		=> '.oxy-ou-acrd-item:hover',
			'property' 		=> 'flex-grow',
			'control_type' 	=> 'slider-measurebox'
		]);
		$flexGrow->setRange( '2', '10', '1');
		$flexGrow->setValue( '4' );
		$flexGrow->setUnits(' ',' ');

		$td = $hvAnim->addStyleControl([
			'selector' 		=> '.oxy-ou-acrd-item',
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox'
		]);
		$td->setRange( '0', '10', '0.1');
		$td->setValue( '0.8' );
		$td->setUnits( 's', 'sec');

		$hvScale = $hvAnim->addControlSection( 'hover_sacle', __('Transform Scale', "oxy-ultimate"), 'assets/icon.png', $this );

		$scaleX = $hvScale->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Scale X', "oxy-ultimate"),
			'slug' 		=> 'ouacfa_scaleX'
		]);

		$scaleX->setUnits(' ',' ');
		$scaleX->setRange('1', '10', '1');

		$scaleY = $hvScale->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Scale Y', "oxy-ultimate"),
			'slug' 		=> 'ouacfa_scaleY'
		]);

		$scaleY->setUnits(' ',' ');
		$scaleY->setRange('1', '10', '1');

		$scaleZ = $hvScale->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Scale Z', "oxy-ultimate"),
			'slug' 		=> 'ouacfa_scaleZ'
		]);

		$scaleZ->setUnits(' ',' ');
		$scaleZ->setRange('1', '10', '1');
		
		$hvAnim->boxShadowSection( __('Image Box Shadow'), '.oxy-ou-acrd-item:hover', $this );

		/*****************************
		 * Caption Settings
		 ****************************/
		$capSet = $this->addControlSection(  'ouacfa_capset', __('Caption Settings', "oxy-ultimate"),'assets/icon.png', $this );

		$capSet->addOptionControl(
			array(
				"name" 			=> __('Caption Appearance', "oxy-ultimate"),
				"slug" 			=> "ouacfa_showcap",
				"type" 			=> 'radio',
				"value" 		=> [ "no" => __("No"), "d" => __("Default"), "hover" => __("Show on Hover"), "hide" => __("Hide on Hover")],
				"default"		=> "no"
			)
		);

		$capSet->addOptionControl(
			array(
				"name" 			=> __('Display Description', "oxy-ultimate"),
				"slug" 			=> "ouacfa_showdesc",
				"type" 			=> 'radio',
				"value" 		=> [ "no" => __("No"), "yes" => __("Yes") ],
				"default"		=> "no"
			)
		);

		$capTd = $capSet->addStyleControl([
			'selector' 		=> '.oxy-ou-acrd-item:hover > .ouacf-acrd-caption,.oxy-ou-acrd-item > a:hover .ouacf-acrd-caption',
			'property' 		=> 'transition-delay',
			'control_type' 	=> 'slider-measurebox',
			'slug' 			=> 'caph_td'
		]);
		$capTd->setRange( '0', '5', '0.1');
		$capTd->setValue( '0.5' );
		$capTd->setUnits( 's', 'sec');
		$capTd->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_acrd_ouacfa_showcap']!='d'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_acrd_ouacfa_showcap']!='no'");

		$titlePos = $capSet->addControl( 'buttons-list', 'cap_pos', __('Vertical Alignment', "oxy-ultimate") );
		$titlePos->setValue( array( "Top", "Center", 'Bottom' ) );
		$titlePos->setValueCSS( array(
			"Top" 		=> " .oxy-ou-acrd-item{align-items: flex-start;} .oxy-ou-acrd-item > a{align-items: flex-start;}",
			"Center" 	=> " .oxy-ou-acrd-item{align-items: center;} .oxy-ou-acrd-item > a{align-items: center;}",
			"Bottom" 	=> " .oxy-ou-acrd-item{align-items: flex-end;} .oxy-ou-acrd-item > a{align-items: flex-end;}"
		));
		$titlePos->setDefaultValue("Top");
		$titlePos->whiteList();

		$capSet->addStyleControl([
			'selector' 		=> '.ouacf-acrd-caption',
			'property' 		=> 'background-color',
			'slug' 			=> 'cap_bgc'
		]);

		$capSpacing = $capSet->addControlSection( 'ouacfa_capspace', __('Spacing', "oxy-ultimate"), 'assets/icon.png', $this );

		$capSpacing->addPreset(
			"padding",
			"capsp_padding",
			__("Padding", "oxy-ultimate"),
			'.ouacf-acrd-caption'
		)->whiteList();

		$capSet->typographySection(__('Title Typography'), '.caption-title', $this);
		$capSet->typographySection(__('Description Typography'), '.caption-desc', $this);
	}

	function render( $options, $defaults, $content ) {

		if( empty($options['field_name'] ) ) {
			echo __("Enter ACF Gallery Field Key.", "oxy-ultimate");
		} else {
			echo '<div class="ouacf-acrd-wrap">';

			if( $options['ouacfa_lightbox'] === 'yes' ) {
				if( ! $this->lghbox_added && ! defined('OXY_ELEMENTS_API_AJAX') ) {
					wp_enqueue_style('ou-mfp-style');
					wp_enqueue_script('ou-mfp-script');
					wp_add_inline_script('ou-mfp-script', "(function($){ $(function(){
						if( ! $('body').hasClass('ct_inner') && $('.oxy-ou-acrd-item a.ouimgp-lightbox').length) {
							$('.oxy-ou-acrd-item a.ouimgp-lightbox').magnificPopup( {
								midClick 			: true,
								closeBtnInside 		: false,
								fixedContentPos 	: false,
								type 				: 'image',
								gallery: {
									enabled: true
								},
								removalDelay: 500,
								callbacks: {
									beforeOpen: function() { 
										this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
										this.st.mainClass = this.st.el.attr('data-effect');
									}
								},
							});
						}
					});})(jQuery);");

					$this->lghbox_added = true;
				}
			}

			if( ! empty( $options['acfg_source'] ) && $options['acfg_source'] === 'import' ) {
				$images = get_post_meta( (int) $options['page_id'], $options['field_name'], true );
			} else {
				$images = get_post_meta( get_the_id(), $options['field_name'], true );
			}
			if( $images ) : 
				$linkOpenTag = $linkCloseTag = '';
				foreach( $images as $i => $imageID ) {
					$acfImg = wp_get_attachment_image_src( $imageID, 'full' );

					echo '<div class="oxy-ou-acrd-item" style="background-image:url(' . esc_url( $acfImg[0] ) . ');">';

					if( $options['ouacfa_lightbox'] === 'yes' ) {
						$linkOpenTag = '<a href="' . esc_url( $acfImg[0] ) . '" class="ouimgp-lightbox" data-effect="' . $options['image_aimeffect'] .'">';
						$linkCloseTag = '</a>';
					}

					echo $linkOpenTag;

						if( $options['ouacfa_showcap'] != "no" )
						{
							$data = $this->ouipacf_get_attachment_data( $imageID );
							echo '<div class="ouacf-acrd-caption">';
							
							$title = ! empty($data->title) ? $data->title : ( ! empty($data->alt) ? $data->alt : '');
							if( $title ) {
								echo '<h3 class="caption-title">' . $title . '</h3>';
							}

							if( $options['ouacfa_showdesc'] != "no" )
							{
								$desc = ! empty($data->description) ? $data->description : ( ! empty($data->caption) ? $data->caption : '');
								if( $desc) {
									echo '<p class="caption-desc">' . $desc . '</p>';
								}
							}

							echo '</div>';
						}

					echo $linkCloseTag;

					echo '</div>';
				}

				if( ! $this->script_added ) {
					$this->El->footerJS("
						jQuery(document).ready(function($){
							$('.oxy-ou-acrd-item').bind('touchstart touchmove', function(event) {
								event.stopPropagation();
							});
						});"
					);
					$this->script_added = true;
				}
			
			endif;

			echo '</div>';
		}
	}

	function customCSS( $original, $selector ) {
		$css =  $transform = '';
		$prefix = $this->El->get_tag();

		// Scale
		if ( isset($original[$prefix . '_ouacfa_scaleX']) && isset($original[$prefix . '_ouacfa_scaleY']) && isset($original[$prefix . '_ouacfa_scaleZ']) &&
			$original[$prefix . '_ouacfa_scaleX'] !== "" && $original[$prefix . '_ouacfa_scaleY'] !== "" && $original[$prefix . '_ouacfa_scaleZ'] !== "" ) {
			$transform .= "scale3d(" 
					. $original[$prefix . '_ouacfa_scaleX'] . "," 
					. $original[$prefix . '_ouacfa_scaleY'] . ","
					. $original[$prefix . '_ouacfa_scaleZ'] . ")";
		}
		else if (isset($original[$prefix . '_ouacfa_scaleX']) && isset($original[$prefix . '_ouacfa_scaleY']) &&
				 $original[$prefix . '_ouacfa_scaleX'] !== "" && $original[$prefix . '_ouacfa_scaleY'] !== "") {
			$transform .= "scale(" 
				. $original[$prefix . '_ouacfa_scaleX'] . "," 
				. $original[$prefix . '_ouacfa_scaleY'] . ")";
		}
		else {

			if (isset($original[$prefix . '_ouacfa_scaleX']) &&
					 $original[$prefix . '_ouacfa_scaleX'] !== "") {
				$transform .= " scaleX(" . $original[$prefix . '_ouacfa_scaleX'] . ")";
			}

			if (isset($original[$prefix . '_ouacfa_scaleY']) &&
					 $original[$prefix . '_ouacfa_scaleY'] !== "") {
				$transform .= " scaleY(" . $original[$prefix . '_ouacfa_scaleY'] . ")";
			}

			if (isset($original[$prefix . '_ouacfa_scaleZ']) &&
					 $original[$prefix . '_ouacfa_scaleZ'] !== "") {
				$transform .= " scaleZ(" . $original[$prefix . '_ouacfa_scaleZ'] . ")";
			}
		}

		if( ! empty( $transform )) {
			$css .= $selector .' .oxy-ou-acrd-item:hover{transform:' . $transform . ';}';
		}

		if( $original[$prefix .'_ouacfa_showcap'] == 'hover' ) {
			$css .= $selector . ' .ouacf-acrd-caption{transition:all 300ms ease;transform: scale(0,0);-webkit-transition:all 300ms ease;-webkit-transform: scale(0,0);-moz-transition:all 300ms ease;-moz-transform: scale(0,0);}';
			$css .= $selector . ' .oxy-ou-acrd-item:hover > .ouacf-acrd-caption,'. $selector .' .oxy-ou-acrd-item > a:hover .ouacf-acrd-caption{transform:scale(1,1);-webkit-transform:scale(1,1);-moz-transform:scale(1,1);}';

			if( isset( $original[$prefix . '_caph_td'] ) ) {
				$css .= $selector .' .oxy-ou-acrd-item:hover > .ouacf-acrd-caption,'. $selector .' .oxy-ou-acrd-item > a:hover .ouacf-acrd-caption{transition-delay:'.$original[$prefix . '_caph_td'].'s;}';
			}
		}

		if( $original[$prefix .'_ouacfa_showcap'] == 'hide' ) {
			$css .= $selector . ' .ouacf-acrd-caption{transition:all 300ms ease;transform: scale(1,1);-webkit-transition:all 300ms ease;-webkit-transform: scale(1,1);-moz-transition:all 300ms ease;-moz-transform: scale(1,1);}';
			$css .= $selector . ' .oxy-ou-acrd-item:hover > .ouacf-acrd-caption,'. $selector .' .oxy-ou-acrd-item > a:hover .ouacf-acrd-caption{transform:scale(0,0);-webkit-transform:scale(0,0);-moz-transform:scale(0,0);}';

			if( isset( $original[$prefix . '_caph_td'] ) ) {
				$css .= $selector .' .oxy-ou-acrd-item:hover > .ouacf-acrd-caption,'. $selector .' .oxy-ou-acrd-item > a:hover .ouacf-acrd-caption{transition-delay:'.$original[$prefix . '_caph_td'].'s;}';
			}
		}

		if( $original[$prefix .'_cap_pos'] == 'Top' ) {
			$css .= $selector .' .oxy-ou-acrd-item > a {align-items: flex-start}';
		}

		if( $original[$prefix .'_cap_pos'] == 'Center' ) {
			$css .= $selector .' .oxy-ou-acrd-item > a {align-items: center}';
		}

		if( $original[$prefix .'_cap_pos'] == 'Bottom' ) {
			$css .= $selector .' .oxy-ou-acrd-item > a {align-items: flex-end}';
		}

		if( $this->css_added == false ) {
			$css .= '.oxy-ou-acf-acrd{display: flex; flex-direction: row; width: 100%; position: relative;}';
			$css .= '.ouacf-acrd-wrap{display: flex; flex-direction: row; flex-wrap: nowrap; align-items: flex-start; width: 100%; height: 450px; position: relative;}';

			$css .= '.oxy-ou-acrd-item{background-size: cover; height: 100%; flex: 1;transition-duration: 0.8s;transition-timing-function: ease;transition-property: all;background-repeat: no-repeat; background-position: center; position: relative; z-index: 1; display: flex; overflow:hidden;}';
			$css .= '.oxy-ou-acrd-item .ouacf-acrd-caption{width: 100%; padding: 10px; position: relative; z-index: 7;}';
			$css .= '.oxy-ou-acrd-item > a{position: relative; z-index: 28; width: 100%; height: 100%; display: inline-flex; text-decoration: none;}';
			$css .= '.oxy-ou-acrd-item:hover{flex-grow: 4; z-index: 5;}';

			$this->css_added = true;
		}

		if( isset( $original[$prefix . '_ouacfacrd_rp'] ) && $original[$prefix . '_ouacfacrd_rp'] == "yes" ) {
			$css .= '@media only screen and (max-width: 768px){ '. $selector .' .ouacf-acrd-wrap{flex-direction: column;} '. $selector .' .oxy-ou-acrd-item{width: 100%;} }';
		}

		return $css;
	}

	function ouipacf_get_attachment_data( $id ) {
		$data = wp_prepare_attachment_for_js( $id );

		if ( gettype( $data ) == 'array' ) {
			return json_decode( json_encode( $data ) );
		}

		return $data;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUACFGalleryAccordion();