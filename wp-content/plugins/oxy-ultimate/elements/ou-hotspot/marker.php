<?php

namespace Oxygen\OxyUltimate;

class OUHotspotMarker extends \OxyUltimateEl {
	public $js_added = false;
	public $css_added = false;

	function name() {
		return __( "Add Marker", 'oxy-ultimate' );
	}

	function button_place() {
		return "ouhpmarker::comp";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-add-marker-elements-label"
				ng-if="isActiveName('oxy-add-marker')&&!hasOpenTabs('oxy-add-marker')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-add-marker-elements"
				ng-if="isActiveName('oxy-add-marker')&&!hasOpenTabs('oxy-add-marker')">
				<?php do_action("oxygen_add_plus_ouhpmarker_comp"); ?>
			</div>
		<?php }, 32 );

		if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'ouhp_enqueue_scripts' ) );
		}
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/pin.svg';
	}

	function markerSettings() {
		$marker = $this->addControlSection( 'marker_config', __('Marker Config'), 'assets/icon.png', $this );

		$marker->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Marker Type'),
			'slug' 		=> 'marker_type',
			'value' 	=> ['icon' => __('Icon'), 'image' => __('Image'), 'text' => __('Text')],
			'default' 	=> 'icon'
		]);

		$text = $marker->addCustomControl(
			'<div class="oxygen-input " ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-add_marker_marker_text\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-add_marker_marker_text\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id,\'oxy-ouli_additem\',\'oxy-add_marker_marker_text\');" class="ng-pristine ng-valid ng-touched" ng-keydown="$event.keyCode === 13 && iframeScope.rebuildDOM(iframeScope.component.active.id)">
			</div>',
			'marker_text'
		);
		$text->setParam( 'heading', __('Text') );
		$text->setParam( 'base64', true );
		$text->setParam( 'description', __('You will hit on Enter button to see the text on the Builder Editor. Enter <span style="color:#ff7171;">&amp;apos;</span> for single quote.', "oxy-ultimate") );
		$text->setParam( 'ng_show', "iframeScope.getOption('oxy-add-marker_marker_type')=='text'");

		//$marker_img = $marker->addControl("mediaurl", 'marker_img', __('Upload Image', "oxy-ultimate"));
		$marker_img = $marker->addCustomControl(
			"<div class=\"oxygen-control\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-add_marker_marker_img'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-add_marker_marker_img')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-add_marker_marker_img']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-add_marker_marker_img\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">". __('browse', 'oxygen') ."</div>
				</div>
			</div>",
			'marker_img'
		);
		$marker_img->setParam('heading', __('Upload Image', "oxy-ultimate") );
		$marker_img->setParam('description', 'Click on Apply Params button and image will show on editor.');
		$marker_img->setParam( 'ng_show', "iframeScope.getOption('oxy-add-marker_marker_type')=='image'");

		$imgw = $marker->addStyleControl(
			array(
				'control_type' 	=> 'measurebox',
				'slug' 			=> 'marker_img_width',
				'name' 			=> __('Width', "oxy-ultimate"),
				'selector' 		=> '.marker-image',
				'property' 		=> 'width',
				'condition' 	=> 'marker_type=image'
			)
		);
		$imgw->setUnits('px', 'px');
		$imgw->setParam('hide_wrapper_end', true);

		$imgh = $marker->addStyleControl(
			array(
				'control_type' 	=> 'measurebox',
				'slug' 			=> 'marker_img_height',
				'name' 			=> __('Height', "oxy-ultimate"),
				'selector' 		=> '.marker-image',
				'property' 		=> 'height',
				'condition' 	=> 'marker_type=image'
			)
		);
		$imgh->setUnits('px', 'px');
		$imgh->setParam('hide_wrapper_start', true);

		$icon = $marker->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxy-ultimate"),
				"slug" 			=> 'marker_icon',
				'value' 		=> 'FontAwesomeicon-map-marker',
				'condition' 	=> 'marker_type=icon'
			)
		);
		$icon->setParam('description', __('Click on Apply Params button and get the icon on editor.') );

		$icon_style = $marker->addControlSection( 'marker_icon_style', __('Icon Style'), 'assets/icon.png', $this );

		$icon_style->addStyleControls([
			[
				'name' 			=> __('Size'),
				'selector' 		=> '.hp-marker-inner svg',
				'property' 		=> 'width|height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			],
			[
				'selector' 		=> '.hp-marker-inner svg',
				'property' 		=> 'color'
			],
			[
				'name' 			=> __('Color on Hover'),
				'selector' 		=> ':hover svg',
				'property' 		=> 'color'
			]
		]);

		$tg = $marker->typographySection( __('Text Font'), '.hp-marker-inner', $this );

		$position = $marker->addControlSection( 'marker_position', __('Position'), 'assets/icon.png', $this );

		$position->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> 'top',
			'control_type' 	=> 'slider-measurebox',
			'unit' 			=> '%',
			'value' 		=> 2,
			'default' 		=> 2,
		]);

		$position->addStyleControl([
			'selector' 		=> ' ',
			'property' 		=> 'left',
			'control_type' 	=> 'slider-measurebox',
			'value' 		=> 2,
			'default' 		=> 2,
			'unit' 			=> '%'
		]);


		$position->addStyleControl([
			'selector' 	=> ' ',
			'property' 	=> 'z-index',
		]);


		$style = $marker->addControlSection( 'marker_btn_size', __('Marker Style'), 'assets/icon.png', $this );

		$style->addStyleControls([
			array(
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'height',
				'control_type' 	=> 'slider-measurebox',
				'unit' 			=> 'px'
			),
			array(
				'selector' 	=> '.hp-marker-inner, .marker-ripple-effect:before',
				'property' 	=> 'background-color'
			),
			array(
				'name' 		=> 'Background Color on Hover',
				'selector' 	=> '.hp-marker-inner:hover, .marker-ripple-effect:hover:before',
				'property' 	=> 'background-color'
			)
		]);

		$ripple = $style->addControl('buttons-list', 'marker_ripple', __('Ripple Effect'));
		$ripple->setValue(['Yes', 'No']);
		$ripple->setValueCSS(['No' => '.marker-ripple-effect:before{content: normal;}']);
		$ripple->setDefaultValue('Yes');
		$ripple->whiteList();

		$style->addStyleControl([
			'control_type' 		=> 'slider-measurebox',
			'name' 				=> __('Animation Duration'),
			'selector' 			=> '.marker-ripple-effect:before',
			'property' 			=> 'animation-duration'
		])->setRange(0,10,0.1)->setUnits('s', 'sec')->setDefaultValue(2);

		$disbaleRipple = $style->addControl('buttons-list', 'marker_ripple_disable', __('Disable Ripple Effect on Hover'));
		$disbaleRipple->setValue(['Yes', 'No']);
		$disbaleRipple->setValueCSS(['Yes' => '.marker-ripple-effect:hover:before{content: normal!important;}']);
		$disbaleRipple->setDefaultValue('No');
		$disbaleRipple->whiteList();
		

		$padding = $marker->addControlSection( 'marker_padding', __('Padding'), 'assets/icon.png', $this );

		$padding->addStyleControl(
			array(
				"name"			=> __('Top'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'padding-top',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_end', true);

		$padding->addStyleControl(
			array(
				"name"			=> __('Right'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'padding-right',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_start', true);

		$padding->addStyleControl(
			array(
				"name"			=> __('Bottom'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'padding-bottom',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_end', true);

		$padding->addStyleControl(
			array(
				"name"			=> __('Left'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'padding-left',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_start', true);


		$margin = $marker->addControlSection( 'marker_margin', __('Margin'), 'assets/icon.png', $this );

		$margin->addStyleControl(
			array(
				"name"			=> __('Top'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'margin-top',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_end', true);

		$margin->addStyleControl(
			array(
				"name"			=> __('Right'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'margin-right',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_start', true);

		$margin->addStyleControl(
			array(
				"name"			=> __('Bottom'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'margin-bottom',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_end', true);

		$margin->addStyleControl(
			array(
				"name"			=> __('Left'),
				'selector' 		=> '.hp-marker-inner',
				'property' 		=> 'margin-left',
				"control_type" 	=> 'measurebox'
			)
		)->setUnits('px','px')->setRange(0,100,5)->setParam('hide_wrapper_start', true);

		$marker->borderSection( __('Border'), '.hp-marker-inner, .marker-ripple-effect:before', $this );
		$marker->borderSection( __('Hover Border'), '.hp-marker-inner:hover, .marker-ripple-effect:hover:before', $this );
		$marker->boxShadowSection( __('Box Shadow'), '.hp-marker-inner', $this );
		$marker->boxShadowSection( __('Hover Box Shadow'), '.hp-marker-inner:hover', $this );
	}

	function tooltipsSettings() {
		$popup = $this->addControlSection( 'popup_config', __('Popup Config'), 'assets/icon.png', $this );

		$preview = $popup->addControl('buttons-list', 'popup_preview', __('Edit Popup Content on Builder Editor'));
		$preview->setValue(['Yes', 'No']);
		$preview->setValueCSS(['No' => '.tooltip-content-toggle{display: none;}']);
		$preview->setDefaultValue('Yes');
		$preview->whiteList();

		$popup->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Trigger'),
			'slug' 		=> 'popup_trigger',
			'value' 	=> [ 'hover' => __( 'Hover' ), 'click' => __( 'Click' ) ],
			'default' 	=> 'hover'
		]);

		$popup->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Arrow'),
			'slug' 		=> 'popup_arrow',
			'value' 	=> [
				'no' 		=> __( 'No' ), 
				'yes' 		=> __( 'Yes' )
			],
			'default' 	=> 'no'
		]);

		$popup->addStyleControl([
			'name' 			=> 'Arrow Color',
			'selector' 		=> '.tippy-arrow',
			'property' 		=> 'color',
			'default' 		=> '#333333',
			'condition' 	=> 'popup_arrow=no'
		]);

		$popup->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Placement'),
			'slug' 		=> 'popup_placement',
			'value' 	=> [
				'top' 			=> __( 'Top' ), 
				'top-start' 	=> __( 'Top Start' ), 
				'top-end' 		=> __( 'Top End' ),
				'right' 		=> __( 'Right' ), 
				'right-start' 	=> __( 'Right Start' ), 
				'right-end' 	=> __( 'Right End' ),
				'bottom' 		=> __( 'Bottom' ), 
				'bottom-start' 	=> __( 'Bottom Start' ), 
				'bottom-end' 	=> __( 'Bottom End' ),
				'left' 			=> __( 'Left' ), 
				'left-start' 	=> __( 'Left Start' ), 
				'left-end' 		=> __( 'Left End' ), 
				'auto' 			=> __( 'Auto' ), 
				'auto-start' 	=> __( 'Auto Start' ), 
				'auto-end' 		=> __( 'Auto End' ),
			],
			'default' 	=> 'auto'
		]);

		$tooltipSize = $popup->addControlSection( 'popup_size', __('Width & Color'), 'assets/icon.png', $this );
		$tooltipSize->addStyleControl([
			'selector' 		=> '.ou-tooltip-content-wrap, .tippy-box',
			'property' 		=> 'width',
			'slug' 			=> 'tippy_box_width',
			'control_type' 	=> 'slider-measurebox'
		])->setRange(0,600,10)->setUnits('px', 'px,%,vw')->setDefaultValue(250);

		$tooltipSize->addStyleControl([
			'selector' 		=> '.ou-tooltip-content-wrap, .tippy-box',
			'property' 		=> 'background-color',
			'default' 		=> '#333333'
		]);

		$tooltipSpacing = $popup->addControlSection( 'popup_spacing', __('Spacing'), 'assets/icon.png', $this );
		$tooltipSpacing->addPreset(
			"padding",
			"ttip_padding",
			__("Padding", "oxy-ultimate"),
			'.ou-tooltip-content-wrap, .tippy-box'
		)->whiteList();

		$popup->borderSection( __('Border'), '.ou-tooltip-content-wrap, .tippy-box', $this );
		$popup->boxShadowSection( __('Box Shadow'), '.ou-tooltip-content-wrap, .tippy-box', $this );

		$layout = $popup->addControlSection( 'popup_child', __('Content Layout'), 'assets/icon.png', $this );
		$layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.ou-tooltip-content',
			'property' 			=> 'display',
			'value' 			=> ['flex' => 'flex', 'inline-flex' => 'inline-flex'],
			'default' 			=> 'flex'
		]);
		$layout->flex('.ou-tooltip-content', $this);



		$effect = $popup->addControlSection( 'popup_effect', __('Animation'), 'assets/icon.png', $this );
		$effect->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Animation'),
			'slug' 		=> 'popup_animation',
			'value' 	=> [
				'fade' 			=> __( 'Fade' ), 
				'shift-away' 	=> __( 'Shift Away' ), 
				'shift-toward' 	=> __( 'Shift Toward' ), 
				'perspective' 	=> __( 'Perspective' )
			],
			'default' 	=> 'fade'
		]);

		$effect->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Offset'),
			'slug' 		=> 'popup_offset'
		])->setRange(0,100,5)->setUnits(' ', ' ')->setDefaultValue(0);

		$effect->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Distance'),
			'slug' 		=> 'popup_distance'
		])->setRange(0,100,5)->setUnits(' ', ' ')->setDefaultValue(10);

		$effect->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Delay Start'),
			'slug' 		=> 'popup_delay_start'
		])->setRange(0,1000,10)->setUnits('ms', 'ms')->setDefaultValue(0);

		$effect->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Delay End'),
			'slug' 		=> 'popup_delay_end'
		])->setRange(0,1000,10)->setUnits('ms', 'ms')->setDefaultValue(0);

		$effect->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Z-Index'),
			'slug' 		=> 'popup_zindex',
			'default' 	=> 9999
		]);
	}

	function controls() {

		$preview = $this->El->addControl('buttons-list', 'popup_preview', __('Edit Popup Content on Builder Editor'));
		$preview->setValue(['Yes', 'No']);
		$preview->setValueCSS(['No' => '.tooltip-content-toggle{display: none;}']);
		$preview->setDefaultValue('Yes');
		$preview->whiteList();

		$instruction = $this->addControlSection('popup_instruction', __('Instruction'), 'assets/icon.png', $this );
		$instruction->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">
				<ul>
					<li style="margin: 0 0 6px;">Select "Yes" option and click on Apply Params button. Therefore tooltip/popup animation will be disabled. It would be good practice when you will edit the tooltip/popup content.</li>
					<li>After finishing the work you will select "No" option and click on Apply Params button. Live preview for tooltip/popup effect of this hotspot button will show on builder editor.</li>
				</ul>
			</div>'), 
			'preview_instruction'
		)->setParam('heading', 'Popup Enable/Disable Instruction');

		$this->markerSettings();

		$this->tooltipsSettings();
	}

	function render( $options, $default, $content ) {
		$dataAttr = ' data-selector="' . $options['selector'] .'" data-tpwidth="' . $options['tippy_box_width'] .'"';
		$dataAttr .= ' data-placement="' . $options['popup_placement'] . '"';
		$dataAttr .= ' data-animation="' . $options['popup_animation'] . '"';
		$dataAttr .= ' data-hptrigger="' . ( ( $options['popup_trigger'] == 'hover' ) ? 'mouseenter focus' : 'click' ) . '"';
		$dataAttr .= ' data-arrow="' . $options['popup_arrow'] . '"';
		$dataAttr .= ' data-zindex="' . ( isset( $options['popup_zindex'] ) ? $options['popup_zindex'] : 9999 ) . '"';
		$dataAttr .= ' data-offset="' . ( isset( $options['popup_offset'] ) ? $options['popup_offset'] : 0 ) . '"';
		$dataAttr .= ' data-distance="' . ( isset( $options['popup_distance'] ) ? $options['popup_distance'] : 10 ) . '"';
		$dataAttr .= ' data-delaystart="' . ( isset( $options['popup_delay_start'] ) ? $options['popup_delay_start'] : 0 ) . '"';
		$dataAttr .= ' data-delayend="' . ( isset( $options['popup_delay_end'] ) ? $options['popup_delay_end'] : 0 ) . '"';

		echo '<div class="hp-marker-inner tippy-selector'. $options['selector'] .' marker-ripple-effect marker-type-' . $options['marker_type'] . '"'. $dataAttr .'>';
			
			if( $options['marker_type'] == 'icon' ) {
				global $oxygen_svg_icons_to_load;

				$oxygen_svg_icons_to_load[] = $options['marker_icon'];

				echo '<svg id="' . $options['selector'] . '-marker-icon" class="marker-icon"><use xlink:href="#' . $options['marker_icon'] . '"></use></svg>';
			}

			if( $options['marker_type'] == 'text' ) {
				echo '<span class="marker-text">' . $options['marker_text'] . '</span>';
			}

			if( $options['marker_type'] == 'image' ) {
				$width = isset($options['marker_img_width']) ? ' width="' . $options['marker_img_width'] . '"' : '';
				$height = isset($options['marker_img_height']) ? ' height="' . $options['marker_img_height'] .'"' : '';

				echo '<img src="' . esc_url( $options['marker_img'] ) .'"'. $width . $height .' class="marker-image" />';
			}

		echo '</div>';

		$class = '';
		if( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) {
			$class = ' tooltip-content-toggle tippy-builder-content'. $options['selector'];
		}

		if( $content ) {
			echo '<div class="ou-tooltip-content-wrap'.$class.'"><div class="ou-tooltip-content oxy-inner-content">';
			
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
			
			echo '</div></div>';

			if( ! empty( $class) ) {
				$this->El->builderInlineJS('
					jQuery(document).ready(function($){
						if( $(".tippy-builder-content'. $options['selector']. '").css("display") == "none") {
							setTimeout(function(){
								tippy( ".tippy-selector'. $options['selector']. '", {
									content: $(".tippy-builder-content'. $options['selector']. '").html(),
									trigger: "'. ( ( $options['popup_trigger'] == 'hover' ) ? 'mouseenter focus' : 'click' ) .'",
									maxWidth: "none",
									allowHTML: true,
									interactive: true,
									animation: "'. $options['popup_animation'] .'",
									delay: ['. intval( $options['popup_delay_start'] ) .','. intval( $options['popup_delay_end'] ) .'],
									offset: ['. intval( $options['popup_offset'] ) .','. intval( $options['popup_distance'] ) .'],
									placement: "'. $options['popup_placement'] .'",
									zIndex: '. intval( $options['popup_zindex'] ) .',
									arrow: '. ( $options['popup_arrow'] == 'no' ? "true" : "false" ) .'
								});
							}, 100 );
						}
					});
				');
			}
		}

		if( ! $this->js_added && ! isset( $_GET['oxygen_iframe'] )) { 
			add_action('wp_footer', array($this, 'ouhp_enqueue_scripts') );
			$this->js_added = true;
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if ( ! $this->css_added ){
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');

			$this->css_added = true;
		}

		return $css;
	}

	function ouhp_enqueue_scripts() {?>
		<script src="<?php echo OXYU_URL;?>assets/js/popper.min.js"></script>
		<script src="<?php echo OXYU_URL;?>assets/js/tippy-bundle.umd.min.js"></script>
		<?php if( ! isset($_GET['oxygen_iframe']) && ! defined('OXY_ELEMENTS_API_AJAX') ): ?>
			<script type="text/javascript">
				jQuery(window).ready(function($){
					$(".hp-marker-inner").each(function(e){
						tippy( ".tippy-selector" + $(this).attr("data-selector"), {
							content: $(this).closest(".oxy-add-marker").find(".ou-tooltip-content-wrap").html(),
							trigger: $(this).attr("data-hptrigger"),
							maxWidth: 'none', //$(this).attr('data-tpwidth')
							allowHTML: true,
							interactive: true,
							animation: $(this).attr("data-animation"),
							delay: [parseInt( $(this).attr("data-delaystart") ), parseInt( $(this).attr("data-delayend") )],
							offset: [parseInt( $(this).attr("data-offset") ), parseInt( $(this).attr("data-distance") )],
							placement: $(this).attr("data-placement"),
							zIndex: parseInt( $(this).attr("data-zindex") ),
							arrow: ( $(this).attr("data-arrow") == 'no' ? true : false )
						});
					});
				});
			</script>
	<?php
		endif;
	}
}

new OUHotspotMarker();