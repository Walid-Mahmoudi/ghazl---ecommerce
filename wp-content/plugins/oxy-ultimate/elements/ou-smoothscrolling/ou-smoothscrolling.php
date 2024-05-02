<?php

namespace Oxygen\OxyUltimate;

class OUSmoothScrolling extends \OxyUltimateEl {
	
	public $has_js = true;
	public $css_added = false;
	public $js_added = false;

	function name() {
		return __( "Smooth Scrolling", 'oxy-ultimate' );
	}

	function oxyu_button_place() {
		return "content";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}

	function controls() {
		$this->addCustomControl( 
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">' . __('Click on <span style="color:#ff7171;">Apply Params</span> buttons, if content is not showing properly on builder editor.') . '</div>', 
			'ss_note'
		)->setParam('heading', 'Note:');

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> '',
			'slug' 		=> 'ouss_type',
			'value' 	=> [ 'backtop' => __('Back To Top'), 'jumpsection' => __('Jump To Section')],
			'default' 	=> 'backtop'
		]);

		$btn = $this->El->addControl('buttons-list', 'hide_btn', __('Hide Back To Top Button for Builder Editor'));
		$btn->setValue(['No', 'Yes']);
		$btn->setValueCSS(['Yes' => '.smoothscrolling-inner-wrap.ss-builder-editor.backtop{opacity: 0;visibility: hidden;}']);
		$btn->setDefaultValue(['No']);
		$btn->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-smooth_scrolling_ouss_type']=='backtop'");
		$btn->whiteList();

		$this->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> __('Enter Target Section Selector'),
			'slug' 			=> 'ss_selector',
			'condition' 	=> 'ouss_type=jumpsection'
		])->setParam('description', __('Enter HTML TAG or CSS Classname or ID. eg. html,body or .ct_div_block or #-section-12-112'));

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Hide on Devices', 'oxy-ultimate'),
			'slug' 		=> 'ss_hidemb',
			'default' 	=> 'no',
			'value' 	=> [
				'no'		=> __('No'),
				'yes' 		=> __('Yes')
			],
			'condition' => 'ouss_type=backtop'
		]);

		$breakpoint = $this->addOptionControl([
			'type' 		=> 'measurebox',
			'name' 		=> __('Breakpoint'),
			'slug' 		=> 'ss_breakpoint',
			'condition' => 'ss_hidemb=yes'
		]);
		$breakpoint->setUnits('px', 'px');
		$breakpoint->setDefaultValue(680);
		$breakpoint->setParam('description', 'Default breakpoint value is 680px.');

		$position = $this->addControlSection('ouss_position', __('Position'), 'assets/icon.png', $this);
		$position->addCustomControl( 
			'<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">This feature will work for Back To Top option only.</div>', 
			'ssbtop_note'
		)->setParam('heading', 'Note:');

		$position->addStyleControl([
			'control_type' 	=> 'radio',
			'selector' 		=> ' ',
			'property' 		=> 'position',
			'value' 		=> ['static' => __('static'), 'relative' => __('relative'), 'absolute' => __('absolute'), 'fixed' => __('fixed')]
		]);

		$position->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> 'top'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em')->setParam('hide_wrapper_end', true);

		$position->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> 'right'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em')->setParam('hide_wrapper_start', true);

		$position->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> 'bottom'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em')->setParam('hide_wrapper_end', true);

		$position->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> 'left'
		])->setRange(0,100,10)->setUnits('px', 'px,%,em')->setParam('hide_wrapper_start', true);

		$position->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> 'z-index'
		]);

		$speed = $this->addControlSection('ouss_speed', __('Transition'), 'assets/icon.png', $this);
		$speed->addOptionControl(
			array(
				"name"			=> __('Offset'),
				"slug" 			=> "scroll_offset",
				"default"		=> "0",
				"type" 			=> 'slider-measurebox',
				'condition' 	=> 'ouss_type=jumpsection'
			)
		)->setUnits('px','px')->setRange(0,1000,10);

		$speed->addOptionControl(
			array(
				"name"			=> __('Scroll Speed'),
				"slug" 			=> "scroll_transition",
				"default"		=> "450",
				"type" 			=> 'slider-measurebox',
			)
		)->setUnits('ms','ms')->setRange(0,1000,10);

		$speed->addOptionControl([
			'type' 			=> 'radio',
			'slug' 			=> 'scroll_easing',
			'name' 			=> 'Easing',
			'value' 		=> ['linear' => __('Linear'), 'swing' => __('Swing')],
			'default' 		=> 'linear'
		]);

		$speed->addOptionControl(
			array(
				"name" 			=> __('Visible after scrolling'),
				"slug" 			=> "ss_visible",
				"default" 		=> "0",
				"type" 			=> 'slider-measurebox',
				'condition' 	=> 'ouss_type=backtop'
			)
		)->setUnits('px','px')->setRange(0,2000,10);

		$speed->addStyleControl(
			array(
				"name"			=> __('Transition Duration for Toggle Effect'),
				'selector' 		=> '.smoothscrolling-inner-wrap',
				"property" 		=> "transition-duration",
				"default"		=> "0.3",
				"control_type" 	=> 'slider-measurebox',
				'condition' 	=> 'ouss_type=backtop'
			)
		)->setUnits('s','sec')->setRange(0,10,.1);

		$style = $this->addControlSection('ouss_style', __('Style'), 'assets/icon.png', $this);
		$size = $style->addControlSection('ouss_size', __('Size'), 'assets/icon.png', $this);
		$size->addStyleControl(
			array(
				"name"			=> __('Width'),
				'property' 		=> 'width',
				'selector' 		=> '.smoothscrolling-inner-wrap',
				"control_type" 	=> 'slider-measurebox'
			)
		)->setUnits('px','px,%,em')->setRange(0,1000,10);

		$size->addStyleControl(
			array(
				"name"			=> __('Height'),
				'property' 		=> 'height|line-height',
				'selector' 		=> '.smoothscrolling-inner-wrap',
				"control_type" 	=> 'slider-measurebox'
			)
		)->setUnits('px','px,%,em')->setRange(0,1000,10);

		$size->addStyleControl([
			'control_type' 	=> 'radio',
			'selector' 		=> '.smoothscrolling-inner-wrap',
			'property' 		=> 'text-align',
			'value' 		=> ['left' => __('Left'), 'center' => __('Center'), 'right' => __('Right')]
		]);

		$color = $style->addControlSection('ouss_color', __('Color'), 'assets/icon.png', $this);
		$color->addStyleControls([
			array(
				'property' 		=> 'background-color',
				'selector' 		=> '.smoothscrolling-inner-wrap'
			),
			array(
				'name' 			=> __('Hover Background Color'),
				'property' 		=> 'background-color',
				'selector' 		=> '.smoothscrolling-inner-wrap:hover'
			),
			array(
				'property' 		=> 'color',
				'selector' 		=> '.smoothscrolling-inner-wrap *'
			),
			array(
				'name' 			=> __('Hover Color'),
				'property' 		=> 'color',
				'selector' 		=> '.smoothscrolling-inner-wrap:hover *'
			)
		]);

		$spacing = $style->addControlSection('ouss_spacing', __('Padding'), 'assets/icon.png', $this);
		$spacing->addPreset(
			"padding",
			"ouss_padding",
			__("Padding"),
			'.smoothscrolling-inner-wrap'
		)->whiteList();

		$style->borderSection(__('Border'), '.smoothscrolling-inner-wrap', $this );
		$style->borderSection(__('Hover Border'), '.smoothscrolling-inner-wrap:hover', $this );
		$style->boxShadowSection(__('Box Shadow'), '.smoothscrolling-inner-wrap', $this );
		$style->boxShadowSection(__('Hover Box Shadow'), '.smoothscrolling-inner-wrap:hover', $this );

		$childElements = $style->addControlSection('ouss_childlayout', __('Child Elements Layout'), 'assets/icon.png', $this);
		$childElements->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.smoothscrolling-inner-wrap',
			'property' 			=> 'display',
			'value' 			=> ['flex' => 'flex', 'inline-flex' => 'inline-flex'],
			'default' 			=> 'flex'
		]);
		$childElements->flex('.smoothscrolling-inner-wrap', $this);
	}

	function render( $options, $defaults, $content ) {
		$sstype = isset($options['ouss_type']) ? $options['ouss_type'] : 'backtop';

		$class = '';
		if( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') )
			$class = ' ss-builder-editor';
		
		$dataAttr = ' data-ss-type="' . $sstype . '"';
		$dataAttr .= ' data-offset="' . $options['scroll_offset'] . '"';
		$dataAttr .= ' data-transition-duration="' . $options['scroll_transition'] . '"';
		$dataAttr .= ' data-transition-easing="' . $options['scroll_easing'] . '"';

		if( $sstype == 'jumpsection' )
			$dataAttr .= ' data-selector="' . $options['ss_selector'] . '"';

		if( $sstype == 'backtop' )
			$dataAttr .= ' data-visibleafter="' . $options['ss_visible'] . '"';
		
		if( function_exists('do_oxygen_elements') )
			$output = do_oxygen_elements( $content );
		else
			$output = do_shortcode( $content );

		echo '<div class="oxy-inner-content smoothscrolling-inner-wrap '. $sstype . $class .'"' . $dataAttr .'>' . $output . '</div>';

		if( ! $this->js_added ) {
			add_action( 'wp_footer', array( $this, 'smooth_scrolling_js') );
		}

	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css = '.oxy-smooth-scrolling{width: auto; min-width: 30px; min-height: 30px; display: inline-flex; cursor: pointer;}
				.smoothscrolling-inner-wrap{display:flex;}
				.smoothscrolling-inner-wrap.backtop{opacity:0;visibility:hidden;transition-property: opacity,visibility;transition-duration: 0.3s;}
				.smoothscrolling-inner-wrap.ss-builder-editor.backtop, .smoothscrolling-inner-wrap.backtop.btn-visible{opacity: 1;visibility: visible;}';

			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();

		if( isset($original[$prefix . '_ss_hidemb']) && $original[$prefix . '_ss_hidemb'] == 'yes' ) {		
			$breakpoint = isset($original[$prefix . '_ss_breakpoint']) ? $original[$prefix . '_ss_breakpoint'] : 680;
			$css .= '@media only screen and (max-width: '. absint($breakpoint) .'px){' . $selector . '{display:none;}}';
		}

		return $css;
	}

	function smooth_scrolling_js() {
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('.oxy-smooth-scrolling').each(function(){
					var sstype = $(this).find('.smoothscrolling-inner-wrap').attr('data-ss-type'),
						offset = $(this).find('.smoothscrolling-inner-wrap').attr('data-offset'),
						scrollDuration = $(this).find('.smoothscrolling-inner-wrap').attr('data-transition-duration'),
						scrollEasing = $(this).find('.smoothscrolling-inner-wrap').attr('data-transition-easing');

					if( sstype == 'jumpsection' ) {
						var selector = $(this).find('.smoothscrolling-inner-wrap').attr('data-selector');
						$(this).click(function(e){
							e.preventDefault();
							$('html, body').animate({scrollTop: $(selector).offset().top + parseInt(offset)},parseInt( scrollDuration ),scrollEasing);
						});
					} else {
						var scrollDown = $(this).find('.smoothscrolling-inner-wrap').attr('data-visibleafter');
						$(this).click(function(e){
							e.preventDefault();
							$('html, body').animate({scrollTop: 0 },parseInt( scrollDuration ), scrollEasing);
						});

						var self = $(this);
						$(window).scroll(function() {
							if( $(this).scrollTop() >= scrollDown ) {
								self.find('.smoothscrolling-inner-wrap').addClass('btn-visible');
							} else {
								self.find('.smoothscrolling-inner-wrap').removeClass('btn-visible');
							}
						});
					}
				});
			});
		</script>

	<?php
	}
}

new OUSmoothScrolling();