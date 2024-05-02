<?php
namespace Oxygen\OxyUltimate;

Class OUImageLightbox extends \OxyUltimateEl {
	public $script_added = false;
	public $css_added = false;

	function name() {
		return __( "Image Lightbox", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_uimage";
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
			<label class="oxygen-control-label oxy-ou_uimage-elements-label"
				ng-if="isActiveName('oxy-ou_uimage')&&!hasOpenTabs('oxy-ou_uimage')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_uimage-elements"
				ng-if="isActiveName('oxy-ou_uimage')&&!hasOpenTabs('oxy-ou_uimage')">
				<?php do_action("oxygen_add_plus_ouimage_comp"); ?>
			</div>
		<?php }, 25 );
	}

	function controls() {

		$linkType = $this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Image Link Type', "oxy-ultimate"),
			'description' => __('Lightbox preview is disabled for Builder editor.'),
			'slug' 		=> 'img_ltype',
			'value' 	=> [
				'none' 		=> __('None'),
				'url' 		=> __('URL'),
				'lightbox' 	=> __('Lightbox'),
				'file' 		=> __('Photo File')
			],
			'css' 		=> false
		]);
		$linkType->setParam('description', __('Lightbox preview is disabled for builder editor', "oxy-ultimate") );

		$imageLink = $this->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_uimage_img_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_uimage_img_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_uimage_img_url\');iframeScope.checkResizeBoxOptions(\'oxy-ou_uimage_img_url\'); " class="ng-pristine ng-valid ng-touched">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_uimage_img_url\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicILUrl">data</div>
			</div>
			',
			"img_url"
		);
		$imageLink->setParam('heading', __('URL', "oxy-ultimate"));
		$imageLink->setParam('css', false);
		$imageLink->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']=='url'");

		$imgURL = $this->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_uimage_ou_imgurl'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_uimage_ou_imgurl')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_ou_imgurl']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_uimage_ou_imgurl\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicILFile\">data</div>
				</div>
			</div>",
			'ilimage'
		);
		$imgURL->setParam('heading', __('Image URL', "oxy-ultimate"));
		$imgURL->setParam('css', false);
		$imgURL->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']!='none'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']!='url'");

		$this->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "url_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self",
				'css' 		=> false
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']!='none'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']!='lightbox'");

		$this->addOptionControl(
			array(
				"name" 			=> __('Follow', "oxy-ultimate"),
				"slug" 			=> "url_follow",
				"type" 			=> 'radio',
				"value" 		=> [ 'none' => __('None'), "follow" => __("Follow") , "nofollow" => __("No Follow")],
				'css' 		=> false
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']!='none'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']!='lightbox'");

		$this->addOptionControl(
			array(
				"name" 			=> __('Animation Effect for Lightbox Image', "oxy-ultimate"),
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
				"default"		=> "mfp-zoom-in",
				'css' 		=> false
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_uimage_img_ltype']=='lightbox'");
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
		$link = false;

		echo '<figure class="ou-image-wrapper">';

		if( isset($options['ou_imgurl'])) {
			$fileUrl = $this->fetchDynamicData( $options['ou_imgurl'] );
		}

		if( isset( $options['img_ltype'] ) && $options['img_ltype'] == 'lightbox' && ! defined('OXY_ELEMENTS_API_AJAX') ) {
			wp_enqueue_style('ou-mfp-style');
			wp_enqueue_script('ou-mfp-script');
			if( ! $this->script_added ) {
				wp_add_inline_script('ou-mfp-script', "(function($){ $(function(){
					if( ! $('body').hasClass('ct_inner') && $('.ou-image-wrapper').length) {
						$('a.ou-image-lightbox').bind('touchstart', function(e){
							e.stopPropagation();
						});

						$('a.ou-image-lightbox').magnificPopup( {
							midClick 			: true,
							closeBtnInside 		: false,
							fixedContentPos 	: false,
							type 				: 'image',
							gallery: {
								enabled: false
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

				$this->script_added = true;
			}
			
			$link = true;

			echo '<a href="' . esc_url( $fileUrl ) . '" class="ou-image-lightbox" data-effect="' . $options['image_aimeffect'] .'">';
		} elseif( isset( $options['img_ltype'] ) && $options['img_ltype'] == 'file' && ! defined('OXY_ELEMENTS_API_AJAX') ) {
			$link = true;

			echo '<a href="' . esc_url( $fileUrl ) . '" target="' . $options['url_target'] . '" rel="' . ( ( $options['url_follow'] !== 'none' ) ? $options['url_follow'] : '' ) . '">';
		} elseif( isset( $options['img_ltype'] ) && $options['img_ltype'] == 'url' && ! defined('OXY_ELEMENTS_API_AJAX') ) {
			$link = true;

			$editable_link = esc_url( $this->fetchDynamicData( $options['img_url'] ) );

			echo '<a href="' . $editable_link . '" target="' . $options['url_target'] . '" rel="' . ( ( $options['url_follow'] !== 'none' ) ? $options['url_follow'] : '' ) . '">';
		} else {}
		
			echo '<div class="oxy-inner-content">';
			if( $content ) {
				if( function_exists('do_oxygen_elements') )
					echo do_oxygen_elements( $content );
				else
					echo do_shortcode( $content );
			}
			echo '</div>';
		
		if( $link && ! defined('OXY_ELEMENTS_API_AJAX') ) {
			echo '</a>';
		}
		
		echo '</figure>';
	}

	function customCSS( $original, $selector ) {
		if( ! $this->css_added ) {
			$this->css_added = true;
			return '.oxy-ou-uimage{width: 100%;}.ou-image-wrapper,.oxy-ou-imgcap{display: flex;flex-direction: column;margin: 0;min-height: 40px;}.ou-image-wrapper img {max-width: 100%;}.oxy-ou-imgcap{line-height: 1.325;}';
		}
	}
}

new OUImageLightbox();

include_once OXYU_DIR . 'elements/ou-ultimate-image/ou-image.php';
include_once OXYU_DIR . 'elements/ou-ultimate-image/ou-image-caption.php';