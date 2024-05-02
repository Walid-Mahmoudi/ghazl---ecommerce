<?php

namespace Oxygen\OxyUltimate;

class OUHotspot extends \OxyUltimateEl {

	public $css_added = false;

	function name() {
		return __( "Hotspot", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_hotspot";
	}

	function oxyu_button_place() {
		return "images";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_hotspot-elements-label"
				ng-if="isActiveName('oxy-ou_hotspot')&&!hasOpenTabs('oxy-ou_hotspot')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_hotspot-elements"
				ng-if="isActiveName('oxy-ou_hotspot')&&!hasOpenTabs('oxy-ou_hotspot')">
				<?php do_action("oxygen_add_plus_ouhpmarker_comp"); ?>
			</div>
		<?php }, 30 );
	}

	function controls(){
		$image = $this->addCustomControl(
			"<div class=\"oxygen-control\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_hotspot_hp_image'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_hotspot_hp_image')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_hotspot_hp_image']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_hotspot_hp_image\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">". __('browse', 'oxygen') ."</div>
				</div>
			</div>",
			'hp_image'
		);
		$image->setParam('heading', __('Upload Image', "oxy-ultimate") );
		$image->setParam('description', __('Click on Apply Params button and image will show on builder editor.', "oxy-ultimate") );

		$this->addStyleControl([
			'selector' 		=> 'img.hp-image',
			'property' 		=> 'width',
			'slug' 			=> 'hp_image_width',
			'control_type' 	=> 'measurebox',
		])->setUnits('px','px')->setParam('hide_wrapper_end', true);

		$this->addStyleControl([
			'selector' 		=> 'img.hp-image',
			'property' 		=> 'height',
			'slug' 			=> 'hp_image_height',
			'control_type' 	=> 'measurebox',
		])->setUnits('px','px')->setParam('hide_wrapper_start', true);

		$imgAlt = $this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Alt Text', "oxy-ultimate"),
			'slug' 		=> 'img_alt'
		]);
		$imgAlt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicHPImageAlt">data</div>');

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Custom CSS Class(for image)', "oxy-ultimate"),
			'slug' 		=> 'css_class'
		]);

		$instruction = $this->addControlSection('instruction', __('Instruction'), 'assets/icon.png', $this );
		$instruction->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">
				<ul>
					<li style="margin: 0 0 6px;">At first you will upload an image.</li>
					<li style="margin: 0 0 6px;">Click on <strong>Add Marker</strong> button under <strong>Available Elements</strong> section and add it inside the Hotspot element.</li>
					<li style="margin: 0 0 6px;">Click on <strong>Marker Config</strong> tab under <strong>Add Marker</strong> element and setup the icon/imges/text for hotspot button.</li>
					<li style="margin: 0 0 6px;">Adjust the hotspot position under <strong>Add Marker -> Marker Config -> Position</strong> feature.</li>
					<li style="margin: 0 0 6px;">You will setup other settings for color, fonts, size etc properties.</li>
					<li style="margin: 0 0 6px;">For popup content you will add the component(s) inside the <strong>Add Marker</strong> element.</li>
					<li style="margin: 0 0 6px;">Popup animation settings will control under <strong>Add Marker -> Popup Config</strong> section</li>
					<li style="margin: 0 0 6px;">Follow the same procedure for more hotspots.</li>
					<li style="margin: 0 0 6px;">Use Builder\'s Breakpoint settings and adjust the responsive design.</li>
				</ul>
			</div>'), 
			'description'
		)->setParam('heading', 'How do I add a hotspot item?');
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
		$img_alt = $class = $srcset = $attach_id = '';

		if( isset( $options['img_alt'] ) ) {
			$img_alt = $this->fetchDynamicData( $options['img_alt'] );
		}

		if( isset($options['hp_image']) ) {

			$image = $this->fetchDynamicData( $options['hp_image'] );

			$imgwidth = isset($options['hp_image_width']) ? ' width="' . intval( $options['hp_image_width'] ) . '"' : '';
			$imgheight = isset($options['hp_image_height']) ? ' height="' . intval( $options['hp_image_height'] ) . '"' : '';

			if( isset( $options['css_class'] ) ) {
				$class = ' ' . esc_html( $options['css_class'] );
			}

			$attach_id = attachment_url_to_postid( esc_url( $image ) );
			if( $attach_id != '' ) {
				$hpImg = wp_get_attachment_image_src( $attach_id, 'full' );
				list($image_src, $image_width, $image_height) = $hpImg;
				$srcset =  ' srcset="' . wp_get_attachment_image_srcset( $attach_id, 'full' ) .'" sizes="(max-width:'. $image_width . 'px) 100vw, '. $image_width . 'px"';

				if( $imgwidth == '' ) { $imgwidth = ' width="' . $image_width . '"'; };
				if( $imgheight == '' ) { $imgheight = ' height="' .$image_height . '"'; };
			}

			echo '<img src="' . esc_url( $image ) . '" class="hp-image'. $class .'" alt="' . esc_attr( $img_alt ) . '"' . $imgwidth . $imgheight . $srcset . '/>';
		}
		
		echo '<div class="ou-hotspot-inner-wrap oxy-inner-content">';
		
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
		
		echo '</div>';
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if ( ! $this->css_added ){
			$css = '.oxy-ou-hotspot{display: inline-block; width: 100%; min-height: 40px; position: relative; margin: 20px auto;}
				.oxy-ou-hotspot img, .ou-hotspot-inner-wrap img {max-width: 100%; height: auto;}';

			$this->css_added = true;
		}

		return $css;
	}
}

new OUHotspot();

include_once OXYU_DIR . 'elements/ou-hotspot/marker.php';