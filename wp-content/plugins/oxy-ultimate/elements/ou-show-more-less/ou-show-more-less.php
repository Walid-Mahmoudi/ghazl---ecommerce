<?php

namespace Oxygen\OxyUltimate;

class OUShowMoreLess extends \OxyUltimateEl
{
	public $css_added = false;
	private $msg = false;
	
	function name() {
		return __( "Show More/Less", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_show_more_less";
	}

	function oxyu_button_place() {
		return "content";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}

	function oushmlGeneralControls() {
		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if content is not displaying properly on Builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		//* Builder Editor Mode
		$toggleBtn = $this->El->addControl('buttons-list', 'oubuilder_preview', __('Builder Editor Mode', 'oxy-ultimate') );
		$toggleBtn->setValue([__('Expand', "oxy-ultimate"), __('Collapse', "oxy-ultimate")]);
		$toggleBtn->setDefaultValue('Collapse');
		$toggleBtn->setValueCSS([
			'Expand' => '.oushml-toggle-content{height:auto;} .oushml-toggle-content:after{opacity: 0;} .toggle-morelink{visibility:hidden;height:0} .toggle-lesslink{display:block;}',
			'Collapse' => '.toggle-morelink{visibility:visible;height:auto} .toggle-lesslink{display:hide;}'
		]);
		$toggleBtn->setParam( 'description', __('Select <span style="color:#ff7171;">"Expand"</span> option when you are editing the content.') );
		$toggleBtn->whiteList();

		//* More Text
		$moreText = $this->addCustomControl(
			'<div class="oxygen-input " ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_show_more_less_more_text\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_show_more_less_more_text\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id,\'oxy-ou_show_more_less\',\'oxy-ou_show_more_less_more_text\')" class="ng-pristine ng-valid ng-touched" ng-keydown="$event.keyCode === 13 && iframeScope.rebuildDOM(iframeScope.component.active.id)">
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicSMLCMoreText">data</div>
			</div>',
			'more_text'
		);
		$moreText->setParam( 'heading', __('More Text') );
		$moreText->setParam( 'base64', true );
		$moreText->setParam( 'description', __('Enter the text for more link. Hit on Enter button to see the text on the Builder Editor. Enter <span style=\"color:#ff7171;\">&amp;apos;</span> for single quote.', "oxy-ultimate") );


		//* Hide More Text
		$moreLessTxt = $this->El->addControl('buttons-list', 'hide_mtext', __('Hide More Text'));
		$moreLessTxt->setValue(['No', 'Yes']);
		$moreLessTxt->setValueCSS(['Yes' => '.ou-more-text{display:none}']);
		$moreLessTxt->setDefaultValue('No');
		$moreLessTxt->setParam('description', __('This option allows you to hide more link text only, when you will just show the icon.', "oxy-ultimate"));
		$moreLessTxt->whiteList();

		//* Less Text
		$lessText = $this->addCustomControl(
			'<div class="oxygen-input " ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_show_more_less_less_text\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_show_more_less_less_text\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id,\'oxy-ou_show_more_less\',\'oxy-ou_show_more_less_less_text\')" class="ng-pristine ng-valid ng-touched" ng-keydown="$event.keyCode === 13 && iframeScope.rebuildDOM(iframeScope.component.active.id)">
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicSMLCLessText">data</div>
			</div>',
			'less_text'
		);
		$lessText->setParam( 'heading', __('Less Text') );
		$lessText->setParam( 'base64', true );
		$lessText->setParam( 'description', __('Enter the text for less link. Enter <span style=\"color:#ff7171;\">&amp;apos;</span> for single quote.', "oxy-ultimate") );

		//* Hide Less Text
		$hideLessTxt = $this->El->addControl('buttons-list', 'hide_ltext', __('Hide Less Text'));
		$hideLessTxt->setValue(['No', 'Yes']);
		$hideLessTxt->setValueCSS(['Yes' => '.ou-less-text{display:none}']);
		$hideLessTxt->setDefaultValue('No');
		$hideLessTxt->setParam('description', __('This option allows you to hide less link text only, when you will just show the icon.', "oxy-ultimate"));
		$hideLessTxt->whiteList();

		//* Hide Less Link Button
		$hideLessTxtBtn = $this->addOptionControl([
			'type' 			=> 'radio',
			'name' 			=> __('Hide Less Link Button',"oxy-ultimate"),
			'slug' 			=> 'hide_less_link',
			'value' 		=> ['no' => 'No', 'yes' => 'Yes'],
			'default' 		=> 'no'
		]);
		$hideLessTxtBtn->setParam('description', __('This option allows you to hide less link, when the text block has been expanded.', "oxy-ultimate"));

		//* Height
		$collapseHeight = $this->addStyleControl([
			'control_type' 		=> 'slider-measurebox',
			'name' 				=> __('Height'),
			'description' 		=> __('Height for collapsed state (in pixels)'),
			'property' 			=> 'height',
			'slug' 				=> 'less_height',
			'selector' 			=> '.ou-more-less-content'
		]);
		$collapseHeight->setRange(0,1000, 10);
		$collapseHeight->setUnits('px','px');
		$collapseHeight->setDefaultValue(150);

		//* Height
		$speed = $this->addOptionControl([
			'type' 			=> 'slider-measurebox',
			'name' 			=> __('Slide Transition Speed'),
			'description' 	=> __('Unit in miliseconds.'),
			'slug' 			=> 'transition_speed'
		]);
		$speed->setRange(0,2000, 10);
		$speed->setUnits('ms','ms');
		$speed->setDefaultValue(700);

		$ts = $this->addStyleControl([
			"name" 	=> 'Link Hover Transition Duration',
			"property" 	=> 'transition-duration',
			"selector" 	=> ".oushml-link-wrapper",
			"control_type" => 'slider-measurebox'
		])->setRange(0,2,0.1)->setUnits('s','sec')->setDefaultValue(0.5);

		$linkAlignment = $this->El->addControl('buttons-list', 'link_alignment', __('Link Alignment'));
		$linkAlignment->setValue(['Left', 'Right', 'Center']);
		$linkAlignment->setValueCSS(['Right' => '.oushml-links{text-align: right}', 'Center'  => '.oushml-links{text-align: center}']);
		$linkAlignment->setDefaultValue('Left');
		$linkAlignment->whiteList();
	}

	/***********************
	 * Fade Effect
	************************/
	function oushmlFadeEffect() {
		$fade = $this->addControlSection('oushml_shadow', __('Fade Effect'), 'assets/icon', $this);

		$fade->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if changes are not showing properly on Builder editor.</div>'), 
			'fade_desc'
		)->setParam('heading', 'Note:');

		$fade->addOptionControl([
			'type' 			=> 'radio',
			'name' 			=> __('Disable Fade Effect',"oxy-ultimate"),
			'slug' 			=> 'disable_shadow',
			'value' 		=> ['no' => 'No', 'yes' => 'Yes'],
			'default' 		=> 'no'
		])->setParam('description', 'Click on Apply Params button to see the changes.');

		$fade->addStyleControl([
			'selector' 		=> '.ou-more-less-content:after',
			'control_type' 	=> 'colorpicker',
			'name' 			=> __('Gradient Background Color 1',"oxy-ultimate"),
			'slug' 			=> 'oushml_gbgclr1',
			'property' 		=> '--oushml-gradient-one',
			'condition' 	=> 'disable_shadow=no'
		]);
		
		$fade->addStyleControl([
			'selector' 		=> '.ou-more-less-content:after',
			'control_type' 	=> 'colorpicker',
			'name' 			=> __('Gradient Background Color 2',"oxy-ultimate"),
			'slug' 			=> 'oushml_gbgclr2',
			'property' 		=> '--oushml-gradient-two',
			'condition' 	=> 'disable_shadow=no'
		]);

		$bgHeight = $fade->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Height'),
			'property' 		=> 'height',
			'selector' 		=> '.ou-more-less-content:after',
			'condition' 	=> 'disable_shadow=no'
		]);
		$bgHeight->setRange(0,100, 10);
		$bgHeight->setUnits('px','px');
		$bgHeight->setDefaultValue(50);
	}

	/***********************
	 * More Link Style
	************************/
	function oushmlMoreLinkStyle() {
		$mLinkStyle = $this->addControlSection('oushml_mlstyle', __('More Link Style'), 'assets/icon', $this);

		$mLinkSize = $mLinkStyle->addControlSection('more_link_size', __('Width / Spacing'), 'assets/icon', $this);

		$link_selector = '.show-more-link .oushml-link-wrapper';

		$mLinkSize->addPreset(
			"padding",
			"morelink_padding",
			__("Padding"),
			$link_selector
		)->whiteList();

		$mLinkSize->addPreset(
			"margin",
			"morelink_margin",
			__("Margin"),
			$link_selector
		)->whiteList();

		$mLinkSize->addStyleControl(
			array(
				"name" 			=> __('Link Width', "oxy-ultimate"),
				"slug" 			=> "oushml_mwidth",
				"selector" 		=> $link_selector,
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'width'
			)
		)->setRange(0, 900, 10)->setUnits("px", "px,%,em");

		$mlink_color = $mLinkStyle->addControlSection("mlink_color", __("Color"), "assets/icon.png", $this);
		$mlink_color->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Note: Initial text color will set from Typography section.</div>'), 
			'txtcolor_desc'
		);

		$mlink_color->addStyleControls([
			array(
				"name" 		=> 'Hover Color',
				"property" 	=> 'color',
				"selector" 	=> $link_selector . ':hover'
			),
			array(
				"property" 	=> 'background-color',
				"selector" 	=> $link_selector
			),
			array(
				"name"     	=> 'Hover Background Color',
				"property" 	=> 'background-color',
				"selector" 	=> $link_selector . ':hover'
			)
		]);

		$mLinkStyle->typographySection( __('Typography'), $link_selector, $this );
		$mLinkStyle->borderSection( __('Border'), $link_selector, $this );
		$mLinkStyle->borderSection( __('Hover Border'), $link_selector . ':hover', $this );
		$mLinkStyle->boxShadowSection( __('Box Shadow'), $link_selector, $this );
		$mLinkStyle->boxShadowSection( __('Hover Box Shadow'), $link_selector . ':hover', $this );
	}

	/***********************
	 * More Link Icon
	************************/
	function oushmlMoreLinkIcon() {
		$mIconStyle = $this->addControlSection('oushml_miconstyle', __('More Link Icon'), 'assets/icon', $this);

		$moreIcon = $mIconStyle->addControlSection('oushml_moreicon', __('Icon'), 'assets/icon', $this);
		$mIcon = $moreIcon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon'),
				"slug" 			=> 'oushml_micon'
			)
		);
		$mIcon->setParam('description', 'After selecting the icon you would click on Apply Params button to get the icon.');

		$mipos = $moreIcon->addOptionControl(
			array(
				"type" 			=> 'radio',
				"name" 			=> __('Position', "oxy-ultimate"),
				"slug" 			=> "oushml_mipos",
				"value" 		=> ["left" => __("Left") , "right" => __("Right"), 'top' => __('Top'), 'bottom' => __('Bottom')],
				"default"		=> "right"
			)
		);
		$mipos->setParam('description', 'Click on Apply Params button to see the changes.');

		$moreIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"slug" 			=> "oushml_migapright",
				"selector" 		=> '.ou-show-more-icon.oushml-icon-right',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '10',
				"property" 		=> 'margin-left',
				"condition" 	=> "oushml_mipos=right"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$moreIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"slug" 			=> "oushml_migaptop",
				"selector" 		=> '.ou-show-more-icon.oushml-icon-top',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '5',
				"property" 		=> 'margin-bottom',
				"condition" 	=> "oushml_mipos=top"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$moreIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Icon & Text', "oxy-ultimate"),
				"slug" 			=> "oushml_migapleft",
				"selector" 		=> '.ou-show-more-icon.oushml-icon-left',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '10',
				"property" 		=> 'margin-right',
				"condition" 	=> "oushml_mipos=left"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$moreIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"slug" 			=> "oushml_migapbottom",
				"selector" 		=> '.ou-show-more-icon.oushml-icon-bottom',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '5',
				"property" 		=> 'margin-top',
				"condition" 	=> "oushml_mipos=bottom"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$miSize = $mIconStyle->addControlSection('more_icon_size', __('Size'), 'assets/icon', $this);
		$miSize->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxy-ultimate"),
				"slug" 			=> "oushml_miconsize",
				"selector" 		=> '.ou-show-more-icon svg',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'width|height'
			)
		)->setRange(20, 80, 10)->setUnits("px", "px")->setDefaultValue(18);

		$miSize->addStyleControl(
			array(
				"name" 			=> __('Icon Wrapper Width', "oxy-ultimate"),
				"slug" 			=> "oushml_miwwidth",
				"selector" 		=> '.ou-show-more-icon',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'width|height'
			)
		)->setRange(20, 120, 10)->setUnits("px", "px");

		$micon_color = $mIconStyle->addControlSection("micon_color", __("Icon Color"), "assets/icon.png", $this);
		$micon_color->addStyleControls([
			array(
				"property" 	=> 'color',
				"selector" 	=> '.ou-show-more-icon svg'
			),
			array(
				"name" 		=> 'Hover Color',
				"property" 	=> 'color',
				"selector" 	=> ".oushml-link:hover .ou-show-more-icon svg"
			),
			array(
				"property" 	=> 'background-color',
				"selector" 	=> '.ou-show-more-icon'
			),
			array(
				"name"     	=> 'Hover Background Color',
				"property" 	=> 'background-color',
				"selector" 	=> ".oushml-link:hover .ou-show-more-icon"
			)
		]);

		$mIconStyle->borderSection( __('Border'), '.ou-show-more-icon', $this );
		$mIconStyle->borderSection( __('Hover Border'), '.oushml-link:hover .ou-show-more-icon', $this );
		$mIconStyle->boxShadowSection( __('Box Shadow'), '.ou-show-more-icon', $this );
		$mIconStyle->boxShadowSection( __('Hover Box Shadow'), '.oushml-link:hover .ou-show-more-icon', $this );
	}

	/***********************
	 * Less Link Style
	************************/
	function oushmlLessLinkStyle() {
		$lLinkStyle = $this->addControlSection('oushml_llstyle', __('Less Link Style'), 'assets/icon', $this);

		$linkPreview = $lLinkStyle->addControl( 'buttons-list', 'lesslink_preview', 'In Editor Mode');
		$linkPreview->setValue(['Enable', 'Disable']);
		$linkPreview->setValueCSS(['Enable' => '.toggle-morelink{visibility:hidden;height:0} .toggle-lesslink{display:block;}']);
		$linkPreview->setDefaultValue('Disable');
		$linkPreview->setParam('description', 'Select <span style="color:#ff7171;">Enable</span> option when you will customize the design. After completing the design, you will <span style="color:#ff7171;">Disable/span> the preview option.');
		$linkPreview->whiteList();

		$link_selector = '.show-less-link .oushml-link-wrapper';

		$lLinkSize = $lLinkStyle->addControlSection('less_link_size', __('Width / Spacing'), 'assets/icon', $this);
		$lLinkSize->addPreset(
			"padding",
			"lesslink_padding",
			__("Padding"),
			$link_selector
		)->whiteList();

		$lLinkSize->addPreset(
			"margin",
			"lesslink_margin",
			__("Margin"),
			$link_selector
		)->whiteList();

		$lLinkSize->addStyleControl(
			array(
				"name" 			=> __('Link Width', "oxy-ultimate"),
				"slug" 			=> "oushml_lwidth",
				"selector" 		=> $link_selector,
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'width'
			)
		)->setRange(0, 900, 10)->setUnits("px", "px,%,em");

		$llink_color = $lLinkStyle->addControlSection("llink_color", __("Color"), "assets/icon.png", $this);
		$llink_color->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.325; font-size: 13px;"><span style="color:#fff;font-weight:bold">NOTE:</span> Initial text color will be set from Typography section.</div>'), 
			'ltxtcolor_desc'
		);

		$llink_color->addStyleControls([
			array(
				"name" 		=> 'Hover Color',
				"property" 	=> 'color',
				"selector" 	=> $link_selector . ':hover'
			),
			array(
				"property" 	=> 'background-color',
				"selector" 	=> $link_selector
			),
			array(
				"name"     	=> 'Hover Background Color',
				"property" 	=> 'background-color',
				"selector" 	=> $link_selector . ':hover'
			)
		]);

		$lLinkStyle->typographySection( __('Typography'), $link_selector, $this );
		$lLinkStyle->borderSection( __('Border'), $link_selector, $this );
		$lLinkStyle->borderSection( __('Hover Border'), $link_selector . ':hover', $this );
		$lLinkStyle->boxShadowSection( __('Box Shadow'), $link_selector, $this );
		$lLinkStyle->boxShadowSection( __('Hover Box Shadow'), $link_selector . ':hover', $this );
	}

	/***********************
	 * Less Link Icon
	************************/
	function oushmlLessLinkIcon() {
		$lIconStyle = $this->addControlSection('oushml_lIconStyle', __('Less Link Icon'), 'assets/icon', $this);

		$linkPreview = $lIconStyle->addControl( 'buttons-list', 'lesslink_preview', 'In Editor Mode');
		$linkPreview->setValue(['Enable', 'Disable']);
		$linkPreview->setValueCSS(['Enable' => '.toggle-morelink{visibility:hidden;height:0} .toggle-lesslink{display:block;}']);
		$linkPreview->setDefaultValue('Disable');
		$linkPreview->setParam('description', 'Select <span style="color:#ff7171;">Enable</span> option when you will customize the design. After completing the design, you will <span style="color:#ff7171;">Disable</span> the preview option.');
		$linkPreview->whiteList();

		$lessIcon = $lIconStyle->addControlSection('oushml_licon', __('Icon'), 'assets/icon', $this);
		$lIcon = $lessIcon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon'),
				"slug" 			=> 'oushml_licon'
			)
		);
		$lIcon->setParam('description', 'After selecting the icon you would click on Apply Params button to get the icon.');

		$lipos = $lessIcon->addOptionControl(
			array(
				"type" 			=> 'radio',
				"name" 			=> __('Position', "oxy-ultimate"),
				"slug" 			=> "oushml_lipos",
				"value" 		=> ["left" => __("Left") , "right" => __("Right"), 'top' => __('Top'), 'bottom' => __('Bottom')],
				"default"		=> "right"
			)
		);
		$lipos->setParam('description', 'Click on Apply Params button to see the changes.');

		$lessIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"slug" 			=> "oushml_migapright",
				"selector" 		=> '.ou-show-less-icon.oushml-icon-right',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '10',
				"property" 		=> 'margin-left',
				"condition" 	=> "oushml_lipos=right"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$lessIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"slug" 			=> "oushml_migaptop",
				"selector" 		=> '.ou-show-less-icon.oushml-icon-top',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '5',
				"property" 		=> 'margin-bottom',
				"condition" 	=> "oushml_lipos=top"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$lessIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Icon & Text', "oxy-ultimate"),
				"slug" 			=> "oushml_migapleft",
				"selector" 		=> '.ou-show-less-icon.oushml-icon-left',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '10',
				"property" 		=> 'margin-right',
				"condition" 	=> "oushml_lipos=left"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$lessIcon->addStyleControl(
			array(
				"name" 			=> __('Gap Between Text & Icon', "oxy-ultimate"),
				"slug" 			=> "oushml_migapbottom",
				"selector" 		=> '.ou-show-less-icon.oushml-icon-bottom',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '5',
				"property" 		=> 'margin-top',
				"condition" 	=> "oushml_lipos=bottom"
			)
		)->setRange(0, 30, 1)->setUnits("px", "px");

		$liSize = $lIconStyle->addControlSection('less_icon_size', __('Size'), 'assets/icon', $this);
		$liSize->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxy-ultimate"),
				"slug" 			=> "oushml_liconsize",
				"selector" 		=> '.ou-show-less-icon svg',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'width|height'
			)
		)->setRange(20, 80, 10)->setUnits("px", "px")->setDefaultValue(18);

		$liSize->addStyleControl(
			array(
				"name" 			=> __('Icon Wrapper Width', "oxy-ultimate"),
				"slug" 			=> "oushml_liwwidth",
				"selector" 		=> '.ou-show-less-icon',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'width|height'
			)
		)->setRange(20, 120, 10)->setUnits("px", "px");

		$liSize->addStyleControl(
			array(
				"name" 			=> __('Link Width', "oxy-ultimate"),
				"slug" 			=> "oushml_lwidth",
				"selector" 		=> '.show-less-link .oushml-link-wrapper',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'width'
			)
		)->setRange(0, 900, 10)->setUnits("px", "px,%,em");

		$liSize->addPreset(
			"padding",
			"lesslink_padding",
			__("Padding"),
			'.show-less-link .oushml-link-wrapper'
		)->whiteList();

		$liSize->addPreset(
			"margin",
			"lesslink_margin",
			__("Margin"),
			'.show-less-link .oushml-link-wrapper'
		)->whiteList();

		$licon_color = $lIconStyle->addControlSection("licon_color", __("Icon Color"), "assets/icon.png", $this);
		$licon_color->addStyleControls([
			array(
				"property" 	=> 'color',
				"selector" 	=> '.ou-show-less-icon svg'
			),
			array(
				"name" 		=> 'Hover Color',
				"property" 	=> 'color',
				"selector" 	=> ".oushml-link:hover .ou-show-less-icon svg"
			),
			array(
				"property" 	=> 'background-color',
				"selector" 	=> '.ou-show-less-icon'
			),
			array(
				"name"     	=> 'Hover Background Color',
				"property" 	=> 'background-color',
				"selector" 	=> ".oushml-link:hover .ou-show-less-icon"
			)
		]);

		$lIconStyle->borderSection( __('Border'), '.ou-show-less-icon', $this );
		$lIconStyle->borderSection( __('Hover Border'), '.oushml-link:hover .ou-show-less-icon', $this );
		$lIconStyle->boxShadowSection( __('Box Shadow'), '.ou-show-less-icon', $this );
		$lIconStyle->boxShadowSection( __('Hover Box Shadow'), '.oushml-link:hover .ou-show-less-icon', $this );
	}

	/***********************
	 * Underline Animation
	************************/
	function oushmlUnderlineAnimation() {
		$ulAnim = $this->addControlSection('oushml_ulanim', __('Underline Animation'), 'assets/icon', $this);

		$ulAnim->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if content is not displaying properly on Builder editor.</div>'), 
			'anim_description'
		)->setParam('heading', 'Note:');

		$ulAnim->addOptionControl(
			array(
				"type" 		=> 'dropdown',
				"name" 		=> __('Type', "oxy-ultimate"),
				"slug" 		=> 'ulanim_type',
				"value" 	=> array(
					'none' 				=> __('Select Underline Type', 'oxy-ultimate'),
					'underline_lr' 		=> __('Underline-Left to Right', 'oxy-ultimate'),
					'underline_rl' 		=> __('Underline-Right to Left', 'oxy-ultimate'),
					'underline_outwards'=> __('Underline-Outwards', 'oxy-ultimate'),
					'underline_inwards'	=> __('Underline-Inwards', 'oxy-ultimate'),
				),
				"default" 	=> 'none'
			)
		)->setParam('description', 'Click on Apply Params button to see the changes.');

		$ulAnim->addOptionControl(
			array(
				"type" 		=> 'radio',
				"name" 		=> '',
				"slug" 		=> 'ulanim_appearance',
				"value" 	=> array(
					'sml-on-hover'		=> __('Show on Hover', 'oxy-ultimate'),
					'sml-off-hover'		=> __('Hide on Hover', 'oxy-ultimate')
				),
				"default" 	=> 'sml-on-hover',
				"condition" => 'ulanim_type!=none'
			)
		)->setParam('description', 'Click on Apply Params button to see the changes.');

		$ulAnim->addStyleControls(
			array(
				array(
					"control_type" 	=> 'colorpicker',
					"name" 			=> __('Color'),
					"property" 		=> 'background-color',
					"slug" 			=> "ul_bgc",
					"selector" 		=> '.oushml-link-wrapper .ul-anim:before,.oushml-link-wrapper .ul-anim:after',
					"condition" 	=> 'ulanim_type!=none'
				),
				array(
					"control_type" 	=> 'slider-measurebox',
					"name" 			=> __('Width'),
					"property" 		=> 'height',
					"slug" 			=> "ul_height",
					"selector" 		=> '.oushml-link-wrapper .ul-anim:before,.oushml-link-wrapper .ul-anim:after',
					"default" 		=> '5',
					"units" 		=> 'px',
					"condition" 	=> 'ulanim_type!=none'
				)
			)
		);

		$ulAnim->addStyleControl(
			array(
				"control_type" 	=> 'slider-measurebox',
				"name" 			=> __('Position'),
				"property" 		=> 'bottom',
				"slug" 			=> "ul_pos",
				"selector" 		=> '.oushml-link-wrapper .ul-anim:before,.oushml-link-wrapper .ul-anim:after',
				"default" 		=> '-10',
				"condition" 	=> 'ulanim_type!=none'
			)
		)->setRange('-10', '10', '1')->setUnits("px", "px");

		$ulAnim->addStyleControl(
			array(
				"name" 			=> __('Transition Duration', "oxy-ultimate"),
				'property' 		=> 'transition-duration', 
				'selector' 		=> '.oushml-link-wrapper .ul-anim:before,.oushml-link-wrapper .ul-anim:after',
				"slug" 			=> "ul_ts",
				"control_type" 	=> 'slider-measurebox',
				"default" 		=> '0.75',
				"condition" 	=> 'ulanim_type!=none'
			)
		)->setRange('0', '10', '0.1')->setUnits("s", "sec");
	}

	function controls() {
		$this->oushmlGeneralControls();
		$this->oushmlFadeEffect();
		$this->oushmlMoreLinkStyle();
		$this->oushmlMoreLinkIcon();
		$this->oushmlLessLinkStyle();
		$this->oushmlLessLinkIcon();
		$this->oushmlUnderlineAnimation();
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
		global $oxygen_svg_icons_to_load;
		$more_icon = $less_icon = $output = $ulAnim = "";
		
		$show_more_text = isset( $options['more_text'] ) ? $this->fetchDynamicData( $options['more_text'] ) : __('Show More', 'oxy-ultimate');
		$show_less_text = isset( $options['less_text'] ) ? $this->fetchDynamicData( $options['less_text'] ) : __('Show Less', 'oxy-ultimate');

		$shadow = isset($options['disable_shadow']) ? $options['disable_shadow'] : 'no';
		$shadowCSSClass = ( $shadow === 'no' ) ? ' oushml-show-shadow' : '';
		$hideLessLink = isset( $options['hide_less_link'] ) ? $options['hide_less_link'] : 'no';

		$less_height = isset( $options['less_height'] ) ? esc_attr($options['less_height']) : '100';
		$speed = isset( $options['transition_speed'] ) ? esc_attr($options['transition_speed']) : '700';

		$builderClass = ( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' oushml-toggle-content' : '';
		$builderMoreClass = ( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-morelink' : '';
		$builderLessClass = ( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) ? ' toggle-lesslink' : '';

		if( isset( $options['ulanim_type'] ) && $options['ulanim_type'] != 'none' )
		{
			$ulAnim = ' ul-anim ' . $options['ulanim_type'] . ' ' . $options['ulanim_appearance'];
		}
		
		if( isset( $options['oushml_micon'] ) ) {
			$oxygen_svg_icons_to_load[] = $options['oushml_micon'];
			$more_icon = '<svg id="' . $options['selector'] . '-more-icon"><use xlink:href="#' . $options['oushml_micon'] . '"></use></svg>';
		}

		if( isset( $options['oushml_licon'] ) ) {
			$oxygen_svg_icons_to_load[] = $options['oushml_licon'];
			$less_icon = '<svg id="' . $options['selector'] . '-less-icon"><use xlink:href="#' . $options['oushml_licon'] . '"></use></svg>';
		}

		echo '<div class="oxy-inner-content ou-more-less-content' . $shadowCSSClass . $builderClass . '" data-speed="'.$speed.'" data-height="'.$less_height.'" data-fadeeffect="'.$shadow.'">';
		
		if( $content ) {
			
			if( function_exists('do_oxygen_elements') )
				$output .= do_oxygen_elements( $content );
			else
				$output .= do_shortcode( $content );

			if( ! isset($_GET['oxygen_iframe']) && ! defined('OXY_ELEMENTS_API_AJAX') ) {
				wp_enqueue_script(
					'ou-shml-script', 
					OXYU_URL . 'assets/js/ou-show-more-less.js',
					array(),
					filemtime( OXYU_DIR . 'assets/js/ou-show-more-less.js' ),
					true
				);
			}
		} elseif( ( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) && ! $this->msg ) {
			$output .= '<p>Content of nest component(s) will show here. Add or drag&drop the component(s) inside the <strong>Show More Less</strong> component.</p>';
			$this->msg = true;
		}

		echo $output . '</div>';
		echo '<span class="oushml-links">';
		echo '<span class="show-more-link oushml-link'.$builderMoreClass.'" role="button"><span class="oushml-link-wrapper items-pos-' . $options['oushml_mipos'] . '">';
		
			if( isset( $options['oushml_mipos'] ) 
				&& ( $options['oushml_mipos'] == 'left' || $options['oushml_mipos'] == 'top' ) 
				&& ! empty( $more_icon ) 
			) {
				echo '<span class="ou-show-more-icon oushml-icon oushml-icon-' . $options['oushml_mipos'] . '">' . $more_icon . '</span>';
			}

			echo '<span class="oushml-link-text ou-more-text'. $ulAnim . '">' . esc_attr( $show_more_text ) . '</span>';
			
			if( isset( $options['oushml_mipos'] ) 
				&& ( $options['oushml_mipos'] == 'right' || $options['oushml_mipos'] == 'bottom' )
				&& ! empty( $more_icon )
			) {
				echo '<span class="ou-show-more-icon oushml-icon oushml-icon-' . $options['oushml_mipos'] . '">' . $more_icon . '</span>';
			}
		
		echo '</span></span>';

		if( $hideLessLink == 'no' ) {
			echo '<span class="show-less-link oushml-link oushml-link-toggle'.$builderLessClass.'" role="button"><span class="oushml-link-wrapper items-pos-' . $options['oushml_lipos'] . '">';

				if( isset( $options['oushml_lipos'] ) 
					&& ( $options['oushml_lipos'] == 'left' || $options['oushml_lipos'] == 'top' )
					&& ! empty( $less_icon ) 
				) {
					echo '<span class="ou-show-less-icon oushml-icon oushml-icon-' . $options['oushml_lipos'] . '">' . $less_icon . '</span>';
				}
				
				echo '<span class="oushml-link-text ou-less-text'. $ulAnim . '">' . esc_attr( $show_less_text ) . '</span>';

				if( isset( $options['oushml_lipos'] ) 
					&& ( $options['oushml_lipos'] == 'right' || $options['oushml_lipos'] == 'bottom' )
					&& ! empty( $less_icon ) 
				) {
					echo '<span class="ou-show-less-icon oushml-icon oushml-icon-' . $options['oushml_lipos'] . '">' . $less_icon . '</span>';
				}

			echo '</span></span>';
		}
		
		echo '</span>';
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$this->css_added = true;
			$css .= '.oxy-ou-show-more-less{display: flex; flex-direction: column; width: 100%;}
					.ou-more-less-content{
						display: block;
						overflow: hidden;
						position: relative;
						width: 100%;
					},
					.ou-more-less-content:empty{
						min-height: 80px;
					}
					body:not(.oxygen-builder-body) .ou-more-less-content, .oushml-toggle-content{height: 150px;}
					.ou-more-less-content:after {
						--oushml-gradient-one: rgba(255,255,255,0.5);
						--oushml-gradient-two: rgba(255,255,255,0.5);
					}
					.ou-more-less-content:after{
						content: "";
						background: linear-gradient(to bottom, var(--oushml-gradient-one), var(--oushml-gradient-two));
						display: block;
						height: 50px;
						position: absolute;
						bottom: 0;
						left: 0;
						width: 100%;
					}
					.ou-more-less-content p {margin-top: 0;}
					.oushml-links{cursor: pointer;}
					.oushml-link-wrapper {
						display: inline-flex;
						flex-direction: row;
						align-items: center;
						position: relative;						
					}
					.oushml-link-wrapper.items-pos-top,
					.oushml-link-wrapper.items-pos-bottom {
						flex-direction: column;
					}
					.oushml-icon {
						display: flex;
						align-items: center;
						flex-direction: column;
						justify-content: center;
					}
					.oushml-icon svg {
						width: 18px;
						height: 18px;
						vertical-align: middle;
						fill: currentColor;
					}
					.oushml-icon-right{margin-left: 10px}
					.oushml-icon-left{margin-right: 10px}
					.oushml-icon-top{margin-bottom: 10px;}
					.oushml-icon-bottom{margin-top: 5px}
					.oushml-link-wrapper{cursor: pointer;transition: all 0.5s ease-in-out;}
					.oushml-link-toggle,.ou-more-less-content:not(.oushml-show-shadow):after,
					.oxygen-builder-body .toggle-lesslink{display: none;}

					.oushml-link-text{position:relative}
					.oushml-link-wrapper .ul-anim:before,
					.oushml-link-wrapper .ul-anim:after{
					  content: "";
					  position: absolute;
					  bottom: -10px;
					  height: 5px;
					  margin: 5px 0 0;
					  transition: all 0.2s ease-in-out;
					  transition-duration: 0.75s;
					  
					  background-color: #e69500;
					  z-index: -1;
					}

					.sml-on-hover.ul-anim:before,
					.sml-on-hover.ul-anim:after,
					.oushml-link-wrapper:hover .sml-off-hover.ul-anim:before,
					.oushml-link-wrapper:hover .sml-off-hover.ul-anim:after {
					  width: 0px;
					  opacity: 0;
					}

					.oushml-link-wrapper .ul-anim.underline_lr:before,
					.oushml-link-wrapper .ul-anim.underline_lr:after {
					  left: 0;
					}

					.oushml-link-wrapper .ul-anim.underline_rl:before,
					.oushml-link-wrapper .ul-anim.underline_rl:after {
					  right: 0;
					}

					.oushml-link-wrapper .ul-anim.underline_outwards:before {
					  left: 50%;
					}

					.oushml-link-wrapper .ul-anim.underline_outwards:after {
					  right: 50%;
					}

					.oushml-link-wrapper .ul-anim.underline_inwards:before {
					  left: 0;
					}

					.oushml-link-wrapper .ul-anim.underline_inwards:after {
					  right: 0;
					}

					.oushml-link-wrapper:hover .sml-on-hover.ul-anim:before,
					.oushml-link-wrapper:hover .sml-on-hover.ul-anim:after,
					.sml-off-hover.ul-anim:before,
					.sml-off-hover.ul-anim:after {
					  width: 100%;
					  opacity: 1;
					}

					.oushml-link-wrapper:hover .sml-on-hover.ul-anim.underline_outwards:before,
					.oushml-link-wrapper:hover .sml-on-hover.ul-anim.underline_outwards:after,
					.oushml-link-wrapper:hover .sml-on-hover.ul-anim.underline_inwards:before,
					.oushml-link-wrapper:hover .sml-on-hover.ul-anim.underline_inwards:after,
					.sml-off-hover.ul-anim.underline_outwards:before,
					.sml-off-hover.ul-anim.underline_outwards:after,
					.sml-off-hover.ul-anim.underline_inwards:before,
					.sml-off-hover.ul-anim.underline_inwards:after {
					  width: 50%;
					}';
		}

		return $css;
	}
}

new OUShowMoreLess();