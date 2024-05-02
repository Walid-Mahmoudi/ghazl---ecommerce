<?php
namespace Oxygen\OxyUltimate;

Class OUImage extends \OxyUltimateEl {
	//public $has_js = true;

	function name() {
		return __( "Add Image", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_image";
	}

	function button_place() {
		return "ouimage::comp";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/ou-ultimate-image.svg';
	}

	function controls() {
		$imgType = $this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> '',
			'slug' 		=> 'img_type',
			'value' 	=> array(
				1 		=> __('Image URL'),
				2 		=> __('Media Library')
			),
			'default' 	=> 1,
			'css' 		=> false
		]);

		$imgURL = $this->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_image_ou_image_url'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_image_ou_image_url')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_ou_image_url']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_image_ou_image_url\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicILImage\">data</div>
				</div>
			</div>",
			'ilimage'
		);
		$imgURL->setParam('heading', __('Image') );
		$imgURL->setParam('css', false );
		$imgURL->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_img_type']=='1'");
		$imgURL->rebuildElementOnChange();

		$imgLib = $this->El->addControl( "mediaurl", 'ou_image_id', __('ID') );
		$imgLib->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_img_type']=='2'");
		$imgLib->setParam('attachment', true);
		$imgLib->rebuildElementOnChange();

		$image_size = $this->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Size", "oxy-ultimate"),
				"slug" 		=> "ouimg_size",
				"value" 	=> $this->oxyu_image_sizes(),
				"default" 	=> "full",
				"css" 		=> false
			)
		);

		$image_size->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_image_img_type']=='2'");
		$image_size->rebuildElementOnChange();

		$this->addStyleControl([
			'selector' 		=> 'img.ou-image',
			'property' 		=> 'width',
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px'
		])->setParam('hide_wrapper_end', true);

		$this->addStyleControl([
			'selector' 		=> 'img.ou-image',
			'property' 		=> 'height',
			'control_type' 	=> 'measurebox',
			'unit' 			=> 'px'
		])->setParam('hide_wrapper_start', true);

		$imgAlt = $this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Alt Text'),
			'slug' 		=> 'img_alt',
			'css' 		=> false
		]);
		$imgAlt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicILAlt">data</div>');
	}

	function fetchDynamicData( $field ) {
		if( strstr( $field, 'oudata_') ) {
			$field = base64_decode( str_replace( 'oudata_', '', $field ) );
			$shortcode = ougssig( $this->El, $field );
			$field = do_shortcode( $shortcode );
		} elseif( strstr( $field, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($field));
			$field = do_shortcode($shortcode);
		}

		return $field;
	}

	function render( $options, $defaults, $content ) {
		
		$img_atts = $img_alt = '';

		if( isset( $options['img_alt'] ) ) {
			$img_alt = $this->fetchDynamicData( $options['img_alt'] );
		}

		$img_atts .= ' alt="' . $img_alt . '"';
		
		if( isset( $options['img_type'] ) && $options['img_type'] == 1 && isset($options['ou_image_url']) ) {

			$ilImgUrl = $this->fetchDynamicData( $options['ou_image_url'] );
			
			echo '<img src="' . esc_url( $ilImgUrl ) . '" class="ou-image"'.$img_atts.'/>';

		} elseif( isset( $options['ou_image_id'] ) ) {
			$attachment_id = intval( $options['ou_image_id'] );

			if ($attachment_id > 0) {
				
				$data = $this->oxyu_get_attachment_data( $attachment_id );
				$image_alt = ( empty( $img_alt ) ) ? ( ! empty( $data->title ) ? $data->title : $data->alt ) : $img_alt;
				$img_atts .= ' alt="' . $image_alt . '"';

				if( strpos($options['ouimg_size'], "x") > 0 )
					$thumb_size = explode("x", $options['ouimg_size']);
				else
					$thumb_size = $options['ouimg_size'];

				$attachment_size = isset($thumb_size) ? $thumb_size : 'thumbnail';
				$source = wp_get_attachment_image_src($attachment_id, $attachment_size);

				if (is_array($source)) {
					list($image_src, $image_width, $image_height) = $source;

					$srcset = wp_get_attachment_image_srcset($attachment_id, $attachment_size);

					echo '<img srcset="' . $srcset . '" sizes="(max-width: '.$image_width.'px) 100vw, '.$image_width.'px" src="' . esc_attr($image_src) . '" class="ou-image"'. $img_atts .' />';
				}
			}
		} else {
			echo '<img src="' . esc_url( OXYU_URL . 'assets/images/after-image.jpg' ) . '" class="ou-image"/>';
		}
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

	function oxyu_get_attachment_data( $id ) {
		$data = wp_prepare_attachment_for_js( $id );

		if ( gettype( $data ) == 'array' ) {
			return json_decode( json_encode( $data ) );
		}

		return $data;
	}
}

new OUImage();