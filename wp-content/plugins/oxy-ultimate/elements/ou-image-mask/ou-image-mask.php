<?php
namespace Oxygen\OxyUltimate;

class OUImageMask extends \OxyUltimateEl {
	public $css_added = false;

	function name() {
		return __( "Image Mask", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_image_mask";
	}

	function oxyu_button_place() {
		return "images";
	}

	function icon() {
		return CT_FW_URI . '/toolbar/UI/oxygen-icons/add-icons/image.svg';
	}

	function controls() {
		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Source'),
			'slug' 		=> 'img_type',
			'value' 	=> array(
				'img_url' 	=> __('Image URL'),
				'media' 	=> __('Media Library')
			),
			'default' 	=> 'img_url'
		]);

		$imgURL = $this->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_image_mask_image'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_image_mask_image')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_mask_image']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_image_mask_image\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicOUIMGM\">data</div>
				</div>
			</div>",
			'before_image'
		);
		$imgURL->setParam('heading', __('Upload Image', "oxy-ultimate") );
		$imgURL->setParam('description', __('Click on Apply Params button and see the image on builder editor', "oxy-ultimate") );
		$imgURL->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_mask_img_type']=='img_url'");
		$imgURL->rebuildElementOnChange();

		$this->addStyleControl([
			'selector' 		=> 'img.ou-image-mask--img',
			'property' 		=> 'width',
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px',
			"condition" => 'img_type=img_url'
		])->setParam('hide_wrapper_end', true);

		$this->addStyleControl([
			'selector' 		=> 'img.ou-image-mask--img',
			'property' 		=> 'height',
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px',
			"condition" 	=> 'img_type=img_url'
		])->setParam('hide_wrapper_start', true);

		$imgAlt = $this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Alt Text'),
			'slug' 		=> 'img_alt'
		]);
		$imgAlt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicMGMAlt">data</div>');
		$imgAlt->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_mask_img_type']=='img_url'");

		$imgLib = $this->El->addControl( "mediaurl", 'mask_image_id', __('Select from WP media library') );
		$imgLib->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_mask_img_type']=='media'");
		$imgLib->setParam('attachment', true);
		$imgLib->rebuildElementOnChange();

		$image_size = $this->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Image Size", "oxy-ultimate"),
				"slug" 		=> "image_size",
				"value" 	=> $this->oxyu_image_sizes(),
				"default" 	=> "medium",
				"condition" => 'img_type=media'
			)
		);
		$image_size->rebuildElementOnChange();

		$shapes = [];
		for ($i=1; $i <= 64 ; $i++) { 
			$shapes[ "shape-" . $i ] = "Shape " . $i;
		}

		$this->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> esc_html__( 'Mask Shape', 'ziultimate' ),
			'slug' 		=> 'mask_shape',
			'default' 	=> 'shape-1',
			'value' 	=> $shapes
		])->rebuildElementOnChange();

		$this->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Direction'),
			'slug' 		=> 'mask_direction'
		])->setValue(['none' => __('None'), 'h' => __('Horizontal'), 'v' => __('Vertical')])
		->setValueCSS([
			'h' 	=> " {transform: scaleX(-1);} 
					img {transform: scaleX(-1);}",
			'v' =>  " {transform: scaleY(-1);} 
					img {transform: scaleY(-1);}"
		])->setDefaultValue('none');

		$this->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Repeat'),
			'slug' 		=> 'mask_repeat'
		])->setValue([ 'no' => 'no-repeat', 'yes' => 'repeat'])
		->setValueCSS([
			'no' 	=> "{-webkit-mask-repeat: no-repeat;}",
			'yes' 	=>  "{-webkit-mask-repeat: repeat}"
		])->setDefaultValue('no');

		$this->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Position'),
			'slug' 		=> 'mask_position'
		])->setValue([ 
			'top' 		=> 'Top', 
			'bottom' 	=> 'Bottom', 
			'center' 	=> 'Center', 
			'left' 		=> 'Left', 
			'right' 	=> 'Right',
			'unset' 	=> 'Custom'
		])->setValueCSS([
			'top' 		=> "{-webkit-mask-position: top;}",
			'bottom' 	=>  "{-webkit-mask-position: bottom}",
			'center' 	=> "{-webkit-mask-position: center;}",
			'left' 		=>  "{-webkit-mask-position: left}",
			'right' 	=> "{-webkit-mask-position: right;}"
		])->setDefaultValue('center');

		$this->addStyleControl([
			"control_type" 	=> 'slider-measurebox',
			"name" 			=> __('X Offset'),
			"property" 		=> '-webkit-mask-position-x',
			"selector" 		=> ' ',
			"condition" 	=> 'mask_position=unset'
		])->setUnits('px','px,%')->setRange('1', '1000', 1)->setDefaultValue(300);

		$this->addStyleControl([
			"control_type" 	=> 'slider-measurebox',
			"name" 			=> __('Y Offset'),
			"property" 		=> '-webkit-mask-position-y',
			"selector" 		=> ' ',
			"condition" 	=> 'mask_position=unset'
		])->setUnits('px','px,%')->setRange('1', '1000', 1)->setDefaultValue(300);

		$this->addOptionControl([
			'type' 		=> 'buttons-list',
			'name' 		=> __('Size'),
			'slug' 		=> 'mask_size'
		])->setValue([ 
			'auto' 		=> 'Auto', 
			'contain' 	=> 'Contain', 
			'cover' 	=> 'Cover', 
			'custom' 	=> 'Custom'
		])->setValueCSS([
			'auto' 		=> "{-webkit-mask-size: auto;}",
			'contain' 	=>  "{-webkit-mask-size: contain}",
			'cover' 	=> "{-webkit-mask-size: cover;}"
		])->setDefaultValue('cover');

		$this->addStyleControl([
			"control_type" 	=> 'slider-measurebox',
			"name" 			=> __('Set Custom Size'),
			"property" 		=> '-webkit-mask-size',
			"selector" 		=> ' ',
			"condition" 	=> 'mask_size=custom'
		])->setUnits('px','px,%')->setRange('1', '10000', 1)->setDefaultValue(500);
	}

	/**
	 * Making thumbnail size list 
	 */ 
	function oxyu_image_sizes() {
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

	function fetchDynamicData( $field ) {
		if( strstr( $field, 'oudata_') ) {
			$field = base64_decode( str_replace( 'oudata_', '', $field ));
			$shortcode = ougssig( $this->El, $field );
			$field = do_shortcode( $shortcode );
		} elseif( strstr( $field, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($field));
			$field = do_shortcode($shortcode);
		}

		return $field;
	}

	function render( $options, $defaults, $content ) {
		$img_type = isset( $options['img_type'] ) ? $options['img_type'] : 'img_url';
		$img_atts = $img_alt = '';

		if( $img_type == 'img_url' && isset($options['image']) ) {

			$image = $this->fetchDynamicData( $options['image'] );
			
			if( isset( $options['img_alt'] ) ) {
				$img_alt = $this->fetchDynamicData( $options['img_alt'] );
			}

			$img_atts .= ' alt="' . $img_alt . '"';

			echo '<img src="' . esc_url( $image ) . '" class="ou-image-mask--img"'.$img_atts.'/>';

		} elseif( isset( $options['mask_image_id'] ) ) {
			$attachment_id = intval( $options['mask_image_id'] );

			if ($attachment_id > 0) {
				
				$data = $this->oxyu_get_attachment_data( $attachment_id );
				$image_alt = empty( $img_alt ) ? ( ! empty( $data->alt ) ? $data->alt : $data->title ) : $img_alt;
				$img_atts .= ' alt="' . $image_alt . '"';

				if( strpos( $options['image_size'], "x" ) > 0 )
					$attachment_size = explode("x", $options['image_size']);
				else
					$attachment_size = isset( $options['image_size'] ) ? $options['image_size'] : 'medium';

				$source = wp_get_attachment_image_src($attachment_id, $attachment_size);

				if ( is_array($source) ) {
					list($image_src, $image_width, $image_height) = $source;

					$srcset = wp_get_attachment_image_srcset($attachment_id, $attachment_size);

					echo '<img srcset="' . $srcset . '" sizes="(max-width: '.$image_width.'px) 100vw, '.$image_width.'px" src="' . esc_attr($image_src) . '" class="ou-image-mask--img" width="' . $image_width . '" height="' . $image_height . '"'. $img_atts .' />';
				}
			}
		} else {
			echo '<img src="' . esc_url( OXYU_URL . 'assets/images/after-image.jpg' ) . '" class="ou-image-mask--img" />';
		}
	}

	function oxyu_get_attachment_data( $id ) {
		$data = wp_prepare_attachment_for_js( $id );

		if ( gettype( $data ) == 'array' ) {
			return json_decode( json_encode( $data ) );
		}

		return $data;
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-image-mask {
						-webkit-mask-size: cover;
					    -webkit-mask-repeat: no-repeat;
					    -webkit-mask-position: center center;
					}
					.oxy-ou-image-mask img {
					    height: 100%;
					    width: 100%;
					}';

			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();
		$path = OXYU_URL . 'elements/ou-image-mask/shapes';
		if( isset( $original[$prefix . '_mask_shape'] ) ) {
			$shape = $original[$prefix . '_mask_shape'];
			$css .= "$selector {-webkit-mask-image: url({$path}/{$shape}.svg); mask-image: url({$path}/{$shape}.svg);}";
		}

		return $css;
	}
}

new OUImageMask();