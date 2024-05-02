<?php

namespace Oxygen\OxyUltimate;

class OUContentSlider extends \OxyUltimateEl {
	
	public $has_js = true;
	public $css_added = false;
	public $js_added = false;
	private $slider_js_code = '';

	function name() {
		return __( "Content Slider", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_content_slider";
	}

	function oxyu_button_place() {
		return "content";
	}

	function icon() {
		return CT_FW_URI . '/toolbar/UI/oxygen-icons/add-icons/slider.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();

		if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'oucntsld_enqueue_scripts' ) );
		}
	}

	function sliderSource() {
		$this->addCustomControl( 
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">' . __('Click on <span style="color:#ff7171;">Apply Params</span> buttons, if content is not showing properly on builder editor. Autoplay effect is disable on builder editor.') . '</div>', 
			'slider_note'
		)->setParam('heading', 'Note:');

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Builder Editor Mode', "oxy-ultimate"),
			'value' 	=> ['edit' => 'Editing', 'preview' => 'Preview'],
			'slug' 		=> 'oucntsld_builder_preview',
			'default' 	=> 'edit'
		])->setParam('description', __('You would select the Editing option when you will edit the slider content. Click on Apply Params button to see the changes.'));

		$source = $this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Slider Content Source', "oxy-ultimate"),
			'value' 	=> ['repeater' => 'Repeater', 'products-list' => __('Products List'), 'related-products' => __('Related Products'), 'product-categories' => __('Product Categories')],
			'slug' 		=> 'oucntsld_source',
			'default' 	=> 'repeater'
		])->setParam('description', __('Click on Apply Params button after selecting the option. See setup guide at below.'));

		$setupGuide = $this->addControlSection('sld_guide', __('Setup Guide'), 'assets/icon.png', $this );

		$html = 'Currently it is supporting the Repeater, Products List & Product Categories components only. Assume you are wanting to create the products slider for your site. So you would select the "Products List" under Slider Content Source option. Afterthat you would add or drag&drop the Products List component inside the Content Slider component and setup the settings.'; /*<br><br> 

				If you select the "DIV Wrapper" option, you would use the DIV component and add image, button, text, heading etc component as a slider content inside the DIV component. So per DIV component will be treat as per slide item into the Content Slider component';*/

		$setupGuide->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.325; font-size: 13px;">' . $html . '</div>',
			'guide_note'
		)->setParam('heading', 'Instruction:');
	}

	function sliderSettings() {
		$sliderSettings = $this->addControlSection('slider_settings', __('Slider Settings'), 'assets/icon.png', $this );

		$sliderSettings->addCustomControl(
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">' . __('Click on <span style="color:#ff7171;">Apply Params</span> buttons to see the changes on Builder editor.') . '</div>', 
			'slider_settings_note'
		)->setParam('heading', 'Note:');

		$sldType = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Slider Type', 'oxy-ultimate'),
			'slug' 		=> 'oucntsld_sldtype',
			'value' 	=> [
				'carousel'	=> __('Carousel', "oxy-ultimate"),
				//'slideshow' => __('Slideshow', "oxy-ultimate"),
				'coverflow' => __('Coverflow', "oxy-ultimate")
			]
		]);
		//$sldType->setParam('description', __('Carousel or Slideshow would be the better option for Ken Burns effect.', "oxy-ultimate") );

		$animType = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Animation Effect', 'oxy-ultimate'),
			'slug' 		=> 'oucntsld_sldeffect',
			'default' 	=> 'slide',
			'value' 	=> [
				'slide'		=> __('Slide', "oxy-ultimate"),
				'fade' 		=> __('Fade', "oxy-ultimate"),
				'cube' 		=> __('Cube', "oxy-ultimate"),
				//'kenburns' 	=> __('Ken Burns', "oxy-ultimate")
			]
		]);

		$centeredSld = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Centered Slide', 'oxy-ultimate'),
			'slug' 		=> 'oucntsld_centered',
			'default' 	=> 'no',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);

		$sldLoop = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Infinite Loop', 'oxy-ultimate'),
			'slug' 		=> 'oucntsld_loop',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
		//$sldLoop->setParam('description', "When you will use the Slideshow effect, you will enable the Infinite loop.");

		$sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Auto Height', 'oxy-ultimate'),
			'slug' 		=> 'oucntsld_auto_height',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")				
			]
		]);

		$sliderSettings->addOptionControl(
			array(
				'type' 			=> 'slider-measurebox',
				'name' 			=> __('Slider Height', "oxy-ultimate"),
				'slug' 			=> 'oucntsld_sldh',
				'description'	=> __('Click on Apply Params button to see the changes.'),
				'property' 		=> 'height',
				'condition' 	=> 'oucntsld_auto_height=no'
			)
		)
		->setUnits('px', 'px')
		->setRange('0', '1500', '10')
		->setValue('300');

		//* Items per Columns
		$maultirow = $sliderSettings->addControlSection('slidesPerColumn', __('Multi Row Layout'), 'assets/icon.png', $this );

		$maultirow->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Number of slides per column, for multirow layout. slidesPerColumn > 1 is currently not compatible with loop mode (infiniteloop: true)</div>'), 
			'multirow_note'
		)->setParam('heading', 'Note:');

		$sldrowsD = $maultirow->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'rows',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldrowsD->setUnits(' ',' ');
		$sldrowsD->setRange('1', '10', '1');
		$sldrowsD->setValue('1');

		$sldrowTb = $maultirow->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'rows_tablet',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldrowTb->setUnits(' ',' ');
		$sldrowTb->setRange('1', '10', '1');
		$sldrowTb->setValue('1');

		$sldrowsPL = $maultirow->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'rows_landscape',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldrowsPL->setUnits(' ',' ');
		$sldrowsPL->setRange('1', '10', '1');
		$sldrowsPL->setValue('1');

		$sldrowsPT = $maultirow->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'rows_portrait',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldrowsPT->setUnits(' ',' ');
		$sldrowsPT->setRange('1', '10', '1');
		$sldrowsPT->setValue('1');

		//* Items per View
		$itemsPerView = $sliderSettings->addControlSection('itemsPerView', __('Items per View'), 'assets/icon.png', $this );
		
		$itemsPerView->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Click on <span style="color:#ff7171;">Apply Params</span> buttons to see the changes on Builder editor.</div>'), 
			'pv_note'
		)->setParam('heading', 'Note:');

		$sldpvD = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'columns'
		]);
		$sldpvD->setUnits(' ',' ');
		$sldpvD->setRange('1', '10', '1');
		$sldpvD->setValue('4');
		$sldpvD->setParam('ng_change', "iframeScope.rebuildDOM(iframeScope.component.active.id)");

		$sldpvTb = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'columns_tablet'
		]);

		$sldpvTb->setUnits(' ',' ');
		$sldpvTb->setRange('1', '10', '1');
		$sldpvTb->setValue('3');

		$sldpvPL = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'columns_landscape'
		]);

		$sldpvPL->setUnits(' ',' ');
		$sldpvPL->setRange('1', '10', '1');
		$sldpvPL->setValue('2');

		$sldpvPT = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'columns_portrait',		
			"css" 		=> false
		]);

		$sldpvPT->setUnits(' ',' ');
		$sldpvPT->setRange('1', '10', '1');
		$sldpvPT->setValue('1');

		//* Slides to Scroll
		$sldSTS = $sliderSettings->addControlSection('sld_scroll', __('Slides to Scroll'), 'assets/icon.png', $this );

		$sldSTS->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Click on <span style="color:#ff7171;">Apply Params</span> buttons to see the changes on Builder editor.</div>'), 
			'sldSTS_note'
		)->setParam('heading', 'Note:');

		$sldSTSD = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll',		
			"css" 		=> false
		]);

		$sldSTSD->setUnits(' ',' ');
		$sldSTSD->setRange('1', '10', '1');
		$sldSTSD->setValue('1');
		$sldSTSD->setParam('description',__('Set numbers of slides to move at a time.', "oxy-ultimate"));

		$sldSTSTB = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll_tablet',		
			"css" 		=> false
		]);

		$sldSTSTB->setUnits(' ',' ');
		$sldSTSTB->setRange('1', '10', '1');
		$sldSTSTB->setValue('1');

		$sldSTSPL = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll_landscape'
		]);

		$sldSTSPL->setUnits(' ',' ');
		$sldSTSPL->setRange('1', '10', '1');
		$sldSTSPL->setValue('1');

		$sldSTSPT = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll_portrait'
		]);

		$sldSTSPT->setUnits(' ',' ');
		$sldSTSPT->setRange('1', '10', '1');
		$sldSTSPT->setValue('1');

		//* Spacing
		$sldSP = $sliderSettings->addControlSection('sld_spacing', __('Gap'), 'assets/icon.png', $this );
		$sldSP->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Click on <span style="color:#ff7171;">Apply Params</span> buttons to see the changes on Builder editor.</div>'), 
			'slider_settings_note'
		)->setParam('heading', 'Note:');

		$sldgap = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'oucntsld_sldgap'
		]);

		$sldgap->setUnits('px','px');
		$sldgap->setRange('5', '50', '5');
		$sldgap->setValue('15');

		$sldgapTB = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'oucntsld_sldgap_tablet'
		]);

		$sldgapTB->setUnits('px','px');
		$sldgapTB->setRange('5', '50', '5');
		$sldgapTB->setValue('15');

		$sldgapPL = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'oucntsld_sldgap_landscape'
		]);

		$sldgapPL->setUnits('px','px');
		$sldgapPL->setRange('5', '50', '5');
		$sldgapPL->setValue('15');

		$sldgapPT = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'oucntsld_sldgap_portrait'
		]);

		$sldgapPT->setUnits('px','px');
		$sldgapPT->setRange('5', '50', '5');
		$sldgapPT->setValue('15');

		$this->settingsSlide( $sliderSettings );
	}

	function settingsSlide( $controlObj ) {
		$slideSettings = $controlObj->addControlSection('slide_settings', __('Transition'), 'assets/icon.png', $this );

		$slideSettings->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Click on <span style="color:#ff7171;">Apply Params</span> buttons to see the changes on Builder editor. Autoplay option is disabled for Builder editor.</div>'), 
			'speed_note'
		)->setParam('heading', 'Note:');

		$transitionSpeed = $slideSettings->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Speed', "oxy-ultimate"),
			'slug' 		=> 'transition_speed'
		]);

		$transitionSpeed->setUnits('ms','ms');
		$transitionSpeed->setRange('1000', '20000', '500');
		$transitionSpeed->setValue('1000');
		$transitionSpeed->setParam('description', __( 'Value unit for form field of time in mili seconds. Such as: "1000 ms"', 'oxy-ultimate' ) );

		$autoPlay = $slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Auto Play', 'oxy-ultimate'),
			'slug' 		=> 'autoplay',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);

		$autoPlaySpeed = $slideSettings->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Auto Play Speed', "oxy-ultimate"),
			'slug' 		=> 'autoplay_speed'
		]);

		$autoPlaySpeed->setUnits('ms','ms');
		$autoPlaySpeed->setRange('1000', '20000', '500');
		$autoPlaySpeed->setValue('5000');
		$autoPlaySpeed->setParam('description', __( 'Value unit for form field of time in mili seconds. Such as: "1000 ms"', 'oxy-ultimate' ) );

		$slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Pause on Hover', 'oxy-ultimate'),
			'slug' 		=> 'pause_on_hover',
			'default' 	=> 'no',
			'value' 	=> [
				'no' 		=> __('No', "oxy-ultimate"),
				'yes'		=> __('Yes', "oxy-ultimate")				
			]
		]);

		$slideSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Pause on Interaction', 'oxy-ultimate'),
			'slug' 		=> 'pause_on_interaction',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
	}

	function settingsNavigationArrows() {
		$arrow = $this->addControlSection('arrow_style', __('Navigation Arrow'), 'assets/icon.png', $this );

		$arrow->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Click on <span style="color:#ff7171;">Apply Params</span> buttons to see the changes on Builder editor.</div>'), 
			'arrow_note'
		)->setParam('heading', 'Note:');

		$navArrow = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show Navigation Arrows', 'oxy-ultimate'),
			'slug' 		=> 'slider_navigation',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);

		$arrowOnHover = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show on Hover', 'oxy-ultimate'),
			'slug' 		=> 'slider_navapr',
			'default' 	=> 'default',
			'value' 	=> [
				'no'		=> __('No', "oxy-ultimate"),
				'onhover' 	=> __('Yes', "oxy-ultimate")
			]
		]);
		$arrowOnHover->setParam('description', "Preview is disable for builder editor.");
		$arrowOnHover->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_content_slider_slider_navigation']!='no'");

		$mbVisibility = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Hide on Devices', 'oxy-ultimate'),
			'slug' 		=> 'slider_hidemb',
			'default' 	=> 'no',
			'value' 	=> [
				'no'		=> __('No', "oxy-ultimate"),
				'yes' 		=> __('Yes', "oxy-ultimate")
			],
			'condition' => 'slider_navigation=yes'
		]);

		$arrowBreakpoint = $arrow->addOptionControl([
			'type' 		=> 'measurebox',
			'name' 		=> __('Breakpoint'),
			'slug' 		=> 'arrow_rsp_breakpoint',
			'condition' => 'slider_hidemb=yes'
		]);
		$arrowBreakpoint->setUnits('px', 'px');
		$arrowBreakpoint->setDefaultValue(680);
		$arrowBreakpoint->setParam('description', 'Default breakpoint value is 680px.');

		$icon = $arrow->addControlSection('arrow_icon', __('Icon'), 'assets/icon.png', $this );
		$leftArrow = $icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Left Arrow', "oxy-ultimate"),
				"slug" 			=> 'arrow_left'
			)
		);

		$rightArrow= $icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Right Arrow', "oxy-ultimate"),
				"slug" 			=> 'arrow_right'
			)
		);

		$pclr = $arrow->addControlSection('arrow_pclr', __('Color & Size'), 'assets/icon.png', $this );

		$pclr->addPreset(
			"padding",
			"arrow_padding",
			__("Padding"),
			'.ou-swiper-button'
		)->whiteList();

		$pclr->addStyleControls([
			[
				"name" 			=> __('Size', "oxy-ultimate"),
				"slug" 			=> "arrow_fs",
				"selector" 		=> '.ou-swiper-button svg',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height',
				"unit" 			=> 'px'
			],
			[
				'selector' 		=> '.ou-swiper-button',
				'property' 		=> 'background-color',
				'slug' 			=> 'arrow_bgc'
			],
			[
				'name' 			=> _('Hover Background Color'),
				'selector' 		=> '.ou-swiper-button:hover',
				'property' 		=> 'background-color',
				'slug' 			=> 'arrow_bghc'
			],
			[
				'selector' 		=> '.ou-swiper-button svg',
				'property' 		=> 'color',
				'slug' 			=> 'arrow_clr'
			],
			[
				'name' 			=> _('Hover Color'),
				'selector' 		=> '.ou-swiper-button:hover svg',
				'property' 		=> 'color',
				'slug' 			=> 'arrow_hclr'
			]
		]);

		$arrowPos = $arrow->addControlSection('arrow_pos', __('Position'), 'assets/icon.png', $this );

		$arrowPos->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on the Apply Params button, if position value is not working properly.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$gapAboveImage = $arrowPos->addStyleControl([
			'name' 		=> __('Gap Above Images'),
			'selector' 	=> '.ou-content-slider-wrapper',
			'slug' 		=> 'arwbtn_gaptop',
			'property' 	=> 'padding-top',
			'control_type' => 'slider-measurebox'
		]);
		$gapAboveImage->setParam('description', 'Add some extra white spaces above the images if you put the arrows outside of the slider wrapper(at top).');
		$gapAboveImage->setRange(0,100,10);
		$gapAboveImage->setUnits('px', 'px,%,em');

		$gapBelowImage = $arrowPos->addStyleControl([
			'name' 		=> __('Gap Below Images'),
			'selector' 	=> '.ou-content-slider-wrapper',
			'slug' 		=> 'arwbtn_gapbtm',
			'property' 	=> 'padding-bottom',
			'control_type' => 'slider-measurebox'
		]);
		$gapBelowImage->setParam('description', 'Add some extra white spaces below the images if you put the arrows outside of the slider wrapper(at bottom).');
		$gapBelowImage->setRange(0,100,10);
		$gapBelowImage->setUnits('px', 'px,%,em');

		// Previous Arrow button
		$arrowPos->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7;line-height: 1.3;font-size:12px">Bottom settings are for previous arrow button.</div>'), 
			'arrow_description'
		)->setParam('heading',__('Previous Arrow'));

		$prevPosTop = $arrowPos->addStyleControl([
			'name' 		=> __('Top'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_top',
			'property' 	=> 'top'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$prevPosBottom = $arrowPos->addStyleControl([
			'name' 		=> __('Bottom'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_btm',
			'property' 	=> 'bottom'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);

		$prevPosLeft = $arrowPos->addStyleControl([
			'name' 		=> __('Left'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_left',
			'property' 	=> 'left'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$prevPosRight = $arrowPos->addStyleControl([
			'name' 		=> __('Right'),
			'selector' 	=> '.ou-swiper-button-prev',
			'slug' 		=> 'prevbtn_right',
			'property' 	=> 'right'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);


		// Next Arrow button
		$arrowPos->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7;line-height: 1.3;font-size:12px">Bottom settings are for next arrow button.</div>'), 
			'arrow_description'
		)->setParam('heading',__('Next Arrow'));
		
		$nextPosTop = $arrowPos->addStyleControl([
			'name' 		=> __('Top'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_top',
			'property' 	=> 'top'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$nextPosBottom = $arrowPos->addStyleControl([
			'name' 		=> __('Bottom'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_btm',
			'property' 	=> 'bottom'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);

		$nextPosLeft = $arrowPos->addStyleControl([
			'name' 		=> __('Left'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_left',
			'property' 	=> 'left'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_end', true);

		$nextPosRight = $arrowPos->addStyleControl([
			'name' 		=> __('Right'),
			'selector' 	=> '.ou-swiper-button-next',
			'slug' 		=> 'nextbtn_right',
			'property' 	=> 'right'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em,auto')->setParam('hide_wrapper_start', true);

		$arrow->borderSection(__('Border'), '.ou-swiper-button', $this );
		$arrow->borderSection(__('Hover Border'), '.ou-swiper-button:hover', $this );

		$arrow->boxShadowSection(__('Shadow'), '.ou-swiper-button', $this );
		$arrow->boxShadowSection(__('Hover Shadow'), '.ou-swiper-button:hover', $this );
	}

	function settingsPagination() {
		$pagination = $this->addControlSection('pagination_style', __('Pagination'), 'assets/icon.png', $this );
		$pagination->addCustomControl( __('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> buttons to see the changes on Builder editor.</div>'), 'pg_description' );

		$pagType = $pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Pagination Type', 'oxy-ultimate'),
			'slug' 		=> 'pagination_type',
			'default' 	=> 'bullets',
			'value' 	=> [
				'none'			=> __( 'None', 'oxy-ultimate' ),
				'bullets'       => __( 'Dots', 'oxy-ultimate' ),
				'fraction'		=> __( 'Fraction', 'oxy-ultimate' ),
				'progress'		=> __( 'Progress', 'oxy-ultimate' ),
			]
		]);
		$pagType->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_content_slider_oucntsld_sldtype']!='slideshow'");

		$pagDots = $pagination->addOptionControl([
			'type' 			=> 'radio',
			'name' 			=> __('Dynamic Dots', 'oxy-ultimate'),
			'slug' 			=> 'pg_dyndots',
			'default' 		=> 'no',
			'value' 		=> [
				'no'			=> __( 'Disable', 'oxy-ultimate' ),
				'yes'      		=> __( 'Enable', 'oxy-ultimate' )
			],
			'condition' => 'pagination_type=bullets'
		]);
		$pagDots->setParam('description', __('Good to enable if you use dots pagination with a lot of slides. So it will keep only few bullets visible at the same time.'));

		$pos = $pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Position', 'oxy-ultimate'),
			'slug' 		=> 'pagination_position',
			'default' 	=> 'outside',
			'value' 	=> [
				'outside'	=> __('Below Image', "oxy-ultimate"),
				'inside' 	=> __('Overlay', "oxy-ultimate")
			]
		]);
		$pos->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_content_slider_oucntsld_sldtype']!='slideshow'");

		$pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Hide on Devices', 'oxy-ultimate'),
			'slug' 		=> 'slider_hidepg',
			'default' 	=> 'no',
			'value' 	=> [
				'no'		=> __('No', "oxy-ultimate"),
				'yes' 		=> __('Yes', "oxy-ultimate")
			],
			'condition' => 'pagination_type!=none'
		]);

		$dotsBP = $pagination->addOptionControl([
			'type' 		=> 'measurebox',
			'name' 		=> __('Breakpoint'),
			'slug' 		=> 'pg_rspbp',
			'condition' => 'slider_hidepg=yes'
		]);
		$dotsBP->setUnits('px', 'px');
		$dotsBP->setDefaultValue(680);
		$dotsBP->setParam('description', 'Default breakpoint value is 680px.');

		$pagination->addStyleControls([
			[
				'name' 			=> _('Dots/Fraction Position'),
				'description' 	=> __('It will work when you will choose the position "Below Image".'),
				'selector' 		=> '.slider-pagination-outside',
				'property' 		=> 'padding-bottom',
				'control_type'	=> 'measurebox',
				'value' 		=> '40',
				'default' 		=> '40',
				'unit' 			=> 'px',
				'slug' 			=> 'bullets_pos',
				'condition' 	=> 'pagination_position=outside'
			],
			[
				'selector' 		=> '.swiper-pagination-bullet, .swiper-container-horizontal>.swiper-pagination-progress',
				'property' 		=> 'background-color',
				'slug' 			=> 'pagination_bg_color'
			],
			[
				'name' 			=> _('Active Background Color'),
				'selector' 		=> '.swiper-pagination-bullet:hover, .swiper-pagination-bullet-active, .swiper-pagination-progress .swiper-pagination-progressbar',
				'property' 		=> 'background-color',
				'slug' 			=> 'pagination_bg_hover'
			],
			[
				'name' 			=> _('Dots Opacity'),
				'selector' 		=> '.swiper-pagination-bullet',
				'property' 		=> 'opacity',
				'default' 		=> '0.2',
				'slug' 			=> 'bullets_opacity',
				'condition' 	=> 'pagination_type=bullets'
			],
			[
				'name' 			=> _('Dots Width'),
				'selector' 		=> '.swiper-pagination-bullet',
				'property' 		=> 'width',
				'slug' 			=> 'bullets_width',
				'condition' 	=> 'pagination_type=bullets'
			],
			[
				'name' 			=> _('Active Dots Width'),
				'selector' 		=> '.swiper-pagination-bullet-active',
				'property' 		=> 'width',
				'slug' 			=> 'bullets_awidth',
				'condition' 	=> 'pagination_type=bullets'
			],
			[
				'name' 			=> _('Dots Height'),
				'selector' 		=> '.swiper-pagination-bullet',
				'property' 		=> 'height',
				'slug' 			=> 'bullets_height',
				'condition' 	=> 'pagination_type=bullets'
			],
			[
				'name' 			=> _('Dots Round Corners'),
				'selector' 		=> '.swiper-pagination-bullet',
				'property' 		=> 'border-radius',
				'slug' 			=> 'bullets_brdrad',
				'condition' 	=> 'pagination_type=bullets'
			],
		]);
	}

	function controls() {
		$this->sliderSource();
		$this->sliderSettings();
		$this->settingsNavigationArrows();
		$this->settingsPagination();
	}

	function render($options, $defaults, $content) {
		$uid = str_replace( ".", "", uniqid( 'oucntsld-', true ) );

		$pg = isset( $options['pagination_type'] ) ? $options['pagination_type'] : 'bullets';
		$pg_position = isset( $options['pagination_position'] ) ? $options['pagination_position'] : 'outside';

		$dataAttr = ' data-builder-preview="' . ( isset($options['oucntsld_builder_preview']) ? $options['oucntsld_builder_preview'] : 'edit' ) . '"';
		$dataAttr .= ' data-sldid="' . $uid . '"';
		$dataAttr .= ' data-slider-source="' . ( isset($options['oucntsld_source']) ? $options['oucntsld_source'] : 'repeater' ) . '"';
		$dataAttr .= ' data-slider-type="' . ( isset($options['oucntsld_sldtype']) ? $options['oucntsld_sldtype'] : 'carousel' ) . '"';
		$dataAttr .= ' data-slider-effect="' . ( isset($options['oucntsld_sldeffect']) ? $options['oucntsld_sldeffect'] : 'slide' ) . '"';
		$dataAttr .= ' data-slider-centered="' . ( isset($options['oucntsld_centered']) ? $options['oucntsld_centered'] : 'no' ) . '"';
		$dataAttr .= ' data-slider-autoheight="' . ( ( isset($options['oucntsld_auto_height'] ) && $options['oucntsld_auto_height'] == 'no' ) ? 'no'  : 'yes' ). '"';
		$dataAttr .= ' data-columns="' . ( isset($options['columns']) ? $options['columns'] : 4 ) . '"';
		$dataAttr .= ' data-columns-tablet="' . ( isset($options['columns_tablet']) ? $options['columns_tablet'] : 3 ) . '"';
		$dataAttr .= ' data-columns-landscape="' . ( isset($options['columns_landscape']) ? $options['columns_landscape'] : 2 ) . '"';
		$dataAttr .= ' data-columns-portrait="' . ( isset($options['columns_portrait']) ? $options['columns_portrait'] : 1 ) . '"';	

		$dataAttr .= ' data-rows="' . ( isset($options['rows']) ? absint($options['rows']) : 1 ) . '"';
		$dataAttr .= ' data-rows-tablet="' . ( isset($options['rows_tablet']) ? absint($options['rows_tablet']) : 1 ) . '"';
		$dataAttr .= ' data-rows-landscape="' . ( isset($options['rows_landscape']) ? absint($options['rows_landscape']) : 1 ) . '"';
		$dataAttr .= ' data-rows-portrait="' . ( isset($options['rows_portrait']) ? absint($options['rows_portrait']) : 1 ) . '"';	

		$dataAttr .= ' data-sts="' . ( isset($options['slides_to_scroll']) ? absint($options['slides_to_scroll']) : 1 ) . '"';
		$dataAttr .= ' data-sts-tablet="' . ( isset($options['slides_to_scroll_tablet']) ? absint($options['slides_to_scroll_tablet']) : 1 ) . '"';
		$dataAttr .= ' data-sts-landscape="' . ( isset($options['slides_to_scroll_landscape']) ? absint($options['slides_to_scroll_landscape']) : 1 ) . '"';
		$dataAttr .= ' data-sts-portrait="' . ( isset($options['slides_to_scroll_portrait']) ? absint($options['slides_to_scroll_portrait']) : 1 ) . '"';
		$dataAttr .= ' data-spb="' . ( isset($options['oucntsld_sldgap']) ? absint($options['oucntsld_sldgap']) : 15 ) . '"';
		$dataAttr .= ' data-spb-tablet="' . ( isset($options['oucntsld_sldgap_tablet']) ? absint($options['oucntsld_sldgap_tablet']) : 15 ) . '"';
		$dataAttr .= ' data-spb-landscape="' . ( isset($options['oucntsld_sldgap_landscape']) ? absint($options['oucntsld_sldgap_landscape']) : 15 ) . '"';
		$dataAttr .= ' data-spb-portrait="' . ( isset($options['oucntsld_sldgap_portrait']) ? absint($options['oucntsld_sldgap_portrait']) : 15 ) . '"';
		$dataAttr .= ' data-pagination="' . $pg . '"';	
		$dataAttr .= ' data-dynamicBullets="' . ( ( isset($options['pg_dyndots']) && $options['pg_dyndots'] == 'yes' ) ? 'yes' : 'no' ) . '"';
		$dataAttr .= ' data-loop="' . ( $options['oucntsld_loop'] == 'no' ? 'no' : 'yes' ) . '"';
		$dataAttr .= ' data-pauseonhover="' . ( $options['pause_on_hover'] == 'no' ? 'no' : 'yes' ) . '"';
		$dataAttr .= ' data-poninteraction="' . ( $options['pause_on_interaction'] == 'no' ? 'no' : 'yes' ) . '"';
		$dataAttr .= ' data-speed="' . $options['transition_speed'] . '"';
		$dataAttr .= ' data-autoplay="' . ( $options['autoplay'] == 'no' ? 'no' : 'yes' ) . '"';
		$dataAttr .= ' data-autoplay-speed="' . ( ( $options['autoplay'] == 'yes' && isset( $options['autoplay_speed'] ) ) ? $options['autoplay_speed'] : 'no' ) . '"';

		echo '<div class="ou-content-slider-wrapper ' . $uid .' slider-pagination-'.$pg_position.' swiper-container oxy-inner-content"'.$dataAttr.'>';

		if( $content ) {
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
		}

		if( $pg != 'none' ) { echo '<div class="swiper-pagination"></div>'; }

		echo '</div>';

		if( isset( $options['slider_navigation'] ) && $options['slider_navigation'] == 'yes' ) { global $oxygen_svg_icons_to_load; ?>
			<?php if( isset($options['arrow_left']) ) { $oxygen_svg_icons_to_load[] = $options['arrow_left']; ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-prev">
					<svg><use xlink:href="#<?php echo $options['arrow_left'];?>"></use></svg>
				</div>
			<?php } else { ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-prev">
					<svg><use xlink:href="#Lineariconsicon-chevron-left"></use></svg>
				</div>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"><defs><symbol id="Lineariconsicon-chevron-left" viewBox="0 0 20 20"><title>chevron-left</title><path class="path1" d="M14 20c0.128 0 0.256-0.049 0.354-0.146 0.195-0.195 0.195-0.512 0-0.707l-8.646-8.646 8.646-8.646c0.195-0.195 0.195-0.512 0-0.707s-0.512-0.195-0.707 0l-9 9c-0.195 0.195-0.195 0.512 0 0.707l9 9c0.098 0.098 0.226 0.146 0.354 0.146z"/></symbol></defs></svg>
			<?php } if( isset($options['arrow_right']) ) { $oxygen_svg_icons_to_load[] = $options['arrow_right']; ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-next"><svg><use xlink:href="#<?php echo $options['arrow_right'];?>"></use></svg></div>
			<?php } else { ?>
				<div class="ou-swiper-button<?php if( isset($options['slider_navapr']) && 'onhover' == $options['slider_navapr']){?> show-on-hover<?php }?> ou-swiper-button-next"><svg><use xlink:href="#Lineariconsicon-chevron-right"></use></svg></div>

				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"><defs><symbol id="Lineariconsicon-chevron-right" viewBox="0 0 20 20"><title>chevron-right</title><path class="path1" d="M5 20c-0.128 0-0.256-0.049-0.354-0.146-0.195-0.195-0.195-0.512 0-0.707l8.646-8.646-8.646-8.646c-0.195-0.195-0.195-0.512 0-0.707s0.512-0.195 0.707 0l9 9c0.195 0.195 0.195 0.512 0 0.707l-9 9c-0.098 0.098-0.226 0.146-0.354 0.146z"/></symbol></defs></svg>
			<?php } ?>
		<?php }

		if ( isset( $_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') )
		{
			if( isset($options['oucntsld_builder_preview']) && $options['oucntsld_builder_preview'] == 'preview' ) {
				$slider_js_code = "
				jQuery(document).ready(function($){
					if( typeof sldTimeout != 'undefined' )
						clearTimeout( sldTimeout );

					var sldTimeout = setTimeout(function(){
						$('.ou-content-slider-wrapper').each(function(){
							if( $(this).find('.oxy-dynamic-list').length ) {
								$(this).find('.oxy-dynamic-list').addClass('swiper-wrapper');
								if( $(this).find('.swiper-wrapper > .ct-div-block').length ) {
									$(this).find('.swiper-wrapper > .ct-div-block').addClass('swiper-slide');
								}
							}

							if( $(this).find('.oxy-woo-products').length ) {
								$(this).removeClass('swiper-container');
								$(this).removeClass($(this).attr('data-sldid'));
								$(this).find('.woocommerce-notices-wrapper,form,.woocommerce-result-count,.woocommerce-pagination').remove();
								$(this).find('.oxy-woo-products div.woocommerce').addClass('swiper-container ' + $(this).attr('data-sldid'));
								$(this).find('.oxy-woo-products ul.products').addClass('swiper-wrapper');
								$(this).find('.oxy-woo-products ul.products li').addClass('swiper-slide');

								if( $(this).hasClass('slider-pagination-outside') ) {
									$(this).find('.oxy-woo-products div.woocommerce').addClass('slider-pagination-outside');
								}

								if( $(this).find('.swiper-pagination').length ) {
									$(this).find('.swiper-pagination').appendTo( $(this).find('.oxy-woo-products div.woocommerce') );
								}
							}

							if( $(this).find('.oxy-related-products').length ) {
								$(this).removeClass('swiper-container');
								$(this).removeClass($(this).attr('data-sldid'));
								$(this).find('.oxy-related-products section.related > h2').remove();
								$(this).find('.oxy-related-products section.related').addClass('swiper-container ' + $(this).attr('data-sldid'));
								$(this).find('.oxy-related-products ul.products').addClass('swiper-wrapper');
								$(this).find('.oxy-related-products ul.products li').addClass('swiper-slide');

								if( $(this).hasClass('slider-pagination-outside') ) {
									$(this).find('.oxy-related-products div.woocommerce').addClass('slider-pagination-outside');
								}

								if( $(this).find('.swiper-pagination').length ) {
									$(this).find('.swiper-pagination').appendTo( $(this).find('.oxy-related-products div.woocommerce') );
								}
							}

							if( $(this).find('.oxy-woo-product-categories').length ) {							
								$(this).find('.oxy-woo-product-categories div.woocommerce').addClass('swiper-container ' + $(this).attr('data-sldid'));
								$(this).find('.oxy-woo-product-categories ul.products').addClass('swiper-wrapper');
								$(this).find('.oxy-woo-product-categories ul.products li').addClass('swiper-slide');

								if( $(this).hasClass('slider-pagination-outside') ) {
									$(this).find('.oxy-woo-product-categories div.woocommerce').addClass('slider-pagination-outside');
								}

								if( $(this).find('.swiper-pagination').length ) {
									$(this).find('.swiper-pagination').appendTo( $(this).find('.oxy-woo-product-categories div.woocommerce') );
								}
							}

							var settings = {
								id: $(this).attr('data-sldid'),
								type: $(this).attr('data-slider-type'),
								effect: $(this).attr('data-slider-effect'),
								initialSlide: 0,
								spaceBetween: {
									desktop: $(this).attr('data-spb'),
									tablet: $(this).attr('data-spb-tablet'),
									landscape: $(this).attr('data-spb-landscape'),
									portrait: $(this).attr('data-spb-portrait')
								},
								slidesPerView: {
									desktop: $(this).attr('data-columns'),
									tablet: $(this).attr('data-columns-tablet'),
									landscape: $(this).attr('data-columns-landscape'),
									portrait: $(this).attr('data-columns-portrait')
								},
								slidesPerColumn: {
									desktop: $(this).attr('data-rows'),
									tablet: $(this).attr('data-rows-tablet'),
									landscape: $(this).attr('data-rows-landscape'),
									portrait: $(this).attr('data-rows-portrait')
								},
								slidesToScroll: {
									desktop: $(this).attr('data-sts'),
									tablet: $(this).attr('data-sts-tablet'),
									landscape: $(this).attr('data-sts-landscape'),
									portrait: $(this).attr('data-sts-portrait')
								},
								isBuilderActive: true,
								builderPreview: $(this).attr('data-builder-preview'),
								pagination: $(this).attr('data-pagination'),
								autoHeight: ( $(this).attr('data-slider-autoheight') == 'yes' ? true : false),
								dynamicBullets: ( $(this).attr('data-dynamicBullets') == 'yes' ? true : false ),
								centered: ( $(this).attr('data-slider-centered') == 'yes' ? true : false),
								loop: ( ( $(this).attr('data-loop') == 'yes' && parseInt( $(this).attr('data-rows') ) <= 1 ) ? true : false ),
								pause_on_hover: ( $(this).attr('data-pauseonhover') == 'yes' ? true : false ),
								pause_on_interaction: ( $(this).attr('data-poninteraction') == 'yes' ? true : false ),
								autoplay: ( $(this).attr('data-autoplay') == 'yes' ? true : false ),
								autoplay_speed: ( ( $(this).attr('data-autoplay') == 'yes' && parseInt( $(this).attr('data-autoplay-speed') ) ) ? parseInt( $(this).attr('data-autoplay-speed') ) : false ),
								speed: parseInt( $(this).attr('data-speed') ),
								breakpoint: {
									desktop: 993,
									tablet: 768,
									landscape: 640,
									portrait: 100
								}
							};

							sldObj = $(this).attr('data-sldid');
							sldObj = new OUContentSlider(settings);
						});
					}, 5500 );
				});";

				$this->El->builderInlineJS( $slider_js_code );
			}
		} else {
			$slider_js_code = "
			jQuery(document).ready(function($){
				var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
				if( is_safari ) {
					$('.ou-content-slider-wrapper .woocommerce').addClass('fix-safari-bug');
				}
				
				$('.ou-content-slider-wrapper').each(function(){
					if( $(this).find('.oxy-dynamic-list').length ) {
						$(this).find('.oxy-dynamic-list').addClass('swiper-wrapper');
						if( $(this).find('.swiper-wrapper > .ct-div-block').length ) {
							$(this).find('.swiper-wrapper > .ct-div-block').addClass('swiper-slide');
						}

						$(this).find('.oxy-repeater-pages-wrap').remove();
					}

					if( $(this).find('.oxy-woo-products').length ) {
						$(this).removeClass('swiper-container');
						$(this).removeClass($(this).attr('data-sldid'));
						$(this).find('.woocommerce-notices-wrapper,form,.woocommerce-result-count,.woocommerce-pagination').remove();
						$(this).find('.oxy-woo-products div.woocommerce').addClass('swiper-container ' + $(this).attr('data-sldid'));
						$(this).find('.oxy-woo-products ul.products').addClass('swiper-wrapper');
						$(this).find('.oxy-woo-products ul.products li').addClass('swiper-slide');

						if( $(this).hasClass('slider-pagination-outside') ) {
							$(this).find('.oxy-woo-products div.woocommerce').addClass('slider-pagination-outside');
						}

						if( $(this).find('.swiper-pagination').length ) {
							$(this).find('.swiper-pagination').appendTo( $(this).find('.oxy-woo-products div.woocommerce') );
						}
					}

					if( $(this).find('.oxy-related-products').length ) {
						$(this).removeClass('swiper-container');
						$(this).removeClass($(this).attr('data-sldid'));
						$(this).find('.oxy-related-products section.related > h2').remove();
						$(this).find('.oxy-related-products section.related').addClass('swiper-container ' + $(this).attr('data-sldid'));
						$(this).find('.oxy-related-products ul.products').addClass('swiper-wrapper');
						$(this).find('.oxy-related-products ul.products li').addClass('swiper-slide');

						if( $(this).hasClass('slider-pagination-outside') ) {
							$(this).find('.oxy-related-products div.woocommerce').addClass('slider-pagination-outside');
						}

						if( $(this).find('.swiper-pagination').length ) {
							$(this).find('.swiper-pagination').appendTo( $(this).find('.oxy-related-products div.woocommerce') );
						}
					}

					if( $(this).find('.oxy-woo-product-categories').length ) {						
						$(this).find('.oxy-woo-product-categories div.woocommerce').addClass('swiper-container ' + $(this).attr('data-sldid'));
						$(this).find('.oxy-woo-product-categories ul.products').addClass('swiper-wrapper');
						$(this).find('.oxy-woo-product-categories ul.products li').addClass('swiper-slide');

						if( $(this).hasClass('slider-pagination-outside') ) {
							$(this).find('.oxy-woo-product-categories div.woocommerce').addClass('slider-pagination-outside');
						}

						if( $(this).find('.swiper-pagination').length ) {
							$(this).find('.swiper-pagination').appendTo( $(this).find('.oxy-woo-product-categories div.woocommerce') );
						}
					}

					var settings = {
						id: $(this).attr('data-sldid'),
						type: $(this).attr('data-slider-type'),
						effect: $(this).attr('data-slider-effect'),
						initialSlide: 0,
						spaceBetween: {
							desktop: $(this).attr('data-spb'),
							tablet: $(this).attr('data-spb-tablet'),
							landscape: $(this).attr('data-spb-landscape'),
							portrait: $(this).attr('data-spb-portrait')
						},
						slidesPerView: {
							desktop: $(this).attr('data-columns'),
							tablet: $(this).attr('data-columns-tablet'),
							landscape: $(this).attr('data-columns-landscape'),
							portrait: $(this).attr('data-columns-portrait')
						},
						slidesPerColumn: {
							desktop: $(this).attr('data-rows'),
							tablet: $(this).attr('data-rows-tablet'),
							landscape: $(this).attr('data-rows-landscape'),
							portrait: $(this).attr('data-rows-portrait')
						},
						slidesToScroll: {
							desktop: $(this).attr('data-sts'),
							tablet: $(this).attr('data-sts-tablet'),
							landscape: $(this).attr('data-sts-landscape'),
							portrait: $(this).attr('data-sts-portrait')
						},
						isBuilderActive: false,
						pagination: $(this).attr('data-pagination'),
						autoHeight: ( $(this).attr('data-slider-autoheight') == 'yes' ? true : false),
						dynamicBullets: ( $(this).attr('data-dynamicBullets') == 'yes' ? true : false ),
						centered: ( $(this).attr('data-slider-centered') == 'yes' ? true : false),
						loop: ( ( $(this).attr('data-loop') == 'yes' && parseInt( $(this).attr('data-rows') ) <= 1 ) ? true : false ),
						pause_on_hover: ( $(this).attr('data-pauseonhover') == 'yes' ? true : false ),
						pause_on_interaction: ( $(this).attr('data-poninteraction') == 'yes' ? true : false ),
						autoplay: ( $(this).attr('data-autoplay') == 'yes' ? true : false ),
							autoplay_speed: ( ( $(this).attr('data-autoplay') == 'yes' && parseInt( $(this).attr('data-autoplay-speed') ) ) ? parseInt( $(this).attr('data-autoplay-speed') ) : false ),
						speed: parseInt( $(this).attr('data-speed') ),
						breakpoint: {
							desktop: 993,
							tablet: 768,
							landscape: 640,
							portrait: 100
						}
					};

					sldObj = $(this).attr('data-sldid');
					sldObj = new OUContentSlider(settings);

					function ouUpdateContentSlider() {
						setTimeout(function() {
							if ( 'number' !== typeof sldObj.swipers.main.length ) {
								sldObj.swipers.main.update();
							} else {
								sldObj.swipers.main.forEach(function(item) {
									if ( 'undefined' !== typeof item ) {
										item.update();
									}
								});
							}
						}, 10);
					}

					$(document).on('ou-accordion-slide-complete', function(e) {
						if ( $(e.target).find('.{$uid}').length > 0 ) {
							ouUpdateContentSlider();
						}
					});

					if ( $('.oxy-tab').length > 0 ) {
						$('.oxy-tab').on('click', function(e) {
							setTimeout(function(){ ouUpdateContentSlider(); }, 5 );
						});
					}

					if( $('.ct-modal').length > 0 ) {
						$('.oxy-modal-backdrop').each(function(){
							var triggerSelector = $(this).attr('data-trigger-selector');
							$(triggerSelector).on('click', function(e) {
								setTimeout(function(){ ouUpdateContentSlider(); }, 5 );
							});
						});
					}
				});
			});";

			if( ! $this->js_added ) {
				add_action( 'wp_footer', array( $this, 'oucntsld_enqueue_scripts' ) );
				$this->js_added = true;
			}

			$this->El->footerJS( $slider_js_code );
		}
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '
					.oxy-ou-content-slider {
						display: flex;
						flex-direction: column;
						min-height: 80px;
					}
					.oxy-ou-content-slider,
					.ou-content-slider-wrapper {
						position: relative;
						width: 100%;
					}
					.oxy-ou-content-slider .oxy-dynamic-list,
					.oxy-ou-content-slider .swiper-wrapper:not(.ct-section):not(.oxy-easy-posts), 
					.oxy-ou-content-slider .swiper-wrapper.oxy-easy-posts .oxy-posts, 
					.oxy-ou-content-slider .swiper-wrapper.ct-section .ct-section-inner-wrap {
						display: flex!important;
						/*flex-direction: row!important;*/
						grid-column-gap: unset!important;
						grid-row-gap: unset!important;
						grid-auto-rows: auto!important;
					}
					.oxy-ou-content-slider .slider-pagination-outside {
						padding-bottom: 40px;
					}
					.oxy-ou-content-slider .ou-swiper-button {
						background-image: none;
						width: auto;
						height: auto;
						line-height: 1;
						position: absolute;
						display: -webkit-inline-box;
						display: -webkit-inline-flex;
						display: -ms-inline-flexbox;
						display: inline-flex;
						z-index: 18;
						cursor: pointer;
						font-size: 25px;
						top: 50%;
						-webkit-transform: translateY(-50%);
						-ms-transform: translateY(-50%);
						transform: translateY(-50%);
					}
					.oxy-ou-content-slider .ou-swiper-button:focus,
					.oxy-ou-content-slider .ou-swiper-button:active {
						outline: 0;
					}
					.oxy-ou-content-slider .ou-swiper-button-prev {
						left: 10px;
					}
					.oxy-ou-content-slider .ou-swiper-button-next {
						right: 10px;
					}
					.oxy-ou-content-slider .ou-swiper-button svg {
						width: 20px;
						height: 20px;
					}
					.oxy-ou-content-slider .ou-swiper-button svg,
					.oxy-ou-content-slider .ou-swiper-button:hover svg {
						fill: currentColor;
					}
					.ou-content-slider-wrapper .oxy-repeater-pages-wrap,
					.ou-content-slider-wrapper .woocommerce-notices-wrapper,
					.ou-content-slider-wrapper .woocommerce-result-count,
					.ou-content-slider-wrapper form.woocommerce-ordering,
					.ou-content-slider-wrapper .page-title,
					.ou-content-slider-wrapper .woocommerce-pagination {
						display: none;
					}
					.ou-content-slider-wrapper .woocommerce .products ul,
					.ou-content-slider-wrapper .woocommerce ul.products,
					.ou-content-slider-wrapper .related ul.products {
						align-items: stretch;
						margin: 0;
						padding: 0!important;
					}
					.oxy-ou-content-slider .swiper-pagination-bullets .swiper-pagination-bullet-active {
					    opacity: 1!important;
					}
					';

			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();
		$autoHeight = isset( $original[$prefix .'_oucntsld_auto_height'] ) ? $original[$prefix . '_oucntsld_auto_height'] : "yes";
		if( $autoHeight == 'no') {
			$css .= $selector . ' .ou-content-slider-wrapper, ' . $selector . ' .swiper-slide{height: '. ( isset($original[$prefix .'_oucntsld_sldh']) ? absint($original[$prefix .'_oucntsld_sldh']) : 300) . 'px;}';
		}

		$Navigation = isset( $original[$prefix . '_slider_navigation'] ) ? $original[$prefix . '_slider_navigation'] : 'yes';

		if( $Navigation == 'yes' )
		{
			$prevPos = $nextPos = '';
			if( isset($original[$prefix . '_prevbtn_top-unit']) && $original[$prefix . '_prevbtn_top-unit'] == 'auto' )
			{
				$prevPos .= 'top: auto;';
			}
			if( isset($original[$prefix . '_prevbtn_btm-unit']) && $original[$prefix . '_prevbtn_btm-unit'] == 'auto' )
			{
				$prevPos .= 'bottom: auto;';
			}
			if( isset($original[$prefix . '_prevbtn_left-unit']) && $original[$prefix . '_prevbtn_left-unit'] == 'auto' )
			{
				$prevPos .= 'left: auto;';
			}
			if( isset($original[$prefix . '_prevbtn_right-unit']) && $original[$prefix . '_prevbtn_right-unit'] == 'auto' )
			{
				$prevPos .= 'right: auto;';
			}

			if( isset($original[$prefix . '_nextbtn_top-unit']) && $original[$prefix . '_nextbtn_top-unit'] == 'auto' )
			{
				$nextPos .= 'top: auto;';
			}
			if( isset($original[$prefix . '_nextbtn_btm-unit']) && $original[$prefix . '_nextbtn_btm-unit'] == 'auto' )
			{
				$nextPos .= 'bottom: auto;';
			}
			if( isset($original[$prefix . '_nextbtn_left-unit']) && $original[$prefix . '_nextbtn_left-unit'] == 'auto' )
			{
				$nextPos .= 'left: auto;';
			}
			if( isset($original[$prefix . '_nextbtn_right-unit']) && $original[$prefix . '_nextbtn_right-unit'] == 'auto' )
			{
				$nextPos .= 'right: auto;';
			}
			$css .= $selector . ' .ou-swiper-button-prev{'. $prevPos .'}';
			$css .= $selector . ' .ou-swiper-button-next{'. $nextPos .'}';

			if( isset($original[$prefix . '_slider_hidemb']) && $original[$prefix . '_slider_hidemb'] == 'yes' ) {		
				$arrowBP = isset($original[$prefix . '_arrow_rsp_breakpoint']) ? $original[$prefix . '_arrow_rsp_breakpoint'] : 680;
				$css .= '@media only screen and (max-width: '. absint($arrowBP) .'px){' . $selector . ' .ou-swiper-button{display:none;}}';
			}

			if( isset($original[$prefix . '_slider_navapr']) && $original[$prefix . '_slider_navapr'] == 'onhover' )
			{
				$css .= 'body:not(.oxygen-builder-body) ' . $selector . ' .ou-swiper-button.show-on-hover{display:none;}';
				$css .= 'body:not(.oxygen-builder-body) ' . $selector . ':hover .ou-swiper-button.show-on-hover{display:inline-flex;}';
			}
		}

		if( isset($original[$prefix . '_pagination_type']) && $original[$prefix . '_pagination_type'] !== 'none' )
		{
			if( isset($original[$prefix . '_slider_hidepg']) && $original[$prefix . '_slider_hidepg'] == 'yes' ) {		
				$pgBP = isset($original[$prefix . '_pg_rspbp']) ? $original[$prefix . '_pg_rspbp'] : 680;
				$css .= '@media only screen and (max-width: '. absint($pgBP) .'px){' . $selector . ' .swiper-pagination{display:none;}}';
			}
		}

		$sldSource = isset($original[$prefix . '_oucntsld_source']) ? $original[$prefix . '_oucntsld_source'] : 'repeater';
		$editMode = isset( $original[$prefix . '_oucntsld_builder_preview'] ) ? $original[$prefix . '_oucntsld_builder_preview'] : 'edit';
		$cols = isset($original[$prefix . '_columns']) ? absint($original[$prefix . '_columns']) : 4;
		$gap = isset($original[$prefix . '_oucntsld_sldgap']) ? absint($original[$prefix . '_oucntsld_sldgap']) : 15;

		if( $editMode == 'edit' && $sldSource == 'repeater' ) {
			$css .= 'body.oxygen-builder-body ' . $selector . ' .oxy-dynamic-list > .ct-div-block{width: calc(100% / ' . $cols .'); padding-right: '. $gap .'px;}';
		}

		if( $sldSource == 'products-list' || $sldSource == 'related-products' || $sldSource == 'product-categories' ) {
			$css .= $selector . ' .woocommerce ul.products li.product, 
					'. $selector . ' .woocommerce-page ul.products li.product, 
					'. $selector . ' .related ul.products li.product {margin-bottom: 0!important;padding: 0!important; flex: 1 0 auto;}';
			$css .= $selector . ' .woocommerce ul.products,' . $selector . ' .related ul.products{flex-wrap: nowrap!important;}';
			$css .= $selector . ' .woocommerce.swiper-container-autoheight ul.products{height: auto!important}';

			$css .= $selector . ' .swiper-container-multirow.woocommerce ul.products{height: 100%!important;}';
			$css .= $selector . ' .swiper-container-multirow.woocommerce ul.products{flex-wrap: wrap!important;}';

			$css .= $selector . ' .woocommerce.fix-safari-bug ul{left: '. $gap .'px;}';

			$tabletGap 		= isset($original[$prefix . '_oucntsld_sldgap_tablet']) ? $original[$prefix . '_oucntsld_sldgap_tablet'] : 15;
			$landscapeGap 	= isset($original[$prefix . '_oucntsld_sldgap_landscape']) ? $original[$prefix . '_oucntsld_sldgap_landscape'] : 15;
			$portraitGap 	= isset($original[$prefix . '_oucntsld_sldgap_portrait']) ? $original[$prefix . '_oucntsld_sldgap_portrait'] : 15;

			$css .= '@media only screen and (max-width: 992px){' . 
						$selector . ' .woocommerce.fix-safari-bug ul{left: '. $tabletGap .'px;}
					}';

			$css .= '@media only screen and (max-width: 769px){' . 
						$selector . ' .woocommerce.fix-safari-bug ul{left: '. $landscapeGap .'px;}
					}';

			$css .= '@media only screen and (max-width: 639px) {' . 
						$selector . ' .woocommerce.fix-safari-bug ul{left: '. $portraitGap .'px;}
					}';

			if( $editMode == 'edit') {
				$css .= 'body.oxygen-builder-body ' . $selector . ' ul.products li.product{flex: 1 0 calc((100% - ' . ( $gap * ($cols - 1) ) . 'px) / ' . $cols . '); margin-left: '. $gap .'px;}';
				$css .= 'body.oxygen-builder-body ' . $selector . ' ul.products li.product:first-child{margin-left: 0;}';
			}
		}

		if( $sldSource == 'repeater' ) {
			$css .= $selector . ' .swiper-container-multirow .swiper-wrapper{height: 100%!important;}';
		}

		return $css;
	}

	function oucntsld_enqueue_scripts() {
		global $ouwoo_constant;

		if( empty( $ouwoo_constant ) || ! $ouwoo_constant['swiper_css'] ) {
			wp_enqueue_style('ou-swiper-style');
			wp_enqueue_script('ou-swiper-script');
		}
		wp_enqueue_script('oucnt-slider-script');
	}

	function enableFullPresets() {
		return true;
	}
}

new OUContentSlider();