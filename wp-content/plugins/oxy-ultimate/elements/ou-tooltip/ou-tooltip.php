<?php

class OUToolTip extends OxyUltimateEl {
	public $css_added = false;
	public $js_added = false;

	function name() {
		return __( "Tooltip", "oxy-ultimate" );
	}

	function slug() {
		return "ou_tooltip";
	}

	function oxyu_button_place() {
		return "content";
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();

		/*if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'ou_tooltip_enqueue_scripts' ) );
		}*/
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function tooltipsSettings() {

		$effect = $this->addControlSection( 'tooltip_effect', __('Popup Config'), 'assets/icon.png', $this );

		$effect->addOptionControl([
			'type' 			=> 'textfield',
			'name' 			=> __('Enter Trigger Selector'),
			'slug' 			=> 'tooltip_trigger_selector'
		])->setParam('description', __('Enter CSS Classname or ID. eg. .ct_div_block or #-section-12-112'));

		$effect->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show On'),
			'slug' 		=> 'tooltip_trigger',
			'value' 	=> [ 'hover' => __( 'Hover' ), 'click' => __( 'Click' ) ],
			'default' 	=> 'hover'
		]);

		$effect->addOptionControl([
			'type' 		=> 'dropdown',
			'name' 		=> __('Popup Placement'),
			'slug' 		=> 'tooltip_placement',
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
			'default' 	=> 'top'
		]);

