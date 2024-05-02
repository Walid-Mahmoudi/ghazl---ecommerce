<?php

namespace Oxygen\OxyUltimate;

Class OUDynamicAccordion extends \OxyUltimateEl {
	public $css_added = false;
	public $has_js = true;

	function name() {
		return __( "Dynamic Accordion", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_dynamic_acrd";
	}

	function oxyu_button_place() {
		return "content";
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

		$this->addOptionControl([
			'name' 		=> __('Editing Mode', 'oxy-ultimate'),
			'type' 		=> 'radio',
			'slug' 		=> 'ouacrd_bep',
			'value' 	=> ['enable' => __('Preview', "oxy-ultimate"), 'disable' => __('Editing')],
			'default' 	=> 'enable',
			'css' 		=> false
		])->rebuildElementOnChange();

		$this->addOptionControl([
			'name' 		=> __('Content Source', 'oxy-ultimate'),
			'type' 		=> 'radio',
			'slug' 		=> 'ouacrd_source',
			'value' 	=> ['acfrep' => __('ACF Repeater', "oxy-ultimate"), 'mbgroup' => __('Meta Box Group')],
			'default' 	=> 'acfrep',
			'css' 		=> false
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Repeater Field Name', 'oxy-ultimate'),
			'slug' 		=> 'acfrep_repfld',
			'condition' => 'ouacrd_source=acfrep'
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Group ID', 'oxy-ultimate'),
			'slug' 		=> 'mggroup_id',
			'condition' => 'ouacrd_source=mbgroup'
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Title Field Name', 'oxy-ultimate'),
			'slug' 		=> 'acfrep_title',
			'condition' => 'ouacrd_source=acfrep'
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Title Field ID', 'oxy-ultimate'),
			'slug' 		=> 'mbgroup_title',
			'condition' => 'ouacrd_source=mbgroup'
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Sub Title Field Name', 'oxy-ultimate'),
			'slug' 		=> 'acfrep_subtitle',
			'condition' => 'ouacrd_source=acfrep'
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Sub Title Field ID', 'oxy-ultimate'),
			'slug' 		=> 'mbgroup_subtitle',
			'condition' => 'ouacrd_source=mbgroup'
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Content Field Name', 'oxy-ultimate'),
			'slug' 		=> 'acfrep_content',
			'condition' => 'ouacrd_source=acfrep'
		]);

		$this->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Content Field ID', 'oxy-ultimate'),
			'slug' 		=> 'mbgroup_content',
			'condition' => 'ouacrd_source=mbgroup'
		]);

		$this->addOptionControl(
			array(
				'type' 		=> "radio",
				"name" 		=> __('Expand First Item', "oxy-ultimate"),
				"slug" 		=> "ouacrd_expand",
				"value" 	=> array(
					"yes" 		=> __('Yes', "oxy-ultimate"),
					"no" 		=> __('No', "oxy-ultimate"),
				),
				"default" 		=> "no",
				"css" 			=> false
			)
		)->rebuildElementOnChange();

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
		)->setParam('description', 'It will add the microdata. Please see the structure details <a href="https://developers.google.com/search/docs/data-types/faqpage" target="_blank">here</a>.');

		$this->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Duration'),
			'slug' 		=> "ouacrd_tsd",
		])->setRange(0, 5000, 50)->setUnits('ms', 'ms')->setDefaultValue(750);


		/***********************************
		 * Heading Section
		 ***********************************/
		$heading = $this->addControlSection( 'heading_section', __('Heading', 'oxy-ultimate'), 'assets/icon.png', $this );

		$heading->addStyleControl(
			[
				'selector' 	=> '.ou-accordion-button',
				'property' 	=> 'background-color'
			]
		);

		$heading->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Hover/Active Transition Duration'),
			'selector' 		=> '.ou-accordion-button',
			'property' 		=> "transition-duration",
		])->setRange(0, 10, 0.1)->setUnits('s', 'sec')->setDefaultValue(0.5);

		$spacing = $heading->addControlSection( 'spacing_section', __('Spacing', 'oxy-ultimate'), 'assets/icon.png', $this );
		$spacing->addPreset(
			"padding",
			"ttlsp_padding",
			__("Padding"),
			'.ou-accordion-button'
		)->whiteList();

		$heading->addPreset(
			"margin",
			"ttlsp_margin",
			__("Margin"),
			'.ou-accordion-item'
		)->whiteList();

		$heading->borderSection( __('Border'), '.ou-accordion-button', $this );
		$heading->boxShadowSection( __('Box Shadow'), '.ou-accordion-button', $this );

		$hover = $heading->addControlSection( 'hover_section', __('Config Hover Color', 'oxy-ultimate'), 'assets/icon.png', $this );
		$hover->addStyleControls([
			[
				'name' 		=> __('Background Color', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-button:hover',
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Title Color', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-button:hover .ou-accordion-label',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Border Color', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-button:hover',
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


		/***********************************
		 * Title Section
		 ***********************************/
		$title = $this->addControlSection( 'title_section', __('Title', 'oxy-ultimate'), 'assets/icon.png', $this );
		$title->addOptionControl(
			array(
				'type' => 'dropdown',
				'name' => __('Tag'),
				'slug' => 'title_tag',
			)

		)->setValue(array(  "h1", "h2", "h3", "h4", "h5", "h6", "div", "span", "p" ))->setDefaultValue('span');
		$title->typographySection( __('Typography'), '.ou-accordion-label', $this );


		/***********************************
		 * Sub Heading Section
		 ***********************************/
		$subtitle = $this->addControlSection( 'subtitle_section', __('Sub Title', 'oxy-ultimate'), 'assets/icon.png', $this );

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
		 * Icon
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
		$icDsp->rebuildElementOnChange();

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
				"name" 			=> __('Open Icon'),
				"slug" 			=> 'ouacrd_openicon'
			)
		);
		$openIcon->rebuildElementOnChange();

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
		$closeIcon->rebuildElementOnChange();

		$position = $icon->addControlSection( 'position_section', __('Position'), 'assets/icon.png', $this );
		$pos = $position->addOptionControl(
			array(
				"name" 			=> __('Position', "oxy-ultimate"),
				"slug" 			=> "ouacrd_iconpos",
				"type" 			=> 'radio',
				"value" 		=> ["left" => __("Left") , "right" => __("Right")],
				"default"		=> "right"
			)
		);
		$pos->rebuildElementOnChange();

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
				'name' 		=> __('Wrapper Background Color - Active', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ouacrd-icon-wrap',
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Icon Color', 'oxy-ultimate'),
				'selector' 	=> '.ouacrd-icon-wrap svg',
				'property' 	=> 'color'
			],
			[
				'name' 		=> __('Icon Color - Active', 'oxy-ultimate'),
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

		$ibrd = $icon->borderSection( __('Border'), '.ouacrd-icon-wrap', $this );
		$ibrd->addStyleControls([
			[
				'name' 		=> __('Border Color - Active', 'oxy-ultimate'),
				'selector' 	=> '.ou-accordion-item-active .ouacrd-icon-wrap',
				'property' 	=> 'border-color'
			]
		]);

		$icon->boxShadowSection( __('Box Shadow'), '.ouacrd-icon-wrap', $this );


		$content = $this->addControlSection('content_section', __('Content', 'oxy-ultimate'), 'assets/icon.png', $this);
		$content->typographySection( 'Typography', '.accordion-content-text, .accordion-content-text p', $this );

		$content->addStyleControl([
			'selector' 	=> '.accordion-content-text',
			'property' 	=> 'background-color'
		]);

		$contentsp = $content->addControlSection('cntsp_section', __('Spacing', 'oxy-ultimate'), 'assets/icon.png', $this);

		$contentsp->addPreset(
			"padding",
			"cntsp_padding",
			__("Padding"),
			'.accordion-content-text'
		)->whiteList();

		$contentsp->addPreset(
			"margin",
			"cntsp_margin",
			__("Margin"),
			'.accordion-content-text'
		)->whiteList();

		$content->borderSection( 'Border', '.accordion-content-text', $this );
		$content->boxShadowSection( 'Box Shadow', '.accordion-content-text', $this );
	}

	function render( $options, $defaults, $content ) {
		global $oxygen_svg_icons_to_load;
		global $wp_embed;
		
		$title_tag = isset( $options['title_tag'] ) ? esc_attr($options['title_tag']) : "span";
    	$subtitle_tag = isset( $options['subtitle_tag'] ) ? esc_attr($options['subtitle_tag']) : "span";

		$icon_pos = isset($options['ouacrd_iconpos']) ? $options['ouacrd_iconpos'] : 'right';
		$isShowIcon = isset( $options['ouacrd_enable_icon']) ? $options['ouacrd_enable_icon'] : 'no';
		$isExpandFirst = ! empty( $options['ouacrd_expand'] ) ? $options['ouacrd_expand'] : 'no';

		$is_same_icon = ! empty( $options['is_same'] ) ? $options['is_same'] : 'no';
		$class 		  = ($is_same_icon == 'yes') ? ' same-toggle-icon' : '';


		if( isset($options['ouacrd_openicon']) ) {
			$oxygen_svg_icons_to_load[] = $options['ouacrd_openicon'];
			$openIcon = $options['ouacrd_openicon'];
		}

		if( isset($options['ouacrd_closeicon']) ) {
			$oxygen_svg_icons_to_load[] = $options['ouacrd_closeicon'];
			$closeIcon = $options['ouacrd_closeicon'];
		}

		$builderClass = ( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-content' : '';

		$questionSchema = $answerSchema = $dataAttr = '';
		if( isset($options['ouacrd_schema']) && $options['ouacrd_schema']== "yes" ) {
			$questionSchema =' itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"';
			$answerSchema = ' itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"';
			$dataAttr .= ' data-schema="' . $options['ouacrd_schema'] . '"';
		}

		if( isset($options['ouacrd_tsd']) ) {
			$dataAttr .= ' data-transitionspeed="' . absint($options['ouacrd_tsd']) . '"';
		}

		$expandIconClass = ( isset($_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-expand-icon' : '';
		$collapseIconClass = ( isset($_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-collapse-icon' : '';

		//ACF Repeater
		if( function_exists('have_rows') && ! empty( $options['ouacrd_source'] ) && $options['ouacrd_source'] == 'acfrep' ) {
			if( isset( $options['acfrep_repfld'] ) && have_rows( $options['acfrep_repfld'] ) ) { ?>
				<div class="ou-accordion" data-expand-first="<?php echo $isExpandFirst; ?>">
			<?php
				$i = 0;
				while( have_rows( $options['acfrep_repfld'] ) ) { the_row();
					echo '<div' . $questionSchema . ' class="ou-accordion-item">';
					if( isset( $options['acfrep_title'] ) ) {
						echo '<div' . $dataAttr . ' class="ou-accordion-button" aria-selected="false" aria-expanded="false">';
							if( $isShowIcon == 'yes' && isset($options['ouacrd_openicon']) && $icon_pos == 'left' ) {
								echo '<span class="ou-accordion-open-icon ouacrd-icon-wrap ouacrd-icon-left'.$expandIconClass. $class . '"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $openIcon . '"></use></svg></span>';
							}

							if( $isShowIcon == 'yes' && isset($options['ouacrd_closeicon']) && $icon_pos == 'left' && $is_same_icon != 'yes') {
								echo '<span class="ou-accordion-close-icon ouacrd-icon-wrap ouacrd-icon-left toggle-icon'.$collapseIconClass.'"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $closeIcon . '"></use></svg></span>';
							}

							echo '<div class="ou-accordion-title-wrap">';
							
								$title = get_post_meta( get_the_ID(), $options['acfrep_repfld'] . '_' . $i . '_' . $options['acfrep_title'], true );
								echo '<'. $title_tag .' class="ou-accordion-label" itemprop="name description">' . wp_kses_post($title) . '</'. $title_tag .'>';

								$subtitle = get_post_meta( get_the_ID(), $options['acfrep_repfld'] . '_' . $i . '_' . $options['acfrep_subtitle'], true );
								if( $subtitle ) {
									echo '<'. $subtitle_tag .' class="ou-accordion-subtitle">' . $subtitle . '</'.$subtitle_tag.'>';
								}

							echo '</div>';

							if( $isShowIcon == 'yes' && isset($options['ouacrd_openicon']) && $icon_pos == 'right' ) {
								echo '<span class="ou-accordion-open-icon ouacrd-icon-wrap ouacrd-icon-right'. $expandIconClass. $class . '"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $openIcon . '"></use></svg></span>';
							}
							if( $isShowIcon == 'yes' && isset($options['ouacrd_closeicon']) && $icon_pos == 'right' && $is_same_icon != 'yes') {
								echo '<span class="ou-accordion-close-icon ouacrd-icon-wrap ouacrd-icon-right toggle-icon'.$collapseIconClass.'"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $closeIcon . '"></use></svg></span>';
							}
						echo '</div>';
					}

					if( isset( $options['acfrep_content'] ) ) {
						$content = get_post_meta( get_the_ID(), $options['acfrep_repfld'] . '_' . $i . '_' . $options['acfrep_content'], true );
						echo '<div' . $answerSchema . ' class="ou-accordion-content clearfix'.$builderClass.'" aria-selected="false" aria-hidden="true">';
							echo '<div class="accordion-content-text" itemprop="text">' . do_shortcode( wpautop( $wp_embed->autoembed( $content ) ) ) . '</div>';
						echo '</div>';
					}
					echo '</div>';
					$i++;
				}
			?>
				</div>
			<?php
			}
		}

		//Meta Box Group
		if( function_exists('rwmb_meta') && ! empty( $options['ouacrd_source'] ) && $options['ouacrd_source'] == 'mbgroup' ) {

			$group_values = isset( $options['mggroup_id'] ) ? rwmb_meta( $options['mggroup_id'] ) : '';
			if ( ! empty( $group_values ) ) {?>
				<div class="ou-accordion" data-expand-first="<?php echo $isExpandFirst; ?>">
					<?php
						foreach ( $group_values as $group_value ) {
							echo '<div' . $questionSchema . ' class="ou-accordion-item">';
							
							if( isset( $options['mbgroup_title'] ) && isset( $group_value[ $options['mbgroup_title'] ] ) ){
								echo '<div' . $dataAttr . ' class="ou-accordion-button" aria-selected="false" aria-expanded="false">';
									if( $isShowIcon == 'yes' && isset($options['ouacrd_openicon']) && $icon_pos == 'left' ) {
										echo '<span class="ou-accordion-open-icon ouacrd-icon-wrap ouacrd-icon-left'.$expandIconClass. $class . '"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $openIcon . '"></use></svg></span>';
									}

									if( $isShowIcon == 'yes' && isset($options['ouacrd_closeicon']) && $icon_pos == 'left' && $is_same_icon != 'yes') {
										echo '<span class="ou-accordion-close-icon ouacrd-icon-wrap ouacrd-icon-left toggle-icon"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $closeIcon . '"></use></svg></span>';
									}
									
									echo '<div class="ou-accordion-title-wrap">';

										$title = $group_value[ $options['mbgroup_title'] ];
										echo '<'. $title_tag .' class="ou-accordion-label" itemprop="name description">' . wp_kses_post($title) . '</'. $title_tag .'>';

										$subtitle = $group_value[ $options['mbgroup_subtitle'] ];
										if( $subtitle ) {
											echo '<'. $subtitle_tag .' class="ou-accordion-subtitle">' . $subtitle . '</'.$subtitle_tag.'>';
										}

									echo '</div>';

									if( $isShowIcon == 'yes' && isset($options['ouacrd_openicon']) && $icon_pos == 'right' ) {
										echo '<span class="ou-accordion-open-icon ouacrd-icon-wrap ouacrd-icon-right'.$expandIconClass. $class . '"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $openIcon . '"></use></svg></span>';
									}
									if( $isShowIcon == 'yes' && isset($options['ouacrd_closeicon']) && $icon_pos == 'right' && $is_same_icon != 'yes') {
										echo '<span class="ou-accordion-close-icon ouacrd-icon-wrap ouacrd-icon-right toggle-icon"><svg id="' . $options['selector'] . '-open-icon"><use xlink:href="#' . $closeIcon . '"></use></svg></span>';
									}
								echo '</div>';
							}

							if( isset( $options['mbgroup_content'] ) && isset( $group_value[ $options['mbgroup_content'] ] ) ) {
								$content = $group_value[ $options['mbgroup_content'] ];
								echo '<div' . $answerSchema . ' class="ou-accordion-content clearfix'.$builderClass.'" aria-selected="false" aria-hidden="true">';
									echo '<div class="accordion-content-text" itemprop="text">' . do_shortcode( wpautop( $wp_embed->autoembed( $content ) ) ) . '</div>';
								echo '</div>';
							}

							echo '</div>';
						}
					?>
				</div>
			<?php
			}
		}

		if( ! isset($_GET['oxygen_iframe']) && ! defined('OXY_ELEMENTS_API_AJAX') ) {
			add_action( 'wp_footer', array( $this, 'oudynamicaccordion_script' ) );
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-dynamic-acrd{min-height: 40px; width: 100%;display:inline-flex;}
				.ou-accordion,.ou-accordion-item {clear: both;width: 100%;}
				.ou-accordion-title-wrap{flex-grow: 1;}
				.ou-accordion .ou-accordion-button {
					background-color: #efefef
					cursor: pointer;
					display: -webkit-flex;
					display: -moz-flex;
					display: flex;
					-webkit-align-items: center;
					align-items: center;
					padding: 12px 18px;
				}
				.oxy-ou-dynamic-acrd .ou-accordion-label,
				.oxy-ou-dynamic-acrd .ou-accordion-subtitle{
					clear: both;
    				display: block;
    				width: 100%;
    			}
				.oxy-ou-dynamic-acrd .ou-accordion-content{clear:both;overflow:hidden;}
				body:not(.oxygen-builder-body) .oxy-ou-dynamic-acrd .ou-accordion-content{display: none;}
				.accordion-content-text{margin-bottom: 12px; padding: 18px 18px 10px;}
				.accordion-content-text p{margin-top:0;}
				.accordion-content-text p:last-child{margin-bottom:0;}
				.ouacrd-icon-wrap {
					display: flex;
					align-items: center;
					justify-content: center;
					line-height: 0;
					width: 20px;
				}
				.ouacrd-icon-wrap svg {width: 15px;height: 15px;fill: currentColor;}
				.oxy-ou-dynamic-acrd .ouacrd-icon-wrap svg {
					width: 15px;
					height: 15px;
					fill: currentColor;
					transition: transform 0.75s;
					-webkit-transition: -webkit-transform 0.75s;
				}
				.oxy-ou-dynamic-acrd .ou-accordion-item-active .ouacrd-icon-wrap.same-toggle-icon svg{
					-webkit-transform: rotateX(180deg);
					-moz-transform: rotateX(180deg);
					transform: rotateX(180deg);
				}
				.ouacrd-icon-left {margin-right: 15px;}
				.ouacrd-icon-right {margin-left: 15px;}
				.ou-accordion-button:hover svg{fill: currentColor;}
				.oxy-ou-dynamic-acrd .toggle-icon:not(.same-toggle-icon){display:none}';
			
			$this->css_added = true;
		}

		return $css;
	}

	function oudynamicaccordion_script() {
		wp_enqueue_script(
			'ou-accordion-script', 
			OXYU_URL . 'assets/js/ou-accordion.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/ou-accordion.js' ), 
			true
		);
	}
}

new OUDynamicAccordion();