<?php

namespace Oxygen\OxyUltimate;

Class OUIconList extends \OxyUltimateEl {
	public $css_added = false;

	function name() {
		return __( "Icon List", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_iconlist";
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
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_iconlist-elements-label"
				ng-if="isActiveName('oxy-ou_iconlist')&&!hasOpenTabs('oxy-ou_iconlist')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_iconlist-elements"
				ng-if="isActiveName('oxy-ou_iconlist')&&!hasOpenTabs('oxy-ou_iconlist')">
				<?php do_action("oxygen_add_plus_ouiconli_comp"); ?>
			</div>
		<?php }, 21 );
	}

	function controls() {
		$layout = $this->El->addControl('buttons-list', 'ul_layout', __('Layout', "oxy-ultimate"));
		$layout->setValue(['Stack', 'Inline']);
		$layout->setValueCSS([
			'Inline' 	=> 'li{display: inline-flex; margin-right: 10px;}',
			'Stack' 	=> 'li{display: flex;}'
		]);
		$layout->setDefaultValue('Stack');
		$layout->whiteList();

		$alignment = $this->El->addControl('buttons-list', 'ul_alignment', __('Alignment', "oxy-ultimate"));
		$alignment->setValue(['Left', 'Right', 'Center']);
		$alignment->setValueCSS([
			'Left' 		=> '.ou-icon-list-ul{margin-left: 0;margin-right: 0;}',
			'Right' 	=> '.ou-icon-list-ul{margin-left: auto;margin-right: 0;}',
			'Center' 	=> '.ou-icon-list-ul{margin-left: auto;margin-right: auto;}'
		]);
		$alignment->setDefaultValue('Left');
		$alignment->whiteList();

		$inAlignment = $this->El->addControl('buttons-list', 'inner_alignment', __('Inner Content Alignment', "oxy-ultimate"));
		$inAlignment->setValue(['Left', 'Right', 'Center']);
		$inAlignment->setValueCSS([
			'Left' 	=> 'li div, li div a{justify-content: flex-start;}',
			'Right' 	=> 'li div, li div a{justify-content: flex-end;}',
			'Center' 	=> 'li div, li div a{justify-content: center;}'
		]);
		$inAlignment->setParam('description', 'It will work when you will set the width into the LI wrapper. Default width is auto.');
		$inAlignment->setDefaultValue('Left');
		$inAlignment->whiteList();

		$this->addStyleControl([
			'name' 			=> __('Outer Wrapper Width(UL Tag)'),
			'selector' 		=> '.ou-icon-list-ul',
			'property' 		=> 'width',
			'control_type' 	=> 'slider-measurebox',
			'units' 		=> 'px'
		]);

		$this->addStyleControl(
			array(
				"name" 			=> __('Hover Transition Duration', "oxy-ultimate"),
				"slug" 			=> "ouliicon_ts",
				"selector" 		=> 'li div,li div a',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '0',
				"property" 		=> 'transition-duration'
			)
		)
		->setRange('0', '10', '0.1')
		->setUnits("s", "sec");
		

		/***************************
		LI Wrapper Style
		***************************/
		$liStyle = $this->addControlSection( 'li_style', __('LI Wrapper Style'), 'assets/icon.png', $this );

		$li_selector = 'li div';

		$liSP = $liStyle->addControlSection( 'li_sp', __('Spacing'), 'assets/icon.png', $this );
		$liSP->addPreset(
			"padding",
			"lisp_padding",
			__("Padding"),
			$li_selector
		)->whiteList();

		$liSP->addPreset(
			"margin",
			"lisp_margin",
			__("Margin"),
			$li_selector
		)->whiteList();

		$liSP->addStyleControls([
			[
				'selector' 		=> 'li',
				'property' 		=> 'width',
				'control_type' 	=> 'slider-measurebox',
				'units' 		=> 'px'
			],
			[
				'selector' 		=> $li_selector,
				'property' 		=> 'height',
				'control_type' 	=> 'slider-measurebox',
				'units' 		=> 'px'
			]
		]);

		$liStyle->addStyleControls([
			[
				'selector' 	=> $li_selector,
				'property' 	=> 'background-color',
				'description' 	=> __('This color will apply to all list items.')
			],
			[
				'name' 		=> __('Hover Background Color'),
				'selector' 	=> $li_selector . ':hover',
				'property' 	=> 'background-color',
				'description' 	=> __('This color will apply to all list items.')
			],
			[
				'name' 		=> __('Hover Border Color'),
				'selector' 	=> $li_selector . ':hover',
				'property' 	=> 'border-color',
				'description' 	=> __('This color will apply to all list items.')
			],
		]);

		$liStyle->borderSection( __('Border'), $li_selector, $this );


		/***************************
		Overall Texts Style
		***************************/
		$textStyle = $this->addControlSection( 'oulitxt_style', __('Overall Texts Style'), 'assets/icon.png', $this );
		
		$selector = '.ou-list-text';

		$txtSpacing = $textStyle->addControlSection( 'litxt_spacing', __('Spacing'), 'assets/icon.png', $this );
		$txtSpacing->addPreset(
			"padding",
			"litxt_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$txtSpacing->addPreset(
			"margin",
			"litxt_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$ttg = $textStyle->typographySection(__('Typography'), $selector . ', li a', $this );
		$ttg->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Hover Background Color'),
				'selector' 	=> 'li div:hover ' . $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Hover Text Color'),
				'selector' 	=> 'li div:hover ' . $selector . ', li div:hover a',
				'property' 	=> 'color'
			]
		]);



		/***************************
		Overall Icons Style
		***************************/
		$iconStyle = $this->addControlSection( 'ouliicon_style', __('Overall Icons Style'), 'assets/icon.png', $this );
		$iconStyle->addPreset(
			"padding",
			"liiconsp_padding",
			__("Padding"),
			'.ou-list-icon-wrap'
		)->whiteList();

		$iconStyle->addPreset(
			"margin",
			"liiconsp_margin",
			__("Margin"),
			'.ou-list-icon-wrap'
		)->whiteList();

		$iconStyle->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxy-ultimate"),
				"slug" 			=> "ouliicon_size",
				"selector" 		=> 'svg.ou-list-icon',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)
		->setRange(20, 80, 5)
		->setUnits("px", "px");

		$iconStyle->addStyleControls([
			[
				'selector' 		=> '.ou-list-icon-wrap',
				'property' 		=> 'width',
				"control_type" 	=> 'slider-measurebox',
				'units' 		=> 'px'
			],
			[
				'selector' 		=> '.ou-list-icon-wrap',
				'property' 		=> 'height',
				"control_type" 	=> 'slider-measurebox',
				'units' 		=> 'px'
			],
			[
				'selector' 		=> '.ou-list-icon-wrap',
				'property' 		=> 'line-height',
				'default' 		=> '2',
				'description' 	=> __('Using it, you can vertically center align the icon.')
			],
			[
				'selector' 		=> '.ou-list-icon-wrap',
				'property' 		=> 'background-color'
			],
			[
				'selector' 		=> '.ou-list-icon-wrap svg',
				'property' 		=> 'color'
			],
			[
				'name' 		=> __('Hover Background Color'),
				'selector' 		=> 'li div:hover .ou-list-icon-wrap',
				'property' 		=> 'background-color'
			],
			[
				'name' 		=> __('Hover Text Color'),
				'selector' 		=> 'li div:hover .ou-list-icon-wrap svg',
				'property' 		=> 'color'
			]
		]);

		$iconStyle->borderSection( __('Border'), '.ou-list-icon-wrap', $this );
	}

	function render( $options, $defaults, $content ) {
		if( $content ) {
			echo '<ul class="oxy-inner-content ou-icon-list-ul">';
			
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
			
			echo '</ul>';
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= 'body.oxygen-builder-body .oxy-ou-iconlist{min-height: 40px;}';
			$css .= '.oxy-ou-iconlist{width: 100%; display: inline-block;}
					.ou-icon-list-ul{display:table; padding:0; margin: 0; list-style: none; list-style-type: none;}';
			$this->css_added = true;
		}

		return $css;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUIconList();

include_once OXYU_DIR . 'elements/ou-icon-list/ou-icon-list-item.php';