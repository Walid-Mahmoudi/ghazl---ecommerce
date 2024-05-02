<?php

namespace Oxygen\OxyUltimate;

Class OUClassicAccordion extends \OxyUltimateEl {
	public $css_added = false;

	function name() {
		return __( "Classic Accordion", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_classic_acrd";
	}

	function oxyu_button_place() {
		return "content";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/ou-accordion.svg';
	}

	function controls() {
		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if accordion content is not displaying properly on Builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$toggleBtn = $this->El->addControl('buttons-list', 'acrd_preview', __('Builder Editor Mode', 'oxy-ultimate') );
		$toggleBtn->setValue([__('Expand', "oxy-ultimate"), __('Collapse', "oxy-ultimate")]);
		$toggleBtn->setDefaultValue('Expand');
		$toggleBtn->setValueCSS([
			'Expand' => '.toggle-content{display:block}.toggle-expand-icon:not(.same-toggle-icon){display:none}.toggle-collapse-icon{display:flex}',
			'Collapse' => '.toggle-content,.toggle-collapse-icon{display:none}.toggle-expand-icon{display:flex}'
		]);
		$toggleBtn->setParam( 'description', __('Select <span style="color:#ff7171;">"Expand"</span> option when you are editing the content.') );
		$toggleBtn->whiteList();


		$this->addOptionControl(
			array(
				'type' 		=> "radio",
				"name" 		=> __('Expand on page load? ', "oxy-ultimate"),
				"slug" 		=> "ouacrd_expand",
				"value" 	=> array(
					"yes" 		=> __('Yes', "oxy-ultimate"),
					"no" 		=> __('No', "oxy-ultimate"),
				),
				"default" 		=> "no",
				"css" 			=> false
			)
		)->setParam('description', 'It would work at frontend only. Perview is disabled for Builder editor.');

		$this->addOptionControl(
			array(
				'type' 		=> "radio",
				"name" 		=> __('Enable FAQPage Google Schema Data', "oxy-ultimate"),
				"slug" 		=> "ouacrd_schema",
				"value" 	=> array(
					"no" 		=> __('No', "oxy-ultimate"),
					"yes" 		=> __('Yes', "oxy-ultimate"),					
				),
				"default" 		=> "no"
			)
		)->setParam('description', 'It will add the microdata. Please see the structure details <a href="https://developers.google.com/search/docs/data-types/faqpage" target="_blank" style="color: rgba(255,255,255,0.5);">here</a>.');

		$this->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Slide Transition Duration'),
			'slug' 		=> "ouacrd_tsd",
		])->setRange(0, 5000, 50)->setUnits('ms', 'ms')->setDefaultValue(750);



		//* Heading Section
		$heading = $this->addControlSection( 'heading_section', __('Title', 'oxy-ultimate'), 'assets/icon.png', $this );

		$title = $heading->addCustomControl(
			'<div class="oxygen-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_classic_acrd_ouacrd_title\')}">
			<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_classic_acrd_ouacrd_title\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id,\'oxy-ou_classic_acrd\',\'oxy-ou_classic_acrd_ouacrd_title\')" class="ng-pristine ng-valid ng-touched" ng-keydown="$event.keyCode === 13 && iframeScope.rebuildDOM(iframeScope.component.active.id)">
			<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicCACRDTitle">data</div>
			</div>',
			'acrd_title'
		);
		$title->setParam( 'heading', __('Title') );
		$title->setParam( 'base64', true );
		$title->setParam( 'description', __('Hit on Enter key to see the text on the Builder Editor.', "oxy-ultimate") );

		$heading->addOptionControl(
			array(
				'type' => 'dropdown',
				'name' => __('Tag'),
				'slug' => 'title_tag',
			)

		)->setValue(array(  "h1", "h2", "h3", "h4", "h5", "h6", "div", "span", "p" ))->setDefaultValue('span');

		$hd_selector = '.ou-accordion-button';

		$heading->addStyleControl(
			[
				'selector' 	=> $hd_selector,
				'property' 	=> 'background-color'
			]
		);

		$heading->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Hover/Active Transition Duration'),
			'selector' 		=> $hd_selector,
			'property' 		=> "transition-duration",
		])->setRange(0, 10, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.5);

		$spacing = $heading->addControlSection( 'spacing_section', __('Spacing', 'oxy-ultimate'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"ttlsp_padding",
			__("Padding"),
			$hd_selector
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"ttlsp_margin",
			__("Margin"),
			'.ou-accordion-item'
		)->whiteList();

		$heading->typographySection( __('Typography'), '.ou-accordion-label', $this );

		$heading->borderSection( 'Border', $hd_selector, $this );
		$heading->boxShadowSection( __('Box Shadow'), $hd_selector, $this );

		$hover = $heading->addControlSection( 'hover_section', __('Config Hover Color', 'oxy-ultimate'), 'assets/icon.png', $this );
		$hover->addStyleControls([
			[
				'name' 		=> __('Background Color', 'oxy-ultimate'),
				'selector' 	=> $hd_selector . ':hover',
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Title Color', 'oxy-ultimate'),
				'selector' 	=> $hd_selector . ':hover .ou-accordion-label',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Border Color', 'oxy-ultimate'),
				'selector' 	=> $hd_selector . ':hover',
				'property' 	=> 'border-color'
			]
		]);

		$active = $heading->addControlSection( 'active_section', __('Config Active Color', 'oxy-ultimate'), 'assets/icon.png', $this );
		$active->addStyleControls([
			[
				'name' 		=> __('Background Color', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ou-accordion-button',
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Title Color', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ou-accordion-label',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Border Color', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ou-accordion-button',
				'property' 	=> 'border-color'
			]
		]);

		//* SubTitle Section
		$subtitle = $this->addControlSection( 'subtitle_section', __('Sub Title', 'oxy-ultimate'), 'assets/icon.png', $this );

		$subinput = $subtitle->addCustomControl(
			'<div class="oxygen-input" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_classic_acrd_sub_title\')}">
			<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_classic_acrd_sub_title\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id,\'oxy-ou_classic_acrd\',\'oxy-ou_classic_acrd_sub_title\')" class="ng-pristine ng-valid ng-touched" ng-keydown="$event.keyCode === 13 && iframeScope.rebuildDOM(iframeScope.component.active.id)">
			<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicCACRDSubTitle">data</div>
			</div>',
			'acrd_title'
		);
		$subinput->setParam( 'heading', __('Sub Title') );
		$subinput->setParam( 'description', __('Hit on Enter key to see the text on the Builder Editor.', "oxy-ultimate") );

		$selector = '.ou-accordion-subtitle';

		$subtitle->addOptionControl(
			array(
				'type' => 'dropdown',
				'name' => __('Tag'),
				'slug' => 'subtitle_tag',
			)

		)->setValue(array(  "h1", "h2", "h3", "h4", "h5", "h6", "div", "span", "p" ))->setDefaultValue('span');

		$sub_title_gap = $subtitle->addControlSection( 'subtlesp_section', __('Spacing', 'oxy-ultimate'), 'assets/icon.png', $this );
		$sub_title_gap->addPreset(
			"margin",
			"subttlsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$subtitle->typographySection( __('Typography'), $selector, $this );

		$subtitle_color = $subtitle->addControlSection( 'subtitle_color', __('Hover/Active Color', 'oxy-ultimate'), 'assets/icon.png', $this );
		$subtitle_color->addStyleControls([
			[
				'name' 		=> __('Color on Hover', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-button:hover ' . $selector,
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Color on Active State', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active ' . $selector,
				'property' 	=> 'color'
			]
		]);


		/***********************************
		 * Icon Section
		 ***********************************/
		$icon = $this->addControlSection( 'icon_section', __('Toggle Icon', 'oxy-ultimate'), 'assets/icon.png', $this );
		$icDsp = $icon->addOptionControl(
			array(
				"name" 			=> __('Display Icon', "oxy-ultimate"),
				"slug" 			=> "ouacrd_enable_icon",
				"type" 			=> 'radio',
				"value" 		=> ["yes" => __("Yes") , "no" => __("No")],
				"default"		=> "no"
			)
		);
		$icDsp->setParam('description', 'Click on Apply Params button to see the changes.');


		$icon->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Use Same Open Icon for Close Icon'),
				'slug' 		=> 'is_same',
				"condition" => 'ouacrd_enable_icon=yes'
			)
		)->setValue(["no" => __("No"), "yes" => __("Yes")])->setDefaultValue('no')->setParam('description', 'Click on Apply Params button to see the changes.');

		$icon->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('Animation Effect'),
				'slug' 		=> 'ouacrd_icon_anim',
				'condition' => 'ouacrd_enable_icon=yes&&is_same=yes'
			)

		)->setValue(array( 
				"none" 		=> __("None"),
				"rotatex" 	=> __("Vertical flip"),
				"rotatey" 	=> __("Horizontal flip"),
				"rotate" 	=> __("Rotate")
			)
		)->setDefaultValue('rotatex')
		->setValueCSS( array(
			"rotatex"  => "
				.ou-accordion-item-active .ouacrd-icon-wrap.same-toggle-icon svg {
					-webkit-transform: rotateX(180deg);				
					-moz-transform: rotateX(180deg);
					transform: rotateX(180deg);
				}",
			"rotatey"  => "
				.ou-accordion-item-active .ouacrd-icon-wrap.same-toggle-icon svg {
					-webkit-transform: rotateY(180deg);
					-moz-transform: rotateY(180deg);
					transform: rotateY(180deg);
				}",
			"rotate"  => "
				.ou-accordion-item-active .ouacrd-icon-wrap.same-toggle-icon svg {
					-webkit-transform: rotate(var(--ouacrd-icon-rotate));
					-moz-transform: rotate(var(--ouacrd-icon-rotate));
					transform: rotate(var(--ouacrd-icon-rotate));
				}",
		));


		$icon->addStyleControl(
			array(
				"name" 			=> __('Angle'),
				"selector" 		=> '.ouacrd-icon-wrap',
				"property" 		=> '--ouacrd-icon-rotate',
				"control_type" 	=> 'slider-measurebox',
				'condition' 	=> 'is_same=yes&&ouacrd_icon_anim=rotate'
			)
		)
		->setUnits('deg','deg')
		->setRange('-180','180');


		$icon->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Animation Transition Duration'),
			'selector' 		=> '.ouacrd-icon-wrap svg',
			'property' 		=> "transition-duration",
			'condition' 	=> 'ouacrd_enable_icon=yes&&is_same=yes'
		])->setRange(0, 10, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.75);
		


		$opicon = $icon->addControlSection( 'openicon_section', __('Open Icon', 'oxy-ultimate'), 'assets/icon.png', $this );

		$openIconPreview = $opicon->addControl('buttons-list', 'acrd_preview', __('Open Icon Preview', 'oxy-ultimate') );
		$openIconPreview->setValue(['Collapse' => __('Yes'), 'Expand' => __('No')]);
		$openIconPreview->setDefaultValue('Expand');
		$openIconPreview->setValueCSS([
			'Expand' => '.toggle-content{display:block}.toggle-expand-icon:not(.same-toggle-icon){display:none}.toggle-collapse-icon{display:flex}',
			'Collapse' => '.toggle-content,.toggle-collapse-icon{display:none}.toggle-expand-icon{display:flex}'
		]);
		$openIconPreview->whiteList();

		$openIcon = $opicon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Select Icon'),
				"slug" 			=> 'ouacrd_openicon'
			)
		);
		$openIcon->setParam('description', 'After selecting the icon, you would click on Apply Params button to see the changes.');

		$clicon = $icon->addControlSection( 'closeicon_section', __('Close Icon', 'oxy-ultimate'), 'assets/icon.png', $this );
		
		$clIconPreview = $clicon->addControl('buttons-list', 'acrd_preview', __('Close Icon Preview', 'oxy-ultimate') );
		$clIconPreview->setValue(['Expand' => __('Yes'),  'Collapse' => __('No')]);
		$clIconPreview->setDefaultValue('Expand');
		$clIconPreview->setValueCSS([
			'Expand' => '.toggle-content{display:block}.toggle-expand-icon:not(.same-toggle-icon){display:none}.toggle-collapse-icon{display:flex}',
			'Collapse' => '.toggle-content,.toggle-collapse-icon{display:none}.toggle-expand-icon{display:flex}'
		]);
		$clIconPreview->whiteList();

		$closeIcon = $clicon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Select Icon'),
				"slug" 			=> 'ouacrd_closeicon'
			)
		);
		$closeIcon->setParam('description', 'After selecting the icon, you would click on Apply Params button to see the changes.');

		$position = $icon->addControlSection( 'position_section', __('Position'), 'assets/icon.png', $this );
		$pos = $position->addOptionControl(
			array(
				"name" 			=> '',
				"slug" 			=> "ouacrd_iconpos",
				"type" 			=> 'radio',
				"value" 		=> ["left" => __("Left") , "right" => __("Right")],
				"default"		=> "right"
			)
		);
		$pos->setParam('description', 'Click on Apply Params button to see the changes.');

		$position->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"slug" 			=> "ouacrd_gapright",
				"selector" 		=> '.ouacrd-icon-right',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '15',
				"property" 		=> 'margin-left',
				"condition" 	=> "ouacrd_iconpos=right"
			)
		)->setRange(0, 80, 5)->setUnits("px", "px");

		$position->addStyleControl(
			array(
				"name" 			=> __('Gap Between Icon & Text', "oxy-ultimate"),
				"slug" 			=> "ouacrd_gapleft",
				"selector" 		=> '.ouacrd-icon-left',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '15',
				"property" 		=> 'margin-right',
				"condition" 	=> "ouacrd_iconpos=left"
			)
		)->setRange(0, 80, 5)->setUnits("px", "px");

		$iconsc = $icon->addControlSection('iconsc_section', __('Size & Color', 'oxy-ultimate'), 'assets/icon.png', $this);
		$iconsc->addStyleControls([
			[
				'name' 		=> __('Wrapper Background Color', 'oxy-ultimate'),
				'selector' 	=> '.ouacrd-icon-wrap',
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Wrapper Background Color of Active State', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ouacrd-icon-wrap',
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Icon Color', 'oxy-ultimate'),
				'selector' 	=> '.ouacrd-icon-wrap svg',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Icon olor on Hover State', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-button:hover .ouacrd-icon-wrap svg',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Icon Color on Active State', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ouacrd-icon-wrap svg',
				'property' 	=> 'color'
			]
		]);

		$iconsc->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxy-ultimate"),
				"slug" 			=> "ouacrd_iconsize",
				"selector" 		=> '.ou-accordion svg',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '15',
				"property" 		=> 'width|height'
			)
		)->setRange(20, 80, 10)->setUnits("px", "px");

		$iconsc->addStyleControl(
			array(
				"name" 			=> __('Icon Wrapper Width & Height', "oxy-ultimate"),
				"slug" 			=> "ouacrd_iconwh",
				"selector" 		=> '.ouacrd-icon-wrap',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)->setRange(20, 80, 10)->setUnits("px", "px");

		$ibrd = $icon->borderSection( 'Border', '.ouacrd-icon-wrap', $this );
		$ibrd->addStyleControls([
			[
				'name' 		=> __('Border Color - Active', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ouacrd-icon-wrap',
				'property' 	=> 'border-color'
			]
		]);

		$icon->boxShadowSection( __('Box Shadow'), '.ouacrd-icon-wrap', $this );


		//* Content Section
		$content = $this->addControlSection('content_section', __('Content', 'oxy-ultimate'), 'assets/icon.png', $this);

		$content->addStyleControl([
			'selector' 	=> '.ou-accordion-content',
			'property' 	=> 'background-color'
		]);

		$contentsp = $content->addControlSection('cntsp_section', __('Spacing', 'oxy-ultimate'), 'assets/icon.png', $this);

		$contentsp->addPreset(
			"padding",
			"cntsp_padding",
			__("Padding"),
			'.ou-accordion-content'
		)->whiteList();

		$contentsp->addPreset(
			"margin",
			"cntsp_margin",
			__("Margin"),
			'.ou-accordion-content'
		)->whiteList();

		$content->borderSection( 'Border', '.ou-accordion-content', $this );
		$content->boxShadowSection( 'Box Shadow', '.accordion-content-text', $this );
	}

	function render( $options, $defaults, $content ) {
		global $oxygen_svg_icons_to_load;

		$icon_pos = isset($options['ouacrd_iconpos']) ? $options['ouacrd_iconpos'] : 'right';
		$isShowIcon = isset( $options['ouacrd_enable_icon']) ? $options['ouacrd_enable_icon'] : 'no';
		$isExpandFirst = ! empty( $options['ouacrd_expand'] ) ? $options['ouacrd_expand'] : 'no';

		$is_same_icon = ! empty( $options['is_same'] ) ? $options['is_same'] : 'no';
		$class 		  = ($is_same_icon == 'yes') ? ' same-toggle-icon' : '';

		$expandIconClass = ( isset($_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-expand-icon' : '';
		$collapseIconClass = ( isset($_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-collapse-icon' : '';

		if( isset($options['ouacrd_openicon']) ) {
			$oxygen_svg_icons_to_load[] = $options['ouacrd_openicon'];
			$openIcon = $options['ouacrd_openicon'];
		}

		if( isset($options['ouacrd_closeicon']) ) {
			$oxygen_svg_icons_to_load[] = $options['ouacrd_closeicon'];
			$closeIcon = $options['ouacrd_closeicon'];
		}

		$questionSchema = $answerSchema = $dataAttr = '';
		if( isset($options['ouacrd_schema']) && $options['ouacrd_schema'] == "yes" ) {
			$questionSchema =' itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"';
			$answerSchema = ' itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"';
			$dataAttr .= ' data-schema="' . $options['ouacrd_schema'] . '"';
		}

		if( isset($options['ouacrd_tsd']) ) {
			$dataAttr .= ' data-transitionspeed="' . absint($options['ouacrd_tsd']) . '"';
		}

		echo '<div class="ou-accordion" data-expand-first="' . $isExpandFirst .'">';
			echo '<div' . $questionSchema . ' class="ou-accordion-item">';

				//if( isset( $options['ouacrd_title'] ) )
				//{
				$title_tag = isset( $options['title_tag'] ) ? esc_attr($options['title_tag']) : "span";
    			$subtitle_tag = isset( $options['subtitle_tag'] ) ? esc_attr($options['subtitle_tag']) : "span";

				$title = wp_kses_post($options['ouacrd_title']);
				if( strstr( $title, 'oudata_' ) ) {
					$title = base64_decode( str_replace( 'oudata_', '', $title ) );
					$shortcode = ougssig( $this->El, $title );
					$title = do_shortcode( $shortcode );
				} elseif( strstr( $title, '[oxygen') ) {
					$shortcode = ct_sign_oxy_dynamic_shortcode(array($title));
					$title = do_shortcode($shortcode);
				}

				if( ! $content && empty( $title ) ) {
					$title = 'Accordion Title';
				}

				echo '<div' . $dataAttr . ' class="ou-accordion-button" aria-selected="false" aria-expanded="false">';

					if( $isShowIcon == 'yes' && isset($options['ouacrd_openicon']) && $icon_pos == 'left' ) {
						echo '<span class="ou-accordion-open-icon ouacrd-icon-wrap ouacrd-icon-left'.$expandIconClass. $class . '"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $openIcon . '"></use></svg></span>';
					}

					if( $isShowIcon == 'yes' && isset($options['ouacrd_closeicon']) && $icon_pos == 'left' && $is_same_icon != 'yes') {
						echo '<span class="ou-accordion-close-icon ouacrd-icon-wrap ouacrd-icon-left toggle-icon'.$collapseIconClass.'"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $closeIcon . '"></use></svg></span>';
					}

					echo '<div class="ou-accordion-title-wrap">';

						echo '<'. $title_tag .' class="ou-accordion-label" itemprop="name description">' . $title . '</'.$title_tag.'>';

						if( isset( $options['sub_title'] ) ) {
							$subtitle = wp_kses_post($options['sub_title']);
							if( strstr( $subtitle, 'oudata_' ) ) {
								$subtitle = base64_decode( str_replace( 'oudata_', '', $subtitle ) );
								$shortcode = ougssig( $this->El, $subtitle );
								$subtitle = do_shortcode( $shortcode );
							} elseif( strstr( $subtitle, '[oxygen') ) {
								$shortcode = ct_sign_oxy_dynamic_shortcode(array($subtitle));
								$subtitle = do_shortcode($shortcode);
							}
							echo '<'. $subtitle_tag .' class="ou-accordion-subtitle">' . $subtitle . '</'.$subtitle_tag.'>';
						}

					echo '</div>';

					if( $isShowIcon == 'yes' && isset($options['ouacrd_openicon']) && $icon_pos == 'right' ) {
						echo '<span class="ou-accordion-open-icon ouacrd-icon-wrap ouacrd-icon-right'.$expandIconClass. $class . '"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $openIcon . '"></use></svg></span>';
					}
					if( $isShowIcon == 'yes' && isset($options['ouacrd_closeicon']) && $icon_pos == 'right' && $is_same_icon != 'yes') {
						echo '<span class="ou-accordion-close-icon ouacrd-icon-wrap ouacrd-icon-right toggle-icon'.$collapseIconClass.'"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $closeIcon . '"></use></svg></span>';
					}

				echo '</div>';
				//}

				if( $content ) 
				{
					$builderClass = ( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-content' : '';
					if( function_exists('do_oxygen_elements') )
						$acrd_item = do_oxygen_elements( $content );
					else
						$acrd_item = do_shortcode( $content );
					
					echo '<div' . $answerSchema . ' class="ou-accordion-content'.$builderClass.'"><div class="oxy-inner-content" itemprop="text">' . $acrd_item . '</div></div>';
				} else {
					echo '<div class="demo-text">Content of nest component(s) will show here. Add or drag&drop the component(s) inside the <strong>Classic Accordion</strong> component.</div>';
				}
				
			echo '</div>';
		echo '</div>';

		if( ! isset($_GET['oxygen_iframe']) && ! defined('OXY_ELEMENTS_API_AJAX') ) {
			add_action( 'wp_footer', array( $this, 'ou_accordion_script' ) );
		}
	}

	function ou_accordion_script() {
		wp_enqueue_script(
			'ou-accordion-script', 
			OXYU_URL . 'assets/js/ou-accordion.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/ou-accordion.js' ), 
			true
		);
	}
	
	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-classic-acrd, body.oxygen-builder-body .ou-accordion-content{min-height: 40px; width: 100%;}
				.oxy-ou-classic-acrd{display:inline-flex;}
				.ou-accordion,.ou-accordion-item {clear: both;width: 100%;}
				.ou-accordion-title-wrap{flex-grow: 1;}
				.ou-accordion-item{margin-bottom: 8px;}
				.ou-accordion .ou-accordion-button {
					background-color: #efefef;
					cursor: pointer;
					display: -webkit-flex;
					display: -moz-flex;
					display: flex;
					-webkit-align-items: center;
					align-items: center;
					padding: 12px 18px;
					transition: all 0.5s;
				}
				.oxy-ou-classic-acrd .ou-accordion-label {
					clear: both;
    				display: block;
					font-size: 18px;
					font-weight: bold;
					letter-spacing: .1em;
					font-family: inherit;
					text-transform: uppercase;
					width: 100%;
				}
				.oxy-ou-classic-acrd .ou-accordion-subtitle {
					color: #999;
					font-size: 14px;
					text-transform: uppercase;
					clear: both;
    				display: block;
    				width: 100%;
				}
				.oxy-ou-classic-acrd .ou-accordion-content,
				body.oxygen-builder-body .oxy-ou-classic-acrd .demo-text {
					clear:both; 
					margin-bottom: 12px; 
					padding: 18px 18px 10px;
					overflow:hidden;
				}
				body:not(.oxygen-builder-body) .oxy-ou-classic-acrd .ou-accordion-content{display: none;}
				.oxy-ou-classic-acrd .ouacrd-icon-wrap {
					display: flex;
					align-items: center;
					justify-content: center;
					line-height: 0;
					width: 20px;
				}
				.oxy-ou-classic-acrd .ouacrd-icon-wrap svg {
					width: 15px;
					height: 15px;
					fill: currentColor;
					transition: transform 0.75s;
					-webkit-transition: -webkit-transform 0.75s;
				}
				.oxy-ou-classic-acrd .ou-accordion-item-active .ouacrd-icon-wrap.same-toggle-icon svg{
					-webkit-transform: rotateX(180deg);
					-moz-transform: rotateX(180deg);
					transform: rotateX(180deg);
				}
				.oxy-ou-classic-acrd .ouacrd-icon-left {margin-right: 15px;}
				.oxy-ou-classic-acrd .ouacrd-icon-right {margin-left: 15px;}
				.ou-accordion-button:hover .ouacrd-icon-wrap svg{fill: currentColor;}
				body:not(.oxygen-builder-body) .toggle-icon:not(.same-toggle-icon){display:none;}
				';
			
			$this->css_added = true;
		}

		return $css;
	}
}

new OUClassicAccordion();