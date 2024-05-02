<?php

namespace Oxygen\OxyUltimate;

class OUGallery extends \OxyUltimateEl {
	
	public $has_js = true;
	public $css_added = false;
	private $gallery_js_code = '';

	function name() {
		return __( "Ultimate Gallery", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_acf_gallery";
	}

	function oxyu_button_place() {
		return "images";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		/*$layout = $this->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Layout Type", "oxy-ultimate"),
				"slug" 		=> "acfg_layout",
				"value" 	=> array("ly-one" => __("Grid with animation effect"), "no" => __("Select Layout")),
				"default" 	=> "no"
			)
		);
		$layout->rebuildElementOnChange();*/

		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7;font-size: 13px; line-height: 1.325">You will enter the <span style="color:#ff7171;">_product_image_gallery</span> into the Gallery Field Name input field for WooCommerce product\'s gallery images.<br/><br/>Click on the <span style="color:#ff7171;">Apply Params</span> button to get the correct preview on Builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$gsource = $this->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Gallery Source"),
				"slug" 		=> "ouacfg_source",
				"value" 	=> array(
					"acf" 		=> __("Custom Field", "oxy-ultimate"),
					"media" 	=> __("Media Library", "oxy-ultimate")
				),
				"default" 	=> "acf"
			)
		);