		$effect->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Z-Index'),
			'slug' 		=> 'tooltip_zindex',
			'default' 	=> 9999
		]);

		$arrow = $effect->addControlSection( 'tooltip_arrow', __('Arrow'), 'assets/icon.png', $this );
		$arrow->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Disable Arrow'),
			'slug' 		=> 'tooltip_arrow',
			'value' 	=> [
				'no' 		=> __( 'No' ), 
				'yes' 		=> __( 'Yes' )
			],
			'default' 	=> 'no'
		]);

		$arrow->addStyleControl([
			'name' 			=> 'Arrow Color',
			'selector' 		=> '.tippy-arrow',
			'property' 		=> 'color',
			'default' 		=> '#333333',
			'condition' 	=> 'tooltip_arrow=no'
		]);

		$animation = $effect->addControlSection( 'popup_animation', __('Animation'), 'assets/icon.png', $this );
		$animation->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Animation'),
			'slug' 		=> 'tooltip_animation',
			'value' 	=> [
				'fade' 			=> __( 'Fade' ), 
				'shift-away' 	=> __( 'Shift Away' ), 
				'shift-toward' 	=> __( 'Shift Toward' ), 
				'perspective' 	=> __( 'Perspective' )
			],
			'default' 	=> 'fade'
		]);

		$animation->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Offset'),
			'slug' 		=> 'tooltip_offset'
		])->setRange(0,100,5)->setUnits(' ', ' ')->setDefaultValue(0);

		$animation->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Distance'),
			'slug' 		=> 'tooltip_distance'
		])->setRange(0,100,5)->setUnits(' ', ' ')->setDefaultValue(10);

		$animation->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Delay Start'),
			'slug' 		=> 'tooltip_delay_start'
		])->setRange(0,1000,10)->setUnits('ms', 'ms')->setDefaultValue(0);

		$animation->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Delay End'),
			'slug' 		=> 'tooltip_delay_end'
		])->setRange(0,1000,10)->setUnits('ms', 'ms')->setDefaultValue(0);


		$style = $this->addControlSection( 'tooltip_style', __('Popup Style'), 'assets/icon.png', $this );

		$tooltipSize = $style->addControlSection( 'tooltip_size', __('Width & Color'), 'assets/icon.png', $this );
		$tooltipSize->addStyleControl([
			'selector' 		=> '.tooltip-inner-wrap, .tippy-box',
			'property' 		=> 'width',
			'slug' 			=> 'tippy_box_width',
			'control_type' 	=> 'slider-measurebox'
		])->setRange(0,6000,10)->setUnits('px', 'px,%,vw')->setDefaultValue(200);

		$tooltipSize->addStyleControl([
			'selector' 		=> '.tooltip-inner-wrap, .tippy-box',
			'property' 		=> 'background-color',
			'default' 		=> '#333333'
		]);

		$tooltipSpacing = $style->addControlSection( 'tooltip_spacing', __('Spacing'), 'assets/icon.png', $this );
		$tooltipSpacing->addPreset(
			"padding",
			"ttip_padding",
			__("Padding", "oxy-ultimate"),
			'.tooltip-inner-wrap, .tippy-box'
		)->whiteList();


		$style->borderSection( __('Border'), '.tooltip-inner-wrap, .tippy-box', $this );
		$style->boxShadowSection( __('Box Shadow'), '.tooltip-inner-wrap, .tippy-box', $this );


		$layout = $this->addControlSection( 'tooltip_child', __('Inner Content'), 'assets/icon.png', $this );
		$layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.tooltip-content-toggle,.tippy-box .tippy-content',
			'property' 			=> 'display',
			'value' 			=> ['flex' => 'flex', 'inline-flex' => 'inline-flex'],
			'default' 			=> 'flex'
		]);
		$layout->flex('.tooltip-content-toggle,.tippy-box .tippy-content', $this);
	}

	function controls() {
		$preview = $this->El->addControl('buttons-list', 'tooltip_preview', __('Visibility on Builder Editor'));
		$preview->setValue(['Yes', 'No']);
		$preview->setValueCSS(['No' => '.tooltip-content-toggle{display: none;}']);
		$preview->setDefaultValue('Yes');
		$preview->setParam('description', 'Live preview is not available on builder editor.');
		$preview->whiteList();

		$this->tooltipsSettings();
	}

	function render( $options, $defaults, $content ) {
		$dataAttr = ' data-outooltip-trigger-selector="' . ( isset( $options['tooltip_trigger_selector'] ) ? $options['tooltip_trigger_selector'] : '' ) . '"';
		$dataAttr .= ' data-placement="' . $options['tooltip_placement'] . '"';
		$dataAttr .= ' data-animation="' . $options['tooltip_animation'] . '"';
		$dataAttr .= ' data-outooltip-trigger-on="' . ( ( $options['tooltip_trigger'] == 'hover' ) ? 'mouseenter focus' : 'click' ) . '"';
		$dataAttr .= ' data-arrow="' . $options['tooltip_arrow'] . '"';
		$dataAttr .= ' data-zindex="' . ( isset( $options['tooltip_zindex'] ) ? $options['tooltip_zindex'] : 9999 ) . '"';
		$dataAttr .= ' data-offset="' . ( isset( $options['tooltip_offset'] ) ? $options['tooltip_offset'] : 0 ) . '"';
		$dataAttr .= ' data-distance="' . ( isset( $options['tooltip_distance'] ) ? $options['tooltip_distance'] : 10 ) . '"';
		$dataAttr .= ' data-delaystart="' . ( isset( $options['tooltip_delay_start'] ) ? $options['tooltip_delay_start'] : 0 ) . '"';
		$dataAttr .= ' data-delayend="' . ( isset( $options['tooltip_delay_end'] ) ? $options['tooltip_delay_end'] : 0 ) . '"';

		$class = '';
		if( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) {
			$class = ' tooltip-content-toggle';
		}

		echo '<div class="oxy-inner-content tooltip-inner-wrap'.$class.'"'. $dataAttr .'>';
		
		if( $content ) {
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
		} else {
			echo 'Content of nest component(s) will show in tooltip. Add the component(s) inside the Tooltip component.';
		}
		
		echo '</div>';

		if( ! $this->js_added && !isset( $_GET['oxygen_iframe'] ) && !defined('OXY_ELEMENTS_API_AJAX')) { 
			add_action('wp_footer', array($this, 'ou_tooltip_enqueue_scripts') );
			$this->js_added = true;
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if ( ! $this->css_added ){
			$css .= 'body:not(.oxygen-builder-body) .oxy-ou-tooltip > .tooltip-inner-wrap{display:none;}
					.oxy-ou-tooltip .tooltip-inner-wrap,
					.oxy-ou-tooltip .tippy-box {
						background-color: #333;
						border-radius: 5px;
						color: #fff;
						padding: 5px 9px;
						width: 200px;
					}
					.oxy-ou-tooltip .tippy-content {
						padding: 0;
					}
					.oxy-ou-tooltip .tooltip-content-toggle,
					.oxy-ou-tooltip .tippy-content {
						display: flex;
						flex-direction: column;
						width: 100%;
					}
					.tippy-box[data-animation=shift-away][data-state=hidden]{opacity:0}.tippy-box[data-animation=shift-away][data-state=hidden][data-placement^=top]{transform:translateY(10px)}.tippy-box[data-animation=shift-away][data-state=hidden][data-placement^=bottom]{transform:translateY(-10px)}.tippy-box[data-animation=shift-away][data-state=hidden][data-placement^=left]{transform:translateX(10px)}.tippy-box[data-animation=shift-away][data-state=hidden][data-placement^=right]{transform:translateX(-10px)}
					.tippy-box[data-animation=scale][data-placement^=top]{transform-origin:bottom}.tippy-box[data-animation=scale][data-placement^=bottom]{transform-origin:top}.tippy-box[data-animation=scale][data-placement^=left]{transform-origin:right}.tippy-box[data-animation=scale][data-placement^=right]{transform-origin:left}.tippy-box[data-animation=scale][data-state=hidden]{transform:scale(.5);opacity:0}
					.tippy-box[data-animation=shift-toward][data-state=hidden]{opacity:0}.tippy-box[data-animation=shift-toward][data-state=hidden][data-placement^=top]{transform:translateY(-10px)}.tippy-box[data-animation=shift-toward][data-state=hidden][data-placement^=bottom]{transform:translateY(10px)}.tippy-box[data-animation=shift-toward][data-state=hidden][data-placement^=left]{transform:translateX(-10px)}.tippy-box[data-animation=shift-toward][data-state=hidden][data-placement^=right]{transform:translateX(10px)}
					.tippy-box[data-animation=perspective][data-placement^=top]{transform-origin:bottom}.tippy-box[data-animation=perspective][data-placement^=top][data-state=visible]{transform:perspective(700px)}.tippy-box[data-animation=perspective][data-placement^=top][data-state=hidden]{transform:perspective(700px) translateY(8px) rotateX(60deg)}.tippy-box[data-animation=perspective][data-placement^=bottom]{transform-origin:top}.tippy-box[data-animation=perspective][data-placement^=bottom][data-state=visible]{transform:perspective(700px)}.tippy-box[data-animation=perspective][data-placement^=bottom][data-state=hidden]{transform:perspective(700px) translateY(-8px) rotateX(-60deg)}.tippy-box[data-animation=perspective][data-placement^=left]{transform-origin:right}.tippy-box[data-animation=perspective][data-placement^=left][data-state=visible]{transform:perspective(700px)}.tippy-box[data-animation=perspective][data-placement^=left][data-state=hidden]{transform:perspective(700px) translateX(8px) rotateY(-60deg)}.tippy-box[data-animation=perspective][data-placement^=right]{transform-origin:left}.tippy-box[data-animation=perspective][data-placement^=right][data-state=visible]{transform:perspective(700px)}.tippy-box[data-animation=perspective][data-placement^=right][data-state=hidden]{transform:perspective(700px) translateX(-8px) rotateY(60deg)}.tippy-box[data-animation=perspective][data-state=hidden]{opacity:0}';

			$this->css_added = true;
		}

		return $css;
	}

	function ou_tooltip_enqueue_scripts() { ?>
		<script src="<?php echo OXYU_URL;?>assets/js/popper.min.js"></script>
		<script src="<?php echo OXYU_URL;?>assets/js/tippy-bundle.umd.min.js"></script>
		<?php if( ! isset($_GET['oxygen_iframe']) && ! defined('OXY_ELEMENTS_API_AJAX') ): ?>
			<script type="text/javascript">
				jQuery(window).ready(function($){
					$(".tooltip-inner-wrap").each(function(e){
						tippy($(this).attr("data-outooltip-trigger-selector"), {
							content: $(this).html(),
							trigger: $(this).attr("data-outooltip-trigger-on"),
							maxWidth: 'none',
							allowHTML: true,
							interactive: true,
							appendTo: () => document.getElementById($(this).closest('.oxy-ou-tooltip').attr('id')),
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

new OUToolTip();