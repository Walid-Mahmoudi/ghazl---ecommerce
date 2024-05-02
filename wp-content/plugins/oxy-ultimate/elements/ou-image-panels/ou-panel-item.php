<?php
namespace Oxygen\OxyUltimate;

Class OUImageAccordionItem extends \OxyUltimateEl {
	public $css_added = false;
	public $script_added = false;

	function name() {
		return __( "Add Image", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_panel_item";
	}

	function button_place() {
		return "ouimgpanel::comp";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/ou-ultimate-image.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_panel_item-elements-label"
				ng-if="isActiveName('oxy-ou_panel_item')&&!hasOpenTabs('oxy-ou_panel_item')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_panel_item-elements"
				ng-if="isActiveName('oxy-ou_panel_item')&&!hasOpenTabs('oxy-ou_panel_item')">
				<?php do_action("oxygen_add_plus_ouimgpanel_comp"); ?>
			</div>
		<?php }, 35 );
	}

	function controls() {

		$uploadImg = $this->addControlSection( 'uplimg', __('Upload Image', "oxy-ultimate"), 'assets/icon.png', $this );

		$imgURL = $uploadImg->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_panel_item_ou_panel_image'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_panel_item_ou_panel_image')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_panel_item_ou_panel_image']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_panel_item_ou_panel_image\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicOUPImg\">data</div>
				</div>
			</div>",
			'before_image'
		);
		$imgURL->setParam('heading', __('Image', "oxy-ultimate") );
		$imgURL->setParam('description', __('Click on Apply Params button and see the image on builder editor', "oxy-ultimate") );
		$imgURL->rebuildElementOnChange();

		$imgpos = $uploadImg->addControl( 'buttons-list', 'imgpos', __('Image Position') );
		$imgpos->setValue( array( "Center", "Custom" ) );
		$imgpos->setValueCSS( array(
			"Center" 	=> "{background-position: center;}"
		));
		$imgpos->setDefaultValue("Center");
		$imgpos->whiteList();

		$custPos = $uploadImg->addStyleControl([
			'selector' 		=> '',
			'name' 			=> __('Position Left'),
			'slug' 			=> 'pimg_cust_posleft',
			'property' 		=> 'background-position-x',
			'control_type'	=> 'slider-measurebox',
			'condition' 	=> 'imgpos=Custom'
		]);
		$custPos->setRange('0', '100', '5');
		$custPos->setUnits( '%', '%' );

		$custPosRght = $uploadImg->addStyleControl([
			'selector' 		=> '',
			'name' 			=> __('Position Right'),
			'slug' 			=> 'pimg_cust_posright',
			'property' 		=> 'background-position-y',
			'control_type'	=> 'slider-measurebox',
			'condition' 	=> 'imgpos=Custom'
		]);
		$custPosRght->setRange('0', '100', '5');
		$custPosRght->setUnits( '%', '%' );


		/******************** 
		 * Call to Action
		 **********************/
		$cta = $this->addControlSection( 'panel_cta', __('Call To Action', "oxy-ultimate"), 'assets/icon.png', $this );

		$cta->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Link Type', "oxy-ultimate" ),
			'slug' 		=> 'ouimgp_linktype',
			'value' 	=> [
				'none' 		=> __('None', "oxy-ultimate"),
				'title' 	=> __('Image Title', "oxy-ultimate"),
				'panel' 	=> __('Panel', "oxy-ultimate"),
				'lightbox' 	=> __('Lightbox', "oxy-ultimate")
			],
			'default' 		=> 'none'
		]);

		$panelLink = $cta->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_panel_item_panel_link\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_panel_item_panel_link\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_panel_item_panel_link\');iframeScope.checkResizeBoxOptions(\'oxy-ou_panel_item_panel_link\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_panel_item_panel_link\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicOUPLink">data</div>
			</div>
			',
			"panel_link"
		);
		$panelLink->setParam('heading', __('URL'));
		$panelLink->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_panel_item_ouimgp_linktype']=='title'||iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_panel_item_ouimgp_linktype']=='panel'");

		$cta->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "link_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self"
			)
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_panel_item_ouimgp_linktype']=='title'||iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_panel_item_ouimgp_linktype']=='panel'");

		$cta->addOptionControl(
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
		)->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_panel_item_ouimgp_linktype']=='lightbox'");


		/*****************************
		 * Caption Settings
		 ****************************/
		$capSet = $this->addControlSection(  'oupimg_capset', __('Caption Settings', "oxy-ultimate"),'assets/icon.png', $this );

		$capSet->addOptionControl(
			array(
				"name" 			=> __('Caption Appearance', "oxy-ultimate"),
				"slug" 			=> "ouimgp_showcap",
				"type" 			=> 'radio',
				"value" 		=> ["d" => __("Default"), "hover" => __("Show on Hover"), "hide" => __("Hide on Hover")],
				"default"		=> "d"
			)
		);

		$capTd = $capSet->addStyleControl([
			'selector' 		=> '.oxy-ou-panel-item:hover .ou-panel-caption,.oxy-ou-panel-item:hover .oupa-caption-bg',
			'property' 		=> 'transition-delay',
			'control_type' 	=> 'slider-measurebox',
			'slug' 			=> 'caph_td'
		]);
		$capTd->setRange( '0', '5', '0.1');
		$capTd->setValue( '0.5' );
		$capTd->setUnits( 's', 'sec');
		$capTd->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_panel_item_ouimgp_showcap']!='d'");

		$capSet->addStyleControl([
			'selector' 		=> '.oupa-caption-bg',
			'property' 		=> 'background-color',
			'slug' 			=> 'cap_bgc'
		]);

		$titlePos = $capSet->addControl( 'buttons-list', 'pititle_pos', __('Vertical Alignment', "oxy-ultimate") );
		$titlePos->setValue( array( "Top", "Center", 'Bottom' ) );
		$titlePos->setValueCSS( array(
			"Top" 		=> "{align-items: flex-start;}",
			"Center" 	=> "{align-items: center;}",
			"Bottom" 	=> "{align-items: flex-end;}"
		));
		$titlePos->setDefaultValue("Bottom");
		$titlePos->whiteList();

		$capSpacing = $capSet->addControlSection( 'oupimg_capspace', __('Spacing', "oxy-ultimate"), 'assets/icon.png', $this );

		$capSpacing->addPreset(
			"padding",
			"capsp_padding",
			__("Padding", "oxy-ultimate"),
			'.ou-panel-caption'
		)->whiteList();
	}

	function render( $options, $defaults, $content ) {

		echo '<div class="oupa-caption-bg"></div>';
		
		$linkOpenTag = $linkCloseTag = '';

		if( $options['ouimgp_linktype'] === 'title' || $options['ouimgp_linktype'] === 'panel' ) 
		{
			if( isset( $options['panel_link'] ) ) {
				$linkOpenTag = '<a href="' . $options['panel_link'] . '" target="'. $options['link_target'] . '" class="link-'. $options['ouimgp_linktype'] .'">';
				$linkCloseTag = '</a>';
			}
		}

		if( $options['ouimgp_linktype'] === 'lightbox' ) {
			$linkOpenTag = '<a href="' . esc_url( $options['ou_panel_image'] ) . '" class="ouimgp-lightbox" data-effect="' . $options['image_aimeffect'] .'">';
			$linkCloseTag = '</a>';

			if( ! $this->script_added && ! defined('OXY_ELEMENTS_API_AJAX') ) {
				wp_enqueue_style('ou-mfp-style');
				wp_enqueue_script('ou-mfp-script');
				wp_add_inline_script('ou-mfp-script', "(function($){ $(function(){
					if( ! $('body').hasClass('ct_inner') && $('a.ouimgp-lightbox').length) {
						$('a.ouimgp-lightbox').magnificPopup( {
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
			}
			
			$this->script_added = true;
		}

		echo $linkOpenTag;
 
		echo '<div class="ou-panel-caption oxy-inner-content">';
		if( $content )
		{
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
		}
		echo '</div>';

		echo $linkCloseTag;

		if( isset( $options['ou_panel_image'] ) ) {
			$acrd_img = $this->fetchDynamicData( $options['ou_panel_image'] );
			$css = '#' . $options['selector'] . '{background-image: url(' . esc_url( $acrd_img ) . ');}';

			echo '<style type="text/css">' . $css . '</style>';
		}
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

	function customCSS( $original, $selector ) {
		$css = '';
		$prefix = $this->El->get_tag();

		if( isset( $original[$prefix .'_ouimgp_showcap'] ) && $original[$prefix .'_ouimgp_showcap'] == 'hover' ) {
			$css .= $selector . ' .ou-panel-caption,' . $selector . ' .oupa-caption-bg{transition:all 300ms ease;transform: scale(0,0);-webkit-transition:all 300ms ease;-webkit-transform: scale(0,0);-moz-transition:all 300ms ease;-moz-transform: scale(0,0);}';
			$css .= $selector . ':hover .ou-panel-caption,' . $selector . ':hover .oupa-caption-bg{transform:scale(1,1);-webkit-transform:scale(1,1);-moz-transform:scale(1,1);}';

			if( isset( $original[$prefix . '_caph_td'] ) ) {
				$css .= $selector .':hover .ou-panel-caption,' . $selector .':hover .oupa-caption-bg{transition-delay:'.$original[$prefix . '_caph_td'].'s;}';
			}
		}

		if( isset( $original[$prefix .'_ouimgp_showcap'] ) && $original[$prefix .'_ouimgp_showcap'] == 'hide' ) {
			$css .= $selector . ' .ou-panel-caption,' . $selector . ' .oupa-caption-bg{transition:all 300ms ease;transform: scale(1,1);-webkit-transition:all 300ms ease;-webkit-transform: scale(1,1);-moz-transition:all 300ms ease;-moz-transform: scale(1,1);}';
			$css .= $selector . ':hover .ou-panel-caption,' . $selector . ':hover .oupa-caption-bg{transform:scale(0,0);-webkit-transform:scale(0,0);-moz-transform:scale(0,0);}';

			if( isset( $original[$prefix . '_caph_td'] ) ) {
				$css .= $selector .':hover .ou-panel-caption,' . $selector .':hover .oupa-caption-bg{transition-delay:'.$original[$prefix . '_caph_td'].'s;}';
			}
		}

		if( isset( $original[$prefix .'_pititle_pos'] ) && $original[$prefix .'_pititle_pos'] == 'Top' ) {
			$css .= $selector .'.oxy-ou-panel-item > a {align-items: flex-start}';
		}

		if( isset( $original[$prefix .'_pititle_pos'] ) && $original[$prefix .'_pititle_pos'] == 'Center' ) {
			$css .= $selector .'.oxy-ou-panel-item > a {align-items: center}';
		}

		if( isset( $original[$prefix .'_pititle_pos'] ) && $original[$prefix .'_pititle_pos'] == 'Bottom' ) {
			$css .= $selector .'.oxy-ou-panel-item > a {align-items: flex-end}';
		}
		
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-panel-item{background-size: cover; height: 100%; flex: 1;transition-duration: 0.8s;transition-timing-function: ease;transition-property: all;background-repeat: no-repeat; background-position: center; position: relative; z-index: 1; display: flex; overflow:hidden;}';
			$css .= '.oxy-ou-panel-item .ou-panel-caption{width: 100%; padding: 10px; position: relative; z-index: 7;}';
			$css .= '.oxy-ou-panel-item > a{width: 100%; display: inline-flex; text-decoration: none;}';
			$css .= '.oxy-ou-panel-item:hover{flex-grow: 4; z-index: 5;}';
			$css .= '.oupa-caption-bg{display: block; width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index:6;}';
			$css .= '.ouimgp-lightbox, .link-panel{ position: relative; z-index: 28; width: 100%; height: 100%;}';
			$css .= '.link-title{height: auto;}';

			$this->css_added = true;
		}

		return $css;
	}
}

new OUImageAccordionItem();