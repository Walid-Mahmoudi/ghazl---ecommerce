<?php

namespace Oxygen\OxyUltimate;

Class OUHighlightedHeading extends \OxyUltimateEl {
	public $has_js = true;
	public $css_added = false;
	public $js_added = false;
	private $hlh_js_code = array();

	function name() {
		return __( "Highlighted Heading", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_hl_heading";
	}

	function oxyu_button_place() {
		return "text";
	}

	function tag() {
		$tags = array('default' => 'h2', 'choices' => 'h1,h2,h3,h4,h5,h6,div,p' );
		return $tags;
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if changes are not displaying properly on Builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$this->addTagControl();

		$hlhbt = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Before Text', "oxy-ultimate"),
				"slug" 		=> 'ouhlh_before_text',
				"base64" 	=> true
			)
		);
		$hlhbt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicHLBText">data</div>');
		$hlhbt->setParam("description", __("This text will be placed before the highlighted text.", "oxy-ultimate"));

		$hlhanmt = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Highlighted Text', "oxy-ultimate"),
				"slug" 		=> 'ouhlh_hl_text',
				"value" 	=> __("Highlighted", "oxy-ultimate"),
				"base64" 	=> true
			)
		);
		$hlhanmt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicHLHText">data</div>');

		$hlhaft = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('After Text', "oxy-ultimate"),
				"slug" 		=> 'ouhlh_after_text',
				"base64" 	=> true
			)
		);
		$hlhaft->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicHLAText">data</div>');
		$hlhaft->setParam("description", __("This text will be placed after the highlighted text.", "oxy-ultimate"));

		$shape_section = $this->addControlSection( "shape_section", __("Shape", "oxy-ultimate"), "assets/icon.png", $this );
		$shape_section->addOptionControl(
			array(
				"type" 		=> 'dropdown',
				"name" 		=> __('Type', "oxy-ultimate"),
				"slug" 		=> 'ouhlh_shape',
				"value" 	=> array(
					'circle'			=> __('Circle', 'oxy-ultimate'),
					'curly'				=> __('Curly', 'oxy-ultimate'),
					'diagonal'			=> __('Diagonal', 'oxy-ultimate'),
					'double'			=> __('Double Underline', 'oxy-ultimate'),
					'doubleub'			=> __('Double Underline Bottom', 'oxy-ultimate'),
					'strikethrough'		=> __('Strikethrough', 'oxy-ultimate'),
					'underline'			=> __('Underline', 'oxy-ultimate'),
					'underline_zigzag'	=> __('Underline Zigzag', 'oxy-ultimate'),
					'underline_lr' 		=> __('Underline-Left to Right', 'oxy-ultimate'),
					'underline_rl' 		=> __('Underline-Right to Left', 'oxy-ultimate'),
					'underline_outwards'=> __('Underline-Outwards', 'oxy-ultimate'),
					'underline_inwards'	=> __('Underline-Inwards', 'oxy-ultimate'),
				),
				"default" 	=> 'circle'
			)
		)->rebuildElementOnChange();

		$shape_section->addOptionControl(
			array(
				"type" 		=> 'radio',
				"name" 		=> __('Loop', "oxy-ultimate"),
				"slug" 		=> 'ouhlh_shape_loop',
				"value" 	=> array(
					'ouhlh-headline-loop'		=> __('Yes', 'oxy-ultimate'),
					'ouhlh-headline-noloop'		=> __('No', 'oxy-ultimate')
				),
				"default" 	=> 'ouhlh-headline-loop',
				"condition" => 'ouhlh_shape!=underline_lr&&ouhlh_shape!=underline_rl&&ouhlh_shape!=underline_outwards&&ouhlh_shape!=underline_inwards'
			)
		);

		$shapeAnim = $shape_section->addOptionControl(
			array(
				"name" 			=> __('Start Animation After Scroll', "oxy-ultimate"),
				"slug" 			=> "ouhlh_onscroll",
				"type" 			=> 'radio',
				"value" 		=> ["no" => __("No"), "yes" => __("Yes")],
				"default"		=> "no",
				"condition" 	=> 'ouhlh_shape!=underline_lr&&ouhlh_shape!=underline_rl&&ouhlh_shape!=underline_outwards&&ouhlh_shape!=underline_inwards'
			)
		);
		$shapeAnim->setParam('description', __("Enable it when you are adding the Animated Heading below the fold. Preview will not show on builder editor.", "oxy-ultimate"));
		$shapeAnim->rebuildElementOnChange();

		$offset = $shape_section->addOptionControl(
			array(
				"type" 		=> 'slider-measurebox',
				"name" 		=> __('Offset', "oxy-ultimate"),
				"slug" 		=> 'ouhlh_wayoffset',
				"condition" => 'ouhlh_shape!=underline_lr&&ouhlh_shape!=underline_rl&&ouhlh_shape!=underline_outwards&&ouhlh_shape!=underline_inwards&&ouhlh_onscroll!=no'
			)
		);
		$offset->setRange('0', '100', '10');
		$offset->setDefaultValue("65");
		$offset->setUnits("%", "%");
		$offset->rebuildElementOnChange();

		$shape_section->addOptionControl(
			array(
				"type" 		=> 'radio',
				"name" 		=> __('Appearance', "oxy-ultimate"),
				"slug" 		=> 'ouhlhul_shapeapp',
				"value" 	=> array(
					'onhover'		=> __('Show on Hover', 'oxy-ultimate'),
					'offhover'		=> __('Hide on Hover', 'oxy-ultimate')
				),
				"default" 	=> 'onhover',
				"condition" => 'ouhlh_shape=underline_lr||ouhlh_shape=underline_rl||ouhlh_shape=underline_outwards||ouhlh_shape=underline_inwards'
			)
		)->rebuildElementOnChange();

		$shape_section->addStyleControls(
			array(
				array(
					"control_type" 	=> 'colorpicker',
					"name" 			=> __('Color'),
					"property" 		=> 'stroke',
					"selector" 		=> '.highlighted-text-wrapper svg path',
					"condition" => 'ouhlh_shape!=underline_lr&&ouhlh_shape!=underline_rl&&ouhlh_shape!=underline_outwards&&ouhlh_shape!=underline_inwards'
				),
				array(
					"control_type" 	=> 'slider-measurebox',
					"name" 			=> __('Width'),
					"property" 		=> 'stroke-width',
					"selector" 		=> '.highlighted-text-wrapper svg path',
					"default" 		=> 9,
					"units" 		=> ' ',
					"condition" => 'ouhlh_shape!=underline_lr&&ouhlh_shape!=underline_rl&&ouhlh_shape!=underline_outwards&&ouhlh_shape!=underline_inwards'
				),
				array(
					"control_type" 	=> 'colorpicker',
					"name" 			=> __('Color'),
					"property" 		=> 'background-color',
					"slug" 			=> "ul_bgc",
					"selector" 		=> '.highlighted-text-wrapper .ul-anim:before,.highlighted-text-wrapper .ul-anim:after',
					"condition" => 'ouhlh_shape=underline_lr||ouhlh_shape=underline_rl||ouhlh_shape=underline_outwards||ouhlh_shape=underline_inwards'
				),
				array(
					"control_type" 	=> 'slider-measurebox',
					"name" 			=> __('Width'),
					"property" 		=> 'height',
					"slug" 			=> "ul_height",
					"selector" 		=> '.highlighted-text-wrapper .ul-anim:before,.highlighted-text-wrapper .ul-anim:after',
					"default" 		=> '5',
					"units" 		=> 'px',
					"condition" => 'ouhlh_shape=underline_lr||ouhlh_shape=underline_rl||ouhlh_shape=underline_outwards||ouhlh_shape=underline_inwards'
				)
			)
		);

		$shape_section->addStyleControl(
			array(
				"control_type" 	=> 'slider-measurebox',
				"name" 			=> __('Position'),
				"property" 		=> 'bottom',
				"slug" 			=> "ul_pos",
				"selector" 		=> '.highlighted-text-wrapper .ul-anim:before,.highlighted-text-wrapper .ul-anim:after',
				"default" 		=> '-10',
				"condition" => 'ouhlh_shape=underline_lr||ouhlh_shape=underline_rl||ouhlh_shape=underline_outwards||ouhlh_shape=underline_inwards'
			)
		)->setRange('-10', '10', '1')->setUnits("px", "px");

		$shape_section->addStyleControl(
			array(
				"name" 			=> __('Transition Duration', "oxy-ultimate"),
				'property' 		=> 'transition-duration', 
				'selector' 		=> '.highlighted-text-wrapper .ul-anim:before,.highlighted-text-wrapper .ul-anim:after',
				"slug" 			=> "ul_ts",
				"control_type" 	=> 'slider-measurebox',
				"default" 		=> '0.75',
				"condition" => 'ouhlh_shape=underline_lr||ouhlh_shape=underline_rl||ouhlh_shape=underline_outwards||ouhlh_shape=underline_inwards'
			)
		)->setRange('0', '10', '0.1')->setUnits("s", "sec");

		$this->typographySection(__("Text", "oxy-ultimate"), ".headline-text", $this);
		$this->typographySection(__("Animated Text", "oxy-ultimate"), ".highlighted-text", $this);

		$this->addApplyParamsButton();
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

	function render($options, $defaults, $content) {
		$underlineAnim = '';
		if( in_array( $options['ouhlh_shape'], ['underline_lr', 'underline_rl', 'underline_outwards', 'underline_inwards']) )
		{
			$underlineAnim = ' ul-anim ' . $options['ouhlh_shape'] . ' ' . $options['ouhlhul_shapeapp'];
		} else {
			if( ! defined('OXY_ELEMENTS_API_AJAX') && $options['ouhlh_onscroll'] == 'yes' ) {
				wp_enqueue_script('ou-waypoints-script');
				wp_enqueue_script('ou-hlh-script');
				$this->hlh_js_code[] = "jQuery(document).ready(function(){
					jQuery('#{$options['selector']} .highlighted-text').waypoint(function(){
						new OUHighlightedHeading({
							selector: '#{$options['selector']} .highlighted-text', 
							type: '{$options['ouhlh_shape']}'
						});
					}, { offset: '" . $options['ouhlh_wayoffset'] . "%' });
				});";
				
			} elseif( ( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) && $options['ouhlh_onscroll'] != 'yes' ) {
				wp_enqueue_script('ou-hlh-script');
				$this->El->builderInlineJS(
					"jQuery(document).ready(function(){
						new OUHighlightedHeading({
							selector: '#%%ELEMENT_ID%% .highlighted-text', 
							type: '%%ouhlh_shape%%'
						});
					});"
				);
			} else {
				wp_enqueue_script('ou-hlh-script');
				$this->hlh_js_code[] = "jQuery(document).ready(function(){
					new OUHighlightedHeading({
						selector: '#{$options['selector']} .highlighted-text', 
						type: '{$options['ouhlh_shape']}'
					});
				});";
			}

			$this->El->footerJS( join('', $this->hlh_js_code) );
		}
	?>
		<span class="<?php echo $options['ouhlh_shape_loop'];?>" data-type="<?php echo $options['ouhlh_shape'];?>">
			<?php 
				if( isset( $options['ouhlh_before_text'] ) ) : 
					$hlbtext = $this->fetchDynamicData( $options["ouhlh_before_text"] );
			?>
			<span class="headline-text"><?php echo $hlbtext;?></span> 
			<?php endif; ?>
			<?php 
				if( isset( $options['ouhlh_hl_text'] ) ) : 
					$hlhtext = $this->fetchDynamicData( $options["ouhlh_hl_text"] );
			?>
			<span class="highlighted-text-wrapper">
				<span class="highlighted-text<?php echo $underlineAnim; ?>"><?php echo $hlhtext;?></span>
			</span> 
			<?php endif; ?>
			<?php 
				if( isset( $options['ouhlh_after_text'] ) ) : 
					$hlatext = $this->fetchDynamicData( $options["ouhlh_after_text"] );
			?>
				<span class="headline-text"><?php echo $hlatext;?></span>
			<?php endif; ?>
		</span>
	<?php
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$this->css_added = true;
			$css.= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
		}
		return $css;
	}
}

new OUHighlightedHeading();