		$wpGallery = $this->addCustomControl("
			<div class='oxygen-control'>
				<div class='oxygen-file-input'
				ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'ouacfg_images')}\">
					<input type=\"text\" spellcheck=\"false\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['ouacfg_images']\" ng-model-options=\"{ debounce: 10 }\"
						ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ou_acf_gallery','ouacfg_images');\">
					<div class=\"oxygen-file-input-browse\"
						data-mediaTitle=\"Select Images\" 
						data-mediaButton=\"Select Images\"
						data-mediaMultiple=\"true\"
						data-mediaProperty=\"ouacfg_images\"
						data-mediaType=\"gallery\">". __("browse","oxygen") . "
					</div>
				</div>
			</div>",
			'ouacfg_images'
    	);
    	$wpGallery->setParam('heading', __('Image IDs', 'oxy-ultimate'));
		$wpGallery->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_gallery_ouacfg_source']=='media'");

		$source = $this->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Images Source"),
				"slug" 		=> "acfg_source",
				"value" 	=> array(
					"same" 		=> __("Same Post/Page", "oxy-ultimate"), 
					"import" 	=> __("Other", "oxy-ultimate")
				),
				"default" 	=> "same"
			)
		);
		$source->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_gallery_ouacfg_source']!='media'");

		$import = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Enter Post/Page ID", "oxy-ultimate"),
				"slug" 		=> "page_id"
			)
		);
		$import->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_gallery_acfg_source']=='import'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_gallery_ouacfg_source']!='media'");
		$import->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicOUGPID">data</div>');
		$import->rebuildElementOnChange();

		$gf_name = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Gallery Field Name", "oxy-ultimate"),
				"slug" 		=> "field_name"
			)
		);
		$gf_name->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_gallery_ouacfg_source']!='media'");
		$gf_name->rebuildElementOnChange();

		$boxName = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("ACPT Metabox Name", "oxy-ultimate"),
				"slug" 		=> "box_name",
			)
		);
		$boxName->setParam('description', "It is for ACPT plugin. You will enter the box name here");
		$boxName->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_gallery_ouacfg_source']!='media'");
		$boxName->rebuildElementOnChange();

		$limit = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Items Limit", "oxy-ultimate"),
				"slug" 		=> "item_limit"
			)
		);
		$limit->setParam('description', "By default it will show all images.");
		$limit->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_acf_gallery_ouacfg_source']!='media'");
		$limit->rebuildElementOnChange();

		$this->addStyleControl(
			array(
				'control_type' 	=> 'radio',
				"name" 			=> __("Images Per Row", "oxy-ultimate"),
				"slug" 			=> "acfg_col",
				'selector' 		=> '.ouacfga-grid-all > *',
				"property" 		=> 'width',
				"value" 		=> array(
					"100" 			=> "1", 
					"50" 			=> "2", 
					"33.33" 		=> "3", 
					"25" 			=> "4",
					"20" 			=> "5"
				),
				"default" 	=> "25"
			)
		)
		->setUnits('%', '%')
		->rebuildElementOnChange();

		$thumb_size = $this->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Thumbnail Resolution", "oxy-ultimate"),
				"slug" 		=> "acfg_thumb_size",
				"value" 	=> $this->oxyu_thumbnail_sizes(),
				"default" 	=> "full"
			)
		);
		$thumb_size->rebuildElementOnChange();

		$aspect = $this->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Aspect Ratio", "oxy-ultimate"),
				"slug" 		=> "acfg_aspectratio",
				"value" 	=> "16:9",
				"default" 	=> "16:9"
			)
		);
		$aspect->rebuildElementOnChange();

		$acfg_anim = $this->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Animation Type", "oxy-ultimate"),
				"slug" 		=> "acfg_animt",
				"value" 	=> array( 
					'none' 				=> 'None', 
					'fadeInLeft' 		=> 'fadeInLeft',
					'fadeInLeftSmall' 	=> 'fadeInLeftSmall', 
					'fadeInLeftBig' 	=> 'fadeInLeftBig',
					'fadeInRight' 		=> 'fadeInRight',
					'fadeInRightSmall' 	=> 'fadeInRightSmall', 
					'fadeInRightBig' 	=> 'fadeInRightBig', 
					'flipInX' 			=> 'flipInX', 
					'flipInY' 			=> 'flipInY' 
				),
				"default" 	=> "none"
			)
		);
		$acfg_anim->rebuildElementOnChange();

		$this->addStyleControl(
			array(
				'control_type' 	=> 'slider-measurebox',
				"property" 		=> 'padding',
				'selector' 		=> '.ou-gallery-acf > *',
				'name' 			=> __('Gap Between Images ', "oxy-ultimate"),
				'default' 		=> "10"
			)
		)
		->setUnits('px', 'px')
		->setRange('0', '30', '5')
		->rebuildElementOnChange();

		$lb = $this->addControlSection( "lightbox_sec", __("Lightbox", "oxy-ultimate"), "assets/icon.png", $this );
		
		$lb->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Image Resolution", "oxy-ultimate"),
				"slug" 		=> "lg_image_size",
				"value" 	=> $this->oxyu_thumbnail_sizes(),
				"default" 	=> "full"
			)
		);
		
		$zoom = $lb->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Zoom Duration", "oxy-ultimate"),
				"slug" 		=> "acfg_zoomdur",
				"value" 	=> '300',
				"default" 	=> "300"
			)
		);
		$zoom->setParam('description', "Duration of the lightbox zoom effect, in milliseconds");

		$lb->addOptionControl(
			array(
				'type' 		=> 'radio',
				'slug' 		=> 'ouacfg_fixedcontentpos',
				'name' 		=> __('Disable Scrolling', "oxy-ultimate"),
				'value' 	=> ["auto" => __("No"), "yes" => __("Yes")],
				'default' 	=> 'auto'
			)
		);

		$lb->addOptionControl(
			array(
				"type" 		=> 'icon_finder',
				"name" 		=> __('Close Icon', "oxy-ultimate"),
				"slug" 		=> 'close_icon',
				"default" 	=> 'Lineariconsicon-cross',
				"default" 	=> 'Lineariconsicon-cross'
			)
		);

		$style_section = $this->addControlSection( "acfg_style", __("Caption Style", "oxy-ultimate"), "assets/icon.png", $this );
		$style_section->addOptionControl(
			array(
				'type' 		=> 'radio',
				'slug' 		=> 'ouacfg_show_caption',
				'name' 		=> __('Show Caption', "oxy-ultimate"),
				'value' 	=> ["yes" => __("Yes"), "no" => __("No")],
				'default' 	=> 'no'
			)
		);

		$style_section->addOptionControl(
			array(
				'type' 		=> 'radio',
				'slug' 		=> 'ouacfg_caption_pos',
				'name' 		=> __('Caption Position', "oxy-ultimate"),
				'value' 	=> ["overlay" => __("Overlay"), "below" => __("Bottom")],
				'default' 	=> 'below'
			)
		)->setCondition('ouacfg_show_caption=yes');

		$style_section->addOptionControl(
			array(
				'type' 		=> 'radio',
				'slug' 		=> 'ouacfg_caption_on_hover',
				'name' 		=> __('Show Caption On Hover', "oxy-ultimate"),
				'value' 	=> ["yes" => __("Yes"), "no" => __("No")],
				'default' 	=> 'no'
			)
		)->setCondition('ouacfg_caption_pos=overlay');

		$style_section->addStyleControls(
			array(
				array(
					'selector'	=> '.ouacfga-caption',
					'name' 		=> __('Background Color', "oxy-ultimate"),
					"property" 	=> "background-color"
				)
			)
		);

		$spacing = $style_section->addControlSection( "cap_spacing", __("Spacing", "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"ouacfgcp_padding",
			__("Padding"),
			'.image-caption-inner'
		)->whiteList();

		$style_section->typographySection( __('Title Typography', "oxy-ultimate"), '.image-caption-inner h3', $this);
		$style_section->typographySection( __('Description Typography', "oxy-ultimate"), '.image-description', $this);


		$img_heffects = $this->addControlSection( "oug_imgheffect", __("Image Hover Effects", "oxy-ultimate"), "assets/icon.png", $this );
		$imghts = $img_heffects->addStyleControl([
			'name' 			=> __('Transition Duration'),
			'selector' 		=> '.lightbox-ouacfga-item',
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox'
		]);
		$imghts->setRange('0', '20', '.1');
		$imghts->setUnits('s', 'sec');
		$imghts->setDefaultValue('0.5');

		$imgInitialtrotate = $img_heffects->addStyleControl([
			'name' 			=> __('Rotate (Initial State)'),
			'selector' 		=> '.ou-gallery-thumb',
			'property' 		=> '--thumb-initial-rotate',
			'control_type' 	=> 'slider-measurebox'
		]);
		$imgInitialtrotate->setRange(-360, 360, 1);
		$imgInitialtrotate->setUnits('deg', 'deg');

		$imghtrotate = $img_heffects->addStyleControl([
			'name' 			=> __('Rotate (Hover State)'),
			'selector' 		=> '.ou-gallery-thumb',
			'property' 		=> '--thumb-hover-rotate',
			'control_type' 	=> 'slider-measurebox'
		]);
		$imghtrotate->setRange(-360, 360, 1);
		$imghtrotate->setUnits('deg', 'deg');

		$imgIntitalScale = $img_heffects->addStyleControl([
			'name' 			=> __('Scale (Initial State)'),
			'selector' 		=> '.ou-gallery-thumb',
			'property' 		=> '--thumb-initial-scale',
			'control_type' 	=> 'slider-measurebox',
		]);
		$imgIntitalScale->setRange(-5, 5, 0.01);
		$imgIntitalScale->setUnits('', ' ');
		$imgIntitalScale->setDefaultValue('1');

		$imghtscale = $img_heffects->addStyleControl([
			'name' 			=> __('Scale (Hover State)'),
			'selector' 		=> '.ou-gallery-thumb',
			'property' 		=> '--thumb-hover-scale',
			'control_type' 	=> 'slider-measurebox',
		]);
		$imghtscale->setRange(-5, 5, 0.01);
		$imghtscale->setUnits('', ' ');
		$imghtscale->setDefaultValue('1');
	}

	function render( $options, $content, $defaults ) {
		$images = '';
		$uid = str_replace( ".", "", uniqid( 'ouacf', true ) );

		if( ! empty( $options['ouacfg_source'] ) && $options['ouacfg_source'] == 'media' && isset( $options['ouacfg_images'] ) ) {
			$images = explode( ",", $options['ouacfg_images'] );
		} else {
			if( empty($options['field_name'] ) ) {
				echo __("Enter Custom Gallery Field Key.", "oxy-ultimate");
			} else {
				if( ! empty( $options['acfg_source'] ) && $options['acfg_source'] === 'import' ) {
					$post_id = (int) $options['page_id'];
					if( strstr( $post_id, 'oudata_') ) {
						$post_id = base64_decode( str_replace( 'oudata_', '', $post_id ) );
						$shortcode = ougssig( $this->El, $post_id );
						$post_id = do_shortcode( $shortcode );
					} elseif( strstr( $post_id, '[oxygen') ) {
						$shortcode = ct_sign_oxy_dynamic_shortcode(array($post_id));
						$post_id = do_shortcode($shortcode);
					}
				} else {
					$post_id = get_the_id();
				}
				
				$meta_single = true;
				
				if( class_exists('RWMB_Loader') ) {
					$settings = rwmb_get_field_settings( $options['field_name'] );
					if( ! empty( $settings ) && is_array( $settings ) ) {
						if( $settings['type'] == 'image_advanced' ) {
							$meta_single = false;
						}
					}
				}

				if( class_exists('WooCommerce') && $options['field_name'] == '_product_image_gallery' ) {
					$images = array();
					if( has_post_thumbnail($post_id) && is_singular('product') ) {
						$images[] = get_post_thumbnail_id($post_id);
					}

					$product = wc_get_product($post_id);
					if (@method_exists($product, 'get_gallery_attachment_ids')) {
						$images = @array_merge( $images, $product->get_gallery_attachment_ids() );
					}
				} else {
					//$images = get_post_meta( $post_id, $options['field_name'], $meta_single );
					
					if( function_exists('get_sub_field') && get_sub_field( $options['field_name'] ) ) {
						$images = get_sub_field( $options['field_name'] );
					} elseif( function_exists('get_acpt_field') && isset( $options['box_name'] ) ) {
						$images = array();
						$acptimages = get_acpt_field(['post_id' => $post_id, 'box_name' => $options['box_name'], 'field_name' => $options['field_name'] ]);
						if( $acptimages ) {
							foreach( $acptimages as $key => $obj ) {
								$images[] = $obj->getId();
							}
						}
					} else {
						$images = get_post_meta( $post_id, $options['field_name'], $meta_single );
					}

					if( ! $images ) {
						$images = get_option( 'options_' . $options['field_name'] );
					}
				}

				if( isset( $images['pod_item_id'] ) ) {
					$images = get_post_meta( $post_id, '_pods_' . $options['field_name'], $meta_single );
				}

				if( function_exists('acf_photo_gallery') ) {
					$images = explode(",", $images);
				}
			}
		}

		$images = apply_filters( 'ou_gallery_images', $images );

		if( $images ) : 
			$scroll = isset( $options['ouacfg_fixedcontentpos'] ) ? $options['ouacfg_fixedcontentpos'] : 'auto';
			$closeBtn = isset( $options['close_icon'] ) ? $options['close_icon'] : 'Lineariconsicon-cross';

			global $oxygen_svg_icons_to_load;
			$oxygen_svg_icons_to_load[] = $closeBtn;
	?>
		<div class="ou-gallery-acf ou-acf-<?php echo $uid; ?> ouacfga-grid-all has-clearfix" data-site-scroll="<?php echo $scroll; ?>" data-trigger-entrances="true" data-id="<?php echo $uid; ?>" data-close-btn="<?php echo $closeBtn; ?>">
			<?php
				$delay_timer = 0;
				$title = '';
				
				$aspect_ratio = !empty($options['acfg_aspectratio']) ? $options['acfg_aspectratio'] : '16:9';
				$aspect_ratio = explode(":", $aspect_ratio);
				$aspect_ratio = ($aspect_ratio[1] / $aspect_ratio[0]);
				$aspect_ratio = number_format($aspect_ratio * 100, 2)."%";

				foreach( $images as $i => $imageID ) {

					if( ! empty( $options['item_limit'] ) && $i >= absint( $options['item_limit'] ) )
						break;

					
					if( strpos($options['acfg_thumb_size'], "x") > 0 )
						$thumb_size = explode("x", $options['acfg_thumb_size']);
					else
						$thumb_size = $options['acfg_thumb_size'];

					$thumbSrc = wp_get_attachment_image_src( $imageID, $thumb_size );
					$thumb = wp_get_attachment_image( $imageID, $thumb_size, "", ["data-image-url" => $thumbSrc[0]] );
					$lgImageSize = isset( $options['lg_image_size'] ) ? $options['lg_image_size'] : 'full';
					$largeImg = wp_get_attachment_image_src( $imageID, $lgImageSize );
					$data = $this->oxyu_get_attachment_data( $imageID );
					$entrance_data = $caption = null;
					if ( 'none' != $options['acfg_animt'] ) {
						$entrance_data  = ' data-entrance="' . $options['acfg_animt'] . '"';
						if ( $delay_timer ) {
							$entrance_data .= ' data-delay="' . $delay_timer . '"';
						}
						$entrance_data .= ' data-chained="true"';
						$delay_timer += 100;
					}

					if( $caption !== '' && 'yes' === $options['ouacfg_show_caption'] ) {
						if( ! empty( $data->title ) )
							$title = ' title="' . $data->title .'"';
						elseif( ! empty( $data->alt ) )
							$title = ' title="' . $data->alt .'"';
					}
			?>
				<div class="ou-gallery-height">
					<a class="lightbox-link" data-effect="mfp-zoom-in" href="<?php echo $largeImg[0]; ?>"<?php echo $title; ?> aria-label="<?php echo $caption; ?>">
						<div class="ou-gallery-thumb" style="padding-bottom: <?php echo $aspect_ratio; ?>;" <?php echo $entrance_data; ?>>
							<span class="lightbox-ouacfga-item" style="background-image: url(<?php echo $thumbSrc[0]; ?>);" data-image-url="<?php echo $thumbSrc[0]; ?>">                    
								<?php echo $thumb; ?>
							</span>
						</div>

						<?php if( $caption !== '' && 'yes' === $options['ouacfg_show_caption'] ) : 
								$title = '';
								if( ! empty( $data->title ) )
									$title = $data->title;
								elseif( ! empty( $data->alt ) )
									$title = $data->alt;

								if( $options['ouacfg_caption_on_hover'] == "yes" ) {
									$capClass = ' overlay-image-caption';
								} else {
									$capClass = ' normal-image-caption';
									$capClass .= ' cap-align-'.$options['ouacfg_caption_pos'];

									if( ! empty( $data->description ) )
										$caption = "<p class='image-description'>{$data->description}</p>";
									else
										$caption = '';
								}
						?>
							<div class="ouacfga-caption<?php echo $capClass;?>" <?php if( $options['ouacfg_caption_on_hover'] != "yes" ) { echo $entrance_data; } ?>> 
								<div class="image-caption-inner v-align-middle">
									<div>
										<h3><?php echo trim( strip_tags( $title ) ); ?></h3>
										<?php echo $caption; ?>
									</div>
								</div>								
							</div>
						<?php endif; ?>
					</a>
				</div>
			<?php } ?>
		</div>

	<?php
			if ( isset( $_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') )
			{
				$this->El->builderInlineJS("
					jQuery(document).ready(function(){
						ouGLB_" . $uid ." = new OUGalleryLightbox({
							id: 'ou-acf-" . $uid ."', 
							delegate: 'a.lightbox-link'
						});

						jQuery(document).on('ou-accordion-slide-complete', function(e) {
							if ( jQuery(e.target).find('.ou-acf-" . $uid ."').length > 0 ) {
								setTimeout(function(){ jQuery(window).trigger('resize');}, 10 );
							}
						});
					});"
				);
			} else {
				$this->gallery_js_code = "jQuery(document).ready(function(){
					jQuery('.ou-gallery-acf').each(function(){
						var galleryID = jQuery(this).attr('data-id');
						new OUGalleryLightbox({
							id: 'ou-acf-' + galleryID, 
							delegate: 'a.lightbox-link'
						});
						jQuery(document).on('ou-accordion-slide-complete', function(e) {
							if ( jQuery(e.target).find('.ou-acf-' + galleryID ).length > 0 ) {
								setTimeout(function(){ jQuery(window).trigger('resize');}, 10 );
							}
						});
						if ( jQuery('.oxy-tab').length > 0 ) {
							jQuery('.oxy-tab').on('click', function(e) {
								setTimeout(function(){ jQuery(window).trigger('resize');}, 5 );
							});
						}
						
						if ( jQuery('.oxy-modal-backdrop').length > 0 ) {
							jQuery( '.oxy-modal-backdrop' ).each(function( index ) {
								var modal = this,
									trigger = jQuery( modal ).data('trigger');
									if( 'user_clicks_element' == trigger ) {
										jQuery( jQuery( modal ).data( 'trigger-selector' ) ).click( function( event ) {
											setTimeout(function(){ jQuery(window).trigger('resize');}, 5 );
										});
									}
							});
						}
					});
				});";
				add_action( 'wp_footer', array( $this, 'oug_enqueue_scripts' ) );
				$this->El->footerJS( $this->gallery_js_code );
			}
		else :
			//echo __("There have no gallery images.", "oxy-ultimate");
		endif;
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}
		
		$prefix = $this->El->get_tag();
		if( isset( $original[$prefix. '_ouacfg_caption_pos'] ) && $original[$prefix. '_ouacfg_caption_pos'] == 'overlay' ) {
			$css .= $selector .' .ouacfga-caption{ width: calc(100% - 2 * ' . $original['oxy-ou_acf_gallery_slug_ougalleryacf_padding']. 'px);}';
			$css .= $selector .' .cap-align-overlay{left:' . $original['oxy-ou_acf_gallery_slug_ougalleryacf_padding']. 'px; bottom: ' . $original['oxy-ou_acf_gallery_slug_ougalleryacf_padding']. 'px;}';

			$css .= $selector .' .overlay-image-caption{left:' . $original['oxy-ou_acf_gallery_slug_ougalleryacf_padding']. 'px; top: ' . $original['oxy-ou_acf_gallery_slug_ougalleryacf_padding']. 'px; height: calc(100% - 2 * ' . $original['oxy-ou_acf_gallery_slug_ougalleryacf_padding']. 'px);}';
		}

		return $css;
	}

	/**
	 * Making thumbnail size list 
	 */ 
	function oxyu_thumbnail_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = $img_sizes =array();

		foreach( get_intermediate_image_sizes() as $s ) {
			$sizes[ $s ] = array( 0, 0 );
			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $s ][0] = get_option( $s . '_size_w' );
				$sizes[ $s ][1] = get_option( $s . '_size_h' );
			} else {
				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
			}
		}

		foreach( $sizes as $size => $atts ) {
			$size_title = ucwords(str_replace("-"," ", $size));
			$img_sizes[$size] =  $size_title . ' (' . implode( 'x', $atts ) . ')';
		}

		$img_sizes['full'] = __('Full');

		return $img_sizes;
	}

	function oxyu_get_attachment_data( $id ) {
		$data = wp_prepare_attachment_for_js( $id );

		if ( gettype( $data ) == 'array' ) {
			return json_decode( json_encode( $data ) );
		}

		return $data;
	}

	function init() {
		$this->El->useAJAXControls();

		if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'oug_enqueue_scripts' ) );
		}
	}

	function oug_enqueue_scripts() {
		wp_enqueue_style('ou-mfp-style');
		wp_enqueue_script('ou-waypoints-script');
		wp_enqueue_script('ou-mfp-script');
		wp_enqueue_script('ou-gallery-script');
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUGallery();
