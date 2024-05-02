<?php

namespace Oxygen\OxyUltimate;

class OUGallerySlider extends \OxyUltimateEl {
	
	public $has_js = true;
	public $css_added = false;
	public $js_added = false;
	private $slide_js_code = array();

	function name() {
		return __( "Gallery Slider", 'oxy-ultimate' );
	}

	function slug() {
		return "ouacfg_slider";
	}

	function oxyu_button_place() {
		return "images";
	}

	function class_names() {
		return array('oxy-ultimate-element ouacfg-slider-wrapper');
	}

	function icon() {
		return CT_FW_URI . '/toolbar/UI/oxygen-icons/add-icons/gallery.svg';
	}

	function settingsGallerySource() {
		$sliderImages = $this->addControlSection('slider_images', __('Slider Images'), 'assets/icon.png', $this );

		$sliderImages->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">For WooCommerce product images, you will select the <strong>Custom Field</strong> option & enter <span style="color:#ff7171;">_product_image_gallery</span> into the <strong>Gallery Field Name</strong> input field.</div>'), 
			'WooCommerce_note'
		)->setParam('heading', 'Note:');

		$gsource = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Gallery Source"),
				"slug" 		=> "oug_source",
				"value" 	=> array(
					"media" 	=> __("Media Library", "oxy-ultimate"), 
					"acf" 		=> __("Custom Field", "oxy-ultimate"),
					"acfrep" 	=> __("ACF Repeater", "oxy-ultimate")
				),
				"default" 	=> "media"
			)
		);		

		$wpGallery = $sliderImages->addCustomControl("
			<div class='oxygen-control'>
				<div class='oxygen-file-input'
				ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oug_images')}\">
					<input type=\"text\" spellcheck=\"false\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oug_images']\" ng-model-options=\"{ debounce: 10 }\"
						ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ouacfg_slider','oug_images');\">
					<div class=\"oxygen-file-input-browse\"
						data-mediaTitle=\"Select Images\" 
						data-mediaButton=\"Select Images\"
						data-mediaMultiple=\"true\"
						data-mediaProperty=\"oug_images\"
						data-mediaType=\"gallery\">". __("browse","oxygen") . "
					</div>
				</div>
			</div>",
			'oug_images'
    	);

    	$wpGallery->setParam('heading', 'Images');
		$wpGallery->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='media'");

		$source = $sliderImages->addOptionControl(
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

		$source->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']!='media'");

		$import = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Enter Post/Page ID", "oxy-ultimate"),
				"slug" 		=> "page_id"
			)
		);

		$import->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']!='media'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_acfg_source']=='import'");
		$import->rebuildElementOnChange();

		$gf_name = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Gallery Field Name", "oxy-ultimate"),
				"slug" 		=> "field_name"
			)
		);

		$gf_name->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='acf'");
		$gf_name->rebuildElementOnChange();


		//* Repeater fields
		$acfrep_name = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Repeater Field Name", "oxy-ultimate"),
				"slug" 		=> "repfield_name"
			)
		);

		$acfrep_name->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='acfrep'");
		$acfrep_name->rebuildElementOnChange();

		$repimg_name = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Image Field Name", "oxy-ultimate"),
				"slug" 		=> "repimg_name"
			)
		);

		$repimg_name->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='acfrep'");
		$repimg_name->rebuildElementOnChange();

		$reptitle_name = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Title Field Name", "oxy-ultimate"),
				"slug" 		=> "reptitle_name"
			)
		);

		$reptitle_name->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='acfrep'");
		$reptitle_name->rebuildElementOnChange();

		$repdesc_name = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Description Field Name", "oxy-ultimate"),
				"slug" 		=> "repdesc_name"
			)
		);

		$repdesc_name->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='acfrep'");
		$repdesc_name->rebuildElementOnChange();

		$repBtnTxt = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Button Text Field Name", "oxy-ultimate"),
				"slug" 		=> "repbtn_txt"
			)
		);

		$repBtnTxt->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='acfrep'");
		$repBtnTxt->rebuildElementOnChange();

		$repBtnLink = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'textfield',
				"name" 		=> __("Button Link Field Name", "oxy-ultimate"),
				"slug" 		=> "repbtn_link"
			)
		);

		$repBtnLink->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']=='acfrep'");
		$repBtnLink->rebuildElementOnChange();

		$altTags = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Add Alt Tags', 'oxy-ultimate'),
				'slug' 		=> 'ouacfg_alttag',
				'value' 	=> ['yes' => __('Yes'), 'no' => __('No')],
				'default' 	=> 'yes'
			)
		);
		$altTags->setParam('description', __('This option will work when you will enable the Auto Height option'));

		$image_size = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Image Size", "oxy-ultimate"),
				"slug" 		=> "image_size",
				"value" 	=> $this->oxyu_image_sizes(),
				"default" 	=> "full"
			)
		);
		$image_size->rebuildElementOnChange();

		$image_fit = $sliderImages->addControl( 'buttons-list', 'image_fit', __('Image Fit') );
		$image_fit->setValue( array( "Cover", "Contain", "Auto" ) );
		$image_fit->setValueCSS( array(
			"Cover" 	=> ".ouacfg-slider-image-container{background-size: cover;}",
			"Contain" 	=> ".ouacfg-slider-image-container{background-size: contain;}",
			"Auto" 		=> ".ouacfg-slider-image-container{background-size: auto;}"
		));
		$image_fit->setParam("description", "This settings will avoid when you will enable the Auto Height option.");
		$image_fit->setDefaultValue("Cover");
		$image_fit->whiteList();

		$order = $sliderImages->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __('Order', 'oxy-ultimate'),
				'slug' 		=> 'ouacfg_order',
				'value' 	=> ['default' => __('Default'), 'rand' => __('Random')],
				'default' 	=> 'default'
			)
		);
	}

	function settingsSlider() {
		$sliderSettings = $this->addControlSection('slider_settings', __('Slider Settings'), 'assets/icon.png', $this );

		$sldType = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Slider Type', 'oxy-ultimate'),
			'slug' 		=> 'ouacfg_sldtype',
			'value' 	=> [
				'carousel'	=> __('Carousel', "oxy-ultimate"),
				'slideshow' => __('Slideshow', "oxy-ultimate"),
				'coverflow' => __('Coverflow', "oxy-ultimate")
			]
		]);
		$sldType->setParam('description', __('Carousel or Slideshow would be the better option for Ken Burns effect.', "oxy-ultimate") );
		$sldType->rebuildElementOnChange();

		$animType = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Animation Effect', 'oxy-ultimate'),
			'slug' 		=> 'ouacfg_sldeffect',
			'default' 	=> 'slide',
			'value' 	=> [
				'slide'		=> __('Slide', "oxy-ultimate"),
				'fade' 		=> __('Fade', "oxy-ultimate"),
				'cube' 		=> __('Cube', "oxy-ultimate"),
				'kenburns' 	=> __('Ken Burns', "oxy-ultimate")
			]
		]);
		$animType->rebuildElementOnChange();

		$centeredSld = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Centered Slide', 'oxy-ultimate'),
			'slug' 		=> 'ougsld_centered',
			'default' 	=> 'no',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
		$centeredSld->rebuildElementOnChange();

		$sldLoop = $sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Infinite Loop', 'oxy-ultimate'),
			'slug' 		=> 'ougsld_loop',
			'default' 	=> 'yes',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
		$sldLoop->setParam('description', "When you will use the Slideshow effect, you will enable the Infinite loop.");
		$sldLoop->rebuildElementOnChange();

		$sliderSettings->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Auto Height', 'oxy-ultimate'),
			'slug' 		=> 'ousld_auto_height',
			'default' 	=> 'no',
			'value' 	=> [
				'no' 		=> __('No', "oxy-ultimate"),
				'yes'		=> __('Yes', "oxy-ultimate")
			]
		])->rebuildElementOnChange();

		$sliderSettings->addStyleControl(
			array(
				'control_type' 	=> 'slider-measurebox',
				'name' 			=> __('Images Height', "oxy-ultimate"),
				'selector'		=> '.ouacfg-slider .ougsld-img', //, .ouacfg-slider.ouacfg-slider-slideshow
				'slug' 			=> 'ouacfg_sldh',
				'property' 		=> 'height',
				'condition' 	=> 'ousld_auto_height=no'
			)
		)
		->setUnits('px', 'px')
		->setRange('0', '1500', '10')
		->setValue('250')
		->rebuildElementOnChange();

		//* Items per Columns
		$maultirow = $sliderSettings->addControlSection('slidesPerColumn', __('Multi Row Layout'), 'assets/icon.png', $this );

		$maultirow->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Number of slides per column, for multirow layout. It is currently not compatible with loop mode (infiniteloop: true). This would be good option for carousel slide effect.</div>'), 
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
		$sldrowsD->rebuildElementOnChange();

		$sldrowTb = $maultirow->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'rows_tablet',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldrowTb->setUnits(' ',' ');
		$sldrowTb->setRange('1', '10', '1');
		$sldrowTb->setValue('1');
		$sldrowTb->rebuildElementOnChange();

		$sldrowsPL = $maultirow->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'rows_landscape',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldrowsPL->setUnits(' ',' ');
		$sldrowsPL->setRange('1', '10', '1');
		$sldrowsPL->setValue('1');
		$sldrowsPL->rebuildElementOnChange();

		$sldrowsPT = $maultirow->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'rows_portrait',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldrowsPT->setUnits(' ',' ');
		$sldrowsPT->setRange('1', '10', '1');
		$sldrowsPT->setValue('1');
		$sldrowsPT->rebuildElementOnChange();

		//* Items per View
		$itemsPerView = $sliderSettings->addControlSection('itemsPerView', __('Items per View'), 'assets/icon.png', $this );
		$sldpvD = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'columns',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldpvD->setUnits(' ',' ');
		$sldpvD->setRange('1', '10', '1');
		$sldpvD->setValue('4');
		$sldpvD->rebuildElementOnChange();

		$sldpvTb = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'columns_tablet',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldpvTb->setUnits(' ',' ');
		$sldpvTb->setRange('1', '10', '1');
		$sldpvTb->setValue('3');
		$sldpvTb->rebuildElementOnChange();

		$sldpvPL = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'columns_landscape',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldpvPL->setUnits(' ',' ');
		$sldpvPL->setRange('1', '10', '1');
		$sldpvPL->setValue('2');
		$sldpvPL->rebuildElementOnChange();

		$sldpvPT = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'columns_portrait',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldpvPT->setUnits(' ',' ');
		$sldpvPT->setRange('1', '10', '1');
		$sldpvPT->setValue('1');
		$sldpvPT->rebuildElementOnChange();

		//* Slides to Scroll
		$sldSTS = $sliderSettings->addControlSection('sld_scroll', __('Slides to Scroll'), 'assets/icon.png', $this );
		$sldSTSD = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldSTSD->setUnits(' ',' ');
		$sldSTSD->setRange('1', '10', '1');
		$sldSTSD->setValue('1');
		$sldSTSD->setParam('description',__('Set numbers of slides to move at a time.', "oxy-ultimate"));

		$sldSTSTB = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll_tablet',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldSTSTB->setUnits(' ',' ');
		$sldSTSTB->setRange('1', '10', '1');
		$sldSTSTB->setValue('1');

		$sldSTSPL = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll_landscape',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldSTSPL->setUnits(' ',' ');
		$sldSTSPL->setRange('1', '10', '1');
		$sldSTSPL->setValue('1');

		$sldSTSPT = $sldSTS->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'slides_to_scroll_portrait',
			'condition' => 'ouacfg_sldtype!=slideshow'
		]);

		$sldSTSPT->setUnits(' ',' ');
		$sldSTSPT->setRange('1', '10', '1');
		$sldSTSPT->setValue('1');

		//* Spacing
		$sldSP = $sliderSettings->addControlSection('sld_spacing', __('Gap Between Items'), 'assets/icon.png', $this );
		$sldgap = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'ouacfg_sldgap'
		]);

		$sldgap->setUnits('px','px');
		$sldgap->setRange('5', '50', '5');
		$sldgap->setValue('15');
		$sldgap->rebuildElementOnChange();

		$sldgapTB = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'ouacfg_sldgap_tablet'
		]);

		$sldgapTB->setUnits('px','px');
		$sldgapTB->setRange('5', '50', '5');
		$sldgapTB->setValue('15');
		$sldgapTB->rebuildElementOnChange();

		$sldgapPL = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'ouacfg_sldgap_landscape'
		]);

		$sldgapPL->setUnits('px','px');
		$sldgapPL->setRange('5', '50', '5');
		$sldgapPL->setValue('15');
		$sldgapPL->rebuildElementOnChange();

		$sldgapPT = $sldSP->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'ouacfg_sldgap_portrait'
		]);

		$sldgapPT->setUnits('px','px');
		$sldgapPT->setRange('5', '50', '5');
		$sldgapPT->setValue('15');
		$sldgapPT->rebuildElementOnChange();

		$this->settingsSlide( $sliderSettings );

		$kenburnsEffect = $sliderSettings->addControlSection('kenburns_effect', __('Kenburns Config'), 'assets/icon.png', $this );
		$kbTD = $kenburnsEffect->addStyleControl([
			'selector'		=> '.swiper-scale-effect .swiper-slide-cover',
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox',
			'name' 			=> __('Transition Duration', "oxy-ultimate"),
			'slug' 			=> 'ouacfg_kbtd',
			'condition' 	=> 'ouacfg_sldeffect=kenburns'
		]);
		$kbTD->setUnits('s','sec');
		$kbTD->setRange('0', '10', '0.5');
		$kbTD->setValue('4.5');

		$kbTS = $kenburnsEffect->addOptionControl([
			'type' 			=> 'slider-measurebox',
			'name' 			=> __('Transform Scale', "oxy-ultimate"),
			'slug' 			=> 'ouacfg_kbts',
			'condition' 	=> 'ouacfg_sldeffect=kenburns'
		]);
		$kbTS->setUnits(' ',' ');
		$kbTS->setRange('0', '4', '0.02');
		$kbTS->setValue('1.18');
		$kbTS->rebuildElementOnChange();
	}

	function settingsSlide( $controlObj ) {
		$slideSettings = $controlObj->addControlSection('slide_settings', __('Speed Settings'), 'assets/icon.png', $this );

		$transitionSpeed = $slideSettings->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Transition Speed', "oxy-ultimate"),
			'slug' 		=> 'transition_speed'
		]);

		$transitionSpeed->setUnits('ms','ms');
		$transitionSpeed->setRange('1000', '20000', '500');
		$transitionSpeed->setValue('1000');
		$transitionSpeed->setParam('description', __( 'Value unit for form field of time in mili seconds. Such as: "1000 ms"', 'oxy-ultimate' ) );
		$transitionSpeed->rebuildElementOnChange();

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
		$autoPlay->rebuildElementOnChange();

		$autoPlaySpeed = $slideSettings->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Auto Play Speed', "oxy-ultimate"),
			'slug' 		=> 'autoplay_speed'
		]);

		$autoPlaySpeed->setUnits('ms','ms');
		$autoPlaySpeed->setRange('1000', '20000', '500');
		$autoPlaySpeed->setValue('5000');
		$autoPlaySpeed->setParam('description', __( 'Value unit for form field of time in mili seconds. Such as: "1000 ms"', 'oxy-ultimate' ) );
		$autoPlaySpeed->rebuildElementOnChange();

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

		$navArrow = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show Navigation Arrows', 'oxy-ultimate'),
			'slug' 		=> 'slider_navigation',
			'default' 	=> 'no',
			'value' 	=> [
				'yes'		=> __('Yes', "oxy-ultimate"),
				'no' 		=> __('No', "oxy-ultimate")
			]
		]);
		$navArrow->rebuildElementOnChange();

		$arrowOnHover = $arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show on Hover', 'oxy-ultimate'),
			'slug' 		=> 'slider_navapr',
			'default' 	=> 'no',
			'value' 	=> [
				'no'		=> __('No', "oxy-ultimate"),
				'onhover' 	=> __('Yes', "oxy-ultimate")
			]
		]);
		$arrowOnHover->setParam('description', "Preview is disable for builder editor.");
		$arrowOnHover->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_slider_navigation']!='no'");
		$arrowOnHover->rebuildElementOnChange();

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
		$arrowBreakpoint->rebuildElementOnChange();

		$icon = $arrow->addControlSection('arrow_icon', __('Icon'), 'assets/icon.png', $this );
		$leftArrow = $icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Left Arrow', "oxy-ultimate"),
				"slug" 			=> 'arrow_left'
			)
		);
		$leftArrow->rebuildElementOnChange();

		$rightArrow= $icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Right Arrow', "oxy-ultimate"),
				"slug" 			=> 'arrow_right'
			)
		);
		$rightArrow->rebuildElementOnChange();

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
			'selector' 	=> '.ouacfg-slider',
			'slug' 		=> 'arwbtn_gaptop',
			'property' 	=> 'padding-top',
			'control_type' => 'slider-measurebox'
		]);
		$gapAboveImage->setParam('description', 'Add some extra white spaces above the images if you put the arrows outside of the images(at top).');
		$gapAboveImage->setRange(0,100,10);
		$gapAboveImage->setUnits('px', 'px,%,em');

		$gapBelowImage = $arrowPos->addStyleControl([
			'name' 		=> __('Gap Below Images'),
			'selector' 	=> '.ouacfg-slider',
			'slug' 		=> 'arwbtn_gapbtm',
			'property' 	=> 'padding-bottom',
			'control_type' => 'slider-measurebox'
		]);
		$gapBelowImage->setParam('description', 'Add some extra white spaces below the images if you put the arrows outside of the images(at bottom).');
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
		/*$pagination->addCustomControl( __('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Pagination option will not work for slideshow slider type</div>'), 'description' );*/

		$pagType = $pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Pagination Type', 'oxy-ultimate'),
			'slug' 		=> 'pagination_type',
			'default' 	=> 'bullets',
			'value' 	=> [
				'none'			=> __( 'None', 'oxy-ultimate' ),
				'bullets'       => __( 'Dots', 'oxy-ultimate' ),
				'fraction'		=> __( 'Fraction', 'oxy-ultimate' ),
				//'progress'		=> __( 'Progress', 'oxy-ultimate' ),
			]
		]);
		/*$pagType->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_ouacfg_sldtype']!='slideshow'");*/
		$pagType->rebuildElementOnChange();

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
		$pagDots->rebuildElementOnChange();

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
		/*$pos->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_ouacfg_sldtype']!='slideshow'");*/
		$pos->rebuildElementOnChange();

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
		$dotsBP->rebuildElementOnChange();

		$pagination->addStyleControls([
			[
				'name' 			=> _('Dots/Fraction Position'),
				'description' 	=> __('It will work when you will choose the position "Below Image".'),
				'selector' 		=> '.ouacfg-slider.ouacfg-navigation-outside',
				'property' 		=> 'padding-bottom',
				'control_type'	=> 'measurebox',
				'value' 		=> '40',
				'default' 		=> '40',
				'unit' 			=> 'px',
				'slug' 			=> 'bullets_pos',
				//'condition' 	=> 'pagination_position=outside&&ouacfg_sldtype!=slideshow'
			],
			[
				'selector' 		=> '.ouacfg-slider .swiper-pagination-bullet, .ouacfg-slider.swiper-container-horizontal>.swiper-pagination-progress',
				'property' 		=> 'background-color',
				'slug' 			=> 'pagination_bg_color',
				'condition' 	=> 'pagination_type=bullets'
			],
			[
				'name' 			=> _('Active Background Color'),
				'selector' 		=> '.ouacfg-slider .swiper-pagination-bullet:hover, .ouacfg-slider .swiper-pagination-bullet-active, .ouacfg-slider .swiper-pagination-progress .swiper-pagination-progressbar',
				'property' 		=> 'background-color',
				'slug' 			=> 'pagination_bg_hover',
				'condition' 	=> 'pagination_type=bullets'
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
				'selector' 		=> '.ouacfg-slider .swiper-pagination-bullet-active',
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
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'default' 		=> 100,
				'step' 			=> 1,
				'min' 			=> 0,
				'max' 			=> 1000,
				'unit' 			=> '%',
				'condition' 	=> 'pagination_type=fraction'
			],
			[
				'name' 			=> _('Dots Round Corners'),
				'selector' 		=> '.swiper-pagination-bullet',
				'property' 		=> 'border-radius',
				'slug' 			=> 'bullets_brdrad',
				'condition' 	=> 'pagination_type=bullets'
			],
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'background-color',
				'condition' 	=> 'pagination_type=fraction'
			],
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'color',
				'condition' 	=> 'pagination_type=fraction'
			],
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'font-size',
				'condition' 	=> 'pagination_type=fraction'
			],
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'font-weight',
				'condition' 	=> 'pagination_type=fraction'
			]
		]);

		$pagination->addStyleControl(
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'padding-top',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'pagination_type=fraction'
			]
		)->setParam('hide_wrapper_end', true);

		$pagination->addStyleControl(
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'padding-bottom',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'pagination_type=fraction'
			]
		)->setParam('hide_wrapper_start', true);

		$pagination->addStyleControl(
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'padding-left',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'pagination_type=fraction'
			]
		)->setParam('hide_wrapper_end', true);

		$pagination->addStyleControl(
			[
				'selector' 		=> '.swiper-pagination-fraction',
				'property' 		=> 'padding-right',
				'control_type' 	=> 'measurebox',
				'unit' 			=> 'px',
				'condition' 	=> 'pagination_type=fraction'
			]
		)->setParam('hide_wrapper_start', true);
	}

	function settingsThumbnailsPagination() {
		$pagination = $this->addControlSection('thumb_pagination', __('Thumbs Pagination'), 'assets/icon.png', $this );
		$pagination->addCustomControl( __('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.325; font-size: 13px;">This option will work for slideshow slider type</div>'), 'pg_desc' );

		$pos = $pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Position', 'oxy-ultimate'),
			'slug' 		=> 'thumb_position',
			'default' 	=> 'below',
			'value' 	=> [
				'above'		=> __('Above', "oxy-ultimate"),
				'below' 	=> __('Below', "oxy-ultimate")
			]
		]);
		$pos->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_ouacfg_sldtype']=='slideshow'");
		$pos->rebuildElementOnChange();

		$thumb_size = $pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Thumbnails Resolution', 'oxy-ultimate'),
			'slug' 		=> 'thumb_size',
			'default' 	=> 'thumbnail',
			'value' 	=> [
				'thumbnail'		=> __('Small', "oxy-ultimate"),
				'medium'		=> __('Medium', "oxy-ultimate"),
				'large'			=> __('Large', "oxy-ultimate"),
				'full'			=> __('Full', "oxy-ultimate")
			]
		]);
		$thumb_size->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_ouacfg_sldtype']=='slideshow'");
		$thumb_size->rebuildElementOnChange();

		$ratio = $pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Aspect Ratio', 'oxy-ultimate'),
			'slug' 		=> 'thumb_ratio',
			'default' 	=> '43',
			'value' 	=> [
				'11' 		=> __('1:1', "oxy-ultimate"),
				'43'		=> __('4:3', "oxy-ultimate"),
				'169'		=> __('16:9', "oxy-ultimate"),
				'219'		=> __('21:9', "oxy-ultimate"),
			]
		]);
		$ratio->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_ouacfg_sldtype']=='slideshow'");
		$ratio->rebuildElementOnChange();

		$pagination->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Hide on Mobile Devices?', 'oxy-ultimate'),
			'slug' 		=> 'hide_thumbs_mobile',
			'default' 	=> 'no',
			'value' 	=> [
				'no' 		=> __('No', "oxy-ultimate"),
				'yes'		=> __('Yes', "oxy-ultimate")
			],
			'condition' 	=> 'ouacfg_sldtype=slideshow'
		]);

		$breakpoint = $pagination->addOptionControl([
			'type' 		=> 'measurebox',
			'name' 		=> __('Breakpoint'),
			'slug' 		=> 'thumb_rsp_breakpoint'
		]);
		$breakpoint->setUnits('px', 'px');
		$breakpoint->setDefaultValue(768);
		$breakpoint->setParam('description', 'Default breakpoint value is 768px.');
		$breakpoint->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_ouacfg_sldtype']=='slideshow'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_hide_thumbs_mobile']=='yes'");
		$breakpoint->rebuildElementOnChange();

		//* Thumbs Width Settings
		$thumbWidthSection = $pagination->addControlSection('thumbs_width', __('Width'), 'assets/icon.png', $this );
		$thumbWidthSection->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">Responsive effect will not work properly on Builder editor.</div>'), 
			'responsive_note'
		)->setParam('heading', 'Note:');

		$thumbWD = $thumbWidthSection->addOptionControl([
			'name' 			=> __('Desktop (Greater than 992px)'),
			'slug' 			=> 'thumbw_desktop',
			'type' 			=> 'slider-measurebox',
			'condition' 	=> 'ouacfg_sldtype=slideshow',
		]);
		$thumbWD->setRange(0, 800, 10);
		$thumbWD->setUnits('px','px');
		$thumbWD->setParam('ng_show',"!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')");
		$thumbWD->rebuildElementOnChange();

		$thumbWT = $thumbWidthSection->addOptionControl([
			'name' 			=> __('Large Devices (Range: 769px to 992px)'),
			'slug' 			=> 'thumbw_tablet',
			'type' 			=> 'slider-measurebox',
			'condition' 	=> 'ouacfg_sldtype=slideshow',
		]);
		$thumbWT->setRange(0, 800, 10);
		$thumbWT->setUnits('px','px');
		$thumbWT->setParam('ng_show',"!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')");
		$thumbWT->rebuildElementOnChange();

		$thumbWL = $thumbWidthSection->addOptionControl([
			'name' 			=> __('Medium Devices (Range: 640px to 768px)'),
			'slug' 			=> 'thumbw_landscape',
			'type' 			=> 'slider-measurebox',
			'condition' 	=> 'ouacfg_sldtype=slideshow',
		]);
		$thumbWL->setRange(0, 800, 10);
		$thumbWL->setUnits('px','px');
		$thumbWL->setParam('ng_show',"!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')");
		$thumbWL->rebuildElementOnChange();

		$thumbWP = $thumbWidthSection->addOptionControl([
			'name' 			=> __('Small Devices (Below 640px)'),
			'slug' 			=> 'thumbw_portrait',
			'type' 			=> 'slider-measurebox',
			'condition' 	=> 'ouacfg_sldtype=slideshow',
		]);
		$thumbWP->setRange(0, 800, 10);
		$thumbWP->setUnits('px','px');
		$thumbWP->setParam('ng_show',"!iframeScope.isEditing('state')&&!iframeScope.isEditing('media')");
		$thumbWP->rebuildElementOnChange();

		$wrapperAlignment = $thumbWidthSection->addControl('buttons-list', 'thumbwrapper_align',  __('Thumbnails Alignment'));
		$wrapperAlignment->setValue(['Left', 'Right', 'Center']);
		$wrapperAlignment->setValueCSS([
			'Left' 		=> '.ou-thumbnails-swiper{margin-left: 0;}',
			'Right' 	=> '.ou-thumbnails-swiper{margin-right: 0;}',
		]);
		$wrapperAlignment->setDefaultValue('Center');
		$wrapperAlignment->whiteList();

		//* Items per View
		$itemsPerView = $pagination->addControlSection('thumbs_view', __('Items per View'), 'assets/icon.png', $this );
		$sldpvD = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('All Devices', "oxy-ultimate"),
			'slug' 		=> 'thumb_columns',
			'condition' => 'ouacfg_sldtype=slideshow'
		]);

		$sldpvD->setUnits(' ',' ');
		$sldpvD->setRange('1', '10', '1');
		$sldpvD->setValue('4');
		$sldpvD->rebuildElementOnChange();

		$sldpvTb = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Large Devices (Range: 769px to 992px)', "oxy-ultimate"),
			'slug' 		=> 'thumb_columns_tablet',
			'condition' => 'ouacfg_sldtype=slideshow'
		]);

		$sldpvTb->setUnits(' ',' ');
		$sldpvTb->setRange('1', '10', '1');
		$sldpvTb->setValue('3');
		$sldpvTb->rebuildElementOnChange();

		$sldpvPL = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Medium Devices (Range: 640px to 768px)', "oxy-ultimate"),
			'slug' 		=> 'thumb_columns_landscape',
			'condition' => 'ouacfg_sldtype=slideshow'
		]);

		$sldpvPL->setUnits(' ',' ');
		$sldpvPL->setRange('1', '10', '1');
		$sldpvPL->setValue('2');
		$sldpvPL->rebuildElementOnChange();

		$sldpvPT = $itemsPerView->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Small Devices (Below 640px)', "oxy-ultimate"),
			'slug' 		=> 'thumb_columns_portrait',
			'condition' => 'ouacfg_sldtype=slideshow'
		]);

		$sldpvPT->setUnits(' ',' ');
		$sldpvPT->setRange('1', '10', '1');
		$sldpvPT->setValue('2');
		$sldpvPT->rebuildElementOnChange();


		$thumbEffect = $pagination->addControlSection('thumbs_effect', __('Effect'), 'assets/icon.png', $this );

		//*Opacity
		$thumbEffect->addStyleControls([
			[
				'name' 			=> __('Initial Opacity'),
				'selector' 		=> '.ouacfg-slider-thumb',
				'property' 		=> 'opacity',
				'value' 		=> 1,
				'units' 		=> ' ',
				'min' 			=> 0,
				'max' 			=> 1,
				'step' 			=> 0.1,
				'condition' 	=> 'ouacfg_sldtype=slideshow'
			],
			[
				'name' 			=> __('Opacity for Active or Hover Thumbnail'),
				'selector' 		=> '.swiper-slide-active .ouacfg-slider-thumb, .ouacfg-slider-thumb:hover',
				'property' 		=> 'opacity',
				'value' 		=> 1,
				'units' 		=> ' ',
				'min' 			=> 0,
				'max' 			=> 1,
				'step' 			=> 0.1,
				'condition' 	=> 'ouacfg_sldtype=slideshow'
			]
		]);

		
		$thumbsTS = $thumbEffect->addStyleControl([
			'name' 			=> __('Transition Duration'),
			'selector' 		=> '.ou-thumbnails-swiper .ouacfg-slider-thumb',
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox'
		]);
		$thumbsTS->setRange('0', '20', '.1');
		$thumbsTS->setUnits('s', 'sec');
		$thumbsTS->setDefaultValue('0.5');

		$imgIntitalScale = $thumbEffect->addStyleControl([
			'name' 			=> __('Scale (Initial State)'),
			'selector' 		=> '.ouacfg-slider-thumb',
			'property' 		=> '--thumb-initial-scale',
			'control_type' 	=> 'slider-measurebox',
		]);
		$imgIntitalScale->setRange(-5, 5, 0.01);
		$imgIntitalScale->setUnits('', ' ');
		$imgIntitalScale->setDefaultValue('1');

		$imghtscale = $thumbEffect->addStyleControl([
			'name' 			=> __('Scale (Hover/Active State)'),
			'selector' 		=> '.ouacfg-slider-thumb',
			'property' 		=> '--thumb-hover-scale',
			'control_type' 	=> 'slider-measurebox',
		]);
		$imghtscale->setRange(-5, 5, 0.01);
		$imghtscale->setUnits('', ' ');
		$imghtscale->setDefaultValue('1');
	}

	function gallerySliderCaption() {
		$caption = $this->addControlSection('caption_section', __('Caption'), 'assets/icon.png', $this );

		$enableCaption = $caption->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Show Caption"),
				"slug" 		=> "enable_caption",
				"value" 	=> array(
					"no" 		=> __("No", "oxy-ultimate"), 
					"yes" 		=> __("Yes", "oxy-ultimate")
				),
				"default" 	=> "no"
			)
		);
		$enableCaption->rebuildElementOnChange();

		$enableDesc = $caption->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Show Description"),
				"slug" 		=> "enable_capdesc",
				"value" 	=> array(
					"no" 		=> __("No", "oxy-ultimate"), 
					"yes" 		=> __("Yes", "oxy-ultimate")
				),
				"default" 	=> "no"
			)
		);
		$enableDesc->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_oug_source']!='acfrep'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_enable_caption']=='yes'");
		$enableDesc->rebuildElementOnChange();

		$style = $caption->addControlSection('caption_style', __('Style'), 'assets/icon.png', $this );

		$ovCaption = $style->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Overlay Caption"),
				"slug" 		=> "caption_overlay",
				"value" 	=> array(
					"yes" 		=> __("Yes", "oxy-ultimate"), 
					"no" 		=> __("No", "oxy-ultimate")
				),
				"default" 	=> "yes"
			)
		);
		$ovCaption->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_enable_caption']=='yes'");
		$ovCaption->rebuildElementOnChange();

		$ovAlignment = $style->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Overlay Position"),
				"slug" 		=> "caption_position",
				"value" 	=> array(
					"top" 		=> __("Top", "oxy-ultimate"), 
					"center" 	=> __("Center", "oxy-ultimate"),
					"bottom" 	=> __("Bottom", "oxy-ultimate"),
					"custom" 	=> __("Custom", "oxy-ultimate")
				),
				"default" 	=> "bottom"
			)
		);
		$ovAlignment->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_enable_caption']=='yes'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ouacfg_slider_caption_overlay']=='yes'");
		$ovAlignment->rebuildElementOnChange();

		$style->addStyleControls([
			[
				'selector' 		=> '.ouacfg-slider-caption.caption-custom',
				'property' 		=> 'width',
				'slug' 			=> 'ougcapow_width',
				'condition' 	=> 'caption_position=custom&&caption_overlay=yes'
			],
			[
				'selector' 		=> '.ouacfg-slider-caption .ouacfg-caption-wrapper',
				'property' 		=> 'width',
				'slug' 			=> 'ougcap_width',
				'condition' 	=> 'caption_position!=custom'
			],
			[
				'selector' 		=> '.ouacfg-slider-caption',
				'property' 		=> 'background-color',
				'slug' 			=> 'oug_captionbg'
			],
		]);

		$style->addStyleControl([
			'name' 			=> __('Position Top', 'oxy-ultimate'),
			'selector' 		=> '.ouacfg-slider-caption.caption-custom',
			'property' 		=> 'top',
			'slug' 			=> 'ougcapow_ptop',
			'condition' 	=> 'caption_position=custom&&caption_overlay=yes'
		])->setParam('hide_wrapper_end', true);

		$style->addStyleControl([
			'name' 			=> __('Position Bottom', 'oxy-ultimate'),
			'selector' 		=> '.ouacfg-slider-caption.caption-custom',
			'property' 		=> 'bottom',
			'slug' 			=> 'ougcapow_pbtm',
			'condition' 	=> 'caption_position=custom&&caption_overlay=yes'
		])->setParam('hide_wrapper_start', true);

		$style->addStyleControl([
			'name' 			=> __('Position Left', 'oxy-ultimate'),
			'selector' 		=> '.ouacfg-slider-caption.caption-custom',
			'property' 		=> 'left',
			'slug' 			=> 'ougcapow_pleft',
			'condition' 	=> 'caption_position=custom&&caption_overlay=yes'
		])->setParam('hide_wrapper_end', true);

		$style->addStyleControl([
			'name' 			=> __('Position Right', 'oxy-ultimate'),
			'selector' 		=> '.ouacfg-slider-caption.caption-custom',
			'property' 		=> 'right',
			'slug' 			=> 'ougcapow_pright',
			'condition' 	=> 'caption_position=custom&&caption_overlay=yes'
		])->setParam('hide_wrapper_start', true);

		$contentAlign = $style->addControl('buttons-list', 'caption_alignment', __('Content Alignment', 'oxy-ultimate'));
		$contentAlign->setValue(['Left', 'Center', 'Right']);
		$contentAlign->setValueCSS([
			'Left' 			=> ".ouacfg-slider-caption{text-align: left;}",
			'Right' 		=> ".ouacfg-slider-caption{text-align: right;}",
		]);
		$contentAlign->setDefaultValue('Center');
		$contentAlign->whiteList();

		$style->addPreset(
			"padding",
			"ougcapsp_padding",
			__("Padding", "oxy-ultimate"),
			'.ouacfg-caption-wrapper'
		)->whiteList();

		$style->addPreset(
			"margin",
			"ougcapsp_margin",
			__("Margin", "oxy-ultimate"),
			'.ouacfg-caption-wrapper'
		)->whiteList();

		$caption->typographySection( __('Title'), '.ouacfg-caption-title', $this );
		$descTG = $caption->typographySection( __('Description'), '.ouacfg-caption-text', $this );

		$descTG->addPreset(
			"margin",
			"ougcaptxt_margin",
			__("Margin", "oxy-ultimate"),
			'.ouacfg-caption-text'
		)->whiteList();

		$btn = $caption->typographySection( __('Button Font & Color'), '.ouacfg-caption-btn', $this );
		$btn_selector = '.ouacfg-caption-btn';

		$btn->addStyleControl(
			array(
				"selector" 			=> $btn_selector,
				"property" 			=> 'width'
			)
		)->setParam('hide_wrapper_end', true);

		$btn->addStyleControl(
			array(
				"name" 				=> __('Text Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'color',
			)
		)->setParam('hide_wrapper_start', true);

		$btn->addStyleControl(
			array(
				"name" 				=> __('BG Color'),
				"selector" 			=> $btn_selector,
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_end', true);

		$btn->addStyleControl(
			array(
				"name" 				=> __('BG Hover Color'),
				"selector" 			=> $btn_selector . ':hover',
				"property" 			=> 'background-color'
			)
		)->setParam('hide_wrapper_start', true);

		$btn->addStyleControl(
			array(
				"name" 				=> __('Border Hover Color'),
				"selector" 			=> $btn_selector . ":hover",
				"property" 			=> 'border-color'
			)
		);

		$btn->addPreset(
			"padding",
			"repbtn_padding",
			__("Padding"),
			$btn_selector
		)->whiteList();

		$caption->borderSection( __( "Button Borders", "oxy-ultimate" ), $btn_selector, $this );
		$caption->boxShadowSection( __("Button Box Shadow"), $btn_selector, $this );
		$caption->boxShadowSection( __("Button Hover Box Shadow"), $btn_selector . ':hover', $this );
	}

	function gallerySliderLightbox() {
		$lightbox = $this->addControlSection('lightbox_section', __('Lightbox'), 'assets/icon.png', $this );

		$enableLB = $lightbox->addOptionControl(
			array(
				'type' 		=> 'radio',
				"name" 		=> __("Enable Lightbox Action"),
				"slug" 		=> "enable_lightbox",
				"value" 	=> array(
					"no" 		=> __("No", "oxy-ultimate"), 
					"yes" 		=> __("Yes", "oxy-ultimate")
				),
				"default" 	=> "no"
			)
		);

		$lightbox->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				"name" 		=> __("Image Resolution", "oxy-ultimate"),
				"slug" 		=> "lg_image_size",
				"value" 	=> $this->oxyu_image_sizes(),
				"default" 	=> "full"
			)
		)->setCondition('enable_lightbox=yes');

		$lightbox->addOptionControl(
			array(
				"name" 			=> __('Animation Effect', "oxy-ultimate"),
				"slug" 			=> "lb_aimeffect",
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
		)->setCondition('enable_lightbox=yes');

		$lightbox->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Site Scrolling', "oxy-ultimate"),
			'slug' 		=> 'disable_scroll'
		])->setValue(['no' => __('No'), 'yes' => __('Yes')])->setCondition('enable_lightbox=yes');

		$lightbox->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Display Image Title/Alt', "oxy-ultimate"),
			'slug' 		=> 'lb_img_title'
		])->setValue(['no' => __('No'), 'yes' => __('Yes')])->setDefaultValue('no')->setCondition('enable_lightbox=yes');

		$lightbox->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Display Image Description/Caption', "oxy-ultimate"),
			'slug' 		=> 'lb_img_desc'
		])->setValue(['no' => __('No'), 'yes' => __('Yes')])->setDefaultValue('no')->setCondition('enable_lightbox=yes');

		$closeBtn = $lightbox->addControlSection('close_btn', __('Close Button'), 'assets/icon.png', $this );

		$closeBtn->addOptionControl(
			array(
				"type" 		=> 'icon_finder',
				"name" 		=> __('Select Icon', "oxy-ultimate"),
				"slug" 		=> 'close_icon',
				"default" 	=> 'Lineariconsicon-cross'
			)
		);
	}

	function controls() {
		$this->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.325; font-size: 13px;">Click on the <span style="color:#ff7171;">Apply Params</span> button to get the correct preview on Builder editor. Autoplay preview is disabled for Builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$this->settingsGallerySource();

		$this->settingsSlider();

		$this->settingsNavigationArrows();

		$this->settingsPagination();

		$this->settingsThumbnailsPagination();

		$this->gallerySliderCaption();

		$this->gallerySliderLightbox();
	}

	function render( $options, $defaults, $content ) {
		$images = '';
		$uid = str_replace( ".", "", uniqid( 'ouacfg', true ) );
		
		if( ! empty( $options['oug_source'] ) && $options['oug_source'] == 'media' && isset( $options['oug_images'] ) ) {

			$images = explode( ",", $options['oug_images'] );

		} elseif( ! empty( $options['oug_source'] ) && $options['oug_source'] == 'acf' ) {

			if( empty( $options['field_name'] ) ) {
				echo __("Enter Gallery Field Key.", "oxy-ultimate");
			} else {
				if( ! empty( $options['acfg_source'] ) && $options['acfg_source'] === 'import' ) {
					$post_id = (int) $options['page_id'];
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
					if( has_post_thumbnail( $post_id ) && is_singular('product') ) {
						$images[] = get_post_thumbnail_id( $post_id );
					}

					$product = wc_get_product($post_id);
					if (@method_exists($product, 'get_gallery_attachment_ids')) {
						$images = @array_merge( $images, $product->get_gallery_attachment_ids() );
					}
				} else {
					$images = get_post_meta( $post_id, $options['field_name'], $meta_single );

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
		} elseif( ! empty( $options['oug_source'] ) && $options['oug_source'] == 'acfrep' && function_exists('have_rows') ) {
			
			if( ! empty( $options['acfg_source'] ) && $options['acfg_source'] === 'import' && isset( $options['page_id'] ) )
			{
				$post_id = (int) $options['page_id'];
			} else {
				$post_id = get_the_ID();
			}

			if( isset( $options['repfield_name'] ) && have_rows( $options['repfield_name'], $post_id ) ):
				$images = array();
				$i = 0;
				while( have_rows( $options['repfield_name'], $post_id ) ) : the_row();
					if( isset( $options['repimg_name'] ) ) {
						$slug = $options['repfield_name'] . '_' . $i . '_' . $options['repimg_name'];
						$images[$i][] = get_post_meta( $post_id, $slug, true );
					}

					if( isset( $options['reptitle_name'] ) ) {
						$slug = $options['repfield_name'] . '_' . $i . '_' . $options['reptitle_name'];
						$images[$i]['title'] = get_post_meta( $post_id, $slug, true );
					}

					if( isset( $options['repdesc_name'] ) ) {
						$slug = $options['repfield_name'] . '_' . $i . '_' . $options['repdesc_name'];
						$images[$i]['desc'] = get_post_meta( $post_id, $slug, true );
					}

					if( isset( $options['repbtn_txt'] ) ) {
						$slug = $options['repfield_name'] . '_' . $i . '_' . $options['repbtn_txt'];
						$images[$i]['btnTxt'] = get_post_meta( $post_id, $slug, true );
					}

					if( isset( $options['repbtn_link'] ) ) {
						$slug = $options['repfield_name'] . '_' . $i . '_' . $options['repbtn_link'];
						$images[$i]['btnLink'] = get_post_meta( $post_id, $slug, true );
					}

					$i++;
				endwhile;
			endif;
		}

		$images = apply_filters( 'ou_gallery_slider_images', $images );

		if( $images ) :

			$dataAttr = array();
			$imageSize = isset( $options['image_size'] ) ? $options['image_size'] : 'full';
			$scroll = isset( $options['disable_scroll'] ) ? $options['disable_scroll'] : 'no';
			$dataAttr[] = 'data-slider-lightbox="' . $scroll . '"';

			if( isset($options['ouacfg_order']) && $options['ouacfg_order'] == 'rand') {
				$images = $this->ou_applyRandomOrder( $images );
			}

			if ( isset( $options['thumb_position'] ) && 'above' == $options['thumb_position'] ) {
				include 'thumbnails.php';
			}
	?>
			<div <?php echo implode(" ", $dataAttr); ?> class="ouacfg-slider ouacfg-slider-<?php echo $uid; ?> swiper-container<?php echo ( $options['ouacfg_sldtype'] == 'slideshow') ? ' ouacfg-slider-slideshow' : ''; ?> ouacfg-navigation-<?php echo $options['pagination_position'];?><?php if( $options['ouacfg_sldeffect'] == 'kenburns') { ?> swiper-scale-effect<?php } ?>" data-sld-uid="<?php echo $uid; ?>">
				<div class="swiper-wrapper auto-height-<?php echo $options['ousld_auto_height'] ;?>">
					<?php foreach( $images as $key => $imageID ) {
						$alt = $title = $lbdesc = '';
						if( is_array( $imageID ) ) {
							$sliderImage = wp_get_attachment_image_src( $imageID[0], $imageSize );
							$srcset = wp_get_attachment_image_srcset($imageID[0], $imageSize);
							$attID = $imageID[0];
						} else {
							$sliderImage = wp_get_attachment_image_src( $imageID, $imageSize );
							$srcset = wp_get_attachment_image_srcset($imageID, $imageSize);
							$attID = $imageID;
						}

						$meta = $data = $this->ougsld_get_attachment_data( $attID );
						$lbTitle = isset($options['lb_img_title']) ? $options['lb_img_title'] : 'no';
						$lbDesc = isset($options['lb_img_desc']) ? $options['lb_img_desc'] : 'no';

						if( $lbTitle == "yes" ) {
							$title = ! empty($meta->alt) ? 'title="' . $meta->alt . '"' : ( ! empty($meta->title) ? 'title="' . $meta->title . '"' : '');
						}
						
						if( $lbDesc == "yes" ) {
							$lbdesc = ! empty($meta->description) ? $meta->description : ( ! empty($meta->caption) ? $meta->caption : '');
							$lbdesc = 'data-caption="' . $lbdesc . '"';
						}

						if( isset($options['ouacfg_alttag']) && $options['ouacfg_alttag'] == 'yes' ) {
							$alt = ! empty($meta->alt) ? ' alt="' . $meta->alt . '"' : ( ! empty($meta->title) ? ' alt="' . $meta->title . '"' : '');
						}

						$ariaLabel = ! empty($meta->alt) ? $meta->alt : ( ! empty($meta->title) ? $meta->title : 'Image Lightbox' );
					?>
					<div class="swiper-slide ouacfg-slider-item"> <!--ouacfg-slider-item-->
						<?php 
							if( isset($options['enable_lightbox']) && $options['enable_lightbox'] == 'yes' ) {
								$lgImageSize = isset( $options['lg_image_size'] ) ? $options['lg_image_size'] : 'full';
								$fullImage = wp_get_attachment_image_src( $attID, $lgImageSize );
								printf('<a href="%1$s" data-effect="%2$s" aria-label="%5$s" %3$s %4$s>', esc_url($fullImage[0]), $options['lb_aimeffect'], $title, $lbdesc, esc_attr( $ariaLabel ) );
							} 
						?>
						<?php 
							if( isset($options['ousld_auto_height']) && $options['ousld_auto_height'] == 'yes' ) : 
								list($image_src, $image_width, $image_height) = $sliderImage;
								
						?>
							<div class="ouacfg-slider-image-container<?php if( $options['ouacfg_sldeffect'] == 'kenburns') { ?> swiper-slide-cover<?php } ?>">
								<img src="<?php echo esc_url( $sliderImage[0] ); ?>" srcset="<?php echo $srcset; ?>" sizes="(max-width: <?php echo $image_width; ?>px) 100vw, <?php echo $image_width; ?>px" width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>"<?php echo $alt; ?>/>
							</div>
						<?php else : ?>
							<div class="ougsld-img ouacfg-slider-image-container<?php if( $options['ouacfg_sldeffect'] == 'kenburns') { ?> swiper-slide-cover<?php } ?>" style="background-image:url(<?php echo esc_url( $sliderImage[0] ); ?>)"></div>
						<?php endif; ?>
						
						<?php 
							if( isset( $options['enable_caption'] ) && $options['enable_caption'] == 'yes' ) { 
								if(isset( $options['caption_overlay'] ) && $options['caption_overlay'] == 'no') {
									$caption_cssclass = ' basic-caption';
								} else {
									$caption_cssclass = ' caption-' . $options["caption_position"];
								}
						?>
							<div class="ouacfg-slider-caption<?php echo $caption_cssclass; ?>">
								<div class="ouacfg-caption-wrapper">
									<?php 
										if( $options['oug_source'] == 'acfrep' ) 
										{ 
											if(! empty( $imageID['title'] ) ): ?>
												<h3 class="ouacfg-caption-title heading"><?php echo $imageID['title']; ?></h3>
											<?php endif; ?>
											<?php if( !empty( $imageID['desc'] ) ): ?>
												<div class="ouacfg-caption-text"><?php echo $imageID['desc']; ?></div>
											<?php endif; ?>

											<?php if( !empty( $imageID['btnTxt'] ) && !empty( $imageID['btnLink'] ) ) : ?>
												<a href="<?php echo esc_url( $imageID['btnLink'] ); ?>" role="button" class="ouacfg-caption-btn"><?php echo $imageID['btnTxt']; ?></a>
											<?php endif; ?>
									<?php 
										} else { 
											//$data = $this->ougsld_get_attachment_data( $imageID );
											$title = ! empty($data->title) ? $data->title : ( ! empty($data->alt) ? $data->alt : '');
											if( $title ) {
												echo '<h3 class="ouacfg-caption-title heading">' . esc_attr( $title ) . '</h3>';
											}

											$desc = ! empty($data->description) ? $data->description : ( ! empty($data->caption) ? $data->caption : '');
											if( $desc && isset( $options['enable_capdesc'] ) && $options['enable_capdesc'] == "yes" ) {
												echo '<p class="ouacfg-caption-text">' . $desc . '</p>';
											}
										} 
									?>
								</div>
							</div>
						<?php } ?>

						<?php if( isset($options['enable_lightbox']) && $options['enable_lightbox'] == 'yes' ) { echo '</a>'; } ?>
					</div>
					<?php } ?>
				</div>

				<?php if ( 1 < count( $images ) ) : ?>
					<?php if( $options['pagination_type'] != 'none' ) { ?><div class="swiper-pagination"></div><?php } ?>
				<?php endif; ?>
			</div>

			<?php if( $options['slider_navigation'] == 'yes' && sizeof($images) > 1 ) { global $oxygen_svg_icons_to_load; ?>
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
			<?php } ?>
				
			<?php 

				if ( isset( $options['thumb_position'] ) && 'below' == $options['thumb_position'] ) {
					include 'thumbnails.php';
				}

				if ( ! isset( $options['thumb_position'] ) ) {
					include 'thumbnails.php';
				}

				ob_start();
			?>

				jQuery(document).ready(function($){
					<?php
						$slides_to_scroll = $slides_to_scroll_tablet = $slides_to_scroll_landscape = $slides_to_scroll_portrait = 1;
						if ( isset( $options['slides_to_scroll'] ) && $options['ouacfg_sldtype'] != 'slideshow' ) {
							$slides_to_scroll = $options['slides_to_scroll'];
						}
						if ( isset( $options['slides_to_scroll_tablet'] ) && $options['ouacfg_sldtype'] != 'slideshow' ) {
							$slides_to_scroll_tablet = $options['slides_to_scroll_tablet'];
						} elseif ( $options['ouacfg_sldtype'] != 'slideshow' ) {
							$slides_to_scroll_tablet = $slides_to_scroll;
						}
						if ( isset( $options['slides_to_scroll_landscape'] ) && $options['ouacfg_sldtype'] != 'slideshow' ) {
							$slides_to_scroll_landscape = $options['slides_to_scroll_landscape'];
						} elseif ( $options['ouacfg_sldtype'] != 'slideshow' ) {
							$slides_to_scroll_landscape = $slides_to_scroll_tablet;
						}
						if ( isset( $options['slides_to_scroll_portrait'] ) && $options['ouacfg_sldtype'] != 'slideshow' ) {
							$slides_to_scroll_portrait = $options['slides_to_scroll_portrait'];
						} elseif ( $options['ouacfg_sldtype'] != 'slideshow' ) {
							$slides_to_scroll_portrait = $slides_to_scroll_landscape;
						}

						if( isset($options['enable_lightbox']) && $options['enable_lightbox'] == 'yes' && ! defined('OXY_ELEMENTS_API_AJAX') ) {

							$closeBtn = isset( $options['close_icon'] ) ? $options['close_icon'] : 'Lineariconsicon-cross';
							global $oxygen_svg_icons_to_load;
							$oxygen_svg_icons_to_load[] = $closeBtn;

							if( ! empty( $closeBtn ) ) {
								$closeMarkup = '<button title="%title%" type="button" class="ougsld-close mfp-close"><svg id="svg-'. esc_attr($options['selector']) .'"><use xlink:href="#' . $closeBtn .'"></use></button>';
							} else {
								$closeMarkup = '<button title="%title%" type="button" class="mfp-close">&#215;</button>';
							}

							wp_enqueue_style('ou-mfp-style');
							wp_enqueue_script('ou-mfp-script');
					?>
							var uacfg = $( '.ouacfg-slider-<?php echo $uid; ?>' );
							if( uacfg.length && typeof $.fn.magnificPopup !== 'undefined') {
								uacfg.magnificPopup({
									delegate: '.ouacfg-slider-item a',
									closeBtnInside: false,
									closeMarkup: '<?php echo $closeMarkup; ?>',
									type: 'image',
									gallery: {
										enabled: true,
										navigateByImgClick: true,
										tCounter: ''
									},
									image: {
										titleSrc: function(item) {
											var markup = '';
											if (item.el[0].hasAttribute("title")) {
												markup += item.el.attr('title');
											}

											if (item.el[0].hasAttribute("data-caption")) {
												markup += '<p>' + item.el.attr('data-caption') + '</p>';
											}
											return markup;
										}
									},
									removalDelay: 500,
									fixedContentPos: 'auto',
									callbacks: {
										beforeOpen: function() {
											if( uacfg.attr('data-slider-lightbox') == "yes" ) {
												$('body').addClass("ougsld-off-scroll");
											}
											this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
											this.st.mainClass = this.st.el.attr('data-effect');
										},
										beforeClose: function() {
											if( uacfg.attr('data-slider-lightbox') == "yes" ) {
												$('body').removeClass("ougsld-off-scroll");
											}
										}
									}
								});
							}
					<?php
						}
					?>
					var settings = {
						id: '<?php echo $uid; ?>',
						type: '<?php echo $options['ouacfg_sldtype']; ?>',
						initialSlide: 0,
						spaceBetween: {
							desktop: '<?php echo $options['ouacfg_sldgap']; ?>',
							tablet: '<?php echo $options['ouacfg_sldgap_tablet']; ?>',
							landscape: '<?php echo $options['ouacfg_sldgap_landscape']; ?>',
							portrait: '<?php echo $options['ouacfg_sldgap_portrait']; ?>'
						},
						slidesPerView: {
							desktop: <?php echo $options['columns']; ?>,
							tablet: <?php echo $options['columns_tablet']; ?>,
							landscape: <?php echo $options['columns_landscape']; ?>,
							portrait: <?php echo $options['columns_portrait']; ?>
						},
						slidesPerColumn: {
							desktop: <?php echo $options['rows']; ?>,
							tablet: <?php echo $options['rows_tablet']; ?>,
							landscape: <?php echo $options['rows_landscape']; ?>,
							portrait: <?php echo $options['rows_portrait']; ?>
						},
						slidesToScroll: {
							desktop: <?php echo $slides_to_scroll; ?>,
							tablet: <?php echo $slides_to_scroll_tablet; ?>,
							landscape: <?php echo $slides_to_scroll_landscape; ?>,
							portrait: <?php echo $slides_to_scroll_portrait; ?>
						},
						slideshow_slidesPerView: {
							desktop: <?php echo $options['thumb_columns']; ?>,
							tablet: <?php echo $options['thumb_columns_tablet']; ?>,
							landscape: <?php echo $options['thumb_columns_landscape']; ?>,
							portrait: <?php echo $options['thumb_columns_portrait']; ?>
						},
						effect: '<?php echo ( ( $options['ouacfg_sldeffect'] == 'kenburns' ) ? 'fade' : $options['ouacfg_sldeffect'] ); ?>',
						isBuilderActive: <?php echo (defined('OXY_ELEMENTS_API_AJAX')) ? 'true' : 'false'; ?>,
						autoplay: <?php echo $options['autoplay'] == 'yes' ? 'true' : 'false'; ?>,
						autoplay_speed: <?php echo $options['autoplay'] == 'yes' ? $options['autoplay_speed'] : 'false'; ?>,
						pagination: '<?php echo $options['pagination_type']; ?>',
						dynamicBullets: <?php echo $options['pg_dyndots'] == 'yes' ? 'true' : 'false'; ?>,
						centered: <?php echo $options['ougsld_centered'] == 'yes' ? 'true' : 'false'; ?>,
						loop: <?php echo ( $options['ougsld_loop'] == 'yes' && absint($options['rows']) <= 1 ) ? 'true' : 'false'; ?>,
						pause_on_hover: <?php echo ( ( $options['pause_on_hover'] == 'yes' && $options['autoplay'] == 'yes' ) ? 'true' : 'false' ); ?>,
						pause_on_interaction: <?php echo $options['pause_on_interaction'] == 'yes' ? 'true' : 'false'; ?>,
						speed: <?php echo isset( $options['transition_speed'] ) ? $options['transition_speed'] : 5000; ?>,
						autoHeight: <?php echo $options['ousld_auto_height'] == 'yes' ? 'true' : 'false'; ?>,
						thumbWidth: {
							desktop: <?php echo (isset($options['thumbw_desktop']) ? ( $options['thumbw_desktop'] * ( ( sizeof($images) > $options['thumb_columns'] ) ? $options['thumb_columns'] : sizeof($images) ) ) : 2 )?>,
							tablet: <?php echo (isset($options['thumbw_tablet']) ? ( $options['thumbw_tablet'] * ( ( sizeof($images) > $options['thumb_columns_tablet'] ) ? $options['thumb_columns_tablet'] : sizeof($images) ) ) : 2 )?>,
							landscape: <?php echo (isset($options['thumbw_landscape']) ? ( $options['thumbw_landscape'] * ( ( sizeof($images) > $options['thumb_columns_landscape'] ) ? $options['thumb_columns_landscape'] : sizeof($images) ) ) : 2 )?>,
							portrait: <?php echo (isset($options['thumbw_portrait']) ? ( $options['thumbw_portrait'] * ( ( sizeof($images) > $options['thumb_columns_portrait'] ) ? $options['thumb_columns_portrait'] : sizeof($images) ) ) : 2 )?>
						},
						breakpoint: {
							desktop: 993,
							tablet: 768,
							landscape: 640,
							portrait: 100
						}
					};

					ougslider_<?php echo $uid; ?> = new OUGallerySlider(settings);

					function ouUpdateCarousel() {
						setTimeout(function() {
							if ( 'number' !== typeof ougslider_<?php echo $uid; ?>.swipers.main.length ) {
								ougslider_<?php echo $uid; ?>.swipers.main.update();
								
								if( 'undefined' !== typeof ougslider_<?php echo $uid; ?>.swipers.thumbs )
									ougslider_<?php echo $uid; ?>.swipers.thumbs.update();
							} else {
								ougslider_<?php echo $uid; ?>.swipers.main.forEach(function(item) {
									if ( 'undefined' !== typeof item ) {
										item.update();
									}
								});
								if( 'undefined' !== typeof ougslider_<?php echo $uid; ?>.swipers.thumbs ) {
									ougslider_<?php echo $uid; ?>.swipers.thumbs.forEach(function(item) {
										if ( 'undefined' !== typeof item ) {
											item.update();
										}
									});
								}
							}
						}, 10);
					}

					$(document).on('ou-accordion-slide-complete', function(e) {
						if ( $(e.target).find('.ouacfg-slider-<?php echo $uid; ?>').length > 0 ) {
							ouUpdateCarousel();
						}
					});

					if ( $('.oxy-tab').length > 0 ) {
						$('.oxy-tab').on('click', function(e) {
							setTimeout(function(){ ouUpdateCarousel(); }, 5 );
						});
					}

					if( $('.ct-modal').length > 0 ) {
						$('.oxy-modal-backdrop').each(function(){
							var triggerSelector = $(this).attr('data-trigger-selector');
							$(triggerSelector).on('click', function(e) {
								setTimeout(function(){ ouUpdateCarousel(); }, 5 );
							});
						});
					}
				});
		<?php
			$js = ob_get_clean();

			if ( isset( $_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') )
			{
				$this->El->builderInlineJS( $js );
			} else {
				$this->slide_js_code[] = $js;
				
				if( ! $this->js_added ) {
					add_action( 'wp_footer', array( $this, 'ougsld_enqueue_scripts' ) );
					$this->js_added = true;
				}

				$this->El->footerJS( join('', $this->slide_js_code) );
			}

		endif;
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

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css.= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();

		if( isset( $original[$prefix . '_ouacfg_kbts'] ) ) {
			$css .= $selector . ' .swiper-scale-effect .swiper-slide-cover{transform: scale(' . $original[$prefix . '_ouacfg_kbts'] .');}';
			$css .= $selector . ' .swiper-scale-effect .swiper-slide.swiper-slide-active .swiper-slide-cover { transform: scale(1);}';
		}

		if( isset( $original[$prefix . '_enable_caption'] ) && $original[$prefix . '_enable_caption'] == 'yes' )
		{
			$css .= $selector . ' .ouacfg-slider-caption{display: flex; flex-direction: row;}';
			
			if( isset( $original[$prefix . '_caption_overlay'] ) && $original[$prefix . '_caption_overlay'] == 'yes' ) {
				$css .= $selector . ' .ouacfg-slider-caption{position: absolute; left:0;width: 100%;z-index:2;}';
			
				if( isset( $original[$prefix . '_caption_position'] ) && $original[$prefix . '_caption_position'] == 'top' ) {
					$css .= $selector . ' .caption-top.ouacfg-slider-caption{align-items: flex-start; top: 0;}';
				}

				if( isset( $original[$prefix . '_caption_position'] ) && $original[$prefix . '_caption_position'] == 'center' ) {
					$css .= $selector . ' .caption-center.ouacfg-slider-caption{align-items: center; height: 100%; top: 0;}';
				}

				if( isset( $original[$prefix . '_caption_position'] ) && $original[$prefix . '_caption_position'] == 'bottom' ) {
					$css .= $selector . ' .caption-bottom.ouacfg-slider-caption{align-items: flex-end; bottom: 0;}';
				}
			}

			$css .= $selector . ' .ouacfg-caption-title, '. $selector . ' .ouacfg-caption-text{width: 100%; line-height: 1.35;}';
		}

		if( isset($original[$prefix . '_ouacfg_sldtype'] ) && $original[$prefix . '_ouacfg_sldtype']== 'slideshow' )
		{
			if ( ! isset( $original[$prefix . '_thumb_position'] ) || ( isset( $original[$prefix . '_thumb_position'] ) && 'below' == $original[$prefix . '_thumb_position'] ) ) {
				$css .= $selector . ' .ouacfg-slider-slideshow {margin-bottom:'. (isset( $original[$prefix . '_ouacfg_sldgap'] ) ? $original[$prefix . '_ouacfg_sldgap'] : '15') .'px}';

				if( isset( $original[$prefix . '_ouacfg_sldgap_tablet'] ) ) {
					$css .= '@media only screen and (max-width: '. oxygen_vsb_get_breakpoint_width('tablet'). 'px){' . $selector . ' .ouacfg-slider-slideshow {margin-bottom:'.$original[$prefix . '_ouacfg_sldgap_tablet'].'px}}';
				}

				if( isset( $original[$prefix . '_ouacfg_sldgap_landscape'] ) ) {
					$css .= '@media only screen and (max-width: '. oxygen_vsb_get_breakpoint_width('phone-landscape'). 'px){' . $selector . ' .ouacfg-slider-slideshow {margin-bottom:'.$original[$prefix . '_ouacfg_sldgap_landscape'].'px}}';
				}

				if( isset( $original[$prefix . '_ouacfg_sldgap_portrait'] ) ) {
					$css .= '@media only screen and (max-width: '. oxygen_vsb_get_breakpoint_width('phone-portrait'). 'px){' . $selector . ' .ouacfg-slider-slideshow {margin-bottom:'.$original[$prefix . '_ouacfg_sldgap_portrait'].'px}}';
				}
			}

			if ( isset( $original[$prefix . '_thumb_position'] ) && 'above' == $original[$prefix . '_thumb_position']  ) {
				$css .= $selector . ' .ouacfg-slider.ouacfg-slider-slideshow {margin-top:'.$original[$prefix . '_ouacfg_sldgap'].'px}';

				if( isset( $original[$prefix . '_ouacfg_sldgap_tablet'] ) ) {
					$css .= '@media only screen and (max-width: '. oxygen_vsb_get_breakpoint_width('tablet'). 'px){' . $selector . ' .ouacfg-slider-slideshow {margin-top:'.$original[$prefix . '_ouacfg_sldgap_tablet'].'px}}';
				}

				if( isset( $original[$prefix . '_ouacfg_sldgap_landscape'] ) ) {
					$css .= '@media only screen and (max-width: '. oxygen_vsb_get_breakpoint_width('phone-landscape'). 'px){' . $selector . ' .ouacfg-slider-slideshow {margin-top:'.$original[$prefix . '_ouacfg_sldgap_landscape'].'px}}';
				}

				if( isset( $original[$prefix . '_ouacfg_sldgap_portrait'] ) ) {
					$css .= '@media only screen and (max-width: '. oxygen_vsb_get_breakpoint_width('phone-portrait'). 'px){' . $selector . ' .ouacfg-slider-slideshow {margin-top:'.$original[$prefix . '_ouacfg_sldgap_portrait'].'px}}';
				}
			}

			if( isset($original[$prefix . '_hide_thumbs_mobile']) && $original[$prefix . '_hide_thumbs_mobile'] == 'yes' ) {
				$thumbsBP = isset($original[$prefix . '_thumb_rsp_breakpoint']) ? $original[$prefix . '_thumb_rsp_breakpoint'] : 768;
				$css .= '@media only screen and (max-width: '. absint($thumbsBP) . 'px){' . $selector . ' .ou-thumbnails-swiper{display:none}}';
			}

			$css .= $selector . ' .ouacfg-slider-thumb{transform: scale(var(--thumb-initial-scale));}';
			$css .= $selector . ' .swiper-slide-active .ouacfg-slider-thumb, '. $selector . ' .ouacfg-slider-thumb:hover{transform: scale(var(--thumb-hover-scale));}';
		}

		if( isset($original[$prefix . '_slider_navigation']) && $original[$prefix . '_slider_navigation'] == 'yes' )
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

		return $css;
	}

	function init() {
		$this->El->useAJAXControls();
		if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'ougsld_enqueue_scripts' ) );
		}
	}

	function ougsld_enqueue_scripts() {
		global $ouwoo_constant;

		if( empty( $ouwoo_constant ) || ! $ouwoo_constant['swiper_css'] ) {
			wp_enqueue_style('ou-swiper-style');
			wp_enqueue_script('ou-swiper-script');
		}

		wp_enqueue_script('ouacfg-slider-script');
	}

	function ou_applyRandomOrder( $images ) {
		
		if( is_array( $images ) ) {
			$new_images = array();
			$keys = array_keys( $images );
			shuffle($keys);

			foreach ($keys as $key) {
				$new_images[$key] = $images[$key];
			}

			return $new_images;
		}

		return $images;

	}

	function ougsld_get_attachment_data( $id ) {
		$data = wp_prepare_attachment_for_js( $id );

		if ( gettype( $data ) == 'array' ) {
			return json_decode( json_encode( $data ) );
		}

		return $data;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUGallerySlider();